@extends('layouts.app')

@section('title', $product->name." — DIABA HOTEL Produits du Sénégal (D.H.P.S)")
@section('meta_description', str(strip_tags($product->short_description ?? $product->description ?? ''))->limit(155))

@section('content')
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">

    <nav class="text-xs text-terroir-dark/50">
        <a href="{{ route('accueil') }}" class="hover:text-terroir-terracotta">Accueil</a> /
        <a href="{{ route('produits.index') }}" class="hover:text-terroir-terracotta">Produits</a> /
        @if($product->category)
            <a href="{{ route('produits.index', ['categorie' => $product->category->slug]) }}" class="hover:text-terroir-terracotta">{{ $product->category->name }}</a> /
        @endif
        <span class="text-terroir-dark">{{ $product->name }}</span>
    </nav>

    <div class="mt-8 grid gap-12 lg:grid-cols-2">
        <div x-data="{ active: 0 }" class="reveal is-visible">
            <div class="aspect-square overflow-hidden rounded-xl2 bg-white shadow-soft">
                @if($product->images->isNotEmpty())
                    @foreach($product->images as $i => $image)
                        <img x-show="active === {{ $i }}" src="{{ asset('fichiers/'.$image->path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                    @endforeach
                @else
                    <div class="flex h-full w-full items-center justify-center text-8xl">🌿</div>
                @endif
            </div>
            @if($product->images->count() > 1)
                <div class="mt-4 flex gap-3">
                    @foreach($product->images as $i => $image)
                        <button @click="active = {{ $i }}" class="h-20 w-20 overflow-hidden rounded-lg border-2 transition" :class="active === {{ $i }} ? 'border-terroir-green' : 'border-transparent opacity-70'">
                            <img src="{{ asset('fichiers/'.$image->path) }}" alt="" class="h-full w-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="reveal is-visible">
            @if($product->category)
                <p class="section-eyebrow">{{ $product->category->name }}</p>
            @endif
            <h1 class="mt-2 font-display text-3xl font-semibold text-terroir-dark">{{ $product->name }}</h1>

            @if($product->averageRating())
                <div class="mt-2">
                    @include('partials.star-rating', ['rating' => $product->averageRating(), 'count' => $product->reviewsCount()])
                </div>
            @endif

            @if($product->producer)
                <p class="mt-2 text-sm text-terroir-dark/60">Producteur : <a href="{{ route('producteurs.show', $product->producer->slug) }}" class="font-semibold text-terroir-green hover:underline">{{ $product->producer->name }}</a></p>
            @endif
            @if($product->origin)
                <p class="mt-1 text-sm text-terroir-dark/60">Origine : {{ $product->origin }}</p>
            @endif

            <div class="mt-6 flex flex-wrap items-baseline gap-3">
                @if($product->isOnPromo())
                    <span class="font-display text-3xl font-bold text-terroir-terracotta">{{ number_format($product->promo_price, 0, ',', ' ') }} FCFA</span>
                    <span class="text-lg text-terroir-dark/40 line-through">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                @else
                    <span class="font-display text-3xl font-bold text-terroir-green">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                @endif
                <span class="text-sm text-terroir-dark/50">/ {{ $product->unit }}</span>
                <span class="text-sm text-terroir-dark/40">(~{{ \App\Support\Currency::formatEur($product->currentPrice()) }} indicatif)</span>
            </div>

            @if($product->professional_price || $product->wholesale_price)
                <div class="mt-3 flex flex-wrap gap-4 text-xs text-terroir-dark/60">
                    @if($product->professional_price)
                        <span>Tarif pro : <strong>{{ number_format($product->professional_price, 0, ',', ' ') }} FCFA</strong></span>
                    @endif
                    @if($product->wholesale_price)
                        <span>Tarif en gros : <strong>{{ number_format($product->wholesale_price, 0, ',', ' ') }} FCFA</strong></span>
                    @endif
                </div>
            @endif

            <p class="mt-6 leading-relaxed text-terroir-dark/80">{{ $product->short_description }}</p>

            <div class="mt-6">
                @if($product->inStock())
                    <span class="inline-flex items-center gap-2 text-sm font-medium text-terroir-green"><span class="h-2 w-2 rounded-full bg-terroir-green"></span> En stock</span>
                @else
                    <span class="inline-flex items-center gap-2 text-sm font-medium text-terroir-terracotta"><span class="h-2 w-2 rounded-full bg-terroir-terracotta"></span> Rupture de stock</span>
                @endif
            </div>

            <form action="{{ route('panier.add', $product) }}" method="POST" class="mt-8 flex flex-wrap items-center gap-4">
                @csrf
                <input type="number" name="quantity" value="1" min="1" max="500" class="input w-24" @if(!$product->inStock()) disabled @endif>
                <button type="submit" class="btn-primary" @if(!$product->inStock()) disabled @endif>Ajouter au panier</button>
                <a href="{{ route('compte.devis.create') }}" class="btn-outline">Demander un devis</a>
            </form>

            @if($product->description)
                <div class="mt-10 border-t border-terroir-green/10 pt-8">
                    <h2 class="font-display text-lg font-semibold text-terroir-dark">Description</h2>
                    <div class="mt-3 leading-relaxed text-terroir-dark/80">{!! nl2br(e($product->description)) !!}</div>
                </div>
            @endif

            <dl class="mt-8 grid grid-cols-2 gap-4 border-t border-terroir-green/10 pt-8 text-sm">
                <div><dt class="text-terroir-dark/50">Référence</dt><dd class="font-medium">{{ $product->reference }}</dd></div>
                @if($product->weight)
                    <div><dt class="text-terroir-dark/50">Poids</dt><dd class="font-medium">{{ $product->weight }} kg</dd></div>
                @endif
            </dl>
        </div>
    </div>

    @php $productReviews = $product->approvedReviews()->with('user')->get(); @endphp
    <div class="mt-16 border-t border-terroir-green/10 pt-10">
        <h2 class="section-title">Avis clients</h2>

        @if($product->averageRating())
            <div class="mt-3">
                @include('partials.star-rating', ['rating' => $product->averageRating(), 'count' => $product->reviewsCount(), 'size' => 'text-lg'])
            </div>
        @endif

        @auth
            @if($product->purchasedBy(auth()->user()) && ! $product->reviewedBy(auth()->user()))
                <form action="{{ route('produits.avis.store', $product) }}" method="POST" class="card mt-6 max-w-lg p-6" x-data="{ rating: 5 }">
                    @csrf
                    <label class="label">Votre note</label>
                    <div class="flex gap-1">
                        @for($s = 1; $s <= 5; $s++)
                            <button type="button" @click="rating = {{ $s }}" class="text-terroir-gold">
                                <span class="material-symbols-outlined text-2xl" :class="rating >= {{ $s }} ? 'is-filled' : ''">star</span>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" x-model="rating">
                    <div class="mt-4">
                        <label class="label" for="comment">Votre commentaire (optionnel)</label>
                        <textarea id="comment" name="comment" rows="3" class="input"></textarea>
                    </div>
                    <button type="submit" class="btn-primary mt-4">Publier mon avis</button>
                </form>
            @endif
        @else
            <p class="mt-4 text-sm text-terroir-dark/60"><a href="{{ route('login') }}" class="font-semibold text-terroir-green hover:underline">Connectez-vous</a> pour laisser un avis si vous avez déjà commandé ce produit.</p>
        @endauth

        @if($productReviews->isNotEmpty())
            <div class="mt-8 space-y-6">
                @foreach($productReviews as $review)
                    <div class="border-b border-terroir-green/10 pb-6">
                        @include('partials.star-rating', ['rating' => $review->rating])
                        @if($review->comment)
                            <p class="mt-2 text-sm leading-relaxed text-terroir-dark/80">{{ $review->comment }}</p>
                        @endif
                        <p class="mt-2 text-xs text-terroir-dark/40">{{ $review->user->name }} — {{ $review->created_at->translatedFormat('d F Y') }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="mt-6 text-sm text-terroir-dark/50">Aucun avis pour le moment.</p>
        @endif
    </div>

    @if($related->isNotEmpty())
        <div class="mt-24">
            <h2 class="section-title text-center">Produits similaires</h2>
            <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($related as $item)
                    @include('partials.product-card', ['product' => $item])
                @endforeach
            </div>
        </div>
    @endif

</section>
@endsection
