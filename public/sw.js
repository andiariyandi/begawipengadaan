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
        icon: payload.icon || '/assets/main/images/resources/logo-1.png',
        badge: payload.badge || '/assets/main/images/resources/logo-1.png',
        tag: payload.tag || ('bp-push-' + Date.now()),
        renotify: true,
        data: payload
    };

    event.waitUntil(self.registration.showNotification(title, options));
});
