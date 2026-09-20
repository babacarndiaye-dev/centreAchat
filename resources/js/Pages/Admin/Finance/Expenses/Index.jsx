import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../../Layouts/AdminLayout';

export default function Index({ expenses, categories, statuses, filters }) {
    function applyFilters(next) {
        router.get(route('admin.depenses.index'), { status: filters?.status ?? '', category: filters?.category ?? '', ...next }, { preserveState: true });
    }

    function handleValidate(expense) {
        router.patch(route('admin.depenses.validate', expense.id), {}, { preserveScroll: true });
    }

    function handleReject(expense) {
        router.patch(route('admin.depenses.reject', expense.id), {}, { preserveScroll: true });
    }

    function handleDelete(expense) {
        if (!confirm('Supprimer cette dépense ?')) return;
        router.delete(route('admin.depenses.destroy', expense.id), { preserveScroll: true });
    }

    return (
        <AdminLayout title="Dépenses">
            <Head title="Dépenses — Administration" />

            <div className="flex flex-wrap items-center justify-between gap-3">
                <div className="flex flex-wrap gap-2">
                    <select
                        defaultValue={filters?.status ?? ''}
                        onChange={(e) => applyFilters({ status: e.target.value })}
                        className="input max-w-[200px]"
                    >
                        <option value="">Tous les statuts</option>
                        {Object.entries(statuses).map(([value, label]) => <option key={value} value={value}>{label}</option>)}
                    </select>
                    <select
                        defaultValue={filters?.category ?? ''}
                        onChange={(e) => applyFilters({ category: e.target.value })}
                        className="input max-w-[220px]"
                    >
                        <option value="">Toutes catégories</option>
                        {categories.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
                    </select>
                </div>
                <div className="flex gap-3">
                    <Link href={route('admin.categories-depenses.index')} className="btn-outline">Catégories</Link>
                    <Link href={route('admin.depenses.create')} className="btn-primary">
                        <span className="material-symbols-outlined text-lg">add</span>
                        Nouvelle dépense
                    </Link>
                </div>
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
                            <th className="pl-6">Date</th>
                            <th>Catégorie</th>
                            <th>Bénéficiaire</th>
                            <th className="text-right">Montant</th>
                            <th>Statut</th>
                            <th className="pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {expenses.data.length === 0 ? (
                            <tr><td colSpan={6} className="py-8 text-center text-terroir-dark/40">Aucune dépense.</td></tr>
                        ) : expenses.data.map((expense) => (
                            <tr key={expense.id}>
                                <td className="pl-6 text-terroir-dark/60">{expense.expense_date}</td>
                                <td className="font-semibold text-terroir-dark">{expense.category_name}</td>
                                <td className="text-terroir-dark/60">{expense.beneficiary ?? '—'}</td>
                                <td className="text-right font-semibold">{expense.amount.toLocaleString('fr-FR')} FCFA</td>
                                <td><span className={expense.status_badge_class}>{expense.status_label}</span></td>
                                <td className="pr-6 text-right">
                                    {expense.status === 'en_attente' ? (
                                        <>
                                            <button type="button" onClick={() => handleValidate(expense)} className="admin-link bg-transparent">Valider</button>
                                            <button type="button" onClick={() => handleReject(expense)} className="admin-link-danger ml-3 bg-transparent">Rejeter</button>
                                        </>
                                    ) : (
                                        <>
                                            {expense.receipt_url && (
                                                <a href={expense.receipt_url} target="_blank" rel="noopener noreferrer" className="admin-link">Justificatif</a>
                                            )}
                                            <button type="button" onClick={() => handleDelete(expense)} className="admin-link-danger ml-3 bg-transparent">Supprimer</button>
                                        </>
                                    )}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>

            {expenses.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {expenses.links.map((link, i) =>
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
