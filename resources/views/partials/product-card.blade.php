@php
    $image = $product->images->first()?->path;
    $rating = $product->averageRating();
    $showProPrice = auth()->user() && auth()->user()->isApprovedB2B() && $product->professional_price;
@endphp
<div class="product-card group flex h-full flex-col border border-terroir-dark/[0.06]">
    <div class="relative">
        <a href="{{ route('produits.show', $product->slug) }}" class="block">
            <div class="aspect-square overflow-hidden bg-terroir-cream p-5">
                @if($image)
                    <img src="{{ asset('fichiers/'.$image) }}" alt="{{ $product->name }}" class="h-full w-full object-contain transition duration-700 ease-out group-hover:scale-[1.04]">
                @else
                    <div class="flex h-full items-center justify-center text-5xl">🌿</div>
                @endif
            </div>

            <div class="absolute left-3 top-3 flex flex-col gap-1.5">
                @if($product->isOnPromo())
                    <span class="rounded-full bg-terroir-terracotta px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-white shadow-soft">Promo</span>
                @endif
                @if($product->is_new)
                    <span class="rounded-full bg-terroir-gold px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-terroir-dark shadow-soft">Nouveau</span>
                @endif
                @if($product->isBestSeller())
                    <span class="rounded-full bg-terroir-green px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-white shadow-soft">Meilleure vente</span>
                @endif
            </div>

            @unless($product->inStock())
                <div class="absolute inset-0 flex items-center justify-center bg-terroir-dark/60 backdrop-blur-[1px]">
                    <span class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-terroir-dark">Rupture de stock</span>
                </div>
            @endunless
        </a>

        @php $isWishlisted = $product->wishlistedBy(auth()->user()); @endphp
        <form action="{{ route('produits.favori.toggle', $product) }}" method="POST" class="absolute right-3 top-3">
            @csrf
            <button type="submit" aria-label="{{ $isWishlisted ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"
                class="flex h-8 w-8 items-center justify-center rounded-full bg-white/95 text-base shadow-soft backdrop-blur transition hover:scale-110 {{ $isWishlisted ? 'text-terroir-terracotta' : 'text-terroir-dark/40' }}">
                <span class="material-symbols-outlined{{ $isWishlisted ? ' is-filled' : '' }}" style="font-size: 1.05rem;">favorite</span>
            </button>
        </form>
    </div>

    <div class="flex flex-1 flex-col p-5">
        @if($product->category)
            <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-terroir-terracotta/90">{{ $product->category->name }}</p>
        @endif

        <a href="{{ route('produits.show', $product->slug) }}" class="text-terroir-dark">
            <h3 class="mt-1.5 font-display text-[1.05rem] font-semibold leading-snug transition group-hover:text-terroir-green">{{ $product->name }}</h3>
        </a>

        @if($rating || $product->producer)
            <div class="mt-1.5 flex items-center gap-1.5 text-xs text-terroir-dark/45">
                @if($rating)
                    <span class="flex items-center gap-0.5 text-terroir-gold">
                        <span class="material-symbols-outlined is-filled text-sm">star</span>
                        <span class="font-semibold text-terroir-dark/70">{{ number_format($rating, 1) }}</span>
                    </span>
                @endif
                @if($rating && $product->producer)
                    <span class="text-terroir-dark/20">·</span>
                @endif
                @if($product->producer)
                    <span class="truncate">{{ $product->producer->name }}</span>
                @endif
            </div>
        @endif

        @if($product->inStock() && $product->stock_quantity <= $product->stock_alert_threshold)
            <p class="mt-2 text-xs font-semibold text-terroir-terracotta">Plus que {{ $product->stock_quantity }} en stock</p>
        @endif

        <div class="mt-4 flex flex-1 items-end justify-between gap-3 border-t border-terroir-dark/[0.06] pt-4">
            <div class="min-w-0">
                @if($product->isOnPromo())
                    <span class="block text-xs text-terroir-dark/35 line-through">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                    <span class="block font-display text-lg font-bold leading-tight text-terroir-terracotta">{{ number_format($product->promo_price, 0, ',', ' ') }} FCFA</span>
                @else
                    <span class="block font-display text-lg font-bold leading-tight text-terroir-dark">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                @endif
                <span class="text-xs text-terroir-dark/40">/ {{ $product->unit }}</span>
                @if($showProPrice)
                    <span class="mt-1 block text-xs font-semibold text-terroir-green">Tarif pro : {{ number_format($product->professional_price, 0, ',', ' ') }} FCFA</span>
                @endif
            </div>

            <form action="{{ route('panier.add', $product) }}" method="POST" class="shrink-0">
                @csrf
                <button type="submit" @if(!$product->inStock()) disabled @endif
                    class="flex h-11 w-11 items-center justify-center rounded-full bg-terroir-green text-lg text-white shadow-soft transition duration-300 hover:-translate-y-0.5 hover:bg-terroir-dark hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:translate-y-0"
                    aria-label="Ajouter au panier">
                    <span class="material-symbols-outlined">add_shopping_cart</span>
                </button>
            </form>
        </div>
    </div>
</div>
