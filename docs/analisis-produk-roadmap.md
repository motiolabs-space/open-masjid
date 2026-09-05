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
6. ✅ **Kampanye donasi** target + progress — SELESAI Sep 2026: `target_donation`
   pada program + progress bar terkumpul/target/% di halaman detail DAN daftar
   program (dana terkumpul dihitung satu query di controller).
7. ✅ **Infaq rutin/terjadwal** — SELESAI Sep 2026: halaman Donasi Rutin
   (`/{username}/donasi-rutin`) sebagai **janji + pengingat** (bukan auto-charge,
   sebab bayar tetap manual/QRIS). Tabel `masjid_recurring_pledges`; command
   `broadcast:reminders` mengirim pengingat WA berisi tautan donasi terisi tiap
   periode lalu memajukan jadwal.

### Tahap 3 — Program Berdampak & Komunitas
8. ✅ **Laporan Dampak program** — SELESAI Sep 2026: jumlah penerima manfaat,
   cerita dampak, foto bukti (banyak) per program; diisi pengurus di
   `dashboard/program/dampak/{id}`, tampil di halaman publik program bila
   dipublikasikan. Kolom pada `masjid_programs` + tabel
   `masjid_program_impact_photos`.
9. ✅ **RSVP + absensi kegiatan** — SELESAI Sep 2026: jamaah konfirmasi kehadiran
   publik (nama/WA/jumlah orang) di halaman program (dedup per nomor, kuota
   ditegakkan); pengurus lihat daftar + ringkasan (pendaftar/tamu/hadir/absen) &
   tandai hadir di `dashboard/program/kehadiran/{id}`. Tabel `masjid_program_rsvps`.
10. ✅ **Relawan** — SELESAI Sep 2026: registry relawan (rekrut, peran, status),
    poin partisipasi (award + log ber-alasan; total relawan disegarkan dari
    jumlah log), dan sertifikat penghargaan cetak (A4 landscape). Menu Relawan
    baru menggantikan modul lama berbasis tag `#relawan`. Tabel
    `masjid_volunteers` + `masjid_volunteer_points`.
11. ✅ **PWA + Web Push** — SELESAI Sep 2026:
    - **PWA**: manifest (disajikan PHP agar adaptif lokal/produksi), service
      worker (`sw.js` di web root) dengan halaman offline, terdaftar di semua
      layout → aplikasi installable. Aset terverifikasi tersaji; registrasi SW
      jalan di browser asli (browser otomasi tak bisa mendaftarkan SW).
    - **Web Push self-hosted VAPID** (tanpa pihak ketiga, payloadless): library
      `WebPush` (ES256 JWT + kirim payloadless; crypto diverifikasi valid),
      command `php spark push:vapid`, langganan (`masjid_push_subscriptions`,
      idempoten per endpoint) via tombol "Aktifkan Notifikasi" di profil masjid,
      dan halaman **Notifikasi** dashboard untuk broadcast. SW mengambil isi
      terbaru (`push/latest`) saat "ketukan" tiba. **Pengiriman nyata perlu
      kunci VAPID di `.env` + uji di perangkat asli** (tak dapat diuji lokal).

### Tahap 4 — Jangkauan Luas
12. ✅ **Discovery antar-masjid** — SELESAI Sep 2026: direktori publik `/jelajah`
    (cari nama/kota, saring provinsi), peta lokasi (Leaflet/OpenStreetMap, hanya
    masjid berkoordinat), tiap kartu tautan Kunjungi + Donasi lintas masjid.
    Tautan "Jelajah Masjid" di navbar publik.
13. **Peta sebaran manfaat**, live streaming, multi-bahasa:
    - ✅ **Live streaming** — SELESAI Sep 2026: `stream_url` per program, pemutar
      YouTube tertanam di halaman publik (non-YouTube → tombol "Tonton Siaran").
    - ◐ **Peta sebaran manfaat** — sebagian: peta lokasi masjid sudah ada di
      Jelajah; peta khusus titik penyaluran menyusul (data koordinat masih tipis).
    - ⛔ **Multi-bahasa** — ditunda: i18n menyeluruh adalah pekerjaan besar dengan
      ROI rendah untuk produk fokus Indonesia. Dikerjakan bila ada kebutuhan
      jangkauan non-Indonesia.

---

## 4. Catatan Keamanan _(yang belum sempurna)_

