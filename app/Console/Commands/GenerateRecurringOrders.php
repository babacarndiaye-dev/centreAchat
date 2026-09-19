<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RecurringOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateRecurringOrders extends Command
{
    protected $signature = 'orders:generate-recurring';

    protected $description = "Génère les commandes dues à partir des commandes récurrentes actives des clients B2B";

    public function handle(): int
    {
        $due = RecurringOrder::with(['user', 'items.product'])
            ->where('status', 'active')
            ->whereDate('next_run_date', '<=', now())
            ->get();

        if ($due->isEmpty()) {
            $this->info('Aucune commande récurrente due aujourd\'hui.');

            return self::SUCCESS;
        }

        foreach ($due as $recurringOrder) {
            DB::transaction(function () use ($recurringOrder) {
                $subtotal = 0;

                $order = Order::create([
                    'order_number' => Order::generateOrderNumber(),
                    'user_id' => $recurringOrder->user_id,
                    'customer_name' => $recurringOrder->user->name,
                    'customer_email' => $recurringOrder->user->email,
                    'customer_phone' => $recurringOrder->user->phone ?? '—',
                    'delivery_address' => $recurringOrder->delivery_address,
                    'city' => $recurringOrder->city,
                    'status' => 'nouvelle',
                    'subtotal' => 0,
                    'delivery_fee' => 0,
                    'total' => 0,
                    'payment_method' => $recurringOrder->payment_method,
                    'payment_status' => 'en_attente',
                    'notes' => 'Générée automatiquement depuis la commande récurrente #'.$recurringOrder->id,
                ]);

                foreach ($recurringOrder->items as $item) {
                    $unitPrice = $item->product->priceFor($recurringOrder->user, $item->quantity);
                    $lineTotal = $unitPrice * $item->quantity;
                    $subtotal += $lineTotal;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'unit_price' => $unitPrice,
                        'price_tier' => $item->product->priceTierFor($recurringOrder->user, $item->quantity),
                        'quantity' => $item->quantity,
                        'total' => $lineTotal,
                    ]);
                }

                $order->update(['subtotal' => $subtotal, 'total' => $subtotal]);

                $recurringOrder->update([
                    'last_run_date' => now(),
                    'next_run_date' => $recurringOrder->computeNextRunDate(),
                ]);

                $this->info("Commande {$order->order_number} générée pour {$recurringOrder->user->name}.");
            });
        }

        return self::SUCCESS;
    }
}
