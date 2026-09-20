import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../../Layouts/AdminLayout';

export default function Create({ categories, accounts }) {
    const { data, setData, post, processing, errors } = useForm({
        expense_category_id: '',
        expense_date: new Date().toISOString().slice(0, 10),
        amount: '',
        beneficiary: '',
        payment_account_id: '',
        description: '',
        receipt: null,
    });

    function handleSubmit(e) {
        e.preventDefault();
        post(route('admin.depenses.store'), { forceFormData: true });
    }

    return (
        <AdminLayout title="Nouvelle dépense">
            <Head title="Nouvelle dépense — Administration" />

            <motion.div initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.35 }} className="admin-card max-w-3xl">
                <form onSubmit={handleSubmit}>
                    <div className="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Catégorie</label>
                            <select
                                value={data.expense_category_id}
                                onChange={(e) => setData('expense_category_id', e.target.value)}
                                required
                                className="input"
                            >
                                <option value="">Choisir...</option>
                                {categories.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
                            </select>
                            {errors.expense_category_id && <p className="mt-1 text-xs text-terroir-terracotta">{errors.expense_category_id}</p>}
                        </div>
                        <div>
                            <label className="label">Date</label>
                            <input
                                type="date"
                                value={data.expense_date}
                                onChange={(e) => setData('expense_date', e.target.value)}
                                required
                                className="input"
                            />
                        </div>
                    </div>

                    <div className="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Montant (FCFA)</label>
                            <input
                                type="number"
                                step="0.01"
                                value={data.amount}
                                onChange={(e) => setData('amount', e.target.value)}
                                required
                                className="input"
                            />
                            {errors.amount && <p className="mt-1 text-xs text-terroir-terracotta">{errors.amount}</p>}
                        </div>
                        <div>
                            <label className="label">Bénéficiaire (optionnel)</label>
                            <input value={data.beneficiary} onChange={(e) => setData('beneficiary', e.target.value)} className="input" />
                        </div>
                    </div>

                    <div className="mt-4">
                        <label className="label">Compte de paiement</label>
                        <select value={data.payment_account_id} onChange={(e) => setData('payment_account_id', e.target.value)} className="input">
                            <option value="">Non renseigné pour l'instant</option>
                            {accounts.map((a) => <option key={a.id} value={a.id}>{a.name} ({a.type_label})</option>)}
                        </select>
                        <p className="mt-1.5 text-sm text-terroir-dark/50">Le compte sera débité automatiquement lors de la validation de la dépense.</p>
                    </div>

                    <div className="mt-4">
                        <label className="label">Description (optionnel)</label>
                        <textarea rows={3} value={data.description} onChange={(e) => setData('description', e.target.value)} className="input" />
                    </div>

                    <div className="mt-4">
                        <label className="label">Justificatif (optionnel)</label>
                        <input
                            type="file"
                            accept="image/*"
                            onChange={(e) => setData('receipt', e.target.files[0] ?? null)}
                            className="input"
                        />
                        {errors.receipt && <p className="mt-1 text-xs text-terroir-terracotta">{errors.receipt}</p>}
                    </div>

                    <div className="mt-6 flex gap-3">
                        <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Enregistrer la dépense</button>
                        <Link href={route('admin.depenses.index')} className="btn-outline">Annuler</Link>
                    </div>
                </form>
            </motion.div>
        </AdminLayout>
    );
}
