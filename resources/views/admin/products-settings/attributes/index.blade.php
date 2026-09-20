@extends('layouts.admin')

@section('title', 'Attributs produits')

@section('content')
<div class="admin-card max-w-3xl">
    <h2 class="font-display text-lg font-semibold">Nouvel attribut</h2>
    <p class="mt-1.5 text-sm text-terroir-dark/50">Ex : Bio, Couleur, Origine précise... Apparaîtra comme champ libre sur chaque fiche produit.</p>
    <form action="{{ route('admin.produits-parametres.attributs.store') }}" method="POST" class="mt-3 flex gap-2">
        @csrf
        <input type="text" name="name" placeholder="Nom de l'attribut" required class="input flex-1">
        <button type="submit" class="btn-primary">Ajouter</button>
    </form>

    <div class="mt-8 flex flex-col gap-2">
        @foreach($productAttributes as $attribute)
            <div class="flex flex-wrap items-center gap-3 rounded-lg bg-terroir-cream px-4 py-2.5">
                <form action="{{ route('admin.produits-parametres.attributs.update', $attribute) }}" method="POST" class="flex flex-1 items-center gap-3">
                    @csrf @method('PATCH')
                    <input type="text" name="name" value="{{ $attribute->name }}" class="input flex-1">
                    <label class="flex items-center gap-1 whitespace-nowrap text-sm"><input type="checkbox" name="is_active" value="1" @checked($attribute->is_active) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"> Actif</label>
                    <button type="submit" class="text-sm font-semibold text-terroir-green">Enregistrer</button>
                </form>
                <form action="{{ route('admin.produits-parametres.attributs.destroy', $attribute) }}" method="POST" onsubmit="return confirm('Supprimer cet attribut ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="admin-link-danger bg-transparent">Supprimer</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
