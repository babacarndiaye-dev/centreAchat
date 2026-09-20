import { Head, Link } from '@inertiajs/react';
import SiteLayout from '../../Layouts/SiteLayout';
import Reveal from '../../Components/Reveal';

export default function BlogShow({ post, related }) {
    return (
        <SiteLayout>
            <Head title={`${post.title} — Centrale d'achat`} />

            <article className="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
                <nav className="text-xs text-terroir-dark/50">
                    <Link href={route('accueil')} className="hover:text-terroir-terracotta">Accueil</Link> /{' '}
                    <Link href={route('blog.index')} className="hover:text-terroir-terracotta">Actualités</Link> /{' '}
                    <span className="text-terroir-dark">{post.title}</span>
                </nav>

                <Reveal>
                    <span className="section-eyebrow mt-6 inline-block">{post.type.charAt(0).toUpperCase() + post.type.slice(1)}</span>
                    <h1 className="mt-2 font-display text-3xl font-semibold text-terroir-dark sm:text-4xl">{post.title}</h1>
                    <p className="mt-3 text-sm text-terroir-dark/50">{post.published_at}</p>

                    {post.cover_image && (
                        <div className="mt-8 aspect-video overflow-hidden rounded-xl2 shadow-soft">
                            <img src={`/fichiers/${post.cover_image}`} alt={post.title} className="h-full w-full object-cover" />
                        </div>
                    )}

                    <div className="prose prose-terroir mt-10 max-w-none whitespace-pre-line leading-relaxed text-terroir-dark/80">
                        {post.content}
                    </div>
                </Reveal>

                {related.length > 0 && (
                    <Reveal delay={0.15} className="mt-20 border-t border-terroir-green/10 pt-10">
                        <h2 className="font-display text-xl font-semibold">À lire aussi</h2>
                        <div className="mt-6 grid gap-6 sm:grid-cols-3">
                            {related.map((item) => (
                                <Link key={item.slug} href={route('blog.show', item.slug)} className="text-sm font-medium text-terroir-dark hover:text-terroir-terracotta">
                                    {item.title}
                                </Link>
                            ))}
                        </div>
                    </Reveal>
                )}
            </article>
        </SiteLayout>
    );
}
