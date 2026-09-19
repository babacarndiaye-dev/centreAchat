@extends('layouts.admin')

@section('title', 'Ouvrir la caisse')

@section('content')
<div class="uk-card uk-card-default" style="max-width:28rem; padding:32px;">
    <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">Ouverture de caisse</h2>
    <p class="uk-text-small uk-text-muted uk-margin-small-top">Indiquez le fond de caisse initial pour démarrer une nouvelle session.</p>

    <form action="{{ route('admin.pos.caisse.store') }}" method="POST" class="uk-margin-top" style="display:flex; flex-direction:column; gap:16px;">
        @csrf
        <div>
            <label class="uk-form-label" for="opening_float">Fond de caisse (FCFA)</label>
            <input type="number" step="0.01" id="opening_float" name="opening_float" value="0" required class="uk-input">
        </div>
        <div>
            <label class="uk-form-label" for="notes">Notes (optionnel)</label>
            <textarea id="notes" name="notes" rows="2" class="uk-textarea"></textarea>
        </div>
        <button type="submit" class="uk-button uk-button-primary uk-width-1-1">Ouvrir la caisse</button>
    </form>
</div>
@endsection
