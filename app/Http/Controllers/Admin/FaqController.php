<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FaqController extends Controller
{
    public function index()
    {
        $entries = FaqEntry::orderBy('position')->orderByDesc('hit_count')->get();

        return Inertia::render('Admin/Faq/Index', [
            'entries' => $entries,
            'aiEnabled' => config('services.openai.chat_enabled') && filled(config('services.openai.api_key')),
            'aiModel' => config('services.openai.model'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Faq/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        FaqEntry::create($data);

        return redirect()->route('admin.messagerie.faq.index')->with('success', 'Question ajoutée à la base de connaissances.');
    }

    public function edit(FaqEntry $faq)
    {
        return Inertia::render('Admin/Faq/Edit', ['entry' => $faq]);
    }

    public function update(Request $request, FaqEntry $faq): RedirectResponse
    {
        $faq->update($this->validated($request));

        return redirect()->route('admin.messagerie.faq.index')->with('success', 'Question mise à jour.');
    }

    public function destroy(FaqEntry $faq): RedirectResponse
    {
        $faq->delete();

        return back()->with('success', 'Question supprimée.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'keywords' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['position'] = $data['position'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
