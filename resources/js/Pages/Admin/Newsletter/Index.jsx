import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Index({ subscribers }) {
    return (
        <AdminLayout title="Abonnés newsletter">
            <Head title="Abonnés newsletter — Administration" />

            <p className="text-sm text-terroir-dark/50">{subscribers.total} abonné(s)</p>

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="admin-card mt-6 overflow-x-auto p-0"
            >
                <table className="admin-table">
                    <thead>
                        <tr>
                            <th className="pl-6">E-mail</th>
                            <th className="pr-6">Inscrit le</th>
                        </tr>
                    </thead>
                    <tbody>
                        {subscribers.data.length === 0 ? (
                            <tr><td colSpan={2} className="py-8 text-center text-terroir-dark/40">Aucun abonné.</td></tr>
                        ) : subscribers.data.map((subscriber) => (
                            <tr key={subscriber.id}>
                                <td className="pl-6 font-semibold text-terroir-dark">{subscriber.email}</td>
                                <td className="pr-6 text-terroir-dark/60">{subscriber.subscribed_at ?? '—'}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>

            {subscribers.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {subscribers.links.map((link, i) =>
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
