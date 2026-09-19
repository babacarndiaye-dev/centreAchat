@extends('layouts.admin')

@section('title', 'Catégories de dépenses')

@section('content')
<div class="uk-card uk-card-default" style="max-width:48rem; padding:32px;">
    <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.1rem; margin:0;">Nouvelle catégorie</h2>
    <form action="{{ route('admin.categories-depenses.store') }}" method="POST" class="uk-flex uk-margin-small-top" style="gap:8px;">
        @csrf
        <input type="text" name="name" placeholder="Ex : Loyer, Électricité, Carburant..." required class="uk-input" style="flex:1;">
        <select name="chart_account_id" class="uk-select" style="max-width:280px;">
            <option value="">Compte comptable (optionnel)</option>
            @foreach($chartAccounts as $chartAccount)
                <option value="{{ $chartAccount->id }}">{{ $chartAccount->code }} — {{ $chartAccount->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="uk-button uk-button-primary">Ajouter</button>
    </form>

    <div class="uk-margin-top" style="display:flex; flex-direction:column; gap:8px;">
        @foreach($categories as $category)
            <div class="uk-flex uk-flex-middle" style="justify-content:space-between; background:#F7F8F5; border-radius:8px; padding:10px 16px;">
                <form action="{{ route('admin.categories-depenses.update', $category) }}" method="POST" class="uk-flex uk-flex-middle" style="flex:1; gap:12px;">
                    @csrf @method('PATCH')
                    <input type="text" name="name" value="{{ $category->name }}" class="uk-input" style="flex:1;">
                    <select name="chart_account_id" class="uk-select" style="max-width:220px;">
                        <option value="">Compte comptable</option>
                        @foreach($chartAccounts as $chartAccount)
                            <option value="{{ $chartAccount->id }}" @selected($category->chart_account_id === $chartAccount->id)>{{ $chartAccount->code }} — {{ $chartAccount->name }}</option>
                        @endforeach
                    </select>
                    <label class="uk-flex uk-flex-middle uk-text-small" style="gap:4px; white-space:nowrap;">
                        <input type="checkbox" name="is_active" value="1" @checked($category->is_active) class="uk-checkbox">
                        Actif
                    </label>
                    <button type="submit" class="uk-text-small" style="font-weight:600; color:#1D8A4E; background:none; border:none; cursor:pointer;">Enregistrer</button>
                </form>
                <form action="{{ route('admin.categories-depenses.destroy', $category) }}" method="POST" onsubmit="return confirm('Supprimer cette catégorie ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="uk-text-small" style="margin-left:12px; font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;">Supprimer</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
