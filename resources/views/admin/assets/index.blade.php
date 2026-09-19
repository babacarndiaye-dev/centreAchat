@extends('layouts.admin')

@section('title', 'Immobilisations')

@section('content')
@php
    $assetStatusColors = [
        'en_service' => ['bg' => 'rgba(29,138,78,.12)', 'color' => '#1D8A4E'],
        'cede' => ['bg' => 'rgba(31,35,40,.08)', 'color' => 'rgba(31,35,40,.6)'],
        'reforme' => ['bg' => 'rgba(232,96,79,.12)', 'color' => '#E8604F'],
    ];
@endphp
<div class="uk-grid-small uk-child-width-1-3" uk-grid>
    <div><div class="uk-card uk-card-default" style="padding:24px;"><p style="font-size:.75rem; color:rgba(31,35,40,.5);">Valeur d'acquisition</p><p class="uk-margin-small-top" style="font-size:1.25rem; font-weight:700;">{{ number_format($totals['acquisition'], 0, ',', ' ') }} FCFA</p></div></div>
    <div><div class="uk-card uk-card-default" style="padding:24px;"><p style="font-size:.75rem; color:rgba(31,35,40,.5);">Amortissement cumulé</p><p class="uk-margin-small-top" style="font-size:1.25rem; font-weight:700; color:#E8604F;">{{ number_format($totals['accumulated'], 0, ',', ' ') }} FCFA</p></div></div>
    <div><div class="uk-card uk-card-default" style="padding:24px;"><p style="font-size:.75rem; color:rgba(31,35,40,.5);">Valeur nette comptable</p><p class="uk-margin-small-top" style="font-size:1.25rem; font-weight:700; color:#1D8A4E;">{{ number_format($totals['net'], 0, ',', ' ') }} FCFA</p></div></div>
</div>

<div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap uk-margin-top" style="gap:12px;">
    <form method="GET" class="uk-flex uk-flex-wrap" style="gap:8px;">
        <select name="category" class="uk-select" style="max-width:220px;" onchange="this.form.submit()">
            <option value="">Toutes catégories</option>
            @foreach(\App\Models\FixedAsset::CATEGORIES as $value => $label)
                <option value="{{ $value }}" @selected(request('category') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="status" class="uk-select" style="max-width:200px;" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            @foreach(\App\Models\FixedAsset::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </form>
    <a href="{{ route('admin.immobilisations.create') }}" class="uk-button uk-button-primary">+ Nouvelle immobilisation</a>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Catégorie</th>
                <th>Acquisition</th>
                <th class="uk-text-right">Valeur d'origine</th>
                <th class="uk-text-right">Amorti</th>
                <th class="uk-text-right">VNC</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $asset)
                <tr>
                    <td style="font-weight:600;"><a href="{{ route('admin.immobilisations.show', $asset) }}" style="color:#1D8A4E;">{{ $asset->name }}</a></td>
                    <td class="uk-text-muted">{{ \App\Models\FixedAsset::CATEGORIES[$asset->category] }}</td>
                    <td class="uk-text-muted">{{ $asset->acquisition_date->format('d/m/Y') }}</td>
                    <td class="uk-text-right">{{ number_format($asset->acquisition_value, 0, ',', ' ') }}</td>
                    <td class="uk-text-right" style="color:#E8604F;">{{ number_format($asset->accumulatedDepreciation(), 0, ',', ' ') }}</td>
                    <td class="uk-text-right" style="font-weight:600; color:#1D8A4E;">{{ number_format($asset->netBookValue(), 0, ',', ' ') }}</td>
                    <td>
                        @php $badge = $assetStatusColors[$asset->status]; @endphp
                        <span class="uk-label" style="background:{{ $badge['bg'] }}; color:{{ $badge['color'] }};">{{ \App\Models\FixedAsset::STATUSES[$asset->status] }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucune immobilisation.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
