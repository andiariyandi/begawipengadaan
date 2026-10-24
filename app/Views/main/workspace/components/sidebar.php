<?php
$currentPath = trim(service('uri')->getPath(), '/');
$roles = (array) session()->get('roles');
$hasRole = static fn (string $role): bool => in_array($role, $roles, true);

// Determine primary role priority
$primaryRole = 'participant';
if ($hasRole('super_administrator')) {
    $primaryRole = 'super_administrator';
} elseif ($hasRole('administrator')) {
    $primaryRole = 'administrator';
} elseif ($hasRole('operator')) {
    $primaryRole = 'operator';
} elseif ($hasRole('trainer')) {
    $primaryRole = 'trainer';
}

$roleLabel = match ($primaryRole) {
    'super_administrator' => 'Super Administrator',
    'administrator'       => 'Administrator',
    'operator'            => 'Operator & Co-Trainer',
    'trainer'             => 'Pengajar (Trainer)',
    default               => 'Peserta (Participant)',
};

$displayName = (string) (session()->get('name') ?: session()->get('username') ?: 'Pengguna');
$initials = strtoupper(mb_substr($displayName, 0, 1, 'UTF-8'));

$wsUserId = (int) (session()->get('user_id') ?: 0);
$wsEmail  = (string) (session()->get('email') ?: '');
$db = db_connect();

// Counter Badges Calculation (Lightweight queries on existing tables)
$wsUnreadCount = 0;
$wsTicketBadge = 0;
$badgeMap = [];

