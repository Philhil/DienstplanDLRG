'use strict';

const CACHE_VERSION = 'v1';
const CACHE_NAME = `dienstplan-${CACHE_VERSION}`;

const PRECACHE_ASSETS = [
    '/css/style.css',
    '/css/dienstplan.css',
    '/css/themes/all-themes.css',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_ASSETS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) =>
            Promise.all(
                cacheNames
                    .filter((name) => name.startsWith('dienstplan-') && name !== CACHE_NAME)
                    .map((name) => caches.delete(name))
            )
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);

    // Only handle requests to our own origin
    if (url.origin !== self.location.origin) return;

    const isHtmlRequest = event.request.headers.get('accept')?.includes('text/html');

    if (isHtmlRequest) {
        // Network-first for HTML: always try to load fresh content
        event.respondWith(
            fetch(event.request).catch(() => caches.match(event.request))
        );
        return;
    }

    // Cache-first for static assets (CSS, JS, images, fonts)
    event.respondWith(
        caches.match(event.request).then((cached) => {
            if (cached) return cached;

            return fetch(event.request).then((response) => {
                if (!response.ok) return response;

                const clone = response.clone();
                caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                return response;
            });
        })
    );
});
