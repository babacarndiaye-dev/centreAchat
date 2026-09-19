@extends('layouts.admin')

@section('title', 'Conversation — ' . $conversation->customerName())

@section('content')
<div class="uk-flex uk-flex-between uk-flex-wrap" style="gap:12px;">
    <div>
        <a href="{{ route('admin.messagerie.index') }}" class="uk-text-small uk-text-muted">&larr; Retour à la messagerie</a>
        <h2 class="uk-margin-small-top" style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">{{ $conversation->customerName() }}</h2>
        <p class="uk-text-small" style="color:rgba(31,35,40,.5);">
            {{ $conversation->user?->email ?? $conversation->guest_email ?? 'Visiteur anonyme' }}
            @if($conversation->assignee) — assignée à {{ $conversation->assignee->name }} @endif
            @if($conversation->bot_enabled) — <span style="color:#1D8A4E;">🤖 assistant actif</span> @endif
        </p>
    </div>
    <div class="uk-flex" style="gap:8px;">
        @if(!$conversation->assignee || $conversation->assignee->id !== auth()->id())
            <form action="{{ route('admin.messagerie.assign', $conversation) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="uk-button uk-button-default uk-button-small" style="border-radius:999px; border:1px solid rgba(29,138,78,.2); color:#1D8A4E;">Me l'assigner</button>
            </form>
        @endif
        @if($conversation->status === 'fermee')
            <form action="{{ route('admin.messagerie.reopen', $conversation) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="uk-button uk-button-default uk-button-small" style="border-radius:999px; border:1px solid rgba(29,138,78,.2); color:#1D8A4E;">Réouvrir</button>
            </form>
        @else
            <form action="{{ route('admin.messagerie.close', $conversation) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="uk-button uk-button-default uk-button-small" style="border-radius:999px; border:1px solid rgba(232,96,79,.3); color:#E8604F;">Fermer</button>
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
    class="uk-card uk-card-default uk-margin-top uk-flex uk-flex-column"
    style="height:32rem; padding:0;"
>
    <div style="flex:1; overflow-y:auto; padding:16px 20px; display:flex; flex-direction:column; gap:12px;" x-ref="thread">
        <template x-for="msg in messages" :key="msg.id">
            <div
                :class="{
                    'uk-margin-auto-left': msg.sender_type === 'staff',
                    'uk-margin-auto-right': msg.sender_type === 'bot' || msg.sender_type === 'client',
                }"
                :style="{
                    background: msg.sender_type === 'staff' ? '#1D8A4E' : (msg.sender_type === 'bot' ? 'rgba(240,169,59,.15)' : '#F7F8F5'),
                    color: msg.sender_type === 'staff' ? '#fff' : '#1F2328',
                }"
                style="max-width:75%; border-radius:16px; padding:10px 16px; font-size:0.875rem;"
            >
                <p x-show="msg.sender_type === 'bot' && !['ai', 'order_assistant'].includes(msg.source)" style="margin-bottom:2px; font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; opacity:.6;">🤖 Assistant automatique</p>
                <p x-show="msg.sender_type === 'bot' && msg.source === 'ai'" style="margin-bottom:2px; font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; opacity:.6;">🧠 Réponse générée par IA</p>
                <p x-show="msg.sender_type === 'bot' && msg.source === 'order_assistant'" style="margin-bottom:2px; font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; opacity:.6;">🧠 Envoyé par l'assistant commande</p>
                <p x-text="msg.body" style="white-space:pre-line;"></p>
                <div x-show="msg.links && msg.links.length" style="margin-top:8px; display:flex; flex-direction:column; gap:4px;">
                    <template x-for="link in msg.links" :key="link.url">
                        <a :href="link.url" target="_blank" style="display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:12px; font-weight:600; text-decoration:underline; opacity:.8;" x-text="link.label"></a>
                    </template>
                </div>
                <p style="margin-top:4px; font-size:10px; opacity:.6;">
                    <span x-show="msg.sender_type === 'staff' && msg.sender_name" x-text="msg.sender_name"></span>
                    <span x-text="msg.created_at"></span>
                </p>
            </div>
        </template>
    </div>

    <form action="{{ route('admin.messagerie.reply', $conversation) }}" method="POST" class="uk-flex uk-flex-middle" style="gap:8px; border-top:1px solid rgba(29,138,78,.1); padding:16px;">
        @csrf
        <input type="text" name="message" required placeholder="Votre réponse..." class="uk-input" style="flex:1;">
        <button type="submit" class="uk-button uk-button-primary">Envoyer</button>
    </form>
</div>
@endsection
