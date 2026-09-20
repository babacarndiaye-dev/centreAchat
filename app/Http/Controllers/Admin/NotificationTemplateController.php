<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Support\Notifications\NotificationEvents;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationTemplateController extends Controller
{
    public function index()
    {
        $groups = NotificationTemplate::orderBy('event_key')->orderBy('channel')->get()
            ->groupBy('event_key')
            ->map(fn ($templates, $eventKey) => [
                'event_key' => $eventKey,
                'event_label' => NotificationEvents::label($eventKey),
                'templates' => $templates->map(fn (NotificationTemplate $template) => [
                    'id' => $template->id,
                    'channel' => $template->channel,
                    'channel_label' => NotificationEvents::CHANNELS[$template->channel] ?? $template->channel,
                    'is_active' => $template->is_active,
                ])->values(),
            ])->values();

        return Inertia::render('Admin/Notifications/Templates/Index', ['groups' => $groups]);
    }

    public function edit(NotificationTemplate $template)
    {
        return Inertia::render('Admin/Notifications/Templates/Edit', [
            'template' => [
                'id' => $template->id,
                'event_key' => $template->event_key,
                'event_label' => NotificationEvents::label($template->event_key),
                'channel' => $template->channel,
                'channel_label' => NotificationEvents::CHANNELS[$template->channel] ?? $template->channel,
                'subject' => $template->subject,
                'body' => $template->body,
                'is_active' => $template->is_active,
            ],
            'placeholders' => NotificationEvents::placeholders($template->event_key),
        ]);
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
