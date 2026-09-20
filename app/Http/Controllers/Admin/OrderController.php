<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentAccount;
use App\Models\PaymentAccountTransaction;
use App\Services\AccountingService;
use App\Support\Notifications\NotificationService;
use App\Support\Orders\OrderAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('q')) {
            $search = $request->string('q');
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(20)->withQueryString();
        $orders->getCollection()->transform(fn (Order $order) => [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'created_at' => $order->created_at->format('d/m/Y H:i'),
            'status' => $order->status,
            'status_label' => Order::STATUSES[$order->status] ?? $order->status,
            'status_badge_class' => $order->statusBadgeClass(),
            'total' => (float) $order->total,
        ]);

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'statuses' => Order::STATUSES,
            'filters' => ['q' => $request->input('q', ''), 'status' => $request->input('status', '')],
        ]);
    }

    public function show(Order $order, OrderAssistantService $assistant)
    {
        $order->load('items.product');
        $paymentAccounts = PaymentAccount::where('is_active', true)->get(['id', 'name']);
        $flags = $assistant->flags($order);
        $amountPaid = $order->amountPaid();

        return Inertia::render('Admin/Orders/Show', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'status_label' => Order::STATUSES[$order->status] ?? $order->status,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'customer_email' => $order->customer_email,
                'delivery_address' => $order->delivery_address,
                'city' => $order->city,
                'hotel_name' => $order->hotel_name,
                'room_number' => $order->room_number,
                'gift_message' => $order->gift_message,
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'payment_status_label' => Order::PAYMENT_STATUSES[$order->payment_status] ?? $order->payment_status,
                'notes' => $order->notes,
                'subtotal' => (float) $order->subtotal,
                'delivery_fee' => (float) $order->delivery_fee,
                'total' => (float) $order->total,
                'amount_paid' => $amountPaid,
                'amount_due' => max(0, (float) $order->total - $amountPaid),
                'items' => $order->items->map(fn ($item) => [
                    'id' => $item->id,
                    'product_name' => $item->product_name,
                    'price_tier' => $item->price_tier,
                    'price_tier_label' => $item->price_tier !== 'retail' ? $item->priceTierLabel() : null,
                    'unit_price' => (float) $item->unit_price,
                    'quantity' => $item->quantity,
                    'total' => (float) $item->total,
                ]),
            ],
            'paymentAccounts' => $paymentAccounts,
            'flags' => $flags,
            'statuses' => Order::STATUSES,
        ]);
    }

    public function suggestion(Order $order, OrderAssistantService $assistant): JsonResponse
    {
        $flags = $assistant->flags($order);
        $result = $assistant->suggest($order, $flags);

        return response()->json([
            'available' => $result['note'] !== null || $result['customer_message'] !== null,
            'note' => $result['note'],
            'customer_message' => $result['customer_message'],
            'sent' => $result['sent'],
        ]);
    }

    public function recordPayment(Request $request, Order $order, AccountingService $accounting, NotificationService $notifications): RedirectResponse
    {
        if ($order->payment_status === 'paye') {
            return back()->with('error', 'Cette commande est déjà entièrement payée.');
        }

        $data = $request->validate([
            'payment_account_id' => ['required', 'exists:payment_accounts,id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:'.$order->total],
        ]);

        $account = PaymentAccount::findOrFail($data['payment_account_id']);

        PaymentAccountTransaction::create([
            'payment_account_id' => $account->id,
            'type' => 'entree',
            'amount' => $data['amount'],
            'category' => 'Vente',
            'description' => 'Encaissement commande '.$order->order_number,
            'reference' => $order->order_number,
            'transaction_date' => now(),
            'created_by' => Auth::id(),
        ]);

        $accounting->postCustomerPayment($order, (float) $data['amount'], $account);

        $order->update([
            'payment_status' => $data['amount'] >= $order->total ? 'paye' : 'partiellement_paye',
        ]);

        $solde = max(0, (float) $order->total - $order->amountPaid());

        $notifications->send('paiement_recu', [$this->orderRecipient($order)], [
            'client_nom' => $order->customer_name,
            'commande_numero' => $order->order_number,
            'paiement_montant' => number_format((float) $data['amount'], 0, ',', ' '),
            'commande_solde' => number_format($solde, 0, ',', ' '),
        ]);

        return back()->with('success', 'Paiement enregistré et écriture comptable générée.');
    }

    public function updateStatus(Request $request, Order $order, NotificationService $notifications): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_keys(Order::STATUSES))],
        ]);

        $order->update($data);

        $variables = [
            'client_nom' => $order->customer_name,
            'commande_numero' => $order->order_number,
            'commande_statut' => Order::STATUSES[$order->status] ?? $order->status,
        ];

        $notifications->send('commande_statut', [$this->orderRecipient($order)], $variables);

        if (in_array($order->status, ['en_livraison', 'livree'], true)) {
            $notifications->send('livraison_statut', [$this->orderRecipient($order)], $variables);
        }

        return back()->with('success', 'Statut de la commande mis à jour.');
    }

    protected function orderRecipient(Order $order): array
    {
        return $order->user
            ? NotificationService::recipientFromUser($order->user)
            : NotificationService::recipientFromGuest($order->customer_name, $order->customer_email, $order->customer_phone);
    }
}
