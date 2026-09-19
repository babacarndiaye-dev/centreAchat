@extends('layouts.app')

@section('title', 'Programmer une commande récurrente')

@section('content')
<section class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
    <span class="section-eyebrow">Espace professionnel</span>
    <h1 class="section-title mt-2">Programmer une commande récurrente</h1>
    <p class="mt-3 text-terroir-dark/70">Automatisez votre réapprovisionnement à fréquence régulière.</p>

    <form action="{{ route('compte.commandes-recurrentes.store') }}" method="POST" class="card mt-8 p-8">
        @csrf

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="label" for="frequency">Fréquence</label>
                <select id="frequency" name="frequency" required class="input">
                    @foreach(\App\Models\RecurringOrder::FREQUENCIES as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="payment_method">Mode de paiement</label>
                <select id="payment_method" name="payment_method" required class="input">
                    <option value="especes">Espèces à la livraison</option>
                    <option value="wave">Wave</option>
                    <option value="orange_money">Orange Money</option>
                    @if(auth()->user()->isApprovedB2B() && auth()->user()->credit_limit)
                        <option value="credit">Paiement à crédit</option>
                    @endif
                </select>
            </div>
        </div>

        <div class="mt-5">
            <label class="label" for="delivery_address">Adresse de livraison</label>
            <textarea id="delivery_address" name="delivery_address" rows="2" required class="input">{{ old('delivery_address') }}</textarea>
        </div>

        <div class="mt-5">
            <label class="label" for="city">Ville</label>
            <input type="text" id="city" name="city" value="{{ old('city', 'Mbour') }}" required class="input">
        </div>

        <div class="mt-6 border-t border-terroir-green/10 pt-6">
            <h3 class="font-display text-base font-semibold">Produits à commander automatiquement</h3>

            @error('products')
                <p class="mt-2 text-xs text-terroir-terracotta">{{ $message }}</p>
            @enderror

            <div class="mt-4 max-h-[380px] space-y-2 overflow-y-auto pr-2">
                @foreach($products as $product)
                    <div class="flex items-center justify-between gap-4 rounded-lg bg-terroir-cream/60 px-4 py-2.5">
                        <p class="text-sm font-medium">{{ $product->name }}</p>
                        <input type="number" name="products[{{ $product->id }}]" value="0" min="0" class="input w-28 text-right">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6">
            <label class="label" for="notes">Notes (optionnel)</label>
            <textarea id="notes" name="notes" rows="2" class="input"></textarea>
        </div>

        <div class="mt-8 flex gap-3">
            <button type="submit" class="btn-primary">Programmer</button>
            <a href="{{ route('compte.commandes-recurrentes.index') }}" class="btn-outline">Annuler</a>
        </div>
    </form>
</section>
@endsection
