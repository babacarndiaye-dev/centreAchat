import { Head, Link, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

function ReceptionForm({ purchaseOrder }) {
    const remainingItems = purchaseOrder.items.filter((item) => item.remaining_quantity > 0);
    const [receptionDate, setReceptionDate] = useState(new Date().toISOString().slice(0, 10));
    const [quantities, setQuantities] = useState(Object.fromEntries(remainingItems.map((item) => [item.id, ''])));
    const [qualities, setQualities] = useState(Object.fromEntries(remainingItems.map((item) => [item.id, 'conforme'])));
    const [notes, setNotes] = useState('');
    const [processing, setProcessing] = useState(false);

    if (remainingItems.length === 0) return null;

    function handleSubmit(e) {
        e.preventDefault();
        setProcessing(true);
        router.post(route('admin.bons-commande.receptions.store', purchaseOrder.id), {
            reception_date: receptionDate,
            notes,
            quantity_received: quantities,
            quality_status: qualities,
        }, {
            preserveScroll: true,
            onFinish: () => setProcessing(false),
        });
    }

    return (
        <div className="admin-card mt-6">
            <h3 className="font-display text-base font-semibold">Enregistrer une réception</h3>
            <p className="text-sm text-terroir-dark/50">Indiquez les quantités reçues et leur conformité. Le stock sera mis à jour automatiquement pour les articles conformes.</p>

            <form onSubmit={handleSubmit} className="mt-4">
                <input type="date" value={receptionDate} onChange={(e) => setReceptionDate(e.target.value)} required className="input max-w-xs" />

                <div className="mt-4 flex flex-col gap-3">
                    {remainingItems.map((item) => (
                        <div key={item.id} className="flex flex-wrap items-center gap-3 rounded-lg bg-terroir-cream/80 px-4 py-2.5">
                            <span className="flex-1 text-sm font-semibold">
                                {item.product_name} <span className="text-sm font-normal text-terroir-dark/50">(reste {item.remaining_quantity})</span>
                            </span>
                            <input
                                type="number"
                                min={0}
                                max={item.remaining_quantity}
                                value={quantities[item.id]}
                                onChange={(e) => setQuantities((q) => ({ ...q, [item.id]: e.target.value }))}
                                placeholder="Qté reçue"
                                className="input w-28"
                            />
                            <select
                                value={qualities[item.id]}
                                onChange={(e) => setQualities((q) => ({ ...q, [item.id]: e.target.value }))}
                                className="input w-40"
                            >
                                <option value="conforme">Conforme</option>
                                <option value="non_conforme">Non conforme</option>
                            </select>
                        </div>
                    ))}
                </div>

                <textarea
                    value={notes}
                    onChange={(e) => setNotes(e.target.value)}
                    rows={2}
                    placeholder="Notes de contrôle qualité (optionnel)"
                    className="input mt-4"
                />

                <button type="submit" disabled={processing} className="btn-primary mt-4 disabled:opacity-50">Enregistrer la réception</button>
            </form>
        </div>
    );
}

function StatusPanel({ purchaseOrder }) {
    function updateStatus(status) {
        router.patch(route('admin.bons-commande.status', purchaseOrder.id), { status }, { preserveScroll: true });
    }

    return (
        <div className="admin-card">
            <h3 className="font-display text-base font-semibold">Statut</h3>
            <dl className="mt-3 space-y-2 text-sm">
                <div><dt className="text-terroir-dark/50">Date de commande</dt><dd className="mt-0.5 font-semibold">{purchaseOrder.order_date}</dd></div>
                {purchaseOrder.expected_date && (
                    <div><dt className="text-terroir-dark/50">Livraison attendue</dt><dd className="mt-0.5 font-semibold">{purchaseOrder.expected_date}</dd></div>
                )}
            </dl>

            {purchaseOrder.status === 'brouillon' && (
                <button type="button" onClick={() => updateStatus('envoyee')} className="btn-primary mt-4 w-full justify-center">Envoyer au fournisseur</button>
            )}

            {purchaseOrder.status === 'envoyee' && (
                <button type="button" onClick={() => updateStatus('confirmee')} className="btn-primary mt-4 w-full justify-center">Marquer confirmée</button>
            )}

            {!['recue', 'annulee'].includes(purchaseOrder.status) && (
                <button
                    type="button"
                    onClick={() => { if (confirm('Annuler ce bon de commande ?')) updateStatus('annulee'); }}
                    className="btn-outline mt-3 w-full justify-center"
                >
                    Annuler le bon de commande
                </button>
            )}
        </div>
    );
}

function PaymentPanel({ purchaseOrder, paymentAccounts }) {
    const { data, setData, post, processing } = useForm({
        purchase_order_id: purchaseOrder.id,
        amount: '',
        payment_date: new Date().toISOString().slice(0, 10),
        method: '',
        payment_account_id: '',
    });

    function handleSubmit(e) {
        e.preventDefault();
        post(route('admin.fournisseurs.paiements.store', purchaseOrder.supplier.id), { preserveScroll: true });
    }

    return (
        <div className="admin-card mt-6">
            <h3 className="font-display text-base font-semibold">Paiement fournisseur</h3>
            <dl className="mt-3 space-y-2 text-sm">
                <div><dt className="text-terroir-dark/50">Total</dt><dd className="mt-0.5 font-semibold">{purchaseOrder.total.toLocaleString('fr-FR')} FCFA</dd></div>
                <div><dt className="text-terroir-dark/50">Payé</dt><dd className="mt-0.5 font-semibold text-terroir-green">{purchaseOrder.amount_paid.toLocaleString('fr-FR')} FCFA</dd></div>
                <div><dt className="text-terroir-dark/50">Solde dû</dt><dd className="mt-0.5 font-semibold text-terroir-terracotta">{purchaseOrder.balance.toLocaleString('fr-FR')} FCFA</dd></div>
            </dl>

            {purchaseOrder.balance > 0 && (
                <form onSubmit={handleSubmit} className="mt-4 flex flex-col gap-2">
                    <input
                        type="number"
                        step="0.01"
                        max={purchaseOrder.balance}
                        value={data.amount}
                        onChange={(e) => setData('amount', e.target.value)}
                        placeholder="Montant"
                        required
                        className="input"
                    />
                    <input type="date" value={data.payment_date} onChange={(e) => setData('payment_date', e.target.value)} required className="input" />
                    <input value={data.method} onChange={(e) => setData('method', e.target.value)} placeholder="Mode de paiement" className="input" />
                    <select value={data.payment_account_id} onChange={(e) => setData('payment_account_id', e.target.value)} className="input">
                        <option value="">Compte de paiement (pour l'écriture comptable)</option>
                        {paymentAccounts.map((account) => <option key={account.id} value={account.id}>{account.name}</option>)}
                    </select>
                    <button type="submit" disabled={processing} className="btn-primary w-full justify-center disabled:opacity-50">Enregistrer le paiement</button>
                </form>
            )}

            {purchaseOrder.payments.length > 0 && (
                <ul className="mt-4 space-y-1 border-t border-terroir-dark/10 pt-3 text-xs text-terroir-dark/60">
                    {purchaseOrder.payments.map((payment) => (
                        <li key={payment.id} className="flex justify-between">
                            <span>{payment.payment_date}</span>
                            <span>{payment.amount.toLocaleString('fr-FR')} FCFA</span>
                        </li>
                    ))}
                </ul>
            )}
        </div>
    );
}

export default function Show({ purchaseOrder, paymentAccounts }) {
    return (
        <AdminLayout title={purchaseOrder.order_number}>
            <Head title={`${purchaseOrder.order_number} — Administration`} />

            <div className="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 className="font-display text-2xl font-semibold">{purchaseOrder.order_number}</h2>
                    <p className="text-sm text-terroir-dark/50">
                        Fournisseur : <Link href={route('admin.fournisseurs.show', purchaseOrder.supplier.id)} className="admin-link">{purchaseOrder.supplier.name}</Link>
                    </p>
                </div>
                <span className={purchaseOrder.status_badge_class + ' px-4 py-1.5 text-sm'}>{purchaseOrder.status_label}</span>
            </div>

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="mt-6 grid gap-6 lg:grid-cols-3"
            >
                <div className="lg:col-span-2">
                    <div className="admin-card">
                        <h3 className="font-display text-base font-semibold">Articles</h3>
                        <table className="admin-table mt-3">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th className="text-right">Commandé</th>
                                    <th className="text-right">Reçu</th>
                                    <th className="text-right">Prix unitaire</th>
                                    <th className="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                {purchaseOrder.items.map((item) => (
                                    <tr key={item.id}>
                                        <td>{item.product_name}</td>
                                        <td className="text-right">{item.quantity_ordered}</td>
                                        <td className={'text-right font-semibold ' + (item.quantity_received < item.quantity_ordered ? 'text-terroir-terracotta' : 'text-terroir-green')}>
                                            {item.quantity_received}
                                        </td>
                                        <td className="text-right">{item.unit_price.toLocaleString('fr-FR')} FCFA</td>
                                        <td className="text-right font-semibold">{item.total.toLocaleString('fr-FR')} FCFA</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                        <div className="ml-auto mt-4 max-w-xs text-right text-base font-bold text-terroir-green">
                            Total : {purchaseOrder.total.toLocaleString('fr-FR')} FCFA
                        </div>
                        {purchaseOrder.notes && <div className="mt-4 rounded-lg bg-terroir-cream p-4 text-sm">{purchaseOrder.notes}</div>}
                    </div>

                    {!['recue', 'annulee'].includes(purchaseOrder.status) && <ReceptionForm purchaseOrder={purchaseOrder} />}

                    {purchaseOrder.receptions.length > 0 && (
                        <div className="admin-card mt-6">
                            <h3 className="font-display text-base font-semibold">Historique des réceptions</h3>
                            <div className="mt-4 flex flex-col gap-4">
                                {purchaseOrder.receptions.map((reception) => (
                                    <div key={reception.id} className="rounded-lg border border-terroir-dark/10 p-4">
                                        <div className="flex items-center justify-between text-sm">
                                            <span className="font-semibold">{reception.reception_date}</span>
                                            <span className="admin-badge-neutral">{reception.quality_status_label}</span>
                                        </div>
                                        <ul className="mt-1.5 space-y-0.5 text-xs text-terroir-dark/70">
                                            {reception.items.map((ri, i) => (
                                                <li key={i}>{ri.product_name} — {ri.quantity_received} ({ri.is_conforme ? 'Conforme' : 'Non conforme'})</li>
                                            ))}
                                        </ul>
                                        {reception.notes && <p className="mt-1.5 text-xs italic text-terroir-dark/50">{reception.notes}</p>}
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}
                </div>

                <div>
                    <StatusPanel purchaseOrder={purchaseOrder} />
                    <PaymentPanel purchaseOrder={purchaseOrder} paymentAccounts={paymentAccounts} />
                </div>
            </motion.div>
        </AdminLayout>
    );
}
