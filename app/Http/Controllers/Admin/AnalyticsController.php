<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AnalyticsController extends Controller
{
    protected const EXCLUDED_STATUSES = ['annulee', 'remboursee', 'brouillon'];

    public function index()
    {
        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->startOfMonth());

        $revenueByMonth = $months->map(function ($month) {
            $total = Order::whereNotIn('status', self::EXCLUDED_STATUSES)
                ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->sum('total');

            return ['label' => ucfirst($month->translatedFormat('M Y')), 'value' => (float) $total];
        });

        $ordersThisMonth = Order::whereNotIn('status', self::EXCLUDED_STATUSES)
            ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)
            ->count();

        $revenueThisMonth = (float) $revenueByMonth->last()['value'];
        $averageBasket = $ordersThisMonth > 0 ? $revenueThisMonth / $ordersThisMonth : 0;

        $topProducts = OrderItem::select('product_name', DB::raw('SUM(total) as revenue'), DB::raw('SUM(quantity) as qty'))
            ->whereHas('order', fn ($q) => $q->whereNotIn('status', self::EXCLUDED_STATUSES))
            ->groupBy('product_name')
            ->orderByDesc('revenue')
            ->take(8)
            ->get();

        $salesByChannel = collect(Order::CHANNELS)->map(function ($label, $channel) {
            $total = Order::where('channel', $channel)->whereNotIn('status', self::EXCLUDED_STATUSES)->sum('total');

            return ['label' => $label, 'value' => (float) $total];
        })->filter(fn ($row) => $row['value'] > 0)->values();

        $topSuppliers = PurchaseOrder::select('supplier_id', DB::raw('SUM(total) as spend'))
            ->whereNotIn('status', ['annulee', 'brouillon'])
            ->groupBy('supplier_id')
            ->orderByDesc('spend')
            ->take(6)
            ->with('supplier')
            ->get();

        $stockValue = Product::where('is_active', true)->get()->sum(fn ($p) => $p->stock_quantity * $p->price);
        $lowStock = Product::whereColumn('stock_quantity', '<=', 'stock_alert_threshold')->where('stock_quantity', '>', 0)->count();
        $outOfStock = Product::where('stock_quantity', 0)->count();

        $dormantProducts = Product::where('is_active', true)
            ->whereDoesntHave('orderItems', function ($q) {
                $q->whereHas('order', fn ($oq) => $oq->where('created_at', '>=', now()->subDays(60))->whereNotIn('status', self::EXCLUDED_STATUSES));
            })
            ->orderByDesc('stock_quantity')
            ->take(6)
            ->get();

        $receivables = Order::whereIn('payment_status', ['en_attente', 'partiellement_paye'])
            ->whereNotIn('status', self::EXCLUDED_STATUSES)
            ->get()
            ->sum(fn ($order) => $order->total - $order->amountPaid());

        $payables = Supplier::all()->sum(fn ($supplier) => $supplier->balance());

        return Inertia::render('Admin/Analytics/Index', [
            'revenueByMonth' => $revenueByMonth->values(),
            'revenueThisMonth' => $revenueThisMonth,
            'ordersThisMonth' => $ordersThisMonth,
            'averageBasket' => $averageBasket,
            'topProducts' => $topProducts->map(fn ($p) => [
                'product_name' => $p->product_name,
                'revenue' => (float) $p->revenue,
                'qty' => (int) $p->qty,
            ]),
            'salesByChannel' => $salesByChannel,
            'topSuppliers' => $topSuppliers->map(fn ($s) => ['label' => $s->supplier->name ?? '—', 'value' => (float) $s->spend]),
            'stockValue' => (float) $stockValue,
            'lowStock' => $lowStock,
            'outOfStock' => $outOfStock,
            'dormantProducts' => $dormantProducts->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'stock_quantity' => $p->stock_quantity]),
            'receivables' => (float) $receivables,
            'payables' => (float) $payables,
        ]);
    }
}
