<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use App\Models\Order;
use App\Models\PaymentAccount;
use App\Models\PaymentAccountTransaction;
use App\Models\PosReturn;
use App\Models\PosReturnItem;
use App\Services\AccountingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PosReturnController extends Controller
{
    public function create(Request $request)
    {
        $order = null;

        if ($request->filled('order_number')) {
            $order = Order::with('items.product')->where('order_number', $request->string('order_number'))->first();

            if (! $order) {
                return redirect()->route('admin.pos.retours.create')->with('error', 'Commande introuvable.');
            }
        }

        return Inertia::render('Admin/Pos/Returns/Create', [
            'orderNumber' => $request->string('order_number')->toString(),
            'order' => $order ? [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'created_at' => $order->created_at->format('d/m/Y'),
                'items' => $order->items->filter(fn ($item) => $item->returnableQuantity() > 0)->values()->map(fn ($item) => [
                    'id' => $item->id,
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'returnable_quantity' => $item->returnableQuantity(),
                ]),
            ] : null,
        ]);
    }

    public function store(Request $request, AccountingService $accounting): RedirectResponse
    {
        $register = CashRegister::where('status', 'ouverte')->first();

        $data = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'quantity' => ['required', 'array'],
            'quantity.*' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $order = Order::with('items')->findOrFail($data['order_id']);
        $lines = collect($data['quantity'])->filter(fn ($qty) => $qty > 0);

        if ($lines->isEmpty()) {
            return back()->withErrors(['quantity' => 'Veuillez indiquer au moins une quantité à retourner.'])->withInput();
        }

        $posReturn = DB::transaction(function () use ($order, $lines, $data, $register) {
            $totalRefund = 0;

            $posReturn = PosReturn::create([
                'order_id' => $order->id,
                'cash_register_id' => $register?->id,
                'processed_by' => Auth::id(),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($lines as $orderItemId => $quantity) {
                $orderItem = $order->items->firstWhere('id', (int) $orderItemId);

                if (! $orderItem) {
                    continue;
                }

                $quantity = min((int) $quantity, $orderItem->returnableQuantity());

                if ($quantity <= 0) {
                    continue;
                }

                $lineTotal = $quantity * $orderItem->unit_price;
                $totalRefund += $lineTotal;

                PosReturnItem::create([
                    'pos_return_id' => $posReturn->id,
                    'order_item_id' => $orderItem->id,
                    'quantity' => $quantity,
                    'unit_price' => $orderItem->unit_price,
                    'total' => $lineTotal,
                ]);

                $orderItem->product?->increment('stock_quantity', $quantity);
            }

            $posReturn->update(['total_refund' => $totalRefund]);

            $allReturned = $order->items->every(fn ($item) => $item->fresh()->returnableQuantity() <= 0);
            $order->update(['status' => $allReturned ? 'remboursee' : $order->status]);

            return $posReturn;
        });

        if ($posReturn->total_refund > 0) {
            $caisseAccount = PaymentAccount::where('type', 'caisse')->first();

            if ($caisseAccount) {
                PaymentAccountTransaction::create([
                    'payment_account_id' => $caisseAccount->id,
                    'type' => 'sortie',
                    'amount' => $posReturn->total_refund,
                    'category' => 'Remboursement',
                    'description' => 'Remboursement commande '.$order->order_number,
                    'reference' => 'RET-'.$posReturn->id,
                    'transaction_date' => now(),
                    'created_by' => Auth::id(),
                ]);
            }

            $accounting->postSaleReturn($posReturn->fresh('order'));
        }

        return redirect()->route('admin.pos.retours.index')->with('success', 'Retour enregistré : '.number_format($posReturn->total_refund, 0, ',', ' ').' FCFA remboursés.');
    }

    public function index()
    {
        $returns = PosReturn::with(['order', 'processedBy'])->latest()->paginate(20)->withQueryString()->through(fn (PosReturn $r) => [
            'id' => $r->id,
            'order_number' => $r->order->order_number,
            'processed_by_name' => $r->processedBy?->name,
            'created_at' => $r->created_at->format('d/m/Y H:i'),
            'total_refund' => (float) $r->total_refund,
        ]);

        return Inertia::render('Admin/Pos/Returns/Index', [
            'returns' => $returns,
        ]);
    }
}
