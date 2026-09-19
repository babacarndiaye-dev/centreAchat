<?php

namespace App\Console\Commands\Notifications;

use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use App\Support\Notifications\NotificationService;
use Illuminate\Console\Command;

class RunScheduledChecks extends Command
{
    protected $signature = 'notifications:check';

    protected $description = "Vérifie le stock faible, les produits expirants et les factures échues, et envoie les notifications correspondantes";

    public function handle(NotificationService $notifications): int
    {
        $this->checkLowStock($notifications);
        $this->checkExpiringProducts($notifications);
        $this->checkOverdueInvoices($notifications);

        return self::SUCCESS;
    }

    protected function alreadyNotifiedToday(string $eventKey, string $entityKey, int $entityId): bool
    {
        return Notification::where('event_key', $eventKey)
            ->whereDate('created_at', today())
            ->where('data->'.$entityKey, $entityId)
            ->exists();
    }

    protected function checkLowStock(NotificationService $notifications): void
    {
        $products = Product::where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'stock_alert_threshold')
            ->get();

        $recipients = NotificationService::staffRecipients('stock.voir');

        foreach ($products as $product) {
            if ($this->alreadyNotifiedToday('stock_faible', 'product_id', $product->id)) {
                continue;
            }

            $notifications->send('stock_faible', $recipients, [
                'product_id' => $product->id,
                'produit_nom' => $product->name,
                'produit_stock' => $product->stock_quantity,
                'produit_seuil' => $product->stock_alert_threshold,
            ]);

            $this->line("Stock faible : {$product->name}");
        }
    }

    protected function checkExpiringProducts(NotificationService $notifications): void
    {
        $products = Product::where('is_active', true)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', today()->addDays(7))
            ->whereDate('expiry_date', '>=', today())
            ->get();

        $recipients = NotificationService::staffRecipients('stock.voir');

        foreach ($products as $product) {
            if ($this->alreadyNotifiedToday('produit_expirant', 'product_id', $product->id)) {
                continue;
            }

            $notifications->send('produit_expirant', $recipients, [
                'product_id' => $product->id,
                'produit_nom' => $product->name,
                'produit_expiration' => $product->expiry_date->format('d/m/Y'),
            ]);

            $this->line("Produit expirant : {$product->name}");
        }
    }

    protected function checkOverdueInvoices(NotificationService $notifications): void
    {
        $orders = Order::whereNotNull('invoice_due_date')
            ->whereDate('invoice_due_date', '<', today())
            ->where('payment_status', '!=', 'paye')
            ->whereNotIn('status', ['annulee', 'remboursee'])
            ->get();

        $recipients = NotificationService::staffRecipients('finance.voir');

        foreach ($orders as $order) {
            if ($this->alreadyNotifiedToday('facture_echue', 'order_id', $order->id)) {
                continue;
            }

            $notifications->send('facture_echue', $recipients, [
                'order_id' => $order->id,
                'commande_numero' => $order->order_number,
                'client_nom' => $order->customer_name,
                'commande_total' => number_format((float) $order->total, 0, ',', ' '),
                'commande_echeance' => $order->invoice_due_date->format('d/m/Y'),
            ]);

            $this->line("Facture échue : {$order->order_number}");
        }
    }
}
