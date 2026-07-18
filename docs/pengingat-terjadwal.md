# Pengingat Terjadwal (Broadcast)

Pengingat otomatis ke grup jamaah: **jadwal sholat harian** dan **laporan kas**.
Diatur pengurus lewat **Dashboard › Broadcast › Pengingat**, dikirim oleh sebuah
perintah yang dipanggil **cron** di server.

> **PENTING:** tanpa cron, pengingat tidak akan pernah terkirim. Pengaturan di
> dashboard hanya menentukan *kapan* dan *ke grup mana* — yang benar-benar
> mengirim adalah cron di bawah ini.

---

## Perintah

```bash
php spark broadcast:reminders          # kirim yang jatuh tempo sekarang
php spark broadcast:reminders --dry    # pratinjau tanpa mengirim (untuk uji)
```

Aman dipanggil sesering apa pun. Setiap pengingat hanya terkirim **sekali per
hari** berkat penanda `last_sent_at`. Bila cron terlewat menit persisnya,
pengingat tetap terkirim pada pemanggilan berikutnya di hari yang sama
(desain "susulan" — lebih baik telat daripada hilang). Bila pengiriman gagal
(mis. bot dikeluarkan dari grup), pengingat TIDAK ditandai terkirim sehingga
dicoba lagi di pemanggilan berikutnya.

## Pasang cron (di server)

Jalankan tiap 5 menit. Zona waktu jam kirim mengikuti **zona waktu masing-masing
masjid** (diatur di Pengaturan Masjid), bukan zona server — perintah menangani
konversinya sendiri, jadi cron cukup jalan sesering mungkin.

```cron
*/5 * * * * cd /home/customer/www/masj.id/app-core && /usr/bin/php spark broadcast:reminders >> writable/logs/reminders.log 2>&1
```

Sesuaikan path `php` dan direktori sesuai server. Di cPanel/SiteGround, tambahkan
lewat menu **Cron Jobs**.

## Verifikasi setelah pasang

```bash
cd app-core && php spark broadcast:reminders --dry
```

Keluaran menampilkan pengingat yang jatuh tempo beserta isi pesannya, tanpa
benar-benar mengirim. Bila kosong padahal ada pengingat aktif, periksa:

- Jam kirim sudah lewat hari ini? (belum tiba → dilewati)
- Frekuensi cocok dengan hari ini? (mingguan/bulanan hanya di hari yang cocok)
- Grup tujuan masih **aktif**?
- Untuk jadwal sholat: koordinat masjid sudah diisi? (kosong → jadwal tak ada,
  pengingat dilewati sampai koordinat diisi)

## Catatan

- Pengingat **jadwal sholat** memakai jadwal yang sama dengan Display TV
  (App\Libraries\PrayerTimes → AlAdhan), termasuk koreksi menit pengurus.
- Pengingat **laporan kas** meringkas pemasukan/pengeluaran/saldo bulan berjalan.
- Menamb/menghapus pengingat dibatasi **Admin Masjid**; pengurus biasa hanya
  melihat.
