<?php
$currentPath = trim(service('uri')->getPath(), '/');
$roles = (array) session()->get('roles');
$hasRole = static fn (string $role): bool => in_array($role, $roles, true);
$isActive = static fn (string $path): bool => $currentPath === trim($path, '/');
$link = static function (string $label, ?string $url, bool $active = false): string {
    $class = 'workspace-sidebar__link' . ($active ? ' is-active' : '');
    if ($url === null) {
        return '<span class="' . $class . ' is-disabled" aria-disabled="true">' . esc($label) . '<small>Segera hadir</small></span>';
    }
    return '<a class="' . $class . '" href="' . esc($url) . '">' . esc($label) . '</a>';
};
$groups = [
    [
        'label' => 'Workspace',
        'items' => [
            ['Dashboard', route_to('workspace.home'), $isActive('workspace')],
        ],
    ],
];
if ($hasRole('participant') || (!$hasRole('trainer') && !$hasRole('evaluator') && !$hasRole('operator'))) {
    $groups[] = [
        'label' => 'Pembelajaran',
        'items' => [
            ['Kelas Saya', route_to('workspace.participant.classes'), $isActive('workspace/peserta/kelas-saya')],
            ['Progress Belajar', null, false],
            ['Sertifikat Saya', route_to('workspace.participant.certificates'), $isActive('workspace/peserta/sertifikat')],
        ],
    ];
    $groups[] = [
        'label' => 'Assessment',
        'items' => [
            ['Pre-test', null, false],
            ['Quiz', null, false],
            ['Post-test', null, false],
            ['Nilai', null, false],
        ],
    ];
    $groups[] = [
        'label' => 'Akses Cepat',
        'items' => [
            ['Bookmark', route_to('workspace.bookmarks.index'), $isActive('workspace/bookmark')],
            ['Notifikasi', route_to('workspace.notifications'), $isActive('workspace/notifikasi')],
            ['Profil', route_to('profile.index'), $isActive('profil')],
        ],
    ];
} else {
    $groups[0]['items'][] = ['Bookmark', route_to('workspace.bookmarks.index'), $isActive('workspace/bookmark')];
    $groups[0]['items'][] = ['Notifikasi', route_to('workspace.notifications'), $isActive('workspace/notifikasi')];
    $groups[0]['items'][] = ['Profil', route_to('profile.index'), $isActive('profil')];
}
if ($hasRole('trainer')) {
    $groups[] = [
        'label' => 'Trainer',
        'items' => [
            ['Kelas Saya', route_to('workspace.trainer.classes'), $isActive('workspace/trainer/kelas-saya')],
            ['Materi', null, false],
            ['Progress Peserta', null, false],
        ],
    ];
}
if ($hasRole('evaluator')) {
    $groups[] = [
        'label' => 'Evaluator',
        'items' => [
            ['Kelas Ditugaskan', route_to('workspace.evaluator.assignments'), $isActive('workspace/evaluator/penugasan')],
            ['Penilaian', null, false],
        ],
    ];
}
if ($hasRole('operator')) {
    $groups[] = [
        'label' => 'Operasional',
        'items' => [
            ['Enrollment', route_to('workspace.operator.enrollments'), $isActive('workspace/operator/enrollment')],
            ['Certificate', null, false],
        ],
    ];
}
?>
<aside class="col-lg-3 col-xl-3">
    <div class="workspace-sidebar">
        <div class="workspace-sidebar__identity">
            <span class="workspace-sidebar__eyebrow">Workspace</span>
            <strong><?= esc($userName ?? 'Pengguna') ?></strong>
            <span><?= esc($roleLabel ?? 'Pengguna') ?></span>
        </div>
        <nav aria-label="Menu Workspace">
            <?php foreach ($groups as $group): ?>
                <section class="workspace-sidebar__group">
                    <h2><?= esc($group['label']) ?></h2>
                    <?php foreach ($group['items'] as [$label, $url, $active]): ?>
                        <?= $link($label, $url, $active) ?>
                    <?php endforeach; ?>
                </section>
            <?php endforeach; ?>
        </nav>
    </div>
</aside>
