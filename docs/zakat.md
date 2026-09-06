# Modul Zakat

Memisahkan **zakat** dari infaq/sedekah biasa di sepanjang alurnya: sejak jamaah
menghitung, saat dana masuk, saat disalurkan, sampai laporannya.

Kenapa dipisah: zakat punya aturan penerima yang mengikat (8 asnaf) dan tidak
boleh bercampur dengan dana operasional. Laporan yang menggabungkan keduanya
tidak bisa dipakai mempertanggungjawabkan zakat.

---

## 1. Kalkulator Zakat — `/{username}/zakat`

Halaman publik per masjid (`Home::zakat`). Tiga jenis perhitungan:

| Jenis | Dasar hitung |
|-------|--------------|
| Maal (harta) | Harta tersimpan ≥ nisab & sudah haul → 2,5% |
| Penghasilan | Pendapatan dikurangi kebutuhan pokok → 2,5% |
| Fitrah | Jumlah jiwa × harga beras setempat |

Hasilnya mengalir langsung ke formulir donasi masjid itu dengan nominal terisi —
menghitung dan menunaikan jadi satu jalur, bukan dua.

## 2. Jenis zakat pada donasi

Kolom `masjid_donations.zakat_type` (VARCHAR 20, **nullable**):

- `maal`, `penghasilan`, `fitrah` → donasi ini adalah zakat.
- `NULL` → infaq/sedekah/donasi biasa.

Nullable disengaja: seluruh donasi yang tercatat sebelum modul ini ada tetap sah
dan otomatis terbaca sebagai non-zakat.

## 3. Asnaf pada mustahik

Kolom `masjid_mustahik.asnaf` (VARCHAR 20, **nullable**) — 8 golongan penerima
zakat, daftarnya di helper `daftar_asnaf()`:

`fakir`, `miskin`, `amil`, `muallaf`, `riqab`, `gharim`, `fisabilillah`,
`ibnu_sabil`.

Mustahik lama berisi `NULL` (belum diklasifikasi); pengurus mengisinya bertahap
tanpa memblokir apa pun.

## 4. Laporan Zakat — `dashboard/reports/zakat`

Terpisah dari laporan keuangan umum. Menyandingkan dua sisi:

- **Terkumpul** per jenis zakat (dari `zakat_type`).
- **Tersalur** per asnaf (dari `asnaf` pada mustahik yang menerima penyaluran).

Sehingga pertanyaan "zakat yang masuk sudah sampai ke golongan mana saja"
terjawab dalam satu halaman.

## Catatan

- Migrasi: `2026-09-05-100000_AddZakatFields` — hanya menambah dua kolom
  nullable, aman dijalankan pada basis data yang sudah berisi.
- Mustahik ber-`asnaf` kosong tidak hilang dari laporan penyaluran umum; ia
  hanya tak terhitung pada rincian per-asnaf sampai diklasifikasi.
