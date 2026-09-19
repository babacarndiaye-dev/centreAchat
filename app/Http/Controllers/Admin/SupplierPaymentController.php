<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Services\AccountingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupplierPaymentController extends Controller
{
    public function store(Request $request, Supplier $supplier, AccountingService $accounting): RedirectResponse
    {
        $data = $request->validate([
            'purchase_order_id' => ['nullable', 'exists:purchase_orders,id'],
            'payment_account_id' => ['nullable', 'exists:payment_accounts,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'method' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['supplier_id'] = $supplier->id;

        $payment = SupplierPayment::create($data);

        if ($data['purchase_order_id'] ?? null) {
            $order = $supplier->purchaseOrders()->find($data['purchase_order_id']);
            $order?->increment('amount_paid', $data['amount']);
        }

        if ($payment->payment_account_id) {
            $accounting->postSupplierPayment($payment);
        }

        return back()->with('success', 'Paiement enregistré.');
    }
}
