import { useEffect, useRef } from 'react';

const POLL_INTERVAL_MS = 20000;

function playChime() {
    try {
        const Ctx = window.AudioContext || window.webkitAudioContext;
        if (!Ctx) return;
        const ctx = new Ctx();
        const now = ctx.currentTime;

        [880, 1318.5].forEach((freq, i) => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.value = freq;
            const start = now + i * 0.12;
            gain.gain.setValueAtTime(0, start);
            gain.gain.linearRampToValueAtTime(0.18, start + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.001, start + 0.5);
            osc.connect(gain).connect(ctx.destination);
            osc.start(start);
            osc.stop(start + 0.55);
        });

        setTimeout(() => ctx.close(), 900);
    } catch {
        // Web Audio unavailable or blocked — fail silently, never break the UI.
    }
}

export default function SoundNotifications() {
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
                    const hasNewOrder = data.latest_order_id > prev.latest_order_id;
                    const hasNewChat = data.unread_chat > prev.unread_chat;
                    const hasNewContact = data.unread_contact > prev.unread_contact;
                    if (hasNewOrder || hasNewChat || hasNewContact) {
                        playChime();
                    }
                }

                previous.current = data;
            } catch {
                // network hiccup — next poll retries
            }
        }

        poll();
        const interval = setInterval(poll, POLL_INTERVAL_MS);
        return () => { cancelled = true; clearInterval(interval); };
    }, []);

    return null;
}
