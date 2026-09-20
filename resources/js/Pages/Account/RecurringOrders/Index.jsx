import { Head, Link, router } from '@inertiajs/react';
import SiteLayout from '../../../Layouts/SiteLayout';

export default function RecurringOrdersIndex({ recurringOrders }) {
    function toggle(id) {
        router.patch(route('compte.commandes-recurrentes.toggle', id), {}, { preserveScroll: true });
    }

    function destroy(id) {
        if (!confirm('Supprimer cette commande récurrente ?')) return;
        router.delete(route('compte.commandes-recurrentes.destroy', id), { preserveScroll: true });
    }

    return (
        <SiteLayout>
            <Head title="Commandes récurrentes — Central d'Achat" />

            <section className="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <span className="section-eyebrow">Espace professionnel</span>
                        <h1 className="section-title mt-2">Commandes récurrentes</h1>
                    </div>
                    <Link href={route('compte.commandes-recurrentes.create')} className="btn-primary">+ Programmer une commande</Link>
                </div>

                <Link href={route('compte.index')} className="mt-4 inline-block text-sm text-terroir-dark/60 hover:text-terroir-terracotta">
                    ← Retour à mon compte
                </Link>

                {recurringOrders.length === 0 ? (
                    <p className="mt-10 text-terroir-dark/60">Aucune commande récurrente programmée.</p>
                ) : (
                    <div className="mt-8 space-y-4">
                        {recurringOrders.map((ro) => (
                            <div key={ro.id} className="card p-5">
                                <div className="flex flex-wrap items-center justify-between gap-4">
                                    <div>
                                        <p className="font-semibold">{ro.frequency_label}</p>
                                        <p className="text-sm text-terroir-dark/50">Prochaine commande le {ro.next_run_date} — livraison {ro.city}</p>
                                    </div>
                                    <span
                                        className={
                                            'rounded-full px-4 py-1.5 text-xs font-semibold ' +
                                            (ro.status === 'active' ? 'bg-terroir-green/10 text-terroir-green' : 'bg-terroir-dark/10 text-terroir-dark/60')
                                        }
                                    >
                                        {ro.status === 'active' ? 'Active' : 'Suspendue'}
                                    </span>
                                </div>
                                <ul className="mt-3 text-xs text-terroir-dark/60">
                                    {ro.items.map((item) => (
                                        <li key={item.id}>{item.quantity} × {item.product_name}</li>
                                    ))}
                                </ul>
                                <div className="mt-4 flex gap-3">
                                    <button onClick={() => toggle(ro.id)} type="button" className="text-sm font-medium text-terroir-green hover:underline">
                                        {ro.status === 'active' ? 'Suspendre' : 'Réactiver'}
                                    </button>
                                    <button onClick={() => destroy(ro.id)} type="button" className="text-sm font-medium text-terroir-terracotta hover:underline">
                                        Supprimer
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </section>
        </SiteLayout>
    );
}
