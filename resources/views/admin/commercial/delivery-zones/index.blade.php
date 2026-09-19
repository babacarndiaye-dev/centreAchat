@extends('layouts.admin')

@section('title', 'Zones de livraison')

@section('content')
<div class="uk-card uk-card-default" style="max-width:48rem; padding:32px;">
    <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">Nouvelle zone de livraison</h2>
    <form action="{{ route('admin.commercial.zones.store') }}" method="POST" class="uk-grid-small uk-margin-top" uk-grid>
        @csrf
        <input type="text" name="name" placeholder="Nom (ex : Mbour centre)" required class="uk-input uk-width-1-1 uk-width-2-5@s">
        <input type="text" name="cities" placeholder="Villes couvertes" class="uk-input uk-width-1-1 uk-width-1-5@s">
        <input type="number" step="0.01" name="fee" placeholder="Tarif FCFA" required class="uk-input uk-width-1-1 uk-width-1-5@s">
        <input type="number" name="delay_days" placeholder="Délai (jours)" class="uk-input uk-width-1-1 uk-width-1-5@s">
        <button type="submit" class="uk-button uk-button-primary uk-width-1-1">Ajouter</button>
    </form>

    <div class="uk-margin-large-top" style="display:flex; flex-direction:column; gap:8px;">
        @foreach($deliveryZones as $zone)
            <div class="uk-flex uk-flex-wrap uk-flex-middle" style="gap:12px; background:#F7F8F5; border-radius:8px; padding:12px 16px;">
                <form action="{{ route('admin.commercial.zones.update', $zone) }}" method="POST" class="uk-flex uk-flex-wrap uk-flex-middle" style="gap:8px; flex:1;">
                    @csrf @method('PATCH')
                    <input type="text" name="name" value="{{ $zone->name }}" class="uk-input" style="flex:1; min-width:140px;">
                    <input type="text" name="cities" value="{{ $zone->cities }}" placeholder="Villes" class="uk-input" style="width:160px;">
                    <input type="number" step="0.01" name="fee" value="{{ $zone->fee }}" class="uk-input" style="width:112px;">
                    <input type="number" step="0.01" name="free_above" value="{{ $zone->free_above }}" placeholder="Gratuit dès" class="uk-input" style="width:128px;">
                    <input type="number" name="delay_days" value="{{ $zone->delay_days }}" placeholder="Jours" class="uk-input" style="width:80px;">
                    <label class="uk-flex uk-flex-middle uk-text-small" style="gap:4px; white-space:nowrap;"><input type="checkbox" name="is_active" value="1" @checked($zone->is_active) class="uk-checkbox"> Actif</label>
                    <button type="submit" style="font-size:.875rem; font-weight:600; color:#1D8A4E; background:none; border:none; cursor:pointer;">Enregistrer</button>
                </form>
                <form action="{{ route('admin.commercial.zones.destroy', $zone) }}" method="POST" onsubmit="return confirm('Supprimer cette zone ?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="font-size:.875rem; font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;">Supprimer</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
