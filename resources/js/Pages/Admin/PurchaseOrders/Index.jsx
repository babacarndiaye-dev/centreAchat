import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Index({ purchaseOrders, statuses, filters }) {
    function handleStatusChange(e) {
        router.get(route('admin.bons-commande.index'), { status: e.target.value }, { preserveState: true });
    }

    return (
        <AdminLayout title="Bons de commande">
            <Head title="Bons de commande — Administration" />

            <div className="flex flex-wrap items-center justify-between gap-3">
                <select value={filters?.status ?? ''} onChange={handleStatusChange} className="input max-w-[240px]">
                    <option value="">Tous les statuts</option>
                    {Object.entries(statuses).map(([value, label]) => <option key={value} value={value}>{label}</option>)}
                </select>
                <Link href={route('admin.bons-commande.create')} className="btn-primary">
                    <span className="material-symbols-outlined text-lg">add</span>
                    Nouveau bon de commande
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
                            <th className="pl-6">N°</th>
                            <th>Fournisseur</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th className="text-right">Total</th>
                            <th className="pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {purchaseOrders.data.length === 0 ? (
                            <tr><td colSpan={6} className="py-8 text-center text-terroir-dark/40">Aucun bon de commande.</td></tr>
                        ) : purchaseOrders.data.map((po) => (
                            <tr key={po.id}>
                                <td className="pl-6 font-semibold text-terroir-dark">{po.order_number}</td>
                                <td>{po.supplier_name}</td>
                                <td className="text-terroir-dark/60">{po.order_date}</td>
                                <td><span className={po.status_badge_class}>{po.status_label}</span></td>
                                <td className="text-right font-semibold">{po.total.toLocaleString('fr-FR')} FCFA</td>
                                <td className="pr-6 text-right">
                                    <Link href={route('admin.bons-commande.show', po.id)} className="admin-link">Voir</Link>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>

            {purchaseOrders.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {purchaseOrders.links.map((link, i) =>
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
