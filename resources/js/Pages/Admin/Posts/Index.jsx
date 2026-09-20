import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Index({ posts }) {
    function handleDelete(post) {
        if (!confirm('Supprimer cet article ?')) return;
        router.delete(route('admin.articles.destroy', post.id), { preserveScroll: true });
    }

    return (
        <AdminLayout title="Actualités">
            <Head title="Actualités — Administration" />

            <div className="flex flex-wrap items-center justify-between gap-3">
                <p className="text-sm text-terroir-dark/50">{posts.total} article(s)</p>
                <Link href={route('admin.articles.create')} className="btn-primary">
                    <span className="material-symbols-outlined text-lg">add</span>
                    Nouvel article
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
                            <th>Type</th>
                            <th>Statut</th>
                            <th className="pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {posts.data.length === 0 ? (
                            <tr><td colSpan={4} className="py-8 text-center text-terroir-dark/40">Aucun article.</td></tr>
                        ) : posts.data.map((post) => (
                            <tr key={post.id}>
                                <td className="pl-6 font-semibold text-terroir-dark">{post.title}</td>
                                <td className="text-terroir-dark/60">{post.type.charAt(0).toUpperCase() + post.type.slice(1)}</td>
                                <td>
                                    {post.is_published ? (
                                        <span className="admin-badge-success">Publié</span>
                                    ) : (
                                        <span className="admin-badge-neutral">Brouillon</span>
                                    )}
                                </td>
                                <td className="pr-6 text-right">
                                    <Link href={route('admin.articles.edit', post.id)} className="admin-link">Modifier</Link>
                                    <button type="button" onClick={() => handleDelete(post)} className="admin-link-danger ml-3 bg-transparent">Supprimer</button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>

            {posts.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {posts.links.map((link, i) =>
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
