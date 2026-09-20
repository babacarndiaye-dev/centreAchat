<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $orders = $user->orders()->latest()->paginate(10)->through(fn (Order $order) => [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'created_at' => $order->created_at->format('d/m/Y'),
            'status_label' => Order::STATUSES[$order->status] ?? $order->status,
            'total' => (float) $order->total,
        ]);

        return Inertia::render('Account/Index', [
            'orders' => $orders,
            'isProfessional' => $user->isProfessionalType(),
            'isApprovedB2b' => $user->isApprovedB2B(),
            'creditLimit' => $user->credit_limit ? (float) $user->credit_limit : null,
            'creditUsed' => $user->creditUsed(),
            'creditAvailable' => $user->creditAvailable(),
            'vapidPublicKey' => config('services.vapid.public_key'),
        ]);
    }
}
