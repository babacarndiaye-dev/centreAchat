@extends('layouts.admin')

@section('title', 'Unités')

@section('content')
<div class="uk-card uk-card-default" style="max-width:56rem; padding:32px;">
    <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">Nouvelle unité</h2>
    <form action="{{ route('admin.produits-parametres.unites.store') }}" method="POST" class="uk-flex uk-margin-top" style="gap:8px;">
        @csrf
        <input type="text" name="name" placeholder="Ex : Kilogramme" required class="uk-input uk-width-expand">
        <input type="text" name="abbreviation" placeholder="Ex : kg" class="uk-input" style="width:7rem;">
        <button type="submit" class="uk-button uk-button-primary">Ajouter</button>
    </form>

    <div class="uk-margin-large-top">
        @foreach($units as $unit)
            <div class="uk-flex uk-flex-wrap uk-flex-middle uk-margin-small-top" style="gap:12px; background:#F7F8F5; border-radius:8px; padding:10px 16px;">
                <form action="{{ route('admin.produits-parametres.unites.update', $unit) }}" method="POST" class="uk-flex uk-flex-middle uk-width-expand" style="gap:12px;">
                    @csrf @method('PATCH')
                    <input type="text" name="name" value="{{ $unit->name }}" class="uk-input uk-width-expand">
                    <input type="text" name="abbreviation" value="{{ $unit->abbreviation }}" class="uk-input" style="width:7rem;">
                    <label class="uk-text-small uk-flex uk-flex-middle" style="gap:4px; white-space:nowrap;"><input type="checkbox" name="is_active" value="1" @checked($unit->is_active) class="uk-checkbox"> Actif</label>
                    <button type="submit" style="font-weight:600; color:#1D8A4E; background:none; border:none; cursor:pointer;" class="uk-text-small">Enregistrer</button>
                </form>
                <form action="{{ route('admin.produits-parametres.unites.destroy', $unit) }}" method="POST" onsubmit="return confirm('Supprimer cette unité ?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;" class="uk-text-small">Supprimer</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
