import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import SiteLayout from '../../Layouts/SiteLayout';
import Reveal from '../../Components/Reveal';

export default function ProducersIndex({ producers }) {
    return (
        <SiteLayout>
            <Head title="Nos producteurs — Central d'Achat" />

            <section className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <Reveal className="text-center">
                    <span className="section-eyebrow">Rencontrez</span>
                    <h1 className="section-title mt-2">Nos producteurs partenaires</h1>
                    <p className="mx-auto mt-3 max-w-xl text-terroir-dark/70">Des hommes et des femmes passionnés, garants de la qualité et de l'authenticité de nos produits.</p>
                </Reveal>

                <div className="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    {producers.data.map((producer, i) => (
                        <Reveal key={producer.slug} delay={Math.min(i, 6) * 0.06} y={20}>
                            <motion.a
                                whileHover={{ y: -4 }}
                                href={route('producteurs.show', producer.slug)}
                                className="card group block overflow-hidden"
                            >
                                <div className="flex h-48 items-center justify-center overflow-hidden bg-terroir-cream text-6xl">
                                    {producer.photo ? (
                                        <img
                                            src={`/fichiers/${producer.photo}`}
                                            alt={producer.name}
                                            className="h-full w-full object-cover transition duration-700 group-hover:scale-105"
                                        />
                                    ) : (
                                        <span className="material-symbols-outlined text-6xl text-terroir-green">agriculture</span>
                                    )}
                                </div>
                                <div className="p-6">
                                    <h3 className="font-display text-lg font-semibold">{producer.name}</h3>
                                    {producer.region && (
                                        <p className="mt-1 flex items-center gap-1 text-sm text-terroir-dark/60">
                                            <span className="material-symbols-outlined text-base">location_on</span> {producer.region}
                                        </p>
                                    )}
                                    {producer.description && <p className="mt-3 line-clamp-2 text-sm text-terroir-dark/70">{producer.description}</p>}
                                    <p className="mt-3 text-xs text-terroir-dark/50">
                                        {producer.products_count} produit{producer.products_count > 1 ? 's' : ''}
                                    </p>
                                    <span className="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-terroir-green">
                                        Voir les produits
                                        <span className="material-symbols-outlined text-sm transition group-hover:translate-x-0.5">arrow_forward</span>
                                    </span>
                                </div>
                            </motion.a>
                        </Reveal>
                    ))}
                </div>

                {producers.links.length > 3 && (
                    <div className="mt-12 flex flex-wrap justify-center gap-2">
                        {producers.links.map((link, i) =>
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
            </section>
        </SiteLayout>
    );
}
