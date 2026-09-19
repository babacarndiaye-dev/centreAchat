<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $supplier = Auth::user()->supplier;
        $supplier->load(['purchaseOrders' => fn ($q) => $q->latest()->take(5), 'payments' => fn ($q) => $q->latest()->take(5)]);

        $stats = [
            'pending_confirmation' => $supplier->purchaseOrders()->where('status', 'envoyee')->count(),
            'in_progress' => $supplier->purchaseOrders()->whereIn('status', ['confirmee', 'partiellement_recue'])->count(),
            'total_owed' => $supplier->totalOwed(),
            'balance' => $supplier->balance(),
        ];

        return view('portal.dashboard', compact('supplier', 'stats'));
    }
}
