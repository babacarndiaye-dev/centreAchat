@extends('layouts.admin')

@section('title', 'Session de caisse')

@section('content')
<div class="uk-flex uk-flex-wrap uk-flex-between uk-flex-middle" style="gap:16px;">
    <div>
        <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.5rem;">Caisse #{{ $register->id }}</h2>
        <p class="uk-text-small uk-text-muted">Ouverte par {{ $register->openedBy->name }} le {{ $register->created_at->format('d/m/Y H:i') }}</p>
    </div>
    <div class="uk-flex uk-flex-middle" style="gap:12px;">
        @if($register->status === 'ouverte')
            <span class="uk-label" style="background:rgba(29,138,78,.12); color:#1D8A4E; padding:6px 16px; font-size:.875rem;">Ouverte</span>
            <a href="{{ route('admin.pos.ventes.create') }}" class="uk-button uk-button-primary">Nouvelle vente</a>
        @else
            <span class="uk-label" style="background:rgba(31,35,40,.08); color:rgba(31,35,40,.6); padding:6px 16px; font-size:.875rem;">Fermée</span>
        @endif
    </div>
</div>

<div class="uk-grid-small uk-child-width-1-2 uk-child-width-1-4@l uk-margin-top" uk-grid>
    <div><div class="uk-card uk-card-default" style="padding:20px;"><p class="uk-text-small uk-text-muted">Fond initial</p><p class="uk-margin-small-top" style="font-size:1.25rem; font-weight:700;">{{ number_format($register->opening_float, 0, ',', ' ') }} FCFA</p></div></div>
    <div><div class="uk-card uk-card-default" style="padding:20px;"><p class="uk-text-small uk-text-muted">Ventes espèces</p><p class="uk-margin-small-top" style="font-size:1.25rem; font-weight:700; color:#1D8A4E;">{{ number_format($register->cashSalesTotal(), 0, ',', ' ') }} FCFA</p></div></div>
    <div><div class="uk-card uk-card-default" style="padding:20px;"><p class="uk-text-small uk-text-muted">Mouvements (+/-)</p><p class="uk-margin-small-top" style="font-size:1.25rem; font-weight:700;">{{ number_format($register->encaissementsTotal() - $register->decaissementsTotal(), 0, ',', ' ') }} FCFA</p></div></div>
    <div><div class="uk-card uk-card-default" style="padding:20px;"><p class="uk-text-small uk-text-muted">Solde théorique</p><p class="uk-margin-small-top" style="font-size:1.25rem; font-weight:700; color:#E8604F;">{{ number_format($register->theoreticalCash(), 0, ',', ' ') }} FCFA</p></div></div>
</div>

