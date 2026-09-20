import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../../Layouts/AdminLayout';

export default function Index({ registers }) {
    return (
        <AdminLayout title="Caisse">
            <Head title="Caisse — Administration" />

            <div className="flex items-center justify-between">
                <p className="text-sm text-terroir-dark/50">Historique des sessions de caisse.</p>
                <Link href={route('admin.pos.caisse.create')} className="btn-primary">Ouvrir / accéder à la caisse</Link>
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
                            <th className="pl-6">Ouverte par</th>
                            <th>Ouverture</th>
                            <th>Fond initial</th>
                            <th>Statut</th>
                            <th className="text-right">Écart</th>
                            <th className="pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {registers.data.length === 0 ? (
                            <tr><td colSpan={6} className="py-8 text-center text-terroir-dark/40">Aucune session de caisse.</td></tr>
                        ) : registers.data.map((r) => (
                            <tr key={r.id}>
                                <td className="pl-6 font-semibold text-terroir-dark">{r.opened_by_name}</td>
                                <td className="text-terroir-dark/60">{r.created_at}</td>
                                <td>{r.opening_float.toLocaleString('fr-FR')} FCFA</td>
                                <td>
                                    {r.status === 'ouverte' ? (
                                        <span className="admin-badge-success">Ouverte</span>
                                    ) : (
                                        <span className="admin-badge-neutral">Fermée</span>
                                    )}
                                </td>
                                <td className={'text-right ' + (r.variance && r.variance !== 0 ? 'font-semibold text-terroir-terracotta' : '')}>
                                    {r.variance !== null ? `${r.variance.toLocaleString('fr-FR')} FCFA` : '—'}
                                </td>
                                <td className="pr-6 text-right">
                                    <Link href={route('admin.pos.caisse.show', r.id)} className="admin-link">Voir</Link>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>

            {registers.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {registers.links.map((link, i) =>
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
