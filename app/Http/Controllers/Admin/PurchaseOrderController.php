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

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with('supplier');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $purchaseOrders = $query->latest()->paginate(20)->withQueryString();

        return view('admin.purchase-orders.index', compact('purchaseOrders'));
    }

    public function create(Request $request)
    {
        $suppliers = Supplier::where('status', 'actif')->orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        $purchaseRequest = null;
        if ($request->filled('demande')) {
            $purchaseRequest = PurchaseRequest::with('items.product')->find($request->integer('demande'));
        }

        return view('admin.purchase-orders.create', compact('suppliers', 'products', 'purchaseRequest'));
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

        return view('admin.purchase-orders.show', ['purchaseOrder' => $bonCommande]);
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
