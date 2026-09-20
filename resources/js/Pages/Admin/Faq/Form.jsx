import { Link } from '@inertiajs/react';

export default function FaqForm({ data, setData, errors, processing, onSubmit, submitLabel }) {
    return (
        <form onSubmit={onSubmit}>
            <div>
                <label className="label">Question</label>
                <input
                    type="text"
                    value={data.question}
                    onChange={(e) => setData('question', e.target.value)}
                    required
                    className="input"
                />
                {errors.question && <p className="mt-1 text-xs text-terroir-terracotta">{errors.question}</p>}
            </div>
            <div className="mt-6">
                <label className="label">Réponse</label>
                <textarea
                    rows={5}
                    value={data.answer}
                    onChange={(e) => setData('answer', e.target.value)}
                    required
                    className="input"
                />
                {errors.answer && <p className="mt-1 text-xs text-terroir-terracotta">{errors.answer}</p>}
            </div>
            <div className="mt-6 grid gap-4 sm:grid-cols-2">
                <div>
                    <label className="label">Mots-clés supplémentaires</label>
                    <input
                        type="text"
                        value={data.keywords}
                        onChange={(e) => setData('keywords', e.target.value)}
                        placeholder="mangue, fruit, saison"
                        className="input"
                    />
                    <p className="mt-2 text-sm text-terroir-dark/50">Séparés par des virgules. Utilisés en plus des mots de la question pour la recherche.</p>
                </div>
                <div>
                    <label className="label">Catégorie</label>
                    <input
                        type="text"
                        value={data.category}
                        onChange={(e) => setData('category', e.target.value)}
                        className="input"
                    />
                </div>
            </div>
            <div className="mt-6">
                <label className="label">Position d'affichage</label>
                <input
                    type="number"
                    value={data.position}
                    onChange={(e) => setData('position', e.target.value)}
                    className="input"
                    style={{ maxWidth: '8rem' }}
                />
            </div>
            <label className="mt-6 flex items-center gap-2 text-sm">
                <input
                    type="checkbox"
                    checked={data.is_active}
                    onChange={(e) => setData('is_active', e.target.checked)}
                    className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"
                />
                Active
            </label>

            <div className="mt-6 flex gap-3">
                <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">{submitLabel}</button>
                <Link href={route('admin.messagerie.faq.index')} className="btn-outline">Annuler</Link>
            </div>
        </form>
    );
}
