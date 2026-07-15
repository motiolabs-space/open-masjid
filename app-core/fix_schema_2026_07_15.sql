-- =====================================================================
--  Penyelarasan skema produksi — 15 Juli 2026
--
--  LATAR BELAKANG
--  Migrasi CodeIgniter TIDAK pernah benar-benar diterapkan di produksi.
--  Tabel `migrations` berhenti di 2026-02-01, sedangkan repositori sudah
--  memuat migrasi sampai 2026-07-15. Kolom-kolom di bawah karenanya tidak
--  pernah terbentuk, meskipun kode aplikasi sudah memakainya.
--
--  DAMPAK SAAT INI (mendesak)
--  MasjidModel::allowedFields memuat running_text, iqomah_settings,
--  sholat_duration, koreksi_menit, dan timezone. Admin::updateProfile
--  meneruskan seluruh field yang lolos allowedFields ke satu UPDATE,
--  sehingga satu kolom yang belum ada membuat SELURUH penyimpanan Profil
--  Masjid gagal: "Unknown column 'running_text' in 'field list'".
--  Selain itu users.avatar yang tidak ada membuat halaman Daftar Pengikut
--  dan pencarian pengurus gagal dengan HTTP 500.
--
--  CARA MENJALANKAN (phpMyAdmin produksi -> tab SQL, atau CLI):
--      mysql -u USER -p NAMA_DB < fix_schema_2026_07_15.sql
--
--  Aman dijalankan sekali. Seluruh kolom bersifat NULL/berdefault sehingga
--  tidak mengubah data yang sudah ada.
-- =====================================================================

-- ── Tabel: masjid ────────────────────────────────────────────────────

-- Token bot Telegram per masjid. Sudah lama terdaftar di allowedFields dan
-- ada input-nya pada form Profil, tetapi kolomnya tidak pernah dibuat di
-- produksi — inilah sebab Simpan Profil gagal bahkan SEBELUM penambahan
-- kolom Display TV di bawah.
ALTER TABLE `masjid`
  ADD COLUMN `telegram_bot_token` VARCHAR(255) NULL AFTER `email`;

-- Status aktif/suspend masjid (migrasi 2026-07-11 AddStatusToMasjid).
ALTER TABLE `masjid`
  ADD COLUMN `status` ENUM('active','suspended') NOT NULL DEFAULT 'active' AFTER `username`;

-- Teks berjalan pada Display TV. Kosong = otomatis dari agenda & berita.
ALTER TABLE `masjid`
  ADD COLUMN `running_text` TEXT NULL AFTER `tagline`;

-- Jeda adzan -> iqomah per waktu sholat, mis.
-- {"Subuh":20,"Dzuhur":10,"Ashar":10,"Maghrib":7,"Isya":10}
ALTER TABLE `masjid`
  ADD COLUMN `iqomah_settings` JSON NULL AFTER `running_text`;

-- Lama layar digelapkan saat sholat berlangsung (menit, dihitung sejak iqomah).
ALTER TABLE `masjid`
  ADD COLUMN `sholat_duration` INT UNSIGNED NOT NULL DEFAULT 10 AFTER `iqomah_settings`;

-- Koreksi jadwal sholat per waktu (menit, boleh negatif), mis.
-- {"Subuh":0,"Dzuhur":2,"Ashar":0,"Maghrib":-1,"Isya":0}
ALTER TABLE `masjid`
  ADD COLUMN `koreksi_menit` JSON NULL AFTER `sholat_duration`;

-- Zona waktu masjid: Asia/Jakarta (WIB), Asia/Makassar (WITA),
-- Asia/Jayapura (WIT). NULL = ditentukan otomatis dari koordinat.
ALTER TABLE `masjid`
  ADD COLUMN `timezone` VARCHAR(64) NULL AFTER `longitude`;

-- ── Tabel: users ─────────────────────────────────────────────────────

-- Dipakai Admin::followers, Admin::pengurus, dan Admin::searchUsers.
ALTER TABLE `users`
  ADD COLUMN `avatar` VARCHAR(255) NULL AFTER `phone`;

-- ── Catatan migrasi ──────────────────────────────────────────────────
-- Setelah skema diselaraskan, tandai migrasi terkait sebagai sudah
-- dijalankan agar `php spark migrate` (bila nanti berfungsi) tidak mencoba
-- menambahkan kolom yang sama dua kali.
-- Nilai `group` dan `namespace` mengikuti baris yang sudah ada di tabel ini.
INSERT INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`)
SELECT * FROM (
  SELECT '2026-07-11-014804' AS v, 'App\\Database\\Migrations\\AddStatusToMasjid' AS c, 'default' AS g, 'App' AS n, UNIX_TIMESTAMP() AS t, (SELECT COALESCE(MAX(batch),0)+1 FROM `migrations` m2) AS b
  UNION ALL SELECT '2026-07-15-090000', 'App\\Database\\Migrations\\AddRunningTextToMasjid', 'default', 'App', UNIX_TIMESTAMP(), (SELECT COALESCE(MAX(batch),0)+1 FROM `migrations` m2)
  UNION ALL SELECT '2026-07-15-093000', 'App\\Database\\Migrations\\AddIqomahSettingsToMasjid', 'default', 'App', UNIX_TIMESTAMP(), (SELECT COALESCE(MAX(batch),0)+1 FROM `migrations` m2)
  UNION ALL SELECT '2026-07-15-101500', 'App\\Database\\Migrations\\AddKoreksiMenitToMasjid', 'default', 'App', UNIX_TIMESTAMP(), (SELECT COALESCE(MAX(batch),0)+1 FROM `migrations` m2)
  UNION ALL SELECT '2026-07-15-104500', 'App\\Database\\Migrations\\AddTimezoneToMasjid', 'default', 'App', UNIX_TIMESTAMP(), (SELECT COALESCE(MAX(batch),0)+1 FROM `migrations` m2)
  UNION ALL SELECT '2026-07-15-113000', 'App\\Database\\Migrations\\AddAvatarToUsers', 'default', 'App', UNIX_TIMESTAMP(), (SELECT COALESCE(MAX(batch),0)+1 FROM `migrations` m2)
  UNION ALL SELECT '2026-07-15-120000', 'App\\Database\\Migrations\\AddProfilFieldsToMasjid', 'default', 'App', UNIX_TIMESTAMP(), (SELECT COALESCE(MAX(batch),0)+1 FROM `migrations` m2)
) AS baru
WHERE NOT EXISTS (SELECT 1 FROM `migrations` m WHERE m.version = baru.v);
