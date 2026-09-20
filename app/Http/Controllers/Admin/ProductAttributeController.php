<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductAttributeController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/ProductSettings/ReferenceList', [
            'title' => 'Attributs',
            'subtitle' => 'Ces attributs deviennent des champs libres sur les fiches produits.',
            'routeName' => 'admin.produits-parametres.attributs',
            'extraField' => null,
            'items' => ProductAttribute::orderBy('name')->get(['id', 'name', 'is_active']),
        ]);
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
