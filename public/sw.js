const CACHE_VERSION = 'centraldachat-v1';
const OFFLINE_URL = '/hors-ligne';
const PRECACHE = [
    OFFLINE_URL,
    '/icons/icon-192.png',
    '/icons/icon-512.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_VERSION).then((cache) => cache.addAll(PRECACHE)).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((k) => k !== CACHE_VERSION).map((k) => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET' || !request.url.startsWith(self.location.origin)) {
        return;
    }

    // Page navigations: try the network first, fall back to a cached offline page.
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match(OFFLINE_URL))
        );
        return;
    }

    // Static build assets and icons: cache-first, refresh in the background.
    const url = new URL(request.url);
    if (url.pathname.startsWith('/build/') || url.pathname.startsWith('/icons/')) {
        event.respondWith(
            caches.match(request).then((cached) => {
                const network = fetch(request).then((response) => {
                    caches.open(CACHE_VERSION).then((cache) => cache.put(request, response.clone()));
                    return response;
                }).catch(() => cached);

                return cached || network;
            })
        );
    }
});

self.addEventListener('push', (event) => {
    if (!event.data) return;

    let payload;
    try {
        payload = event.data.json();
    } catch (e) {
        payload = { title: 'Central d\'Achat', body: event.data.text() };
    }

    event.waitUntil(
        self.registration.showNotification(payload.title || 'Central d\'Achat', {
            body: payload.body || '',
            icon: '/icons/icon-192.png',
            badge: '/icons/icon-192.png',
            data: { url: payload.url || '/' },
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data && event.notification.data.url ? event.notification.data.url : '/';

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientsArr) => {
            const existing = clientsArr.find((c) => c.url === url);
            if (existing) return existing.focus();
            return self.clients.openWindow(url);
        })
    );
});
