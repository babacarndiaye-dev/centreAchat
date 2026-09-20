@extends('layouts.portal')

@section('title', 'Tableau de bord')

@section('content')
<h1 class="font-display text-2xl font-semibold">Bonjour, {{ $supplier->name }}</h1>
<p class="mt-1 text-sm text-terroir-dark/60">Voici un aperçu de votre relation avec Centrale d'achat.</p>

<div class="mt-6 grid grid-cols-2 gap-5 lg:grid-cols-4">
    <div class="card p-5">
        <p class="text-xs text-terroir-dark/50">À confirmer</p>
        <p class="mt-1 text-2xl font-bold text-terroir-terracotta">{{ $stats['pending_confirmation'] }}</p>
    </div>
    <div class="card p-5">
        <p class="text-xs text-terroir-dark/50">En cours</p>
        <p class="mt-1 text-2xl font-bold">{{ $stats['in_progress'] }}</p>
    </div>
    <div class="card p-5">
        <p class="text-xs text-terroir-dark/50">Total commandé</p>
        <p class="mt-1 text-2xl font-bold">{{ number_format($stats['total_owed'], 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="card p-5">
        <p class="text-xs text-terroir-dark/50">Solde à recevoir</p>
        <p class="mt-1 text-2xl font-bold text-terroir-green">{{ number_format($stats['balance'], 0, ',', ' ') }} FCFA</p>
    </div>
</div>

<div class="mt-8 grid gap-6 lg:grid-cols-2">
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-base font-semibold">Commandes récentes</h2>
            <a href="{{ route('portail.commandes.index') }}" class="text-sm font-semibold text-terroir-green hover:underline">Voir tout</a>
        </div>
        <div class="mt-4 space-y-3">
            @forelse($supplier->purchaseOrders as $po)
                <a href="{{ route('portail.commandes.show', $po) }}" class="flex items-center justify-between rounded-lg bg-terroir-cream/60 px-4 py-3 text-sm hover:bg-terroir-cream">
                    <span class="font-medium">{{ $po->order_number }}</span>
                    <span class="text-terroir-dark/60">{{ \App\Models\PurchaseOrder::STATUSES[$po->status] }}</span>
                    <span class="font-semibold text-terroir-green">{{ number_format($po->total, 0, ',', ' ') }} FCFA</span>
                </a>
            @empty
                <p class="text-sm text-terroir-dark/50">Aucune commande pour le moment.</p>
            @endforelse
        </div>
    </div>

    <div class="card p-6">
        <h2 class="font-display text-base font-semibold">Mes informations</h2>
        <dl class="mt-4 space-y-2 text-sm">
            <div><dt class="text-terroir-dark/50">Responsable</dt><dd class="font-medium">{{ $supplier->contact_name ?? '—' }}</dd></div>
            <div><dt class="text-terroir-dark/50">Téléphone</dt><dd class="font-medium">{{ $supplier->phone }}</dd></div>
            <div><dt class="text-terroir-dark/50">E-mail</dt><dd class="font-medium">{{ $supplier->email ?? '—' }}</dd></div>
            <div><dt class="text-terroir-dark/50">Conditions de paiement</dt><dd class="font-medium">{{ $supplier->payment_terms ?? '—' }}</dd></div>
            <div><dt class="text-terroir-dark/50">Statut</dt><dd class="font-medium">{{ \App\Models\Supplier::STATUSES[$supplier->status] }}</dd></div>
        </dl>
        <p class="mt-4 text-xs text-terroir-dark/40">Pour toute modification de ces informations, contactez votre interlocuteur chez Centrale d'achat.</p>
    </div>
</div>
@endsection
