@extends('layouts.admin')

@section('title', 'Taxes')

@section('content')
<div class="uk-card uk-card-default" style="max-width:40rem; padding:32px;">
    <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">Nouveau taux de taxe</h2>
    <form action="{{ route('admin.commercial.taxes.store') }}" method="POST" class="uk-flex uk-margin-top" style="gap:8px;">
        @csrf
        <input type="text" name="name" placeholder="Ex : TVA standard" required class="uk-input" style="flex:1;">
        <input type="number" step="0.01" name="rate" placeholder="Taux %" required class="uk-input" style="width:128px;">
        <button type="submit" class="uk-button uk-button-primary">Ajouter</button>
    </form>

    <div class="uk-margin-large-top" style="display:flex; flex-direction:column; gap:8px;">
        @foreach($taxRates as $tax)
            <div class="uk-flex uk-flex-middle uk-flex-between" style="background:#F7F8F5; border-radius:8px; padding:10px 16px;">
                <form action="{{ route('admin.commercial.taxes.update', $tax) }}" method="POST" class="uk-flex uk-flex-middle" style="gap:12px; flex:1;">
                    @csrf @method('PATCH')
                    <input type="text" name="name" value="{{ $tax->name }}" class="uk-input" style="flex:1;">
                    <input type="number" step="0.01" name="rate" value="{{ $tax->rate }}" class="uk-input" style="width:96px;">
                    <span class="uk-text-small">%</span>
                    <label class="uk-flex uk-flex-middle uk-text-small" style="gap:4px; white-space:nowrap;">
                        <input type="checkbox" name="is_active" value="1" @checked($tax->is_active) class="uk-checkbox">
                        Actif
                    </label>
                    <button type="submit" style="font-size:.875rem; font-weight:600; color:#1D8A4E; background:none; border:none; cursor:pointer;">Enregistrer</button>
                </form>
                @if($tax->is_default)
                    <span class="uk-label" style="margin-left:12px; background:rgba(29,138,78,.1); color:#1D8A4E;">Par défaut</span>
                @else
                    <form action="{{ route('admin.commercial.taxes.default', $tax) }}" method="POST" style="margin-left:12px;">
                        @csrf @method('PATCH')
                        <button type="submit" style="font-size:.75rem; font-weight:600; color:rgba(31,35,40,.6); background:none; border:none; cursor:pointer;">Définir par défaut</button>
                    </form>
                @endif
                <form action="{{ route('admin.commercial.taxes.destroy', $tax) }}" method="POST" onsubmit="return confirm('Supprimer ce taux ?')" style="margin-left:12px;">
                    @csrf @method('DELETE')
                    <button type="submit" style="font-size:.875rem; font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;">Supprimer</button>
                </form>
            </div>
        @endforeach
    </div>
    <p class="uk-text-small uk-text-muted uk-margin-top">Le taux "par défaut" est celui appliqué automatiquement au calcul des commandes.</p>
</div>
@endsection
