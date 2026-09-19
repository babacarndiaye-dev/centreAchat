<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartAccount;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JournalEntryController extends Controller
{
    public function index(Request $request)
    {
        $query = JournalEntry::with('journal');

        if ($request->filled('journal')) {
            $query->where('journal_id', $request->integer('journal'));
        }

        if ($request->filled('from')) {
            $query->whereDate('entry_date', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('entry_date', '<=', $request->date('to'));
        }

        $entries = $query->withSum('lines as total_debit', 'debit')->latest('entry_date')->latest('id')->paginate(25)->withQueryString();
        $journals = Journal::orderBy('code')->get();

        return view('admin.accounting.entries.index', compact('entries', 'journals'));
    }

    public function create()
    {
        $journals = Journal::orderBy('code')->get();
        $accounts = ChartAccount::where('is_active', true)->orderBy('code')->get();

        return view('admin.accounting.entries.create', compact('journals', 'accounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'journal_id' => ['required', 'exists:journals,id'],
            'entry_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'chart_account_id' => ['required', 'array', 'min:2'],
            'chart_account_id.*' => ['required', 'exists:chart_of_accounts,id'],
            'debit' => ['required', 'array'],
            'debit.*' => ['nullable', 'numeric', 'min:0'],
            'credit' => ['required', 'array'],
            'credit.*' => ['nullable', 'numeric', 'min:0'],
            'label' => ['nullable', 'array'],
        ]);

        $totalDebit = array_sum(array_map('floatval', $data['debit']));
        $totalCredit = array_sum(array_map('floatval', $data['credit']));

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            return back()->withErrors(['debit' => "L'écriture n'est pas équilibrée : débit {$totalDebit}, crédit {$totalCredit}."])->withInput();
        }

        if ($totalDebit <= 0) {
            return back()->withErrors(['debit' => 'Le montant total doit être supérieur à zéro.'])->withInput();
        }

        DB::transaction(function () use ($data) {
            $entry = JournalEntry::create([
                'journal_id' => $data['journal_id'],
                'entry_date' => $data['entry_date'],
                'reference' => $data['reference'] ?? null,
                'description' => $data['description'],
                'created_by' => Auth::id(),
            ]);

            foreach ($data['chart_account_id'] as $i => $accountId) {
                $debit = (float) ($data['debit'][$i] ?? 0);
                $credit = (float) ($data['credit'][$i] ?? 0);

                if ($debit <= 0 && $credit <= 0) {
                    continue;
                }

                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_account_id' => $accountId,
                    'debit' => $debit,
                    'credit' => $credit,
                    'label' => $data['label'][$i] ?? null,
                ]);
            }
        });

        return redirect()->route('admin.comptabilite.ecritures.index')->with('success', 'Écriture enregistrée.');
    }

    public function show(JournalEntry $ecriture)
    {
        $ecriture->load(['lines.account', 'journal', 'creator']);

        return view('admin.accounting.entries.show', ['entry' => $ecriture]);
    }

    public function destroy(JournalEntry $ecriture): RedirectResponse
    {
        $ecriture->delete();

        return redirect()->route('admin.comptabilite.ecritures.index')->with('success', 'Écriture supprimée.');
    }
}
