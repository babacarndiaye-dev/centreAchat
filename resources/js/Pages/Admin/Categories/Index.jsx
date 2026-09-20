import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Index({ categories }) {
    function handleDelete(category) {
        if (!confirm('Supprimer cette catégorie ?')) return;
        router.delete(route('admin.categories.destroy', category.id), { preserveScroll: true });
    }

    return (
        <AdminLayout title="Catégories">
            <Head title="Catégories — Administration" />

            <div className="flex items-center justify-between">
                <p className="text-sm text-terroir-dark/50">{categories.total} catégorie(s)</p>
                <Link href={route('admin.categories.create')} className="btn-primary">
                    <span className="material-symbols-outlined text-lg">add</span>
                    Nouvelle catégorie
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
                            <th className="pl-6">Nom</th>
                            <th>Catégorie parente</th>
                            <th>Position</th>
                            <th>Statut</th>
                            <th className="pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {categories.data.length === 0 ? (
                            <tr><td colSpan={5} className="py-8 text-center text-terroir-dark/40">Aucune catégorie.</td></tr>
                        ) : categories.data.map((category) => (
                            <tr key={category.id}>
                                <td className="pl-6 font-semibold text-terroir-dark">{category.name}</td>
                                <td className="text-terroir-dark/60">{category.parent?.name ?? '—'}</td>
                                <td>{category.position}</td>
                                <td>
                                    {category.is_active ? (
                                        <span className="admin-badge-success">Active</span>
                                    ) : (
                                        <span className="admin-badge-neutral">Inactive</span>
                                    )}
                                </td>
                                <td className="pr-6 text-right">
                                    <Link href={route('admin.categories.edit', category.id)} className="admin-link">Modifier</Link>
                                    <button type="button" onClick={() => handleDelete(category)} className="admin-link-danger ml-3 bg-transparent">
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>

            {categories.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {categories.links.map((link, i) =>
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
