import { Head, router, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

function fcfa(value) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(value)) + ' FCFA';
}

function ImportForm({ account }) {
    const { data, setData, post, processing, errors, reset } = useForm({ file: null });

    function handleSubmit(e) {
        e.preventDefault();
        post(route('admin.comptabilite.rapprochement.import', account.id), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    }

    return (
        <form onSubmit={handleSubmit} className="mt-3 flex flex-wrap gap-3">
            <input
                type="file"
                accept=".csv,.txt"
                required
                onChange={(e) => setData('file', e.target.files[0])}
                className="input min-w-[240px] flex-1"
            />
            <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Importer et rapprocher</button>
            {errors.file && <p className="w-full text-xs text-terroir-terracotta">{errors.file}</p>}
        </form>
    );
}

function MatchForm({ line, unreconciledTransactions }) {
    const { data, setData, post, processing } = useForm({ transaction_id: '' });

    function handleSubmit(e) {
        e.preventDefault();
        if (!data.transaction_id) return;
        post(route('admin.comptabilite.rapprochement.match', line.id), { preserveScroll: true });
    }

    return (
        <form onSubmit={handleSubmit} className="flex flex-1 gap-2">
            <select value={data.transaction_id} onChange={(e) => setData('transaction_id', e.target.value)} className="input flex-1">
                <option value="">Associer à un mouvement système...</option>
                {unreconciledTransactions.map((t) => (
                    <option key={t.id} value={t.id}>{t.transaction_date} — {t.description} ({t.signed_amount.toLocaleString('fr-FR')})</option>
                ))}
            </select>
            <button type="submit" disabled={processing} className="text-sm font-semibold text-terroir-green disabled:opacity-50">Lier</button>
        </form>
    );
}

export default function Reconciliation({ accounts, account, unmatchedLines, unreconciledTransactions, reconciledCount, stats, selectedAccount }) {
    function handleChange(e) {
        const compte = e.target.value;
        router.get(route('admin.comptabilite.rapprochement.index'), compte ? { compte } : {}, { preserveState: true });
    }

    function handleCreateTransaction(line) {
        router.post(route('admin.comptabilite.rapprochement.create-transaction', line.id), {}, { preserveScroll: true });
    }

    function handleDiscrepancy(line) {
        router.patch(route('admin.comptabilite.rapprochement.discrepancy', line.id), {}, { preserveScroll: true });
    }

    return (
        <AdminLayout title="Rapprochement bancaire">
            <Head title="Rapprochement bancaire — Administration" />

            <form className="flex gap-2">
                <select defaultValue={selectedAccount ?? ''} onChange={handleChange} className="input max-w-md">
                    <option value="">Choisir un compte bancaire ou mobile money...</option>
                    {accounts.map((acc) => <option key={acc.id} value={acc.id}>{acc.name}</option>)}
                </select>
            </form>

            {account ? (
                <motion.div initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.35 }}>
                    <div className="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
                        <div className="admin-card">
                            <p className="text-sm text-terroir-dark/50">Solde comptable</p>
                            <p className="mt-1 text-xl font-bold text-terroir-green">{fcfa(stats.solde_comptable)}</p>
                        </div>
                        <div className="admin-card">
                            <p className="text-sm text-terroir-dark/50">Total relevé importé</p>
                            <p className="mt-1 text-xl font-bold">{fcfa(stats.total_releve)}</p>
                        </div>
                        <div className="admin-card">
                            <p className="text-sm text-terroir-dark/50">Lignes rapprochées</p>
                            <p className="mt-1 text-xl font-bold text-terroir-green">{reconciledCount}</p>
                        </div>
                        <div className="admin-card">
                            <p className="text-sm text-terroir-dark/50">En attente</p>
                            <p className={'mt-1 text-xl font-bold ' + (stats.lignes_en_attente > 0 ? 'text-terroir-terracotta' : '')}>
                                {stats.lignes_en_attente}
                            </p>
                        </div>
                    </div>

                    <div className="admin-card mt-6">
                        <h3 className="font-display text-base font-semibold">Importer un relevé bancaire</h3>
                        <p className="mt-1 text-sm text-terroir-dark/50">
                            Fichier CSV : date;description;montant (montant positif = crédit, négatif = débit). Séparateur point-virgule ou virgule.
                        </p>
                        <ImportForm account={account} />
                    </div>

                    <div className="mt-6 grid gap-6 lg:grid-cols-2">
                        <div className="admin-card">
                            <h3 className="font-display text-base font-semibold">Lignes du relevé non rapprochées</h3>
                            <div className="mt-3 flex flex-col gap-3">
                                {unmatchedLines.length === 0 ? (
                                    <p className="text-sm text-terroir-dark/50">Aucune ligne en attente — tout est rapproché.</p>
                                ) : unmatchedLines.map((line) => (
                                    <div key={line.id} className="rounded-lg bg-terroir-cream p-4 text-sm">
                                        <div className="flex items-center justify-between">
                                            <span className="font-semibold">{line.statement_date} — {line.description}</span>
                                            <span className={'font-semibold ' + (line.amount >= 0 ? 'text-terroir-green' : 'text-terroir-terracotta')}>
                                                {fcfa(line.amount)}
                                            </span>
                                        </div>
                                        {line.status === 'ecart' && <span className="admin-badge-danger mt-1.5">Écart signalé</span>}

                                        <div className="mt-3 flex flex-wrap gap-2">
                                            <MatchForm line={line} unreconciledTransactions={unreconciledTransactions} />
                                        </div>
                                        <div className="mt-3 flex gap-3">
                                            <button
                                                type="button"
                                                onClick={() => handleCreateTransaction(line)}
                                                className="text-sm font-semibold text-terroir-green"
                                            >
                                                Créer le mouvement correspondant
                                            </button>
                                            {line.status !== 'ecart' && (
                                                <button
                                                    type="button"
                                                    onClick={() => handleDiscrepancy(line)}
                                                    className="text-sm font-semibold text-terroir-terracotta"
                                                >
                                                    Marquer comme écart
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        <div className="admin-card">
                            <h3 className="font-display text-base font-semibold">Mouvements système non rapprochés</h3>
                            <div className="mt-3 flex flex-col gap-2">
                                {unreconciledTransactions.length === 0 ? (
                                    <p className="text-sm text-terroir-dark/50">Aucun mouvement en attente de rapprochement.</p>
                                ) : unreconciledTransactions.map((t) => (
                                    <div key={t.id} className="flex items-center justify-between rounded-lg bg-terroir-cream px-4 py-2.5 text-sm">
                                        <span>{t.transaction_date} — {t.description}</span>
                                        <span className={'font-semibold ' + (t.type === 'entree' ? 'text-terroir-green' : 'text-terroir-terracotta')}>
                                            {fcfa(t.signed_amount)}
                                        </span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </motion.div>
            ) : (
                <p className="mt-6 text-terroir-dark/50">Sélectionnez un compte bancaire ou mobile money pour démarrer le rapprochement.</p>
            )}
        </AdminLayout>
    );
}
