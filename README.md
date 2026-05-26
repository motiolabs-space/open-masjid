# Open Masjid - Sistem Open Source untuk Digitalisasi Masjid yang Amanah, Transparan, dan Modern

![Open Source](https://img.shields.io/badge/Open%20Source-Yes-green)
![CodeIgniter4](https://img.shields.io/badge/CodeIgniter-4-red)
![PHP](https://img.shields.io/badge/PHP-8-blue)
![License](https://img.shields.io/badge/license-MIT-yellow)
![Contributions Welcome](https://img.shields.io/badge/contributions-welcome-brightgreen)

Open Masjid adalah sistem manajemen masjid berbasis open source untuk membantu DKM mengelola keuangan, jamaah, program donasi, kegiatan, dan distribusi bantuan sosial secara lebih amanah, transparan, dan modern.

Project ini menjadi fondasi teknologi dari ekosistem Masj.id, namun tetap dapat dipasang secara mandiri oleh masjid, komunitas, yayasan, atau developer yang ingin membantu digitalisasi masjid di Indonesia.

## Why Open Masjid?

Banyak masjid masih mengelola keuangan, data jamaah, program sosial, dan laporan kegiatan secara manual. Akibatnya, laporan sering terlambat, data tercecer, dan kepercayaan jamaah sulit dibangun secara konsisten.

Open Masjid hadir untuk membantu masjid:
- Menyajikan laporan keuangan yang transparan.
- Mengelola program donasi berbasis tujuan.
- Mendata jamaah dan mustahik secara lebih rapi.
- Menampilkan informasi masjid melalui TV display.
- Membuka ruang kontribusi developer untuk kemaslahatan umat.

## Who Is This For?

Open Masjid cocok untuk:
- DKM / Takmir Masjid
- Yayasan pengelola masjid
- Masjid kampus
- Masjid perumahan
- Masjid perusahaan
- Komunitas developer muslim
- Lembaga sosial dan filantropi Islam

## Key Features

### Finance & Transparency
- Import mutasi bank dari CSV BCA, BSI, dan Mandiri.
- Laporan pemasukan dan pengeluaran yang dapat dipublikasikan.
- Program-based funding untuk pembangunan, zakat, infak, sedekah, dan kegiatan sosial.
- Halaman laporan publik untuk meningkatkan kepercayaan jamaah.

### Masjid Digital Profile
- Landing page publik untuk setiap masjid.
- Informasi kegiatan, program, jadwal, dan laporan.
- Profil masjid yang mudah dibagikan ke jamaah dan donatur.

### Jamaah & Engagement
- Manajemen data jamaah.
- Integrasi WhatsApp untuk komunikasi.
- TV Display Mode untuk layar informasi masjid.

### Social Impact
- Database mustahik.
- Profil ekonomi penerima bantuan.
- Tracking distribusi bantuan dengan dokumentasi/evidence.

## Support This Project

Jika Anda percaya bahwa masjid di Indonesia perlu menjadi lebih amanah, transparan, dan modern, bantu project ini dengan:
- Star repository ini.
- Fork dan coba install secara mandiri.
- Share ke DKM, developer, dan komunitas muslim.
- Kontribusi melalui issue, pull request, dokumentasi, atau testing.

## Roadmap

### Phase 1 — Core Masjid Management
- Dashboard masjid
- Manajemen jamaah
- Laporan keuangan
- Import mutasi bank
- Program donasi

### Phase 2 — Engagement & Display
- TV display mode
- WhatsApp integration
- Jadwal kegiatan
- Public profile masjid

### Phase 3 — Social Impact
- Database mustahik
- Distribusi bantuan
- Evidence tracking
- Laporan dampak sosial

### Phase 4 — Ecosystem
- Payment gateway integration
- Multi-masjid SaaS mode
- API integration
- Mobile-friendly jamaah portal

## 🚀 Teknologi yang Digunakan

- **Backend**: [CodeIgniter 4](https://codeigniter.com/) (PHP 8+)
- **Frontend**: [Tailwind CSS](https://tailwindcss.com/)
- **Ikonografi**: [Material Symbols](https://fonts.google.com/icons)
- **Database**: MySQL / MariaDB (Konfigurasi via `.env`)

## 🐳 Menjalankan dengan Docker (disarankan)

Cukup **Docker** dan **Docker Compose** — tidak perlu XAMPP/Laragon.

### Persyaratan

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/macOS) atau Docker Engine + Compose (Linux)

### Langkah

```bash
git clone https://github.com/motiolabs-space/open-masjid.git
cd open-masjid
docker compose up --build
```

Tunggu hingga log menampilkan `Application ready`. Lalu buka:

| URL | Keterangan |
|-----|------------|
| http://localhost:8080/ | Beranda |
| http://localhost:8080/login | Login |
| http://localhost:8080/dashboard | Dashboard (setelah login) |

**Login default:** `admin@openmasjid.com` / `password123`

### Yang dijalankan otomatis

- **app** — PHP 8.2 + Apache (CodeIgniter, `mod_rewrite`)
- **db** — MariaDB 10.11
- Impor `database.sql` saat database pertama kali dibuat
- Salin `docker/.env.docker` → `app-core/.env` (jika belum ada)
- `composer install` di `app-core` (jika `vendor/` belum ada)
- `php spark migrate`

### Perintah berguna

```bash
# Jalankan di background
docker compose up -d --build

# Hentikan
docker compose down

# Reset database (hapus volume) lalu jalankan ulang
docker compose down -v
docker compose up --build
```

### Kustomisasi port / password

Salin `.env.example` ke `.env` di root proyek (opsional):

```bash
cp .env.example .env
```

Contoh variabel:

```ini
APP_PORT=8080
MYSQL_PORT=3307
MYSQL_PASSWORD=open_masjid_secret
```

---

## 🛠️ Instalasi & Setup Lokal (tanpa Docker)

1. **Clone Repositori**

   ```bash
   git clone https://github.com/motiolabs-space/open-masjid.git
   cd open-masjid
   ```

2. **Konfigurasi Environment**
   - Masuk ke direktori `app-core`.
   - Salin file `env` menjadi `.env`.
   - Sesuaikan konfigurasi database dan `baseURL` seperti berikut:
     ```ini
     app.baseURL = 'http://localhost/masjid2/public/'
     database.default.hostname = localhost
     database.default.database = nama_database_anda
     database.default.username = user_anda
     database.default.password = password_anda
     ```

3. **Install Dependencies** (Jika diperlukan)

   ```bash
   composer install
   ```

3. **Setup Database**
   - Buat database baru di MySQL/MariaDB.
   - Impor file `database.sql` yang ada di root direktori untuk membangun struktur tabel dasar dan data awal.
   - Gunakan fitur **Migrations** untuk memastikan skema database Anda adalah yang terbaru:
     ```bash
     php spark migrate
     ```
   - **Login Default (Superadmin):**
     - Email: `admin@openmasjid.com`
     - Password: `password123`

4. **Menjalankan Aplikasi**
   - Akses via web server (XAMPP/Laragon) di: `http://localhost/open-masjid/`
   - Buka **Halaman Admin** di: `http://localhost/open-masjid/dashboard` (setelah setup login).

## 📄 Struktur Direktori

- `app-core/`: Berisi logika inti aplikasi (MVC CodeIgniter 4).
- `public/`: Direktori publik untuk aset dan index utama.
- `.htaccess`: Konfigurasi routing server.

## A Movement for Better Mosque Management

Open Masjid bukan hanya aplikasi.

Ini adalah gerakan open source untuk membantu masjid di Indonesia menjadi:
- lebih amanah,
- lebih transparan,
- lebih modern,
- dan lebih berdampak bagi jamaah dan masyarakat.

Kami percaya teknologi dapat membantu meningkatkan kepercayaan jamaah, efisiensi pengelolaan masjid, dan distribusi bantuan sosial yang lebih tepat sasaran.

## About Masj.id

Open Masjid adalah fondasi open-source dari ekosistem Masj.id.

Masj.id berfokus pada digitalisasi masjid Indonesia melalui:
- transparansi laporan,
- engagement jamaah,
- pengelolaan program,
- dan social impact management.

## Contributing

Kami membuka kontribusi dari developer, designer, tester, penulis dokumentasi, DKM, dan relawan digital.

Anda dapat membantu melalui:
- Membuat issue untuk bug atau ide fitur.
- Memperbaiki dokumentasi.
- Membuat UI/UX improvement.
- Menambahkan integrasi bank/payment gateway.
- Membantu testing pada masjid nyata.
- Membuat panduan instalasi untuk shared hosting, VPS, atau localhost.

Silakan fork repository ini dan kirim Pull Request.
## Community & Social Media

Follow perkembangan Open Masjid dan gerakan digitalisasi masjid Indonesia:

- Instagram: [@webmasjid](https://instagram.com/webmasjid)
- LinkedIn: [Portal Masjid](https://www.linkedin.com/company/portal-masjid/)

Kami membuka kolaborasi dengan:
- DKM / Takmir Masjid
- Developer Open Source
- Komunitas Muslim
- Lembaga Sosial & Filantropi
- Relawan Digitalisasi Masjid


## Disclaimer

Open Masjid adalah project open source yang terus dikembangkan. Beberapa fitur dapat berubah mengikuti kebutuhan lapangan, masukan DKM, dan kontribusi komunitas. Untuk penggunaan produksi, pastikan konfigurasi keamanan, backup database, dan hak akses sudah diuji dengan baik.


---

© 2024 Open Masjid - Memberdayakan Masjid melalui Teknologi Terbuka.
