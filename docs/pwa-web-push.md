# PWA & Web Push

Aplikasi bisa **dipasang** (installable) di ponsel/desktop, dan masjid bisa
mengirim **notifikasi** ke jamaah tanpa app store dan tanpa layanan pihak ketiga.

> **PENTING:** notifikasi TIDAK akan terkirim sebelum sepasang kunci VAPID
> dipasang di `.env` server. Halaman dashboard tetap bisa dibuka dan pesan tetap
> tersimpan, tapi pengirimannya diam saja. Lihat *Pemasangan* di bawah.

---

## Bagian PWA

| Berkas | Peran |
|--------|-------|
| `Home::manifest` (rute `manifest.webmanifest`) | Manifest disajikan PHP, bukan berkas statis, supaya `start_url`/`scope`/ikon mengikuti `base_url` — di lokal aplikasi ada di `/masjid/`, di produksi di root. |
| `sw.js` (di **web root**, bukan `public/`) | Service worker: halaman offline saat navigasi gagal + penanganan `push` dan `notificationclick`. Ditaruh di root agar cakupannya penuh di kedua lingkungan. |
| `offline.html` (web root) | Halaman yang tampil saat perangkat kehilangan jaringan. |
| `partials/pwa.php` | Tautan manifest, `theme-color`, ikon, dan pendaftaran service worker. Disisipkan ke **semua** layout. |

Jalur internal service worker diturunkan dari `registration.scope`, jadi tak ada
path yang di-hardcode.

## Bagian Web Push — kenapa *payloadless*

Standar Web Push punya dua lapis: **VAPID** (RFC 8292, membuktikan siapa
pengirimnya) dan **enkripsi payload** (RFC 8291, `aes128gcm`). Yang kedua adalah
bagian tersulit sekaligus paling rawan salah.

Di sini isinya **tidak** dikirim lewat push. Server hanya mengirim "ketukan"
berbadan kosong; service worker yang menerima ketukan lalu **mengambil** isi
notifikasi dari `push/latest?masjid=<id>`. Hasilnya enkripsi payload tak
diperlukan sama sekali, dan `App\Libraries\WebPush` cukup menangani VAPID saja.

Konsekuensinya: isi notifikasi harus sudah tersimpan **sebelum** ketukan
dikirim — itulah urutan yang dipakai `Admin::sendPush`.

## Pemasangan (sekali per server)

```bash
cd app-core && php spark push:vapid
```

Salin tiga baris keluarannya ke `app-core/.env`:

```ini
vapid.publicKey  = '...'   # titik publik EC 65 byte, base64url
vapid.privateKey = '...'   # PEM kunci privat, di-base64 agar muat satu baris
vapid.subject    = 'mailto:admin@masj.id'
```

`vapid.privateKey` adalah **rahasia** — jangan pernah dikomit. `publicKey`
otomatis dipakai tombol "Aktifkan Notifikasi" di halaman profil masjid; bila
kunci belum ada, tombolnya disembunyikan.

Di XAMPP/Windows `openssl_pkey_new` kadang gagal bila `OPENSSL_CONF` belum
ter-set — atur dulu bila perintah di atas melaporkan gagal membuat kunci EC.

## Alur pemakaian

1. Jamaah membuka halaman masjid → menekan **Aktifkan Notifikasi** → browser
   meminta izin → langganan dikirim ke `POST push/subscribe`.
2. Langganan disimpan di `masjid_push_subscriptions`, **idempoten** per
   `endpoint_hash` (`sha256` dari endpoint, indeks unik) — menekan tombolnya
   dua kali tidak membuat baris dobel.
3. Pengurus menulis notifikasi di **Dashboard › Notifikasi**
   (`dashboard/notifikasi`). Pesan masuk `masjid_push_messages` lebih dulu.
4. Server mengirim ketukan ke tiap langganan. Balasan `404`/`410` berarti
   langganan sudah mati (browser mencabutnya) dan barisnya **dihapus otomatis**.
5. Service worker menerima ketukan → mengambil `push/latest` → menampilkan
   notifikasi.

## Keamanan

`push/subscribe` adalah rute **publik dan dikecualikan dari CSRF** (dipanggil
JavaScript, bukan formulir). Karena baris yang ia simpan kelak menjadi alamat
tujuan `curl` di langkah 4, ada dua penjaga:

- `WebPush::endpointSah()` — endpoint wajib `https` **dan** berada di host
  layanan push yang dikenal (FCM, Mozilla, Apple, WNS). Tanpa ini siapa pun bisa
  mendaftarkan `http://127.0.0.1:…` atau alamat internal lalu memancing server
  menembak jaringan dalam (SSRF). Diperiksa saat menyimpan **dan** saat mengirim.
- Pembatas laju 10 permintaan/menit per alamat IP.

## Yang tak bisa diuji di lokal

Pendaftaran service worker dan pengiriman push **nyata** perlu browser asli
(browser otomasi tak bisa mendaftarkan SW) serta kunci VAPID yang sah. Yang bisa
diuji tanpa perangkat asli: manifest valid, `sw.js`/`offline.html` tersaji,
kebenaran kriptografi VAPID (tanda tangan ES256 terverifikasi, publik 65 byte,
signature 64 byte), dan penyimpanan langganan yang idempoten.

## Tabel

- `masjid_push_subscriptions` — `masjid_id`, `endpoint`, `p256dh`, `auth`,
  `endpoint_hash` (unik).
- `masjid_push_messages` — `masjid_id`, `title`, `body`, `url`. Yang diambil
  service worker selalu baris **terbaru** milik masjid itu.
