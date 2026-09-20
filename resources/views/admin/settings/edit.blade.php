@extends('layouts.admin')

@section('title', 'Paramètres')

@section('content')
<div class="admin-card max-w-3xl">
    <form action="{{ route('admin.parametres.update') }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PATCH')

        <div>
            <h2 class="font-display text-lg font-semibold">Général</h2>
            <div class="mt-3 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label" for="site_name">Nom du site</label>
                    <input type="text" id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" class="input">
                </div>
                <div>
                    <label class="label" for="tagline">Slogan</label>
                    <input type="text" id="tagline" name="tagline" value="{{ old('tagline', $settings['tagline']) }}" class="input">
                </div>
                <div>
                    <label class="label" for="currency_symbol">Devise</label>
                    <input type="text" id="currency_symbol" name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? 'FCFA') }}" class="input">
                </div>
                <div>
                    <label class="label" for="timezone">Fuseau horaire</label>
                    <select id="timezone" name="timezone" class="input">
                        @foreach(['Africa/Dakar', 'Africa/Abidjan', 'Europe/Paris', 'UTC'] as $tz)
                            <option value="{{ $tz }}" @selected(old('timezone', $settings['timezone'] ?? 'Africa/Dakar') === $tz)>{{ $tz }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <span class="label block">Langues actives</span>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="lang_fr_active" value="1" checked disabled class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
                        Français (toujours active)
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="lang_en_active" value="1" @checked(old('lang_en_active', $settings['lang_en_active'])) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
                        Anglais
                    </label>
                </div>
                <p class="mt-1.5 text-sm text-terroir-dark/50">L'anglais est réservé pour l'espace touristes ; la traduction complète du site reste un chantier séparé.</p>
            </div>

            <div class="mt-4">
                <label class="label" for="logo">Logo</label>
                <div class="flex items-center gap-4">
                    @if($settings['logo_path'])
                        <img src="{{ asset('fichiers/'.$settings['logo_path']) }}" alt="Logo actuel" class="h-12 w-12 rounded-lg object-cover">
                    @endif
                    <input type="file" id="logo" name="logo" accept="image/*" class="input">
                </div>
            </div>
        </div>

        <div class="mt-8 border-t border-terroir-green/10 pt-8">
            <h2 class="font-display text-lg font-semibold">Coordonnées</h2>
            <div class="mt-3 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label" for="phone">Téléphone</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $settings['phone']) }}" class="input">
                </div>
                <div>
                    <label class="label" for="whatsapp">WhatsApp</label>
                    <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $settings['whatsapp']) }}" class="input">
                </div>
                <div>
                    <label class="label" for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $settings['email']) }}" class="input">
                </div>
                <div>
                    <label class="label" for="opening_hours">Horaires d'ouverture</label>
                    <input type="text" id="opening_hours" name="opening_hours" value="{{ old('opening_hours', $settings['opening_hours']) }}" class="input">
                </div>
                <div class="sm:col-span-2">
                    <label class="label" for="address">Adresse</label>
                    <input type="text" id="address" name="address" value="{{ old('address', $settings['address']) }}" class="input">
                </div>
            </div>
        </div>

        <div class="mt-8 border-t border-terroir-green/10 pt-8">
            <h2 class="font-display text-lg font-semibold">Réseaux sociaux</h2>
            <div class="mt-3 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label" for="facebook_url">Facebook</label>
                    <input type="url" id="facebook_url" name="facebook_url" value="{{ old('facebook_url', $settings['facebook_url']) }}" class="input">
                </div>
                <div>
                    <label class="label" for="instagram_url">Instagram</label>
                    <input type="url" id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $settings['instagram_url']) }}" class="input">
                </div>
            </div>
        </div>

        <div class="mt-8 border-t border-terroir-green/10 pt-8">
            <h2 class="font-display text-lg font-semibold">Couleurs du site</h2>
            <p class="mt-1.5 text-sm text-terroir-dark/50">Appliquées immédiatement sur tout le site, sans build ni déploiement.</p>
            <div class="mt-3 grid gap-4 sm:grid-cols-3">
                @php $colorFields = ['color_primary' => 'Couleur primaire (vert)', 'color_secondary' => 'Couleur secondaire (terracotta)', 'color_accent' => 'Couleur accent (or)']; @endphp
                @foreach($colorFields as $key => $label)
                    <div>
                        <label class="label" for="{{ $key }}">{{ $label }}</label>
                        <div class="flex items-center gap-2">
                            <input type="color" value="{{ old($key, $settings[$key] ?? '#1E4A3D') }}" onchange="document.getElementById('{{ $key }}').value = this.value" class="h-10 w-16 shrink-0 rounded-lg border border-terroir-green/20 p-0.5">
                            <input type="text" id="{{ $key }}" name="{{ $key }}" value="{{ old($key, $settings[$key] ?? '') }}" class="input">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-8 border-t border-terroir-green/10 pt-8">
            <h2 class="font-display text-lg font-semibold">Bannière d'accueil</h2>
            <div class="mt-3 flex flex-col gap-5">
                <div>
                    <label class="label" for="hero_title">Titre principal</label>
                    <input type="text" id="hero_title" name="hero_title" value="{{ old('hero_title', $settings['hero_title']) }}" placeholder="Le meilleur du terroir local, sélectionné pour vous." class="input">
                </div>
                <div>
                    <label class="label" for="hero_subtitle">Sous-titre</label>
                    <textarea id="hero_subtitle" name="hero_subtitle" rows="2" class="input">{{ old('hero_subtitle', $settings['hero_subtitle']) }}</textarea>
                </div>
                <div>
                    <label class="label" for="hero_image">Image de fond (optionnel)</label>
                    <div class="flex items-center gap-4">
                        @if($settings['hero_image_path'])
                            <img src="{{ asset('fichiers/'.$settings['hero_image_path']) }}" alt="" class="h-16 w-28 rounded-lg object-cover">
                        @endif
                        <input type="file" id="hero_image" name="hero_image" accept="image/*" class="input">
                    </div>
                </div>
            </div>

            <div class="mt-6 border-t border-terroir-green/10 pt-6">
                <label class="flex items-center gap-2 text-sm font-semibold">
                    <input type="checkbox" name="announcement_active" value="1" @checked(old('announcement_active', $settings['announcement_active'])) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
                    Afficher un bandeau d'annonce en haut du site
                </label>
                <input type="text" name="announcement_text" value="{{ old('announcement_text', $settings['announcement_text']) }}" placeholder="Ex : Livraison offerte dès 50 000 FCFA d'achat" class="input mt-3">
            </div>
        </div>

        <div class="mt-8 border-t border-terroir-green/10 pt-8">
            <h2 class="font-display text-lg font-semibold">Sections de la page d'accueil</h2>
            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                @php
                    $sectionToggles = [
                        'show_featured_products' => 'Produits en vedette',
                        'show_producers' => 'Nos producteurs',
                        'show_testimonials' => 'Avis clients',
                        'show_newsletter' => 'Bloc newsletter',
                    ];
                @endphp
                @foreach($sectionToggles as $key => $label)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="{{ $key }}" value="1" @checked(old($key, $settings[$key])) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn-primary mt-8">Enregistrer les paramètres</button>
    </form>
</div>
@endsection
