-- BegawiPBJ Dummy Data v3
-- MySQL 8.4+ | InnoDB | utf8mb4
--
-- Prasyarat:
--   1. Jalankan begawipbj-schema-fresh-v3.sql pada database kosong.
--   2. Import master wilayah resmi secara manual.
--   3. File ini TIDAK menyentuh tabel wilayah.
--
-- Akun development/demo:
--   superadmin@begawipbj.com / superadmin
--   admin@begawipbj.com      / admin
--   operator@begawipbj.com   / operator
--   trainer@begawipbj.com    / trainer
--   evaluator@begawipbj.com  / evaluator
--   peserta1@begawipbj.com   / peserta1
--   peserta2@begawipbj.com   / peserta2
--   peserta3@begawipbj.com   / peserta3
--   public@begawipbj.com     / public
--
-- Password dibuat oleh PHP password_hash() dengan bcrypt cost 10.
-- Hanya untuk development/QA. Jangan gunakan credential ini di production.
--
-- OPD/unit organisasi di bawah root menggunakan daftar resmi yang diberikan pengguna.
-- File ini bersifat fresh seeder: jalankan sekali pada database fresh/schema kosong.

USE `db_begawipbj`;
SET NAMES utf8mb4;
SET time_zone = '+00:00';

START TRANSACTION;

-- =============================================================================
-- 00. GUARD: MASTER WILAYAH RESMI HARUS SUDAH DIIMPORT
-- =============================================================================

DROP PROCEDURE IF EXISTS assert_begawipbj_wilayah_ready;
DELIMITER $$
CREATE PROCEDURE assert_begawipbj_wilayah_ready()
BEGIN
    IF NOT EXISTS (SELECT 1 FROM wilayah WHERE kode = '18') THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Master wilayah belum diimport. Import database/reference/wilayah.sql terlebih dahulu.';
    END IF;
END$$
DELIMITER ;
CALL assert_begawipbj_wilayah_ready();
DROP PROCEDURE assert_begawipbj_wilayah_ready;

-- =============================================================================
-- 01. IDENTITY, RBAC, ORGANISASI, DAN PROFIL
-- =============================================================================

INSERT INTO roles (code, name, description, is_system) VALUES
('super_admin', 'Super Admin', 'Akses konfigurasi dan seluruh modul sistem.', 1),
('admin', 'Admin', 'Akses administrasi dan pengelolaan modul.', 1),
('operator', 'Operator', 'Dukungan administrasi, verifikasi, enrollment, dan absensi.', 1),
('trainer', 'Trainer', 'Pengelola kelas dan materi sesuai penugasan.', 1),
('evaluator', 'Evaluator', 'Penilai assignment manual sesuai penugasan.', 1),
('participant', 'Peserta', 'Peserta pembelajaran Arena Belajar.', 1),
('public_user', 'Public User', 'Akun publik terdaftar dengan akses dasar.', 1);

INSERT INTO permissions (module, action, code, name, description) VALUES
('users', 'view', 'users.view', 'Lihat pengguna', 'Melihat daftar dan detail pengguna.'),
('users', 'manage', 'users.manage', 'Kelola pengguna', 'Membuat dan memperbarui pengguna.'),
('users', 'assign_role', 'users.assign_role', 'Tetapkan role pengguna', 'Menetapkan role kepada pengguna.'),
('learning', 'manage', 'learning.manage', 'Kelola pembelajaran', 'Mengelola program, course, class, materi, dan assessment.'),
('learning', 'enroll', 'learning.enroll', 'Kelola enrollment', 'Memproses enrollment dan roster.'),
('learning', 'study', 'learning.study', 'Belajar', 'Mengakses pembelajaran peserta.'),
('learning', 'grade', 'learning.assignment.grade', 'Nilai assignment', 'Menilai assignment manual.'),
('learning', 'configure_assessment', 'learning.class.configure_assessment', 'Konfigurasi assessment class', 'Mengatur assessment configuration class.'),
('knowledge', 'manage', 'knowledge.manage', 'Kelola Bunga Rampai', 'Mengelola artikel, FAQ, studi kasus, dan template.'),
('regulation', 'manage', 'regulation.manage', 'Kelola regulasi', 'Mengelola regulasi dan versi dokumen.'),
('communication', 'manage', 'communication.manage', 'Kelola komunikasi', 'Mengelola berita dan event.'),
('communication', 'attendance', 'communication.event.attendance', 'Kelola absensi event', 'Mencatat absensi event.'),
('certificate', 'issue', 'certificate.issue', 'Terbitkan sertifikat', 'Menerbitkan sertifikat digital.'),
('certificate', 'revoke', 'certificate.revoke', 'Batalkan sertifikat', 'Membatalkan sertifikat digital.'),
('approval', 'review', 'approval.review', 'Review approval', 'Memproses workflow approval.'),
('audit', 'view', 'audit.view', 'Lihat audit', 'Melihat audit log.'),
('intelligence', 'view', 'intelligence.view', 'Lihat intelligence', 'Melihat Government Intelligence PBJ.'),
('intelligence', 'export', 'intelligence.export', 'Export intelligence', 'Mengekspor data intelligence.'),
('settings', 'manage', 'settings.manage', 'Kelola pengaturan', 'Mengelola pengaturan sistem.');

INSERT INTO role_permissions (role_id, permission_id, created_at)
SELECT r.id, p.id, UTC_TIMESTAMP()
FROM roles r
CROSS JOIN permissions p
WHERE r.code = 'super_admin';

INSERT INTO role_permissions (role_id, permission_id, created_at)
SELECT r.id, p.id, UTC_TIMESTAMP()
FROM roles r
JOIN permissions p ON p.code IN (
    'users.view', 'learning.manage', 'learning.enroll', 'learning.class.configure_assessment',
    'knowledge.manage', 'regulation.manage', 'communication.manage',
    'certificate.issue', 'approval.review', 'audit.view', 'intelligence.view'
)
WHERE r.code = 'admin';

INSERT INTO role_permissions (role_id, permission_id, created_at)
SELECT r.id, p.id, UTC_TIMESTAMP()
FROM roles r
JOIN permissions p ON p.code IN ('learning.enroll', 'communication.event.attendance', 'approval.review')
WHERE r.code = 'operator';

INSERT INTO role_permissions (role_id, permission_id, created_at)
SELECT r.id, p.id, UTC_TIMESTAMP()
FROM roles r
JOIN permissions p ON p.code IN ('learning.manage', 'learning.class.configure_assessment', 'learning.enroll')
WHERE r.code = 'trainer';

INSERT INTO role_permissions (role_id, permission_id, created_at)
SELECT r.id, p.id, UTC_TIMESTAMP()
FROM roles r
JOIN permissions p ON p.code IN ('learning.assignment.grade')
WHERE r.code = 'evaluator';

INSERT INTO role_permissions (role_id, permission_id, created_at)
SELECT r.id, p.id, UTC_TIMESTAMP()
FROM roles r
JOIN permissions p ON p.code IN ('learning.study')
WHERE r.code = 'participant';

