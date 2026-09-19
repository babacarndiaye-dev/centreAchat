<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index()
    {
        $journals = Journal::orderBy('code')->get();

        return view('admin.accounting.journals.index', compact('journals'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:journals,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:'.implode(',', array_keys(Journal::TYPES))],
        ]);

        Journal::create($data);

        return back()->with('success', 'Journal créé.');
    }

    public function destroy(Journal $journal): RedirectResponse
    {
        if ($journal->entries()->exists()) {
            return back()->with('error', 'Ce journal contient des écritures, il ne peut pas être supprimé.');
        }

        $journal->delete();

        return back()->with('success', 'Journal supprimé.');
    }
}
