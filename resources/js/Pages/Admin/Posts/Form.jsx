import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

const TYPES = [
    { value: 'actualite', label: 'Actualité' },
    { value: 'recette', label: 'Recette' },
    { value: 'blog', label: 'Blog' },
];

export default function Form({ post }) {
    const isEdit = !!post;
    const { data, setData, post: submitPost, processing, errors } = useForm({
        title: post?.title ?? '',
        type: post?.type ?? 'actualite',
        excerpt: post?.excerpt ?? '',
        content: post?.content ?? '',
        cover_image: null,
        is_published: post?.is_published ?? true,
        ...(isEdit ? { _method: 'put' } : {}),
    });

    function handleSubmit(e) {
        e.preventDefault();
        const url = isEdit ? route('admin.articles.update', post.id) : route('admin.articles.store');
        submitPost(url, { forceFormData: true, preserveScroll: true });
    }

    return (
        <AdminLayout title={isEdit ? "Modifier l'article" : 'Nouvel article'}>
            <Head title={`${isEdit ? "Modifier l'article" : 'Nouvel article'} — Administration`} />

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
                            <label className="label">Type</label>
                            <select value={data.type} onChange={(e) => setData('type', e.target.value)} required className="input">
                                {TYPES.map(({ value, label }) => <option key={value} value={value}>{label}</option>)}
                            </select>
                        </div>
                    </div>

                    <div className="mt-6">
                        <label className="label">Résumé</label>
                        <input value={data.excerpt} onChange={(e) => setData('excerpt', e.target.value)} className="input" />
                    </div>

                    <div className="mt-6">
                        <label className="label">Contenu</label>
                        <textarea rows={10} value={data.content} onChange={(e) => setData('content', e.target.value)} className="input" />
                    </div>

                    <div className="mt-6">
                        <label className="label">Image de couverture</label>
                        <input
                            type="file"
                            accept="image/*"
                            onChange={(e) => setData('cover_image', e.target.files[0] ?? null)}
                            className="input file:mr-3 file:rounded-lg file:border-0 file:bg-terroir-green file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-white"
                        />
                        {errors.cover_image && <p className="mt-1 text-xs text-terroir-terracotta">{errors.cover_image}</p>}
                        {post?.cover_image_url && (
                            <img src={post.cover_image_url} alt="" className="mt-3 h-24 w-40 rounded-lg object-cover" />
                        )}
                    </div>

                    <div className="mt-6">
                        <label className="flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                checked={data.is_published}
                                onChange={(e) => setData('is_published', e.target.checked)}
                                className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"
                            />
                            Publié
                        </label>
                    </div>

                    <div className="mt-6 flex gap-3">
                        <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Enregistrer</button>
                        <Link href={route('admin.articles.index')} className="btn-outline">Annuler</Link>
                    </div>
                </form>
            </motion.div>
        </AdminLayout>
    );
}
