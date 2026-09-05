<?php
// Tombol "Aktifkan Notifikasi" + langganan Web Push. Butuh $masjid.
// Bila kunci VAPID belum diatur di server, tombol tidak dirender sama sekali.
$vapidPub = env('vapid.publicKey');
if (! $vapidPub) {
    return;
}
?>
<button id="btnNotif" type="button"
        data-masjid="<?= (int) $masjid['id'] ?>"
        data-vapid="<?= esc($vapidPub, 'attr') ?>"
        data-scope="<?= esc(base_url('/'), 'attr') ?>"
        onclick="aktifkanNotifikasi()"
        class="inline-flex items-center gap-2 bg-white dark:bg-white/5 border border-primary text-primary px-6 py-3 rounded-xl font-bold hover:bg-primary hover:text-white transition-all">
    <span class="material-symbols-outlined">notifications_active</span>
    <span id="btnNotifText">Aktifkan Notifikasi</span>
</button>

<script>
function _b64ToUint8(base64) {
    const pad = '='.repeat((4 - base64.length % 4) % 4);
    const b64 = (base64 + pad).replace(/-/g, '+').replace(/_/g, '/');
    const raw = atob(b64);
    return Uint8Array.from([...raw].map(c => c.charCodeAt(0)));
}
async function aktifkanNotifikasi() {
    const btn = document.getElementById('btnNotif');
    const txt = document.getElementById('btnNotifText');
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        txt.innerText = 'Browser tak mendukung'; return;
    }
    try {
        const izin = await Notification.requestPermission();
        if (izin !== 'granted') { txt.innerText = 'Izin ditolak'; return; }

        const reg = await navigator.serviceWorker.ready;
        let sub = await reg.pushManager.getSubscription();
        if (!sub) {
            sub = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: _b64ToUint8(btn.dataset.vapid),
            });
        }

        // Simpan konteks masjid agar service worker tahu isi mana yang diambil.
        try { const c = await caches.open('masjid-ctx'); await c.put(btn.dataset.scope + '__ctx__', new Response(btn.dataset.masjid)); } catch (_) {}

        const res = await fetch(btn.dataset.scope + 'push/subscribe', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ masjid_id: parseInt(btn.dataset.masjid, 10), subscription: sub }),
        });
        txt.innerText = res.ok ? 'Notifikasi Aktif ✓' : 'Gagal, coba lagi';
        if (res.ok) btn.classList.add('bg-primary', 'text-white');
    } catch (e) {
        console.warn('Push subscribe gagal:', e);
        txt.innerText = 'Gagal mengaktifkan';
    }
}
</script>
