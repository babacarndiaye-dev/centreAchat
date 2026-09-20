import { Head, useForm, router } from '@inertiajs/react';
import AdminLayout from '../../../Layouts/AdminLayout';

function defaultValidUntil() {
    const d = new Date();
    d.setDate(d.getDate() + 7);
    return d.toISOString().slice(0, 10);
}

export default function Show({ quote }) {
    const { data, setData, post, processing, errors } = useForm({
        unit_price: Object.fromEntries(quote.items.map((item) => [item.id, item.suggested_price])),
        valid_until: defaultValidUntil(),
    });

    function handleSend(e) {
        e.preventDefault();
        post(route('admin.devis.send', quote.id));
    }

    function handleConvert() {
        router.post(route('admin.devis.convert', quote.id));
    }

    return (
        <AdminLayout title={quote.quote_number}>
            <Head title={`${quote.quote_number} — Devis`} />

            <div className="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 className="font-display text-2xl font-semibold">{quote.quote_number}</h2>
                    <p className="text-sm text-terroir-dark/50">
                        Demandé par {quote.user.name} ({quote.user.company_name ?? quote.user.email})
                    </p>
                </div>
                <span className={quote.status_badge_class + ' px-4 py-1.5 text-sm'}>{quote.status_label}</span>
            </div>

            {quote.notes && <div className="admin-card mt-6 text-sm">{quote.notes}</div>}

            <div className="admin-card mt-6">
                <h3 className="font-display text-base font-semibold">Articles demandés</h3>

                {quote.editable ? (
                    <form onSubmit={handleSend} className="mt-3">
                        <table className="admin-table">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th className="text-right">Quantité</th>
                                    <th className="text-right">Prix unitaire proposé</th>
                                </tr>
                            </thead>
                            <tbody>
                                {quote.items.map((item) => (
                                    <tr key={item.id}>
                                        <td>{item.product_name}</td>
                                        <td className="text-right">{item.quantity}</td>
                                        <td className="text-right">
                                            <input
                                                type="number"
                                                step="0.01"
                                                value={data.unit_price[item.id]}
                                                onChange={(e) => setData('unit_price', { ...data.unit_price, [item.id]: e.target.value })}
                                                required
                                                className="input w-32 text-right"
                                            />
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>

                        <div className="mt-4 max-w-xs">
                            <label className="label" htmlFor="valid_until">Valable jusqu'au</label>
                            <input
                                type="date"
                                id="valid_until"
                                value={data.valid_until}
                                onChange={(e) => setData('valid_until', e.target.value)}
                                className="input"
                            />
                        </div>

                        <button type="submit" disabled={processing} className="btn-primary mt-4 disabled:opacity-50">
                            Envoyer le devis au client
                        </button>
                    </form>
                ) : (
                    <>
                        <table className="admin-table mt-3">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th className="text-right">Quantité</th>
                                    <th className="text-right">Prix unitaire</th>
                                    <th className="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                {quote.items.map((item) => (
                                    <tr key={item.id}>
                                        <td>{item.product_name}</td>
                                        <td className="text-right">{item.quantity}</td>
                                        <td className="text-right">{item.unit_price.toLocaleString('fr-FR')} FCFA</td>
                                        <td className="text-right font-semibold">{item.total.toLocaleString('fr-FR')} FCFA</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                        <div className="mt-4 text-right text-base font-bold text-terroir-green">
                            Total : {quote.total.toLocaleString('fr-FR')} FCFA
                        </div>

                        {quote.status === 'accepte' && (
                            <button type="button" onClick={handleConvert} className="btn-primary mt-4">
                                Convertir en commande
                            </button>
                        )}
                    </>
                )}
            </div>
        </AdminLayout>
    );
}
