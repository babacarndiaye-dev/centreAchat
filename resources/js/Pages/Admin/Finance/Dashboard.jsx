import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

function fcfa(value) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(value)) + ' FCFA';
}

export default function Dashboard({
    balancesByType, types, treasuryAvailable, receivables, payables,
    monthRevenue, monthPurchases, monthExpenses, provisionalResult,
    pendingExpenses, recentExpenses,
}) {
    return (
        <AdminLayout title="Trésorerie">
            <Head title="Trésorerie — Administration" />

            <div className="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div className="admin-card">
                    <p className="text-sm text-terroir-dark/50">Trésorerie disponible</p>
                    <p className="mt-1.5 text-2xl font-bold text-terroir-green">{fcfa(treasuryAvailable)}</p>
                </div>
                <div className="admin-card">
                    <p className="text-sm text-terroir-dark/50">Créances clients</p>
                    <p className="mt-1.5 text-2xl font-bold text-terroir-terracotta">{fcfa(receivables)}</p>
                </div>
                <div className="admin-card">
                    <p className="text-sm text-terroir-dark/50">Dettes fournisseurs</p>
                    <p className="mt-1.5 text-2xl font-bold text-terroir-terracotta">{fcfa(payables)}</p>
                </div>
                <div className="admin-card">
                    <p className="text-sm text-terroir-dark/50">Dépenses en attente</p>
                    <p className="mt-1.5 text-2xl font-bold">{pendingExpenses}</p>
                </div>
            </div>

            <motion.div initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.35 }} className="mt-6 grid gap-6 lg:grid-cols-2">
                <div className="admin-card">
                    <h2 className="font-display text-base font-semibold">Solde par type de compte</h2>
                    <div className="mt-3 flex flex-col gap-3">
                        {Object.entries(types).map(([type, label]) => (
                            <div key={type} className="flex items-center justify-between rounded-lg bg-terroir-cream px-4 py-3 text-sm">
                                <span>{label}</span>
                                <span className="font-semibold">{fcfa(balancesByType[type] ?? 0)}</span>
                            </div>
                        ))}
                    </div>
                    <Link href={route('admin.comptes-paiement.index')} className="mt-3 block text-sm font-semibold text-terroir-green">
                        Gérer les comptes →
                    </Link>
                </div>

                <div className="admin-card">
                    <h2 className="font-display text-base font-semibold">Résultat provisoire du mois</h2>
                    <dl className="mt-3 text-sm">
                        <div className="mb-2 flex justify-between">
                            <dt className="text-terroir-dark/50">Chiffre d'affaires (ventes)</dt>
                            <dd className="font-medium text-terroir-green">+{fcfa(monthRevenue)}</dd>
                        </div>
                        <div className="mb-2 flex justify-between">
                            <dt className="text-terroir-dark/50">Achats fournisseurs</dt>
                            <dd className="font-medium text-terroir-terracotta">-{fcfa(monthPurchases)}</dd>
                        </div>
                        <div className="mb-2 flex justify-between">
                            <dt className="text-terroir-dark/50">Dépenses validées</dt>
                            <dd className="font-medium text-terroir-terracotta">-{fcfa(monthExpenses)}</dd>
                        </div>
                        <div className={'flex justify-between border-t border-terroir-green/10 pt-2 text-base font-bold ' + (provisionalResult >= 0 ? 'text-terroir-green' : 'text-terroir-terracotta')}>
                            <dt>Résultat provisoire</dt>
                            <dd>{fcfa(provisionalResult)}</dd>
                        </div>
                    </dl>
                    <p className="mt-3 text-sm text-terroir-dark/40">
                        Estimation en trésorerie (encaissements/décaissements), hors amortissements et charges non décaissées.
                    </p>
                </div>
            </motion.div>

            <div className="admin-card mt-6">
                <div className="flex items-center justify-between">
                    <h2 className="font-display text-base font-semibold">Dépenses récentes validées</h2>
                    <Link href={route('admin.depenses.index')} className="admin-link">Voir tout →</Link>
                </div>
                <table className="admin-table mt-3">
                    <thead>
                        <tr>
                            <th>Catégorie</th>
                            <th>Date</th>
                            <th className="text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        {recentExpenses.length === 0 ? (
                            <tr><td colSpan={3} className="py-6 text-center text-terroir-dark/40">Aucune dépense validée.</td></tr>
                        ) : recentExpenses.map((expense) => (
                            <tr key={expense.id}>
                                <td>{expense.category_name}</td>
                                <td className="text-terroir-dark/60">{expense.expense_date}</td>
                                <td className="text-right font-semibold">{fcfa(expense.amount)}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </AdminLayout>
    );
}
