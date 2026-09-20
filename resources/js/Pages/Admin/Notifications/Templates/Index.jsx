import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../../Layouts/AdminLayout';

export default function Index({ groups }) {
    return (
        <AdminLayout title="Modèles de messages">
            <Head title="Modèles de messages — Administration" />

            <p className="text-sm text-terroir-dark/50">
                Personnalisez les messages envoyés pour chaque événement et chaque canal. Les canaux SMS et WhatsApp nécessitent une passerelle configurée avant activation.
            </p>

            <div className="mt-6 flex flex-col gap-6">
                {groups.map((group, gi) => (
                    <motion.div
                        key={group.event_key}
                        initial={{ opacity: 0, y: 12 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.3, delay: gi * 0.03 }}
                        className="admin-card overflow-hidden p-0"
                    >
                        <div className="border-b border-terroir-green/10 bg-terroir-cream px-5 py-3">
                            <h3 className="font-display text-sm font-semibold">{group.event_label}</h3>
                        </div>
                        <table className="admin-table">
                            <tbody>
                                {group.templates.map((template) => (
                                    <tr key={template.id}>
                                        <td className="w-32 pl-6 font-semibold text-terroir-dark">{template.channel_label}</td>
                                        <td className="text-terroir-dark/60">
                                            {template.is_active ? (
                                                <span className="text-xs font-semibold text-terroir-green">Actif</span>
                                            ) : (
                                                <span className="text-xs font-semibold text-terroir-dark/40">Inactif</span>
                                            )}
                                        </td>
                                        <td className="pr-6 text-right">
                                            <Link href={route('admin.notifications.templates.edit', template.id)} className="admin-link">Modifier</Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </motion.div>
                ))}
            </div>
        </AdminLayout>
    );
}
