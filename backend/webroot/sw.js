const CACHE_NAME = 'albyei-static-v1';

// Daftar asset statis minimal yang aman untuk dicache
const STATIC_ASSETS = [
    './manifest.json',
    './img/pwa-icon.svg'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cache => {
                    if (cache !== CACHE_NAME) {
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', event => {
    const request = event.request;
    const url = new URL(request.url);

    // Bypass API, Login, OTP, Session, dan semua selain GET
    if (request.method !== 'GET' || 
        url.pathname.match(/\/(api|users|transactions|otp|login|logout)/i)) {
        return; // Bypass ke network
    }

    // Strategi Stale-While-Revalidate untuk asset statis
    if (url.pathname.match(/\.(css|js|png|jpg|jpeg|svg|gif|woff2?|ico)$/i)) {
        event.respondWith(
            caches.match(request).then(cachedResponse => {
                const fetchPromise = fetch(request).then(networkResponse => {
                    if (networkResponse && networkResponse.ok) {
                        caches.open(CACHE_NAME).then(cache => {
                            cache.put(request, networkResponse.clone());
                        });
                    }
                    return networkResponse;
                }).catch(() => {
                    // Fail silently jika offline
                });
                
                return cachedResponse || fetchPromise;
            })
        );
        return;
    }

    // Network-only untuk request navigasi HTML/halaman dinamis yang tidak di-bypass
    if (request.mode === 'navigate' || request.headers.get('accept').includes('text/html')) {
        event.respondWith(
            fetch(request)
        );
        return;
    }
});
