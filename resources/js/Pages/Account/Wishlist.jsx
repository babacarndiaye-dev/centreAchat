import { Head, Link } from '@inertiajs/react';
import SiteLayout from '../../Layouts/SiteLayout';
import ProductCard from '../../Components/ProductCard';

export default function AccountWishlist({ products, showProPrice }) {
    return (
        <SiteLayout>
            <Head title="Mes favoris — Central d'Achat" />

            <section className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <span className="section-eyebrow">Mon compte</span>
                <h1 className="section-title mt-2">Mes favoris</h1>

                <Link href={route('compte.index')} className="mt-4 inline-block text-sm text-terroir-dark/60 hover:text-terroir-terracotta">
                    ← Retour à mon compte
                </Link>

                {products.length === 0 ? (
                    <p className="mt-10 text-terroir-dark/60">Vous n'avez pas encore de produit favori. Cliquez sur le cœur d'un produit pour l'ajouter ici.</p>
                ) : (
                    <div className="mt-10 grid grid-cols-2 gap-5 md:grid-cols-4">
                        {products.map((product) => (
                            <ProductCard key={product.id} product={product} showProPrice={showProPrice} />
                        ))}
                    </div>
                )}
            </section>
        </SiteLayout>
    );
}
