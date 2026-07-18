# MCP Server (akses agen AI)

Masj.id menyediakan **MCP (Model Context Protocol) server** hanya-baca: agen AI
mana pun yang berbicara MCP (mis. Claude) dapat menanyakan data sebuah masjid
lewat token — kas, jadwal sholat, donasi.

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
- **Semua tool HANYA-BACA.** Tidak ada yang mengubah data. Menambah tool yang
  menulis kelak menuntut pertimbangan tersendiri.
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

Semuanya tanpa argumen dan terbatas pada masjid pemilik token.

## Catatan pengembangan

- Kode: `app/Controllers/Api/Mcp.php`. Menambah tool = tambah entri di
  `daftarTool()` dan cabang di `panggilTool()`, keduanya WAJIB memakai
  `$this->masjid['id']` untuk penyaringan tenant.
- Rute `api/mcp` sengaja dikecualikan dari CSRF (agen memakai token Bearer,
  bukan token CSRF) — lihat `app/Config/Filters.php`.
- Transport saat ini HTTP JSON-RPC sederhana (POST → JSON). Bila kelak perlu
  streaming (SSE) untuk klien MCP tertentu, itu penambahan transport, bukan
  perubahan tool.