if ($wsUserId > 0) {
    try {
        $wsNotifService = new \App\Services\NotificationService();
        $wsUnreadCount = $wsNotifService->unreadCount($wsUserId);
    } catch (\Throwable) {
    }

    try {
        // Active consultation reply badge for user
        if ($db->tableExists('contact_messages') && $wsEmail !== '') {
            $wsTicketBadge = (int) $db->table('contact_messages')
                ->where('sender_email', $wsEmail)
                ->whereIn('status', ['replied', 'waiting_user'])
                ->countAllResults();
        }
    } catch (\Throwable) {
    }

    try {
        if (in_array($primaryRole, ['super_administrator', 'administrator', 'operator'], true)) {
            $badgeMap['approvals'] = (int) $db->table('approvals')->where('status', 'pending')->countAllResults();
            $badgeMap['verifications'] = (int) $db->table('profile_verifications')->where('status', 'pending')->countAllResults();
            $badgeMap['profile_changes'] = (int) $db->table('profile_change_requests')->whereIn('status', ['submitted', 'in_review'])->countAllResults();

            if ($primaryRole === 'operator') {
                $badgeMap['assigned_classes'] = (int) $db->table('class_trainers')
                    ->where(['user_id' => $wsUserId, 'status' => 'active'])
                    ->countAllResults();
            }
        } elseif ($primaryRole === 'trainer') {
            $badgeMap['trainer_classes'] = (int) $db->table('class_trainers')
                ->where(['user_id' => $wsUserId, 'status' => 'active'])
                ->countAllResults();
            $badgeMap['trainer_events'] = (int) $db->table('event_registrations er')
                ->join('events e', 'e.id = er.event_id')
                ->where('er.user_id', $wsUserId)
                ->where('e.deleted_at IS NULL')
                ->countAllResults();
        } else {
            $badgeMap['participant_classes'] = (int) $db->table('enrollments')
                ->where('user_id', $wsUserId)
                ->whereIn('status', ['pending', 'active', 'completed'])
                ->countAllResults();
            $badgeMap['participant_events'] = (int) $db->table('event_registrations er')
                ->join('events e', 'e.id = er.event_id')
                ->where('er.user_id', $wsUserId)
                ->where('e.deleted_at IS NULL')
                ->countAllResults();
            $badgeMap['participant_certs'] = (int) $db->table('certificates')
                ->where(['user_id' => $wsUserId, 'status' => 'issued'])
                ->countAllResults();
        }
    } catch (\Throwable) {
    }
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

$renderLink = static function (string $label, ?string $url, string $icon, bool $active = false, ?int $badgeCount = null, string $badgeColor = 'bg-danger'): string {
    $class = 'workspace-sidebar__link' . ($active ? ' is-active' : '');
    $badgeHtml = '';
    if ($badgeCount !== null && $badgeCount > 0) {
        $badgeText = $badgeCount > 99 ? '99+' : (string) $badgeCount;
        $badgeHtml = '<span class="badge ' . esc($badgeColor) . ' rounded-pill ms-auto px-2 py-0.5" style="font-size: 10.5px;">' . $badgeText . '</span>';
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

// Role-based grouped menu definitions (Streamlined & Organized)
$menuGroups = match ($primaryRole) {
    'super_administrator', 'administrator' => [
        [
            'title' => 'Komando Eksekutif & RBAC',
            'items' => [
                ['Pusat Eksekutif', route_to('workspace.admin.home'), 'fas fa-crown', null, 'bg-primary'],
                ['Penugasan Kelas (RBAC)', route_to('workspace.operator.class-trainers'), 'fas fa-sitemap', null, 'bg-primary'],
                ['Monitoring Pembelajaran', route_to('workspace.operator.learning-monitoring'), 'fas fa-chart-line', null, 'bg-primary'],
                ['Operasional Event & Presensi', route_to('workspace.operator.events'), 'fas fa-calendar-check', null, 'bg-primary'],
                ['Verifikasi Sertifikat', route_to('workspace.operator.certificates'), 'fas fa-award', null, 'bg-success'],
                ['Dashboard Utama', site_url('dashboard'), 'fas fa-tachometer-alt', null, 'bg-secondary'],
            ],
        ],
        [
            'title' => 'Pusat Approval & Verifikasi',
            'items' => [
                ['Antrean Approval', route_to('workspace.operator.approvals'), 'fas fa-stamp', $badgeMap['approvals'] ?? 0, 'bg-danger'],
                ['Verifikasi Akun Baru', route_to('workspace.operator.profile-verifications'), 'fas fa-id-card', $badgeMap['verifications'] ?? 0, 'bg-warning text-dark'],
                ['Approval Perubahan Profil', route_to('workspace.operator.profile-change-requests'), 'fas fa-user-edit', $badgeMap['profile_changes'] ?? 0, 'bg-warning text-dark'],
                ['Audit Aktivitas Sistem', route_to('workspace.operator.activity-monitoring'), 'fas fa-history', null, 'bg-secondary'],
            ],
        ],
    ],
    'operator' => [
        [
            'title' => 'Pengelolaan Kelas & Event',
            'items' => [
                ['Beranda Operasional', route_to('workspace.operator.home'), 'fas fa-home', null, 'bg-primary'],
                ['Kelas & Penugasan Trainer', route_to('workspace.operator.class-trainers'), 'fas fa-sitemap', $badgeMap['assigned_classes'] ?? 0, 'bg-primary'],
                ['Ruang Asisten Trainer', route_to('workspace.trainer.classes'), 'fas fa-chalkboard-teacher', $badgeMap['assigned_classes'] ?? 0, 'bg-info'],
                ['Monitoring & Evaluasi Kelas', route_to('workspace.operator.learning-monitoring'), 'fas fa-chart-line', null, 'bg-primary'],
                ['Operasional Event & Presensi', route_to('workspace.operator.events'), 'fas fa-calendar-check', null, 'bg-primary'],
                ['Verifikasi Sertifikat', route_to('workspace.operator.certificates'), 'fas fa-award', null, 'bg-success'],
            ],
        ],
        [
            'title' => 'Verifikasi & Approval',
            'items' => [
                ['Antrean Approval', route_to('workspace.operator.approvals'), 'fas fa-stamp', $badgeMap['approvals'] ?? 0, 'bg-danger'],
                ['Verifikasi Akun Baru', route_to('workspace.operator.profile-verifications'), 'fas fa-id-card', $badgeMap['verifications'] ?? 0, 'bg-warning text-dark'],
                ['Approval Perubahan Profil', route_to('workspace.operator.profile-change-requests'), 'fas fa-user-edit', $badgeMap['profile_changes'] ?? 0, 'bg-warning text-dark'],
                ['Log Aktivitas Sistem', route_to('workspace.operator.activity-monitoring'), 'fas fa-history', null, 'bg-secondary'],
            ],
        ],
    ],
    'trainer' => [
        [
            'title' => 'Pengajaran & Kelas',
            'items' => [
                ['Beranda Pengajar', route_to('workspace.trainer.home'), 'fas fa-home', null, 'bg-primary'],
                ['Kelas & Kurikulum Saya', route_to('workspace.trainer.classes'), 'fas fa-graduation-cap', $badgeMap['trainer_classes'] ?? 0, 'bg-primary'],
                ['Bank Materi Kelas', route_to('workspace.trainer.all-materials'), 'fas fa-book-open', null, 'bg-primary'],
                ['Soal & Assessment', route_to('workspace.trainer.all-quizzes'), 'fas fa-clipboard-check', null, 'bg-primary'],
                ['Peserta & Progress', route_to('workspace.trainer.all-participants'), 'fas fa-users', null, 'bg-primary'],
            ],
        ],
        [
            'title' => 'Event & Agenda Pengajar',
            'items' => [
                ['Event & Agenda Saya', route_to('workspace.trainer.events'), 'fas fa-calendar-alt', $badgeMap['trainer_events'] ?? 0, 'bg-info'],
                ['Presensi Kegiatan Saya', route_to('workspace.trainer.attendance'), 'fas fa-user-check', null, 'bg-success'],
            ],
        ],
    ],
    default => [ // participant
        [
            'title' => 'Pembelajaran & Kegiatan',
            'items' => [
                ['Beranda Workspace', route_to('workspace.home'), 'fas fa-home', null, 'bg-primary'],
                ['Pembelajaran & Kelas', route_to('workspace.participant.classes'), 'fas fa-graduation-cap', $badgeMap['participant_classes'] ?? 0, 'bg-primary'],
                ['Tugas Mandiri', route_to('workspace.participant.all-assignments'), 'fas fa-tasks', null, 'bg-primary'],
                ['Assessment & Ujian', route_to('workspace.participant.all-assessments'), 'fas fa-clipboard-check', null, 'bg-primary'],
                ['Event & Agenda Saya', route_to('workspace.participant.events'), 'fas fa-calendar-alt', $badgeMap['participant_events'] ?? 0, 'bg-info'],
                ['Presensi Kehadiran', route_to('workspace.participant.attendance'), 'fas fa-user-check', null, 'bg-success'],
                ['Sertifikat Saya', route_to('workspace.participant.certificates'), 'fas fa-award', $badgeMap['participant_certs'] ?? 0, 'bg-success'],
            ],
        ],
    ],
};

// Personal & Helpdesk features group (Available to all roles)
$personalGroup = [
    'title' => 'Fitur Personal & Helpdesk',
    'items' => [
        ['Konsultasi & Room Chat', route_to('workspace.consultation.index'), 'fas fa-comments', $wsTicketBadge, 'bg-info'],
        ['Bookmark Tersimpan', route_to('workspace.bookmarks.index'), 'fas fa-bookmark', null, 'bg-primary'],
        ['Pusat Notifikasi', route_to('workspace.notifications'), 'fas fa-bell', $wsUnreadCount, 'bg-danger'],
        ['Profil & Keamanan', route_to('workspace.profile.index'), 'fas fa-user-shield', null, 'bg-secondary'],
    ],
];
$menuGroups[] = $personalGroup;
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
            <?php if ($wsUnreadCount > 0): ?>
                <span class="badge bg-danger rounded-pill me-2"><?= $wsUnreadCount ?></span>
            <?php endif; ?>
            <i class="fas fa-chevron-down small"></i>
        </button>

        <div class="collapse d-lg-block" id="wsSidebarMenuCollapse">
            <nav aria-label="Menu Workspace">
                <?php foreach ($menuGroups as $group): ?>
                    <section class="workspace-sidebar__group">
                        <h2><?= esc($group['title']) ?></h2>
                        <?php foreach ($group['items'] as [$label, $url, $icon, $badgeCount, $badgeColor]): ?>
                            <?= $renderLink($label, $url, $icon, $isCurrent($url), $badgeCount, $badgeColor) ?>
                        <?php endforeach; ?>
                    </section>
                <?php endforeach; ?>
            </nav>
        </div>
    </div>
</aside>
