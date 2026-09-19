<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $query = Conversation::with(['user', 'assignee', 'latestMessage'])->withCount([
            'messages as unread_count' => fn ($q) => $q->where('sender_type', 'client')->whereNull('read_at'),
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $conversations = $query->orderByDesc('last_message_at')->paginate(20)->withQueryString();

        return view('admin.messagerie.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $conversation->load(['user', 'assignee', 'messages.sender']);
        $conversation->messages()->where('sender_type', 'client')->whereNull('read_at')->update(['read_at' => now()]);

        return view('admin.messagerie.show', compact('conversation'));
    }

    public function messages(Conversation $conversation): JsonResponse
    {
        $conversation->messages()->where('sender_type', 'client')->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json([
            'status' => $conversation->status,
            'messages' => $conversation->messages->map(fn (ChatMessage $message) => [
                'id' => $message->id,
                'sender_type' => $message->sender_type,
                'sender_name' => $message->sender?->name,
                'body' => $message->body,
                'links' => $message->meta['links'] ?? [],
                'source' => $message->meta['source'] ?? null,
                'created_at' => $message->created_at->format('d/m H:i'),
            ]),
        ]);
    }

    public function reply(Request $request, Conversation $conversation): RedirectResponse
    {
        $data = $request->validate(['message' => ['required', 'string', 'max:2000']]);

        $conversation->messages()->create([
            'sender_type' => 'staff',
            'sender_user_id' => Auth::id(),
            'body' => $data['message'],
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'status' => 'en_cours',
            'bot_enabled' => false,
            'assigned_to' => $conversation->assigned_to ?? Auth::id(),
        ]);

        return back();
    }

    public function assign(Conversation $conversation): RedirectResponse
    {
        $conversation->update(['assigned_to' => Auth::id(), 'status' => $conversation->status === 'ouverte' ? 'en_cours' : $conversation->status]);

        return back()->with('success', 'Conversation assignée.');
    }

    public function close(Conversation $conversation): RedirectResponse
    {
        $conversation->update(['status' => 'fermee']);

        return back()->with('success', 'Conversation fermée.');
    }

    public function reopen(Conversation $conversation): RedirectResponse
    {
        $conversation->update(['status' => 'ouverte']);

        return back()->with('success', 'Conversation réouverte.');
    }
}
