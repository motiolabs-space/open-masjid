# Analisis Produk & Roadmap — Masj.id

_Audit modul, benchmark platform sejenis, dan usulan fitur. Disusun Sep 2026._

Fokus arah yang diminta: **berangkat dari transparansi (atau kemudahan) laporan
keuangan → naik ke program yang berdampak bagi masyarakat sekitar dan luas.**

---

## 1. Inventaris Modul — sudah vs belum

### ✅ Sudah dikerjakan

| Domain | Modul |
|--------|-------|
| **Autentikasi** | Daftar masjid, daftar jamaah, login, Google OAuth, lupa/reset sandi, pilih masjid (multi-tenant) |
| **Keuangan** | Kategori, transaksi masuk/keluar, saldo, impor CSV mutasi bank + pemetaan, **FinanceAI** (input bahasa natural), **Virtual Auditor** (audit AI) |
| **Transparansi & Laporan** | Halaman laporan transparansi publik, generator laporan keuangan/program/inventaris, **AI Report Generator** |
| **Program Kebaikan** | Kategori, CRUD, halaman publik + detail program |
| **Donasi** | Alur donasi, Payment (Midtrans), pembayaran manual, setelan pembayaran, simulasi |
| **Distribusi/Penyaluran** | Mustahik, distribusi CRUD |
| **Berita** | Kategori, CRUD, galeri |
| **Jamaah/Warga** | Warga CRUD, dashboard jamaah, followers, subscribers |
| **Pengurus & Peran** | Pengurus CRUD, **pembedaan admin vs pengurus** |
| **Jadwal Sholat** | CRUD jadwal, koreksi menit, running text (papan digital) |
| **Inventaris** | Aset masjid CRUD |
| **Relawan** | Volunteers |
| **Broadcast (5 tahap)** | Grup Telegram/WhatsApp, pengingat terjadwal, pengumuman AI, ringkas obrolan grup |
| **LMS/Pelatihan** | Modul & materi (disusun superadmin) |
| **API & MCP** | REST baca+tulis, 6 tool MCP, **audit log**, panduan, generate token |
| **Superadmin** | Dashboard, kelola masjid/user, monitoring program, LMS, pemakaian AI, **Laporan GTM** |
| **Email** | Reset sandi |

### ⛔ Belum ada / parsial (peluang)

| Prioritas | Item | Catatan |
|-----------|------|---------|
| 🔴 | **Email selamat datang, verifikasi registrasi, laporan rutin mingguan** | Fondasi `Mailer` sudah ada; tinggal template + pemicu. (Sempat direncanakan, belum jadi.) |
| ✅ | ~~**Kwitansi donasi otomatis**~~ | Selesai Sep 2026 — lihat Tahap 1. |
| ✅ | ~~**Modul Zakat**~~ | Selesai Sep 2026 — kalkulator, jenis zakat pada donasi, 8 asnaf pada mustahik, laporan zakat terpisah. Lihat Tahap 2. |
| 🟠 | **Laporan Dampak program** | Penerima manfaat, sebelum/sesudah, bukti foto — mengubah "program" jadi "dampak". |
| 🟠 | **RSVP kegiatan + absensi** | Jamaah konfirmasi hadir; pengurus lihat perkiraan & kehadiran. |
| 🟢 | **PWA + Web Push** (adzan, pengumuman, program) | Installable, notifikasi tanpa app store. |
| ✅ | ~~**Dinding Transparansi**~~ | Selesai Sep 2026 — feed donasi + penyaluran & bukti di `/{username}/laporan`. |
| 🟢 | Kiosk donasi (perangkat fisik di masjid) | QRIS statis sudah ada; kiosk khusus opsional. |
| 🟢 | **Donasi rutin/terjadwal** (recurring infaq) | Pendapatan berulang & kebiasaan beramal. |
| 🟢 | **Sertifikat & poin relawan** | Retensi komunitas. |
| 🟢 | Live streaming kajian, multi-bahasa (EN/AR) | Jangkauan. |

---

## 2. Benchmark platform sejenis

Global (Masjidbox, MadinaApps, The Masjid App) dan Indonesia (eMasjid, Maslam,
Maskunting, DKM Digital) menempatkan **transparansi keuangan** sebagai pintu
masuk kepercayaan, lalu bertumpu pada **kemudahan donasi digital** dan
**keterlibatan komunitas**.

Fitur yang berulang muncul dan layak dibawa ke sini:

- **Kategori donasi terpisah + target + progress bar + laporan harian/mingguan**
  yang bisa dilihat jamaah langsung — "melihat jumlah masuk dan ke mana dana
  dipakai" adalah inti kepercayaan.
- **Kwitansi otomatis & real-time collection tracking.**
- **Kiosk/QRIS donasi** — menjembatani kotak amal fisik ke digital.
- **Manajemen relawan online**: daftar, peran, pengingat, pelacakan partisipasi.
- **Perencanaan kegiatan + pengumuman/notifikasi instan.**
- **Push notification + jadwal sholat/adzan**, papan digital (running text —
  _sudah ada di sini_).
- **Analitik untuk pengurus** (_sebagian sudah: GTM & laporan AI_).
- **Manajemen zakat** sebagai modul tersendiri.

