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

---

## Masjid yang disuspensi

`masjid.status` bernilai `active` atau `suspended`. Saat disuspensi:

| Tetap jalan | Ditutup |
|---|---|
| Profil, program, berita, laporan, kalkulator zakat — semuanya tetap 200 | Formulir donasi (`Donation::create`) |
| Spanduk penjelasan muncul di seluruh halaman publik masjid itu | Pemrosesan donasi (`Donation::store`) |
| Kwitansi donasi lama tetap bisa dibuka | Pendaftaran donasi rutin (`Home::simpanDonasiRutin`) |

Alasannya: tautan yang sudah tersebar tidak boleh mati dan laporan yang sudah
terbit tetap harus bisa dipertanggungjawabkan. Yang dihentikan hanya penerimaan
dana — satu-satunya hal yang sulit dibatalkan bila suspensinya ternyata memang
karena ada masalah.

Aturannya ditafsirkan di satu tempat saja, helper `masjid_aktif()`. Tombol yang
disembunyikan di tampilan hanyalah kerapian; **penjaganya ada di controller**,
sebab `masjid_id` datang dari formulir dan bisa dikirim langsung.


## Callback pembayaran

`POST payment/callback` **dikecualikan dari CSRF** — memang harus, sebab yang
memanggilnya adalah server payment gateway, bukan browser jamaah. Konsekuensinya
setiap jalur di dalamnya wajib punya pembuktian sendiri:

- **Multipay**: header `X-Api-Signature` + `X-Api-Timestamp` diverifikasi HMAC
  terhadap `multipay_secret_key` milik masjid yang bersangkutan. Gagal → 401.
- **Simulasi** (POST formulir tanpa tanda tangan): **hanya hidup di luar
  produksi**. Ini alat bantu pengembangan; `payment_mode` hanya mengenal
  `manual` dan `multipay`, dan tak ada halaman yang menautkannya.

Jangan pernah menghidupkan kembali jalur tanpa tanda tangan di produksi: tanpa
CSRF dan tanpa login, nomor invoice saja sudah cukup untuk menandai donasi lunas
— lengkap dengan masuk buku kas dan terbitnya kwitansi sah.
