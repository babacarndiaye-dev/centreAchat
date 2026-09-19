@extends('layouts.app')

@section('title', "Central d'Achat — Le meilleur du terroir local, sélectionné pour vous")

@section('content')

    @php
        $categoryIcons = [
            'infusions-boissons-locales' => 'tea',
            'farines-poudres-locales' => 'grain',
            'fruits-seches-snacks' => 'fruit',
            'plats-traditionnels-prets' => 'bowl',
            'coffrets-cadeaux' => 'gift',
        ];
        $slideProducts = $featuredProducts->filter(fn ($p) => $p->mainImage())->take(5);
        $heroDiscount = $promoProduct && $promoProduct->price > 0
            ? (int) round((1 - $promoProduct->promo_price / $promoProduct->price) * 100)
            : null;
    @endphp

    {{-- HERO --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-terroir-green via-terroir-dark to-terroir-dark">
        <div class="pointer-events-none absolute -right-32 -top-32 h-96 w-96 rounded-full bg-terroir-gold/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-24 h-80 w-80 rounded-full bg-white/5 blur-3xl"></div>

        <div class="relative mx-auto max-w-7xl px-4 pb-10 pt-16 sm:px-6 md:pb-12 md:pt-24 lg:px-8">
            <div class="grid items-center gap-12 md:grid-cols-2">
                <div class="reveal is-visible">
                    @if($promoProduct && $promoProduct->promo_ends_at)
                        <span class="mb-3 inline-flex items-center rounded-full bg-terroir-terracotta/90 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-white">Offre à durée limitée</span>
                        <br>
                    @endif
                    <span class="section-eyebrow !text-terroir-gold">Fabriqué au Sénégal</span>
                    <h1 class="mt-3 max-w-lg font-display text-4xl font-semibold leading-tight tracking-tight text-white sm:text-5xl">
                        {{ \App\Models\Setting::get('hero_title') ?: 'Le meilleur du terroir local, sélectionné pour vous.' }}
                    </h1>
                    <p class="mt-4 max-w-md text-lg leading-relaxed text-white/70">
                        {{ \App\Models\Setting::get('hero_subtitle') ?: "Des producteurs locaux aux hôtels, professionnels et consommateurs, Central d'Achat facilite l'accès à des produits authentiques, frais et de qualité." }}
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3.5">
                        <a href="{{ route('produits.index') }}" class="btn-gold">
                            Découvrir nos produits
                            <span class="material-symbols-outlined text-lg">arrow_forward</span>
                        </a>
                        <a href="{{ route('pages.show', 'coffrets-cadeaux') }}" class="btn border border-white/35 bg-white/10 text-white hover:-translate-y-0.5 hover:bg-white/20">Voir les coffrets</a>
                    </div>
                </div>

                <div class="reveal is-visible relative" style="transition-delay:.1s">
                    @if($heroDiscount && $heroDiscount > 0)
                        <div class="absolute -right-3 -top-3 z-10 flex h-20 w-20 rotate-6 flex-col items-center justify-center rounded-full bg-terroir-terracotta text-center text-white shadow-soft sm:-right-4 sm:-top-4 sm:h-24 sm:w-24">
                            <span class="text-[10px] font-semibold uppercase leading-none">Jusqu'à</span>
                            <span class="font-display text-2xl font-bold leading-tight sm:text-3xl">-{{ $heroDiscount }}%</span>
                        </div>
                    @endif
                    @if($slideProducts->isNotEmpty())
                        <div x-data="{ active: 0 }" x-init="setInterval(() => active = (active + 1) % {{ $slideProducts->count() }}, 4500)" x-cloak
                             class="relative aspect-square overflow-hidden rounded-xl2 shadow-soft">
                            @foreach($slideProducts as $i => $product)
                                <a href="{{ route('produits.show', $product->slug) }}" x-show="active === {{ $i }}"
                                   x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                   class="absolute inset-0 block">
                                    <img src="{{ asset('fichiers/'.$product->mainImage()) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-terroir-dark/85 to-transparent p-7 pt-16 text-white">
                                        <p class="text-sm opacity-80">{{ $product->category?->name }}</p>
                                        <h3 class="mt-1 font-display text-xl font-semibold">{{ $product->name }}</h3>
                                        <p class="mt-1 font-bold">{{ number_format($product->currentPrice(), 0, ',', ' ') }} FCFA</p>
                                    </div>
                                </a>
                            @endforeach
                            <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-1.5">
                                @foreach($slideProducts as $i => $product)
                                    <span :class="active === {{ $i }} ? 'w-6 bg-white' : 'w-1.5 bg-white/50'" class="h-1.5 rounded-full transition-all duration-300"></span>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="flex aspect-square items-center justify-center rounded-xl2 bg-gradient-to-br from-white/10 to-white/5 text-7xl">🌿</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- TRUST BADGES --}}
        <div class="relative bg-white">
            <div class="mx-auto grid max-w-7xl grid-cols-2 gap-y-5 px-4 py-6 sm:px-6 md:grid-cols-4 lg:px-8">
                @foreach([
                    ['icon' => 'truck', 'text' => 'Livraison à Mbour & Dakar'],
                    ['icon' => 'card', 'text' => 'Espèces, Wave, Orange Money'],
                    ['icon' => 'lock', 'text' => 'Commande sécurisée'],
                    ['icon' => 'chat', 'text' => 'Support réactif par chat'],
                ] as $badge)
                    <div class="flex items-center justify-center gap-2.5 text-sm font-medium text-terroir-dark/70">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-terroir-green/10 text-lg text-terroir-green">
                            @include('partials.icons.'.$badge['icon'])
                        </span>
                        <span>{{ $badge['text'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- PRESENTATION --}}
    <section class="bg-terroir-cream py-4 md:py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid items-center gap-4 md:grid-cols-2 md:gap-8">
                <div class="reveal order-1">
                    <img src="{{ asset('images/mbour-terroir.jpg') }}" alt="Centre d'achat de Mbour — produits du terroir sénégalais" class="mx-auto w-full max-w-[5.5rem] rounded-xl2 shadow-soft">
                </div>
                <div class="reveal order-2" style="transition-delay:.1s">
                    <span class="section-eyebrow">Qui sommes-nous</span>
                    <h2 class="section-title mt-1">Centre d'achat de Mbour</h2>
                    <p class="mt-2 text-sm leading-relaxed text-terroir-dark/70">Le Centre d'achat de Mbour est une plateforme dédiée à la commercialisation et à la valorisation des produits locaux sénégalais. Situé à Mbour – Rond-Point Malicounda, il met en relation producteurs, fournisseurs et artisans avec les hôtels, restaurants, entreprises et touristes.</p>
                    <p class="mt-2 text-sm leading-relaxed text-terroir-dark/70">Sa mission est de faciliter l'accès aux marchés, promouvoir le savoir-faire local et renforcer les circuits de distribution des produits sénégalais.</p>
                    <p class="mt-3 font-display text-lg italic text-terroir-green">« Le terroir sénégalais au cœur du commerce. »</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CATEGORIES --}}
    @if($categories->count())
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 md:py-24 lg:px-8">
        <div class="reveal text-center">
            <span class="section-eyebrow">Explorez</span>
            <h2 class="section-title mt-1">Nos catégories</h2>
        </div>
        <div class="mt-12 grid grid-cols-2 gap-4 md:grid-cols-4">
            @foreach($categories as $i => $category)
                <a href="{{ route('produits.index', ['categorie' => $category->slug]) }}"
                   class="reveal group flex flex-col items-center rounded-xl2 border border-terroir-dark/5 bg-white p-8 text-center shadow-soft transition duration-300 hover:-translate-y-1 hover:shadow-xl"
                   style="transition-delay: {{ $i * 0.06 }}s">
                    <span class="flex h-14 w-14 items-center justify-center rounded-full bg-terroir-green/10 text-2xl text-terroir-green transition duration-300 group-hover:scale-110 group-hover:bg-terroir-green group-hover:text-white">
                        @include('partials.icons.'.($categoryIcons[$category->slug] ?? 'leaf'))
                    </span>
                    <span class="mt-3.5 text-sm font-semibold text-terroir-dark">{{ $category->name }}</span>
                    <span class="mt-1 inline-flex items-center gap-1 text-xs font-semibold text-terroir-green">
                        Voir
                        <span class="material-symbols-outlined text-sm transition group-hover:translate-x-0.5">arrow_forward</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- PRODUITS VEDETTES --}}
    @if(\App\Models\Setting::getBool('show_featured_products', true) && $featuredProducts->count())
    <section class="bg-white py-16 md:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="reveal flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="section-eyebrow">Sélection</span>
                    <h2 class="section-title mt-1">Produits en vedette</h2>
                </div>
                <a href="{{ route('produits.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-terroir-green hover:text-terroir-dark">
                    Voir tous les produits
                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                </a>
            </div>
            <div class="mt-12 grid grid-cols-2 gap-5 md:grid-cols-4">
                @foreach($featuredProducts as $i => $product)
                    <div class="reveal" style="transition-delay: {{ $i * 0.05 }}s">@include('partials.product-card', ['product' => $product])</div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- OFFRE DU MOMENT --}}
    @if($promoProduct)
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 md:py-24 lg:px-8">
        <div class="reveal grid overflow-hidden rounded-xl2 bg-gradient-to-br from-terroir-gold/15 to-terroir-gold/5 shadow-soft md:grid-cols-2">
            <div class="flex flex-col justify-center p-11">
                <span class="section-eyebrow">Offre du moment</span>
                <h2 class="section-title mt-1">{{ $promoProduct->name }}</h2>
                <p class="mt-2.5 leading-relaxed text-terroir-dark/60">{{ $promoProduct->short_description }}</p>
                <div class="mt-3 flex items-center gap-3.5">
                    <span class="font-display text-3xl font-bold text-terroir-terracotta">{{ number_format($promoProduct->promo_price, 0, ',', ' ') }} FCFA</span>
                    <span class="text-terroir-dark/35 line-through">{{ number_format($promoProduct->price, 0, ',', ' ') }} FCFA</span>
                </div>
                @if($promoProduct->stock_quantity > 0)
                    <p class="mt-2.5 text-sm font-medium text-terroir-terracotta">Il reste {{ $promoProduct->stock_quantity }} unité{{ $promoProduct->stock_quantity > 1 ? 's' : '' }} en stock</p>
                @endif
                @if($promoProduct->promo_ends_at)
                    <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-terroir-dark/40">Offre valable jusqu'au {{ $promoProduct->promo_ends_at->translatedFormat('d F Y') }}</p>
                    <div x-data="{
                            target: {{ $promoProduct->promo_ends_at->timestamp * 1000 }},
                            d: 0, h: 0, m: 0, s: 0,
                            tick() {
                                const diff = Math.max(0, this.target - Date.now());
                                this.d = Math.floor(diff / 86400000);
                                this.h = Math.floor((diff % 86400000) / 3600000);
                                this.m = Math.floor((diff % 3600000) / 60000);
                                this.s = Math.floor((diff % 60000) / 1000);
                            }
                         }" x-init="tick(); setInterval(() => tick(), 1000)" class="mt-2 flex gap-2">
                        <div class="flex w-14 flex-col items-center rounded-lg bg-white py-2 shadow-soft">
                            <span class="font-display text-lg font-bold text-terroir-dark" x-text="String(d).padStart(2,'0')"></span>
                            <span class="text-[10px] uppercase text-terroir-dark/40">Jours</span>
                        </div>
                        <div class="flex w-14 flex-col items-center rounded-lg bg-white py-2 shadow-soft">
                            <span class="font-display text-lg font-bold text-terroir-dark" x-text="String(h).padStart(2,'0')"></span>
                            <span class="text-[10px] uppercase text-terroir-dark/40">Heures</span>
                        </div>
                        <div class="flex w-14 flex-col items-center rounded-lg bg-white py-2 shadow-soft">
                            <span class="font-display text-lg font-bold text-terroir-dark" x-text="String(m).padStart(2,'0')"></span>
                            <span class="text-[10px] uppercase text-terroir-dark/40">Min</span>
                        </div>
                        <div class="flex w-14 flex-col items-center rounded-lg bg-white py-2 shadow-soft">
                            <span class="font-display text-lg font-bold text-terroir-dark" x-text="String(s).padStart(2,'0')"></span>
                            <span class="text-[10px] uppercase text-terroir-dark/40">Sec</span>
                        </div>
                    </div>
                @endif
                <a href="{{ route('produits.show', $promoProduct->slug) }}" class="btn-primary mt-6 self-start">Voir l'offre</a>
            </div>
            <div class="flex items-center justify-center bg-white/50 p-11">
                @if($promoProduct->mainImage())
                    <img src="{{ asset('fichiers/'.$promoProduct->mainImage()) }}" alt="{{ $promoProduct->name }}" class="max-h-64 rounded-2xl object-cover shadow-soft">
                @else
                    <span class="text-7xl">🌿</span>
                @endif
            </div>
        </div>
    </section>
    @endif

    {{-- ESPACES DEDIES --}}
    <section class="bg-terroir-cream py-16 md:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-2">
                <div class="reveal flex h-full flex-col rounded-xl2 bg-terroir-green p-11 text-white shadow-soft">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white/15 text-2xl">@include('partials.icons.hotel')</span>
                    <h3 class="mt-4.5 font-display text-xl font-semibold">Hôtels &amp; Professionnels</h3>
                    <p class="mt-3 leading-relaxed text-white/85">Tarifs dégressifs, commandes en gros, devis personnalisés et facturation professionnelle.</p>
                    <a href="{{ route('pages.show', 'hotels-professionnels') }}" class="btn mt-6 self-start bg-white text-terroir-green hover:-translate-y-0.5">Découvrir l'offre B2B</a>
                </div>
                <div class="reveal flex h-full flex-col rounded-xl2 bg-terroir-gold p-11 text-terroir-dark shadow-soft" style="transition-delay:.08s">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white/40 text-2xl">@include('partials.icons.gift')</span>
                    <h3 class="mt-4.5 font-display text-xl font-semibold">Coffrets &amp; Cadeaux</h3>
                    <p class="mt-3 leading-relaxed text-terroir-dark/80">Des coffrets prêts à offrir, composés à partir de notre gamme locale.</p>
                    <a href="{{ route('pages.show', 'coffrets-cadeaux') }}" class="btn-primary mt-6 self-start">Voir les coffrets</a>
                </div>
            </div>
        </div>
    </section>

    {{-- NOUVEAUTES --}}
    @if($newProducts->count())
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 md:py-24 lg:px-8">
        <div class="reveal flex flex-wrap items-center justify-between gap-4">
            <div>
                <span class="section-eyebrow">Fraîchement arrivé</span>
                <h2 class="section-title mt-1">Nouveautés</h2>
            </div>
            <a href="{{ route('produits.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-terroir-green hover:text-terroir-dark">
                Voir tout
                <span class="material-symbols-outlined text-lg">arrow_forward</span>
            </a>
        </div>
        <div class="mt-12 grid grid-cols-2 gap-5 md:grid-cols-4">
            @foreach($newProducts as $i => $product)
                <div class="reveal" style="transition-delay: {{ $i * 0.05 }}s">@include('partials.product-card', ['product' => $product])</div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- PRODUCTEUR --}}
    @if(\App\Models\Setting::getBool('show_producers', true) && $producers->isNotEmpty())
        @php $producer = $producers->first(); @endphp
        <section class="relative overflow-hidden bg-terroir-green py-16 md:py-24 text-white">
            <div class="pointer-events-none absolute left-1/2 top-0 h-80 w-[40rem] -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/5 blur-3xl"></div>
            <div class="reveal relative mx-auto max-w-2xl px-4 text-center sm:px-6">
                <span class="section-eyebrow !text-terroir-gold">Fabriqué par</span>
                <h2 class="section-title mt-1 !text-white">{{ $producer->name }}</h2>
                @if($producer->region)
                    <p class="mt-2 flex items-center justify-center gap-1.5 text-white/70">
                        <span class="material-symbols-outlined text-lg">location_on</span>
                        {{ $producer->region }}
                    </p>
                @endif
                @if($producer->description)
                    <p class="mx-auto mt-5 max-w-lg leading-loose text-white/90">{{ $producer->description }}</p>
                @endif
                <a href="{{ route('producteurs.show', $producer->slug) }}" class="btn mt-6 bg-white text-terroir-green hover:-translate-y-0.5">Voir tous les produits</a>
            </div>
        </section>
    @endif

    {{-- TEMOIGNAGES --}}
    @if(\App\Models\Setting::getBool('show_testimonials', true) && $testimonials->count())
    <section class="bg-white py-16 md:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="reveal text-center">
                <span class="section-eyebrow">Ils nous font confiance</span>
                <h2 class="section-title mt-1">Avis de nos clients</h2>
            </div>
            <div class="mt-12 grid gap-5 md:grid-cols-3">
                @foreach($testimonials->take(3) as $i => $testimonial)
                    <div class="reveal card p-9" style="transition-delay: {{ $i * 0.08 }}s">
                        <div class="flex gap-0.5 text-terroir-gold">
                            @for($s = 1; $s <= 5; $s++)
                                <span class="material-symbols-outlined text-lg{{ $s <= $testimonial->rating ? ' is-filled' : '' }}">star</span>
                            @endfor
                        </div>
                        <p class="mt-4.5 text-sm italic leading-relaxed text-terroir-dark/70">"{{ $testimonial->content }}"</p>
                        <p class="mt-4.5 text-sm font-bold text-terroir-dark">{{ $testimonial->author_name }}</p>
                        @if($testimonial->author_role)
                            <p class="text-sm text-terroir-dark/40">{{ $testimonial->author_role }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- NEWSLETTER --}}
    @if(\App\Models\Setting::getBool('show_newsletter', true))
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="reveal rounded-xl2 bg-terroir-green p-11 text-white shadow-soft">
            <div class="grid items-center gap-6 md:grid-cols-3">
                <div class="md:col-span-2">
                    <h3 class="font-display text-2xl font-semibold">Restez informé de nos nouveautés</h3>
                    <p class="mt-2 text-white/85">Recevez nos nouveaux produits et offres par e-mail.</p>
                </div>
                <form action="{{ route('newsletter.store') }}" method="POST" class="flex gap-2.5">
                    @csrf
                    <input type="email" name="email" required placeholder="Votre e-mail" class="input border-white/25 bg-white/10 text-white placeholder:text-white/50 focus:border-white">
                    <button type="submit" class="btn-gold shrink-0">S'inscrire</button>
                </form>
            </div>
        </div>
    </section>
    @endif

    {{-- LOCALISATION --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 pt-8 sm:px-6 md:pb-24 md:pt-10 lg:px-8">
        <div class="reveal flex flex-col items-center justify-between gap-5 rounded-xl2 bg-terroir-dark p-11 text-white md:flex-row">
            <div class="text-center md:text-left">
                <h3 class="flex items-center justify-center gap-2 font-display text-xl font-semibold md:justify-start">
                    <span class="material-symbols-outlined text-xl text-terroir-gold">location_on</span>
                    Rond-Point Malicounda, Mbour – Sénégal
                </h3>
                <p class="mt-2 text-white/70">Visitez notre boutique ou passez commande en ligne, livraison partout au Sénégal.</p>
            </div>
            <a href="{{ route('pages.show', 'contact') }}" class="btn-gold shrink-0 whitespace-nowrap">Nous contacter</a>
        </div>
    </section>

@endsection
