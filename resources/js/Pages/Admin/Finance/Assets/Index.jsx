import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../../Layouts/AdminLayout';

function fcfa(value) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(value)) + ' FCFA';
}

export default function Index({ assets, totals, categories, statuses, filters }) {
    function applyFilters(next) {
        router.get(
            route('admin.immobilisations.index'),
            { category: filters?.category ?? '', status: filters?.status ?? '', ...next },
            { preserveState: true }
        );
    }

    return (
        <AdminLayout title="Immobilisations">
            <Head title="Immobilisations — Administration" />

            <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div className="admin-card">
                    <p className="text-xs text-terroir-dark/50">Valeur d'acquisition</p>
                    <p className="mt-1.5 text-xl font-bold">{fcfa(totals.acquisition)}</p>
                </div>
                <div className="admin-card">
                    <p className="text-xs text-terroir-dark/50">Amortissement cumulé</p>
                    <p className="mt-1.5 text-xl font-bold text-terroir-terracotta">{fcfa(totals.accumulated)}</p>
                </div>
                <div className="admin-card">
                    <p className="text-xs text-terroir-dark/50">Valeur nette comptable</p>
                    <p className="mt-1.5 text-xl font-bold text-terroir-green">{fcfa(totals.net)}</p>
                </div>
            </div>

            <div className="mt-6 flex flex-wrap items-center justify-between gap-3">
                <div className="flex flex-wrap gap-2">
                    <select
                        defaultValue={filters?.category ?? ''}
                        onChange={(e) => applyFilters({ category: e.target.value })}
                        className="input max-w-[220px]"
                    >
                        <option value="">Toutes catégories</option>
                        {Object.entries(categories).map(([value, label]) => <option key={value} value={value}>{label}</option>)}
                    </select>
                    <select
                        defaultValue={filters?.status ?? ''}
                        onChange={(e) => applyFilters({ status: e.target.value })}
                        className="input max-w-[200px]"
                    >
                        <option value="">Tous les statuts</option>
                        {Object.entries(statuses).map(([value, label]) => <option key={value} value={value}>{label}</option>)}
                    </select>
                </div>
                <Link href={route('admin.immobilisations.create')} className="btn-primary">
                    <span className="material-symbols-outlined text-lg">add</span>
                    Nouvelle immobilisation
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
                            <th className="pl-6">Désignation</th>
                            <th>Catégorie</th>
                            <th>Acquisition</th>
                            <th className="text-right">Valeur d'origine</th>
                            <th className="text-right">Amorti</th>
                            <th className="text-right">VNC</th>
                            <th className="pr-6">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        {assets.length === 0 ? (
                            <tr><td colSpan={7} className="py-8 text-center text-terroir-dark/40">Aucune immobilisation.</td></tr>
                        ) : assets.map((asset) => (
                            <tr key={asset.id}>
                                <td className="pl-6 font-semibold">
                                    <Link href={route('admin.immobilisations.show', asset.id)} className="admin-link">{asset.name}</Link>
                                </td>
                                <td className="text-terroir-dark/60">{asset.category_label}</td>
                                <td className="text-terroir-dark/60">{asset.acquisition_date}</td>
                                <td className="text-right">{asset.acquisition_value.toLocaleString('fr-FR')}</td>
                                <td className="text-right text-terroir-terracotta">{asset.accumulated_depreciation.toLocaleString('fr-FR')}</td>
                                <td className="text-right font-semibold text-terroir-green">{asset.net_book_value.toLocaleString('fr-FR')}</td>
                                <td className="pr-6"><span className={asset.status_badge_class}>{asset.status_label}</span></td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>
        </AdminLayout>
    );
}
