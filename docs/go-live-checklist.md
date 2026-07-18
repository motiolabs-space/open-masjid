# Checklist Go-Live Masj.id

Daftar hal yang **harus dikerjakan di server / GitHub** (di luar kode) agar
seluruh fitur hidup di produksi. Kode, migrasi, dan git sudah beres — item di
sini semuanya butuh tangan Anda.

Server: `https://masj.id` (SiteGround). Path aplikasi:
`/home/customer/www/masj.id/app-core` — sesuaikan bila berbeda.

Urutan: kerjakan **🔴 keamanan lebih dulu**, lalu 🟡 agar fitur hidup, lalu 🟢
per-masjid.

---

## 🔴 A. Keamanan — kerjakan pertama

### A1. Cabut deploy key yang bocor
Kunci privat SSH lama ada di **riwayat publik** repo (`f7923d2:github_deploy_key`).
Selama belum dicabut, siapa pun bisa mengunduhnya dan punya akses SSH server.

- [ ] GitHub → repo `open-masjid` → **Settings → Deploy keys** → hapus key lama.
- [ ] Di server: buka `~/.ssh/authorized_keys`, hapus baris kunci publik lama.
- [ ] Terbitkan kunci baru; simpan privatnya **hanya** sebagai GitHub Secret
      (`SITEGROUND_SSH_PRIVATE_KEY`), jangan sebagai berkas.
- [ ] (Opsional) tulis-ulang riwayat git untuk membuang berkasnya — tetapi
      anggap kunci itu SUDAH bocor apa pun yang terjadi; pencabutan di atas yang
      menentukan.

**Verifikasi:** `git show f7923d2:github_deploy_key` masih menampilkan isi, tetapi
kunci itu kini tak lagi diterima server (sudah dihapus dari authorized_keys).

### A2. Matikan mode development
`.env` server masih `CI_ENVIRONMENT = development` → debug toolbar menyajikan isi
dalam server (path, query, konfigurasi) ke publik.

- [ ] Edit `/home/customer/www/masj.id/app-core/.env`:
      ```
      CI_ENVIRONMENT = production
      ```

**Verifikasi:**
```bash
curl -s "https://masj.id/?debugbar" -o /dev/null -w "%{http_code}\n"
# dan pastikan halaman biasa tak lagi memuat elemen debugbar
curl -s https://masj.id/ | grep -c debugbar_loader    # harus 0
```

---

## 🟡 B. Agar fitur AI & pengingat hidup (di `.env` server)

Edit `/home/customer/www/masj.id/app-core/.env`. Tidak perlu restart — CI4
membaca `.env` tiap permintaan. `.env` dikecualikan dari rsync deploy, jadi aman
dari penimpaan.

### B1. Kunci AI (SumoPod)
- [ ] ```
      sumopod.apiKey = 'sk-ISI-KUNCI-ANDA'
      sumopod.modelBerat = 'claude-haiku-4-5'
      ```

**Verifikasi kunci hidup** (sebelum mengandalkan halaman):
```bash
curl -s https://ai.sumopod.com/v1/chat/completions \
  -H "Authorization: Bearer sk-KUNCI" -H "Content-Type: application/json" \
  -d '{"model":"gpt-4o-mini","messages":[{"role":"user","content":"balas: ok"}],"max_tokens":10}'
# balasan berisi "content" = hidup; 401/invalid = salah
```
Tanpa ini MATI: Laporan AI, Virtual Auditor, bot Telegram, skoring mustahik,
ringkas obrolan, bantu susun pengumuman. (Lihat `docs/model-ai-sumopod.md`.)

### B2. Cron pengingat terjadwal
Tanpa cron, pengingat **dan** pemangkasan pesan grup (retensi) tak pernah jalan.

- [ ] cPanel → **Cron Jobs** → tambahkan (tiap 5 menit):
      ```
      */5 * * * * cd /home/customer/www/masj.id/app-core && /usr/bin/php spark broadcast:reminders >> writable/logs/reminders.log 2>&1
      ```
      (sesuaikan path `php`).

**Verifikasi:**
```bash
cd /home/customer/www/masj.id/app-core && php spark broadcast:reminders --dry
# menampilkan pengingat jatuh tempo tanpa mengirim
```
(Lihat `docs/pengingat-terjadwal.md`.)

### B3. (Opsional) Gateway WhatsApp
Hanya bila ingin siaran/kuitansi lewat WhatsApp. Per masjid, bukan global —
diisi di **Pengaturan Masjid** tiap masjid (B ini hanya pengingat bahwa fitur WA
butuh akun Fonnte). Telegram tidak memerlukan ini dan gratis.

---

## 🟢 C. Konfigurasi per-masjid (admin masjid, lewat dashboard)

Bukan sekali untuk semua — tiap masjid mengisi sendiri di **Dashboard**.

- [ ] **Pengaturan Pembayaran** → rekening / QRIS (agar donasi manual bisa
      ditransfer).
- [ ] **Pengaturan Masjid** → koordinat (latitude/longitude) & zona waktu
      (agar jadwal sholat & pengingatnya benar).
- [ ] **Pengaturan Masjid** → Token Bot Telegram; lalu daftarkan Webhook
      (URL ada di halaman itu). Untuk **ringkas obrolan**: matikan *privacy mode*
      bot di @BotFather (lihat `docs/ringkas-obrolan-grup.md`).
- [ ] **Broadcast → Grup**: masukkan bot ke grup, kirim satu pesan, lalu
      **Aktifkan** grup yang muncul.
- [ ] **Broadcast → Pengingat**: atur jadwal sholat harian / laporan kas.
- [ ] (Opsional) **Pengaturan Masjid → Token MCP**: buat token bila ingin
      menghubungkan agen AI (lihat `docs/mcp-server.md`).
- [ ] (Opsional) **Pengaturan Masjid → Kunci Gateway WhatsApp** (Fonnte).

---

## D. Uji asap setelah go-live (cepat)

```bash
# Halaman inti hidup
for p in "" login jogokariyan jogokariyan/display; do
  echo "$p -> $(curl -s -o /dev/null -w '%{http_code}' https://masj.id/$p)"
done
# MCP menolak tanpa token (401), bukan 404/500
curl -s -o /dev/null -w "mcp -> %{http_code}\n" -X POST https://masj.id/api/mcp \
  -H "Content-Type: application/json" -d '{"jsonrpc":"2.0","id":1,"method":"tools/list"}'
```

Uji fungsional yang butuh kredensial asli (belum pernah ditembak ke layanan
nyata dalam pengembangan):
- [ ] Bot Telegram menjawab di grup nyata saat di-mention.
- [ ] Satu pengiriman WhatsApp (Fonnte) benar-benar sampai.
- [ ] Satu siaran nyata masuk ke grup jamaah.

---

## Dokumen terkait
- `docs/model-ai-sumopod.md` — pemilihan & biaya model AI
- `docs/pengingat-terjadwal.md` — pengingat & cron
- `docs/ringkas-obrolan-grup.md` — ringkas obrolan + privacy mode
- `docs/mcp-server.md` — MCP server (agen AI)
