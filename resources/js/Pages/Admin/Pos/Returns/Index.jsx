import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../../Layouts/AdminLayout';

export default function Index({ returns }) {
    return (
        <AdminLayout title="Retours">
            <Head title="Retours — Administration" />

            <div className="flex items-center justify-between">
                <p className="text-sm text-terroir-dark/50">{returns.total} retour(s) enregistré(s)</p>
                <Link href={route('admin.pos.retours.create')} className="btn-primary">
                    <span className="material-symbols-outlined text-lg">add</span>
                    Nouveau retour
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
                            <th className="pl-6">Commande</th>
                            <th>Traité par</th>
                            <th>Date</th>
                            <th className="pr-6 text-right">Montant remboursé</th>
                        </tr>
                    </thead>
                    <tbody>
                        {returns.data.length === 0 ? (
                            <tr><td colSpan={4} className="py-8 text-center text-terroir-dark/40">Aucun retour.</td></tr>
                        ) : returns.data.map((r) => (
                            <tr key={r.id}>
                                <td className="pl-6 font-semibold text-terroir-dark">{r.order_number}</td>
                                <td className="text-terroir-dark/60">{r.processed_by_name}</td>
                                <td className="text-terroir-dark/60">{r.created_at}</td>
                                <td className="pr-6 text-right font-semibold text-terroir-terracotta">{r.total_refund.toLocaleString('fr-FR')} FCFA</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>

            {returns.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {returns.links.map((link, i) =>
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
