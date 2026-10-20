<?php
$currentPath = trim(service('uri')->getPath(), '/');
$roles = (array) session()->get('roles');
$hasRole = static fn (string $role): bool => in_array($role, $roles, true);

// Determine primary role priority
$primaryRole = 'participant';
if ($hasRole('super_admin') || $hasRole('super_administrator')) {
    $primaryRole = 'super_admin';
} elseif ($hasRole('admin') || $hasRole('administrator')) {
    $primaryRole = 'admin';
} elseif ($hasRole('operator')) {
    $primaryRole = 'operator';
} elseif ($hasRole('trainer')) {
    $primaryRole = 'trainer';
}

$roleLabel = match ($primaryRole) {
    'super_admin' => 'Super Administrator',
    'admin'       => 'Administrator',
    'operator'    => 'Operator',
    'trainer'     => 'Pengajar (Trainer)',
    default       => 'Peserta (Participant)',
};

$displayName = (string) (session()->get('name') ?: session()->get('username') ?: 'Pengguna');
$initials = strtoupper(mb_substr($displayName, 0, 1, 'UTF-8'));

$wsUserId = (int) (session()->get('user_id') ?: 0);
$wsUnreadCount = 0;
if ($wsUserId > 0) {
    $wsNotifService = new \App\Services\NotificationService();
    $wsUnreadCount = $wsNotifService->unreadCount($wsUserId);
}

$isCurrent = static function (?string $url) use ($currentPath): bool {
    if (empty($url)) {
        return false;
    }
    $path = trim(parse_url($url, PHP_URL_PATH) ?? '', '/');
    if ($path === '' || $path === 'workspace') {
        return $currentPath === $path;
    }
    return $currentPath === $path || str_starts_with($currentPath, $path . '/');
};

$renderLink = static function (string $label, ?string $url, string $icon, bool $active = false, ?int $badgeCount = null): string {
    $class = 'workspace-sidebar__link' . ($active ? ' is-active' : '');
    $badgeHtml = '';
    if ($badgeCount !== null && $badgeCount > 0) {
        $badgeText = $badgeCount > 99 ? '99+' : (string) $badgeCount;
        $badgeHtml = '<span class="badge bg-danger rounded-pill ms-auto px-2 py-0.5" style="font-size: 11px;">' . $badgeText . '</span>';
    }
    if ($url === null) {
        return '<span class="' . $class . ' is-disabled" aria-disabled="true">'
            . '<span class="d-flex align-items-center gap-2"><i class="' . esc($icon) . ' text-muted"></i> ' . esc($label) . '</span>'
            . '<small>Segera</small></span>';
    }
    return '<a class="' . $class . ' d-flex align-items-center justify-content-between" href="' . esc($url) . '">'
        . '<span class="d-flex align-items-center gap-2"><i class="' . esc($icon) . '"></i> ' . esc($label) . '</span>'
        . $badgeHtml
        . '</a>';
};

// Trainer class shortcut resolver
$trainerClassId = 0;
if ($primaryRole === 'trainer') {
    $row = db_connect()->table('class_trainers')
        ->select('class_id')
        ->where('user_id', (int) session()->get('user_id'))
        ->where('status', 'active')
        ->orderBy('class_id', 'ASC')
        ->limit(1)
        ->get()->getRowArray();
    $trainerClassId = (int) ($row['class_id'] ?? 0);
}

$classesUrl  = route_to('workspace.trainer.classes');
$moduleUrl   = ($trainerClassId > 0) ? route_to('workspace.trainer.modules', $trainerClassId)    : $classesUrl;
$materialUrl = ($trainerClassId > 0) ? route_to('workspace.trainer.materials', $trainerClassId)  : $classesUrl;
$pesertaUrl  = ($trainerClassId > 0) ? route_to('workspace.trainer.participants', $trainerClassId) : $classesUrl;
$progressUrl = ($trainerClassId > 0) ? route_to('workspace.trainer.progress', $trainerClassId)   : $classesUrl;
$quizzesUrl  = ($trainerClassId > 0) ? route_to('workspace.trainer.quizzes', $trainerClassId)    : $classesUrl;

