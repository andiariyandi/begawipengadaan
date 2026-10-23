<?php
$currentPath = trim(service('uri')->getPath(), '/');
$isActive = static function (string $path) use ($currentPath): bool {
    return trim($path, '/') === $currentPath;
};
$isPrefixActive = static function (string $prefix) use ($currentPath): bool {
    $prefix = trim($prefix, '/');
    return $currentPath === $prefix || str_starts_with($currentPath, $prefix . '/');
};
$isLoggedIn = session()->get('isLoggedIn') === true;
$roles = (array) session()->get('roles');
$userName = trim((string) (session()->get('name') ?: session()->get('username') ?: 'Pengguna'));
$workspaceHome = match (true) {
    in_array('super_administrator', $roles, true) || in_array('administrator', $roles, true) => route_to('workspace.admin.home'),
    in_array('trainer', $roles, true)                                        => route_to('workspace.trainer.home'),
    in_array('operator', $roles, true)                                       => route_to('workspace.operator.home'),
    default                                                                  => route_to('workspace.home'),
};
$roleLabel = match (true) {
    in_array('super_administrator', $roles, true) => 'Super Admin',
    in_array('administrator', $roles, true)       => 'Admin',
    in_array('operator', $roles, true)    => 'Operator',
    in_array('evaluator', $roles, true)   => 'Evaluator',
    in_array('trainer', $roles, true)     => 'Pengajar',
    default                               => 'Peserta',
};
$accountInitial = strtoupper(substr($userName, 0, 1));
$hasDashboardAccess = (bool) session()->get('dashboard_access')
    || in_array('administrator', $roles, true)
    || in_array('super_administrator', $roles, true);
