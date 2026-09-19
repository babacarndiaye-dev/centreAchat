<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FixedAsset;
use App\Models\PaymentAccount;
use App\Models\Supplier;
use App\Services\AccountingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FixedAssetController extends Controller
{
    public function index(Request $request)
    {
        $query = FixedAsset::query();

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $assets = $query->orderBy('acquisition_date', 'desc')->get();

        $totals = [
            'acquisition' => $assets->sum('acquisition_value'),
            'accumulated' => $assets->sum(fn ($a) => $a->accumulatedDepreciation()),
            'net' => $assets->sum(fn ($a) => $a->netBookValue()),
        ];

        return view('admin.assets.index', compact('assets', 'totals'));
    }

    public function create()
    {
        $paymentAccounts = PaymentAccount::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.assets.create', compact('paymentAccounts', 'suppliers'));
    }

    public function store(Request $request, AccountingService $accounting): RedirectResponse
    {
        $data = $this->validated($request);

        $asset = FixedAsset::create($data);

        if ($asset->payment_account_id) {
            $accounting->postAssetAcquisition($asset);
        }

        return redirect()->route('admin.immobilisations.index')->with('success', 'Immobilisation enregistrée.');
    }

    public function edit(FixedAsset $immobilisation)
    {
        $paymentAccounts = PaymentAccount::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.assets.edit', ['asset' => $immobilisation, 'paymentAccounts' => $paymentAccounts, 'suppliers' => $suppliers]);
    }

    public function update(Request $request, FixedAsset $immobilisation): RedirectResponse
    {
        $immobilisation->update($this->validated($request));

        return redirect()->route('admin.immobilisations.index')->with('success', 'Immobilisation mise à jour.');
    }

    public function show(FixedAsset $immobilisation)
    {
        $schedule = $immobilisation->depreciationSchedule();

        return view('admin.assets.show', ['asset' => $immobilisation, 'schedule' => $schedule]);
    }

    public function dispose(Request $request, FixedAsset $immobilisation): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:cede,reforme'],
            'disposal_date' => ['required', 'date'],
            'disposal_value' => ['nullable', 'numeric', 'min:0'],
        ]);

        $immobilisation->update($data);

        return back()->with('success', 'Immobilisation mise hors service.');
    }

    public function destroy(FixedAsset $immobilisation): RedirectResponse
    {
        $immobilisation->delete();

        return redirect()->route('admin.immobilisations.index')->with('success', 'Immobilisation supprimée.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'category' => ['required', 'in:'.implode(',', array_keys(FixedAsset::CATEGORIES))],
            'name' => ['required', 'string', 'max:255'],
            'acquisition_date' => ['required', 'date'],
            'acquisition_value' => ['required', 'numeric', 'min:0.01'],
            'useful_life_years' => ['required', 'integer', 'min:1', 'max:50'],
            'depreciation_method' => ['required', 'in:lineaire,degressif'],
            'payment_account_id' => ['nullable', 'exists:payment_accounts,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