| Tingkat | Temuan | Tindakan |
|---------|--------|----------|
| 🔴 | **Deploy key SSH bocor** di riwayat publik (`f7923d2:github_deploy_key`) | Cabut di GitHub + `authorized_keys` server, terbitkan kunci baru. (di [go-live-checklist](go-live-checklist.md) A1) |
| 🔴 | **`CI_ENVIRONMENT = development` di server** | Set `production` — debug toolbar membocorkan path/query/konfigurasi. (checklist A2) |
| 🟠 | **Rate limiting belum menyeluruh** | Form publik sudah dibatasi (lihat di bawah), tapi **login masih rawan brute-force** dan endpoint tulis `api/v1/*` belum dibatasi per-token. Pasang `Throttler` CI4 di keduanya. |
| 🟠 | **Token API/MCP tanpa masa berlaku & tanpa alert** | Audit log sudah mencatat penolakan, tapi tak ada notifikasi saat lonjakan penolakan (indikasi token bocor). Tambah alert (Telegram) ke pengurus + opsi kedaluwarsa/rotasi token. |
| 🟢 | Sudah baik | IDOR/tenant scoping banyak diperbaiki, CSRF aktif, cek kepemilikan pada API tulis, MasjidWriter memusatkan aturan tenant. |

### Sudah diperbaiki (Sep 2026)

Hasil review kode Tahap 3–4, seluruhnya diuji ulang di lingkungan lokal:

| Temuan | Perbaikan |
|--------|-----------|
| 🔴 **Stored XSS di peta Jelajah** — nama masjid (isian bebas saat pendaftaran) dirangkai sebagai string HTML ke `bindPopup`, dan `json_encode` polos di dalam `<script>` bisa diputus oleh nama berisi `</script>`. | Popup dibangun sebagai simpul DOM (`textContent`), `json_encode` memakai `JSON_HEX_*`. Diuji dengan muatan `</script><img src=x onerror=…>`: tak ada tag mentah yang lolos. |
| 🟠 **SSRF lewat `push/subscribe`** — endpoint langganan disimpan apa adanya lalu jadi tujuan `curl` saat broadcast; alamat internal (`127.0.0.1`, `169.254.169.254`) bisa didaftarkan siapa saja. | `WebPush::endpointSah()` — wajib `https` + allowlist host layanan push (FCM/Mozilla/Apple/WNS), diperiksa saat menyimpan **dan** saat mengirim. 13 kasus uji lulus, termasuk upaya `fcm.googleapis.com.evil.com`. |
| 🟠 **`stream_url` & `registration_link` tanpa validasi skema** — `esc($url, 'attr')` tidak menyaring `javascript:`, sehingga pengurus bisa menanam skrip di halaman publik program. | Helper `tautan_aman()` (hanya http/https), dipakai saat menyimpan (ditolak dengan pesan) **dan** saat merender (menjaga baris lama). |
| 🟠 **Form publik tanpa rate limit** — RSVP, donasi rutin, dan langganan push bisa dibanjiri skrip. | `Throttler` CI4 per alamat IP: 5/menit untuk RSVP & donasi rutin, 10/menit untuk langganan push. Diuji: 8 kiriman → 5 masuk, 3 ditolak. |
| 🟠 **Kuota RSVP bisa diborong** — `guests` tak dibatasi atas. | Dibatasi 50 orang per kiriman. Diuji: `guests=999999` tersimpan sebagai 50. |
| 🟢 **Hapus relawan lewat GET** | Dipindah ke POST + `csrf_field()`. |
| 🟢 **XSS atribut di dashboard relawan** — `json_encode` polos di dalam `onclick='…'`; nama ber-apostrof memutus atribut. | `JSON_HEX_APOS`/`HEX_QUOT`/`HEX_TAG`/`HEX_AMP`. |
| 🟢 **Escape ganda** pada pesan RSVP & poin relawan | `esc()` dilepas dari controller; view sudah meng-`esc()`. |
| 🟢 **Penanda peta di koordinat 0,0** — 3 dari 5 masjid berkoordinat kosong tampil di Teluk Guinea, memaksa `fitBounds()` menampilkan separuh dunia. | 0,0 dan koordinat di luar rentang diperlakukan sebagai "belum ada lokasi". |
| 🟢 **Jelajah memuat seluruh masjid dalam satu halaman** | Dipaginasi 24/halaman; penyaring `q`/`provinsi` dipertahankan saat berpindah halaman. |
| 🟢 **Penyaring provinsi memuat masjid non-aktif** | Kueri provinsi ikut dibatasi `status = active`. |

**Sisa yang diketahui:** dedup RSVP memakai nomor WA tanpa verifikasi, jadi orang
yang tahu nomor pendaftar lain masih bisa menimpa nama/jumlah tamunya. Menutup
ini butuh OTP WhatsApp — ditunda sampai ada kebutuhan nyata; sementara ini
pembatas laju menahan penyalahgunaan massal.

---

## 5. Halaman 404 — selesai

- **Framework 404** (`errors/html/error_404.php`): diganti dari bawaan CI4
  (polos, bahasa Inggris) menjadi halaman branded Masj.id — badge kubah, dwibahasa
  ramah, sadar mode gelap, tombol **Beranda** + **Dashboard Pengurus**, pesan
  debug hanya di non-produksi.
- Catatan: halaman publik untuk _slug masjid tak dikenal_ sudah punya versi
  branded tersendiri (`public/masjid_not_found.php`, "Masjid ini tidak ditemukan"
  + ajakan daftar) — dibiarkan, sudah baik.
