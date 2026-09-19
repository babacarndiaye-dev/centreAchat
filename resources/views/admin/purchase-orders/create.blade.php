@extends('layouts.admin')

@section('title', 'Nouveau bon de commande')

@section('content')
@php
    $initialRows = $purchaseRequest
        ? $purchaseRequest->items->map(fn ($item) => ['product_id' => $item->product_id, 'quantity' => $item->quantity, 'unit_price' => 0])->values()
        : collect([['product_id' => '', 'quantity' => 1, 'unit_price' => 0]]);
@endphp
<div class="uk-card uk-card-default" style="max-width:64rem; padding:32px;" x-data="{
    rows: {{ $initialRows->toJson() }},
    products: {{ $products->map(fn ($p) => ['id' => $p->id, 'name' => $p->name])->toJson() }},
    addRow() { this.rows.push({ product_id: '', quantity: 1, unit_price: 0 }) },
    removeRow(i) { this.rows.splice(i, 1) },
    total() { return this.rows.reduce((sum, r) => sum + (Number(r.quantity) || 0) * (Number(r.unit_price) || 0), 0) }
}">
    @if($purchaseRequest)
        <div class="uk-margin-bottom" style="border-radius:8px; background:#F7F8F5; padding:12px 16px; font-size:.875rem;">
            Créé à partir de la demande d'achat <strong>{{ $purchaseRequest->reference }}</strong>.
        </div>
    @endif

    <form action="{{ route('admin.bons-commande.store') }}" method="POST">
        @csrf
        @if($purchaseRequest)
            <input type="hidden" name="purchase_request_id" value="{{ $purchaseRequest->id }}">
        @endif

        <div class="uk-grid-small uk-child-width-1-3@s" uk-grid>
            <div>
                <label class="uk-form-label" for="supplier_id">Fournisseur</label>
                <select id="supplier_id" name="supplier_id" required class="uk-select">
                    <option value="">Choisir...</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="uk-form-label" for="order_date">Date de commande</label>
                <input type="date" id="order_date" name="order_date" value="{{ old('order_date', now()->format('Y-m-d')) }}" required class="uk-input">
            </div>
            <div>
                <label class="uk-form-label" for="expected_date">Livraison attendue</label>
                <input type="date" id="expected_date" name="expected_date" class="uk-input">
            </div>
        </div>

        <div class="uk-margin-top" style="border-top:1px solid rgba(31,35,40,.08); padding-top:24px;">
            <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Articles commandés</h3>

            <div class="uk-margin-small-top" style="display:flex; flex-direction:column; gap:12px;">
                <template x-for="(row, i) in rows" :key="i">
                    <div class="uk-flex uk-flex-wrap uk-flex-middle" style="gap:12px;">
                        <select :name="'product_id[' + i + ']'" x-model="row.product_id" required class="uk-select" style="flex:1; min-width:200px;">
                            <option value="">Produit...</option>
                            <template x-for="p in products" :key="p.id">
                                <option :value="p.id" x-text="p.name" :selected="row.product_id == p.id"></option>
                            </template>
                        </select>
                        <input type="number" :name="'quantity[' + i + ']'" x-model.number="row.quantity" min="1" placeholder="Qté" required class="uk-input" style="width:6rem;">
                        <input type="number" step="0.01" :name="'unit_price[' + i + ']'" x-model.number="row.unit_price" min="0" placeholder="Prix unitaire" required class="uk-input" style="width:8rem;">
                        <button type="button" @click="removeRow(i)" style="color:#E8604F; font-weight:600; background:none; border:none; cursor:pointer;" aria-label="Retirer">✕</button>
                    </div>
                </template>
            </div>

            <button type="button" @click="addRow()" class="uk-button uk-button-default uk-margin-top">+ Ajouter un article</button>

            <div class="uk-margin-top" style="text-align:right; font-size:1.125rem; font-weight:700; color:#1D8A4E;">
                Total : <span x-text="total().toLocaleString('fr-FR') + ' FCFA'"></span>
            </div>
        </div>

        <div class="uk-margin-top">
            <label class="uk-form-label" for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="2" class="uk-textarea"></textarea>
        </div>

        <div class="uk-flex uk-margin-top" style="gap:12px;">
            <button type="submit" class="uk-button uk-button-primary">Créer le bon de commande</button>
            <a href="{{ route('admin.bons-commande.index') }}" class="uk-button uk-button-default">Annuler</a>
        </div>
    </form>
</div>
@endsection
