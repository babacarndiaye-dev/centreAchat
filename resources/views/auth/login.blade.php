@extends('layouts.app')

@section('title', "Connexion — DIABA HOTEL Produits du Sénégal (D.H.P.S)")

@section('content')
<section class="mx-auto flex max-w-md flex-col px-4 py-24 sm:px-6 lg:px-8">
    <h1 class="section-title text-center">Connexion</h1>

    <form action="{{ route('login') }}" method="POST" class="card mt-10 space-y-5 p-8">
        @csrf
        <div>
            <label class="label" for="email">E-mail</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus class="input">
            @error('email') <p class="mt-1 text-xs text-terroir-terracotta">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="label" for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required class="input">
        </div>
        <label class="flex items-center gap-2 text-sm text-terroir-dark/70">
            <input type="checkbox" name="remember" class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green">
            Se souvenir de moi
        </label>
        <button type="submit" class="btn-primary w-full justify-center">Se connecter</button>
    </form>

    <p class="mt-6 text-center text-sm text-terroir-dark/60">
        Pas encore de compte ?
        <a href="{{ route('register') }}" class="font-semibold text-terroir-green hover:underline">Créer un compte</a>
    </p>
</section>
@endsection
