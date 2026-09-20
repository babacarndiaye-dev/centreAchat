import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Show({ purchaseRequest }) {
    function handleSubmitForValidation() {
        router.patch(route('admin.demandes-achat.submit', purchaseRequest.id), {}, { preserveScroll: true });
    }

    function handleValidate() {
        router.patch(route('admin.demandes-achat.validate', purchaseRequest.id), {}, { preserveScroll: true });
    }

    function handleReject() {
        router.patch(route('admin.demandes-achat.reject', purchaseRequest.id), {}, { preserveScroll: true });
    }

    function handleDelete() {
        if (!confirm('Supprimer cette demande ?')) return;
        router.delete(route('admin.demandes-achat.destroy', purchaseRequest.id));
    }

    return (
        <AdminLayout title={purchaseRequest.reference}>
            <Head title={`${purchaseRequest.reference} — Administration`} />

            <div className="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 className="font-display text-2xl font-semibold">{purchaseRequest.reference}</h2>
                    <p className="text-sm text-terroir-dark/50">
                        Demandée par {purchaseRequest.requester_name ?? '—'} le {purchaseRequest.created_at}
                    </p>
                </div>
                <span className={purchaseRequest.status_badge_class + ' px-4 py-1.5 text-sm'}>{purchaseRequest.status_label}</span>
            </div>

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="mt-6 grid gap-6 lg:grid-cols-3"
            >
                <div className="lg:col-span-2">
                    <div className="admin-card">
                        <h3 className="font-display text-base font-semibold">Produits demandés</h3>
                        <table className="admin-table mt-3">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th className="text-right">Quantité demandée</th>
                                </tr>
                            </thead>
                            <tbody>
                                {purchaseRequest.items.map((item) => (
                                    <tr key={item.id}>
                                        <td>{item.product_name}</td>
                                        <td className="text-right font-semibold">{item.quantity} {item.unit}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>

                        {purchaseRequest.reason && (
                            <div className="mt-4 rounded-lg bg-terroir-cream p-4 text-sm">
                                <strong>Motif :</strong> {purchaseRequest.reason}
                            </div>
                        )}

                        {purchaseRequest.purchase_orders.length > 0 && (
                            <div className="mt-6 border-t border-terroir-dark/10 pt-6">
                                <h3 className="font-display text-base font-semibold">Bons de commande liés</h3>
                                <ul className="mt-3 flex flex-col gap-2 text-sm">
                                    {purchaseRequest.purchase_orders.map((po) => (
                                        <li key={po.id}>
                                            <Link href={route('admin.bons-commande.show', po.id)} className="admin-link">{po.order_number}</Link> — {po.supplier_name}
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        )}
                    </div>
                </div>

                <div>
                    <div className="admin-card">
                        <h3 className="font-display text-base font-semibold">Actions</h3>
                        <div className="mt-4 flex flex-col gap-3">
                            {purchaseRequest.status === 'brouillon' && (
                                <button type="button" onClick={handleSubmitForValidation} className="btn-primary w-full justify-center">
                                    Soumettre pour validation
                                </button>
                            )}

                            {purchaseRequest.status === 'en_attente_validation' && (
                                <>
                                    <button type="button" onClick={handleValidate} className="btn-primary w-full justify-center">Valider la demande</button>
                                    <button type="button" onClick={handleReject} className="btn-outline w-full justify-center">Rejeter</button>
                                </>
                            )}

                            {purchaseRequest.status === 'validee' && (
                                <Link href={route('admin.bons-commande.create', { demande: purchaseRequest.id })} className="btn-primary w-full justify-center">
                                    Créer le bon de commande
                                </Link>
                            )}

                            {['brouillon', 'en_attente_validation'].includes(purchaseRequest.status) && (
                                <button type="button" onClick={handleDelete} className="admin-link-danger bg-transparent">Supprimer la demande</button>
                            )}
                        </div>
                    </div>

                    {purchaseRequest.validator_name && (
                        <div className="admin-card mt-6 text-sm">
                            <p className="text-terroir-dark/50">{purchaseRequest.status === 'rejetee' ? 'Rejetée' : 'Validée'} par</p>
                            <p className="mt-1.5 font-semibold">{purchaseRequest.validator_name}</p>
                            <p className="text-terroir-dark/50">{purchaseRequest.validated_at}</p>
                        </div>
                    )}
                </div>
            </motion.div>
        </AdminLayout>
    );
}
