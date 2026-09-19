@php($entry = $entry ?? null)

<div>
    <label class="label">Question</label>
    <input type="text" name="question" value="{{ old('question', $entry->question ?? '') }}" required class="input">
</div>
<div class="mt-6">
    <label class="label">Réponse</label>
    <textarea name="answer" rows="5" required class="input">{{ old('answer', $entry->answer ?? '') }}</textarea>
</div>
<div class="mt-6 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label">Mots-clés supplémentaires</label>
        <input type="text" name="keywords" value="{{ old('keywords', $entry->keywords ?? '') }}" placeholder="mangue, fruit, saison" class="input">
        <p class="mt-2 text-sm text-terroir-dark/50">Séparés par des virgules. Utilisés en plus des mots de la question pour la recherche.</p>
    </div>
    <div>
        <label class="label">Catégorie</label>
        <input type="text" name="category" value="{{ old('category', $entry->category ?? '') }}" class="input">
    </div>
</div>
<div class="mt-6">
    <label class="label">Position d'affichage</label>
    <input type="number" name="position" value="{{ old('position', $entry->position ?? 0) }}" class="input" style="max-width:8rem;">
</div>
<label class="mt-6 flex items-center gap-2 text-sm">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $entry->is_active ?? true)) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
    Active
</label>

<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-primary">Enregistrer</button>
    <a href="{{ route('admin.messagerie.faq.index') }}" class="btn-outline">Annuler</a>
</div>
