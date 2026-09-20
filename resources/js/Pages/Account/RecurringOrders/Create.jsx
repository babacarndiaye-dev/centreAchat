import { Head, Link, useForm } from '@inertiajs/react';
import SiteLayout from '../../../Layouts/SiteLayout';

const FREQUENCIES = [
    { value: 'hebdomadaire', label: 'Chaque semaine' },
    { value: 'bimensuelle', label: 'Toutes les deux semaines' },
    { value: 'mensuelle', label: 'Chaque mois' },
];

export default function RecurringOrdersCreate({ products, canPayOnCredit }) {
    const { data, setData, post, processing, errors } = useForm({
        frequency: 'hebdomadaire',
        payment_method: 'especes',
        delivery_address: '',
        city: 'Mbour',
        notes: '',
        products: Object.fromEntries(products.map((p) => [p.id, 0])),
    });

    function setQuantity(productId, quantity) {
        setData('products', { ...data.products, [productId]: quantity });
    }

    function submit(e) {
        e.preventDefault();
        post(route('compte.commandes-recurrentes.store'));
    }

    return (
        <SiteLayout>
            <Head title="Programmer une commande récurrente — Central d'Achat" />

            <section className="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
                <span className="section-eyebrow">Espace professionnel</span>
                <h1 className="section-title mt-2">Programmer une commande récurrente</h1>
                <p className="mt-3 text-terroir-dark/70">Automatisez votre réapprovisionnement à fréquence régulière.</p>

                <form onSubmit={submit} className="card mt-8 p-8">
                    <div className="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label className="label" htmlFor="frequency">Fréquence</label>
                            <select
                                id="frequency"
                                value={data.frequency}
                                onChange={(e) => setData('frequency', e.target.value)}
                                required
                                className="input"
                            >
                                {FREQUENCIES.map((f) => (
                                    <option key={f.value} value={f.value}>{f.label}</option>
                                ))}
                            </select>
                        </div>
                        <div>
                            <label className="label" htmlFor="payment_method">Mode de paiement</label>
                            <select
                                id="payment_method"
                                value={data.payment_method}
                                onChange={(e) => setData('payment_method', e.target.value)}
                                required
                                className="input"
                            >
                                <option value="especes">Espèces à la livraison</option>
                                <option value="wave">Wave</option>
                                <option value="orange_money">Orange Money</option>
                                {canPayOnCredit && <option value="credit">Paiement à crédit</option>}
                            </select>
                            {errors.payment_method && <p className="mt-1 text-xs text-terroir-terracotta">{errors.payment_method}</p>}
                        </div>
                    </div>

                    <div className="mt-5">
                        <label className="label" htmlFor="delivery_address">Adresse de livraison</label>
                        <textarea
                            id="delivery_address"
                            rows="2"
                            value={data.delivery_address}
                            onChange={(e) => setData('delivery_address', e.target.value)}
                            required
                            className="input"
                        />
                        {errors.delivery_address && <p className="mt-1 text-xs text-terroir-terracotta">{errors.delivery_address}</p>}
                    </div>

                    <div className="mt-5">
                        <label className="label" htmlFor="city">Ville</label>
                        <input
                            type="text"
                            id="city"
                            value={data.city}
                            onChange={(e) => setData('city', e.target.value)}
                            required
                            className="input"
                        />
                        {errors.city && <p className="mt-1 text-xs text-terroir-terracotta">{errors.city}</p>}
                    </div>

                    <div className="mt-6 border-t border-terroir-green/10 pt-6">
                        <h3 className="font-display text-base font-semibold">Produits à commander automatiquement</h3>

                        {errors.products && <p className="mt-2 text-xs text-terroir-terracotta">{errors.products}</p>}

                        <div className="mt-4 max-h-[380px] space-y-2 overflow-y-auto pr-2">
                            {products.map((product) => (
                                <div key={product.id} className="flex items-center justify-between gap-4 rounded-lg bg-terroir-cream/60 px-4 py-2.5">
                                    <p className="text-sm font-medium">{product.name}</p>
                                    <input
                                        type="number"
                                        value={data.products[product.id] ?? 0}
                                        min="0"
                                        onChange={(e) => setQuantity(product.id, Number(e.target.value))}
                                        className="input w-28 text-right"
                                    />
                                </div>
                            ))}
                        </div>
                    </div>

                    <div className="mt-6">
                        <label className="label" htmlFor="notes">Notes (optionnel)</label>
                        <textarea
                            id="notes"
                            rows="2"
                            value={data.notes}
                            onChange={(e) => setData('notes', e.target.value)}
                            className="input"
                        />
                    </div>

                    <div className="mt-8 flex gap-3">
                        <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Programmer</button>
                        <Link href={route('compte.commandes-recurrentes.index')} className="btn-outline">Annuler</Link>
                    </div>
                </form>
            </section>
        </SiteLayout>
    );
}
