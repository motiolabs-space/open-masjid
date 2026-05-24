CREATE TABLE `masjid_followers` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `masjid_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `masjid_id` (`masjid_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `masjid_followers_masjid_id_foreign` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `masjid_followers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
