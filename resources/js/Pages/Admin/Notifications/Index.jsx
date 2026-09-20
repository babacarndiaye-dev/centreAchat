import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Index({ notifications }) {
    function handleMarkAllRead() {
        router.patch(route('admin.notifications.read-all'), {}, { preserveScroll: true });
    }

    function handleMarkRead(notification) {
        router.patch(route('admin.notifications.read', notification.id), {}, { preserveScroll: true });
    }

    return (
        <AdminLayout title="Notifications">
            <Head title="Notifications — Administration" />

            <div className="flex items-center justify-between">
                <p className="text-sm text-terroir-dark/50">Notifications internes qui vous concernent.</p>
                <button type="button" onClick={handleMarkAllRead} className="text-sm font-semibold text-terroir-green">
                    Tout marquer comme lu
                </button>
            </div>

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="admin-card mt-6 p-0"
            >
                {notifications.data.length === 0 ? (
                    <p className="py-8 text-center text-terroir-dark/40">Aucune notification pour le moment.</p>
                ) : notifications.data.map((notification, i) => (
                    <div
                        key={notification.id}
                        className={
                            'flex justify-between gap-4 px-5 py-4 ' +
                            (i === notifications.data.length - 1 ? '' : 'border-b border-terroir-dark/[0.08] ') +
                            (notification.read_at ? '' : 'bg-terroir-green/5')
                        }
                    >
                        <div>
                            <p className="font-semibold">{notification.title}</p>
                            <p className="mt-1.5 text-sm text-terroir-dark/50">{notification.body}</p>
                            <p className="mt-2 text-xs text-terroir-dark/40">{notification.created_at_human}</p>
                        </div>
                        {!notification.read_at && (
                            <button type="button" onClick={() => handleMarkRead(notification)} className="whitespace-nowrap text-xs font-semibold text-terroir-green">
                                Marquer comme lu
                            </button>
                        )}
                    </div>
                ))}
            </motion.div>

            {notifications.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {notifications.links.map((link, i) =>
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
