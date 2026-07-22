# MCP Server & REST API (akses agen AI / integrasi)

Masj.id menyediakan **MCP (Model Context Protocol) server** dan **REST API** per
masjid. Keduanya dapat **membaca** (kas, jadwal sholat, donasi) **dan mengubah
data** (buat/ubah/hapus transaksi kas, berita, program) lewat token.

> **PENTING — token ini bisa mengubah & menghapus data.** Perlakukan seperti
> password. Setiap perubahan, berhasil maupun **ditolak**, tercatat di
> **Dashboard › API/MCP › Audit**. Bila ada yang tidak dikenali, cabut tokennya.

Ditulis tangan sebagai JSON-RPC 2.0 minimal (tanpa pustaka pihak ketiga), agar
permukaan serangannya kecil dan penyaringan tenant-nya mudah diaudit.

---

## Keamanan — yang paling penting

Aplikasi ini multi-tenant (banyak masjid, satu server). Karena itu:

- **Token menentukan masjid.** Setiap token (`masjid.mcp_token`, unik) memetakan
  tepat SATU masjid. Seluruh permintaan dari pemegang token hanya menyentuh data
  masjid itu.
- **Tidak ada tool yang menerima `masjid_id` dari pemanggil.** Masjid selalu
  diturunkan dari token — agen tidak bisa meminta data masjid lain, secara
  struktural. (Diuji: token masjid A mengembalikan kas masjid A; token masjid B
  mengembalikan kas masjid B; tidak ada kebocoran silang.)
- **Tool baca DAN tulis tersedia.** Seluruh operasi tulis melewati satu layanan
  bersama (`App\Libraries\MasjidWriter`) yang dipakai MCP maupun REST, sehingga
  aturan keamanannya hanya ditulis sekali dan tak mungkin berbeda antar
  antarmuka. Ubah/hapus wajib menyasar data milik sendiri; `kategori_id` yang
  dikirim juga diperiksa kepemilikannya.
- **SETIAP perubahan tercatat** di `api_audit_logs` — berhasil maupun gagal,
  beserta sumber (API/MCP), aksi, entitas, alasan penolakan, dan IP. Percobaan
  yang ditolak justru sinyal terpenting saat menyelidiki token bocor. Dilihat di
  **Dashboard › API/MCP › Audit**; tidak bisa diubah/dihapus lewat API mana pun.
- **Token bisa dicabut** kapan saja dari Pengaturan Masjid — langsung memutus
  akses agen.

## Mengaktifkan (admin masjid)

Dashboard › **Pengaturan Masjid** › bagian **Token MCP** › **Buat Token MCP**.
Salin tokennya. "Buat ulang" mengganti token lama (langsung tidak berlaku);
"Cabut" menonaktifkan MCP untuk masjid itu.

## Endpoint

```
POST https://masj.id/api/mcp
Authorization: Bearer <token>
Content-Type: application/json
```

Isi body JSON-RPC 2.0. Metode yang didukung: `initialize`, `tools/list`,
`tools/call`.

### Contoh: daftar tool

```bash
curl -X POST https://masj.id/api/mcp \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"jsonrpc":"2.0","id":1,"method":"tools/list"}'
```

### Contoh: panggil tool

```bash
curl -X POST https://masj.id/api/mcp \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"jsonrpc":"2.0","id":2,"method":"tools/call","params":{"name":"cek_kas"}}'
```

## Tool tersedia

| Tool | Keterangan |
|------|-----------|
| `cek_kas` | Ringkasan kas bulan berjalan: pemasukan, pengeluaran, saldo. |
| `jadwal_sholat` | Jadwal sholat hari ini (termasuk koreksi menit pengurus). |
| `donasi_terbaru` | Ringkasan donasi berhasil terbaru (maks 10). |
| `buat_data` | Membuat data baru: `{entitas, data}`. |
| `ubah_data` | Mengubah data: `{entitas, id, data}` — kirim hanya field yang diubah. |
| `hapus_data` | Menghapus data: `{entitas, id}`. Tidak dapat dibatalkan. |

`entitas` yang didukung: **transaksi**, **berita**, **program**. Semua terbatas
pada masjid pemilik token.

Field per entitas:
- **transaksi**: `tanggal`, `jenis` (pemasukan|pengeluaran), `nominal`,
  `kategori_id`, `keterangan`
- **berita**: `judul`, `isi`, `kategori_id?`, `status?` (published|draft)
- **program**: `judul`, `deskripsi`, `tanggal_mulai`, `lokasi?`,
  `target_donasi?`, `kategori_id?`

## REST API — menulis data

```
POST   /api/v1/{entitas}        buat   -> 201 {status, data:{id}}
PUT    /api/v1/{entitas}/{id}   ubah
DELETE /api/v1/{entitas}/{id}   hapus
```

Contoh:
```bash
curl -X POST https://masj.id/api/v1/transaksi \
  -H "Authorization: Bearer <api_token>" -H "Content-Type: application/json" \
  -d '{"tanggal":"2026-07-20","jenis":"pemasukan","nominal":"2.500.000","kategori_id":12,"keterangan":"Infaq Jumat"}'
```

Nominal menerima format rupiah (`2.500.000`) maupun angka polos. Tanggal
menerima `YYYY-MM-DD` maupun `DD/MM/YYYY`.

## Catatan pengembangan

- Kode: `app/Controllers/Api/Mcp.php`. Menambah tool = tambah entri di
  `daftarTool()` dan cabang di `panggilTool()`, keduanya WAJIB memakai
  `$this->masjid['id']` untuk penyaringan tenant.
- Rute `api/mcp` sengaja dikecualikan dari CSRF (agen memakai token Bearer,
  bukan token CSRF) — lihat `app/Config/Filters.php`.
- Transport saat ini HTTP JSON-RPC sederhana (POST → JSON). Bila kelak perlu
  streaming (SSE) untuk klien MCP tertentu, itu penambahan transport, bukan
  perubahan tool.
