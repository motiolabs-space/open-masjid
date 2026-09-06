# Transparansi & Donasi

Rantai yang ingin ditutup: **donasi masuk → dipakai untuk apa → buktinya mana**.
Selama salah satu mata rantai tak terlihat jamaah, laporan hanya jadi klaim.

---

## Kwitansi donasi otomatis

`donation/kwitansi/{invoice}` — halaman kwitansi yang bisa dicetak atau disimpan
sebagai PDF lewat dialog cetak browser (tanpa pustaka PDF). Berisi nominal angka
**dan terbilang** (helper `terbilang()`).

Ditautkan dari halaman sukses donasi dan dari pesan WhatsApp yang dikirim ke
donatur. **Hanya donasi berstatus lunas** yang menerbitkan kwitansi sah.

## Dinding Transparansi — `/{username}/laporan`

Dua bagian yang saling melengkapi:

1. **Donasi Terbaru** — feed dana masuk. Donatur yang memilih anonim tampil
   sebagai "Hamba Allah".
2. **Penyaluran & Bukti** — penyaluran beserta foto buktinya dari
   `masjid_distributions`.

Bersama-sama keduanya menutup rantai donasi → penyaluran → bukti dalam satu
halaman publik, tanpa perlu jamaah bertanya.

## Laporan bulanan

Pemilih bulan (sejak bulan masjid berdiri sampai bulan berjalan) plus opsi
**Seluruh Periode** di halaman transparansi. Tombol Cetak menghasilkan PDF.

## QRIS statis

Sudah ada sejak sebelum modul ini: diunggah pengurus di **Setelan Pembayaran**
(`masjid_payments.qris_image`) dan tampil ke donatur di halaman pembayaran
manual. Tidak perlu dibangun ulang.

## Donasi rutin — `/{username}/donasi-rutin`

**Bukan auto-charge.** Pembayaran di sini tetap manual/QRIS, jadi yang bisa
dijanjikan sistem adalah **komitmen + pengingat**, bukan potongan otomatis.
Menyebutnya "langganan" akan menjanjikan sesuatu yang tak bisa ditepati.

Alurnya:

1. Donatur mengisi nama, WhatsApp, nominal, dan frekuensi (`mingguan` /
   `bulanan`) di halaman donasi rutin.
2. Baris masuk ke `masjid_recurring_pledges` dengan `next_reminder_date` = satu
   periode dari sekarang (donatur tetap bisa berdonasi hari ini secara terpisah;
   ini komitmen ke depan).
3. Perintah `broadcast:reminders` — **cron yang sama** dengan pengingat siaran,
   lihat [pengingat-terjadwal.md](pengingat-terjadwal.md) — mengirim pengingat
   WhatsApp berisi tautan donasi yang sudah terisi, lalu memajukan
   `next_reminder_date` satu periode.

> Tanpa cron, pengingat tidak akan pernah terkirim.

`program_id` pada komitmen divalidasi milik masjid yang sama, agar formulir tak
bisa menautkan komitmen ke program masjid lain.

Formulir ini publik tanpa login, jadi dibatasi **5 kiriman/menit per IP**.

Tabel `masjid_recurring_pledges`: `masjid_id`, `program_id`, `donor_name`,
`donor_phone`, `donor_email`, `amount`, `frequency`, `next_reminder_date`,
`active`, `last_reminded_at`.
