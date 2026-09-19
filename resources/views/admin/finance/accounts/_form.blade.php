@csrf

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label" for="name">Nom du compte</label>
        <input type="text" id="name" name="name" value="{{ old('name', $account->name ?? '') }}" required class="input">
    </div>
    <div>
        <label class="label" for="type">Type</label>
        <select id="type" name="type" required class="input">
            @foreach(\App\Models\PaymentAccount::TYPES as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $account->type ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label" for="provider">Opérateur / Banque (optionnel)</label>
        <input type="text" id="provider" name="provider" value="{{ old('provider', $account->provider ?? '') }}" placeholder="Ex : Wave, Orange Money, Ecobank" class="input">
    </div>
    <div>
        <label class="label" for="account_number">Numéro de compte (optionnel)</label>
        <input type="text" id="account_number" name="account_number" value="{{ old('account_number', $account->account_number ?? '') }}" class="input">
    </div>
</div>

<div class="mt-6">
    <label class="label" for="chart_account_id">Compte comptable lié (classe 5)</label>
    <select id="chart_account_id" name="chart_account_id" class="input">
        <option value="">Non lié</option>
        @foreach($chartAccounts ?? [] as $chartAccount)
            <option value="{{ $chartAccount->id }}" @selected(old('chart_account_id', $account->chart_account_id ?? '') == $chartAccount->id)>{{ $chartAccount->code }} — {{ $chartAccount->name }}</option>
        @endforeach
    </select>
    <p class="mt-2 text-sm text-terroir-dark/50">Nécessaire pour générer automatiquement les écritures comptables liées à ce compte.</p>
</div>

<div class="mt-6">
    <label class="label" for="initial_balance">Solde initial (FCFA)</label>
    <input type="number" step="0.01" id="initial_balance" name="initial_balance" value="{{ old('initial_balance', $account->initial_balance ?? 0) }}" required class="input" style="max-width:280px;">
</div>

<div class="mt-6">
    <label class="label" for="notes">Notes (optionnel)</label>
    <textarea id="notes" name="notes" rows="2" class="input">{{ old('notes', $account->notes ?? '') }}</textarea>
</div>

<div class="mt-6">
    <label class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $account->is_active ?? true)) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
        Compte actif
    </label>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-primary">Enregistrer</button>
    <a href="{{ route('admin.comptes-paiement.index') }}" class="btn-outline">Annuler</a>
</div>
