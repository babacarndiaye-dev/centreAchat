<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaxRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaxRateController extends Controller
{
    public function index()
    {
        $taxRates = TaxRate::orderBy('name')->get()->map(fn (TaxRate $tax) => [
            'id' => $tax->id,
            'name' => $tax->name,
            'rate' => (float) $tax->rate,
            'is_active' => $tax->is_active,
            'is_default' => $tax->is_default,
        ]);

        return Inertia::render('Admin/Commercial/TaxRates', ['taxRates' => $taxRates]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $data['is_active'] = true;

        TaxRate::create($data);

        return back()->with('success', 'Taux de taxe créé.');
    }

    public function update(Request $request, TaxRate $taxRate): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $taxRate->update($data);

        return back()->with('success', 'Taux de taxe mis à jour.');
    }

    public function setDefault(TaxRate $taxRate): RedirectResponse
    {
        TaxRate::query()->update(['is_default' => false]);
        $taxRate->update(['is_default' => true]);

        return back()->with('success', 'Taux par défaut mis à jour.');
    }

    public function destroy(TaxRate $taxRate): RedirectResponse
    {
        $taxRate->delete();

        return back()->with('success', 'Taux de taxe supprimé.');
    }
}
