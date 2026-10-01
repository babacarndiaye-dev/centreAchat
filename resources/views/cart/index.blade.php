@extends('layouts.app')

@section('title', "Votre panier — DIABA HOTEL Produits du Sénégal (D.H.P.S)")

@section('content')
<section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
    <h1 class="section-title text-center">Votre panier</h1>

    @if($items->isEmpty())
        <div class="mt-16 text-center text-terroir-dark/60">
            <p class="text-6xl">🛒</p>
            <p class="mt-4">Votre panier est vide pour le moment.</p>
            <a href="{{ route('produits.index') }}" class="btn-primary mt-6">Découvrir nos produits</a>
        </div>
    @else
        <div class="mt-10 space-y-4">
            @foreach($items as $item)
                <div class="card flex flex-wrap items-center gap-4 p-4">
                    <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg bg-terroir-cream">
                        @if($image = $item->product->images->first())
                            <img src="{{ asset('fichiers/'.$image->path) }}" alt="" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-2xl">🌿</div>
                        @endif
                    </div>

                    <div class="min-w-[180px] flex-1">
                        <a href="{{ route('produits.show', $item->product->slug) }}" class="font-semibold text-terroir-dark hover:text-terroir-terracotta">{{ $item->product->name }}</a>
                        <p class="text-sm text-terroir-dark/50">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA / {{ $item->product->unit }}</p>
                    </div>

                    <form action="{{ route('panier.update', $item->product) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="0" max="500" class="input w-20" onchange="this.form.submit()">
                    </form>

                    <div class="w-28 text-right font-semibold text-terroir-green">{{ number_format($item->total, 0, ',', ' ') }} FCFA</div>

                    <form action="{{ route('panier.remove', $item->product) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-lg text-terroir-dark/40 transition hover:text-terroir-terracotta" aria-label="Retirer">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="card mt-10 flex flex-col items-end gap-4 p-6">
            <div class="flex w-full max-w-xs items-center justify-between text-lg">
                <span class="font-medium text-terroir-dark/70">Sous-total</span>
                <span class="font-bold text-terroir-green">{{ number_format($subtotal, 0, ',', ' ') }} FCFA</span>
            </div>
            <a href="{{ route('commande.index') }}" class="btn-primary w-full max-w-xs justify-center">Passer la commande</a>
            <a href="{{ route('produits.index') }}" class="text-sm text-terroir-dark/60 hover:text-terroir-terracotta">← Continuer mes achats</a>
        </div>
    @endif
</section>
@endsection
