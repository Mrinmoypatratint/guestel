self.addEventListener('install', event => self.skipWaiting());
self.addEventListener('activate', event => event.waitUntil(self.clients.claim()));
// Intentionally network-only: guest/order/chat responses can contain private session data.
self.addEventListener('fetch', event => { event.respondWith(fetch(event.request)); });
