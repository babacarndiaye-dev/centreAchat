<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Support\Notifications\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PurchaseRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseRequest::with('requester');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $purchaseRequests = $query->latest()->paginate(20)->withQueryString()->through(fn (PurchaseRequest $pr) => [
            'id' => $pr->id,
            'reference' => $pr->reference,
            'requester_name' => $pr->requester?->name,
            'created_at' => $pr->created_at->format('d/m/Y'),
            'status' => $pr->status,
            'status_label' => PurchaseRequest::STATUSES[$pr->status],
            'status_badge_class' => $pr->statusBadgeClass(),
        ]);

        return Inertia::render('Admin/PurchaseRequests/Index', [
            'purchaseRequests' => $purchaseRequests,
            'statuses' => PurchaseRequest::STATUSES,
            'filters' => $request->only('status'),
        ]);
    }

    public function create()
    {
        $products = Product::orderBy('name')->get(['id', 'name', 'stock_quantity', 'unit']);

        return Inertia::render('Admin/PurchaseRequests/Create', ['products' => $products]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:2000'],
            'needed_by_date' => ['nullable', 'date'],
            'products' => ['required', 'array', 'min:1'],
            'products.*' => ['nullable', 'integer', 'min:0'],
            'submit_for_validation' => ['nullable', 'boolean'],
        ]);

        $items = collect($data['products'])->filter(fn ($qty) => $qty > 0);

        if ($items->isEmpty()) {
            return back()->withErrors(['products' => 'Veuillez indiquer au moins une quantité.'])->withInput();
        }

        $purchaseRequest = DB::transaction(function () use ($data, $items, $request) {
            $purchaseRequest = PurchaseRequest::create([
                'reference' => PurchaseRequest::generateReference(),
                'requested_by' => Auth::id(),
                'status' => $request->boolean('submit_for_validation') ? 'en_attente_validation' : 'brouillon',
                'reason' => $data['reason'] ?? null,
                'needed_by_date' => $data['needed_by_date'] ?? null,
            ]);

            foreach ($items as $productId => $quantity) {
                PurchaseRequestItem::create([
                    'purchase_request_id' => $purchaseRequest->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }

            return $purchaseRequest;
        });

        return redirect()->route('admin.demandes-achat.show', $purchaseRequest)->with('success', 'Demande d\'achat créée.');
    }

    public function show(PurchaseRequest $demandeAchat)
    {
        $demandeAchat->load(['items.product', 'requester', 'validator', 'purchaseOrders.supplier']);

        return Inertia::render('Admin/PurchaseRequests/Show', [
            'purchaseRequest' => [
                'id' => $demandeAchat->id,
                'reference' => $demandeAchat->reference,
                'requester_name' => $demandeAchat->requester?->name,
                'created_at' => $demandeAchat->created_at->format('d/m/Y'),
                'status' => $demandeAchat->status,
                'status_label' => PurchaseRequest::STATUSES[$demandeAchat->status],
                'status_badge_class' => $demandeAchat->statusBadgeClass(),
                'reason' => $demandeAchat->reason,
                'items' => $demandeAchat->items->map(fn ($item) => [
                    'id' => $item->id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'unit' => $item->product->unit,
                ]),
                'purchase_orders' => $demandeAchat->purchaseOrders->map(fn ($po) => [
                    'id' => $po->id,
                    'order_number' => $po->order_number,
                    'supplier_name' => $po->supplier->name,
                ]),
                'validator_name' => $demandeAchat->validator?->name,
                'validated_at' => $demandeAchat->validated_at?->format('d/m/Y H:i'),
            ],
        ]);
    }

    public function submit(PurchaseRequest $demandeAchat, NotificationService $notifications): RedirectResponse
    {
        $demandeAchat->update(['status' => 'en_attente_validation']);

        $demandeAchat->load('requester');

        $notifications->send(
            'demande_fournisseur',
            NotificationService::staffRecipients('fournisseurs.valider'),
            [
                'demande_reference' => $demandeAchat->reference,
                'demande_demandeur' => $demandeAchat->requester?->name ?? 'Utilisateur',
                'demande_lien' => route('admin.demandes-achat.show', $demandeAchat),
            ]
        );

        return back()->with('success', 'Demande soumise pour validation.');
    }

    public function validateRequest(PurchaseRequest $demandeAchat): RedirectResponse
    {
        $demandeAchat->update([
            'status' => 'validee',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        return back()->with('success', 'Demande d\'achat validée.');
    }

    public function reject(PurchaseRequest $demandeAchat): RedirectResponse
    {
        $demandeAchat->update([
            'status' => 'rejetee',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        return back()->with('success', 'Demande d\'achat rejetée.');
    }

    public function destroy(PurchaseRequest $demandeAchat): RedirectResponse
    {
        $demandeAchat->delete();

        return redirect()->route('admin.demandes-achat.index')->with('success', 'Demande d\'achat supprimée.');
    }
}
