# Ringkas Obrolan Grup (Broadcast)

Pengurus dapat meminta ringkasan AI atas obrolan grup Telegram jamaah, untuk
menyorot hal yang perlu **perhatian atau tindakan** (pertanyaan belum terjawab,
keluhan, usulan, kesepakatan). Tombol **Ringkas** ada di setiap grup Telegram
aktif pada halaman **Dashboard › Broadcast › Grup**.

---

## ⚠️ Ini menyimpan percakapan jamaah — baca dulu

Fitur ini menyimpan pesan grup untuk sementara agar bisa diringkas. Karena itu
sensitif, batas-batasnya:

- **Hanya grup terdaftar & aktif** yang pesannya disimpan. Grup asing atau yang
  masih menunggu persetujuan tidak pernah tersimpan.
- **Retensi pendek: 3 hari.** Pesan lama dibuang otomatis oleh cron
  (`broadcast:reminders` sekaligus memangkasnya). Tujuannya meringkas aktivitas
  terbaru, bukan mengarsipkan obrolan jamaah.
- **Yang disimpan seperlunya:** nama pengirim, teks, waktu. **Nomor telепon
  tidak disimpan.**
- **Hanya pengurus** yang bisa meminta ringkasan, dan hanya untuk grup masjidnya
  sendiri.

Ubah masa retensi lewat konstanta `RETENSI_HARI` pada
`app/Models/MasjidGroupMessageModel.php`.

## Prasyarat: matikan "privacy mode" bot

Secara bawaan, bot Telegram **hanya menerima** pesan yang menyebut namanya
(mention) atau membalas pesannya — bukan seluruh obrolan grup. Agar ringkasan
mencakup seluruh percakapan, matikan privacy mode bot:

1. Buka **@BotFather** di Telegram.
2. `/mybots` → pilih bot masjid → **Bot Settings** → **Group Privacy** →
   **Turn off**.
3. Keluarkan lalu masukkan kembali bot ke grup agar setelan baru berlaku.

Bila privacy mode tetap menyala, fitur masih berjalan tetapi ringkasannya hanya
mencakup pesan yang menyapa bot — jauh lebih terbatas.

## Cara kerja singkat

1. Webhook menyimpan pesan dari grup terdaftar-aktif (lihat `Api\Telegram`).
2. Pengurus menekan **Ringkas** pada sebuah grup.
3. AI (tingkat *berat*, tercatat di **Pemakaian AI** sebagai `ringkas_grup`)
   meringkas hingga 150 pesan terakhir menjadi poin-poin tindakan.
4. Cron memangkas pesan yang lebih tua dari masa retensi.

Butuh minimal 3 pesan tersimpan untuk bisa diringkas.
