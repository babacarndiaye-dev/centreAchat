@extends('layouts.admin')

@section('title', 'Conversation — ' . $conversation->customerName())

@section('content')
<div class="flex flex-wrap items-start justify-between gap-3">
    <div>
        <a href="{{ route('admin.messagerie.index') }}" class="text-sm text-terroir-dark/50">&larr; Retour à la messagerie</a>
        <h2 class="mt-1.5 font-display text-lg font-semibold">{{ $conversation->customerName() }}</h2>
        <p class="text-sm text-terroir-dark/50">
            {{ $conversation->user?->email ?? $conversation->guest_email ?? 'Visiteur anonyme' }}
            @if($conversation->assignee) — assignée à {{ $conversation->assignee->name }} @endif
            @if($conversation->bot_enabled) — <span class="text-terroir-green">🤖 assistant actif</span> @endif
        </p>
    </div>
    <div class="flex gap-2">
        @if(!$conversation->assignee || $conversation->assignee->id !== auth()->id())
            <form action="{{ route('admin.messagerie.assign', $conversation) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="rounded-full border border-terroir-green/20 px-3.5 py-1.5 text-sm text-terroir-green">Me l'assigner</button>
            </form>
        @endif
        @if($conversation->status === 'fermee')
            <form action="{{ route('admin.messagerie.reopen', $conversation) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="rounded-full border border-terroir-green/20 px-3.5 py-1.5 text-sm text-terroir-green">Réouvrir</button>
            </form>
        @else
            <form action="{{ route('admin.messagerie.close', $conversation) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="rounded-full border border-terroir-terracotta/30 px-3.5 py-1.5 text-sm text-terroir-terracotta">Fermer</button>
            </form>
        @endif
    </div>
</div>

<div
    x-data="{
        messages: {{ $conversation->messages->map(fn ($m) => ['id' => $m->id, 'sender_type' => $m->sender_type, 'sender_name' => $m->sender?->name, 'body' => $m->body, 'links' => $m->meta['links'] ?? [], 'source' => $m->meta['source'] ?? null, 'created_at' => $m->created_at->format('d/m H:i')])->toJson() }},
        poll() {
            fetch('{{ route('admin.messagerie.messages', $conversation) }}', { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    if (data.messages.length !== this.messages.length) {
                        this.messages = data.messages;
                        this.$nextTick(() => this.scrollToBottom());
                    }
                })
                .catch(() => {});
        },
        scrollToBottom() {
            const el = this.$refs.thread;
            if (el) el.scrollTop = el.scrollHeight;
        },
    }"
    x-init="scrollToBottom(); setInterval(() => poll(), 4000)"
    class="admin-card mt-6 flex flex-col p-0"
    style="height:32rem;"
>
    <div class="flex flex-1 flex-col gap-3 overflow-y-auto px-5 py-4" x-ref="thread">
        <template x-for="msg in messages" :key="msg.id">
            <div
                :class="{
                    'ml-auto bg-terroir-green text-white': msg.sender_type === 'staff',
                    'mr-auto bg-terroir-gold/15 text-terroir-dark': msg.sender_type === 'bot',
                    'mr-auto bg-terroir-cream text-terroir-dark': msg.sender_type === 'client',
                }"
                class="max-w-[75%] rounded-2xl px-4 py-2.5 text-sm"
            >
                <p x-show="msg.sender_type === 'bot' && !['ai', 'order_assistant'].includes(msg.source)" class="mb-0.5 text-[10px] font-semibold uppercase tracking-wide opacity-60">🤖 Assistant automatique</p>
                <p x-show="msg.sender_type === 'bot' && msg.source === 'ai'" class="mb-0.5 text-[10px] font-semibold uppercase tracking-wide opacity-60">🧠 Réponse générée par IA</p>
                <p x-show="msg.sender_type === 'bot' && msg.source === 'order_assistant'" class="mb-0.5 text-[10px] font-semibold uppercase tracking-wide opacity-60">🧠 Envoyé par l'assistant commande</p>
                <p x-text="msg.body" class="whitespace-pre-line"></p>
                <div x-show="msg.links && msg.links.length" class="mt-2 flex flex-col gap-1">
                    <template x-for="link in msg.links" :key="link.url">
                        <a :href="link.url" target="_blank" class="block truncate text-xs font-semibold underline opacity-80" x-text="link.label"></a>
                    </template>
                </div>
                <p class="mt-1 text-[10px] opacity-60">
                    <span x-show="msg.sender_type === 'staff' && msg.sender_name" x-text="msg.sender_name"></span>
                    <span x-text="msg.created_at"></span>
                </p>
            </div>
        </template>
    </div>

    <form action="{{ route('admin.messagerie.reply', $conversation) }}" method="POST" class="flex items-center gap-2 border-t border-terroir-green/10 p-4">
        @csrf
        <input type="text" name="message" required placeholder="Votre réponse..." class="input flex-1">
        <button type="submit" class="btn-primary">Envoyer</button>
    </form>
</div>
@endsection
