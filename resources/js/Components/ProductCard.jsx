import { Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';

function formatFcfa(amount) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(amount)) + ' FCFA';
}

export default function ProductCard({ product, showProPrice }) {
    function toggleFavorite(e) {
        e.preventDefault();
        router.post(route('produits.favori.toggle', product.id), {}, { preserveScroll: true });
    }

    function addToCart(e) {
        e.preventDefault();
        router.post(route('panier.add', product.id), {}, { preserveScroll: true });
    }

    return (
        <div className="product-card group flex h-full flex-col border border-terroir-dark/[0.06]">
            <div className="relative">
                <Link href={route('produits.show', product.slug)} className="block">
                    <div className="aspect-square overflow-hidden bg-terroir-cream p-5">
                        {product.image ? (
                            <img
                                src={`/fichiers/${product.image}`}
                                alt={product.name}
                                className="h-full w-full object-contain transition duration-700 ease-out group-hover:scale-[1.04]"
                            />
                        ) : (
                            <div className="flex h-full items-center justify-center text-5xl">🌿</div>
                        )}
                    </div>

                    <div className="absolute left-3 top-3 flex flex-col gap-1.5">
                        {product.is_on_promo && (
                            <span className="rounded-full bg-terroir-terracotta px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-white shadow-soft">Promo</span>
                        )}
                        {product.is_new && (
                            <span className="rounded-full bg-terroir-gold px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-terroir-dark shadow-soft">Nouveau</span>
                        )}
                        {product.is_best_seller && (
                            <span className="rounded-full bg-terroir-green px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-white shadow-soft">Meilleure vente</span>
                        )}
                    </div>

                    {!product.in_stock && (
                        <div className="absolute inset-0 flex items-center justify-center bg-terroir-dark/60 backdrop-blur-[1px]">
                            <span className="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-terroir-dark">Rupture de stock</span>
                        </div>
                    )}
                </Link>

                <motion.button
                    onClick={toggleFavorite}
                    type="button"
                    whileTap={{ scale: 0.8 }}
                    aria-label={product.is_wishlisted ? 'Retirer des favoris' : 'Ajouter aux favoris'}
                    className={
                        'absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded-full bg-white/95 text-base shadow-soft backdrop-blur transition hover:scale-110 ' +
                        (product.is_wishlisted ? 'text-terroir-terracotta' : 'text-terroir-dark/40')
                    }
                >
                    <motion.span
                        key={product.is_wishlisted ? 'filled' : 'outline'}
                        initial={{ scale: 0.5 }}
                        animate={{ scale: 1 }}
                        transition={{ type: 'spring', stiffness: 500, damping: 15 }}
                        className={'material-symbols-outlined' + (product.is_wishlisted ? ' is-filled' : '')}
                        style={{ fontSize: '1.05rem' }}
                    >
                        favorite
                    </motion.span>
                </motion.button>
            </div>

            <div className="flex flex-1 flex-col p-5">
                {product.category_name && (
                    <p className="text-[11px] font-semibold uppercase tracking-[0.08em] text-terroir-terracotta/90">{product.category_name}</p>
                )}

                <Link href={route('produits.show', product.slug)} className="text-terroir-dark">
                    <h3 className="mt-1.5 font-display text-[1.05rem] font-semibold leading-snug transition group-hover:text-terroir-green">{product.name}</h3>
                </Link>

                {(product.rating || product.producer_name) && (
                    <div className="mt-1.5 flex items-center gap-1.5 text-xs text-terroir-dark/45">
                        {product.rating && (
                            <span className="flex items-center gap-0.5 text-terroir-gold">
                                <span className="material-symbols-outlined is-filled text-sm">star</span>
                                <span className="font-semibold text-terroir-dark/70">{product.rating.toFixed(1)}</span>
                            </span>
                        )}
                        {product.rating && product.producer_name && <span className="text-terroir-dark/20">·</span>}
                        {product.producer_name && <span className="truncate">{product.producer_name}</span>}
                    </div>
                )}

                {product.in_stock && product.stock_quantity <= product.stock_alert_threshold && (
                    <p className="mt-2 text-xs font-semibold text-terroir-terracotta">Plus que {product.stock_quantity} en stock</p>
                )}

                <div className="mt-4 flex flex-1 items-end justify-between gap-3 border-t border-terroir-dark/[0.06] pt-4">
                    <div className="min-w-0">
                        {product.is_on_promo ? (
                            <>
                                <span className="block text-xs text-terroir-dark/35 line-through">{formatFcfa(product.price)}</span>
                                <span className="block font-display text-lg font-bold leading-tight text-terroir-terracotta">{formatFcfa(product.promo_price)}</span>
                            </>
                        ) : (
                            <span className="block font-display text-lg font-bold leading-tight text-terroir-dark">{formatFcfa(product.price)}</span>
                        )}
                        <span className="text-xs text-terroir-dark/40">/ {product.unit}</span>
                        {showProPrice && product.professional_price && (
                            <span className="mt-1 block text-xs font-semibold text-terroir-green">Tarif pro : {formatFcfa(product.professional_price)}</span>
                        )}
                    </div>

                    <motion.button
                        onClick={addToCart}
                        type="button"
                        disabled={!product.in_stock}
                        whileTap={product.in_stock ? { scale: 0.85 } : undefined}
                        whileHover={product.in_stock ? { y: -2 } : undefined}
                        className="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-terroir-green text-lg text-white shadow-soft transition-colors duration-300 hover:bg-terroir-dark hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-40"
                        aria-label="Ajouter au panier"
                    >
                        <span className="material-symbols-outlined">add_shopping_cart</span>
                    </motion.button>
                </div>
            </div>
        </div>
    );
}
