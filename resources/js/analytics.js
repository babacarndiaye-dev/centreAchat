import { Chart, LineController, BarController, DoughnutController, LineElement, BarElement, ArcElement, PointElement, LinearScale, CategoryScale, Tooltip, Legend } from 'chart.js';

Chart.register(LineController, BarController, DoughnutController, LineElement, BarElement, ArcElement, PointElement, LinearScale, CategoryScale, Tooltip, Legend);

const palette = {
    green: '#009C4A',
    terracotta: '#C62828',
    gold: '#101818',
    mint: '#1DBF63',
    sienna: '#6B726D',
    blue: '#0A7EA4',
};

const categoricalOrder = [palette.green, palette.terracotta, palette.gold, palette.mint, palette.sienna, palette.blue];

const baseFont = { family: 'Montserrat, sans-serif', size: 12 };

Chart.defaults.font = baseFont;
Chart.defaults.color = '#6B726D';
Chart.defaults.plugins.tooltip.backgroundColor = '#101818';
Chart.defaults.plugins.tooltip.padding = 10;
Chart.defaults.plugins.tooltip.cornerRadius = 8;
Chart.defaults.plugins.tooltip.titleFont = baseFont;
Chart.defaults.plugins.tooltip.bodyFont = baseFont;

function formatFcfa(value) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(value)) + ' FCFA';
}

export function initAnalyticsCharts(data) {
    const revenueCanvas = document.getElementById('chart-revenue');
    if (revenueCanvas && data.revenue?.length) {
        new Chart(revenueCanvas, {
            type: 'line',
            data: {
                labels: data.revenue.map((r) => r.label),
                datasets: [{
                    data: data.revenue.map((r) => r.value),
                    borderColor: palette.green,
                    backgroundColor: 'rgba(47, 158, 107, 0.12)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: palette.green,
                    fill: true,
                    tension: 0.3,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: (ctx) => formatFcfa(ctx.parsed.y) } },
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(91,58,41,0.08)' }, ticks: { callback: (v) => new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(v) } },
                    x: { grid: { display: false } },
                },
            },
        });
    }

    const productsCanvas = document.getElementById('chart-products');
    if (productsCanvas && data.products?.length) {
        new Chart(productsCanvas, {
            type: 'bar',
            data: {
                labels: data.products.map((p) => p.product_name),
                datasets: [{
                    data: data.products.map((p) => p.revenue),
                    backgroundColor: palette.green,
                    borderRadius: 4,
                    maxBarThickness: 22,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: (ctx) => formatFcfa(ctx.parsed.x) } },
                },
                scales: {
                    x: { beginAtZero: true, grid: { color: 'rgba(91,58,41,0.08)' }, ticks: { callback: (v) => new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(v) } },
                    y: { grid: { display: false } },
                },
            },
        });
    }

    const channelsCanvas = document.getElementById('chart-channels');
    if (channelsCanvas && data.channels?.length) {
        new Chart(channelsCanvas, {
            type: 'doughnut',
            data: {
                labels: data.channels.map((c) => c.label),
                datasets: [{
                    data: data.channels.map((c) => c.value),
                    backgroundColor: categoricalOrder.slice(0, data.channels.length),
                    borderColor: '#F0F0E8',
                    borderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12 } },
                    tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${formatFcfa(ctx.parsed)}` } },
                },
            },
        });
    }

    const suppliersCanvas = document.getElementById('chart-suppliers');
    if (suppliersCanvas && data.suppliers?.length) {
        new Chart(suppliersCanvas, {
            type: 'bar',
            data: {
                labels: data.suppliers.map((s) => s.label),
                datasets: [{
                    data: data.suppliers.map((s) => s.value),
                    backgroundColor: palette.sienna,
                    borderRadius: 4,
                    maxBarThickness: 22,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: (ctx) => formatFcfa(ctx.parsed.x) } },
                },
                scales: {
                    x: { beginAtZero: true, grid: { color: 'rgba(91,58,41,0.08)' }, ticks: { callback: (v) => new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(v) } },
                    y: { grid: { display: false } },
                },
            },
        });
    }
}
