import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

function Stars({ rating }) {
    return (
        <span className="inline-flex gap-0.5">
            {[1, 2, 3, 4, 5].map((s) => (
                <span key={s} className={'material-symbols-outlined text-base' + (s <= rating ? ' is-filled' : '')}>star</span>
            ))}
        </span>
    );
}

function truncate(text, length) {
    if (!text) return '—';
    return text.length > length ? text.slice(0, length) + '…' : text;
}

export default function Index({ reviews }) {
    function handleToggle(review) {
        router.patch(route('admin.avis-produits.toggle', review.id), {}, { preserveScroll: true });
    }

    function handleDelete(review) {
        if (!confirm('Supprimer cet avis ?')) return;
        router.delete(route('admin.avis-produits.destroy', review.id), { preserveScroll: true });
    }

    return (
        <AdminLayout title="Avis produits">
            <Head title="Avis produits — Administration" />

            <p className="text-sm text-terroir-dark/50">
                {reviews.total} avis produits (venant des fiches produits, distincts des avis clients généraux)
            </p>

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="admin-card mt-6 overflow-x-auto p-0"
            >
                <table className="admin-table">
                    <thead>
                        <tr>
                            <th className="pl-6">Produit</th>
                            <th>Client</th>
                            <th>Note</th>
                            <th>Commentaire</th>
                            <th>Statut</th>
                            <th className="pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {reviews.data.length === 0 ? (
                            <tr><td colSpan={6} className="py-8 text-center text-terroir-dark/40">Aucun avis produit.</td></tr>
                        ) : reviews.data.map((review) => (
                            <tr key={review.id}>
                                <td className="pl-6 font-semibold text-terroir-dark">{review.product_name}</td>
                                <td className="text-terroir-dark/70">{review.user_name}</td>
                                <td className="text-terroir-gold"><Stars rating={review.rating} /></td>
                                <td className="text-terroir-dark/60">{truncate(review.comment, 80)}</td>
                                <td>
                                    {review.is_approved ? (
                                        <span className="admin-badge-success">Publié</span>
                                    ) : (
                                        <span className="admin-badge-neutral">Masqué</span>
                                    )}
                                </td>
                                <td className="pr-6 text-right">
                                    <button type="button" onClick={() => handleToggle(review)} className="admin-link bg-transparent">
                                        {review.is_approved ? 'Masquer' : 'Republier'}
                                    </button>
                                    <button type="button" onClick={() => handleDelete(review)} className="admin-link-danger ml-3 bg-transparent">Supprimer</button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>

            {reviews.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {reviews.links.map((link, i) =>
                        link.url ? (
                            <Link
                                key={i}
                                href={link.url}
                                preserveScroll
                                className={
                                    'rounded-lg px-3 py-1.5 text-sm ' +
                                    (link.active ? 'bg-terroir-green text-white' : 'bg-white text-terroir-dark/70 hover:bg-terroir-cream')
                                }
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ) : (
                            <span key={i} className="rounded-lg px-3 py-1.5 text-sm text-terroir-dark/30" dangerouslySetInnerHTML={{ __html: link.label }} />
                        )
                    )}
                </div>
            )}
        </AdminLayout>
    );
}
