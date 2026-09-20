import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../Layouts/AdminLayout';

const CARD_DEFS = [
    { key: 'revenue_month', label: "Chiffre d'affaires (mois)", icon: 'payments', format: (v) => `${v.toLocaleString('fr-FR')} FCFA` },
    { key: 'orders_month', label: 'Commandes (mois)', icon: 'receipt_long' },
    { key: 'orders_pending', label: 'Commandes en attente', icon: 'pending_actions' },
    { key: 'products_count', label: 'Produits actifs', icon: 'inventory_2' },
    { key: 'products_low_stock', label: 'Stock faible', icon: 'warning' },
    { key: 'unread_messages', label: 'Messages non lus', icon: 'mail' },
];

export default function Dashboard({ stats, recentOrders, lowStockProducts, orderStatuses }) {
    return (
        <AdminLayout title="Tableau de bord">
            <Head title="Tableau de bord — Administration" />

            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {CARD_DEFS.map((card, i) => (
                    <motion.div
                        key={card.key}
                        initial={{ opacity: 0, y: 12 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.35, delay: i * 0.05 }}
                        className="flex items-center gap-4 rounded-xl2 bg-white p-6 shadow-soft"
                    >
                        <span className="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-terroir-green/10 text-2xl text-terroir-green">
                            <span className="material-symbols-outlined">{card.icon}</span>
                        </span>
                        <div>
                            <p className="text-sm text-terroir-dark/50">{card.label}</p>
                            <p className="mt-0.5 font-display text-2xl font-bold text-terroir-dark">
                                {card.format ? card.format(stats[card.key]) : stats[card.key]}
                            </p>
                        </div>
                    </motion.div>
                ))}
            </div>

            <div className="mt-6 grid gap-4 lg:grid-cols-2">
                <motion.div
                    initial={{ opacity: 0, y: 12 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.35, delay: 0.15 }}
                    className="rounded-xl2 bg-white p-6 shadow-soft"
                >
                    <h2 className="font-display text-lg font-semibold text-terroir-dark">Commandes récentes</h2>
                    <div className="mt-4 overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b border-terroir-dark/10 text-left text-xs uppercase tracking-wide text-terroir-dark/40">
                                    <th className="pb-2 pr-2">N°</th>
                                    <th className="pb-2 pr-2">Client</th>
                                    <th className="pb-2 pr-2">Statut</th>
                                    <th className="pb-2 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-terroir-dark/5">
                                {recentOrders.length === 0 ? (
                                    <tr><td colSpan={4} className="py-8 text-center text-terroir-dark/40">Aucune commande.</td></tr>
                                ) : recentOrders.map((order) => (
                                    <tr key={order.id}>
                                        <td className="py-2.5 pr-2">
                                            <Link href={route('admin.commandes.show', order.id)} className="font-semibold text-terroir-green hover:underline">
                                                {order.order_number}
                                            </Link>
                                        </td>
                                        <td className="py-2.5 pr-2 text-terroir-dark/70">{order.customer_name}</td>
                                        <td className="py-2.5 pr-2">
                                            <span className="rounded-full bg-terroir-cream px-2.5 py-1 text-xs font-medium text-terroir-dark/70">
                                                {orderStatuses[order.status] ?? order.status}
                                            </span>
                                        </td>
                                        <td className="py-2.5 text-right font-semibold text-terroir-dark">{order.total.toLocaleString('fr-FR')} FCFA</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </motion.div>

                <motion.div
                    initial={{ opacity: 0, y: 12 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.35, delay: 0.2 }}
                    className="rounded-xl2 bg-white p-6 shadow-soft"
                >
                    <h2 className="font-display text-lg font-semibold text-terroir-dark">Produits en stock faible</h2>
                    <div className="mt-4 overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b border-terroir-dark/10 text-left text-xs uppercase tracking-wide text-terroir-dark/40">
                                    <th className="pb-2 pr-2">Produit</th>
                                    <th className="pb-2 pr-2 text-right">Stock</th>
                                    <th className="pb-2 text-right">Seuil</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-terroir-dark/5">
                                {lowStockProducts.length === 0 ? (
                                    <tr><td colSpan={3} className="py-8 text-center text-terroir-dark/40">Aucune alerte de stock.</td></tr>
                                ) : lowStockProducts.map((product) => (
                                    <tr key={product.id}>
                                        <td className="py-2.5 pr-2">
                                            <Link href={route('admin.produits.edit', product.id)} className="font-semibold text-terroir-green hover:underline">
                                                {product.name}
                                            </Link>
                                        </td>
                                        <td className="py-2.5 pr-2 text-right font-semibold text-terroir-terracotta">{product.stock_quantity}</td>
                                        <td className="py-2.5 text-right text-terroir-dark/50">{product.stock_alert_threshold}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </motion.div>
            </div>
        </AdminLayout>
    );
}
