<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\CashRegister;
use App\Models\PaymentAccount;
use App\Models\PaymentAccountTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CashRegisterController extends Controller
{
    public function index()
    {
        $registers = CashRegister::with(['openedBy', 'closedBy'])->latest()->paginate(15)->withQueryString()->through(fn (CashRegister $r) => [
            'id' => $r->id,
            'opened_by_name' => $r->openedBy->name,
            'created_at' => $r->created_at->format('d/m/Y H:i'),
            'opening_float' => (float) $r->opening_float,
            'status' => $r->status,
            'variance' => $r->variance() !== null ? (float) $r->variance() : null,
        ]);

        return Inertia::render('Admin/Pos/Registers/Index', [
            'registers' => $registers,
        ]);
    }

    public function create()
    {
        if ($open = CashRegister::where('status', 'ouverte')->first()) {
            return redirect()->route('admin.pos.caisse.show', $open);
        }

        return Inertia::render('Admin/Pos/Registers/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (CashRegister::where('status', 'ouverte')->exists()) {
            return redirect()->route('admin.pos.caisse.index')->with('error', 'Une caisse est déjà ouverte.');
        }

        $data = $request->validate([
            'opening_float' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $register = CashRegister::create([
            ...$data,
            'opened_by' => Auth::id(),
            'status' => 'ouverte',
        ]);

        return redirect()->route('admin.pos.caisse.show', $register)->with('success', 'Caisse ouverte.');
    }

    public function show(CashRegister $caisse)
    {
        $caisse->load(['movements.creator', 'salePayments.order', 'returns.order', 'openedBy', 'closedBy']);
        $sales = $caisse->salePayments->pluck('order')->unique('id')->values();

        return Inertia::render('Admin/Pos/Registers/Show', [
            'register' => [
                'id' => $caisse->id,
                'status' => $caisse->status,
                'opened_by_name' => $caisse->openedBy->name,
                'created_at' => $caisse->created_at->format('d/m/Y H:i'),
                'opening_float' => (float) $caisse->opening_float,
                'cash_sales_total' => (float) $caisse->cashSalesTotal(),
                'movements_net' => (float) ($caisse->encaissementsTotal() - $caisse->decaissementsTotal()),
                'theoretical_cash' => (float) $caisse->theoreticalCash(),
                'actual_closing_amount' => $caisse->actual_closing_amount !== null ? (float) $caisse->actual_closing_amount : null,
                'variance' => $caisse->variance() !== null ? (float) $caisse->variance() : null,
                'closed_by_name' => $caisse->closedBy?->name,
                'closed_at' => $caisse->closed_at?->format('d/m/Y H:i'),
                'movements' => $caisse->movements->map(fn ($m) => [
                    'id' => $m->id,
                    'reason' => $m->reason,
                    'type' => $m->type,
                    'amount' => (float) $m->amount,
                ]),
            ],
            'sales' => $sales->map(fn ($order) => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'total' => (float) $order->total,
            ]),
        ]);
    }

    public function addMovement(Request $request, CashRegister $caisse): RedirectResponse
    {
        if ($caisse->status !== 'ouverte') {
            return back()->with('error', 'Cette caisse est fermée.');
        }

        $data = $request->validate([
            'type' => ['required', 'in:encaissement,decaissement'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        CashMovement::create([
            ...$data,
            'cash_register_id' => $caisse->id,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Mouvement de caisse enregistré.');
    }

    public function close(Request $request, CashRegister $caisse): RedirectResponse
    {
        if ($caisse->status !== 'ouverte') {
            return back()->with('error', 'Cette caisse est déjà fermée.');
        }

        $data = $request->validate([
            'actual_closing_amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $theoreticalCash = $caisse->theoreticalCash();

        $caisse->update([
            'actual_closing_amount' => $data['actual_closing_amount'],
            'notes' => $data['notes'] ?? $caisse->notes,
            'status' => 'fermee',
            'closed_by' => Auth::id(),
            'closed_at' => now(),
        ]);

        $variance = round((float) $data['actual_closing_amount'] - $theoreticalCash, 2);

        if ($variance !== 0.0 && ($caisseAccount = PaymentAccount::where('type', 'caisse')->first())) {
            PaymentAccountTransaction::create([
                'payment_account_id' => $caisseAccount->id,
                'type' => $variance > 0 ? 'entree' : 'sortie',
                'amount' => abs($variance),
                'category' => 'Écart de caisse',
                'description' => 'Écart constaté à la clôture de la caisse #'.$caisse->id,
                'reference' => 'CAISSE-'.$caisse->id,
                'transaction_date' => now(),
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()->route('admin.pos.caisse.show', $caisse)->with('success', 'Caisse clôturée.');
    }

    public function updateClosure(Request $request, CashRegister $caisse): RedirectResponse
    {
        if ($caisse->status !== 'fermee') {
            return back()->with('error', 'Cette correction n\'est possible que sur une caisse clôturée.');
        }

        $data = $request->validate([
            'opening_float' => ['required', 'numeric', 'min:0'],
            'actual_closing_amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $caisse->update($data);

        return redirect()->route('admin.pos.caisse.show', $caisse)->with('success', 'Caisse corrigée.');
    }

    public function reopen(CashRegister $caisse): RedirectResponse
    {
        if ($caisse->status !== 'fermee') {
            return back()->with('error', 'Cette caisse n\'est pas clôturée.');
        }

        if (CashRegister::where('status', 'ouverte')->exists()) {
            return back()->with('error', 'Une autre caisse est déjà ouverte. Fermez-la avant de rouvrir celle-ci.');
        }

        $caisse->update([
            'status' => 'ouverte',
            'closed_by' => null,
            'closed_at' => null,
            'actual_closing_amount' => null,
        ]);

        return redirect()->route('admin.pos.caisse.show', $caisse)->with('success', 'Caisse rouverte. Pensez à la re-clôturer une fois la correction effectuée.');
    }
}
