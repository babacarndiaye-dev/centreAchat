import { Head, Link, useForm } from '@inertiajs/react';
import GuestLayout from '../../Layouts/GuestLayout';

const B2B_TYPES = ['professionnel', 'hotel', 'restaurant', 'entreprise', 'institution', 'revendeur'];

const USER_TYPES = {
    particulier: 'Particulier',
    professionnel: 'Professionnel',
    hotel: 'Hôtel',
    restaurant: 'Restaurant',
    entreprise: 'Entreprise',
    institution: 'Institution',
    touriste: 'Touriste',
    revendeur: 'Revendeur',
};

export default function Register({ initialType = 'particulier' }) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        phone: '',
        email: '',
        user_type: initialType,
        company_name: '',
        business_registration_number: '',
        password: '',
        password_confirmation: '',
    });

    const isB2b = B2B_TYPES.includes(data.user_type);

    function submit(e) {
        e.preventDefault();
        post(route('register'));
    }

    return (
        <GuestLayout>
            <Head title="Créer un compte — DIABA HOTEL Produits du Sénégal (D.H.P.S)" />

            <section className="mx-auto flex w-full max-w-6xl flex-1 flex-col lg:my-auto lg:flex-row">
                <div className="relative hidden overflow-hidden rounded-xl2 bg-terroir-green lg:block lg:w-2/5">
                    <img
                        src="/images/mbour-terroir-nobg.png"
                        alt="DIABA HOTEL Produits du Sénégal (D.H.P.S) — produits du terroir sénégalais"
                        className="absolute inset-0 h-full w-full object-cover object-top"
                    />
                </div>

                <div className="flex w-full flex-col px-4 py-12 sm:px-6 lg:w-3/5 lg:justify-center lg:px-12 lg:py-16">
                <h1 className="section-title text-center lg:text-left">Créer un compte</h1>
                <p className="mt-2 text-center text-sm text-terroir-dark/60 lg:text-left">
                    Particulier, hôtel, restaurant, entreprise... rejoignez DIABA HOTEL Produits du Sénégal (D.H.P.S).
                </p>

                <form onSubmit={submit} className="card mt-10 space-y-5 p-8">
                    <div className="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label className="label" htmlFor="name">Nom complet</label>
                            <input
                                type="text"
                                id="name"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                required
                                autoFocus
                                className="input"
                            />
                            {errors.name && <p className="mt-1 text-xs text-terroir-terracotta">{errors.name}</p>}
                        </div>
                        <div>
                            <label className="label" htmlFor="phone">Téléphone</label>
                            <input
                                type="text"
                                id="phone"
                                value={data.phone}
                                onChange={(e) => setData('phone', e.target.value)}
                                className="input"
                            />
                        </div>
                    </div>

                    <div>
                        <label className="label" htmlFor="email">E-mail</label>
                        <input
                            type="email"
                            id="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            required
                            className="input"
                        />
                        {errors.email && <p className="mt-1 text-xs text-terroir-terracotta">{errors.email}</p>}
                    </div>

                    <div>
                        <label className="label" htmlFor="user_type">Type de compte</label>
                        <select
                            id="user_type"
                            value={data.user_type}
                            onChange={(e) => setData('user_type', e.target.value)}
                            required
                            className="input"
                        >
                            {Object.entries(USER_TYPES).map(([value, label]) => (
                                <option key={value} value={value}>{label}</option>
                            ))}
                        </select>
                    </div>

                    {isB2b && (
                        <div className="rounded-xl bg-terroir-cream/60 p-4 text-xs text-terroir-dark/60">
                            En tant que compte professionnel, votre demande sera examinée par notre équipe avant
                            validation. Une fois validé, vous accédez aux tarifs professionnels/de gros, aux devis et
                            aux commandes récurrentes.
                        </div>
                    )}

                    <div>
                        <label className="label" htmlFor="company_name">Nom de l'entreprise / structure (optionnel)</label>
                        <input
                            type="text"
                            id="company_name"
                            value={data.company_name}
                            onChange={(e) => setData('company_name', e.target.value)}
                            className="input"
                        />
                    </div>

                    {isB2b && (
                        <div>
                            <label className="label" htmlFor="business_registration_number">Numéro NINEA / RCCM (optionnel)</label>
                            <input
                                type="text"
                                id="business_registration_number"
                                value={data.business_registration_number}
                                onChange={(e) => setData('business_registration_number', e.target.value)}
                                className="input"
                            />
                            {errors.business_registration_number && (
                                <p className="mt-1 text-xs text-terroir-terracotta">{errors.business_registration_number}</p>
                            )}
                        </div>
                    )}

                    <div className="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label className="label" htmlFor="password">Mot de passe</label>
                            <input
                                type="password"
                                id="password"
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                required
                                className="input"
                            />
                            {errors.password && <p className="mt-1 text-xs text-terroir-terracotta">{errors.password}</p>}
                        </div>
                        <div>
                            <label className="label" htmlFor="password_confirmation">Confirmer</label>
                            <input
                                type="password"
                                id="password_confirmation"
                                value={data.password_confirmation}
                                onChange={(e) => setData('password_confirmation', e.target.value)}
                                required
                                className="input"
                            />
                        </div>
                    </div>

                    <button type="submit" disabled={processing} className="btn-primary w-full justify-center disabled:opacity-60">
                        Créer mon compte
                    </button>
                </form>

                <p className="mt-6 text-center text-sm text-terroir-dark/60 lg:text-left">
                    Déjà un compte ?{' '}
                    <Link href={route('login')} className="font-semibold text-terroir-green hover:underline">
                        Se connecter
                    </Link>
                </p>
                </div>
            </section>
        </GuestLayout>
    );
}
