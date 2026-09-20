import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import AdminLayout from '../../../../Layouts/AdminLayout';

export default function Create({ order, orderNumber, errors: pageErrors }) {
    const [searchValue, setSearchValue] = useState(orderNumber ?? '');

    function handleSearch(e) {
        e.preventDefault();
        router.get(route('admin.pos.retours.create'), { order_number: searchValue }, { preserveState: true });
    }

    return (
        <AdminLayout title="Nouveau retour">
            <Head title="Nouveau retour — Administration" />

            <div className="admin-card max-w-2xl">
                <h2 className="font-display text-lg font-semibold">Rechercher une commande</h2>
                <form onSubmit={handleSearch} className="mt-3 flex gap-2">
                    <input
                        value={searchValue}
                        onChange={(e) => setSearchValue(e.target.value)}
                        placeholder="Numéro de commande (ex : CA-20260818-XXXXXX)"
                        required
                        className="input flex-1"
                    />
                    <button type="submit" className="btn-outline">Rechercher</button>
                </form>
            </div>

            {order && <ReturnForm order={order} />}
        </AdminLayout>
    );
}

function ReturnForm({ order }) {
    const { data, setData, post, processing, errors } = useForm({
        order_id: order.id,
        quantity: Object.fromEntries(order.items.map((item) => [item.id, 0])),
        notes: '',
    });

    function updateQuantity(itemId, value) {
        setData('quantity', { ...data.quantity, [itemId]: value });
    }

    function handleSubmit(e) {
        e.preventDefault();
        post(route('admin.pos.retours.store'));
    }

    return (
        <div className="admin-card mt-6 max-w-2xl">
            <h2 className="font-display text-lg font-semibold">{order.order_number}</h2>
            <p className="text-sm text-terroir-dark/50">{order.customer_name} — {order.created_at}</p>

            <form onSubmit={handleSubmit} className="mt-4">
                <div className="flex flex-col gap-3">
                    {order.items.map((item) => (
                        <div key={item.id} className="flex items-center justify-between gap-4 rounded-lg bg-terroir-cream/60 px-4 py-2.5">
                            <div>
                                <p className="text-sm font-semibold">{item.product_name}</p>
                                <p className="text-sm text-terroir-dark/50">Acheté : {item.quantity} — Retournable : {item.returnable_quantity}</p>
                            </div>
                            <input
                                type="number"
                                min="0"
                                max={item.returnable_quantity}
                                value={data.quantity[item.id]}
                                onChange={(e) => updateQuantity(item.id, e.target.value)}
                                className="input w-24"
                            />
                        </div>
                    ))}
                </div>

                <div className="mt-4">
                    <label className="label">Motif du retour</label>
                    <textarea rows={2} value={data.notes} onChange={(e) => setData('notes', e.target.value)} className="input" />
                </div>

                {errors.quantity && <p className="mt-2 text-sm text-terroir-terracotta">{errors.quantity}</p>}

                <button type="submit" disabled={processing} className="btn-primary mt-4 disabled:opacity-50">Valider le retour</button>
            </form>
        </div>
    );
}
