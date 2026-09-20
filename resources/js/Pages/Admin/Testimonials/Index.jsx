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

export default function Index({ testimonials }) {
    function handleDelete(testimonial) {
        if (!confirm('Supprimer cet avis ?')) return;
        router.delete(route('admin.avis.destroy', testimonial.id), { preserveScroll: true });
    }

    return (
        <AdminLayout title="Avis clients">
            <Head title="Avis clients — Administration" />

            <div className="flex flex-wrap items-center justify-between gap-3">
                <p className="text-sm text-terroir-dark/50">{testimonials.total} avis</p>
                <Link href={route('admin.avis.create')} className="btn-primary">
                    <span className="material-symbols-outlined text-lg">add</span>
                    Nouvel avis
                </Link>
            </div>

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="admin-card mt-6 overflow-x-auto p-0"
            >
                <table className="admin-table">
                    <thead>
                        <tr>
                            <th className="pl-6">Auteur</th>
                            <th>Note</th>
                            <th>Statut</th>
                            <th className="pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {testimonials.data.length === 0 ? (
                            <tr><td colSpan={4} className="py-8 text-center text-terroir-dark/40">Aucun avis.</td></tr>
                        ) : testimonials.data.map((testimonial) => (
                            <tr key={testimonial.id}>
                                <td className="pl-6">
                                    <p className="font-semibold text-terroir-dark">{testimonial.author_name}</p>
                                    <p className="text-xs text-terroir-dark/50">{testimonial.author_role}</p>
                                </td>
                                <td className="text-terroir-gold"><Stars rating={testimonial.rating} /></td>
                                <td>
                                    {testimonial.is_published ? (
                                        <span className="admin-badge-success">Publié</span>
                                    ) : (
                                        <span className="admin-badge-neutral">Masqué</span>
                                    )}
                                </td>
                                <td className="pr-6 text-right">
                                    <Link href={route('admin.avis.edit', testimonial.id)} className="admin-link">Modifier</Link>
                                    <button type="button" onClick={() => handleDelete(testimonial)} className="admin-link-danger ml-3 bg-transparent">Supprimer</button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>

            {testimonials.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {testimonials.links.map((link, i) =>
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
