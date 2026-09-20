@extends('layouts.admin')

@section('title', 'Session de caisse')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <h2 class="font-display text-2xl font-semibold">Caisse #{{ $register->id }}</h2>
        <p class="text-sm text-terroir-dark/50">Ouverte par {{ $register->openedBy->name }} le {{ $register->created_at->format('d/m/Y H:i') }}</p>
    </div>
    <div class="flex items-center gap-3">
        @if($register->status === 'ouverte')
            <span class="admin-badge-success px-4 py-1.5 text-sm">Ouverte</span>
            <a href="{{ route('admin.pos.ventes.create') }}" class="btn-primary">Nouvelle vente</a>
        @else
            <span class="admin-badge-neutral px-4 py-1.5 text-sm">Fermée</span>
        @endif
    </div>
</div>

<div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
    <div class="admin-card"><p class="text-sm text-terroir-dark/50">Fond initial</p><p class="mt-1.5 text-xl font-bold">{{ number_format($register->opening_float, 0, ',', ' ') }} FCFA</p></div>
    <div class="admin-card"><p class="text-sm text-terroir-dark/50">Ventes espèces</p><p class="mt-1.5 text-xl font-bold text-terroir-green">{{ number_format($register->cashSalesTotal(), 0, ',', ' ') }} FCFA</p></div>
    <div class="admin-card"><p class="text-sm text-terroir-dark/50">Mouvements (+/-)</p><p class="mt-1.5 text-xl font-bold">{{ number_format($register->encaissementsTotal() - $register->decaissementsTotal(), 0, ',', ' ') }} FCFA</p></div>
    <div class="admin-card"><p class="text-sm text-terroir-dark/50">Solde théorique</p><p class="mt-1.5 text-xl font-bold text-terroir-terracotta">{{ number_format($register->theoreticalCash(), 0, ',', ' ') }} FCFA</p></div>
</div>

<div class="mt-8 grid gap-6 lg:grid-cols-2">
    <div class="admin-card">
        <h3 class="font-display text-base font-semibold">Ventes de la session</h3>
        <div class="mt-3 flex flex-col gap-2">
            @forelse($sales as $sale)
                <a href="{{ route('admin.pos.ventes.show', $sale) }}" class="flex items-center justify-between gap-3 rounded-lg bg-terroir-cream/60 px-4 py-2.5 text-sm">
                    <span class="font-semibold">{{ $sale->order_number }}</span>
                    <span class="text-terroir-dark/50">{{ $sale->customer_name }}</span>
                    <span class="font-semibold text-terroir-green">{{ number_format($sale->total, 0, ',', ' ') }} FCFA</span>
                </a>
            @empty
                <p class="text-sm text-terroir-dark/50">Aucune vente pour le moment.</p>
            @endforelse
        </div>
    </div>

    <div class="admin-card">
        <h3 class="font-display text-base font-semibold">Mouvements de caisse</h3>
        @if($register->status === 'ouverte')
            <form action="{{ route('admin.pos.caisse.mouvement', $register) }}" method="POST" class="mt-3 flex flex-wrap gap-2">
                @csrf
                <select name="type" required class="input w-40">
                    <option value="encaissement">Encaissement</option>
                    <option value="decaissement">Décaissement</option>
                </select>
                <input type="number" step="0.01" name="amount" placeholder="Montant" required class="input w-32">
                <input type="text" name="reason" placeholder="Motif" required class="input min-w-[140px] flex-1">
                <button type="submit" class="btn-outline">Ajouter</button>
            </form>
        @endif

        <div class="mt-3 flex flex-col gap-2">
            @forelse($register->movements as $movement)
                <div class="flex items-center justify-between gap-3 rounded-lg bg-terroir-cream/60 px-4 py-2.5 text-sm">
                    <span>{{ $movement->reason }}</span>
                    <span class="font-semibold {{ $movement->type === 'encaissement' ? 'text-terroir-green' : 'text-terroir-terracotta' }}">
                        {{ $movement->type === 'encaissement' ? '+' : '-' }}{{ number_format($movement->amount, 0, ',', ' ') }} FCFA
                    </span>
                </div>
            @empty
                <p class="text-sm text-terroir-dark/50">Aucun mouvement.</p>
            @endforelse
        </div>
    </div>
