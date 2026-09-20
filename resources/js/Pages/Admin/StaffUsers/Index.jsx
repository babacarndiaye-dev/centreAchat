import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Index({ users, currentUserId }) {
    function handleRevoke(user) {
        if (!confirm("Retirer l'accès staff de cet utilisateur ?")) return;
        router.delete(route('admin.utilisateurs.destroy', user.id), { preserveScroll: true });
    }

    return (
        <AdminLayout title="Utilisateurs internes">
            <Head title="Utilisateurs internes — Administration" />

            <div className="flex items-center justify-between">
                <p className="text-sm text-terroir-dark/50">Comptes du personnel ayant accès à l'administration.</p>
                <Link href={route('admin.utilisateurs.create')} className="btn-primary">Nouvel utilisateur</Link>
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
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th className="pr-6"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {users.length === 0 ? (
                            <tr><td colSpan={5} className="py-8 text-center text-terroir-dark/40">Aucun utilisateur interne.</td></tr>
                        ) : users.map((user) => (
                            <tr key={user.id}>
                                <td className="pl-6 font-semibold text-terroir-dark">
                                    {user.name}
                                    {user.is_admin && <span className="admin-badge-warning ml-2">super admin</span>}
                                </td>
                                <td className="text-terroir-dark/60">{user.email}</td>
                                <td>{user.role_name ?? '—'}</td>
                                <td>
                                    {user.is_active ? (
                                        <span className="text-xs font-semibold text-terroir-green">Actif</span>
                                    ) : (
                                        <span className="text-xs font-semibold text-terroir-dark/40">Inactif</span>
                                    )}
                                </td>
                                <td className="pr-6 text-right">
                                    <Link href={route('admin.utilisateurs.edit', user.id)} className="admin-link">Modifier</Link>
                                    {user.id !== currentUserId && (
                                        <button type="button" onClick={() => handleRevoke(user)} className="admin-link-danger ml-3 bg-transparent">
                                            Retirer l'accès
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
