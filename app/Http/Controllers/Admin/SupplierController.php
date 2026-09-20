<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentAccount;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('q')) {
            $search = $request->string('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $suppliers = $query->orderBy('name')->paginate(20)->withQueryString()->through(fn (Supplier $supplier) => [
            'id' => $supplier->id,
            'name' => $supplier->name,
            'company_name' => $supplier->company_name,
            'phone' => $supplier->phone,
            'city' => $supplier->city,
            'region' => $supplier->region,
            'status' => $supplier->status,
            'status_label' => Supplier::STATUSES[$supplier->status],
            'status_badge_class' => $supplier->statusBadgeClass(),
            'balance' => $supplier->balance(),
        ]);

        return Inertia::render('Admin/Suppliers/Index', [
            'suppliers' => $suppliers,
            'statuses' => Supplier::STATUSES,
            'filters' => $request->only('q', 'status'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Suppliers/Form', ['statuses' => Supplier::STATUSES]);
    }

    public function store(Request $request): RedirectResponse
    {
        Supplier::create($this->validated($request));

        return redirect()->route('admin.fournisseurs.index')->with('success', 'Fournisseur créé.');
    }

    public function show(Supplier $fournisseur)
    {
        $fournisseur->load(['supplierProducts.product', 'purchaseOrders', 'payments', 'user']);

        return Inertia::render('Admin/Suppliers/Show', [
            'supplier' => [
                'id' => $fournisseur->id,
                'name' => $fournisseur->name,
                'company_name' => $fournisseur->company_name,
                'contact_name' => $fournisseur->contact_name,
                'phone' => $fournisseur->phone,
                'email' => $fournisseur->email,
                'address' => $fournisseur->address,
                'city' => $fournisseur->city,
                'region' => $fournisseur->region,
                'payment_terms' => $fournisseur->payment_terms,
                'delivery_delay_days' => $fournisseur->delivery_delay_days,
                'notes' => $fournisseur->notes,
                'status' => $fournisseur->status,
                'status_label' => Supplier::STATUSES[$fournisseur->status],
                'total_owed' => $fournisseur->totalOwed(),
                'total_paid' => $fournisseur->totalPaid(),
                'balance' => $fournisseur->balance(),
                'user_email' => $fournisseur->user?->email,
                'supplier_products' => $fournisseur->supplierProducts->map(fn ($sp) => [
                    'id' => $sp->id,
                    'product_name' => $sp->product->name,
                    'supplier_price' => (float) $sp->supplier_price,
                    'supplier_reference' => $sp->supplier_reference,
                ]),
                'purchase_orders' => $fournisseur->purchaseOrders->map(fn ($po) => [
                    'id' => $po->id,
                    'order_number' => $po->order_number,
                    'order_date' => $po->order_date->format('d/m/Y'),
                    'status_label' => PurchaseOrder::STATUSES[$po->status],
                    'total' => (float) $po->total,
                    'balance' => $po->balance(),
                ]),
            ],
            'products' => Product::orderBy('name')->get(['id', 'name']),
            'paymentAccounts' => PaymentAccount::where('is_active', true)->get(['id', 'name']),
        ]);
    }

    public function edit(Supplier $fournisseur)
    {
        return Inertia::render('Admin/Suppliers/Form', [
            'statuses' => Supplier::STATUSES,
            'supplier' => [
                'id' => $fournisseur->id,
                'name' => $fournisseur->name,
                'company_name' => $fournisseur->company_name,
                'contact_name' => $fournisseur->contact_name,
                'phone' => $fournisseur->phone,
                'email' => $fournisseur->email,
                'address' => $fournisseur->address,
                'city' => $fournisseur->city,
                'region' => $fournisseur->region,
                'payment_terms' => $fournisseur->payment_terms,
                'delivery_delay_days' => $fournisseur->delivery_delay_days,
                'status' => $fournisseur->status,
                'rating' => $fournisseur->rating !== null ? (float) $fournisseur->rating : null,
                'notes' => $fournisseur->notes,
            ],
        ]);
    }

    public function update(Request $request, Supplier $fournisseur): RedirectResponse
    {
        $fournisseur->update($this->validated($request));

        return redirect()->route('admin.fournisseurs.index')->with('success', 'Fournisseur mis à jour.');
    }

    public function destroy(Supplier $fournisseur): RedirectResponse
    {
        $fournisseur->delete();

        return back()->with('success', 'Fournisseur supprimé.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:2000'],
            'city' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
            'payment_terms' => ['nullable', 'string', 'max:255'],
            'delivery_delay_days' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:'.implode(',', array_keys(Supplier::STATUSES))],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
