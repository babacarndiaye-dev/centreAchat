import { useEffect, useRef, useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import { AnimatePresence, motion, useReducedMotion, useScroll, useTransform } from 'framer-motion';
import SiteLayout from '../Layouts/SiteLayout';
import Reveal from '../Components/Reveal';
import ProductCard from '../Components/ProductCard';
import MotionLink from '../Components/MotionLink';
import Marquee from '../Components/Marquee';

const MARQUEE_ITEMS = ['Qualité sénégalaise', 'Approvisionnement local', 'Produits authentiques', 'Livraison rapide'];

const CATEGORY_ICONS = {
    'infusions-boissons-locales': 'emoji_food_beverage',
    'farines-poudres-locales': 'grain',
    'fruits-seches-snacks': 'nutrition',
    'plats-traditionnels-prets': 'ramen_dining',
    'coffrets-cadeaux': 'card_giftcard',
};

const TRUST_BADGES = [
    { icon: 'local_shipping', text: 'Livraison à Mbour & Dakar' },
    { icon: 'credit_card', text: 'Espèces, Wave, Orange Money' },
    { icon: 'lock', text: 'Commande sécurisée' },
    { icon: 'support_agent', text: 'Support réactif par chat' },
];

function formatFcfa(amount) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(amount)) + ' FCFA';
}

function HeroCarousel({ slides }) {
    const [active, setActive] = useState(0);

    useEffect(() => {
        if (slides.length < 2) return;
        const interval = setInterval(() => setActive((a) => (a + 1) % slides.length), 4500);
        return () => clearInterval(interval);
    }, [slides.length]);

    if (slides.length === 0) {
        return (
            <div className="flex aspect-square items-center justify-center rounded-xl2 bg-gradient-to-br from-white/10 to-white/5 text-7xl"><span className="material-symbols-outlined text-7xl">eco</span></div>
        );
    }

    const slide = slides[active];

    return (
        <div className="relative aspect-square overflow-hidden rounded-xl2 shadow-soft">
            <AnimatePresence mode="wait">
                <MotionLink
                    key={slide.slug}
                    href={route('produits.show', slide.slug)}
                    initial={{ opacity: 0, scale: 1.03 }}
                    animate={{ opacity: 1, scale: 1 }}
                    exit={{ opacity: 0 }}
                    transition={{ duration: 0.7, ease: 'easeOut' }}
                    className="absolute inset-0 block"
                >
                    <img src={`/fichiers/${slide.image}`} alt={slide.name} className="h-full w-full object-cover" />
                    <div className="absolute inset-x-0 bottom-0 bg-gradient-to-t from-terroir-dark/85 to-transparent p-7 pt-16 text-white">
                        <p className="text-sm opacity-80">{slide.category_name}</p>
                        <h3 className="mt-1 font-display text-xl font-semibold">{slide.name}</h3>
                        <p className="mt-1 font-bold">{formatFcfa(slide.price)}</p>
                    </div>
                </MotionLink>
            </AnimatePresence>
            <div className="absolute bottom-4 left-0 right-0 flex justify-center gap-1.5">
                {slides.map((s, i) => (
                    <button
                        key={s.slug}
                        type="button"
                        onClick={() => setActive(i)}
                        aria-label={`Voir ${s.name}`}
                        className={'h-1.5 rounded-full transition-all duration-300 ' + (active === i ? 'w-6 bg-white' : 'w-1.5 bg-white/50')}
                    />
                ))}
            </div>
        </div>
    );
}

