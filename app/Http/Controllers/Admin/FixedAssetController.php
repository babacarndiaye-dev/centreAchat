<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FixedAsset;
use App\Models\PaymentAccount;
use App\Models\Supplier;
use App\Services\AccountingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
            'acquisition' => (float) $assets->sum('acquisition_value'),
            'accumulated' => (float) $assets->sum(fn ($a) => $a->accumulatedDepreciation()),
            'net' => (float) $assets->sum(fn ($a) => $a->netBookValue()),
        ];

        return Inertia::render('Admin/Finance/Assets/Index', [
            'assets' => $assets->map(fn (FixedAsset $asset) => [
                'id' => $asset->id,
                'name' => $asset->name,
                'category_label' => FixedAsset::CATEGORIES[$asset->category],
                'acquisition_date' => $asset->acquisition_date->format('d/m/Y'),
                'acquisition_value' => (float) $asset->acquisition_value,
                'accumulated_depreciation' => (float) $asset->accumulatedDepreciation(),
                'net_book_value' => (float) $asset->netBookValue(),
                'status_label' => FixedAsset::STATUSES[$asset->status],
                'status_badge_class' => $asset->statusBadgeClass(),
            ]),
            'totals' => $totals,
            'categories' => FixedAsset::CATEGORIES,
            'statuses' => FixedAsset::STATUSES,
            'filters' => $request->only('category', 'status'),
        ]);
    }

    protected function formOptions(): array
    {
        return [
            'paymentAccounts' => PaymentAccount::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'categories' => FixedAsset::CATEGORIES,
            'methods' => FixedAsset::METHODS,
        ];
    }

    public function create()
    {
        return Inertia::render('Admin/Finance/Assets/Form', $this->formOptions());
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
        return Inertia::render('Admin/Finance/Assets/Form', [
            ...$this->formOptions(),
            'asset' => [
                'id' => $immobilisation->id,
                'name' => $immobilisation->name,
                'category' => $immobilisation->category,
                'acquisition_date' => $immobilisation->acquisition_date->format('Y-m-d'),
                'acquisition_value' => (float) $immobilisation->acquisition_value,
                'useful_life_years' => $immobilisation->useful_life_years,
                'depreciation_method' => $immobilisation->depreciation_method,
                'payment_account_id' => $immobilisation->payment_account_id,
                'supplier_id' => $immobilisation->supplier_id,
                'notes' => $immobilisation->notes,
            ],
        ]);
    }

    public function update(Request $request, FixedAsset $immobilisation): RedirectResponse
    {
        $immobilisation->update($this->validated($request));

        return redirect()->route('admin.immobilisations.index')->with('success', 'Immobilisation mise à jour.');
    }

    public function show(FixedAsset $immobilisation)
    {
        $schedule = $immobilisation->depreciationSchedule();
        $currentYear = (int) floor($immobilisation->yearsElapsed()) + 1;

        return Inertia::render('Admin/Finance/Assets/Show', [
            'asset' => [
                'id' => $immobilisation->id,
                'name' => $immobilisation->name,
                'category_label' => FixedAsset::CATEGORIES[$immobilisation->category],
                'status' => $immobilisation->status,
                'status_label' => FixedAsset::STATUSES[$immobilisation->status],
                'status_badge_class' => $immobilisation->statusBadgeClass(),
                'acquisition_date' => $immobilisation->acquisition_date->format('d/m/Y'),
                'acquisition_value' => (float) $immobilisation->acquisition_value,
                'annual_depreciation' => (float) $immobilisation->annualDepreciation(),
                'accumulated_depreciation' => (float) $immobilisation->accumulatedDepreciation(),
                'net_book_value' => (float) $immobilisation->netBookValue(),
                'depreciation_method_label' => FixedAsset::METHODS[$immobilisation->depreciation_method],
                'useful_life_years' => $immobilisation->useful_life_years,
                'payment_account_name' => $immobilisation->paymentAccount?->name,
                'supplier_name' => $immobilisation->supplier?->name,
                'notes' => $immobilisation->notes,
                'disposal_date' => $immobilisation->disposal_date?->format('d/m/Y'),
                'disposal_value' => $immobilisation->disposal_value !== null ? (float) $immobilisation->disposal_value : null,
            ],
            'schedule' => $schedule,
            'currentYear' => $currentYear,
        ]);
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
