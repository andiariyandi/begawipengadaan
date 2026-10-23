<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermissionCatalogSeeder extends Seeder
{
    /**
     * Comprehensive catalog of system permissions across 12 module domains.
     *
     * @return array<int, array{key: string, name: string, domain: string, description: string, is_system: int}>
     */
    public function getCatalog(): array
    {
        return [
            // 1. Domain dashboard (Utama)
            [
                'key'         => 'dashboard.view',
                'name'        => 'Melihat Ringkasan Dashboard Utama',
                'domain'      => 'dashboard',
                'description' => 'Akses membuka halaman beranda/ringkasan dashboard admin.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'dashboard.indicators.view',
                'name'        => 'Melihat Indikator Sistem & KPI Internal',
                'domain'      => 'dashboard',
                'description' => 'Akses memantau metrik performa sistem, KPI, dan aktivitas terkini.',
                'is_system'   => 1,
            ],

            // 2. Domain landing (Kelola Landing Page)
            [
                'key'         => 'landing.view',
                'name'        => 'Melihat Modul Landing Page CMS',
                'domain'      => 'landing',
                'description' => 'Akses melihat daftar konten dinamis landing page publik.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'landing.slider.manage',
                'name'        => 'Mengelola Slider Hero & Promosi',
                'domain'      => 'landing',
                'description' => 'Akses menambah, mengubah, dan menghapus banner carousel dan sorotan.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'landing.profile.manage',
                'name'        => 'Mengelola Profil Lembaga & Pilar PATIK',
                'domain'      => 'landing',
                'description' => 'Akses mengedit deskripsi lembaga, pilar hierarki PBJ, dan ikon fitur.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'landing.portal.manage',
                'name'        => 'Mengelola Tautan Portal Eksternal',
                'domain'      => 'landing',
                'description' => 'Akses mengelola link portal integrasi LKPP, SiRUP, LPSE, dan layanan eksternal.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'landing.team.manage',
                'name'        => 'Mengelola Tim Inovator & Testimoni',
                'domain'      => 'landing',
                'description' => 'Akses mengelola direktori tim pembina dan ulasan/testimoni pengguna.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'landing.announcement.manage',
                'name'        => 'Mengelola Pengumuman & Banner CMS',
                'domain'      => 'landing',
                'description' => 'Akses mengelola pengumuman berjalan dan banner halaman sekunder.',
                'is_system'   => 1,
            ],

            // 3. Domain access (Manajemen Akses & Pengguna)
            [
                'key'         => 'access.manage',
                'name'        => 'Manajemen Akses & Otorisasi Pengguna',
                'domain'      => 'access',
                'description' => 'Akses administratif menyeluruh ke modul manajemen akses pengguna.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'access.user.view',
                'name'        => 'Melihat Data Pengguna Platform',
                'domain'      => 'access',
                'description' => 'Akses melihat daftar seluruh akun pengguna dan detail profilnya.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'access.user.manage',
                'name'        => 'Mengelola Akun Pengguna Platform',
                'domain'      => 'access',
                'description' => 'Akses membuat, memperbarui, mengaktifkan, dan menonaktifkan akun.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'access.verification.view',
                'name'        => 'Melihat Antrean Verifikasi Profil',
                'domain'      => 'access',
                'description' => 'Akses melihat daftar pengguna dan permohonan perubahan profil yang menunggu review.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'access.verification.approve',
                'name'        => 'Memproses Verifikasi & Perubahan Profil',
                'domain'      => 'access',
                'description' => 'Akses menyetujui atau menolak verifikasi berkas ASN, Penyedia, atau Akademisi.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'access.role.view',
                'name'        => 'Melihat Daftar Peran (Role) & Matriks',
                'domain'      => 'access',
                'description' => 'Akses melihat katalog role dan matriks role-permission.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'access.role.manage',
                'name'        => 'Mengelola Peran (Role) & Hak Akses',
                'domain'      => 'access',
                'description' => 'Akses membuat role baru, mengatur permission, dan memproses permohonan role.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'access.organization.view',
                'name'        => 'Melihat Unit Organisasi PBJ',
                'domain'      => 'access',
                'description' => 'Akses melihat hierarki unit kerja perangkat daerah dan instansi.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'access.organization.manage',
                'name'        => 'Mengelola Unit Organisasi PBJ',
                'domain'      => 'access',
                'description' => 'Akses menambah, mengedit, dan mengarsipkan unit organisasi.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'access.procurementrole.manage',
                'name'        => 'Mengelola Peran & Penugasan Pengadaan',
                'domain'      => 'access',
                'description' => 'Akses mengelola klasifikasi peran fungsional PBJ (PPK, PPTK, Pokja, dsb.).',
                'is_system'   => 1,
            ],
            [
                'key'         => 'user.manage',
                'name'        => 'Kelola Akun Pengguna (Alias)',
                'domain'      => 'access',
                'description' => 'Alias izin administratif untuk manajemen akun pengguna.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'user.verify',
                'name'        => 'Verifikasi Profil Akun (Alias)',
                'domain'      => 'access',
                'description' => 'Alias izin persetujuan verifikasi akun pengguna.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'role.manage',
                'name'        => 'Kelola Konfigurasi Peran (Alias)',
                'domain'      => 'access',
                'description' => 'Alias izin manajerial untuk role dan permission.',
                'is_system'   => 1,
            ],

            // 4. Domain learning (Arena Belajar)
            [
                'key'         => 'learning.view',
                'name'        => 'Melihat Arena Pembelajaran PBJ',
                'domain'      => 'learning',
                'description' => 'Akses melihat katalog program belajar dan kelas pelatihan PBJ.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'learning.program.manage',
                'name'        => 'Mengelola Program Pembelajaran PBJ',
                'domain'      => 'learning',
                'description' => 'Akses membuat dan mengelola taksonomi program kompetensi PBJ.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'learning.course.manage',
                'name'        => 'Mengelola Kursus & Silabus',
                'domain'      => 'learning',
                'description' => 'Akses mengelola materi kursus, kompetensi dasar, dan capaian pembelajaran.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'learning.class.view',
                'name'        => 'Melihat Kelas Pembelajaran PBJ',
                'domain'      => 'learning',
                'description' => 'Akses melihat jadwal kelas dan daftar rombongan belajar peserta.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'learning.class.create',
                'name'        => 'Membuat Kelas Pembelajaran Baru',
                'domain'      => 'learning',
                'description' => 'Akses membuka kelas baru dan menetapkan kuota pendaftaran.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'learning.class.update',
                'name'        => 'Memperbarui Informasi Kelas Pembelajaran',
                'domain'      => 'learning',
                'description' => 'Akses mengubah silabus, jadwal, instruktur, dan status kelas.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'learning.class.delete',
                'name'        => 'Menghapus Kelas Pembelajaran',
                'domain'      => 'learning',
                'description' => 'Akses membatalkan atau menghapus rekaman kelas pelatihan.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'learning.curriculum.manage',
                'name'        => 'Mengelola Kurikulum & Modul Belajar',
                'domain'      => 'learning',
                'description' => 'Akses menyusun modul silabus dan butir lesson bahan ajar.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'learning.assessment.view',
                'name'        => 'Melihat Kuis & Assessment Pembelajaran',
                'domain'      => 'learning',
                'description' => 'Akses meninjau kuis, soal latihan, dan ujian kompetensi.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'learning.assessment.manage',
                'name'        => 'Mengelola Kuis & Bank Soal',
                'domain'      => 'learning',
                'description' => 'Akses menyusun bank butir soal ujian, passing grade, dan batas durasi.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'learning.enrollment.view',
                'name'        => 'Melihat Pendaftaran Peserta Kelas',
                'domain'      => 'learning',
                'description' => 'Akses melihat status pendaftaran peserta di setiap kelas pelatihan.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'learning.enrollment.verify',
                'name'        => 'Memverifikasi Pendaftaran Peserta',
                'domain'      => 'learning',
                'description' => 'Akses menyetujui atau menolak pendaftaran calon peserta kelas.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'learning.grading.manage',
                'name'        => 'Menilai Tugas & Evaluasi Peserta',
                'domain'      => 'learning',
                'description' => 'Akses memeriksa penugasan dan memberikan nilai akhir kelas.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'learning.certificate.manage',
                'name'        => 'Monitoring Kelayakan Sertifikat Pembelajaran',
                'domain'      => 'learning',
                'description' => 'Akses mengevaluasi kelulusan peserta untuk penerbitan sertifikat e-learning.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'learning.report.view',
                'name'        => 'Melihat Laporan Statistik Pembelajaran',
                'domain'      => 'learning',
                'description' => 'Akses memantau progres ketuntasan dan statistik kelas.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'lms.manage',
                'name'        => 'Manajemen Sistem Pembelajaran (LMS Alias)',
                'domain'      => 'learning',
                'description' => 'Izin administratif penuh atas modul Arena Belajar LMS.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'enrollment.verify',
                'name'        => 'Verifikasi Pendaftaran Kelas (Alias)',
                'domain'      => 'learning',
                'description' => 'Alias verifikasi pendaftaran calon peserta diklat.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'assessment.evaluate',
                'name'        => 'Evaluasi Penilaian Peserta (Alias)',
                'domain'      => 'learning',
                'description' => 'Alias evaluasi dan penilaian hasil ujian peserta.',
                'is_system'   => 1,
            ],

            // 5. Domain bunga (Bunga Rampai / KMS)
            [
                'key'         => 'bunga.view',
                'name'        => 'Melihat Direktori Bunga Rampai PBJ',
                'domain'      => 'bunga',
                'description' => 'Akses membaca naskah, panduan, artikel, dan template PBJ.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'bunga.article.manage',
                'name'        => 'Mengelola Naskah & Artikel PBJ',
                'domain'      => 'bunga',
                'description' => 'Akses membuat, menyunting, dan menerbitkan artikel naskah PBJ.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'bunga.faq.manage',
                'name'        => 'Mengelola Tanya Jawab (FAQ) PBJ',
                'domain'      => 'bunga',
                'description' => 'Akses mengelola basis pengetahuan tanya jawab konsultasi PBJ.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'bunga.casestudy.manage',
                'name'        => 'Mengelola Studi Kasus PBJ',
                'domain'      => 'bunga',
                'description' => 'Akses mengelola telaah dan studi kasus penanganan masalah PBJ.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'bunga.template.manage',
                'name'        => 'Mengelola Template Dokumen PBJ',
                'domain'      => 'bunga',
                'description' => 'Akses mengunggah dan memperbarui berkas Standar Dokumen Pemilihan (SDP).',
                'is_system'   => 1,
            ],
            [
                'key'         => 'bunga.media.manage',
                'name'        => 'Mengelola Pustaka Media & Galeri PBJ',
                'domain'      => 'bunga',
                'description' => 'Akses mengunggah video infografis dan dokumentasi multimedia.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'bunga.taxonomy.manage',
                'name'        => 'Mengelola Kategori & Tag Bunga Rampai',
                'domain'      => 'bunga',
                'description' => 'Akses mengelola taksonomi bidang dan penanda topik artikel.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'kms.manage',
                'name'        => 'Manajemen Knowledge Management System (Alias)',
                'domain'      => 'bunga',
                'description' => 'Izin administratif penuh atas modul Knowledge Management System (KMS).',
                'is_system'   => 1,
            ],

            // 6. Domain regulation (Regulasi PBJ)
            [
                'key'         => 'regulation.view',
                'name'        => 'Melihat Direktori Regulasi PBJ',
                'domain'      => 'regulation',
                'description' => 'Akses melihat daftar dan mengunduh berkas regulasi hukum PBJ.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'regulation.create',
                'name'        => 'Menambah Dokumen Regulasi Baru',
                'domain'      => 'regulation',
                'description' => 'Akses mengunggah peraturan perundang-undangan baru ke repositori.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'regulation.edit',
                'name'        => 'Mengubah Dokumen Regulasi PBJ',
                'domain'      => 'regulation',
                'description' => 'Akses memperbarui metadata regulasi, konsideran, dan relasi aturan.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'regulation.delete',
                'name'        => 'Menghapus Dokumen Regulasi PBJ',
                'domain'      => 'regulation',
                'description' => 'Akses menghapus atau mencabut dokumen regulasi dari katalog.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'regulation.approve',
                'name'        => 'Menyetujui & Mempublikasikan Regulasi',
                'domain'      => 'regulation',
                'description' => 'Akses otorisasi rilis dokumen regulasi ke portal publik.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'regulation.category.manage',
                'name'        => 'Mengelola Kategori Regulasi PBJ',
                'domain'      => 'regulation',
                'description' => 'Akses mengatur klasifikasi regulasi (Perpres, Perlem LKPP, Pergub, dsb.).',
                'is_system'   => 1,
            ],
            [
                'key'         => 'regulation.stage.manage',
                'name'        => 'Mengelola Tahapan PBJ & Kompetensi',
                'domain'      => 'regulation',
                'description' => 'Akses memetakan regulasi ke tahapan siklus PBJ dan kompetensi JF PPBJ.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'regulation.manage',
                'name'        => 'Manajemen Penuh Regulasi PBJ (Alias)',
                'domain'      => 'regulation',
                'description' => 'Izin administratif penuh atas modul repositori regulasi PBJ.',
                'is_system'   => 1,
            ],

            // 7. Domain communication (Komunikasi & Event)
            [
                'key'         => 'communication.view',
                'name'        => 'Melihat Modul Komunikasi & Event',
                'domain'      => 'communication',
                'description' => 'Akses membuka modul berita, event, dan layanan konsultasi.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'communication.news.manage',
                'name'        => 'Mengelola Warta Berita PBJ',
                'domain'      => 'communication',
                'description' => 'Akses menulis, menyunting, dan menerbitkan rilis berita PBJ.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'communication.event.view',
                'name'        => 'Melihat Agenda Event & Bimtek',
                'domain'      => 'communication',
                'description' => 'Akses melihat kalender kegiatan sosialisasi dan bimbingan teknis.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'communication.event.manage',
                'name'        => 'Mengelola Agenda Event & Bimtek',
                'domain'      => 'communication',
                'description' => 'Akses membuat event, menetapkan kuota, dan mengatur sesi kegiatan.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'communication.attendance.manage',
                'name'        => 'Mengelola Presensi & Kehadiran Event',
                'domain'      => 'communication',
                'description' => 'Akses memvalidasi kehadiran peserta dan menutup sesi presensi.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'communication.inbox.manage',
                'name'        => 'Mengelola Pesan Masuk & Helpdesk',
                'domain'      => 'communication',
                'description' => 'Akses membaca tiket permohonan konsultasi dan pesan masuk publik.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'communication.helpdesk.manage',
                'name'        => 'Mengelola Pengaturan Helpdesk Widget',
                'domain'      => 'communication',
                'description' => 'Akses mengatur nomor kontak dan jam operasional WhatsApp Helpdesk.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'communication.newsletter.manage',
                'name'        => 'Mengelola Berlangganan Newsletter',
                'domain'      => 'communication',
                'description' => 'Akses mengekspor daftar email pelanggan buletin berkala.',
                'is_system'   => 1,
            ],

            // 8. Domain certificate (Sertifikat)
            [
                'key'         => 'certificate.view',
                'name'        => 'Melihat Master & Riwayat Sertifikat',
                'domain'      => 'certificate',
                'description' => 'Akses melihat rekap sertifikat pelatihan dan event yang telah terbit.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'certificate.manage',
                'name'        => 'Mengelola Penerbitan Sertifikat',
                'domain'      => 'certificate',
                'description' => 'Akses menerbitkan, memperbarui, atau mencabut sertifikat peserta.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'certificate.batch.manage',
                'name'        => 'Mengelola Penerbitan Batch Sertifikat',
                'domain'      => 'certificate',
                'description' => 'Akses memproses penomoran otomatis dan generate e-sertifikat massal.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'certificate.template.manage',
                'name'        => 'Mengelola Desain Template Sertifikat',
                'domain'      => 'certificate',
                'description' => 'Akses mengatur tata letak, tanda tangan digital, dan latar sertifikat.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'certificate.approve',
                'name'        => 'Menyetujui Penerbitan Sertifikat',
                'domain'      => 'certificate',
                'description' => 'Akses otorisasi penandatanganan dan pengesahan sertifikat.',
                'is_system'   => 1,
            ],

            // 9. Domain intelligence (Government Intelligence)
            [
                'key'         => 'intelligence.view',
                'name'        => 'Melihat Dashboard Intelligence SDM PBJ',
                'domain'      => 'intelligence',
                'description' => 'Akses memantau sebaran SDM JF PPBJ dan analitik kesenjangan kompetensi.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'intelligence.jfppbj.manage',
                'name'        => 'Mengelola Data & Riwayat SDM JF PPBJ',
                'domain'      => 'intelligence',
                'description' => 'Akses memperbarui jenjang jabatan dan penugasan pejabat fungsional PBJ.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'intelligence.competency.manage',
                'name'        => 'Mengelola Data Sertifikat Kompetensi',
                'domain'      => 'intelligence',
                'description' => 'Akses mencatat jenis dan masa berlaku sertifikasi pengadaan aparatur.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'intelligence.opd.manage',
                'name'        => 'Mengelola Analisis Pemetaan Unit Kerja/OPD',
                'domain'      => 'intelligence',
                'description' => 'Akses analisis kebutuhan SDM pengadaan di setiap instansi perangkat daerah.',
                'is_system'   => 1,
            ],

            // 10. Domain reports (Pusat Laporan)
            [
                'key'         => 'reports.view',
                'name'        => 'Melihat Pusat Laporan Dashboard',
                'domain'      => 'reports',
                'description' => 'Akses membuka konsol laporan terpadu seluruh aktivitas platform.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'reports.learning.view',
                'name'        => 'Melihat Laporan Statistik Pembelajaran',
                'domain'      => 'reports',
                'description' => 'Akses meninjau laporan capaian nilai dan ketuntasan kelas diklat.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'reports.event.view',
                'name'        => 'Melihat Laporan Keikutsertaan Event',
                'domain'      => 'reports',
                'description' => 'Akses meninjau laporan partisipasi dan kehadiran kegiatan sosialisasi.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'reports.certificate.view',
                'name'        => 'Melihat Laporan Penerbitan Sertifikat',
                'domain'      => 'reports',
                'description' => 'Akses meninjau rekapitulasi penerbitan sertifikat berkala.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'reports.competency.view',
                'name'        => 'Melihat Laporan Kesenjangan Kompetensi',
                'domain'      => 'reports',
                'description' => 'Akses meninjau laporan analisis gap kompetensi SDM pengadaan.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'reports.user.view',
                'name'        => 'Melihat Laporan Aktivitas Pengguna',
                'domain'      => 'reports',
                'description' => 'Akses meninjau rekap pertumbuhan pengguna dan keaktifan akun.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'reports.export',
                'name'        => 'Mengekspor Berkas Laporan & Riwayat Ekspor',
                'domain'      => 'reports',
                'description' => 'Akses mengunduh laporan format Excel/CSV dan melihat riwayat ekspor.',
                'is_system'   => 1,
            ],

            // 11. Domain survey (Survei Kepuasan IKM)
            [
                'key'         => 'survey.view',
                'name'        => 'Melihat Hasil & Statistik Survei Kepuasan',
                'domain'      => 'survey',
                'description' => 'Akses memantau skor Indeks Kepuasan Masyarakat (IKM) dan umpan balik responden.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'survey.manage',
                'name'        => 'Mengelola Pertanyaan & Konfigurasi Survei',
                'domain'      => 'survey',
                'description' => 'Akses mengonfigurasi indikator survei kepuasan layanan PBJ.',
                'is_system'   => 1,
            ],

            // 12. Domain system (Sistem & Keamanan)
            [
                'key'         => 'system.settings',
                'name'        => 'Mengelola Pengaturan Sistem & Platform',
                'domain'      => 'system',
                'description' => 'Akses konfigurasi umum, identitas instansi, integrasi, dan keamanan sistem.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'approval.view',
                'name'        => 'Melihat Antrean Approval Sistem',
                'domain'      => 'system',
                'description' => 'Akses melihat daftar antrean persetujuan perubahan profil dan pendaftaran.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'approvals.review',
                'name'        => 'Meninjau Antrean Approval Sistem',
                'domain'      => 'system',
                'description' => 'Akses memeriksa kelengkapan dokumen pendukung sebelum disetujui.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'approval.decide',
                'name'        => 'Memutuskan (Setujui/Tolak) Approval',
                'domain'      => 'system',
                'description' => 'Akses eksekusi persetujuan atau penolakan pengajuan pada Approval Hub.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'audit.view',
                'name'        => 'Melihat Jejak Audit Log Aktivitas',
                'domain'      => 'system',
                'description' => 'Akses menelusuri rekam jejak aktivitas seluruh pengguna di sistem.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'audit.export',
                'name'        => 'Mengekspor Jejak Audit Log',
                'domain'      => 'system',
                'description' => 'Akses mengekspor berkas CSV jejak audit sistem untuk keperluan forensik.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'notification.view',
                'name'        => 'Melihat Pusat Notifikasi Dashboard',
                'domain'      => 'system',
                'description' => 'Akses membuka konsol notifikasi sistem dan peringatan dini.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'notification.send',
                'name'        => 'Mengirim Notifikasi Broadcast Sistem',
                'domain'      => 'system',
                'description' => 'Akses menyebarkan notifikasi pengumuman ke seluruh atau sebagian pengguna.',
                'is_system'   => 1,
            ],
            [
                'key'         => 'system.maintenance',
                'name'        => 'Mengelola Mode Pemeliharaan Platform',
                'domain'      => 'system',
                'description' => 'Akses mengaktifkan mode maintenance platform dan flush cache sistem.',
                'is_system'   => 1,
            ],
        ];
    }

    public function run(): void
    {
        $db = \Config\Database::connect();
        $catalog = $this->getCatalog();
        $now = date('Y-m-d H:i:s');

        // 1. Upsert catalog permissions
        foreach ($catalog as $item) {
            $existing = $db->table('permissions')->where('key', $item['key'])->get()->getRowArray();
            if ($existing) {
                $db->table('permissions')->where('id', $existing['id'])->update([
                    'name'        => $item['name'],
                    'domain'      => $item['domain'],
                    'description' => $item['description'],
                    'is_system'   => 1,
                ]);
            } else {
                $db->table('permissions')->insert([
                    'key'         => $item['key'],
                    'name'        => $item['name'],
                    'domain'      => $item['domain'],
                    'description' => $item['description'],
                    'is_system'   => 1,
                ]);
            }
        }

        // 2. Fetch all current permissions keyed by `key`
        $allPerms = $db->table('permissions')->get()->getResultArray();
        $permMap = [];
        foreach ($allPerms as $p) {
            $permMap[$p['key']] = (int) $p['id'];
        }

        // 3. Auto-grant to canonical roles
        // Role ID 10: super_administrator -> Full access
        // Role ID 11: administrator -> Full access
        $superAdminRole = $db->table('roles')->where('key', 'super_administrator')->get()->getRowArray();
        $adminRole      = $db->table('roles')->where('key', 'administrator')->get()->getRowArray();
        $operatorRole   = $db->table('roles')->where('key', 'operator')->get()->getRowArray();
        $trainerRole    = $db->table('roles')->where('key', 'trainer')->get()->getRowArray();
        $evaluatorRole  = $db->table('roles')->where('key', 'evaluator')->get()->getRowArray();

        $adminUser = $db->table('users')->orderBy('id', 'ASC')->get()->getRowArray();
        $grantedBy = ! empty($adminUser['id']) ? (int) $adminUser['id'] : null;

        $grantPermissions = static function(int $roleId, array $permissionIds) use ($db, $now, $grantedBy): void {
            if ($roleId <= 0 || empty($permissionIds)) {
                return;
            }
            $existingGrants = $db->table('role_permissions')
                ->where('role_id', $roleId)
                ->get()
                ->getResultArray();
            $existingIds = array_map('intval', array_column($existingGrants, 'permission_id'));

            $toInsert = [];
            foreach ($permissionIds as $pId) {
                if (! in_array($pId, $existingIds, true)) {
                    $toInsert[] = [
                        'role_id'       => $roleId,
                        'permission_id' => $pId,
                        'granted_by'    => $grantedBy,
                        'created_at'    => $now,
                    ];
                }
            }

            if (! empty($toInsert)) {
                $db->table('role_permissions')->insertBatch($toInsert);
            }
        };

        $allPermIds = array_values($permMap);

        // Grant to super_administrator & administrator
        if ($superAdminRole) {
            $grantPermissions((int) $superAdminRole['id'], $allPermIds);
        }
        if ($adminRole) {
            $grantPermissions((int) $adminRole['id'], $allPermIds);
        }

        // Grant to operator
        if ($operatorRole) {
            $opKeys = [
                'dashboard.view', 'dashboard.indicators.view',
                'access.manage', 'access.user.view', 'access.verification.view', 'access.verification.approve',
                'user.verify', 'approvals.review', 'approval.decide', 'approval.view',
                'learning.view', 'learning.class.view', 'learning.enrollment.view', 'learning.enrollment.verify', 'enrollment.verify',
                'communication.view', 'communication.event.view', 'communication.event.manage', 'communication.attendance.manage', 'communication.inbox.manage',
                'certificate.view', 'certificate.manage', 'certificate.batch.manage',
                'audit.view', 'notification.view', 'notification.send', 'survey.view',
            ];
            $opIds = [];
            foreach ($opKeys as $k) {
                if (isset($permMap[$k])) {
                    $opIds[] = $permMap[$k];
                }
            }
            $grantPermissions((int) $operatorRole['id'], $opIds);
        }

        // Grant to trainer
        if ($trainerRole) {
            $trKeys = [
                'dashboard.view', 'learning.view', 'learning.class.view', 'learning.curriculum.manage',
                'learning.assessment.view', 'learning.assessment.manage', 'learning.grading.manage',
                'bunga.view',
            ];
            $trIds = [];
            foreach ($trKeys as $k) {
                if (isset($permMap[$k])) {
                    $trIds[] = $permMap[$k];
                }
            }
            $grantPermissions((int) $trainerRole['id'], $trIds);
        }

        // Grant to evaluator
        if ($evaluatorRole) {
            $evKeys = [
                'dashboard.view', 'learning.view', 'learning.class.view', 'learning.assessment.view',
                'learning.grading.manage', 'assessment.evaluate', 'learning.report.view',
                'survey.view', 'reports.view', 'reports.learning.view',
            ];
            $evIds = [];
            foreach ($evKeys as $k) {
                if (isset($permMap[$k])) {
                    $evIds[] = $permMap[$k];
                }
            }
            $grantPermissions((int) $evaluatorRole['id'], $evIds);
        }
    }
}
