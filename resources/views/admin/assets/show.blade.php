@extends('layouts.admin')

@section('title', $asset->name)

@section('content')
@php
    $assetStatusColors = [
        'en_service' => ['bg' => 'rgba(29,138,78,.12)', 'color' => '#1D8A4E'],
        'cede' => ['bg' => 'rgba(31,35,40,.08)', 'color' => 'rgba(31,35,40,.6)'],
        'reforme' => ['bg' => 'rgba(232,96,79,.12)', 'color' => '#E8604F'],
    ];
@endphp
<div class="uk-flex uk-flex-wrap uk-flex-middle uk-flex-between" style="gap:16px;">
    <div>
        <span class="uk-text-small uk-text-muted" style="text-transform:uppercase; letter-spacing:.05em;">{{ \App\Models\FixedAsset::CATEGORIES[$asset->category] }}</span>
        <h2 class="uk-margin-small-top" style="font-family:'Fraunces',serif; font-weight:600; font-size:1.5rem;">{{ $asset->name }}</h2>
    </div>
    <div class="uk-flex uk-flex-middle" style="gap:12px;">
        @php $badge = $assetStatusColors[$asset->status]; @endphp
        <span class="uk-label" style="background:{{ $badge['bg'] }}; color:{{ $badge['color'] }}; padding:8px 16px; font-size:.875rem;">{{ \App\Models\FixedAsset::STATUSES[$asset->status] }}</span>
        <a href="{{ route('admin.immobilisations.edit', $asset) }}" class="uk-button uk-button-default">Modifier</a>
    </div>
</div>

<div class="uk-grid-small uk-child-width-1-2 uk-child-width-1-4@l uk-margin-top" uk-grid>
    <div><div class="uk-card uk-card-default" style="padding:20px;"><p style="font-size:.75rem; color:rgba(31,35,40,.5);">Valeur d'acquisition</p><p class="uk-margin-small-top" style="font-size:1.25rem; font-weight:700;">{{ number_format($asset->acquisition_value, 0, ',', ' ') }} FCFA</p></div></div>
    <div><div class="uk-card uk-card-default" style="padding:20px;"><p style="font-size:.75rem; color:rgba(31,35,40,.5);">Amortissement annuel</p><p class="uk-margin-small-top" style="font-size:1.25rem; font-weight:700;">{{ number_format($asset->annualDepreciation(), 0, ',', ' ') }} FCFA</p></div></div>
    <div><div class="uk-card uk-card-default" style="padding:20px;"><p style="font-size:.75rem; color:rgba(31,35,40,.5);">Amorti à ce jour</p><p class="uk-margin-small-top" style="font-size:1.25rem; font-weight:700; color:#E8604F;">{{ number_format($asset->accumulatedDepreciation(), 0, ',', ' ') }} FCFA</p></div></div>
    <div><div class="uk-card uk-card-default" style="padding:20px;"><p style="font-size:.75rem; color:rgba(31,35,40,.5);">Valeur nette comptable</p><p class="uk-margin-small-top" style="font-size:1.25rem; font-weight:700; color:#1D8A4E;">{{ number_format($asset->netBookValue(), 0, ',', ' ') }} FCFA</p></div></div>
</div>

<div class="uk-grid-small uk-margin-top" uk-grid>
    <div class="uk-width-2-3@l">
        <div class="uk-card uk-card-default" style="padding:24px;">
            <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Tableau d'amortissement ({{ \App\Models\FixedAsset::METHODS[$asset->depreciation_method] }})</h3>
            <table class="uk-table uk-table-divider uk-table-middle uk-margin-small-top">
                <thead>
                    <tr>
                        <th>Année</th>
                        <th class="uk-text-right">Dotation annuelle</th>
                        <th class="uk-text-right">Cumul</th>
                        <th class="uk-text-right">VNC fin d'année</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedule as $row)
                        <tr style="{{ (int) floor($asset->yearsElapsed()) + 1 === $row['year'] && $asset->status === 'en_service' ? 'background:#F7F8F5; font-weight:600;' : '' }}">
                            <td>{{ $row['year'] }}</td>
                            <td class="uk-text-right">{{ number_format($row['annual'], 0, ',', ' ') }}</td>
                            <td class="uk-text-right">{{ number_format($row['accumulated'], 0, ',', ' ') }}</td>
                            <td class="uk-text-right">{{ number_format($row['net_value'], 0, ',', ' ') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="uk-width-1-3@l">
        <div class="uk-card uk-card-default" style="padding:24px;">
            <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Informations</h3>
            <dl class="uk-margin-small-top" style="font-size:.875rem;">
                <div class="uk-margin-small-bottom"><dt style="color:rgba(31,35,40,.5);">Date d'acquisition</dt><dd style="font-weight:600; margin-top:2px;">{{ $asset->acquisition_date->format('d/m/Y') }}</dd></div>
                <div class="uk-margin-small-bottom"><dt style="color:rgba(31,35,40,.5);">Durée d'amortissement</dt><dd style="font-weight:600; margin-top:2px;">{{ $asset->useful_life_years }} ans</dd></div>
                @if($asset->paymentAccount)
                    <div class="uk-margin-small-bottom"><dt style="color:rgba(31,35,40,.5);">Financé par</dt><dd style="font-weight:600; margin-top:2px;">{{ $asset->paymentAccount->name }}</dd></div>
                @endif
                @if($asset->supplier)
                    <div class="uk-margin-small-bottom"><dt style="color:rgba(31,35,40,.5);">Fournisseur</dt><dd style="font-weight:600; margin-top:2px;">{{ $asset->supplier->name }}</dd></div>
                @endif
                @if($asset->notes)
                    <div class="uk-margin-small-bottom"><dt style="color:rgba(31,35,40,.5);">Notes</dt><dd style="font-weight:600; margin-top:2px;">{{ $asset->notes }}</dd></div>
                @endif
            </dl>
        </div>

        @if($asset->status === 'en_service')
            <div class="uk-card uk-card-default uk-margin-top" style="padding:24px;">
                <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Mettre hors service</h3>
                <form action="{{ route('admin.immobilisations.dispose', $asset) }}" method="POST" class="uk-margin-top" style="display:flex; flex-direction:column; gap:12px;" onsubmit="return confirm('Confirmer la mise hors service de cette immobilisation ?')">
                    @csrf
                    <select name="status" required class="uk-select">
                        <option value="cede">Cédé (vendu)</option>
                        <option value="reforme">Réformé (mis au rebut)</option>
                    </select>
                    <input type="date" name="disposal_date" value="{{ now()->format('Y-m-d') }}" required class="uk-input">
                    <input type="number" step="0.01" name="disposal_value" placeholder="Valeur de cession (FCFA, optionnel)" class="uk-input">
                    <button type="submit" class="uk-button uk-button-default uk-width-1-1">Confirmer</button>
                </form>
            </div>
        @else
            <div class="uk-card uk-card-default uk-margin-top" style="padding:24px; font-size:.875rem;">
                <p style="color:rgba(31,35,40,.5);">{{ \App\Models\FixedAsset::STATUSES[$asset->status] }} le</p>
                <p style="font-weight:600;">{{ optional($asset->disposal_date)->format('d/m/Y') }}</p>
                @if($asset->disposal_value)
                    <p class="uk-margin-small-top" style="color:rgba(31,35,40,.5);">Valeur de cession</p>
                    <p style="font-weight:600;">{{ number_format($asset->disposal_value, 0, ',', ' ') }} FCFA</p>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
