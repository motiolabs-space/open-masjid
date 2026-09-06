# Relawan

Registry relawan masjid: siapa, perannya apa, aktif atau tidak, berapa poin
partisipasinya, dan sertifikat penghargaan yang bisa dicetak.

Menggantikan modul lama yang hanya menyaring warga ber-tag `#relawan` — tag tidak
menyimpan peran, tidak menyimpan riwayat, dan tidak bisa jadi dasar penghargaan.

---

## Halaman

| Rute | Isi |
|------|-----|
| `dashboard/relawan` | Daftar relawan urut poin + ringkasan (jumlah aktif, total poin) |
| `dashboard/relawan/save` (POST) | Rekrut / ubah data relawan |
| `dashboard/relawan/points` (POST) | Beri poin partisipasi |
| `dashboard/relawan/delete` (POST, admin masjid) | Hapus relawan + seluruh log poinnya |
| `dashboard/relawan/sertifikat/{id}` | Sertifikat cetak A4 landscape |

Semua berada di bawah `dashboard/*` sehingga sudah digerbang `dashboardGuard`
(login + konteks masjid), dan setiap operasi memeriksa kepemilikan masjid
sebelum menulis — `id` dari POST tidak pernah dipercaya begitu saja.

## Poin: log dulu, total menyusul

Pemberian poin **tidak** menambah angka di kolom `points` begitu saja. Urutannya:

1. Satu baris masuk ke `masjid_volunteer_points` (nilai + alasan + waktu).
2. Kolom `masjid_volunteers.points` **disegarkan dari `SUM` seluruh log**.

Alasannya: kolom `points` adalah cache, dan cache yang ditambah-tambah akan
menyimpang begitu ada log yang dikoreksi atau dihapus. Dengan menghitung ulang,
angka di daftar selalu sama dengan riwayatnya.

## Tabel

`masjid_volunteers`
: `masjid_id`, `warga_id` (opsional — relawan boleh bukan warga terdaftar),
  `name`, `phone`, `role`, `points` (cache), `status` (`active`/`inactive`),
  `joined_at`.

`masjid_volunteer_points`
: `masjid_id`, `volunteer_id`, `program_id` (opsional), `points`, `reason`.

## Sertifikat

Halaman cetak berdiri sendiri (A4 landscape) berisi nama, peran, total poin, dan
tanggal. Material Symbols ikut dimuat di halaman itu agar ikonnya tetap terender
meski di luar layout dashboard. Cetak/Simpan-PDF lewat dialog cetak browser —
tanpa pustaka PDF.

## Catatan

- Menghapus relawan ikut membersihkan log poinnya agar tak menggantung.
- Hanya **Admin Masjid** yang boleh menghapus; pengurus biasa bisa merekrut,
  mengubah, dan memberi poin.
