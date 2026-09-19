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

class PosReturnController extends Controller
{
    public function create(Request $request)
    {
        $order = null;

        if ($request->filled('order_number')) {
            $order = Order::with('items')->where('order_number', $request->string('order_number'))->first();

            if (! $order) {
                return back()->withErrors(['order_number' => 'Commande introuvable.']);
            }
        }

        return view('admin.pos.returns.create', compact('order'));
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
        $returns = PosReturn::with(['order', 'processedBy'])->latest()->paginate(20);

        return view('admin.pos.returns.index', compact('returns'));
    }
}