-- Root organisasi dan seluruh daftar OPD/unit yang diberikan.
INSERT INTO organization_units (parent_id, code, name, unit_type, is_active, sort_order) VALUES
(NULL, 'PEMDA-PROV-LAMPUNG', 'Pemerintah Daerah Provinsi Lampung', 'province', 1, 1),
(1, 'BKD-LAMPUNG', 'Badan Kepegawaian Daerah Provinsi Lampung', 'agency', 1, 10),
(1, 'KESBANGPOL-LAMPUNG', 'Badan Kesatuan Bangsa & Politik Provinsi Lampung', 'agency', 1, 20),
(1, 'BPBD-LAMPUNG', 'Badan Penanggulangan Bencana Daerah Provinsi Lampung', 'agency', 1, 30),
(1, 'BAPENDA-LAMPUNG', 'Badan Pendapatan Daerah Provinsi Lampung', 'agency', 1, 40),
(1, 'BALITBANGDA-LAMPUNG', 'Badan Penelitian & Pengembangan Daerah Provinsi Lampung', 'agency', 1, 50),
(1, 'BPKAD-LAMPUNG', 'Badan Pengelolaan Keuangan dan Aset Daerah Prov. Lampung', 'agency', 1, 60),
(1, 'BPSDMD-LAMPUNG', 'Badan Pengembangan Sumber Daya Manusia Daerah Provinsi Lampung', 'agency', 1, 70),
(1, 'BADAN-PENGHUBUNG-JKT', 'Badan Penghubung Provinsi Lampung di Jakarta', 'agency', 1, 80),
(1, 'BAPPEDA-LAMPUNG', 'Badan Perencanaan Pembangunan Daerah Provinsi Lampung', 'agency', 1, 90),
(1, 'BIRO-ADPEM-LAMPUNG', 'Biro Administrasi Pembangunan Daerah Provinsi Lampung', 'bureau', 1, 100),
(1, 'BIRO-ADPIM-LAMPUNG', 'Biro Administrasi Pimpinan Provinsi Lampung', 'bureau', 1, 110),
(1, 'BIRO-HUKUM-LAMPUNG', 'Biro Hukum Setda Provinsi Lampung', 'bureau', 1, 120),
(1, 'BIRO-KESRA-LAMPUNG', 'Biro Kesejahteraan Rakyat Provinsi Lampung', 'bureau', 1, 130),
(1, 'BIRO-ORGANISASI-LAMPUNG', 'Biro Organisasi Setda Provinsi Lampung', 'bureau', 1, 140),
(1, 'BIRO-PEMOTDA-LAMPUNG', 'Biro Pemerintahan dan Otonomi Daerah Provinsi Lampung', 'bureau', 1, 150),
(1, 'BIRO-PBJ-LAMPUNG', 'Biro Pengadaan Barang dan Jasa', 'bureau', 1, 160),
(1, 'BIRO-PEREKONOMIAN-LAMPUNG', 'Biro Perekonomian Setda Provinsi Lampung', 'bureau', 1, 170),
(1, 'BIRO-UMUM-LAMPUNG', 'Biro Umum Setda Provinsi Lampung', 'bureau', 1, 180),
(1, 'DBMBK-LAMPUNG', 'Dinas Bina marga dan Bina Konstruksi Provinsi Lampung', 'department', 1, 190),
(1, 'DESDM-LAMPUNG', 'Dinas Energi dan Sumber Daya Mineral Provinsi Lampung', 'department', 1, 200),
(1, 'DISHUT-LAMPUNG', 'Dinas Kehutanan Provinsi Lampung', 'department', 1, 210),
(1, 'DKP-LAMPUNG', 'Dinas Kelautan dan Perikanan Provinsi Lampung', 'department', 1, 220),
(1, 'DISDUKCAPIL-LAMPUNG', 'Dinas Kependudukan dan Catatan Sipil Provinsi Lampung', 'department', 1, 230),
(1, 'DINKES-LAMPUNG', 'Dinas Kesehatan Provinsi Lampung', 'department', 1, 240),
(1, 'DKPTHP-LAMPUNG', 'Dinas Ketahanan Pangan Tanaman dan Hortikultura Pangan Prov. Lampung', 'department', 1, 250),
(1, 'DISKOMINFOTIK-LAMPUNG', 'Dinas Komunikasi Informatika dan Statistika Provinsi Lampung', 'department', 1, 260),
(1, 'DISKOPUKM-LAMPUNG', 'Dinas Koperasi, Usaha Kecil dan Menengah Provinsi Lampung', 'department', 1, 270),
(1, 'DLH-LAMPUNG', 'Dinas Lingkungan Hidup Provinsi Lampung', 'department', 1, 280),
(1, 'DISPAREKRAF-LAMPUNG', 'Dinas Pariwisata dan Ekonomi Kreatif Provinsi Lampung', 'department', 1, 290),
(1, 'DPMDT-LAMPUNG', 'Dinas Pemberdayaan Masyarakat Desa dan Transmigrasi Provinsi Lampung', 'department', 1, 300),
(1, 'DPPPA-LAMPUNG', 'Dinas Pemberdayaan Perempuan & Perlindungan Anak Provinsi Lampung', 'department', 1, 310),
(1, 'DISPORA-LAMPUNG', 'Dinas Pemuda dan Olahraga Provinsi Lampung', 'department', 1, 320),
(1, 'DPMPTSP-LAMPUNG', 'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu Provinsi Lampung', 'department', 1, 330),
(1, 'DISDIKBUD-LAMPUNG', 'Dinas Pendidikan dan Kebudayaan Provinsi Lampung', 'department', 1, 340),
(1, 'DPSDA-LAMPUNG', 'Dinas Pengelolaan Sumber Daya Air Provinsi Lampung', 'department', 1, 350),
(1, 'DISHUB-LAMPUNG', 'Dinas Perhubungan Provinsi Lampung', 'department', 1, 360),
(1, 'DISPERINDAG-LAMPUNG', 'Dinas Perindustrian dan Perdagangan Provinsi Lampung', 'department', 1, 370),
(1, 'DISBUN-LAMPUNG', 'Dinas Perkebunan Provinsi Lampung', 'department', 1, 380),
(1, 'DISPUSIP-LAMPUNG', 'Dinas Perpustakaan dan Kearsipan Provinsi Lampung', 'department', 1, 390),
(1, 'DPKPCK-LAMPUNG', 'Dinas Perumahan, Kawasan Permukiman dan Cipta Karya Provinsi Lampung', 'department', 1, 400),
(1, 'DISNAKKESWAN-LAMPUNG', 'Dinas Peternakan dan Kesehatan Provinsi Lampung', 'department', 1, 410),
(1, 'DINSOS-LAMPUNG', 'Dinas Sosial Provinsi Lampung', 'department', 1, 420),
(1, 'DISNAKER-LAMPUNG', 'Dinas Tenaga Kerja Provinsi Lampung', 'department', 1, 430),
(1, 'INSPEKTORAT-LAMPUNG', 'Inspektorat Provinsi Lampung', 'inspectorate', 1, 440),
(1, 'RSARP-LAMPUNG', 'Rumah Sakit Alamsjah Ratu Perwiranegara', 'hospital', 1, 450),
(1, 'RSUD-BNH-LAMPUNG', 'Rumah Sakit Umum Daerah Bandar Negara Husada', 'hospital', 1, 460),
(1, 'RSUD-AM-LAMPUNG', 'Rumah Sakit Umum Daerah Dr H Abdul Moeloek', 'hospital', 1, 470),
(1, 'SATPOL-PP-LAMPUNG', 'Satuan Polisi Pamong Peraja Provinsi Lampung', 'agency', 1, 480),
(1, 'SETWAN-DPRD-LAMPUNG', 'Sekretariat DPRD Provinsi Lampung', 'secretariat', 1, 490);

