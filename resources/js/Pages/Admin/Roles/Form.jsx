import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Form({ modules, actions, role, assignedPermissions }) {
    const isEdit = !!role;
    const { data, setData, post, patch, processing, errors } = useForm({
        name: role?.name ?? '',
        description: role?.description ?? '',
        permissions: assignedPermissions ?? [],
    });

    function togglePermission(permission, checked) {
        setData('permissions', checked
            ? [...data.permissions, permission]
            : data.permissions.filter((p) => p !== permission));
    }

    function handleSubmit(e) {
        e.preventDefault();
        if (isEdit) {
            patch(route('admin.roles.update', role.id), { preserveScroll: true });
        } else {
            post(route('admin.roles.store'), { preserveScroll: true });
        }
    }

    return (
        <AdminLayout title={isEdit ? 'Modifier le rôle' : 'Nouveau rôle'}>
            <Head title={`${isEdit ? 'Modifier le rôle' : 'Nouveau rôle'} — Administration`} />

            <motion.form
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                onSubmit={handleSubmit}
            >
                <div className="admin-card max-w-5xl">
                    <div className="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Nom du rôle</label>
                            <input
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                required
                                readOnly={role?.is_system}
                                className="input"
                            />
                            {errors.name && <p className="mt-1 text-xs text-terroir-terracotta">{errors.name}</p>}
                        </div>
                        <div>
                            <label className="label">Description</label>
                            <input value={data.description} onChange={(e) => setData('description', e.target.value)} className="input" />
                        </div>
                    </div>
                </div>

                <div className="admin-card mt-6 max-w-5xl overflow-x-auto p-0">
                    <table className="admin-table">
                        <thead>
                            <tr>
                                <th className="pl-6">Module</th>
                                {Object.entries(actions).map(([key, label]) => (
                                    <th key={key} className="text-center">{label}</th>
                                ))}
                            </tr>
                        </thead>
                        <tbody>
                            {Object.entries(modules).map(([moduleKey, moduleLabel]) => (
                                <tr key={moduleKey}>
                                    <td className="pl-6 font-semibold text-terroir-dark">{moduleLabel}</td>
                                    {Object.keys(actions).map((actionKey) => {
                                        const permission = `${moduleKey}.${actionKey}`;
                                        return (
                                            <td key={actionKey} className="text-center">
                                                <input
                                                    type="checkbox"
                                                    checked={data.permissions.includes(permission)}
                                                    onChange={(e) => togglePermission(permission, e.target.checked)}
                                                    className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"
                                                />
                                            </td>
                                        );
                                    })}
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                <div className="mt-6 max-w-5xl">
                    <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Enregistrer</button>
                    <Link href={route('admin.roles.index')} className="ml-3 text-sm font-semibold text-terroir-dark/60 hover:text-terroir-dark">Annuler</Link>
                </div>
            </motion.form>
        </AdminLayout>
    );
}
