import { usePage } from '@inertiajs/react';

export default function Ping({ generatedAt }) {
    const { props } = usePage();

    return (
        <div className="flex min-h-screen items-center justify-center bg-terroir-cream p-6">
            <div className="admin-card max-w-md space-y-3 rounded-xl2 bg-white p-6 shadow-soft">
                <h1 className="font-display text-xl font-semibold text-terroir-dark">
                    Pipeline Inertia + React OK
                </h1>
                <p className="text-sm text-terroir-dark/70">Généré côté serveur à {generatedAt}.</p>
                <pre className="overflow-x-auto rounded-lg bg-terroir-cream/60 p-3 text-xs text-terroir-dark/70">
                    {JSON.stringify({ auth: props.auth, cart: props.cart, flash: props.flash }, null, 2)}
                </pre>
            </div>
        </div>
    );
}
