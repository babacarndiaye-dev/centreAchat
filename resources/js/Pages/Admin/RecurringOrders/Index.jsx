import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Index({ recurringOrders, frequencies }) {
    return (
        <AdminLayout title="Commandes récurrentes">
            <Head title="Commandes récurrentes — Administration" />

            <p className="text-sm text-terroir-dark/50">
                Vue d'ensemble des commandes récurrentes programmées par les clients professionnels.
                Générées automatiquement chaque jour via <code>php artisan orders:generate-recurring</code>.
            </p>

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
                            <th>Fréquence</th>
                            <th>Produits</th>
                            <th>Prochaine exécution</th>
                            <th className="pr-6">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        {recurringOrders.data.length === 0 ? (
                            <tr><td colSpan={5} className="py-8 text-center text-terroir-dark/40">Aucune commande récurrente.</td></tr>
                        ) : recurringOrders.data.map((ro) => (
                            <tr key={ro.id}>
                                <td className="pl-6 font-semibold text-terroir-dark">{ro.user_name}</td>
                                <td className="text-terroir-dark/60">{frequencies[ro.frequency]}</td>
                                <td className="text-terroir-dark/60">{ro.products}</td>
                                <td>{ro.next_run_date}</td>
                                <td className="pr-6">
                                    <span className={ro.status === 'active' ? 'admin-badge-success' : 'admin-badge-neutral'}>
                                        {ro.status === 'active' ? 'Active' : 'Suspendue'}
                                    </span>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>

            {recurringOrders.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {recurringOrders.links.map((link, i) =>
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
