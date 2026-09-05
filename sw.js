/*
 * Service Worker Masj.id.
 *
 * Berada di web root agar cakupannya penuh: di lokal /masjid/, di produksi /.
 * Seluruh jalur internal diturunkan dari self.registration.scope supaya berkas
 * ini sama untuk kedua lingkungan tanpa diubah.
 *
 * Fungsi: (1) halaman offline saat navigasi gagal; (2) Web Push tanpa payload —
 * saat push tiba, SW mengambil isi notifikasi terbaru dari server lalu
 * menampilkannya (tak perlu enkripsi payload di sisi pengirim).
 */
const SCOPE = self.registration.scope;
const CACHE = 'masjid-shell-v1';
const OFFLINE_URL = SCOPE + 'offline.html';
const ICON = SCOPE + 'public/logo_masjid_200.png';

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE).then((c) => c.add(OFFLINE_URL)).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

// Navigasi: coba jaringan; bila gagal (offline), sajikan halaman offline.
self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;
    if (event.request.mode === 'navigate') {
        event.respondWith(fetch(event.request).catch(() => caches.match(OFFLINE_URL)));
    }
});

// Push tanpa payload: ambil isi terbaru dari server. Masjid konteks disimpan
// saat berlangganan (cache 'masjid-ctx'); bila tak ada, ambil terbaru global.
self.addEventListener('push', (event) => {
    event.waitUntil((async () => {
        let masjidId = '';
        try {
            const res = await caches.match(SCOPE + '__ctx__');
            if (res) masjidId = (await res.text()) || '';
        } catch (_) {}

        let data = { title: 'Masj.id', body: 'Ada pembaruan dari masjid Anda.', url: SCOPE };
        try {
            const r = await fetch(SCOPE + 'push/latest' + (masjidId ? ('?masjid=' + encodeURIComponent(masjidId)) : ''), { credentials: 'omit' });
            if (r.ok) {
                const j = await r.json();
                if (j && j.title) data = j;
            }
        } catch (_) {}
        // Payload langsung (bila kelak pengirim menyertakannya) menang.
        try { if (event.data) { const p = event.data.json(); if (p && p.title) data = p; } } catch (_) {}

        await self.registration.showNotification(data.title, {
            body: data.body || '',
            icon: ICON,
            badge: ICON,
            data: { url: data.url || SCOPE },
        });
    })());
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = (event.notification.data && event.notification.data.url) || SCOPE;
    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((list) => {
            for (const c of list) {
                if (c.url === url && 'focus' in c) return c.focus();
            }
            return self.clients.openWindow(url);
        })
    );
});
