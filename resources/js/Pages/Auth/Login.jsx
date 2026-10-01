import { Head, Link, useForm } from '@inertiajs/react';
import GuestLayout from '../../Layouts/GuestLayout';

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    function submit(e) {
        e.preventDefault();
        post(route('login'));
    }

    return (
        <GuestLayout>
            <Head title="Connexion — DIABA HOTEL Produits du Sénégal (D.H.P.S)" />

            <section className="mx-auto flex w-full max-w-5xl flex-1 flex-col lg:my-auto lg:flex-row">
                <div className="relative hidden overflow-hidden rounded-xl2 bg-terroir-green lg:block lg:w-2/5">
                    <img
                        src="/images/mbour-terroir-nobg.png"
                        alt="DIABA HOTEL Produits du Sénégal (D.H.P.S) — produits du terroir sénégalais"
                        className="absolute inset-0 h-full w-full object-cover object-top"
                    />
                </div>

                <div className="flex w-full flex-col px-4 py-12 sm:px-6 lg:w-3/5 lg:justify-center lg:px-12 lg:py-16">
                    <h1 className="section-title text-center lg:text-left">Connexion</h1>

                    <form onSubmit={submit} className="card mt-10 space-y-5 p-8">
                        <div>
                            <label className="label" htmlFor="email">E-mail</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                required
                                autoFocus
                                className="input"
                            />
                            {errors.email && <p className="mt-1 text-xs text-terroir-terracotta">{errors.email}</p>}
                        </div>
                        <div>
                            <label className="label" htmlFor="password">Mot de passe</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                required
                                className="input"
                            />
                        </div>
                        <label className="flex items-center gap-2 text-sm text-terroir-dark/70">
                            <input
                                type="checkbox"
                                checked={data.remember}
                                onChange={(e) => setData('remember', e.target.checked)}
                                className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green"
                            />
                            Se souvenir de moi
                        </label>
                        <button type="submit" disabled={processing} className="btn-primary w-full justify-center disabled:opacity-60">
                            Se connecter
                        </button>
                    </form>

                    <p className="mt-6 text-center text-sm text-terroir-dark/60 lg:text-left">
                        Pas encore de compte ?{' '}
                        <Link href={route('register')} className="font-semibold text-terroir-green hover:underline">
                            Créer un compte
                        </Link>
                    </p>
                </div>
            </section>
        </GuestLayout>
    );
}
