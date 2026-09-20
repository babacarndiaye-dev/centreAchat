import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

function Row({ tax, index }) {
    const [busy, setBusy] = useState(false);

    function handleSave(e) {
        e.preventDefault();
        const form = new FormData(e.target);
        setBusy(true);
        router.patch(route('admin.commercial.taxes.update', tax.id), {
            name: form.get('name'),
            rate: form.get('rate'),
            is_active: form.get('is_active') === 'on',
        }, {
            preserveScroll: true,
            onFinish: () => setBusy(false),
        });
    }

    function handleSetDefault() {
        router.patch(route('admin.commercial.taxes.default', tax.id), {}, { preserveScroll: true });
    }

    function handleDelete() {
        if (!confirm('Supprimer ce taux ?')) return;
        router.delete(route('admin.commercial.taxes.destroy', tax.id), { preserveScroll: true });
    }

    return (
        <motion.div
            layout
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ duration: 0.2, delay: index * 0.02 }}
            className="flex items-center justify-between rounded-lg bg-terroir-cream px-4 py-2.5"
        >
            <form onSubmit={handleSave} className="flex flex-1 items-center gap-3">
                <input name="name" defaultValue={tax.name} className="input flex-1" />
                <input name="rate" type="number" step="0.01" defaultValue={tax.rate} className="input w-24" />
                <span className="text-sm">%</span>
                <label className="flex items-center gap-1 whitespace-nowrap text-sm">
                    <input type="checkbox" name="is_active" defaultChecked={tax.is_active} className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20" />
                    Actif
                </label>
                <button type="submit" disabled={busy} className="text-sm font-semibold text-terroir-green disabled:opacity-50">Enregistrer</button>
            </form>
            {tax.is_default ? (
                <span className="admin-badge-success ml-3">Par défaut</span>
            ) : (
                <button type="button" onClick={handleSetDefault} className="ml-3 whitespace-nowrap text-xs font-semibold text-terroir-dark/60">
                    Définir par défaut
                </button>
            )}
            <button type="button" onClick={handleDelete} className="admin-link-danger ml-3 bg-transparent">Supprimer</button>
        </motion.div>
    );
}

export default function TaxRates({ taxRates }) {
    const { data, setData, post, processing, reset } = useForm({ name: '', rate: '' });

    function handleCreate(e) {
        e.preventDefault();
        post(route('admin.commercial.taxes.store'), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    }

    return (
        <AdminLayout title="Taxes">
            <Head title="Taxes — Administration" />

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="admin-card max-w-2xl"
            >
                <h2 className="font-display text-lg font-semibold">Nouveau taux de taxe</h2>
                <form onSubmit={handleCreate} className="mt-3 flex gap-2">
                    <input
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        placeholder="Ex : TVA standard"
                        required
                        className="input flex-1"
                    />
                    <input
                        type="number"
                        step="0.01"
                        value={data.rate}
                        onChange={(e) => setData('rate', e.target.value)}
                        placeholder="Taux %"
                        required
                        className="input w-32"
                    />
                    <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Ajouter</button>
                </form>

                <div className="mt-8 flex flex-col gap-2">
                    {taxRates.length === 0 ? (
                        <p className="py-8 text-center text-terroir-dark/40">Aucun taux de taxe.</p>
                    ) : (
                        taxRates.map((tax, i) => <Row key={tax.id} tax={tax} index={i} />)
                    )}
                </div>
                <p className="mt-4 text-sm text-terroir-dark/50">Le taux "par défaut" est celui appliqué automatiquement au calcul des commandes.</p>
            </motion.div>
        </AdminLayout>
    );
}
