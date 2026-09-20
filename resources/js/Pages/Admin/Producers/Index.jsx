import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Index({ producers }) {
    function handleDelete(producer) {
        if (!confirm('Supprimer ce producteur ?')) return;
        router.delete(route('admin.producteurs.destroy', producer.id), { preserveScroll: true });
    }

    return (
        <AdminLayout title="Producteurs">
            <Head title="Producteurs — Administration" />

            <div className="flex items-center justify-between">
                <p className="text-sm text-terroir-dark/50">{producers.total} producteur(s)</p>
                <Link href={route('admin.producteurs.create')} className="btn-primary">
                    <span className="material-symbols-outlined text-lg">add</span>
                    Nouveau producteur
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
                            <th>Région</th>
                            <th>Vedette</th>
                            <th>Statut</th>
                            <th className="pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {producers.data.length === 0 ? (
                            <tr><td colSpan={5} className="py-8 text-center text-terroir-dark/40">Aucun producteur.</td></tr>
                        ) : producers.data.map((producer) => (
                            <tr key={producer.id}>
                                <td className="pl-6 font-semibold text-terroir-dark">{producer.name}</td>
                                <td className="text-terroir-dark/60">{producer.region ?? '—'}</td>
                                <td>{producer.is_featured ? 'Oui' : 'Non'}</td>
                                <td>
                                    {producer.is_active ? (
                                        <span className="admin-badge-success">Actif</span>
                                    ) : (
                                        <span className="admin-badge-neutral">Inactif</span>
                                    )}
                                </td>
                                <td className="pr-6 text-right">
                                    <Link href={route('admin.producteurs.edit', producer.id)} className="admin-link">Modifier</Link>
                                    <button type="button" onClick={() => handleDelete(producer)} className="admin-link-danger ml-3 bg-transparent">
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>

            {producers.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {producers.links.map((link, i) =>
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
