import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Index({ suppliers, statuses, filters }) {
    const [q, setQ] = useState(filters?.q ?? '');

    function handleFilter(e) {
        e.preventDefault();
        router.get(route('admin.fournisseurs.index'), { q, status: filters?.status ?? '' }, { preserveState: true });
    }

    function handleStatusChange(e) {
        router.get(route('admin.fournisseurs.index'), { q, status: e.target.value }, { preserveState: true });
    }

    function handleDelete(supplier) {
        if (!confirm('Supprimer ce fournisseur ?')) return;
        router.delete(route('admin.fournisseurs.destroy', supplier.id), { preserveScroll: true });
    }

    return (
        <AdminLayout title="Fournisseurs">
            <Head title="Fournisseurs — Administration" />

            <div className="flex flex-wrap items-center justify-between gap-3">
                <form onSubmit={handleFilter} className="flex flex-wrap items-center gap-3">
                    <input
                        value={q}
                        onChange={(e) => setQ(e.target.value)}
                        placeholder="Nom, société, téléphone..."
                        className="input max-w-xs"
                    />
                    <select value={filters?.status ?? ''} onChange={handleStatusChange} className="input max-w-[180px]">
                        <option value="">Tous les statuts</option>
                        {Object.entries(statuses).map(([value, label]) => <option key={value} value={value}>{label}</option>)}
                    </select>
                    <button type="submit" className="btn-outline">Filtrer</button>
                </form>
                <Link href={route('admin.fournisseurs.create')} className="btn-primary">
                    <span className="material-symbols-outlined text-lg">add</span>
                    Nouveau fournisseur
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
                            <th className="pl-6">Nom</th>
                            <th>Contact</th>
                            <th>Ville / Région</th>
                            <th>Statut</th>
                            <th className="text-right">Solde dû</th>
                            <th className="pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {suppliers.data.length === 0 ? (
                            <tr><td colSpan={6} className="py-8 text-center text-terroir-dark/40">Aucun fournisseur.</td></tr>
                        ) : suppliers.data.map((supplier) => (
                            <tr key={supplier.id}>
                                <td className="pl-6 font-semibold text-terroir-dark">
                                    {supplier.name}<br /><span className="text-xs text-terroir-dark/50">{supplier.company_name}</span>
                                </td>
                                <td className="text-terroir-dark/60">{supplier.phone}</td>
                                <td className="text-terroir-dark/60">{supplier.city} {supplier.region ? `(${supplier.region})` : ''}</td>
                                <td><span className={supplier.status_badge_class}>{supplier.status_label}</span></td>
                                <td className={'text-right font-semibold ' + (supplier.balance > 0 ? 'text-terroir-terracotta' : '')}>
                                    {supplier.balance.toLocaleString('fr-FR')} FCFA
                                </td>
                                <td className="pr-6 text-right">
                                    <Link href={route('admin.fournisseurs.show', supplier.id)} className="admin-link">Fiche</Link>
                                    <Link href={route('admin.fournisseurs.edit', supplier.id)} className="admin-link ml-3">Modifier</Link>
                                    <button type="button" onClick={() => handleDelete(supplier)} className="admin-link-danger ml-3 bg-transparent">Supprimer</button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>

            {suppliers.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {suppliers.links.map((link, i) =>
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
