import { Head, useForm, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

function CategoryRow({ category, chartAccounts }) {
    const { data, setData, patch, processing } = useForm({
        name: category.name,
        chart_account_id: category.chart_account_id ?? '',
        is_active: category.is_active,
    });

    function handleSubmit(e) {
        e.preventDefault();
        patch(route('admin.categories-depenses.update', category.id), { preserveScroll: true });
    }

    function handleDelete() {
        if (!confirm('Supprimer cette catégorie ?')) return;
        router.delete(route('admin.categories-depenses.destroy', category.id), { preserveScroll: true });
    }

    return (
        <div className="flex items-center justify-between rounded-lg bg-terroir-cream px-4 py-2.5">
            <form onSubmit={handleSubmit} className="flex flex-1 items-center gap-3">
                <input value={data.name} onChange={(e) => setData('name', e.target.value)} className="input flex-1" />
                <select value={data.chart_account_id} onChange={(e) => setData('chart_account_id', e.target.value)} className="input max-w-[220px]">
                    <option value="">Compte comptable</option>
                    {chartAccounts.map((ca) => <option key={ca.id} value={ca.id}>{ca.label}</option>)}
                </select>
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
            <button type="button" onClick={handleDelete} className="admin-link-danger ml-3 bg-transparent">Supprimer</button>
        </div>
    );
}

export default function ExpenseCategories({ categories, chartAccounts }) {
    const { data, setData, post, processing, errors, reset } = useForm({ name: '', chart_account_id: '' });

    function handleCreate(e) {
        e.preventDefault();
        post(route('admin.categories-depenses.store'), { preserveScroll: true, onSuccess: () => reset() });
    }

    return (
        <AdminLayout title="Catégories de dépenses">
            <Head title="Catégories de dépenses — Administration" />

            <motion.div initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.35 }} className="admin-card max-w-3xl">
                <h2 className="font-display text-lg font-semibold">Nouvelle catégorie</h2>
                <form onSubmit={handleCreate} className="mt-3 flex gap-2">
                    <input
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        placeholder="Ex : Loyer, Électricité, Carburant..."
                        required
                        className="input flex-1"
                    />
                    <select value={data.chart_account_id} onChange={(e) => setData('chart_account_id', e.target.value)} className="input max-w-[280px]">
                        <option value="">Compte comptable (optionnel)</option>
                        {chartAccounts.map((ca) => <option key={ca.id} value={ca.id}>{ca.label}</option>)}
                    </select>
                    <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Ajouter</button>
                </form>
                {errors.name && <p className="mt-1 text-xs text-terroir-terracotta">{errors.name}</p>}

                <div className="mt-4 flex flex-col gap-2">
                    {categories.map((category) => (
                        <CategoryRow key={category.id} category={category} chartAccounts={chartAccounts} />
                    ))}
                </div>
            </motion.div>
        </AdminLayout>
    );
}
