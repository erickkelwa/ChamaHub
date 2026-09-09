// ─────────────────────────────────────────────────────────────────────────────
//  ChamaHub Service Worker — Production Grade PWA
//  Version: 2.0.0
// ─────────────────────────────────────────────────────────────────────────────

const CACHE_VERSION   = 'v2';
const SHELL_CACHE     = `chamahub-shell-${CACHE_VERSION}`;
const DYNAMIC_CACHE   = `chamahub-dynamic-${CACHE_VERSION}`;
const OFFLINE_URL     = '/offline.html';
const BG_SYNC_TAG     = 'chamahub-bg-sync';

// Static shell assets to pre-cache on install
const SHELL_ASSETS = [
    '/offline.html',
    '/manifest.json',
    '/favicon.svg',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
];

// ── Install: pre-cache the shell ──────────────────────────────────────────────
self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(SHELL_CACHE).then(cache => {
            return cache.addAll(SHELL_ASSETS).catch(err => {
                console.warn('[SW] Shell cache addAll non-critical error:', err);
            });
        })
    );
});

// ── Activate: delete old caches ───────────────────────────────────────────────
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(name => name !== SHELL_CACHE && name !== DYNAMIC_CACHE)
                    .map(name => {
                        console.log('[SW] Deleting old cache:', name);
                        return caches.delete(name);
                    })
            );
        }).then(() => self.clients.claim())
    );
});

// ── Fetch: smart routing strategy ─────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET and non-HTTP(S) requests
    if (request.method !== 'GET' || !request.url.startsWith('http')) return;

    // Skip API calls (always network-only — no stale auth data)
    if (url.pathname.startsWith('/api/')) return;

    // ── Navigation requests: network-first, fallback to offline.html ──
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then(response => {
                    // Clone and cache successful navigation responses
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(DYNAMIC_CACHE).then(cache => cache.put(request, clone));
                    }
                    return response;
                })
                .catch(async () => {
                    // Try dynamic cache first, then shell offline page
                    const cached = await caches.match(request);
                    return cached || caches.match(OFFLINE_URL);
                })
        );
        return;
    }

    // ── Static assets (CSS/JS/fonts/icons): stale-while-revalidate ──
    const isStatic = /\.(css|js|woff2?|ttf|eot|svg|png|jpg|jpeg|gif|ico|webp)$/i.test(url.pathname)
        || url.hostname === 'fonts.googleapis.com'
        || url.hostname === 'fonts.gstatic.com'
        || url.hostname === 'cdn.jsdelivr.net';

    if (isStatic) {
        event.respondWith(
            caches.open(SHELL_CACHE).then(cache => {
                return cache.match(request).then(cached => {
                    const networkFetch = fetch(request).then(response => {
                        if (response.ok) cache.put(request, response.clone());
                        return response;
                    }).catch(() => null);

                    return cached || networkFetch;
                });
            })
        );
        return;
    }

    // ── Everything else: network-first, dynamic cache fallback ──
    event.respondWith(
        fetch(request)
            .then(response => {
                if (response.ok) {
                    const clone = response.clone();
                    caches.open(DYNAMIC_CACHE).then(cache => cache.put(request, clone));
                }
                return response;
            })
            .catch(() => caches.match(request))
    );
});

// ── Push Notifications ────────────────────────────────────────────────────────
self.addEventListener('push', event => {
    let data = { title: 'ChamaHub', body: 'You have a new notification.', icon: '/icons/icon-192.png', badge: '/icons/icon-96.png' };

    try {
        if (event.data) {
            const parsed = event.data.json();
            data = { ...data, ...parsed };
        }
    } catch (e) {
        data.body = event.data ? event.data.text() : data.body;
    }

    const options = {
        body:    data.body,
        icon:    data.icon    || '/icons/icon-192.png',
        badge:   data.badge   || '/icons/icon-96.png',
        tag:     data.tag     || 'chamahub-notification',
        data:    { url: data.url || '/dashboard' },
        actions: data.actions || [],
        vibrate: [100, 50, 100],
        requireInteraction: data.requireInteraction || false,
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// Notification click — open the app to the linked page
self.addEventListener('notificationclick', event => {
    event.notification.close();
    const targetUrl = event.notification.data?.url || '/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clientList => {
            for (const client of clientList) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    client.navigate(targetUrl);
                    return client.focus();
                }
            }
            return clients.openWindow(targetUrl);
        })
    );
});

// ── Background Sync (offline loan/contribution queuing) ───────────────────────
self.addEventListener('sync', event => {
    if (event.tag === BG_SYNC_TAG) {
        event.waitUntil(replayOfflineRequests());
    }
});

async function replayOfflineRequests() {
    // Read queued requests from IndexedDB
    const db = await openDB();
    const requests = await getAllFromDB(db, 'offlineQueue');

    for (const item of requests) {
        try {
            const response = await fetch(item.url, {
                method:  item.method,
                headers: item.headers,
                body:    item.body,
            });

            if (response.ok) {
                await deleteFromDB(db, 'offlineQueue', item.id);
                // Notify all open clients about the sync
                const clientList = await clients.matchAll({ type: 'window' });
                clientList.forEach(client => {
                    client.postMessage({ type: 'OFFLINE_SYNC_COMPLETE', item });
                });
            }
        } catch (err) {
            console.warn('[SW] BG sync replay failed for:', item.url, err);
        }
    }
}

// ── Minimal IndexedDB helpers ──────────────────────────────────────────────────
function openDB() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open('chamahub-offline', 1);
        req.onupgradeneeded = e => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains('offlineQueue')) {
                db.createObjectStore('offlineQueue', { keyPath: 'id', autoIncrement: true });
            }
        };
        req.onsuccess  = e => resolve(e.target.result);
        req.onerror    = e => reject(e.target.error);
    });
}

function getAllFromDB(db, store) {
    return new Promise((resolve, reject) => {
        const tx = db.transaction(store, 'readonly');
        const req = tx.objectStore(store).getAll();
        req.onsuccess = e => resolve(e.target.result);
        req.onerror   = e => reject(e.target.error);
    });
}

function deleteFromDB(db, store, id) {
    return new Promise((resolve, reject) => {
        const tx = db.transaction(store, 'readwrite');
        const req = tx.objectStore(store).delete(id);
        req.onsuccess = () => resolve();
        req.onerror   = e => reject(e.target.error);
    });
}

// Message from app: queue an offline request
self.addEventListener('message', event => {
    if (event.data?.type === 'QUEUE_OFFLINE_REQUEST') {
        openDB().then(db => {
            const tx = db.transaction('offlineQueue', 'readwrite');
            tx.objectStore('offlineQueue').add(event.data.request);
        });
    }
});
