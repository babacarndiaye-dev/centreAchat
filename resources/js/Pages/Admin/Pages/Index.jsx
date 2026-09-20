import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Index({ pages }) {
    function handleDelete(page) {
        if (!confirm('Supprimer cette page ?')) return;
        router.delete(route('admin.pages.destroy', page.id), { preserveScroll: true });
    }

    return (
        <AdminLayout title="Pages">
            <Head title="Pages — Administration" />

            <div className="flex flex-wrap items-center justify-between gap-3">
                <p className="text-sm text-terroir-dark/50">{pages.total} page(s)</p>
                <Link href={route('admin.pages.create')} className="btn-primary">
                    <span className="material-symbols-outlined text-lg">add</span>
                    Nouvelle page
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
                            <th className="pl-6">Titre</th>
                            <th>Slug</th>
                            <th>Statut</th>
                            <th className="pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {pages.data.length === 0 ? (
                            <tr><td colSpan={4} className="py-8 text-center text-terroir-dark/40">Aucune page.</td></tr>
                        ) : pages.data.map((page) => (
                            <tr key={page.id}>
                                <td className="pl-6 font-semibold text-terroir-dark">{page.title}</td>
                                <td className="text-terroir-dark/60">/{page.slug}</td>
                                <td>
                                    {page.is_published ? (
                                        <span className="admin-badge-success">Publiée</span>
                                    ) : (
                                        <span className="admin-badge-neutral">Brouillon</span>
                                    )}
                                </td>
                                <td className="pr-6 text-right">
                                    <Link href={route('admin.pages.edit', page.id)} className="admin-link">Modifier</Link>
                                    <button type="button" onClick={() => handleDelete(page)} className="admin-link-danger ml-3 bg-transparent">Supprimer</button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>

            {pages.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {pages.links.map((link, i) =>
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
