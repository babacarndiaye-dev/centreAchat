<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UnitController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/ProductSettings/ReferenceList', [
            'title' => 'Unités',
            'subtitle' => null,
            'routeName' => 'admin.produits-parametres.unites',
            'extraField' => ['key' => 'abbreviation', 'label' => 'Abréviation'],
            'items' => Unit::orderBy('name')->get(['id', 'name', 'abbreviation', 'is_active']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:units,name'],
            'abbreviation' => ['nullable', 'string', 'max:20'],
        ]);

        $data['is_active'] = true;

        Unit::create($data);

        return back()->with('success', 'Unité créée.');
    }

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:units,name,'.$unit->id],
            'abbreviation' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $unit->update($data);

        return back()->with('success', 'Unité mise à jour.');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        $unit->delete();

        return back()->with('success', 'Unité supprimée.');
    }
}
