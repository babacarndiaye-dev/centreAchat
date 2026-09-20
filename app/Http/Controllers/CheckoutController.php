<?php

namespace App\Http\Controllers;

use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\TaxRate;
use App\Services\AccountingService;
use App\Support\Cart;
use App\Support\Notifications\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $items = Cart::items();

        if ($items->isEmpty()) {
            return redirect()->route('panier.index')->with('error', 'Votre panier est vide.');
        }

        $subtotal = Cart::subtotal();
        $deliveryZones = DeliveryZone::where('is_active', true)->orderBy('position')->orderBy('name')->get();
        $selectedZone = $deliveryZones->firstWhere('id', (int) $request->query('zone')) ?? $deliveryZones->first();
        $deliveryFee = $selectedZone ? $selectedZone->feeFor($subtotal) : 0;

        $taxRate = TaxRate::default();
        $taxAmount = $taxRate ? round($subtotal * ((float) $taxRate->rate / 100), 2) : 0;

        $total = $subtotal + $deliveryFee + $taxAmount;

        return Inertia::render('Checkout/Index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'deliveryZones' => $deliveryZones->map(fn ($z) => [
                'id' => (string) $z->id,
                'name' => $z->name,
                'fee' => (float) $z->fee,
                'free_above' => $z->free_above !== null ? (float) $z->free_above : null,
            ]),
            'selectedZoneId' => $selectedZone?->id ? (string) $selectedZone->id : null,
            'taxRate' => $taxRate ? ['name' => $taxRate->name, 'rate' => (float) $taxRate->rate] : null,
        ]);
    }

    public function store(Request $request, AccountingService $accounting, NotificationService $notifications): RedirectResponse
    {
        $items = Cart::items();

        if ($items->isEmpty()) {
            return redirect()->route('panier.index')->with('error', 'Votre panier est vide.');
        }

        $validMethods = $this->availablePaymentMethods()->pluck('code')->push('credit')->unique();

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'delivery_address' => ['required', 'string', 'max:2000'],
            'city' => ['nullable', 'string', 'max:255'],
            'hotel_name' => ['nullable', 'string', 'max:255'],
            'room_number' => ['nullable', 'string', 'max:30'],
            'gift_message' => ['nullable', 'string', 'max:500'],
            'delivery_zone_id' => ['nullable', 'exists:delivery_zones,id'],
            'payment_method' => ['required', 'in:'.$validMethods->implode(',')],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $subtotal = Cart::subtotal();
        $zone = ($data['delivery_zone_id'] ?? null) ? DeliveryZone::find($data['delivery_zone_id']) : null;
        $deliveryFee = $zone ? $zone->feeFor($subtotal) : 0;

        $taxRate = TaxRate::default();
        $taxAmount = $taxRate ? round($subtotal * ((float) $taxRate->rate / 100), 2) : 0;

        $total = $subtotal + $deliveryFee + $taxAmount;

        if ($data['payment_method'] === 'credit') {
            if (! Auth::check() || ! Auth::user()->isApprovedB2B()) {
                return back()->withErrors(['payment_method' => 'Le paiement à crédit est réservé aux comptes professionnels validés.'])->withInput();
            }

            if ($total > Auth::user()->creditAvailable()) {
                return back()->withErrors(['payment_method' => 'Cette commande dépasse votre encours de crédit disponible ('.number_format(Auth::user()->creditAvailable(), 0, ',', ' ').' FCFA).'])->withInput();
            }
        }

        $order = DB::transaction(function () use ($data, $items, $subtotal, $deliveryFee, $taxAmount, $total, $zone) {
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => Auth::id(),
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'],
                'delivery_address' => $data['delivery_address'],
                'city' => $data['city'] ?? $zone?->name ?? 'Mbour',
                'hotel_name' => $data['hotel_name'] ?? null,
                'room_number' => $data['room_number'] ?? null,
                'gift_message' => $data['gift_message'] ?? null,
                'delivery_zone_id' => $data['delivery_zone_id'] ?? null,
                'status' => 'nouvelle',
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'tax_amount' => $taxAmount,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'payment_method' => $data['payment_method'],
                'payment_status' => 'en_attente',
                'invoice_due_date' => $data['payment_method'] === 'credit' ? now()->addDays(30) : null,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'product_name' => $item->product->name,
                    'unit_price' => $item->unit_price,
                    'price_tier' => $item->price_tier,
                    'quantity' => $item->quantity,
                    'total' => $item->total,
                ]);

                $item->product->decrement('stock_quantity', min($item->quantity, $item->product->stock_quantity));
            }

            return $order;
        });

        Cart::clear();

        $accounting->postSaleInvoice($order);

        $recipient = Auth::check()
            ? NotificationService::recipientFromUser(Auth::user())
            : NotificationService::recipientFromGuest($order->customer_name, $order->customer_email, $order->customer_phone);

        $notifications->send('commande_creee', [$recipient], [
            'client_nom' => $order->customer_name,
            'commande_numero' => $order->order_number,
            'commande_total' => number_format((float) $order->total, 0, ',', ' '),
            'commande_lien' => route('commande.confirmation', $order->order_number),
        ]);

        return redirect()->route('commande.confirmation', $order->order_number);
    }

    public function confirmation(string $orderNumber)
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();

        return Inertia::render('Checkout/Confirmation', ['order' => $order]);
    }

    protected function availablePaymentMethods()
    {
        $query = PaymentMethod::where('is_active', true)->where('available_online', true);

        if (! Auth::check() || ! Auth::user()->isApprovedB2B()) {
            $query->where('requires_b2b', false);
        }

        return $query->orderBy('position')->get();
    }
}
