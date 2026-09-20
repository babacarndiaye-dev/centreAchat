import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import AdminLayout from '../../../../Layouts/AdminLayout';

export default function Create({ products, items, subtotal, customer, customers, paymentMethods, filters }) {
    const [q, setQ] = useState(filters?.q ?? '');

    function handleSearch(e) {
        e.preventDefault();
        router.get(route('admin.pos.ventes.create'), { q }, { preserveState: true });
    }

    function handleAdd(product) {
        if (!product.in_stock) return;
        router.post(route('admin.pos.ventes.add', product.id), { quantity: 1 }, { preserveScroll: true });
    }

    function handleQuantityChange(productId, value) {
        router.patch(route('admin.pos.ventes.update', productId), { quantity: value }, { preserveScroll: true });
    }

    function handleRemove(productId) {
        router.delete(route('admin.pos.ventes.remove', productId), { preserveScroll: true });
    }

    function handleCustomerChange(userId) {
        router.post(route('admin.pos.ventes.customer'), { user_id: userId }, { preserveScroll: true });
    }

    return (
        <AdminLayout title="Nouvelle vente">
            <Head title="Nouvelle vente — Administration" />

            <div className="grid gap-6 lg:grid-cols-3">
                <div className="lg:col-span-2">
                    <form onSubmit={handleSearch} className="flex gap-2">
                        <input
                            value={q}
                            onChange={(e) => setQ(e.target.value)}
                            placeholder="Rechercher un produit ou une référence..."
                            className="input flex-1"
                        />
                        <button type="submit" className="btn-outline">Rechercher</button>
                    </form>

                    <div className="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        {products.map((product) => (
                            <button
                                key={product.id}
                                type="button"
                                disabled={!product.in_stock}
                                onClick={() => handleAdd(product)}
                                className="admin-card flex w-full flex-col items-start gap-1 border-0 p-4 text-left transition hover:-translate-y-0.5 hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:translate-y-0"
                            >
                                <span className="text-sm font-semibold">{product.name}</span>
                                <span className="text-sm text-terroir-dark/50">{product.reference} — Stock : {product.stock_quantity}</span>
                                <span className="mt-1 font-bold text-terroir-green">{product.price.toLocaleString('fr-FR')} FCFA</span>
                            </button>
                        ))}
                    </div>
                </div>

                <div>
                    <div className="admin-card">
                        <h3 className="font-display text-base font-semibold">Vente en cours</h3>

                        <select
                            defaultValue={customer?.id ?? ''}
                            onChange={(e) => handleCustomerChange(e.target.value)}
                            className="input mt-3"
                        >
                            <option value="">Client comptoir (sans compte)</option>
                            {customers.map((u) => <option key={u.id} value={u.id}>{u.name} ({u.company_name})</option>)}
                        </select>

                        <div className="mt-4 flex flex-col gap-2">
                            {items.length === 0 ? (
                                <p className="text-sm text-terroir-dark/50">Panier vide.</p>
                            ) : items.map((item) => (
                                <div key={item.product_id} className="flex items-center gap-2 text-sm">
                                    <span className="flex-1">{item.product_name}</span>
                                    <input
                                        type="number"
                                        min="0"
                                        defaultValue={item.quantity}
                                        onBlur={(e) => handleQuantityChange(item.product_id, e.target.value)}
                                        className="input w-16 text-right"
                                    />
                                    <span className="w-24 text-right font-semibold">{item.total.toLocaleString('fr-FR')}</span>
                                    <button type="button" onClick={() => handleRemove(item.product_id)} className="text-terroir-terracotta">
                                        <span className="material-symbols-outlined text-lg">close</span>
                                    </button>
                                </div>
                            ))}
                        </div>

                        <div className="mt-4 border-t border-terroir-dark/10 pt-4 text-right text-lg font-bold text-terroir-green">
                            {subtotal.toLocaleString('fr-FR')} FCFA
                        </div>

                        {items.length > 0 && (
                            <CheckoutForm subtotal={subtotal} customer={customer} paymentMethods={paymentMethods} />
                        )}
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}

function CheckoutForm({ subtotal, customer, paymentMethods }) {
    const { data, setData, post, processing, errors } = useForm({
        customer_name: customer?.name ?? '',
        payments: [{ method: paymentMethods[0]?.code ?? 'especes', amount: subtotal }],
    });

    const total = data.payments.reduce((s, p) => s + (Number(p.amount) || 0), 0);

    function updatePayment(i, field, value) {
        const payments = data.payments.slice();
        payments[i] = { ...payments[i], [field]: value };
        setData('payments', payments);
    }

    function addPayment() {
        setData('payments', [...data.payments, { method: paymentMethods[0]?.code ?? 'especes', amount: 0 }]);
    }

    function removePayment(i) {
        setData('payments', data.payments.filter((_, idx) => idx !== i));
    }

    function handleSubmit(e) {
        e.preventDefault();
        post(route('admin.pos.ventes.store'));
    }

    return (
        <form onSubmit={handleSubmit} className="mt-8 border-t border-terroir-dark/10 pt-4">
            <input
                value={data.customer_name}
                onChange={(e) => setData('customer_name', e.target.value)}
                placeholder="Nom du client (optionnel)"
                className="input"
            />

            <div className="mt-3 flex flex-col gap-2">
                {data.payments.map((p, i) => (
                    <div key={i} className="flex gap-2">
                        <select value={p.method} onChange={(e) => updatePayment(i, 'method', e.target.value)} className="input flex-1">
                            {paymentMethods.map((method) => <option key={method.code} value={method.code}>{method.name}</option>)}
                        </select>
                        <input
                            type="number"
                            step="0.01"
                            value={p.amount}
                            onChange={(e) => updatePayment(i, 'amount', e.target.value)}
                            className="input w-32"
                        />
                        <button type="button" onClick={() => removePayment(i)} className="text-terroir-terracotta">
                            <span className="material-symbols-outlined text-lg">close</span>
                        </button>
                    </div>
                ))}
            </div>

            <button type="button" onClick={addPayment} className="mt-3 block text-sm font-semibold text-terroir-green">+ Ajouter un paiement</button>

            <p className="mt-3 text-sm">Total réglé : <span className="font-semibold">{total.toLocaleString('fr-FR')} FCFA</span></p>
            {errors.payments && <p className="mt-1 text-xs text-terroir-terracotta">{errors.payments}</p>}

            <button type="submit" disabled={processing} className="btn-primary mt-4 w-full justify-center disabled:opacity-50">Encaisser</button>
        </form>
    );
}
