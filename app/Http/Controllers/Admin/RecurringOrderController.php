<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecurringOrder;
use Inertia\Inertia;

class RecurringOrderController extends Controller
{
    public function index()
    {
        $recurringOrders = RecurringOrder::with(['user', 'items.product'])->latest()->paginate(20);
        $recurringOrders->getCollection()->transform(fn (RecurringOrder $ro) => [
            'id' => $ro->id,
            'user_name' => $ro->user->name,
            'frequency' => $ro->frequency,
            'products' => $ro->items->pluck('product.name')->join(', '),
            'next_run_date' => $ro->next_run_date->format('d/m/Y'),
            'status' => $ro->status,
        ]);

        return Inertia::render('Admin/RecurringOrders/Index', [
            'recurringOrders' => $recurringOrders,
            'frequencies' => RecurringOrder::FREQUENCIES,
        ]);
    }
}
