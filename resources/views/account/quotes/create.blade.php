@extends('layouts.app')

@section('title', 'Demander un devis')

@section('content')
<section class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
    <span class="section-eyebrow">Espace professionnel</span>
    <h1 class="section-title mt-2">Demander un devis</h1>
    <p class="mt-3 text-terroir-dark/70">Sélectionnez les produits et quantités souhaités, notre équipe vous enverra une offre tarifaire adaptée.</p>

    <form action="{{ route('compte.devis.store') }}" method="POST" class="card mt-8 p-8">
        @csrf

        @error('products')
            <p class="mb-4 text-sm text-terroir-terracotta">{{ $message }}</p>
        @enderror

        <div class="max-h-[420px] space-y-2 overflow-y-auto pr-2">
            @foreach($products as $product)
                <div class="flex items-center justify-between gap-4 rounded-lg bg-terroir-cream/60 px-4 py-2.5">
                    <p class="text-sm font-medium">{{ $product->name }}</p>
                    <input type="number" name="products[{{ $product->id }}]" value="0" min="0" class="input w-28 text-right">
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            <label class="label" for="notes">Précisions (optionnel)</label>
            <textarea id="notes" name="notes" rows="3" class="input"></textarea>
        </div>

        <div class="mt-8 flex gap-3">
            <button type="submit" class="btn-primary">Envoyer la demande</button>
            <a href="{{ route('compte.devis.index') }}" class="btn-outline">Annuler</a>
        </div>
    </form>
</section>
@endsection
