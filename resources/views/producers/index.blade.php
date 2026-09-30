@extends('layouts.app')

@section('title', "Nos producteurs — DIABA HOTEL")

@section('content')
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="text-center">
        <span class="section-eyebrow">Rencontrez</span>
        <h1 class="section-title mt-2">Nos producteurs partenaires</h1>
        <p class="mx-auto mt-3 max-w-xl text-terroir-dark/70">Des hommes et des femmes passionnés, garants de la qualité et de l'authenticité de nos produits.</p>
    </div>

    <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($producers as $producer)
            <a href="{{ route('producteurs.show', $producer->slug) }}" class="card group overflow-hidden transition hover:-translate-y-1">
                <div class="flex h-48 items-center justify-center overflow-hidden bg-terroir-cream text-6xl">
                    @if($producer->photo)
                        <img src="{{ asset('fichiers/'.$producer->photo) }}" alt="{{ $producer->name }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                    @else
                        🧑‍🌾
                    @endif
                </div>
                <div class="p-6">
                    <h3 class="font-display text-lg font-semibold">{{ $producer->name }}</h3>
                    @if($producer->region)
                        <p class="mt-1 text-sm text-terroir-dark/60">📍 {{ $producer->region }}</p>
                    @endif
                    @if($producer->description)
                        <p class="mt-3 text-sm text-terroir-dark/70 line-clamp-2">{{ $producer->description }}</p>
                    @endif
                    <p class="mt-3 text-xs text-terroir-dark/50">{{ $producer->products_count }} produit{{ $producer->products_count > 1 ? 's' : '' }}</p>
                    <span class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-terroir-green">
                        Voir les produits
                        <span class="material-symbols-outlined text-sm transition group-hover:translate-x-0.5">arrow_forward</span>
                    </span>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-12">{{ $producers->links() }}</div>
</section>
@endsection
