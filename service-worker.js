self.addEventListener('install', event => {
    event.waitUntil(
        caches.open('testsit-v1').then(cache => {
            return cache.addAll([
                '/index.html',
                '/accessories.html',
                '/vendor-armoon.html',
                '/styles.css',
                '/assets/images/logo-testsit.png'
            ]);
        })
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request).then(response => {
            return response || fetch(event.request);
        })
    );
});
