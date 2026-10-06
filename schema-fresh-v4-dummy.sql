-- ============================================================================
-- Dummy Seed: Platform Pembelajaran & Knowledge Provinsi Lampung
-- MySQL 8+ / MariaDB yang mendukung JSON dan CHECK constraint
-- Hanya untuk local/development. Password seluruh akun: password
-- ============================================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET @seed_now := UTC_TIMESTAMP();
START TRANSACTION;

-- ----------------------------------------------------------------------------
-- 1) ORGANIZATION UNITS
-- ----------------------------------------------------------------------------
INSERT INTO organization_units
    (parent_id, code, name, type, is_active, sort_order, created_at, updated_at)
VALUES
    (NULL, 'LPG-ROOT', 'Pemerintah Provinsi Lampung', 'province', 1, 1, @seed_now, @seed_now),
    ((SELECT id FROM organization_units ou WHERE ou.code = 'LPG-ROOT' LIMIT 1), 'LPG-BPSDM', 'Badan Pengembangan Sumber Daya Manusia Daerah Provinsi Lampung', 'opd', 1, 8, @seed_now, @seed_now),
    ((SELECT id FROM organization_units ou WHERE ou.code = 'LPG-ROOT' LIMIT 1), 'LPG-BIRO-PBJ', 'Biro Pengadaan Barang dan Jasa', 'bureau', 1, 17, @seed_now, @seed_now),
    ((SELECT id FROM organization_units ou WHERE ou.code = 'LPG-ROOT' LIMIT 1), 'LPG-DISKOMINFO', 'Dinas Komunikasi Informatika dan Statistika Provinsi Lampung', 'opd', 1, 27, @seed_now, @seed_now),
    ((SELECT id FROM organization_units ou WHERE ou.code = 'LPG-ROOT' LIMIT 1), 'LPG-INSPEKTORAT', 'Inspektorat Provinsi Lampung', 'opd', 1, 45, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE
    name = VALUES(name), type = VALUES(type), is_active = VALUES(is_active),
    sort_order = VALUES(sort_order), updated_at = VALUES(updated_at);

-- ----------------------------------------------------------------------------
-- 2) USERS
-- bcrypt hash di bawah adalah hash Laravel umum untuk password: password
-- ----------------------------------------------------------------------------
INSERT INTO users
    (name, email, username, password_hash, phone, profile_type, account_status,
     email_verified_at, last_login_at, session_version, status_reason,
     created_at, updated_at, deleted_at)
