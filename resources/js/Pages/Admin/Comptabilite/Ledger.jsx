import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Ledger({ accounts, account, lines, selectedAccount }) {
    function handleChange(e) {
        const compte = e.target.value;
        router.get(route('admin.comptabilite.grand-livre.index'), compte ? { compte } : {}, { preserveState: true });
    }

    return (
        <AdminLayout title="Grand livre">
            <Head title="Grand livre — Administration" />

            <form className="flex gap-2">
                <select defaultValue={selectedAccount ?? ''} onChange={handleChange} className="input max-w-md">
                    <option value="">Choisir un compte...</option>
                    {accounts.map((acc) => <option key={acc.id} value={acc.id}>{acc.code} — {acc.name}</option>)}
                </select>
            </form>

            {account ? (
                <motion.div
                    initial={{ opacity: 0, y: 12 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.35 }}
                    className="admin-card mt-6 overflow-x-auto p-0"
                >
                    <div className="bg-terroir-cream px-5 py-3 font-display text-sm font-semibold text-terroir-green">
                        {account.code} — {account.name}
                    </div>
                    <table className="admin-table">
                        <thead>
                            <tr>
                                <th className="pl-6">Date</th>
                                <th>Libellé</th>
                                <th className="text-right">Débit</th>
                                <th className="text-right">Crédit</th>
                                <th className="pr-6 text-right">Solde</th>
                            </tr>
                        </thead>
                        <tbody>
                            {lines.length === 0 ? (
                                <tr><td colSpan={5} className="py-8 text-center text-terroir-dark/40">Aucun mouvement sur ce compte.</td></tr>
                            ) : lines.map((line) => (
                                <tr key={line.id}>
                                    <td className="pl-6 text-terroir-dark/60">{line.date}</td>
                                    <td>
                                        <Link href={route('admin.comptabilite.ecritures.show', line.entry_id)} className="admin-link">
                                            {line.description}
                                        </Link>
                                        {line.label ? ` — ${line.label}` : ''}
                                    </td>
                                    <td className="text-right">{line.debit > 0 ? line.debit.toLocaleString('fr-FR') : ''}</td>
                                    <td className="text-right">{line.credit > 0 ? line.credit.toLocaleString('fr-FR') : ''}</td>
                                    <td className={'pr-6 text-right font-semibold ' + (line.running_balance < 0 ? 'text-terroir-terracotta' : '')}>
                                        {line.running_balance.toLocaleString('fr-FR')}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </motion.div>
            ) : (
                <p className="mt-6 text-terroir-dark/50">Sélectionnez un compte pour consulter son grand livre.</p>
            )}
        </AdminLayout>
    );
}
