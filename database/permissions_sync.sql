-- ==================================================================
-- BEGAWIPENGADAAN MODULAR PERMISSIONS CATALOG SYNC SCRIPT
-- Eksekusi query ini di phpMyAdmin / MySQL CLI server deploy
-- ==================================================================

-- 1. Masukkan / Update 90 Permission Modular ke Tabel permissions
INSERT INTO `permissions` (`key`, `name`, `domain`, `description`, `is_system`)
VALUES
  ('dashboard.view', 'Melihat Ringkasan Dashboard Utama', 'dashboard', 'Akses membuka halaman beranda/ringkasan dashboard admin.', 1),
  ('dashboard.indicators.view', 'Melihat Indikator Sistem & KPI Internal', 'dashboard', 'Akses memantau metrik performa sistem, KPI, dan aktivitas terkini.', 1),
  ('landing.view', 'Melihat Modul Landing Page CMS', 'landing', 'Akses melihat daftar konten dinamis landing page publik.', 1),
  ('landing.slider.manage', 'Mengelola Slider Hero & Promosi', 'landing', 'Akses menambah, mengubah, dan menghapus banner carousel dan sorotan.', 1),
  ('landing.profile.manage', 'Mengelola Profil Lembaga & Pilar PATIK', 'landing', 'Akses mengedit deskripsi lembaga, pilar hierarki PBJ, dan ikon fitur.', 1),
  ('landing.portal.manage', 'Mengelola Tautan Portal Eksternal', 'landing', 'Akses mengelola link portal integrasi LKPP, SiRUP, LPSE, dan layanan eksternal.', 1),
  ('landing.team.manage', 'Mengelola Tim Inovator & Testimoni', 'landing', 'Akses mengelola direktori tim pembina dan ulasan/testimoni pengguna.', 1),
  ('landing.announcement.manage', 'Mengelola Pengumuman & Banner CMS', 'landing', 'Akses mengelola pengumuman berjalan dan banner halaman sekunder.', 1),
  ('access.manage', 'Manajemen Akses & Otorisasi Pengguna', 'access', 'Akses administratif menyeluruh ke modul manajemen akses pengguna.', 1),
  ('access.user.view', 'Melihat Data Pengguna Platform', 'access', 'Akses melihat daftar seluruh akun pengguna dan detail profilnya.', 1),
  ('access.user.manage', 'Mengelola Akun Pengguna Platform', 'access', 'Akses membuat, memperbarui, mengaktifkan, dan menonaktifkan akun.', 1),
  ('access.verification.view', 'Melihat Antrean Verifikasi Profil', 'access', 'Akses melihat daftar pengguna dan permohonan perubahan profil yang menunggu review.', 1),
  ('access.verification.approve', 'Memproses Verifikasi & Perubahan Profil', 'access', 'Akses menyetujui atau menolak verifikasi berkas ASN, Penyedia, atau Akademisi.', 1),
  ('access.role.view', 'Melihat Daftar Peran (Role) & Matriks', 'access', 'Akses melihat katalog role dan matriks role-permission.', 1),
  ('access.role.manage', 'Mengelola Peran (Role) & Hak Akses', 'access', 'Akses membuat role baru, mengatur permission, dan memproses permohonan role.', 1),
  ('access.organization.view', 'Melihat Unit Organisasi PBJ', 'access', 'Akses melihat hierarki unit kerja perangkat daerah dan instansi.', 1),
  ('access.organization.manage', 'Mengelola Unit Organisasi PBJ', 'access', 'Akses menambah, mengedit, dan mengarsipkan unit organisasi.', 1),
  ('access.procurementrole.manage', 'Mengelola Peran & Penugasan Pengadaan', 'access', 'Akses mengelola klasifikasi peran fungsional PBJ (PPK, PPTK, Pokja, dsb.).', 1),
  ('user.manage', 'Kelola Akun Pengguna (Alias)', 'access', 'Alias izin administratif untuk manajemen akun pengguna.', 1),
  ('user.verify', 'Verifikasi Profil Akun (Alias)', 'access', 'Alias izin persetujuan verifikasi akun pengguna.', 1),
  ('role.manage', 'Kelola Konfigurasi Peran (Alias)', 'access', 'Alias izin manajerial untuk role dan permission.', 1),
  ('learning.view', 'Melihat Arena Pembelajaran PBJ', 'learning', 'Akses melihat katalog program belajar dan kelas pelatihan PBJ.', 1),
  ('learning.program.manage', 'Mengelola Program Pembelajaran PBJ', 'learning', 'Akses membuat dan mengelola taksonomi program kompetensi PBJ.', 1),
  ('learning.course.manage', 'Mengelola Kursus & Silabus', 'learning', 'Akses mengelola materi kursus, kompetensi dasar, dan capaian pembelajaran.', 1),
  ('learning.class.view', 'Melihat Kelas Pembelajaran PBJ', 'learning', 'Akses melihat jadwal kelas dan daftar rombongan belajar peserta.', 1),
  ('learning.class.create', 'Membuat Kelas Pembelajaran Baru', 'learning', 'Akses membuka kelas baru dan menetapkan kuota pendaftaran.', 1),
  ('learning.class.update', 'Memperbarui Informasi Kelas Pembelajaran', 'learning', 'Akses mengubah silabus, jadwal, instruktur, dan status kelas.', 1),
  ('learning.class.delete', 'Menghapus Kelas Pembelajaran', 'learning', 'Akses membatalkan atau menghapus rekaman kelas pelatihan.', 1),
  ('learning.curriculum.manage', 'Mengelola Kurikulum & Modul Belajar', 'learning', 'Akses menyusun modul silabus dan butir lesson bahan ajar.', 1),
  ('learning.assessment.view', 'Melihat Kuis & Assessment Pembelajaran', 'learning', 'Akses meninjau kuis, soal latihan, dan ujian kompetensi.', 1),
  ('learning.assessment.manage', 'Mengelola Kuis & Bank Soal', 'learning', 'Akses menyusun bank butir soal ujian, passing grade, dan batas durasi.', 1),
  ('learning.enrollment.view', 'Melihat Pendaftaran Peserta Kelas', 'learning', 'Akses melihat status pendaftaran peserta di setiap kelas pelatihan.', 1),
  ('learning.enrollment.verify', 'Memverifikasi Pendaftaran Peserta', 'learning', 'Akses menyetujui atau menolak pendaftaran calon peserta kelas.', 1),
  ('learning.grading.manage', 'Menilai Tugas & Evaluasi Peserta', 'learning', 'Akses memeriksa penugasan dan memberikan nilai akhir kelas.', 1),
  ('learning.certificate.manage', 'Monitoring Kelayakan Sertifikat Pembelajaran', 'learning', 'Akses mengevaluasi kelulusan peserta untuk penerbitan sertifikat e-learning.', 1),
  ('learning.report.view', 'Melihat Laporan Statistik Pembelajaran', 'learning', 'Akses memantau progres ketuntasan dan statistik kelas.', 1),
  ('lms.manage', 'Manajemen Sistem Pembelajaran (LMS Alias)', 'learning', 'Izin administratif penuh atas modul Arena Belajar LMS.', 1),
  ('enrollment.verify', 'Verifikasi Pendaftaran Kelas (Alias)', 'learning', 'Alias verifikasi pendaftaran calon peserta diklat.', 1),
  ('assessment.evaluate', 'Evaluasi Penilaian Peserta (Alias)', 'learning', 'Alias evaluasi dan penilaian hasil ujian peserta.', 1),
  ('bunga.view', 'Melihat Direktori Bunga Rampai PBJ', 'bunga', 'Akses membaca naskah, panduan, artikel, dan template PBJ.', 1),
  ('bunga.article.manage', 'Mengelola Naskah & Artikel PBJ', 'bunga', 'Akses membuat, menyunting, dan menerbitkan artikel naskah PBJ.', 1),
  ('bunga.faq.manage', 'Mengelola Tanya Jawab (FAQ) PBJ', 'bunga', 'Akses mengelola basis pengetahuan tanya jawab konsultasi PBJ.', 1),
  ('bunga.casestudy.manage', 'Mengelola Studi Kasus PBJ', 'bunga', 'Akses mengelola telaah dan studi kasus penanganan masalah PBJ.', 1),
  ('bunga.template.manage', 'Mengelola Template Dokumen PBJ', 'bunga', 'Akses mengunggah dan memperbarui berkas Standar Dokumen Pemilihan (SDP).', 1),
  ('bunga.media.manage', 'Mengelola Pustaka Media & Galeri PBJ', 'bunga', 'Akses mengunggah video infografis dan dokumentasi multimedia.', 1),
  ('bunga.taxonomy.manage', 'Mengelola Kategori & Tag Bunga Rampai', 'bunga', 'Akses mengelola taksonomi bidang dan penanda topik artikel.', 1),
  ('kms.manage', 'Manajemen Knowledge Management System (Alias)', 'bunga', 'Izin administratif penuh atas modul Knowledge Management System (KMS).', 1),
  ('regulation.view', 'Melihat Direktori Regulasi PBJ', 'regulation', 'Akses melihat daftar dan mengunduh berkas regulasi hukum PBJ.', 1),
  ('regulation.create', 'Menambah Dokumen Regulasi Baru', 'regulation', 'Akses mengunggah peraturan perundang-undangan baru ke repositori.', 1),
  ('regulation.edit', 'Mengubah Dokumen Regulasi PBJ', 'regulation', 'Akses memperbarui metadata regulasi, konsideran, dan relasi aturan.', 1),
  ('regulation.delete', 'Menghapus Dokumen Regulasi PBJ', 'regulation', 'Akses menghapus atau mencabut dokumen regulasi dari katalog.', 1),
  ('regulation.approve', 'Menyetujui & Mempublikasikan Regulasi', 'regulation', 'Akses otorisasi rilis dokumen regulasi ke portal publik.', 1),
  ('regulation.category.manage', 'Mengelola Kategori Regulasi PBJ', 'regulation', 'Akses mengatur klasifikasi regulasi (Perpres, Perlem LKPP, Pergub, dsb.).', 1),
  ('regulation.stage.manage', 'Mengelola Tahapan PBJ & Kompetensi', 'regulation', 'Akses memetakan regulasi ke tahapan siklus PBJ dan kompetensi JF PPBJ.', 1),
  ('regulation.manage', 'Manajemen Penuh Regulasi PBJ (Alias)', 'regulation', 'Izin administratif penuh atas modul repositori regulasi PBJ.', 1),
  ('communication.view', 'Melihat Modul Komunikasi & Event', 'communication', 'Akses membuka modul berita, event, dan layanan konsultasi.', 1),
  ('communication.news.manage', 'Mengelola Warta Berita PBJ', 'communication', 'Akses menulis, menyunting, dan menerbitkan rilis berita PBJ.', 1),
  ('communication.event.view', 'Melihat Agenda Event & Bimtek', 'communication', 'Akses melihat kalender kegiatan sosialisasi dan bimbingan teknis.', 1),
  ('communication.event.manage', 'Mengelola Agenda Event & Bimtek', 'communication', 'Akses membuat event, menetapkan kuota, dan mengatur sesi kegiatan.', 1),
  ('communication.attendance.manage', 'Mengelola Presensi & Kehadiran Event', 'communication', 'Akses memvalidasi kehadiran peserta dan menutup sesi presensi.', 1),
  ('communication.inbox.manage', 'Mengelola Pesan Masuk & Helpdesk', 'communication', 'Akses membaca tiket permohonan konsultasi dan pesan masuk publik.', 1),
  ('communication.helpdesk.manage', 'Mengelola Pengaturan Helpdesk Widget', 'communication', 'Akses mengatur nomor kontak dan jam operasional WhatsApp Helpdesk.', 1),
  ('communication.newsletter.manage', 'Mengelola Berlangganan Newsletter', 'communication', 'Akses mengekspor daftar email pelanggan buletin berkala.', 1),
  ('certificate.view', 'Melihat Master & Riwayat Sertifikat', 'certificate', 'Akses melihat rekap sertifikat pelatihan dan event yang telah terbit.', 1),
  ('certificate.manage', 'Mengelola Penerbitan Sertifikat', 'certificate', 'Akses menerbitkan, memperbarui, atau mencabut sertifikat peserta.', 1),
  ('certificate.batch.manage', 'Mengelola Penerbitan Batch Sertifikat', 'certificate', 'Akses memproses penomoran otomatis dan generate e-sertifikat massal.', 1),
  ('certificate.template.manage', 'Mengelola Desain Template Sertifikat', 'certificate', 'Akses mengatur tata letak, tanda tangan digital, dan latar sertifikat.', 1),
  ('certificate.approve', 'Menyetujui Penerbitan Sertifikat', 'certificate', 'Akses otorisasi penandatanganan dan pengesahan sertifikat.', 1),
  ('intelligence.view', 'Melihat Dashboard Intelligence SDM PBJ', 'intelligence', 'Akses memantau sebaran SDM JF PPBJ dan analitik kesenjangan kompetensi.', 1),
  ('intelligence.jfppbj.manage', 'Mengelola Data & Riwayat SDM JF PPBJ', 'intelligence', 'Akses memperbarui jenjang jabatan dan penugasan pejabat fungsional PBJ.', 1),
  ('intelligence.competency.manage', 'Mengelola Data Sertifikat Kompetensi', 'intelligence', 'Akses mencatat jenis dan masa berlaku sertifikasi pengadaan aparatur.', 1),
  ('intelligence.opd.manage', 'Mengelola Analisis Pemetaan Unit Kerja/OPD', 'intelligence', 'Akses analisis kebutuhan SDM pengadaan di setiap instansi perangkat daerah.', 1),
  ('reports.view', 'Melihat Pusat Laporan Dashboard', 'reports', 'Akses membuka konsol laporan terpadu seluruh aktivitas platform.', 1),
  ('reports.learning.view', 'Melihat Laporan Statistik Pembelajaran', 'reports', 'Akses meninjau laporan capaian nilai dan ketuntasan kelas diklat.', 1),
  ('reports.event.view', 'Melihat Laporan Keikutsertaan Event', 'reports', 'Akses meninjau laporan partisipasi dan kehadiran kegiatan sosialisasi.', 1),
  ('reports.certificate.view', 'Melihat Laporan Penerbitan Sertifikat', 'reports', 'Akses meninjau rekapitulasi penerbitan sertifikat berkala.', 1),
  ('reports.competency.view', 'Melihat Laporan Kesenjangan Kompetensi', 'reports', 'Akses meninjau laporan analisis gap kompetensi SDM pengadaan.', 1),
  ('reports.user.view', 'Melihat Laporan Aktivitas Pengguna', 'reports', 'Akses meninjau rekap pertumbuhan pengguna dan keaktifan akun.', 1),
  ('reports.export', 'Mengekspor Berkas Laporan & Riwayat Ekspor', 'reports', 'Akses mengunduh laporan format Excel/CSV dan melihat riwayat ekspor.', 1),
  ('survey.view', 'Melihat Hasil & Statistik Survei Kepuasan', 'survey', 'Akses memantau skor Indeks Kepuasan Masyarakat (IKM) dan umpan balik responden.', 1),
  ('survey.manage', 'Mengelola Pertanyaan & Konfigurasi Survei', 'survey', 'Akses mengonfigurasi indikator survei kepuasan layanan PBJ.', 1),
  ('system.settings', 'Mengelola Pengaturan Sistem & Platform', 'system', 'Akses konfigurasi umum, identitas instansi, integrasi, dan keamanan sistem.', 1),
  ('approval.view', 'Melihat Antrean Approval Sistem', 'system', 'Akses melihat daftar antrean persetujuan perubahan profil dan pendaftaran.', 1),
  ('approvals.review', 'Meninjau Antrean Approval Sistem', 'system', 'Akses memeriksa kelengkapan dokumen pendukung sebelum disetujui.', 1),
  ('approval.decide', 'Memutuskan (Setujui/Tolak) Approval', 'system', 'Akses eksekusi persetujuan atau penolakan pengajuan pada Approval Hub.', 1),
  ('audit.view', 'Melihat Jejak Audit Log Aktivitas', 'system', 'Akses menelusuri rekam jejak aktivitas seluruh pengguna di sistem.', 1),
  ('audit.export', 'Mengekspor Jejak Audit Log', 'system', 'Akses mengekspor berkas CSV jejak audit sistem untuk keperluan forensik.', 1),
  ('notification.view', 'Melihat Pusat Notifikasi Dashboard', 'system', 'Akses membuka konsol notifikasi sistem dan peringatan dini.', 1),
  ('notification.send', 'Mengirim Notifikasi Broadcast Sistem', 'system', 'Akses menyebarkan notifikasi pengumuman ke seluruh atau sebagian pengguna.', 1),
  ('system.maintenance', 'Mengelola Mode Pemeliharaan Platform', 'system', 'Akses mengaktifkan mode maintenance platform dan flush cache sistem.', 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `domain` = VALUES(`domain`), `description` = VALUES(`description`), `is_system` = 1;

-- 2. Berikan Seluruh Hak Akses ke Super Administrator (role_id = 10) & Administrator (role_id = 11)
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `granted_by`, `created_at`)
SELECT 10, p.id, 1, NOW() FROM `permissions` p;

INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `granted_by`, `created_at`)
SELECT 11, p.id, 1, NOW() FROM `permissions` p;

-- 3. Berikan Hak Akses Default ke Operator (role_id = 12)
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `granted_by`, `created_at`)
SELECT 12, p.id, 1, NOW() FROM `permissions` p WHERE p.key IN (
  'dashboard.view', 'dashboard.indicators.view',
  'access.manage', 'access.user.view', 'access.verification.view', 'access.verification.approve',
  'user.verify', 'approvals.review', 'approval.decide', 'approval.view',
  'learning.view', 'learning.class.view', 'learning.enrollment.view', 'learning.enrollment.verify', 'enrollment.verify',
  'communication.view', 'communication.event.view', 'communication.event.manage', 'communication.attendance.manage', 'communication.inbox.manage',
  'certificate.view', 'certificate.manage', 'certificate.batch.manage',
  'audit.view', 'notification.view', 'notification.send', 'survey.view'
);

-- 4. Berikan Hak Akses Default ke Trainer (role_id = 13)
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `granted_by`, `created_at`)
SELECT 13, p.id, 1, NOW() FROM `permissions` p WHERE p.key IN (
  'dashboard.view', 'learning.view', 'learning.class.view', 'learning.curriculum.manage',
  'learning.assessment.view', 'learning.assessment.manage', 'learning.grading.manage',
  'bunga.view'
);

-- 5. Berikan Hak Akses Default ke Evaluator (role_id = 14)
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `granted_by`, `created_at`)
SELECT 14, p.id, 1, NOW() FROM `permissions` p WHERE p.key IN (
  'dashboard.view', 'learning.view', 'learning.class.view', 'learning.assessment.view',
  'learning.grading.manage', 'assessment.evaluate', 'learning.report.view',
  'survey.view', 'reports.view', 'reports.learning.view'
);