$isHomeView = !empty($isHome);
$headerUserId = (int) (session()->get('user_id') ?: 0);
$userUnreadNotifCount = 0;
$latestNotifs = [];
if ($isLoggedIn && $headerUserId > 0) {
    $headerNotifService = new \App\Services\NotificationService();
    $userUnreadNotifCount = $headerNotifService->unreadCount($headerUserId);
    $latestNotifs = $headerNotifService->getUserNotifications($headerUserId, 5);
}
?>
<header class="main-header">

    <nav class="main-menu">
        <div class="main-menu__wrapper">
            <div class="container">
                <div class="main-menu__wrapper-inner">
                    <div class="main-menu__left">
                        <div class="main-menu__logo">
                            <?php
                            $customLogo = setting('organization.logo_url');
                            $logoLightSrc = ! empty($customLogo) ? (str_starts_with($customLogo, 'http') ? $customLogo : base_url($customLogo)) : base_url('assets/main/images/resources/logo-1.png');
                            $logoDarkSrc = ! empty($customLogo) ? (str_starts_with($customLogo, 'http') ? $customLogo : base_url($customLogo)) : base_url('assets/main/images/resources/logo-4.png');
                            ?>
                            <a href="<?= site_url('/') ?>" aria-label="Beranda BegawiPengadaan">
                                <img src="<?= esc($logoLightSrc) ?>" alt="BegawiPengadaan" class="main-header__logo-light">
                                <img src="<?= esc($logoDarkSrc) ?>" alt="BegawiPengadaan" class="main-header__logo-dark">
                            </a>
                        </div>
                    </div>

                    <div class="main-menu__main-menu-box">
                        <a href="#" class="mobile-nav__toggler"><i class="fa fa-bars"></i></a>
                        <ul class="main-menu__list">
                            <li class="<?= $isActive('') ? 'current' : '' ?>"><a href="<?= site_url('/') ?>">Beranda</a></li>
                            <li class="dropdown <?= $isPrefixActive('tentang') ? 'current' : '' ?>">
                                <a href="<?= site_url('tentang') ?>">Tentang</a>
                                <ul class="shadow-box">
                                    <li><a href="<?= site_url('tentang') ?>">Profil Biro PBJ</a></li>
                                    <li><a href="<?= site_url('tentang#visi-misi') ?>">Visi &amp; Misi 2024–2029</a></li>
                                    <li><a href="<?= site_url('tentang#pilar') ?>">5 Pilar Ekosistem BegawiPengadaan</a></li>
                                    <li><a href="<?= site_url('tentang#struktur') ?>">Struktur Organisasi</a></li>
                                    <li><a href="<?= site_url('tentang#maklumat') ?>">Maklumat Pelayanan UKPBJ</a></li>
                                </ul>
                            </li>
                            <li class=" <?= $isPrefixActive('arena-belajar') ? 'current' : '' ?>">
                                <a href="<?= site_url('arena-belajar') ?>">Arena Belajar</a>
                            </li>
                            <li class="dropdown <?= $isPrefixActive('bunga-rampai') ? 'current' : '' ?>">
                                <a href="<?= site_url('bunga-rampai') ?>">Bunga Rampai</a>
                                <ul class="shadow-box">
                                    <li><a href="<?= site_url('bunga-rampai') ?>">Beranda Bunga Rampai</a></li>
                                    <li><a href="<?= site_url('bunga-rampai/artikel') ?>">Artikel PBJ</a></li>
                                    <li><a href="<?= site_url('bunga-rampai/faq') ?>">Tanya Jawab (FAQ)</a></li>
                                    <li><a href="<?= site_url('bunga-rampai/studi-kasus') ?>">Studi Kasus Lapangan</a></li>
                                    <li><a href="<?= site_url('bunga-rampai/template-dokumen') ?>">Template Dokumen Kerja</a></li>
                                    <li><a href="<?= site_url('bunga-rampai/multimedia') ?>">Multimedia PBJ</a></li>
                                </ul>
                            </li>
                            <li class="dropdown <?= $isPrefixActive('regulasi') ? 'current' : '' ?>">
                                <a href="<?= site_url('regulasi') ?>">Regulasi</a>
                                <ul class="shadow-box">
                                    <li><a href="<?= site_url('regulasi') ?>">Pusat Regulasi PBJ</a></li>
                                    <li><a href="<?= site_url('regulasi?category=peraturan-presiden') ?>">Peraturan Presiden (Perpres)</a></li>
                                    <li><a href="<?= site_url('regulasi?category=peraturan-lkpp') ?>">Peraturan LKPP (PerLKPP)</a></li>
                                    <li><a href="<?= site_url('regulasi?category=peraturan-daerah-gubernur') ?>">Peraturan Daerah &amp; Pergub</a></li>
                                    <li><a href="<?= site_url('regulasi?category=standar-dokumen-pemilihan') ?>">Standar Dokumen Pemilihan (SDP)</a></li>
                                    <li><a href="<?= site_url('regulasi?category=surat-edaran') ?>">Surat Edaran (SE) PBJ</a></li>
                                </ul>
                            </li>
                            <li class="<?= $isPrefixActive('berita') ? 'current' : '' ?>">
                                <a href="<?= site_url('berita') ?>">Berita</a>
                            </li>
                            <li class="dropdown <?= $isPrefixActive('event') ? 'current' : '' ?>">
                                <a href="<?= site_url('event') ?>">Event &amp; Agenda</a>
                                <ul class="shadow-box">
                                    <li><a href="<?= site_url('event') ?>">Semua Event &amp; Agenda</a></li>
                                    <li><a href="<?= site_url('event/agenda') ?>">Agenda Mendatang</a></li>
                                    <li><a href="<?= site_url('event?tipe=webinar') ?>">Webinar PBJ Daring</a></li>
                                    <li><a href="<?= site_url('event?tipe=bimtek') ?>">Bimbingan Teknis (Bimtek)</a></li>
                                    <li><a href="<?= site_url('event?tipe=workshop') ?>">Workshop &amp; Lokakarya</a></li>
                                    <li><a href="<?= site_url('event?tipe=sosialisasi') ?>">Sosialisasi Regulasi</a></li>
                                    <li><a href="<?= site_url('event/kalender') ?>">Kalender Kegiatan PBJ</a></li>
                                </ul>
                            </li>
                            <li class="dropdown <?= $isPrefixActive('statistik') || $isPrefixActive('verifikasi-sertifikat') || $isActive('kontak') || $isPrefixActive('survei-kepuasan/hasil') ? 'current' : '' ?>">
                                <a href="#">Lainnya</a>
                                <ul class="shadow-box">
                                    <li><a href="<?= site_url('statistik') ?>">Statistik Publik PBJ</a></li>
                                    <li><a href="<?= site_url('statistik/survei-kepuasan') ?>"><i class="fas fa-chart-pie text-primary me-2"></i>Hasil Survei Kepuasan Publik (IKM)</a></li>
                                    <li><a href="<?= site_url('verifikasi-sertifikat') ?>">Verifikasi Sertifikat Digital</a></li>
                                    <li><a href="<?= site_url('kontak') ?>">Hubungi Kami &amp; Helpdesk LPSE</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>

                    <div class="main-menu__right">
                        <div class="main-menu__survey-box">
                            <button type="button" class="main-menu__survey-btn" data-bs-toggle="modal" data-bs-target="#modalSurveyKepuasan" aria-label="Isi Survei Kepuasan Layanan Pengguna">
                                <i class="fas fa-star text-warning"></i>
                                <span>Survei Kepuasan</span>
                            </button>
                        </div>
                        <?php if (! $isLoggedIn): ?>
                            <div class="main-menu__btn-boxes">
                                <div class="main-menu__btn-box-1"><a href="<?= route_to('dashboard.auth.login') ?>" class="thm-btn">Masuk</a></div>
                                <div class="main-menu__btn-box-2"><a href="<?= route_to('dashboard.auth.register') ?>" class="thm-btn">Daftar</a></div>
                            </div>
                        <?php else: ?>
                            <div class="main-menu__notif-box dropdown me-2">
                                <button type="button" class="main-menu__notif-btn" id="headerNotifDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Pemberitahuan Sistem">
                                    <i class="fas fa-bell" aria-hidden="true"></i>
                                    <?php if ($userUnreadNotifCount > 0): ?>
                                        <span class="main-menu__notif-badge"><?= $userUnreadNotifCount > 99 ? '99+' : $userUnreadNotifCount ?></span>
                                    <?php endif; ?>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end main-menu__notif-dropdown p-0 shadow-lg border-0" aria-labelledby="headerNotifDropdown">
                                    <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <strong class="text-dark fs-6 mb-0">Notifikasi</strong>
                                            <?php if ($userUnreadNotifCount > 0): ?>
                                                <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 11px;"><?= $userUnreadNotifCount ?> baru</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($userUnreadNotifCount > 0): ?>
                                            <form action="<?= route_to('workspace.notifications.read-all') ?>" method="post" class="m-0">
                                                <?php if (! is_cli()): ?>
                                                    <?= csrf_field() ?>
                                                <?php else: ?>
                                                    <input type="hidden" name="csrf_test_name" value="cli-test-token">
                                                <?php endif; ?>
                                                <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none text-primary" style="font-size: 11.5px;">Tandai semua dibaca</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                    <div class="notif-dropdown-body" style="max-height: 320px; overflow-y: auto;">
                                        <?php if (empty($latestNotifs)): ?>
                                            <div class="text-center py-4 px-3 text-muted">
                                                <i class="fas fa-bell-slash text-secondary opacity-50 fs-3 mb-2 d-block"></i>
                                                <p class="small mb-0">Belum ada notifikasi baru.</p>
                                            </div>
                                        <?php else: ?>
                                            <div class="list-group list-group-flush">
                                                <?php foreach ($latestNotifs as $n):
                                                    $isNUnread = empty($n['read_at']);
                                                    $nMeta = \App\Services\NotificationService::formatCategory($n['type'] ?? 'system');
                                                    $nTime = \App\Services\NotificationService::timeAgo($n['created_at'] ?? null);
                                                    $nData = !empty($n['data']) ? json_decode($n['data'], true) : [];
                                                    $targetUrl = !empty($nData['url']) ? $nData['url'] : route_to('workspace.notifications');
                                                ?>
                                                    <a href="<?= esc($targetUrl) ?>" class="list-group-item list-group-item-action notif-item p-2 px-3 border-bottom <?= $isNUnread ? 'is-unread bg-primary-subtle bg-opacity-10' : '' ?> d-flex gap-2 align-items-start">
                                                        <span class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1" style="width: 30px; height: 30px; background: rgba(8, 47, 92, 0.08);">
                                                            <i class="<?= esc($nMeta['fa']) ?>" style="font-size: 13px;"></i>
                                                        </span>
                                                        <div class="flex-grow-1 overflow-hidden" style="min-width: 0;">
                                                            <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                                                <strong class="text-truncate text-navy small" style="font-size: 12px;"><?= esc($n['title']) ?></strong>
                                                                <span class="text-muted text-nowrap" style="font-size: 10px;"><?= esc($nTime) ?></span>
                                                            </div>
                                                            <p class="text-muted small mb-1 text-truncate" style="font-size: 11px;"><?= esc($n['body'] ?? '') ?></p>
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <span class="badge <?= esc($nMeta['badge']) ?> rounded-pill" style="font-size: 9.5px; padding: 2px 7px;">
                                                                    <?= esc($nMeta['label']) ?>
                                                                </span>
                                                                <?php if ($isNUnread): ?>
                                                                    <span class="badge bg-primary rounded-pill small" style="font-size: 9px; padding: 1px 5px;">Baru</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="p-2 text-center bg-light border-top">
                                        <a href="<?= route_to('workspace.notifications') ?>" class="text-primary fw-semibold small text-decoration-none">
                                            Lihat Semua Notifikasi <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="main-menu__user">
                                <a href="<?= esc($workspaceHome) ?>" class="main-menu__user-trigger" aria-haspopup="true" aria-expanded="false">
                                    <span class="main-menu__user-avatar" aria-hidden="true"><?= esc($accountInitial ?: 'U') ?></span>
                                    <span class="main-menu__user-name"><?= esc(mb_strimwidth($userName, 0, 16, '…')) ?></span>
                                    <span class="icon-down-arrow" aria-hidden="true"></span>
                                </a>
                                <ul class="main-menu__user-menu shadow-box" role="menu">
                                    <li class="main-menu__user-meta">
                                        <strong><?= esc($userName) ?></strong>
                                        <small><?= esc($roleLabel) ?></small>
                                    </li>
                                    <li role="none"><a role="menuitem" href="<?= esc($workspaceHome) ?>">Dashboard Workspace</a></li>
                                    <li role="none"><a role="menuitem" href="<?= route_to('workspace.participant.classes') ?>">Kelas Saya</a></li>
                                    <li role="none"><a role="menuitem" href="<?= route_to('workspace.bookmarks.index') ?>">Bookmark</a></li>
                                    <li role="none">
                                        <a role="menuitem" href="<?= route_to('workspace.notifications') ?>" class="d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-bell me-2"></i>Notifikasi</span>
                                            <?php if ($userUnreadNotifCount > 0): ?>
                                                <span class="badge bg-danger rounded-pill px-2 py-0.5" style="font-size: 10.5px;"><?= $userUnreadNotifCount > 99 ? '99+' : $userUnreadNotifCount ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                    <li role="none"><a role="menuitem" href="<?= route_to('workspace.profile.index') ?>">Profil Saya</a></li>
                                    <li role="none"><a role="menuitem" href="<?= route_to('workspace.consultation.index') ?>"><i class="fas fa-comments me-2"></i>Konsultasi &amp; Tiket</a></li>
                                    <?php if ($hasDashboardAccess): ?>
                                        <li role="none"><a role="menuitem" href="<?= route_to('dashboard.home') ?>">Dashboard Administrasi</a></li>
                                    <?php endif; ?>
                                    <li class="main-menu__user-divider" aria-hidden="true"></li>
                                    <li role="none">
                                        <form action="<?= route_to('dashboard.auth.logout') ?>" method="post">
                                            <?php if (! is_cli()): ?>
                                                <?= csrf_field() ?>
                                            <?php else: ?>
                                                <input type="hidden" name="csrf_test_name" value="cli-test-token">
                                            <?php endif; ?>
                                            <button type="submit" role="menuitem" class="main-menu__user-logout-btn"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> Keluar</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>