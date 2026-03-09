CREATE TABLE IF NOT EXISTS `trs_project_status_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_charter_id` bigint unsigned DEFAULT NULL,
  `version` int unsigned NOT NULL,
  `tanggal` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `trs_project_status_history_project_charter_id_foreign` (`project_charter_id`),
  CONSTRAINT `trs_project_status_history_project_charter_id_foreign`
    FOREIGN KEY (`project_charter_id`) REFERENCES `trs_project_charters` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
