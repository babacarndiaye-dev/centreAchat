@extends('layouts.admin')

@section('title', 'Nouveau bon de commande')

@section('content')
@php
    $initialRows = $purchaseRequest
        ? $purchaseRequest->items->map(fn ($item) => ['product_id' => $item->product_id, 'quantity' => $item->quantity, 'unit_price' => 0])->values()
        : collect([['product_id' => '', 'quantity' => 1, 'unit_price' => 0]]);
@endphp
<div class="admin-card max-w-4xl" x-data="{
    rows: {{ $initialRows->toJson() }},
    products: {{ $products->map(fn ($p) => ['id' => $p->id, 'name' => $p->name])->toJson() }},
    addRow() { this.rows.push({ product_id: '', quantity: 1, unit_price: 0 }) },
    removeRow(i) { this.rows.splice(i, 1) },
    total() { return this.rows.reduce((sum, r) => sum + (Number(r.quantity) || 0) * (Number(r.unit_price) || 0), 0) }
}">
    @if($purchaseRequest)
        <div class="mb-4 rounded-lg bg-terroir-cream px-4 py-3 text-sm">
            Créé à partir de la demande d'achat <strong>{{ $purchaseRequest->reference }}</strong>.
        </div>
    @endif

    <form action="{{ route('admin.bons-commande.store') }}" method="POST">
        @csrf
        @if($purchaseRequest)
            <input type="hidden" name="purchase_request_id" value="{{ $purchaseRequest->id }}">
        @endif

        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="label" for="supplier_id">Fournisseur</label>
                <select id="supplier_id" name="supplier_id" required class="input">
                    <option value="">Choisir...</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="order_date">Date de commande</label>
                <input type="date" id="order_date" name="order_date" value="{{ old('order_date', now()->format('Y-m-d')) }}" required class="input">
            </div>
            <div>
                <label class="label" for="expected_date">Livraison attendue</label>
                <input type="date" id="expected_date" name="expected_date" class="input">
            </div>
        </div>

        <div class="mt-6 border-t border-terroir-dark/10 pt-6">
            <h3 class="font-display text-base font-semibold">Articles commandés</h3>

            <div class="mt-3 flex flex-col gap-3">
                <template x-for="(row, i) in rows" :key="i">
                    <div class="flex flex-wrap items-center gap-3">
                        <select :name="'product_id[' + i + ']'" x-model="row.product_id" required class="input min-w-[200px] flex-1">
                            <option value="">Produit...</option>
                            <template x-for="p in products" :key="p.id">
                                <option :value="p.id" x-text="p.name" :selected="row.product_id == p.id"></option>
                            </template>
                        </select>
                        <input type="number" :name="'quantity[' + i + ']'" x-model.number="row.quantity" min="1" placeholder="Qté" required class="input w-24">
                        <input type="number" step="0.01" :name="'unit_price[' + i + ']'" x-model.number="row.unit_price" min="0" placeholder="Prix unitaire" required class="input w-32">
                        <button type="button" @click="removeRow(i)" class="font-semibold text-terroir-terracotta" aria-label="Retirer">✕</button>
                    </div>
                </template>
            </div>

            <button type="button" @click="addRow()" class="btn-outline mt-4">+ Ajouter un article</button>

            <div class="mt-4 text-right text-lg font-bold text-terroir-green">
                Total : <span x-text="total().toLocaleString('fr-FR') + ' FCFA'"></span>
            </div>
        </div>

        <div class="mt-6">
            <label class="label" for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="2" class="input"></textarea>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="btn-primary">Créer le bon de commande</button>
            <a href="{{ route('admin.bons-commande.index') }}" class="btn-outline">Annuler</a>
        </div>
    </form>
</div>
@endsection
