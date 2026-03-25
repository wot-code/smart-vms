/**
 * Smart VMS — Service Worker
 * Provides offline resilience for the visitor registration page.
 * Strategy: Cache-first for static assets; network-first for pages.
 */

const CACHE_NAME    = 'smart-vms-v1';
const OFFLINE_URL   = '/offline.html';
const CHECKIN_URL   = '/checkin';

// Assets to pre-cache on install
const PRECACHE_ASSETS = [
    OFFLINE_URL,
    CHECKIN_URL,
    'https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js',
    'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap',
];

// ─── INSTALL ─────────────────────────────────────────────────────────────────
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            // Cache what we can; ignore failures (e.g. CDN blocked)
            return Promise.allSettled(
                PRECACHE_ASSETS.map(url => cache.add(url).catch(() => null))
            );
        }).then(() => self.skipWaiting())
    );
});

// ─── ACTIVATE ────────────────────────────────────────────────────────────────
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
            )
        ).then(() => self.clients.claim())
    );
});

// ─── FETCH ───────────────────────────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET and non-same-origin requests (except allowed CDNs)
    if (request.method !== 'GET') return;

    // For the check-in page: network-first, fall back to cache, then offline.html
    if (url.pathname === CHECKIN_URL || url.pathname === '/') {
        event.respondWith(
            fetch(request)
                .then(response => {
                    // Cache a fresh copy
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(c => c.put(request, clone));
                    return response;
                })
                .catch(async () => {
                    const cached = await caches.match(request);
                    if (cached) return cached;
                    return caches.match(OFFLINE_URL);
                })
        );
        return;
    }

    // For static assets (JS, CSS, fonts): cache-first
    if (
        url.pathname.match(/\.(js|css|woff2?|ttf|png|ico|svg)$/) ||
        url.hostname.includes('jsdelivr') ||
        url.hostname.includes('fonts.googleapis') ||
        url.hostname.includes('fonts.gstatic')
    ) {
        event.respondWith(
            caches.match(request).then(cached => {
                if (cached) return cached;
                return fetch(request).then(response => {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(c => c.put(request, clone));
                    return response;
                }).catch(() => new Response('', { status: 404 }));
            })
        );
        return;
    }

    // Everything else: network-first
    event.respondWith(
        fetch(request).catch(() => caches.match(OFFLINE_URL))
    );
});

// ─── BACKGROUND SYNC ────────────────────────────────────────────────────────
self.addEventListener('sync', event => {
    if (event.tag === 'vms-offline-queue') {
        event.waitUntil(syncOfflineQueue());
    }
});

async function syncOfflineQueue() {
    // Open IndexedDB and flush queued records
    const db  = await openDB();
    const tx  = db.transaction('queue', 'readwrite');
    const store = tx.objectStore('queue');
    const all = await getAllFromStore(store);

    for (const record of all) {
        try {
            const response = await fetch('/visitor/offline-sync', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(record.data)
            });
            if (response.ok) {
                const delTx = db.transaction('queue', 'readwrite');
                delTx.objectStore('queue').delete(record.id);
            }
        } catch {
            // Will retry on next sync
        }
    }
}

// ─── IndexedDB HELPERS ───────────────────────────────────────────────────────
function openDB() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open('SmartVMS', 1);
        req.onupgradeneeded = e => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains('queue')) {
                db.createObjectStore('queue', { keyPath: 'id', autoIncrement: true });
            }
        };
        req.onsuccess = e => resolve(e.target.result);
        req.onerror   = e => reject(e.target.error);
    });
}

function getAllFromStore(store) {
    return new Promise((resolve, reject) => {
        const req = store.getAll();
        req.onsuccess = e => resolve(e.target.result);
        req.onerror   = e => reject(e.target.error);
    });
}
