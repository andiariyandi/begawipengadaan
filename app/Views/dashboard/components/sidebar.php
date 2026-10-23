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
$isAdmin = $auth->isAdmin();

// Granular RBAC permissions check for sidebar visibility
$canSummary    = $isAdmin || $auth->can('dashboard.view');
$canIndicators = $isAdmin || $auth->can('dashboard.indicators.view');
$canDashboard  = $canSummary || $canIndicators;
$canCategoryMain = $canDashboard;

// Platform Management Modules
$canLanding = $isAdmin || $auth->canAny([
    'landing.view', 'landing.slider.manage', 'landing.profile.manage',
    'landing.portal.manage', 'landing.team.manage', 'landing.announcement.manage',
]);

$canAccessUsers         = $isAdmin || $auth->canAny(['access.user.view', 'access.user.manage', 'access.manage', 'user.manage']);
$canAccessVerifications = $isAdmin || $auth->canAny(['access.verification.view', 'access.verification.approve', 'user.verify', 'approvals.review']);
$canAccessRoles         = $isSuperAdmin || $auth->canAny(['access.role.manage', 'role.manage']);
$canAccessOrg           = $isAdmin || $auth->canAny(['access.organization.view', 'access.organization.manage', 'access.procurementrole.manage', 'access.manage']);
$canAccess              = $canAccessUsers || $canAccessVerifications || $canAccessRoles || $canAccessOrg;

$canLearning = $isAdmin || $auth->canAny([
    'learning.view', 'lms.manage', 'learning.class.view', 'learning.assessment.view', 'learning.enrollment.view',
    'learning.curriculum.manage', 'learning.certificate.manage', 'learning.report.view',
]);

$canKnowledge = $isAdmin || $auth->canAny([
    'bunga.view', 'kms.manage', 'bunga.article.manage', 'bunga.faq.manage', 'bunga.casestudy.manage',
    'bunga.template.manage', 'bunga.media.manage', 'bunga.taxonomy.manage',
]);

$canRegulations = $isAdmin || $auth->canAny([
    'regulation.view', 'regulation.manage', 'regulation.create', 'regulation.edit', 'regulation.category.manage', 'regulation.stage.manage',
]);

$canCommunication = $isAdmin || $auth->canAny([
    'communication.view', 'communication.news.manage', 'communication.event.view', 'communication.event.manage',
    'communication.inbox.manage', 'communication.helpdesk.manage', 'communication.newsletter.manage',
]);

$canCertificates = $isAdmin || $auth->canAny([
    'certificate.view', 'certificate.manage', 'certificate.batch.manage', 'certificate.template.manage', 'certificate.approve',
]);

$canCategoryPlatform = $canLanding || $canAccess || $canLearning || $canKnowledge || $canRegulations || $canCommunication || $canCertificates;

// Analysis and Reports Modules
$canIntelligence = $isAdmin || $auth->canAny([
    'intelligence.view', 'intelligence.jfppbj.manage', 'intelligence.competency.manage', 'intelligence.opd.manage',
]);

$canReports = $isAdmin || $auth->canAny([
    'reports.view', 'reports.learning.view', 'reports.event.view', 'reports.certificate.view', 'reports.competency.view', 'reports.user.view', 'reports.export',
]);

$canSurvey = $isAdmin || $auth->canAny(['survey.view', 'survey.manage']);

$canCategoryAnalysis = $canIntelligence || $canReports || $canSurvey;

// System & Security Modules
$canApprovals     = $isAdmin || $auth->canAny(['approval.view', 'approvals.review', 'approval.decide']);
$canAudit         = $isAdmin || $auth->canAny(['audit.view', 'audit.export']);
$canNotifications = $isAdmin || $auth->canAny(['notification.view', 'notification.send']);
$canSettings      = $isSuperAdmin || $isAdmin || $auth->can('system.settings');

