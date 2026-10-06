<?php
$currentPath = trim(service('uri')->getPath(), '/');
$roles = (array) session()->get('roles');
$hasRole = static fn (string $role): bool => in_array($role, $roles, true);
$isActive = static fn (string $path): bool => $currentPath === trim($path, '/');
$link = static function (string $label, ?string $url, bool $active = false, bool $sub = false): string {
    $base = 'workspace-sidebar__link' . ($active ? ' is-active' : '') . ($sub ? ' workspace-sidebar__link--sub' : '');
    if ($url === null) {
        return '<span class="' . $base . ' is-disabled" aria-disabled="true">' . esc($label) . '<small>Segera hadir</small></span>';
    }
    return '<a class="' . $base . '" href="' . esc($url) . '">' . esc($label) . '</a>';
};
$displayName = $userName ?? 'Pengguna';
$initials = strtoupper(mb_substr($displayName, 0, 1, 'UTF-8'));

// === Role-aware sidebar ===========================================================
$isTrainer      = $hasRole('trainer') && ! $hasRole('super_admin') && ! $hasRole('admin');
$trainerClassId = 0;
if ($isTrainer) {
    $row = db_connect()->table('class_trainers')
        ->select('class_id')
        ->where('user_id', (int) session()->get('user_id'))
        ->where('status', 'active')
        ->orderBy('class_id', 'ASC')
        ->limit(1)
        ->get()->getRowArray();
    $trainerClassId = (int) ($row['class_id'] ?? 0);
}
$classesUrl  = $isTrainer ? route_to('workspace.trainer.classes') : '#';
$moduleUrl   = ($isTrainer && $trainerClassId > 0) ? route_to('workspace.trainer.modules', $trainerClassId)    : $classesUrl;
$materialUrl = ($isTrainer && $trainerClassId > 0) ? route_to('workspace.trainer.materials', $trainerClassId)  : $classesUrl;
$pesertaUrl  = ($isTrainer && $trainerClassId > 0) ? route_to('workspace.trainer.participants', $trainerClassId) : $classesUrl;
$progressUrl = ($isTrainer && $trainerClassId > 0) ? route_to('workspace.trainer.progress', $trainerClassId)   : $classesUrl;
$quizzesUrl  = ($isTrainer && $trainerClassId > 0) ? route_to('workspace.trainer.quizzes', $trainerClassId)   : $classesUrl;

if (! $isTrainer) {
$dashboard = [
    ['Ringkasan', route_to('workspace.home'), $isActive('workspace')],
    ['Kelas Aktif', route_to('workspace.participant.classes'), $isActive('workspace/peserta/kelas-saya')],
    ['Progress', null, false],
    ['Aktivitas Terakhir', null, false],
];

$pembelajaran = [
    [
        'label' => 'Kelas Saya',
        'items' => [
            ['Semua Kelas', route_to('workspace.participant.classes'), $isActive('workspace/peserta/kelas-saya')],
            ['Menunggu Approval', route_to('workspace.participant.classes'), false],
            ['Aktif', route_to('workspace.participant.classes'), false],
            ['Selesai', route_to('workspace.participant.classes'), false],
            ['Ditolak', route_to('workspace.participant.classes'), false],
        ],
    ],
    [
        'label' => 'Struktur Pembelajaran',
        'items' => [
            ['Modul & Lesson', route_to('workspace.participant.classes'), false],
        ],
    ],
    [
        'label' => 'Progress Belajar',
        'items' => [
            ['Lihat Progress', route_to('workspace.participant.progress', 1), false],
        ],
    ],
    [
        'label' => 'Sertifikat Saya',
        'items' => [
            ['Sertifikat', route_to('workspace.participant.certificates'), $isActive('workspace/peserta/sertifikat')],
        ],
    ],
];

$assessment = [
    ['Pre-test', null, false],
    ['Quiz', null, false],
    ['Post-test', null, false],
    ['Nilai', null, false],
    ['Feedback', null, false],
];

$tugas = [
    ['Tugas Aktif', null, false],
    ['Pengumpulan', null, false],
    ['Revisi', null, false],
    ['Riwayat Tugas', null, false],
];

$bookmark = [
    ['Course', null, false],
    ['Modul', null, false],
    ['Lesson', null, false],
    ['Bunga Rampai', null, false],
    ['Regulasi', null, false],
    ['Berita', null, false],
    ['Event', null, false],
];

$notifikasi = [
    ['Enrollment', route_to('workspace.notifications'), $isActive('workspace/notifikasi')],
    ['Pembelajaran', route_to('workspace.notifications'), false],
    ['Assessment', route_to('workspace.notifications'), false],
    ['Tugas', route_to('workspace.notifications'), false],
    ['Sistem', route_to('workspace.notifications'), false],
];

$profil = [
    ['Data Diri', route_to('profile.index'), $isActive('profil')],
    ['Kompetensi', null, false],
    ['Riwayat Belajar', null, false],
    ['Pengaturan', null, false],
];

$aktivitas = [
    ['Login', null, false],
    ['Aktivitas Belajar', null, false],
    ['Aktivitas Sistem', null, false],
];
} else {
    $dashboard = [
        ['Ringkasan', route_to('workspace.trainer.home'), $isActive('workspace/trainer')],
        ['Kelas Saya', $classesUrl, $isActive('workspace/trainer/kelas-saya')],
    ];
    $pembelajaran = [
        [
            'label' => 'Manajemen Kelas',
            'items' => [
                ['Modul', $moduleUrl, $isActive('workspace/trainer/kelas/' . $trainerClassId . '/modul')],
                ['Materi / Lesson', $materialUrl, $isActive('workspace/trainer/kelas/' . $trainerClassId . '/materi')],
                ['Peserta', $pesertaUrl, $isActive('workspace/trainer/kelas/' . $trainerClassId . '/peserta')],
                ['Progress', $progressUrl, $isActive('workspace/trainer/kelas/' . $trainerClassId . '/progress')],
            ],
        ],
        [
            'label' => 'Penilaian',
            'items' => [
                ['Quiz / Ujian', $quizzesUrl, $isActive('workspace/trainer/kelas/' . $trainerClassId . '/quiz')],
            ],
        ],
    ];
    $assessment = [];
    $tugas      = [];
    $bookmark   = [];
    $notifikasi = [];
    $profil     = [
        ['Data Diri', route_to('profile.index'), $isActive('profil')],
    ];
    $aktivitas  = [];
}

