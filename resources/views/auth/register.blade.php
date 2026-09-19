@extends('layouts.app')

@section('title', "Créer un compte — Central d'Achat")

@section('content')
<section class="mx-auto flex max-w-lg flex-col px-4 py-24 sm:px-6 lg:px-8">
    <h1 class="section-title text-center">Créer un compte</h1>
    <p class="mt-2 text-center text-sm text-terroir-dark/60">Particulier, hôtel, restaurant, entreprise... rejoignez Central d'Achat.</p>

    @php
        $b2bTypes = ['professionnel', 'hotel', 'restaurant', 'entreprise', 'institution', 'revendeur'];
        $initialType = old('user_type', request('type', 'particulier'));
    @endphp
    <form action="{{ route('register') }}" method="POST" class="card mt-10 space-y-5 p-8" x-data="{ type: '{{ $initialType }}' }">
        @csrf
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="label" for="name">Nom complet</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus class="input">
                @error('name') <p class="mt-1 text-xs text-terroir-terracotta">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="phone">Téléphone</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="input">
            </div>
        </div>

        <div>
            <label class="label" for="email">E-mail</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required class="input">
            @error('email') <p class="mt-1 text-xs text-terroir-terracotta">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label" for="user_type">Type de compte</label>
            <select id="user_type" name="user_type" required class="input" x-model="type">
                @foreach(['particulier' => 'Particulier', 'professionnel' => 'Professionnel', 'hotel' => 'Hôtel', 'restaurant' => 'Restaurant', 'entreprise' => 'Entreprise', 'institution' => 'Institution', 'touriste' => 'Touriste', 'revendeur' => 'Revendeur'] as $value => $label)
                    <option value="{{ $value }}" @selected($initialType === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div x-show="{{ json_encode($b2bTypes) }}.includes(type)" x-cloak class="rounded-xl bg-terroir-cream/60 p-4 text-xs text-terroir-dark/60">
            En tant que compte professionnel, votre demande sera examinée par notre équipe avant validation. Une fois validé, vous accédez aux tarifs professionnels/de gros, aux devis et aux commandes récurrentes.
        </div>

        <div>
            <label class="label" for="company_name">Nom de l'entreprise / structure (optionnel)</label>
            <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" class="input">
        </div>

        <div x-show="{{ json_encode($b2bTypes) }}.includes(type)" x-cloak>
            <label class="label" for="business_registration_number">Numéro NINEA / RCCM (optionnel)</label>
            <input type="text" id="business_registration_number" name="business_registration_number" value="{{ old('business_registration_number') }}" class="input">
            @error('business_registration_number') <p class="mt-1 text-xs text-terroir-terracotta">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="label" for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required class="input">
                @error('password') <p class="mt-1 text-xs text-terroir-terracotta">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="password_confirmation">Confirmer</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="input">
            </div>
        </div>

        <button type="submit" class="btn-primary w-full justify-center">Créer mon compte</button>
    </form>

    <p class="mt-6 text-center text-sm text-terroir-dark/60">
        Déjà un compte ?
        <a href="{{ route('login') }}" class="font-semibold text-terroir-green hover:underline">Se connecter</a>
    </p>
</section>
@endsection
