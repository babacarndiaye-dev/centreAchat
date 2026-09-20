<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()
            ->where('channel', 'interne')
            ->paginate(30)
            ->through(fn ($notification) => [
                'id' => $notification->id,
                'title' => $notification->title,
                'body' => $notification->body,
                'read_at' => $notification->read_at,
                'created_at_human' => $notification->created_at->diffForHumans(),
            ]);

        return Inertia::render('Admin/Notifications/Index', ['notifications' => $notifications]);
    }

    public function markAsRead(Request $request, \App\Models\Notification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        $notification->markAsRead();

        return back();
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
}
