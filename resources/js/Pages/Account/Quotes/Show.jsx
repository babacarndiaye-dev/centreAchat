import { Head, Link, router } from '@inertiajs/react';
import SiteLayout from '../../../Layouts/SiteLayout';

function formatFcfa(amount) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(amount)) + ' FCFA';
}

export default function QuotesShow({ quote }) {
    function accept() {
        router.patch(route('compte.devis.accept', quote.id));
    }

    function refuse() {
        router.patch(route('compte.devis.refuse', quote.id));
    }

    return (
        <SiteLayout>
            <Head title={`${quote.quote_number} — DIABA HOTEL`} />

            <section className="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
                <Link href={route('compte.devis.index')} className="inline-flex items-center gap-1 text-sm text-terroir-dark/60 hover:text-terroir-terracotta"><span className="material-symbols-outlined text-base">arrow_back</span> Mes devis</Link>

                <div className="mt-4 flex flex-wrap items-center justify-between gap-4">
                    <h1 className="section-title">{quote.quote_number}</h1>
                    <span className="rounded-full bg-terroir-cream px-4 py-1.5 text-sm font-semibold text-terroir-green">{quote.status_label}</span>
                </div>

                <div className="card mt-8 p-8">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="text-left text-terroir-dark/50">
                                <th className="pb-2">Produit</th>
                                <th className="pb-2 text-right">Quantité</th>
                                <th className="pb-2 text-right">Prix unitaire</th>
                                <th className="pb-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-terroir-green/10">
                            {quote.items.map((item) => (
                                <tr key={item.id}>
                                    <td className="py-2">{item.product_name}</td>
                                    <td className="py-2 text-right">{item.quantity}</td>
                                    <td className="py-2 text-right">{item.unit_price ? formatFcfa(item.unit_price) : 'À définir'}</td>
                                    <td className="py-2 text-right font-medium">{item.total !== null ? formatFcfa(item.total) : '—'}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>

                    {quote.total > 0 && (
                        <div className="mt-4 ml-auto max-w-xs text-right text-base font-bold text-terroir-green">
                            Total : {formatFcfa(quote.total)}
                        </div>
                    )}

                    {quote.valid_until && (
                        <p className="mt-2 text-right text-xs text-terroir-dark/50">Valable jusqu'au {quote.valid_until}</p>
                    )}

                    {quote.notes && <div className="mt-6 rounded-lg bg-terroir-cream p-4 text-sm">{quote.notes}</div>}

                    {quote.status === 'envoye' && (
                        <div className="mt-8 flex gap-3">
                            <button onClick={accept} type="button" className="btn-primary">Accepter le devis</button>
                            <button onClick={refuse} type="button" className="btn-outline">Refuser</button>
                        </div>
                    )}
                    {quote.status === 'en_attente' && (
                        <p className="mt-8 text-sm text-terroir-dark/60">Votre demande est en cours de traitement, notre équipe vous enverra une offre tarifaire prochainement.</p>
                    )}
                    {quote.status === 'accepte' && (
                        <p className="mt-8 text-sm text-terroir-green">Devis accepté — notre équipe vous contactera pour finaliser la commande.</p>
                    )}
                </div>
            </section>
        </SiteLayout>
    );
}