$canSystemModule  = $canApprovals || $canAudit || $canNotifications || $canSettings;
$canCategorySystem = $canSystemModule;

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
            <?php if ($canCategoryMain): ?>
                <li class="sidebar-menu-group-title">UTAMA</li>

                <?php if ($canDashboard): ?>
                    <li class="dropdown <?= $openClass('summary') ?>">
                        <a href="javascript:void(0)">
                            <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                            <span>Dashboard</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <?php if ($canSummary): ?>
                                <li>
                                    <a class="<?= $activeClass('summary', ['dashboard', 'summary', '']) ?>" href="<?= site_url('dashboard') ?>">
                                        <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Ringkasan
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($canIndicators): ?>
                                <li>
                                    <a class="<?= $activeClass('summary', ['system-indicators', 'indicators', 'kpi', 'kpi-internal', 'recent-activity', 'aktivitas-terbaru']) ?>" href="<?= site_url('dashboard/indikator-sistem') ?>">
                                        <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Indikator Sistem
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

            <!-- ============================================== -->
            <!-- II. KATEGORI MANAJEMEN PLATFORM                 -->
            <!-- ============================================== -->
            <?php if ($canCategoryPlatform): ?>
                <li class="sidebar-menu-group-title">MANAJEMEN PLATFORM</li>

                <!-- 2. Kelola Landing Page -->
                <?php if ($canLanding): ?>
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
                <?php endif; ?>

                <!-- 3. Manajemen Akses -->
                <?php if ($canAccess): ?>
                    <li class="dropdown <?= $openClass('access') ?>">
                        <a href="javascript:void(0)">
                            <iconify-icon icon="solar:users-group-rounded-outline" class="menu-icon"></iconify-icon>
                            <span>Manajemen Akses</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <?php if ($canAccessUsers): ?>
                                <li>
                                    <a class="<?= $activeClass('access', ['users', 'pengguna', 'profile-verifications', 'verifikasi-profil']) ?>" href="<?= site_url('dashboard/akses/pengguna') ?>">
                                        <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Pengguna &amp; Verifikasi
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($canAccessVerifications): ?>
                                <li>
                                    <a class="<?= $activeClass('access', ['profile-change-requests', 'permohonan-perubahan-profil']) ?>" href="<?= site_url('dashboard/akses/permohonan-perubahan-profil') ?>">
                                        <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Perubahan Profil
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($canAccessRoles): ?>
                                <li>
                                    <a class="<?= $activeClass('access', ['roles', 'role-requests', 'peran', 'permission', 'permohonan-role']) ?>" href="<?= site_url('dashboard/akses/role') ?>">
                                        <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Role &amp; Hak Akses
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($canAccessOrg): ?>
                                <li>
                                    <a class="<?= $activeClass('access', ['organization-units', 'procurement-roles', 'unit-organisasi', 'peran-pengadaan']) ?>" href="<?= site_url('dashboard/akses/unit-organisasi') ?>">
                                        <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Unit Organisasi &amp; Peran
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- 4. Arena Belajar -->
                <?php if ($canLearning): ?>
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
                <?php endif; ?>

                <!-- 5. Bunga Rampai -->
                <?php if ($canKnowledge): ?>
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
                <?php endif; ?>

                <!-- 6. Regulasi PBJ -->
                <?php if ($canRegulations): ?>
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
                <?php endif; ?>

                <!-- 7. Komunikasi -->
                <?php if ($canCommunication): ?>
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
                <?php endif; ?>

                <!-- 8. Sertifikat -->
                <?php if ($canCertificates): ?>
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
                <?php endif; ?>
            <?php endif; ?>

            <!-- ============================================== -->
            <!-- III. KATEGORI ANALISIS DAN LAPORAN              -->
            <!-- ============================================== -->
            <?php if ($canCategoryAnalysis): ?>
                <li class="sidebar-menu-group-title">ANALISIS DAN LAPORAN</li>

                <!-- 9. Government Intelligence -->
                <?php if ($canIntelligence): ?>
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
                <?php endif; ?>

                <!-- 10. Pusat Laporan -->
                <?php if ($canReports): ?>
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
                <?php endif; ?>

                <!-- 11. Survei Kepuasan (IKM) -->
                <?php if ($canSurvey): ?>
                    <li class="<?= $currentMenu === 'survey' ? 'active-page' : '' ?>">
                        <a class="<?= $currentMenu === 'survey' ? 'active-page' : '' ?>" href="<?= site_url('dashboard/survei-kepuasan') ?>">
                            <iconify-icon icon="solar:like-outline" class="menu-icon"></iconify-icon>
                            <span>Survei Kepuasan (IKM)</span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

            <!-- ============================================== -->
            <!-- IV. KATEGORI SISTEM                            -->
            <!-- ============================================== -->
            <?php if ($canCategorySystem): ?>
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
                        <?php if ($canApprovals): ?>
                            <li>
                                <a class="<?= $activeClass('system', ['approvals', 'approval', 'approval-hub']) ?>" href="<?= site_url('dashboard/sistem/approval') ?>">
                                    <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Approval Hub
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($canAudit): ?>
                            <li>
                                <a class="<?= $activeClass('system', ['audit-trail', 'audit']) ?>" href="<?= site_url('dashboard/sistem/audit-trail') ?>">
                                    <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Audit Trail
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($canNotifications): ?>
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
                        <?php endif; ?>
                        <?php if ($canSettings): ?>
                            <li>
                                <a class="<?= $activeClass('system', ['settings', 'general-settings', 'general', 'security-settings', 'security', 'certificate-settings', 'certificate', 'maintenance', 'pengaturan', 'pengaturan-umum', 'pengaturan-organisasi', 'pengaturan-pembelajaran', 'pengaturan-bunga-rampai', 'pengaturan-regulasi', 'pengaturan-berita', 'pengaturan-event', 'pengaturan-sertifikat', 'pengaturan-notifikasi', 'pengaturan-integrasi', 'pengaturan-keamanan']) ?>" href="<?= site_url('dashboard/sistem/pengaturan') ?>">
                                    <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Pusat Pengaturan
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</aside>
