@csrf

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label" for="name">Nom</label>
        <input type="text" id="name" name="name" value="{{ old('name', $supplier->name ?? '') }}" required class="input">
    </div>
    <div>
        <label class="label" for="company_name">Raison sociale</label>
        <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $supplier->company_name ?? '') }}" class="input">
    </div>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label" for="contact_name">Responsable</label>
        <input type="text" id="contact_name" name="contact_name" value="{{ old('contact_name', $supplier->contact_name ?? '') }}" class="input">
    </div>
    <div>
        <label class="label" for="status">Statut</label>
        <select id="status" name="status" required class="input">
            @foreach(\App\Models\Supplier::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $supplier->status ?? 'en_attente') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label" for="phone">Téléphone</label>
        <input type="text" id="phone" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}" required class="input">
    </div>
    <div>
        <label class="label" for="email">E-mail</label>
        <input type="email" id="email" name="email" value="{{ old('email', $supplier->email ?? '') }}" class="input">
    </div>
</div>

<div class="mt-6">
    <label class="label" for="address">Adresse</label>
    <textarea id="address" name="address" rows="2" class="input">{{ old('address', $supplier->address ?? '') }}</textarea>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label" for="city">Ville</label>
        <input type="text" id="city" name="city" value="{{ old('city', $supplier->city ?? '') }}" class="input">
    </div>
    <div>
        <label class="label" for="region">Région</label>
        <input type="text" id="region" name="region" value="{{ old('region', $supplier->region ?? '') }}" class="input">
    </div>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-3">
    <div>
        <label class="label" for="payment_terms">Conditions de paiement</label>
        <input type="text" id="payment_terms" name="payment_terms" value="{{ old('payment_terms', $supplier->payment_terms ?? '') }}" placeholder="Ex : 30 jours" class="input">
    </div>
    <div>
        <label class="label" for="delivery_delay_days">Délai de livraison (jours)</label>
        <input type="number" id="delivery_delay_days" name="delivery_delay_days" value="{{ old('delivery_delay_days', $supplier->delivery_delay_days ?? '') }}" class="input">
    </div>
    <div>
        <label class="label" for="rating">Note (0 à 5)</label>
        <input type="number" step="0.1" min="0" max="5" id="rating" name="rating" value="{{ old('rating', $supplier->rating ?? '') }}" class="input">
    </div>
</div>

<div class="mt-6">
    <label class="label" for="notes">Notes internes</label>
    <textarea id="notes" name="notes" rows="3" class="input">{{ old('notes', $supplier->notes ?? '') }}</textarea>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-primary">Enregistrer</button>
    <a href="{{ route('admin.fournisseurs.index') }}" class="btn-outline">Annuler</a>
</div>
