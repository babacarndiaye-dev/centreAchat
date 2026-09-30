@extends('layouts.app')

@section('title', $producer->name." — DIABA HOTEL")

@section('content')
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <nav class="text-xs text-terroir-dark/50">
        <a href="{{ route('accueil') }}" class="hover:text-terroir-terracotta">Accueil</a> /
        <a href="{{ route('producteurs.index') }}" class="hover:text-terroir-terracotta">Producteurs</a> /
        <span class="text-terroir-dark">{{ $producer->name }}</span>
    </nav>

    <div class="mt-8 grid gap-10 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <div class="aspect-square overflow-hidden rounded-xl2 bg-terroir-cream shadow-soft">
                @if($producer->photo)
                    <img src="{{ asset('fichiers/'.$producer->photo) }}" alt="{{ $producer->name }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full w-full items-center justify-center text-8xl">🧑‍🌾</div>
                @endif
            </div>
        </div>
        <div class="lg:col-span-2">
            <h1 class="font-display text-3xl font-semibold text-terroir-dark">{{ $producer->name }}</h1>
            @if($producer->region)
                <p class="mt-2 text-sm text-terroir-dark/60">📍 {{ $producer->region }}</p>
            @endif
            @if($producer->description)
                <p class="mt-6 leading-relaxed text-terroir-dark/80">{{ $producer->description }}</p>
            @endif
        </div>
    </div>

    @if($producer->products->isNotEmpty())
        <div class="mt-20">
            <h2 class="section-title text-center">Produits de {{ $producer->name }}</h2>
            <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($producer->products as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    @endif
</section>
@endsection
