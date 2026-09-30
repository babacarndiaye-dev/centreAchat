import { useEffect, useRef, useState } from 'react';
import { Link, usePage } from '@inertiajs/react';

const QUICK_REPLIES = [
    { icon: 'shopping_cart', label: 'Voir les produits', text: 'Comment voir vos produits ?' },
    { icon: 'local_shipping', label: 'Suivre ma commande', text: 'Suivre ma commande' },
    { icon: 'local_shipping', label: 'Livraison', text: 'Quels sont vos délais et zones de livraison ?' },
    { icon: 'credit_card', label: 'Moyens de paiement', text: 'Quels moyens de paiement acceptez-vous ?' },
    { icon: 'hotel', label: 'Je suis un professionnel', text: 'Comment devenir client professionnel ?' },
    { icon: 'handshake', label: 'Devenir fournisseur', text: 'Comment devenir fournisseur ?' },
    { icon: 'luggage', label: 'Je suis touriste', text: 'Je suis touriste, comment puis-je commander ?' },
];

const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

export default function ChatWidget() {
    const { props } = usePage();
    const isAuthenticated = Boolean(props.auth?.user);

    const [open, setOpen] = useState(false);
    const [messages, setMessages] = useState([]);
    const [renderedText, setRenderedText] = useState({});
    const [typing, setTyping] = useState(false);
    const [suggestions, setSuggestions] = useState([]);
    const [draft, setDraft] = useState('');
    const [sending, setSending] = useState(false);
    const [hasUnread, setHasUnread] = useState(false);

    const initializedRef = useRef(false);
    const messagesRef = useRef([]);
    const openRef = useRef(open);
    const revealChainRef = useRef(Promise.resolve());
    const scrollRef = useRef(null);

    useEffect(() => { messagesRef.current = messages; }, [messages]);
    useEffect(() => { openRef.current = open; }, [open]);

    function scrollToBottom() {
        requestAnimationFrame(() => {
            if (scrollRef.current) scrollRef.current.scrollTop = scrollRef.current.scrollHeight;
        });
    }

    async function typewrite(id, text) {
        setRenderedText((prev) => ({ ...prev, [id]: '' }));
        const frames = Math.min(text.length, 45);
        const step = Math.max(1, Math.ceil(text.length / frames));
        for (let i = 0; i <= text.length; i += step) {
            const slice = text.slice(0, i);
            setRenderedText((prev) => ({ ...prev, [id]: slice }));
            scrollToBottom();
            await wait(14);
        }
        setRenderedText((prev) => ({ ...prev, [id]: text }));
    }

    async function revealNewMessages(newMessages) {
        for (const msg of newMessages) {
            if (msg.sender_type === 'client') {
                setRenderedText((prev) => ({ ...prev, [msg.id]: msg.body }));
                continue;
            }
            setTyping(true);
            scrollToBottom();
            await wait(500 + Math.random() * 500);
            setTyping(false);
            await typewrite(msg.id, msg.body);
        }
    }

    async function applyState(data) {
        if (data.suggestions) setSuggestions(data.suggestions);

        const knownIds = new Set(messagesRef.current.map((m) => m.id));
        const newMessages = (data.messages ?? []).filter((m) => !knownIds.has(m.id));
        setMessages(data.messages ?? []);

        if (!initializedRef.current) {
            const initial = {};
            (data.messages ?? []).forEach((m) => { initial[m.id] = m.body; });
            setRenderedText((prev) => ({ ...prev, ...initial }));
            initializedRef.current = true;
            return;
        }

        if (newMessages.length === 0) {
            setTyping(false);
            return;
        }

        if (!openRef.current) {
            setHasUnread(true);
            const map = {};
            newMessages.forEach((m) => { map[m.id] = m.body; });
            setRenderedText((prev) => ({ ...prev, ...map }));
            return;
        }

        revealChainRef.current = revealChainRef.current.then(() => revealNewMessages(newMessages));
        await revealChainRef.current;
    }

    function poll() {
        fetch(route('chat.state'), { headers: { Accept: 'application/json' } })
            .then((r) => r.json())
            .then(applyState)
            .catch(() => {});
    }

    useEffect(() => {
        poll();
        const interval = setInterval(poll, 4000);
        return () => clearInterval(interval);
    }, []);

    function post(url, body) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            },
            body: JSON.stringify(body),
        }).then((r) => r.json());
    }

    function send(text) {
        const message = (text ?? draft).trim();
        if (!message || sending) return;
        setSending(true);
        setTyping(true);
        scrollToBottom();
        setDraft('');
        post(route('chat.send'), { message })
            .then((data) => {
                setSending(false);
                return applyState(data);
            })
            .catch(() => { setSending(false); setTyping(false); });
    }

    function askAgent() {
        post(route('chat.transfer'), {}).then(applyState).catch(() => {});
    }

    function toggle() {
        const next = !open;
        setOpen(next);
        if (next) {
            setHasUnread(false);
            scrollToBottom();
        }
    }

    return (
        <div className="fixed bottom-5 right-5 z-50">
            <button
                onClick={toggle}
                type="button"
                aria-label="Ouvrir le chat"
                className="relative flex h-14 w-14 items-center justify-center rounded-full bg-terroir-green text-2xl text-white shadow-xl transition hover:bg-terroir-green/90"
            >
                <span className="material-symbols-outlined is-filled">chat</span>
                {hasUnread && (
                    <span className="absolute -right-0.5 -top-0.5 h-3.5 w-3.5 rounded-full bg-terroir-terracotta ring-2 ring-white" />
                )}
            </button>

            {open && (
                <div className="absolute bottom-[4.5rem] right-0 flex h-[30rem] w-80 max-w-[calc(100vw-2.5rem)] flex-col overflow-hidden rounded-2xl border border-terroir-green/10 bg-white shadow-2xl">
                    <div className="flex items-center justify-between bg-terroir-green px-4 py-3 text-white">
                        <span className="font-display text-sm font-semibold">Discuter avec nous</span>
                        <div className="flex items-center gap-3">
                            <button onClick={askAgent} type="button" className="text-xs text-white/80 underline hover:text-white">
                                Parler à un agent
                            </button>
                            <button onClick={() => setOpen(false)} type="button" aria-label="Fermer" className="text-lg text-white/80 hover:text-white">
                                <span className="material-symbols-outlined">close</span>
                            </button>
                        </div>
                    </div>

                    <div ref={scrollRef} className="flex-1 space-y-3 overflow-y-auto px-4 py-3">
                        {messages.length === 0 && (
                            <div>
                                <p className="flex items-center gap-1.5 text-sm text-terroir-dark">
                                    <span className="material-symbols-outlined text-base">waving_hand</span>
                                    Bonjour et bienvenue chez DIABA HOTEL !
                                </p>
                                <p className="mt-1 text-sm text-terroir-dark/60">Comment puis-je vous aider aujourd'hui ?</p>
                                <div className="mt-3 flex flex-wrap gap-1.5">
                                    {QUICK_REPLIES.map((q) => (
                                        <button
                                            key={q.label}
                                            type="button"
                                            onClick={() => send(q.text)}
                                            className="inline-flex items-center gap-1 rounded-full border border-terroir-green/20 px-2.5 py-1 text-xs text-terroir-green hover:bg-terroir-green/5"
                                        >
                                            <span className="material-symbols-outlined text-sm">{q.icon}</span>
                                            {q.label}
                                        </button>
                                    ))}
                                    <button
                                        type="button"
                                        onClick={askAgent}
                                        className="inline-flex items-center gap-1 rounded-full border border-terroir-green/20 px-2.5 py-1 text-xs text-terroir-green hover:bg-terroir-green/5"
                                    >
                                        <span className="material-symbols-outlined text-sm">forum</span>
                                        Parler à un conseiller
                                    </button>
                                </div>
                                {suggestions.length > 0 && (
                                    <div className="mt-4 border-t border-terroir-green/10 pt-3">
                                        <p className="text-[11px] font-semibold uppercase tracking-wide text-terroir-dark/40">Questions fréquentes</p>
                                        <div className="mt-2 flex flex-wrap gap-1.5">
                                            {suggestions.map((q) => (
                                                <button
                                                    key={q}
                                                    type="button"
                                                    onClick={() => send(q)}
                                                    className="rounded-full bg-terroir-cream px-2.5 py-1 text-xs text-terroir-dark/70 hover:bg-terroir-cream/70"
                                                >
                                                    {q}
                                                </button>
                                            ))}
                                        </div>
                                    </div>
                                )}
                            </div>
                        )}

                        {messages.map((msg) => (
                            <div
                                key={msg.id}
                                className={
                                    'max-w-[85%] rounded-2xl px-3 py-2 text-sm ' +
                                    (msg.sender_type === 'client'
                                        ? 'ml-auto bg-terroir-green text-white'
                                        : msg.sender_type === 'bot'
                                            ? 'mr-auto bg-terroir-gold/15 text-terroir-dark'
                                            : 'mr-auto bg-terroir-cream text-terroir-dark')
                                }
                            >
                                {msg.sender_type === 'bot' && (
                                    <p className="mb-0.5 flex items-center gap-1 text-[10px] font-semibold uppercase tracking-wide opacity-60">
                                        <span className="material-symbols-outlined text-xs">smart_toy</span>
                                        Assistant
                                    </p>
                                )}
                                <p className="whitespace-pre-line">{renderedText[msg.id] ?? ''}</p>
                                {(renderedText[msg.id] ?? '') === msg.body && msg.links?.length > 0 && (
                                    <div className="mt-2 space-y-1">
                                        {msg.links.map((link) => (
                                            <a
                                                key={link.url}
                                                href={link.url}
                                                className="block truncate text-xs font-medium underline opacity-80 hover:opacity-100"
                                            >
                                                {link.label}
                                            </a>
                                        ))}
                                    </div>
                                )}
                                <p className="mt-1 text-[10px] opacity-60">{msg.created_at}</p>
                            </div>
                        ))}

                        {typing && (
                            <div className="mr-auto flex max-w-[85%] items-center gap-2 rounded-2xl bg-terroir-gold/15 px-3.5 py-3">
                                <span className="flex items-center gap-1">
                                    <span className="h-1.5 w-1.5 animate-bounce rounded-full bg-terroir-dark/40" style={{ animationDelay: '0ms' }} />
                                    <span className="h-1.5 w-1.5 animate-bounce rounded-full bg-terroir-dark/40" style={{ animationDelay: '150ms' }} />
                                    <span className="h-1.5 w-1.5 animate-bounce rounded-full bg-terroir-dark/40" style={{ animationDelay: '300ms' }} />
                                </span>
                                <span className="text-xs text-terroir-dark/50">L'assistant réfléchit...</span>
                            </div>
                        )}
                    </div>

                    <form
                        onSubmit={(e) => { e.preventDefault(); send(); }}
                        className="border-t border-terroir-green/10 p-3"
                    >
                        <div className="flex gap-2">
                            <input
                                value={draft}
                                onChange={(e) => setDraft(e.target.value)}
                                type="text"
                                placeholder="Votre message..."
                                className="input flex-1 text-sm"
                                disabled={sending}
                            />
                            <button
                                type="submit"
                                className="rounded-full bg-terroir-green px-4 py-2 text-sm font-medium text-white disabled:opacity-50"
                                disabled={sending || !draft.trim()}
                            >
                                Envoyer
                            </button>
                        </div>
                        {isAuthenticated && (
                            <Link href={route('compte.messages.index')} className="mt-2 block text-center text-xs text-terroir-dark/40 hover:underline">
                                Historique de mes conversations
                            </Link>
                        )}
                    </form>
                </div>
            )}
        </div>
    );
}
