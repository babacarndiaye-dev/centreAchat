import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

function Row({ zone, index }) {
    const [busy, setBusy] = useState(false);

    function handleSave(e) {
        e.preventDefault();
        const form = new FormData(e.target);
        setBusy(true);
        router.patch(route('admin.commercial.zones.update', zone.id), {
            name: form.get('name'),
            cities: form.get('cities'),
            fee: form.get('fee'),
            free_above: form.get('free_above'),
            delay_days: form.get('delay_days'),
            is_active: form.get('is_active') === 'on',
        }, {
            preserveScroll: true,
            onFinish: () => setBusy(false),
        });
    }

    function handleDelete() {
        if (!confirm('Supprimer cette zone ?')) return;
        router.delete(route('admin.commercial.zones.destroy', zone.id), { preserveScroll: true });
    }

    return (
        <motion.div
            layout
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ duration: 0.2, delay: index * 0.02 }}
            className="flex flex-wrap items-center gap-3 rounded-lg bg-terroir-cream px-4 py-3"
        >
            <form onSubmit={handleSave} className="flex flex-1 flex-wrap items-center gap-2">
                <input name="name" defaultValue={zone.name} className="input min-w-[140px] flex-1" />
                <input name="cities" defaultValue={zone.cities ?? ''} placeholder="Villes" className="input w-40" />
                <input name="fee" type="number" step="0.01" defaultValue={zone.fee} className="input w-28" />
                <input name="free_above" type="number" step="0.01" defaultValue={zone.free_above ?? ''} placeholder="Gratuit dès" className="input w-32" />
                <input name="delay_days" type="number" defaultValue={zone.delay_days ?? ''} placeholder="Jours" className="input w-20" />
                <label className="flex items-center gap-1 whitespace-nowrap text-sm">
                    <input type="checkbox" name="is_active" defaultChecked={zone.is_active} className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20" />
                    Actif
                </label>
                <button type="submit" disabled={busy} className="text-sm font-semibold text-terroir-green disabled:opacity-50">Enregistrer</button>
            </form>
            <button type="button" onClick={handleDelete} className="admin-link-danger bg-transparent">Supprimer</button>
        </motion.div>
    );
}

export default function DeliveryZones({ deliveryZones }) {
    const { data, setData, post, processing, reset } = useForm({ name: '', cities: '', fee: '', delay_days: '' });

    function handleCreate(e) {
        e.preventDefault();
        post(route('admin.commercial.zones.store'), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    }

    return (
        <AdminLayout title="Zones de livraison">
            <Head title="Zones de livraison — Administration" />

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="admin-card max-w-3xl"
            >
                <h2 className="font-display text-lg font-semibold">Nouvelle zone de livraison</h2>
                <form onSubmit={handleCreate} className="mt-3 flex flex-wrap gap-3">
                    <input
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        placeholder="Nom (ex : Mbour centre)"
                        required
                        className="input min-w-[200px] flex-1"
                    />
                    <input
                        value={data.cities}
                        onChange={(e) => setData('cities', e.target.value)}
                        placeholder="Villes couvertes"
                        className="input w-40"
                    />
                    <input
                        type="number"
                        step="0.01"
                        value={data.fee}
                        onChange={(e) => setData('fee', e.target.value)}
                        placeholder="Tarif FCFA"
                        required
                        className="input w-32"
                    />
                    <input
                        type="number"
                        value={data.delay_days}
                        onChange={(e) => setData('delay_days', e.target.value)}
                        placeholder="Délai (jours)"
                        className="input w-32"
                    />
                    <button type="submit" disabled={processing} className="btn-primary w-full justify-center disabled:opacity-50">Ajouter</button>
                </form>

                <div className="mt-8 flex flex-col gap-2">
                    {deliveryZones.length === 0 ? (
                        <p className="py-8 text-center text-terroir-dark/40">Aucune zone de livraison.</p>
                    ) : (
                        deliveryZones.map((zone, i) => <Row key={zone.id} zone={zone} index={i} />)
                    )}
                </div>
            </motion.div>
        </AdminLayout>
    );
}
