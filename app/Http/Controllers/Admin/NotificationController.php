<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Conversation;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Lightweight polling endpoint for the admin sound-notification widget
     * (orders, chat, contact messages) — deliberately cheap, no pagination.
     */
    public function live(Request $request): JsonResponse
    {
        return response()->json([
            'latest_order_id' => (int) (Order::max('id') ?? 0),
            'orders_pending' => Order::whereIn('status', ['nouvelle', 'confirmee', 'en_preparation'])->count(),
            'unread_chat' => Conversation::unreadForStaffCount(),
            'unread_notifications' => $request->user()->unreadNotificationsCount(),
            'unread_contact' => ContactMessage::where('is_read', false)->count(),
        ]);
    }

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
