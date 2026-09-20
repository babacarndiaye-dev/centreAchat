@extends('layouts.admin')

@section('title', 'Ouvrir la caisse')

@section('content')
<div class="admin-card max-w-md">
    <h2 class="font-display text-lg font-semibold">Ouverture de caisse</h2>
    <p class="mt-1.5 text-sm text-terroir-dark/50">Indiquez le fond de caisse initial pour démarrer une nouvelle session.</p>

    <form action="{{ route('admin.pos.caisse.store') }}" method="POST" class="mt-4 flex flex-col gap-4">
        @csrf
        <div>
            <label class="label" for="opening_float">Fond de caisse (FCFA)</label>
            <input type="number" step="0.01" id="opening_float" name="opening_float" value="0" required class="input">
        </div>
        <div>
            <label class="label" for="notes">Notes (optionnel)</label>
            <textarea id="notes" name="notes" rows="2" class="input"></textarea>
        </div>
        <button type="submit" class="btn-primary w-full justify-center">Ouvrir la caisse</button>
    </form>
</div>
@endsection
