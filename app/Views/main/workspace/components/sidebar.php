<?php
$currentPath = trim(service('uri')->getPath(), '/');
$roles = (array) session()->get('roles');
$hasRole = static fn (string $role): bool => in_array($role, $roles, true);

// Determine primary role priority
$primaryRole = 'participant';
if ($hasRole('operator')) {
    $primaryRole = 'operator';
} elseif ($hasRole('evaluator')) {
    $primaryRole = 'evaluator';
} elseif ($hasRole('trainer')) {
    $primaryRole = 'trainer';
}

$roleLabel = match ($primaryRole) {
    'operator'  => 'Operator',
    'evaluator' => 'Evaluator',
    'trainer'   => 'Pengajar (Trainer)',
    default     => 'Peserta (Participant)',
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
        ['Kelas Saya', $classesUrl, 'fas fa-graduation-cap'],
        ['Materi', $materialUrl, 'fas fa-book'],
        ['Modul', $moduleUrl, 'fas fa-layer-group'],
        ['Peserta', $pesertaUrl, 'fas fa-users'],
        ['Assessment', $quizzesUrl, 'fas fa-clipboard-check'],
        ['Progress Peserta', $progressUrl, 'fas fa-chart-line'],
        ['Aktivitas', route_to('workspace.activity'), 'fas fa-history'],
        ['Profil', route_to('workspace.profile.index'), 'fas fa-user-circle'],
    ],
    'evaluator' => [
        ['Beranda', route_to('workspace.evaluator.home'), 'fas fa-home'],
        ['Review Submission', route_to('workspace.evaluator.all-submissions'), 'fas fa-tasks'],
        ['Penilaian', route_to('workspace.evaluator.all-grading'), 'fas fa-clipboard-check'],
        ['Feedback', route_to('workspace.evaluator.all-feedback'), 'fas fa-comment-dots'],
        ['Riwayat Penilaian', route_to('workspace.evaluator.history'), 'fas fa-history'],
        ['Profil', route_to('workspace.profile.index'), 'fas fa-user-circle'],
    ],
    'operator' => [
        ['Beranda', route_to('workspace.operator.home'), 'fas fa-home'],
        ['Approval', route_to('workspace.operator.approvals'), 'fas fa-check-double'],
        ['Verifikasi User', route_to('workspace.operator.profile-verifications'), 'fas fa-id-card'],
        ['Event Operation', route_to('workspace.operator.events'), 'fas fa-calendar-alt'],
        ['Presensi', route_to('workspace.operator.attendance'), 'fas fa-user-check'],
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