# Development Roadmap - Masj.id

Dokumen ini memuat peta jalan (roadmap) implementasi pengembangan ekosistem Open Masjid (Masj.id), mencakup fitur *Artificial Intelligence* (AI) menggunakan API SumoPod, serta sistem manajerial dan pembelajaran (LMS). Roadmap ini disusun berdasarkan penyelesaian *pain problems* yang dihadapi oleh pengurus masjid (DKM).

---

## 🚀 Phase 1: Administrative Efficiency & Reporting
**Target**: Menyelesaikan beban kerja manual pengurus dan mempercepat proses transparansi pelaporan.

### 1. AI-Powered Bank Mutation Auto-Categorization
* **Goal**: Membaca deskripsi dari CSV mutasi bank (BCA, BSI, Mandiri) dan secara otomatis merekomendasikan kategori dana (*Program-Based Funding*).
* **Technical Implementation**:
  1. Buat *System Prompt* yang menyimpan daftar/konteks kategori yang ada di sistem (contoh: Zakat Fitrah, Sedekah Pembangunan, Operasional).
  2. Parsing file CSV saat diunggah, kirim narasi mutasi (dalam *batch*) ke API SumoPod.
  3. AI merespons dengan format JSON berisi `transaction_id` dan `suggested_category_id`.
  4. Buat UI agar Admin DKM bisa melakukan **Review & Approve** prediksi AI sebelum transaksi disimpan secara permanen.

### 2. Automated Report & Copywriting Generator
* **Goal**: Menghasilkan narasi laporan keuangan bulanan dan kegiatan yang hangat, transparan, dan mudah dibaca oleh jamaah awam.
* **Technical Implementation**:
  1. Bangun query agregasi bulanan (total pemasukan per kategori, total pengeluaran, saldo akhir, jumlah kegiatan).
  2. Inject agregasi tersebut sebagai JSON ke dalam *Prompt* SumoPod dengan persona "Sekretaris Masjid Profesional".
  3. AI merespons dengan draf teks laporan (*copywriting*).
  4. Sediakan fitur *1-click-publish* untuk menyebarkan narasi ini ke *Landing Page* publik, layar *TV Display*, dan siap *copy-paste* untuk *WhatsApp Broadcast*.

---

## 💬 Phase 2: Jamaah Engagement
**Target**: Membangun komunikasi yang *real-time* dan responsif antara masjid dan jamaah tanpa menghabiskan waktu DKM.

### 3. Smart WhatsApp Assistant (Layanan Jamaah 24/7)
* **Goal**: Memiliki *virtual assistant* via WhatsApp untuk merespons pertanyaan berulang seputar donasi, jadwal, dan info kajian.
* **Technical Implementation**:
  1. Integrasi dengan Webhook API WhatsApp (menggunakan layanan pihak ketiga seperti Watzap, Wablas, atau official API).
  2. Implementasi skema **RAG (Retrieval-Augmented Generation)** sederhana: saat pesan masuk, ambil data profil masjid, jadwal waktu sholat terkini, dan rekap program kajian ke dalam konteks Prompt.
  3. AI memberikan jawaban yang relevan, ramah, dan mengarahkan jamaah (misal: membalas dengan link halaman donasi jika ditanya rekening).

---

## 📊 Phase 3: Advanced Intelligence & Data Analysis
**Target**: Mewujudkan pendistribusian dana sosial yang sangat presisi dan menjaga akuntabilitas keuangan secara berlapis.

### 4. Mustahik Scoring & Recommendation
* **Goal**: Menentukan prioritas penerima bantuan agar lebih adil, objektif, dan tepat sasaran.
* **Technical Implementation**:
  1. Ekstrak data *Mustahik Database* (pendapatan, jumlah tanggungan, status kepemilikan rumah, historis bantuan).
  2. Minta AI memberikan "Scoring Kelayakan" (misal skala 1-10) beserta penjabaran alasannya (contoh: "*Skor 9 karena janda anak tiga dengan penghasilan di bawah UMR dan rumah mengontrak*").
  3. Tampilkan hasil *scoring* dan *reasoning* dari AI di halaman *dashboard* penyaluran bantuan DKM.

### 5. Anomaly Detection for Financial Transparency
* **Goal**: Menghindari *human error* atau ketidakwajaran saat *data entry* keuangan sebelum dipublikasikan ke jamaah.
* **Technical Implementation**:
  1. Kumpulkan rata-rata pengeluaran bulanan historis per kategori sebagai parameter referensi.
  2. Saat Admin DKM akan menutup pembukuan akhir bulan (*closing*), jalankan *batch check* ke API SumoPod.
  3. AI akan bertindak sebagai *Virtual Auditor*, mendeteksi angka yang *outlier* (contoh: biaya listrik bulanan melesat dari Rp1 Juta ke Rp10 Juta karena *typo*).
  4. Sistem akan menahan tombol "Publish ke Publik" dan memberikan peringatan (Alert) merah agar Admin mengecek ulang struk/evidence.

---

## 🎓 Phase 4: Capacity Building (LMS Pengurus)
**Target**: Meningkatkan kapasitas dan standar manajemen para pengurus masjid di seluruh platform Masj.id.

### 6. Learning Management System (LMS) Terintegrasi
* **Goal**: Menyediakan wadah belajar (pelatihan manajemen masjid) yang tersentralisasi dan mudah diakses oleh pengurus (DKM) secara mandiri.
* **Technical Implementation**:
  1. Buat struktur database baru (Modul, Materi/Topik, Progres Belajar).
  2. Implementasi antarmuka khusus (LMS Dashboard) di area Superadmin untuk mengunggah materi, dan di area DKM untuk belajar.
  3. Mendukung ragam format materi pelatihan (*Video Embedding* dari YouTube, Dokumen PDF, Slide Presentasi, maupun Artikel Text).
  4. **Kurikulum Awal**: Bekerjasama atau mengambil *best practice* pelatihan dari masjid-masjid percontohan (seperti Masjid Jogokariyan) tentang strategi pemakmuran masjid, manajemen keuangan, hingga SOP layanan umat.
  5. *Optional*: Gamifikasi (Sertifikat Kelulusan) bagi DKM yang telah menyelesaikan modul dasar.
