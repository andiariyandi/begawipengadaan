-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for db_begawipbj
CREATE DATABASE IF NOT EXISTS `db_begawipbj` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `db_begawipbj`;

-- Dumping structure for table db_begawipbj.academic_profiles
CREATE TABLE IF NOT EXISTS `academic_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `institution_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `institution_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expertise` json DEFAULT NULL,
  `institution_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `professional_identifier` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `portfolio_url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `academic_profiles_user_id_unique` (`user_id`),
  CONSTRAINT `academic_profiles_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.approvals
CREATE TABLE IF NOT EXISTS `approvals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `resource_type` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resource_id` bigint unsigned NOT NULL,
  `requested_action` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `requested_by` bigint unsigned DEFAULT NULL,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `expires_at` datetime DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `approvals_resource_idx` (`resource_type`,`resource_id`),
  KEY `approvals_status_created_idx` (`status`,`created_at`),
  KEY `approvals_reviewed_status_idx` (`reviewed_by`,`status`),
  KEY `approvals_requested_by_fk` (`requested_by`),
  CONSTRAINT `approvals_requested_by_fk` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `approvals_reviewed_by_fk` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `approvals_status_chk` CHECK ((`status` in (_utf8mb4'pending',_utf8mb4'in_review',_utf8mb4'approved',_utf8mb4'rejected',_utf8mb4'revision_required',_utf8mb4'cancelled',_utf8mb4'expired')))
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.asn_profiles
CREATE TABLE IF NOT EXISTS `asn_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `organization_unit_id` bigint unsigned DEFAULT NULL,
  `employee_identifier` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employment_status` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `institution_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_grade` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `official_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `asn_profiles_user_id_unique` (`user_id`),
  KEY `asn_profiles_organization_unit_id_idx` (`organization_unit_id`),
  KEY `asn_profiles_origin_type_idx` (`origin_type`),
  CONSTRAINT `asn_profiles_organization_unit_id_fk` FOREIGN KEY (`organization_unit_id`) REFERENCES `organization_units` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `asn_profiles_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `asn_profiles_origin_type_chk` CHECK ((`origin_type` in (_utf8mb4'pemprov_lampung',_utf8mb4'kementerian_lembaga',_utf8mb4'pemerintah_daerah_lain',_utf8mb4'pemerintah_desa',_utf8mb4'bumn_bumd',_utf8mb4'lainnya')))
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.assignments
CREATE TABLE IF NOT EXISTS `assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint unsigned NOT NULL,
  `lesson_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `instructions` longtext COLLATE utf8mb4_unicode_ci,
  `due_at` datetime DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `max_attempts` int unsigned NOT NULL DEFAULT '1',
  `allowed_mimes` json DEFAULT NULL,
  `max_file_size_bytes` bigint unsigned DEFAULT NULL,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `assignments_class_status_idx` (`class_id`,`status`),
  KEY `assignments_lesson_id_idx` (`lesson_id`),
  KEY `assignments_due_at_idx` (`due_at`),
  KEY `assignments_created_by_fk` (`created_by`),
  KEY `assignments_updated_by_fk` (`updated_by`),
  CONSTRAINT `assignments_class_id_fk` FOREIGN KEY (`class_id`) REFERENCES `learning_classes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `assignments_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `assignments_lesson_id_fk` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `assignments_updated_by_fk` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.assignment_submissions
CREATE TABLE IF NOT EXISTS `assignment_submissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `assignment_id` bigint unsigned NOT NULL,
  `enrollment_id` bigint unsigned NOT NULL,
  `attempt_number` int unsigned NOT NULL,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `submitted_at` datetime DEFAULT NULL,
  `graded_at` datetime DEFAULT NULL,
  `graded_by` bigint unsigned DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `feedback` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `assignment_submissions_unique` (`assignment_id`,`enrollment_id`,`attempt_number`),
  KEY `assignment_submissions_enrollment_status_idx` (`enrollment_id`,`status`),
  KEY `assignment_submissions_grader_status_idx` (`graded_by`,`status`),
  CONSTRAINT `assignment_submissions_assignment_id_fk` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `assignment_submissions_enrollment_id_fk` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `assignment_submissions_graded_by_fk` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `assignment_submissions_score_chk` CHECK (((`score` is null) or ((`score` >= 0.00) and (`score` <= 100.00)))),
  CONSTRAINT `assignment_submissions_status_chk` CHECK ((`status` in (_utf8mb4'draft',_utf8mb4'submitted',_utf8mb4'in_review',_utf8mb4'revision_required',_utf8mb4'graded',_utf8mb4'cancelled')))
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.assignment_submission_files
CREATE TABLE IF NOT EXISTS `assignment_submission_files` (
  `submission_id` bigint unsigned NOT NULL,
  `file_id` bigint unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`submission_id`,`file_id`),
  KEY `assignment_submission_files_file_id_idx` (`file_id`),
  CONSTRAINT `assignment_submission_files_file_id_fk` FOREIGN KEY (`file_id`) REFERENCES `file_assets` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `assignment_submission_files_submission_id_fk` FOREIGN KEY (`submission_id`) REFERENCES `assignment_submissions` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.attendances
CREATE TABLE IF NOT EXISTS `attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_registration_id` bigint unsigned NOT NULL,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recorded_by` bigint unsigned DEFAULT NULL,
  `recorded_at` datetime NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendances_event_registration_unique` (`event_registration_id`),
  KEY `attendances_recorded_by_fk` (`recorded_by`),
  CONSTRAINT `attendances_event_registration_id_fk` FOREIGN KEY (`event_registration_id`) REFERENCES `event_registrations` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `attendances_recorded_by_fk` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `attendances_status_chk` CHECK ((`status` in (_utf8mb4'present',_utf8mb4'absent',_utf8mb4'excused')))
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.audit_logs
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `actor_id` bigint unsigned DEFAULT NULL,
  `action` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resource_type` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resource_id` bigint unsigned DEFAULT NULL,
  `result` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `before_snapshot` json DEFAULT NULL,
  `after_snapshot` json DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `ip_hash` char(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent_hash` char(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_actor_created_idx` (`actor_id`,`created_at`),
  KEY `audit_logs_resource_created_idx` (`resource_type`,`resource_id`,`created_at`),
  KEY `audit_logs_action_created_idx` (`action`,`created_at`),
  KEY `audit_logs_result_created_idx` (`result`,`created_at`),
  CONSTRAINT `audit_logs_actor_id_fk` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `audit_logs_result_chk` CHECK ((`result` in (_utf8mb4'success',_utf8mb4'failed',_utf8mb4'denied',_utf8mb4'partial')))
) ENGINE=InnoDB AUTO_INCREMENT=1358 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.bookmarks
CREATE TABLE IF NOT EXISTS `bookmarks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `bookmarkable_type` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bookmarkable_id` bigint unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bookmarks_unique` (`user_id`,`bookmarkable_type`,`bookmarkable_id`),
  KEY `bookmarks_resource_idx` (`bookmarkable_type`,`bookmarkable_id`),
  CONSTRAINT `bookmarks_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.certificates
