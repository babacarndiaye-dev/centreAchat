import { Head, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../../Layouts/AdminLayout';

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({ opening_float: 0, notes: '' });

    function handleSubmit(e) {
        e.preventDefault();
        post(route('admin.pos.caisse.store'));
    }

    return (
        <AdminLayout title="Ouvrir la caisse">
            <Head title="Ouvrir la caisse — Administration" />

            <motion.div initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.35 }} className="admin-card max-w-md">
                <h2 className="font-display text-lg font-semibold">Ouverture de caisse</h2>
                <p className="mt-1.5 text-sm text-terroir-dark/50">Indiquez le fond de caisse initial pour démarrer une nouvelle session.</p>

                <form onSubmit={handleSubmit} className="mt-4 flex flex-col gap-4">
                    <div>
                        <label className="label">Fond de caisse (FCFA)</label>
                        <input
                            type="number"
                            step="0.01"
                            value={data.opening_float}
                            onChange={(e) => setData('opening_float', e.target.value)}
                            required
                            className="input"
                        />
                        {errors.opening_float && <p className="mt-1 text-xs text-terroir-terracotta">{errors.opening_float}</p>}
                    </div>
                    <div>
                        <label className="label">Notes (optionnel)</label>
                        <textarea rows={2} value={data.notes} onChange={(e) => setData('notes', e.target.value)} className="input" />
                    </div>
                    <button type="submit" disabled={processing} className="btn-primary w-full justify-center disabled:opacity-50">Ouvrir la caisse</button>
                </form>
            </motion.div>
        </AdminLayout>
    );
}