VALUES
    ('Super Administrator', 'superadmin@example.test', 'superadmin', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', '081200000001', 'asn', 'active', @seed_now, NULL, 1, NULL, @seed_now, @seed_now, NULL),
    ('Editor Konten', 'content.editor@example.test', 'content.editor', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', '081200000002', 'asn', 'active', @seed_now, NULL, 1, NULL, @seed_now, @seed_now, NULL),
    ('Administrator Pembelajaran', 'learning.admin@example.test', 'learning.admin', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', '081200000003', 'asn', 'active', @seed_now, NULL, 1, NULL, @seed_now, @seed_now, NULL),
    ('Ahmad Fauzi', 'trainer.ahmad@example.test', 'trainer.ahmad', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', '081200000004', 'academic', 'active', @seed_now, NULL, 1, NULL, @seed_now, @seed_now, NULL),
    ('Siti Rahmawati', 'evaluator.siti@example.test', 'evaluator.siti', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', '081200000005', 'academic', 'active', @seed_now, NULL, 1, NULL, @seed_now, @seed_now, NULL),
    ('Administrator Event', 'event.admin@example.test', 'event.admin', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', '081200000006', 'asn', 'active', @seed_now, NULL, 1, NULL, @seed_now, @seed_now, NULL),
    ('Administrator Kompetensi', 'kompetensi.admin@example.test', 'kompetensi.admin', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', '081200000007', 'asn', 'active', @seed_now, NULL, 1, NULL, @seed_now, @seed_now, NULL),
    ('Budi Santoso', 'peserta.budi@example.test', 'peserta.budi', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', '081200000008', 'asn', 'active', @seed_now, NULL, 1, NULL, @seed_now, @seed_now, NULL),
    ('Ayu Lestari', 'peserta.ayu@example.test', 'peserta.ayu', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', '081200000009', 'asn', 'active', @seed_now, NULL, 1, NULL, @seed_now, @seed_now, NULL),
    ('Pengguna Publik', 'public.visitor@example.test', 'public.visitor', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', '081200000010', 'public', 'active', @seed_now, NULL, 1, NULL, @seed_now, @seed_now, NULL),
    ('Akun Menunggu Verifikasi', 'pending@example.test', 'account.pending', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', '081200000011', 'asn', 'pending_verification', NULL, NULL, 1, 'Menunggu verifikasi alamat email.', @seed_now, @seed_now, NULL),
    ('Akun Ditangguhkan', 'suspended@example.test', 'account.suspended', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', '081200000012', 'public', 'suspended', @seed_now, NULL, 1, 'Contoh akun yang ditangguhkan.', @seed_now, @seed_now, NULL)
ON DUPLICATE KEY UPDATE
    name = VALUES(name), phone = VALUES(phone), profile_type = VALUES(profile_type),
    account_status = VALUES(account_status), updated_at = VALUES(updated_at);

SET @admin_id := (SELECT id FROM users WHERE username = 'superadmin' LIMIT 1);
SET @editor_id := (SELECT id FROM users WHERE username = 'content.editor' LIMIT 1);
SET @learning_admin_id := (SELECT id FROM users WHERE username = 'learning.admin' LIMIT 1);
SET @trainer_id := (SELECT id FROM users WHERE username = 'trainer.ahmad' LIMIT 1);
SET @evaluator_id := (SELECT id FROM users WHERE username = 'evaluator.siti' LIMIT 1);
SET @event_admin_id := (SELECT id FROM users WHERE username = 'event.admin' LIMIT 1);
SET @competency_admin_id := (SELECT id FROM users WHERE username = 'kompetensi.admin' LIMIT 1);
SET @budi_id := (SELECT id FROM users WHERE username = 'peserta.budi' LIMIT 1);
SET @ayu_id := (SELECT id FROM users WHERE username = 'peserta.ayu' LIMIT 1);
SET @public_id := (SELECT id FROM users WHERE username = 'public.visitor' LIMIT 1);
SET @suspended_id := (SELECT id FROM users WHERE username = 'account.suspended' LIMIT 1);

-- ----------------------------------------------------------------------------
-- 3) RBAC: ROLES, PERMISSIONS, ROLE PERMISSIONS, USER ROLES
-- ----------------------------------------------------------------------------
INSERT INTO roles (`key`, name, description, is_system, created_at, updated_at) VALUES
    ('super_admin', 'Super Administrator', 'Akses penuh untuk seluruh modul.', 1, @seed_now, @seed_now),
    ('content_admin', 'Administrator Konten', 'Mengelola Knowledge, regulasi, dan berita.', 1, @seed_now, @seed_now),
    ('learning_admin', 'Administrator Pembelajaran', 'Mengelola program, course, kelas, dan peserta.', 1, @seed_now, @seed_now),
    ('trainer', 'Trainer', 'Mengampu kelas pembelajaran.', 1, @seed_now, @seed_now),
    ('evaluator', 'Evaluator', 'Menilai assignment peserta.', 1, @seed_now, @seed_now),
    ('event_admin', 'Administrator Event', 'Mengelola event dan kehadiran.', 1, @seed_now, @seed_now),
    ('competency_admin', 'Administrator Kompetensi', 'Mengelola JF-PPBJ dan kompetensi.', 1, @seed_now, @seed_now),
    ('participant', 'Peserta', 'Peserta pembelajaran dan event.', 1, @seed_now, @seed_now),
    ('public_user', 'Pengguna Publik', 'Akses konten publik.', 1, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), updated_at = VALUES(updated_at);

INSERT INTO permissions (`key`, name, domain, description, is_system, created_at, updated_at) VALUES
    ('users.view', 'Lihat Pengguna', 'users', 'Melihat pengguna.', 1, @seed_now, @seed_now),
    ('users.manage', 'Kelola Pengguna', 'users', 'Mengelola pengguna.', 1, @seed_now, @seed_now),
    ('roles.view', 'Lihat Role', 'roles', 'Melihat role dan permission.', 1, @seed_now, @seed_now),
    ('roles.manage', 'Kelola Role', 'roles', 'Mengelola role dan permission.', 1, @seed_now, @seed_now),
    ('organizations.view', 'Lihat Organisasi', 'organizations', 'Melihat unit organisasi.', 1, @seed_now, @seed_now),
    ('organizations.manage', 'Kelola Organisasi', 'organizations', 'Mengelola unit organisasi.', 1, @seed_now, @seed_now),
    ('files.upload', 'Unggah File', 'files', 'Mengunggah file.', 1, @seed_now, @seed_now),
    ('files.manage', 'Kelola File', 'files', 'Mengelola asset file.', 1, @seed_now, @seed_now),
    ('learning.view', 'Lihat Pembelajaran', 'learning', 'Melihat pembelajaran.', 1, @seed_now, @seed_now),
    ('learning.manage', 'Kelola Pembelajaran', 'learning', 'Mengelola pembelajaran.', 1, @seed_now, @seed_now),
    ('learning.train', 'Mengajar', 'learning', 'Mengajar kelas.', 1, @seed_now, @seed_now),
    ('learning.evaluate', 'Evaluasi', 'learning', 'Menilai peserta.', 1, @seed_now, @seed_now),
    ('knowledge.view', 'Lihat Knowledge', 'knowledge', 'Melihat knowledge.', 1, @seed_now, @seed_now),
    ('knowledge.manage', 'Kelola Knowledge', 'knowledge', 'Mengelola knowledge.', 1, @seed_now, @seed_now),
    ('regulations.view', 'Lihat Regulasi', 'regulations', 'Melihat regulasi.', 1, @seed_now, @seed_now),
    ('regulations.manage', 'Kelola Regulasi', 'regulations', 'Mengelola regulasi.', 1, @seed_now, @seed_now),
    ('news.view', 'Lihat Berita', 'news', 'Melihat berita.', 1, @seed_now, @seed_now),
    ('news.manage', 'Kelola Berita', 'news', 'Mengelola berita.', 1, @seed_now, @seed_now),
    ('events.view', 'Lihat Event', 'events', 'Melihat event.', 1, @seed_now, @seed_now),
    ('events.manage', 'Kelola Event', 'events', 'Mengelola event.', 1, @seed_now, @seed_now),
    ('certificates.view', 'Lihat Sertifikat', 'certificates', 'Melihat sertifikat.', 1, @seed_now, @seed_now),
    ('certificates.manage', 'Kelola Sertifikat', 'certificates', 'Mengelola sertifikat.', 1, @seed_now, @seed_now),
    ('competencies.view', 'Lihat Kompetensi', 'competencies', 'Melihat kompetensi.', 1, @seed_now, @seed_now),
    ('competencies.manage', 'Kelola Kompetensi', 'competencies', 'Mengelola kompetensi.', 1, @seed_now, @seed_now),
    ('exports.request', 'Minta Ekspor', 'exports', 'Meminta ekspor.', 1, @seed_now, @seed_now),
    ('audit.view', 'Lihat Audit', 'audit', 'Melihat audit log.', 1, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE name = VALUES(name), domain = VALUES(domain), description = VALUES(description), updated_at = VALUES(updated_at);

-- Super admin mendapat semua permission.
INSERT IGNORE INTO role_permissions (role_id, permission_id, granted_by, created_at)
SELECT r.id, p.id, @admin_id, @seed_now
FROM roles r CROSS JOIN permissions p
WHERE r.`key` = 'super_admin';

-- Pemetaan role terbatas.
INSERT IGNORE INTO role_permissions (role_id, permission_id, granted_by, created_at)
SELECT r.id, p.id, @admin_id, @seed_now
FROM roles r JOIN permissions p ON p.`key` IN ('knowledge.view','knowledge.manage','regulations.view','regulations.manage','news.view','news.manage','files.upload','files.manage')
WHERE r.`key` = 'content_admin';

INSERT IGNORE INTO role_permissions (role_id, permission_id, granted_by, created_at)
SELECT r.id, p.id, @admin_id, @seed_now
FROM roles r JOIN permissions p ON p.`key` IN ('learning.view','learning.manage','learning.train','learning.evaluate','certificates.view','files.upload')
WHERE r.`key` = 'learning_admin';

INSERT IGNORE INTO role_permissions (role_id, permission_id, granted_by, created_at)
SELECT r.id, p.id, @admin_id, @seed_now
FROM roles r JOIN permissions p ON p.`key` IN ('learning.view','learning.train')
WHERE r.`key` = 'trainer';

INSERT IGNORE INTO role_permissions (role_id, permission_id, granted_by, created_at)
SELECT r.id, p.id, @admin_id, @seed_now
FROM roles r JOIN permissions p ON p.`key` IN ('learning.view','learning.evaluate')
WHERE r.`key` = 'evaluator';

INSERT IGNORE INTO role_permissions (role_id, permission_id, granted_by, created_at)
SELECT r.id, p.id, @admin_id, @seed_now
FROM roles r JOIN permissions p ON p.`key` IN ('events.view','events.manage','certificates.view','certificates.manage','files.upload')
WHERE r.`key` = 'event_admin';

INSERT IGNORE INTO role_permissions (role_id, permission_id, granted_by, created_at)
SELECT r.id, p.id, @admin_id, @seed_now
FROM roles r JOIN permissions p ON p.`key` IN ('competencies.view','competencies.manage','certificates.view','files.upload')
WHERE r.`key` = 'competency_admin';

INSERT IGNORE INTO role_permissions (role_id, permission_id, granted_by, created_at)
SELECT r.id, p.id, @admin_id, @seed_now
FROM roles r JOIN permissions p ON p.`key` IN ('learning.view','knowledge.view','regulations.view','news.view','events.view','certificates.view')
WHERE r.`key` = 'participant';

INSERT IGNORE INTO role_permissions (role_id, permission_id, granted_by, created_at)
SELECT r.id, p.id, @admin_id, @seed_now
FROM roles r JOIN permissions p ON p.`key` IN ('knowledge.view','regulations.view','news.view','events.view')
WHERE r.`key` = 'public_user';

-- User-role dengan UNIQUE(user_id, role_id).
INSERT INTO user_roles (user_id, role_id, status, granted_by, granted_at, revoked_by, revoked_at, reason, created_at, updated_at)
SELECT u.id, r.id, 'active', @admin_id, @seed_now, NULL, NULL, NULL, @seed_now, @seed_now
FROM users u JOIN roles r ON (u.username = 'superadmin' AND r.`key` = 'super_admin')
ON DUPLICATE KEY UPDATE status = 'active', revoked_by = NULL, revoked_at = NULL, reason = NULL, updated_at = @seed_now;

INSERT INTO user_roles (user_id, role_id, status, granted_by, granted_at, revoked_by, revoked_at, reason, created_at, updated_at)
SELECT u.id, r.id, 'active', @admin_id, @seed_now, NULL, NULL, NULL, @seed_now, @seed_now
FROM users u JOIN roles r ON (u.username = 'content.editor' AND r.`key` = 'content_admin')
    OR (u.username = 'learning.admin' AND r.`key` = 'learning_admin')
    OR (u.username = 'trainer.ahmad' AND r.`key` = 'trainer')
    OR (u.username = 'evaluator.siti' AND r.`key` = 'evaluator')
    OR (u.username = 'event.admin' AND r.`key` = 'event_admin')
    OR (u.username = 'kompetensi.admin' AND r.`key` = 'competency_admin')
    OR (u.username IN ('peserta.budi','peserta.ayu') AND r.`key` = 'participant')
    OR (u.username = 'public.visitor' AND r.`key` = 'public_user')
ON DUPLICATE KEY UPDATE status = 'active', revoked_by = NULL, revoked_at = NULL, reason = NULL, updated_at = @seed_now;

INSERT INTO user_roles (user_id, role_id, status, granted_by, granted_at, revoked_by, revoked_at, reason, created_at, updated_at)
SELECT @suspended_id, r.id, 'revoked', @admin_id, @seed_now, @admin_id, @seed_now, 'Contoh role yang dicabut.', @seed_now, @seed_now
FROM roles r WHERE r.`key` = 'participant'
ON DUPLICATE KEY UPDATE status = 'revoked', revoked_by = @admin_id, revoked_at = @seed_now, reason = 'Contoh role yang dicabut.', updated_at = @seed_now;

-- ----------------------------------------------------------------------------
-- 4) FILE ASSETS
-- Database record ini TIDAK menciptakan file fisik di storage.
-- ----------------------------------------------------------------------------
INSERT INTO file_assets
    (disk, path, original_name, stored_name, extension, mime_type, size_bytes,
     checksum, visibility, lifecycle_status, resource_type, resource_id,
     uploaded_by, available_at, expires_at, deleted_at, metadata, created_at, updated_at)
VALUES
    ('public', 'seed/covers/program-pengadaan.jpg', 'program-pengadaan.jpg', 'program-pengadaan.jpg', 'jpg', 'image/jpeg', 245760, NULL, 'internal', 'available', NULL, NULL, @admin_id, @seed_now, NULL, NULL, JSON_OBJECT('seeded', TRUE, 'purpose', 'program_cover'), @seed_now, @seed_now),
    ('public', 'seed/covers/course-pengadaan-dasar.jpg', 'course-pengadaan-dasar.jpg', 'course-pengadaan-dasar.jpg', 'jpg', 'image/jpeg', 220000, NULL, 'internal', 'available', NULL, NULL, @admin_id, @seed_now, NULL, NULL, JSON_OBJECT('seeded', TRUE, 'purpose', 'course_cover'), @seed_now, @seed_now),
    ('public', 'seed/covers/article-integritas.jpg', 'article-integritas.jpg', 'article-integritas.jpg', 'jpg', 'image/jpeg', 210000, NULL, 'public', 'available', NULL, NULL, @editor_id, @seed_now, NULL, NULL, JSON_OBJECT('seeded', TRUE, 'purpose', 'article_cover'), @seed_now, @seed_now),
    ('public', 'seed/covers/news-pelatihan.jpg', 'news-pelatihan.jpg', 'news-pelatihan.jpg', 'jpg', 'image/jpeg', 200000, NULL, 'public', 'available', NULL, NULL, @editor_id, @seed_now, NULL, NULL, JSON_OBJECT('seeded', TRUE, 'purpose', 'news_cover'), @seed_now, @seed_now),
    ('public', 'seed/events/webinar-pbj.jpg', 'webinar-pbj.jpg', 'webinar-pbj.jpg', 'jpg', 'image/jpeg', 190000, NULL, 'public', 'available', NULL, NULL, @event_admin_id, @seed_now, NULL, NULL, JSON_OBJECT('seeded', TRUE, 'purpose', 'event_cover'), @seed_now, @seed_now),
    ('public', 'seed/certificates/learning-template.png', 'learning-template.png', 'learning-template.png', 'png', 'image/png', 320000, NULL, 'internal', 'available', NULL, NULL, @learning_admin_id, @seed_now, NULL, NULL, JSON_OBJECT('seeded', TRUE, 'purpose', 'learning_certificate_template'), @seed_now, @seed_now),
    ('public', 'seed/certificates/event-template.png', 'event-template.png', 'event-template.png', 'png', 'image/png', 320000, NULL, 'internal', 'available', NULL, NULL, @event_admin_id, @seed_now, NULL, NULL, JSON_OBJECT('seeded', TRUE, 'purpose', 'event_certificate_template'), @seed_now, @seed_now),
    ('local', 'seed/documents/regulasi-pbj-v1.pdf', 'regulasi-pbj-v1.pdf', 'regulasi-pbj-v1.pdf', 'pdf', 'application/pdf', 512000, NULL, 'internal', 'available', NULL, NULL, @editor_id, @seed_now, NULL, NULL, JSON_OBJECT('seeded', TRUE, 'purpose', 'regulation_document'), @seed_now, @seed_now),
    ('local', 'seed/documents/bukti-jf-ppbj.pdf', 'bukti-jf-ppbj.pdf', 'bukti-jf-ppbj.pdf', 'pdf', 'application/pdf', 180000, NULL, 'restricted', 'available', NULL, NULL, @competency_admin_id, @seed_now, NULL, NULL, JSON_OBJECT('seeded', TRUE, 'purpose', 'competency_evidence'), @seed_now, @seed_now),
    ('local', 'seed/submissions/tugas-peserta-budi.pdf', 'tugas-peserta-budi.pdf', 'tugas-peserta-budi.pdf', 'pdf', 'application/pdf', 160000, NULL, 'restricted', 'available', NULL, NULL, @budi_id, @seed_now, NULL, NULL, JSON_OBJECT('seeded', TRUE, 'purpose', 'assignment_submission'), @seed_now, @seed_now),
    ('local', 'seed/certificates/certifikat-budi.pdf', 'certifikat-budi.pdf', 'certifikat-budi.pdf', 'pdf', 'application/pdf', 155000, NULL, 'restricted', 'available', NULL, NULL, @learning_admin_id, @seed_now, NULL, NULL, JSON_OBJECT('seeded', TRUE, 'purpose', 'certificate_pdf'), @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE original_name = VALUES(original_name), mime_type = VALUES(mime_type), lifecycle_status = VALUES(lifecycle_status), updated_at = VALUES(updated_at);

SET @program_cover_id := (SELECT id FROM file_assets WHERE path = 'seed/covers/program-pengadaan.jpg' LIMIT 1);
SET @course_cover_id := (SELECT id FROM file_assets WHERE path = 'seed/covers/course-pengadaan-dasar.jpg' LIMIT 1);
SET @article_cover_id := (SELECT id FROM file_assets WHERE path = 'seed/covers/article-integritas.jpg' LIMIT 1);
SET @news_cover_id := (SELECT id FROM file_assets WHERE path = 'seed/covers/news-pelatihan.jpg' LIMIT 1);
SET @event_cover_id := (SELECT id FROM file_assets WHERE path = 'seed/events/webinar-pbj.jpg' LIMIT 1);
SET @learning_bg_id := (SELECT id FROM file_assets WHERE path = 'seed/certificates/learning-template.png' LIMIT 1);
SET @event_bg_id := (SELECT id FROM file_assets WHERE path = 'seed/certificates/event-template.png' LIMIT 1);
SET @regulation_pdf_id := (SELECT id FROM file_assets WHERE path = 'seed/documents/regulasi-pbj-v1.pdf' LIMIT 1);
SET @competency_evidence_id := (SELECT id FROM file_assets WHERE path = 'seed/documents/bukti-jf-ppbj.pdf' LIMIT 1);
SET @submission_file_id := (SELECT id FROM file_assets WHERE path = 'seed/submissions/tugas-peserta-budi.pdf' LIMIT 1);
SET @certificate_pdf_id := (SELECT id FROM file_assets WHERE path = 'seed/certificates/certifikat-budi.pdf' LIMIT 1);

-- ----------------------------------------------------------------------------
-- 5) LEARNING
-- ----------------------------------------------------------------------------
INSERT INTO programs (title, slug, summary, description, status, visibility, featured, cover_file_id, created_by, updated_by, published_by, published_at, created_at, updated_at, deleted_at)
VALUES ('Dasar-Dasar Pengadaan Barang dan Jasa', 'dasar-pengadaan-barang-jasa', 'Program penguatan kompetensi dasar PBJ.', 'Program untuk memahami proses pengadaan, perencanaan, pemilihan penyedia, dan integritas.', 'published', 'internal', 1, @program_cover_id, @learning_admin_id, @learning_admin_id, @learning_admin_id, @seed_now, @seed_now, @seed_now, NULL)
ON DUPLICATE KEY UPDATE title = VALUES(title), status = 'published', cover_file_id = VALUES(cover_file_id), updated_at = VALUES(updated_at);
SET @program_id := (SELECT id FROM programs WHERE slug = 'dasar-pengadaan-barang-jasa' LIMIT 1);

INSERT INTO courses (program_id, title, slug, summary, description, level, duration_minutes, status, visibility, cover_file_id, created_by, updated_by, published_by, published_at, created_at, updated_at, deleted_at)
VALUES (@program_id, 'Pengadaan Barang dan Jasa Tingkat Dasar', 'pengadaan-barang-jasa-tingkat-dasar', 'Pengantar konsep dan tata kelola PBJ.', 'Course dasar yang mencakup siklus pengadaan dan prinsip integritas.', 'dasar', 480, 'published', 'internal', @course_cover_id, @learning_admin_id, @learning_admin_id, @learning_admin_id, @seed_now, @seed_now, @seed_now, NULL)
ON DUPLICATE KEY UPDATE program_id = VALUES(program_id), title = VALUES(title), status = 'published', updated_at = VALUES(updated_at);
SET @course_id := (SELECT id FROM courses WHERE slug = 'pengadaan-barang-jasa-tingkat-dasar' LIMIT 1);

INSERT INTO learning_classes (course_id, code, name, status, registration_open_at, registration_close_at, start_at, end_at, capacity, certificate_enabled, passing_grade, quiz_weight, assignment_weight, post_test_weight, created_by, updated_by, created_at, updated_at, deleted_at)
VALUES (@course_id, 'PBJ-DASAR-2026-A', 'Kelas PBJ Dasar Angkatan A Tahun 2026', 'published', DATE_SUB(@seed_now, INTERVAL 45 DAY), DATE_SUB(@seed_now, INTERVAL 31 DAY), DATE_SUB(@seed_now, INTERVAL 30 DAY), DATE_SUB(@seed_now, INTERVAL 1 DAY), 30, 1, 70.00, 30.00, 10.00, 60.00, @learning_admin_id, @learning_admin_id, @seed_now, @seed_now, NULL)
ON DUPLICATE KEY UPDATE name = VALUES(name), status = VALUES(status), updated_at = VALUES(updated_at);
SET @class_id := (SELECT id FROM learning_classes WHERE course_id = @course_id AND code = 'PBJ-DASAR-2026-A' LIMIT 1);

INSERT IGNORE INTO class_trainers (class_id, user_id, assigned_by, assigned_at, status) VALUES (@class_id, @trainer_id, @learning_admin_id, @seed_now, 'active');
INSERT IGNORE INTO class_evaluators (class_id, user_id, assigned_by, assigned_at, status) VALUES (@class_id, @evaluator_id, @learning_admin_id, @seed_now, 'active');

INSERT INTO learning_modules (class_id, title, description, sort_order, is_required, status, created_at, updated_at) VALUES
(@class_id, 'Pengantar Pengadaan', 'Konsep dan prinsip dasar PBJ.', 1, 1, 'published', @seed_now, @seed_now),
(@class_id, 'Perencanaan dan Integritas', 'Tahapan perencanaan dan mitigasi risiko.', 2, 1, 'published', @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description), status = VALUES(status), updated_at = VALUES(updated_at);
SET @module1_id := (SELECT id FROM learning_modules WHERE class_id = @class_id AND sort_order = 1 LIMIT 1);
SET @module2_id := (SELECT id FROM learning_modules WHERE class_id = @class_id AND sort_order = 2 LIMIT 1);

INSERT INTO lessons (module_id, title, slug, content_type, content, file_id, external_url, sort_order, is_required, status, visibility, created_by, updated_by, created_at, updated_at) VALUES
(@module1_id, 'Prinsip Dasar Pengadaan', 'prinsip-dasar-pengadaan', 'text', '<p>Pelajari efisien, efektif, transparan, terbuka, bersaing, adil, dan akuntabel.</p>', NULL, NULL, 1, 1, 'published', 'internal', @trainer_id, @trainer_id, @seed_now, @seed_now),
(@module1_id, 'Siklus Pengadaan', 'siklus-pengadaan', 'video', '<p>Video pengantar siklus pengadaan.</p>', NULL, 'https://example.test/video/siklus-pengadaan', 2, 1, 'published', 'internal', @trainer_id, @trainer_id, @seed_now, @seed_now),
(@module2_id, 'Perencanaan Pengadaan', 'perencanaan-pengadaan', 'text', '<p>Perencanaan pengadaan berbasis kebutuhan dan risiko.</p>', NULL, NULL, 1, 1, 'published', 'internal', @trainer_id, @trainer_id, @seed_now, @seed_now),
(@module2_id, 'Integritas dalam PBJ', 'integritas-pbj', 'link', '<p>Referensi kebijakan integritas.</p>', NULL, 'https://example.test/referensi/integritas-pbj', 2, 1, 'published', 'internal', @trainer_id, @trainer_id, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), status = VALUES(status), updated_at = VALUES(updated_at);
SET @lesson1_id := (SELECT id FROM lessons WHERE module_id = @module1_id AND sort_order = 1 LIMIT 1);
SET @lesson2_id := (SELECT id FROM lessons WHERE module_id = @module1_id AND sort_order = 2 LIMIT 1);
SET @lesson3_id := (SELECT id FROM lessons WHERE module_id = @module2_id AND sort_order = 1 LIMIT 1);
SET @lesson4_id := (SELECT id FROM lessons WHERE module_id = @module2_id AND sort_order = 2 LIMIT 1);

INSERT INTO enrollments (class_id, user_id, status, enrolled_at, activated_at, completed_at, final_score, passed_at, certificate_eligible_at, withdrawn_at, cancelled_at, created_at, updated_at) VALUES
(@class_id, @budi_id, 'completed', DATE_SUB(@seed_now, INTERVAL 44 DAY), DATE_SUB(@seed_now, INTERVAL 43 DAY), DATE_SUB(@seed_now, INTERVAL 1 DAY), 86.50, DATE_SUB(@seed_now, INTERVAL 1 DAY), DATE_SUB(@seed_now, INTERVAL 1 DAY), NULL, NULL, @seed_now, @seed_now),
(@class_id, @ayu_id, 'active', DATE_SUB(@seed_now, INTERVAL 44 DAY), DATE_SUB(@seed_now, INTERVAL 43 DAY), NULL, NULL, NULL, NULL, NULL, NULL, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE status = VALUES(status), final_score = VALUES(final_score), updated_at = VALUES(updated_at);
SET @budi_enrollment_id := (SELECT id FROM enrollments WHERE class_id = @class_id AND user_id = @budi_id LIMIT 1);
SET @ayu_enrollment_id := (SELECT id FROM enrollments WHERE class_id = @class_id AND user_id = @ayu_id LIMIT 1);

INSERT INTO pre_tests (class_id, is_enabled, attempt_limit, status, created_at, updated_at)
VALUES (@class_id, 1, 1, 'published', @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE is_enabled = VALUES(is_enabled), attempt_limit = VALUES(attempt_limit), status = VALUES(status), updated_at = VALUES(updated_at);

INSERT INTO quizzes (class_id, lesson_id, type, title, instructions, attempt_limit, is_required, status, created_at, updated_at) VALUES
(@class_id, NULL, 'pre_test', 'Pre-Test PBJ Dasar', 'Jawab seluruh pertanyaan sebelum memulai kelas.', 1, 1, 'published', @seed_now, @seed_now),
(@class_id, @lesson2_id, 'lesson_quiz', 'Kuis Siklus Pengadaan', 'Pilih jawaban paling tepat.', 2, 1, 'published', @seed_now, @seed_now),
(@class_id, NULL, 'post_test', 'Post-Test PBJ Dasar', 'Evaluasi akhir kelas.', 1, 1, 'published', @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE title = VALUES(title), instructions = VALUES(instructions), status = VALUES(status), updated_at = VALUES(updated_at);
SET @pre_quiz_id := (SELECT id FROM quizzes WHERE class_id = @class_id AND type = 'pre_test' LIMIT 1);
SET @lesson_quiz_id := (SELECT id FROM quizzes WHERE class_id = @class_id AND type = 'lesson_quiz' AND lesson_id = @lesson2_id LIMIT 1);
SET @post_quiz_id := (SELECT id FROM quizzes WHERE class_id = @class_id AND type = 'post_test' LIMIT 1);

INSERT INTO quiz_questions (quiz_id, type, prompt, points, sort_order, explanation, created_at, updated_at) VALUES
(@pre_quiz_id, 'multiple_choice', 'Prinsip pengadaan yang menekankan keterbukaan informasi adalah?', 1.00, 1, 'Transparan berarti informasi proses tersedia secara proporsional.', @seed_now, @seed_now),
(@lesson_quiz_id, 'multiple_choice', 'Tahap awal siklus pengadaan adalah?', 1.00, 1, 'Pengadaan dimulai dari identifikasi kebutuhan dan perencanaan.', @seed_now, @seed_now),
(@post_quiz_id, 'multiple_choice', 'Prinsip yang menuntut pertanggungjawaban proses dan hasil adalah?', 1.00, 1, 'Akuntabel.', @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE prompt = VALUES(prompt), explanation = VALUES(explanation), updated_at = VALUES(updated_at);
SET @pre_question_id := (SELECT id FROM quiz_questions WHERE quiz_id = @pre_quiz_id AND sort_order = 1 LIMIT 1);
SET @lesson_question_id := (SELECT id FROM quiz_questions WHERE quiz_id = @lesson_quiz_id AND sort_order = 1 LIMIT 1);
SET @post_question_id := (SELECT id FROM quiz_questions WHERE quiz_id = @post_quiz_id AND sort_order = 1 LIMIT 1);

INSERT INTO quiz_options (question_id, option_text, is_correct, sort_order, created_at, updated_at) VALUES
(@pre_question_id, 'Transparan', 1, 1, @seed_now, @seed_now), (@pre_question_id, 'Tertutup', 0, 2, @seed_now, @seed_now), (@pre_question_id, 'Rahasia', 0, 3, @seed_now, @seed_now),
(@lesson_question_id, 'Identifikasi kebutuhan dan perencanaan', 1, 1, @seed_now, @seed_now), (@lesson_question_id, 'Pembayaran akhir', 0, 2, @seed_now, @seed_now), (@lesson_question_id, 'Audit penutupan', 0, 3, @seed_now, @seed_now),
(@post_question_id, 'Akuntabel', 1, 1, @seed_now, @seed_now), (@post_question_id, 'Eksklusif', 0, 2, @seed_now, @seed_now), (@post_question_id, 'Tertutup', 0, 3, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE option_text = VALUES(option_text), is_correct = VALUES(is_correct), updated_at = VALUES(updated_at);
SET @pre_correct_option_id := (SELECT id FROM quiz_options WHERE question_id = @pre_question_id AND is_correct = 1 LIMIT 1);
SET @lesson_correct_option_id := (SELECT id FROM quiz_options WHERE question_id = @lesson_question_id AND is_correct = 1 LIMIT 1);
SET @post_correct_option_id := (SELECT id FROM quiz_options WHERE question_id = @post_question_id AND is_correct = 1 LIMIT 1);

INSERT INTO quiz_attempts (quiz_id, enrollment_id, attempt_number, status, started_at, submitted_at, graded_at, score, invalidated_by, invalidated_reason, created_at, updated_at) VALUES
(@pre_quiz_id, @budi_enrollment_id, 1, 'graded', DATE_SUB(@seed_now, INTERVAL 43 DAY), DATE_SUB(@seed_now, INTERVAL 43 DAY), DATE_SUB(@seed_now, INTERVAL 43 DAY), 100.00, NULL, NULL, @seed_now, @seed_now),
(@lesson_quiz_id, @budi_enrollment_id, 1, 'graded', DATE_SUB(@seed_now, INTERVAL 20 DAY), DATE_SUB(@seed_now, INTERVAL 20 DAY), DATE_SUB(@seed_now, INTERVAL 20 DAY), 100.00, NULL, NULL, @seed_now, @seed_now),
(@post_quiz_id, @budi_enrollment_id, 1, 'graded', DATE_SUB(@seed_now, INTERVAL 2 DAY), DATE_SUB(@seed_now, INTERVAL 2 DAY), DATE_SUB(@seed_now, INTERVAL 2 DAY), 90.00, NULL, NULL, @seed_now, @seed_now),
(@lesson_quiz_id, @ayu_enrollment_id, 1, 'started', DATE_SUB(@seed_now, INTERVAL 1 DAY), NULL, NULL, NULL, NULL, NULL, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE status = VALUES(status), score = VALUES(score), updated_at = VALUES(updated_at);
SET @budi_pre_attempt_id := (SELECT id FROM quiz_attempts WHERE quiz_id = @pre_quiz_id AND enrollment_id = @budi_enrollment_id AND attempt_number = 1 LIMIT 1);
SET @budi_lesson_attempt_id := (SELECT id FROM quiz_attempts WHERE quiz_id = @lesson_quiz_id AND enrollment_id = @budi_enrollment_id AND attempt_number = 1 LIMIT 1);
SET @budi_post_attempt_id := (SELECT id FROM quiz_attempts WHERE quiz_id = @post_quiz_id AND enrollment_id = @budi_enrollment_id AND attempt_number = 1 LIMIT 1);

INSERT INTO quiz_answers (attempt_id, question_id, answer_payload, is_correct, score_awarded, created_at, updated_at) VALUES
(@budi_pre_attempt_id, @pre_question_id, JSON_OBJECT('selected_option_id', @pre_correct_option_id), 1, 1.00, @seed_now, @seed_now),
(@budi_lesson_attempt_id, @lesson_question_id, JSON_OBJECT('selected_option_id', @lesson_correct_option_id), 1, 1.00, @seed_now, @seed_now),
(@budi_post_attempt_id, @post_question_id, JSON_OBJECT('selected_option_id', @post_correct_option_id), 1, 1.00, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE answer_payload = VALUES(answer_payload), is_correct = VALUES(is_correct), score_awarded = VALUES(score_awarded), updated_at = VALUES(updated_at);

INSERT INTO assignments (class_id, lesson_id, title, instructions, due_at, is_required, max_attempts, allowed_mimes, max_file_size_bytes, status, created_by, updated_by, created_at, updated_at)
VALUES (@class_id, @lesson3_id, 'Analisis Kebutuhan Pengadaan', 'Susun analisis singkat kebutuhan pengadaan pada unit kerja Anda.', DATE_SUB(@seed_now, INTERVAL 10 DAY), 1, 2, JSON_ARRAY('application/pdf','application/vnd.openxmlformats-officedocument.wordprocessingml.document'), 5242880, 'published', @trainer_id, @trainer_id, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE title = VALUES(title), instructions = VALUES(instructions), status = VALUES(status), updated_at = VALUES(updated_at);
SET @assignment_id := (SELECT id FROM assignments WHERE class_id = @class_id AND lesson_id = @lesson3_id LIMIT 1);

INSERT INTO assignment_submissions (assignment_id, enrollment_id, attempt_number, status, content, submitted_at, graded_at, graded_by, score, feedback, created_at, updated_at)
VALUES (@assignment_id, @budi_enrollment_id, 1, 'graded', 'Analisis kebutuhan pengadaan perangkat kerja untuk unit layanan.', DATE_SUB(@seed_now, INTERVAL 12 DAY), DATE_SUB(@seed_now, INTERVAL 9 DAY), @evaluator_id, 85.00, 'Analisis sudah baik; tambahkan estimasi risiko dan mitigasi.', @seed_now, @seed_now),
(@assignment_id, @ayu_enrollment_id, 1, 'revision_required', 'Draft analisis kebutuhan pengadaan.', DATE_SUB(@seed_now, INTERVAL 2 DAY), NULL, @evaluator_id, NULL, 'Mohon lengkapi justifikasi kebutuhan dan indikator keberhasilan.', @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE status = VALUES(status), score = VALUES(score), feedback = VALUES(feedback), updated_at = VALUES(updated_at);
SET @budi_submission_id := (SELECT id FROM assignment_submissions WHERE assignment_id = @assignment_id AND enrollment_id = @budi_enrollment_id AND attempt_number = 1 LIMIT 1);
INSERT IGNORE INTO assignment_submission_files (submission_id, file_id, created_at) VALUES (@budi_submission_id, @submission_file_id, @seed_now);

INSERT INTO learning_progress (enrollment_id, lesson_id, status, started_at, completed_at, progress_percent, created_at, updated_at) VALUES
(@budi_enrollment_id, @lesson1_id, 'completed', DATE_SUB(@seed_now, INTERVAL 40 DAY), DATE_SUB(@seed_now, INTERVAL 40 DAY), 100.00, @seed_now, @seed_now),
(@budi_enrollment_id, @lesson2_id, 'completed', DATE_SUB(@seed_now, INTERVAL 30 DAY), DATE_SUB(@seed_now, INTERVAL 30 DAY), 100.00, @seed_now, @seed_now),
(@budi_enrollment_id, @lesson3_id, 'completed', DATE_SUB(@seed_now, INTERVAL 18 DAY), DATE_SUB(@seed_now, INTERVAL 18 DAY), 100.00, @seed_now, @seed_now),
(@budi_enrollment_id, @lesson4_id, 'completed', DATE_SUB(@seed_now, INTERVAL 15 DAY), DATE_SUB(@seed_now, INTERVAL 15 DAY), 100.00, @seed_now, @seed_now),
(@ayu_enrollment_id, @lesson1_id, 'completed', DATE_SUB(@seed_now, INTERVAL 15 DAY), DATE_SUB(@seed_now, INTERVAL 14 DAY), 100.00, @seed_now, @seed_now),
(@ayu_enrollment_id, @lesson2_id, 'in_progress', DATE_SUB(@seed_now, INTERVAL 1 DAY), NULL, 55.00, @seed_now, @seed_now),
(@ayu_enrollment_id, @lesson3_id, 'not_started', NULL, NULL, 0.00, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE status = VALUES(status), progress_percent = VALUES(progress_percent), completed_at = VALUES(completed_at), updated_at = VALUES(updated_at);

INSERT INTO feedbacks (enrollment_id, rating, comment, status, submitted_at, created_at, updated_at) VALUES
(@budi_enrollment_id, 5, 'Materi jelas, trainer komunikatif, dan tugas relevan dengan pekerjaan.', 'submitted', DATE_SUB(@seed_now, INTERVAL 1 DAY), @seed_now, @seed_now),
(@ayu_enrollment_id, NULL, NULL, 'not_started', NULL, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment), status = VALUES(status), updated_at = VALUES(updated_at);

-- ----------------------------------------------------------------------------
-- 6) KNOWLEDGE
-- ----------------------------------------------------------------------------
INSERT INTO knowledge_categories (parent_id, name, slug, description, sort_order, is_active, created_at, updated_at) VALUES
(NULL, 'Pengadaan Barang dan Jasa', 'pengadaan-barang-dan-jasa', 'Konten seputar pengadaan.', 1, 1, @seed_now, @seed_now),
(NULL, 'Integritas dan Tata Kelola', 'integritas-dan-tata-kelola', 'Konten integritas dan tata kelola.', 2, 1, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), updated_at = VALUES(updated_at);
SET @knowledge_cat_pbj_id := (SELECT id FROM knowledge_categories WHERE slug = 'pengadaan-barang-dan-jasa' LIMIT 1);
SET @knowledge_cat_integrity_id := (SELECT id FROM knowledge_categories WHERE slug = 'integritas-dan-tata-kelola' LIMIT 1);

INSERT INTO knowledge_tags (name, slug, created_at, updated_at) VALUES
('PBJ', 'pbj', @seed_now, @seed_now), ('Integritas', 'integritas', @seed_now, @seed_now), ('Perencanaan', 'perencanaan', @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE name = VALUES(name), updated_at = VALUES(updated_at);
SET @tag_pbj_id := (SELECT id FROM knowledge_tags WHERE slug = 'pbj' LIMIT 1);
SET @tag_integrity_id := (SELECT id FROM knowledge_tags WHERE slug = 'integritas' LIMIT 1);

INSERT INTO knowledge_articles (type, title, slug, summary, content, workflow_status, visibility, featured, carousel_enabled, carousel_order, cover_file_id, author_id, reviewed_by, published_by, published_at, archived_at, created_at, updated_at, deleted_at)
VALUES ('article', 'Integritas dalam Proses Pengadaan', 'integritas-dalam-proses-pengadaan', 'Panduan ringkas menjaga integritas pada setiap tahapan pengadaan.', '<h2>Integritas</h2><p>Integritas memastikan proses pengadaan berlangsung adil, transparan, dan akuntabel.</p>', 'published', 'public', 1, 1, 1, @article_cover_id, @editor_id, @admin_id, @admin_id, @seed_now, NULL, @seed_now, @seed_now, NULL)
ON DUPLICATE KEY UPDATE title = VALUES(title), workflow_status = 'published', visibility = 'public', updated_at = VALUES(updated_at);
SET @article_id := (SELECT id FROM knowledge_articles WHERE slug = 'integritas-dalam-proses-pengadaan' LIMIT 1);
INSERT IGNORE INTO knowledge_article_categories (article_id, category_id, created_at) VALUES (@article_id, @knowledge_cat_pbj_id, @seed_now), (@article_id, @knowledge_cat_integrity_id, @seed_now);
INSERT IGNORE INTO knowledge_article_tags (article_id, tag_id, created_at) VALUES (@article_id, @tag_pbj_id, @seed_now), (@article_id, @tag_integrity_id, @seed_now);

INSERT INTO knowledge_media (article_id, type, title, caption, alt_text, file_id, external_url, thumbnail_file_id, workflow_status, visibility, sort_order, featured, carousel_enabled, created_by, published_by, published_at, created_at, updated_at)
VALUES (@article_id, 'image', 'Infografik Integritas', 'Ringkasan prinsip integritas.', 'Infografik integritas pengadaan.', @article_cover_id, NULL, NULL, 'published', 'public', 1, 1, 1, @editor_id, @admin_id, @seed_now, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE title = VALUES(title), workflow_status = 'published', updated_at = VALUES(updated_at);

-- ----------------------------------------------------------------------------
-- 7) REGULATIONS
-- ----------------------------------------------------------------------------
INSERT INTO regulation_categories (name, slug, description, is_active, created_at, updated_at)
VALUES ('Pengadaan Barang dan Jasa', 'pengadaan-barang-dan-jasa', 'Regulasi terkait PBJ.', 1, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE name = VALUES(name), updated_at = VALUES(updated_at);
SET @reg_category_id := (SELECT id FROM regulation_categories WHERE slug = 'pengadaan-barang-dan-jasa' LIMIT 1);

INSERT INTO regulations (category_id, title, slug, document_number, document_year, issuer_name, official_date, effective_date, summary, official_source_url, workflow_status, legal_status, visibility, current_version_id, created_by, reviewed_by, published_by, published_at, archived_at, created_at, updated_at, deleted_at)
VALUES (@reg_category_id, 'Pedoman Pengadaan Barang dan Jasa Internal', 'pedoman-pengadaan-barang-jasa-internal', 'SE-001/PBJ', 2026, 'Pemerintah Provinsi Lampung', DATE_SUB(@seed_now, INTERVAL 90 DAY), DATE_SUB(@seed_now, INTERVAL 85 DAY), 'Pedoman internal pengadaan barang dan jasa.', 'https://example.test/regulasi/se-001-pbj-2026', 'published', 'active', 'internal', NULL, @editor_id, @admin_id, @admin_id, @seed_now, NULL, @seed_now, @seed_now, NULL)
ON DUPLICATE KEY UPDATE title = VALUES(title), workflow_status = 'published', legal_status = 'active', updated_at = VALUES(updated_at);
SET @regulation_id := (SELECT id FROM regulations WHERE slug = 'pedoman-pengadaan-barang-jasa-internal' LIMIT 1);

INSERT INTO regulation_versions (regulation_id, version_number, file_id, changelog, is_current, uploaded_by, created_at)
VALUES (@regulation_id, 1, @regulation_pdf_id, 'Versi awal pedoman.', 1, @editor_id, @seed_now)
ON DUPLICATE KEY UPDATE file_id = VALUES(file_id), changelog = VALUES(changelog), is_current = 1;
SET @regulation_version_id := (SELECT id FROM regulation_versions WHERE regulation_id = @regulation_id AND version_number = 1 LIMIT 1);
UPDATE regulations SET current_version_id = @regulation_version_id, updated_at = @seed_now WHERE id = @regulation_id;

INSERT INTO regulation_downloads (regulation_id, version_id, file_id, user_id, access_type, created_at)
VALUES (@regulation_id, @regulation_version_id, @regulation_pdf_id, @budi_id, 'authenticated', @seed_now);

-- ----------------------------------------------------------------------------
-- 8) NEWS
-- ----------------------------------------------------------------------------
INSERT INTO news_categories (name, slug, description, is_active, created_at, updated_at)
VALUES ('Pembelajaran', 'pembelajaran', 'Berita pembelajaran dan pengembangan kompetensi.', 1, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE name = VALUES(name), updated_at = VALUES(updated_at);
SET @news_category_id := (SELECT id FROM news_categories WHERE slug = 'pembelajaran' LIMIT 1);

INSERT INTO news (category_id, title, slug, summary, content, cover_file_id, workflow_status, visibility, published_at, scheduled_at, view_count, author_id, reviewed_by, published_by, created_at, updated_at, deleted_at)
VALUES (@news_category_id, 'Kelas PBJ Dasar Angkatan A Telah Selesai', 'kelas-pbj-dasar-angkatan-a-telah-selesai', 'Pelaksanaan kelas PBJ dasar Angkatan A telah selesai.', '<p>Peserta menyelesaikan pembelajaran dan evaluasi akhir kelas.</p>', @news_cover_id, 'published', 'public', @seed_now, NULL, 128, @editor_id, @admin_id, @admin_id, @seed_now, @seed_now, NULL)
ON DUPLICATE KEY UPDATE title = VALUES(title), view_count = VALUES(view_count), updated_at = VALUES(updated_at);

-- ----------------------------------------------------------------------------
-- 9) EVENTS
-- ----------------------------------------------------------------------------
INSERT INTO events (title, slug, type, mode, status, visibility, summary, description, cover_file_id, start_at, end_at, location, meeting_url, registration_open_at, registration_close_at, capacity, waitlist_enabled, certificate_enabled, created_by, updated_by, cancelled_by, cancelled_at, cancellation_reason, created_at, updated_at, deleted_at)
VALUES ('Webinar Strategi Pengadaan Berintegritas', 'webinar-strategi-pengadaan-berintegritas', 'webinar', 'online', 'finished', 'public', 'Webinar penguatan integritas pengadaan.', '<p>Webinar membahas praktik pengadaan berintegritas.</p>', @event_cover_id, DATE_SUB(@seed_now, INTERVAL 20 DAY), DATE_SUB(@seed_now, INTERVAL 20 DAY) + INTERVAL 3 HOUR, NULL, 'https://example.test/meeting/webinar-integritas', DATE_SUB(@seed_now, INTERVAL 45 DAY), DATE_SUB(@seed_now, INTERVAL 21 DAY), 100, 1, 1, @event_admin_id, @event_admin_id, NULL, NULL, NULL, @seed_now, @seed_now, NULL)
ON DUPLICATE KEY UPDATE title = VALUES(title), status = 'finished', updated_at = VALUES(updated_at);
SET @event_id := (SELECT id FROM events WHERE slug = 'webinar-strategi-pengadaan-berintegritas' LIMIT 1);

INSERT INTO event_registrations (event_id, user_id, status, registered_at, cancelled_at, promoted_at, status_reason, created_at, updated_at) VALUES
(@event_id, @budi_id, 'registered', DATE_SUB(@seed_now, INTERVAL 40 DAY), NULL, NULL, NULL, @seed_now, @seed_now),
(@event_id, @ayu_id, 'registered', DATE_SUB(@seed_now, INTERVAL 39 DAY), NULL, NULL, NULL, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE status = VALUES(status), updated_at = VALUES(updated_at);
SET @budi_event_registration_id := (SELECT id FROM event_registrations WHERE event_id = @event_id AND user_id = @budi_id LIMIT 1);
SET @ayu_event_registration_id := (SELECT id FROM event_registrations WHERE event_id = @event_id AND user_id = @ayu_id LIMIT 1);

INSERT INTO attendances (event_registration_id, status, recorded_by, recorded_at, notes, created_at, updated_at) VALUES
(@budi_event_registration_id, 'present', @event_admin_id, DATE_SUB(@seed_now, INTERVAL 20 DAY), 'Mengikuti webinar sampai selesai.', @seed_now, @seed_now),
(@ayu_event_registration_id, 'excused', @event_admin_id, DATE_SUB(@seed_now, INTERVAL 20 DAY), 'Berhalangan karena tugas kedinasan.', @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE status = VALUES(status), recorded_by = VALUES(recorded_by), updated_at = VALUES(updated_at);

-- ----------------------------------------------------------------------------
-- 10) CERTIFICATES
-- ----------------------------------------------------------------------------
INSERT INTO certificate_templates (name, source_type, template_payload, background_file_id, is_active, created_by, updated_by, created_at, updated_at) VALUES
('Template Sertifikat Pembelajaran', 'learning', JSON_OBJECT('title','Sertifikat Kompetensi','layout','landscape'), @learning_bg_id, 1, @learning_admin_id, @learning_admin_id, @seed_now, @seed_now),
('Template Sertifikat Event', 'event', JSON_OBJECT('title','Sertifikat Partisipasi','layout','landscape'), @event_bg_id, 1, @event_admin_id, @event_admin_id, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE template_payload = VALUES(template_payload), is_active = 1, updated_at = VALUES(updated_at);
SET @learning_template_id := (SELECT id FROM certificate_templates WHERE name = 'Template Sertifikat Pembelajaran' LIMIT 1);
SET @event_template_id := (SELECT id FROM certificate_templates WHERE name = 'Template Sertifikat Event' LIMIT 1);

INSERT INTO certificates (source_type, source_id, user_id, template_id, certificate_number, verification_token_hash, status, issued_at, expires_at, revoked_at, revoked_by, revocation_reason, pdf_file_id, replaced_by_certificate_id, issued_by, created_at, updated_at)
VALUES
('learning_enrollment', @budi_enrollment_id, @budi_id, @learning_template_id, 'LPG-LRN-2026-000001', SHA2('verify-learning-budi-2026', 256), 'issued', DATE_SUB(@seed_now, INTERVAL 1 DAY), NULL, NULL, NULL, NULL, @certificate_pdf_id, NULL, @learning_admin_id, @seed_now, @seed_now),
('event_registration', @budi_event_registration_id, @budi_id, @event_template_id, 'LPG-EVT-2026-000001', SHA2('verify-event-budi-2026', 256), 'issued', DATE_SUB(@seed_now, INTERVAL 19 DAY), NULL, NULL, NULL, NULL, @certificate_pdf_id, NULL, @event_admin_id, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE status = 'issued', issued_at = VALUES(issued_at), pdf_file_id = VALUES(pdf_file_id), updated_at = VALUES(updated_at);

-- ----------------------------------------------------------------------------
-- 11) COMPETENCY / JF-PPBJ
-- ----------------------------------------------------------------------------
INSERT INTO competencies (`key`, name, description, is_active, created_at, updated_at) VALUES
('pbj_perencanaan', 'Perencanaan Pengadaan', 'Kompetensi perencanaan pengadaan.', 1, @seed_now, @seed_now),
('pbj_pemilihan', 'Pemilihan Penyedia', 'Kompetensi pemilihan penyedia.', 1, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), updated_at = VALUES(updated_at);
SET @competency_id := (SELECT id FROM competencies WHERE `key` = 'pbj_perencanaan' LIMIT 1);

INSERT INTO jf_ppbj_levels (`key`, name, sort_order, is_active, created_at, updated_at) VALUES
('pemula', 'JF PPBJ Pemula', 1, 1, @seed_now, @seed_now),
('terampil', 'JF PPBJ Terampil', 2, 1, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE name = VALUES(name), sort_order = VALUES(sort_order), updated_at = VALUES(updated_at);
SET @jf_level_id := (SELECT id FROM jf_ppbj_levels WHERE `key` = 'pemula' LIMIT 1);

INSERT INTO competency_certificate_types (`key`, name, description, is_active, created_at, updated_at) VALUES
('pbj_dasar', 'Sertifikat Kompetensi PBJ Dasar', 'Bukti kompetensi dasar pengadaan.', 1, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), updated_at = VALUES(updated_at);
SET @competency_certificate_type_id := (SELECT id FROM competency_certificate_types WHERE `key` = 'pbj_dasar' LIMIT 1);
SET @bpsdm_unit_id := (SELECT id FROM organization_units WHERE code = 'LPG-BPSDM' LIMIT 1);

INSERT INTO jf_ppbj_records (user_id, organization_unit_id, employee_identifier, name_snapshot, level_id, status, effective_at, ended_at, change_type, evidence_file_id, created_by, updated_by, created_at, updated_at)
VALUES (@budi_id, @bpsdm_unit_id, '198809152014011001', 'Budi Santoso', @jf_level_id, 'active', DATE_SUB(@seed_now, INTERVAL 365 DAY), NULL, 'addition', @competency_evidence_id, @competency_admin_id, @competency_admin_id, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE level_id = VALUES(level_id), status = 'active', evidence_file_id = VALUES(evidence_file_id), updated_at = VALUES(updated_at);
SET @jf_record_id := (SELECT id FROM jf_ppbj_records WHERE user_id = @budi_id AND employee_identifier = '198809152014011001' LIMIT 1);

INSERT INTO competency_certificates (jf_ppbj_record_id, certificate_type_id, certificate_number, issued_at, expires_at, status, evidence_file_id, verified_by, verified_at, created_at, updated_at)
VALUES (@jf_record_id, @competency_certificate_type_id, 'KOMP-PBJ-2026-000001', DATE_SUB(@seed_now, INTERVAL 365 DAY), DATE_ADD(@seed_now, INTERVAL 365 DAY), 'verified', @competency_evidence_id, @competency_admin_id, @seed_now, @seed_now, @seed_now)
ON DUPLICATE KEY UPDATE status = 'verified', verified_by = VALUES(verified_by), verified_at = VALUES(verified_at), updated_at = VALUES(updated_at);

INSERT INTO competency_gap_snapshots (organization_unit_id, competency_id, period_date, required_count, available_count, gap_count, metadata, generated_by, created_at)
VALUES (@bpsdm_unit_id, @competency_id, CURDATE(), 25, 18, 7, JSON_OBJECT('scope','dummy_seed','note','Gap kompetensi perencanaan'), @competency_admin_id, @seed_now)
ON DUPLICATE KEY UPDATE required_count = VALUES(required_count), available_count = VALUES(available_count), gap_count = VALUES(gap_count), metadata = VALUES(metadata), generated_by = VALUES(generated_by), created_at = VALUES(created_at);

COMMIT;
-- ============================================================================
-- END OF SQL SEED
-- ============================================================================