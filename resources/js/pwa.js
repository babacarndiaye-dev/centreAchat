function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = atob(base64);
    return Uint8Array.from([...rawData].map((c) => c.charCodeAt(0)));
}

function csrfToken() {
    const meta = document.querySelector('meta[name=csrf-token]');
    return meta ? meta.content : '';
}

async function registerServiceWorker() {
    if (!('serviceWorker' in navigator)) return;
    try {
        await navigator.serviceWorker.register('/sw.js');
    } catch (e) {
        console.warn('Service worker registration failed', e);
    }
}

window.DiabaHotelPush = {
    async status() {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) return 'unsupported';
        const reg = await navigator.serviceWorker.ready;
        const sub = await reg.pushManager.getSubscription();
        return sub ? 'subscribed' : 'not-subscribed';
    },
    async subscribe(vapidPublicKey) {
        const reg = await navigator.serviceWorker.ready;
        const permission = await Notification.requestPermission();
        if (permission !== 'granted') return false;

        const sub = await reg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
        });

        await fetch('/push/abonnement', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify(sub.toJSON()),
        });

        return true;
    },
    async unsubscribe() {
        const reg = await navigator.serviceWorker.ready;
        const sub = await reg.pushManager.getSubscription();
        if (!sub) return true;

        await fetch('/push/abonnement', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify({ endpoint: sub.endpoint }),
        });

        await sub.unsubscribe();

        return true;
    },
};

registerServiceWorker();
