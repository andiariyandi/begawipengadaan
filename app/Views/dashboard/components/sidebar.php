<?php
$currentMenu = $activeMenu ?? '';
$currentSubmenu = $activeSubmenu ?? '';

$isSubmenuActive = static function(string $menu, array|string $submenus) use ($currentMenu, $currentSubmenu): bool {
    if ($currentMenu !== $menu) {
        return false;
    }
    if (is_array($submenus)) {
        return in_array($currentSubmenu, $submenus, true);
    }
    return $currentSubmenu === $submenus;
};

$activeClass = static fn(string $menu, array|string|null $submenus = null): string =>
    ($submenus === null ? $currentMenu === $menu : $isSubmenuActive($menu, $submenus)) ? 'active-page' : '';

$openClass = static fn(string $menu): string => $currentMenu === $menu ? 'open' : '';

$auth = new \App\Services\Identity\AuthorizationService();
$isSuperAdmin = $auth->isSuperAdmin();

$sbUserId = (int) (session()->get('user_id') ?: 0);
$sbNotifService = new \App\Services\NotificationService();
$adminUnreadNotifCount = $sbUserId > 0
    ? $sbNotifService->unreadCount($sbUserId)
    : $sbNotifService->unreadCountAll();
?>

<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>

    <div>
        <a href="<?= site_url('dashboard') ?>" class="sidebar-logo">
            <img src="<?= base_url('assets/dashboard/images/logo.png') ?>" alt="BegawiPengadaan" class="light-logo">
            <img src="<?= base_url('assets/dashboard/images/logo-light.png') ?>" alt="BegawiPengadaan" class="dark-logo">
            <img src="<?= base_url('assets/dashboard/images/logo-icon.png') ?>" alt="BegawiPengadaan" class="logo-icon">
        </a>
    </div>

    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            <!-- ============================================== -->
            <!-- I. KATEGORI UTAMA                              -->
            <!-- ============================================== -->
            <li class="sidebar-menu-group-title">UTAMA</li>

            <li class="dropdown <?= $openClass('summary') ?>">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a class="<?= $activeClass('summary', ['dashboard', 'summary', '']) ?>" href="<?= site_url('dashboard') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Ringkasan
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('summary', ['system-indicators', 'indicators', 'kpi', 'kpi-internal', 'recent-activity', 'aktivitas-terbaru']) ?>" href="<?= site_url('dashboard/indikator-sistem') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Indikator Sistem
                        </a>
                    </li>
                </ul>
            </li>

            <!-- ============================================== -->
            <!-- II. KATEGORI MANAJEMEN PLATFORM                 -->
            <!-- ============================================== -->
            <li class="sidebar-menu-group-title">MANAJEMEN PLATFORM</li>

            <!-- 2. Kelola Landing Page -->
            <li class="dropdown <?= $openClass('landing') ?>">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:monitor-smartphone-outline" class="menu-icon"></iconify-icon>
                    <span>Kelola Landing Page</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a class="<?= $activeClass('landing', ['slider', 'spotlight-center', 'pengumuman', 'banner-halaman', 'carousel', 'sorotan-carousel']) ?>" href="<?= site_url('dashboard/landing/slider') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Hero &amp; Promosi
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('landing', ['profil-lembaga', 'profile', 'pilar-hierarki', 'highlight']) ?>" href="<?= site_url('dashboard/landing/profil-lembaga') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Profil Lembaga &amp; PATIK
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('landing', ['portal-eksternal', 'portal', 'kategori-layanan', 'category']) ?>" href="<?= site_url('dashboard/landing/portal-eksternal') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Portal Eksternal &amp; Layanan
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('landing', ['tim', 'team', 'testimoni', 'testimonial']) ?>" href="<?= site_url('dashboard/landing/tim') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Tim Inovator &amp; Testimoni
                        </a>
                    </li>
                </ul>
            </li>

            <!-- 3. Manajemen Akses -->
            <li class="dropdown <?= $openClass('access') ?>">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:users-group-rounded-outline" class="menu-icon"></iconify-icon>
                    <span>Manajemen Akses</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a class="<?= $activeClass('access', ['users', 'pengguna']) ?>" href="<?= site_url('dashboard/akses/pengguna') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Pengguna &amp; Profil
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('access', ['profile-verifications', 'profile-change-requests', 'verifikasi-profil', 'permohonan-perubahan-profil']) ?>" href="<?= site_url('dashboard/akses/verifikasi-profil') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Verifikasi Profil
                        </a>
                    </li>
                    <?php if ($isSuperAdmin): ?>
                        <li>
                            <a class="<?= $activeClass('access', ['roles', 'role-requests', 'peran', 'permission', 'permohonan-role']) ?>" href="<?= site_url('dashboard/akses/role') ?>">
                                <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Role &amp; Hak Akses
                            </a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a class="<?= $activeClass('access', ['organization-units', 'procurement-roles', 'unit-organisasi', 'peran-pengadaan']) ?>" href="<?= site_url('dashboard/akses/unit-organisasi') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Unit Organisasi &amp; Peran
                        </a>
                    </li>
                </ul>
            </li>

            <!-- 4. Arena Belajar -->
            <li class="dropdown <?= $openClass('learning') ?>">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:book-2-outline" class="menu-icon"></iconify-icon>
                    <span>Arena Belajar</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a class="<?= $activeClass('learning', ['overview', 'programs', 'courses', 'curriculum', 'program', 'kursus', 'kurikulum']) ?>" href="<?= site_url('dashboard/pembelajaran') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Overview &amp; Kursus
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('learning', ['classes', 'trainers', 'enrollments', 'kelas', 'participant-feedback', 'feedback']) ?>" href="<?= site_url('dashboard/pembelajaran/kelas') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Kelas &amp; Peserta
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('learning', ['assessments', 'assessment', 'quiz', 'question-bank']) ?>" href="<?= site_url('dashboard/pembelajaran/assessment') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Evaluasi &amp; Assessment
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('learning', ['certificate-eligibility', 'progress', 'reports', 'kelayakan-sertifikat', 'progres', 'laporan']) ?>" href="<?= site_url('dashboard/pembelajaran/kelayakan-sertifikat') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Monitoring Kelulusan
                        </a>
                    </li>
                </ul>
            </li>

            <!-- 5. Bunga Rampai -->
            <li class="dropdown <?= $openClass('knowledge') ?>">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:document-text-outline" class="menu-icon"></iconify-icon>
                    <span>Bunga Rampai</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a class="<?= $activeClass('knowledge', ['articles', 'overview', 'faqs', 'case-studies', 'document-templates', 'carousel']) ?>" href="<?= site_url('dashboard/bunga-rampai/artikel') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Pusat Naskah PBJ
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('knowledge', ['media', 'videos', 'gallery', 'media-gallery']) ?>" href="<?= site_url('dashboard/bunga-rampai/media-galeri') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Pustaka Media Terpadu
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('knowledge', ['categories', 'tags', 'bookmarks']) ?>" href="<?= site_url('dashboard/bunga-rampai/kategori') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Taksonomi &amp; Bookmark
                        </a>
                    </li>
                </ul>
            </li>

            <!-- 6. Regulasi PBJ -->
            <li class="dropdown <?= $openClass('regulations') ?>">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:clipboard-text-outline" class="menu-icon"></iconify-icon>
                    <span>Regulasi PBJ</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a class="<?= $activeClass('regulations', ['regulations', 'index', 'dokumen', 'versions', 'relations', 'versi', 'relasi']) ?>" href="<?= site_url('dashboard/regulasi/dokumen') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Direktori Regulasi
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('regulations', ['categories', 'kategori']) ?>" href="<?= site_url('dashboard/regulasi/kategori') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Kategori Regulasi
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('regulations', ['procurement-stages', 'competencies', 'download-logs']) ?>" href="<?= site_url('dashboard/regulasi/tahapan-pbj') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Tahapan PBJ &amp; Log
                        </a>
                    </li>
                </ul>
            </li>

            <!-- 7. Komunikasi -->
            <li class="dropdown <?= $openClass('communication') ?>">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:chat-round-dots-outline" class="menu-icon"></iconify-icon>
                    <span>Komunikasi</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a class="<?= $activeClass('communication', ['news', 'news-categories']) ?>" href="<?= site_url('dashboard/komunikasi/berita') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Warta Berita &amp; Kategori
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('communication', ['events', 'event-registrations', 'attendance', 'waitlist-cancellations']) ?>" href="<?= site_url('dashboard/komunikasi/event-agenda') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Konsol Event &amp; Presensi
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('communication', ['inbox', 'helpdesk', 'newsletter', 'pesan-masuk', 'whatsapp-widget']) ?>" href="<?= site_url('dashboard/komunikasi/pesan-masuk') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Layanan Bantuan &amp; Inbox
                        </a>
                    </li>
                </ul>
            </li>

            <!-- 8. Sertifikat -->
            <li class="dropdown <?= $openClass('certificates') ?>">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:medal-ribbons-star-outline" class="menu-icon"></iconify-icon>
                    <span>Sertifikat</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a class="<?= $activeClass('certificates', ['riwayat', 'overview', 'learning', 'event', 'verification']) ?>" href="<?= site_url('dashboard/sertifikat/riwayat') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Master Sertifikat
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('certificates', ['batch', 'approvals']) ?>" href="<?= site_url('dashboard/sertifikat/batch') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Penerbitan Batch
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('certificates', ['templates', 'template']) ?>" href="<?= site_url('dashboard/sertifikat/template') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Desain Template
                        </a>
                    </li>
                </ul>
            </li>

            <!-- ============================================== -->
            <!-- III. KATEGORI ANALISIS DAN LAPORAN              -->
            <!-- ============================================== -->
            <li class="sidebar-menu-group-title">ANALISIS DAN LAPORAN</li>

            <!-- 9. Government Intelligence -->
            <li class="dropdown <?= $openClass('intelligence') ?>">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:chart-2-outline" class="menu-icon"></iconify-icon>
                    <span>Government Intelligence</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a class="<?= $activeClass('intelligence', ['dashboard', 'organization-units', 'competency-gap']) ?>" href="<?= site_url('dashboard/intelligence') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Dashboard Intelligence
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('intelligence', ['personnel', 'personnel-histories', 'personnel-levels']) ?>" href="<?= site_url('dashboard/intelligence/sdm-jf-ppbj') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> SDM JF PPBJ
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('intelligence', ['competency-certificates', 'competency-certificate-types']) ?>" href="<?= site_url('dashboard/intelligence/sertifikat-kompetensi') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Sertifikat Kompetensi
                        </a>
                    </li>
                </ul>
            </li>

            <!-- 10. Pusat Laporan -->
            <li class="dropdown <?= $openClass('reports') ?>">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:chart-square-outline" class="menu-icon"></iconify-icon>
                    <span>Laporan</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a class="<?= $activeClass('reports', ['learning', 'events', 'certificates', 'competency', 'users']) ?>" href="<?= site_url('dashboard/laporan/pembelajaran') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Laporan Terpadu
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('reports', ['export-history']) ?>" href="<?= site_url('dashboard/laporan/riwayat-export') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Riwayat Export
                        </a>
                    </li>
                </ul>
            </li>

            <!-- 11. Survei Kepuasan (IKM) -->
            <li class="<?= $currentMenu === 'survey' ? 'active-page' : '' ?>">
                <a class="<?= $currentMenu === 'survey' ? 'active-page' : '' ?>" href="<?= site_url('dashboard/survei-kepuasan') ?>">
                    <iconify-icon icon="solar:like-outline" class="menu-icon"></iconify-icon>
                    <span>Survei Kepuasan (IKM)</span>
                </a>
            </li>

            <!-- ============================================== -->
            <!-- IV. KATEGORI SISTEM                            -->
            <!-- ============================================== -->
            <li class="sidebar-menu-group-title">SISTEM</li>

            <!-- 12. Sistem & Keamanan -->
            <li class="dropdown <?= $openClass('system') ?>">
                <a href="javascript:void(0)">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:settings-outline" class="menu-icon"></iconify-icon>
                        <span>Sistem</span>
                        <?php if ($adminUnreadNotifCount > 0): ?>
                            <span class="w-8-px h-8-px bg-danger-600 rounded-circle flex-shrink-0" title="<?= $adminUnreadNotifCount ?> notifikasi baru"></span>
                        <?php endif; ?>
                    </div>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a class="<?= $activeClass('system', ['approvals', 'approval', 'approval-hub']) ?>" href="<?= site_url('dashboard/sistem/approval') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Approval Hub
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('system', ['audit-trail', 'audit']) ?>" href="<?= site_url('dashboard/sistem/audit-trail') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Audit Trail
                        </a>
                    </li>
                    <li>
                        <a class="<?= $activeClass('system', ['notifications', 'notifikasi']) ?> d-flex align-items-center justify-content-between" href="<?= site_url('dashboard/sistem/notifikasi') ?>">
                            <span><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Notifikasi</span>
                            <?php if ($adminUnreadNotifCount > 0): ?>
                                <span class="badge rounded-pill bg-danger text-white px-8 py-2 text-xs">
                                    <?= $adminUnreadNotifCount > 99 ? '99+' : $adminUnreadNotifCount ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php if ($isSuperAdmin): ?>
                        <li>
                            <a class="<?= $activeClass('system', ['settings', 'general-settings', 'general', 'security-settings', 'security', 'certificate-settings', 'certificate', 'maintenance', 'pengaturan', 'pengaturan-umum', 'pengaturan-organisasi', 'pengaturan-pembelajaran', 'pengaturan-bunga-rampai', 'pengaturan-regulasi', 'pengaturan-berita', 'pengaturan-event', 'pengaturan-sertifikat', 'pengaturan-notifikasi', 'pengaturan-integrasi', 'pengaturan-keamanan']) ?>" href="<?= site_url('dashboard/sistem/pengaturan') ?>">
                                <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Pusat Pengaturan
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        </ul>
    </div>
</aside>
