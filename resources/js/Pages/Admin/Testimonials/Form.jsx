import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Form({ testimonial }) {
    const isEdit = !!testimonial;
    const { data, setData, post, put, processing, errors } = useForm({
        author_name: testimonial?.author_name ?? '',
        author_role: testimonial?.author_role ?? '',
        content: testimonial?.content ?? '',
        rating: testimonial?.rating ?? 5,
        is_published: testimonial?.is_published ?? true,
    });

    function handleSubmit(e) {
        e.preventDefault();
        if (isEdit) {
            put(route('admin.avis.update', testimonial.id), { preserveScroll: true });
        } else {
            post(route('admin.avis.store'), { preserveScroll: true });
        }
    }

    return (
        <AdminLayout title={isEdit ? "Modifier l'avis" : 'Nouvel avis'}>
            <Head title={`${isEdit ? "Modifier l'avis" : 'Nouvel avis'} — Administration`} />

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="admin-card max-w-[48rem]"
            >
                <form onSubmit={handleSubmit}>
                    <div className="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Nom de l'auteur</label>
                            <input value={data.author_name} onChange={(e) => setData('author_name', e.target.value)} required className="input" />
                            {errors.author_name && <p className="mt-1 text-xs text-terroir-terracotta">{errors.author_name}</p>}
                        </div>
                        <div>
                            <label className="label">Rôle / structure</label>
                            <input
                                value={data.author_role}
                                onChange={(e) => setData('author_role', e.target.value)}
                                placeholder="Ex : Hôtel Teranga"
                                className="input"
                            />
                        </div>
                    </div>

                    <div className="mt-6">
                        <label className="label">Témoignage</label>
                        <textarea rows={4} value={data.content} onChange={(e) => setData('content', e.target.value)} required className="input" />
                        {errors.content && <p className="mt-1 text-xs text-terroir-terracotta">{errors.content}</p>}
                    </div>

                    <div className="mt-6 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Note (1 à 5)</label>
                            <input
                                type="number"
                                min={1}
                                max={5}
                                value={data.rating}
                                onChange={(e) => setData('rating', e.target.value)}
                                required
                                className="input"
                            />
                        </div>
                        <div className="flex items-end">
                            <label className="flex items-center gap-2 text-sm">
                                <input
                                    type="checkbox"
                                    checked={data.is_published}
                                    onChange={(e) => setData('is_published', e.target.checked)}
                                    className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"
                                />
                                Publié sur le site
                            </label>
                        </div>
                    </div>

                    <div className="mt-6 flex gap-3">
                        <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Enregistrer</button>
                        <Link href={route('admin.avis.index')} className="btn-outline">Annuler</Link>
                    </div>
                </form>
            </motion.div>
        </AdminLayout>
    );
}