INSERT INTO users (username, email, password_hash, account_status, email_verified_at, last_login_at) VALUES
('superadmin', 'superadmin@begawipbj.com', '$2y$10$zmzcCJaY5klFmo0uDZED3e/LJc2ZAKltolKYRK2DP1x8L3Iu4OmAm', 'active', UTC_TIMESTAMP(), UTC_TIMESTAMP()),
('admin', 'admin@begawipbj.com', '$2y$10$1HIoIICuWneBOdIlujLrgOfHHMIGnrfLx4iHs3gFcYyG4dAQy0oXG', 'active', UTC_TIMESTAMP(), UTC_TIMESTAMP()),
('operator', 'operator@begawipbj.com', '$2y$10$yx0Igg3zF6NzotCikz.BpuVfmmV6a6Wac17WSQ8m4x1MA7mZ9.y8.', 'active', UTC_TIMESTAMP(), NULL),
('trainer', 'trainer@begawipbj.com', '$2y$10$B9yZLpU1Yx1DX8zgQq0XUeK15I5N8qT2mNPZrxw59VMdIZ0Mqubqi', 'active', UTC_TIMESTAMP(), NULL),
('evaluator', 'evaluator@begawipbj.com', '$2y$10$e1LFv.bqFZ2iCcJyfLLXB.pP1F1dQV1AHtUsgX2eUf1YKyWlriYbe', 'active', UTC_TIMESTAMP(), NULL),
('peserta1', 'peserta1@begawipbj.com', '$2y$10$WOvhXr7lAU6T2BeHngYKZezdABxL0H2siYDA5jK26tzI4MlzLj.Vm', 'active', UTC_TIMESTAMP(), NULL),
('peserta2', 'peserta2@begawipbj.com', '$2y$10$QplxD2EbLBvYt0mkg.x0AuIqTSyhu5DZDQe5dC6oJT0e3jeRMRSpG', 'active', UTC_TIMESTAMP(), NULL),
('peserta3', 'peserta3@begawipbj.com', '$2y$10$VfeP25Tlt2PCqac84ZvyIeMcvXvYfuVujcFO7B6S3qN00pN1pUKuy', 'active', UTC_TIMESTAMP(), NULL),
('public', 'public@begawipbj.com', '$2y$10$LdPLK8pK2UhjEGaL1oVE7.fz5lYoK8lgaCJm1kQbxKEaXlZv9HLUy', 'active', UTC_TIMESTAMP(), NULL);

INSERT INTO user_profiles (user_id, profile_category, full_name, phone, province_code, regency_code, district_code, village_code)
SELECT id,
       CASE username
           WHEN 'superadmin' THEN 'asn'
           WHEN 'admin' THEN 'asn'
           WHEN 'operator' THEN 'asn'
           WHEN 'trainer' THEN 'academic_practitioner'
           WHEN 'evaluator' THEN 'academic_practitioner'
           WHEN 'peserta1' THEN 'asn'
           WHEN 'peserta2' THEN 'provider'
           WHEN 'peserta3' THEN 'public'
           ELSE 'public'
       END,
       CASE username
           WHEN 'superadmin' THEN 'Super Admin BegawiPBJ'
           WHEN 'admin' THEN 'Admin BegawiPBJ'
           WHEN 'operator' THEN 'Operator BegawiPBJ'
           WHEN 'trainer' THEN 'Trainer PBJ Lampung'
           WHEN 'evaluator' THEN 'Evaluator PBJ Lampung'
           WHEN 'peserta1' THEN 'Peserta ASN Satu'
           WHEN 'peserta2' THEN 'Peserta Penyedia Dua'
           WHEN 'peserta3' THEN 'Peserta Publik Tiga'
           ELSE 'Public User BegawiPBJ'
       END,
       '081200000000', '18', '18.01', '18.01.04', '18.01.04.2001'
FROM users;

INSERT INTO user_asn_profiles (user_id, nip, employment_status, position_name, rank_name, grade_name, organization_unit_id)
SELECT id,
       CASE username
           WHEN 'superadmin' THEN '198001012005011001'
           WHEN 'admin' THEN '198102022006021002'
           WHEN 'operator' THEN '198203032007031003'
           WHEN 'peserta1' THEN '199001012015011004'
       END,
       'active',
       CASE username
           WHEN 'superadmin' THEN 'Administrator Sistem BegawiPBJ'
           WHEN 'admin' THEN 'Administrator PBJ'
           WHEN 'operator' THEN 'Operator PBJ'
           ELSE 'Analis Pengadaan Barang/Jasa'
       END,
       'Penata', 'III/c',
       CASE username
           WHEN 'superadmin' THEN 17
           WHEN 'admin' THEN 17
           WHEN 'operator' THEN 17
           ELSE 2
       END
FROM users
WHERE username IN ('superadmin', 'admin', 'operator', 'peserta1');

INSERT INTO user_provider_profiles (user_id, nib, company_name, responsible_person)
SELECT id, '9120000000002', 'PT Penyedia Demo Lampung', 'Peserta Penyedia Dua'
FROM users WHERE username = 'peserta2';

INSERT INTO user_academic_profiles (user_id, institution_name, expertise)
SELECT id, 'Pusat Pembelajaran PBJ Lampung', 'Pengadaan Barang/Jasa Pemerintah'
FROM users WHERE username IN ('trainer', 'evaluator');

INSERT INTO user_public_profiles (user_id, additional_information)
SELECT id, JSON_OBJECT('source', 'dummy_data_v3', 'note', 'Akun publik demo')
FROM users WHERE username IN ('peserta3', 'public');

INSERT INTO user_organizations (user_id, organization_unit_id, is_primary, start_date, end_date)
SELECT id,
       CASE username
           WHEN 'superadmin' THEN 17
           WHEN 'admin' THEN 17
           WHEN 'operator' THEN 17
           WHEN 'peserta1' THEN 2
           WHEN 'public' THEN 27
       END,
       1, '2025-01-01', NULL
FROM users
WHERE username IN ('superadmin', 'admin', 'operator', 'peserta1', 'public');

INSERT INTO user_roles (user_id, role_id, assigned_by, assigned_at)
SELECT u.id, r.id, sa.id, UTC_TIMESTAMP()
FROM users u
JOIN roles r ON r.code = CASE u.username
    WHEN 'superadmin' THEN 'super_admin'
    WHEN 'admin' THEN 'admin'
    WHEN 'operator' THEN 'operator'
    WHEN 'trainer' THEN 'trainer'
    WHEN 'evaluator' THEN 'evaluator'
    WHEN 'peserta1' THEN 'participant'
    WHEN 'peserta2' THEN 'participant'
    WHEN 'peserta3' THEN 'participant'
    WHEN 'public' THEN 'public_user'
END
CROSS JOIN (SELECT id FROM users WHERE username = 'superadmin') sa;

INSERT INTO login_attempts (identifier, user_id, ip_address, was_successful, attempted_at)
SELECT email, id, '127.0.0.1', 1, UTC_TIMESTAMP() FROM users;

INSERT INTO password_reset_tokens (user_id, token_hash, expires_at, used_at)
SELECT id, SHA2(CONCAT('dummy-reset-', username), 256), DATE_ADD(UTC_TIMESTAMP(), INTERVAL 1 DAY), NULL
FROM users WHERE username = 'public';

-- =============================================================================
-- 02. FILE ASSET, APPROVAL, AUDIT, DAN NOTIFIKASI
-- =============================================================================

