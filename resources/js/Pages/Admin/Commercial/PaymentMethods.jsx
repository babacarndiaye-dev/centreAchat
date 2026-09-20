import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

function Row({ method, index }) {
    const [busy, setBusy] = useState(false);

    function handleSave(e) {
        e.preventDefault();
        const form = new FormData(e.target);
        setBusy(true);
        router.patch(route('admin.commercial.paiements.update', method.id), {
            name: form.get('name'),
            available_online: form.get('available_online') === 'on',
            available_pos: form.get('available_pos') === 'on',
            requires_b2b: form.get('requires_b2b') === 'on',
            is_active: form.get('is_active') === 'on',
        }, {
            preserveScroll: true,
            onFinish: () => setBusy(false),
        });
    }

    function handleDelete() {
        if (!confirm(`Supprimer ${method.name} ?`)) return;
        router.delete(route('admin.commercial.paiements.destroy', method.id), { preserveScroll: true });
    }

    return (
        <motion.div
            layout
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ duration: 0.2, delay: index * 0.02 }}
            className="flex flex-wrap items-center gap-3 rounded-lg bg-terroir-cream px-4 py-3"
        >
            <form onSubmit={handleSave} className="flex flex-1 flex-wrap items-center gap-3">
                <span className="w-28 font-mono text-sm text-terroir-dark/50">{method.code}</span>
                <input name="name" defaultValue={method.name} className="input min-w-[160px] flex-1" />
                <label className="flex items-center gap-1 text-sm"><input type="checkbox" name="available_online" defaultChecked={method.available_online} className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20" /> Site web</label>
                <label className="flex items-center gap-1 text-sm"><input type="checkbox" name="available_pos" defaultChecked={method.available_pos} className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20" /> Caisse (POS)</label>
                <label className="flex items-center gap-1 text-sm"><input type="checkbox" name="requires_b2b" defaultChecked={method.requires_b2b} className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20" /> Pro validé uniquement</label>
                <label className="flex items-center gap-1 text-sm"><input type="checkbox" name="is_active" defaultChecked={method.is_active} className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20" /> Actif</label>
                <button type="submit" disabled={busy} className="text-sm font-semibold text-terroir-green disabled:opacity-50">Enregistrer</button>
            </form>
            <button type="button" onClick={handleDelete} className="admin-link-danger bg-transparent">Supprimer</button>
        </motion.div>
    );
}

export default function PaymentMethods({ paymentMethods }) {
    const { data, setData, post, processing, reset } = useForm({ code: '', name: '' });

    function handleCreate(e) {
        e.preventDefault();
        post(route('admin.commercial.paiements.store'), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    }

    return (
        <AdminLayout title="Modes de paiement">
            <Head title="Modes de paiement — Administration" />

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="admin-card max-w-3xl"
            >
                <h2 className="font-display text-lg font-semibold">Nouveau mode de paiement</h2>
                <form onSubmit={handleCreate} className="mt-3 flex gap-2">
                    <input
                        value={data.code}
                        onChange={(e) => setData('code', e.target.value)}
                        placeholder="Code (ex : wave)"
                        required
                        className="input w-40"
                    />
                    <input
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        placeholder="Nom affiché (ex : Wave)"
                        required
                        className="input flex-1"
                    />
                    <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Ajouter</button>
                </form>

                <div className="mt-8 flex flex-col gap-2">
                    {paymentMethods.length === 0 ? (
                        <p className="py-8 text-center text-terroir-dark/40">Aucun mode de paiement.</p>
                    ) : (
                        paymentMethods.map((method, i) => <Row key={method.id} method={method} index={i} />)
                    )}
                </div>
            </motion.div>
        </AdminLayout>
    );
}
