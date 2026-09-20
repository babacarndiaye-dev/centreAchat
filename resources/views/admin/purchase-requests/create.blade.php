@extends('layouts.admin')

@section('title', "Nouvelle demande d'achat")

@section('content')
<div class="admin-card max-w-3xl">
    <form action="{{ route('admin.demandes-achat.store') }}" method="POST">
        @csrf

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label" for="needed_by_date">Besoin pour le (optionnel)</label>
                <input type="date" id="needed_by_date" name="needed_by_date" value="{{ old('needed_by_date') }}" class="input">
            </div>
        </div>

        <div class="mt-4">
            <label class="label" for="reason">Motif de la demande</label>
            <textarea id="reason" name="reason" rows="2" class="input">{{ old('reason') }}</textarea>
        </div>

        <div class="mt-6 border-t border-terroir-dark/10 pt-6">
            <h3 class="font-display text-base font-semibold">Produits nécessaires</h3>
            <p class="text-sm text-terroir-dark/50">Indiquez une quantité pour chaque produit concerné, laissez à 0 sinon.</p>

            @error('products')
                <p class="mt-1.5 text-xs text-terroir-terracotta">{{ $message }}</p>
            @enderror

            <div class="mt-4 flex max-h-[420px] flex-col gap-2 overflow-y-auto pr-2">
                @foreach($products as $product)
                    <div class="flex items-center justify-between gap-4 rounded-lg bg-terroir-cream/80 px-4 py-2.5">
                        <div>
                            <p class="text-sm font-semibold">{{ $product->name }}</p>
                            <p class="text-sm text-terroir-dark/50">Stock actuel : {{ $product->stock_quantity }} {{ $product->unit }}</p>
                        </div>
                        <input type="number" name="products[{{ $product->id }}]" value="{{ old('products.'.$product->id, 0) }}" min="0" class="input w-28 text-right">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <button type="submit" name="submit_for_validation" value="0" class="btn-outline">Enregistrer en brouillon</button>
            <button type="submit" name="submit_for_validation" value="1" class="btn-primary">Soumettre pour validation</button>
            <a href="{{ route('admin.demandes-achat.index') }}" class="btn-outline">Annuler</a>
        </div>
    </form>
</div>
@endsection
