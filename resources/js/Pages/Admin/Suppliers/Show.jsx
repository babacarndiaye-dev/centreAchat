import { Head, Link, router, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

function AccessCard({ supplier }) {
    const { data, setData, post, processing, errors } = useForm({ email: supplier.user_email ?? '' });

    function handleCreate(e) {
        e.preventDefault();
        post(route('admin.fournisseurs.acces.store', supplier.id), { preserveScroll: true });
    }

    function handleRevoke() {
        if (!confirm("Révoquer l'accès portail de ce fournisseur ?")) return;
        router.delete(route('admin.fournisseurs.acces.destroy', supplier.id), { preserveScroll: true });
    }

    return (
        <div className="admin-card">
            <h3 className="font-display text-base font-semibold">Accès au portail fournisseur</h3>
            {supplier.user_email ? (
                <>
                    <p className="mt-1.5 text-sm text-terroir-dark/70">Accès actif pour <strong>{supplier.user_email}</strong>.</p>
                    <button type="button" onClick={handleRevoke} className="btn-outline mt-4">Révoquer l'accès</button>
                </>
            ) : (
                <>
                    <p className="mt-1.5 text-sm text-terroir-dark/50">
                        Ce fournisseur n'a pas encore d'accès au portail. Créez un compte pour lui permettre de consulter ses commandes et confirmer leur disponibilité.
                    </p>
                    <form onSubmit={handleCreate} className="mt-4 flex flex-wrap gap-3">
                        <input
                            type="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            placeholder="E-mail du fournisseur"
                            required
                            className="input max-w-xs"
                        />
                        <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Créer l'accès</button>
                    </form>
                    {errors.email && <p className="mt-1 text-xs text-terroir-terracotta">{errors.email}</p>}
                </>
            )}
        </div>
    );
}

function PaymentCard({ supplier, paymentAccounts }) {
    const { data, setData, post, processing, reset } = useForm({
        amount: '',
        payment_date: new Date().toISOString().slice(0, 10),
        purchase_order_id: '',
        method: '',
        reference: '',
        payment_account_id: '',
    });

    function handleSubmit(e) {
        e.preventDefault();
        post(route('admin.fournisseurs.paiements.store', supplier.id), {
            preserveScroll: true,
            onSuccess: () => reset('amount', 'method', 'reference'),
        });
    }

    return (
        <div className="admin-card">
            <h3 className="font-display text-base font-semibold">Enregistrer un paiement</h3>
            <form onSubmit={handleSubmit} className="mt-4 flex flex-col gap-3">
                <div className="grid grid-cols-2 gap-3">
                    <input
                        type="number"
                        step="0.01"
                        value={data.amount}
                        onChange={(e) => setData('amount', e.target.value)}
                        placeholder="Montant (FCFA)"
                        required
                        className="input"
                    />
                    <input
                        type="date"
                        value={data.payment_date}
                        onChange={(e) => setData('payment_date', e.target.value)}
                        required
                        className="input"
                    />
                </div>
                <select value={data.purchase_order_id} onChange={(e) => setData('purchase_order_id', e.target.value)} className="input">
                    <option value="">Sans bon de commande lié</option>
                    {supplier.purchase_orders.map((po) => (
                        <option key={po.id} value={po.id}>{po.order_number} — solde {po.balance.toLocaleString('fr-FR')} FCFA</option>
                    ))}
                </select>
                <div className="grid grid-cols-2 gap-3">
                    <input
                        value={data.method}
                        onChange={(e) => setData('method', e.target.value)}
                        placeholder="Mode (Espèces, Wave...)"
                        className="input"
                    />
                    <input value={data.reference} onChange={(e) => setData('reference', e.target.value)} placeholder="Référence" className="input" />
                </div>
                <select value={data.payment_account_id} onChange={(e) => setData('payment_account_id', e.target.value)} className="input">
                    <option value="">Compte de paiement (pour l'écriture comptable)</option>
                    {paymentAccounts.map((account) => <option key={account.id} value={account.id}>{account.name}</option>)}
                </select>
                <button type="submit" disabled={processing} className="btn-primary w-full justify-center disabled:opacity-50">Enregistrer le paiement</button>
            </form>
        </div>
    );
}

function CatalogueCard({ supplier, products }) {
    const { data, setData, post, processing, reset } = useForm({ product_id: '', supplier_price: '', supplier_reference: '' });

    function handleAdd(e) {
        e.preventDefault();
        post(route('admin.fournisseurs.produits.store', supplier.id), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    }

    function handleRemove(sp) {
        if (!confirm('Retirer ce produit du catalogue ?')) return;
        router.delete(route('admin.fournisseurs.produits.destroy', sp.id), { preserveScroll: true });
    }

    return (
        <div className="admin-card mt-6">
            <h3 className="font-display text-base font-semibold">Catalogue produits du fournisseur</h3>
            <form onSubmit={handleAdd} className="mt-4 flex flex-wrap gap-3">
                <select value={data.product_id} onChange={(e) => setData('product_id', e.target.value)} required className="input min-w-[220px] flex-1">
                    <option value="">Choisir un produit...</option>
                    {products.map((product) => <option key={product.id} value={product.id}>{product.name}</option>)}
                </select>
                <input
                    type="number"
                    step="0.01"
                    value={data.supplier_price}
                    onChange={(e) => setData('supplier_price', e.target.value)}
                    placeholder="Prix d'achat"
                    required
                    className="input w-40"
                />
                <input
                    value={data.supplier_reference}
                    onChange={(e) => setData('supplier_reference', e.target.value)}
                    placeholder="Réf. fournisseur"
                    className="input w-40"
                />
                <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Ajouter</button>
            </form>

            <div className="mt-4 overflow-x-auto">
                <table className="admin-table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th className="text-right">Prix d'achat</th>
                            <th>Réf. fournisseur</th>
                            <th className="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {supplier.supplier_products.length === 0 ? (
                            <tr><td colSpan={4} className="py-6 text-center text-terroir-dark/40">Aucun produit référencé pour ce fournisseur.</td></tr>
                        ) : supplier.supplier_products.map((sp) => (
                            <tr key={sp.id}>
                                <td>{sp.product_name}</td>
                                <td className="text-right">{sp.supplier_price.toLocaleString('fr-FR')} FCFA</td>
                                <td>{sp.supplier_reference ?? '—'}</td>
                                <td className="text-right">
                                    <button type="button" onClick={() => handleRemove(sp)} className="admin-link-danger bg-transparent">Retirer</button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}

export default function Show({ supplier, products, paymentAccounts }) {
    return (
        <AdminLayout title={supplier.name}>
            <Head title={`${supplier.name} — Administration`} />

            <div className="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 className="font-display text-2xl font-semibold">{supplier.name}</h2>
                    <p className="text-sm text-terroir-dark/50">{supplier.company_name}</p>
                </div>
                <Link href={route('admin.fournisseurs.edit', supplier.id)} className="btn-outline">Modifier la fiche</Link>
            </div>

            <div className="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div className="admin-card">
                    <p className="text-xs text-terroir-dark/50">Total commandé</p>
                    <p className="mt-1.5 text-xl font-bold">{supplier.total_owed.toLocaleString('fr-FR')} FCFA</p>
                </div>
                <div className="admin-card">
                    <p className="text-xs text-terroir-dark/50">Total payé</p>
                    <p className="mt-1.5 text-xl font-bold text-terroir-green">{supplier.total_paid.toLocaleString('fr-FR')} FCFA</p>
                </div>
                <div className="admin-card">
                    <p className="text-xs text-terroir-dark/50">Solde dû</p>
                    <p className="mt-1.5 text-xl font-bold text-terroir-terracotta">{supplier.balance.toLocaleString('fr-FR')} FCFA</p>
                </div>
                <div className="admin-card">
                    <p className="text-xs text-terroir-dark/50">Statut</p>
                    <p className="mt-1.5 text-xl font-bold">{supplier.status_label}</p>
                </div>
            </div>

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="mt-6 grid gap-6 lg:grid-cols-2"
            >
                <div className="admin-card">
                    <dl className="space-y-2 text-sm">
                        <div><dt className="text-terroir-dark/50">Responsable</dt><dd className="mt-0.5 font-semibold">{supplier.contact_name ?? '—'}</dd></div>
                        <div><dt className="text-terroir-dark/50">Téléphone</dt><dd className="mt-0.5 font-semibold">{supplier.phone}</dd></div>
                        <div><dt className="text-terroir-dark/50">E-mail</dt><dd className="mt-0.5 font-semibold">{supplier.email ?? '—'}</dd></div>
                        <div><dt className="text-terroir-dark/50">Adresse</dt><dd className="mt-0.5 font-semibold">{supplier.address ?? '—'}, {supplier.city} {supplier.region}</dd></div>
                        <div><dt className="text-terroir-dark/50">Conditions de paiement</dt><dd className="mt-0.5 font-semibold">{supplier.payment_terms ?? '—'}</dd></div>
                        <div><dt className="text-terroir-dark/50">Délai de livraison</dt><dd className="mt-0.5 font-semibold">{supplier.delivery_delay_days ? `${supplier.delivery_delay_days} jours` : '—'}</dd></div>
                        {supplier.notes && (
                            <div><dt className="text-terroir-dark/50">Notes</dt><dd className="mt-0.5 font-semibold">{supplier.notes}</dd></div>
                        )}
                    </dl>
                </div>

                <AccessCard supplier={supplier} />
                <PaymentCard supplier={supplier} paymentAccounts={paymentAccounts} />
            </motion.div>

            <CatalogueCard supplier={supplier} products={products} />

            <div className="admin-card mt-6">
                <div className="flex items-center justify-between">
                    <h3 className="font-display text-base font-semibold">Bons de commande</h3>
                    <Link href={route('admin.bons-commande.create')} className="admin-link">+ Nouveau bon de commande</Link>
                </div>
                <div className="mt-4 overflow-x-auto">
                    <table className="admin-table">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th className="text-right">Total</th>
                                <th className="text-right">Solde</th>
                            </tr>
                        </thead>
                        <tbody>
                            {supplier.purchase_orders.length === 0 ? (
                                <tr><td colSpan={5} className="py-6 text-center text-terroir-dark/40">Aucun bon de commande pour ce fournisseur.</td></tr>
                            ) : supplier.purchase_orders.map((po) => (
                                <tr key={po.id}>
                                    <td><Link href={route('admin.bons-commande.show', po.id)} className="admin-link">{po.order_number}</Link></td>
                                    <td className="text-terroir-dark/60">{po.order_date}</td>
                                    <td>{po.status_label}</td>
                                    <td className="text-right">{po.total.toLocaleString('fr-FR')} FCFA</td>
                                    <td className={'text-right ' + (po.balance > 0 ? 'font-semibold text-terroir-terracotta' : '')}>{po.balance.toLocaleString('fr-FR')} FCFA</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </AdminLayout>
    );
}
