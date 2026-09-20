import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Index({ messages }) {
    function handleDelete(message) {
        if (!confirm('Supprimer ce message ?')) return;
        router.delete(route('admin.messages.destroy', message.id), { preserveScroll: true });
    }

    return (
        <AdminLayout title="Messages">
            <Head title="Messages — Administration" />

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="admin-card overflow-x-auto p-0"
            >
                <table className="admin-table">
                    <thead>
                        <tr>
                            <th className="pl-6">Nom</th>
                            <th>Sujet</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th className="pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {messages.data.length === 0 ? (
                            <tr><td colSpan={5} className="py-8 text-center text-terroir-dark/40">Aucun message.</td></tr>
                        ) : messages.data.map((message) => (
                            <tr key={message.id} className={message.is_read ? '' : 'font-semibold'}>
                                <td className="pl-6">
                                    {message.name}
                                    <span className="block text-xs font-normal text-terroir-dark/50">{message.email}</span>
                                </td>
                                <td>{message.subject ?? '—'}</td>
                                <td className="text-terroir-dark/50">{message.created_at}</td>
                                <td>
                                    <div className="flex flex-wrap gap-1.5">
                                        {message.is_read ? (
                                            <span className="admin-badge-neutral">Lu</span>
                                        ) : (
                                            <span className="admin-badge-danger">Non lu</span>
                                        )}
                                        {message.has_reply && <span className="admin-badge-success">Répondu</span>}
                                    </div>
                                </td>
                                <td className="pr-6 text-right">
                                    <Link href={route('admin.messages.show', message.id)} className="admin-link">Lire</Link>
                                    <button type="button" onClick={() => handleDelete(message)} className="admin-link-danger ml-3 bg-transparent">Supprimer</button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>

            {messages.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {messages.links.map((link, i) =>
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
