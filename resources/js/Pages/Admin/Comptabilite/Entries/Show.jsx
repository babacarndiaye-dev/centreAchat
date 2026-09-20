import { Head, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../../Layouts/AdminLayout';

export default function Show({ entry }) {
    function handleDelete() {
        if (!confirm('Supprimer cette écriture ?')) return;
        router.delete(route('admin.comptabilite.ecritures.destroy', entry.id));
    }

    return (
        <AdminLayout title={entry.description}>
            <Head title={`${entry.description} — Administration`} />

            <div className="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 className="font-display text-2xl font-semibold">{entry.description}</h2>
                    <p className="text-sm text-terroir-dark/50">
                        {entry.journal.code} — {entry.entry_date}
                        {entry.reference ? ` — Réf. ${entry.reference}` : ''}
                    </p>
                </div>
                {!entry.source_type && (
                    <button type="button" onClick={handleDelete} className="btn-outline text-terroir-terracotta">Supprimer</button>
                )}
            </div>

            <motion.div initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.35 }} className="admin-card mt-6">
                <dl className="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt className="text-sm text-terroir-dark/50">Journal</dt>
                        <dd className="font-semibold">{entry.journal.code} — {entry.journal.name}</dd>
                    </div>
                    <div>
                        <dt className="text-sm text-terroir-dark/50">Date</dt>
                        <dd>{entry.entry_date}</dd>
                    </div>
                    <div>
                        <dt className="text-sm text-terroir-dark/50">Référence</dt>
                        <dd>{entry.reference || '—'}</dd>
                    </div>
                    <div>
                        <dt className="text-sm text-terroir-dark/50">Libellé</dt>
                        <dd>{entry.description}</dd>
                    </div>
                </dl>
            </motion.div>

            <div className="admin-card mt-6 overflow-x-auto p-0">
                <table className="admin-table">
                    <thead>
                        <tr>
                            <th className="pl-6">Compte</th>
                            <th>Libellé</th>
                            <th className="text-right">Débit</th>
                            <th className="pr-6 text-right">Crédit</th>
                        </tr>
                    </thead>
                    <tbody>
                        {entry.lines.map((line) => (
                            <tr key={line.id}>
                                <td className="pl-6 font-mono">{line.account_code} — {line.account_name}</td>
                                <td className="text-terroir-dark/60">{line.label}</td>
                                <td className="text-right">{line.debit > 0 ? line.debit.toLocaleString('fr-FR') : ''}</td>
                                <td className="pr-6 text-right">{line.credit > 0 ? line.credit.toLocaleString('fr-FR') : ''}</td>
                            </tr>
                        ))}
                    </tbody>
                    <tfoot>
                        <tr className="border-t-2 border-terroir-dark/20 font-bold text-terroir-green">
                            <td className="pl-6" colSpan={2}>Total</td>
                            <td className="text-right">{entry.total_debit.toLocaleString('fr-FR')}</td>
                            <td className="pr-6 text-right">{entry.total_credit.toLocaleString('fr-FR')}</td>
                        </tr>
                    </tfoot>
                </table>
                {entry.source_type && <p className="p-5 text-sm text-terroir-dark/50">Écriture générée automatiquement.</p>}
            </div>
        </AdminLayout>
    );
}
