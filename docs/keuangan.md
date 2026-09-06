# Keuangan

Modul inti produk: pembukuan kas masjid, impor mutasi bank, dan tiga fitur AI
yang menempel padanya (kategorisasi, narasi laporan, deteksi kejanggalan).

> **Ada DUA jalur impor CSV yang berbeda** dan sering tertukar. Bacalah bagian
> [Dua jalur impor](#dua-jalur-impor--jangan-tertukar) sebelum memakai salah
> satunya.

---

## Dasar: kategori, transaksi, saldo

| Rute | Isi |
|------|-----|
| `dashboard/keuangan` | Daftar transaksi, saldo, ringkasan |
| `dashboard/keuangan/save` (POST) | Simpan transaksi |
| `dashboard/keuangan/delete` (POST, **admin masjid**) | Hapus transaksi |
| `dashboard/keuangan/category/save` (POST) | Simpan kategori |
| `dashboard/keuangan/category/delete` (POST, **admin masjid**) | Hapus kategori |
| `dashboard/reports/finance` | Laporan keuangan cetak |

Kategori punya `type` — `pemasukan` atau `pengeluaran` — dan itu **mengikat**:
kategori pemasukan tidak sah menampung pengeluaran. Setiap kategori juga punya
`slug` unik per masjid, dipakai untuk mencari-atau-membuat kategori otomatis.

Menghapus transaksi dan kategori dibatasi **Admin Masjid**; pengurus biasa hanya
mencatat dan mengubah.

---

## Dua jalur impor — jangan tertukar

Keduanya sama-sama menerima CSV, tetapi **format masukan, tujuan AI, dan
hasilnya berbeda**.

| | **A. Mutasi Bank** | **B. Import CSV (AI)** |
|---|---|---|
| Rute | `dashboard/keuangan/mutasi` | `dashboard/keuangan/import-csv` |
| Controller | `Admin::mutasi` / `uploadMutasi` / `mapMutasi` | `FinanceAI::importCSV` / `processCSV` / `reviewCSV` / `saveCSV` |
| Format | Sesuai bank: **BCA**, **Mandiri**, **BSI**, atau generic | Tetap: `Tanggal, Deskripsi, Jumlah, Tipe` |
| AI memetakan ke | **Program** | **Kategori** |
| Kategori transaksi | Otomatis: "Donasi Terikat Program" / "Pengeluaran Program" | Dipilih/diusulkan per baris |
| Data antara | Tak ada — langsung ke halaman pemetaan | Draf di tabel `csv_import_drafts` |

Pilih **A** bila Anda mengunduh mutasi langsung dari internet banking dan ingin
menautkan dana ke program. Pilih **B** bila Anda punya catatan sendiri dan ingin
merapikannya ke kategori kas.

### Jalur A — format per bank

`App\Libraries\BankMutationParser` memetakan kolom sesuai bank yang dipilih:

| Bank | Susunan kolom yang diharapkan |
|------|-------------------------------|
| `bca` | Tgl, Keterangan, Cabang, **Nominal**, **Tipe (CR/DB)** |
| `mandiri`, `bsi` | Date, Description, Ref, **Debit**, **Credit** |
| generic | Tanggal, Deskripsi, **Nominal bertanda** — minus berarti uang keluar |

Nominal selalu disimpan **positif**; arah uang ditentukan kolom CR/DB (atau
tanda minus pada mode generic).

Tanggal dibakukan ke `Y-m-d` **satu kali di parser**, supaya tampilan dan
penyimpanan tak perlu lagi menebak urutan hari/bulan. Baris yang tanggalnya tak
terbaca **dibuang**, bukan disimpan apa adanya — transaksi bertanggal ngawur
yang menyelinap ke buku kas lebih berbahaya daripada baris yang hilang.

Saat memetakan (`mapMutasi`), `program_id` dari formulir **divalidasi milik
masjid sendiri**; tanpa itu transaksi bisa ditempelkan ke program masjid lain.

### Jalur B — draf, review, simpan

1. **Unggah** → CSV dibaca, lalu dikirim ke AI bersama daftar kategori masjid.
   AI mengembalikan `category_id` atau usulan nama kategori baru per baris.
2. **Draf disimpan ke basis data**, bukan ke session. Data impor yang besar
   membuat berkas session membengkak dan gagal ditulis di sebagian hosting —
   gejalanya "error session" saat mengunggah. Id draf dibawa lewat URL.
3. **Review** (`review-csv/{draftId}`) — pengurus memeriksa dan mengoreksi
   sebelum apa pun masuk buku kas. Draf hanya bisa dibuka bila milik masjid
   **dan** pengguna itu, jadi id draf orang lain tak bisa ditebak.
4. **Simpan** — `category_id` dari formulir diterima hanya bila kategorinya
   milik masjid ini. Bila kosong tapi ada usulan nama, kategori baru dibuat
   (atau dipakai ulang bila slug-nya sudah ada). Baris yang tanggalnya tak
   terbaca dilewati, dan jumlahnya dilaporkan di pesan sukses.

Draf yang menganggur dipangkas setelah **6 jam** oleh cron `broadcast:reminders`
(numpang cron yang sama, tak perlu cron kedua).

> **Bila AI tak terjangkau**, impor tetap berjalan — seluruh baris masuk dengan
> kategori kosong dan pengurus memilih sendiri. Ini dikatakan apa adanya lewat
> pesan galat, sebab diamnya menyesatkan: pengurus melihat semua kategori
> berisi "Lainnya" dan mengira fiturnya rusak.

---

## Fitur AI

Ketiganya lewat `App\Libraries\SumoPodAI` — lihat
[model-ai-sumopod.md](model-ai-sumopod.md) untuk konfigurasi model & tingkatnya.

### 1. Kategorisasi otomatis
Dijelaskan di jalur A dan B di atas. Tingkat model **`ringan`** (`csv_kategori`)
— tugas rutin bervolume tinggi, `temperature` 0.1.

### 2. Generator narasi laporan — `dashboard/keuangan/report`
Menyusun draf teks laporan bulanan siap kirim ke WhatsApp Broadcast: total
pemasukan, pengeluaran, saldo, dan jumlah program aktif bulan berjalan, dengan
persona "Sekretaris Masjid". Tingkat model **`berat`** (`laporan`) — hasilnya
dibaca seluruh jamaah, jadi harus rapi.

Bila AI tak terjangkau, balasannya **503 dengan pesan jelas**, bukan sukses
berisi kotak kosong.

### 3. Virtual Auditor — `dashboard/auditor`
Membandingkan pengeluaran per kategori bulan yang dipilih terhadap **rata-rata 3
bulan sebelumnya**, lalu meminta AI menandai yang janggal (mis. listrik melonjak
dari Rp1 juta ke Rp10 juta karena salah ketik). Tingkat model **`berat`**.

Bulan tanpa pengeluaran dijawab apa adanya ("tidak ada pengeluaran untuk
diaudit"), bukan dipaksa menghasilkan temuan.

---

## Catatan

- Seluruh rute di atas berada di bawah `dashboard/*` (`dashboardGuard`), dan
  setiap operasi memakai `masjid_id` dari session — tak ada endpoint yang
  menerima `masjid_id` dari pemanggil.
- `parse_rupiah()` dan `parse_tanggal()` di `custom_helper` adalah satu-satunya
  tempat format angka & tanggal Indonesia ditafsirkan; jangan menafsir ulang di
  tempat lain.
- Angka yang tampil di laporan publik berasal dari tabel yang sama
  (`masjid_finance_transactions`), jadi kesalahan input langsung terlihat
  jamaah — itulah alasan Virtual Auditor ada di depan tombol publikasi.
