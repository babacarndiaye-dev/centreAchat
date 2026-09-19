<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReception;
use App\Models\PurchaseReceptionItem;
use App\Services\AccountingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseReceptionController extends Controller
{
    public function store(Request $request, PurchaseOrder $purchaseOrder, AccountingService $accounting): RedirectResponse
    {
        $data = $request->validate([
            'reception_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'quantity_received' => ['required', 'array'],
            'quantity_received.*' => ['nullable', 'integer', 'min:0'],
            'quality_status' => ['required', 'array'],
            'quality_status.*' => ['required', 'in:conforme,non_conforme'],
        ]);

        $purchaseOrder->load('items');

        $hasNonConforme = false;
        $hasReceived = false;
        $reception = null;

        DB::transaction(function () use ($data, $purchaseOrder, &$hasNonConforme, &$hasReceived, &$reception) {
            $reception = PurchaseReception::create([
                'purchase_order_id' => $purchaseOrder->id,
                'received_by' => Auth::id(),
                'reception_date' => $data['reception_date'],
                'quality_status' => 'conforme',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['quantity_received'] as $orderItemId => $quantity) {
                $quantity = (int) $quantity;

                if ($quantity <= 0) {
                    continue;
                }

                $orderItem = $purchaseOrder->items->firstWhere('id', (int) $orderItemId);

                if (! $orderItem) {
                    continue;
                }

                $quantity = min($quantity, $orderItem->remainingQuantity());

                if ($quantity <= 0) {
                    continue;
                }

                $quality = $data['quality_status'][$orderItemId] ?? 'conforme';
                $hasReceived = true;

                PurchaseReceptionItem::create([
                    'purchase_reception_id' => $reception->id,
                    'purchase_order_item_id' => $orderItem->id,
                    'quantity_received' => $quantity,
                    'quality_status' => $quality,
                ]);

                $orderItem->increment('quantity_received', $quantity);

                if ($quality === 'conforme') {
                    $orderItem->product?->increment('stock_quantity', $quantity);
                } else {
                    $hasNonConforme = true;
                }
            }

            $reception->update(['quality_status' => $hasNonConforme ? ($hasReceived ? 'partielle' : 'non_conforme') : 'conforme']);

            $purchaseOrder->refresh()->load('items');
            $purchaseOrder->update([
                'status' => $purchaseOrder->isFullyReceived() ? 'recue' : ($purchaseOrder->isPartiallyReceived() ? 'partiellement_recue' : $purchaseOrder->status),
            ]);
        });

        if ($hasReceived && $reception) {
            $accounting->postPurchaseReception($reception);
        }

        return back()->with('success', $hasReceived ? 'Réception enregistrée et stock mis à jour.' : 'Aucune quantité reçue n\'a été renseignée.');
    }
}
