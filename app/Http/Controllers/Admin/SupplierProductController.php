<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupplierProductController extends Controller
{
    public function store(Request $request, Supplier $supplier): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'supplier_price' => ['required', 'numeric', 'min:0'],
            'supplier_reference' => ['nullable', 'string', 'max:255'],
            'lead_time_days' => ['nullable', 'integer', 'min:0'],
            'is_preferred' => ['nullable', 'boolean'],
        ]);

        $data['is_preferred'] = $request->boolean('is_preferred');

        SupplierProduct::updateOrCreate(
            ['supplier_id' => $supplier->id, 'product_id' => $data['product_id']],
            $data
        );

        return back()->with('success', 'Produit ajouté au catalogue du fournisseur.');
    }

    public function destroy(SupplierProduct $supplierProduct): RedirectResponse
    {
        $supplierId = $supplierProduct->supplier_id;
        $supplierProduct->delete();

        return redirect()->route('admin.fournisseurs.show', $supplierId)->with('success', 'Produit retiré du catalogue.');
    }
}
