<?php
/**
 * Email sambutan untuk jamaah yang mendaftar sendiri.
 * Butuh $nama, $urlCari, $urlContoh.
 *
 * Lebih pendek daripada versi masjid: jamaah tidak punya pekerjaan menyiapkan
 * apa pun, jadi satu ajakan saja — menemukan masjidnya.
 */
?>
<div style="font-family:Arial,Helvetica,sans-serif;max-width:480px;margin:0 auto;padding:24px;color:#1f2937">
    <div style="text-align:center;margin-bottom:24px">
        <div style="font-size:20px;font-weight:bold;color:#065F46">Masj.id</div>
    </div>

    <h2 style="font-size:18px;color:#111827;margin:0 0 12px">Selamat bergabung</h2>

    <p style="font-size:14px;line-height:1.6;margin:0 0 16px">
        Assalamu'alaikum <?= esc($nama) ?>,
    </p>

    <p style="font-size:14px;line-height:1.6;margin:0 0 20px">
        Akun Anda sudah aktif. Dari sini Anda bisa mengikuti masjid, melihat
        laporan keuangannya secara terbuka, dan menyalurkan donasi kapan saja
        &mdash; tanpa perlu menunggu diumumkan.
    </p>

    <div style="text-align:center;margin:24px 0">
        <a href="<?= esc($urlMulai, 'attr') ?>"
           style="display:inline-block;background:#065F46;color:#ffffff;text-decoration:none;font-weight:bold;padding:12px 28px;border-radius:8px;font-size:14px">
            Konfirmasi &amp; Cari Masjid
        </a>
    </div>

    <p style="font-size:12px;line-height:1.6;color:#6b7280;margin:0 0 20px;text-align:center">
        Tombol di atas sekalian mengesahkan alamat email ini.
    </p>

    <p style="font-size:13px;line-height:1.6;color:#374151;margin:0 0 20px">
        Masjid Anda belum terdaftar? Tunjukkan
        <a href="<?= esc($urlContoh, 'attr') ?>" style="color:#065F46">halaman contoh ini</a>
        kepada pengurusnya &mdash; mendaftar gratis dan tidak menambah pekerjaan mereka.
    </p>

    <hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0">
    <p style="font-size:11px;color:#9ca3af;text-align:center;margin:0">
        Anda menerima email ini karena mendaftar di Masj.id.
    </p>
</div>
