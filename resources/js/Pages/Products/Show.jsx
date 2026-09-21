import { useState } from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import SiteLayout from '../../Layouts/SiteLayout';
import Reveal from '../../Components/Reveal';
import ProductCard from '../../Components/ProductCard';

function formatFcfa(amount) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(amount)) + ' FCFA';
}

function StarRating({ rating, count, size = 'text-base' }) {
    return (
        <div className="flex items-center gap-1">
            <div className="flex gap-0.5 text-terroir-gold">
                {[1, 2, 3, 4, 5].map((s) => (
                    <span key={s} className={'material-symbols-outlined ' + size + (s <= Math.round(rating) ? ' is-filled' : '')}>star</span>
                ))}
            </div>
            <span className="text-sm font-semibold text-terroir-dark">{rating.toFixed(1)}</span>
            {count !== undefined && <span className="text-xs text-terroir-dark/50">({count} avis)</span>}
        </div>
    );
}

function ImageGallery({ images, name }) {
    const [active, setActive] = useState(0);

    return (
        <Reveal>
            <div className="aspect-square overflow-hidden rounded-xl2 bg-white shadow-soft">
                {images.length > 0 ? (
                    <AnimatePresence mode="wait">
                        <motion.img
                            key={images[active]}
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            transition={{ duration: 0.25 }}
                            src={`/fichiers/${images[active]}`}
                            alt={name}
                            className="h-full w-full object-cover"
                        />
                    </AnimatePresence>
                ) : (
                    <div className="flex h-full w-full items-center justify-center text-8xl"><span className="material-symbols-outlined text-8xl">eco</span></div>
                )}
            </div>
            {images.length > 1 && (
                <div className="mt-4 flex gap-3">
                    {images.map((image, i) => (
                        <motion.button
                            key={image}
                            whileTap={{ scale: 0.92 }}
                            onClick={() => setActive(i)}
                            className={'h-20 w-20 overflow-hidden rounded-lg border-2 transition ' + (active === i ? 'border-terroir-green' : 'border-transparent opacity-70')}
                        >
                            <img src={`/fichiers/${image}`} alt="" className="h-full w-full object-cover" />
                        </motion.button>
                    ))}
                </div>
            )}
        </Reveal>
    );
}

function ReviewForm({ productId }) {
    const { data, setData, post, processing, reset } = useForm({ rating: 5, comment: '' });

    function submit(e) {
        e.preventDefault();
        post(route('produits.avis.store', productId), { onSuccess: () => reset('comment') });
    }

    return (
        <form onSubmit={submit} className="card mt-6 max-w-lg p-6">
            <label className="label">Votre note</label>
            <div className="flex gap-1">
                {[1, 2, 3, 4, 5].map((s) => (
                    <motion.button
                        key={s}
                        type="button"
                        whileTap={{ scale: 0.8 }}
                        onClick={() => setData('rating', s)}
                        className="text-terroir-gold"
                    >
                        <span className={'material-symbols-outlined text-2xl' + (data.rating >= s ? ' is-filled' : '')}>star</span>
                    </motion.button>
                ))}
            </div>
            <div className="mt-4">
                <label className="label" htmlFor="comment">Votre commentaire (optionnel)</label>
                <textarea
                    id="comment"
                    rows="3"
                    value={data.comment}
                    onChange={(e) => setData('comment', e.target.value)}
                    className="input"
                />
            </div>
            <button type="submit" disabled={processing} className="btn-primary mt-4 disabled:opacity-50">Publier mon avis</button>
        </form>
    );
}

