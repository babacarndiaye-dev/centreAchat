@extends('layouts.admin')

@section('title', 'Paramètres')

@section('content')
<div class="uk-card uk-card-default" style="max-width:56rem; padding:32px;">
    <form action="{{ route('admin.parametres.update') }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PATCH')

        <div>
            <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">Général</h2>
            <div class="uk-grid-small uk-child-width-1-2@s uk-margin-small-top" uk-grid>
                <div>
                    <label class="uk-form-label" for="site_name">Nom du site</label>
                    <input type="text" id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" class="uk-input">
                </div>
                <div>
                    <label class="uk-form-label" for="tagline">Slogan</label>
                    <input type="text" id="tagline" name="tagline" value="{{ old('tagline', $settings['tagline']) }}" class="uk-input">
                </div>
                <div>
                    <label class="uk-form-label" for="currency_symbol">Devise</label>
                    <input type="text" id="currency_symbol" name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? 'FCFA') }}" class="uk-input">
                </div>
                <div>
                    <label class="uk-form-label" for="timezone">Fuseau horaire</label>
                    <select id="timezone" name="timezone" class="uk-select">
                        @foreach(['Africa/Dakar', 'Africa/Abidjan', 'Europe/Paris', 'UTC'] as $tz)
                            <option value="{{ $tz }}" @selected(old('timezone', $settings['timezone'] ?? 'Africa/Dakar') === $tz)>{{ $tz }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="uk-margin-top">
                <span class="uk-form-label" style="display:block;">Langues actives</span>
                <div class="uk-flex" style="gap:24px;">
                    <label class="uk-flex uk-flex-middle uk-text-small" style="gap:8px;">
                        <input type="checkbox" name="lang_fr_active" value="1" checked disabled class="uk-checkbox">
                        Français (toujours active)
                    </label>
                    <label class="uk-flex uk-flex-middle uk-text-small" style="gap:8px;">
                        <input type="checkbox" name="lang_en_active" value="1" @checked(old('lang_en_active', $settings['lang_en_active'])) class="uk-checkbox">
                        Anglais
                    </label>
                </div>
                <p class="uk-text-small uk-text-muted uk-margin-small-top">L'anglais est réservé pour l'espace touristes ; la traduction complète du site reste un chantier séparé.</p>
            </div>

            <div class="uk-margin-top">
                <label class="uk-form-label" for="logo">Logo</label>
                <div class="uk-flex uk-flex-middle" style="gap:16px;">
                    @if($settings['logo_path'])
                        <img src="{{ asset('fichiers/'.$settings['logo_path']) }}" alt="Logo actuel" style="height:48px; width:48px; border-radius:8px; object-fit:cover;">
                    @endif
                    <input type="file" id="logo" name="logo" accept="image/*" class="uk-input">
                </div>
            </div>
        </div>

        <div class="uk-margin-top" style="border-top:1px solid rgba(29,138,78,.1); padding-top:32px;">
            <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">Coordonnées</h2>
            <div class="uk-grid-small uk-child-width-1-2@s uk-margin-small-top" uk-grid>
                <div>
                    <label class="uk-form-label" for="phone">Téléphone</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $settings['phone']) }}" class="uk-input">
                </div>
                <div>
                    <label class="uk-form-label" for="whatsapp">WhatsApp</label>
                    <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $settings['whatsapp']) }}" class="uk-input">
                </div>
                <div>
                    <label class="uk-form-label" for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $settings['email']) }}" class="uk-input">
                </div>
                <div>
                    <label class="uk-form-label" for="opening_hours">Horaires d'ouverture</label>
                    <input type="text" id="opening_hours" name="opening_hours" value="{{ old('opening_hours', $settings['opening_hours']) }}" class="uk-input">
                </div>
                <div class="uk-width-1-1@s" style="grid-column:1/-1;">
                    <label class="uk-form-label" for="address">Adresse</label>
                    <input type="text" id="address" name="address" value="{{ old('address', $settings['address']) }}" class="uk-input">
                </div>
            </div>
        </div>

        <div class="uk-margin-top" style="border-top:1px solid rgba(29,138,78,.1); padding-top:32px;">
            <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">Réseaux sociaux</h2>
            <div class="uk-grid-small uk-child-width-1-2@s uk-margin-small-top" uk-grid>
                <div>
                    <label class="uk-form-label" for="facebook_url">Facebook</label>
                    <input type="url" id="facebook_url" name="facebook_url" value="{{ old('facebook_url', $settings['facebook_url']) }}" class="uk-input">
                </div>
                <div>
                    <label class="uk-form-label" for="instagram_url">Instagram</label>
                    <input type="url" id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $settings['instagram_url']) }}" class="uk-input">
                </div>
            </div>
        </div>

        <div class="uk-margin-top" style="border-top:1px solid rgba(29,138,78,.1); padding-top:32px;">
            <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">Couleurs du site</h2>
            <p class="uk-text-small uk-text-muted uk-margin-small-top">Appliquées immédiatement sur tout le site, sans build ni déploiement.</p>
            <div class="uk-grid-small uk-child-width-1-3@s uk-margin-small-top" uk-grid>
                @php $colorFields = ['color_primary' => 'Couleur primaire (vert)', 'color_secondary' => 'Couleur secondaire (terracotta)', 'color_accent' => 'Couleur accent (or)']; @endphp
                @foreach($colorFields as $key => $label)
                    <div>
                        <label class="uk-form-label" for="{{ $key }}">{{ $label }}</label>
                        <div class="uk-flex uk-flex-middle" style="gap:8px;">
                            <input type="color" value="{{ old($key, $settings[$key] ?? '#1E4A3D') }}" onchange="document.getElementById('{{ $key }}').value = this.value" class="uk-input" style="width:60px; height:40px; padding:2px; flex-shrink:0;">
                            <input type="text" id="{{ $key }}" name="{{ $key }}" value="{{ old($key, $settings[$key] ?? '') }}" class="uk-input">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="uk-margin-top" style="border-top:1px solid rgba(29,138,78,.1); padding-top:32px;">
            <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">Bannière d'accueil</h2>
            <div class="uk-margin-small-top" style="display:flex; flex-direction:column; gap:20px;">
                <div>
                    <label class="uk-form-label" for="hero_title">Titre principal</label>
                    <input type="text" id="hero_title" name="hero_title" value="{{ old('hero_title', $settings['hero_title']) }}" placeholder="Le meilleur du terroir local, sélectionné pour vous." class="uk-input">
                </div>
                <div>
                    <label class="uk-form-label" for="hero_subtitle">Sous-titre</label>
                    <textarea id="hero_subtitle" name="hero_subtitle" rows="2" class="uk-textarea">{{ old('hero_subtitle', $settings['hero_subtitle']) }}</textarea>
                </div>
                <div>
                    <label class="uk-form-label" for="hero_image">Image de fond (optionnel)</label>
                    <div class="uk-flex uk-flex-middle" style="gap:16px;">
                        @if($settings['hero_image_path'])
                            <img src="{{ asset('fichiers/'.$settings['hero_image_path']) }}" alt="" style="height:64px; width:112px; border-radius:8px; object-fit:cover;">
                        @endif
                        <input type="file" id="hero_image" name="hero_image" accept="image/*" class="uk-input">
                    </div>
                </div>
            </div>

            <div class="uk-margin-top" style="border-top:1px solid rgba(29,138,78,.1); padding-top:24px;">
                <label class="uk-flex uk-flex-middle" style="gap:8px; font-weight:600; font-size:.875rem;">
                    <input type="checkbox" name="announcement_active" value="1" @checked(old('announcement_active', $settings['announcement_active'])) class="uk-checkbox">
                    Afficher un bandeau d'annonce en haut du site
                </label>
                <input type="text" name="announcement_text" value="{{ old('announcement_text', $settings['announcement_text']) }}" placeholder="Ex : Livraison offerte dès 50 000 FCFA d'achat" class="uk-input uk-margin-small-top">
            </div>
        </div>

        <div class="uk-margin-top" style="border-top:1px solid rgba(29,138,78,.1); padding-top:32px;">
            <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">Sections de la page d'accueil</h2>
            <div class="uk-grid-small uk-child-width-1-2@s uk-margin-small-top" uk-grid>
                @php
                    $sectionToggles = [
                        'show_featured_products' => 'Produits en vedette',
                        'show_producers' => 'Nos producteurs',
                        'show_testimonials' => 'Avis clients',
                        'show_newsletter' => 'Bloc newsletter',
                    ];
                @endphp
                @foreach($sectionToggles as $key => $label)
                    <label class="uk-flex uk-flex-middle uk-text-small" style="gap:8px;">
                        <input type="checkbox" name="{{ $key }}" value="1" @checked(old($key, $settings[$key])) class="uk-checkbox">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

        <button type="submit" class="uk-button uk-button-primary uk-margin-top">Enregistrer les paramètres</button>
    </form>
</div>
@endsection
