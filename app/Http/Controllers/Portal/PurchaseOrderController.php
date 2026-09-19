<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $supplier = Auth::user()->supplier;

        $query = $supplier->purchaseOrders();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $purchaseOrders = $query->latest()->paginate(15)->withQueryString();

        return view('portal.orders.index', compact('purchaseOrders'));
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $this->authorizeSupplierOrder($purchaseOrder);

        $purchaseOrder->load(['items.product', 'receptions.items.orderItem.product', 'payments']);

        return view('portal.orders.show', compact('purchaseOrder'));
    }

    public function confirm(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorizeSupplierOrder($purchaseOrder);

        if ($purchaseOrder->status !== 'envoyee') {
            return back()->with('error', 'Cette commande ne peut plus être confirmée.');
        }

        $purchaseOrder->update(['status' => 'confirmee']);

        return back()->with('success', 'Disponibilité confirmée pour la commande '.$purchaseOrder->order_number.'.');
    }

    public function document(PurchaseOrder $purchaseOrder)
    {
        $this->authorizeSupplierOrder($purchaseOrder);

        $purchaseOrder->load(['items.product', 'supplier']);

        return view('portal.orders.document', compact('purchaseOrder'));
    }

    protected function authorizeSupplierOrder(PurchaseOrder $purchaseOrder): void
    {
        if ($purchaseOrder->supplier_id !== Auth::user()->supplier->id) {
            abort(403);
        }
    }
}
