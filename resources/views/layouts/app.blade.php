<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DIABA HOTEL Produits du Sénégal (D.H.P.S) — Le meilleur du terroir sénégalais')</title>
    <meta name="description" content="@yield('meta_description', 'DIABA HOTEL Produits du Sénégal (D.H.P.S) sélectionne, valorise et livre des produits locaux authentiques aux particuliers, hôtels, restaurants et professionnels à Mbour et au Sénégal.')">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('icons/favicon-64.png') }}">

    <link rel="manifest" href="{{ route('pwa.manifest') }}">
    <meta name="theme-color" content="#101818">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="DIABA HOTEL Produits du Sénégal (D.H.P.S)">
    <meta name="vapid-public-key" content="{{ config('services.vapid.public_key') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">

    <style>{!! \App\Support\Theme::cssVariables() !!}</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $siteName = \App\Models\Setting::get('site_name') ?: 'DIABA HOTEL Produits du Sénégal (D.H.P.S)';
    $logoPath = \App\Models\Setting::get('logo_path');
    $brandMain = trim(\Illuminate\Support\Str::before($siteName, ' Produits du'));
    $brandSub = $brandMain !== $siteName ? trim(substr($siteName, strlen($brandMain))) : '';
    $announcementActive = \App\Models\Setting::getBool('announcement_active', false);
    $announcementText = \App\Models\Setting::get('announcement_text');
