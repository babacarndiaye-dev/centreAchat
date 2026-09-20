<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentAccountTransaction;
use App\Models\PaymentMethod;
use App\Models\PosSalePayment;
use App\Models\Product;
use App\Models\User;
use App\Services\AccountingService;
use App\Support\PosCart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PosSaleController extends Controller
{
    public function create(Request $request)
    {
        $register = CashRegister::where('status', 'ouverte')->first();

        if (! $register) {
            return redirect()->route('admin.pos.caisse.index')->with('error', 'Vous devez ouvrir une caisse avant de vendre.');
        }

        $query = Product::where('is_active', true);

        if ($request->filled('q')) {
            $search = $request->string('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('reference', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->take(40)->get();
        $items = PosCart::items();
        $customer = PosCart::customer();

        return Inertia::render('Admin/Pos/Sale/Create', [
            'products' => $products->map(fn (Product $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'reference' => $p->reference,
                'stock_quantity' => $p->stock_quantity,
                'in_stock' => $p->inStock(),
                'price' => (float) $p->priceFor($customer),
            ]),
            'items' => $items->map(fn ($item) => [
                'product_id' => $item->product->id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'total' => (float) $item->total,
            ]),
            'subtotal' => (float) PosCart::subtotal(),
            'customer' => $customer ? ['id' => $customer->id, 'name' => $customer->name] : null,
            'customers' => User::whereIn('user_type', User::B2B_TYPES)->where('b2b_status', 'valide')->get()->map(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'company_name' => $u->company_name,
            ]),
            'paymentMethods' => PaymentMethod::where('is_active', true)->where('available_pos', true)->orderBy('position')->get(['code', 'name']),
            'filters' => $request->only('q'),
        ]);
    }

    public function addToCart(Request $request, Product $product): RedirectResponse
    {
        PosCart::add($product->id, $request->integer('quantity', 1) ?: 1);

        return back()->with('success', 'Produit ajouté.');
    }

    public function updateCart(Request $request, Product $product): RedirectResponse
    {
        PosCart::update($product->id, $request->integer('quantity', 0));

        return back();
    }

    public function removeFromCart(Product $product): RedirectResponse
    {
        PosCart::remove($product->id);

        return back();
    }

    public function setCustomer(Request $request): RedirectResponse
    {
        $data = $request->validate(['user_id' => ['nullable', 'exists:users,id']]);

        PosCart::setCustomer($data['user_id'] ?? null);

        return back();
    }

    public function store(Request $request, AccountingService $accounting): RedirectResponse
    {
        $register = CashRegister::where('status', 'ouverte')->first();

        if (! $register) {
            return redirect()->route('admin.pos.caisse.index')->with('error', 'Aucune caisse ouverte.');
        }

        $items = PosCart::items();

        if ($items->isEmpty()) {
            return back()->with('error', 'Le panier de vente est vide.');
        }

        $validMethods = PaymentMethod::where('is_active', true)->where('available_pos', true)->pluck('code');

        $data = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:255'],
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.method' => ['required', 'in:'.$validMethods->implode(',')],
            'payments.*.amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $subtotal = PosCart::subtotal();
        $totalPaid = collect($data['payments'])->sum('amount');
        $customer = PosCart::customer();
        $accountsByMethod = PaymentMethod::whereIn('code', collect($data['payments'])->pluck('method'))
            ->with('paymentAccount')->get()->keyBy('code');

        $paymentLines = [];

        $order = DB::transaction(function () use ($items, $data, $subtotal, $totalPaid, $customer, $register, $accountsByMethod, &$paymentLines) {
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $customer?->id,
                'channel' => 'boutique',
                'customer_name' => $data['customer_name'] ?: ($customer->name ?? 'Client comptoir'),
                'customer_phone' => $customer->phone ?? '—',
                'delivery_address' => 'Retrait en boutique',
                'city' => 'Mbour',
                'status' => 'terminee',
                'subtotal' => $subtotal,
                'delivery_fee' => 0,
                'total' => $subtotal,
                'payment_method' => collect($data['payments'])->pluck('method')->unique()->count() > 1 ? 'mixte' : $data['payments'][0]['method'],
                'payment_status' => $totalPaid >= $subtotal ? 'paye' : 'partiellement_paye',
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'product_name' => $item->product->name,
                    'unit_price' => $item->unit_price,
                    'price_tier' => $item->price_tier,
                    'quantity' => $item->quantity,
                    'total' => $item->total,
                ]);

                $item->product->decrement('stock_quantity', min($item->quantity, $item->product->stock_quantity));
            }

            foreach ($data['payments'] as $payment) {
                PosSalePayment::create([
                    'order_id' => $order->id,
                    'cash_register_id' => $register->id,
                    'method' => $payment['method'],
                    'amount' => $payment['amount'],
                ]);

                $account = $accountsByMethod->get($payment['method'])?->paymentAccount;

                if ($account) {
                    PaymentAccountTransaction::create([
                        'payment_account_id' => $account->id,
                        'type' => 'entree',
                        'amount' => $payment['amount'],
                        'category' => 'Vente',
                        'description' => 'Vente POS : '.$order->order_number,
                        'reference' => $order->order_number,
                        'transaction_date' => now(),
                        'created_by' => Auth::id(),
                    ]);

                    $paymentLines[] = ['account' => $account, 'amount' => (float) $payment['amount']];
                }
            }

            return $order;
        });

        PosCart::clear();

        $accounting->postSaleInvoice($order);

        if (! empty($paymentLines)) {
            $accounting->postSaleReceipt($order, $paymentLines);
        }

        return redirect()->route('admin.pos.ventes.show', $order)->with('success', 'Vente enregistrée.');
    }

    public function show(Order $vente)
    {
        $vente->load(['items', 'posPayments']);

        return Inertia::render('Admin/Pos/Sale/Receipt', [
            'order' => [
                'order_number' => $vente->order_number,
                'created_at' => $vente->created_at->format('d/m/Y H:i'),
                'customer_name' => $vente->customer_name,
                'total' => (float) $vente->total,
                'items' => $vente->items->map(fn (OrderItem $item) => [
                    'id' => $item->id,
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'total' => (float) $item->total,
                ]),
                'payments' => $vente->posPayments->map(fn ($p) => [
                    'id' => $p->id,
                    'method' => $p->method,
                    'amount' => (float) $p->amount,
                ]),
            ],
            'siteAddress' => \App\Models\Setting::get('address'),
            'sitePhone' => \App\Models\Setting::get('phone'),
        ]);
    }
}
