import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Show({ message }) {
    const { data, setData, post, processing, reset, errors } = useForm({ reply: '' });

    function handleSubmit(e) {
        e.preventDefault();
        post(route('admin.messages.reply', message.id), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    }

    return (
        <AdminLayout title={`Message de ${message.name}`}>
            <Head title={`Message de ${message.name} — Administration`} />

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="admin-card max-w-3xl"
            >
                <dl className="space-y-2 text-sm">
                    <div><dt className="inline text-terroir-dark/50">Nom : </dt><dd className="inline font-semibold text-terroir-dark">{message.name}</dd></div>
                    <div><dt className="inline text-terroir-dark/50">E-mail : </dt><dd className="inline font-semibold text-terroir-dark">{message.email}</dd></div>
                    {message.phone && (
                        <div><dt className="inline text-terroir-dark/50">Téléphone : </dt><dd className="inline font-semibold text-terroir-dark">{message.phone}</dd></div>
                    )}
                    {message.subject && (
                        <div><dt className="inline text-terroir-dark/50">Sujet : </dt><dd className="inline font-semibold text-terroir-dark">{message.subject}</dd></div>
                    )}
                    <div><dt className="inline text-terroir-dark/50">Date : </dt><dd className="inline font-semibold text-terroir-dark">{message.created_at}</dd></div>
                </dl>

                <div className="mt-4 rounded-lg bg-terroir-cream/60 p-4 text-sm leading-relaxed text-terroir-dark/80">
                    {message.message}
                </div>

                {message.reply && (
                    <div className="mt-6 border-t border-terroir-dark/10 pt-6">
                        <p className="text-xs font-semibold uppercase tracking-wide text-terroir-green">
                            Réponse envoyée
                            {message.replied_at ? ` le ${message.replied_at}` : ''}
                            {message.replied_by_name ? ` par ${message.replied_by_name}` : ''}
                        </p>
                        <div className="mt-2 rounded-lg bg-terroir-green/5 p-4 text-sm leading-relaxed text-terroir-dark/80">{message.reply}</div>
                    </div>
                )}

                <form onSubmit={handleSubmit} className="mt-6 border-t border-terroir-dark/10 pt-6">
                    <label className="label">{message.reply ? 'Envoyer une nouvelle réponse' : 'Répondre'}</label>
                    <textarea
                        rows={5}
                        value={data.reply}
                        onChange={(e) => setData('reply', e.target.value)}
                        required
                        placeholder="Votre réponse..."
                        className="input"
                    />
                    {errors.reply && <p className="mt-1 text-xs text-terroir-terracotta">{errors.reply}</p>}
                    <div className="mt-3 flex gap-3">
                        <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">
                            <span className="material-symbols-outlined text-lg">send</span>
                            Envoyer la réponse
                        </button>
                        <Link href={route('admin.messages.index')} className="btn-outline">Retour</Link>
                    </div>
                </form>
            </motion.div>
        </AdminLayout>
    );
}
