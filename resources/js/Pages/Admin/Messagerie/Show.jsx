import { useEffect, useRef, useState } from 'react';
import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import AdminLayout from '../../../Layouts/AdminLayout';

function MessageBubble({ msg }) {
    const bubbleClass =
        msg.sender_type === 'staff'
            ? 'ml-auto bg-terroir-green text-white'
            : msg.sender_type === 'bot'
                ? 'mr-auto bg-terroir-gold/15 text-terroir-dark'
                : 'mr-auto bg-terroir-cream text-terroir-dark';

    const isPlainBot = msg.sender_type === 'bot' && !['ai', 'order_assistant'].includes(msg.source);
    const isAiBot = msg.sender_type === 'bot' && msg.source === 'ai';
    const isOrderAssistantBot = msg.sender_type === 'bot' && msg.source === 'order_assistant';

    return (
        <div className={'max-w-[75%] rounded-2xl px-4 py-2.5 text-sm ' + bubbleClass}>
            {isPlainBot && (
                <p className="mb-0.5 flex items-center gap-1 text-[10px] font-semibold uppercase tracking-wide opacity-60">
                    <span className="material-symbols-outlined text-xs">smart_toy</span> Assistant automatique
                </p>
            )}
            {isAiBot && (
                <p className="mb-0.5 flex items-center gap-1 text-[10px] font-semibold uppercase tracking-wide opacity-60">
                    <span className="material-symbols-outlined text-xs">psychology</span> Réponse générée par IA
                </p>
            )}
            {isOrderAssistantBot && (
                <p className="mb-0.5 flex items-center gap-1 text-[10px] font-semibold uppercase tracking-wide opacity-60">
                    <span className="material-symbols-outlined text-xs">psychology</span> Envoyé par l'assistant commande
                </p>
            )}
            <p className="whitespace-pre-line">{msg.body}</p>
            {msg.links && msg.links.length > 0 && (
                <div className="mt-2 flex flex-col gap-1">
                    {msg.links.map((link) => (
                        <a key={link.url} href={link.url} target="_blank" rel="noreferrer" className="block truncate text-xs font-semibold underline opacity-80">
                            {link.label}
                        </a>
                    ))}
                </div>
            )}
            <p className="mt-1 text-[10px] opacity-60">
                {msg.sender_type === 'staff' && msg.sender_name && <span>{msg.sender_name} </span>}
                <span>{msg.created_at}</span>
            </p>
        </div>
    );
}

export default function Show({ conversation }) {
    const { props } = usePage();
    const currentUserId = props.auth?.user?.id;
    const [messages, setMessages] = useState(conversation.messages);
    const threadRef = useRef(null);

    useEffect(() => {
        setMessages(conversation.messages);
    }, [conversation]);

    function scrollToBottom() {
        const el = threadRef.current;
        if (el) el.scrollTop = el.scrollHeight;
    }

    useEffect(() => {
        scrollToBottom();
    }, [messages]);

    useEffect(() => {
        const interval = setInterval(() => {
            fetch(route('admin.messagerie.messages', conversation.id), { headers: { Accept: 'application/json' } })
                .then((r) => r.json())
                .then((data) => {
                    setMessages((current) => (data.messages.length !== current.length ? data.messages : current));
                })
                .catch(() => {});
        }, 4000);

        return () => clearInterval(interval);
    }, [conversation.id]);

    const { data, setData, post, processing, reset } = useForm({ message: '' });

    function handleSend(e) {
        e.preventDefault();
        post(route('admin.messagerie.reply', conversation.id), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    }

    function handleAssign() {
        router.patch(route('admin.messagerie.assign', conversation.id), {}, { preserveScroll: true });
    }

    function handleClose() {
        router.patch(route('admin.messagerie.close', conversation.id), {}, { preserveScroll: true });
    }

    function handleReopen() {
        router.patch(route('admin.messagerie.reopen', conversation.id), {}, { preserveScroll: true });
    }

    return (
        <AdminLayout title={`Conversation — ${conversation.customer_name}`}>
            <Head title={`Conversation — ${conversation.customer_name} — Administration`} />

            <div className="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <Link href={route('admin.messagerie.index')} className="text-sm text-terroir-dark/50">&larr; Retour à la messagerie</Link>
                    <h2 className="mt-1.5 font-display text-lg font-semibold">{conversation.customer_name}</h2>
                    <p className="text-sm text-terroir-dark/50">
                        {conversation.email ?? 'Visiteur anonyme'}
                        {conversation.assignee && ` — assignée à ${conversation.assignee.name}`}
                        {conversation.bot_enabled && (
                            <span className="inline-flex items-center gap-1 text-terroir-green"> — <span className="material-symbols-outlined text-base">smart_toy</span> assistant actif</span>
                        )}
                    </p>
                </div>
                <div className="flex gap-2">
                    {(!conversation.assignee || conversation.assignee.id !== currentUserId) && (
                        <button type="button" onClick={handleAssign} className="rounded-full border border-terroir-green/20 px-3.5 py-1.5 text-sm text-terroir-green">
                            Me l'assigner
                        </button>
                    )}
                    {conversation.status === 'fermee' ? (
                        <button type="button" onClick={handleReopen} className="rounded-full border border-terroir-green/20 px-3.5 py-1.5 text-sm text-terroir-green">
                            Réouvrir
                        </button>
                    ) : (
                        <button type="button" onClick={handleClose} className="rounded-full border border-terroir-terracotta/30 px-3.5 py-1.5 text-sm text-terroir-terracotta">
                            Fermer
                        </button>
                    )}
                </div>
            </div>

            <div className="admin-card mt-6 flex flex-col p-0" style={{ height: '32rem' }}>
                <div ref={threadRef} className="flex flex-1 flex-col gap-3 overflow-y-auto px-5 py-4">
                    {messages.map((msg) => <MessageBubble key={msg.id} msg={msg} />)}
                </div>

                <form onSubmit={handleSend} className="flex items-center gap-2 border-t border-terroir-green/10 p-4">
                    <input
                        type="text"
                        value={data.message}
                        onChange={(e) => setData('message', e.target.value)}
                        required
                        placeholder="Votre réponse..."
                        className="input flex-1"
                    />
                    <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Envoyer</button>
                </form>
            </div>
        </AdminLayout>
    );
}
