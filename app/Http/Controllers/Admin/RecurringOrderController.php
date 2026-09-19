<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecurringOrder;

class RecurringOrderController extends Controller
{
    public function index()
    {
        $recurringOrders = RecurringOrder::with(['user', 'items.product'])->latest()->paginate(20);

        return view('admin.recurring-orders.index', compact('recurringOrders'));
    }
}
