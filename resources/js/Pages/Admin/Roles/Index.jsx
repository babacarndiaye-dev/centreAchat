import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Index({ roles }) {
    function handleDelete(role) {
        if (!confirm('Supprimer ce rôle ?')) return;
        router.delete(route('admin.roles.destroy', role.id), { preserveScroll: true });
    }

    return (
        <AdminLayout title="Rôles">
            <Head title="Rôles — Administration" />

            <div className="flex items-center justify-between">
                <p className="text-sm text-terroir-dark/50">Définissez les rôles internes et les permissions accordées à chacun.</p>
                <Link href={route('admin.roles.create')} className="btn-primary">Nouveau rôle</Link>
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
                            <th className="pl-6">Nom</th>
                            <th>Description</th>
                            <th>Permissions</th>
                            <th>Utilisateurs</th>
                            <th className="pr-6"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {roles.length === 0 ? (
                            <tr><td colSpan={5} className="py-8 text-center text-terroir-dark/40">Aucun rôle défini.</td></tr>
                        ) : roles.map((role) => (
                            <tr key={role.id}>
                                <td className="pl-6 font-semibold text-terroir-dark">
                                    {role.name}
                                    {role.is_system && <span className="admin-badge-warning ml-2">système</span>}
                                </td>
                                <td className="text-terroir-dark/60">{role.description || '—'}</td>
                                <td>{role.permissions_count}</td>
                                <td>{role.users_count}</td>
                                <td className="pr-6 text-right">
                                    <Link href={route('admin.roles.edit', role.id)} className="admin-link">Modifier</Link>
                                    {!role.is_system && (
                                        <button type="button" onClick={() => handleDelete(role)} className="admin-link-danger ml-3 bg-transparent">
                                            Supprimer
                                        </button>
                                    )}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>
        </AdminLayout>
    );
}
