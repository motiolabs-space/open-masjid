<?php
/**
 * Email sambutan untuk masjid yang baru mendaftar.
 * Butuh $nama (PIC), $namaMasjid, $urlMasjid, $urlDashboard, $urlContoh.
 *
 * TUGASNYA BUKAN MENYAPA, MELAINKAN MEMANCING LANGKAH PERTAMA.
 * Data aktivasi menunjukkan mayoritas masjid mendaftar lalu tak pernah mengisi
 * apa pun. Email yang hanya berisi "selamat datang" tidak mengubah itu. Karena
 * itu susunannya: kabar konkret yang bisa langsung diperiksa (halamannya sudah
 * hidup), SATU ajakan utama, lalu tiga langkah pendek — bukan daftar fitur.
 *
 * HTML email: gaya inline, tabel — supaya konsisten di berbagai klien email.
 */
?>
<div style="font-family:Arial,Helvetica,sans-serif;max-width:480px;margin:0 auto;padding:24px;color:#1f2937">
    <div style="text-align:center;margin-bottom:24px">
        <div style="font-size:20px;font-weight:bold;color:#065F46">Masj.id</div>
    </div>

    <h2 style="font-size:18px;color:#111827;margin:0 0 12px">Halaman <?= esc($namaMasjid) ?> sudah bisa dibuka</h2>

    <p style="font-size:14px;line-height:1.6;margin:0 0 16px">
        Assalamu'alaikum <?= esc($nama) ?>,
    </p>

    <p style="font-size:14px;line-height:1.6;margin:0 0 16px">
        Pendaftaran <strong><?= esc($namaMasjid) ?></strong> sudah selesai. Halaman
        publiknya aktif sejak sekarang dan bisa dibagikan ke jamaah:
    </p>

    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:12px;margin:0 0 20px;text-align:center">
        <a href="<?= esc($urlMasjid, 'attr') ?>" style="color:#065F46;font-weight:bold;font-size:14px;text-decoration:none;word-break:break-all">
            <?= esc($urlMasjid) ?>
        </a>
    </div>

    <p style="font-size:14px;line-height:1.6;margin:0 0 20px">
        Halaman itu masih kosong. Satu hal kecil di bawah sudah cukup membuatnya
        layak dibagikan &mdash; tidak perlu langsung lengkap.
    </p>

    <div style="text-align:center;margin:24px 0">
        <a href="<?= esc($urlDashboard, 'attr') ?>"
           style="display:inline-block;background:#065F46;color:#ffffff;text-decoration:none;font-weight:bold;padding:12px 28px;border-radius:8px;font-size:14px">
            Mulai Isi Data Masjid
        </a>
    </div>

    <p style="font-size:13px;font-weight:bold;color:#111827;margin:24px 0 8px">Tiga langkah pertama</p>
    <ol style="font-size:13px;line-height:1.7;color:#374151;margin:0 0 20px;padding-left:20px">
        <li><strong>Lengkapi profil</strong> &mdash; foto, alamat, dan jadwal. Lima menit.</li>
        <li><strong>Catat pemasukan bulan ini</strong> &mdash; cukup satu baris untuk memulai.</li>
        <li><strong>Bagikan halamannya</strong> ke grup jamaah.</li>
    </ol>

    <p style="font-size:13px;line-height:1.6;color:#374151;margin:0 0 20px">
        Ingin melihat wujudnya bila sudah terisi? Kami menyiapkan contohnya di
        <a href="<?= esc($urlContoh, 'attr') ?>" style="color:#065F46"><?= esc($urlContoh) ?></a>
        &mdash; seluruh angka di sana hanya peraga.
    </p>

    <hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0">
    <p style="font-size:12px;line-height:1.6;color:#6b7280;margin:0 0 8px">
        Ada yang membingungkan saat memulai? Balas email ini &mdash; kami baca satu per satu.
    </p>
    <p style="font-size:11px;color:#9ca3af;text-align:center;margin:16px 0 0">
        Anda menerima email ini karena mendaftarkan masjid di Masj.id.
    </p>
</div>
