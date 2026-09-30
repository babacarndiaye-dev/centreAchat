import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import SiteLayout from '../../Layouts/SiteLayout';
import Reveal from '../../Components/Reveal';
import ProductCard from '../../Components/ProductCard';

export default function ProductsIndex({ products, categories, filters }) {
    const [q, setQ] = useState(filters.q);
    const [categorie, setCategorie] = useState(filters.categorie);
    const [tri, setTri] = useState(filters.tri);

    function submit(e) {
        e?.preventDefault();
        router.get(route('produits.index'), { q, categorie, tri }, { preserveState: true, replace: true });
    }

    function submitWith(overrides) {
        router.get(route('produits.index'), { q, categorie, tri, ...overrides }, { preserveState: true, replace: true });
    }

    return (
        <SiteLayout>
            <Head title="Nos produits — DIABA HOTEL" />

            <section className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <Reveal className="text-center">
                    <span className="section-eyebrow">Catalogue</span>
                    <h1 className="section-title mt-2">Nos produits du terroir</h1>
                    <p className="mx-auto mt-3 max-w-xl text-terroir-dark/70">Une sélection rigoureuse de produits locaux, frais et authentiques.</p>
                </Reveal>

                <form onSubmit={submit} className="mt-10 flex flex-wrap items-center justify-center gap-3">
                    <input
                        type="text"
                        value={q}
                        onChange={(e) => setQ(e.target.value)}
                        placeholder="Rechercher un produit..."
                        className="input max-w-xs"
                    />

                    <select
                        value={categorie}
                        onChange={(e) => { setCategorie(e.target.value); submitWith({ categorie: e.target.value }); }}
                        className="input max-w-[200px]"
                    >
                        <option value="">Toutes catégories</option>
                        {categories.map((category) => (
                            <option key={category.slug} value={category.slug}>{category.name}</option>
                        ))}
                    </select>

                    <select
                        value={tri}
                        onChange={(e) => { setTri(e.target.value); submitWith({ tri: e.target.value }); }}
                        className="input max-w-[200px]"
                    >
                        <option value="recent">Plus récents</option>
                        <option value="prix_asc">Prix croissant</option>
                        <option value="prix_desc">Prix décroissant</option>
                        <option value="nom">Nom (A-Z)</option>
                    </select>

                    <motion.button whileTap={{ scale: 0.96 }} type="submit" className="btn-primary">Filtrer</motion.button>
                </form>

                {products.data.length === 0 ? (
                    <div className="mt-16 text-center text-terroir-dark/60">
                        <span className="material-symbols-outlined text-5xl text-terroir-green">eco</span>
                        <p className="mt-4">Aucun produit ne correspond à votre recherche pour le moment.</p>
                    </div>
                ) : (
                    <>
                        <div className="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                            {products.data.map((product, i) => (
                                <Reveal key={product.id} delay={Math.min(i, 8) * 0.04} y={16}>
                                    <ProductCard product={product} showProPrice={false} />
                                </Reveal>
                            ))}
                        </div>

                        {products.links.length > 3 && (
                            <div className="mt-12 flex flex-wrap justify-center gap-2">
                                {products.links.map((link, i) =>
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
