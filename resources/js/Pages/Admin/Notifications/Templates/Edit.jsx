import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../../Layouts/AdminLayout';

export default function Edit({ template, placeholders }) {
    const { data, setData, patch, processing, errors } = useForm({
        subject: template.subject ?? '',
        body: template.body ?? '',
        is_active: template.is_active,
    });

    function handleSubmit(e) {
        e.preventDefault();
        patch(route('admin.notifications.templates.update', template.id), { preserveScroll: true });
    }

    return (
        <AdminLayout title="Modifier le modèle">
            <Head title="Modifier le modèle — Administration" />

            <motion.form
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                onSubmit={handleSubmit}
                className="admin-card max-w-2xl"
            >
                <div className="text-sm text-terroir-dark/50">
                    {template.event_label} — {template.channel_label}
                </div>

                {template.channel === 'email' && (
                    <div className="mt-4">
                        <label className="label">Objet</label>
                        <input value={data.subject} onChange={(e) => setData('subject', e.target.value)} className="input" />
                    </div>
                )}

                <div className="mt-4">
                    <label className="label">Message</label>
                    <textarea rows={6} value={data.body} onChange={(e) => setData('body', e.target.value)} required className="input" />
                    {errors.body && <p className="mt-1 text-xs text-terroir-terracotta">{errors.body}</p>}
                </div>

                {placeholders.length > 0 && (
                    <div className="mt-4 rounded-lg bg-terroir-cream px-4 py-3 text-sm text-terroir-dark/50">
                        Variables disponibles :{' '}
                        {placeholders.map((placeholder) => (
                            <code key={placeholder} className="mx-0.5 rounded bg-white px-1.5 py-0.5">{`{{${placeholder}}}`}</code>
                        ))}
                    </div>
                )}

                <label className="mt-4 flex items-center gap-2 text-sm">
                    <input
                        type="checkbox"
                        checked={data.is_active}
                        onChange={(e) => setData('is_active', e.target.checked)}
                        className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"
                    />
                    Actif
                    {['sms', 'whatsapp'].includes(template.channel) && (
                        <span className="text-xs text-terroir-dark/40">
                            (nécessite une passerelle {template.channel === 'sms' ? 'SMS' : 'WhatsApp'} configurée)
                        </span>
                    )}
                </label>

                <div className="mt-6">
                    <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Enregistrer</button>
                    <Link href={route('admin.notifications.templates.index')} className="ml-3 text-sm font-semibold text-terroir-dark/60">Annuler</Link>
                </div>
            </motion.form>
        </AdminLayout>
    );
}
