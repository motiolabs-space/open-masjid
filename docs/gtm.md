# Catatan GTM — bahan diskusi

**Status: draf pembuka, belum ada keputusan.** Dokumen ini menyiapkan bahannya
saja: apa yang sudah bisa diukur platform hari ini, apa yang **tidak** bisa,
pertanyaan yang perlu dijawab, dan apa yang harus dibangun agar jawabannya bisa
diukur — bukan sebuah rencana yang sudah jadi.

Semua angka pasar, harga, dan target di bawah sengaja **dikosongkan**. Mengisinya
dengan tebakan yang terlihat rapi justru berbahaya: angka karangan cenderung
dikutip ulang sebagai fakta di rapat berikutnya.

Konteks produk ada di [analisis-produk-roadmap.md](analisis-produk-roadmap.md) —
termasuk benchmark platform sejenis beserta sumbernya. Pelaksanaan di sisi
sosial media ada di [gtm-sosmed.md](gtm-sosmed.md); catat bahwa kerja di sana
**belum bisa dinilai dengan data** selama kanal akuisisi tidak dicatat (bagian 2
dan 5 di bawah).

---

## 1. Yang sudah bisa diukur hari ini

Halaman **Superadmin › Laporan GTM** (`/gtm`, `SuperAdmin::gtm`) menampilkan tren
bulanan untuk 6/12/24 bulan terakhir:

| Kartu | Sumber | Mode |
|---|---|---|
| Akuisisi Masjid | `masjid.created_at` | arus per bulan |
| Total Masjid | kumulatif | total berjalan |
| Akuisisi Pengguna | `users.created_at` | arus per bulan |
| Total Pengguna | kumulatif | total berjalan |
| Warga Terdaftar Baru | `masjid_warga.created_at` | arus per bulan |
| Program Baru | `masjid_programs.created_at` | arus per bulan |
| Donasi Masuk | `masjid_donations.paid_at`, status lunas | rupiah |
| Transaksi Keuangan | `masjid_finance_transactions.date` | arus per bulan |

Ditambah tiga **snapshot** tanpa garis tren: MAU (30 hari), DAU (24 jam), dan
Masjid Aktif (ada pengurus yang login dalam 30 hari).

Target tahunan diatur di **Superadmin › Pengaturan** (`platform_settings`:
`target_masjid`, `target_program`, `target_jamaah`, `target_mustahik`,
`target_donasi`) dan dibandingkan dengan capaian tahun berjalan di dashboard
superadmin.

## 2. Yang TIDAK bisa diukur — dan kenapa itu penting

Ini bagian terpenting dari catatan ini. Keputusan GTM yang bertumpu pada metrik
yang tak benar-benar ada akan salah arah tanpa ketahuan.

| Tak terukur | Sebabnya | Akibatnya bagi GTM |
|---|---|---|
| ~~**Retensi & kohort**~~ ✅ | **Selesai Sep 2026** — tabel `user_login_events` mencatat tiap login; retensi kohort masjid tampil di **Laporan GTM › Retensi Masjid**. | — |
| ~~**Sumber akuisisi**~~ ✅ | **Selesai Sep 2026** — UTM & rujukan dicatat saat pendaftaran, ditampilkan di **Laporan GTM › Kanal Akuisisi Masjid**. Lihat [gtm-sosmed.md](gtm-sosmed.md) untuk cara membuat tautannya. | Terjawab, **untuk pendaftar baru saja** — masjid yang sudah ada sebelum ini tampil "Tidak tercatat" |
| **Funnel aktivasi** | Tak ada pencatatan peristiwa (event) | Tak bisa tahu di langkah mana masjid berhenti: daftar → isi profil → input transaksi pertama → publikasi laporan pertama |
| ~~**DAU/MAU sebagai tren**~~ ✅ | **Selesai Sep 2026** — ikut terbuka oleh `user_login_events`; kartu "Pengguna Aktif Bulanan" kini bergaris tren. | — |
| **Segmen masjid** | Tak ada atribut ukuran/tipe (kampung, kampus, perumahan, korporat) | Tak bisa tahu segmen mana yang paling cepat mengadopsi |
| **Biaya & pendapatan** | Tak ada model harga di produk | Unit economics belum bisa dibicarakan sama sekali |

> Catatan jujur yang sudah tertulis di kode `SuperAdmin::gtm`: metrik tanpa
> stempel waktu asli sengaja **tidak** digambarkan sebagai tren. Prinsip itu
> layak dipertahankan saat menambah metrik baru.

## 3. Yang perlu dijawab dalam diskusi

Diurutkan dari yang paling menentukan keputusan berikutnya.

**a. Siapa penggunanya yang sebenarnya membayar/memutuskan?**
Bendahara DKM, ketua takmir, atau yayasan pengelola? Produk ini menyelesaikan
beban kerja **bendahara**, tapi yang memutuskan adopsi biasanya **ketua**.
Keduanya butuh argumen yang berbeda.