</div>

@if($register->status === 'ouverte')
    <div class="admin-card mt-8">
        <h3 class="font-display text-base font-semibold">Clôturer la caisse</h3>
        <p class="mt-1.5 text-sm text-terroir-dark/50">Solde théorique attendu : <strong>{{ number_format($register->theoreticalCash(), 0, ',', ' ') }} FCFA</strong></p>
        <form action="{{ route('admin.pos.caisse.close', $register) }}" method="POST" class="mt-4 flex flex-wrap items-end gap-3" onsubmit="return confirm('Confirmer la clôture de caisse ?')">
            @csrf
            <div>
                <label class="label" for="actual_closing_amount">Montant compté physiquement</label>
                <input type="number" step="0.01" id="actual_closing_amount" name="actual_closing_amount" required class="input w-48">
            </div>
            <button type="submit" class="btn-primary">Clôturer</button>
        </form>
    </div>
@else
    <div class="admin-card mt-8" x-data="{ correcting: false }">
        <h3 class="font-display text-base font-semibold">Clôture</h3>
        <dl class="mt-3 grid grid-cols-3 gap-4 text-sm">
            <div><dt class="text-terroir-dark/50">Théorique</dt><dd class="font-semibold">{{ number_format($register->theoreticalCash(), 0, ',', ' ') }} FCFA</dd></div>
            <div><dt class="text-terroir-dark/50">Compté</dt><dd class="font-semibold">{{ number_format($register->actual_closing_amount, 0, ',', ' ') }} FCFA</dd></div>
            <div><dt class="text-terroir-dark/50">Écart</dt><dd class="font-semibold {{ $register->variance() != 0 ? 'text-terroir-terracotta' : 'text-terroir-green' }}">{{ number_format($register->variance(), 0, ',', ' ') }} FCFA</dd></div>
        </dl>
        <p class="mt-1.5 text-sm text-terroir-dark/50">Clôturée par {{ $register->closedBy?->name }} le {{ optional($register->closed_at)->format('d/m/Y H:i') }}</p>

        <div class="mt-4 flex flex-wrap gap-3 border-t border-terroir-dark/10 pt-4">
            <button type="button" @click="correcting = !correcting" class="btn-outline" x-text="correcting ? 'Annuler la correction' : 'Corriger les montants'"></button>
            <form action="{{ route('admin.pos.caisse.reopen', $register) }}" method="POST" onsubmit="return confirm('Rouvrir cette caisse pour ajouter un mouvement ou une vente oubliée ? Vous devrez la re-clôturer ensuite.')">
                @csrf
                <button type="submit" class="btn-outline text-terroir-terracotta">Rouvrir la caisse</button>
            </form>
        </div>

        <form x-show="correcting" x-cloak x-transition action="{{ route('admin.pos.caisse.correct', $register) }}" method="POST" class="mt-4 flex flex-wrap items-end gap-3 rounded-lg bg-terroir-cream/60 p-4">
            @csrf @method('PATCH')
            <div>
                <label class="label" for="opening_float">Fond de caisse initial</label>
                <input type="number" step="0.01" id="opening_float" name="opening_float" value="{{ $register->opening_float }}" required class="input w-40">
            </div>
            <div>
                <label class="label" for="actual_closing_amount">Montant compté</label>
                <input type="number" step="0.01" id="actual_closing_amount" name="actual_closing_amount" value="{{ $register->actual_closing_amount }}" required class="input w-40">
            </div>
            <button type="submit" class="btn-primary">Enregistrer la correction</button>
        </form>
        <p class="mt-1.5 text-sm text-terroir-dark/50" x-show="correcting" x-cloak>Corrige uniquement les montants saisis (ne rouvre pas la caisse aux nouveaux mouvements).</p>
    </div>
@endif
@endsection
