<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    public function index()
    {
        $productAttributes = ProductAttribute::orderBy('name')->get();

        return view('admin.products-settings.attributes.index', compact('productAttributes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:product_attributes,name'],
        ]);

        $data['is_active'] = true;

        ProductAttribute::create($data);

        return back()->with('success', 'Attribut créé.');
    }

    public function update(Request $request, ProductAttribute $productAttribute): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:product_attributes,name,'.$productAttribute->id],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $productAttribute->update($data);

        return back()->with('success', 'Attribut mis à jour.');
    }

    public function destroy(ProductAttribute $productAttribute): RedirectResponse
    {
        $productAttribute->delete();

        return back()->with('success', 'Attribut supprimé.');
    }
}