CREATE TABLE IF NOT EXISTS `certificates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `source_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `template_id` bigint unsigned DEFAULT NULL,
  `certificate_number` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verification_token_hash` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `issued_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `revoked_at` datetime DEFAULT NULL,
  `revoked_by` bigint unsigned DEFAULT NULL,
  `revocation_reason` text COLLATE utf8mb4_unicode_ci,
  `pdf_file_id` bigint unsigned DEFAULT NULL,
  `replaced_by_certificate_id` bigint unsigned DEFAULT NULL,
  `issued_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `certificates_number_unique` (`certificate_number`),
  UNIQUE KEY `certificates_verification_token_unique` (`verification_token_hash`),
  UNIQUE KEY `certificates_source_unique` (`source_type`,`source_id`),
  KEY `certificates_user_status_idx` (`user_id`,`status`),
  KEY `certificates_status_expires_idx` (`status`,`expires_at`),
  KEY `certificates_template_id_fk` (`template_id`),
  KEY `certificates_revoked_by_fk` (`revoked_by`),
  KEY `certificates_pdf_file_id_fk` (`pdf_file_id`),
  KEY `certificates_replaced_by_fk` (`replaced_by_certificate_id`),
  KEY `certificates_issued_by_fk` (`issued_by`),
  CONSTRAINT `certificates_issued_by_fk` FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `certificates_pdf_file_id_fk` FOREIGN KEY (`pdf_file_id`) REFERENCES `file_assets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `certificates_replaced_by_fk` FOREIGN KEY (`replaced_by_certificate_id`) REFERENCES `certificates` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `certificates_revoked_by_fk` FOREIGN KEY (`revoked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `certificates_template_id_fk` FOREIGN KEY (`template_id`) REFERENCES `certificate_templates` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `certificates_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `certificates_source_type_chk` CHECK ((`source_type` in (_utf8mb4'learning_enrollment',_utf8mb4'event_registration'))),
  CONSTRAINT `certificates_status_chk` CHECK ((`status` in (_utf8mb4'pending',_utf8mb4'issued',_utf8mb4'revoked',_utf8mb4'expired')))
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.certificate_sequences
CREATE TABLE IF NOT EXISTS `certificate_sequences` (
  `year` smallint unsigned NOT NULL,
  `last_number` int unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.certificate_templates
CREATE TABLE IF NOT EXISTS `certificate_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_payload` json NOT NULL,
  `background_file_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `certificate_templates_source_active_idx` (`source_type`,`is_active`),
  KEY `certificate_templates_background_file_id_fk` (`background_file_id`),
  KEY `certificate_templates_created_by_fk` (`created_by`),
  KEY `certificate_templates_updated_by_fk` (`updated_by`),
  CONSTRAINT `certificate_templates_background_file_id_fk` FOREIGN KEY (`background_file_id`) REFERENCES `file_assets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `certificate_templates_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `certificate_templates_updated_by_fk` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `certificate_templates_source_type_chk` CHECK ((`source_type` in (_utf8mb4'learning',_utf8mb4'event')))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.class_evaluators
CREATE TABLE IF NOT EXISTS `class_evaluators` (
  `class_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `assigned_by` bigint unsigned DEFAULT NULL,
  `assigned_at` datetime NOT NULL,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  PRIMARY KEY (`class_id`,`user_id`),
  KEY `class_evaluators_user_id_idx` (`user_id`),
  KEY `class_evaluators_assigned_by_fk` (`assigned_by`),
  CONSTRAINT `class_evaluators_assigned_by_fk` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `class_evaluators_class_id_fk` FOREIGN KEY (`class_id`) REFERENCES `learning_classes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `class_evaluators_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `class_evaluators_status_chk` CHECK ((`status` in (_utf8mb4'active',_utf8mb4'revoked')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.class_trainers
CREATE TABLE IF NOT EXISTS `class_trainers` (
  `class_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `assigned_by` bigint unsigned DEFAULT NULL,
  `assigned_at` datetime NOT NULL,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `role` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'primary',
  PRIMARY KEY (`class_id`,`user_id`),
  KEY `class_trainers_user_id_idx` (`user_id`),
  KEY `class_trainers_assigned_by_fk` (`assigned_by`),
  CONSTRAINT `class_trainers_assigned_by_fk` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `class_trainers_class_id_fk` FOREIGN KEY (`class_id`) REFERENCES `learning_classes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `class_trainers_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `class_trainers_status_chk` CHECK ((`status` in (_utf8mb4'active',_utf8mb4'revoked')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.competencies
CREATE TABLE IF NOT EXISTS `competencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `competencies_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.competency_certificates
CREATE TABLE IF NOT EXISTS `competency_certificates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `jf_ppbj_record_id` bigint unsigned NOT NULL,
  `certificate_type_id` bigint unsigned NOT NULL,
  `certificate_number` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issued_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `evidence_file_id` bigint unsigned DEFAULT NULL,
  `verified_by` bigint unsigned DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `competency_certificates_record_status_idx` (`jf_ppbj_record_id`,`status`),
  KEY `competency_certificates_type_expires_idx` (`certificate_type_id`,`expires_at`),
  KEY `competency_certificates_evidence_file_id_fk` (`evidence_file_id`),
  KEY `competency_certificates_verified_by_fk` (`verified_by`),
  CONSTRAINT `competency_certificates_evidence_file_id_fk` FOREIGN KEY (`evidence_file_id`) REFERENCES `file_assets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `competency_certificates_record_id_fk` FOREIGN KEY (`jf_ppbj_record_id`) REFERENCES `jf_ppbj_records` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `competency_certificates_type_id_fk` FOREIGN KEY (`certificate_type_id`) REFERENCES `competency_certificate_types` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `competency_certificates_verified_by_fk` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `competency_certificates_status_chk` CHECK ((`status` in (_utf8mb4'pending',_utf8mb4'verified',_utf8mb4'rejected',_utf8mb4'expired')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.competency_certificate_types
CREATE TABLE IF NOT EXISTS `competency_certificate_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `competency_certificate_types_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.competency_gap_snapshots
CREATE TABLE IF NOT EXISTS `competency_gap_snapshots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_unit_id` bigint unsigned NOT NULL,
  `competency_id` bigint unsigned DEFAULT NULL,
  `period_date` date NOT NULL,
  `required_count` int unsigned NOT NULL DEFAULT '0',
  `available_count` int unsigned NOT NULL DEFAULT '0',
  `gap_count` int NOT NULL DEFAULT '0',
  `metadata` json DEFAULT NULL,
  `generated_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `competency_gap_snapshots_unique` (`organization_unit_id`,`competency_id`,`period_date`),
  KEY `competency_gap_snapshots_period_idx` (`period_date`),
  KEY `competency_gap_snapshots_competency_id_fk` (`competency_id`),
  KEY `competency_gap_snapshots_generated_by_fk` (`generated_by`),
  CONSTRAINT `competency_gap_snapshots_competency_id_fk` FOREIGN KEY (`competency_id`) REFERENCES `competencies` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `competency_gap_snapshots_generated_by_fk` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `competency_gap_snapshots_organization_id_fk` FOREIGN KEY (`organization_unit_id`) REFERENCES `organization_units` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `competency_gap_snapshots_gap_chk` CHECK ((`gap_count` = (`required_count` - `available_count`)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.courses
CREATE TABLE IF NOT EXISTS `courses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `program_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `target_audience` text COLLATE utf8mb4_unicode_ci,
  `prerequisites` text COLLATE utf8mb4_unicode_ci,
  `learning_objectives` text COLLATE utf8mb4_unicode_ci,
  `level` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration_minutes` int unsigned DEFAULT NULL,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `visibility` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'internal',
  `cover_file_id` bigint unsigned DEFAULT NULL,
  `cover_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `published_by` bigint unsigned DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courses_slug_unique` (`slug`),
  KEY `courses_program_id_idx` (`program_id`),
  KEY `courses_status_visibility_published_idx` (`status`,`visibility`,`published_at`),
  KEY `courses_cover_file_id_fk` (`cover_file_id`),
  KEY `courses_created_by_fk` (`created_by`),
  KEY `courses_updated_by_fk` (`updated_by`),
  KEY `courses_published_by_fk` (`published_by`),
  CONSTRAINT `courses_cover_file_id_fk` FOREIGN KEY (`cover_file_id`) REFERENCES `file_assets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `courses_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `courses_program_id_fk` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `courses_published_by_fk` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `courses_updated_by_fk` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.email_verifications
CREATE TABLE IF NOT EXISTS `email_verifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `token_hash` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_verifications_token_hash_unique` (`token_hash`),
  KEY `email_verifications_user_expires_idx` (`user_id`,`expires_at`),
  CONSTRAINT `email_verifications_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.enrollments
CREATE TABLE IF NOT EXISTS `enrollments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `enrolled_at` datetime NOT NULL,
  `activated_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `final_score` decimal(5,2) DEFAULT NULL,
  `passed_at` datetime DEFAULT NULL,
  `certificate_eligible_at` datetime DEFAULT NULL,
  `withdrawn_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `enrollments_class_user_unique` (`class_id`,`user_id`),
  KEY `enrollments_user_status_idx` (`user_id`,`status`),
  KEY `enrollments_class_status_idx` (`class_id`,`status`),
  KEY `enrollments_certificate_eligible_idx` (`certificate_eligible_at`),
  CONSTRAINT `enrollments_class_id_fk` FOREIGN KEY (`class_id`) REFERENCES `learning_classes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `enrollments_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `enrollments_final_score_chk` CHECK (((`final_score` is null) or ((`final_score` >= 0.00) and (`final_score` <= 100.00)))),
  CONSTRAINT `enrollments_status_chk` CHECK ((`status` in (_utf8mb4'pending',_utf8mb4'active',_utf8mb4'completed',_utf8mb4'withdrawn',_utf8mb4'cancelled')))
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.events
CREATE TABLE IF NOT EXISTS `events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mode` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `visibility` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'internal',
  `summary` text COLLATE utf8mb4_unicode_ci,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `cover_file_id` bigint unsigned DEFAULT NULL,
  `start_at` datetime NOT NULL,
  `end_at` datetime NOT NULL,
  `location` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meeting_url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_open_at` datetime DEFAULT NULL,
  `registration_close_at` datetime DEFAULT NULL,
  `capacity` int unsigned DEFAULT NULL,
  `waitlist_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `certificate_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `attendance_code` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attendance_code_hash` char(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attendance_open_at` datetime DEFAULT NULL,
  `attendance_close_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `cancelled_by` bigint unsigned DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `cancellation_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `events_slug_unique` (`slug`),
  KEY `events_status_visibility_start_idx` (`status`,`visibility`,`start_at`),
  KEY `events_registration_period_idx` (`registration_open_at`,`registration_close_at`),
  KEY `events_cover_file_id_fk` (`cover_file_id`),
  KEY `events_created_by_fk` (`created_by`),
  KEY `events_updated_by_fk` (`updated_by`),
  KEY `events_cancelled_by_fk` (`cancelled_by`),
  KEY `events_attendance_code_idx` (`attendance_code`),
  CONSTRAINT `events_cancelled_by_fk` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `events_cover_file_id_fk` FOREIGN KEY (`cover_file_id`) REFERENCES `file_assets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `events_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `events_updated_by_fk` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `events_mode_chk` CHECK ((`mode` in (_utf8mb4'online',_utf8mb4'offline',_utf8mb4'hybrid'))),
  CONSTRAINT `events_period_chk` CHECK ((`end_at` >= `start_at`)),
  CONSTRAINT `events_status_chk` CHECK ((`status` in (_utf8mb4'draft',_utf8mb4'coming_soon',_utf8mb4'open_registration',_utf8mb4'full',_utf8mb4'finished',_utf8mb4'cancelled'))),
  CONSTRAINT `events_visibility_chk` CHECK ((`visibility` in (_utf8mb4'public',_utf8mb4'internal',_utf8mb4'restricted')))
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.event_registrations
CREATE TABLE IF NOT EXISTS `event_registrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'registered',
  `registered_at` datetime NOT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `promoted_at` datetime DEFAULT NULL,
  `status_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_registrations_event_user_unique` (`event_id`,`user_id`),
  KEY `event_registrations_event_status_registered_idx` (`event_id`,`status`,`registered_at`),
  KEY `event_registrations_user_status_idx` (`user_id`,`status`),
  CONSTRAINT `event_registrations_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `event_registrations_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `event_registrations_status_chk` CHECK ((`status` in (_utf8mb4'registered',_utf8mb4'waitlisted',_utf8mb4'cancelled',_utf8mb4'rejected')))
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.exports
CREATE TABLE IF NOT EXISTS `exports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `requested_by` bigint unsigned NOT NULL,
  `type` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'queued',
  `filters` json DEFAULT NULL,
  `file_id` bigint unsigned DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `expires_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `exports_requested_status_created_idx` (`requested_by`,`status`,`created_at`),
  KEY `exports_status_expires_idx` (`status`,`expires_at`),
  KEY `exports_file_id_fk` (`file_id`),
  CONSTRAINT `exports_file_id_fk` FOREIGN KEY (`file_id`) REFERENCES `file_assets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `exports_requested_by_fk` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `exports_status_chk` CHECK ((`status` in (_utf8mb4'queued',_utf8mb4'generating',_utf8mb4'ready',_utf8mb4'failed',_utf8mb4'expired',_utf8mb4'deleted')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.feedbacks
CREATE TABLE IF NOT EXISTS `feedbacks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `enrollment_id` bigint unsigned NOT NULL,
  `rating` tinyint unsigned DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not_started',
  `submitted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `feedbacks_enrollment_id_unique` (`enrollment_id`),
  CONSTRAINT `feedbacks_enrollment_id_fk` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `feedbacks_rating_chk` CHECK (((`rating` is null) or ((`rating` >= 1) and (`rating` <= 5)))),
  CONSTRAINT `feedbacks_status_chk` CHECK ((`status` in (_utf8mb4'not_started',_utf8mb4'submitted')))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.file_assets
CREATE TABLE IF NOT EXISTS `file_assets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `disk` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stored_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size_bytes` bigint unsigned NOT NULL,
  `checksum` char(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visibility` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'internal',
  `lifecycle_status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending_upload',
  `resource_type` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resource_id` bigint unsigned DEFAULT NULL,
  `uploaded_by` bigint unsigned DEFAULT NULL,
  `available_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `file_assets_resource_idx` (`resource_type`,`resource_id`),
  KEY `file_assets_visibility_lifecycle_idx` (`visibility`,`lifecycle_status`),
  KEY `file_assets_uploaded_by_created_idx` (`uploaded_by`,`created_at`),
  KEY `file_assets_expires_at_idx` (`expires_at`),
  CONSTRAINT `file_assets_uploaded_by_fk` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `file_assets_lifecycle_status_chk` CHECK ((`lifecycle_status` in (_utf8mb4'pending_upload',_utf8mb4'stored',_utf8mb4'scanned',_utf8mb4'available',_utf8mb4'rejected',_utf8mb4'quarantined',_utf8mb4'deleted'))),
  CONSTRAINT `file_assets_visibility_chk` CHECK ((`visibility` in (_utf8mb4'public',_utf8mb4'internal',_utf8mb4'restricted')))
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.jf_ppbj_levels
CREATE TABLE IF NOT EXISTS `jf_ppbj_levels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jf_ppbj_levels_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.jf_ppbj_records
CREATE TABLE IF NOT EXISTS `jf_ppbj_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `organization_unit_id` bigint unsigned NOT NULL,
  `employee_identifier` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_snapshot` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level_id` bigint unsigned NOT NULL,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `effective_at` datetime NOT NULL,
  `ended_at` datetime DEFAULT NULL,
  `change_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `evidence_file_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jf_ppbj_records_org_level_status_idx` (`organization_unit_id`,`level_id`,`status`),
  KEY `jf_ppbj_records_user_id_idx` (`user_id`),
  KEY `jf_ppbj_records_period_idx` (`effective_at`,`ended_at`),
  KEY `jf_ppbj_records_level_id_fk` (`level_id`),
  KEY `jf_ppbj_records_evidence_file_id_fk` (`evidence_file_id`),
  KEY `jf_ppbj_records_created_by_fk` (`created_by`),
  KEY `jf_ppbj_records_updated_by_fk` (`updated_by`),
  CONSTRAINT `jf_ppbj_records_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `jf_ppbj_records_evidence_file_id_fk` FOREIGN KEY (`evidence_file_id`) REFERENCES `file_assets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `jf_ppbj_records_level_id_fk` FOREIGN KEY (`level_id`) REFERENCES `jf_ppbj_levels` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `jf_ppbj_records_organization_unit_id_fk` FOREIGN KEY (`organization_unit_id`) REFERENCES `organization_units` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `jf_ppbj_records_updated_by_fk` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `jf_ppbj_records_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `jf_ppbj_records_change_type_chk` CHECK ((`change_type` in (_utf8mb4'addition',_utf8mb4'promotion',_utf8mb4'transfer_in',_utf8mb4'transfer_out',_utf8mb4'retirement',_utf8mb4'deactivation',_utf8mb4'update')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.knowledge_articles
CREATE TABLE IF NOT EXISTS `knowledge_articles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'article',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `document_version` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'v1.0',
  `document_category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `legal_basis` text COLLATE utf8mb4_unicode_ci,
  `resolution_summary` text COLLATE utf8mb4_unicode_ci,
  `case_severity` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `procurement_stage` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organization_unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_url` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reading_time` int unsigned DEFAULT '5',
  `workflow_status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `visibility` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'internal',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `carousel_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `carousel_order` int unsigned DEFAULT NULL,
  `sort_order` int unsigned DEFAULT '0',
  `cover_file_id` bigint unsigned DEFAULT NULL,
  `author_id` bigint unsigned NOT NULL,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `published_by` bigint unsigned DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `archived_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `knowledge_articles_slug_unique` (`slug`),
  KEY `knowledge_articles_publication_idx` (`type`,`workflow_status`,`visibility`,`published_at`),
  KEY `knowledge_articles_featured_carousel_idx` (`featured`,`carousel_enabled`,`carousel_order`),
  KEY `knowledge_articles_cover_file_id_fk` (`cover_file_id`),
  KEY `knowledge_articles_author_id_fk` (`author_id`),
  KEY `knowledge_articles_reviewed_by_fk` (`reviewed_by`),
  KEY `knowledge_articles_published_by_fk` (`published_by`),
  CONSTRAINT `knowledge_articles_author_id_fk` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `knowledge_articles_cover_file_id_fk` FOREIGN KEY (`cover_file_id`) REFERENCES `file_assets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `knowledge_articles_published_by_fk` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `knowledge_articles_reviewed_by_fk` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `knowledge_articles_type_chk` CHECK ((`type` in (_utf8mb4'article',_utf8mb4'faq',_utf8mb4'case_study',_utf8mb4'template'))),
  CONSTRAINT `knowledge_articles_visibility_chk` CHECK ((`visibility` in (_utf8mb4'public',_utf8mb4'internal',_utf8mb4'restricted'))),
  CONSTRAINT `knowledge_articles_workflow_chk` CHECK ((`workflow_status` in (_utf8mb4'draft',_utf8mb4'submitted',_utf8mb4'in_review',_utf8mb4'approved',_utf8mb4'published',_utf8mb4'revision_required',_utf8mb4'rejected',_utf8mb4'archived')))
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.knowledge_article_categories
CREATE TABLE IF NOT EXISTS `knowledge_article_categories` (
  `article_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`article_id`,`category_id`),
  KEY `knowledge_article_categories_category_id_idx` (`category_id`),
  CONSTRAINT `knowledge_article_categories_article_id_fk` FOREIGN KEY (`article_id`) REFERENCES `knowledge_articles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `knowledge_article_categories_category_id_fk` FOREIGN KEY (`category_id`) REFERENCES `knowledge_categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.knowledge_article_tags
CREATE TABLE IF NOT EXISTS `knowledge_article_tags` (
  `article_id` bigint unsigned NOT NULL,
  `tag_id` bigint unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`article_id`,`tag_id`),
  KEY `knowledge_article_tags_tag_id_idx` (`tag_id`),
  CONSTRAINT `knowledge_article_tags_article_id_fk` FOREIGN KEY (`article_id`) REFERENCES `knowledge_articles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `knowledge_article_tags_tag_id_fk` FOREIGN KEY (`tag_id`) REFERENCES `knowledge_tags` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.knowledge_categories
CREATE TABLE IF NOT EXISTS `knowledge_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `knowledge_categories_slug_unique` (`slug`),
  KEY `knowledge_categories_parent_active_idx` (`parent_id`,`is_active`),
  CONSTRAINT `knowledge_categories_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `knowledge_categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.knowledge_media
CREATE TABLE IF NOT EXISTS `knowledge_media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `article_id` bigint unsigned DEFAULT NULL,
  `type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caption` text COLLATE utf8mb4_unicode_ci,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `hashtags` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_id` bigint unsigned DEFAULT NULL,
  `external_url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumbnail_file_id` bigint unsigned DEFAULT NULL,
  `workflow_status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `visibility` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'internal',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `likes_count` int unsigned DEFAULT '0',
  `views_count` int unsigned DEFAULT '0',
  `shares_count` int unsigned DEFAULT '0',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `carousel_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` bigint unsigned DEFAULT NULL,
  `published_by` bigint unsigned DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `knowledge_media_article_id_idx` (`article_id`),
  KEY `knowledge_media_publication_idx` (`type`,`workflow_status`,`visibility`,`published_at`),
  KEY `knowledge_media_file_id_fk` (`file_id`),
  KEY `knowledge_media_thumbnail_file_id_fk` (`thumbnail_file_id`),
  KEY `knowledge_media_created_by_fk` (`created_by`),
  KEY `knowledge_media_published_by_fk` (`published_by`),
  CONSTRAINT `knowledge_media_article_id_fk` FOREIGN KEY (`article_id`) REFERENCES `knowledge_articles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `knowledge_media_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `knowledge_media_file_id_fk` FOREIGN KEY (`file_id`) REFERENCES `file_assets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `knowledge_media_published_by_fk` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `knowledge_media_thumbnail_file_id_fk` FOREIGN KEY (`thumbnail_file_id`) REFERENCES `file_assets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `knowledge_media_type_chk` CHECK ((`type` in (_utf8mb4'image',_utf8mb4'poster',_utf8mb4'flyer',_utf8mb4'infographic',_utf8mb4'gallery',_utf8mb4'video_file',_utf8mb4'youtube',_utf8mb4'social_link',_utf8mb4'external_link'))),
  CONSTRAINT `knowledge_media_visibility_chk` CHECK ((`visibility` in (_utf8mb4'public',_utf8mb4'internal',_utf8mb4'restricted'))),
  CONSTRAINT `knowledge_media_workflow_chk` CHECK ((`workflow_status` in (_utf8mb4'draft',_utf8mb4'submitted',_utf8mb4'in_review',_utf8mb4'approved',_utf8mb4'published',_utf8mb4'revision_required',_utf8mb4'rejected',_utf8mb4'archived')))
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.knowledge_tags
CREATE TABLE IF NOT EXISTS `knowledge_tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `knowledge_tags_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.learning_classes
CREATE TABLE IF NOT EXISTS `learning_classes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint unsigned NOT NULL,
  `code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `learning_method` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'online',
  `location_or_link` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_audience` text COLLATE utf8mb4_unicode_ci,
  `registration_open_at` datetime DEFAULT NULL,
  `registration_close_at` datetime DEFAULT NULL,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `capacity` int unsigned DEFAULT NULL,
  `certificate_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `passing_grade` decimal(5,2) NOT NULL DEFAULT '70.00',
  `quiz_weight` decimal(5,2) NOT NULL DEFAULT '40.00',
  `assignment_weight` decimal(5,2) NOT NULL DEFAULT '0.00',
  `post_test_weight` decimal(5,2) NOT NULL DEFAULT '60.00',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `learning_classes_course_code_unique` (`course_id`,`code`),
  KEY `learning_classes_course_status_idx` (`course_id`,`status`),
  KEY `learning_classes_registration_idx` (`registration_open_at`,`registration_close_at`),
  KEY `learning_classes_period_idx` (`start_at`,`end_at`),
  KEY `learning_classes_created_by_fk` (`created_by`),
  KEY `learning_classes_updated_by_fk` (`updated_by`),
  CONSTRAINT `learning_classes_course_id_fk` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `learning_classes_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `learning_classes_updated_by_fk` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `learning_classes_passing_grade_chk` CHECK (((`passing_grade` >= 0.00) and (`passing_grade` <= 100.00))),
  CONSTRAINT `learning_classes_post_test_weight_chk` CHECK ((`post_test_weight` = 60.00)),
  CONSTRAINT `learning_classes_weights_chk` CHECK ((((`quiz_weight` + `assignment_weight`) + `post_test_weight`) = 100.00))
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.learning_modules
CREATE TABLE IF NOT EXISTS `learning_modules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int unsigned NOT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '1',
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `learning_modules_class_order_unique` (`class_id`,`sort_order`),
  CONSTRAINT `learning_modules_class_id_fk` FOREIGN KEY (`class_id`) REFERENCES `learning_classes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=148 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.learning_progress
CREATE TABLE IF NOT EXISTS `learning_progress` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `enrollment_id` bigint unsigned NOT NULL,
  `lesson_id` bigint unsigned NOT NULL,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not_started',
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `progress_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `learning_progress_enrollment_lesson_unique` (`enrollment_id`,`lesson_id`),
  KEY `learning_progress_enrollment_status_idx` (`enrollment_id`,`status`),
  KEY `learning_progress_lesson_id_fk` (`lesson_id`),
  CONSTRAINT `learning_progress_enrollment_id_fk` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `learning_progress_lesson_id_fk` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `learning_progress_percent_chk` CHECK (((`progress_percent` >= 0.00) and (`progress_percent` <= 100.00))),
  CONSTRAINT `learning_progress_status_chk` CHECK ((`status` in (_utf8mb4'not_started',_utf8mb4'in_progress',_utf8mb4'completed')))
) ENGINE=InnoDB AUTO_INCREMENT=222 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.lessons
CREATE TABLE IF NOT EXISTS `lessons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `module_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `file_id` bigint unsigned DEFAULT NULL,
  `external_url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int unsigned NOT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '1',
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `visibility` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'internal',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lessons_module_order_unique` (`module_id`,`sort_order`),
  KEY `lessons_module_status_visibility_idx` (`module_id`,`status`,`visibility`),
  KEY `lessons_slug_idx` (`slug`),
  KEY `lessons_file_id_fk` (`file_id`),
  KEY `lessons_created_by_fk` (`created_by`),
  KEY `lessons_updated_by_fk` (`updated_by`),
  CONSTRAINT `lessons_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `lessons_file_id_fk` FOREIGN KEY (`file_id`) REFERENCES `file_assets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `lessons_module_id_fk` FOREIGN KEY (`module_id`) REFERENCES `learning_modules` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `lessons_updated_by_fk` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `lessons_content_type_chk` CHECK ((`content_type` in (_utf8mb4'text',_utf8mb4'document',_utf8mb4'video',_utf8mb4'link'))),
  CONSTRAINT `lessons_visibility_chk` CHECK ((`visibility` in (_utf8mb4'public',_utf8mb4'internal',_utf8mb4'restricted')))
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.news
CREATE TABLE IF NOT EXISTS `news` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `cover_file_id` bigint unsigned DEFAULT NULL,
  `workflow_status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `visibility` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'internal',
  `published_at` datetime DEFAULT NULL,
  `scheduled_at` datetime DEFAULT NULL,
  `view_count` bigint unsigned NOT NULL DEFAULT '0',
  `author_id` bigint unsigned DEFAULT NULL,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `published_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `news_slug_unique` (`slug`),
  KEY `news_category_publication_idx` (`category_id`,`workflow_status`,`visibility`,`published_at`),
  KEY `news_cover_file_id_fk` (`cover_file_id`),
  KEY `news_author_id_fk` (`author_id`),
  KEY `news_reviewed_by_fk` (`reviewed_by`),
  KEY `news_published_by_fk` (`published_by`),
  CONSTRAINT `news_author_id_fk` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `news_category_id_fk` FOREIGN KEY (`category_id`) REFERENCES `news_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `news_cover_file_id_fk` FOREIGN KEY (`cover_file_id`) REFERENCES `file_assets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `news_published_by_fk` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `news_reviewed_by_fk` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `news_visibility_chk` CHECK ((`visibility` in (_utf8mb4'public',_utf8mb4'internal',_utf8mb4'restricted'))),
  CONSTRAINT `news_workflow_chk` CHECK ((`workflow_status` in (_utf8mb4'draft',_utf8mb4'submitted',_utf8mb4'in_review',_utf8mb4'approved',_utf8mb4'published',_utf8mb4'revision_required',_utf8mb4'rejected',_utf8mb4'archived')))
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.news_categories
CREATE TABLE IF NOT EXISTS `news_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `news_categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `channel` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'database',
  `type` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `data` json DEFAULT NULL,
  `read_at` datetime DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_read_created_idx` (`user_id`,`read_at`,`created_at`),
  KEY `notifications_channel_sent_idx` (`channel`,`sent_at`),
  CONSTRAINT `notifications_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=183 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.organization_units
CREATE TABLE IF NOT EXISTS `organization_units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `organization_units_code_unique` (`code`),
  KEY `organization_units_parent_id_idx` (`parent_id`),
  KEY `organization_units_type_active_idx` (`type`,`is_active`),
  CONSTRAINT `organization_units_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `organization_units` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.password_resets
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `token_hash` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `password_resets_token_hash_unique` (`token_hash`),
  KEY `password_resets_user_expires_idx` (`user_id`,`expires_at`),
  CONSTRAINT `password_resets_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_system` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_key_unique` (`key`),
  KEY `permissions_domain_idx` (`domain`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.pre_tests
CREATE TABLE IF NOT EXISTS `pre_tests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint unsigned NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `attempt_limit` int unsigned NOT NULL DEFAULT '1',
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pre_tests_class_id_unique` (`class_id`),
  CONSTRAINT `pre_tests_class_id_fk` FOREIGN KEY (`class_id`) REFERENCES `learning_classes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.procurement_roles
CREATE TABLE IF NOT EXISTS `procurement_roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `procurement_roles_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.procurement_stages
CREATE TABLE IF NOT EXISTS `procurement_stages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.profile_change_requests
CREATE TABLE IF NOT EXISTS `profile_change_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `requested_payload` json NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `review_notes` text COLLATE utf8mb4_unicode_ci,
  `applied_at` datetime DEFAULT NULL,
  `archived_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `profile_change_requests_user_status_idx` (`user_id`,`status`),
  KEY `profile_change_requests_status_created_idx` (`status`,`created_at`),
  KEY `profile_change_requests_reviewed_by_fk` (`reviewed_by`),
  CONSTRAINT `profile_change_requests_reviewed_by_fk` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `profile_change_requests_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `profile_change_requests_status_chk` CHECK ((`status` in (_utf8mb4'draft',_utf8mb4'submitted',_utf8mb4'in_review',_utf8mb4'revision_required',_utf8mb4'approved',_utf8mb4'rejected',_utf8mb4'cancelled',_utf8mb4'archived')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.profile_verifications
CREATE TABLE IF NOT EXISTS `profile_verifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `verification_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `requested_by` bigint unsigned DEFAULT NULL,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `profile_verifications_user_type_status_idx` (`user_id`,`verification_type`,`status`),
  KEY `profile_verifications_status_created_idx` (`status`,`created_at`),
  KEY `profile_verifications_requested_by_fk` (`requested_by`),
  KEY `profile_verifications_reviewed_by_fk` (`reviewed_by`),
  CONSTRAINT `profile_verifications_requested_by_fk` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `profile_verifications_reviewed_by_fk` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `profile_verifications_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `profile_verifications_status_chk` CHECK ((`status` in (_utf8mb4'pending',_utf8mb4'in_review',_utf8mb4'approved',_utf8mb4'rejected',_utf8mb4'revision_required')))
) ENGINE=InnoDB AUTO_INCREMENT=175 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.programs
CREATE TABLE IF NOT EXISTS `programs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `target_audience` text COLLATE utf8mb4_unicode_ci,
  `prerequisites` text COLLATE utf8mb4_unicode_ci,
  `learning_objectives` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `visibility` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'internal',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `cover_file_id` bigint unsigned DEFAULT NULL,
  `cover_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `published_by` bigint unsigned DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `programs_slug_unique` (`slug`),
  KEY `programs_status_visibility_published_idx` (`status`,`visibility`,`published_at`),
  KEY `programs_cover_file_id_fk` (`cover_file_id`),
  KEY `programs_created_by_fk` (`created_by`),
  KEY `programs_updated_by_fk` (`updated_by`),
  KEY `programs_published_by_fk` (`published_by`),
  CONSTRAINT `programs_cover_file_id_fk` FOREIGN KEY (`cover_file_id`) REFERENCES `file_assets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `programs_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `programs_published_by_fk` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `programs_updated_by_fk` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.provider_profiles
CREATE TABLE IF NOT EXISTS `provider_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `business_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_form` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_field` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_identifier` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registrant_position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `responsible_person` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_address` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `provider_profiles_user_id_unique` (`user_id`),
  CONSTRAINT `provider_profiles_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.public_profiles
CREATE TABLE IF NOT EXISTS `public_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `national_identifier` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation_status` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `organization_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interest_areas` json DEFAULT NULL,
  `usage_purpose` json DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `public_profiles_user_id_unique` (`user_id`),
  CONSTRAINT `public_profiles_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.quizzes
CREATE TABLE IF NOT EXISTS `quizzes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint unsigned NOT NULL,
  `lesson_id` bigint unsigned DEFAULT NULL,
  `type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `instructions` text COLLATE utf8mb4_unicode_ci,
  `attempt_limit` int unsigned NOT NULL DEFAULT '1',
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `quizzes_class_type_status_idx` (`class_id`,`type`,`status`),
  KEY `quizzes_lesson_id_idx` (`lesson_id`),
  CONSTRAINT `quizzes_class_id_fk` FOREIGN KEY (`class_id`) REFERENCES `learning_classes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `quizzes_lesson_id_fk` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `quizzes_type_chk` CHECK ((`type` in (_utf8mb4'pre_test',_utf8mb4'lesson_quiz',_utf8mb4'post_test')))
) ENGINE=InnoDB AUTO_INCREMENT=309 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.quiz_answers
CREATE TABLE IF NOT EXISTS `quiz_answers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attempt_id` bigint unsigned NOT NULL,
  `question_id` bigint unsigned NOT NULL,
  `answer_payload` json DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `score_awarded` decimal(5,2) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quiz_answers_attempt_question_unique` (`attempt_id`,`question_id`),
  KEY `quiz_answers_question_id_fk` (`question_id`),
  CONSTRAINT `quiz_answers_attempt_id_fk` FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `quiz_answers_question_id_fk` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `quiz_answers_score_awarded_chk` CHECK (((`score_awarded` is null) or (`score_awarded` >= 0.00)))
) ENGINE=InnoDB AUTO_INCREMENT=2512 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.quiz_attempts
CREATE TABLE IF NOT EXISTS `quiz_attempts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` bigint unsigned NOT NULL,
  `enrollment_id` bigint unsigned NOT NULL,
  `attempt_number` int unsigned NOT NULL,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'started',
  `started_at` datetime NOT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `graded_at` datetime DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `invalidated_by` bigint unsigned DEFAULT NULL,
  `invalidated_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quiz_attempts_unique` (`quiz_id`,`enrollment_id`,`attempt_number`),
  KEY `quiz_attempts_enrollment_status_idx` (`enrollment_id`,`status`),
  KEY `quiz_attempts_invalidated_by_fk` (`invalidated_by`),
  CONSTRAINT `quiz_attempts_enrollment_id_fk` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `quiz_attempts_invalidated_by_fk` FOREIGN KEY (`invalidated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `quiz_attempts_quiz_id_fk` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `quiz_attempts_score_chk` CHECK (((`score` is null) or ((`score` >= 0.00) and (`score` <= 100.00)))),
  CONSTRAINT `quiz_attempts_status_chk` CHECK ((`status` in (_utf8mb4'started',_utf8mb4'submitted',_utf8mb4'graded',_utf8mb4'invalidated')))
) ENGINE=InnoDB AUTO_INCREMENT=284 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.quiz_options
CREATE TABLE IF NOT EXISTS `quiz_options` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `question_id` bigint unsigned NOT NULL,
  `option_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quiz_options_question_order_unique` (`question_id`,`sort_order`),
  CONSTRAINT `quiz_options_question_id_fk` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5265 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.quiz_questions
CREATE TABLE IF NOT EXISTS `quiz_questions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` bigint unsigned NOT NULL,
  `type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prompt` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `points` decimal(5,2) NOT NULL DEFAULT '1.00',
  `sort_order` int unsigned NOT NULL,
  `explanation` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quiz_questions_quiz_order_unique` (`quiz_id`,`sort_order`),
  CONSTRAINT `quiz_questions_quiz_id_fk` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `quiz_questions_points_chk` CHECK ((`points` >= 0.00))
) ENGINE=InnoDB AUTO_INCREMENT=1345 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.regulations
CREATE TABLE IF NOT EXISTS `regulations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_year` smallint unsigned NOT NULL,
  `issuer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `official_date` datetime DEFAULT NULL,
  `effective_date` datetime DEFAULT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `keywords` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `official_source_url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `workflow_status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `legal_status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `visibility` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'internal',
  `is_featured` tinyint(1) DEFAULT '0',
  `current_version_id` bigint unsigned DEFAULT NULL,
  `cover_file_id` int unsigned DEFAULT NULL,
  `cover_image_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `published_by` bigint unsigned DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `archived_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `regulations_slug_unique` (`slug`),
  UNIQUE KEY `regulations_document_unique` (`document_number`,`document_year`,`issuer_name`),
  KEY `regulations_category_workflow_legal_visibility_idx` (`category_id`,`workflow_status`,`legal_status`,`visibility`),
  KEY `regulations_published_at_idx` (`published_at`),
  KEY `regulations_created_by_fk` (`created_by`),
  KEY `regulations_reviewed_by_fk` (`reviewed_by`),
  KEY `regulations_published_by_fk` (`published_by`),
  KEY `regulations_current_version_id_fk` (`current_version_id`),
  CONSTRAINT `regulations_category_id_fk` FOREIGN KEY (`category_id`) REFERENCES `regulation_categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `regulations_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `regulations_current_version_id_fk` FOREIGN KEY (`current_version_id`) REFERENCES `regulation_versions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `regulations_published_by_fk` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `regulations_reviewed_by_fk` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `regulations_legal_status_chk` CHECK ((`legal_status` in (_utf8mb4'active',_utf8mb4'amended',_utf8mb4'revoked',_utf8mb4'expired'))),
  CONSTRAINT `regulations_visibility_chk` CHECK ((`visibility` in (_utf8mb4'public',_utf8mb4'internal',_utf8mb4'restricted'))),
  CONSTRAINT `regulations_workflow_chk` CHECK ((`workflow_status` in (_utf8mb4'draft',_utf8mb4'submitted',_utf8mb4'in_review',_utf8mb4'approved',_utf8mb4'published',_utf8mb4'revision_required',_utf8mb4'rejected',_utf8mb4'archived')))
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.regulation_categories
CREATE TABLE IF NOT EXISTS `regulation_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon_class` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'solar:document-outline',
  `icon_file_id` int unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `regulation_categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.regulation_competencies
CREATE TABLE IF NOT EXISTS `regulation_competencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `regulation_id` bigint unsigned NOT NULL,
  `competency_id` bigint unsigned NOT NULL,
  `relevance` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rc_unique` (`regulation_id`,`competency_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.regulation_downloads
CREATE TABLE IF NOT EXISTS `regulation_downloads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `regulation_id` bigint unsigned NOT NULL,
  `version_id` bigint unsigned DEFAULT NULL,
  `file_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `access_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `regulation_downloads_regulation_created_idx` (`regulation_id`,`created_at`),
  KEY `regulation_downloads_user_created_idx` (`user_id`,`created_at`),
  KEY `regulation_downloads_version_id_fk` (`version_id`),
  KEY `regulation_downloads_file_id_fk` (`file_id`),
  CONSTRAINT `regulation_downloads_file_id_fk` FOREIGN KEY (`file_id`) REFERENCES `file_assets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `regulation_downloads_regulation_id_fk` FOREIGN KEY (`regulation_id`) REFERENCES `regulations` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `regulation_downloads_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `regulation_downloads_version_id_fk` FOREIGN KEY (`version_id`) REFERENCES `regulation_versions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=264 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.regulation_procurement_stages
CREATE TABLE IF NOT EXISTS `regulation_procurement_stages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `regulation_id` bigint unsigned NOT NULL,
  `procurement_stage_id` bigint unsigned NOT NULL,
  `relevance` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rps_unique` (`regulation_id`,`procurement_stage_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.regulation_relations
CREATE TABLE IF NOT EXISTS `regulation_relations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `source_regulation_id` bigint unsigned NOT NULL,
  `target_regulation_id` bigint unsigned NOT NULL,
  `relation_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `regulation_relations_unique` (`source_regulation_id`,`target_regulation_id`,`relation_type`),
  KEY `regulation_relations_target_idx` (`target_regulation_id`),
  KEY `regulation_relations_created_by_fk` (`created_by`),
  CONSTRAINT `regulation_relations_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `regulation_relations_source_id_fk` FOREIGN KEY (`source_regulation_id`) REFERENCES `regulations` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `regulation_relations_target_id_fk` FOREIGN KEY (`target_regulation_id`) REFERENCES `regulations` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `regulation_relations_type_chk` CHECK ((`relation_type` in (_utf8mb4'amends',_utf8mb4'amended_by',_utf8mb4'revokes',_utf8mb4'revoked_by',_utf8mb4'replaces',_utf8mb4'replaced_by',_utf8mb4'implements',_utf8mb4'implemented_by',_utf8mb4'references',_utf8mb4'referenced_by')))
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.regulation_versions
CREATE TABLE IF NOT EXISTS `regulation_versions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `regulation_id` bigint unsigned NOT NULL,
  `version_number` int unsigned NOT NULL,
  `file_id` bigint unsigned DEFAULT NULL,
  `changelog` text COLLATE utf8mb4_unicode_ci,
  `is_current` tinyint(1) NOT NULL DEFAULT '0',
  `uploaded_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `regulation_versions_regulation_version_unique` (`regulation_id`,`version_number`),
  KEY `regulation_versions_regulation_current_idx` (`regulation_id`,`is_current`),
  KEY `regulation_versions_file_id_fk` (`file_id`),
  KEY `regulation_versions_uploaded_by_fk` (`uploaded_by`),
  CONSTRAINT `regulation_versions_file_id_fk` FOREIGN KEY (`file_id`) REFERENCES `file_assets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `regulation_versions_regulation_id_fk` FOREIGN KEY (`regulation_id`) REFERENCES `regulations` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `regulation_versions_uploaded_by_fk` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_system` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.role_permissions
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  `granted_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `role_permissions_permission_id_idx` (`permission_id`),
  KEY `role_permissions_granted_by_fk` (`granted_by`),
  CONSTRAINT `role_permissions_granted_by_fk` FOREIGN KEY (`granted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `role_permissions_permission_id_fk` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `role_permissions_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.role_requests
CREATE TABLE IF NOT EXISTS `role_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `requested_role_id` bigint unsigned NOT NULL,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `review_notes` text COLLATE utf8mb4_unicode_ci,
  `archived_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `role_requests_user_status_idx` (`user_id`,`status`),
  KEY `role_requests_role_status_idx` (`requested_role_id`,`status`),
  KEY `role_requests_status_created_idx` (`status`,`created_at`),
  KEY `role_requests_reviewed_by_fk` (`reviewed_by`),
  CONSTRAINT `role_requests_requested_role_id_fk` FOREIGN KEY (`requested_role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `role_requests_reviewed_by_fk` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `role_requests_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `role_requests_status_chk` CHECK ((`status` in (_utf8mb4'draft',_utf8mb4'submitted',_utf8mb4'in_review',_utf8mb4'revision_required',_utf8mb4'approved',_utf8mb4'rejected',_utf8mb4'cancelled',_utf8mb4'archived')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `email_verified_at` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `session_version` bigint unsigned NOT NULL DEFAULT '1',
  `status_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`),
  KEY `users_account_status_idx` (`account_status`),
  KEY `users_profile_type_idx` (`profile_type`),
  KEY `users_email_verified_at_idx` (`email_verified_at`),
  KEY `users_deleted_at_idx` (`deleted_at`),
  CONSTRAINT `users_account_status_chk` CHECK ((`account_status` in (_utf8mb4'draft',_utf8mb4'submitted',_utf8mb4'pending_verification',_utf8mb4'revision_required',_utf8mb4'active',_utf8mb4'rejected',_utf8mb4'inactive',_utf8mb4'suspended',_utf8mb4'archived'))),
  CONSTRAINT `users_profile_type_chk` CHECK ((`profile_type` in (_utf8mb4'asn',_utf8mb4'provider',_utf8mb4'academic',_utf8mb4'public')))
) ENGINE=InnoDB AUTO_INCREMENT=267 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.user_procurement_roles
CREATE TABLE IF NOT EXISTS `user_procurement_roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `procurement_role_id` bigint unsigned NOT NULL,
  `verification_status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'self_declared',
  `verified_by` bigint unsigned DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `assignment_start_at` datetime DEFAULT NULL,
  `assignment_end_at` datetime DEFAULT NULL,
  `evidence_file_id` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_procurement_roles_unique` (`user_id`,`procurement_role_id`),
  KEY `user_procurement_roles_verification_status_idx` (`verification_status`),
  KEY `user_procurement_roles_role_id_fk` (`procurement_role_id`),
  KEY `user_procurement_roles_verified_by_fk` (`verified_by`),
  KEY `user_procurement_roles_evidence_file_id_fk` (`evidence_file_id`),
  CONSTRAINT `user_procurement_roles_evidence_file_id_fk` FOREIGN KEY (`evidence_file_id`) REFERENCES `file_assets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `user_procurement_roles_role_id_fk` FOREIGN KEY (`procurement_role_id`) REFERENCES `procurement_roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `user_procurement_roles_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `user_procurement_roles_verified_by_fk` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `user_procurement_roles_status_chk` CHECK ((`verification_status` in (_utf8mb4'self_declared',_utf8mb4'pending',_utf8mb4'verified',_utf8mb4'rejected')))
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.user_profiles
CREATE TABLE IF NOT EXISTS `user_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `address_line` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `province_code` varchar(13) COLLATE utf8mb4_unicode_ci NOT NULL,
  `regency_code` varchar(13) COLLATE utf8mb4_unicode_ci NOT NULL,
  `district_code` varchar(13) COLLATE utf8mb4_unicode_ci NOT NULL,
  `village_code` varchar(13) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_completion_status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_profiles_user_id_unique` (`user_id`),
  KEY `user_profiles_wilayah_idx` (`province_code`,`regency_code`,`district_code`),
  KEY `user_profiles_village_code_idx` (`village_code`),
  KEY `user_profiles_regency_code_fk` (`regency_code`),
  KEY `user_profiles_district_code_fk` (`district_code`),
  CONSTRAINT `user_profiles_district_code_fk` FOREIGN KEY (`district_code`) REFERENCES `wilayah` (`kode`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `user_profiles_province_code_fk` FOREIGN KEY (`province_code`) REFERENCES `wilayah` (`kode`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `user_profiles_regency_code_fk` FOREIGN KEY (`regency_code`) REFERENCES `wilayah` (`kode`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `user_profiles_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `user_profiles_village_code_fk` FOREIGN KEY (`village_code`) REFERENCES `wilayah` (`kode`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=182 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.user_roles
CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `granted_by` bigint unsigned DEFAULT NULL,
  `granted_at` datetime NOT NULL,
  `revoked_by` bigint unsigned DEFAULT NULL,
  `revoked_at` datetime DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_roles_user_role_unique` (`user_id`,`role_id`),
  KEY `user_roles_user_status_idx` (`user_id`,`status`),
  KEY `user_roles_role_status_idx` (`role_id`,`status`),
  KEY `user_roles_granted_by_fk` (`granted_by`),
  KEY `user_roles_revoked_by_fk` (`revoked_by`),
  CONSTRAINT `user_roles_granted_by_fk` FOREIGN KEY (`granted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `user_roles_revoked_by_fk` FOREIGN KEY (`revoked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `user_roles_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `user_roles_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `user_roles_status_chk` CHECK ((`status` in (_utf8mb4'active',_utf8mb4'revoked',_utf8mb4'expired')))
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.wilayah
CREATE TABLE IF NOT EXISTS `wilayah` (
  `kode` varchar(13) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`kode`),
  KEY `wilayah_name_idx` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_begawipbj.workflow_transitions
CREATE TABLE IF NOT EXISTS `workflow_transitions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `resource_type` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resource_id` bigint unsigned NOT NULL,
  `from_state` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_state` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actor_id` bigint unsigned DEFAULT NULL,
  `actor_type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_transitions_resource_created_idx` (`resource_type`,`resource_id`,`created_at`),
  KEY `workflow_transitions_actor_created_idx` (`actor_id`,`created_at`),
  KEY `workflow_transitions_to_state_created_idx` (`to_state`,`created_at`),
  CONSTRAINT `workflow_transitions_actor_id_fk` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `workflow_transitions_actor_type_chk` CHECK ((`actor_type` in (_utf8mb4'user',_utf8mb4'system')))
) ENGINE=InnoDB AUTO_INCREMENT=248 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
