<div
    x-data="{
        open: false,
        conversationId: null,
        status: null,
        messages: [],
        renderedText: {},
        typing: false,
        initialized: false,
        revealChain: Promise.resolve(),
        suggestions: [],
        draft: '',
        sending: false,
        hasUnread: false,
        init() {
            this.poll();
            setInterval(() => this.poll(), 4000);
        },
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.hasUnread = false;
                this.$nextTick(() => this.scrollToBottom());
            }
        },
        poll() {
            fetch('{{ route('chat.state') }}', { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => this.applyState(data))
                .catch(() => {});
        },
        wait(ms) { return new Promise((resolve) => setTimeout(resolve, ms)); },
        async typewrite(id, text) {
            this.renderedText[id] = '';
            const frames = Math.min(text.length, 45);
            const step = Math.max(1, Math.ceil(text.length / frames));
            for (let i = 0; i <= text.length; i += step) {
                this.renderedText[id] = text.slice(0, i);
                this.$nextTick(() => this.scrollToBottom());
                await this.wait(14);
            }
            this.renderedText[id] = text;
        },
        async revealNewMessages(newMessages) {
            for (const msg of newMessages) {
                if (msg.sender_type === 'client') {
                    this.renderedText[msg.id] = msg.body;
                    continue;
                }
                this.typing = true;
                this.$nextTick(() => this.scrollToBottom());
                await this.wait(500 + Math.random() * 500);
                this.typing = false;
                await this.typewrite(msg.id, msg.body);
            }
        },
        async applyState(data) {
            this.conversationId = data.conversation ? data.conversation.id : this.conversationId;
            this.status = data.conversation ? data.conversation.status : this.status;
            if (data.suggestions) this.suggestions = data.suggestions;

            const knownIds = new Set(this.messages.map((m) => m.id));
            const newMessages = data.messages.filter((m) => !knownIds.has(m.id));
            this.messages = data.messages;

            if (!this.initialized) {
                // First load (history/refresh): show instantly, no typing replay.
                data.messages.forEach((m) => { this.renderedText[m.id] = m.body; });
                this.initialized = true;
                return;
            }

            if (newMessages.length === 0) { this.typing = false; return; }

            if (!this.open) {
                this.hasUnread = true;
                newMessages.forEach((m) => { this.renderedText[m.id] = m.body; });
                return;
            }

            this.revealChain = this.revealChain.then(() => this.revealNewMessages(newMessages));
            await this.revealChain;
        },
        post(url, body) {
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify(body),
            }).then(r => r.json());
        },
        send(text) {
            const message = (text ?? this.draft).trim();
            if (!message || this.sending) return;
            this.sending = true;
            this.typing = true;
            this.$nextTick(() => this.scrollToBottom());
            const body = { message };
            this.draft = '';
            this.post('{{ route('chat.send') }}', body)
                .then(data => {
                    this.sending = false;
                    return this.applyState(data);
                })
                .catch(() => { this.sending = false; this.typing = false; });
        },
        askAgent() {
            this.post('{{ route('chat.transfer') }}', {})
                .then(data => this.applyState(data))
                .catch(() => {});
        },
        scrollToBottom() {
            const el = this.$refs.scrollArea;
            if (el) el.scrollTop = el.scrollHeight;
        },
    }"
    class="fixed bottom-5 right-5 z-50"
