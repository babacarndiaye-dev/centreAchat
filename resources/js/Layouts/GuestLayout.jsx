import { Link, usePage } from '@inertiajs/react';
import BrandName from '../Components/BrandName';
import PageTransition from '../Components/PageTransition';

export default function GuestLayout({ children }) {
    const { props } = usePage();
    const { name: siteName, logoUrl } = props.site ?? {};

    return (
        <div className="flex min-h-screen flex-col bg-terroir-cream">
            <header className="py-6">
                <div className="mx-auto flex max-w-7xl justify-center px-4 sm:px-6 lg:px-8">
                    <Link href={route('accueil')} className="flex items-center gap-2">
                        <img src={logoUrl || '/images/logo.svg'} alt="" className="h-12 w-auto object-contain" />
                        <BrandName name={siteName} size="lg" />
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
