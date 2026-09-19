@extends('layouts.admin')

@section('title', 'Statistiques')

@section('content')
<div class="uk-grid-small uk-child-width-1-2@s uk-child-width-1-4@m" uk-grid>
    <div class="uk-card uk-card-default" style="padding:20px;">
        <p class="uk-text-small uk-text-muted">CA du mois</p>
        <p style="margin-top:4px; font-size:1.5rem; font-weight:700; color:#1D8A4E;">{{ number_format($revenueThisMonth, 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="uk-card uk-card-default" style="padding:20px;">
        <p class="uk-text-small uk-text-muted">Panier moyen</p>
        <p style="margin-top:4px; font-size:1.5rem; font-weight:700; color:#1F2328;">{{ number_format($averageBasket, 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="uk-card uk-card-default" style="padding:20px;">
        <p class="uk-text-small uk-text-muted">Valeur du stock</p>
        <p style="margin-top:4px; font-size:1.5rem; font-weight:700; color:#1F2328;">{{ number_format($stockValue, 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="uk-card uk-card-default" style="padding:20px;">
        <p class="uk-text-small uk-text-muted">Ruptures / Stock faible</p>
        <p style="margin-top:4px; font-size:1.5rem; font-weight:700; {{ $outOfStock > 0 ? 'color:#E8604F;' : 'color:#1F2328;' }}">{{ $outOfStock }} / {{ $lowStock }}</p>
    </div>
</div>

<div class="uk-grid-small uk-child-width-1-2@s uk-margin-top" uk-grid>
    <div class="uk-card uk-card-default" style="padding:20px;">
        <p class="uk-text-small uk-text-muted">Créances clients (impayés)</p>
        <p style="margin-top:4px; font-size:1.5rem; font-weight:700; color:#E8604F;">{{ number_format($receivables, 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="uk-card uk-card-default" style="padding:20px;">
        <p class="uk-text-small uk-text-muted">Dettes fournisseurs</p>
        <p style="margin-top:4px; font-size:1.5rem; font-weight:700; color:#E8604F;">{{ number_format($payables, 0, ',', ' ') }} FCFA</p>
    </div>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="padding:24px;">
    <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Chiffre d'affaires — 6 derniers mois</h2>
    <div class="uk-margin-small-top" style="height: 280px;">
        <canvas id="chart-revenue"></canvas>
    </div>
    <div class="uk-flex uk-flex-wrap uk-margin-small-top" style="gap:24px 24px; border-top:1px solid rgba(29,138,78,.1); padding-top:16px; font-size:.75rem; color:rgba(31,35,40,.6);">
        @foreach($revenueByMonth as $row)
            <span>{{ $row['label'] }} : <strong style="color:#1F2328;">{{ number_format($row['value'], 0, ',', ' ') }} FCFA</strong></span>
        @endforeach
    </div>
</div>

<div class="uk-grid-small uk-child-width-1-2@l uk-margin-top" uk-grid>
    <div class="uk-card uk-card-default" style="padding:24px;">
        <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Produits les plus vendus</h2>
        <div class="uk-margin-small-top" style="height: 280px;">
            <canvas id="chart-products"></canvas>
        </div>
    </div>

    <div class="uk-card uk-card-default" style="padding:24px;">
        <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Ventes par canal</h2>
        @if($salesByChannel->isNotEmpty())
            <div class="uk-margin-small-top" style="height: 280px;">
                <canvas id="chart-channels"></canvas>
            </div>
            <ul class="uk-margin-small-top" style="border-top:1px solid rgba(29,138,78,.1); padding-top:16px; font-size:.75rem; color:rgba(31,35,40,.6); list-style:none; margin-left:0;">
                @foreach($salesByChannel as $row)
                    <li class="uk-flex uk-flex-between" style="margin-top:4px;"><span>{{ $row['label'] }}</span><strong style="color:#1F2328;">{{ number_format($row['value'], 0, ',', ' ') }} FCFA</strong></li>
                @endforeach
            </ul>
        @else
            <p class="uk-text-small uk-text-muted uk-margin-small-top">Pas encore de données de vente.</p>
        @endif
    </div>
</div>

<div class="uk-grid-small uk-child-width-1-2@l uk-margin-top" uk-grid>
    <div class="uk-card uk-card-default" style="padding:24px;">
        <div class="uk-flex uk-flex-middle uk-flex-between">
            <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Top fournisseurs (montant achats)</h2>
            <a href="{{ route('admin.fournisseurs.index') }}" style="font-size:.875rem; font-weight:600; color:#1D8A4E;">Voir tout →</a>
        </div>
        <div class="uk-margin-small-top" style="height: 240px;">
            <canvas id="chart-suppliers"></canvas>
        </div>
    </div>

    <div class="uk-card uk-card-default" style="padding:24px;">
        <div class="uk-flex uk-flex-middle uk-flex-between">
            <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Produits dormants (60 jours sans vente)</h2>
            <a href="{{ route('admin.produits.index') }}" style="font-size:.875rem; font-weight:600; color:#1D8A4E;">Voir tout →</a>
        </div>
        <table class="uk-table uk-table-divider uk-table-middle uk-table-small uk-margin-small-top">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th class="uk-text-right">Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dormantProducts as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td class="uk-text-right" style="font-weight:600;">{{ $product->stock_quantity }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="uk-text-center uk-text-muted" style="padding:24px 0;">Aucun produit dormant.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    window.__analyticsData = {
        revenue: @json($revenueByMonth->values()),
        products: @json($topProducts),
        channels: @json($salesByChannel),
        suppliers: @json($topSuppliers->map(fn ($s) => ['label' => $s->supplier->name ?? '—', 'value' => (float) $s->spend])),
    };
</script>
@endsection
