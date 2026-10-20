<?php
$isLoggedIn = session()->get('isLoggedIn') === true;
$roles = (array) session()->get('roles');
$userName = trim((string) (session()->get('name') ?: session()->get('username') ?: 'Pengguna'));
$accountInitial = strtoupper(mb_substr($userName, 0, 1, 'UTF-8'));
$roleLabel = match (true) {
    in_array('super_admin', $roles, true) => 'Super Admin',
    in_array('admin', $roles, true)       => 'Admin',
    in_array('operator', $roles, true)    => 'Operator',
    in_array('trainer', $roles, true)     => 'Pengajar',
    default                               => 'Peserta',
};
$workspaceHome = match (true) {
    in_array('super_admin', $roles, true) || in_array('admin', $roles, true) => route_to('workspace.admin.home'),
    in_array('trainer', $roles, true)                                        => route_to('workspace.trainer.home'),
    in_array('operator', $roles, true)                                       => route_to('workspace.operator.home'),
    default                                                                  => route_to('workspace.home'),
};
$hasDashboardAccess = (bool) session()->get('dashboard_access')
    || in_array('admin', $roles, true)
    || in_array('super_admin', $roles, true);
$mobileUserId = (int) (session()->get('user_id') ?: 0);
$mobileUnreadNotifCount = 0;
if ($isLoggedIn && $mobileUserId > 0) {
    $mobileNotifService = new \App\Services\NotificationService();
    $mobileUnreadNotifCount = $mobileNotifService->unreadCount($mobileUserId);
}
?>
<div class="mobile-nav__wrapper">
    <div class="mobile-nav__overlay mobile-nav__toggler"></div>
    <div class="mobile-nav__content">
        <span class="mobile-nav__close mobile-nav__toggler" aria-label="Tutup Menu Navigasi"><i class="fa fa-times"></i></span>
        <div class="logo-box">
            <?php
            $customLogo = setting('organization.logo_url');
            $logoSrc = ! empty($customLogo) ? (str_starts_with($customLogo, 'http') ? $customLogo : base_url($customLogo)) : base_url('assets/main/images/resources/logo-1.png');
            ?>
            <a href="<?= site_url('/') ?>" aria-label="BegawiPengadaan">
                <img src="<?= esc($logoSrc) ?>" width="125" alt="BegawiPengadaan">
            </a>
        </div>
        
        <div class="mobile-nav__actions px-3 pt-2 pb-3">
            <?php if (! $isLoggedIn): ?>
                <div class="d-flex gap-2 mb-2">
                    <a href="<?= route_to('dashboard.auth.login') ?>" class="btn btn-outline-light w-50 py-2 fw-semibold rounded-3 text-center">
                        <i class="fas fa-sign-in-alt me-1"></i> Masuk
                    </a>
                    <a href="<?= route_to('dashboard.auth.register') ?>" class="btn btn-warning w-50 py-2 fw-bold text-dark rounded-3 text-center">
                        <i class="fas fa-user-plus me-1"></i> Daftar
                    </a>
                </div>
            <?php else: ?>
                <!-- User Profile Card in Mobile Drawer -->
                <div class="mobile-nav__user-card p-3 rounded-3 mb-2" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle fw-bold text-white shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background: linear-gradient(135deg, #0c5aa6 0%, #f3a20b 100%); font-size: 14px;">
                            <?= esc($accountInitial ?: 'U') ?>
                        </span>
                        <div class="overflow-hidden flex-grow-1">
                            <strong class="d-block text-white text-truncate" style="font-size: 13.5px;"><?= esc($userName) ?></strong>
                            <span class="badge bg-warning text-dark fw-bold px-2 py-0.5 rounded-pill" style="font-size: 10px;"><?= esc($roleLabel) ?></span>
                        </div>
                    </div>
                    <div class="row g-1 pt-1">
                        <div class="col-6">
                            <a href="<?= esc($workspaceHome) ?>" class="btn btn-primary btn-sm w-100 py-1.5 rounded-2 d-flex align-items-center justify-content-center gap-1 text-truncate" style="font-size: 11.5px;">
                                <i class="fas fa-th-large"></i> Workspace
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= route_to('workspace.notifications') ?>" class="btn btn-outline-light btn-sm w-100 py-1.5 rounded-2 d-flex align-items-center justify-content-center gap-1 text-truncate position-relative" style="font-size: 11.5px;">
                                <i class="fas fa-bell"></i> Notifikasi
                                <?php if ($mobileUnreadNotifCount > 0): ?>
                                    <span class="badge bg-danger rounded-pill px-1.5 py-0.5 ms-1" style="font-size: 9px; line-height: 1;"><?= $mobileUnreadNotifCount > 99 ? '99+' : $mobileUnreadNotifCount ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= route_to('workspace.profile.index') ?>" class="btn btn-outline-light btn-sm w-100 py-1.5 rounded-2 d-flex align-items-center justify-content-center gap-1 text-truncate" style="font-size: 11.5px;">
                                <i class="fas fa-user-circle"></i> Profil
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= route_to('workspace.participant.classes') ?>" class="btn btn-outline-light btn-sm w-100 py-1.5 rounded-2 d-flex align-items-center justify-content-center gap-1 text-truncate" style="font-size: 11.5px;">
                                <i class="fas fa-graduation-cap"></i> Kelas
                            </a>
                        </div>
                        <?php if ($hasDashboardAccess): ?>
                            <div class="col-6">
                                <a href="<?= route_to('dashboard.home') ?>" class="btn btn-outline-info btn-sm w-100 py-1.5 rounded-2 d-flex align-items-center justify-content-center gap-1 text-truncate" style="font-size: 11.5px;">
                                    <i class="fas fa-tachometer-alt"></i> Admin
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="<?= $hasDashboardAccess ? 'col-6' : 'col-12 mt-1' ?>">
                            <form action="<?= route_to('dashboard.auth.logout') ?>" method="post" class="m-0 p-0">
                                <?php if (! is_cli()): ?>
                                    <?= csrf_field() ?>
                                <?php else: ?>
                                    <input type="hidden" name="csrf_test_name" value="cli-test-token">
                                <?php endif; ?>
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 py-1.5 rounded-2 d-flex align-items-center justify-content-center gap-1 text-truncate" style="font-size: 11.5px; color: #fca5a5; border-color: rgba(239,68,68,0.5);">
                                    <i class="fas fa-sign-out-alt"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <button type="button" class="btn btn-warning w-100 py-2 fw-bold text-dark rounded-3 shadow-sm mobile-survey-btn" data-bs-toggle="modal" data-bs-target="#modalSurveyKepuasan">
                <i class="fas fa-star me-1"></i> Isi Survei Kepuasan
            </button>
        </div>

        <div class="mobile-nav__container"></div>

        <ul class="mobile-nav__contact list-unstyled px-3 mt-3 mb-2">
            <li><i class="fa fa-envelope me-2"></i><a href="mailto:<?= esc(setting('organization.email', 'biropbj@lampungprov.go.id')) ?>"><?= esc(setting('organization.email', 'biropbj@lampungprov.go.id')) ?></a></li>
            <li><i class="fas fa-phone me-2"></i><a href="tel:<?= preg_replace('/[^0-9+]/', '', setting('organization.phone', '+6285117779783')) ?>"><?= esc(setting('organization.phone', '+62 851-1777-9783')) ?></a></li>
        </ul>

        <div class="mobile-nav__top px-3 pb-3">
            <div class="mobile-nav__social">
                <a href="<?= esc(setting('social.linkedin_url', '#')) ?>" class="fab fa-linkedin-in" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer"></a>
                <a href="<?= esc(setting('social.facebook_url', '#')) ?>" class="fab fa-facebook-square" aria-label="Facebook" target="_blank" rel="noopener noreferrer"></a>
                <a href="<?= esc(setting('social.instagram_url', '#')) ?>" class="fab fa-instagram" aria-label="Instagram" target="_blank" rel="noopener noreferrer"></a>
                <a href="<?= esc(setting('social.youtube_url', '#')) ?>" class="fab fa-youtube" aria-label="Youtube" target="_blank" rel="noopener noreferrer"></a>
            </div>
        </div>
    </div>
</div>