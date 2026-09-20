<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with('supplier');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $purchaseOrders = $query->latest()->paginate(20)->withQueryString()->through(fn (PurchaseOrder $po) => [
            'id' => $po->id,
            'order_number' => $po->order_number,
            'supplier_name' => $po->supplier->name,
            'order_date' => $po->order_date->format('d/m/Y'),
            'status' => $po->status,
            'status_label' => PurchaseOrder::STATUSES[$po->status],
            'status_badge_class' => $po->statusBadgeClass(),
            'total' => (float) $po->total,
        ]);

        return Inertia::render('Admin/PurchaseOrders/Index', [
            'purchaseOrders' => $purchaseOrders,
            'statuses' => PurchaseOrder::STATUSES,
            'filters' => $request->only('status'),
        ]);
    }

    public function create(Request $request)
    {
        $suppliers = Supplier::where('status', 'actif')->orderBy('name')->get(['id', 'name']);
        $products = Product::orderBy('name')->get(['id', 'name']);

        $purchaseRequest = null;
        if ($request->filled('demande')) {
            $pr = PurchaseRequest::with('items.product')->find($request->integer('demande'));
            if ($pr) {
                $purchaseRequest = [
                    'id' => $pr->id,
                    'reference' => $pr->reference,
                    'items' => $pr->items->map(fn ($item) => [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                    ]),
                ];
            }
        }

        return Inertia::render('Admin/PurchaseOrders/Create', [
            'suppliers' => $suppliers,
            'products' => $products,
            'purchaseRequest' => $purchaseRequest,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_request_id' => ['nullable', 'exists:purchase_requests,id'],
            'order_date' => ['required', 'date'],
            'expected_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'product_id' => ['required', 'array', 'min:1'],
            'product_id.*' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'array'],
            'quantity.*' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'array'],
            'unit_price.*' => ['required', 'numeric', 'min:0'],
        ]);

        $order = DB::transaction(function () use ($data) {
            $subtotal = 0;

            foreach ($data['quantity'] as $i => $qty) {
                $subtotal += $qty * $data['unit_price'][$i];
            }

            $order = PurchaseOrder::create([
                'order_number' => PurchaseOrder::generateOrderNumber(),
                'supplier_id' => $data['supplier_id'],
                'purchase_request_id' => $data['purchase_request_id'] ?? null,
                'created_by' => Auth::id(),
                'status' => 'brouillon',
                'order_date' => $data['order_date'],
                'expected_date' => $data['expected_date'] ?? null,
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['product_id'] as $i => $productId) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity_ordered' => $data['quantity'][$i],
                    'unit_price' => $data['unit_price'][$i],
                    'total' => $data['quantity'][$i] * $data['unit_price'][$i],
                ]);
            }

            if (! empty($data['purchase_request_id'])) {
                PurchaseRequest::where('id', $data['purchase_request_id'])->update(['status' => 'convertie']);
            }

            return $order;
        });

        return redirect()->route('admin.bons-commande.show', $order)->with('success', 'Bon de commande créé.');
    }

    public function show(PurchaseOrder $bonCommande)
    {
        $bonCommande->load(['supplier', 'items.product', 'receptions.items.orderItem.product', 'payments']);

        return Inertia::render('Admin/PurchaseOrders/Show', [
            'purchaseOrder' => [
                'id' => $bonCommande->id,
                'order_number' => $bonCommande->order_number,
                'status' => $bonCommande->status,
                'status_label' => PurchaseOrder::STATUSES[$bonCommande->status],
                'status_badge_class' => $bonCommande->statusBadgeClass(),
                'order_date' => $bonCommande->order_date->format('d/m/Y'),
                'expected_date' => $bonCommande->expected_date?->format('d/m/Y'),
                'notes' => $bonCommande->notes,
                'total' => (float) $bonCommande->total,
                'amount_paid' => (float) $bonCommande->amount_paid,
                'balance' => $bonCommande->balance(),
                'supplier' => [
                    'id' => $bonCommande->supplier->id,
                    'name' => $bonCommande->supplier->name,
                ],
                'items' => $bonCommande->items->map(fn ($item) => [
                    'id' => $item->id,
                    'product_name' => $item->product->name,
                    'quantity_ordered' => $item->quantity_ordered,
                    'quantity_received' => $item->quantity_received,
                    'remaining_quantity' => $item->remainingQuantity(),
                    'unit_price' => (float) $item->unit_price,
                    'total' => (float) $item->total,
                ]),
                'receptions' => $bonCommande->receptions->map(fn ($reception) => [
                    'id' => $reception->id,
                    'reception_date' => $reception->reception_date->format('d/m/Y'),
                    'quality_status_label' => \App\Models\PurchaseReception::QUALITY_STATUSES[$reception->quality_status],
                    'notes' => $reception->notes,
                    'items' => $reception->items->map(fn ($ri) => [
                        'product_name' => $ri->orderItem->product->name,
                        'quantity_received' => $ri->quantity_received,
                        'is_conforme' => $ri->quality_status === 'conforme',
                    ]),
                ]),
                'payments' => $bonCommande->payments->map(fn ($payment) => [
                    'id' => $payment->id,
                    'payment_date' => $payment->payment_date->format('d/m/Y'),
                    'amount' => (float) $payment->amount,
                ]),
            ],
            'paymentAccounts' => \App\Models\PaymentAccount::where('is_active', true)->get(['id', 'name']),
        ]);
    }

    public function updateStatus(Request $request, PurchaseOrder $bonCommande): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:envoyee,confirmee,annulee'],
        ]);

        $bonCommande->update($data);

        return back()->with('success', 'Statut du bon de commande mis à jour.');
    }
}
