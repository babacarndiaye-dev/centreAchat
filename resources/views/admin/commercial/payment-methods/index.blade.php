@extends('layouts.admin')

@section('title', 'Modes de paiement')

@section('content')
<div class="uk-card uk-card-default" style="max-width:48rem; padding:32px;">
    <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">Nouveau mode de paiement</h2>
    <form action="{{ route('admin.commercial.paiements.store') }}" method="POST" class="uk-flex uk-margin-top" style="gap:8px;">
        @csrf
        <input type="text" name="code" placeholder="Code (ex : wave)" required class="uk-input" style="width:160px;">
        <input type="text" name="name" placeholder="Nom affiché (ex : Wave)" required class="uk-input" style="flex:1;">
        <button type="submit" class="uk-button uk-button-primary">Ajouter</button>
    </form>

    <div class="uk-margin-large-top" style="display:flex; flex-direction:column; gap:8px;">
        @foreach($paymentMethods as $method)
            <div class="uk-flex uk-flex-wrap uk-flex-middle" style="gap:12px; background:#F7F8F5; border-radius:8px; padding:12px 16px;">
                <form action="{{ route('admin.commercial.paiements.update', $method) }}" method="POST" class="uk-flex uk-flex-wrap uk-flex-middle" style="gap:12px; flex:1;">
                    @csrf @method('PATCH')
                    <span class="uk-text-small" style="width:112px; font-family:monospace; color:rgba(31,35,40,.5);">{{ $method->code }}</span>
                    <input type="text" name="name" value="{{ $method->name }}" class="uk-input" style="flex:1; min-width:160px;">
                    <label class="uk-flex uk-flex-middle uk-text-small" style="gap:4px;"><input type="checkbox" name="available_online" value="1" @checked($method->available_online) class="uk-checkbox"> Site web</label>
                    <label class="uk-flex uk-flex-middle uk-text-small" style="gap:4px;"><input type="checkbox" name="available_pos" value="1" @checked($method->available_pos) class="uk-checkbox"> Caisse (POS)</label>
                    <label class="uk-flex uk-flex-middle uk-text-small" style="gap:4px;"><input type="checkbox" name="requires_b2b" value="1" @checked($method->requires_b2b) class="uk-checkbox"> Pro validé uniquement</label>
                    <label class="uk-flex uk-flex-middle uk-text-small" style="gap:4px;"><input type="checkbox" name="is_active" value="1" @checked($method->is_active) class="uk-checkbox"> Actif</label>
                    <button type="submit" style="font-size:.875rem; font-weight:600; color:#1D8A4E; background:none; border:none; cursor:pointer;">Enregistrer</button>
                </form>
                <form action="{{ route('admin.commercial.paiements.destroy', $method) }}" method="POST" onsubmit="return confirm('Supprimer {{ $method->name }} ?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="font-size:.875rem; font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;">Supprimer</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