// Role-based menu definitions
$menuItems = match ($primaryRole) {
    'super_admin', 'admin' => [
        ['Pusat Eksekutif', route_to('workspace.admin.home'), 'fas fa-crown'],
        ['Pusat Operasional', route_to('workspace.operator.home'), 'fas fa-cogs'],
        ['Antrean Approval', route_to('workspace.operator.approvals'), 'fas fa-stamp'],
        ['Verifikasi User', route_to('workspace.operator.profile-verifications'), 'fas fa-id-card'],
        ['Perubahan Profil', route_to('workspace.operator.profile-change-requests'), 'fas fa-user-edit'],
        ['Penugasan Trainer', route_to('workspace.operator.class-trainers'), 'fas fa-chalkboard-teacher'],
        ['Monitoring Belajar', route_to('workspace.operator.learning-monitoring'), 'fas fa-chart-line'],
        ['Verifikasi Sertifikat', route_to('workspace.operator.certificates'), 'fas fa-award'],
        ['Audit Aktivitas', route_to('workspace.operator.activity-monitoring'), 'fas fa-history'],
        ['Dashboard WowDash', site_url('dashboard'), 'fas fa-tachometer-alt'],
    ],
    'trainer' => [
        ['Beranda', route_to('workspace.trainer.home'), 'fas fa-home'],
        ['Kelas Saya', route_to('workspace.trainer.classes'), 'fas fa-graduation-cap'],
        ['Materi Pembelajaran', route_to('workspace.trainer.all-materials'), 'fas fa-book'],
        ['Assessment', route_to('workspace.trainer.all-quizzes'), 'fas fa-clipboard-check'],
        ['Peserta', route_to('workspace.trainer.all-participants'), 'fas fa-users'],
        ['Progress Peserta', route_to('workspace.trainer.all-progress'), 'fas fa-chart-line'],
    ],
    'operator' => [
        ['Beranda', route_to('workspace.operator.home'), 'fas fa-home'],
        ['Approval', route_to('workspace.operator.approvals'), 'fas fa-stamp'],
        ['Verifikasi User', route_to('workspace.operator.profile-verifications'), 'fas fa-id-card'],
        ['Perubahan Profil', route_to('workspace.operator.profile-change-requests'), 'fas fa-user-edit'],
        ['Kelas & Trainer Assignment', route_to('workspace.operator.class-trainers'), 'fas fa-chalkboard-teacher'],
        ['Operasional Event', route_to('workspace.operator.events'), 'fas fa-calendar-alt'],
        ['Presensi', route_to('workspace.operator.attendance'), 'fas fa-user-check'],
        ['Monitoring Pembelajaran', route_to('workspace.operator.learning-monitoring'), 'fas fa-chart-line'],
        ['Verifikasi Sertifikat', route_to('workspace.operator.certificates'), 'fas fa-award'],
        ['Evaluasi & Override', route_to('workspace.operator.evaluations'), 'fas fa-clipboard-check'],
        ['Aktivitas Sistem', route_to('workspace.operator.activity-monitoring'), 'fas fa-history'],
    ],
    default => [ // participant
        ['Beranda', route_to('workspace.home'), 'fas fa-home'],
        ['Pembelajaran Saya', route_to('workspace.participant.classes'), 'fas fa-graduation-cap'],
        ['Tugas', route_to('workspace.participant.all-assignments'), 'fas fa-tasks'],
        ['Assessment', route_to('workspace.participant.all-assessments'), 'fas fa-clipboard-check'],
        ['Event Saya', route_to('workspace.participant.events'), 'fas fa-calendar-alt'],
        ['Presensi Saya', route_to('workspace.participant.attendance'), 'fas fa-user-check'],
        ['Sertifikat Saya', route_to('workspace.participant.certificates'), 'fas fa-award'],
        ['Konsultasi & Tiket', route_to('workspace.consultation.index'), 'fas fa-comments'],
        ['Bookmark', route_to('workspace.bookmarks.index'), 'fas fa-bookmark'],
        ['Notifikasi', route_to('workspace.notifications'), 'fas fa-bell'],
        ['Profil', route_to('workspace.profile.index'), 'fas fa-user-circle'],
    ],
};

// Global features list
$globalItems = [
    ['Konsultasi & Tiket', route_to('workspace.consultation.index'), 'fas fa-comments'],
    ['Bookmark Saya', route_to('workspace.bookmarks.index'), 'fas fa-bookmark'],
    ['Notifikasi', route_to('workspace.notifications'), 'fas fa-bell'],
    ['Profil Saya', route_to('workspace.profile.index'), 'fas fa-user-circle'],
    ['Aktivitas Saya', route_to('workspace.activity'), 'fas fa-history'],
];
?>
<aside class="col-lg-3 col-xl-3 workspace-sidebar-wrap">
    <div class="workspace-sidebar">
        <a href="<?= esc(route_to('workspace.profile.index')) ?>" class="workspace-sidebar__identity text-decoration-none" title="Lihat dan kelola profil">
            <span class="avatar"><?= esc($initials ?: 'U') ?></span>
            <div>
                <strong><?= esc($displayName) ?></strong>
                <span><?= esc($roleLabel) ?></span>
            </div>
        </a>

        <!-- Mobile Toggle Button (Visible only on screens < lg) -->
        <button class="btn btn-outline-primary btn-sm w-100 d-lg-none mt-3 mb-2 rounded-pill d-flex align-items-center justify-content-between px-3" 
                type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#wsSidebarMenuCollapse" 
                aria-expanded="false" 
                aria-controls="wsSidebarMenuCollapse">
            <span class="fw-semibold"><i class="fas fa-bars me-2"></i> Menu Navigasi Workspace</span>
            <i class="fas fa-chevron-down small"></i>
        </button>

        <div class="collapse d-lg-block" id="wsSidebarMenuCollapse">
            <nav aria-label="Menu Workspace">
                <section class="workspace-sidebar__group">
                    <h2>Menu Utama (<?= esc($roleLabel) ?>)</h2>
                    <?php foreach ($menuItems as [$label, $url, $icon]): ?>
                        <?php $badgeCount = ($label === 'Notifikasi') ? $wsUnreadCount : null; ?>
                        <?= $renderLink($label, $url, $icon, $isCurrent($url), $badgeCount) ?>
                    <?php endforeach; ?>
                </section>

                <?php if ($primaryRole !== 'participant'): ?>
                    <section class="workspace-sidebar__group">
                        <h2>Fitur Personal</h2>
                        <?php foreach ($globalItems as [$label, $url, $icon]): ?>
                            <?php $badgeCount = ($label === 'Notifikasi') ? $wsUnreadCount : null; ?>
                            <?= $renderLink($label, $url, $icon, $isCurrent($url), $badgeCount) ?>
                        <?php endforeach; ?>
                    </section>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</aside>