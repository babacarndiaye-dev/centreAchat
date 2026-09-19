@extends('layouts.admin')

@section('title', 'Attributs produits')

@section('content')
<div class="uk-card uk-card-default" style="max-width:56rem; padding:32px;">
    <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">Nouvel attribut</h2>
    <p class="uk-text-small uk-text-muted uk-margin-small-top">Ex : Bio, Couleur, Origine précise... Apparaîtra comme champ libre sur chaque fiche produit.</p>
    <form action="{{ route('admin.produits-parametres.attributs.store') }}" method="POST" class="uk-flex uk-margin-top" style="gap:8px;">
        @csrf
        <input type="text" name="name" placeholder="Nom de l'attribut" required class="uk-input uk-width-expand">
        <button type="submit" class="uk-button uk-button-primary">Ajouter</button>
    </form>

    <div class="uk-margin-large-top">
        @foreach($productAttributes as $attribute)
            <div class="uk-flex uk-flex-wrap uk-flex-middle uk-margin-small-top" style="gap:12px; background:#F7F8F5; border-radius:8px; padding:10px 16px;">
                <form action="{{ route('admin.produits-parametres.attributs.update', $attribute) }}" method="POST" class="uk-flex uk-flex-middle uk-width-expand" style="gap:12px;">
                    @csrf @method('PATCH')
                    <input type="text" name="name" value="{{ $attribute->name }}" class="uk-input uk-width-expand">
                    <label class="uk-text-small uk-flex uk-flex-middle" style="gap:4px; white-space:nowrap;"><input type="checkbox" name="is_active" value="1" @checked($attribute->is_active) class="uk-checkbox"> Actif</label>
                    <button type="submit" style="font-weight:600; color:#1D8A4E; background:none; border:none; cursor:pointer;" class="uk-text-small">Enregistrer</button>
                </form>
                <form action="{{ route('admin.produits-parametres.attributs.destroy', $attribute) }}" method="POST" onsubmit="return confirm('Supprimer cet attribut ?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;" class="uk-text-small">Supprimer</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
