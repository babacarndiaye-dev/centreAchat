import { Head, useForm, usePage } from '@inertiajs/react';
import { useMemo } from 'react';
import SiteLayout from '../../Layouts/SiteLayout';

function formatFcfa(amount) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(amount)) + ' FCFA';
}

export default function CheckoutIndex({ items, subtotal, deliveryZones, selectedZoneId, taxRate }) {
    const { props } = usePage();
    const user = props.auth?.user;

    const { data, setData, post, processing, errors } = useForm({
        customer_name: user?.name ?? '',
        customer_phone: user?.phone ?? '',
        customer_email: user?.email ?? '',
        delivery_address: '',
        delivery_zone_id: selectedZoneId ? String(selectedZoneId) : '',
        payment_method: 'especes',
    });

    const fee = useMemo(() => {
        const zone = deliveryZones.find((z) => z.id === data.delivery_zone_id);
        if (!zone) return 0;
        if (zone.free_above !== null && subtotal >= zone.free_above) return 0;
        return zone.fee;
    }, [deliveryZones, data.delivery_zone_id, subtotal]);

    const tax = useMemo(() => Math.round(subtotal * ((taxRate?.rate ?? 0) / 100)), [subtotal, taxRate]);
    const total = subtotal + fee + tax;

    function submit(e) {
        e.preventDefault();
        post(route('commande.store'));
    }

    return (
        <SiteLayout>
            <Head title="Finaliser la commande — DIABA HOTEL" />

            <section className="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
                <h1 className="section-title text-center">Finaliser votre commande</h1>

                <div className="mt-10 grid gap-10 lg:grid-cols-3">
                    <form onSubmit={submit} className="card space-y-5 p-8 lg:col-span-2">
                        <div className="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label className="label" htmlFor="customer_name">Nom complet</label>
                                <input
                                    type="text"
                                    id="customer_name"
                                    value={data.customer_name}
                                    onChange={(e) => setData('customer_name', e.target.value)}
                                    required
                                    className="input"
                                />
                                {errors.customer_name && <p className="mt-1 text-xs text-terroir-terracotta">{errors.customer_name}</p>}
                            </div>
                            <div>
                                <label className="label" htmlFor="customer_phone">Téléphone</label>
                                <input
                                    type="text"
                                    id="customer_phone"
                                    value={data.customer_phone}
                                    onChange={(e) => setData('customer_phone', e.target.value)}
                                    required
                                    className="input"
                                />
                                {errors.customer_phone && <p className="mt-1 text-xs text-terroir-terracotta">{errors.customer_phone}</p>}
                            </div>
                        </div>

                        <div>
                            <label className="label" htmlFor="customer_email">E-mail (optionnel)</label>
                            <input
                                type="email"
                                id="customer_email"
                                value={data.customer_email}
                                onChange={(e) => setData('customer_email', e.target.value)}
                                className="input"
                            />
                            {errors.customer_email && <p className="mt-1 text-xs text-terroir-terracotta">{errors.customer_email}</p>}
                        </div>

                        <div>
                            <label className="label" htmlFor="delivery_address">Adresse de livraison</label>
                            <textarea
                                id="delivery_address"
                                rows="3"
                                value={data.delivery_address}
                                onChange={(e) => setData('delivery_address', e.target.value)}
                                required
                                className="input"
                            />
                            {errors.delivery_address && <p className="mt-1 text-xs text-terroir-terracotta">{errors.delivery_address}</p>}
                        </div>

                        {deliveryZones.length > 0 && (
                            <div>
                                <label className="label" htmlFor="delivery_zone_id">Zone de livraison</label>
                                <select
                                    id="delivery_zone_id"
                                    value={data.delivery_zone_id}
                                    onChange={(e) => setData('delivery_zone_id', e.target.value)}
                                    className="input"
                                >
                                    {deliveryZones.map((zone) => (
                                        <option key={zone.id} value={zone.id}>
                                            {zone.name} — {zone.fee > 0 ? formatFcfa(zone.fee) : 'Gratuit'}
                                        </option>
                                    ))}
                                </select>
                                {errors.delivery_zone_id && <p className="mt-1 text-xs text-terroir-terracotta">{errors.delivery_zone_id}</p>}
                            </div>
                        )}

                        <button type="submit" disabled={processing} className="btn-primary w-full justify-center disabled:opacity-50">
                            Confirmer ma commande
                        </button>
                    </form>

                    <div className="card h-fit p-6">
                        <h2 className="font-display text-lg font-semibold">Récapitulatif</h2>
                        <ul className="mt-4 space-y-3 text-sm">
                            {items.map((item) => (
                                <li key={item.product.id} className="flex justify-between gap-3">
                                    <span className="text-terroir-dark/70">{item.quantity} × {item.product.name}</span>
                                    <span className="font-medium">{formatFcfa(item.total)}</span>
                                </li>
                            ))}
                        </ul>
                        <div className="mt-6 space-y-2 border-t border-terroir-green/10 pt-4 text-sm">
                            <div className="flex justify-between"><span className="text-terroir-dark/60">Sous-total</span><span>{formatFcfa(subtotal)}</span></div>
                            {taxRate && (
                                <div className="flex justify-between"><span className="text-terroir-dark/60">{taxRate.name} ({taxRate.rate}%)</span><span>{formatFcfa(tax)}</span></div>
                            )}
                            <div className="flex justify-between"><span className="text-terroir-dark/60">Livraison</span><span>{fee > 0 ? formatFcfa(fee) : 'Offerte'}</span></div>
                            <div className="flex justify-between text-base font-bold text-terroir-green"><span>Total</span><span>{formatFcfa(total)}</span></div>
                        </div>
                    </div>
                </div>
            </section>
        </SiteLayout>
    );
}
