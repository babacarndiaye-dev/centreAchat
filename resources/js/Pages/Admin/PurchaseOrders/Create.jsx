import { Head, Link, router } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Create({ suppliers, products, purchaseRequest }) {
    const [supplierId, setSupplierId] = useState('');
    const [orderDate, setOrderDate] = useState(new Date().toISOString().slice(0, 10));
    const [expectedDate, setExpectedDate] = useState('');
    const [notes, setNotes] = useState('');
    const [rows, setRows] = useState(
        purchaseRequest
            ? purchaseRequest.items.map((item) => ({ product_id: item.product_id, quantity: item.quantity, unit_price: 0 }))
            : [{ product_id: '', quantity: 1, unit_price: 0 }]
    );
    const [processing, setProcessing] = useState(false);
    const [errors, setErrors] = useState({});

    const total = useMemo(
        () => rows.reduce((sum, r) => sum + (Number(r.quantity) || 0) * (Number(r.unit_price) || 0), 0),
        [rows]
    );

    function updateRow(index, field, value) {
        setRows((current) => current.map((row, i) => (i === index ? { ...row, [field]: value } : row)));
    }

    function addRow() {
        setRows((current) => [...current, { product_id: '', quantity: 1, unit_price: 0 }]);
    }

    function removeRow(index) {
        setRows((current) => current.filter((_, i) => i !== index));
    }

    function handleSubmit(e) {
        e.preventDefault();
        setProcessing(true);
        router.post(route('admin.bons-commande.store'), {
            supplier_id: supplierId,
            purchase_request_id: purchaseRequest?.id ?? null,
            order_date: orderDate,
            expected_date: expectedDate || null,
            notes,
            product_id: rows.map((r) => r.product_id),
            quantity: rows.map((r) => r.quantity),
            unit_price: rows.map((r) => r.unit_price),
        }, {
            preserveScroll: true,
            onError: (errs) => setErrors(errs),
            onFinish: () => setProcessing(false),
        });
    }

    return (
        <AdminLayout title="Nouveau bon de commande">
            <Head title="Nouveau bon de commande — Administration" />

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="admin-card max-w-4xl"
            >
                {purchaseRequest && (
                    <div className="mb-4 rounded-lg bg-terroir-cream px-4 py-3 text-sm">
                        Créé à partir de la demande d'achat <strong>{purchaseRequest.reference}</strong>.
                    </div>
                )}

                <form onSubmit={handleSubmit}>
                    <div className="grid gap-4 sm:grid-cols-3">
                        <div>
                            <label className="label">Fournisseur</label>
                            <select value={supplierId} onChange={(e) => setSupplierId(e.target.value)} required className="input">
                                <option value="">Choisir...</option>
                                {suppliers.map((supplier) => <option key={supplier.id} value={supplier.id}>{supplier.name}</option>)}
                            </select>
                            {errors.supplier_id && <p className="mt-1 text-xs text-terroir-terracotta">{errors.supplier_id}</p>}
                        </div>
                        <div>
                            <label className="label">Date de commande</label>
                            <input type="date" value={orderDate} onChange={(e) => setOrderDate(e.target.value)} required className="input" />
                        </div>
                        <div>
                            <label className="label">Livraison attendue</label>
                            <input type="date" value={expectedDate} onChange={(e) => setExpectedDate(e.target.value)} className="input" />
                        </div>
                    </div>

                    <div className="mt-6 border-t border-terroir-dark/10 pt-6">
                        <h3 className="font-display text-base font-semibold">Articles commandés</h3>

                        <div className="mt-3 flex flex-col gap-3">
                            {rows.map((row, i) => (
                                <div key={i} className="flex flex-wrap items-center gap-3">
                                    <select
                                        value={row.product_id}
                                        onChange={(e) => updateRow(i, 'product_id', e.target.value)}
                                        required
                                        className="input min-w-[200px] flex-1"
                                    >
                                        <option value="">Produit...</option>
                                        {products.map((p) => <option key={p.id} value={p.id}>{p.name}</option>)}
                                    </select>
                                    <input
                                        type="number"
                                        min={1}
                                        value={row.quantity}
                                        onChange={(e) => updateRow(i, 'quantity', e.target.value)}
                                        placeholder="Qté"
                                        required
                                        className="input w-24"
                                    />
                                    <input
                                        type="number"
                                        step="0.01"
                                        min={0}
                                        value={row.unit_price}
                                        onChange={(e) => updateRow(i, 'unit_price', e.target.value)}
                                        placeholder="Prix unitaire"
                                        required
                                        className="input w-32"
                                    />
                                    <button type="button" onClick={() => removeRow(i)} className="font-semibold text-terroir-terracotta" aria-label="Retirer">
                                        <span className="material-symbols-outlined text-lg">close</span>
                                    </button>
                                </div>
                            ))}
                        </div>

                        <button type="button" onClick={addRow} className="btn-outline mt-4">+ Ajouter un article</button>

                        <div className="mt-4 text-right text-lg font-bold text-terroir-green">
                            Total : {total.toLocaleString('fr-FR')} FCFA
                        </div>
                    </div>

                    <div className="mt-6">
                        <label className="label">Notes</label>
                        <textarea rows={2} value={notes} onChange={(e) => setNotes(e.target.value)} className="input" />
                    </div>

                    <div className="mt-6 flex gap-3">
                        <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Créer le bon de commande</button>
                        <Link href={route('admin.bons-commande.index')} className="btn-outline">Annuler</Link>
                    </div>
                </form>
            </motion.div>
        </AdminLayout>
    );
}
