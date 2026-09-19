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
            <Head title="Connexion — Central d'Achat" />

            <section className="mx-auto flex w-full max-w-md flex-col px-4 py-12 sm:px-6 lg:px-8">
                <h1 className="section-title text-center">Connexion</h1>

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

                <p className="mt-6 text-center text-sm text-terroir-dark/60">
                    Pas encore de compte ?{' '}
                    <Link href={route('register')} className="font-semibold text-terroir-green hover:underline">
                        Créer un compte
                    </Link>
                </p>
            </section>
        </GuestLayout>
    );
}
