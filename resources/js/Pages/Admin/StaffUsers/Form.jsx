import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Form({ roles, user }) {
    const isEdit = !!user;
    const { data, setData, post, patch, processing, errors } = useForm({
        name: user?.name ?? '',
        email: user?.email ?? '',
        password: '',
        role_id: user?.role_id ?? '',
        is_admin: user?.is_admin ?? false,
        is_active: user?.is_active ?? true,
    });

    function handleSubmit(e) {
        e.preventDefault();
        if (isEdit) {
            patch(route('admin.utilisateurs.update', user.id), { preserveScroll: true });
        } else {
            post(route('admin.utilisateurs.store'), { preserveScroll: true });
        }
    }

    return (
        <AdminLayout title={isEdit ? "Modifier l'utilisateur" : 'Nouvel utilisateur'}>
            <Head title={`${isEdit ? "Modifier l'utilisateur" : 'Nouvel utilisateur'} — Administration`} />

            <motion.form
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                onSubmit={handleSubmit}
                className="admin-card max-w-xl"
            >
                <div>
                    <label className="label">Nom</label>
                    <input value={data.name} onChange={(e) => setData('name', e.target.value)} required className="input" />
                    {errors.name && <p className="mt-1 text-xs text-terroir-terracotta">{errors.name}</p>}
                </div>
                <div className="mt-4">
                    <label className="label">Email</label>
                    <input type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} required className="input" />
                    {errors.email && <p className="mt-1 text-xs text-terroir-terracotta">{errors.email}</p>}
                </div>
                <div className="mt-4">
                    <label className="label">{isEdit ? 'Nouveau mot de passe' : 'Mot de passe'}</label>
                    <input
                        type="password"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        placeholder={isEdit ? 'Laisser vide pour ne pas changer' : undefined}
                        required={!isEdit}
                        className="input"
                    />
                    {errors.password && <p className="mt-1 text-xs text-terroir-terracotta">{errors.password}</p>}
                </div>
                <div className="mt-4">
                    <label className="label">Rôle</label>
                    <select value={data.role_id} onChange={(e) => setData('role_id', e.target.value)} className="input">
                        <option value="">— Aucun —</option>
                        {roles.map((role) => <option key={role.id} value={role.id}>{role.name}</option>)}
                    </select>
                </div>
                <label className="mt-4 flex items-center gap-2 text-sm">
                    <input
                        type="checkbox"
                        checked={data.is_admin}
                        onChange={(e) => setData('is_admin', e.target.checked)}
                        className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"
                    />
                    Super administrateur (accès complet, ignore les permissions du rôle)
                </label>
                {isEdit && (
                    <label className="mt-3 flex items-center gap-2 text-sm">
                        <input
                            type="checkbox"
                            checked={data.is_active}
                            onChange={(e) => setData('is_active', e.target.checked)}
                            className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"
                        />
                        Compte actif
                    </label>
                )}

                <div className="mt-6">
                    <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">{isEdit ? 'Enregistrer' : 'Créer'}</button>
                    <Link href={route('admin.utilisateurs.index')} className="ml-3 text-sm font-semibold text-terroir-dark/60">Annuler</Link>
                </div>
            </motion.form>
        </AdminLayout>
    );
}
