import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import SiteLayout from '../../Layouts/SiteLayout';
import Reveal from '../../Components/Reveal';

const TYPES = [
    { value: '', label: 'Tout' },
    { value: 'actualite', label: 'Actualités' },
    { value: 'recette', label: 'Recettes' },
    { value: 'blog', label: 'Blog' },
];

export default function BlogIndex({ posts, currentType }) {
    return (
        <SiteLayout>
            <Head title="Actualités & recettes — Central d'Achat" />

            <section className="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
                <Reveal className="text-center">
                    <span className="section-eyebrow">Le journal</span>
                    <h1 className="section-title mt-2">Actualités, recettes &amp; blog</h1>
                </Reveal>

                <div className="mt-8 flex flex-wrap justify-center gap-3">
                    {TYPES.map((t) => (
                        <a
                            key={t.value}
                            href={route('blog.index', t.value ? { type: t.value } : {})}
                            className={
                                'rounded-full px-5 py-2 text-sm font-medium transition ' +
                                (currentType === t.value ? 'bg-terroir-green text-white' : 'bg-white text-terroir-dark/70 hover:bg-terroir-cream')
                            }
                        >
                            {t.label}
                        </a>
                    ))}
                </div>

                {posts.data.length === 0 ? (
                    <p className="mt-16 text-center text-terroir-dark/60">Aucun article publié pour le moment.</p>
                ) : (
                    <>
                        <div className="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                            {posts.data.map((post, i) => (
                                <Reveal key={post.slug} delay={Math.min(i, 6) * 0.06} y={20}>
                                    <motion.a whileHover={{ y: -4 }} href={route('blog.show', post.slug)} className="card group block overflow-hidden">
                                        <div className="flex h-48 items-center justify-center overflow-hidden bg-terroir-cream text-5xl">
                                            {post.cover_image ? (
                                                <img
                                                    src={`/fichiers/${post.cover_image}`}
                                                    alt={post.title}
                                                    className="h-full w-full object-cover transition duration-700 group-hover:scale-105"
                                                />
                                            ) : (
                                                '📰'
                                            )}
                                        </div>
                                        <div className="p-6">
                                            <span className="text-xs font-semibold uppercase tracking-wide text-terroir-terracotta">{post.type.charAt(0).toUpperCase() + post.type.slice(1)}</span>
                                            <h3 className="mt-2 line-clamp-2 font-display text-lg font-semibold">{post.title}</h3>
                                            <p className="mt-2 line-clamp-2 text-sm text-terroir-dark/60">{post.excerpt}</p>
                                            <p className="mt-4 text-xs text-terroir-dark/40">{post.published_at}</p>
                                        </div>
                                    </motion.a>
                                </Reveal>
                            ))}
                        </div>

                        {posts.links.length > 3 && (
                            <div className="mt-12 flex flex-wrap justify-center gap-2">
                                {posts.links.map((link, i) =>
                                    link.url ? (
                                        <Link
                                            key={i}
                                            href={link.url}
                                            preserveScroll
                                            className={
                                                'rounded-full px-4 py-2 text-sm font-medium ' +
                                                (link.active ? 'bg-terroir-green text-white' : 'bg-terroir-cream text-terroir-dark hover:bg-terroir-cream/70')
                                            }
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    ) : (
                                        <span key={i} className="rounded-full px-4 py-2 text-sm font-medium text-terroir-dark/30" dangerouslySetInnerHTML={{ __html: link.label }} />
                                    )
                                )}
                            </div>
                        )}
                    </>
                )}
            </section>
        </SiteLayout>
    );
}