function CountdownTimer({ target }) {
    const [remaining, setRemaining] = useState({ d: 0, h: 0, m: 0, s: 0 });

    useEffect(() => {
        function tick() {
            const diff = Math.max(0, target * 1000 - Date.now());
            setRemaining({
                d: Math.floor(diff / 86400000),
                h: Math.floor((diff % 86400000) / 3600000),
                m: Math.floor((diff % 3600000) / 60000),
                s: Math.floor((diff % 60000) / 1000),
            });
        }
        tick();
        const interval = setInterval(tick, 1000);
        return () => clearInterval(interval);
    }, [target]);

    const units = [
        ['Jours', remaining.d],
        ['Heures', remaining.h],
        ['Min', remaining.m],
        ['Sec', remaining.s],
    ];

    return (
        <div className="mt-2 flex gap-2">
            {units.map(([label, value]) => (
                <div key={label} className="flex w-14 flex-col items-center rounded-[2px] bg-white py-2 shadow-soft">
                    <AnimatePresence mode="popLayout">
                        <motion.span
                            key={value}
                            initial={{ y: -8, opacity: 0 }}
                            animate={{ y: 0, opacity: 1 }}
                            exit={{ y: 8, opacity: 0 }}
                            transition={{ duration: 0.2 }}
                            className="font-display text-lg font-bold text-terroir-dark"
                        >
                            {String(value).padStart(2, '0')}
                        </motion.span>
                    </AnimatePresence>
                    <span className="text-[10px] uppercase text-terroir-dark/40">{label}</span>
                </div>
            ))}
        </div>
    );
}

