<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Quote;
use App\Models\Setting;
use App\Services\InvoicePdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Throwable;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Quote::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $quotes = $query->latest()->paginate(20)->withQueryString();
        $quotes->getCollection()->transform(fn (Quote $quote) => [
            'id' => $quote->id,
            'quote_number' => $quote->quote_number,
            'user_name' => $quote->user->name,
            'created_at' => $quote->created_at->format('d/m/Y'),
            'status' => $quote->status,
            'status_label' => Quote::STATUSES[$quote->status],
            'status_badge_class' => $quote->statusBadgeClass(),
            'total' => (float) $quote->total,
        ]);

        return Inertia::render('Admin/Quotes/Index', [
            'quotes' => $quotes,
            'statuses' => Quote::STATUSES,
            'filters' => ['status' => $request->input('status', '')],
        ]);
    }

    public function show(Quote $devis)
    {
        $devis->load(['items.product', 'user']);

        $editable = in_array($devis->status, ['en_attente', 'envoye'], true);

        return Inertia::render('Admin/Quotes/Show', [
            'quote' => [
                'id' => $devis->id,
                'quote_number' => $devis->quote_number,
                'notes' => $devis->notes,
                'status' => $devis->status,
                'status_label' => Quote::STATUSES[$devis->status],
                'status_badge_class' => $devis->statusBadgeClass(),
                'total' => (float) $devis->total,
                'user' => [
                    'name' => $devis->user->name,
                    'company_name' => $devis->user->company_name,
                    'email' => $devis->user->email,
                ],
                'editable' => $editable,
                'items' => $devis->items->map(fn ($item) => [
                    'id' => $item->id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price !== null ? (float) $item->unit_price : null,
                    'suggested_price' => (float) ($item->unit_price ?? $item->product->professional_price ?? $item->product->price),
                    'total' => (float) $item->total(),
                ]),
            ],
        ]);
    }

    public function send(Request $request, Quote $devis, InvoicePdfService $invoices): RedirectResponse
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

        $this->sendQuoteEmail($devis, $invoices);

        return back()->with('success', 'Devis envoyé au client.');
    }

    protected function sendQuoteEmail(Quote $devis, InvoicePdfService $invoices): void
    {
        $devis->loadMissing('user');

        if (! $devis->user?->email) {
            return;
        }

        try {
            $pdfContent = $invoices->forQuote($devis)->output();
        } catch (Throwable $e) {
            Log::warning('Génération du devis PDF échouée', ['quote_id' => $devis->id, 'exception' => get_class($e), 'message' => $e->getMessage()]);
            return;
        }

        Log::info('Devis PDF généré', ['quote_id' => $devis->id, 'bytes' => strlen($pdfContent)]);

        try {
            $siteName = Setting::get('site_name') ?: "Centrale d'achat";

            Mail::send('emails.layout', [
                'title' => 'Votre devis — '.$devis->quote_number,
                'body' => "Voici votre devis, valable jusqu'au ".$devis->valid_until?->format('d/m/Y').".\n\nVous trouverez le détail en pièce jointe.",
            ], function ($message) use ($devis, $pdfContent, $siteName) {
                $message->to($devis->user->email)
                    ->subject('Devis '.$devis->quote_number.' — '.$siteName)
                    ->attachData($pdfContent, 'devis-'.$devis->quote_number.'.pdf', ['mime' => 'application/pdf']);
            });
            Log::info('E-mail de devis envoyé', ['quote_id' => $devis->id]);
        } catch (Throwable $e) {
            Log::warning('Envoi du devis par e-mail échoué', ['quote_id' => $devis->id, 'exception' => get_class($e), 'message' => $e->getMessage()]);
        }
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
