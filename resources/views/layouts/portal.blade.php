<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portail fournisseur') — Centrale d'achat</title>
    <style>{!! \App\Support\Theme::cssVariables() !!}</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-terroir-cream text-terroir-dark" x-data="{ mobileOpen: false }">

    <header class="border-b border-terroir-green/10 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6">
            <a href="{{ route('portail.dashboard') }}" class="flex items-center gap-2">
                <span class="material-symbols-outlined text-2xl">handshake</span>
                <div>
                    <span class="block font-display text-lg font-semibold text-terroir-green">Portail Fournisseur</span>
                    <span class="block text-xs text-terroir-dark/50">Centrale d'achat</span>
                </div>
            </a>

            <nav class="hidden items-center gap-6 sm:flex">
                <a href="{{ route('portail.dashboard') }}" class="text-sm font-medium {{ request()->routeIs('portail.dashboard') ? 'text-terroir-green' : 'text-terroir-dark/70 hover:text-terroir-green' }}">Tableau de bord</a>
                <a href="{{ route('portail.commandes.index') }}" class="text-sm font-medium {{ request()->routeIs('portail.commandes.*') ? 'text-terroir-green' : 'text-terroir-dark/70 hover:text-terroir-green' }}">Mes commandes</a>
            </nav>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm font-medium text-terroir-dark/60 hover:text-terroir-terracotta">Se déconnecter</button>
            </form>
        </div>
        <nav class="flex gap-4 border-t border-terroir-green/10 px-4 py-2 sm:hidden">
            <a href="{{ route('portail.dashboard') }}" class="text-sm font-medium {{ request()->routeIs('portail.dashboard') ? 'text-terroir-green' : 'text-terroir-dark/70' }}">Tableau de bord</a>
            <a href="{{ route('portail.commandes.index') }}" class="text-sm font-medium {{ request()->routeIs('portail.commandes.*') ? 'text-terroir-green' : 'text-terroir-dark/70' }}">Mes commandes</a>
        </nav>
    </header>

    @if (session('success'))
        <div class="mx-auto mt-4 max-w-6xl rounded-lg bg-terroir-green/10 px-4 py-3 text-sm font-medium text-terroir-green">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mx-auto mt-4 max-w-6xl rounded-lg bg-terroir-terracotta/10 px-4 py-3 text-sm font-medium text-terroir-terracotta">{{ session('error') }}</div>
    @endif

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        @yield('content')
    </main>

</body>
</html>
