-- BegawiPBJ Fresh Schema V4
-- Baseline schema for MySQL 8.0+ / MariaDB 10.6+ with InnoDB and utf8mb4.
--
-- The official `wilayah` master table is created by this schema.
-- Import official master data manually afterward from database/reference/wilayah.sql.
-- This schema never inserts, truncates, or otherwise seeds `wilayah` data.

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS competency_gap_snapshots;
DROP TABLE IF EXISTS competency_certificates;
DROP TABLE IF EXISTS competency_certificate_types;
DROP TABLE IF EXISTS jf_ppbj_records;
DROP TABLE IF EXISTS jf_ppbj_levels;
DROP TABLE IF EXISTS competencies;
DROP TABLE IF EXISTS certificates;
DROP TABLE IF EXISTS certificate_templates;
DROP TABLE IF EXISTS attendances;
DROP TABLE IF EXISTS event_registrations;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS news;
DROP TABLE IF EXISTS news_categories;
DROP TABLE IF EXISTS regulation_downloads;
DROP TABLE IF EXISTS regulation_relations;
DROP TABLE IF EXISTS regulation_versions;
DROP TABLE IF EXISTS regulations;
DROP TABLE IF EXISTS regulation_categories;
DROP TABLE IF EXISTS knowledge_media;
DROP TABLE IF EXISTS knowledge_article_tags;
DROP TABLE IF EXISTS knowledge_article_categories;
DROP TABLE IF EXISTS knowledge_articles;
DROP TABLE IF EXISTS knowledge_tags;
DROP TABLE IF EXISTS knowledge_categories;
DROP TABLE IF EXISTS feedbacks;
DROP TABLE IF EXISTS learning_progress;
DROP TABLE IF EXISTS assignment_submission_files;
DROP TABLE IF EXISTS assignment_submissions;
DROP TABLE IF EXISTS assignments;
DROP TABLE IF EXISTS quiz_answers;
DROP TABLE IF EXISTS quiz_attempts;
DROP TABLE IF EXISTS quiz_options;
DROP TABLE IF EXISTS quiz_questions;
DROP TABLE IF EXISTS quizzes;
DROP TABLE IF EXISTS pre_tests;
DROP TABLE IF EXISTS enrollments;
DROP TABLE IF EXISTS lessons;
DROP TABLE IF EXISTS learning_modules;
DROP TABLE IF EXISTS class_evaluators;
DROP TABLE IF EXISTS class_trainers;
DROP TABLE IF EXISTS learning_classes;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS programs;
DROP TABLE IF EXISTS exports;
DROP TABLE IF EXISTS bookmarks;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS file_assets;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS workflow_transitions;
DROP TABLE IF EXISTS approvals;
DROP TABLE IF EXISTS profile_verifications;
DROP TABLE IF EXISTS profile_change_requests;
DROP TABLE IF EXISTS user_procurement_roles;
DROP TABLE IF EXISTS procurement_roles;
DROP TABLE IF EXISTS organization_units;
DROP TABLE IF EXISTS role_requests;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS email_verifications;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS user_roles;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS public_profiles;
DROP TABLE IF EXISTS academic_profiles;
DROP TABLE IF EXISTS provider_profiles;
DROP TABLE IF EXISTS asn_profiles;
DROP TABLE IF EXISTS user_profiles;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS wilayah;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE wilayah (
    kode VARCHAR(13) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    PRIMARY KEY (kode),
    KEY wilayah_name_idx (nama)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(32) NOT NULL,
    profile_type VARCHAR(64) NOT NULL,
    account_status VARCHAR(64) NOT NULL DEFAULT 'draft',
    email_verified_at DATETIME NULL,
    last_login_at DATETIME NULL,
    session_version BIGINT UNSIGNED NOT NULL DEFAULT 1,
    status_reason TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY users_email_unique (email),
    UNIQUE KEY users_username_unique (username),
    KEY users_account_status_idx (account_status),
    KEY users_profile_type_idx (profile_type),
    KEY users_email_verified_at_idx (email_verified_at),
    KEY users_deleted_at_idx (deleted_at),
    CONSTRAINT users_profile_type_chk CHECK (profile_type IN ('asn', 'provider', 'academic', 'public')),
    CONSTRAINT users_account_status_chk CHECK (account_status IN ('draft', 'submitted', 'pending_verification', 'revision_required', 'active', 'rejected', 'inactive', 'suspended', 'archived'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_profiles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    address_line TEXT NOT NULL,
    province_code VARCHAR(13) NOT NULL,
    regency_code VARCHAR(13) NOT NULL,
    district_code VARCHAR(13) NOT NULL,
    village_code VARCHAR(13) NULL,
    profile_completion_status VARCHAR(64) NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY user_profiles_user_id_unique (user_id),
    KEY user_profiles_wilayah_idx (province_code, regency_code, district_code),
    KEY user_profiles_village_code_idx (village_code),
    CONSTRAINT user_profiles_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT user_profiles_province_code_fk FOREIGN KEY (province_code) REFERENCES wilayah (kode) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT user_profiles_regency_code_fk FOREIGN KEY (regency_code) REFERENCES wilayah (kode) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT user_profiles_district_code_fk FOREIGN KEY (district_code) REFERENCES wilayah (kode) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT user_profiles_village_code_fk FOREIGN KEY (village_code) REFERENCES wilayah (kode) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE organization_units (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    parent_id BIGINT UNSIGNED NULL,
    code VARCHAR(64) NOT NULL,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(64) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY organization_units_code_unique (code),
    KEY organization_units_parent_id_idx (parent_id),
    KEY organization_units_type_active_idx (type, is_active),
    CONSTRAINT organization_units_parent_id_fk FOREIGN KEY (parent_id) REFERENCES organization_units (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE asn_profiles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    organization_unit_id BIGINT UNSIGNED NULL,
    employee_identifier VARCHAR(64) NULL,
    employment_status VARCHAR(64) NULL,
    origin_type VARCHAR(64) NOT NULL,
    institution_name VARCHAR(255) NULL,
    unit_name VARCHAR(255) NULL,
    position_name VARCHAR(255) NULL,
    rank_grade VARCHAR(128) NULL,
    official_email VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY asn_profiles_user_id_unique (user_id),
    KEY asn_profiles_organization_unit_id_idx (organization_unit_id),
    KEY asn_profiles_origin_type_idx (origin_type),
    CONSTRAINT asn_profiles_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT asn_profiles_organization_unit_id_fk FOREIGN KEY (organization_unit_id) REFERENCES organization_units (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT asn_profiles_origin_type_chk CHECK (origin_type IN ('pemprov_lampung', 'kementerian_lembaga', 'pemerintah_daerah_lain', 'pemerintah_desa', 'bumn_bumd', 'lainnya'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE provider_profiles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    business_name VARCHAR(255) NOT NULL,
    business_form VARCHAR(64) NOT NULL,
    business_field VARCHAR(255) NULL,
    business_identifier VARCHAR(64) NULL,
    registrant_position VARCHAR(255) NOT NULL,
    responsible_person VARCHAR(255) NOT NULL,
    business_email VARCHAR(255) NULL,
    business_address TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY provider_profiles_user_id_unique (user_id),
    CONSTRAINT provider_profiles_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE academic_profiles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    institution_name VARCHAR(255) NOT NULL,
    institution_type VARCHAR(64) NOT NULL,
    role_name VARCHAR(255) NOT NULL,
    expertise JSON NULL,
    institution_email VARCHAR(255) NULL,
    professional_identifier VARCHAR(128) NULL,
    portfolio_url VARCHAR(2048) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY academic_profiles_user_id_unique (user_id),
    CONSTRAINT academic_profiles_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE public_profiles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    national_identifier VARCHAR(32) NULL,
    occupation_status VARCHAR(128) NOT NULL,
    organization_name VARCHAR(255) NULL,
    interest_areas JSON NULL,
    usage_purpose JSON NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY public_profiles_user_id_unique (user_id),
    CONSTRAINT public_profiles_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE roles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(64) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    is_system TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY roles_key_unique (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE permissions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(128) NOT NULL,
    name VARCHAR(255) NOT NULL,
    domain VARCHAR(64) NOT NULL,
    description TEXT NULL,
    is_system TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY permissions_key_unique (`key`),
    KEY permissions_domain_idx (domain)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_roles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'active',
    granted_by BIGINT UNSIGNED NULL,
    granted_at DATETIME NOT NULL,
    revoked_by BIGINT UNSIGNED NULL,
    revoked_at DATETIME NULL,
    reason TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY user_roles_user_role_unique (user_id, role_id),
    KEY user_roles_user_status_idx (user_id, status),
    KEY user_roles_role_status_idx (role_id, status),
    CONSTRAINT user_roles_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT user_roles_role_id_fk FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT user_roles_granted_by_fk FOREIGN KEY (granted_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT user_roles_revoked_by_fk FOREIGN KEY (revoked_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT user_roles_status_chk CHECK (status IN ('active', 'revoked', 'expired'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE role_permissions (
    role_id BIGINT UNSIGNED NOT NULL,
    permission_id BIGINT UNSIGNED NOT NULL,
    granted_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    KEY role_permissions_permission_id_idx (permission_id),
    CONSTRAINT role_permissions_role_id_fk FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT role_permissions_permission_id_fk FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT role_permissions_granted_by_fk FOREIGN KEY (granted_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE email_verifications (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    token_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    verified_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY email_verifications_token_hash_unique (token_hash),
    KEY email_verifications_user_expires_idx (user_id, expires_at),
    CONSTRAINT email_verifications_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_resets (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    token_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY password_resets_token_hash_unique (token_hash),
    KEY password_resets_user_expires_idx (user_id, expires_at),
    CONSTRAINT password_resets_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE role_requests (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    requested_role_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'draft',
    reason TEXT NULL,
    reviewed_by BIGINT UNSIGNED NULL,
    reviewed_at DATETIME NULL,
    review_notes TEXT NULL,
    archived_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY role_requests_user_status_idx (user_id, status),
    KEY role_requests_role_status_idx (requested_role_id, status),
    KEY role_requests_status_created_idx (status, created_at),
    CONSTRAINT role_requests_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT role_requests_requested_role_id_fk FOREIGN KEY (requested_role_id) REFERENCES roles (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT role_requests_reviewed_by_fk FOREIGN KEY (reviewed_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT role_requests_status_chk CHECK (status IN ('draft', 'submitted', 'in_review', 'revision_required', 'approved', 'rejected', 'cancelled', 'archived'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE procurement_roles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(64) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY procurement_roles_key_unique (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE profile_change_requests (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'draft',
    requested_payload JSON NOT NULL,
    reason TEXT NULL,
    reviewed_by BIGINT UNSIGNED NULL,
    reviewed_at DATETIME NULL,
    review_notes TEXT NULL,
    applied_at DATETIME NULL,
    archived_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY profile_change_requests_user_status_idx (user_id, status),
    KEY profile_change_requests_status_created_idx (status, created_at),
    CONSTRAINT profile_change_requests_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT profile_change_requests_reviewed_by_fk FOREIGN KEY (reviewed_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT profile_change_requests_status_chk CHECK (status IN ('draft', 'submitted', 'in_review', 'revision_required', 'approved', 'rejected', 'cancelled', 'archived'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE profile_verifications (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    verification_type VARCHAR(64) NOT NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'pending',
    requested_by BIGINT UNSIGNED NULL,
    reviewed_by BIGINT UNSIGNED NULL,
    reason TEXT NULL,
    notes TEXT NULL,
    reviewed_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY profile_verifications_user_type_status_idx (user_id, verification_type, status),
    KEY profile_verifications_status_created_idx (status, created_at),
    CONSTRAINT profile_verifications_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT profile_verifications_requested_by_fk FOREIGN KEY (requested_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT profile_verifications_reviewed_by_fk FOREIGN KEY (reviewed_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT profile_verifications_status_chk CHECK (status IN ('pending', 'in_review', 'approved', 'rejected', 'revision_required'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE file_assets (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    disk VARCHAR(64) NOT NULL,
    path VARCHAR(1024) NOT NULL,
    original_name VARCHAR(512) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    extension VARCHAR(32) NOT NULL,
    mime_type VARCHAR(255) NOT NULL,
    size_bytes BIGINT UNSIGNED NOT NULL,
    checksum CHAR(64) NULL,
    visibility VARCHAR(64) NOT NULL DEFAULT 'internal',
    lifecycle_status VARCHAR(64) NOT NULL DEFAULT 'pending_upload',
    resource_type VARCHAR(128) NULL,
    resource_id BIGINT UNSIGNED NULL,
    uploaded_by BIGINT UNSIGNED NULL,
    available_at DATETIME NULL,
    expires_at DATETIME NULL,
    deleted_at DATETIME NULL,
    metadata JSON NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY file_assets_resource_idx (resource_type, resource_id),
    KEY file_assets_visibility_lifecycle_idx (visibility, lifecycle_status),
    KEY file_assets_uploaded_by_created_idx (uploaded_by, created_at),
    KEY file_assets_expires_at_idx (expires_at),
    CONSTRAINT file_assets_uploaded_by_fk FOREIGN KEY (uploaded_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT file_assets_visibility_chk CHECK (visibility IN ('public', 'internal', 'restricted')),
    CONSTRAINT file_assets_lifecycle_status_chk CHECK (lifecycle_status IN ('pending_upload', 'stored', 'scanned', 'available', 'rejected', 'quarantined', 'deleted'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_procurement_roles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    procurement_role_id BIGINT UNSIGNED NOT NULL,
    verification_status VARCHAR(64) NOT NULL DEFAULT 'self_declared',
    verified_by BIGINT UNSIGNED NULL,
    verified_at DATETIME NULL,
    assignment_start_at DATETIME NULL,
    assignment_end_at DATETIME NULL,
    evidence_file_id BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY user_procurement_roles_unique (user_id, procurement_role_id),
    KEY user_procurement_roles_verification_status_idx (verification_status),
    CONSTRAINT user_procurement_roles_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT user_procurement_roles_role_id_fk FOREIGN KEY (procurement_role_id) REFERENCES procurement_roles (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT user_procurement_roles_verified_by_fk FOREIGN KEY (verified_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT user_procurement_roles_evidence_file_id_fk FOREIGN KEY (evidence_file_id) REFERENCES file_assets (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT user_procurement_roles_status_chk CHECK (verification_status IN ('self_declared', 'pending', 'verified', 'rejected'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE approvals (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    resource_type VARCHAR(128) NOT NULL,
    resource_id BIGINT UNSIGNED NOT NULL,
    requested_action VARCHAR(128) NOT NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'pending',
    requested_by BIGINT UNSIGNED NULL,
    reviewed_by BIGINT UNSIGNED NULL,
    reason TEXT NULL,
    notes TEXT NULL,
    expires_at DATETIME NULL,
    reviewed_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY approvals_resource_idx (resource_type, resource_id),
    KEY approvals_status_created_idx (status, created_at),
    KEY approvals_reviewed_status_idx (reviewed_by, status),
    CONSTRAINT approvals_requested_by_fk FOREIGN KEY (requested_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT approvals_reviewed_by_fk FOREIGN KEY (reviewed_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT approvals_status_chk CHECK (status IN ('pending', 'in_review', 'approved', 'rejected', 'revision_required', 'cancelled', 'expired'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE workflow_transitions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    resource_type VARCHAR(128) NOT NULL,
    resource_id BIGINT UNSIGNED NOT NULL,
    from_state VARCHAR(64) NULL,
    to_state VARCHAR(64) NOT NULL,
    action VARCHAR(128) NOT NULL,
    actor_id BIGINT UNSIGNED NULL,
    actor_type VARCHAR(32) NOT NULL DEFAULT 'user',
    reason TEXT NULL,
    notes TEXT NULL,
    metadata JSON NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY workflow_transitions_resource_created_idx (resource_type, resource_id, created_at),
    KEY workflow_transitions_actor_created_idx (actor_id, created_at),
    KEY workflow_transitions_to_state_created_idx (to_state, created_at),
    CONSTRAINT workflow_transitions_actor_id_fk FOREIGN KEY (actor_id) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT workflow_transitions_actor_type_chk CHECK (actor_type IN ('user', 'system'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE audit_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    actor_id BIGINT UNSIGNED NULL,
    action VARCHAR(128) NOT NULL,
    resource_type VARCHAR(128) NULL,
    resource_id BIGINT UNSIGNED NULL,
    result VARCHAR(32) NOT NULL,
    before_snapshot JSON NULL,
    after_snapshot JSON NULL,
    reason TEXT NULL,
    ip_hash CHAR(64) NULL,
    user_agent_hash CHAR(64) NULL,
    request_id VARCHAR(128) NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY audit_logs_actor_created_idx (actor_id, created_at),
    KEY audit_logs_resource_created_idx (resource_type, resource_id, created_at),
    KEY audit_logs_action_created_idx (action, created_at),
    KEY audit_logs_result_created_idx (result, created_at),
    CONSTRAINT audit_logs_actor_id_fk FOREIGN KEY (actor_id) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT audit_logs_result_chk CHECK (result IN ('success', 'failed', 'denied', 'partial'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notifications (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    channel VARCHAR(64) NOT NULL DEFAULT 'database',
    type VARCHAR(128) NOT NULL,
    title VARCHAR(255) NOT NULL,
    body TEXT NULL,
    data JSON NULL,
    read_at DATETIME NULL,
    sent_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY notifications_user_read_created_idx (user_id, read_at, created_at),
    KEY notifications_channel_sent_idx (channel, sent_at),
    CONSTRAINT notifications_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE bookmarks (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    bookmarkable_type VARCHAR(128) NOT NULL,
    bookmarkable_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY bookmarks_unique (user_id, bookmarkable_type, bookmarkable_id),
    KEY bookmarks_resource_idx (bookmarkable_type, bookmarkable_id),
    CONSTRAINT bookmarks_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE exports (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    requested_by BIGINT UNSIGNED NOT NULL,
    type VARCHAR(128) NOT NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'queued',
    filters JSON NULL,
    file_id BIGINT UNSIGNED NULL,
    error_message TEXT NULL,
    expires_at DATETIME NULL,
    completed_at DATETIME NULL,
    deleted_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY exports_requested_status_created_idx (requested_by, status, created_at),
    KEY exports_status_expires_idx (status, expires_at),
    CONSTRAINT exports_requested_by_fk FOREIGN KEY (requested_by) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT exports_file_id_fk FOREIGN KEY (file_id) REFERENCES file_assets (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT exports_status_chk CHECK (status IN ('queued', 'generating', 'ready', 'failed', 'expired', 'deleted'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE programs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    summary TEXT NULL,
    description LONGTEXT NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'draft',
    visibility VARCHAR(64) NOT NULL DEFAULT 'internal',
    featured TINYINT(1) NOT NULL DEFAULT 0,
    cover_file_id BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    published_by BIGINT UNSIGNED NULL,
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY programs_slug_unique (slug),
    KEY programs_status_visibility_published_idx (status, visibility, published_at),
    CONSTRAINT programs_cover_file_id_fk FOREIGN KEY (cover_file_id) REFERENCES file_assets (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT programs_created_by_fk FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT programs_updated_by_fk FOREIGN KEY (updated_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT programs_published_by_fk FOREIGN KEY (published_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE courses (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    program_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    summary TEXT NULL,
    description LONGTEXT NULL,
    level VARCHAR(64) NULL,
    duration_minutes INT UNSIGNED NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'draft',
    visibility VARCHAR(64) NOT NULL DEFAULT 'internal',
    cover_file_id BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    published_by BIGINT UNSIGNED NULL,
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY courses_slug_unique (slug),
    KEY courses_program_id_idx (program_id),
    KEY courses_status_visibility_published_idx (status, visibility, published_at),
    CONSTRAINT courses_program_id_fk FOREIGN KEY (program_id) REFERENCES programs (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT courses_cover_file_id_fk FOREIGN KEY (cover_file_id) REFERENCES file_assets (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT courses_created_by_fk FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT courses_updated_by_fk FOREIGN KEY (updated_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT courses_published_by_fk FOREIGN KEY (published_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE learning_classes (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    course_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(64) NOT NULL,
    name VARCHAR(255) NOT NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'draft',
    registration_open_at DATETIME NULL,
    registration_close_at DATETIME NULL,
    start_at DATETIME NULL,
    end_at DATETIME NULL,
    capacity INT UNSIGNED NULL,
    certificate_enabled TINYINT(1) NOT NULL DEFAULT 0,
    passing_grade DECIMAL(5,2) NOT NULL DEFAULT 70.00,
    quiz_weight DECIMAL(5,2) NOT NULL DEFAULT 40.00,
    assignment_weight DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    post_test_weight DECIMAL(5,2) NOT NULL DEFAULT 60.00,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY learning_classes_course_code_unique (course_id, code),
    KEY learning_classes_course_status_idx (course_id, status),
    KEY learning_classes_registration_idx (registration_open_at, registration_close_at),
    KEY learning_classes_period_idx (start_at, end_at),
    CONSTRAINT learning_classes_course_id_fk FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT learning_classes_created_by_fk FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT learning_classes_updated_by_fk FOREIGN KEY (updated_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT learning_classes_weights_chk CHECK (quiz_weight + assignment_weight + post_test_weight = 100.00),
    CONSTRAINT learning_classes_post_test_weight_chk CHECK (post_test_weight = 60.00),
    CONSTRAINT learning_classes_passing_grade_chk CHECK (passing_grade >= 0.00 AND passing_grade <= 100.00)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE class_trainers (
    class_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    assigned_by BIGINT UNSIGNED NULL,
    assigned_at DATETIME NOT NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'active',
    PRIMARY KEY (class_id, user_id),
    KEY class_trainers_user_id_idx (user_id),
    CONSTRAINT class_trainers_class_id_fk FOREIGN KEY (class_id) REFERENCES learning_classes (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT class_trainers_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT class_trainers_assigned_by_fk FOREIGN KEY (assigned_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT class_trainers_status_chk CHECK (status IN ('active', 'revoked'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE class_evaluators (
    class_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    assigned_by BIGINT UNSIGNED NULL,
    assigned_at DATETIME NOT NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'active',
    PRIMARY KEY (class_id, user_id),
    KEY class_evaluators_user_id_idx (user_id),
    CONSTRAINT class_evaluators_class_id_fk FOREIGN KEY (class_id) REFERENCES learning_classes (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT class_evaluators_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT class_evaluators_assigned_by_fk FOREIGN KEY (assigned_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT class_evaluators_status_chk CHECK (status IN ('active', 'revoked'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE learning_modules (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    class_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    sort_order INT UNSIGNED NOT NULL,
    is_required TINYINT(1) NOT NULL DEFAULT 1,
    status VARCHAR(64) NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY learning_modules_class_order_unique (class_id, sort_order),
    CONSTRAINT learning_modules_class_id_fk FOREIGN KEY (class_id) REFERENCES learning_classes (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE lessons (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    module_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NULL,
    content_type VARCHAR(64) NOT NULL,
    content LONGTEXT NULL,
    file_id BIGINT UNSIGNED NULL,
    external_url VARCHAR(2048) NULL,
    sort_order INT UNSIGNED NOT NULL,
    is_required TINYINT(1) NOT NULL DEFAULT 1,
    status VARCHAR(64) NOT NULL DEFAULT 'draft',
    visibility VARCHAR(64) NOT NULL DEFAULT 'internal',
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY lessons_module_order_unique (module_id, sort_order),
    KEY lessons_module_status_visibility_idx (module_id, status, visibility),
    KEY lessons_slug_idx (slug),
    CONSTRAINT lessons_module_id_fk FOREIGN KEY (module_id) REFERENCES learning_modules (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT lessons_file_id_fk FOREIGN KEY (file_id) REFERENCES file_assets (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT lessons_created_by_fk FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT lessons_updated_by_fk FOREIGN KEY (updated_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT lessons_content_type_chk CHECK (content_type IN ('text', 'document', 'video', 'link')),
    CONSTRAINT lessons_visibility_chk CHECK (visibility IN ('public', 'internal', 'restricted'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE enrollments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    class_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'pending',
    enrolled_at DATETIME NOT NULL,
    activated_at DATETIME NULL,
    completed_at DATETIME NULL,
    final_score DECIMAL(5,2) NULL,
    passed_at DATETIME NULL,
    certificate_eligible_at DATETIME NULL,
    withdrawn_at DATETIME NULL,
    cancelled_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY enrollments_class_user_unique (class_id, user_id),
    KEY enrollments_user_status_idx (user_id, status),
    KEY enrollments_class_status_idx (class_id, status),
    KEY enrollments_certificate_eligible_idx (certificate_eligible_at),
    CONSTRAINT enrollments_class_id_fk FOREIGN KEY (class_id) REFERENCES learning_classes (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT enrollments_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT enrollments_status_chk CHECK (status IN ('pending', 'active', 'completed', 'withdrawn', 'cancelled')),
    CONSTRAINT enrollments_final_score_chk CHECK (final_score IS NULL OR (final_score >= 0.00 AND final_score <= 100.00))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pre_tests (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    class_id BIGINT UNSIGNED NOT NULL,
    is_enabled TINYINT(1) NOT NULL DEFAULT 0,
    attempt_limit INT UNSIGNED NOT NULL DEFAULT 1,
    status VARCHAR(64) NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY pre_tests_class_id_unique (class_id),
    CONSTRAINT pre_tests_class_id_fk FOREIGN KEY (class_id) REFERENCES learning_classes (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE quizzes (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    class_id BIGINT UNSIGNED NOT NULL,
    lesson_id BIGINT UNSIGNED NULL,
    type VARCHAR(64) NOT NULL,
    title VARCHAR(255) NOT NULL,
    instructions TEXT NULL,
    attempt_limit INT UNSIGNED NOT NULL DEFAULT 1,
    is_required TINYINT(1) NOT NULL DEFAULT 0,
    status VARCHAR(64) NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY quizzes_class_type_status_idx (class_id, type, status),
    KEY quizzes_lesson_id_idx (lesson_id),
    CONSTRAINT quizzes_class_id_fk FOREIGN KEY (class_id) REFERENCES learning_classes (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT quizzes_lesson_id_fk FOREIGN KEY (lesson_id) REFERENCES lessons (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT quizzes_type_chk CHECK (type IN ('pre_test', 'lesson_quiz', 'post_test'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE quiz_questions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    quiz_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(64) NOT NULL,
    prompt LONGTEXT NOT NULL,
    points DECIMAL(5,2) NOT NULL DEFAULT 1.00,
    sort_order INT UNSIGNED NOT NULL,
    explanation TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY quiz_questions_quiz_order_unique (quiz_id, sort_order),
    CONSTRAINT quiz_questions_quiz_id_fk FOREIGN KEY (quiz_id) REFERENCES quizzes (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT quiz_questions_points_chk CHECK (points >= 0.00)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE quiz_options (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    question_id BIGINT UNSIGNED NOT NULL,
    option_text TEXT NOT NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY quiz_options_question_order_unique (question_id, sort_order),
    CONSTRAINT quiz_options_question_id_fk FOREIGN KEY (question_id) REFERENCES quiz_questions (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE quiz_attempts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    quiz_id BIGINT UNSIGNED NOT NULL,
    enrollment_id BIGINT UNSIGNED NOT NULL,
    attempt_number INT UNSIGNED NOT NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'started',
    started_at DATETIME NOT NULL,
    submitted_at DATETIME NULL,
    graded_at DATETIME NULL,
    score DECIMAL(5,2) NULL,
    invalidated_by BIGINT UNSIGNED NULL,
    invalidated_reason TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY quiz_attempts_unique (quiz_id, enrollment_id, attempt_number),
    KEY quiz_attempts_enrollment_status_idx (enrollment_id, status),
    CONSTRAINT quiz_attempts_quiz_id_fk FOREIGN KEY (quiz_id) REFERENCES quizzes (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT quiz_attempts_enrollment_id_fk FOREIGN KEY (enrollment_id) REFERENCES enrollments (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT quiz_attempts_invalidated_by_fk FOREIGN KEY (invalidated_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT quiz_attempts_status_chk CHECK (status IN ('started', 'submitted', 'graded', 'invalidated')),
    CONSTRAINT quiz_attempts_score_chk CHECK (score IS NULL OR (score >= 0.00 AND score <= 100.00))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE quiz_answers (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    attempt_id BIGINT UNSIGNED NOT NULL,
    question_id BIGINT UNSIGNED NOT NULL,
    answer_payload JSON NULL,
    is_correct TINYINT(1) NULL,
    score_awarded DECIMAL(5,2) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY quiz_answers_attempt_question_unique (attempt_id, question_id),
    CONSTRAINT quiz_answers_attempt_id_fk FOREIGN KEY (attempt_id) REFERENCES quiz_attempts (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT quiz_answers_question_id_fk FOREIGN KEY (question_id) REFERENCES quiz_questions (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT quiz_answers_score_awarded_chk CHECK (score_awarded IS NULL OR score_awarded >= 0.00)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE assignments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    class_id BIGINT UNSIGNED NOT NULL,
    lesson_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    instructions LONGTEXT NULL,
    due_at DATETIME NULL,
    is_required TINYINT(1) NOT NULL DEFAULT 0,
    max_attempts INT UNSIGNED NOT NULL DEFAULT 1,
    allowed_mimes JSON NULL,
    max_file_size_bytes BIGINT UNSIGNED NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'draft',
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY assignments_class_status_idx (class_id, status),
    KEY assignments_lesson_id_idx (lesson_id),
    KEY assignments_due_at_idx (due_at),
    CONSTRAINT assignments_class_id_fk FOREIGN KEY (class_id) REFERENCES learning_classes (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT assignments_lesson_id_fk FOREIGN KEY (lesson_id) REFERENCES lessons (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT assignments_created_by_fk FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT assignments_updated_by_fk FOREIGN KEY (updated_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE assignment_submissions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    assignment_id BIGINT UNSIGNED NOT NULL,
    enrollment_id BIGINT UNSIGNED NOT NULL,
    attempt_number INT UNSIGNED NOT NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'draft',
    content LONGTEXT NULL,
    submitted_at DATETIME NULL,
    graded_at DATETIME NULL,
    graded_by BIGINT UNSIGNED NULL,
    score DECIMAL(5,2) NULL,
    feedback LONGTEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY assignment_submissions_unique (assignment_id, enrollment_id, attempt_number),
    KEY assignment_submissions_enrollment_status_idx (enrollment_id, status),
    KEY assignment_submissions_grader_status_idx (graded_by, status),
    CONSTRAINT assignment_submissions_assignment_id_fk FOREIGN KEY (assignment_id) REFERENCES assignments (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT assignment_submissions_enrollment_id_fk FOREIGN KEY (enrollment_id) REFERENCES enrollments (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT assignment_submissions_graded_by_fk FOREIGN KEY (graded_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT assignment_submissions_status_chk CHECK (status IN ('draft', 'submitted', 'in_review', 'revision_required', 'graded', 'cancelled')),
    CONSTRAINT assignment_submissions_score_chk CHECK (score IS NULL OR (score >= 0.00 AND score <= 100.00))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE assignment_submission_files (
    submission_id BIGINT UNSIGNED NOT NULL,
    file_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (submission_id, file_id),
    KEY assignment_submission_files_file_id_idx (file_id),
    CONSTRAINT assignment_submission_files_submission_id_fk FOREIGN KEY (submission_id) REFERENCES assignment_submissions (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT assignment_submission_files_file_id_fk FOREIGN KEY (file_id) REFERENCES file_assets (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE learning_progress (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    enrollment_id BIGINT UNSIGNED NOT NULL,
    lesson_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'not_started',
    started_at DATETIME NULL,
    completed_at DATETIME NULL,
    progress_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY learning_progress_enrollment_lesson_unique (enrollment_id, lesson_id),
    KEY learning_progress_enrollment_status_idx (enrollment_id, status),
    CONSTRAINT learning_progress_enrollment_id_fk FOREIGN KEY (enrollment_id) REFERENCES enrollments (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT learning_progress_lesson_id_fk FOREIGN KEY (lesson_id) REFERENCES lessons (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT learning_progress_status_chk CHECK (status IN ('not_started', 'in_progress', 'completed')),
    CONSTRAINT learning_progress_percent_chk CHECK (progress_percent >= 0.00 AND progress_percent <= 100.00)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE feedbacks (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    enrollment_id BIGINT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NULL,
    comment TEXT NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'not_started',
    submitted_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY feedbacks_enrollment_id_unique (enrollment_id),
    CONSTRAINT feedbacks_enrollment_id_fk FOREIGN KEY (enrollment_id) REFERENCES enrollments (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT feedbacks_status_chk CHECK (status IN ('not_started', 'submitted')),
    CONSTRAINT feedbacks_rating_chk CHECK (rating IS NULL OR (rating >= 1 AND rating <= 5))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE knowledge_categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    parent_id BIGINT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY knowledge_categories_slug_unique (slug),
    KEY knowledge_categories_parent_active_idx (parent_id, is_active),
    CONSTRAINT knowledge_categories_parent_id_fk FOREIGN KEY (parent_id) REFERENCES knowledge_categories (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE knowledge_tags (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY knowledge_tags_slug_unique (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE knowledge_articles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    type VARCHAR(64) NOT NULL DEFAULT 'article',
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    summary TEXT NULL,
    content LONGTEXT NULL,
    workflow_status VARCHAR(64) NOT NULL DEFAULT 'draft',
    visibility VARCHAR(64) NOT NULL DEFAULT 'internal',
    featured TINYINT(1) NOT NULL DEFAULT 0,
    carousel_enabled TINYINT(1) NOT NULL DEFAULT 0,
    carousel_order INT UNSIGNED NULL,
    cover_file_id BIGINT UNSIGNED NULL,
    author_id BIGINT UNSIGNED NOT NULL,
    reviewed_by BIGINT UNSIGNED NULL,
    published_by BIGINT UNSIGNED NULL,
    published_at DATETIME NULL,
    archived_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY knowledge_articles_slug_unique (slug),
    KEY knowledge_articles_publication_idx (type, workflow_status, visibility, published_at),
    KEY knowledge_articles_featured_carousel_idx (featured, carousel_enabled, carousel_order),
    CONSTRAINT knowledge_articles_cover_file_id_fk FOREIGN KEY (cover_file_id) REFERENCES file_assets (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT knowledge_articles_author_id_fk FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT knowledge_articles_reviewed_by_fk FOREIGN KEY (reviewed_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT knowledge_articles_published_by_fk FOREIGN KEY (published_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT knowledge_articles_type_chk CHECK (type IN ('article', 'faq', 'case_study', 'template')),
    CONSTRAINT knowledge_articles_workflow_chk CHECK (workflow_status IN ('draft', 'submitted', 'in_review', 'approved', 'published', 'revision_required', 'rejected', 'archived')),
    CONSTRAINT knowledge_articles_visibility_chk CHECK (visibility IN ('public', 'internal', 'restricted'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE knowledge_article_categories (
    article_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (article_id, category_id),
    KEY knowledge_article_categories_category_id_idx (category_id),
    CONSTRAINT knowledge_article_categories_article_id_fk FOREIGN KEY (article_id) REFERENCES knowledge_articles (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT knowledge_article_categories_category_id_fk FOREIGN KEY (category_id) REFERENCES knowledge_categories (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE knowledge_article_tags (
    article_id BIGINT UNSIGNED NOT NULL,
    tag_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (article_id, tag_id),
    KEY knowledge_article_tags_tag_id_idx (tag_id),
    CONSTRAINT knowledge_article_tags_article_id_fk FOREIGN KEY (article_id) REFERENCES knowledge_articles (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT knowledge_article_tags_tag_id_fk FOREIGN KEY (tag_id) REFERENCES knowledge_tags (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE knowledge_media (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    article_id BIGINT UNSIGNED NULL,
    type VARCHAR(64) NOT NULL,
    title VARCHAR(255) NULL,
    caption TEXT NULL,
    alt_text VARCHAR(500) NULL,
    file_id BIGINT UNSIGNED NULL,
    external_url VARCHAR(2048) NULL,
    thumbnail_file_id BIGINT UNSIGNED NULL,
    workflow_status VARCHAR(64) NOT NULL DEFAULT 'draft',
    visibility VARCHAR(64) NOT NULL DEFAULT 'internal',
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    featured TINYINT(1) NOT NULL DEFAULT 0,
    carousel_enabled TINYINT(1) NOT NULL DEFAULT 0,
    created_by BIGINT UNSIGNED NULL,
    published_by BIGINT UNSIGNED NULL,
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY knowledge_media_article_id_idx (article_id),
    KEY knowledge_media_publication_idx (type, workflow_status, visibility, published_at),
    CONSTRAINT knowledge_media_article_id_fk FOREIGN KEY (article_id) REFERENCES knowledge_articles (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT knowledge_media_file_id_fk FOREIGN KEY (file_id) REFERENCES file_assets (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT knowledge_media_thumbnail_file_id_fk FOREIGN KEY (thumbnail_file_id) REFERENCES file_assets (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT knowledge_media_created_by_fk FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT knowledge_media_published_by_fk FOREIGN KEY (published_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT knowledge_media_type_chk CHECK (type IN ('image', 'poster', 'flyer', 'infographic', 'gallery', 'video_file', 'youtube', 'social_link', 'external_link')),
    CONSTRAINT knowledge_media_workflow_chk CHECK (workflow_status IN ('draft', 'submitted', 'in_review', 'approved', 'published', 'revision_required', 'rejected', 'archived')),
    CONSTRAINT knowledge_media_visibility_chk CHECK (visibility IN ('public', 'internal', 'restricted'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE regulation_categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY regulation_categories_slug_unique (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE regulations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    document_number VARCHAR(255) NOT NULL,
    document_year SMALLINT UNSIGNED NOT NULL,
    issuer_name VARCHAR(255) NOT NULL,
    official_date DATETIME NULL,
    effective_date DATETIME NULL,
    summary TEXT NULL,
    official_source_url VARCHAR(2048) NULL,
    workflow_status VARCHAR(64) NOT NULL DEFAULT 'draft',
    legal_status VARCHAR(64) NOT NULL DEFAULT 'active',
    visibility VARCHAR(64) NOT NULL DEFAULT 'internal',
    current_version_id BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NULL,
    reviewed_by BIGINT UNSIGNED NULL,
    published_by BIGINT UNSIGNED NULL,
    published_at DATETIME NULL,
    archived_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY regulations_slug_unique (slug),
    UNIQUE KEY regulations_document_unique (document_number, document_year, issuer_name),
    KEY regulations_category_workflow_legal_visibility_idx (category_id, workflow_status, legal_status, visibility),
    KEY regulations_published_at_idx (published_at),
    CONSTRAINT regulations_category_id_fk FOREIGN KEY (category_id) REFERENCES regulation_categories (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT regulations_created_by_fk FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT regulations_reviewed_by_fk FOREIGN KEY (reviewed_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT regulations_published_by_fk FOREIGN KEY (published_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT regulations_workflow_chk CHECK (workflow_status IN ('draft', 'submitted', 'in_review', 'approved', 'published', 'revision_required', 'rejected', 'archived')),
    CONSTRAINT regulations_legal_status_chk CHECK (legal_status IN ('active', 'amended', 'revoked', 'expired')),
    CONSTRAINT regulations_visibility_chk CHECK (visibility IN ('public', 'internal', 'restricted'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE regulation_versions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    regulation_id BIGINT UNSIGNED NOT NULL,
    version_number INT UNSIGNED NOT NULL,
    file_id BIGINT UNSIGNED NULL,
    changelog TEXT NULL,
    is_current TINYINT(1) NOT NULL DEFAULT 0,
    uploaded_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY regulation_versions_regulation_version_unique (regulation_id, version_number),
    KEY regulation_versions_regulation_current_idx (regulation_id, is_current),
    CONSTRAINT regulation_versions_regulation_id_fk FOREIGN KEY (regulation_id) REFERENCES regulations (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT regulation_versions_file_id_fk FOREIGN KEY (file_id) REFERENCES file_assets (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT regulation_versions_uploaded_by_fk FOREIGN KEY (uploaded_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE regulations
    ADD CONSTRAINT regulations_current_version_id_fk
    FOREIGN KEY (current_version_id) REFERENCES regulation_versions (id)
    ON DELETE SET NULL ON UPDATE CASCADE;

CREATE TABLE regulation_relations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    source_regulation_id BIGINT UNSIGNED NOT NULL,
    target_regulation_id BIGINT UNSIGNED NOT NULL,
    relation_type VARCHAR(64) NOT NULL,
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY regulation_relations_unique (source_regulation_id, target_regulation_id, relation_type),
    KEY regulation_relations_target_idx (target_regulation_id),
    CONSTRAINT regulation_relations_source_id_fk FOREIGN KEY (source_regulation_id) REFERENCES regulations (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT regulation_relations_target_id_fk FOREIGN KEY (target_regulation_id) REFERENCES regulations (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT regulation_relations_created_by_fk FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    -- CHECK dihapus karena MySQL tidak mengizinkan kombinasi ini dengan FK
    CONSTRAINT regulation_relations_type_chk CHECK (relation_type IN ('amends', 'amended_by', 'revokes', 'revoked_by', 'replaces', 'replaced_by', 'implements', 'implemented_by', 'references', 'referenced_by'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE regulation_downloads (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    regulation_id BIGINT UNSIGNED NOT NULL,
    version_id BIGINT UNSIGNED NULL,
    file_id BIGINT UNSIGNED NULL,
    user_id BIGINT UNSIGNED NULL,
    access_type VARCHAR(64) NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY regulation_downloads_regulation_created_idx (regulation_id, created_at),
    KEY regulation_downloads_user_created_idx (user_id, created_at),
    CONSTRAINT regulation_downloads_regulation_id_fk FOREIGN KEY (regulation_id) REFERENCES regulations (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT regulation_downloads_version_id_fk FOREIGN KEY (version_id) REFERENCES regulation_versions (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT regulation_downloads_file_id_fk FOREIGN KEY (file_id) REFERENCES file_assets (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT regulation_downloads_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE news_categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY news_categories_slug_unique (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE news (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    summary TEXT NULL,
    content LONGTEXT NULL,
    cover_file_id BIGINT UNSIGNED NULL,
    workflow_status VARCHAR(64) NOT NULL DEFAULT 'draft',
    visibility VARCHAR(64) NOT NULL DEFAULT 'internal',
    published_at DATETIME NULL,
    scheduled_at DATETIME NULL,
    view_count BIGINT UNSIGNED NOT NULL DEFAULT 0,
    author_id BIGINT UNSIGNED NULL,
    reviewed_by BIGINT UNSIGNED NULL,
    published_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY news_slug_unique (slug),
    KEY news_category_publication_idx (category_id, workflow_status, visibility, published_at),
    CONSTRAINT news_category_id_fk FOREIGN KEY (category_id) REFERENCES news_categories (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT news_cover_file_id_fk FOREIGN KEY (cover_file_id) REFERENCES file_assets (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT news_author_id_fk FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT news_reviewed_by_fk FOREIGN KEY (reviewed_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT news_published_by_fk FOREIGN KEY (published_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT news_workflow_chk CHECK (workflow_status IN ('draft', 'submitted', 'in_review', 'approved', 'published', 'revision_required', 'rejected', 'archived')),
    CONSTRAINT news_visibility_chk CHECK (visibility IN ('public', 'internal', 'restricted'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE events (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    type VARCHAR(64) NOT NULL,
    mode VARCHAR(32) NOT NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'draft',
    visibility VARCHAR(64) NOT NULL DEFAULT 'internal',
    summary TEXT NULL,
    description LONGTEXT NULL,
    cover_file_id BIGINT UNSIGNED NULL,
    start_at DATETIME NOT NULL,
    end_at DATETIME NOT NULL,
    location VARCHAR(500) NULL,
    meeting_url VARCHAR(2048) NULL,
    registration_open_at DATETIME NULL,
    registration_close_at DATETIME NULL,
    capacity INT UNSIGNED NULL,
    waitlist_enabled TINYINT(1) NOT NULL DEFAULT 0,
    certificate_enabled TINYINT(1) NOT NULL DEFAULT 0,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    cancelled_by BIGINT UNSIGNED NULL,
    cancelled_at DATETIME NULL,
    cancellation_reason TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY events_slug_unique (slug),
    KEY events_status_visibility_start_idx (status, visibility, start_at),
    KEY events_registration_period_idx (registration_open_at, registration_close_at),
    CONSTRAINT events_cover_file_id_fk FOREIGN KEY (cover_file_id) REFERENCES file_assets (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT events_created_by_fk FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT events_updated_by_fk FOREIGN KEY (updated_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT events_cancelled_by_fk FOREIGN KEY (cancelled_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT events_mode_chk CHECK (mode IN ('online', 'offline', 'hybrid')),
    CONSTRAINT events_status_chk CHECK (status IN ('draft', 'coming_soon', 'open_registration', 'full', 'finished', 'cancelled')),
    CONSTRAINT events_visibility_chk CHECK (visibility IN ('public', 'internal', 'restricted')),
    CONSTRAINT events_period_chk CHECK (end_at >= start_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE event_registrations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    event_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'registered',
    registered_at DATETIME NOT NULL,
    cancelled_at DATETIME NULL,
    promoted_at DATETIME NULL,
    status_reason TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY event_registrations_event_user_unique (event_id, user_id),
    KEY event_registrations_event_status_registered_idx (event_id, status, registered_at),
    KEY event_registrations_user_status_idx (user_id, status),
    CONSTRAINT event_registrations_event_id_fk FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT event_registrations_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT event_registrations_status_chk CHECK (status IN ('registered', 'waitlisted', 'cancelled', 'rejected'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE attendances (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    event_registration_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(64) NOT NULL,
    recorded_by BIGINT UNSIGNED NULL,
    recorded_at DATETIME NOT NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY attendances_event_registration_unique (event_registration_id),
    CONSTRAINT attendances_event_registration_id_fk FOREIGN KEY (event_registration_id) REFERENCES event_registrations (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT attendances_recorded_by_fk FOREIGN KEY (recorded_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT attendances_status_chk CHECK (status IN ('present', 'absent', 'excused'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE certificate_templates (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    source_type VARCHAR(64) NOT NULL,
    template_payload JSON NOT NULL,
    background_file_id BIGINT UNSIGNED NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY certificate_templates_source_active_idx (source_type, is_active),
    CONSTRAINT certificate_templates_background_file_id_fk FOREIGN KEY (background_file_id) REFERENCES file_assets (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT certificate_templates_created_by_fk FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT certificate_templates_updated_by_fk FOREIGN KEY (updated_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT certificate_templates_source_type_chk CHECK (source_type IN ('learning', 'event'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE certificates (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    source_type VARCHAR(64) NOT NULL,
    source_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    template_id BIGINT UNSIGNED NULL,
    certificate_number VARCHAR(128) NOT NULL,
    verification_token_hash CHAR(64) NOT NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'pending',
    issued_at DATETIME NULL,
    expires_at DATETIME NULL,
    revoked_at DATETIME NULL,
    revoked_by BIGINT UNSIGNED NULL,
    revocation_reason TEXT NULL,
    pdf_file_id BIGINT UNSIGNED NULL,
    replaced_by_certificate_id BIGINT UNSIGNED NULL,
    issued_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY certificates_number_unique (certificate_number),
    UNIQUE KEY certificates_verification_token_unique (verification_token_hash),
    UNIQUE KEY certificates_source_unique (source_type, source_id),
    KEY certificates_user_status_idx (user_id, status),
    KEY certificates_status_expires_idx (status, expires_at),
    CONSTRAINT certificates_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT certificates_template_id_fk FOREIGN KEY (template_id) REFERENCES certificate_templates (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT certificates_revoked_by_fk FOREIGN KEY (revoked_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT certificates_pdf_file_id_fk FOREIGN KEY (pdf_file_id) REFERENCES file_assets (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT certificates_replaced_by_fk FOREIGN KEY (replaced_by_certificate_id) REFERENCES certificates (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT certificates_issued_by_fk FOREIGN KEY (issued_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT certificates_source_type_chk CHECK (source_type IN ('learning_enrollment', 'event_registration')),
    CONSTRAINT certificates_status_chk CHECK (status IN ('pending', 'issued', 'revoked', 'expired'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE competencies (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(64) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY competencies_key_unique (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE jf_ppbj_levels (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(64) NOT NULL,
    name VARCHAR(255) NOT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY jf_ppbj_levels_key_unique (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE competency_certificate_types (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(64) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY competency_certificate_types_key_unique (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE jf_ppbj_records (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NULL,
    organization_unit_id BIGINT UNSIGNED NOT NULL,
    employee_identifier VARCHAR(128) NOT NULL,
    name_snapshot VARCHAR(255) NOT NULL,
    level_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'active',
    effective_at DATETIME NOT NULL,
    ended_at DATETIME NULL,
    change_type VARCHAR(64) NOT NULL,
    evidence_file_id BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY jf_ppbj_records_org_level_status_idx (organization_unit_id, level_id, status),
    KEY jf_ppbj_records_user_id_idx (user_id),
    KEY jf_ppbj_records_period_idx (effective_at, ended_at),
    CONSTRAINT jf_ppbj_records_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT jf_ppbj_records_organization_unit_id_fk FOREIGN KEY (organization_unit_id) REFERENCES organization_units (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT jf_ppbj_records_level_id_fk FOREIGN KEY (level_id) REFERENCES jf_ppbj_levels (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT jf_ppbj_records_evidence_file_id_fk FOREIGN KEY (evidence_file_id) REFERENCES file_assets (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT jf_ppbj_records_created_by_fk FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT jf_ppbj_records_updated_by_fk FOREIGN KEY (updated_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT jf_ppbj_records_change_type_chk CHECK (change_type IN ('addition', 'promotion', 'transfer_in', 'transfer_out', 'retirement', 'deactivation', 'update'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE competency_certificates (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    jf_ppbj_record_id BIGINT UNSIGNED NOT NULL,
    certificate_type_id BIGINT UNSIGNED NOT NULL,
    certificate_number VARCHAR(128) NULL,
    issued_at DATETIME NULL,
    expires_at DATETIME NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'pending',
    evidence_file_id BIGINT UNSIGNED NULL,
    verified_by BIGINT UNSIGNED NULL,
    verified_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY competency_certificates_record_status_idx (jf_ppbj_record_id, status),
    KEY competency_certificates_type_expires_idx (certificate_type_id, expires_at),
    CONSTRAINT competency_certificates_record_id_fk FOREIGN KEY (jf_ppbj_record_id) REFERENCES jf_ppbj_records (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT competency_certificates_type_id_fk FOREIGN KEY (certificate_type_id) REFERENCES competency_certificate_types (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT competency_certificates_evidence_file_id_fk FOREIGN KEY (evidence_file_id) REFERENCES file_assets (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT competency_certificates_verified_by_fk FOREIGN KEY (verified_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT competency_certificates_status_chk CHECK (status IN ('pending', 'verified', 'rejected', 'expired'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE competency_gap_snapshots (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    organization_unit_id BIGINT UNSIGNED NOT NULL,
    competency_id BIGINT UNSIGNED NULL,
    period_date DATE NOT NULL,
    required_count INT UNSIGNED NOT NULL DEFAULT 0,
    available_count INT UNSIGNED NOT NULL DEFAULT 0,
    gap_count INT NOT NULL DEFAULT 0,
    metadata JSON NULL,
    generated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY competency_gap_snapshots_unique (organization_unit_id, competency_id, period_date),
    KEY competency_gap_snapshots_period_idx (period_date),
    CONSTRAINT competency_gap_snapshots_organization_id_fk FOREIGN KEY (organization_unit_id) REFERENCES organization_units (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT competency_gap_snapshots_competency_id_fk FOREIGN KEY (competency_id) REFERENCES competencies (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT competency_gap_snapshots_generated_by_fk FOREIGN KEY (generated_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT competency_gap_snapshots_gap_chk CHECK (gap_count = required_count - available_count)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional full-text indexes may be enabled after engine/version validation:
-- ALTER TABLE knowledge_articles ADD FULLTEXT KEY knowledge_articles_search_ft (title, summary, content);
-- ALTER TABLE regulations ADD FULLTEXT KEY regulations_search_ft (title, document_number, issuer_name, summary);
-- ALTER TABLE news ADD FULLTEXT KEY news_search_ft (title, summary, content);

SET time_zone = '+00:00';
