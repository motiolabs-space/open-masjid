-- Migration SQL for Masjid2 Updates (2026-02-17)
-- Run this in your SiteGround phpMyAdmin

-- 1. Create masjid_warga
CREATE TABLE IF NOT EXISTS `masjid_warga` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `masjid_id` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `nik` VARCHAR(20) NULL,
  `kk` VARCHAR(20) NULL,
  `phone` VARCHAR(20) NULL,
  `address` TEXT NULL,
  `economic_status` ENUM('mampu', 'cukup', 'kurang_mampu', 'fakir', 'miskin', 'yatim') DEFAULT 'cukup',
  `status` ENUM('active', 'inactive', 'moved', 'deceased') DEFAULT 'active',
  `notes` TEXT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  CONSTRAINT `masjid_warga_masjid_id_foreign` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 2. Alter masjid_programs (Add target_donation)
-- Note: If this fails saying column exists, you can ignore it
ALTER TABLE `masjid_programs` ADD COLUMN `target_donation` DECIMAL(15,2) NULL AFTER `quota`;

-- 2a. Alter masjid (Add logo, about_us, contacts)
ALTER TABLE `masjid` ADD COLUMN `logo` VARCHAR(255) NULL AFTER `foto_utama`;
ALTER TABLE `masjid` ADD COLUMN `tagline` VARCHAR(255) NULL AFTER `name`;
ALTER TABLE `masjid` ADD COLUMN `about_us` TEXT NULL AFTER `misi`;
ALTER TABLE `masjid` ADD COLUMN `phone` VARCHAR(20) NULL AFTER `address`;
ALTER TABLE `masjid` ADD COLUMN `whatsapp` VARCHAR(20) NULL AFTER `phone`;
ALTER TABLE `masjid` ADD COLUMN `email` VARCHAR(100) NULL AFTER `whatsapp`;
ALTER TABLE `masjid` ADD COLUMN `action_button_active` TINYINT(1) DEFAULT 1 AFTER `email`;
ALTER TABLE `masjid` ADD COLUMN `action_button_text` VARCHAR(50) DEFAULT 'Donasi' AFTER `action_button_active`;
ALTER TABLE `masjid` ADD COLUMN `action_button_url` VARCHAR(255) DEFAULT '#donasi' AFTER `action_button_text`;
ALTER TABLE `masjid` ADD COLUMN `regency_id` VARCHAR(50) NULL AFTER `kabupaten`;

-- 2b. Create masjid_socials
CREATE TABLE IF NOT EXISTS `masjid_socials` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `masjid_id` INT(11) UNSIGNED NOT NULL,
  `platform` VARCHAR(50) NOT NULL, -- instagram, tiktok, facebook, youtube, twitter, whatsapp_group, telegram_group, website
  `url` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  CONSTRAINT `masjid_socials_masjid_id_foreign` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 3. Create masjid_donations
