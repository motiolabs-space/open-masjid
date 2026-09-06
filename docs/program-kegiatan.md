# Program & Kegiatan

Satu program menampung empat hal yang dulu terpisah: **kampanye donasi**,
**RSVP/absensi**, **laporan dampak**, dan **live streaming**. Semuanya opsional —
program tanpa satu pun tetap sah.

---

## 1. Kampanye donasi (target + progress)

Kolom `masjid_programs.target_donation`. Bila diisi, halaman detail **dan**
daftar program menampilkan progress bar terkumpul/target/persen.

Dana terkumpul pada halaman daftar dihitung **satu query di controller** untuk
seluruh program, bukan satu query per kartu.

## 2. RSVP + absensi kegiatan

**Sisi jamaah** — di halaman publik program, bila program itu **tidak** punya
tautan pendaftaran eksternal, muncul kartu "Konfirmasi Kehadiran": nama, nomor
WhatsApp, jumlah orang yang dibawa. Tanpa login.

**Sisi pengurus** — `dashboard/program/kehadiran/{id}`: daftar pendaftar +
ringkasan (pendaftar/tamu/hadir/absen), tautan `wa.me` ke tiap pendaftar, dan
tombol tandai **Hadir**/**Absen**.

Aturan yang berlaku:

- **Satu nomor = satu pendaftaran.** Kirim ulang dengan nomor yang sama akan
  *memperbarui* barisnya, bukan menambah baris baru.
- **Kuota ditegakkan** bila `quota` diisi. Saat menghitung sisa tempat,
  pendaftaran milik nomor yang sedang mengirim **tidak** ikut dihitung — kalau
  ikut, orang yang sekadar mengubah jumlah tamunya akan tertuduh melebihi kuota
  oleh pendaftarannya sendiri.
- **Maksimum 50 orang** per satu kiriman, dan **5 kiriman/menit per IP**.
  Keduanya menahan satu skrip memborong seluruh kuota acara.

> **Batas yang diketahui:** dedup memakai nomor WhatsApp tanpa verifikasi. Orang
> yang tahu nomor pendaftar lain masih bisa menimpa nama & jumlah tamunya.
> Menutupnya butuh OTP WhatsApp — belum dikerjakan.

Tabel `masjid_program_rsvps`: `masjid_id`, `program_id`, `name`, `phone`,
`guests`, `note`, `status` (`registered` / `attended` / `no_show`).

## 3. Laporan dampak

Mengubah "program" menjadi "dampak" — yang dilihat donatur bukan lagi sekadar
rencana kegiatan, tapi hasilnya.

Diisi pengurus di `dashboard/program/dampak/{id}`:

- `beneficiaries_count` — jumlah penerima manfaat
- `impact_narrative` — cerita dampak
- `impact_published` — saklar tampil/tidak di halaman publik
- Foto bukti (banyak) di tabel `masjid_program_impact_photos` (`photo`, `caption`)

Baru tampil di halaman publik program **setelah** `impact_published` dinyalakan,
jadi laporan setengah jadi tidak bocor lebih dulu.

## 4. Live streaming

Kolom `masjid_programs.stream_url` (nullable). Di halaman publik program:

- URL **YouTube** (bentuk `watch?v=`, `youtu.be/`, `live/`, `embed/`, `shorts/`)
  diubah ke bentuk embed dan ditampilkan sebagai pemutar tertanam.
- URL lain **tidak** di-`iframe` — ditampilkan sebagai tombol "Tonton Siaran"
  yang membuka tab baru. Meng-`iframe` URL sembarang sama dengan menjalankan
  halaman orang lain di dalam halaman masjid.

Skema tautan disaring `tautan_aman()` (hanya `http`/`https`) saat menyimpan
**dan** saat merender: `esc($url, 'attr')` mengamankan teks di dalam atribut,
tapi tidak menyaring `javascript:`. Berlaku sama untuk `registration_link`.

## Migrasi terkait

| Migrasi | Isi |
|---------|-----|
| `2026-09-05-140000_AddProgramImpactFields` | 3 kolom dampak + tabel foto bukti |
| `2026-09-05-160000_CreateProgramRsvpsTable` | Tabel RSVP |
| `2026-09-05-220000_AddProgramStreamUrl` | Kolom `stream_url` |
