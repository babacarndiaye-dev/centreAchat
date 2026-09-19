<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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

        $suppliers = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Supplier::create($this->validated($request));

        return redirect()->route('admin.fournisseurs.index')->with('success', 'Fournisseur créé.');
    }

    public function show(Supplier $fournisseur)
    {
        $fournisseur->load(['supplierProducts.product', 'purchaseOrders', 'payments', 'user']);
        $products = Product::orderBy('name')->get();

        return view('admin.suppliers.show', ['supplier' => $fournisseur, 'products' => $products]);
    }

    public function edit(Supplier $fournisseur)
    {
        return view('admin.suppliers.edit', ['supplier' => $fournisseur]);
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
