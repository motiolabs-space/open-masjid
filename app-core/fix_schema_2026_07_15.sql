-- =====================================================================
--  Penyelarasan skema produksi — 15 Juli 2026
--
--  LATAR BELAKANG
--  Migrasi CodeIgniter tidak pernah benar-benar diterapkan di produksi.
--  Tabel `migrations` berhenti di 2026-02-01, sedangkan repositori sudah
--  memuat migrasi sampai 2026-07-15. Kolom-kolom di bawah karenanya tidak
--  pernah terbentuk, meskipun kode aplikasi sudah memakainya.
--
--  DAMPAK
--  MasjidModel::allowedFields memuat telegram_bot_token, running_text,
--  iqomah_settings, sholat_duration, koreksi_menit, dan timezone.
--  Admin::updateProfile meneruskan seluruh field yang lolos allowedFields ke
--  SATU UPDATE, sehingga satu kolom yang belum ada membuat SELURUH
--  penyimpanan Profil Masjid gagal ("Unknown column ..."). users.avatar yang
--  tidak ada juga membuat Daftar Pengikut dan pencarian pengurus HTTP 500.
--
--  AMAN DIJALANKAN BERULANG
--  Setiap kolom hanya ditambahkan bila belum ada. MySQL tidak mendukung
--  "ADD COLUMN IF NOT EXISTS", jadi dipakai prosedur bantu yang memeriksa
--  information_schema. Skrip ini aman dijalankan meskipun sebagian kolom
--  sudah terbentuk (mis. bila `spark migrate` ternyata sempat berhasil).
--
--  CARA MENJALANKAN
--      phpMyAdmin produksi -> pilih database -> tab SQL -> tempel -> Go
--  atau:
--      mysql -u USER -p NAMA_DB < fix_schema_2026_07_15.sql
-- =====================================================================

DROP PROCEDURE IF EXISTS _tambah_kolom;

DELIMITER $$
CREATE PROCEDURE _tambah_kolom(
    IN p_tabel VARCHAR(64),
    IN p_kolom VARCHAR(64),
    IN p_definisi TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME   = p_tabel
          AND COLUMN_NAME  = p_kolom
    ) THEN
        SET @sql = CONCAT('ALTER TABLE `', p_tabel, '` ADD COLUMN `', p_kolom, '` ', p_definisi);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
        SELECT CONCAT('ditambahkan: ', p_tabel, '.', p_kolom) AS hasil;
    ELSE
        SELECT CONCAT('dilewati (sudah ada): ', p_tabel, '.', p_kolom) AS hasil;
    END IF;
END$$
DELIMITER ;

-- ── Tabel: masjid ────────────────────────────────────────────────────

-- Token bot Telegram per masjid. Sudah lama terdaftar di allowedFields dan ada
-- input-nya pada form Profil, tetapi kolomnya tidak pernah dibuat di produksi —
-- inilah sebab Simpan Profil sudah gagal bahkan sebelum fitur Display TV.
CALL _tambah_kolom('masjid', 'telegram_bot_token', 'VARCHAR(255) NULL AFTER `email`');

-- Status aktif/suspend masjid (migrasi 2026-07-11 AddStatusToMasjid).
CALL _tambah_kolom('masjid', 'status', "ENUM('active','suspended') NOT NULL DEFAULT 'active' AFTER `username`");

-- Teks berjalan pada Display TV. Kosong = otomatis dari agenda & berita.
CALL _tambah_kolom('masjid', 'running_text', 'TEXT NULL AFTER `tagline`');

-- Jeda adzan -> iqomah per waktu sholat, mis.
-- {"Subuh":20,"Dzuhur":10,"Ashar":10,"Maghrib":7,"Isya":10}
CALL _tambah_kolom('masjid', 'iqomah_settings', 'JSON NULL AFTER `running_text`');

-- Lama layar digelapkan saat sholat berlangsung (menit, dihitung sejak iqomah).
CALL _tambah_kolom('masjid', 'sholat_duration', 'INT UNSIGNED NOT NULL DEFAULT 10 AFTER `iqomah_settings`');

-- Koreksi jadwal sholat per waktu (menit, boleh negatif), mis.
-- {"Subuh":0,"Dzuhur":2,"Ashar":0,"Maghrib":-1,"Isya":0}
CALL _tambah_kolom('masjid', 'koreksi_menit', 'JSON NULL AFTER `sholat_duration`');

-- Zona waktu masjid: Asia/Jakarta (WIB), Asia/Makassar (WITA),
-- Asia/Jayapura (WIT). NULL = ditentukan otomatis dari koordinat.
CALL _tambah_kolom('masjid', 'timezone', 'VARCHAR(64) NULL AFTER `longitude`');

-- ── Tabel: users ─────────────────────────────────────────────────────

-- Dipakai Admin::followers, Admin::pengurus, dan Admin::searchUsers.
CALL _tambah_kolom('users', 'avatar', 'VARCHAR(255) NULL AFTER `phone`');

DROP PROCEDURE IF EXISTS _tambah_kolom;

-- ── Catatan migrasi ──────────────────────────────────────────────────
-- Tandai migrasi terkait sebagai sudah dijalankan agar `php spark migrate`
-- tidak mencoba menambahkan kolom yang sama untuk kedua kalinya.
-- Hanya disisipkan bila belum tercatat.
INSERT INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`)
SELECT baru.v, baru.c, 'default', 'App', UNIX_TIMESTAMP(),
       (SELECT COALESCE(MAX(m2.batch), 0) + 1 FROM `migrations` m2)
FROM (
            SELECT '2026-07-11-014804' AS v, 'App\\Database\\Migrations\\AddStatusToMasjid' AS c
  UNION ALL SELECT '2026-07-15-090000', 'App\\Database\\Migrations\\AddRunningTextToMasjid'
  UNION ALL SELECT '2026-07-15-093000', 'App\\Database\\Migrations\\AddIqomahSettingsToMasjid'
  UNION ALL SELECT '2026-07-15-101500', 'App\\Database\\Migrations\\AddKoreksiMenitToMasjid'
  UNION ALL SELECT '2026-07-15-104500', 'App\\Database\\Migrations\\AddTimezoneToMasjid'
  UNION ALL SELECT '2026-07-15-113000', 'App\\Database\\Migrations\\AddAvatarToUsers'
  UNION ALL SELECT '2026-07-15-120000', 'App\\Database\\Migrations\\AddProfilFieldsToMasjid'
) AS baru
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` m WHERE m.version = baru.v
);

-- Hasil akhir: seharusnya tidak ada baris yang dikembalikan.
SELECT 'PERIKSA: kolom berikut masih hilang' AS peringatan, k.tabel, k.kolom
FROM (
            SELECT 'masjid' AS tabel, 'telegram_bot_token' AS kolom
  UNION ALL SELECT 'masjid', 'status'
  UNION ALL SELECT 'masjid', 'running_text'
  UNION ALL SELECT 'masjid', 'iqomah_settings'
  UNION ALL SELECT 'masjid', 'sholat_duration'
  UNION ALL SELECT 'masjid', 'koreksi_menit'
  UNION ALL SELECT 'masjid', 'timezone'
  UNION ALL SELECT 'users',  'avatar'
) AS k
WHERE NOT EXISTS (
    SELECT 1 FROM information_schema.COLUMNS c
    WHERE c.TABLE_SCHEMA = DATABASE() AND c.TABLE_NAME = k.tabel AND c.COLUMN_NAME = k.kolom
);
