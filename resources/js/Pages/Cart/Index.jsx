import { Head, Link, router } from '@inertiajs/react';
import SiteLayout from '../../Layouts/SiteLayout';

function formatFcfa(amount) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(amount)) + ' FCFA';
}

export default function CartIndex({ items, subtotal }) {
    function updateQuantity(productId, quantity) {
        router.patch(route('panier.update', productId), { quantity }, { preserveScroll: true });
    }

    function remove(productId) {
        router.delete(route('panier.remove', productId), { preserveScroll: true });
    }

    return (
        <SiteLayout>
            <Head title="Votre panier — Central d'Achat" />

            <section className="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
                <h1 className="section-title text-center">Votre panier</h1>

                {items.length === 0 ? (
                    <div className="mt-16 text-center text-terroir-dark/60">
                        <p className="text-6xl">🛒</p>
                        <p className="mt-4">Votre panier est vide pour le moment.</p>
                        <a href={route('produits.index')} className="btn-primary mt-6">Découvrir nos produits</a>
                    </div>
                ) : (
                    <>
                        <div className="mt-10 space-y-4">
                            {items.map((item) => {
                                const image = item.product.images?.[0];
                                return (
                                    <div key={item.product.id} className="card flex flex-wrap items-center gap-4 p-4">
                                        <div className="h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg bg-terroir-cream">
                                            {image ? (
                                                <img src={`/fichiers/${image.path}`} alt="" className="h-full w-full object-cover" />
                                            ) : (
                                                <div className="flex h-full w-full items-center justify-center text-2xl">🌿</div>
                                            )}
                                        </div>

                                        <div className="min-w-[180px] flex-1">
                                            <a
                                                href={route('produits.show', item.product.slug)}
                                                className="font-semibold text-terroir-dark hover:text-terroir-terracotta"
                                            >
                                                {item.product.name}
                                            </a>
                                            <p className="text-sm text-terroir-dark/50">
                                                {formatFcfa(item.unit_price)} / {item.product.unit}
                                            </p>
                                        </div>

                                        <input
                                            type="number"
                                            defaultValue={item.quantity}
                                            min="0"
                                            max="500"
                                            className="input w-20"
                                            onBlur={(e) => updateQuantity(item.product.id, Number(e.target.value))}
                                            onKeyDown={(e) => {
                                                if (e.key === 'Enter') {
                                                    e.preventDefault();
                                                    updateQuantity(item.product.id, Number(e.currentTarget.value));
                                                }
                                            }}
                                        />

                                        <div className="w-28 text-right font-semibold text-terroir-green">{formatFcfa(item.total)}</div>

                                        <button
                                            onClick={() => remove(item.product.id)}
                                            type="button"
                                            className="text-lg text-terroir-dark/40 transition hover:text-terroir-terracotta"
                                            aria-label="Retirer"
                                        >
                                            <span className="material-symbols-outlined">delete</span>
                                        </button>
                                    </div>
                                );
                            })}
                        </div>

                        <div className="card mt-10 flex flex-col items-end gap-4 p-6">
                            <div className="flex w-full max-w-xs items-center justify-between text-lg">
                                <span className="font-medium text-terroir-dark/70">Sous-total</span>
                                <span className="font-bold text-terroir-green">{formatFcfa(subtotal)}</span>
                            </div>
                            <Link href={route('commande.index')} className="btn-primary w-full max-w-xs justify-center">
                                Passer la commande
                            </Link>
                            <a href={route('produits.index')} className="text-sm text-terroir-dark/60 hover:text-terroir-terracotta">
                                ← Continuer mes achats
                            </a>
                        </div>
                    </>
                )}
            </section>
        </SiteLayout>
    );
}
