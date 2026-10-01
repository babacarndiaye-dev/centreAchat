import { Head, Link } from '@inertiajs/react';
import SiteLayout from '../../Layouts/SiteLayout';
import Reveal from '../../Components/Reveal';
import ProductCard from '../../Components/ProductCard';

export default function ProducersShow({ producer, products }) {
    return (
        <SiteLayout>
            <Head title={`${producer.name} — DIABA HOTEL Produits du Sénégal (D.H.P.S)`} />

            <section className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <nav className="text-xs text-terroir-dark/50">
                    <Link href={route('accueil')} className="hover:text-terroir-terracotta">Accueil</Link> /{' '}
                    <Link href={route('producteurs.index')} className="hover:text-terroir-terracotta">Producteurs</Link> /{' '}
                    <span className="text-terroir-dark">{producer.name}</span>
                </nav>

                <div className="mt-8 grid gap-10 lg:grid-cols-3">
                    <Reveal className="lg:col-span-1">
                        <div className="aspect-square overflow-hidden rounded-xl2 bg-terroir-cream shadow-soft">
                            {producer.photo ? (
                                <img src={`/fichiers/${producer.photo}`} alt={producer.name} className="h-full w-full object-cover" />
                            ) : (
                                <div className="flex h-full w-full items-center justify-center text-8xl">
                                    <span className="material-symbols-outlined text-8xl text-terroir-green">agriculture</span>
                                </div>
                            )}
                        </div>
                    </Reveal>
                    <Reveal delay={0.1} className="lg:col-span-2">
                        <h1 className="font-display text-3xl font-semibold text-terroir-dark">{producer.name}</h1>
                        {producer.region && (
                            <p className="mt-2 flex items-center gap-1 text-sm text-terroir-dark/60">
                                <span className="material-symbols-outlined text-base">location_on</span> {producer.region}
                            </p>
                        )}
                        {producer.description && <p className="mt-6 whitespace-pre-line leading-relaxed text-terroir-dark/80">{producer.description}</p>}
                    </Reveal>
                </div>

                {products.length > 0 && (
                    <div className="mt-20">
                        <h2 className="section-title text-center">Produits de {producer.name}</h2>
                        <div className="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                            {products.map((product, i) => (
                                <Reveal key={product.id} delay={i * 0.05} y={16}>
                                    <ProductCard product={product} showProPrice={false} />
                                </Reveal>
                            ))}
                        </div>
                    </div>
                )}
            </section>
        </SiteLayout>
    );
}