>
    <button @click="toggle()" type="button" aria-label="Ouvrir le chat" class="relative flex h-14 w-14 items-center justify-center rounded-full bg-terroir-green text-2xl text-white shadow-xl transition hover:bg-terroir-green/90">
        <span class="material-symbols-outlined is-filled">chat</span>
        <span x-show="hasUnread" x-cloak class="absolute -right-0.5 -top-0.5 h-3.5 w-3.5 rounded-full bg-terroir-terracotta ring-2 ring-white"></span>
    </button>

    <div x-show="open" x-transition x-cloak class="absolute bottom-[4.5rem] right-0 flex h-[30rem] w-80 max-w-[calc(100vw-2.5rem)] flex-col overflow-hidden rounded-2xl border border-terroir-green/10 bg-white shadow-2xl">
        <div class="flex items-center justify-between bg-terroir-green px-4 py-3 text-white">
            <span class="font-display text-sm font-semibold">Discuter avec nous</span>
            <div class="flex items-center gap-3">
                <button @click="askAgent()" type="button" class="text-xs text-white/80 underline hover:text-white">Parler à un agent</button>
                <button @click="open = false" type="button" aria-label="Fermer" class="text-lg text-white/80 hover:text-white">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>

        <div class="flex-1 space-y-3 overflow-y-auto px-4 py-3" x-ref="scrollArea">
            <template x-if="messages.length === 0">
                <div>
                    <p class="text-sm text-terroir-dark">👋 Bonjour et bienvenue chez Central d'Achat !</p>
                    <p class="mt-1 text-sm text-terroir-dark/60">Comment puis-je vous aider aujourd'hui ?</p>
                    <div class="mt-3 flex flex-wrap gap-1.5">
                        <button type="button" @click="send('Comment voir vos produits ?')" class="rounded-full border border-terroir-green/20 px-2.5 py-1 text-xs text-terroir-green hover:bg-terroir-green/5">🛒 Voir les produits</button>
                        <button type="button" @click="send('Suivre ma commande')" class="rounded-full border border-terroir-green/20 px-2.5 py-1 text-xs text-terroir-green hover:bg-terroir-green/5">📦 Suivre ma commande</button>
                        <button type="button" @click="send('Quels sont vos délais et zones de livraison ?')" class="rounded-full border border-terroir-green/20 px-2.5 py-1 text-xs text-terroir-green hover:bg-terroir-green/5">🚚 Livraison</button>
                        <button type="button" @click="send('Quels moyens de paiement acceptez-vous ?')" class="rounded-full border border-terroir-green/20 px-2.5 py-1 text-xs text-terroir-green hover:bg-terroir-green/5">💳 Moyens de paiement</button>
                        <button type="button" @click="send('Comment devenir client professionnel ?')" class="rounded-full border border-terroir-green/20 px-2.5 py-1 text-xs text-terroir-green hover:bg-terroir-green/5">🏨 Je suis un professionnel</button>
                        <button type="button" @click="send('Comment devenir fournisseur ?')" class="rounded-full border border-terroir-green/20 px-2.5 py-1 text-xs text-terroir-green hover:bg-terroir-green/5">🤝 Devenir fournisseur</button>
                        <button type="button" @click="send('Je suis touriste, comment puis-je commander ?')" class="rounded-full border border-terroir-green/20 px-2.5 py-1 text-xs text-terroir-green hover:bg-terroir-green/5">🧳 Je suis touriste</button>
                        <button type="button" @click="askAgent()" class="rounded-full border border-terroir-green/20 px-2.5 py-1 text-xs text-terroir-green hover:bg-terroir-green/5">💬 Parler à un conseiller</button>
                    </div>
                    <div x-show="suggestions.length" class="mt-4 border-t border-terroir-green/10 pt-3">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-terroir-dark/40">Questions fréquentes</p>
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            <template x-for="q in suggestions" :key="q">
                                <button type="button" @click="send(q)" class="rounded-full bg-terroir-cream px-2.5 py-1 text-xs text-terroir-dark/70 hover:bg-terroir-cream/70" x-text="q"></button>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
            <template x-for="msg in messages" :key="msg.id">
                <div
                    :class="{
                        'ml-auto bg-terroir-green text-white': msg.sender_type === 'client',
                        'mr-auto bg-terroir-gold/15 text-terroir-dark': msg.sender_type === 'bot',
                        'mr-auto bg-terroir-cream text-terroir-dark': msg.sender_type === 'staff',
                    }"
                    class="max-w-[85%] rounded-2xl px-3 py-2 text-sm"
                >
                    <p x-show="msg.sender_type === 'bot'" class="mb-0.5 text-[10px] font-semibold uppercase tracking-wide opacity-60">🤖 Assistant</p>
                    <p x-text="renderedText[msg.id] ?? ''" class="whitespace-pre-line"></p>
                    <div x-show="(renderedText[msg.id] ?? '') === msg.body && msg.links && msg.links.length" class="mt-2 space-y-1">
                        <template x-for="link in msg.links" :key="link.url">
                            <a :href="link.url" class="block truncate text-xs font-medium underline opacity-80 hover:opacity-100" x-text="link.label"></a>
                        </template>
                    </div>
                    <p class="mt-1 text-[10px] opacity-60" x-text="msg.created_at"></p>
                </div>
            </template>
            <template x-if="typing">
                <div class="mr-auto flex max-w-[85%] items-center gap-2 rounded-2xl bg-terroir-gold/15 px-3.5 py-3">
                    <span class="flex items-center gap-1">
                        <span class="h-1.5 w-1.5 animate-bounce rounded-full bg-terroir-dark/40" style="animation-delay: 0ms"></span>
                        <span class="h-1.5 w-1.5 animate-bounce rounded-full bg-terroir-dark/40" style="animation-delay: 150ms"></span>
                        <span class="h-1.5 w-1.5 animate-bounce rounded-full bg-terroir-dark/40" style="animation-delay: 300ms"></span>
                    </span>
                    <span class="text-xs text-terroir-dark/50">L'assistant réfléchit...</span>
                </div>
            </template>
        </div>

        <form @submit.prevent="send()" class="border-t border-terroir-green/10 p-3">
            <div class="flex gap-2">
                <input x-model="draft" type="text" placeholder="Votre message..." class="input flex-1 text-sm" :disabled="sending">
                <button type="submit" class="rounded-full bg-terroir-green px-4 py-2 text-sm font-medium text-white disabled:opacity-50" :disabled="sending || !draft.trim()">Envoyer</button>
            </div>
            @auth
                <a href="{{ route('compte.messages.index') }}" class="mt-2 block text-center text-xs text-terroir-dark/40 hover:underline">Historique de mes conversations</a>
            @endauth
        </form>
    </div>
</div>
