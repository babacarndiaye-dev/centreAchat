@csrf

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label" for="name">Désignation</label>
        <input type="text" id="name" name="name" value="{{ old('name', $asset->name ?? '') }}" required class="input">
    </div>
    <div>
        <label class="label" for="category">Catégorie</label>
        <select id="category" name="category" required class="input">
            @foreach(\App\Models\FixedAsset::CATEGORIES as $value => $label)
                <option value="{{ $value }}" @selected(old('category', $asset->category ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label" for="acquisition_date">Date d'acquisition</label>
        <input type="date" id="acquisition_date" name="acquisition_date" value="{{ old('acquisition_date', isset($asset) ? $asset->acquisition_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required class="input">
    </div>
    <div>
        <label class="label" for="acquisition_value">Valeur d'acquisition (FCFA)</label>
        <input type="number" step="0.01" id="acquisition_value" name="acquisition_value" value="{{ old('acquisition_value', $asset->acquisition_value ?? '') }}" required class="input">
    </div>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label" for="useful_life_years">Durée d'amortissement (années)</label>
        <input type="number" id="useful_life_years" name="useful_life_years" value="{{ old('useful_life_years', $asset->useful_life_years ?? 5) }}" min="1" max="50" required class="input">
    </div>
    <div>
        <label class="label" for="depreciation_method">Méthode d'amortissement</label>
        <select id="depreciation_method" name="depreciation_method" required class="input">
            @foreach(\App\Models\FixedAsset::METHODS as $value => $label)
                <option value="{{ $value }}" @selected(old('depreciation_method', $asset->depreciation_method ?? 'lineaire') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label" for="payment_account_id">Compte de paiement (optionnel)</label>
        <select id="payment_account_id" name="payment_account_id" class="input">
            <option value="">Non renseigné</option>
            @foreach($paymentAccounts as $account)
                <option value="{{ $account->id }}" @selected(old('payment_account_id', $asset->payment_account_id ?? '') == $account->id)>{{ $account->name }}</option>
            @endforeach
        </select>
        <p class="mt-2 text-xs text-terroir-dark/50">Génère l'écriture comptable d'acquisition si renseigné.</p>
    </div>
    <div>
        <label class="label" for="supplier_id">Fournisseur (optionnel)</label>
        <select id="supplier_id" name="supplier_id" class="input">
            <option value="">Non renseigné</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected(old('supplier_id', $asset->supplier_id ?? '') == $supplier->id)>{{ $supplier->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mt-6">
    <label class="label" for="notes">Notes (optionnel)</label>
    <textarea id="notes" name="notes" rows="2" class="input">{{ old('notes', $asset->notes ?? '') }}</textarea>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-primary">Enregistrer</button>
    <a href="{{ route('admin.immobilisations.index') }}" class="btn-outline">Annuler</a>
</div>