export default function Home({
    heroTitle,
    heroSubtitle,
    categories,
    featuredProducts,
    newProducts,
    slideProducts,
    promoProduct,
    producer,
    testimonials,
    showProPrice,
    showFeaturedProducts,
    showProducers,
    showTestimonials,
    showNewsletter,
}) {
    const heroDiscount = promoProduct && promoProduct.price > 0
        ? Math.round((1 - promoProduct.promo_price / promoProduct.price) * 100)
        : null;

    const heroRef = useRef(null);
    const reduceMotion = useReducedMotion();
    const { scrollYProgress: heroScrollProgress } = useScroll({ target: heroRef, offset: ['start start', 'end start'] });
    const heroParallaxY = useTransform(heroScrollProgress, [0, 1], reduceMotion ? [0, 0] : [0, 40]);

    return (
        <SiteLayout>
            <Head title="Centrale d'achat — Le meilleur du terroir local, sélectionné pour vous" />

            {/* HERO */}
            <section ref={heroRef} className="relative overflow-hidden bg-gradient-to-br from-terroir-green via-terroir-dark to-terroir-dark">
                <motion.div
                    animate={{ scale: [1, 1.15, 1], opacity: [0.2, 0.3, 0.2] }}
                    transition={{ duration: 10, repeat: Infinity, ease: 'easeInOut' }}
                    className="pointer-events-none absolute -right-32 -top-32 h-96 w-96 rounded-full bg-terroir-gold/20 blur-3xl"
                />
                <motion.div
                    animate={{ scale: [1, 1.1, 1], opacity: [0.05, 0.12, 0.05] }}
                    transition={{ duration: 12, repeat: Infinity, ease: 'easeInOut', delay: 1 }}
                    className="pointer-events-none absolute -bottom-24 -left-24 h-80 w-80 rounded-full bg-white/5 blur-3xl"
                />

                <div className="relative mx-auto max-w-7xl px-4 pb-10 pt-16 sm:px-6 md:pb-12 md:pt-24 lg:px-8">
                    <div className="grid items-center gap-12 md:grid-cols-2">
                        <motion.div
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.7, ease: [0.16, 1, 0.3, 1] }}
                        >
                            {promoProduct?.promo_ends_at && (
                                <>
                                    <span className="mb-3 inline-flex items-center rounded-[2px] bg-terroir-gold px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-terroir-dark">
                                        Offre à durée limitée
                                    </span>
                                    <br />
                                </>
                            )}
                            <span className="section-eyebrow !text-terroir-gold">Fabriqué au Sénégal</span>
                            <h1 className="mt-3 max-w-lg font-display text-4xl font-semibold leading-tight tracking-tight text-white sm:text-6xl">
                                {heroTitle || 'Le meilleur du terroir local, sélectionné pour vous.'}
                            </h1>
                            <p className="mt-4 max-w-md text-lg leading-relaxed text-white/70">
                                {heroSubtitle || "Des producteurs locaux aux hôtels, professionnels et consommateurs, Centrale d'achat facilite l'accès à des produits authentiques, frais et de qualité."}
                            </p>
                            <div className="mt-8 flex flex-wrap gap-3.5">
                                <MotionLink whileHover={{ y: -2 }} whileTap={{ scale: 0.97 }} href={route('produits.index')} className="btn-gold">
                                    Découvrir nos produits
                                    <span className="material-symbols-outlined text-lg">arrow_forward</span>
                                </MotionLink>
                                <MotionLink
                                    whileHover={{ y: -2 }}
                                    whileTap={{ scale: 0.97 }}
                                    href={route('pages.show', 'contact')}
                                    className="btn border border-white/35 bg-white/10 text-white hover:bg-white/20"
                                >
                                    Nous contacter
                                </MotionLink>
                            </div>
                        </motion.div>

                        <motion.div
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.7, delay: 0.1, ease: [0.16, 1, 0.3, 1] }}
                            className="relative"
                        >
                            {heroDiscount > 0 && (
                                <motion.div
                                    initial={{ scale: 0, rotate: -20 }}
                                    animate={{ scale: 1, rotate: 6 }}
                                    transition={{ type: 'spring', stiffness: 260, damping: 18, delay: 0.5 }}
                                    className="absolute -right-3 -top-3 z-10 flex h-20 w-20 flex-col items-center justify-center rounded-full bg-terroir-gold text-center text-terroir-dark shadow-soft sm:-right-4 sm:-top-4 sm:h-24 sm:w-24"
                                >
                                    <span className="text-[10px] font-semibold uppercase leading-none">Jusqu'à</span>
                                    <span className="font-display text-2xl font-bold leading-tight sm:text-3xl">-{heroDiscount}%</span>
                                </motion.div>
                            )}
                            <motion.div style={{ y: heroParallaxY }}>
                                <HeroCarousel slides={slideProducts} />
                            </motion.div>
                        </motion.div>
                    </div>
                </div>

                {/* TRUST BADGES */}
                <div className="relative bg-white">
                    <Reveal className="mx-auto grid max-w-7xl grid-cols-2 gap-y-5 px-4 py-6 sm:px-6 md:grid-cols-4 lg:px-8">
                        {TRUST_BADGES.map((badge) => (
                            <div key={badge.text} className="flex items-center justify-center gap-2.5 text-sm font-medium text-terroir-dark/70">
                                <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-terroir-green/10 text-lg text-terroir-green">
                                    <span className="material-symbols-outlined">{badge.icon}</span>
                                </span>
                                <span>{badge.text}</span>
                            </div>
                        ))}
                    </Reveal>
                </div>
            </section>

            {/* OFFRE DU MOMENT */}
            {promoProduct && (
                <section className="mx-auto max-w-7xl px-4 py-16 sm:px-6 md:py-24 lg:px-8">
                    <Reveal className="grid overflow-hidden rounded-xl2 bg-gradient-to-br from-terroir-gold/15 to-terroir-gold/5 shadow-soft md:grid-cols-2">
                        <div className="flex flex-col justify-center p-11">
                            <span className="section-eyebrow">Offre du moment</span>
                            <h2 className="section-title mt-1">{promoProduct.name}</h2>
                            <p className="mt-2.5 leading-relaxed text-terroir-dark/60">{promoProduct.short_description}</p>
                            <div className="mt-3 flex items-center gap-3.5">
                                <span className="font-display text-3xl font-bold text-terroir-gold">{formatFcfa(promoProduct.promo_price)}</span>
                                <span className="text-terroir-dark/35 line-through">{formatFcfa(promoProduct.price)}</span>
                            </div>
                            {promoProduct.stock_quantity > 0 && (
                                <p className="mt-2.5 text-sm font-medium text-terroir-terracotta">
                                    Il reste {promoProduct.stock_quantity} unité{promoProduct.stock_quantity > 1 ? 's' : ''} en stock
                                </p>
                            )}
                            {promoProduct.promo_ends_at && (
                                <>
                                    <p className="mt-3 text-xs font-semibold uppercase tracking-wide text-terroir-dark/40">
                                        Offre valable jusqu'au {promoProduct.promo_ends_at_label}
                                    </p>
                                    <CountdownTimer target={promoProduct.promo_ends_at} />
                                </>
                            )}
                            <MotionLink whileHover={{ y: -2 }} whileTap={{ scale: 0.97 }} href={route('produits.show', promoProduct.slug)} className="btn-primary mt-6 self-start">
                                Voir l'offre
                            </MotionLink>
                        </div>
                        <div className="flex items-center justify-center bg-white/50 p-11">
                            {promoProduct.image ? (
                                <img src={`/fichiers/${promoProduct.image}`} alt={promoProduct.name} className="max-h-64 rounded-[2px] object-cover shadow-soft" />
                            ) : (
                                <span className="material-symbols-outlined text-7xl">eco</span>
                            )}
                        </div>
                    </Reveal>
                </section>
            )}

            {/* CATEGORIES */}
            {categories.length > 0 && (
                <section className="mx-auto max-w-7xl px-4 py-16 sm:px-6 md:py-24 lg:px-8">
                    <Reveal className="text-center">
                        <span className="section-eyebrow">Explorez</span>
                        <h2 className="section-title mt-1">Nos catégories</h2>
                    </Reveal>
                    <div className="mt-12 flex flex-wrap justify-center gap-5">
                        {categories.map((category, i) => (
                            <Reveal
                                key={category.id}
                                delay={i * 0.06}
                                y={16}
                                className="w-[calc(50%-0.625rem)] md:w-[calc(25%-0.9375rem)]"
                            >
                                <MotionLink
                                    whileHover={{ y: -4 }}
                                    href={route('produits.index', { categorie: category.slug })}
                                    className="group flex flex-col items-center rounded-xl2 border border-terroir-dark/5 bg-white p-10 text-center shadow-soft transition-shadow duration-300 hover:shadow-xl md:p-12"
                                >
                                    <motion.span
                                        whileHover={{ scale: 1.1, backgroundColor: 'rgb(var(--terroir-green))' }}
                                        className="flex h-20 w-20 items-center justify-center rounded-full bg-terroir-green/10 text-4xl text-terroir-green transition-colors duration-300 group-hover:text-white"
                                    >
                                        <span className="material-symbols-outlined text-4xl">{CATEGORY_ICONS[category.slug] ?? 'eco'}</span>
                                    </motion.span>
                                    <span className="mt-5 font-display text-lg font-semibold text-terroir-dark">{category.name}</span>
                                    <span className="mt-1.5 inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wide text-terroir-green">
                                        Voir
                                        <span className="material-symbols-outlined text-sm transition group-hover:translate-x-0.5">arrow_forward</span>
                                    </span>
                                </MotionLink>
                            </Reveal>
                        ))}
                    </div>
                </section>
            )}

            <Marquee items={MARQUEE_ITEMS} />

            {/* PRODUITS VEDETTES */}
            {showFeaturedProducts && featuredProducts.length > 0 && (
                <section className="bg-white py-16 md:py-24">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <Reveal className="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <span className="section-eyebrow">Sélection</span>
                                <h2 className="section-title mt-1">Produits en vedette</h2>
                            </div>
                            <Link href={route('produits.index')} className="inline-flex items-center gap-1.5 text-sm font-semibold text-terroir-green hover:text-terroir-dark">
                                Voir tous les produits
                                <span className="material-symbols-outlined text-lg">arrow_forward</span>
                            </Link>
                        </Reveal>
                        <div className="mt-12 grid grid-cols-2 gap-5 md:grid-cols-4">
                            {featuredProducts.map((product, i) => (
                                <Reveal key={product.id} delay={i * 0.05} y={16}>
                                    <ProductCard product={product} showProPrice={showProPrice} />
                                </Reveal>
                            ))}
                        </div>
                    </div>
                </section>
            )}

            {/* ESPACES DEDIES */}
            <section className="bg-terroir-cream py-16 md:py-24">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="grid gap-6 md:grid-cols-2">
                        <Reveal className="flex h-full flex-col rounded-xl2 bg-terroir-green p-11 text-white shadow-soft">
                            <span className="flex h-12 w-12 items-center justify-center rounded-full bg-white/15 text-2xl">
                                <span className="material-symbols-outlined">hotel</span>
                            </span>
                            <h3 className="mt-4.5 font-display text-xl font-semibold">Hôtels &amp; Professionnels</h3>
                            <p className="mt-3 leading-relaxed text-white/85">Tarifs dégressifs, commandes en gros, devis personnalisés et facturation professionnelle.</p>
                            <MotionLink whileHover={{ y: -2 }} whileTap={{ scale: 0.97 }} href={route('pages.show', 'hotels-professionnels')} className="btn mt-6 self-start bg-white text-terroir-green">
                                Découvrir l'offre B2B
                            </MotionLink>
                        </Reveal>
                        <Reveal delay={0.08} className="flex h-full flex-col rounded-xl2 bg-terroir-gold p-11 text-terroir-dark shadow-soft">
                            <span className="flex h-12 w-12 items-center justify-center rounded-full bg-white/40 text-2xl">
                                <span className="material-symbols-outlined">card_giftcard</span>
                            </span>
                            <h3 className="mt-4.5 font-display text-xl font-semibold">Coffrets &amp; Cadeaux</h3>
                            <p className="mt-3 leading-relaxed text-terroir-dark/80">Des coffrets prêts à offrir, composés à partir de notre gamme locale.</p>
                            <MotionLink whileHover={{ y: -2 }} whileTap={{ scale: 0.97 }} href={route('pages.show', 'coffrets-cadeaux')} className="btn-primary mt-6 self-start">
                                Voir les coffrets
                            </MotionLink>
                        </Reveal>
                    </div>
                </div>
            </section>

            {/* NOUVEAUTES */}
            {newProducts.length > 0 && (
                <section className="bg-terroir-cream py-16 md:py-24">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <Reveal className="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <span className="section-eyebrow">Fraîchement arrivé</span>
                                <h2 className="section-title mt-1">Nouveautés</h2>
                            </div>
                            <Link href={route('produits.index')} className="inline-flex items-center gap-1.5 text-sm font-semibold text-terroir-green hover:text-terroir-dark">
                                Voir tout
                                <span className="material-symbols-outlined text-lg">arrow_forward</span>
                            </Link>
                        </Reveal>
                    </div>
                    <Reveal delay={0.08} className="mx-auto mt-12 max-w-7xl">
                        <div className="scrollbar-none flex gap-5 overflow-x-auto px-4 pb-2 snap-x snap-mandatory sm:px-6 lg:px-8">
                            {newProducts.map((product) => (
                                <div key={product.id} className="w-[65vw] shrink-0 snap-start sm:w-56 lg:w-64">
                                    <ProductCard product={product} showProPrice={showProPrice} />
                                </div>
                            ))}
                            <div className="w-px shrink-0" aria-hidden="true" />
                        </div>
                    </Reveal>
                </section>
            )}

            {/* PRESENTATION */}
            <section className="bg-white py-16 md:py-24">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="grid items-center gap-4 md:grid-cols-2 md:gap-8">
                        <Reveal className="order-1">
                            <img
                                src="/images/mbour-terroir-nobg.png"
                                alt="Centre d'achat de Mbour — produits du terroir sénégalais"
                                className="mx-auto w-full max-w-sm sm:max-w-md"
                            />
                        </Reveal>
                        <Reveal delay={0.1} className="order-2">
                            <span className="section-eyebrow">Qui sommes-nous</span>
                            <h2 className="section-title mt-1">Centre d'achat de Mbour</h2>
                            <p className="mt-2 text-sm leading-relaxed text-terroir-dark/70">
                                Le Centre d'achat de Mbour est une plateforme dédiée à la commercialisation et à la valorisation des produits locaux sénégalais. Situé à Mbour – Rond-Point Malicounda, il met en relation producteurs, fournisseurs et artisans avec les hôtels, restaurants, entreprises et touristes.
                            </p>
                            <p className="mt-2 text-sm leading-relaxed text-terroir-dark/70">
                                Sa mission est de faciliter l'accès aux marchés, promouvoir le savoir-faire local et renforcer les circuits de distribution des produits sénégalais.
                            </p>
                            <p className="mt-3 font-display text-lg italic text-terroir-green">« Le terroir sénégalais au cœur du commerce. »</p>
                        </Reveal>
                    </div>
                </div>
            </section>

            {/* PRODUCTEUR */}
            {showProducers && producer && (
                <section className="relative overflow-hidden bg-terroir-green py-16 text-white md:py-24">
                    <div className="pointer-events-none absolute left-1/2 top-0 h-80 w-[40rem] -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/5 blur-3xl" />
                    <Reveal className="relative mx-auto max-w-2xl px-4 text-center sm:px-6">
                        <span className="section-eyebrow !text-terroir-gold">Fabriqué par</span>
                        <h2 className="section-title mt-1 !text-white">{producer.name}</h2>
                        {producer.region && (
                            <p className="mt-2 flex items-center justify-center gap-1.5 text-white/70">
                                <span className="material-symbols-outlined text-lg">location_on</span>
                                {producer.region}
                            </p>
                        )}
                        {producer.description && <p className="mx-auto mt-5 max-w-lg leading-loose text-white/90">{producer.description}</p>}
                        <MotionLink whileHover={{ y: -2 }} whileTap={{ scale: 0.97 }} href={route('producteurs.show', producer.slug)} className="btn mt-6 bg-white text-terroir-green">
                            Voir tous les produits
                        </MotionLink>
                    </Reveal>
                </section>
            )}

            {/* TEMOIGNAGES */}
            {showTestimonials && testimonials.length > 0 && (
                <section className="bg-white py-16 md:py-24">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <Reveal className="text-center">
                            <span className="section-eyebrow">Ils nous font confiance</span>
                            <h2 className="section-title mt-1">Avis de nos clients</h2>
                        </Reveal>
                        <div className="mt-12 grid gap-5 md:grid-cols-3">
                            {testimonials.map((testimonial, i) => (
                                <Reveal key={testimonial.id} delay={i * 0.08} y={16}>
                                    <motion.div whileHover={{ y: -4 }} className="card h-full p-9">
                                        <div className="flex gap-0.5 text-terroir-gold">
                                            {[1, 2, 3, 4, 5].map((s) => (
                                                <span key={s} className={'material-symbols-outlined text-lg' + (s <= testimonial.rating ? ' is-filled' : '')}>
                                                    star
                                                </span>
                                            ))}
                                        </div>
                                        <p className="mt-4.5 text-sm italic leading-relaxed text-terroir-dark/70">"{testimonial.content}"</p>
                                        <p className="mt-4.5 text-sm font-bold text-terroir-dark">{testimonial.author_name}</p>
                                        {testimonial.author_role && <p className="text-sm text-terroir-dark/40">{testimonial.author_role}</p>}
                                    </motion.div>
                                </Reveal>
                            ))}
                        </div>
                    </div>
                </section>
            )}

            {/* NEWSLETTER + LOCALISATION */}
            <section className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div className={showNewsletter ? 'grid gap-6 lg:grid-cols-2' : ''}>
                    {showNewsletter && (
                        <Reveal className="flex h-full flex-col justify-center rounded-xl2 bg-terroir-green p-8 text-white shadow-soft sm:p-11">
                            <h3 className="font-display text-2xl font-semibold text-white">Restez informé de nos nouveautés</h3>
                            <p className="mt-2 text-white">Recevez nos nouveaux produits et offres par e-mail.</p>
                            <form action={route('newsletter.store')} method="POST" className="mt-6 flex flex-col gap-2.5 sm:flex-row">
                                <input type="hidden" name="_token" value={document.querySelector('meta[name=csrf-token]')?.content} />
                                <input
                                    type="email"
                                    name="email"
                                    required
                                    placeholder="Votre e-mail"
                                    className="input border-white/25 bg-white/10 text-white placeholder:text-white/50 focus:border-white"
                                />
                                <button type="submit" className="btn-gold shrink-0">S'inscrire</button>
                            </form>
                        </Reveal>
                    )}

                    <Reveal delay={showNewsletter ? 0.1 : 0} className="flex h-full flex-col justify-center rounded-xl2 bg-terroir-dark p-8 text-white sm:p-11">
                        <h3 className="flex items-center gap-2 font-display text-xl font-semibold text-white">
                            <span className="material-symbols-outlined text-xl text-terroir-gold">location_on</span>
                            Rond-Point Malicounda, Mbour – Sénégal
                        </h3>
                        <p className="mt-2 text-white">Visitez notre boutique ou passez commande en ligne, livraison partout au Sénégal.</p>
                        <MotionLink
                            whileHover={{ y: -2 }}
                            whileTap={{ scale: 0.97 }}
                            href={route('pages.show', 'contact')}
                            className="btn-gold mt-6 self-start whitespace-nowrap"
                        >
                            Nous contacter
                        </MotionLink>
                    </Reveal>
                </div>
            </section>
        </SiteLayout>
    );
}
