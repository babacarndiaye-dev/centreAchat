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

class PurchaseRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseRequest::with('requester');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $purchaseRequests = $query->latest()->paginate(20)->withQueryString();

        return view('admin.purchase-requests.index', compact('purchaseRequests'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();

        return view('admin.purchase-requests.create', compact('products'));
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

        return view('admin.purchase-requests.show', ['purchaseRequest' => $demandeAchat]);
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
