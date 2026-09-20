import { Link } from '@inertiajs/react';

export default function ProducerForm({ data, setData, errors, processing, onSubmit, submitLabel, photoUrl }) {
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
                    <label className="label" htmlFor="region">Région</label>
                    <input
                        type="text"
                        id="region"
                        value={data.region}
                        onChange={(e) => setData('region', e.target.value)}
                        className="input"
                    />
                </div>
            </div>

            <div className="mt-6">
                <label className="label" htmlFor="description">Description</label>
                <textarea
                    id="description"
                    rows={4}
                    value={data.description}
                    onChange={(e) => setData('description', e.target.value)}
                    className="input"
                />
            </div>

            <div className="mt-6">
                <label className="label" htmlFor="photo">Photo</label>
                <input
                    type="file"
                    id="photo"
                    accept="image/*"
                    onChange={(e) => setData('photo', e.target.files[0] ?? null)}
                    className="input file:mr-3 file:rounded-full file:border-0 file:bg-terroir-green file:px-4 file:py-1.5 file:text-sm file:font-semibold file:text-white file:transition hover:file:bg-terroir-dark"
                />
                {errors.photo && <p className="mt-1 text-xs text-terroir-terracotta">{errors.photo}</p>}
                {photoUrl && (
                    <img src={photoUrl} alt="" className="mt-3 h-20 w-20 rounded-lg object-cover" />
                )}
            </div>

            <div className="mt-6 flex gap-6">
                <label className="flex items-center gap-2 text-sm">
                    <input
                        type="checkbox"
                        checked={data.is_featured}
                        onChange={(e) => setData('is_featured', e.target.checked)}
                        className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"
                    />
                    Producteur vedette
                </label>
                <label className="flex items-center gap-2 text-sm">
                    <input
                        type="checkbox"
                        checked={data.is_active}
                        onChange={(e) => setData('is_active', e.target.checked)}
                        className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"
                    />
                    Actif
                </label>
            </div>

            <div className="mt-6 flex gap-3">
                <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">{submitLabel}</button>
                <Link href={route('admin.producteurs.index')} className="btn-outline">Annuler</Link>
            </div>
        </form>
    );
}
