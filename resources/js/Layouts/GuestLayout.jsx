import { Link, usePage } from '@inertiajs/react';
import PageTransition from '../Components/PageTransition';

export default function GuestLayout({ children }) {
    const { props } = usePage();
    const { name: siteName, logoUrl } = props.site ?? {};

    return (
        <div className="flex min-h-screen flex-col bg-terroir-cream">
            <header className="py-6">
                <div className="mx-auto flex max-w-7xl justify-center px-4 sm:px-6 lg:px-8">
                    <Link href={route('accueil')} className="flex items-center gap-2">
                        {logoUrl ? (
                            <img src={logoUrl} alt={siteName} className="h-9 w-9 rounded-full object-cover" />
                        ) : (
                            <span className="text-2xl">🌿</span>
                        )}
                        <span className="font-display text-xl font-semibold text-terroir-green">{siteName}</span>
                    </Link>
                </div>
            </header>

            <main className="flex flex-1 flex-col">
                <PageTransition className="flex flex-1 flex-col">{children}</PageTransition>
            </main>

            <footer className="py-8 text-center text-xs text-terroir-dark/40">
                &copy; {new Date().getFullYear()} {siteName}. Tous droits réservés.
            </footer>
        </div>
    );
}
