import { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

function OrderAssistant({ order, flags }) {
    const [state, setState] = useState({
        note: null,
        customerMessage: null,
        sent: false,
        loading: false,
        checked: false,
        available: true,
    });

    async function fetchSuggestion() {
        setState((s) => ({ ...s, loading: true }));
        try {
            const res = await fetch(route('admin.commandes.suggestion', order.id), {
                headers: { Accept: 'application/json' },
            });
            const data = await res.json();
            setState({
                available: data.available,
                note: data.note,
                customerMessage: data.customer_message,
                sent: data.sent,
                checked: true,
                loading: false,
            });
        } catch {
            setState((s) => ({ ...s, loading: false, checked: true, available: false }));
        }
    }

    return (
        <div className="admin-card mb-6">
            <div className="flex items-center justify-between">
                <h2 className="flex items-center gap-1.5 font-display text-lg font-semibold">
                    <span className="material-symbols-outlined">psychology</span> Assistant commande
                </h2>
                <button type="button" onClick={fetchSuggestion} disabled={state.loading} className="text-sm font-semibold text-terroir-green disabled:opacity-50">
                    {state.loading ? 'Analyse en cours…' : state.checked ? 'Réanalyser' : "Analyser avec l'IA"}
                </button>
            </div>
            <p className="mt-1.5 text-sm text-terroir-dark/50">
                Si l'IA juge un message au client utile, il est envoyé automatiquement via le chat — sans validation supplémentaire.
            </p>

            {flags.length > 0 ? (
                <ul className="mt-4 flex flex-col gap-2">
                    {flags.map((flag, i) => (
                        <li
                            key={i}
                            className={
                                'flex items-start gap-2 rounded-lg px-3 py-2 text-sm ' +
                                (flag.severity === 'critical' ? 'bg-terroir-terracotta/10 text-terroir-terracotta' : 'bg-terroir-gold/15 text-terroir-brown')
                            }
                        >
                            <span className="material-symbols-outlined text-base">{flag.severity === 'critical' ? 'warning' : 'visibility'}</span>
                            <span>{flag.label}</span>
                        </li>
                    ))}
                </ul>
            ) : (
                <p className="mt-1.5 text-sm text-terroir-dark/50">Aucune alerte détectée sur cette commande.</p>
            )}

            <AnimatePresence>
                {state.checked && (
                    <motion.div
                        initial={{ opacity: 0, height: 0 }}
                        animate={{ opacity: 1, height: 'auto' }}
                        exit={{ opacity: 0, height: 0 }}
                        className="mt-4 flex flex-col gap-3 overflow-hidden border-t border-terroir-dark/10 pt-4"
                    >
                        {state.available && state.note && (
                            <div>
                                <p className="text-[11px] font-semibold uppercase tracking-wide text-terroir-dark/40">Note pour l'équipe</p>
                                <p className="mt-1.5 whitespace-pre-line text-sm">{state.note}</p>
                            </div>
                        )}
                        {state.sent && state.customerMessage && (
                            <div className="rounded-lg bg-terroir-green/10 px-3 py-2.5">
                                <p className="flex items-center gap-1 text-[11px] font-semibold uppercase tracking-wide text-terroir-green">
                                    <span className="material-symbols-outlined is-filled text-sm">check_circle</span> Message envoyé au client
                                </p>
                                <p className="mt-1.5 whitespace-pre-line text-sm">{state.customerMessage}</p>
                            </div>
                        )}
                        {!state.sent && state.customerMessage && (
                            <div className="rounded-lg bg-terroir-gold/15 px-3 py-2.5">
                                <p className="text-[11px] font-semibold uppercase tracking-wide text-terroir-brown">Message rédigé mais non envoyé</p>
                                <p className="mt-1.5 whitespace-pre-line text-sm">{state.customerMessage}</p>
                                <p className="mt-1.5 text-sm text-terroir-dark/50">
                                    Ce client n'a pas de compte associé à la commande — l'envoi automatique via le chat n'est possible que pour les clients connectés.
                                </p>
                            </div>
                        )}
                        {!state.available && (
                            <p className="text-sm text-terroir-dark/50">
                                Suggestion IA indisponible pour le moment (aucune clé configurée ou service temporairement inaccessible) —
                                les alertes ci-dessus restent fiables, elles ne dépendent pas de l'IA.
                            </p>
                        )}
                    </motion.div>
                )}
            </AnimatePresence>
        </div>
    );
}

function PaymentForm({ order, paymentAccounts }) {
    const { data, setData, post, processing, errors } = useForm({
        payment_account_id: '',
        amount: order.amount_due,
    });

    function handleSubmit(e) {
        e.preventDefault();
        post(route('admin.commandes.payment', order.id));
    }

    return (
        <div className="admin-card mt-6">
            <h2 className="font-display text-lg font-semibold">Enregistrer un paiement</h2>
            <p className="mt-1.5 text-sm text-terroir-dark/50">Génère automatiquement l'écriture comptable d'encaissement.</p>
            <form onSubmit={handleSubmit} className="mt-4 flex flex-col gap-3">
                <select
                    value={data.payment_account_id}
                    onChange={(e) => setData('payment_account_id', e.target.value)}
                    required
                    className="input"
                >
                    <option value="">Compte de paiement</option>
                    {paymentAccounts.map((account) => (
                        <option key={account.id} value={account.id}>{account.name}</option>
                    ))}
                </select>
                {errors.payment_account_id && <p className="text-xs text-terroir-terracotta">{errors.payment_account_id}</p>}
                <input
                    type="number"
                    step="0.01"
                    value={data.amount}
                    onChange={(e) => setData('amount', e.target.value)}
                    max={order.total}
                    required
                    className="input"
                />
                {errors.amount && <p className="text-xs text-terroir-terracotta">{errors.amount}</p>}
                <button type="submit" disabled={processing} className="btn-primary w-full justify-center disabled:opacity-50">Encaisser</button>
            </form>
        </div>
    );
}

function StatusForm({ order, statuses }) {
    const { data, setData, patch, processing } = useForm({ status: order.status });

    function handleSubmit(e) {
        e.preventDefault();
        patch(route('admin.commandes.status', order.id));
    }

    return (
        <div className="admin-card mt-6">
            <h2 className="font-display text-lg font-semibold">Statut de la commande</h2>
            <form onSubmit={handleSubmit} className="mt-4 flex flex-col gap-3">
                <select value={data.status} onChange={(e) => setData('status', e.target.value)} className="input">
                    {Object.entries(statuses).map(([value, label]) => (
                        <option key={value} value={value}>{label}</option>
                    ))}
                </select>
                <button type="submit" disabled={processing} className="btn-primary w-full justify-center disabled:opacity-50">Mettre à jour</button>
            </form>
        </div>
    );
}

export default function Show({ order, paymentAccounts, flags, statuses }) {
    return (
        <AdminLayout title={`Commande ${order.order_number}`}>
            <Head title={`Commande ${order.order_number} — Administration`} />

            <OrderAssistant order={order} flags={flags} />

            <div className="grid gap-6 lg:grid-cols-3">
                <div className="lg:col-span-2">
                    <div className="admin-card">
                        <h2 className="font-display text-lg font-semibold">Articles</h2>
                        <table className="admin-table mt-3">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th className="text-right">Prix unitaire</th>
                                    <th className="text-right">Quantité</th>
                                    <th className="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                {order.items.map((item) => (
                                    <tr key={item.id}>
                                        <td>
                                            {item.product_name}
                                            {item.price_tier_label && (
                                                <span className="ml-1 rounded-full bg-terroir-gold/20 px-2 py-0.5 text-[10px] font-medium text-terroir-brown">
                                                    {item.price_tier_label}
                                                </span>
                                            )}
                                        </td>
                                        <td className="text-right">{item.unit_price.toLocaleString('fr-FR')} FCFA</td>
                                        <td className="text-right">{item.quantity}</td>
                                        <td className="text-right font-semibold">{item.total.toLocaleString('fr-FR')} FCFA</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>

                        <div className="ml-auto mt-4 flex max-w-xs flex-col gap-2 border-t border-terroir-dark/10 pt-4 text-sm">
                            <div className="flex justify-between"><span className="text-terroir-dark/60">Sous-total</span><span>{order.subtotal.toLocaleString('fr-FR')} FCFA</span></div>
                            <div className="flex justify-between"><span className="text-terroir-dark/60">Livraison</span><span>{order.delivery_fee.toLocaleString('fr-FR')} FCFA</span></div>
                            <div className="flex justify-between text-base font-bold text-terroir-green"><span>Total</span><span>{order.total.toLocaleString('fr-FR')} FCFA</span></div>
                        </div>

                        {order.notes && (
                            <div className="mt-4 rounded-lg bg-terroir-cream p-4 text-sm">
                                <strong>Notes :</strong> {order.notes}
                            </div>
                        )}
                    </div>
                </div>

                <div>
                    <div className="admin-card">
                        <h2 className="font-display text-lg font-semibold">Client</h2>
                        <dl className="mt-3 flex flex-col gap-2 text-sm">
                            <div><dt className="text-terroir-dark/50">Nom</dt><dd className="font-semibold">{order.customer_name}</dd></div>
                            <div><dt className="text-terroir-dark/50">Téléphone</dt><dd className="font-semibold">{order.customer_phone}</dd></div>
                            {order.customer_email && (
                                <div><dt className="text-terroir-dark/50">E-mail</dt><dd className="font-semibold">{order.customer_email}</dd></div>
                            )}
                            <div><dt className="text-terroir-dark/50">Adresse</dt><dd className="font-semibold">{order.delivery_address}, {order.city}</dd></div>
                            {order.hotel_name && (
                                <div>
                                    <dt className="flex items-center gap-1 text-terroir-dark/50"><span className="material-symbols-outlined text-base">luggage</span> Livraison hôtel</dt>
                                    <dd className="font-semibold">{order.hotel_name}{order.room_number ? ` — Chambre ${order.room_number}` : ''}</dd>
                                </div>
                            )}
                            {order.gift_message && (
                                <div>
                                    <dt className="flex items-center gap-1 text-terroir-dark/50"><span className="material-symbols-outlined text-base">card_giftcard</span> Message cadeau</dt>
                                    <dd className="rounded-lg bg-terroir-gold/10 p-2 font-semibold italic">« {order.gift_message} »</dd>
                                </div>
                            )}
                            <div>
                                <dt className="text-terroir-dark/50">Paiement</dt>
                                <dd className="font-semibold">
                                    {order.payment_method.replace(/_/g, ' ').replace(/^./, (c) => c.toUpperCase())} — {order.payment_status_label}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {order.payment_status !== 'paye' && <PaymentForm order={order} paymentAccounts={paymentAccounts} />}

                    <StatusForm order={order} statuses={statuses} />
                </div>
            </div>
        </AdminLayout>
    );
}
