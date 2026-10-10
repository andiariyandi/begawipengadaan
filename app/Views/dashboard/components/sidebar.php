<?php
$currentMenu = $activeMenu ?? '';
$currentSubmenu = $activeSubmenu ?? '';
$activeClass = static fn(string $menu, ?string $submenu = null): string =>
    $currentMenu === $menu && ($submenu === null || $currentSubmenu === $submenu) ? 'active-page' : '';
$openClass = static fn(string $menu): string => $currentMenu === $menu ? 'open' : '';

$auth = new \App\Services\Identity\AuthorizationService();
$isSuperAdmin = $auth->isSuperAdmin();
?>

<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>

    <div>
        <a href="<?= site_url('dashboard') ?>" class="sidebar-logo">
            <img src="<?= base_url('assets/dashboard/images/logo.png') ?>" alt="BegawiPBJ" class="light-logo">
            <img src="<?= base_url('assets/dashboard/images/logo-light.png') ?>" alt="BegawiPBJ" class="dark-logo">
            <img src="<?= base_url('assets/dashboard/images/logo-icon.png') ?>" alt="BegawiPBJ" class="logo-icon">
        </a>
    </div>

    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            <li class="sidebar-menu-group-title">UTAMA</li>

            <li class="dropdown <?= $openClass('summary') ?>">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a class="<?= $activeClass('summary', 'dashboard') ?>" href="<?= site_url('dashboard') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Ringkasan</a></li>
                    <li><a class="<?= $activeClass('summary', 'kpi') ?>" href="<?= site_url('dashboard/kpi-internal') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> KPI Internal</a></li>
                    <li><a class="<?= $activeClass('summary', 'approval-queue') ?>" href="<?= site_url('dashboard/antrean-persetujuan') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Antrean Persetujuan</a></li>
                    <li><a class="<?= $activeClass('summary', 'recent-activity') ?>" href="<?= site_url('dashboard/aktivitas-terbaru') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Aktivitas Terbaru</a></li>
                    <li><a class="<?= $activeClass('summary', 'system-indicators') ?>" href="<?= site_url('dashboard/indikator-sistem') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Indikator Sistem</a></li>
                </ul>
            </li>

            <li class="sidebar-menu-group-title">MANAJEMEN PLATFORM</li>

            <li class="dropdown <?= $openClass('access') ?>">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:users-group-rounded-outline" class="menu-icon"></iconify-icon>
                    <span>Manajemen Akses</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a class="<?= $activeClass('access', 'users') ?>" href="<?= site_url('dashboard/akses/pengguna') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Pengguna</a></li>
                    <li><a class="<?= $activeClass('access', 'profile-verifications') ?>" href="<?= site_url('dashboard/akses/verifikasi-profil') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Verifikasi Profil</a></li>
                    <li><a class="<?= $activeClass('access', 'profile-change-requests') ?>" href="<?= site_url('dashboard/akses/permohonan-perubahan-profil') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Perubahan Profil</a></li>
                    <?php if ($isSuperAdmin): ?>
                        <li><a class="<?= $activeClass('access', 'roles') ?>" href="<?= site_url('dashboard/akses/role') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Role dan Permission</a></li>
                        <li><a class="<?= $activeClass('access', 'role-requests') ?>" href="<?= site_url('dashboard/akses/permohonan-role') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Permohonan Role</a></li>
                    <?php endif; ?>
                    <li><a class="<?= $activeClass('access', 'organization-units') ?>" href="<?= site_url('dashboard/akses/unit-organisasi') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Unit Organisasi</a></li>
                    <li><a class="<?= $activeClass('access', 'procurement-roles') ?>" href="<?= site_url('dashboard/akses/peran-pengadaan') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Peran Pengadaan</a></li>
                </ul>
            </li>

            <li class="dropdown <?= $openClass('learning') ?>">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:book-2-outline" class="menu-icon"></iconify-icon>
                    <span>Arena Belajar</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a class="<?= $activeClass('learning', 'programs') ?>" href="<?= site_url('dashboard/pembelajaran/program') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Program</a></li>
                    <li><a class="<?= $activeClass('learning', 'courses') ?>" href="<?= site_url('dashboard/pembelajaran/course') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Course</a></li>
                    <li><a class="<?= $activeClass('learning', 'classes') ?>" href="<?= site_url('dashboard/pembelajaran/kelas') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Kelas</a></li>
                    <li><a class="<?= $activeClass('learning', 'modules') ?>" href="<?= site_url('dashboard/pembelajaran/materi-modul') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Materi dan Modul</a></li>
                    <li><a class="<?= $activeClass('learning', 'assessments') ?>" href="<?= site_url('dashboard/pembelajaran/assessment') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Assessment dan Bank Soal</a></li>
                    <li><a class="<?= $activeClass('learning', 'enrollments') ?>" href="<?= site_url('dashboard/pembelajaran/enrollment') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Enrollment</a></li>
                    <li><a class="<?= $activeClass('learning', 'gradings') ?>" href="<?= site_url('dashboard/pembelajaran/penilaian') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Penilaian</a></li>
                    <li><a class="<?= $activeClass('learning', 'progress-completion') ?>" href="<?= site_url('dashboard/pembelajaran/progres-kelulusan') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Progres dan Kelulusan</a></li>
                    <li><a class="<?= $activeClass('learning', 'participant-feedback') ?>" href="<?= site_url('dashboard/pembelajaran/feedback-peserta') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Feedback Peserta</a></li>
                </ul>
            </li>

            <li class="dropdown <?= $openClass('knowledge') ?>">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:document-text-outline" class="menu-icon"></iconify-icon>
                    <span>Bunga Rampai</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a class="<?= $activeClass('knowledge', 'articles') ?>" href="<?= site_url('dashboard/bunga-rampai/artikel') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Artikel</a></li>
                    <li><a class="<?= $activeClass('knowledge', 'faqs') ?>" href="<?= site_url('dashboard/bunga-rampai/faq') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> FAQ</a></li>
                    <li><a class="<?= $activeClass('knowledge', 'case-studies') ?>" href="<?= site_url('dashboard/bunga-rampai/studi-kasus') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Studi Kasus</a></li>
                    <li><a class="<?= $activeClass('knowledge', 'document-templates') ?>" href="<?= site_url('dashboard/bunga-rampai/template-dokumen') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Template Dokumen</a></li>
                    <li><a class="<?= $activeClass('knowledge', 'media-gallery') ?>" href="<?= site_url('dashboard/bunga-rampai/media-galeri') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Media dan Galeri</a></li>
                    <li><a class="<?= $activeClass('knowledge', 'carousel') ?>" href="<?= site_url('dashboard/bunga-rampai/carousel-konten-unggulan') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Carousel dan Konten Unggulan</a></li>
                    <li><a class="<?= $activeClass('knowledge', 'categories') ?>" href="<?= site_url('dashboard/bunga-rampai/kategori') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Kategori</a></li>
                    <li><a class="<?= $activeClass('knowledge', 'tags') ?>" href="<?= site_url('dashboard/bunga-rampai/tag') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Tag</a></li>
                </ul>
            </li>

            <li class="dropdown <?= $openClass('regulations') ?>">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:clipboard-text-outline" class="menu-icon"></iconify-icon>
                    <span>Regulasi PBJ</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a class="<?= $activeClass('regulations', 'index') ?>" href="<?= site_url('dashboard/regulasi/dokumen') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Regulasi</a></li>
                    <li><a class="<?= $activeClass('regulations', 'categories') ?>" href="<?= site_url('dashboard/regulasi/kategori') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Kategori Regulasi</a></li>
                    <li><a class="<?= $activeClass('regulations', 'versions') ?>" href="<?= site_url('dashboard/regulasi/versi') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Versi Dokumen</a></li>
                    <li><a class="<?= $activeClass('regulations', 'relations') ?>" href="<?= site_url('dashboard/regulasi/relasi') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Relasi Regulasi</a></li>
                    <li><a class="<?= $activeClass('regulations', 'procurement-stages') ?>" href="<?= site_url('dashboard/regulasi/tahapan-pbj') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Tahapan PBJ</a></li>
                    <li><a class="<?= $activeClass('regulations', 'competencies') ?>" href="<?= site_url('dashboard/regulasi/kompetensi-terkait') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Kompetensi Terkait</a></li>
                    <li><a class="<?= $activeClass('regulations', 'download-logs') ?>" href="<?= site_url('dashboard/regulasi/log-download') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Log Download</a></li>
                </ul>
            </li>

            <li class="dropdown <?= $openClass('communication') ?>">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:chat-round-dots-outline" class="menu-icon"></iconify-icon>
                    <span>Komunikasi</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a class="<?= $activeClass('communication', 'news') ?>" href="<?= site_url('dashboard/komunikasi/berita') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Berita</a></li>
                    <li><a class="<?= $activeClass('communication', 'news-categories') ?>" href="<?= site_url('dashboard/komunikasi/kategori-berita') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Kategori Berita</a></li>
                    <li><a class="<?= $activeClass('communication', 'events') ?>" href="<?= site_url('dashboard/komunikasi/event-agenda') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Event dan Agenda</a></li>
                    <li><a class="<?= $activeClass('communication', 'event-registrations') ?>" href="<?= site_url('dashboard/komunikasi/registrasi-event') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Registrasi Event</a></li>
                    <li><a class="<?= $activeClass('communication', 'attendance') ?>" href="<?= site_url('dashboard/komunikasi/kehadiran') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Kehadiran</a></li>
                    <li><a class="<?= $activeClass('communication', 'waitlist-cancellations') ?>" href="<?= site_url('dashboard/komunikasi/waitlist-pembatalan') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Waitlist dan Pembatalan</a></li>
                </ul>
            </li>

            <li class="dropdown <?= $openClass('certificates') ?>">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:medal-ribbons-star-outline" class="menu-icon"></iconify-icon>
                    <span>Sertifikat</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a class="<?= $activeClass('certificates', 'approvals') ?>" href="<?= site_url('dashboard/sertifikat/approval') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Approval / Pengajuan</a></li>
                    <li><a class="<?= $activeClass('certificates', 'index') ?>" href="<?= site_url('dashboard/sertifikat') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Sertifikat Terbit</a></li>
                    <li><a class="<?= $activeClass('certificates', 'event-approval') ?>" href="<?= site_url('dashboard/sertifikat/pengajuan-event') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Pengajuan Event</a></li>
                    <li><a class="<?= $activeClass('certificates', 'templates') ?>" href="<?= site_url('dashboard/sertifikat/template') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Template</a></li>
                    <li><a class="<?= $activeClass('certificates', 'verification') ?>" href="<?= site_url('verifikasi-sertifikat') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Verifikasi Publik</a></li>
                </ul>
            </li>

            <li class="sidebar-menu-group-title">ANALISIS DAN LAPORAN</li>

            <li class="dropdown <?= $openClass('intelligence') ?>">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:chart-2-outline" class="menu-icon"></iconify-icon>
                    <span>Government Intelligence</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a class="<?= $activeClass('intelligence', 'dashboard') ?>" href="<?= site_url('dashboard/intelligence') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Dashboard Intelligence</a></li>
                    <li><a class="<?= $activeClass('intelligence', 'organization-units') ?>" href="<?= site_url('dashboard/intelligence/opd-unit') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> OPD/Unit</a></li>
                    <li><a class="<?= $activeClass('intelligence', 'personnel') ?>" href="<?= site_url('dashboard/intelligence/sdm-jf-ppbj') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> SDM JF PPBJ</a></li>
                    <li><a class="<?= $activeClass('intelligence', 'personnel-histories') ?>" href="<?= site_url('dashboard/intelligence/riwayat-jf-ppbj') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Riwayat JF PPBJ</a></li>
                    <li><a class="<?= $activeClass('intelligence', 'personnel-levels') ?>" href="<?= site_url('dashboard/intelligence/jenjang-jf-ppbj') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Jenjang JF PPBJ</a></li>
                    <li><a class="<?= $activeClass('intelligence', 'competency-certificates') ?>" href="<?= site_url('dashboard/intelligence/sertifikat-kompetensi') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Sertifikat Kompetensi</a></li>
                    <li><a class="<?= $activeClass('intelligence', 'competency-certificate-types') ?>" href="<?= site_url('dashboard/intelligence/jenis-sertifikat-kompetensi') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Jenis Sertifikat Kompetensi</a></li>
                    <li><a class="<?= $activeClass('intelligence', 'competency-gap') ?>" href="<?= site_url('dashboard/intelligence/gap-kompetensi') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Gap Kompetensi</a></li>
                </ul>
            </li>

            <li class="dropdown <?= $openClass('reports') ?>">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:chart-square-outline" class="menu-icon"></iconify-icon>
                    <span>Laporan</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a class="<?= $activeClass('reports', 'learning') ?>" href="<?= site_url('dashboard/laporan/pembelajaran') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Laporan Pembelajaran</a></li>
                    <li><a class="<?= $activeClass('reports', 'events') ?>" href="<?= site_url('dashboard/laporan/event') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Laporan Event</a></li>
                    <li><a class="<?= $activeClass('reports', 'certificates') ?>" href="<?= site_url('dashboard/laporan/sertifikat') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Laporan Sertifikat</a></li>
                    <li><a class="<?= $activeClass('reports', 'competency') ?>" href="<?= site_url('dashboard/laporan/kompetensi') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Laporan Kompetensi</a></li>
                    <li><a class="<?= $activeClass('reports', 'users') ?>" href="<?= site_url('dashboard/laporan/pengguna') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Laporan Pengguna</a></li>
                    <li><a class="<?= $activeClass('reports', 'export-history') ?>" href="<?= site_url('dashboard/laporan/riwayat-export') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Riwayat Export</a></li>
                </ul>
            </li>

            <li class="sidebar-menu-group-title">SISTEM</li>

            <li class="dropdown <?= $openClass('system') ?>">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:settings-outline" class="menu-icon"></iconify-icon>
                    <span>Sistem</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a class="<?= $activeClass('system', 'approvals') ?>" href="<?= site_url('dashboard/sistem/approval') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Approval</a></li>
                    <li><a class="<?= $activeClass('system', 'audit-trail') ?>" href="<?= site_url('dashboard/sistem/audit-trail') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Audit Trail</a></li>
                    <li><a class="<?= $activeClass('system', 'files-media') ?>" href="<?= site_url('dashboard/sistem/file-media') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> File dan Media</a></li>
                    <li><a class="<?= $activeClass('system', 'notifications') ?>" href="<?= site_url('dashboard/sistem/notifikasi') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Notifikasi</a></li>
                    <?php if ($isSuperAdmin): ?>
                        <li><a class="<?= $activeClass('system', 'general') ?>" href="<?= site_url('dashboard/sistem/pengaturan-umum') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Pengaturan Umum</a></li>
                        <li><a class="<?= $activeClass('system', 'security') ?>" href="<?= site_url('dashboard/sistem/pengaturan-keamanan') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Pengaturan Keamanan</a></li>
                        <li><a class="<?= $activeClass('system', 'file-storage') ?>" href="<?= site_url('dashboard/sistem/pengaturan-file-storage') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Pengaturan File Storage</a></li>
                        <li><a class="<?= $activeClass('system', 'certificate') ?>" href="<?= site_url('dashboard/sistem/pengaturan-sertifikat') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Pengaturan Sertifikat</a></li>
                        <li><a class="<?= $activeClass('system', 'appearance') ?>" href="<?= site_url('dashboard/sistem/pengaturan-tampilan') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Pengaturan Tampilan</a></li>
                        <li><a class="<?= $activeClass('system', 'maintenance') ?>" href="<?= site_url('dashboard/sistem/maintenance') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Maintenance</a></li>
                    <?php endif; ?>
                </ul>
            </li>
        </ul>
    </div>
</aside>