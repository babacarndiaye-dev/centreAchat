import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Index({ orders, statuses, filters }) {
    const [q, setQ] = useState(filters?.q ?? '');
    const [status, setStatus] = useState(filters?.status ?? '');

    function submit(overrides = {}) {
        const params = { q, status, ...overrides };
        const query = {};
        if (params.q) query.q = params.q;
        if (params.status) query.status = params.status;
        router.get(route('admin.commandes.index'), query, { preserveState: true, replace: true });
    }

    function handleSubmit(e) {
        e.preventDefault();
        submit();
    }

    function handleStatusChange(e) {
        const value = e.target.value;
        setStatus(value);
        submit({ status: value });
    }

    return (
        <AdminLayout title="Commandes">
            <Head title="Commandes — Administration" />

            <form onSubmit={handleSubmit} className="flex flex-wrap items-center gap-3">
                <input
                    type="text"
                    value={q}
                    onChange={(e) => setQ(e.target.value)}
                    placeholder="N° commande, client, téléphone..."
                    className="input max-w-xs"
                />
                <select value={status} onChange={handleStatusChange} className="input max-w-[220px]">
                    <option value="">Tous les statuts</option>
                    {Object.entries(statuses).map(([value, label]) => (
                        <option key={value} value={value}>{label}</option>
                    ))}
                </select>
                <button type="submit" className="btn-outline">Filtrer</button>
            </form>

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
                            <th>Client</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th className="text-right">Total</th>
                            <th className="pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {orders.data.length === 0 ? (
                            <tr><td colSpan={6} className="py-8 text-center text-terroir-dark/40">Aucune commande.</td></tr>
                        ) : orders.data.map((order) => (
                            <tr key={order.id}>
                                <td className="pl-6 font-semibold text-terroir-dark">{order.order_number}</td>
                                <td>{order.customer_name}<br /><span className="text-xs text-terroir-dark/50">{order.customer_phone}</span></td>
                                <td className="text-terroir-dark/60">{order.created_at}</td>
                                <td><span className={order.status_badge_class}>{order.status_label}</span></td>
                                <td className="text-right font-semibold">{order.total.toLocaleString('fr-FR')} FCFA</td>
                                <td className="pr-6 text-right">
                                    <Link href={route('admin.commandes.show', order.id)} className="admin-link">Voir</Link>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>

            {orders.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {orders.links.map((link, i) =>
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
