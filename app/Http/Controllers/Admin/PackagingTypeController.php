<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PackagingType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PackagingTypeController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/ProductSettings/ReferenceList', [
            'title' => 'Conditionnements',
            'subtitle' => null,
            'routeName' => 'admin.produits-parametres.conditionnements',
            'extraField' => null,
            'items' => PackagingType::orderBy('name')->get(['id', 'name', 'is_active']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:packaging_types,name'],
        ]);

        $data['is_active'] = true;

        PackagingType::create($data);

        return back()->with('success', 'Conditionnement créé.');
    }

    public function update(Request $request, PackagingType $packagingType): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:packaging_types,name,'.$packagingType->id],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $packagingType->update($data);

        return back()->with('success', 'Conditionnement mis à jour.');
    }

    public function destroy(PackagingType $packagingType): RedirectResponse
    {
        $packagingType->delete();

        return back()->with('success', 'Conditionnement supprimé.');
    }
}
