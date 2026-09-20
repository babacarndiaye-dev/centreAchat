<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class QuoteController extends Controller
{
    public function index()
    {
        $quotes = Auth::user()->quotes()->latest()->paginate(10)->through(fn (Quote $quote) => [
            'id' => $quote->id,
            'quote_number' => $quote->quote_number,
            'created_at' => $quote->created_at->format('d/m/Y'),
            'status_label' => Quote::STATUSES[$quote->status],
            'total' => (float) $quote->total,
        ]);

        return Inertia::render('Account/Quotes/Index', ['quotes' => $quotes]);
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Account/Quotes/Create', ['products' => $products]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
            'products' => ['required', 'array', 'min:1'],
            'products.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $items = collect($data['products'])->filter(fn ($qty) => $qty > 0);

        if ($items->isEmpty()) {
            return back()->withErrors(['products' => 'Veuillez indiquer au moins une quantité.'])->withInput();
        }

        $quote = DB::transaction(function () use ($items, $data) {
            $quote = Quote::create([
                'quote_number' => Quote::generateNumber(),
                'user_id' => Auth::id(),
                'status' => 'en_attente',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($items as $productId => $quantity) {
                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }

            return $quote;
        });

        return redirect()->route('compte.devis.show', $quote)->with('success', 'Votre demande de devis a été envoyée.');
    }

    public function show(Quote $devis)
    {
        $this->authorizeOwnership($devis);
        $devis->load('items.product');

        return Inertia::render('Account/Quotes/Show', [
            'quote' => [
                'id' => $devis->id,
                'quote_number' => $devis->quote_number,
                'status' => $devis->status,
                'status_label' => Quote::STATUSES[$devis->status],
                'total' => (float) $devis->total,
                'valid_until' => $devis->valid_until?->format('d/m/Y'),
                'notes' => $devis->notes,
                'items' => $devis->items->map(fn (QuoteItem $item) => [
                    'id' => $item->id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price ? (float) $item->unit_price : null,
                    'total' => $item->unit_price ? $item->total() : null,
                ]),
            ],
        ]);
    }

    public function accept(Quote $devis): RedirectResponse
    {
        $this->authorizeOwnership($devis);

        if ($devis->status !== 'envoye') {
            return back()->with('error', 'Ce devis ne peut plus être accepté.');
        }

        $devis->update(['status' => 'accepte']);

        return back()->with('success', 'Devis accepté. Notre équipe va vous contacter pour finaliser la commande.');
    }

    public function refuse(Quote $devis): RedirectResponse
    {
        $this->authorizeOwnership($devis);

        if ($devis->status !== 'envoye') {
            return back()->with('error', 'Ce devis ne peut plus être refusé.');
        }

        $devis->update(['status' => 'refuse']);

        return back()->with('success', 'Devis refusé.');
    }

    protected function authorizeOwnership(Quote $quote): void
    {
        if ($quote->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
