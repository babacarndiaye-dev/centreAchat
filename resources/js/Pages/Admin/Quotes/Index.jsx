import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Index({ quotes, statuses, filters }) {
    function handleStatusChange(e) {
        const status = e.target.value;
        router.get(route('admin.devis.index'), status ? { status } : {}, { preserveState: true, replace: true });
    }

    return (
        <AdminLayout title="Devis">
            <Head title="Devis — Administration" />

            <form className="flex gap-2">
                <select
                    value={filters?.status ?? ''}
                    onChange={handleStatusChange}
                    className="input max-w-[260px]"
                >
                    <option value="">Tous les statuts</option>
                    {Object.entries(statuses).map(([value, label]) => (
                        <option key={value} value={value}>{label}</option>
                    ))}
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
                            <th className="pl-6">N°</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th className="text-right">Total</th>
                            <th className="pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {quotes.data.length === 0 ? (
                            <tr><td colSpan={6} className="py-8 text-center text-terroir-dark/40">Aucun devis.</td></tr>
                        ) : quotes.data.map((quote) => (
                            <tr key={quote.id}>
                                <td className="pl-6 font-semibold text-terroir-dark">{quote.quote_number}</td>
                                <td>{quote.user_name}</td>
                                <td className="text-terroir-dark/60">{quote.created_at}</td>
                                <td><span className={quote.status_badge_class}>{quote.status_label}</span></td>
                                <td className="text-right font-semibold">
                                    {quote.total > 0 ? `${quote.total.toLocaleString('fr-FR')} FCFA` : '—'}
                                </td>
                                <td className="pr-6 text-right">
                                    <Link href={route('admin.devis.show', quote.id)} className="admin-link">Traiter</Link>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>

            {quotes.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {quotes.links.map((link, i) =>
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
