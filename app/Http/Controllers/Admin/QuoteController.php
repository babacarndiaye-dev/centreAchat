<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Quote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Quote::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $quotes = $query->latest()->paginate(20)->withQueryString();

        return view('admin.quotes.index', compact('quotes'));
    }

    public function show(Quote $devis)
    {
        $devis->load(['items.product', 'user']);

        return view('admin.quotes.show', ['quote' => $devis]);
    }

    public function send(Request $request, Quote $devis): RedirectResponse
    {
        $data = $request->validate([
            'unit_price' => ['required', 'array'],
            'unit_price.*' => ['required', 'numeric', 'min:0'],
            'valid_until' => ['nullable', 'date'],
        ]);

        $total = 0;

        foreach ($devis->items as $item) {
            $price = $data['unit_price'][$item->id] ?? 0;
            $item->update(['unit_price' => $price]);
            $total += $price * $item->quantity;
        }

        $devis->update([
            'status' => 'envoye',
            'total' => $total,
            'valid_until' => $data['valid_until'] ?? now()->addDays(7),
        ]);

        return back()->with('success', 'Devis envoyé au client.');
    }

    public function convert(Quote $devis): RedirectResponse
    {
        if ($devis->status !== 'accepte') {
            return back()->with('error', 'Seul un devis accepté peut être converti en commande.');
        }

        $order = DB::transaction(function () use ($devis) {
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $devis->user_id,
                'customer_name' => $devis->user->name,
                'customer_email' => $devis->user->email,
                'customer_phone' => $devis->user->phone ?? '—',
                'delivery_address' => 'À confirmer avec le client',
                'city' => '—',
                'status' => 'nouvelle',
                'subtotal' => $devis->total,
                'delivery_fee' => 0,
                'total' => $devis->total,
                'payment_method' => 'especes',
                'payment_status' => 'en_attente',
                'notes' => 'Commande générée depuis le devis '.$devis->quote_number,
            ]);

            foreach ($devis->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'total' => $item->total(),
                ]);
            }

            $devis->update(['status' => 'converti', 'order_id' => $order->id]);

            return $order;
        });

        return redirect()->route('admin.commandes.show', $order)->with('success', 'Devis converti en commande '.$order->order_number.'.');
    }
}
