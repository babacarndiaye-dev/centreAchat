<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RecurringOrder;
use App\Models\RecurringOrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecurringOrderController extends Controller
{
    public function index()
    {
        $recurringOrders = Auth::user()->recurringOrders()->with('items.product')->latest()->get();

        return view('account.recurring-orders.index', compact('recurringOrders'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('account.recurring-orders.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'frequency' => ['required', 'in:hebdomadaire,bimensuelle,mensuelle'],
            'delivery_address' => ['required', 'string', 'max:2000'],
            'city' => ['required', 'string', 'max:255'],
            'payment_method' => ['required', 'in:especes,wave,orange_money,credit'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'products' => ['required', 'array', 'min:1'],
            'products.*' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($data['payment_method'] === 'credit' && ! Auth::user()->isApprovedB2B()) {
            return back()->withErrors(['payment_method' => 'Le paiement à crédit est réservé aux comptes professionnels validés.'])->withInput();
        }

        $items = collect($data['products'])->filter(fn ($qty) => $qty > 0);

        if ($items->isEmpty()) {
            return back()->withErrors(['products' => 'Veuillez indiquer au moins une quantité.'])->withInput();
        }

        $recurringOrder = DB::transaction(function () use ($data, $items) {
            $recurringOrder = RecurringOrder::create([
                'user_id' => Auth::id(),
                'frequency' => $data['frequency'],
                'delivery_address' => $data['delivery_address'],
                'city' => $data['city'],
                'payment_method' => $data['payment_method'],
                'status' => 'active',
                'next_run_date' => now()->addDay(),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($items as $productId => $quantity) {
                RecurringOrderItem::create([
                    'recurring_order_id' => $recurringOrder->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }

            return $recurringOrder;
        });

        return redirect()->route('compte.commandes-recurrentes.index')->with('success', 'Commande récurrente programmée avec succès.');
    }

    public function toggle(RecurringOrder $commandeRecurrente): RedirectResponse
    {
        $this->authorizeOwnership($commandeRecurrente);

        $commandeRecurrente->update([
            'status' => $commandeRecurrente->status === 'active' ? 'suspendu' : 'active',
        ]);

        return back()->with('success', $commandeRecurrente->status === 'active' ? 'Commande récurrente réactivée.' : 'Commande récurrente suspendue.');
    }

    public function destroy(RecurringOrder $commandeRecurrente): RedirectResponse
    {
        $this->authorizeOwnership($commandeRecurrente);

        $commandeRecurrente->delete();

        return back()->with('success', 'Commande récurrente supprimée.');
    }

    protected function authorizeOwnership(RecurringOrder $recurringOrder): void
    {
        if ($recurringOrder->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
