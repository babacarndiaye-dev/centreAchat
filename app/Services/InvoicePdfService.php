<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Quote;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PdfInstance;

class InvoicePdfService
{
    protected function company(): array
    {
        return [
            'name' => Setting::get('site_name') ?: 'DIABA HOTEL',
            'address' => Setting::get('address'),
            'phone' => Setting::get('phone'),
            'email' => Setting::get('email'),
        ];
    }

    public function forOrder(Order $order): PdfInstance
    {
        $order->loadMissing('items');

        $data = [
            'documentType' => 'Facture',
            'documentNumber' => $order->order_number,
            'date' => $order->created_at->format('d/m/Y'),
            'dueLabel' => $order->invoice_due_date ? 'Échéance' : null,
            'dueDate' => $order->invoice_due_date?->format('d/m/Y'),
            'company' => $this->company(),
            'client' => [
                'name' => $order->customer_name,
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
                'company' => null,
            ],
            'items' => $order->items->map(fn ($item) => [
                'name' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'total' => (float) $item->total,
            ])->all(),
            'subtotal' => (float) $order->subtotal,
            'deliveryFee' => (float) $order->delivery_fee,
            'taxAmount' => (float) $order->tax_amount,
            'discountAmount' => (float) $order->discount_amount,
            'total' => (float) $order->total,
            'notes' => $order->notes,
        ];

        return Pdf::loadView('pdf.invoice', $data);
    }

    public function forQuote(Quote $quote): PdfInstance
    {
        $quote->loadMissing(['items.product', 'user']);

        $data = [
            'documentType' => 'Devis',
            'documentNumber' => $quote->quote_number,
            'date' => $quote->created_at->format('d/m/Y'),
            'dueLabel' => $quote->valid_until ? 'Valable jusqu\'au' : null,
            'dueDate' => $quote->valid_until?->format('d/m/Y'),
            'company' => $this->company(),
            'client' => [
                'name' => $quote->user->name,
                'email' => $quote->user->email,
                'phone' => $quote->user->phone,
                'company' => $quote->user->company_name,
            ],
            'items' => $quote->items->map(fn ($item) => [
                'name' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => (float) ($item->unit_price ?? 0),
                'total' => $item->total(),
            ])->all(),
            'subtotal' => (float) $quote->total,
            'deliveryFee' => 0,
            'taxAmount' => 0,
            'discountAmount' => 0,
            'total' => (float) $quote->total,
            'notes' => $quote->notes,
        ];

        return Pdf::loadView('pdf.invoice', $data);
    }
}