<div class="uk-grid-small uk-child-width-1-1 uk-child-width-1-2@l uk-margin-large-top" uk-grid>
    <div>
        <div class="uk-card uk-card-default" style="padding:24px;">
            <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Ventes de la session</h3>
            <div class="uk-margin-top" style="display:flex; flex-direction:column; gap:8px;">
                @forelse($sales as $sale)
                    <a href="{{ route('admin.pos.ventes.show', $sale) }}" class="uk-flex uk-flex-between uk-flex-middle uk-text-small" style="border-radius:8px; background:rgba(247,248,245,.6); padding:10px 16px;">
                        <span style="font-weight:600;">{{ $sale->order_number }}</span>
                        <span class="uk-text-muted">{{ $sale->customer_name }}</span>
                        <span style="font-weight:600; color:#1D8A4E;">{{ number_format($sale->total, 0, ',', ' ') }} FCFA</span>
                    </a>
                @empty
                    <p class="uk-text-small uk-text-muted">Aucune vente pour le moment.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div>
        <div class="uk-card uk-card-default" style="padding:24px;">
            <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Mouvements de caisse</h3>
            @if($register->status === 'ouverte')
                <form action="{{ route('admin.pos.caisse.mouvement', $register) }}" method="POST" class="uk-margin-top uk-flex uk-flex-wrap" style="gap:8px;">
                    @csrf
                    <select name="type" required class="uk-select" style="width:10rem;">
                        <option value="encaissement">Encaissement</option>
                        <option value="decaissement">Décaissement</option>
                    </select>
                    <input type="number" step="0.01" name="amount" placeholder="Montant" required class="uk-input" style="width:8rem;">
                    <input type="text" name="reason" placeholder="Motif" required class="uk-input" style="flex:1; min-width:140px;">
                    <button type="submit" class="uk-button uk-button-default">Ajouter</button>
                </form>
            @endif

            <div class="uk-margin-top" style="display:flex; flex-direction:column; gap:8px;">
                @forelse($register->movements as $movement)
                    <div class="uk-flex uk-flex-between uk-flex-middle uk-text-small" style="border-radius:8px; background:rgba(247,248,245,.6); padding:10px 16px;">
                        <span>{{ $movement->reason }}</span>
                        <span style="font-weight:600; {{ $movement->type === 'encaissement' ? 'color:#1D8A4E;' : 'color:#E8604F;' }}">
                            {{ $movement->type === 'encaissement' ? '+' : '-' }}{{ number_format($movement->amount, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                @empty
                    <p class="uk-text-small uk-text-muted">Aucun mouvement.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@if($register->status === 'ouverte')
    <div class="uk-card uk-card-default uk-margin-large-top" style="padding:24px;">
        <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Clôturer la caisse</h3>
        <p class="uk-text-small uk-text-muted uk-margin-small-top">Solde théorique attendu : <strong>{{ number_format($register->theoreticalCash(), 0, ',', ' ') }} FCFA</strong></p>
        <form action="{{ route('admin.pos.caisse.close', $register) }}" method="POST" class="uk-margin-top uk-flex uk-flex-wrap uk-flex-bottom" style="gap:12px;" onsubmit="return confirm('Confirmer la clôture de caisse ?')">
            @csrf
            <div>
                <label class="uk-form-label" for="actual_closing_amount">Montant compté physiquement</label>
                <input type="number" step="0.01" id="actual_closing_amount" name="actual_closing_amount" required class="uk-input" style="width:12rem;">
            </div>
            <button type="submit" class="uk-button uk-button-primary">Clôturer</button>
        </form>
    </div>
@else
    <div class="uk-card uk-card-default uk-margin-large-top" style="padding:24px;" x-data="{ correcting: false }">
        <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Clôture</h3>
        <dl class="uk-grid-small uk-child-width-1-3 uk-margin-small-top uk-text-small" uk-grid>
            <div><dt class="uk-text-muted">Théorique</dt><dd style="font-weight:600;">{{ number_format($register->theoreticalCash(), 0, ',', ' ') }} FCFA</dd></div>
            <div><dt class="uk-text-muted">Compté</dt><dd style="font-weight:600;">{{ number_format($register->actual_closing_amount, 0, ',', ' ') }} FCFA</dd></div>
            <div><dt class="uk-text-muted">Écart</dt><dd style="font-weight:600; {{ $register->variance() != 0 ? 'color:#E8604F;' : 'color:#1D8A4E;' }}">{{ number_format($register->variance(), 0, ',', ' ') }} FCFA</dd></div>
        </dl>
        <p class="uk-text-small uk-text-muted uk-margin-small-top">Clôturée par {{ $register->closedBy?->name }} le {{ optional($register->closed_at)->format('d/m/Y H:i') }}</p>

        <div class="uk-margin-top uk-flex uk-flex-wrap" style="gap:12px; border-top:1px solid rgba(31,35,40,.08); padding-top:16px;">
            <button type="button" @click="correcting = !correcting" class="uk-button uk-button-default" x-text="correcting ? 'Annuler la correction' : 'Corriger les montants'"></button>
            <form action="{{ route('admin.pos.caisse.reopen', $register) }}" method="POST" onsubmit="return confirm('Rouvrir cette caisse pour ajouter un mouvement ou une vente oubliée ? Vous devrez la re-clôturer ensuite.')">
                @csrf
                <button type="submit" class="uk-button uk-button-default" style="color:#E8604F;">Rouvrir la caisse</button>
            </form>
        </div>

        <form x-show="correcting" x-cloak x-transition action="{{ route('admin.pos.caisse.correct', $register) }}" method="POST" class="uk-margin-top uk-flex uk-flex-wrap uk-flex-bottom" style="gap:12px; border-radius:8px; background:rgba(247,248,245,.6); padding:16px;">
            @csrf @method('PATCH')
            <div>
                <label class="uk-form-label" for="opening_float">Fond de caisse initial</label>
                <input type="number" step="0.01" id="opening_float" name="opening_float" value="{{ $register->opening_float }}" required class="uk-input" style="width:10rem;">
            </div>
            <div>
                <label class="uk-form-label" for="actual_closing_amount">Montant compté</label>
                <input type="number" step="0.01" id="actual_closing_amount" name="actual_closing_amount" value="{{ $register->actual_closing_amount }}" required class="uk-input" style="width:10rem;">
            </div>
            <button type="submit" class="uk-button uk-button-primary">Enregistrer la correction</button>
        </form>
        <p class="uk-text-small uk-text-muted uk-margin-small-top" x-show="correcting" x-cloak>Corrige uniquement les montants saisis (ne rouvre pas la caisse aux nouveaux mouvements).</p>
    </div>
@endif
@endsection
