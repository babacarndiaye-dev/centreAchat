import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function TrialBalance({ accounts, totalDebit, totalCredit }) {
    const isBalanced = Math.round(totalDebit * 100) === Math.round(totalCredit * 100);

    return (
        <AdminLayout title="Balance générale">
            <Head title="Balance générale — Administration" />

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="admin-card overflow-x-auto p-0"
            >
                <table className="admin-table">
                    <thead>
                        <tr>
                            <th className="pl-6">Compte</th>
                            <th className="text-right">Débit</th>
                            <th className="text-right">Crédit</th>
                            <th className="pr-6 text-right">Solde</th>
                        </tr>
                    </thead>
                    <tbody>
                        {accounts.length === 0 ? (
                            <tr><td colSpan={4} className="py-8 text-center text-terroir-dark/40">Aucune écriture enregistrée.</td></tr>
                        ) : accounts.map((account) => (
                            <tr key={account.id}>
                                <td className="pl-6">
                                    <Link href={route('admin.comptabilite.grand-livre.index', { compte: account.id })} className="font-mono text-terroir-green">
                                        {account.code}
                                    </Link>
                                    {' '}— {account.name}
                                </td>
                                <td className="text-right">{account.debit_total.toLocaleString('fr-FR')}</td>
                                <td className="text-right">{account.credit_total.toLocaleString('fr-FR')}</td>
                                <td className={'pr-6 text-right font-semibold ' + (account.solde < 0 ? 'text-terroir-terracotta' : '')}>
                                    {account.solde.toLocaleString('fr-FR')}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                    <tfoot>
                        <tr className="border-t-2 border-terroir-dark/20 font-bold text-terroir-green">
                            <td className="pl-6">Total</td>
                            <td className="text-right">{totalDebit.toLocaleString('fr-FR')}</td>
                            <td className="text-right">{totalCredit.toLocaleString('fr-FR')}</td>
                            <td className="pr-6 text-right">
                                {isBalanced ? (
                                    <span className="inline-flex items-center gap-1">
                                        <span className="material-symbols-outlined is-filled text-base">check_circle</span> Équilibrée
                                    </span>
                                ) : 'Écart'}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </motion.div>
        </AdminLayout>
    );
}