Sumber:
- [Masjidbox — Mosque Management Software: The Complete Guide](https://masjidbox.com/blog/mosque-management-software-the-complete-guide)
- [MadinaApps — Complete Guide to Masjid Management Software 2026](https://madinaapps.com/the-complete-guide-to-masjid-management-software-features-every-mosque-needs-in-2026/)
- [The Masjid App — Donation Management](https://themasjidapp.org/about/donations)
- [eMasjid.id — Fitur Aplikasi Keuangan Masjid](https://www.emasjid.id/blog/detail/423/fitur-aplikasi-keuangan-masjid)
- [Maslam Apps — Digitalisasi Masjid](https://maslam.id/daftar-artikel/digitalisasi-masjid-dengan-maslam-apps-solusi-modern-untuk-manajemen-masjid)
- [Maskunting — Aplikasi Keuangan Masjid](https://maskunting.com/artikel/aplikasi-keuangan-masjid/)

---

## 3. Roadmap berjenjang (sesuai arah: transparansi → dampak)

### Tahap 1 — Transparansi & Kepercayaan _(fondasi)_ — ✅ SELESAI Sep 2026
1. ✅ **Kwitansi donasi otomatis** — halaman `donation/kwitansi/{invoice}`,
   cetak/Simpan-PDF (tanpa pustaka), nominal + terbilang, ditautkan dari halaman
   sukses & pesan WhatsApp. Hanya donasi lunas terbit kwitansi sah.
2. ✅ **Dinding Transparansi publik** — di `/{username}/laporan`: _Donasi Terbaru_
   (feed masuk, anonim → "Hamba Allah") + _Penyaluran & Bukti_ (foto bukti dari
   `masjid_distributions`), menutup rantai _donasi → penyaluran → bukti_.
3. ✅ **Laporan bulanan** — pemilih bulan (per bulan berdiri s.d. kini) +
   "Seluruh Periode" di halaman transparansi; tombol Cetak = unduh PDF.
4. ✅ **QRIS statis per masjid** — _ternyata sudah ada_: unggah di Setelan
   Pembayaran (`masjid_payments.qris_image`) & tampil ke donatur di halaman
   pembayaran manual. Tidak perlu dibangun ulang.

### Tahap 2 — Kemudahan Beramal
5. ✅ **Modul Zakat** — SELESAI Sep 2026:
   - Kalkulator Zakat per-masjid (maal/penghasilan/fitrah) → alir ke donasi.
   - `zakat_type` pada donasi (memisahkan zakat dari infaq/donasi biasa).
   - `asnaf` (8 golongan) pada mustahik → mewarnai penyalurannya.
   - Laporan Zakat: terkumpul per jenis vs tersalur per asnaf (`dashboard/reports/zakat`).
6. **Kampanye donasi** target + progress (perluas modul program). _(belum)_
7. **Infaq rutin/terjadwal.** _(belum)_

### Tahap 3 — Program Berdampak & Komunitas
8. **Laporan Dampak program** — penerima manfaat, indikator, bukti foto,
   sebelum/sesudah.
9. **RSVP + absensi kegiatan.**
10. **Relawan**: rekrut → peran → poin partisipasi → sertifikat.
11. **PWA + Web Push** untuk pengumuman/adzan/program.

### Tahap 4 — Jangkauan Luas
12. **Discovery antar-masjid** (cari & donasi lintas masjid).
13. **Peta sebaran manfaat**, live streaming, multi-bahasa.

---

## 4. Catatan Keamanan _(yang belum sempurna)_

| Tingkat | Temuan | Tindakan |
|---------|--------|----------|
| 🔴 | **Deploy key SSH bocor** di riwayat publik (`f7923d2:github_deploy_key`) | Cabut di GitHub + `authorized_keys` server, terbitkan kunci baru. (di [go-live-checklist](go-live-checklist.md) A1) |
| 🔴 | **`CI_ENVIRONMENT = development` di server** | Set `production` — debug toolbar membocorkan path/query/konfigurasi. (checklist A2) |
| 🟠 | **Tidak ada rate limiting sama sekali** | Login rawan brute-force; endpoint tulis `api/v1/*` rawan penyalahgunaan (kini bisa hapus data). Pasang `Throttler` CI4 di login & per-token pada API tulis. |
| 🟠 | **Token API/MCP tanpa masa berlaku & tanpa alert** | Audit log sudah mencatat penolakan, tapi tak ada notifikasi saat lonjakan penolakan (indikasi token bocor). Tambah alert (Telegram) ke pengurus + opsi kedaluwarsa/rotasi token. |
| 🟢 | Sudah baik | IDOR/tenant scoping banyak diperbaiki, CSRF aktif, cek kepemilikan pada API tulis, MasjidWriter memusatkan aturan tenant. |

---

## 5. Halaman 404 — selesai

- **Framework 404** (`errors/html/error_404.php`): diganti dari bawaan CI4
  (polos, bahasa Inggris) menjadi halaman branded Masj.id — badge kubah, dwibahasa
  ramah, sadar mode gelap, tombol **Beranda** + **Dashboard Pengurus**, pesan
  debug hanya di non-produksi.
- Catatan: halaman publik untuk _slug masjid tak dikenal_ sudah punya versi
  branded tersendiri (`public/masjid_not_found.php`, "Masjid ini tidak ditemukan"
  + ajakan daftar) — dibiarkan, sudah baik.
