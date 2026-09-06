# Masjid Contoh (Demo)

Halaman peraga di **`/digital`** — sebuah masjid yang tidak benar-benar ada,
diisi konten contoh supaya calon pengurus bisa melihat wujud Masj.id sebelum
mendaftar, dan supaya materi pemasaran punya tautan hidup untuk ditunjuk.

---

## Menjalankan

```bash
cd app-core && php spark db:seed DemoMasjidSeeder
```

Mengosongkan kembali (profil masjidnya tetap ada, hanya kontennya yang hilang):

```bash
cd app-core && php spark db:seed DemoMasjidClearSeeder
```

**Aman dijalankan berulang kali.** Seeder selalu menghapus konten lama milik
masjid contoh lebih dulu, baru mengisi ulang — jadi tidak pernah menumpuk. Efek
sampingnya menyenangkan: tanggalnya ikut bergerak mengikuti hari saat dijalankan,
sehingga laporan bulan berjalan tidak pernah tampak basi. Jalankan ulang sesekali
untuk menyegarkan.

> **Batas tegas:** seeder hanya menyentuh baris yang `masjid_id`-nya milik masjid
> contoh. Masjid sungguhan tidak pernah tersentuh, bahkan bila seeder dijalankan
> di produksi.

## Prasyarat

Masjid dengan username `digital` harus sudah ada. Bila belum, seeder berhenti
dengan pesan dan tidak melakukan apa pun — daftarkan dulu lewat alur pendaftaran
biasa, baru jalankan seeder.

## Isinya

| Bagian | Isi |
|--------|-----|
| Profil | Nama, alamat, koordinat (Yogyakarta), visi & misi, logo, foto utama |
| Keuangan | 7 kategori + **42 transaksi** enam bulan terakhir (pemasukan & pengeluaran) |
| Program | 3 program: satu berkampanye dengan target, dua dengan laporan dampak |
| Donasi | 18 donasi lunas, sebagian anonim, sebagian tertaut ke program |
| Dampak | 3 foto bukti pada laporan dampak |
| Penyaluran | 3 penyaluran berbukti + 4 mustahik/warga penerima |
| Berita | 2 berita terbit |
| Jadwal | 4 jadwal Jumat ke depan (imam, khatib, muadzin) |

Angka transaksinya dibuat dari benih acak tetap, jadi tampilannya konsisten
setiap kali seeder dijalankan — bukan berubah-ubah acak.

## Soal kejujuran data

Halaman ini **publik** dan berisi angka keuangan serta nama donatur yang
seluruhnya fiktif. Karena produk ini menjual transparansi, status peraganya
dinyatakan terang-terangan, bukan disembunyikan:

- Nama resmi: **"Masjid Jami Digital (Contoh)"** — tampil di bawah nama pada hero.
- Tagline: "Halaman Contoh Masj.id — seluruh angka dan nama di halaman ini fiktif".
- Profil (`about_us`) menyatakan bahwa masjidnya tidak ada dan angkanya tidak
  boleh dijadikan rujukan.
- Nama donatur sengaja disingkat/anonim ("Hamba Allah", "Ibu R. W.") agar tidak
  menyerupai orang tertentu.

**Jangan menghapus penanda-penanda itu.** Laporan keuangan karangan yang tampak
sungguhan adalah hal terakhir yang boleh terbit dari platform transparansi.

## Gambar

Seeder memakai berkas yang **sudah ada di penyimpanan bersama** (S3/CDN), bukan
mengunggah berkas baru. Jalurnya disimpan relatif seperti kolom gambar lain,
sehingga `Storage::url()` menyusun alamatnya sendiri — sama benar di lokal maupun
produksi. Daftarnya ada di konstanta `GAMBAR`, `LOGO`, dan `HERO` pada seeder.

Bila salah satu berkas itu kelak dihapus dari CDN, gambarnya akan kosong tetapi
halamannya tetap jalan; ganti saja jalurnya di konstanta.

## Kaitannya dengan materi pemasaran

Halaman seri tulisan ada di **`/seri`** (lihat
[seri-konten-sosmed.md](seri-konten-sosmed.md)) dan menunjuk ke masjid contoh ini
sebagai versi hidup dari tiap tangkapan layar.

Rute `/seri` sengaja **tidak** memakai slug `digital` — slug itu milik masjid
contoh, dan catch-all `(:any)` di akhir daftar rute akan tertutup bila ada rute
statis dengan nama yang sama.
