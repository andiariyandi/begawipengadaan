/**
 * BegawiPengadaan - Progressive Web App & Service Worker
 * Native Browser Push & Desktop Notification Dispatcher
 */
const SW_VERSION = '1.0.0-bp-notif';

self.addEventListener('install', function(event) {
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    event.waitUntil(self.clients.claim());
});

// Penanganan pesan dari tab/jendela (Foreground / Background Message Dispatcher)
self.addEventListener('message', function(event) {
    if (!event.data) return;

    if (event.data.type === 'SHOW_NOTIFICATION') {
        const title = event.data.title || 'BegawiPengadaan';
        const options = event.data.options || {};
        event.waitUntil(self.registration.showNotification(title, options));
    }
});

// Penanganan klik pada banner notifikasi native browser / OS
self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    const notifData = event.notification.data || {};
    const targetUrl = notifData.url || '/workspace/notifikasi';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            // Jika ada tab yang sedang terbuka, bawa ke depan (focus) dan arahkan ke URL
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url && 'focus' in client) {
                    client.focus();
                    if ('navigate' in client && targetUrl) {
                        client.navigate(targetUrl);
                    }
                    return;
                }
            }
            // Jika tidak ada jendela/tab yang terbuka, buka tab baru
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});

// Penanganan payload push latar belakang
self.addEventListener('push', function(event) {
    let payload = {};
    try {
        payload = event.data ? event.data.json() : {};
    } catch (e) {
        payload = {
            title: 'BegawiPengadaan',
            body: event.data ? event.data.text() : 'Pemberitahuan baru.'
        };
    }

    const title = payload.title || 'BegawiPengadaan';
    const options = {
        body: payload.body || 'Pemberitahuan baru di BegawiPengadaan.',
        icon: payload.icon || '/assets/dashboard/images/favicon.png',
        badge: payload.badge || '/assets/dashboard/images/favicon.png',
        tag: payload.tag || ('bp-push-' + Date.now()),
        renotify: true,
        vibrate: [300, 100, 300, 100, 300],
        timestamp: Date.now(),
        data: payload
    };

    event.waitUntil(self.registration.showNotification(title, options));
});
