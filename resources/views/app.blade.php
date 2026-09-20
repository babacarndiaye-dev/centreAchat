<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title inertia>@yield('title', 'Central d\'Achat')</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('icons/favicon-64.png') }}">
    <meta name="theme-color" content="#123D2E">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    <meta name="vapid-public-key" content="{{ config('services.vapid.public_key') }}">

    <style>{!! \App\Support\Theme::cssVariables() !!}</style>
    @routes
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/inertia-app.jsx'])
    @inertiaHead
</head>
<body class="bg-terroir-cream text-terroir-dark antialiased">
    <div id="boot-splash">
        <img src="{{ asset('icons/icon-512.png') }}" alt="Central d'Achat" width="88" height="88">
        <span>Central d'Achat</span>
    </div>

    @inertia

    <script>
        (function () {
            var splash = document.getElementById('boot-splash');
            if (!splash) return;
            var shownAt = Date.now();
            window.addEventListener('load', function () {
                var remaining = Math.max(0, 1400 - (Date.now() - shownAt));
                setTimeout(function () {
                    splash.classList.add('is-hidden');
                    setTimeout(function () { splash.remove(); }, 600);
                }, remaining);
            });
        })();
    </script>
</body>
</html>
