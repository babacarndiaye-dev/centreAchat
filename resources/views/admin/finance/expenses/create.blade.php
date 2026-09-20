@extends('layouts.admin')

@section('title', 'Nouvelle dépense')

@section('content')
<div class="admin-card max-w-3xl">
    <form action="{{ route('admin.depenses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label" for="expense_category_id">Catégorie</label>
                <select id="expense_category_id" name="expense_category_id" required class="input">
                    <option value="">Choisir...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="expense_date">Date</label>
                <input type="date" id="expense_date" name="expense_date" value="{{ now()->format('Y-m-d') }}" required class="input">
            </div>
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label" for="amount">Montant (FCFA)</label>
                <input type="number" step="0.01" id="amount" name="amount" required class="input">
            </div>
            <div>
                <label class="label" for="beneficiary">Bénéficiaire (optionnel)</label>
                <input type="text" id="beneficiary" name="beneficiary" class="input">
            </div>
        </div>

        <div class="mt-4">
            <label class="label" for="payment_account_id">Compte de paiement</label>
            <select id="payment_account_id" name="payment_account_id" class="input">
                <option value="">Non renseigné pour l'instant</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }} ({{ \App\Models\PaymentAccount::TYPES[$account->type] }})</option>
                @endforeach
            </select>
            <p class="mt-1.5 text-sm text-terroir-dark/50">Le compte sera débité automatiquement lors de la validation de la dépense.</p>
        </div>

        <div class="mt-4">
            <label class="label" for="description">Description (optionnel)</label>
            <textarea id="description" name="description" rows="3" class="input"></textarea>
        </div>

        <div class="mt-4">
            <label class="label" for="receipt">Justificatif (optionnel)</label>
            <div uk-form-custom="target: true">
                <input type="file" id="receipt" name="receipt" accept="image/*">
                <span class="input inline-flex items-center text-terroir-dark/50">Choisir un fichier...</span>
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="btn-primary">Enregistrer la dépense</button>
            <a href="{{ route('admin.depenses.index') }}" class="btn-outline">Annuler</a>
        </div>
    </form>
</div>
@endsection
