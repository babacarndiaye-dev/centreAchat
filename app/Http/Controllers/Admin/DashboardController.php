<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'revenue_month' => Order::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->whereNotIn('status', ['annulee', 'remboursee'])->sum('total'),
            'orders_month' => Order::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'orders_pending' => Order::whereIn('status', ['nouvelle', 'confirmee', 'en_preparation'])->count(),
            'products_count' => Product::count(),
            'products_low_stock' => Product::whereColumn('stock_quantity', '<=', 'stock_alert_threshold')->count(),
            'unread_messages' => ContactMessage::where('is_read', false)->count(),
        ];

        $recentOrders = Order::latest()->take(8)->get();

        $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'stock_alert_threshold')->take(8)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'lowStockProducts'));
    }
}
