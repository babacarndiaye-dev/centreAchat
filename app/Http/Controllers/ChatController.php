<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\FaqEntry;
use App\Support\Chat\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function state(Request $request): JsonResponse
    {
        $conversation = $this->resolveConversation($request);

        if (! $conversation) {
            return response()->json([
                'conversation' => null,
                'messages' => [],
                'suggestions' => FaqEntry::mostPopular(4)->pluck('question'),
            ]);
        }

        $conversation->messages()->where('sender_type', '!=', 'client')->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'status' => $conversation->status,
            ],
            'messages' => $this->formatMessages($conversation),
            'suggestions' => [],
        ]);
    }

    public function send(Request $request, ChatbotService $chatbot): JsonResponse
    {
        $conversation = $this->resolveConversation($request);
        $isNew = ! $conversation;

        $data = $request->validate(['message' => ['required', 'string', 'max:2000']]);

        if ($isNew) {
            $conversation = Conversation::create([
                'user_id' => Auth::id(),
                'guest_name' => null,
                'guest_email' => null,
                'status' => 'ouverte',
                'bot_enabled' => true,
                'last_message_at' => now(),
            ]);

            if (! Auth::check()) {
                $request->session()->put('chat_conversation_id', $conversation->id);
            }
        } elseif ($conversation->status === 'fermee') {
            $conversation->update(['status' => 'ouverte']);
        }

        $conversation->messages()->create([
            'sender_type' => 'client',
            'sender_user_id' => Auth::id(),
            'body' => $data['message'],
        ]);

        $conversation->update(['last_message_at' => now()]);

        $chatbot->respond($conversation, $data['message']);

        return $this->state($request);
    }

    public function transfer(Request $request, ChatbotService $chatbot): JsonResponse
    {
        $conversation = $this->resolveConversation($request);

        if ($conversation) {
            $chatbot->transfer($conversation);
        }

        return $this->state($request);
    }

    public function history(Request $request)
    {
        $conversations = Conversation::where('user_id', Auth::id())
            ->with('latestMessage')
            ->orderByDesc('last_message_at')
            ->paginate(15);

        return view('account.messages.index', compact('conversations'));
    }

    public function historyShow(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->user_id === Auth::id(), 403);

        $conversation->load('messages');

        return view('account.messages.show', compact('conversation'));
    }

    protected function formatMessages(Conversation $conversation): array
    {
        return $conversation->messages->map(fn (ChatMessage $message) => [
            'id' => $message->id,
            'sender_type' => $message->sender_type,
            'body' => $message->body,
            'links' => $message->meta['links'] ?? [],
            'created_at' => $message->created_at->format('H:i'),
        ])->all();
    }

    protected function resolveConversation(Request $request): ?Conversation
    {
        if (Auth::check()) {
            return Conversation::where('user_id', Auth::id())
                ->where('status', '!=', 'fermee')
                ->latest('last_message_at')
                ->first();
        }

        $id = $request->session()->get('chat_conversation_id');

        return $id ? Conversation::find($id) : null;
    }
}
