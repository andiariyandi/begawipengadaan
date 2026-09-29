-- BegawiPBJ Fresh Database Schema v2
-- MySQL 8.4+ | InnoDB | utf8mb4 | Baseline baru, bukan migration legacy
-- Jalankan pada server MySQL development baru / database target baru.
-- Setelah file ini berhasil, jalankan begawipbj-dummy-data-v3.sql untuk data dummy QA.

CREATE DATABASE IF NOT EXISTS `db_begawipbj`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_0900_ai_ci;

USE `db_begawipbj`;
SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- =============================================================================
-- 01. IDENTITY, RBAC, WILAYAH, ORGANISASI, DAN PROFIL
-- =============================================================================

CREATE TABLE users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    account_status VARCHAR(32) NOT NULL DEFAULT 'pending',
    email_verified_at DATETIME NULL,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username),
    UNIQUE KEY uq_users_email (email),
    KEY ix_users_status_created (account_status, created_at)
) ENGINE=InnoDB;

CREATE TABLE roles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(64) NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    is_system TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_roles_code (code)
) ENGINE=InnoDB;

CREATE TABLE permissions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(128) NOT NULL,
    module VARCHAR(64) NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_permissions_code (code),
    KEY ix_permissions_module (module)
) ENGINE=InnoDB;

CREATE TABLE role_permissions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    role_id BIGINT UNSIGNED NOT NULL,
    permission_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_role_permission (role_id, permission_id),
    KEY ix_rp_permission (permission_id),
    CONSTRAINT fk_rp_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT,
    CONSTRAINT fk_rp_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE user_roles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    assigned_by BIGINT UNSIGNED NULL,
    assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_user_role (user_id, role_id),
    KEY ix_ur_role (role_id),
    KEY ix_ur_assigner (assigned_by),
    CONSTRAINT fk_ur_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_ur_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT,
    CONSTRAINT fk_ur_assigner FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Standar master wilayah: kode administratif berjenjang tanpa parent_code/level.
-- Contoh: 18 → 18.01 → 18.01.04 → 18.01.04.2001.
CREATE TABLE wilayah (
    kode VARCHAR(13) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    PRIMARY KEY (kode),
    KEY wilayah_name_idx (nama)
) ENGINE=InnoDB;

