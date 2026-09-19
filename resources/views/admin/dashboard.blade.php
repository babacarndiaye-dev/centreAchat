@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @foreach([
        ['label' => "Chiffre d'affaires (mois)", 'value' => number_format($stats['revenue_month'], 0, ',', ' ').' FCFA', 'icon' => 'payments'],
        ['label' => 'Commandes (mois)', 'value' => $stats['orders_month'], 'icon' => 'receipt_long'],
        ['label' => 'Commandes en attente', 'value' => $stats['orders_pending'], 'icon' => 'pending_actions'],
        ['label' => 'Produits actifs', 'value' => $stats['products_count'], 'icon' => 'inventory_2'],
        ['label' => 'Stock faible', 'value' => $stats['products_low_stock'], 'icon' => 'warning'],
        ['label' => 'Messages non lus', 'value' => $stats['unread_messages'], 'icon' => 'mail'],
    ] as $card)
        <div class="flex items-center gap-4 rounded-xl2 bg-white p-6 shadow-soft">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-terroir-green/10 text-2xl text-terroir-green">
                <span class="material-symbols-outlined">{{ $card['icon'] }}</span>
            </span>
            <div>
                <p class="text-sm text-terroir-dark/50">{{ $card['label'] }}</p>
                <p class="mt-0.5 font-display text-2xl font-bold text-terroir-dark">{{ $card['value'] }}</p>
            </div>
        </div>
    @endforeach
</div>

<div class="mt-6 grid gap-4 lg:grid-cols-2">
    <div class="rounded-xl2 bg-white p-6 shadow-soft">
        <h2 class="font-display text-lg font-semibold text-terroir-dark">Commandes récentes</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-terroir-dark/10 text-left text-xs uppercase tracking-wide text-terroir-dark/40">
                        <th class="pb-2 pr-2">N°</th>
                        <th class="pb-2 pr-2">Client</th>
                        <th class="pb-2 pr-2">Statut</th>
                        <th class="pb-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-terroir-dark/5">
                    @forelse($recentOrders as $order)
                        <tr>
                            <td class="py-2.5 pr-2"><a href="{{ route('admin.commandes.show', $order) }}" class="font-semibold text-terroir-green hover:underline">{{ $order->order_number }}</a></td>
                            <td class="py-2.5 pr-2 text-terroir-dark/70">{{ $order->customer_name }}</td>
                            <td class="py-2.5 pr-2"><span class="rounded-full bg-terroir-cream px-2.5 py-1 text-xs font-medium text-terroir-dark/70">{{ \App\Models\Order::STATUSES[$order->status] ?? $order->status }}</span></td>
                            <td class="py-2.5 text-right font-semibold text-terroir-dark">{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-8 text-center text-terroir-dark/40">Aucune commande.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-xl2 bg-white p-6 shadow-soft">
        <h2 class="font-display text-lg font-semibold text-terroir-dark">Produits en stock faible</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-terroir-dark/10 text-left text-xs uppercase tracking-wide text-terroir-dark/40">
                        <th class="pb-2 pr-2">Produit</th>
                        <th class="pb-2 pr-2 text-right">Stock</th>
                        <th class="pb-2 text-right">Seuil</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-terroir-dark/5">
                    @forelse($lowStockProducts as $product)
                        <tr>
                            <td class="py-2.5 pr-2"><a href="{{ route('admin.produits.edit', $product) }}" class="font-semibold text-terroir-green hover:underline">{{ $product->name }}</a></td>
                            <td class="py-2.5 pr-2 text-right font-semibold text-terroir-terracotta">{{ $product->stock_quantity }}</td>
                            <td class="py-2.5 text-right text-terroir-dark/50">{{ $product->stock_alert_threshold }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-8 text-center text-terroir-dark/40">Aucune alerte de stock.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
