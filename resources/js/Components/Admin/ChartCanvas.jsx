import { useEffect, useRef } from 'react';
import {
    Chart,
    LineController,
    BarController,
    DoughnutController,
    LineElement,
    BarElement,
    ArcElement,
    PointElement,
    LinearScale,
    CategoryScale,
    Tooltip,
    Legend,
} from 'chart.js';

Chart.register(
    LineController,
    BarController,
    DoughnutController,
    LineElement,
    BarElement,
    ArcElement,
    PointElement,
    LinearScale,
    CategoryScale,
    Tooltip,
    Legend
);

export const chartPalette = {
    green: '#2F9E6B',
    terracotta: '#C1440E',
    gold: '#D9A61E',
    mint: '#3FAF8C',
    sienna: '#A0522D',
    blue: '#0A7EA4',
};

export const chartCategoricalOrder = [
    chartPalette.green,
    chartPalette.terracotta,
    chartPalette.gold,
    chartPalette.mint,
    chartPalette.sienna,
    chartPalette.blue,
];

Chart.defaults.font = { family: 'Figtree, sans-serif', size: 12 };
Chart.defaults.color = '#5B3A29';
Chart.defaults.plugins.tooltip.backgroundColor = '#16201B';
Chart.defaults.plugins.tooltip.padding = 10;
Chart.defaults.plugins.tooltip.cornerRadius = 8;
Chart.defaults.plugins.tooltip.titleFont = Chart.defaults.font;
Chart.defaults.plugins.tooltip.bodyFont = Chart.defaults.font;

export function formatFcfa(value) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(value)) + ' FCFA';
}

export default function ChartCanvas({ type, data, options, height = 280 }) {
    const canvasRef = useRef(null);
    const chartRef = useRef(null);

    useEffect(() => {
        if (!canvasRef.current) return undefined;
        chartRef.current = new Chart(canvasRef.current, { type, data, options });
        return () => chartRef.current?.destroy();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [JSON.stringify(data), JSON.stringify(options), type]);

    return (
        <div style={{ height }}>
            <canvas ref={canvasRef} />
        </div>
    );
}
