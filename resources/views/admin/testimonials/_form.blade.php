@csrf

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label" for="author_name">Nom de l'auteur</label>
        <input type="text" id="author_name" name="author_name" value="{{ old('author_name', $testimonial->author_name ?? '') }}" required class="input">
    </div>
    <div>
        <label class="label" for="author_role">Rôle / structure</label>
        <input type="text" id="author_role" name="author_role" value="{{ old('author_role', $testimonial->author_role ?? '') }}" placeholder="Ex : Hôtel Teranga" class="input">
    </div>
</div>

<div class="mt-6">
    <label class="label" for="content">Témoignage</label>
    <textarea id="content" name="content" rows="4" required class="input">{{ old('content', $testimonial->content ?? '') }}</textarea>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label" for="rating">Note (1 à 5)</label>
        <input type="number" id="rating" name="rating" min="1" max="5" value="{{ old('rating', $testimonial->rating ?? 5) }}" required class="input">
    </div>
    <div class="flex items-end">
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $testimonial->is_published ?? true)) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
            Publié sur le site
        </label>
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-primary">Enregistrer</button>
    <a href="{{ route('admin.avis.index') }}" class="btn-outline">Annuler</a>
</div>
