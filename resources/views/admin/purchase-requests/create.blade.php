@extends('layouts.admin')

@section('title', "Nouvelle demande d'achat")

@section('content')
<div class="uk-card uk-card-default" style="max-width:56rem; padding:32px;">
    <form action="{{ route('admin.demandes-achat.store') }}" method="POST">
        @csrf

        <div class="uk-grid-small uk-child-width-1-2@s" uk-grid>
            <div>
                <label class="uk-form-label" for="needed_by_date">Besoin pour le (optionnel)</label>
                <input type="date" id="needed_by_date" name="needed_by_date" value="{{ old('needed_by_date') }}" class="uk-input">
            </div>
        </div>

        <div class="uk-margin-top">
            <label class="uk-form-label" for="reason">Motif de la demande</label>
            <textarea id="reason" name="reason" rows="2" class="uk-textarea">{{ old('reason') }}</textarea>
        </div>

        <div class="uk-margin-top" style="border-top:1px solid rgba(31,35,40,.08); padding-top:24px;">
            <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Produits nécessaires</h3>
            <p class="uk-text-small uk-text-muted">Indiquez une quantité pour chaque produit concerné, laissez à 0 sinon.</p>

            @error('products')
                <p class="uk-margin-small-top" style="font-size:.75rem; color:#E8604F;">{{ $message }}</p>
            @enderror

            <div class="uk-margin-top" style="max-height:420px; overflow-y:auto; padding-right:8px; display:flex; flex-direction:column; gap:8px;">
                @foreach($products as $product)
                    <div class="uk-flex uk-flex-middle uk-flex-between" style="gap:16px; border-radius:8px; background:rgba(247,248,245,.8); padding:10px 16px;">
                        <div>
                            <p style="font-size:.875rem; font-weight:600;">{{ $product->name }}</p>
                            <p class="uk-text-small uk-text-muted">Stock actuel : {{ $product->stock_quantity }} {{ $product->unit }}</p>
                        </div>
                        <input type="number" name="products[{{ $product->id }}]" value="{{ old('products.'.$product->id, 0) }}" min="0" class="uk-input uk-text-right" style="width:7rem;">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="uk-flex uk-flex-wrap uk-margin-top" style="gap:12px;">
            <button type="submit" name="submit_for_validation" value="0" class="uk-button uk-button-default">Enregistrer en brouillon</button>
            <button type="submit" name="submit_for_validation" value="1" class="uk-button uk-button-primary">Soumettre pour validation</button>
            <a href="{{ route('admin.demandes-achat.index') }}" class="uk-button uk-button-default">Annuler</a>
        </div>
    </form>
</div>
@endsection
