@extends('layouts.app')

@section('title', 'Mes favoris')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <span class="section-eyebrow">Mon compte</span>
    <h1 class="section-title mt-2">Mes favoris</h1>

    <a href="{{ route('compte.index') }}" class="mt-4 inline-block text-sm text-terroir-dark/60 hover:text-terroir-terracotta">← Retour à mon compte</a>

    @if($products->isEmpty())
        <p class="mt-10 text-terroir-dark/60">Vous n'avez pas encore de produit favori. Cliquez sur le cœur d'un produit pour l'ajouter ici.</p>
    @else
        <div class="mt-10 grid grid-cols-2 gap-5 md:grid-cols-4">
            @foreach($products as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    @endif
</section>
@endsection