@endphp
<body class="uk-flex uk-flex-column" style="min-height: 100vh;">

    <div id="page-loader">
        <img src="{{ $logoPath ? asset('fichiers/'.$logoPath) : asset('images/logo.svg') }}" alt="{{ $siteName }}">
    </div>
    <script>
        if (sessionStorage.getItem('caPageLoaderShown')) {
            document.getElementById('page-loader').style.display = 'none';
        }
    </script>

    <div class="hidden items-center justify-center gap-6 bg-terroir-dark px-4 py-2 text-xs font-medium text-white/90 sm:flex">
        <span class="flex items-center gap-1.5"><span class="shrink-0 text-sm text-terroir-gold">@include('partials.icons.truck')</span> Livraison rapide au Sénégal</span>
        <span class="flex items-center gap-1.5"><span class="shrink-0 text-sm text-terroir-gold">@include('partials.icons.lock')</span> Paiement sécurisé</span>
        <span class="flex items-center gap-1.5"><span class="shrink-0 text-sm text-terroir-gold">@include('partials.icons.chat')</span> Support client</span>
    </div>

    @if($announcementActive && $announcementText)
        <div class="flex items-center justify-center gap-2 bg-terroir-dark px-4 py-2.5 text-center text-sm font-medium text-white">
            <span class="material-symbols-outlined text-base is-filled">campaign</span>
            <span>{{ $announcementText }}</span>
        </div>
    @endif

    <header id="site-header" x-data="{ mobileOpen: false }" class="sticky top-0 z-50 bg-white/60 backdrop-blur transition-colors duration-300">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('accueil') }}" class="flex items-center gap-2">
                <img src="{{ $logoPath ? asset('fichiers/'.$logoPath) : asset('images/logo.svg') }}" alt="" class="h-11 w-auto object-contain">
                <span class="flex flex-col whitespace-nowrap leading-none">
                    <span class="font-display text-2xl uppercase tracking-wider text-terroir-green">{{ $brandMain }}</span>
                    @if($brandSub)<span class="mt-1 text-[9px] font-semibold uppercase tracking-[0.1em] text-terroir-dark/70">{{ $brandSub }}</span>@endif
                </span>
            </a>

            <nav class="ml-10 hidden items-center gap-7 lg:flex">
                <a href="{{ route('produits.index') }}" class="text-sm font-medium text-terroir-dark/80 transition hover:text-terroir-green">Nos produits</a>
                <a href="{{ route('produits.index', ['promo' => 1]) }}" class="text-sm font-medium text-terroir-dark/80 transition hover:text-terroir-green">Promotions</a>
                <a href="{{ route('pages.show', 'hotels-professionnels') }}" class="text-sm font-medium text-terroir-dark/80 transition hover:text-terroir-green">Hôtels &amp; Pro</a>
                <a href="{{ route('pages.show', 'espace-touristes') }}" class="text-sm font-medium text-terroir-dark/80 transition hover:text-terroir-green">Touristes</a>
                <a href="{{ route('blog.index') }}" class="text-sm font-medium text-terroir-dark/80 transition hover:text-terroir-green">Actualités</a>
                <a href="{{ route('pages.show', 'contact') }}" class="text-sm font-medium text-terroir-dark/80 transition hover:text-terroir-green">Contact</a>
            </nav>

            <form action="{{ route('produits.index') }}" method="GET" class="relative mx-4 hidden max-w-xs flex-1 xl:flex">
                <input type="search" name="q" placeholder="Rechercher un produit..." class="w-full rounded-full border border-terroir-green/15 bg-terroir-cream/60 py-2 pl-4 pr-9 text-sm text-terroir-dark placeholder:text-terroir-dark/40 focus:border-terroir-green focus:outline-none">
                <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-lg text-terroir-dark/40" aria-label="Rechercher">
                    <span class="material-symbols-outlined">search</span>
                </button>
            </form>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('compte.index') }}" class="hidden text-sm font-medium text-terroir-dark/80 hover:text-terroir-green sm:block">
                        {{ auth()->user()->is_admin ? 'Administration' : 'Mon compte' }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden text-sm font-medium text-terroir-dark/80 hover:text-terroir-green sm:block">Connexion</a>
                @endauth

                <a href="{{ route('produits.index') }}" class="hidden rounded-full bg-terroir-green px-5 py-2.5 text-sm font-bold uppercase tracking-wide text-white transition hover:-translate-y-0.5 hover:bg-terroir-dark sm:inline-block">Commander</a>

                @auth
                    <a href="{{ route('compte.favoris.index') }}" class="hidden h-10 w-10 items-center justify-center rounded-full text-xl text-terroir-dark/70 transition hover:bg-terroir-cream sm:inline-flex" aria-label="Mes favoris">
                        <span class="material-symbols-outlined">favorite</span>
                    </a>
                @endauth

                <a href="{{ route('panier.index') }}" class="relative inline-flex h-10 w-10 items-center justify-center rounded-full bg-terroir-green text-xl text-terroir-cream transition hover:bg-terroir-dark" aria-label="Panier">
                    <span class="material-symbols-outlined">shopping_cart</span>
                    @if(\App\Support\Cart::count() > 0)
                        <span class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-terroir-green text-[11px] font-bold text-white">{{ \App\Support\Cart::count() }}</span>
                    @endif
                </a>

                <button @click="mobileOpen = !mobileOpen" class="inline-flex h-10 w-10 items-center justify-center rounded-full text-2xl text-terroir-dark lg:hidden" aria-label="Menu">
                    <span class="material-symbols-outlined" x-show="!mobileOpen">menu</span>
                    <span class="material-symbols-outlined" x-show="mobileOpen" x-cloak>close</span>
                </button>
            </div>
        </div>

        <div x-show="mobileOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="border-t border-terroir-green/10 bg-white px-4 pb-6 pt-2 lg:hidden">
            <nav class="flex flex-col gap-1">
                <a href="{{ route('produits.index') }}" class="rounded-lg px-3 py-3 text-sm font-medium text-terroir-dark transition-colors active:bg-terroir-cream hover:bg-terroir-cream">Nos produits</a>
                <a href="{{ route('produits.index', ['promo' => 1]) }}" class="rounded-lg px-3 py-3 text-sm font-medium text-terroir-dark transition-colors active:bg-terroir-cream hover:bg-terroir-cream">Promotions</a>
                <a href="{{ route('pages.show', 'hotels-professionnels') }}" class="rounded-lg px-3 py-3 text-sm font-medium text-terroir-dark transition-colors active:bg-terroir-cream hover:bg-terroir-cream">Hôtels &amp; Professionnels</a>
                <a href="{{ route('pages.show', 'espace-touristes') }}" class="rounded-lg px-3 py-3 text-sm font-medium text-terroir-dark transition-colors active:bg-terroir-cream hover:bg-terroir-cream">Espace Touristes</a>
                <a href="{{ route('blog.index') }}" class="rounded-lg px-3 py-3 text-sm font-medium text-terroir-dark transition-colors active:bg-terroir-cream hover:bg-terroir-cream">Actualités</a>
                <a href="{{ route('pages.show', 'contact') }}" class="rounded-lg px-3 py-3 text-sm font-medium text-terroir-dark transition-colors active:bg-terroir-cream hover:bg-terroir-cream">Contact</a>
                @auth
                    <a href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('compte.index') }}" class="rounded-lg px-3 py-3 text-sm font-medium text-terroir-dark transition-colors active:bg-terroir-cream hover:bg-terroir-cream">{{ auth()->user()->is_admin ? 'Administration' : 'Mon compte' }}</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-3 text-sm font-medium text-terroir-dark transition-colors active:bg-terroir-cream hover:bg-terroir-cream">Connexion</a>
                @endauth
            </nav>
        </div>
    </header>
    </div>

    @if (session('success'))
        <script>document.addEventListener('DOMContentLoaded', () => UIkit.notification({message: @json(session('success')), status: 'success', pos: 'top-right', timeout: 2000}));</script>
    @endif
    @if (session('error'))
        <script>document.addEventListener('DOMContentLoaded', () => UIkit.notification({message: @json(session('error')), status: 'danger', pos: 'top-right', timeout: 2800}));</script>
    @endif

    <main class="uk-flex-1">
        @yield('content')
    </main>

    <footer class="mt-16 bg-terroir-dark text-white/85">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid gap-10 sm:grid-cols-2 md:grid-cols-4">
                <div class="hidden sm:block">
                    <div class="flex items-center">
                        <img src="{{ $logoPath ? asset('fichiers/'.$logoPath) : asset('images/logo-white.svg') }}" alt="" class="mr-3 h-12 w-auto object-contain">
                        <span class="flex flex-col whitespace-nowrap leading-none">
                            <span class="font-display text-2xl uppercase tracking-wider text-white">{{ $brandMain }}</span>
                            @if($brandSub)<span class="mt-1 text-[9px] font-semibold uppercase tracking-[0.1em] text-white/70">{{ $brandSub }}</span>@endif
                        </span>
                    </div>
                    <p class="mt-4 text-sm text-white/70">Du terroir local à votre table. Nous soutenons l'économie locale en facilitant l'accès à des produits frais et authentiques du Sénégal.</p>
                    <p class="mt-3 text-sm text-white/70">📍 {{ \App\Models\Setting::get('address', 'Rond-Point Malicounda, Mbour – Sénégal') }}</p>
                </div>

                <div class="hidden sm:block">
                    <h4 class="font-semibold text-white">Découvrir</h4>
                    <ul class="mt-3 space-y-2 text-sm text-white/70">
                        <li><a href="{{ route('produits.index') }}" class="hover:text-white">Nos produits</a></li>
                        <li><a href="{{ route('producteurs.index') }}" class="hover:text-white">Nos producteurs</a></li>
                        <li><a href="{{ route('pages.show', 'a-propos') }}" class="hover:text-white">À propos</a></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-white">Actualités &amp; recettes</a></li>
                        <li><a href="{{ route('pages.show', 'devenir-fournisseur') }}" class="hover:text-white">Devenir fournisseur</a></li>
                    </ul>
                </div>

                <div class="hidden sm:block">
                    <h4 class="font-semibold text-white">Assistance</h4>
                    <ul class="mt-3 space-y-2 text-sm text-white/70">
                        <li><a href="{{ route('pages.show', 'faq') }}" class="hover:text-white">FAQ</a></li>
                        <li><a href="{{ route('pages.show', 'livraison') }}" class="hover:text-white">Livraison</a></li>
                        <li><a href="{{ route('pages.show', 'contact') }}" class="hover:text-white">Contact</a></li>
                        <li><a href="{{ route('pages.show', 'mentions-legales') }}" class="hover:text-white">Mentions légales</a></li>
                        <li><a href="{{ route('pages.show', 'politique-de-confidentialite') }}" class="hover:text-white">Confidentialité</a></li>
                        <li><a href="{{ route('pages.show', 'conditions-generales') }}" class="hover:text-white">CGV</a></li>
                    </ul>
                </div>

                @if(\App\Models\Setting::getBool('show_newsletter', true))
                    <div>
                        <h4 class="font-semibold text-white">Newsletter</h4>
                        <p class="mt-3 text-sm text-white/70">Recevez nos nouveautés et offres du terroir.</p>
                        <form action="{{ route('newsletter.store') }}" method="POST" class="mt-3 space-y-2">
                            @csrf
                            <input type="email" name="email" required placeholder="Votre e-mail" class="input w-full border-white/20 bg-white/10 text-white placeholder:text-white/50 focus:border-white">
                            <button type="submit" class="btn-primary w-full">S'inscrire</button>
                        </form>
                    </div>
                @endif
            </div>

            <div class="mt-12 flex flex-wrap items-center justify-between gap-2 border-t border-white/10 pt-8 text-xs text-white/50">
                <p>&copy; {{ date('Y') }} {{ $siteName }} — L'authenticité du local. L'élégance du digital.</p>
                <p>Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    @include('partials.chat-widget')

</body>
</html>