CREATE TABLE organization_units (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    parent_id BIGINT UNSIGNED NULL,
    code VARCHAR(64) NOT NULL,
    name VARCHAR(255) NOT NULL,
    unit_type VARCHAR(32) NOT NULL DEFAULT 'opd',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_org_code (code),
    KEY ix_org_parent (parent_id),
    KEY ix_org_type_active (unit_type, is_active),
    CONSTRAINT fk_org_parent FOREIGN KEY (parent_id) REFERENCES organization_units(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE user_organizations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    organization_unit_id BIGINT UNSIGNED NOT NULL,
    is_primary TINYINT(1) NOT NULL DEFAULT 0,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_user_org_start (user_id, organization_unit_id, start_date),
    KEY ix_uo_org (organization_unit_id),
    KEY ix_uo_user_primary (user_id, is_primary),
    CONSTRAINT fk_uo_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_uo_org FOREIGN KEY (organization_unit_id) REFERENCES organization_units(id) ON DELETE RESTRICT,
    CONSTRAINT ck_uo_dates CHECK (end_date IS NULL OR end_date >= start_date)
) ENGINE=InnoDB;

-- path bernilai NULL apabila metadata file tersedia tetapi berkas fisik belum tersedia.
-- UNIQUE mengizinkan banyak NULL tetapi menolak path fisik yang sama.
CREATE TABLE file_assets (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    disk VARCHAR(32) NOT NULL DEFAULT 'local_private',
    path VARCHAR(500) NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(150) NOT NULL,
    extension VARCHAR(20) NOT NULL,
    size_bytes BIGINT UNSIGNED NOT NULL DEFAULT 0,
    checksum_sha256 CHAR(64) NULL,
    visibility VARCHAR(16) NOT NULL DEFAULT 'private',
    uploaded_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_file_path (path),
    KEY ix_file_uploader (uploaded_by),
    KEY ix_file_mime (mime_type),
    KEY ix_file_checksum (checksum_sha256),
    CONSTRAINT fk_file_uploader FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE user_profiles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    profile_category VARCHAR(32) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    title_prefix VARCHAR(30) NULL,
    title_suffix VARCHAR(80) NULL,
    phone VARCHAR(25) NULL,
    avatar_file_asset_id BIGINT UNSIGNED NULL,
    province_code VARCHAR(13) NULL,
    regency_code VARCHAR(13) NULL,
    district_code VARCHAR(13) NULL,
    village_code VARCHAR(13) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_profile_user (user_id),
    KEY ix_profile_category (profile_category),
    KEY ix_profile_avatar (avatar_file_asset_id),
    KEY ix_profile_province (province_code),
    KEY ix_profile_regency (regency_code),
    KEY ix_profile_district (district_code),
    KEY ix_profile_village (village_code),
    CONSTRAINT fk_profile_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_profile_avatar FOREIGN KEY (avatar_file_asset_id) REFERENCES file_assets(id) ON DELETE SET NULL,
    CONSTRAINT fk_profile_province FOREIGN KEY (province_code) REFERENCES wilayah(kode) ON DELETE RESTRICT,
    CONSTRAINT fk_profile_regency FOREIGN KEY (regency_code) REFERENCES wilayah(kode) ON DELETE RESTRICT,
    CONSTRAINT fk_profile_district FOREIGN KEY (district_code) REFERENCES wilayah(kode) ON DELETE RESTRICT,
    CONSTRAINT fk_profile_village FOREIGN KEY (village_code) REFERENCES wilayah(kode) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE user_asn_profiles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    nip VARCHAR(32) NULL,
    employment_status VARCHAR(32) NOT NULL,
    position_name VARCHAR(150) NULL,
    rank_name VARCHAR(100) NULL,
    grade VARCHAR(50) NULL,
    organization_unit_id BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_asn_user (user_id),
    UNIQUE KEY uq_asn_nip (nip),
    KEY ix_asn_org (organization_unit_id),
    CONSTRAINT fk_asn_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_asn_org FOREIGN KEY (organization_unit_id) REFERENCES organization_units(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE user_provider_profiles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    nib VARCHAR(32) NULL,
    business_name VARCHAR(180) NOT NULL,
    responsible_person VARCHAR(150) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_provider_user (user_id),
    UNIQUE KEY uq_provider_nib (nib),
    CONSTRAINT fk_provider_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE user_academic_profiles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    institution VARCHAR(180) NOT NULL,
    expertise VARCHAR(180) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_academic_user (user_id),
    CONSTRAINT fk_academic_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE user_public_profiles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    identity_number VARCHAR(64) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_public_user (user_id),
    CONSTRAINT fk_public_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =============================================================================
-- 02. SHARED: APPROVAL, AUDIT, NOTIFIKASI
-- =============================================================================

CREATE TABLE approvals (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    subject_type VARCHAR(64) NOT NULL,
    subject_id BIGINT UNSIGNED NOT NULL,
    workflow_status VARCHAR(32) NOT NULL DEFAULT 'draft',
    submitted_by BIGINT UNSIGNED NOT NULL,
    submitted_at DATETIME NULL,
    current_reviewer_id BIGINT UNSIGNED NULL,
    decided_by BIGINT UNSIGNED NULL,
    decided_at DATETIME NULL,
    decision_notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ix_approvals_subject (subject_type, subject_id, workflow_status),
    KEY ix_approvals_queue (workflow_status, current_reviewer_id),
    KEY ix_approvals_submitter (submitted_by),
    KEY ix_approvals_decider (decided_by),
    CONSTRAINT fk_approvals_submitter FOREIGN KEY (submitted_by) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_approvals_reviewer FOREIGN KEY (current_reviewer_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_approvals_decider FOREIGN KEY (decided_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE approval_histories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    approval_id BIGINT UNSIGNED NOT NULL,
    from_status VARCHAR(32) NULL,
    to_status VARCHAR(32) NOT NULL,
    action VARCHAR(64) NOT NULL,
    acted_by BIGINT UNSIGNED NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ix_approval_history_time (approval_id, created_at),
    KEY ix_approval_actor (acted_by),
    CONSTRAINT fk_ah_approval FOREIGN KEY (approval_id) REFERENCES approvals(id) ON DELETE RESTRICT,
    CONSTRAINT fk_ah_actor FOREIGN KEY (acted_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    actor_user_id BIGINT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    subject_type VARCHAR(64) NULL,
    subject_id BIGINT UNSIGNED NULL,
    route_name VARCHAR(150) NULL,
    request_id VARCHAR(64) NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    before_data JSON NULL,
    after_data JSON NULL,
    context_data JSON NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ix_audit_actor (actor_user_id, created_at),
    KEY ix_audit_subject (subject_type, subject_id, created_at),
    KEY ix_audit_action (action, created_at),
    KEY ix_audit_request (request_id),
    CONSTRAINT fk_audit_actor FOREIGN KEY (actor_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE notifications (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NULL,
    channel VARCHAR(32) NOT NULL,
    notification_type VARCHAR(64) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data JSON NULL,
    read_at DATETIME NULL,
    sent_at DATETIME NULL,
    failed_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ix_notifications_inbox (user_id, read_at, created_at),
    KEY ix_notification_type (notification_type),
    CONSTRAINT fk_notification_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =============================================================================
-- 03. ARENA BELAJAR
-- =============================================================================

CREATE TABLE programs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(64) NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    summary TEXT NULL,
    description LONGTEXT NULL,
    cover_file_asset_id BIGINT UNSIGNED NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'draft',
    created_by BIGINT UNSIGNED NOT NULL,
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_program_code (code),
    UNIQUE KEY uq_program_slug (slug),
    KEY ix_program_publish (status, published_at),
    KEY ix_program_creator (created_by),
    KEY ix_program_cover (cover_file_asset_id),
    CONSTRAINT fk_program_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_program_cover FOREIGN KEY (cover_file_asset_id) REFERENCES file_assets(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE courses (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    program_id BIGINT UNSIGNED NULL,
    code VARCHAR(64) NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    summary TEXT NULL,
    description LONGTEXT NULL,
    learning_outcomes TEXT NULL,
    estimated_duration_minutes INT UNSIGNED NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'draft',
    created_by BIGINT UNSIGNED NOT NULL,
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_course_code (code),
    UNIQUE KEY uq_course_slug (slug),
    KEY ix_course_program (program_id),
    KEY ix_course_publish (status, published_at),
    KEY ix_course_creator (created_by),
    CONSTRAINT fk_course_program FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE RESTRICT,
    CONSTRAINT fk_course_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE learning_classes (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    course_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(64) NOT NULL,
    title VARCHAR(255) NOT NULL,
    delivery_mode VARCHAR(16) NOT NULL DEFAULT 'online',
    start_at DATETIME NOT NULL,
    end_at DATETIME NOT NULL,
    enrollment_open_at DATETIME NULL,
    enrollment_close_at DATETIME NULL,
    quota INT UNSIGNED NOT NULL DEFAULT 0,
    status VARCHAR(32) NOT NULL DEFAULT 'draft',
    passing_grade DECIMAL(5,2) NOT NULL DEFAULT 70.00,
    quiz_weight DECIMAL(5,2) NOT NULL DEFAULT 50.00,
    assignment_weight DECIMAL(5,2) NOT NULL DEFAULT 50.00,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_learning_class_code (code),
    KEY ix_lc_course (course_id),
    KEY ix_lc_status_start (status, start_at),
    KEY ix_lc_creator (created_by),
    CONSTRAINT fk_lc_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE RESTRICT,
    CONSTRAINT fk_lc_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT ck_lc_dates CHECK (end_at >= start_at),
    CONSTRAINT ck_lc_grade CHECK (passing_grade BETWEEN 0 AND 100),
    CONSTRAINT ck_lc_weights CHECK (quiz_weight >= 0 AND assignment_weight >= 0 AND quiz_weight + assignment_weight = 100)
) ENGINE=InnoDB;

CREATE TABLE class_trainers (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    learning_class_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    role VARCHAR(32) NOT NULL DEFAULT 'lead',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_class_trainer (learning_class_id, user_id),
    KEY ix_class_trainer_user (user_id),
    CONSTRAINT fk_ct_class FOREIGN KEY (learning_class_id) REFERENCES learning_classes(id) ON DELETE RESTRICT,
    CONSTRAINT fk_ct_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE class_evaluators (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    learning_class_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_class_evaluator (learning_class_id, user_id),
    KEY ix_class_evaluator_user (user_id),
    CONSTRAINT fk_ce_class FOREIGN KEY (learning_class_id) REFERENCES learning_classes(id) ON DELETE RESTRICT,
    CONSTRAINT fk_ce_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE learning_modules (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    course_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    sequence_order INT UNSIGNED NOT NULL DEFAULT 0,
    is_required TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ix_lm_course_order (course_id, sequence_order),
    CONSTRAINT fk_lm_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE lessons (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    learning_module_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    content_type VARCHAR(16) NOT NULL DEFAULT 'text',
    content_text LONGTEXT NULL,
    content_url VARCHAR(500) NULL,
    sequence_order INT UNSIGNED NOT NULL DEFAULT 0,
    duration_minutes INT UNSIGNED NULL,
    is_required TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ix_lesson_module_order (learning_module_id, sequence_order),
    CONSTRAINT fk_lesson_module FOREIGN KEY (learning_module_id) REFERENCES learning_modules(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE lesson_materials (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    lesson_id BIGINT UNSIGNED NOT NULL,
    file_asset_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NULL,
    sequence_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_lesson_file (lesson_id, file_asset_id),
    KEY ix_lmat_file (file_asset_id),
    CONSTRAINT fk_lmat_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE RESTRICT,
    CONSTRAINT fk_lmat_file FOREIGN KEY (file_asset_id) REFERENCES file_assets(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE enrollments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    learning_class_id BIGINT UNSIGNED NOT NULL,
    enrollment_status VARCHAR(32) NOT NULL DEFAULT 'pending',
    enrolled_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    approved_by BIGINT UNSIGNED NULL,
    approved_at DATETIME NULL,
    completed_at DATETIME NULL,
    final_status VARCHAR(32) NOT NULL DEFAULT 'in_progress',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_enroll_user_class (user_id, learning_class_id),
    KEY ix_enroll_class_status (learning_class_id, enrollment_status),
    KEY ix_enroll_user_final (user_id, final_status),
    KEY ix_enroll_approver (approved_by),
    CONSTRAINT fk_enroll_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_enroll_class FOREIGN KEY (learning_class_id) REFERENCES learning_classes(id) ON DELETE RESTRICT,
    CONSTRAINT fk_enroll_approver FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE learning_progress (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    enrollment_id BIGINT UNSIGNED NOT NULL,
    lesson_id BIGINT UNSIGNED NOT NULL,
    started_at DATETIME NULL,
    completed_at DATETIME NULL,
    last_accessed_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_progress_enrollment_lesson (enrollment_id, lesson_id),
    KEY ix_progress_enrollment_completed (enrollment_id, completed_at),
    KEY ix_progress_lesson (lesson_id),
    CONSTRAINT fk_progress_enroll FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE RESTRICT,
    CONSTRAINT fk_progress_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE quizzes (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    learning_class_id BIGINT UNSIGNED NULL,
    learning_module_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    instructions TEXT NULL,
    attempt_limit INT UNSIGNED NOT NULL DEFAULT 1,
    duration_minutes INT UNSIGNED NULL,
    passing_grade DECIMAL(5,2) NOT NULL DEFAULT 70.00,
    available_from DATETIME NULL,
    available_until DATETIME NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'draft',
    created_by BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ix_quiz_class (learning_class_id),
    KEY ix_quiz_module (learning_module_id),
    KEY ix_quiz_creator (created_by),
    KEY ix_quiz_available (status, available_from, available_until),
    CONSTRAINT fk_quiz_class FOREIGN KEY (learning_class_id) REFERENCES learning_classes(id) ON DELETE RESTRICT,
    CONSTRAINT fk_quiz_module FOREIGN KEY (learning_module_id) REFERENCES learning_modules(id) ON DELETE RESTRICT,
    CONSTRAINT fk_quiz_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT ck_quiz_target CHECK (learning_class_id IS NOT NULL OR learning_module_id IS NOT NULL),
    CONSTRAINT ck_quiz_pass CHECK (passing_grade BETWEEN 0 AND 100)
) ENGINE=InnoDB;

CREATE TABLE questions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    quiz_id BIGINT UNSIGNED NOT NULL,
    question_type VARCHAR(32) NOT NULL DEFAULT 'multiple_choice',
    question_text LONGTEXT NOT NULL,
    explanation TEXT NULL,
    score_weight DECIMAL(8,2) NOT NULL DEFAULT 1.00,
    sequence_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ix_question_quiz_order (quiz_id, sequence_order),
    CONSTRAINT fk_question_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE question_options (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    question_id BIGINT UNSIGNED NOT NULL,
    option_text TEXT NOT NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    sequence_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ix_qopt_question_order (question_id, sequence_order),
    CONSTRAINT fk_qopt_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE quiz_attempts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    quiz_id BIGINT UNSIGNED NOT NULL,
    enrollment_id BIGINT UNSIGNED NOT NULL,
    attempt_number INT UNSIGNED NOT NULL,
    started_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    submitted_at DATETIME NULL,
    auto_score DECIMAL(5,2) NULL,
    final_score DECIMAL(5,2) NULL,
    is_passed TINYINT(1) NOT NULL DEFAULT 0,
    status VARCHAR(32) NOT NULL DEFAULT 'in_progress',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_attempt_number (quiz_id, enrollment_id, attempt_number),
    KEY ix_attempt_enroll_quiz (enrollment_id, quiz_id),
    CONSTRAINT fk_attempt_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE RESTRICT,
    CONSTRAINT fk_attempt_enroll FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE quiz_answers (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    quiz_attempt_id BIGINT UNSIGNED NOT NULL,
    question_id BIGINT UNSIGNED NOT NULL,
    answer_snapshot JSON NULL,
    is_correct TINYINT(1) NULL,
    score DECIMAL(8,2) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_answer_attempt_question (quiz_attempt_id, question_id),
    KEY ix_answer_question (question_id),
    CONSTRAINT fk_answer_attempt FOREIGN KEY (quiz_attempt_id) REFERENCES quiz_attempts(id) ON DELETE RESTRICT,
    CONSTRAINT fk_answer_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE quiz_answer_options (
    quiz_answer_id BIGINT UNSIGNED NOT NULL,
    question_option_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (quiz_answer_id, question_option_id),
    KEY ix_qao_option (question_option_id),
    CONSTRAINT fk_qao_answer FOREIGN KEY (quiz_answer_id) REFERENCES quiz_answers(id) ON DELETE RESTRICT,
    CONSTRAINT fk_qao_option FOREIGN KEY (question_option_id) REFERENCES question_options(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE assignments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    learning_class_id BIGINT UNSIGNED NOT NULL,
    learning_module_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    instructions LONGTEXT NULL,
    max_score DECIMAL(8,2) NOT NULL DEFAULT 100.00,
    weight DECIMAL(5,2) NOT NULL DEFAULT 100.00,
    available_from DATETIME NULL,
    due_at DATETIME NULL,
    allow_late_submission TINYINT(1) NOT NULL DEFAULT 0,
    status VARCHAR(32) NOT NULL DEFAULT 'draft',
    created_by BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ix_assignment_class (learning_class_id),
    KEY ix_assignment_module (learning_module_id),
    KEY ix_assignment_due (status, due_at),
    KEY ix_assignment_creator (created_by),
    CONSTRAINT fk_assignment_class FOREIGN KEY (learning_class_id) REFERENCES learning_classes(id) ON DELETE RESTRICT,
    CONSTRAINT fk_assignment_module FOREIGN KEY (learning_module_id) REFERENCES learning_modules(id) ON DELETE RESTRICT,
    CONSTRAINT fk_assignment_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT ck_assignment_max CHECK (max_score > 0),
    CONSTRAINT ck_assignment_weight CHECK (weight BETWEEN 0 AND 100)
) ENGINE=InnoDB;

CREATE TABLE assignment_submissions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    assignment_id BIGINT UNSIGNED NOT NULL,
    enrollment_id BIGINT UNSIGNED NOT NULL,
    attempt_number INT UNSIGNED NOT NULL DEFAULT 1,
    submission_text LONGTEXT NULL,
    submitted_at DATETIME NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_submission_attempt (assignment_id, enrollment_id, attempt_number),
    KEY ix_submission_enroll (enrollment_id, assignment_id, status),
    CONSTRAINT fk_submission_assignment FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE RESTRICT,
    CONSTRAINT fk_submission_enroll FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE assignment_submission_files (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    assignment_submission_id BIGINT UNSIGNED NOT NULL,
    file_asset_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_sub_file (assignment_submission_id, file_asset_id),
    KEY ix_sub_file_asset (file_asset_id),
    CONSTRAINT fk_sub_file_submission FOREIGN KEY (assignment_submission_id) REFERENCES assignment_submissions(id) ON DELETE RESTRICT,
    CONSTRAINT fk_sub_file_asset FOREIGN KEY (file_asset_id) REFERENCES file_assets(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE assignment_grades (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    assignment_submission_id BIGINT UNSIGNED NOT NULL,
    evaluator_id BIGINT UNSIGNED NOT NULL,
    score DECIMAL(8,2) NOT NULL,
    feedback LONGTEXT NULL,
    graded_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_grade_submission (assignment_submission_id),
    KEY ix_grade_evaluator (evaluator_id),
    CONSTRAINT fk_grade_submission FOREIGN KEY (assignment_submission_id) REFERENCES assignment_submissions(id) ON DELETE RESTRICT,
    CONSTRAINT fk_grade_evaluator FOREIGN KEY (evaluator_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT ck_grade_nonnegative CHECK (score >= 0)
) ENGINE=InnoDB;

CREATE TABLE final_grades (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    enrollment_id BIGINT UNSIGNED NOT NULL,
    quiz_score DECIMAL(5,2) NULL,
    assignment_score DECIMAL(5,2) NULL,
    final_score DECIMAL(5,2) NOT NULL,
    is_passed TINYINT(1) NOT NULL DEFAULT 0,
    calculated_at DATETIME NOT NULL,
    calculated_by BIGINT UNSIGNED NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_final_enroll (enrollment_id),
    KEY ix_final_passed (is_passed),
    KEY ix_final_actor (calculated_by),
    CONSTRAINT fk_final_enroll FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE RESTRICT,
    CONSTRAINT fk_final_actor FOREIGN KEY (calculated_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT ck_final_score CHECK (final_score BETWEEN 0 AND 100)
) ENGINE=InnoDB;

-- =============================================================================
-- 04. BUNGA RAMPAI
-- =============================================================================

CREATE TABLE knowledge_categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    parent_id BIGINT UNSIGNED NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL,
    description TEXT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_kcategory_slug (slug),
    KEY ix_kcategory_parent (parent_id),
    CONSTRAINT fk_kcategory_parent FOREIGN KEY (parent_id) REFERENCES knowledge_categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE tags (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_tag_slug (slug)
) ENGINE=InnoDB;

CREATE TABLE articles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    knowledge_category_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    excerpt TEXT NULL,
    content LONGTEXT NOT NULL,
    cover_file_asset_id BIGINT UNSIGNED NULL,
    workflow_status VARCHAR(32) NOT NULL DEFAULT 'draft',
    author_id BIGINT UNSIGNED NOT NULL,
    published_at DATETIME NULL,
    view_count INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_article_slug (slug),
    KEY ix_article_category (knowledge_category_id),
    KEY ix_article_public (workflow_status, published_at),
    KEY ix_article_author (author_id),
    KEY ix_article_cover (cover_file_asset_id),
    CONSTRAINT fk_article_category FOREIGN KEY (knowledge_category_id) REFERENCES knowledge_categories(id) ON DELETE RESTRICT,
    CONSTRAINT fk_article_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_article_cover FOREIGN KEY (cover_file_asset_id) REFERENCES file_assets(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE article_tags (
    article_id BIGINT UNSIGNED NOT NULL,
    tag_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (article_id, tag_id),
    KEY ix_at_tag (tag_id),
    CONSTRAINT fk_at_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE RESTRICT,
    CONSTRAINT fk_at_tag FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE faqs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    knowledge_category_id BIGINT UNSIGNED NULL,
    question VARCHAR(500) NOT NULL,
    answer LONGTEXT NOT NULL,
    workflow_status VARCHAR(32) NOT NULL DEFAULT 'draft',
    sequence_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_by BIGINT UNSIGNED NOT NULL,
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ix_faq_category (knowledge_category_id),
    KEY ix_faq_public (workflow_status, sequence_order),
    KEY ix_faq_creator (created_by),
    CONSTRAINT fk_faq_category FOREIGN KEY (knowledge_category_id) REFERENCES knowledge_categories(id) ON DELETE RESTRICT,
    CONSTRAINT fk_faq_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE case_studies (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    knowledge_category_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    summary TEXT NULL,
    content LONGTEXT NOT NULL,
    workflow_status VARCHAR(32) NOT NULL DEFAULT 'draft',
    author_id BIGINT UNSIGNED NOT NULL,
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_case_slug (slug),
    KEY ix_case_category (knowledge_category_id),
    KEY ix_case_public (workflow_status, published_at),
    KEY ix_case_author (author_id),
    CONSTRAINT fk_case_category FOREIGN KEY (knowledge_category_id) REFERENCES knowledge_categories(id) ON DELETE RESTRICT,
    CONSTRAINT fk_case_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE document_templates (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    knowledge_category_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT NULL,
    file_asset_id BIGINT UNSIGNED NOT NULL,
    version_label VARCHAR(64) NULL,
    workflow_status VARCHAR(32) NOT NULL DEFAULT 'draft',
    created_by BIGINT UNSIGNED NOT NULL,
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_doctemplate_slug (slug),
    KEY ix_dt_category (knowledge_category_id),
    KEY ix_dt_file (file_asset_id),
    KEY ix_dt_creator (created_by),
    KEY ix_dt_public (workflow_status, published_at),
    CONSTRAINT fk_dt_category FOREIGN KEY (knowledge_category_id) REFERENCES knowledge_categories(id) ON DELETE RESTRICT,
    CONSTRAINT fk_dt_file FOREIGN KEY (file_asset_id) REFERENCES file_assets(id) ON DELETE RESTRICT,
    CONSTRAINT fk_dt_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE bookmarks (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    bookmarkable_type VARCHAR(32) NOT NULL,
    bookmarkable_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_bookmark (user_id, bookmarkable_type, bookmarkable_id),
    KEY ix_bookmark_subject (bookmarkable_type, bookmarkable_id),
    CONSTRAINT fk_bookmark_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =============================================================================
-- 05. REGULASI PBJ
-- =============================================================================

CREATE TABLE regulation_categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(64) NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_regcat_code (code)
) ENGINE=InnoDB;

CREATE TABLE procurement_stages (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(64) NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_stage_code (code)
) ENGINE=InnoDB;

CREATE TABLE competencies (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(64) NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_competency_code (code)
) ENGINE=InnoDB;

CREATE TABLE regulations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    regulation_category_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(500) NOT NULL,
    regulation_number VARCHAR(150) NOT NULL,
    `year` YEAR NOT NULL,
    official_date DATE NULL,
    effective_date DATE NULL,
    legal_status VARCHAR(32) NOT NULL DEFAULT 'active',
    workflow_status VARCHAR(32) NOT NULL DEFAULT 'draft',
    summary TEXT NULL,
    source_url VARCHAR(500) NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_regulation_identity (regulation_category_id, regulation_number, `year`),
    KEY ix_regulation_year (legal_status, `year`),
    KEY ix_regulation_public (workflow_status, published_at),
    KEY ix_regulation_creator (created_by),
    CONSTRAINT fk_reg_category FOREIGN KEY (regulation_category_id) REFERENCES regulation_categories(id) ON DELETE RESTRICT,
    CONSTRAINT fk_reg_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE regulation_versions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    regulation_id BIGINT UNSIGNED NOT NULL,
    version_number INT UNSIGNED NOT NULL,
    file_asset_id BIGINT UNSIGNED NOT NULL,
    change_notes TEXT NULL,
    is_current TINYINT(1) NOT NULL DEFAULT 0,
    uploaded_by BIGINT UNSIGNED NOT NULL,
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_regversion (regulation_id, version_number),
    KEY ix_regversion_current (regulation_id, is_current),
    KEY ix_regversion_file (file_asset_id),
    KEY ix_regversion_uploader (uploaded_by),
    CONSTRAINT fk_regversion_reg FOREIGN KEY (regulation_id) REFERENCES regulations(id) ON DELETE RESTRICT,
    CONSTRAINT fk_regversion_file FOREIGN KEY (file_asset_id) REFERENCES file_assets(id) ON DELETE RESTRICT,
    CONSTRAINT fk_regversion_uploader FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE regulation_procurement_stages (
    regulation_id BIGINT UNSIGNED NOT NULL,
    procurement_stage_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (regulation_id, procurement_stage_id),
    KEY ix_rps_stage (procurement_stage_id),
    CONSTRAINT fk_rps_reg FOREIGN KEY (regulation_id) REFERENCES regulations(id) ON DELETE RESTRICT,
    CONSTRAINT fk_rps_stage FOREIGN KEY (procurement_stage_id) REFERENCES procurement_stages(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE regulation_competencies (
    regulation_id BIGINT UNSIGNED NOT NULL,
    competency_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (regulation_id, competency_id),
    KEY ix_rc_competency (competency_id),
    CONSTRAINT fk_rc_reg FOREIGN KEY (regulation_id) REFERENCES regulations(id) ON DELETE RESTRICT,
    CONSTRAINT fk_rc_competency FOREIGN KEY (competency_id) REFERENCES competencies(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE regulation_keywords (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    regulation_id BIGINT UNSIGNED NOT NULL,
    keyword VARCHAR(150) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_reg_keyword (regulation_id, keyword),
    CONSTRAINT fk_rkeyword_reg FOREIGN KEY (regulation_id) REFERENCES regulations(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE regulation_download_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    regulation_version_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    downloaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ix_rdl_version_date (regulation_version_id, downloaded_at),
    KEY ix_rdl_user (user_id),
    CONSTRAINT fk_rdl_version FOREIGN KEY (regulation_version_id) REFERENCES regulation_versions(id) ON DELETE RESTRICT,
    CONSTRAINT fk_rdl_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =============================================================================
-- 06. BERITA, AGENDA, EVENT
-- =============================================================================

CREATE TABLE news_categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_news_category_slug (slug)
) ENGINE=InnoDB;

CREATE TABLE news (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    news_category_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    excerpt TEXT NULL,
    content LONGTEXT NOT NULL,
    cover_file_asset_id BIGINT UNSIGNED NULL,
    workflow_status VARCHAR(32) NOT NULL DEFAULT 'draft',
    author_id BIGINT UNSIGNED NOT NULL,
    published_at DATETIME NULL,
    view_count INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_news_slug (slug),
    KEY ix_news_category (news_category_id),
    KEY ix_news_public (workflow_status, published_at),
    KEY ix_news_author (author_id),
    KEY ix_news_cover (cover_file_asset_id),
    CONSTRAINT fk_news_category FOREIGN KEY (news_category_id) REFERENCES news_categories(id) ON DELETE RESTRICT,
    CONSTRAINT fk_news_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_news_cover FOREIGN KEY (cover_file_asset_id) REFERENCES file_assets(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE events (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description LONGTEXT NULL,
    event_type VARCHAR(32) NOT NULL DEFAULT 'other',
    delivery_mode VARCHAR(16) NOT NULL DEFAULT 'online',
    location_name VARCHAR(255) NULL,
    meeting_url VARCHAR(500) NULL,
    start_at DATETIME NOT NULL,
    end_at DATETIME NULL,
    registration_open_at DATETIME NULL,
    registration_close_at DATETIME NULL,
    quota INT UNSIGNED NOT NULL DEFAULT 0,
    event_status VARCHAR(32) NOT NULL DEFAULT 'draft',
    workflow_status VARCHAR(32) NOT NULL DEFAULT 'draft',
    certificate_enabled TINYINT(1) NOT NULL DEFAULT 0,
    created_by BIGINT UNSIGNED NOT NULL,
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_event_slug (slug),
    KEY ix_event_status_start (event_status, start_at),
    KEY ix_event_workflow (workflow_status, published_at),
    KEY ix_event_creator (created_by),
    CONSTRAINT fk_event_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT ck_event_dates CHECK (end_at IS NULL OR end_at >= start_at),
    CONSTRAINT ck_event_registration_dates CHECK (registration_close_at IS NULL OR registration_open_at IS NULL OR registration_close_at >= registration_open_at)
) ENGINE=InnoDB;

CREATE TABLE event_registrations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    event_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    registration_status VARCHAR(32) NOT NULL DEFAULT 'registered',
    registered_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cancelled_at DATETIME NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_event_user (event_id, user_id),
    KEY ix_evreg_event_status (event_id, registration_status),
    KEY ix_evreg_user (user_id),
    CONSTRAINT fk_evreg_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE RESTRICT,
    CONSTRAINT fk_evreg_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE event_attendances (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    event_registration_id BIGINT UNSIGNED NOT NULL,
    attendance_status VARCHAR(16) NOT NULL DEFAULT 'absent',
    checked_in_at DATETIME NULL,
    checked_out_at DATETIME NULL,
    recorded_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_event_attendance_registration (event_registration_id),
    KEY ix_ea_recorder (recorded_by),
    CONSTRAINT fk_ea_registration FOREIGN KEY (event_registration_id) REFERENCES event_registrations(id) ON DELETE RESTRICT,
    CONSTRAINT fk_ea_recorder FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT ck_ea_check_times CHECK (checked_out_at IS NULL OR checked_in_at IS NULL OR checked_out_at >= checked_in_at)
) ENGINE=InnoDB;

-- =============================================================================
-- 07. SERTIFIKAT DIGITAL
-- =============================================================================

CREATE TABLE certificate_templates (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(64) NOT NULL,
    name VARCHAR(150) NOT NULL,
    certificate_type VARCHAR(32) NOT NULL,
    layout_file_asset_id BIGINT UNSIGNED NULL,
    html_template LONGTEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_certtemplate_code (code),
    KEY ix_certtemplate_type (certificate_type, is_active),
    KEY ix_certtemplate_file (layout_file_asset_id),
    KEY ix_certtemplate_creator (created_by),
    CONSTRAINT fk_certtemplate_file FOREIGN KEY (layout_file_asset_id) REFERENCES file_assets(id) ON DELETE RESTRICT,
    CONSTRAINT fk_certtemplate_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE certificates (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    recipient_user_id BIGINT UNSIGNED NOT NULL,
    certificate_template_id BIGINT UNSIGNED NULL,
    source_type VARCHAR(32) NOT NULL,
    source_id BIGINT UNSIGNED NOT NULL,
    certificate_number VARCHAR(128) NOT NULL,
    issued_at DATETIME NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'pending',
    file_asset_id BIGINT UNSIGNED NULL,
    verification_token VARCHAR(128) NOT NULL,
    issued_by BIGINT UNSIGNED NULL,
    expires_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_certificate_number (certificate_number),
    UNIQUE KEY uq_certificate_token (verification_token),
    UNIQUE KEY uq_certificate_source (source_type, source_id),
    KEY ix_certificate_recipient (recipient_user_id, status),
    KEY ix_certificate_template (certificate_template_id),
    KEY ix_certificate_file (file_asset_id),
    KEY ix_certificate_issuer (issued_by),
    CONSTRAINT fk_cert_recipient FOREIGN KEY (recipient_user_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_cert_template FOREIGN KEY (certificate_template_id) REFERENCES certificate_templates(id) ON DELETE RESTRICT,
    CONSTRAINT fk_cert_file FOREIGN KEY (file_asset_id) REFERENCES file_assets(id) ON DELETE RESTRICT,
    CONSTRAINT fk_cert_issuer FOREIGN KEY (issued_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE certificate_sequences (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    sequence_scope VARCHAR(32) NOT NULL,
    `year` SMALLINT UNSIGNED NOT NULL,
    last_number INT UNSIGNED NOT NULL DEFAULT 0,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_certificate_sequence (sequence_scope, `year`)
) ENGINE=InnoDB;

CREATE TABLE certificate_verification_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    certificate_id BIGINT UNSIGNED NULL,
    verified_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    verification_result VARCHAR(32) NOT NULL,
    PRIMARY KEY (id),
    KEY ix_cert_verify_date (certificate_id, verified_at),
    CONSTRAINT fk_certverify_cert FOREIGN KEY (certificate_id) REFERENCES certificates(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE certificate_revocations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    certificate_id BIGINT UNSIGNED NOT NULL,
    revoked_by BIGINT UNSIGNED NOT NULL,
    revoked_at DATETIME NOT NULL,
    reason TEXT NOT NULL,
    replacement_certificate_id BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_cert_revoked (certificate_id),
    KEY ix_revoker (revoked_by),
    KEY ix_replacement (replacement_certificate_id),
    CONSTRAINT fk_revoke_cert FOREIGN KEY (certificate_id) REFERENCES certificates(id) ON DELETE RESTRICT,
    CONSTRAINT fk_revoke_user FOREIGN KEY (revoked_by) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_revoke_replacement FOREIGN KEY (replacement_certificate_id) REFERENCES certificates(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE event_certificates (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    event_registration_id BIGINT UNSIGNED NOT NULL,
    certificate_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_ev_cert_registration (event_registration_id),
    UNIQUE KEY uq_ev_cert_certificate (certificate_id),
    CONSTRAINT fk_ev_cert_registration FOREIGN KEY (event_registration_id) REFERENCES event_registrations(id) ON DELETE RESTRICT,
    CONSTRAINT fk_ev_cert_certificate FOREIGN KEY (certificate_id) REFERENCES certificates(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =============================================================================
-- 08. GOVERNMENT INTELLIGENCE PBJ
-- =============================================================================

CREATE TABLE jf_ppbj_levels (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(64) NOT NULL,
    name VARCHAR(150) NOT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_jf_level_code (code)
) ENGINE=InnoDB;

CREATE TABLE jf_ppbj_personnel (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NULL,
    organization_unit_id BIGINT UNSIGNED NOT NULL,
    employee_name VARCHAR(150) NOT NULL,
    employee_identifier VARCHAR(64) NULL,
    jf_ppbj_level_id BIGINT UNSIGNED NOT NULL,
    employment_status VARCHAR(32) NOT NULL DEFAULT 'active',
    effective_start_date DATE NULL,
    effective_end_date DATE NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ix_jfp_user (user_id),
    KEY ix_jfp_org_status (organization_unit_id, employment_status),
    KEY ix_jfp_level (jf_ppbj_level_id),
    CONSTRAINT fk_jfp_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_jfp_org FOREIGN KEY (organization_unit_id) REFERENCES organization_units(id) ON DELETE RESTRICT,
    CONSTRAINT fk_jfp_level FOREIGN KEY (jf_ppbj_level_id) REFERENCES jf_ppbj_levels(id) ON DELETE RESTRICT,
    CONSTRAINT ck_jfp_dates CHECK (effective_end_date IS NULL OR effective_start_date IS NULL OR effective_end_date >= effective_start_date)
) ENGINE=InnoDB;

CREATE TABLE jf_ppbj_histories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    jf_ppbj_personnel_id BIGINT UNSIGNED NOT NULL,
    change_type VARCHAR(32) NOT NULL,
    from_level_id BIGINT UNSIGNED NULL,
    to_level_id BIGINT UNSIGNED NULL,
    from_organization_unit_id BIGINT UNSIGNED NULL,
    to_organization_unit_id BIGINT UNSIGNED NULL,
    effective_date DATE NOT NULL,
    notes TEXT NULL,
    recorded_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ix_jfh_person_date (jf_ppbj_personnel_id, effective_date),
    KEY ix_jfh_from_level (from_level_id),
    KEY ix_jfh_to_level (to_level_id),
    KEY ix_jfh_from_org (from_organization_unit_id),
    KEY ix_jfh_to_org (to_organization_unit_id),
    KEY ix_jfh_recorder (recorded_by),
    CONSTRAINT fk_jfh_person FOREIGN KEY (jf_ppbj_personnel_id) REFERENCES jf_ppbj_personnel(id) ON DELETE RESTRICT,
    CONSTRAINT fk_jfh_from_level FOREIGN KEY (from_level_id) REFERENCES jf_ppbj_levels(id) ON DELETE RESTRICT,
    CONSTRAINT fk_jfh_to_level FOREIGN KEY (to_level_id) REFERENCES jf_ppbj_levels(id) ON DELETE RESTRICT,
    CONSTRAINT fk_jfh_from_org FOREIGN KEY (from_organization_unit_id) REFERENCES organization_units(id) ON DELETE RESTRICT,
    CONSTRAINT fk_jfh_to_org FOREIGN KEY (to_organization_unit_id) REFERENCES organization_units(id) ON DELETE RESTRICT,
    CONSTRAINT fk_jfh_recorder FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE competency_certificate_types (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(64) NOT NULL,
    name VARCHAR(150) NOT NULL,
    issuer_name VARCHAR(255) NULL,
    validity_months INT UNSIGNED NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_cct_code (code)
) ENGINE=InnoDB;

CREATE TABLE competency_certificates (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NULL,
    organization_unit_id BIGINT UNSIGNED NOT NULL,
    holder_name VARCHAR(150) NOT NULL,
    holder_identifier VARCHAR(64) NULL,
    competency_certificate_type_id BIGINT UNSIGNED NOT NULL,
    certificate_number VARCHAR(150) NOT NULL,
    issued_at DATE NULL,
    expires_at DATE NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'unknown',
    evidence_file_asset_id BIGINT UNSIGNED NULL,
    verified_by BIGINT UNSIGNED NULL,
    verified_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_compcert_type_number (competency_certificate_type_id, certificate_number),
    KEY ix_compcert_org (organization_unit_id, status),
    KEY ix_compcert_user (user_id),
    KEY ix_compcert_expiry (expires_at),
    KEY ix_compcert_evidence (evidence_file_asset_id),
    KEY ix_compcert_verifier (verified_by),
    CONSTRAINT fk_compcert_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_compcert_org FOREIGN KEY (organization_unit_id) REFERENCES organization_units(id) ON DELETE RESTRICT,
    CONSTRAINT fk_compcert_type FOREIGN KEY (competency_certificate_type_id) REFERENCES competency_certificate_types(id) ON DELETE RESTRICT,
    CONSTRAINT fk_compcert_evidence FOREIGN KEY (evidence_file_asset_id) REFERENCES file_assets(id) ON DELETE RESTRICT,
    CONSTRAINT fk_compcert_verifier FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT ck_compcert_dates CHECK (expires_at IS NULL OR issued_at IS NULL OR expires_at >= issued_at)
) ENGINE=InnoDB;

CREATE TABLE competency_gap_snapshots (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    organization_unit_id BIGINT UNSIGNED NOT NULL,
    period_date DATE NOT NULL,
    metric_code VARCHAR(64) NOT NULL,
    required_count INT UNSIGNED NOT NULL DEFAULT 0,
    available_count INT UNSIGNED NOT NULL DEFAULT 0,
    gap_count INT NOT NULL DEFAULT 0,
    generated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    generated_by BIGINT UNSIGNED NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_gap_org_period_metric (organization_unit_id, period_date, metric_code),
    KEY ix_gap_generator (generated_by),
    CONSTRAINT fk_gap_org FOREIGN KEY (organization_unit_id) REFERENCES organization_units(id) ON DELETE RESTRICT,
    CONSTRAINT fk_gap_generator FOREIGN KEY (generated_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT ck_gap_math CHECK (gap_count = CAST(required_count AS SIGNED) - CAST(available_count AS SIGNED))
) ENGINE=InnoDB;

-- End of fresh schema v2.
-- Required follow-up: import master wilayah lengkap, then run application seeders/dummy data.
