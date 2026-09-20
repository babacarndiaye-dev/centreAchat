import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Index({ entries, aiEnabled, aiModel }) {
    function handleDelete(entry) {
        if (!confirm('Supprimer cette question ?')) return;
        router.delete(route('admin.messagerie.faq.destroy', entry.id), { preserveScroll: true });
    }

    return (
        <AdminLayout title="Base de connaissances (FAQ)">
            <Head title="Base de connaissances (FAQ) — Administration" />

            <div className="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <Link href={route('admin.messagerie.index')} className="text-sm text-terroir-dark/50">&larr; Retour à la messagerie</Link>
                    <p className="mt-1.5 text-sm text-terroir-dark/50">
                        Ces questions/réponses alimentent les réponses automatiques du chatbot par correspondance de mots-clés.
                        Si aucune ne correspond, une IA de secours prend le relais en s'appuyant uniquement sur ce contenu — jamais sur ses propres connaissances.
                    </p>
                    <p className="mt-1.5 text-sm">
                        IA de secours :{' '}
                        {aiEnabled ? (
                            <span className="inline-flex items-center gap-1 font-semibold text-terroir-green">
                                <span className="material-symbols-outlined text-base">psychology</span> Active ({aiModel})
                            </span>
                        ) : (
                            <span className="font-semibold text-terroir-dark/40">Inactive — aucune clé API configurée (variable OPENAI_API_KEY)</span>
                        )}
                    </p>
                </div>
                <Link href={route('admin.messagerie.faq.create')} className="btn-primary">Nouvelle question</Link>
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
                            <th className="pl-6">Question</th>
                            <th>Catégorie</th>
                            <th>Utilisée</th>
                            <th>Statut</th>
                            <th className="pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {entries.length === 0 ? (
                            <tr><td colSpan={5} className="py-8 text-center text-terroir-dark/40">Aucune question dans la base de connaissances.</td></tr>
                        ) : entries.map((entry) => (
                            <tr key={entry.id}>
                                <td className="pl-6 font-semibold text-terroir-dark">{entry.question}</td>
                                <td className="text-terroir-dark/60">{entry.category || '—'}</td>
                                <td className="text-terroir-dark/60">{entry.hit_count} fois</td>
                                <td>
                                    {entry.is_active ? (
                                        <span className="admin-badge-success">Active</span>
                                    ) : (
                                        <span className="admin-badge-neutral">Inactive</span>
                                    )}
                                </td>
                                <td className="pr-6 text-right">
                                    <Link href={route('admin.messagerie.faq.edit', entry.id)} className="admin-link">Modifier</Link>
                                    <button type="button" onClick={() => handleDelete(entry)} className="admin-link-danger ml-3 bg-transparent">
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>
        </AdminLayout>
    );
}
