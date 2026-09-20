import { useEffect, useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import ChatWidget from '../Components/ChatWidget';
import PageTransition from '../Components/PageTransition';
import PullToRefresh from '../Components/PullToRefresh';

const NAV_LINKS = [
    { href: () => route('produits.index'), label: 'Nos produits' },
    { href: () => route('pages.show', 'hotels-professionnels'), label: 'Hôtels & Pro' },
    { href: () => route('pages.show', 'espace-touristes'), label: 'Touristes' },
    { href: () => route('blog.index'), label: 'Actualités' },
    { href: () => route('pages.show', 'contact'), label: 'Contact' },
];

function FlashToast() {
    const { props } = usePage();
    const [message, setMessage] = useState(null);

    useEffect(() => {
        if (props.flash?.success) {
            setMessage({ type: 'success', text: props.flash.success });
        } else if (props.flash?.error) {
            setMessage({ type: 'error', text: props.flash.error });
        } else {
            return;
        }
        const timeout = setTimeout(() => setMessage(null), 3000);
        return () => clearTimeout(timeout);
    }, [props.flash?.success, props.flash?.error]);

    return (
        <div className="pointer-events-none fixed right-5 top-5 z-[60] flex max-w-xs flex-col items-end">
            <AnimatePresence>
                {message && (
                    <motion.div
                        key={message.text}
                        initial={{ opacity: 0, x: 40, scale: 0.9 }}
                        animate={{ opacity: 1, x: 0, scale: 1 }}
                        exit={{ opacity: 0, x: 40, scale: 0.9 }}
                        transition={{ type: 'spring', stiffness: 400, damping: 28 }}
                        className={
                            'pointer-events-auto rounded-xl px-4 py-3 text-sm font-medium text-white shadow-lg ' +
                            (message.type === 'success' ? 'bg-terroir-green' : 'bg-terroir-terracotta')
                        }
                    >
                        {message.text}
                    </motion.div>
                )}
            </AnimatePresence>
        </div>
    );
}

export default function SiteLayout({ children }) {
    const { props } = usePage();
    const { name: siteName, logoUrl, announcementActive, announcementText, address, showNewsletter } = props.site ?? {};
    const user = props.auth?.user ?? null;
    const cartCount = props.cart?.count ?? 0;
    const [mobileOpen, setMobileOpen] = useState(false);
    const [search, setSearch] = useState('');

    function submitSearch(e) {
        e.preventDefault();
        router.get(route('produits.index'), search ? { q: search } : {});
    }

    return (
        <div className="flex min-h-screen flex-col bg-white">
            <FlashToast />

            <div className="hidden items-center justify-center gap-6 bg-terroir-dark px-4 py-2 text-xs font-medium text-white/90 sm:flex">
                <span className="flex items-center gap-1.5">
                    <span className="shrink-0 text-sm text-terroir-gold"><span className="material-symbols-outlined">local_shipping</span></span>
                    Livraison rapide au Sénégal
                </span>
                <span className="flex items-center gap-1.5">
                    <span className="shrink-0 text-sm text-terroir-gold"><span className="material-symbols-outlined">lock</span></span>
                    Paiement sécurisé
                </span>
                <span className="flex items-center gap-1.5">
                    <span className="shrink-0 text-sm text-terroir-gold"><span className="material-symbols-outlined">support_agent</span></span>
                    Support client
                </span>
            </div>

            {announcementActive && announcementText && (
                <div className="flex items-center justify-center gap-2 bg-terroir-gold px-4 py-2.5 text-center text-sm font-medium text-terroir-dark">
                    <span className="material-symbols-outlined is-filled text-base">campaign</span>
                    <span>{announcementText}</span>
                </div>
            )}

            <header className="sticky top-0 z-50 bg-white/60 backdrop-blur transition-colors duration-300">
                <div className="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                    <Link href={route('accueil')} className="flex items-center gap-2">
                        {logoUrl ? (
                            <img src={logoUrl} alt={siteName} className="h-9 w-9 rounded-full object-cover" />
                        ) : (
                            <span className="material-symbols-outlined text-2xl">eco</span>
                        )}
                        <span className="font-display text-xl font-semibold text-terroir-green">{siteName}</span>
                    </Link>

                    <nav className="ml-10 hidden items-center gap-7 lg:flex">
                        {NAV_LINKS.map((link) => (
                            <Link
                                key={link.label}
                                href={link.href()}
                                className="text-xs font-semibold uppercase tracking-wide text-terroir-dark/80 transition hover:text-terroir-gold"
                            >
                                {link.label}
                            </Link>
                        ))}
                    </nav>

                    <form onSubmit={submitSearch} className="relative mx-4 hidden max-w-xs flex-1 xl:flex">
                        <input
                            type="search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Rechercher un produit..."
                            className="w-full rounded-[2px] border border-terroir-green/15 bg-terroir-cream/60 py-2 pl-4 pr-9 text-sm text-terroir-dark placeholder:text-terroir-dark/40 focus:border-terroir-green focus:outline-none"
                        />
                        <button type="submit" className="absolute right-2.5 top-1/2 -translate-y-1/2 text-lg text-terroir-dark/40" aria-label="Rechercher">
                            <span className="material-symbols-outlined">search</span>
                        </button>
                    </form>

                    <div className="flex items-center gap-3">
                        {user ? (
                            user.is_admin ? (
                                <a href={route('admin.dashboard')} className="hidden text-xs font-semibold uppercase tracking-wide text-terroir-dark/80 hover:text-terroir-gold sm:block">
                                    Administration
                                </a>
                            ) : (
                                <Link href={route('compte.index')} className="hidden text-xs font-semibold uppercase tracking-wide text-terroir-dark/80 hover:text-terroir-gold sm:block">
                                    Mon compte
                                </Link>
                            )
                        ) : (
                            <Link
                                href={route('login')}
                                className="btn-outline hidden !px-5 !py-2.5 !text-xs sm:inline-flex"
                            >
                                Se connecter
                            </Link>
                        )}

                        <Link
                            href={route('produits.index')}
                            className="btn-gold hidden !px-5 !py-2.5 !text-xs sm:inline-flex"
                        >
                            Commander
                        </Link>

                        {user && (
                            <Link
                                href={route('compte.favoris.index')}
                                className="hidden h-10 w-10 items-center justify-center rounded-[2px] text-xl text-terroir-dark/70 transition hover:bg-terroir-cream sm:inline-flex"
                                aria-label="Mes favoris"
                            >
                                <span className="material-symbols-outlined">favorite</span>
                            </Link>
                        )}

                        <Link
                            href={route('panier.index')}
                            className="relative inline-flex h-10 w-10 items-center justify-center rounded-[2px] bg-terroir-green text-xl text-terroir-cream transition hover:bg-terroir-dark"
                            aria-label="Panier"
                        >
                            <span className="material-symbols-outlined">shopping_cart</span>
                            {cartCount > 0 && (
                                <span className="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-terroir-gold text-[11px] font-bold text-terroir-dark">
                                    {cartCount}
                                </span>
                            )}
                        </Link>

                        <button
                            onClick={() => setMobileOpen((v) => !v)}
                            type="button"
                            className="inline-flex h-10 w-10 items-center justify-center rounded-[2px] text-2xl text-terroir-dark lg:hidden"
                            aria-label="Menu"
                        >
                            <span className="material-symbols-outlined">{mobileOpen ? 'close' : 'menu'}</span>
                        </button>
                    </div>
                </div>

                <AnimatePresence>
                    {mobileOpen && (
                        <motion.div
                            initial={{ opacity: 0, height: 0 }}
                            animate={{ opacity: 1, height: 'auto' }}
                            exit={{ opacity: 0, height: 0 }}
                            transition={{ duration: 0.2 }}
                            className="overflow-hidden border-t border-terroir-green/10 bg-white lg:hidden"
                        >
                            <nav className="flex flex-col gap-1 px-4 pb-6 pt-2">
                                {NAV_LINKS.map((link) => (
                                    <Link
                                        key={link.label}
                                        href={link.href()}
                                        className="rounded-[2px] px-3 py-3 text-sm font-medium text-terroir-dark transition-colors hover:bg-terroir-cream active:bg-terroir-cream"
                                    >
                                        {link.label}
                                    </Link>
                                ))}
                                {user ? (
                                    user.is_admin ? (
                                        <a href={route('admin.dashboard')} className="rounded-[2px] px-3 py-3 text-sm font-medium text-terroir-dark transition-colors hover:bg-terroir-cream active:bg-terroir-cream">
                                            Administration
                                        </a>
                                    ) : (
                                        <Link href={route('compte.index')} className="rounded-[2px] px-3 py-3 text-sm font-medium text-terroir-dark transition-colors hover:bg-terroir-cream active:bg-terroir-cream">
                                            Mon compte
                                        </Link>
                                    )
                                ) : (
                                    <Link
                                        href={route('login')}
                                        className="rounded-[2px] px-3 py-3 text-sm font-medium text-terroir-dark transition-colors hover:bg-terroir-cream active:bg-terroir-cream"
                                    >
                                        Connexion
                                    </Link>
                                )}
                            </nav>
                        </motion.div>
                    )}
                </AnimatePresence>
            </header>

            <main className="flex-1">
                <PullToRefresh>
                    <PageTransition>{children}</PageTransition>
                </PullToRefresh>
            </main>

            <SiteFooter siteName={siteName} logoUrl={logoUrl} address={address} showNewsletter={showNewsletter} />

            <ChatWidget />
        </div>
    );
}

function SiteFooter({ siteName, logoUrl, address, showNewsletter }) {
    return (
        <footer className="mt-16 bg-terroir-dark text-white/85">
            <div className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div className="grid gap-10 sm:grid-cols-2 md:grid-cols-4">
                    <div className="hidden sm:block">
                        <div className="flex items-center">
                            {logoUrl ? (
                                <img src={logoUrl} alt={siteName} width="32" height="32" className="mr-2 rounded-full object-cover" />
                            ) : (
                                <span className="material-symbols-outlined mr-1.5 text-2xl">eco</span>
                            )}
                            <span className="font-display text-lg font-semibold text-white">{siteName}</span>
                        </div>
                        <p className="mt-4 text-sm text-white/70">
                            Du terroir local à votre table. Nous soutenons l'économie locale en facilitant l'accès à des produits frais et authentiques du Sénégal.
                        </p>
                        <p className="mt-3 flex items-center gap-1 text-sm text-white/70">
                            <span className="material-symbols-outlined text-base">location_on</span> {address}
                        </p>
                    </div>

                    <div className="hidden sm:block">
                        <h4 className="text-xs font-semibold uppercase tracking-widest text-terroir-gold">Découvrir</h4>
                        <ul className="mt-3 space-y-2 text-sm text-white/70">
                            <li><Link href={route('produits.index')} className="hover:text-white">Nos produits</Link></li>
                            <li><Link href={route('producteurs.index')} className="hover:text-white">Nos producteurs</Link></li>
                            <li><Link href={route('pages.show', 'a-propos')} className="hover:text-white">À propos</Link></li>
                            <li><Link href={route('blog.index')} className="hover:text-white">Actualités &amp; recettes</Link></li>
                            <li><Link href={route('pages.show', 'devenir-fournisseur')} className="hover:text-white">Devenir fournisseur</Link></li>
                        </ul>
                    </div>

                    <div className="hidden sm:block">
                        <h4 className="text-xs font-semibold uppercase tracking-widest text-terroir-gold">Assistance</h4>
                        <ul className="mt-3 space-y-2 text-sm text-white/70">
                            <li><Link href={route('pages.show', 'faq')} className="hover:text-white">FAQ</Link></li>
                            <li><Link href={route('pages.show', 'livraison')} className="hover:text-white">Livraison</Link></li>
                            <li><Link href={route('pages.show', 'contact')} className="hover:text-white">Contact</Link></li>
                            <li><Link href={route('pages.show', 'mentions-legales')} className="hover:text-white">Mentions légales</Link></li>
                            <li><Link href={route('pages.show', 'politique-de-confidentialite')} className="hover:text-white">Confidentialité</Link></li>
                            <li><Link href={route('pages.show', 'conditions-generales')} className="hover:text-white">CGV</Link></li>
                        </ul>
                    </div>

                    {showNewsletter && (
                        <div>
                            <h4 className="text-xs font-semibold uppercase tracking-widest text-terroir-gold">Newsletter</h4>
                            <p className="mt-3 text-sm text-white/70">Recevez nos nouveautés et offres du terroir.</p>
                            <form action={route('newsletter.store')} method="POST" className="mt-3 space-y-2">
                                <input type="hidden" name="_token" value={document.querySelector('meta[name=csrf-token]')?.content} />
                                <input
                                    type="email"
                                    name="email"
                                    required
                                    placeholder="Votre e-mail"
                                    className="input w-full border-white/20 bg-white/10 text-white placeholder:text-white/50 focus:border-white"
                                />
                                <button type="submit" className="btn-primary w-full">S'inscrire</button>
                            </form>
                        </div>
                    )}
                </div>

                <div className="mt-12 flex flex-wrap items-center justify-between gap-2 border-t border-white/10 pt-8 text-xs text-white/50">
                    <p>&copy; {new Date().getFullYear()} {siteName} — L'authenticité du local. L'élégance du digital.</p>
                    <p>Tous droits réservés.</p>
                </div>
            </div>
        </footer>
    );
}