INSERT INTO file_assets (disk, path, original_name, stored_name, mime_type, extension, size_bytes, checksum_sha256, visibility, uploaded_by) VALUES
('local_private', NULL, 'cover-pelatihan-pbj.jpg', NULL, NULL, NULL, NULL, NULL, 'public', 2),
('local_private', NULL, 'modul-dasar-pbj.pdf', NULL, NULL, NULL, NULL, NULL, 'protected', 4),
('local_private', NULL, 'template-berita.docx', NULL, NULL, NULL, NULL, 'protected', 2),
('local_private', NULL, 'regulasi-pbj-versi-1.pdf', NULL, NULL, NULL, NULL, 'protected', 2),
('local_private', NULL, 'certificate-layout.png', NULL, NULL, NULL, NULL, 'protected', 1),
('local_private', NULL, 'assignment-peserta1.pdf', NULL, NULL, NULL, NULL, 'private', 6),
('local_private', NULL, 'bukti-sertifikat-kompetensi.pdf', NULL, NULL, NULL, NULL, 'private', 2),
('local_private', NULL, 'dummy-certificate.pdf', NULL, NULL, NULL, NULL, 'protected', 1);

INSERT INTO approvals (subject_type, subject_id, workflow_status, submitted_by, submitted_at, reviewed_by, reviewed_at, approved_by, approved_at, published_at) VALUES
('learning_course', 1, 'published', 4, UTC_TIMESTAMP(), 2, UTC_TIMESTAMP(), 2, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
('article', 1, 'published', 2, UTC_TIMESTAMP(), 2, UTC_TIMESTAMP(), 2, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
('regulation', 1, 'published', 2, UTC_TIMESTAMP(), 2, UTC_TIMESTAMP(), 2, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
('news', 1, 'published', 2, UTC_TIMESTAMP(), 2, UTC_TIMESTAMP(), 2, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
('event', 1, 'published', 2, UTC_TIMESTAMP(), 2, UTC_TIMESTAMP(), 2, UTC_TIMESTAMP(), UTC_TIMESTAMP());

INSERT INTO approval_histories (approval_id, from_status, to_status, action, notes, acted_by, acted_at) VALUES
(1, NULL, 'draft', 'create', 'Course dibuat.', 4, UTC_TIMESTAMP()),
(1, 'draft', 'submitted', 'submit', 'Course diajukan.', 4, UTC_TIMESTAMP()),
(1, 'submitted', 'published', 'approve_publish', 'Course dipublikasikan.', 2, UTC_TIMESTAMP()),
(2, NULL, 'published', 'approve_publish', 'Artikel dipublikasikan.', 2, UTC_TIMESTAMP()),
(3, NULL, 'published', 'approve_publish', 'Regulasi dipublikasikan.', 2, UTC_TIMESTAMP()),
(4, NULL, 'published', 'approve_publish', 'Berita dipublikasikan.', 2, UTC_TIMESTAMP()),
(5, NULL, 'published', 'approve_publish', 'Event dipublikasikan.', 2, UTC_TIMESTAMP());

INSERT INTO audit_logs (actor_user_id, action, subject_type, subject_id, route_name, request_id, ip_address, user_agent, before_data, after_data, context_data) VALUES
(1, 'users.assign_role', 'user', 2, 'admin.users.roles.assign', 'dummy-request-001', '127.0.0.1', 'BegawiPBJ Dummy Seeder', NULL, JSON_OBJECT('role', 'admin'), JSON_OBJECT('source', 'dummy_data_v3')),
(4, 'learning.class.configure_assessment', 'learning_class', 1, 'learning.class.assessment.update', 'dummy-request-002', '127.0.0.1', 'BegawiPBJ Dummy Seeder', NULL, JSON_OBJECT('post_test_weight', 60), JSON_OBJECT('source', 'dummy_data_v3')),
(5, 'learning.assignment.grade', 'assignment_submission', 1, 'workspace.evaluator.grade', 'dummy-request-003', '127.0.0.1', 'BegawiPBJ Dummy Seeder', NULL, JSON_OBJECT('score', 82), JSON_OBJECT('source', 'dummy_data_v3'));

INSERT INTO notifications (user_id, channel, notification_type, title, message, data, read_at, sent_at, failed_at) VALUES
(6, 'in_app', 'learning.enrollment.approved', 'Enrollment disetujui', 'Anda telah terdaftar pada Kelas Dasar PBJ Angkatan 2026-A.', JSON_OBJECT('enrollment_id', 1), NULL, UTC_TIMESTAMP(), NULL),
(6, 'email', 'learning.assignment.revision', 'Revisi assignment diperlukan', 'Evaluator meminta revisi pada assignment Anda.', JSON_OBJECT('assignment_id', 1), NULL, UTC_TIMESTAMP(), NULL),
(7, 'in_app', 'event.registration.waitlisted', 'Masuk daftar tunggu', 'Anda berada pada daftar tunggu event.', JSON_OBJECT('event_id', 2), NULL, UTC_TIMESTAMP(), NULL),
(1, 'in_app', 'system.seed.completed', 'Dummy data siap', 'Dummy data BegawiPBJ v3 berhasil dimuat.', JSON_OBJECT('version', 'v3'), UTC_TIMESTAMP(), UTC_TIMESTAMP(), NULL);

-- =============================================================================
-- 03. ARENA BELAJAR
-- =============================================================================

INSERT INTO programs (code, title, slug, summary, description, cover_file_asset_id, workflow_status, created_by, published_at) VALUES
('PROGRAM-PBJ-2026', 'Program Penguatan Kompetensi PBJ 2026', 'program-penguatan-kompetensi-pbj-2026', 'Program pembelajaran dasar dan lanjutan PBJ.', 'Program demo untuk pengujian Arena Belajar.', 1, 'published', 2, UTC_TIMESTAMP());

INSERT INTO courses (program_id, code, title, slug, summary, description, learning_outcomes, estimated_duration_minutes, workflow_status, created_by, published_at) VALUES
(1, 'COURSE-DASAR-PBJ', 'Dasar-Dasar Pengadaan Barang/Jasa Pemerintah', 'dasar-dasar-pbj', 'Course dasar untuk peserta PBJ.', 'Materi dasar pengadaan barang/jasa pemerintah.', 'Peserta memahami prinsip, regulasi, dan tahapan PBJ.', 480, 'published', 4, UTC_TIMESTAMP()),
(1, 'COURSE-REGULASI-PBJ', 'Pemahaman Regulasi PBJ', 'pemahaman-regulasi-pbj', 'Course regulasi PBJ.', 'Course demo kedua untuk katalog.', 'Peserta mampu menelusuri regulasi PBJ.', 240, 'draft', 4, NULL);

INSERT INTO learning_classes (course_id, code, title, delivery_mode, start_at, end_at, enrollment_open_at, enrollment_close_at, quota, status, created_by) VALUES
(1, 'KELAS-DASAR-PBJ-2026-A', 'Kelas Dasar PBJ Angkatan 2026-A', 'blended', '2026-10-01 01:00:00', '2026-10-31 09:00:00', '2026-09-01 01:00:00', '2026-09-30 09:00:00', 30, 'open', 4),
(1, 'KELAS-DASAR-PBJ-2026-B', 'Kelas Dasar PBJ Angkatan 2026-B', 'online', '2026-11-01 01:00:00', '2026-11-30 09:00:00', '2026-10-01 01:00:00', '2026-10-31 09:00:00', 20, 'draft', 4);

INSERT INTO assessment_configurations (learning_class_id, pre_test_enabled, pre_test_attempt_limit, quiz_enabled, quiz_attempt_limit, quiz_best_attempt_enabled, assignment_enabled, assignment_required_when_enabled, post_test_enabled, post_test_attempt_limit, post_test_best_attempt_enabled, quiz_weight, assignment_weight, post_test_weight, passing_grade, participant_feedback_required, certificate_enabled, created_by, updated_by) VALUES
(1, 1, 1, 1, 3, 1, 1, 1, 1, 3, 1, 20.00, 20.00, 60.00, 70.00, 1, 1, 4, 4),
(2, 1, 1, 1, 2, 1, 0, 1, 1, 2, 1, 40.00, 0.00, 60.00, 70.00, 1, 1, 4, 4);

INSERT INTO class_trainers (learning_class_id, user_id, is_primary, assigned_by, assigned_at) VALUES
(1, 4, 1, 2, UTC_TIMESTAMP()), (2, 4, 1, 2, UTC_TIMESTAMP());

INSERT INTO class_evaluators (learning_class_id, user_id, assigned_by, assigned_at) VALUES
(1, 5, 2, UTC_TIMESTAMP()), (2, 5, 2, UTC_TIMESTAMP());

INSERT INTO learning_modules (course_id, title, description, sequence_order, is_required) VALUES
(1, 'Modul 1: Konsep Dasar PBJ', 'Prinsip dan konsep dasar PBJ.', 1, 1),
(1, 'Modul 2: Regulasi dan Tahapan PBJ', 'Regulasi serta tahapan proses PBJ.', 2, 1);

INSERT INTO lessons (learning_module_id, title, slug, content_type, content_body, external_url, sequence_order, estimated_duration_minutes, is_required, is_published) VALUES
(1, 'Prinsip Pengadaan Barang/Jasa', 'prinsip-pengadaan-barang-jasa', 'text', '<p>Materi prinsip PBJ.</p>', NULL, 1, 60, 1, 1),
(1, 'Etika Pengadaan', 'etika-pengadaan', 'document', '<p>Materi etika pengadaan.</p>', NULL, 2, 45, 1, 1),
(2, 'Tahapan Pengadaan', 'tahapan-pengadaan', 'video', '<p>Materi tahapan pengadaan.</p>', 'https://example.test/video/tahapan-pengadaan', 1, 75, 1, 1),
(2, 'Penelusuran Regulasi PBJ', 'penelusuran-regulasi-pbj', 'link', '<p>Materi penelusuran regulasi.</p>', 'https://example.test/regulasi', 2, 45, 0, 1);

INSERT INTO lesson_materials (lesson_id, file_asset_id, title, sequence_order, is_required) VALUES
(2, 2, 'Modul Dasar PBJ', 1, 1);

INSERT INTO enrollments (user_id, learning_class_id, enrollment_status, enrolled_at, approved_by, approved_at, completed_at, final_status) VALUES
(6, 1, 'completed', '2026-09-10 01:00:00', 3, '2026-09-11 01:00:00', '2026-10-30 08:00:00', 'passed'),
(7, 1, 'approved', '2026-09-12 01:00:00', 3, '2026-09-13 01:00:00', NULL, 'in_progress'),
(8, 1, 'pending', '2026-09-15 01:00:00', NULL, NULL, NULL, 'in_progress');

INSERT INTO learning_progress (enrollment_id, lesson_id, started_at, completed_at, progress_percent, last_accessed_at) VALUES
(1, 1, '2026-10-01 02:00:00', '2026-10-01 03:00:00', 100.00, '2026-10-01 03:00:00'),
(1, 2, '2026-10-02 02:00:00', '2026-10-02 03:00:00', 100.00, '2026-10-02 03:00:00'),
(1, 3, '2026-10-03 02:00:00', '2026-10-03 03:00:00', 100.00, '2026-10-03 03:00:00'),
(1, 4, '2026-10-04 02:00:00', NULL, 70.00, '2026-10-04 03:00:00'),
(2, 1, '2026-10-05 02:00:00', '2026-10-05 03:00:00', 100.00, '2026-10-05 03:00:00'),
(2, 2, '2026-10-06 02:00:00', NULL, 50.00, '2026-10-06 02:30:00');

INSERT INTO quizzes (lesson_id, learning_class_id, title, assessment_type, instructions, attempt_limit, duration_minutes, passing_score, is_required, is_published, available_from, available_until, created_by) VALUES
(NULL, 1, 'Pre-test Dasar PBJ', 'pre_test', 'Kerjakan pre-test satu kali.', 1, 30, NULL, 1, 1, '2026-10-01 01:00:00', '2026-10-31 09:00:00', 4),
(1, NULL, 'Quiz Prinsip PBJ', 'lesson_quiz', 'Quiz pemahaman prinsip PBJ.', 3, 20, 70.00, 1, 1, '2026-10-01 01:00:00', '2026-10-31 09:00:00', 4),
(3, NULL, 'Quiz Tahapan PBJ', 'lesson_quiz', 'Quiz pemahaman tahapan PBJ.', 3, 20, 70.00, 1, 1, '2026-10-01 01:00:00', '2026-10-31 09:00:00', 4),
(NULL, 1, 'Post-test Dasar PBJ', 'post_test', 'Kerjakan post-test setelah seluruh komponen selesai.', 3, 45, 70.00, 1, 1, '2026-10-01 01:00:00', '2026-10-31 09:00:00', 4),
(NULL, 2, 'Pre-test Kelas B', 'pre_test', 'Pre-test kelas B.', 1, 30, NULL, 1, 0, NULL, NULL, 4),
(NULL, 2, 'Post-test Kelas B', 'post_test', 'Post-test kelas B.', 2, 45, 70.00, 1, 0, NULL, NULL, 4);

INSERT INTO questions (quiz_id, question_type, question_text, explanation, score_weight, sequence_order, is_active) VALUES
(1, 'multiple_choice', 'Apa tujuan utama pengadaan barang/jasa pemerintah?', 'Untuk memenuhi kebutuhan pemerintah secara efektif.', 1.00, 1, 1),
(1, 'true_false', 'Pre-test dihitung dalam nilai akhir.', 'Pernyataan ini salah.', 1.00, 2, 1),
(2, 'multiple_choice', 'Prinsip PBJ yang tepat adalah?', 'Efisien, efektif, transparan, terbuka, bersaing, adil, akuntabel.', 1.00, 1, 1),
(2, 'multiple_response', 'Pilih prinsip PBJ.', 'Pilih semua opsi yang benar.', 1.00, 2, 1),
(3, 'multiple_choice', 'Tahapan yang terkait perencanaan pengadaan adalah?', 'Identifikasi kebutuhan dan perencanaan.', 1.00, 1, 1),
(4, 'multiple_choice', 'Dokumen yang menjadi salah satu rujukan pengadaan adalah?', 'Regulasi PBJ yang berlaku.', 1.00, 1, 1),
(4, 'true_false', 'Post-test memiliki kontribusi 60% pada nilai akhir baseline.', 'Pernyataan benar.', 1.00, 2, 1);

INSERT INTO question_options (question_id, option_text, is_correct, sequence_order) VALUES
(1, 'Memenuhi kebutuhan pemerintah secara efektif dan akuntabel', 1, 1),
(1, 'Menambah beban administrasi tanpa tujuan', 0, 2),
(2, 'Benar', 0, 1), (2, 'Salah', 1, 2),
(3, 'Efisien, efektif, transparan, terbuka, bersaing, adil, dan akuntabel', 1, 1),
(3, 'Rahasia dan tanpa pertanggungjawaban', 0, 2),
(4, 'Efisien', 1, 1), (4, 'Transparan', 1, 2), (4, 'Tidak akuntabel', 0, 3),
(5, 'Identifikasi kebutuhan dan perencanaan pengadaan', 1, 1), (5, 'Pencabutan sertifikat peserta', 0, 2),
(6, 'Regulasi PBJ yang berlaku', 1, 1), (6, 'Dokumen tanpa sumber resmi', 0, 2),
(7, 'Benar', 1, 1), (7, 'Salah', 0, 2);

INSERT INTO quiz_attempts (quiz_id, enrollment_id, attempt_number, started_at, submitted_at, score, is_passed, status) VALUES
(1, 1, 1, '2026-10-01 01:30:00', '2026-10-01 01:45:00', 55.00, 1, 'graded'),
(2, 1, 1, '2026-10-02 01:30:00', '2026-10-02 01:40:00', 70.00, 1, 'graded'),
(2, 1, 2, '2026-10-02 02:00:00', '2026-10-02 02:10:00', 90.00, 1, 'graded'),
(3, 1, 1, '2026-10-03 01:30:00', '2026-10-03 01:40:00', 80.00, 1, 'graded'),
(4, 1, 1, '2026-10-30 06:00:00', '2026-10-30 06:20:00', 70.00, 1, 'graded'),
(4, 1, 2, '2026-10-30 07:00:00', '2026-10-30 07:20:00', 85.00, 1, 'graded'),
(1, 2, 1, '2026-10-05 01:30:00', '2026-10-05 01:45:00', 45.00, 1, 'graded'),
(2, 2, 1, '2026-10-06 01:30:00', NULL, NULL, 0, 'in_progress');

INSERT INTO pre_test_attempts (enrollment_id, quiz_id, quiz_attempt_id, started_at, submitted_at, score, is_completed) VALUES
(1, 1, 1, '2026-10-01 01:30:00', '2026-10-01 01:45:00', 55.00, 1),
(2, 1, 7, '2026-10-05 01:30:00', '2026-10-05 01:45:00', 45.00, 1);

INSERT INTO quiz_answers (quiz_attempt_id, question_id, answer_text, is_correct, score_awarded) VALUES
(1, 1, NULL, 1, 1.00), (1, 2, NULL, 0, 0.00),
(2, 3, NULL, 1, 1.00), (2, 4, NULL, 0, 0.40),
(3, 3, NULL, 1, 1.00), (3, 4, NULL, 1, 1.00),
(4, 5, NULL, 1, 1.00),
(5, 6, NULL, 1, 1.00), (5, 7, NULL, 0, 0.00),
(6, 6, NULL, 1, 1.00), (6, 7, NULL, 1, 1.00);

INSERT INTO quiz_answer_options (quiz_answer_id, question_option_id) VALUES
(1, 1), (2, 1), (3, 5), (4, 7), (5, 5), (6, 7), (6, 8),
(7, 10), (8, 12), (9, 15), (10, 12), (11, 14);

INSERT INTO assignments (learning_class_id, lesson_id, title, instruction, due_at, max_score, is_required, is_active, allow_resubmission, max_submission_count, created_by) VALUES
(1, 3, 'Assignment Analisis Tahapan PBJ', 'Buat analisis ringkas tahapan pengadaan barang/jasa.', '2026-10-20 09:00:00', 100.00, 1, 1, 1, 3, 4);

INSERT INTO assignment_submissions (assignment_id, enrollment_id, submission_number, submission_text, status, submitted_at, revision_requested_at, resubmission_due_at) VALUES
(1, 1, 1, 'Analisis awal tahapan PBJ oleh peserta 1.', 'revision_required', '2026-10-15 08:00:00', '2026-10-16 08:00:00', '2026-10-19 09:00:00'),
(1, 1, 2, 'Analisis revisi tahapan PBJ oleh peserta 1.', 'graded', '2026-10-18 08:00:00', NULL, NULL),
(1, 2, 1, 'Draft assignment peserta 2.', 'submitted', '2026-10-18 08:00:00', NULL, NULL);

INSERT INTO assignment_submission_files (assignment_submission_id, file_asset_id) VALUES (1, 6), (2, 6);

INSERT INTO assignment_grades (assignment_submission_id, grader_user_id, score, feedback, graded_at) VALUES
(1, 5, 65.00, 'Mohon perbaiki hubungan antara perencanaan dan pemilihan penyedia.', '2026-10-16 08:00:00'),
(2, 5, 82.00, 'Revisi sudah baik dan argumentasi lebih lengkap.', '2026-10-19 08:00:00');

INSERT INTO post_test_attempts (enrollment_id, quiz_id, quiz_attempt_id, attempt_number, started_at, submitted_at, score, is_completed) VALUES
(1, 4, 5, 1, '2026-10-30 06:00:00', '2026-10-30 06:20:00', 70.00, 1),
(1, 4, 6, 2, '2026-10-30 07:00:00', '2026-10-30 07:20:00', 85.00, 1);

INSERT INTO participant_feedbacks (enrollment_id, rating, feedback_text, submitted_at) VALUES
(1, 5, 'Materi dan evaluasi membantu pemahaman dasar PBJ.', '2026-10-30 08:00:00');

INSERT INTO final_grades (enrollment_id, quiz_score, assignment_score, post_test_score, final_score, passing_grade, is_passed, all_required_lessons_completed, all_required_assessments_completed, participant_feedback_completed, certificate_eligible, calculated_at, calculated_by, notes) VALUES
(1, 85.00, 82.00, 85.00, 84.40, 70.00, 1, 1, 1, 1, 1, '2026-10-30 08:15:00', 5, 'Eligible untuk sertifikat pembelajaran.'),
(2, NULL, NULL, NULL, NULL, 70.00, 0, 0, 0, 0, 0, '2026-10-18 09:00:00', 5, 'Masih dalam proses pembelajaran.');

-- =============================================================================
-- 04. BUNGA RAMPAI
-- =============================================================================

INSERT INTO knowledge_categories (parent_id, name, slug, description, sort_order, is_active) VALUES
(NULL, 'Dasar PBJ', 'dasar-pbj', 'Konten dasar pengadaan barang/jasa.', 1, 1),
(1, 'Perencanaan Pengadaan', 'perencanaan-pengadaan', 'Konten perencanaan pengadaan.', 2, 1),
(NULL, 'Regulasi dan Kepatuhan', 'regulasi-kepatuhan', 'Konten regulasi PBJ.', 3, 1);

INSERT INTO tags (name, slug) VALUES ('PBJ', 'pbj'), ('Perencanaan', 'perencanaan'), ('Regulasi', 'regulasi'), ('Best Practice', 'best-practice');

INSERT INTO articles (knowledge_category_id, title, slug, excerpt, content, cover_file_asset_id, workflow_status, author_id, published_at, view_count) VALUES
(1, 'Memahami Prinsip Dasar PBJ', 'memahami-prinsip-dasar-pbj', 'Ringkasan prinsip dasar PBJ.', '<p>Artikel demo mengenai prinsip dasar PBJ.</p>', 1, 'published', 2, UTC_TIMESTAMP(), 125),
(2, 'Checklist Perencanaan Pengadaan', 'checklist-perencanaan-pengadaan', 'Checklist perencanaan pengadaan.', '<p>Artikel demo checklist perencanaan.</p>', NULL, 'draft', 2, NULL, 0);

INSERT INTO article_tags (article_id, tag_id) VALUES (1, 1), (1, 4), (2, 1), (2, 2);

INSERT INTO faqs (knowledge_category_id, question, answer, workflow_status, sequence_order, created_by, published_at) VALUES
(1, 'Apa itu PBJ?', '<p>PBJ adalah Pengadaan Barang/Jasa Pemerintah.</p>', 'published', 1, 2, UTC_TIMESTAMP()),
(3, 'Di mana regulasi PBJ dapat ditelusuri?', '<p>Regulasi dapat ditelusuri pada menu Regulasi PBJ.</p>', 'published', 2, 2, UTC_TIMESTAMP());

INSERT INTO case_studies (knowledge_category_id, title, slug, summary, content, workflow_status, author_id, published_at) VALUES
(2, 'Studi Kasus Perencanaan Belanja', 'studi-kasus-perencanaan-belanja', 'Studi kasus perencanaan belanja pemerintah.', '<p>Isi studi kasus demo.</p>', 'published', 4, UTC_TIMESTAMP());

INSERT INTO document_templates (knowledge_category_id, title, slug, description, file_asset_id, version_label, workflow_status, created_by, published_at) VALUES
(2, 'Template Checklist Perencanaan PBJ', 'template-checklist-perencanaan-pbj', 'Template dokumen perencanaan.', 3, 'v1.0', 'published', 2, UTC_TIMESTAMP());

INSERT INTO bookmarks (user_id, bookmarkable_type, bookmarkable_id) VALUES (6, 'article', 1), (6, 'case_study', 1), (7, 'document_template', 1);

-- =============================================================================
-- 05. REGULASI PBJ
-- =============================================================================

INSERT INTO regulation_categories (parent_id, name, slug, description, sort_order, is_active) VALUES
(NULL, 'Peraturan Presiden', 'peraturan-presiden', 'Kategori regulasi tingkat Peraturan Presiden.', 1, 1),
(NULL, 'Peraturan LKPP', 'peraturan-lkpp', 'Kategori regulasi LKPP.', 2, 1);

INSERT INTO procurement_stages (code, name, description, sort_order, is_active) VALUES
('PERENCANAAN', 'Perencanaan Pengadaan', 'Tahap perencanaan pengadaan.', 1, 1),
('PERSIAPAN', 'Persiapan Pengadaan', 'Tahap persiapan pengadaan.', 2, 1),
('PEMILIHAN', 'Pemilihan Penyedia', 'Tahap pemilihan penyedia.', 3, 1),
('PELAKSANAAN', 'Pelaksanaan Kontrak', 'Tahap pelaksanaan kontrak.', 4, 1);

INSERT INTO competencies (code, name, description, sort_order, is_active) VALUES
('PBJ_DASAR', 'Kompetensi Dasar PBJ', 'Kompetensi dasar pengadaan barang/jasa.', 1, 1),
('PERENCANAAN_PBJ', 'Kompetensi Perencanaan PBJ', 'Kompetensi perencanaan pengadaan.', 2, 1),
('MANAJEMEN_KONTRAK', 'Kompetensi Manajemen Kontrak', 'Kompetensi pelaksanaan kontrak.', 3, 1);

INSERT INTO regulations (regulation_category_id, title, regulation_number, `year`, official_date, effective_date, legal_status, workflow_status, summary, source_url, created_by, published_at) VALUES
(1, 'Peraturan Presiden tentang Pengadaan Barang/Jasa Pemerintah', '16', 2018, '2018-03-22', '2018-03-22', 'amended', 'published', 'Regulasi dasar PBJ.', 'https://example.test/regulasi/perpres-16-2018', 2, UTC_TIMESTAMP()),
(2, 'Peraturan LKPP tentang Pedoman Pengadaan', '1', 2025, '2025-01-10', '2025-01-10', 'active', 'draft', 'Regulasi demo LKPP.', 'https://example.test/regulasi/perlkpp-1-2025', 2, NULL);

INSERT INTO regulation_versions (regulation_id, version_number, file_asset_id, change_notes, is_current, workflow_status, uploaded_by, published_at) VALUES
(1, 1, 4, 'Versi dokumen regulasi demo.', 1, 'published', 2, UTC_TIMESTAMP()),
(2, 1, 4, 'Draft awal regulasi demo.', 1, 'draft', 2, NULL);

INSERT INTO regulation_procurement_stages (regulation_id, procurement_stage_id) VALUES (1, 1), (1, 2), (1, 3), (1, 4);
INSERT INTO regulation_competencies (regulation_id, competency_id) VALUES (1, 1), (1, 2), (1, 3);
INSERT INTO regulation_keywords (regulation_id, keyword, created_at) VALUES
(1, 'pengadaan barang jasa', UTC_TIMESTAMP()), (1, 'perencanaan pengadaan', UTC_TIMESTAMP()), (1, 'pemilihan penyedia', UTC_TIMESTAMP());
INSERT INTO regulation_download_logs (regulation_version_id, user_id, ip_address, downloaded_at) VALUES
(1, 6, '127.0.0.1', UTC_TIMESTAMP()), (1, NULL, '127.0.0.2', UTC_TIMESTAMP());

-- =============================================================================
-- 06. BERITA, AGENDA, DAN EVENT
-- =============================================================================

INSERT INTO news_categories (name, slug, description, sort_order, is_active) VALUES
('Pengumuman', 'pengumuman', 'Pengumuman resmi BegawiPBJ.', 1, 1),
('Kegiatan', 'kegiatan', 'Publikasi kegiatan PBJ.', 2, 1),
('Kebijakan', 'kebijakan', 'Update kebijakan PBJ.', 3, 1);

INSERT INTO news (news_category_id, title, slug, excerpt, content, cover_file_asset_id, workflow_status, author_id, published_at, view_count) VALUES
(1, 'Pembukaan Kelas Dasar PBJ Angkatan 2026-A', 'pembukaan-kelas-dasar-pbj-2026-a', 'Informasi pembukaan kelas.', '<p>Kelas Dasar PBJ Angkatan 2026-A dibuka.</p>', 1, 'published', 2, UTC_TIMESTAMP(), 80),
(2, 'Workshop Penguatan Kompetensi PBJ', 'workshop-penguatan-kompetensi-pbj', 'Informasi workshop PBJ.', '<p>Workshop demo untuk kalender kegiatan.</p>', NULL, 'draft', 2, NULL, 0);

INSERT INTO events (title, slug, description, event_type, delivery_mode, location_text, meeting_url, start_at, end_at, registration_open_at, registration_close_at, quota, event_status, workflow_status, certificate_enabled, created_by, published_at) VALUES
('Webinar Dasar PBJ Pemerintah', 'webinar-dasar-pbj-pemerintah', 'Webinar pengenalan dasar PBJ.', 'webinar', 'online', NULL, 'https://example.test/meeting/webinar-pbj', '2026-10-15 02:00:00', '2026-10-15 04:00:00', '2026-09-20 01:00:00', '2026-10-14 09:00:00', 100, 'finished', 'published', 1, 2, UTC_TIMESTAMP()),
('Workshop Perencanaan Pengadaan', 'workshop-perencanaan-pengadaan', 'Workshop tatap muka perencanaan.', 'workshop', 'offline', 'Bandar Lampung', NULL, '2026-11-10 01:00:00', '2026-11-10 08:00:00', '2026-10-01 01:00:00', '2026-11-05 09:00:00', 2, 'full', 'published', 1, 2, UTC_TIMESTAMP()),
('Sosialisasi Regulasi PBJ', 'sosialisasi-regulasi-pbj', 'Sosialisasi regulasi baru.', 'socialization', 'hybrid', 'Aula Biro PBJ', 'https://example.test/meeting/sosialisasi', '2026-12-01 01:00:00', '2026-12-01 04:00:00', '2026-11-01 01:00:00', '2026-11-28 09:00:00', 50, 'coming_soon', 'draft', 0, 2, NULL);

INSERT INTO event_registrations (event_id, user_id, registration_status, registered_at, cancelled_at, approved_by, notes) VALUES
(1, 6, 'registered', '2026-09-25 01:00:00', NULL, 3, 'Peserta terdaftar.'),
(1, 7, 'registered', '2026-09-26 01:00:00', NULL, 3, 'Peserta terdaftar.'),
(2, 6, 'registered', '2026-10-05 01:00:00', NULL, 3, 'Kuota terisi.'),
(2, 7, 'registered', '2026-10-05 02:00:00', NULL, 3, 'Kuota terisi.'),
(2, 8, 'waitlisted', '2026-10-05 03:00:00', NULL, NULL, 'Daftar tunggu.'),
(3, 6, 'cancelled', '2026-11-03 01:00:00', '2026-11-04 01:00:00', NULL, 'Pembatalan peserta.');

INSERT INTO event_attendances (event_registration_id, attendance_status, checked_in_at, checked_out_at, recorded_by) VALUES
(1, 'present', '2026-10-15 02:00:00', '2026-10-15 04:00:00', 3),
(2, 'excused', NULL, NULL, 3);

-- =============================================================================
-- 07. SERTIFIKAT DIGITAL
-- =============================================================================

INSERT INTO certificate_templates (code, name, certificate_type, html_template, layout_file_asset_id, is_active, created_by) VALUES
('CERT-LEARNING-DEFAULT', 'Sertifikat Kelulusan Pembelajaran', 'learning', '<html><body><h1>Sertifikat Kelulusan</h1><p>{{recipient_name}}</p></body></html>', 5, 1, 1),
('CERT-EVENT-DEFAULT', 'Sertifikat Partisipasi Event', 'event', '<html><body><h1>Sertifikat Partisipasi</h1><p>{{recipient_name}}</p></body></html>', 5, 1, 1);

INSERT INTO certificate_sequences (sequence_scope, `year`, last_number, updated_at) VALUES
('learning', 2026, 1, UTC_TIMESTAMP()), ('event', 2026, 1, UTC_TIMESTAMP());

INSERT INTO certificates (recipient_user_id, certificate_template_id, source_type, source_id, certificate_number, issued_at, status, file_asset_id, verification_token, issued_by, expires_at) VALUES
(6, 1, 'learning_enrollment', 1, 'BGPBJ/LRN/2026/000001', '2026-10-30 08:30:00', 'issued', 8, 'verify-learning-2026-peserta1-000001', 2, NULL),
(6, 2, 'event_registration', 1, 'BGPBJ/EVT/2026/000001', '2026-10-15 05:00:00', 'issued', 8, 'verify-event-2026-peserta1-000001', 2, NULL);

INSERT INTO certificate_verification_logs (certificate_id, verification_token, verified_at, ip_address, user_agent, result) VALUES
(1, 'verify-learning-2026-peserta1-000001', UTC_TIMESTAMP(), '127.0.0.1', 'BegawiPBJ Dummy Seeder', 'valid'),
(2, 'verify-event-2026-peserta1-000001', UTC_TIMESTAMP(), '127.0.0.1', 'BegawiPBJ Dummy Seeder', 'valid'),
(NULL, 'invalid-demo-token', UTC_TIMESTAMP(), '127.0.0.1', 'BegawiPBJ Dummy Seeder', 'not_found');

-- =============================================================================
-- 08. GOVERNMENT INTELLIGENCE PBJ
-- =============================================================================

INSERT INTO jf_ppbj_levels (code, name, sort_order, is_active) VALUES
('PERTAMA', 'JF PPBJ Pertama', 1, 1), ('MUDA', 'JF PPBJ Muda', 2, 1),
('MADYA', 'JF PPBJ Madya', 3, 1), ('UTAMA', 'JF PPBJ Utama', 4, 1);

INSERT INTO jf_ppbj_personnel (user_id, organization_unit_id, employee_name, employee_identifier, jf_ppbj_level_id, employment_status, effective_start_date, effective_end_date, notes) VALUES
(2, 17, 'Admin BegawiPBJ', '198102022006021002', 2, 'active', '2024-01-01', NULL, 'Data dummy intelligence.'),
(3, 17, 'Operator BegawiPBJ', '198203032007031003', 1, 'active', '2025-01-01', NULL, 'Data dummy intelligence.'),
(NULL, 17, 'Personel JF Demo Madya', '197901012004011005', 3, 'transferred', '2020-01-01', '2026-06-30', 'Data dummy tanpa akun.');

INSERT INTO jf_ppbj_histories (jf_ppbj_personnel_id, event_type, effective_date, from_organization_unit_id, to_organization_unit_id, from_level_id, to_level_id, notes, recorded_by) VALUES
(1, 'added', '2024-01-01', NULL, 17, NULL, 2, 'Pencatatan awal personel.', 1),
(2, 'promoted', '2025-01-01', 17, 17, 1, 1, 'Riwayat dummy promosi/penetapan.', 1),
(3, 'transferred_out', '2026-06-30', 17, 2, 3, 3, 'Mutasi keluar dari unit PBJ.', 1);

INSERT INTO competency_certificate_types (code, name, description, is_active) VALUES
('PBJ_LEVEL_1', 'PBJ Level 1', 'Sertifikat kompetensi PBJ Level 1.', 1),
('PPK_TYPE_A', 'PPK Tipe A', 'Sertifikat kompetensi PPK Tipe A.', 1),
('PPK_TYPE_B', 'PPK Tipe B', 'Sertifikat kompetensi PPK Tipe B.', 1),
('PPK_TYPE_C', 'PPK Tipe C', 'Sertifikat kompetensi PPK Tipe C.', 1);

INSERT INTO competency_certificates (user_id, organization_unit_id, holder_name, holder_identifier, competency_certificate_type_id, certificate_number, issued_at, expires_at, status, evidence_file_asset_id, verified_by, verified_at) VALUES
(2, 17, 'Admin BegawiPBJ', '198102022006021002', 1, 'PBJ-L1-DEMO-0001', '2025-01-01', NULL, 'active', 7, 1, UTC_TIMESTAMP()),
(3, 17, 'Operator BegawiPBJ', '198203032007031003', 2, 'PPK-A-DEMO-0001', '2024-01-01', '2027-01-01', 'active', 7, 1, UTC_TIMESTAMP()),
(NULL, 17, 'Personel JF Demo Madya', '197901012004011005', 3, 'PPK-B-DEMO-0001', '2020-01-01', '2025-01-01', 'expired', NULL, 1, UTC_TIMESTAMP());

INSERT INTO competency_gap_snapshots (organization_unit_id, period_date, metric_code, required_count, available_count, gap_count, generated_at, generated_by) VALUES
(17, '2026-01-01', 'JF_PPBJ_MUDA', 5, 2, 3, UTC_TIMESTAMP(), 1),
(17, '2026-01-01', 'PBJ_LEVEL_1', 10, 7, 3, UTC_TIMESTAMP(), 1),
(2, '2026-01-01', 'PPK_TYPE_A', 4, 1, 3, UTC_TIMESTAMP(), 1);

COMMIT;

-- =============================================================================
-- VALIDATION SUMMARY
-- =============================================================================
-- 1. Tabel wilayah tidak menerima INSERT/UPDATE/DELETE dari file ini.
-- 2. Organization_units memuat root Pemerintah Daerah Provinsi Lampung
--    dan seluruh 49 OPD/unit dari daftar yang diberikan.
-- 3. Seluruh kategori, status, jenis, role, permission, dan label domain utama
--    memiliki contoh data dummy.
-- 4. Placeholder file sengaja menggunakan file_assets.path = NULL.
-- 5. Data sertifikat memakai UNIQUE(source_type, source_id).
-- 6. Gunakan hanya untuk development/QA. Jangan dijalankan pada production.
