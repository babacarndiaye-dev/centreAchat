import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Index({ purchaseRequests, statuses, filters }) {
    function handleStatusChange(e) {
        router.get(route('admin.demandes-achat.index'), { status: e.target.value }, { preserveState: true });
    }

    return (
        <AdminLayout title="Demandes d'achat">
            <Head title="Demandes d'achat — Administration" />

            <div className="flex flex-wrap items-center justify-between gap-3">
                <select value={filters?.status ?? ''} onChange={handleStatusChange} className="input max-w-[240px]">
                    <option value="">Tous les statuts</option>
                    {Object.entries(statuses).map(([value, label]) => <option key={value} value={value}>{label}</option>)}
                </select>
                <Link href={route('admin.demandes-achat.create')} className="btn-primary">
                    <span className="material-symbols-outlined text-lg">add</span>
                    Nouvelle demande d'achat
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
                            <th className="pl-6">Référence</th>
                            <th>Demandeur</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th className="pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {purchaseRequests.data.length === 0 ? (
                            <tr><td colSpan={5} className="py-8 text-center text-terroir-dark/40">Aucune demande d'achat.</td></tr>
                        ) : purchaseRequests.data.map((pr) => (
                            <tr key={pr.id}>
                                <td className="pl-6 font-semibold text-terroir-dark">{pr.reference}</td>
                                <td className="text-terroir-dark/60">{pr.requester_name ?? '—'}</td>
                                <td className="text-terroir-dark/60">{pr.created_at}</td>
                                <td><span className={pr.status_badge_class}>{pr.status_label}</span></td>
                                <td className="pr-6 text-right">
                                    <Link href={route('admin.demandes-achat.show', pr.id)} className="admin-link">Voir</Link>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>

            {purchaseRequests.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {purchaseRequests.links.map((link, i) =>
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
