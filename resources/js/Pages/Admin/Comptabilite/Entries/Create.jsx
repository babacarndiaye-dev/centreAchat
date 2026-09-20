import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../../Layouts/AdminLayout';

const emptyLine = () => ({ chart_account_id: '', debit: '', credit: '', label: '' });

export default function Create({ journals, accounts }) {
    const { data, setData, post, processing, errors, transform } = useForm({
        journal_id: journals[0]?.id ?? '',
        entry_date: new Date().toISOString().slice(0, 10),
        reference: '',
        description: '',
        lines: [emptyLine(), emptyLine()],
    });

    transform((formData) => ({
        journal_id: formData.journal_id,
        entry_date: formData.entry_date,
        reference: formData.reference,
        description: formData.description,
        chart_account_id: formData.lines.map((l) => l.chart_account_id),
        debit: formData.lines.map((l) => l.debit || 0),
        credit: formData.lines.map((l) => l.credit || 0),
        label: formData.lines.map((l) => l.label),
    }));

    const totalDebit = data.lines.reduce((s, l) => s + (Number(l.debit) || 0), 0);
    const totalCredit = data.lines.reduce((s, l) => s + (Number(l.credit) || 0), 0);
    const balanced = Math.abs(totalDebit - totalCredit) < 0.01 && totalDebit > 0;

    function updateLine(i, field, value) {
        const lines = data.lines.slice();
        lines[i] = { ...lines[i], [field]: value };
        setData('lines', lines);
    }

    function addLine() {
        setData('lines', [...data.lines, emptyLine()]);
    }

    function removeLine(i) {
        setData('lines', data.lines.filter((_, idx) => idx !== i));
    }

    function handleSubmit(e) {
        e.preventDefault();
        post(route('admin.comptabilite.ecritures.store'));
    }

    return (
        <AdminLayout title="Nouvelle écriture">
            <Head title="Nouvelle écriture — Administration" />

            <motion.div initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.35 }} className="admin-card max-w-4xl">
                <form onSubmit={handleSubmit}>
                    <div className="grid gap-4 sm:grid-cols-3">
                        <div>
                            <label className="label">Journal</label>
                            <select value={data.journal_id} onChange={(e) => setData('journal_id', e.target.value)} required className="input">
                                {journals.map((j) => <option key={j.id} value={j.id}>{j.code} — {j.name}</option>)}
                            </select>
                        </div>
                        <div>
                            <label className="label">Date</label>
                            <input
                                type="date"
                                value={data.entry_date}
                                onChange={(e) => setData('entry_date', e.target.value)}
                                required
                                className="input"
                            />
                        </div>
                        <div>
                            <label className="label">Référence (optionnel)</label>
                            <input value={data.reference} onChange={(e) => setData('reference', e.target.value)} className="input" />
                        </div>
                    </div>

                    <div className="mt-4">
                        <label className="label">Libellé de l'écriture</label>
                        <input
                            value={data.description}
                            onChange={(e) => setData('description', e.target.value)}
                            required
                            className="input"
                        />
                        {errors.description && <p className="mt-1 text-xs text-terroir-terracotta">{errors.description}</p>}
                    </div>

                    <div className="mt-6 border-t border-terroir-dark/10 pt-6">
                        <h3 className="font-display text-base font-semibold">Lignes d'écriture</h3>

                        <div className="mt-3 flex flex-col gap-3">
                            {data.lines.map((line, i) => (
                                <div key={i} className="flex flex-wrap items-center gap-2">
                                    <select
                                        value={line.chart_account_id}
                                        onChange={(e) => updateLine(i, 'chart_account_id', e.target.value)}
                                        required
                                        className="input min-w-[200px] flex-1"
                                    >
                                        <option value="">Compte...</option>
                                        {accounts.map((a) => <option key={a.id} value={a.id}>{a.label}</option>)}
                                    </select>
                                    <input
                                        value={line.label}
                                        onChange={(e) => updateLine(i, 'label', e.target.value)}
                                        placeholder="Libellé ligne"
                                        className="input w-40"
                                    />
                                    <input
                                        type="number"
                                        step="0.01"
                                        value={line.debit}
                                        onChange={(e) => updateLine(i, 'debit', e.target.value)}
                                        placeholder="Débit"
                                        className="input w-28"
                                    />
                                    <input
                                        type="number"
                                        step="0.01"
                                        value={line.credit}
                                        onChange={(e) => updateLine(i, 'credit', e.target.value)}
                                        placeholder="Crédit"
                                        className="input w-28"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => removeLine(i)}
                                        className="font-semibold text-terroir-terracotta"
                                        aria-label="Retirer"
                                    >
                                        <span className="material-symbols-outlined text-lg">close</span>
                                    </button>
                                </div>
                            ))}
                        </div>

                        <button type="button" onClick={addLine} className="btn-outline mt-4">+ Ajouter une ligne</button>

                        {errors.debit && <p className="mt-2 text-xs text-terroir-terracotta">{errors.debit}</p>}

                        <div className="mt-4 flex justify-end gap-8 text-sm">
                            <span>Débit : <strong>{totalDebit.toLocaleString('fr-FR')}</strong></span>
                            <span>Crédit : <strong>{totalCredit.toLocaleString('fr-FR')}</strong></span>
                            <span className={'flex items-center gap-1 font-semibold ' + (balanced ? 'text-terroir-green' : 'text-terroir-terracotta')}>
                                {balanced && <span className="material-symbols-outlined is-filled text-base">check_circle</span>}
                                {balanced ? 'Équilibrée' : 'Non équilibrée'}
                            </span>
                        </div>
                    </div>

                    <div className="mt-6 flex gap-3">
                        <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Enregistrer l'écriture</button>
                        <Link href={route('admin.comptabilite.ecritures.index')} className="btn-outline">Annuler</Link>
                    </div>
                </form>
            </motion.div>
        </AdminLayout>
    );
}
