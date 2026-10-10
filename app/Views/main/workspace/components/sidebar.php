<?php
$currentPath = trim(service('uri')->getPath(), '/');
$roles = (array) session()->get('roles');
$hasRole = static fn (string $role): bool => in_array($role, $roles, true);

// Determine primary role priority
$primaryRole = 'participant';
if ($hasRole('operator') || $hasRole('admin') || $hasRole('super_admin') || $hasRole('administrator')) {
    $primaryRole = 'operator';
} elseif ($hasRole('trainer')) {
    $primaryRole = 'trainer';
}

$roleLabel = match ($primaryRole) {
    'operator' => 'Operator',
    'trainer'  => 'Pengajar (Trainer)',
    default    => 'Peserta (Participant)',
};

$displayName = (string) (session()->get('name') ?: session()->get('username') ?: 'Pengguna');
$initials = strtoupper(mb_substr($displayName, 0, 1, 'UTF-8'));

$isCurrent = static function (?string $url) use ($currentPath): bool {
    if (empty($url)) {
        return false;
    }
    $path = trim(parse_url($url, PHP_URL_PATH) ?? '', '/');
    return $currentPath === $path || str_starts_with($currentPath, $path . '/');
};

$renderLink = static function (string $label, ?string $url, string $icon, bool $active = false): string {
    $class = 'workspace-sidebar__link' . ($active ? ' is-active' : '');
    if ($url === null) {
        return '<span class="' . $class . ' is-disabled" aria-disabled="true">'
            . '<span class="d-flex align-items-center gap-2"><i class="' . esc($icon) . ' text-muted"></i> ' . esc($label) . '</span>'
            . '<small>Segera</small></span>';
    }
    return '<a class="' . $class . '" href="' . esc($url) . '">'
        . '<span class="d-flex align-items-center gap-2"><i class="' . esc($icon) . '"></i> ' . esc($label) . '</span>'
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
    'trainer' => [
        ['Beranda', route_to('workspace.trainer.home'), 'fas fa-home'],
        ['Kelas Saya', route_to('workspace.trainer.classes'), 'fas fa-graduation-cap'],
        ['Materi Pembelajaran', route_to('workspace.trainer.all-materials'), 'fas fa-book'],
        ['Assessment', route_to('workspace.trainer.all-quizzes'), 'fas fa-clipboard-check'],
        ['Peserta', route_to('workspace.trainer.all-participants'), 'fas fa-users'],
        ['Progress Peserta', route_to('workspace.trainer.all-progress'), 'fas fa-chart-line'],
        ['Aktivitas', route_to('workspace.activity'), 'fas fa-history'],
        ['Profil', route_to('workspace.profile.index'), 'fas fa-user-circle'],
    ],
    'operator' => [
        ['Beranda', route_to('workspace.operator.home'), 'fas fa-home'],
        ['Verifikasi Profil', route_to('workspace.operator.profile-verifications'), 'fas fa-id-card'],
        ['Enrollment', route_to('workspace.operator.enrollments'), 'fas fa-user-graduate'],
        ['Evaluasi & Override', route_to('workspace.operator.evaluations'), 'fas fa-clipboard-check'],
        ['Event Operation', route_to('workspace.operator.events'), 'fas fa-calendar-alt'],
        ['Presensi', route_to('workspace.operator.attendance'), 'fas fa-user-check'],
        ['Approval', route_to('workspace.operator.approvals'), 'fas fa-stamp'],
        ['Monitoring Aktivitas', route_to('workspace.operator.activity-monitoring'), 'fas fa-chart-line'],
        ['Profil', route_to('workspace.profile.index'), 'fas fa-user-circle'],
    ],
    default => [ // participant
        ['Beranda', route_to('workspace.home'), 'fas fa-home'],
        ['Pembelajaran Saya', route_to('workspace.participant.classes'), 'fas fa-graduation-cap'],
        ['Tugas', route_to('workspace.participant.all-assignments'), 'fas fa-tasks'],
        ['Assessment', route_to('workspace.participant.all-assessments'), 'fas fa-clipboard-check'],
        ['Event Saya', route_to('workspace.participant.events'), 'fas fa-calendar-alt'],
        ['Presensi Saya', route_to('workspace.participant.attendance'), 'fas fa-user-check'],
        ['Sertifikat Saya', route_to('workspace.participant.certificates'), 'fas fa-award'],
        ['Bookmark', route_to('workspace.bookmarks.index'), 'fas fa-bookmark'],
        ['Notifikasi', route_to('workspace.notifications'), 'fas fa-bell'],
        ['Profil', route_to('workspace.profile.index'), 'fas fa-user-circle'],
    ],
};

// Global features list
$globalItems = [
    ['Bookmark Saya', route_to('workspace.bookmarks.index'), 'fas fa-bookmark'],
    ['Notifikasi', route_to('workspace.notifications'), 'fas fa-bell'],
    ['Profil Saya', route_to('workspace.profile.index'), 'fas fa-user-circle'],
    ['Aktivitas Saya', route_to('workspace.activity'), 'fas fa-history'],
];
?>
<aside class="workspace-sidebar-wrap">
    <div class="workspace-sidebar">
        <div class="workspace-sidebar__identity">
            <span class="avatar"><?= esc($initials ?: 'U') ?></span>
            <div>
                <strong><?= esc($displayName) ?></strong>
                <span><?= esc($roleLabel) ?></span>
            </div>
        </div>
        <nav aria-label="Menu Workspace">
            <section class="workspace-sidebar__group">
                <h2>Menu Utama (<?= esc($roleLabel) ?>)</h2>
                <?php foreach ($menuItems as [$label, $url, $icon]): ?>
                    <?= $renderLink($label, $url, $icon, $isCurrent($url)) ?>
                <?php endforeach; ?>
            </section>

            <?php if ($primaryRole !== 'participant'): ?>
                <section class="workspace-sidebar__group">
                    <h2>Fitur Personal</h2>
                    <?php foreach ($globalItems as [$label, $url, $icon]): ?>
                        <?= $renderLink($label, $url, $icon, $isCurrent($url)) ?>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>
        </nav>
    </div>
</aside>