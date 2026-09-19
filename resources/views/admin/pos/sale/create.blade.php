@extends('layouts.admin')

@section('title', 'Nouvelle vente')

@section('content')
<div uk-grid class="uk-grid-small">
    <div class="uk-width-2-3@l">
        <form method="GET" class="uk-flex" style="gap:8px;">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un produit ou une référence..." class="uk-input" style="flex:1;">
            <button type="submit" class="uk-button uk-button-default">Rechercher</button>
        </form>

        <div class="uk-grid-small uk-child-width-1-2 uk-child-width-1-3@s uk-margin-top" uk-grid>
            @foreach($products as $product)
                <div>
                    <form action="{{ route('admin.pos.ventes.add', $product) }}" method="POST">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="uk-card uk-card-default uk-card-hover" style="display:flex; width:100%; flex-direction:column; align-items:flex-start; gap:4px; padding:16px; text-align:left; border:none; cursor:pointer;" @if(!$product->inStock()) disabled @endif>
                            <span class="uk-text-small" style="font-weight:600;">{{ $product->name }}</span>
                            <span class="uk-text-small uk-text-muted">{{ $product->reference }} — Stock : {{ $product->stock_quantity }}</span>
                            <span class="uk-margin-small-top" style="font-weight:700; color:#1D8A4E;">{{ number_format($product->priceFor(\App\Support\PosCart::customer()), 0, ',', ' ') }} FCFA</span>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <div class="uk-width-1-3@l">
        <div class="uk-card uk-card-default" style="padding:24px;">
            <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Vente en cours</h3>

            <form action="{{ route('admin.pos.ventes.customer') }}" method="POST" class="uk-margin-small-top">
                @csrf
                <select name="user_id" onchange="this.form.submit()" class="uk-select">
                    <option value="">Client comptoir (sans compte)</option>
                    @foreach(\App\Models\User::whereIn('user_type', \App\Models\User::B2B_TYPES)->where('b2b_status', 'valide')->get() as $u)
                        <option value="{{ $u->id }}" @selected($customer?->id === $u->id)>{{ $u->name }} ({{ $u->company_name }})</option>
                    @endforeach
                </select>
            </form>

            <div class="uk-margin-top" style="display:flex; flex-direction:column; gap:8px;">
                @forelse($items as $item)
                    <div class="uk-flex uk-flex-middle uk-text-small" style="gap:8px;">
                        <span style="flex:1;">{{ $item->product->name }}</span>
                        <form action="{{ route('admin.pos.ventes.update', $item->product) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="0" onchange="this.form.submit()" class="uk-input uk-text-right" style="width:4rem;">
                        </form>
                        <span style="width:6rem; text-align:right; font-weight:600;">{{ number_format($item->total, 0, ',', ' ') }}</span>
                        <form action="{{ route('admin.pos.ventes.remove', $item->product) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" style="color:#E8604F; background:none; border:none; cursor:pointer;">✕</button>
                        </form>
                    </div>
                @empty
                    <p class="uk-text-small uk-text-muted">Panier vide.</p>
                @endforelse
            </div>

            <div class="uk-margin-top uk-text-right" style="border-top:1px solid rgba(31,35,40,.08); padding-top:16px; font-size:1.125rem; font-weight:700; color:#1D8A4E;">
                {{ number_format($subtotal, 0, ',', ' ') }} FCFA
            </div>

            @if($items->isNotEmpty())
                <form action="{{ route('admin.pos.ventes.store') }}" method="POST" x-data="{
                    payments: [{ method: 'especes', amount: {{ $subtotal }} }],
                    addPayment() { this.payments.push({ method: 'especes', amount: 0 }) },
                    removePayment(i) { this.payments.splice(i, 1) },
                    total() { return this.payments.reduce((s, p) => s + (Number(p.amount) || 0), 0) }
                }" class="uk-margin-large-top" style="border-top:1px solid rgba(31,35,40,.08); padding-top:16px;">
                    @csrf
                    <input type="text" name="customer_name" placeholder="Nom du client (optionnel)" class="uk-input" value="{{ $customer->name ?? '' }}">

                    <div class="uk-margin-small-top" style="display:flex; flex-direction:column; gap:8px;">
                        <template x-for="(p, i) in payments" :key="i">
                            <div class="uk-flex" style="gap:8px;">
                                <select :name="'payments[' + i + '][method]'" x-model="p.method" class="uk-select" style="flex:1;">
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method->code }}">{{ $method->name }}</option>
                                    @endforeach
                                </select>
                                <input type="number" step="0.01" :name="'payments[' + i + '][amount]'" x-model.number="p.amount" class="uk-input" style="width:8rem;">
                                <button type="button" @click="removePayment(i)" style="color:#E8604F; background:none; border:none; cursor:pointer;">✕</button>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addPayment()" class="uk-margin-small-top uk-text-small" style="display:block; font-weight:600; color:#1D8A4E; background:none; border:none; cursor:pointer;">+ Ajouter un paiement</button>

                    <p class="uk-margin-small-top uk-text-small">Total réglé : <span style="font-weight:600;" x-text="total().toLocaleString('fr-FR') + ' FCFA'"></span></p>

                    <button type="submit" class="uk-button uk-button-primary uk-width-1-1 uk-margin-top">Encaisser</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
