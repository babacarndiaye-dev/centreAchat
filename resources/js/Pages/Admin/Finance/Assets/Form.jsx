import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../../Layouts/AdminLayout';

export default function Form({ asset, categories, methods, paymentAccounts, suppliers }) {
    const isEdit = !!asset;
    const { data, setData, post, put, processing, errors } = useForm({
        name: asset?.name ?? '',
        category: asset?.category ?? Object.keys(categories)[0] ?? '',
        acquisition_date: asset?.acquisition_date ?? new Date().toISOString().slice(0, 10),
        acquisition_value: asset?.acquisition_value ?? '',
        useful_life_years: asset?.useful_life_years ?? 5,
        depreciation_method: asset?.depreciation_method ?? 'lineaire',
        payment_account_id: asset?.payment_account_id ?? '',
        supplier_id: asset?.supplier_id ?? '',
        notes: asset?.notes ?? '',
    });

    function handleSubmit(e) {
        e.preventDefault();
        if (isEdit) {
            put(route('admin.immobilisations.update', asset.id));
        } else {
            post(route('admin.immobilisations.store'));
        }
    }

    return (
        <AdminLayout title={isEdit ? "Modifier l'immobilisation" : 'Nouvelle immobilisation'}>
            <Head title={`${isEdit ? "Modifier l'immobilisation" : 'Nouvelle immobilisation'} — Administration`} />

            <motion.div initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.35 }} className="admin-card max-w-3xl">
                <form onSubmit={handleSubmit}>
                    <div className="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Désignation</label>
                            <input value={data.name} onChange={(e) => setData('name', e.target.value)} required className="input" />
                            {errors.name && <p className="mt-1 text-xs text-terroir-terracotta">{errors.name}</p>}
                        </div>
                        <div>
                            <label className="label">Catégorie</label>
                            <select value={data.category} onChange={(e) => setData('category', e.target.value)} required className="input">
                                {Object.entries(categories).map(([value, label]) => <option key={value} value={value}>{label}</option>)}
                            </select>
                        </div>
                    </div>

                    <div className="mt-6 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Date d'acquisition</label>
                            <input
                                type="date"
                                value={data.acquisition_date}
                                onChange={(e) => setData('acquisition_date', e.target.value)}
                                required
                                className="input"
                            />
                        </div>
                        <div>
                            <label className="label">Valeur d'acquisition (FCFA)</label>
                            <input
                                type="number"
                                step="0.01"
                                value={data.acquisition_value}
                                onChange={(e) => setData('acquisition_value', e.target.value)}
                                required
                                className="input"
                            />
                            {errors.acquisition_value && <p className="mt-1 text-xs text-terroir-terracotta">{errors.acquisition_value}</p>}
                        </div>
                    </div>

                    <div className="mt-6 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Durée d'amortissement (années)</label>
                            <input
                                type="number"
                                min="1"
                                max="50"
                                value={data.useful_life_years}
                                onChange={(e) => setData('useful_life_years', e.target.value)}
                                required
                                className="input"
                            />
                        </div>
                        <div>
                            <label className="label">Méthode d'amortissement</label>
                            <select
                                value={data.depreciation_method}
                                onChange={(e) => setData('depreciation_method', e.target.value)}
                                required
                                className="input"
                            >
                                {Object.entries(methods).map(([value, label]) => <option key={value} value={value}>{label}</option>)}
                            </select>
                        </div>
                    </div>

                    <div className="mt-6 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Compte de paiement (optionnel)</label>
                            <select
                                value={data.payment_account_id}
                                onChange={(e) => setData('payment_account_id', e.target.value)}
                                className="input"
                            >
                                <option value="">Non renseigné</option>
                                {paymentAccounts.map((a) => <option key={a.id} value={a.id}>{a.name}</option>)}
                            </select>
                            <p className="mt-2 text-xs text-terroir-dark/50">Génère l'écriture comptable d'acquisition si renseigné.</p>
                        </div>
                        <div>
                            <label className="label">Fournisseur (optionnel)</label>
                            <select value={data.supplier_id} onChange={(e) => setData('supplier_id', e.target.value)} className="input">
                                <option value="">Non renseigné</option>
                                {suppliers.map((s) => <option key={s.id} value={s.id}>{s.name}</option>)}
                            </select>
                        </div>
                    </div>

                    <div className="mt-6">
                        <label className="label">Notes (optionnel)</label>
                        <textarea rows={2} value={data.notes} onChange={(e) => setData('notes', e.target.value)} className="input" />
                    </div>

                    <div className="mt-6 flex gap-3">
                        <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Enregistrer</button>
                        <Link href={route('admin.immobilisations.index')} className="btn-outline">Annuler</Link>
                    </div>
                </form>
            </motion.div>
        </AdminLayout>
    );
}