CREATE TABLE IF NOT EXISTS `masjid_donations` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `masjid_id` INT(11) UNSIGNED NOT NULL,
  `program_id` INT(11) UNSIGNED NULL,
  `invoice_number` VARCHAR(50) NOT NULL UNIQUE,
  `amount` DECIMAL(15,2) NOT NULL,
  `donor_name` VARCHAR(100) NULL,
  `donor_email` VARCHAR(100) NULL,
  `donor_phone` VARCHAR(20) NULL,
  `message` TEXT NULL,
  `payment_method` VARCHAR(50) NULL,
  `payment_channel` VARCHAR(50) NULL,
  `payment_ref` VARCHAR(255) NULL,
  `payment_url` TEXT NULL,
  `status` ENUM('pending', 'success', 'failed', 'expired') DEFAULT 'pending',
  `paid_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  CONSTRAINT `masjid_donations_masjid_id_foreign` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `masjid_donations_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `masjid_programs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 4. Create masjid_inventory
CREATE TABLE IF NOT EXISTS `masjid_inventory` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `masjid_id` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `brand` VARCHAR(100) NULL,
  `quantity` INT(11) UNSIGNED DEFAULT 1,
  `unit` VARCHAR(50) DEFAULT 'pcs',
  `condition` ENUM('good', 'damaged_light', 'damaged_heavy', 'lost') DEFAULT 'good',
  `purchase_date` DATE NULL,
  `purchase_price` DECIMAL(15,2) NULL,
  `description` TEXT NULL,
  `photo` VARCHAR(255) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  CONSTRAINT `masjid_inventory_masjid_id_foreign` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 5. Create masjid_payments
CREATE TABLE IF NOT EXISTS `masjid_payments` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `masjid_id` INT(11) UNSIGNED NOT NULL,
  `payment_mode` ENUM('manual', 'multipay') DEFAULT 'manual',
  `bank_name` VARCHAR(100) NULL,
  `bank_account_name` VARCHAR(100) NULL,
  `bank_account_number` VARCHAR(50) NULL,
  `qris_image` VARCHAR(255) NULL,
  `multipay_api_key` VARCHAR(255) NULL,
  `multipay_secret_key` VARCHAR(255) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  CONSTRAINT `masjid_payments_masjid_id_foreign` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 6. Create masjid_subscribers
CREATE TABLE IF NOT EXISTS `masjid_subscribers` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `masjid_id` INT(11) UNSIGNED NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NULL,
  `name` VARCHAR(100) NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  CONSTRAINT `masjid_subscribers_masjid_id_foreign` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 7. Create masjid_broadcasts
CREATE TABLE IF NOT EXISTS `masjid_broadcasts` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `masjid_id` INT(11) UNSIGNED NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `type` ENUM('email', 'whatsapp') DEFAULT 'email',
  `status` ENUM('draft', 'sent', 'failed') DEFAULT 'draft',
  `recipient_count` INT(11) DEFAULT 0,
  `sent_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  CONSTRAINT `masjid_broadcasts_masjid_id_foreign` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 8. Create masjid_distributions
CREATE TABLE IF NOT EXISTS `masjid_distributions` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `masjid_id` INT(11) UNSIGNED NOT NULL,
  `warga_id` INT(11) UNSIGNED NULL,
  `program_id` INT(11) UNSIGNED NULL,
  `date` DATE NOT NULL,
  `type` ENUM('money', 'goods', 'service') DEFAULT 'money',
  `amount` DECIMAL(15,2) DEFAULT 0,
  `items` TEXT NULL,
  `description` TEXT NULL,
  `evidence_photo` VARCHAR(255) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  CONSTRAINT `masjid_distributions_masjid_id_foreign` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `masjid_distributions_warga_id_foreign` FOREIGN KEY (`warga_id`) REFERENCES `masjid_warga` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `masjid_distributions_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `masjid_programs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 9. Create masjid_schedules
CREATE TABLE IF NOT EXISTS `masjid_schedules` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `masjid_id` INT(11) UNSIGNED NOT NULL,
  `date` DATE NOT NULL,
  `prayer_type` ENUM('subuh', 'dzuhur', 'ashar', 'maghrib', 'isya', 'jumat', 'tarawih', 'eid_fitr', 'eid_adha') NOT NULL,
  `imam_name` VARCHAR(100) NULL,
  `khatib_name` VARCHAR(100) NULL,
  `muadzin_name` VARCHAR(100) NULL,
  `bilal_name` VARCHAR(100) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  CONSTRAINT `masjid_schedules_masjid_id_foreign` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX idx_schedule_lookup ON masjid_schedules(masjid_id, date, prayer_type);

-- 10. Update Regencies Table (Support Alphanumeric IDs from MyQuran)
-- This is handled automatically by public/seed_regencies_smart.php, but included here for reference.
ALTER TABLE `regencies` MODIFY `id` VARCHAR(50) NOT NULL;
