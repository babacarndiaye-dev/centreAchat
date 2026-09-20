import { Head, useForm, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Journals({ journals, types }) {
    const { data, setData, post, processing, errors, reset } = useForm({ code: '', name: '', type: Object.keys(types)[0] ?? '' });

    function handleCreate(e) {
        e.preventDefault();
        post(route('admin.comptabilite.journaux.store'), { preserveScroll: true, onSuccess: () => reset('code', 'name') });
    }

    function handleDelete(journal) {
        if (!confirm('Supprimer ce journal ?')) return;
        router.delete(route('admin.comptabilite.journaux.destroy', journal.id), { preserveScroll: true });
    }

    return (
        <AdminLayout title="Journaux comptables">
            <Head title="Journaux comptables — Administration" />

            <motion.div initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.35 }} className="admin-card max-w-2xl">
                <h2 className="font-display text-lg font-semibold">Nouveau journal</h2>
                <form onSubmit={handleCreate} className="mt-3 flex flex-wrap gap-3">
                    <input value={data.code} onChange={(e) => setData('code', e.target.value)} placeholder="Code" required className="input w-28" />
                    <input
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        placeholder="Intitulé"
                        required
                        className="input min-w-[180px] flex-1"
                    />
                    <select value={data.type} onChange={(e) => setData('type', e.target.value)} required className="input w-40">
                        {Object.entries(types).map(([value, label]) => <option key={value} value={value}>{label}</option>)}
                    </select>
                    <button type="submit" disabled={processing} className="btn-primary w-full justify-center disabled:opacity-50">Ajouter</button>
                    {errors.code && <p className="w-full text-xs text-terroir-terracotta">{errors.code}</p>}
                </form>

                <div className="mt-4 flex flex-col gap-2">
                    {journals.map((journal) => (
                        <div key={journal.id} className="flex items-center justify-between rounded-lg bg-terroir-cream px-4 py-3">
                            <div>
                                <span className="font-mono font-semibold">{journal.code}</span>
                                <span className="ml-2">{journal.name}</span>
                                <span className="ml-2 text-sm text-terroir-dark/50">({journal.type_label})</span>
                            </div>
                            <button type="button" onClick={() => handleDelete(journal)} className="admin-link-danger bg-transparent">Supprimer</button>
                        </div>
                    ))}
                </div>
            </motion.div>
        </AdminLayout>
    );
}
