<?php
// Pemasangan PWA: manifest, warna tema, ikon layar utama, dan pendaftaran
// service worker. Disertakan di setiap layout <head>. base_url() dipakai agar
// benar di lokal (/masjid/) maupun produksi (/).
?>
<link rel="manifest" href="<?= base_url('manifest.webmanifest') ?>">
<meta name="theme-color" content="#065f46">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<link rel="apple-touch-icon" href="<?= asset_url('logo_masjid_200.png') ?>">
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('<?= base_url('sw.js') ?>').catch(function (e) {
                console.warn('SW gagal didaftarkan:', e);
            });
        });
    }
</script>