**b. Apa pintu masuknya?**
Arah roadmap sudah memilih **transparansi laporan keuangan** sebagai pintu masuk
kepercayaan, dan benchmark platform sejenis menunjuk arah yang sama. Yang belum
diputuskan: apakah pintu masuk itu juga jadi pesan pemasarannya, atau pesannya
justru "kurangi kerja manual bendahara".

**c. Model bisnisnya apa?**
Belum ada apa pun di produk. Opsi yang lazim di kategori ini — gratis dengan
donasi, langganan per masjid, gratis untuk masjid + berbayar untuk yayasan
multi-masjid, atau bagi hasil dari payment gateway. Masing-masing mengubah
prioritas fitur secara drastis, jadi ini keputusan paling awal yang perlu
diambil.

**d. Kanal akuisisinya apa?**
Kandidat yang terlihat dari produk & repo: jejaring masjid percontohan (roadmap
menyebut Jogokariyan untuk kurikulum LMS), komunitas developer muslim (repo
open-source), dan efek jaringan dari halaman publik masjid (setiap halaman
laporan yang dibagikan ke jamaah membawa merek platform). Belum satu pun diuji.

**e. Apa definisi "masjid teraktivasi"?**
Usulan untuk dibahas: masjid yang **menerbitkan laporan transparansi pertamanya**
— bukan sekadar mendaftar. Itu momen ketika manfaat produk benar-benar sampai ke
jamaah, dan ia bisa diukur dari data yang sudah ada.

## 4. Yang sudah jadi kekuatan (dari produk, bukan klaim)

- **Halaman publik per masjid** — tiap masjid punya profil, laporan transparansi,
  dan halaman donasi yang bisa dibagikan. Ini distribusi yang melekat pada
  produk, bukan biaya pemasaran terpisah.
- **Direktori `/jelajah`** — pengunjung satu masjid bisa menemukan masjid lain.
- **Rantai donasi → penyaluran → bukti** sudah tertutup, yang merupakan inti
  kepercayaan menurut benchmark.
- **Open source** — menurunkan hambatan untuk dicoba dan mengundang kontribusi.
- **Sudah dipakai data nyata** — bukan produk kosong.

## 5. Instrumentasi minimum agar GTM bisa diukur

Diurutkan menurut rasio manfaat terhadap usaha. Dua yang pertama sudah terpasang.

1. ✅ **Tabel peristiwa login** — `user_login_events`, terpasang Sep 2026.
   Membuka retensi kohort dan tren DAU/MAU sekaligus. Dipangkas otomatis setelah
   24 bulan oleh cron `broadcast:reminders`, jadi tidak tumbuh tanpa batas.
2. ✅ **Kanal akuisisi saat pendaftaran** — terpasang Sep 2026. Sentuhan
   **pertama** disimpan di cookie 90 hari lalu ikut tercatat saat mendaftar,
   sebab orang jarang mendaftar pada kunjungan yang sama saat ia pertama
   menemukan situs ini.
3. **Penanda aktivasi per masjid** — kapan transaksi pertama dicatat, kapan
   laporan pertama diterbitkan. **Belum dikerjakan.** Ini yang berikutnya paling
   berguna: retensi menjawab "masih dipakai atau tidak", aktivasi menjawab
   "sempat benar-benar dipakai atau tidak".
4. **Segmen masjid** — satu kolom tipe (kampung/perumahan/kampus/korporat/
   yayasan), diisi saat pendaftaran. **Belum dikerjakan.**

Tidak ada yang mengubah tampilan bagi pengurus masjid; semuanya di sisi data.

> **Keduanya baru mulai mencatat sejak dipasang.** Masjid yang mendaftar sebelum
> Sep 2026 tidak punya asal maupun riwayat keaktifan. Di laporan hal itu
> ditampilkan apa adanya sebagai **"—"** dan **"Tidak tercatat"** — bukan sebagai
> 0%. Membedakan "tidak aktif" dari "tidak terekam" itu penting: yang pertama
> masalah produk, yang kedua sekadar batas data. Angka retensi baru benar-benar
> bisa dibaca setelah beberapa bulan berjalan.

---

## Bahan yang masih kurang untuk diskusi

Perlu diisi orang, bukan bisa disimpulkan dari kode:

- Jumlah masjid & DKM yang jadi target realistis di wilayah awal.
- Anggaran dan tenaga yang tersedia.
- Apakah sudah ada masjid percontohan yang bersedia jadi rujukan.
- Batasan hukum/kelembagaan soal penghimpunan dana (izin PUB, aturan zakat).
- Apakah ada kompetitor yang sudah masuk ke wilayah sasaran.
