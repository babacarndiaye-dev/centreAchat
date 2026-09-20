@extends('layouts.admin')

@section('title', 'Unités')

@section('content')
<div class="admin-card max-w-3xl">
    <h2 class="font-display text-lg font-semibold">Nouvelle unité</h2>
    <form action="{{ route('admin.produits-parametres.unites.store') }}" method="POST" class="mt-3 flex gap-2">
        @csrf
        <input type="text" name="name" placeholder="Ex : Kilogramme" required class="input flex-1">
        <input type="text" name="abbreviation" placeholder="Ex : kg" class="input w-28">
        <button type="submit" class="btn-primary">Ajouter</button>
    </form>

    <div class="mt-8 flex flex-col gap-2">
        @foreach($units as $unit)
            <div class="flex flex-wrap items-center gap-3 rounded-lg bg-terroir-cream px-4 py-2.5">
                <form action="{{ route('admin.produits-parametres.unites.update', $unit) }}" method="POST" class="flex flex-1 items-center gap-3">
                    @csrf @method('PATCH')
                    <input type="text" name="name" value="{{ $unit->name }}" class="input flex-1">
                    <input type="text" name="abbreviation" value="{{ $unit->abbreviation }}" class="input w-28">
                    <label class="flex items-center gap-1 whitespace-nowrap text-sm"><input type="checkbox" name="is_active" value="1" @checked($unit->is_active) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"> Actif</label>
                    <button type="submit" class="text-sm font-semibold text-terroir-green">Enregistrer</button>
                </form>
                <form action="{{ route('admin.produits-parametres.unites.destroy', $unit) }}" method="POST" onsubmit="return confirm('Supprimer cette unité ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="admin-link-danger bg-transparent">Supprimer</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