export default function ProductsShow({ product, reviews, related }) {
    const { props } = usePage();
    const user = props.auth?.user;
    const { data, setData, post, processing } = useForm({ quantity: 1 });

    function addToCart(e) {
        e.preventDefault();
        post(route('panier.add', product.id), { preserveScroll: true });
    }

    return (
        <SiteLayout>
            <Head title={`${product.name} — Centrale d'achat`} />

            <section className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <nav className="text-xs text-terroir-dark/50">
                    <Link href={route('accueil')} className="hover:text-terroir-terracotta">Accueil</Link> /{' '}
                    <Link href={route('produits.index')} className="hover:text-terroir-terracotta">Produits</Link> /{' '}
                    {product.category && (
                        <>
                            <Link href={route('produits.index', { categorie: product.category.slug })} className="hover:text-terroir-terracotta">{product.category.name}</Link> /{' '}
                        </>
                    )}
                    <span className="text-terroir-dark">{product.name}</span>
                </nav>

                <div className="mt-8 grid gap-12 lg:grid-cols-2">
                    <ImageGallery images={product.images} name={product.name} />

                    <Reveal delay={0.1}>
                        {product.category && <p className="section-eyebrow">{product.category.name}</p>}
                        <h1 className="mt-2 font-display text-3xl font-semibold text-terroir-dark">{product.name}</h1>

                        {product.rating && (
                            <div className="mt-2">
                                <StarRating rating={product.rating} count={product.reviews_count} />
                            </div>
                        )}

                        {product.producer && (
                            <p className="mt-2 text-sm text-terroir-dark/60">
                                Producteur : <Link href={route('producteurs.show', product.producer.slug)} className="font-semibold text-terroir-green hover:underline">{product.producer.name}</Link>
                            </p>
                        )}
                        {product.origin && <p className="mt-1 text-sm text-terroir-dark/60">Origine : {product.origin}</p>}

                        <div className="mt-6 flex flex-wrap items-baseline gap-3">
                            {product.is_on_promo ? (
                                <>
                                    <span className="font-display text-3xl font-bold text-terroir-terracotta">{formatFcfa(product.promo_price)}</span>
                                    <span className="text-lg text-terroir-dark/40 line-through">{formatFcfa(product.price)}</span>
                                </>
                            ) : (
                                <span className="font-display text-3xl font-bold text-terroir-green">{formatFcfa(product.price)}</span>
                            )}
                            <span className="text-sm text-terroir-dark/50">/ {product.unit}</span>
                            <span className="text-sm text-terroir-dark/40">(~{product.price_eur_indicative} indicatif)</span>
                        </div>

                        {(product.professional_price || product.wholesale_price) && (
                            <div className="mt-3 flex flex-wrap gap-4 text-xs text-terroir-dark/60">
                                {product.professional_price && <span>Tarif pro : <strong>{formatFcfa(product.professional_price)}</strong></span>}
                                {product.wholesale_price && <span>Tarif en gros : <strong>{formatFcfa(product.wholesale_price)}</strong></span>}
                            </div>
                        )}

                        <p className="mt-6 leading-relaxed text-terroir-dark/80">{product.short_description}</p>

                        <div className="mt-6">
                            {product.in_stock ? (
                                <span className="inline-flex items-center gap-2 text-sm font-medium text-terroir-green"><span className="h-2 w-2 rounded-full bg-terroir-green" /> En stock</span>
                            ) : (
                                <span className="inline-flex items-center gap-2 text-sm font-medium text-terroir-terracotta"><span className="h-2 w-2 rounded-full bg-terroir-terracotta" /> Rupture de stock</span>
                            )}
                        </div>

                        <form onSubmit={addToCart} className="mt-8 flex flex-wrap items-center gap-4">
                            <input
                                type="number"
                                value={data.quantity}
                                onChange={(e) => setData('quantity', Number(e.target.value))}
                                min="1"
                                max="500"
                                disabled={!product.in_stock}
                                className="input w-24"
                            />
                            <motion.button
                                whileTap={product.in_stock ? { scale: 0.96 } : undefined}
                                type="submit"
                                disabled={!product.in_stock || processing}
                                className="btn-primary disabled:opacity-50"
                            >
                                Ajouter au panier
                            </motion.button>
                            {user?.b2b_status === 'valide' ? (
                                <Link href={route('compte.devis.create')} className="btn-outline">Demander un devis</Link>
                            ) : (
                                <Link href={route('pages.show', 'hotels-professionnels')} className="btn-outline">Devenir client professionnel</Link>
                            )}
                        </form>

                        {product.description && (
                            <div className="mt-10 border-t border-terroir-green/10 pt-8">
                                <h2 className="font-display text-lg font-semibold text-terroir-dark">Description</h2>
                                <div className="mt-3 whitespace-pre-line leading-relaxed text-terroir-dark/80">{product.description}</div>
                            </div>
                        )}

                        <dl className="mt-8 grid grid-cols-2 gap-4 border-t border-terroir-green/10 pt-8 text-sm">
                            <div><dt className="text-terroir-dark/50">Référence</dt><dd className="font-medium">{product.reference}</dd></div>
                            {product.weight && <div><dt className="text-terroir-dark/50">Poids</dt><dd className="font-medium">{product.weight} kg</dd></div>}
                        </dl>
                    </Reveal>
                </div>

                <div className="mt-16 border-t border-terroir-green/10 pt-10">
                    <h2 className="section-title">Avis clients</h2>

                    {product.rating && (
                        <div className="mt-3">
                            <StarRating rating={product.rating} count={product.reviews_count} size="text-lg" />
                        </div>
                    )}

                    {user ? (
                        product.can_review && <ReviewForm productId={product.id} />
                    ) : (
                        <p className="mt-4 text-sm text-terroir-dark/60">
                            <Link href={route('login')} className="font-semibold text-terroir-green hover:underline">Connectez-vous</Link> pour laisser un avis si vous avez déjà commandé ce produit.
                        </p>
                    )}

                    {reviews.length > 0 ? (
                        <div className="mt-8 space-y-6">
                            {reviews.map((review) => (
                                <div key={review.id} className="border-b border-terroir-green/10 pb-6">
                                    <StarRating rating={review.rating} />
                                    {review.comment && <p className="mt-2 text-sm leading-relaxed text-terroir-dark/80">{review.comment}</p>}
                                    <p className="mt-2 text-xs text-terroir-dark/40">{review.user_name} — {review.created_at}</p>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <p className="mt-6 text-sm text-terroir-dark/50">Aucun avis pour le moment.</p>
                    )}
                </div>

                {related.length > 0 && (
                    <div className="mt-24">
                        <h2 className="section-title text-center">Produits similaires</h2>
                        <div className="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                            {related.map((item, i) => (
                                <Reveal key={item.id} delay={i * 0.05} y={16}>
                                    <ProductCard product={item} showProPrice={false} />
                                </Reveal>
                            ))}
                        </div>
                    </div>
                )}
            </section>
        </SiteLayout>
    );
}
