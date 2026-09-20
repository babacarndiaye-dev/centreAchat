import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Form({ page }) {
    const isEdit = !!page;
    const { data, setData, post, put, processing, errors } = useForm({
        title: page?.title ?? '',
        slug: page?.slug ?? '',
        content: page?.content ?? '',
        meta_title: page?.meta_title ?? '',
        meta_description: page?.meta_description ?? '',
        is_published: page?.is_published ?? true,
    });

    function handleSubmit(e) {
        e.preventDefault();
        if (isEdit) {
            put(route('admin.pages.update', page.id), { preserveScroll: true });
        } else {
            post(route('admin.pages.store'), { preserveScroll: true });
        }
    }

    return (
        <AdminLayout title={isEdit ? 'Modifier la page' : 'Nouvelle page'}>
            <Head title={`${isEdit ? 'Modifier la page' : 'Nouvelle page'} — Administration`} />

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="admin-card max-w-[64rem]"
            >
                <form onSubmit={handleSubmit}>
                    <div className="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Titre</label>
                            <input value={data.title} onChange={(e) => setData('title', e.target.value)} required className="input" />
                            {errors.title && <p className="mt-1 text-xs text-terroir-terracotta">{errors.title}</p>}
                        </div>
                        <div>
                            <label className="label">Slug (URL)</label>
                            <input
                                value={data.slug}
                                onChange={(e) => setData('slug', e.target.value)}
                                placeholder="genere-automatiquement-si-vide"
                                className="input"
                            />
                            {errors.slug && <p className="mt-1 text-xs text-terroir-terracotta">{errors.slug}</p>}
                        </div>
                    </div>

                    <div className="mt-6">
                        <label className="label">Contenu</label>
                        <textarea rows={10} value={data.content} onChange={(e) => setData('content', e.target.value)} className="input" />
                    </div>

                    <div className="mt-6 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Titre SEO (optionnel)</label>
                            <input value={data.meta_title} onChange={(e) => setData('meta_title', e.target.value)} className="input" />
                        </div>
                        <div>
                            <label className="label">Meta description (optionnel)</label>
                            <input value={data.meta_description} onChange={(e) => setData('meta_description', e.target.value)} className="input" />
                        </div>
                    </div>

                    <div className="mt-6">
                        <label className="flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                checked={data.is_published}
                                onChange={(e) => setData('is_published', e.target.checked)}
                                className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"
                            />
                            Page publiée
                        </label>
                    </div>

                    <div className="mt-6 flex gap-3">
                        <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Enregistrer</button>
                        <Link href={route('admin.pages.index')} className="btn-outline">Annuler</Link>
                    </div>
                </form>
            </motion.div>
        </AdminLayout>
    );
}
