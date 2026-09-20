import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Form({ supplier, statuses }) {
    const isEdit = !!supplier;
    const { data, setData, post, put, processing, errors } = useForm({
        name: supplier?.name ?? '',
        company_name: supplier?.company_name ?? '',
        contact_name: supplier?.contact_name ?? '',
        status: supplier?.status ?? 'en_attente',
        phone: supplier?.phone ?? '',
        email: supplier?.email ?? '',
        address: supplier?.address ?? '',
        city: supplier?.city ?? '',
        region: supplier?.region ?? '',
        payment_terms: supplier?.payment_terms ?? '',
        delivery_delay_days: supplier?.delivery_delay_days ?? '',
        rating: supplier?.rating ?? '',
        notes: supplier?.notes ?? '',
    });

    function handleSubmit(e) {
        e.preventDefault();
        if (isEdit) {
            put(route('admin.fournisseurs.update', supplier.id), { preserveScroll: true });
        } else {
            post(route('admin.fournisseurs.store'), { preserveScroll: true });
        }
    }

    return (
        <AdminLayout title={isEdit ? 'Modifier le fournisseur' : 'Nouveau fournisseur'}>
            <Head title={`${isEdit ? 'Modifier le fournisseur' : 'Nouveau fournisseur'} — Administration`} />

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="admin-card max-w-[48rem]"
            >
                <form onSubmit={handleSubmit}>
                    <div className="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Nom</label>
                            <input value={data.name} onChange={(e) => setData('name', e.target.value)} required className="input" />
                            {errors.name && <p className="mt-1 text-xs text-terroir-terracotta">{errors.name}</p>}
                        </div>
                        <div>
                            <label className="label">Raison sociale</label>
                            <input value={data.company_name} onChange={(e) => setData('company_name', e.target.value)} className="input" />
                        </div>
                    </div>

                    <div className="mt-6 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Responsable</label>
                            <input value={data.contact_name} onChange={(e) => setData('contact_name', e.target.value)} className="input" />
                        </div>
                        <div>
                            <label className="label">Statut</label>
                            <select value={data.status} onChange={(e) => setData('status', e.target.value)} required className="input">
                                {Object.entries(statuses).map(([value, label]) => <option key={value} value={value}>{label}</option>)}
                            </select>
                        </div>
                    </div>

                    <div className="mt-6 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Téléphone</label>
                            <input value={data.phone} onChange={(e) => setData('phone', e.target.value)} required className="input" />
                            {errors.phone && <p className="mt-1 text-xs text-terroir-terracotta">{errors.phone}</p>}
                        </div>
                        <div>
                            <label className="label">E-mail</label>
                            <input type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} className="input" />
                        </div>
                    </div>

                    <div className="mt-6">
                        <label className="label">Adresse</label>
                        <textarea rows={2} value={data.address} onChange={(e) => setData('address', e.target.value)} className="input" />
                    </div>

                    <div className="mt-6 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Ville</label>
                            <input value={data.city} onChange={(e) => setData('city', e.target.value)} className="input" />
                        </div>
                        <div>
                            <label className="label">Région</label>
                            <input value={data.region} onChange={(e) => setData('region', e.target.value)} className="input" />
                        </div>
                    </div>

                    <div className="mt-6 grid gap-4 sm:grid-cols-3">
                        <div>
                            <label className="label">Conditions de paiement</label>
                            <input
                                value={data.payment_terms}
                                onChange={(e) => setData('payment_terms', e.target.value)}
                                placeholder="Ex : 30 jours"
                                className="input"
                            />
                        </div>
                        <div>
                            <label className="label">Délai de livraison (jours)</label>
                            <input
                                type="number"
                                value={data.delivery_delay_days}
                                onChange={(e) => setData('delivery_delay_days', e.target.value)}
                                className="input"
                            />
                        </div>
                        <div>
                            <label className="label">Note (0 à 5)</label>
                            <input
                                type="number"
                                step="0.1"
                                min="0"
                                max="5"
                                value={data.rating}
                                onChange={(e) => setData('rating', e.target.value)}
                                className="input"
                            />
                        </div>
                    </div>

                    <div className="mt-6">
                        <label className="label">Notes internes</label>
                        <textarea rows={3} value={data.notes} onChange={(e) => setData('notes', e.target.value)} className="input" />
                    </div>

                    <div className="mt-6 flex gap-3">
                        <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Enregistrer</button>
                        <Link href={route('admin.fournisseurs.index')} className="btn-outline">Annuler</Link>
                    </div>
                </form>
            </motion.div>
        </AdminLayout>
    );
}
