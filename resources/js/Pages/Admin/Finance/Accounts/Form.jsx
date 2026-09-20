import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../../Layouts/AdminLayout';

export default function Form({ account, types, chartAccounts }) {
    const isEdit = !!account;
    const { data, setData, post, put, processing, errors } = useForm({
        name: account?.name ?? '',
        type: account?.type ?? Object.keys(types)[0] ?? '',
        provider: account?.provider ?? '',
        account_number: account?.account_number ?? '',
        chart_account_id: account?.chart_account_id ?? '',
        initial_balance: account?.initial_balance ?? 0,
        notes: account?.notes ?? '',
        is_active: account?.is_active ?? true,
    });

    function handleSubmit(e) {
        e.preventDefault();
        if (isEdit) {
            put(route('admin.comptes-paiement.update', account.id));
        } else {
            post(route('admin.comptes-paiement.store'));
        }
    }

    return (
        <AdminLayout title={isEdit ? 'Modifier le compte' : 'Nouveau compte'}>
            <Head title={`${isEdit ? 'Modifier le compte' : 'Nouveau compte'} — Administration`} />

            <motion.div initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.35 }} className="admin-card max-w-[56rem]">
                <form onSubmit={handleSubmit}>
                    <div className="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Nom du compte</label>
                            <input value={data.name} onChange={(e) => setData('name', e.target.value)} required className="input" />
                            {errors.name && <p className="mt-1 text-xs text-terroir-terracotta">{errors.name}</p>}
                        </div>
                        <div>
                            <label className="label">Type</label>
                            <select value={data.type} onChange={(e) => setData('type', e.target.value)} required className="input">
                                {Object.entries(types).map(([value, label]) => <option key={value} value={value}>{label}</option>)}
                            </select>
                        </div>
                    </div>

                    <div className="mt-6 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Opérateur / Banque (optionnel)</label>
                            <input
                                value={data.provider}
                                onChange={(e) => setData('provider', e.target.value)}
                                placeholder="Ex : Wave, Orange Money, Ecobank"
                                className="input"
                            />
                        </div>
                        <div>
                            <label className="label">Numéro de compte (optionnel)</label>
                            <input value={data.account_number} onChange={(e) => setData('account_number', e.target.value)} className="input" />
                        </div>
                    </div>

                    <div className="mt-6">
                        <label className="label">Compte comptable lié (classe 5)</label>
                        <select value={data.chart_account_id} onChange={(e) => setData('chart_account_id', e.target.value)} className="input">
                            <option value="">Non lié</option>
                            {chartAccounts.map((ca) => <option key={ca.id} value={ca.id}>{ca.label}</option>)}
                        </select>
                        <p className="mt-2 text-sm text-terroir-dark/50">Nécessaire pour générer automatiquement les écritures comptables liées à ce compte.</p>
                    </div>

                    <div className="mt-6">
                        <label className="label">Solde initial (FCFA)</label>
                        <input
                            type="number"
                            step="0.01"
                            value={data.initial_balance}
                            onChange={(e) => setData('initial_balance', e.target.value)}
                            required
                            className="input"
                            style={{ maxWidth: '280px' }}
                        />
                    </div>

                    <div className="mt-6">
                        <label className="label">Notes (optionnel)</label>
                        <textarea rows={2} value={data.notes} onChange={(e) => setData('notes', e.target.value)} className="input" />
                    </div>

                    <div className="mt-6">
                        <label className="flex items-center gap-2">
                            <input
                                type="checkbox"
                                checked={data.is_active}
                                onChange={(e) => setData('is_active', e.target.checked)}
                                className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"
                            />
                            Compte actif
                        </label>
                    </div>

                    <div className="mt-6 flex gap-3">
                        <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Enregistrer</button>
                        <Link href={route('admin.comptes-paiement.index')} className="btn-outline">Annuler</Link>
                    </div>
                </form>
            </motion.div>
        </AdminLayout>
    );
}
