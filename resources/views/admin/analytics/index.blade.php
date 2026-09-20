@extends('layouts.admin')

@section('title', 'Statistiques')

@section('content')
<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
    <div class="admin-card">
        <p class="text-sm text-terroir-dark/50">CA du mois</p>
        <p class="mt-1 text-2xl font-bold text-terroir-green">{{ number_format($revenueThisMonth, 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="admin-card">
        <p class="text-sm text-terroir-dark/50">Panier moyen</p>
        <p class="mt-1 text-2xl font-bold">{{ number_format($averageBasket, 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="admin-card">
        <p class="text-sm text-terroir-dark/50">Valeur du stock</p>
        <p class="mt-1 text-2xl font-bold">{{ number_format($stockValue, 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="admin-card">
        <p class="text-sm text-terroir-dark/50">Ruptures / Stock faible</p>
        <p class="mt-1 text-2xl font-bold {{ $outOfStock > 0 ? 'text-terroir-terracotta' : '' }}">{{ $outOfStock }} / {{ $lowStock }}</p>
    </div>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-2">
    <div class="admin-card">
        <p class="text-sm text-terroir-dark/50">Créances clients (impayés)</p>
        <p class="mt-1 text-2xl font-bold text-terroir-terracotta">{{ number_format($receivables, 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="admin-card">
        <p class="text-sm text-terroir-dark/50">Dettes fournisseurs</p>
        <p class="mt-1 text-2xl font-bold text-terroir-terracotta">{{ number_format($payables, 0, ',', ' ') }} FCFA</p>
    </div>
</div>

<div class="admin-card mt-6">
    <h2 class="font-display text-base font-semibold">Chiffre d'affaires — 6 derniers mois</h2>
    <div class="mt-3" style="height: 280px;">
        <canvas id="chart-revenue"></canvas>
    </div>
    <div class="mt-3 flex flex-wrap gap-6 border-t border-terroir-green/10 pt-4 text-xs text-terroir-dark/60">
        @foreach($revenueByMonth as $row)
            <span>{{ $row['label'] }} : <strong class="text-terroir-dark">{{ number_format($row['value'], 0, ',', ' ') }} FCFA</strong></span>
        @endforeach
    </div>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <div class="admin-card">
        <h2 class="font-display text-base font-semibold">Produits les plus vendus</h2>
        <div class="mt-3" style="height: 280px;">
            <canvas id="chart-products"></canvas>
        </div>
    </div>

    <div class="admin-card">
        <h2 class="font-display text-base font-semibold">Ventes par canal</h2>
        @if($salesByChannel->isNotEmpty())
            <div class="mt-3" style="height: 280px;">
                <canvas id="chart-channels"></canvas>
            </div>
            <ul class="mt-3 space-y-1 border-t border-terroir-green/10 pt-4 text-xs text-terroir-dark/60">
                @foreach($salesByChannel as $row)
                    <li class="flex justify-between"><span>{{ $row['label'] }}</span><strong class="text-terroir-dark">{{ number_format($row['value'], 0, ',', ' ') }} FCFA</strong></li>
                @endforeach
            </ul>
        @else
            <p class="mt-3 text-sm text-terroir-dark/50">Pas encore de données de vente.</p>
        @endif
    </div>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <div class="admin-card">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-base font-semibold">Top fournisseurs (montant achats)</h2>
            <a href="{{ route('admin.fournisseurs.index') }}" class="admin-link">Voir tout →</a>
        </div>
        <div class="mt-3" style="height: 240px;">
            <canvas id="chart-suppliers"></canvas>
        </div>
    </div>

    <div class="admin-card">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-base font-semibold">Produits dormants (60 jours sans vente)</h2>
            <a href="{{ route('admin.produits.index') }}" class="admin-link">Voir tout →</a>
        </div>
        <table class="admin-table mt-3">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th class="text-right">Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dormantProducts as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td class="text-right font-semibold">{{ $product->stock_quantity }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="py-6 text-center text-terroir-dark/40">Aucun produit dormant.</td></tr>
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
