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
| **Retensi & kohort** | `users.last_login` hanya menyimpan login **terakhir**; tak ada histori | Tak bisa menjawab "masjid yang daftar bulan X, berapa yang masih aktif bulan X+3" — pertanyaan paling menentukan bagi produk berbasis langganan/adopsi |
| **Sumber akuisisi** | Pendaftaran hanya menyimpan `register_ip` & `register_country`; tak ada UTM, referrer, atau kanal | Tak bisa tahu kanal mana yang berhasil, jadi anggaran/tenaga tak bisa dialihkan berdasarkan bukti |
| **Funnel aktivasi** | Tak ada pencatatan peristiwa (event) | Tak bisa tahu di langkah mana masjid berhenti: daftar → isi profil → input transaksi pertama → publikasi laporan pertama |
| **DAU/MAU sebagai tren** | Sama seperti retensi — hanya posisi terkini | Grafik pertumbuhan keterlibatan tak bisa dibuat tanpa mengarang |
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

Bila ingin keputusan GTM berbasis bukti, ini prasyaratnya. Diurutkan menurut
rasio manfaat terhadap usaha:

1. **Tabel peristiwa login** (`user_id`, `logged_at`) — membuka retensi, kohort,
   dan tren DAU/MAU sekaligus. Perubahan paling kecil dengan hasil terbesar.
2. **Kanal akuisisi saat pendaftaran** — simpan UTM/referrer pada pendaftaran
   masjid. Satu kolom, tapi tanpanya kanal tak akan pernah bisa dinilai.
3. **Penanda aktivasi per masjid** — kapan transaksi pertama dicatat, kapan
   laporan pertama diterbitkan.
4. **Segmen masjid** — satu kolom tipe (kampung/perumahan/kampus/korporat/
   yayasan), diisi saat pendaftaran.

Ketiga hal pertama tidak mengubah tampilan apa pun bagi pengurus masjid; semuanya
di sisi data.

---

## Bahan yang masih kurang untuk diskusi

Perlu diisi orang, bukan bisa disimpulkan dari kode:

- Jumlah masjid & DKM yang jadi target realistis di wilayah awal.
- Anggaran dan tenaga yang tersedia.
- Apakah sudah ada masjid percontohan yang bersedia jadi rujukan.
- Batasan hukum/kelembagaan soal penghimpunan dana (izin PUB, aturan zakat).
- Apakah ada kompetitor yang sudah masuk ke wilayah sasaran.
