-- Open Masjid Database Schema (Clean Version)
-- Optimized for public repository (No sensitive data)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- 1. Core Tables
-- --------------------------------------------------------

-- Table structure for `provinces`
CREATE TABLE IF NOT EXISTS `provinces` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `regencies`
CREATE TABLE IF NOT EXISTS `regencies` (
  `id` varchar(50) NOT NULL,
  `province_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `province_id` (`province_id`),
  CONSTRAINT `regencies_province_id_foreign` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `users`
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('superadmin','user') DEFAULT 'user',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `masjid`
CREATE TABLE IF NOT EXISTS `masjid` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `tagline` varchar(255) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telegram_bot_token` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `foto_utama` varchar(255) DEFAULT NULL,
  `about_us` text DEFAULT NULL,
  `visi` text DEFAULT NULL,
  `misi` text DEFAULT NULL,
  `provinsi` varchar(100) DEFAULT NULL,
  `kabupaten` varchar(100) DEFAULT NULL,
  `regency_id` varchar(50) DEFAULT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `menu_berita` tinyint(1) DEFAULT 1,
  `menu_program` tinyint(1) DEFAULT 1,
  `menu_laporan` tinyint(1) DEFAULT 1,
  `menu_kontak` tinyint(1) DEFAULT 1,
  `is_public_report` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `masjid_pengurus`
CREATE TABLE IF NOT EXISTS `masjid_pengurus` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `masjid_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `role` enum('admin','staff') DEFAULT 'admin',
  `title` varchar(100) DEFAULT NULL,
  `is_creator` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `masjid_id` (`masjid_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `masjid_pengurus_masjid_id_foreign` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `masjid_pengurus_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 2. Feature Tables
-- --------------------------------------------------------

-- News
CREATE TABLE IF NOT EXISTS `masjid_news_categories` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `masjid_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `news_cat_masjid_id_foreign` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `masjid_news` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `masjid_id` int(11) UNSIGNED NOT NULL,
  `category_id` int(11) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `status` enum('published','draft') DEFAULT 'published',
  `views` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `news_masjid_id_foreign` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Programs
CREATE TABLE IF NOT EXISTS `masjid_programs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `masjid_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `target_donation` decimal(15,2) DEFAULT NULL,
  `status` enum('published','draft') DEFAULT 'published',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `programs_masjid_id_foreign` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Finance
CREATE TABLE IF NOT EXISTS `masjid_finance_categories` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `masjid_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('pemasukan','pengeluaran') DEFAULT 'pemasukan',
  `slug` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fin_cat_masjid_id_foreign` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `masjid_finance_transactions` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `masjid_id` int(11) UNSIGNED NOT NULL,
  `category_id` int(11) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `amount` decimal(15,2) DEFAULT 0.00,
  `type` enum('pemasukan','pengeluaran') NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fin_trans_masjid_id_foreign` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Payments Configuration
CREATE TABLE IF NOT EXISTS `masjid_payments` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `masjid_id` int(11) UNSIGNED NOT NULL,
  `payment_mode` enum('manual','multipay','midtrans','xendit','bank_direct') DEFAULT 'manual',
  -- Manual Transfer Info
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_account_name` varchar(100) DEFAULT NULL,
  `bank_account_number` varchar(50) DEFAULT NULL,
  `qris_image` varchar(255) DEFAULT NULL,
  -- Modular Config (Encrypted JSON or specific fields)
  `api_key` varchar(255) DEFAULT NULL,
  `api_secret` varchar(255) DEFAULT NULL,
  `merchant_id` varchar(100) DEFAULT NULL,
  `callback_token` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `masjid_id` (`masjid_id`),
  CONSTRAINT `payments_masjid_id_foreign` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 3. Initial Dummy Data
-- --------------------------------------------------------

-- Default Superadmin (Password: password123)
INSERT INTO `users` (`name`, `email`, `password_hash`, `role`, `created_at`, `updated_at`) VALUES
('Super Admin', 'admin@openmasjid.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin', NOW(), NOW());

-- --------------------------------------------------------
-- 4. LMS Tables
-- --------------------------------------------------------

-- Table structure for `lms_modules`
CREATE TABLE IF NOT EXISTS `lms_modules` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `lembaga_pemateri` varchar(255) DEFAULT NULL,
  `status` enum('published','draft') DEFAULT 'draft',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `lms_materials`
CREATE TABLE IF NOT EXISTS `lms_materials` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('video','pdf','html') NOT NULL DEFAULT 'video',
  `content` text NOT NULL,
  `order_number` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `lms_materials_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `lms_modules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `lms_progress`
CREATE TABLE IF NOT EXISTS `lms_progress` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL,
  `masjid_id` int(11) UNSIGNED NOT NULL,
  `material_id` int(11) UNSIGNED NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_material_unique` (`user_id`, `material_id`),
  CONSTRAINT `lms_progress_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `lms_progress_masjid_id_foreign` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `lms_progress_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `lms_materials` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 5. Mustahik Scoring & Recommendations (Module 4)
-- --------------------------------------------------------

-- Table structure for `masjid_mustahik`
CREATE TABLE IF NOT EXISTS `masjid_mustahik` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `masjid_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `nik` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `income_per_month` decimal(15,2) DEFAULT 0.00,
  `dependents_count` int(11) DEFAULT 0,
  `house_ownership` enum('milik_sendiri','ngontrak','numpang','lainnya') DEFAULT 'lainnya',
  `status` enum('active','inactive') DEFAULT 'active',
  `ai_score` int(11) DEFAULT NULL,
  `ai_reasoning` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `mustahik_masjid_id_foreign` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `masjid_mustahik_distributions`
CREATE TABLE IF NOT EXISTS `masjid_mustahik_distributions` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `masjid_id` int(11) UNSIGNED NOT NULL,
  `mustahik_id` int(11) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `amount` decimal(15,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `dist_mustahik_id_foreign` FOREIGN KEY (`mustahik_id`) REFERENCES `masjid_mustahik` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `dist_masjid_id_foreign` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

