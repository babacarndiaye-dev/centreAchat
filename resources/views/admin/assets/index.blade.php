@extends('layouts.admin')

@section('title', 'Immobilisations')

@section('content')
<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="admin-card"><p class="text-xs text-terroir-dark/50">Valeur d'acquisition</p><p class="mt-1.5 text-xl font-bold">{{ number_format($totals['acquisition'], 0, ',', ' ') }} FCFA</p></div>
    <div class="admin-card"><p class="text-xs text-terroir-dark/50">Amortissement cumulé</p><p class="mt-1.5 text-xl font-bold text-terroir-terracotta">{{ number_format($totals['accumulated'], 0, ',', ' ') }} FCFA</p></div>
    <div class="admin-card"><p class="text-xs text-terroir-dark/50">Valeur nette comptable</p><p class="mt-1.5 text-xl font-bold text-terroir-green">{{ number_format($totals['net'], 0, ',', ' ') }} FCFA</p></div>
</div>

<div class="mt-6 flex flex-wrap items-center justify-between gap-3">
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="category" class="input max-w-[220px]" onchange="this.form.submit()">
            <option value="">Toutes catégories</option>
            @foreach(\App\Models\FixedAsset::CATEGORIES as $value => $label)
                <option value="{{ $value }}" @selected(request('category') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="status" class="input max-w-[200px]" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            @foreach(\App\Models\FixedAsset::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </form>
    <a href="{{ route('admin.immobilisations.create') }}" class="btn-primary">
        <span class="material-symbols-outlined text-lg">add</span>
        Nouvelle immobilisation
    </a>
</div>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Désignation</th>
                <th>Catégorie</th>
                <th>Acquisition</th>
                <th class="text-right">Valeur d'origine</th>
                <th class="text-right">Amorti</th>
                <th class="text-right">VNC</th>
                <th class="pr-6">Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $asset)
                <tr>
                    <td class="pl-6 font-semibold"><a href="{{ route('admin.immobilisations.show', $asset) }}" class="admin-link">{{ $asset->name }}</a></td>
                    <td class="text-terroir-dark/60">{{ \App\Models\FixedAsset::CATEGORIES[$asset->category] }}</td>
                    <td class="text-terroir-dark/60">{{ $asset->acquisition_date->format('d/m/Y') }}</td>
                    <td class="text-right">{{ number_format($asset->acquisition_value, 0, ',', ' ') }}</td>
                    <td class="text-right text-terroir-terracotta">{{ number_format($asset->accumulatedDepreciation(), 0, ',', ' ') }}</td>
                    <td class="text-right font-semibold text-terroir-green">{{ number_format($asset->netBookValue(), 0, ',', ' ') }}</td>
                    <td class="pr-6"><span class="{{ $asset->statusBadgeClass() }}">{{ \App\Models\FixedAsset::STATUSES[$asset->status] }}</span></td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-8 text-center text-terroir-dark/40">Aucune immobilisation.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
