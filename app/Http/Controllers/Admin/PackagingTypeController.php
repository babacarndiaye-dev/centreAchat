<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PackagingType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PackagingTypeController extends Controller
{
    public function index()
    {
        $packagingTypes = PackagingType::orderBy('name')->get();

        return view('admin.products-settings.packaging-types.index', compact('packagingTypes'));
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
