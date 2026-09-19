@extends('layouts.admin')

@section('title', 'Nouvelle dépense')

@section('content')
<div class="uk-card uk-card-default" style="max-width:56rem; padding:32px;">
    <form action="{{ route('admin.depenses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="uk-grid-small uk-child-width-1-2@s" uk-grid>
            <div>
                <label class="uk-form-label" for="expense_category_id">Catégorie</label>
                <select id="expense_category_id" name="expense_category_id" required class="uk-select">
                    <option value="">Choisir...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="uk-form-label" for="expense_date">Date</label>
                <input type="date" id="expense_date" name="expense_date" value="{{ now()->format('Y-m-d') }}" required class="uk-input">
            </div>
        </div>

        <div class="uk-grid-small uk-child-width-1-2@s uk-margin-top" uk-grid>
            <div>
                <label class="uk-form-label" for="amount">Montant (FCFA)</label>
                <input type="number" step="0.01" id="amount" name="amount" required class="uk-input">
            </div>
            <div>
                <label class="uk-form-label" for="beneficiary">Bénéficiaire (optionnel)</label>
                <input type="text" id="beneficiary" name="beneficiary" class="uk-input">
            </div>
        </div>

        <div class="uk-margin-top">
            <label class="uk-form-label" for="payment_account_id">Compte de paiement</label>
            <select id="payment_account_id" name="payment_account_id" class="uk-select">
                <option value="">Non renseigné pour l'instant</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }} ({{ \App\Models\PaymentAccount::TYPES[$account->type] }})</option>
                @endforeach
            </select>
            <p class="uk-text-small uk-text-muted uk-margin-small-top">Le compte sera débité automatiquement lors de la validation de la dépense.</p>
        </div>

        <div class="uk-margin-top">
            <label class="uk-form-label" for="description">Description (optionnel)</label>
            <textarea id="description" name="description" rows="3" class="uk-textarea"></textarea>
        </div>

        <div class="uk-margin-top">
            <label class="uk-form-label" for="receipt">Justificatif (optionnel)</label>
            <div uk-form-custom="target: true">
                <input type="file" id="receipt" name="receipt" accept="image/*">
                <span class="uk-input" style="display:inline-flex; align-items:center; color:rgba(31,35,40,.5);">Choisir un fichier...</span>
            </div>
        </div>

        <div class="uk-flex uk-margin-top" style="gap:12px;">
            <button type="submit" class="uk-button uk-button-primary">Enregistrer la dépense</button>
            <a href="{{ route('admin.depenses.index') }}" class="uk-button uk-button-default">Annuler</a>
        </div>
    </form>
</div>
@endsection
