<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title inertia>@yield('title', 'Central d\'Achat')</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('icons/favicon-64.png') }}">
    <meta name="theme-color" content="#1D8A4E">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    <meta name="vapid-public-key" content="{{ config('services.vapid.public_key') }}">

    <style>{!! \App\Support\Theme::cssVariables() !!}</style>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/inertia-app.jsx'])
    @inertiaHead
</head>
<body class="bg-terroir-cream text-terroir-dark antialiased">
    @inertia
</body>
</html>
