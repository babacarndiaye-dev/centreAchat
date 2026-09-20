import { Link } from '@inertiajs/react';

export default function CategoryForm({ data, setData, errors, parents, processing, onSubmit, submitLabel }) {
    return (
        <form onSubmit={onSubmit}>
            <div className="grid gap-4 sm:grid-cols-2">
                <div>
                    <label className="label" htmlFor="name">Nom</label>
                    <input
                        type="text"
                        id="name"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        required
                        className="input"
                    />
                    {errors.name && <p className="mt-1 text-xs text-terroir-terracotta">{errors.name}</p>}
                </div>
                <div>
                    <label className="label" htmlFor="parent_id">Catégorie parente (optionnel)</label>
                    <select
                        id="parent_id"
                        value={data.parent_id ?? ''}
                        onChange={(e) => setData('parent_id', e.target.value)}
                        className="input"
                    >
                        <option value="">Aucune (catégorie principale)</option>
                        {parents.map((parent) => (
                            <option key={parent.id} value={parent.id}>{parent.name}</option>
                        ))}
                    </select>
                </div>
            </div>

            <div className="mt-6">
                <label className="label" htmlFor="description">Description</label>
                <textarea
                    id="description"
                    rows={3}
                    value={data.description ?? ''}
                    onChange={(e) => setData('description', e.target.value)}
                    className="input"
                />
            </div>

            <div className="mt-6 grid gap-4 sm:grid-cols-2">
                <div>
                    <label className="label" htmlFor="position">Position (ordre d'affichage)</label>
                    <input
                        type="number"
                        id="position"
                        value={data.position ?? 0}
                        onChange={(e) => setData('position', e.target.value)}
                        className="input"
                    />
                </div>
                <div className="flex items-end">
                    <label className="flex items-center gap-2 text-sm">
                        <input
                            type="checkbox"
                            checked={data.is_active}
                            onChange={(e) => setData('is_active', e.target.checked)}
                            className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"
                        />
                        Catégorie active
                    </label>
                </div>
            </div>

            <div className="mt-6 flex gap-3">
                <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">{submitLabel}</button>
                <Link href={route('admin.categories.index')} className="btn-outline">Annuler</Link>
            </div>
        </form>
    );
}
