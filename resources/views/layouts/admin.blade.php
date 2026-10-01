<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration') — DIABA HOTEL Produits du Sénégal (D.H.P.S)</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('icons/favicon-64.png') }}">
    <link rel="manifest" href="{{ route('pwa.manifest') }}">
    <meta name="theme-color" content="#101818">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="vapid-public-key" content="{{ config('services.vapid.public_key') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">

    <style>{!! \App\Support\Theme::cssVariables() !!}</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-terroir-cream text-terroir-dark antialiased">

<div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

    <aside class="hidden w-64 shrink-0 flex-col bg-terroir-dark lg:flex">
        @include('admin.partials.sidebar-nav')
    </aside>

    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 lg:hidden">
        <div class="absolute inset-0 bg-black/50" @click="sidebarOpen = false"></div>
        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
             class="relative flex h-full w-64 flex-col bg-terroir-dark">
            @include('admin.partials.sidebar-nav')
        </div>
    </div>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="flex h-16 shrink-0 items-center justify-between border-b border-terroir-dark/10 bg-white px-6">
            <button @click="sidebarOpen = true" type="button" class="text-xl text-terroir-dark lg:hidden" aria-label="Menu">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <h1 class="font-display text-lg font-semibold">@yield('title', 'Tableau de bord')</h1>
            <div class="flex items-center gap-4">
                @if(config('services.vapid.public_key'))
                    <button
                        x-data="{
                            status: 'unsupported',
                            async refresh() { this.status = window.DiabaHotelPush ? await window.DiabaHotelPush.status() : 'unsupported'; },
                            async toggle() {
                                if (!window.DiabaHotelPush) return;
                                if (this.status === 'subscribed') {
                                    await window.DiabaHotelPush.unsubscribe();
                                } else {
                                    await window.DiabaHotelPush.subscribe('{{ config('services.vapid.public_key') }}');
                                }
                                await this.refresh();
                            },
                        }"
                        x-init="setTimeout(() => refresh(), 300)"
                        x-show="status !== 'unsupported'"
                        x-cloak
                        @click="toggle()"
                        type="button"
                        class="relative text-xl text-terroir-dark/50"
                        :class="status === 'subscribed' ? 'text-terroir-green' : ''"
                        :aria-label="status === 'subscribed' ? 'Désactiver les notifications push' : 'Activer les notifications push'"
                        :title="status === 'subscribed' ? 'Notifications push activées' : 'Activer les notifications push'"
                    >
                        <span class="material-symbols-outlined">notifications</span>
                        <span x-show="status === 'subscribed'" x-cloak class="absolute -right-0.5 -top-0.5 h-2 w-2 rounded-full bg-terroir-green ring-2 ring-white"></span>
                    </button>
                @endif
                @php
                    $unreadChat = auth()->user()->hasPermission('messagerie.voir') ? \App\Models\Conversation::unreadForStaffCount() : 0;
                    $unreadNotifications = auth()->user()->unreadNotificationsCount();
                    $unreadTotal = $unreadChat + $unreadNotifications;
                    $bellRoute = $unreadChat > 0 ? route('admin.messagerie.index') : route('admin.notifications.index');
                @endphp
                <a href="{{ $bellRoute }}" class="relative text-xl text-terroir-dark/50 hover:text-terroir-dark" aria-label="Notifications">
                    <span class="material-symbols-outlined">notifications</span>
                    @if($unreadTotal > 0)
                        <span class="absolute -right-1.5 -top-1.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-terroir-terracotta px-1 text-[10px] font-bold text-white">{{ $unreadTotal > 9 ? '9+' : $unreadTotal }}</span>
                    @endif
                </a>
                <span class="hidden text-sm text-terroir-dark/60 sm:inline">{{ auth()->user()->name }}</span>
            </div>
        </header>

        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" class="mx-6 mt-4 flex items-center justify-between gap-3 rounded-lg bg-terroir-green/10 px-4 py-3 text-sm font-medium text-terroir-green">
                {{ session('success') }}
                <button @click="show = false" class="text-terroir-green/60 hover:text-terroir-green"><span class="material-symbols-outlined text-lg">close</span></button>
            </div>
        @endif
        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" class="mx-6 mt-4 flex items-center justify-between gap-3 rounded-lg bg-terroir-terracotta/10 px-4 py-3 text-sm font-medium text-terroir-terracotta">
                {{ session('error') }}
                <button @click="show = false" class="text-terroir-terracotta/60 hover:text-terroir-terracotta"><span class="material-symbols-outlined text-lg">close</span></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="mx-6 mt-4 rounded-lg bg-terroir-terracotta/10 px-4 py-3 text-sm text-terroir-terracotta">
                <ul class="list-inside list-disc space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>
