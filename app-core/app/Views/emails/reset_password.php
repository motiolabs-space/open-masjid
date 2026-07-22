<?php
/**
 * Template email reset password. Butuh $nama dan $link.
 * HTML email: gaya inline, tabel — supaya konsisten di berbagai klien email.
 */
?>
<div style="font-family:Arial,Helvetica,sans-serif;max-width:480px;margin:0 auto;padding:24px;color:#1f2937">
    <div style="text-align:center;margin-bottom:24px">
        <div style="font-size:20px;font-weight:bold;color:#065F46">Masj.id</div>
    </div>
    <h2 style="font-size:18px;color:#111827;margin:0 0 12px">Reset Password</h2>
    <p style="font-size:14px;line-height:1.6;margin:0 0 16px">
        Assalamu'alaikum <?= esc($nama) ?>,
    </p>
    <p style="font-size:14px;line-height:1.6;margin:0 0 20px">
        Kami menerima permintaan untuk mengatur ulang password akun Masj.id Anda.
        Klik tombol di bawah untuk membuat password baru. Tautan ini berlaku
        <strong>1 jam</strong> dan hanya bisa dipakai sekali.
    </p>
    <div style="text-align:center;margin:24px 0">
        <a href="<?= esc($link, 'attr') ?>"
           style="display:inline-block;background:#065F46;color:#ffffff;text-decoration:none;font-weight:bold;padding:12px 28px;border-radius:8px;font-size:14px">
            Atur Password Baru
        </a>
    </div>
    <p style="font-size:12px;line-height:1.6;color:#6b7280;margin:0 0 8px">
        Jika tombol tidak berfungsi, salin tautan ini ke browser Anda:<br>
        <a href="<?= esc($link, 'attr') ?>" style="color:#065F46;word-break:break-all"><?= esc($link) ?></a>
    </p>
    <p style="font-size:12px;line-height:1.6;color:#6b7280;margin:16px 0 0">
        Bila Anda tidak meminta reset password, abaikan email ini &mdash; password
        Anda tidak berubah.
    </p>
    <hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0">
    <p style="font-size:11px;color:#9ca3af;text-align:center;margin:0">
        Email otomatis dari Masj.id. Mohon tidak membalas email ini.
    </p>
</div>
