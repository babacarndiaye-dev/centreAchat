@extends('layouts.admin')

@section('title', 'Nouvelle vente')

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un produit ou une référence..." class="input flex-1">
            <button type="submit" class="btn-outline">Rechercher</button>
        </form>

        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
            @foreach($products as $product)
                <form action="{{ route('admin.pos.ventes.add', $product) }}" method="POST">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button
                        type="submit"
                        @if(!$product->inStock()) disabled @endif
                        class="admin-card flex w-full flex-col items-start gap-1 border-0 p-4 text-left transition hover:-translate-y-0.5 hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:translate-y-0"
                    >
                        <span class="text-sm font-semibold">{{ $product->name }}</span>
                        <span class="text-sm text-terroir-dark/50">{{ $product->reference }} — Stock : {{ $product->stock_quantity }}</span>
                        <span class="mt-1 font-bold text-terroir-green">{{ number_format($product->priceFor(\App\Support\PosCart::customer()), 0, ',', ' ') }} FCFA</span>
                    </button>
                </form>
            @endforeach
        </div>
    </div>

    <div>
        <div class="admin-card">
            <h3 class="font-display text-base font-semibold">Vente en cours</h3>

            <form action="{{ route('admin.pos.ventes.customer') }}" method="POST" class="mt-3">
                @csrf
                <select name="user_id" onchange="this.form.submit()" class="input">
                    <option value="">Client comptoir (sans compte)</option>
                    @foreach(\App\Models\User::whereIn('user_type', \App\Models\User::B2B_TYPES)->where('b2b_status', 'valide')->get() as $u)
                        <option value="{{ $u->id }}" @selected($customer?->id === $u->id)>{{ $u->name }} ({{ $u->company_name }})</option>
                    @endforeach
                </select>
            </form>

            <div class="mt-4 flex flex-col gap-2">
                @forelse($items as $item)
                    <div class="flex items-center gap-2 text-sm">
                        <span class="flex-1">{{ $item->product->name }}</span>
                        <form action="{{ route('admin.pos.ventes.update', $item->product) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="0" onchange="this.form.submit()" class="input w-16 text-right">
                        </form>
                        <span class="w-24 text-right font-semibold">{{ number_format($item->total, 0, ',', ' ') }}</span>
                        <form action="{{ route('admin.pos.ventes.remove', $item->product) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-terroir-terracotta">✕</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-terroir-dark/50">Panier vide.</p>
                @endforelse
            </div>

            <div class="mt-4 border-t border-terroir-dark/10 pt-4 text-right text-lg font-bold text-terroir-green">
                {{ number_format($subtotal, 0, ',', ' ') }} FCFA
            </div>

            @if($items->isNotEmpty())
                <form action="{{ route('admin.pos.ventes.store') }}" method="POST" x-data="{
                    payments: [{ method: 'especes', amount: {{ $subtotal }} }],
                    addPayment() { this.payments.push({ method: 'especes', amount: 0 }) },
                    removePayment(i) { this.payments.splice(i, 1) },
                    total() { return this.payments.reduce((s, p) => s + (Number(p.amount) || 0), 0) }
                }" class="mt-8 border-t border-terroir-dark/10 pt-4">
                    @csrf
                    <input type="text" name="customer_name" placeholder="Nom du client (optionnel)" class="input" value="{{ $customer->name ?? '' }}">

                    <div class="mt-3 flex flex-col gap-2">
                        <template x-for="(p, i) in payments" :key="i">
                            <div class="flex gap-2">
                                <select :name="'payments[' + i + '][method]'" x-model="p.method" class="input flex-1">
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method->code }}">{{ $method->name }}</option>
                                    @endforeach
                                </select>
                                <input type="number" step="0.01" :name="'payments[' + i + '][amount]'" x-model.number="p.amount" class="input w-32">
                                <button type="button" @click="removePayment(i)" class="text-terroir-terracotta">✕</button>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addPayment()" class="mt-3 block text-sm font-semibold text-terroir-green">+ Ajouter un paiement</button>

                    <p class="mt-3 text-sm">Total réglé : <span class="font-semibold" x-text="total().toLocaleString('fr-FR') + ' FCFA'"></span></p>

                    <button type="submit" class="btn-primary mt-4 w-full justify-center">Encaisser</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
