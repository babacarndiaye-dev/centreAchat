@csrf

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label" for="name">Nom</label>
        <input type="text" id="name" name="name" value="{{ old('name', $category->name ?? '') }}" required class="input">
    </div>
    <div>
        <label class="label" for="parent_id">Catégorie parente (optionnel)</label>
        <select id="parent_id" name="parent_id" class="input">
            <option value="">Aucune (catégorie principale)</option>
            @foreach($parents as $parent)
                <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id ?? '') == $parent->id)>{{ $parent->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mt-6">
    <label class="label" for="description">Description</label>
    <textarea id="description" name="description" rows="3" class="input">{{ old('description', $category->description ?? '') }}</textarea>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label" for="position">Position (ordre d'affichage)</label>
        <input type="number" id="position" name="position" value="{{ old('position', $category->position ?? 0) }}" class="input">
    </div>
    <div class="flex items-end">
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true)) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
            Catégorie active
        </label>
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-primary">Enregistrer</button>
    <a href="{{ route('admin.categories.index') }}" class="btn-outline">Annuler</a>
</div>
