<?php
/**
 * Email konfirmasi alamat, dikirim saat pengguna meminta kirim ulang.
 * Butuh $nama dan $urlMulai.
 *
 * Lebih pendek daripada email sambutan: penerimanya sudah tahu Masj.id itu apa
 * dan sedang menunggu satu hal saja — tautannya.
 */
?>
<div style="font-family:Arial,Helvetica,sans-serif;max-width:480px;margin:0 auto;padding:24px;color:#1f2937">
    <div style="text-align:center;margin-bottom:24px">
        <div style="font-size:20px;font-weight:bold;color:#065F46">Masj.id</div>
    </div>

    <h2 style="font-size:18px;color:#111827;margin:0 0 12px">Konfirmasi alamat email</h2>

    <p style="font-size:14px;line-height:1.6;margin:0 0 16px">
        Assalamu'alaikum <?= esc($nama) ?>,
    </p>

    <p style="font-size:14px;line-height:1.6;margin:0 0 20px">
        Klik tombol di bawah untuk mengesahkan alamat email ini. Gunanya satu:
        bila suatu saat Anda lupa kata sandi, kami punya cara yang pasti sampai
        untuk memulihkan akses Anda.
    </p>

    <div style="text-align:center;margin:24px 0">
        <a href="<?= esc($urlMulai, 'attr') ?>"
           style="display:inline-block;background:#065F46;color:#ffffff;text-decoration:none;font-weight:bold;padding:12px 28px;border-radius:8px;font-size:14px">
            Konfirmasi Email Saya
        </a>
    </div>

    <p style="font-size:12px;line-height:1.6;color:#6b7280;margin:0 0 8px">
        Jika tombol tidak berfungsi, salin tautan ini ke browser Anda:<br>
        <a href="<?= esc($urlMulai, 'attr') ?>" style="color:#065F46;word-break:break-all"><?= esc($urlMulai) ?></a>
    </p>

    <p style="font-size:12px;line-height:1.6;color:#6b7280;margin:16px 0 0">
        Tautan ini berlaku 7 hari. Bila Anda tidak meminta apa pun, abaikan saja
        email ini &mdash; tidak ada yang berubah pada akun Anda.
    </p>

    <hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0">
    <p style="font-size:11px;color:#9ca3af;text-align:center;margin:0">
        Email otomatis dari Masj.id.
    </p>
</div>
