<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Support\Notifications\NotificationEvents;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationTemplateController extends Controller
{
    public function index()
    {
        $templates = NotificationTemplate::orderBy('event_key')->orderBy('channel')->get()->groupBy('event_key');

        return view('admin.notifications.templates.index', compact('templates'));
    }

    public function edit(NotificationTemplate $template)
    {
        $placeholders = NotificationEvents::placeholders($template->event_key);

        return view('admin.notifications.templates.edit', compact('template', 'placeholders'));
    }

    public function update(Request $request, NotificationTemplate $template): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $template->update([
            'subject' => $data['subject'] ?? null,
            'body' => $data['body'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.notifications.templates.index')->with('success', 'Modèle de message mis à jour.');
    }
}
