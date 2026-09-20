import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

const BADGE_CLASS = {
    valide: 'admin-badge-success',
    refuse: 'admin-badge-danger',
};

function CreditForm({ client }) {
    const [value, setValue] = useState(client.credit_limit ?? '');
    const [busy, setBusy] = useState(false);

    function handleSubmit(e) {
        e.preventDefault();
        setBusy(true);
        router.patch(route('admin.b2b.credit', client.id), { credit_limit: value || null }, {
            preserveScroll: true,
            onFinish: () => setBusy(false),
        });
    }

    return (
        <form onSubmit={handleSubmit} className="flex items-center gap-2">
            <input
                type="number"
                step="0.01"
                value={value}
                onChange={(e) => setValue(e.target.value)}
                placeholder="0"
                className="input w-32"
            />
            <button type="submit" disabled={busy} className="text-sm font-semibold text-terroir-green disabled:opacity-50">OK</button>
        </form>
    );
}

export default function Index({ clients, filters }) {
    function handleStatusChange(e) {
        const status = e.target.value;
        router.get(route('admin.b2b.index'), status ? { status } : {}, { preserveState: true, replace: true });
    }

    function handleApprove(client) {
        router.patch(route('admin.b2b.approve', client.id), {}, { preserveScroll: true });
    }

    function handleReject(client) {
        router.patch(route('admin.b2b.reject', client.id), {}, { preserveScroll: true });
    }

    return (
        <AdminLayout title="Clients professionnels">
            <Head title="Clients professionnels — Administration" />

            <form className="flex gap-2">
                <select
                    value={filters?.status ?? ''}
                    onChange={handleStatusChange}
                    className="input max-w-[240px]"
                >
                    <option value="">Tous les statuts</option>
                    <option value="en_attente">En attente</option>
                    <option value="valide">Validé</option>
                    <option value="refuse">Refusé</option>
                </select>
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
                            <th className="pl-6">Client</th>
                            <th>Type</th>
                            <th>Statut</th>
                            <th>Plafond crédit</th>
                            <th className="pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {clients.data.length === 0 ? (
                            <tr><td colSpan={5} className="py-8 text-center text-terroir-dark/40">Aucun client professionnel.</td></tr>
                        ) : clients.data.map((client) => (
                            <tr key={client.id}>
                                <td className="pl-6 font-semibold text-terroir-dark">
                                    {client.name}<br />
                                    <span className="text-xs font-normal text-terroir-dark/50">{client.company_name} — {client.email}</span>
                                    {client.business_registration_number && (
                                        <>
                                            <br />
                                            <span className="text-xs font-normal text-terroir-dark/50">NINEA/RCCM : {client.business_registration_number}</span>
                                        </>
                                    )}
                                </td>
                                <td className="text-terroir-dark/60">{client.user_type.charAt(0).toUpperCase() + client.user_type.slice(1)}</td>
                                <td>
                                    <span className={BADGE_CLASS[client.b2b_status] ?? 'admin-badge-warning'}>
                                        {client.b2b_status.charAt(0).toUpperCase() + client.b2b_status.slice(1)}
                                    </span>
                                </td>
                                <td>
                                    <CreditForm client={client} />
                                </td>
                                <td className="pr-6 text-right">
                                    {client.b2b_status === 'en_attente' ? (
                                        <>
                                            <button type="button" onClick={() => handleApprove(client)} className="admin-link bg-transparent">Valider</button>
                                            <button type="button" onClick={() => handleReject(client)} className="admin-link-danger ml-3 bg-transparent">Refuser</button>
                                        </>
                                    ) : client.b2b_status === 'refuse' ? (
                                        <button type="button" onClick={() => handleApprove(client)} className="admin-link bg-transparent">Valider</button>
                                    ) : (
                                        <span className="text-terroir-dark/40">—</span>
                                    )}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>

            {clients.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {clients.links.map((link, i) =>
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
