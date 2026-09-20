import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';
import ChartCanvas, { chartPalette, chartCategoricalOrder, formatFcfa } from '../../../Components/Admin/ChartCanvas';

function fcfa(value) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(value)) + ' FCFA';
}

function compact(value) {
    return new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(value);
}

export default function Index({
    revenueByMonth, revenueThisMonth, averageBasket,
    topProducts, salesByChannel, topSuppliers,
    stockValue, lowStock, outOfStock, dormantProducts,
    receivables, payables,
}) {
    return (
        <AdminLayout title="Statistiques">
            <Head title="Statistiques — Administration" />

            <div className="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div className="admin-card">
                    <p className="text-sm text-terroir-dark/50">CA du mois</p>
                    <p className="mt-1 text-2xl font-bold text-terroir-green">{fcfa(revenueThisMonth)}</p>
                </div>
                <div className="admin-card">
                    <p className="text-sm text-terroir-dark/50">Panier moyen</p>
                    <p className="mt-1 text-2xl font-bold">{fcfa(averageBasket)}</p>
                </div>
                <div className="admin-card">
                    <p className="text-sm text-terroir-dark/50">Valeur du stock</p>
                    <p className="mt-1 text-2xl font-bold">{fcfa(stockValue)}</p>
                </div>
                <div className="admin-card">
                    <p className="text-sm text-terroir-dark/50">Ruptures / Stock faible</p>
                    <p className={'mt-1 text-2xl font-bold ' + (outOfStock > 0 ? 'text-terroir-terracotta' : '')}>
                        {outOfStock} / {lowStock}
                    </p>
                </div>
            </div>

            <div className="mt-6 grid gap-4 sm:grid-cols-2">
                <div className="admin-card">
                    <p className="text-sm text-terroir-dark/50">Créances clients (impayés)</p>
                    <p className="mt-1 text-2xl font-bold text-terroir-terracotta">{fcfa(receivables)}</p>
                </div>
                <div className="admin-card">
                    <p className="text-sm text-terroir-dark/50">Dettes fournisseurs</p>
                    <p className="mt-1 text-2xl font-bold text-terroir-terracotta">{fcfa(payables)}</p>
                </div>
            </div>

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="admin-card mt-6"
            >
                <h2 className="font-display text-base font-semibold">Chiffre d'affaires — 6 derniers mois</h2>
                {revenueByMonth.length > 0 && (
                    <div className="mt-3">
                        <ChartCanvas
                            type="line"
                            height={280}
                            data={{
                                labels: revenueByMonth.map((r) => r.label),
                                datasets: [{
                                    data: revenueByMonth.map((r) => r.value),
                                    borderColor: chartPalette.green,
                                    backgroundColor: 'rgba(47, 158, 107, 0.12)',
                                    borderWidth: 2,
                                    pointRadius: 3,
                                    pointHoverRadius: 6,
                                    pointBackgroundColor: chartPalette.green,
                                    fill: true,
                                    tension: 0.3,
                                }],
                            }}
                            options={{
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: { callbacks: { label: (ctx) => formatFcfa(ctx.parsed.y) } },
                                },
                                scales: {
                                    y: { beginAtZero: true, grid: { color: 'rgba(91,58,41,0.08)' }, ticks: { callback: (v) => compact(v) } },
                                    x: { grid: { display: false } },
                                },
                            }}
                        />
                    </div>
                )}
                <div className="mt-3 flex flex-wrap gap-6 border-t border-terroir-green/10 pt-4 text-xs text-terroir-dark/60">
                    {revenueByMonth.map((row) => (
                        <span key={row.label}>{row.label} : <strong className="text-terroir-dark">{fcfa(row.value)}</strong></span>
                    ))}
                </div>
            </motion.div>

            <div className="mt-6 grid gap-6 lg:grid-cols-2">
                <div className="admin-card">
                    <h2 className="font-display text-base font-semibold">Produits les plus vendus</h2>
                    {topProducts.length > 0 && (
                        <div className="mt-3">
                            <ChartCanvas
                                type="bar"
                                height={280}
                                data={{
                                    labels: topProducts.map((p) => p.product_name),
                                    datasets: [{
                                        data: topProducts.map((p) => p.revenue),
                                        backgroundColor: chartPalette.green,
                                        borderRadius: 4,
                                        maxBarThickness: 22,
                                    }],
                                }}
                                options={{
                                    indexAxis: 'y',
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: { callbacks: { label: (ctx) => formatFcfa(ctx.parsed.x) } },
                                    },
                                    scales: {
                                        x: { beginAtZero: true, grid: { color: 'rgba(91,58,41,0.08)' }, ticks: { callback: (v) => compact(v) } },
                                        y: { grid: { display: false } },
                                    },
                                }}
                            />
                        </div>
                    )}
                </div>

                <div className="admin-card">
                    <h2 className="font-display text-base font-semibold">Ventes par canal</h2>
                    {salesByChannel.length > 0 ? (
                        <>
                            <div className="mt-3">
                                <ChartCanvas
                                    type="doughnut"
                                    height={280}
                                    data={{
                                        labels: salesByChannel.map((c) => c.label),
                                        datasets: [{
                                            data: salesByChannel.map((c) => c.value),
                                            backgroundColor: chartCategoricalOrder.slice(0, salesByChannel.length),
                                            borderColor: '#F6F1E4',
                                            borderWidth: 2,
                                        }],
                                    }}
                                    options={{
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12 } },
                                            tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${formatFcfa(ctx.parsed)}` } },
                                        },
                                    }}
                                />
                            </div>
                            <ul className="mt-3 space-y-1 border-t border-terroir-green/10 pt-4 text-xs text-terroir-dark/60">
                                {salesByChannel.map((row) => (
                                    <li key={row.label} className="flex justify-between">
                                        <span>{row.label}</span>
                                        <strong className="text-terroir-dark">{fcfa(row.value)}</strong>
                                    </li>
                                ))}
                            </ul>
                        </>
                    ) : (
                        <p className="mt-3 text-sm text-terroir-dark/50">Pas encore de données de vente.</p>
                    )}
                </div>
            </div>

            <div className="mt-6 grid gap-6 lg:grid-cols-2">
                <div className="admin-card">
                    <div className="flex items-center justify-between">
                        <h2 className="font-display text-base font-semibold">Top fournisseurs (montant achats)</h2>
                        <Link href={route('admin.fournisseurs.index')} className="admin-link">Voir tout →</Link>
                    </div>
                    {topSuppliers.length > 0 && (
                        <div className="mt-3">
                            <ChartCanvas
                                type="bar"
                                height={240}
                                data={{
                                    labels: topSuppliers.map((s) => s.label),
                                    datasets: [{
                                        data: topSuppliers.map((s) => s.value),
                                        backgroundColor: chartPalette.sienna,
                                        borderRadius: 4,
                                        maxBarThickness: 22,
                                    }],
                                }}
                                options={{
                                    indexAxis: 'y',
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: { callbacks: { label: (ctx) => formatFcfa(ctx.parsed.x) } },
                                    },
                                    scales: {
                                        x: { beginAtZero: true, grid: { color: 'rgba(91,58,41,0.08)' }, ticks: { callback: (v) => compact(v) } },
                                        y: { grid: { display: false } },
                                    },
                                }}
                            />
                        </div>
                    )}
                </div>

                <div className="admin-card">
                    <div className="flex items-center justify-between">
                        <h2 className="font-display text-base font-semibold">Produits dormants (60 jours sans vente)</h2>
                        <Link href={route('admin.produits.index')} className="admin-link">Voir tout →</Link>
                    </div>
                    <table className="admin-table mt-3">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th className="text-right">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            {dormantProducts.length === 0 ? (
                                <tr><td colSpan={2} className="py-6 text-center text-terroir-dark/40">Aucun produit dormant.</td></tr>
                            ) : dormantProducts.map((product) => (
                                <tr key={product.id}>
                                    <td>{product.name}</td>
                                    <td className="text-right font-semibold">{product.stock_quantity}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </AdminLayout>
    );
}
