import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

function Row({ item, routeName, extraField, index }) {
    const [busy, setBusy] = useState(false);

    function handleSave(e) {
        e.preventDefault();
        const form = new FormData(e.target);
        const data = {
            name: form.get('name'),
            is_active: form.get('is_active') === 'on',
        };
        if (extraField) data[extraField.key] = form.get(extraField.key);

        setBusy(true);
        router.patch(route(`${routeName}.update`, item.id), data, {
            preserveScroll: true,
            onFinish: () => setBusy(false),
        });
    }

    function handleDelete() {
        if (!confirm('Supprimer cet élément ?')) return;
        router.delete(route(`${routeName}.destroy`, item.id), { preserveScroll: true });
    }

    return (
        <motion.form
            layout
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ duration: 0.2, delay: index * 0.02 }}
            onSubmit={handleSave}
            className="flex flex-wrap items-center gap-3 border-b border-terroir-dark/5 py-3 last:border-0"
        >
            <input name="name" defaultValue={item.name} required className="input flex-1 min-w-[140px]" />
            {extraField && (
                <input
                    name={extraField.key}
                    defaultValue={item[extraField.key] ?? ''}
                    placeholder={extraField.label}
                    className="input w-32"
                />
            )}
            <label className="flex items-center gap-1.5 text-sm text-terroir-dark/70">
                <input type="checkbox" name="is_active" defaultChecked={item.is_active} className="rounded border-terroir-dark/20" />
                Actif
            </label>
            <button type="submit" disabled={busy} className="btn-outline !px-4 !py-1.5 text-xs disabled:opacity-50">
                Enregistrer
            </button>
            <button type="button" onClick={handleDelete} className="admin-link-danger text-xs">
                Supprimer
            </button>
        </motion.form>
    );
}

export default function ReferenceList({ title, subtitle, routeName, extraField, items }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        ...(extraField ? { [extraField.key]: '' } : {}),
    });

    function handleCreate(e) {
        e.preventDefault();
        post(route(`${routeName}.store`), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    }

    return (
        <AdminLayout title={title}>
            <Head title={`${title} — Administration`} />

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="admin-card max-w-2xl"
            >
                {subtitle && <p className="mb-4 text-sm text-terroir-dark/60">{subtitle}</p>}

                <form onSubmit={handleCreate} className="mb-6 flex flex-wrap items-end gap-3 border-b border-terroir-dark/10 pb-6">
                    <div className="min-w-[160px] flex-1">
                        <label className="label">Nom</label>
                        <input value={data.name} onChange={(e) => setData('name', e.target.value)} required className="input" />
                        {errors.name && <p className="mt-1 text-xs text-terroir-terracotta">{errors.name}</p>}
                    </div>
                    {extraField && (
                        <div className="w-32">
                            <label className="label">{extraField.label}</label>
                            <input
                                value={data[extraField.key]}
                                onChange={(e) => setData(extraField.key, e.target.value)}
                                className="input"
                            />
                            {errors[extraField.key] && <p className="mt-1 text-xs text-terroir-terracotta">{errors[extraField.key]}</p>}
                        </div>
                    )}
                    <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">
                        Ajouter
                    </button>
                </form>

                <div>
                    {items.length === 0 ? (
                        <p className="py-8 text-center text-terroir-dark/40">Aucun élément.</p>
                    ) : (
                        items.map((item, i) => (
                            <Row key={item.id} item={item} routeName={routeName} extraField={extraField} index={i} />
                        ))
                    )}
                </div>
            </motion.div>
        </AdminLayout>
    );
}
