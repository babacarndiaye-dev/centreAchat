import { useEffect, useRef, useState } from 'react';

const POLL_INTERVAL_MS = 20000;

function playTone(freqs, { gain = 0.18, gap = 0.12, duration = 0.5 } = {}) {
    try {
        const Ctx = window.AudioContext || window.webkitAudioContext;
        if (!Ctx) return;
        const ctx = new Ctx();
        const now = ctx.currentTime;

        freqs.forEach((freq, i) => {
            const osc = ctx.createOscillator();
            const g = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.value = freq;
            const start = now + i * gap;
            g.gain.setValueAtTime(0, start);
            g.gain.linearRampToValueAtTime(gain, start + 0.02);
            g.gain.exponentialRampToValueAtTime(0.001, start + duration);
            osc.connect(g).connect(ctx.destination);
            osc.start(start);
            osc.stop(start + duration + 0.05);
        });

        setTimeout(() => ctx.close(), (freqs.length * gap + duration) * 1000 + 300);
    } catch {
        // Web Audio unavailable or blocked — fail silently, never break the UI.
    }
}

// Notification générique (messages/contacts) : deux notes montantes.
function playMessageChime() {
    playTone([880, 1318.5]);
}

// Nouvelle commande : trois notes façon "caisse enregistreuse", volontairement distinct du son ci-dessus.
function playOrderChime() {
    playTone([523.25, 659.25, 783.99], { gain: 0.2, gap: 0.09, duration: 0.4 });
}

export default function NotificationBells({ initialUnreadChat = 0, initialUnreadNotifications = 0, initialOrdersPending = 0 }) {
    const [unreadChat, setUnreadChat] = useState(initialUnreadChat);
    const [unreadNotifications, setUnreadNotifications] = useState(initialUnreadNotifications);
    const [ordersPending, setOrdersPending] = useState(initialOrdersPending);
    const previous = useRef(null);

    useEffect(() => {
        let cancelled = false;

        async function poll() {
            try {
                const res = await fetch(route('admin.notifications.live'), { headers: { Accept: 'application/json' } });
                if (!res.ok) return;
                const data = await res.json();
                if (cancelled) return;

                if (previous.current) {
                    const prev = previous.current;
                    if (data.latest_order_id > prev.latest_order_id) playOrderChime();
                    if (
                        data.unread_chat > prev.unread_chat
                        || data.unread_notifications > prev.unread_notifications
                        || data.unread_contact > prev.unread_contact
                    ) {
                        playMessageChime();
                    }
                }
                previous.current = data;

                setUnreadChat(data.unread_chat);
                setUnreadNotifications(data.unread_notifications);
                setOrdersPending(data.orders_pending);
            } catch {
                // network hiccup — next poll retries
            }
        }

        poll();
        const interval = setInterval(poll, POLL_INTERVAL_MS);
        return () => { cancelled = true; clearInterval(interval); };
    }, []);

    const unreadTotal = unreadChat + unreadNotifications;
    const bellHref = unreadChat > 0 ? route('admin.messagerie.index') : route('admin.notifications.index');

    return (
        <>
            <a href={route('admin.commandes.index')} className="relative text-xl text-terroir-dark/50 hover:text-terroir-dark" aria-label="Commandes en attente">
                <span className="material-symbols-outlined">shopping_bag</span>
                {ordersPending > 0 && (
                    <span className="absolute -right-1.5 -top-1.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-terroir-green px-1 text-[10px] font-bold text-white">
                        {ordersPending > 9 ? '9+' : ordersPending}
                    </span>
                )}
            </a>
            <a href={bellHref} className="relative text-xl text-terroir-dark/50 hover:text-terroir-dark" aria-label="Notifications">
                <span className="material-symbols-outlined">notifications</span>
                {unreadTotal > 0 && (
                    <span className="absolute -right-1.5 -top-1.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-terroir-gold px-1 text-[10px] font-bold text-terroir-dark">
                        {unreadTotal > 9 ? '9+' : unreadTotal}
                    </span>
                )}
            </a>
        </>
    );
}
