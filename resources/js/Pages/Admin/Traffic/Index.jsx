import { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';
import ChartCanvas, { chartPalette, formatFcfa } from '../../../Components/Admin/ChartCanvas';
import Counter from '../../../Components/Counter';

const DEVICE_LABELS = { mobile: 'Mobile', desktop: 'Ordinateur', tablet: 'Tablette' };

const FUNNEL_STEPS = [
    ['sessions', 'Sessions'],
    ['product_view', 'Ont consulté un produit'],
    ['add_to_cart', 'Ont ajouté au panier'],
    ['purchase', 'Ont commandé'],
];

function LiveVisitors() {
    const [sessions, setSessions] = useState(null);

    useEffect(() => {
        let cancelled = false;

        async function poll() {
            try {
                const res = await fetch(route('admin.trafic.live'), { headers: { Accept: 'application/json' } });
                const data = await res.json();
                if (!cancelled) setSessions(data.sessions);
            } catch {
                // silent — next poll will retry
            }
        }

        poll();
        const interval = setInterval(poll, 15000);
        return () => { cancelled = true; clearInterval(interval); };
    }, []);

    return (
        <div className="admin-card">
            <div className="flex items-center gap-2">
                <span className="h-2.5 w-2.5 animate-pulse rounded-full bg-terroir-green" />
                <h2 className="font-display text-base font-semibold">
                    {sessions === null ? '…' : sessions.length} visiteur{(sessions?.length ?? 0) > 1 ? 's' : ''} en direct
                </h2>
            </div>
            <div className="mt-3 divide-y divide-terroir-dark/5">
                {sessions?.length === 0 && <p className="py-4 text-sm text-terroir-dark/50">Personne sur le site pour l'instant.</p>}
                {sessions?.map((s) => (
                    <div key={s.visitor_uid} className="flex items-center justify-between gap-3 py-2.5 text-sm">
                        <div>
                            <p className="font-medium text-terroir-dark">{s.visitor_uid}</p>
                            <p className="text-xs text-terroir-dark/50">/{s.page}</p>
                        </div>
                        <div className="text-right text-xs text-terroir-dark/50">
                            <p>{DEVICE_LABELS[s.device_type] ?? s.device_type} · {s.browser ?? '—'}</p>
                            <p>{s.country ?? '—'} · {s.last_activity_at}</p>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}

export default function TrafficIndex({ stats, visitsByDay, deviceBreakdown, topPages, topProducts, funnel }) {
    const deviceEntries = Object.entries(deviceBreakdown);

    return (
        <AdminLayout title="Trafic">
            <Head title="Trafic — Administration" />

            <div className="grid grid-cols-2 gap-4 lg:grid-cols-4">
                {[
                    ['Visiteurs (30j)', stats.visitors],
                    ['Sessions (30j)', stats.sessions],
                    ['Commandes (30j)', stats.orders],
                    ['CA (30j)', stats.revenue, formatFcfa],
                ].map(([label, value, format], i) => (
                    <motion.div
                        key={label}
                        initial={{ opacity: 0, y: 12 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.35, delay: i * 0.05 }}
                        className="admin-card"
                    >
                        <p className="text-sm text-terroir-dark/50">{label}</p>
                        <p className="mt-1 text-2xl font-bold text-terroir-green">
                            <Counter value={value} delay={i * 0.05} format={(v) => (format ? format(v) : Math.round(v).toLocaleString('fr-FR'))} />
                        </p>
                    </motion.div>
                ))}
            </div>

            <div className="mt-6 grid gap-6 lg:grid-cols-3">
                <motion.div
                    initial={{ opacity: 0, y: 12 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.35, delay: 0.15 }}
                    className="admin-card lg:col-span-2"
                >
                    <h2 className="font-display text-base font-semibold">Visites — 30 derniers jours</h2>
                    <div className="mt-3">
                        <ChartCanvas
                            type="line"
                            height={260}
                            data={{
                                labels: visitsByDay.map((r) => r.label),
                                datasets: [{
                                    data: visitsByDay.map((r) => r.value),
                                    borderColor: chartPalette.green,
                                    backgroundColor: 'rgba(47, 158, 107, 0.12)',
                                    borderWidth: 2,
                                    pointRadius: 0,
                                    fill: true,
                                    tension: 0.3,
                                }],
                            }}
                            options={{
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    y: { beginAtZero: true, grid: { color: 'rgba(91,58,41,0.08)' } },
                                    x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 8 } },
                                },
                            }}
                        />
                    </div>
                </motion.div>

                <motion.div
                    initial={{ opacity: 0, y: 12 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.35, delay: 0.2 }}
                    className="admin-card"
                >
                    <h2 className="font-display text-base font-semibold">Appareils</h2>
                    {deviceEntries.length > 0 ? (
                        <div className="mt-3">
                            <ChartCanvas
                                type="doughnut"
                                height={200}
                                data={{
                                    labels: deviceEntries.map(([k]) => DEVICE_LABELS[k] ?? k),
                                    datasets: [{
                                        data: deviceEntries.map(([, v]) => v),
                                        backgroundColor: [chartPalette.green, chartPalette.gold, chartPalette.blue],
                                        borderColor: '#F6F1E4',
                                        borderWidth: 2,
                                    }],
                                }}
                                options={{
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 10 } } },
                                }}
                            />
                        </div>
                    ) : (
                        <p className="mt-3 text-sm text-terroir-dark/50">Pas encore de données.</p>
                    )}
                </motion.div>
            </div>

            <div className="mt-6 grid gap-6 lg:grid-cols-2">
                <motion.div initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.35, delay: 0.25 }} className="admin-card">
                    <h2 className="font-display text-base font-semibold">Pages les plus vues</h2>
                    <table className="admin-table mt-3">
                        <tbody>
                            {topPages.length === 0 ? (
                                <tr><td className="py-6 text-center text-terroir-dark/40">Aucune donnée.</td></tr>
                            ) : topPages.map((row) => (
                                <tr key={row.url}>
                                    <td>/{row.url}</td>
                                    <td className="text-right font-semibold">{row.total}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </motion.div>

                <motion.div initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.35, delay: 0.3 }} className="admin-card">
                    <h2 className="font-display text-base font-semibold">Produits les plus consultés</h2>
                    <table className="admin-table mt-3">
                        <tbody>
                            {topProducts.length === 0 ? (
                                <tr><td className="py-6 text-center text-terroir-dark/40">Aucune donnée.</td></tr>
                            ) : topProducts.map((row) => (
                                <tr key={row.name}>
                                    <td>{row.name}</td>
                                    <td className="text-right font-semibold">{row.views} vues</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </motion.div>
            </div>

            <div className="mt-6 grid gap-6 lg:grid-cols-2">
                <motion.div initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.35, delay: 0.35 }} className="admin-card">
                    <h2 className="font-display text-base font-semibold">Entonnoir de conversion (30j)</h2>
                    <div className="mt-4 space-y-3">
                        {FUNNEL_STEPS.map(([key, label]) => {
                            const value = funnel[key] ?? 0;
                            const pct = funnel.sessions > 0 ? Math.round((value / funnel.sessions) * 100) : 0;
                            return (
                                <div key={key}>
                                    <div className="flex items-center justify-between text-sm">
                                        <span className="text-terroir-dark/70">{label}</span>
                                        <span className="font-semibold text-terroir-dark">{value} ({pct}%)</span>
                                    </div>
                                    <div className="mt-1 h-2 rounded-[2px] bg-terroir-dark/5">
                                        <div className="h-2 rounded-[2px] bg-terroir-green" style={{ width: `${pct}%` }} />
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </motion.div>

                <motion.div initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.35, delay: 0.4 }}>
                    <LiveVisitors />
                </motion.div>
            </div>
        </AdminLayout>
    );
}
