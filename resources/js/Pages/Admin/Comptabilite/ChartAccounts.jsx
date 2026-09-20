import { Head, useForm, router } from '@inertiajs/react';
import { useState } from 'react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

function AccountRow({ account }) {
    const { data, setData, patch, processing } = useForm({ name: account.name, is_active: account.is_active });

    function handleSubmit(e) {
        e.preventDefault();
        patch(route('admin.comptabilite.plan-comptable.update', account.id), { preserveScroll: true });
    }

    function handleDelete() {
        if (!confirm('Supprimer ce compte ?')) return;
        router.delete(route('admin.comptabilite.plan-comptable.destroy', account.id), { preserveScroll: true });
    }

    return (
        <tr>
            <td className="pl-6">
                <form onSubmit={handleSubmit} className="flex items-center gap-3">
                    <span className="w-16 font-mono font-semibold text-terroir-dark/70">{account.code}</span>
                    <input value={data.name} onChange={(e) => setData('name', e.target.value)} className="input flex-1" />
                    <label className="flex items-center gap-1 whitespace-nowrap text-sm">
                        <input
                            type="checkbox"
                            checked={data.is_active}
                            onChange={(e) => setData('is_active', e.target.checked)}
                            className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"
                        />
                        Actif
                    </label>
                    <button type="submit" disabled={processing} className="text-sm font-semibold text-terroir-green disabled:opacity-50">Enregistrer</button>
                </form>
            </td>
            <td className="pr-6 text-right">
                <button type="button" onClick={handleDelete} className="admin-link-danger bg-transparent">Supprimer</button>
            </td>
        </tr>
    );
}

export default function ChartAccounts({ accounts, classes }) {
    const { data, setData, post, processing, errors, reset } = useForm({ code: '', name: '', class: '1' });

    function handleCreate(e) {
        e.preventDefault();
        post(route('admin.comptabilite.plan-comptable.store'), { preserveScroll: true, onSuccess: () => reset() });
    }

    return (
        <AdminLayout title="Plan comptable">
            <Head title="Plan comptable — Administration" />

            <motion.div initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.35 }} className="admin-card max-w-3xl">
                <h2 className="font-display text-lg font-semibold">Nouveau compte</h2>
                <form onSubmit={handleCreate} className="mt-3 flex flex-wrap gap-3">
                    <input
                        value={data.code}
                        onChange={(e) => setData('code', e.target.value)}
                        placeholder="Code (ex : 701)"
                        required
                        className="input w-32"
                    />
                    <input
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        placeholder="Intitulé"
                        required
                        className="input min-w-[200px] flex-1"
                    />
                    <select value={data.class} onChange={(e) => setData('class', e.target.value)} required className="input w-40">
                        {Object.entries(classes).map(([value, label]) => <option key={value} value={value}>Classe {value}</option>)}
                    </select>
                    <button type="submit" disabled={processing} className="btn-primary w-full justify-center disabled:opacity-50">Ajouter</button>
                    {errors.code && <p className="w-full text-xs text-terroir-terracotta">{errors.code}</p>}
                </form>
            </motion.div>

            {Object.entries(classes).map(([classNum, classLabel]) => {
                const group = accounts[classNum];
                if (!group || group.length === 0) return null;

                return (
                    <div key={classNum} className="admin-card mt-6 overflow-x-auto p-0">
                        <div className="bg-terroir-cream px-5 py-3 font-display text-sm font-semibold text-terroir-green">{classLabel}</div>
                        <table className="admin-table">
                            <tbody>
                                {group.map((account) => <AccountRow key={account.id} account={account} />)}
                            </tbody>
                        </table>
                    </div>
                );
            })}
        </AdminLayout>
    );
}