$renderGroup = static function (string $label, array $items) use (&$renderGroup): string {
    $html = '<section class="workspace-sidebar__group"><h2>' . esc($label) . '</h2>';
    foreach ($items as $item) {
        if (isset($item['items'])) {
            $html .= '<div class="workspace-sidebar__subgroup"><h3>' . esc($item['label']) . '</h3>';
            foreach ($item['items'] as $sub) {
                $html .= $renderGroup('', [$sub]);
            }
            $html .= '</div>';
        } else {
            $html .= ($item[2] ?? false)
                ? $GLOBALS['sidebar_link_fn']($item[0], $item[1] ?? null, true, false)
                : $GLOBALS['sidebar_link_fn']($item[0], $item[1] ?? null, false, false);
        }
    }
    $html .= '</section>';
    return $html;
};
$sidebar_link_fn = $link;
?>
<aside class="workspace-sidebar-wrap">
    <div class="workspace-sidebar">
        <div class="workspace-sidebar__identity">
            <span class="avatar"><?= esc($initials ?: 'U') ?></span>
            <div>
                <strong><?= esc($displayName) ?></strong>
                <span><?= esc($roleLabel ?? 'Pengguna') ?></span>
            </div>
        </div>
        <nav aria-label="Menu Workspace">
            <section class="workspace-sidebar__group">
                <h2>Dashboard</h2>
                <?php foreach ($dashboard as [$label, $url, $active]): ?>
                    <?= $link($label, $url, $active) ?>
                <?php endforeach; ?>
            </section>

            <section class="workspace-sidebar__group">
                <h2>Pembelajaran</h2>
                <?php foreach ($pembelajaran as $group): ?>
                    <div class="workspace-sidebar__subgroup">
                        <h3><?= esc($group['label']) ?></h3>
                        <?php foreach ($group['items'] as [$label, $url, $active]): ?>
                            <?= $link($label, $url, $active, true) ?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </section>

            <section class="workspace-sidebar__group">
                <h2>Assessment</h2>
                <?php foreach ($assessment as [$label, $url, $active]): ?>
                    <?= $link($label, $url, $active) ?>
                <?php endforeach; ?>
            </section>

            <section class="workspace-sidebar__group">
                <h2>Tugas</h2>
                <?php foreach ($tugas as [$label, $url, $active]): ?>
                    <?= $link($label, $url, $active) ?>
                <?php endforeach; ?>
            </section>

            <section class="workspace-sidebar__group">
                <h2>Bookmark</h2>
                <?php foreach ($bookmark as [$label, $url, $active]): ?>
                    <?= $link($label, $url, $active) ?>
                <?php endforeach; ?>
            </section>

            <section class="workspace-sidebar__group">
                <h2>Notifikasi</h2>
                <?php foreach ($notifikasi as [$label, $url, $active]): ?>
                    <?= $link($label, $url, $active) ?>
                <?php endforeach; ?>
            </section>

            <section class="workspace-sidebar__group">
                <h2>Profil</h2>
                <?php foreach ($profil as [$label, $url, $active]): ?>
                    <?= $link($label, $url, $active) ?>
                <?php endforeach; ?>
            </section>

            <section class="workspace-sidebar__group">
                <h2>Aktivitas</h2>
                <?php foreach ($aktivitas as [$label, $url, $active]): ?>
                    <?= $link($label, $url, $active) ?>
                <?php endforeach; ?>
            </section>
        </nav>
    </div>
</aside>