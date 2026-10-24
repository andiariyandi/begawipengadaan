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
                                                    <a href="<?= esc($targetUrl) ?>" class="list-group-item list-group-item-action notif-item landing-notif-link p-2 px-3 border-bottom <?= $isNUnread ? 'is-unread bg-primary-subtle bg-opacity-10' : '' ?> d-flex gap-2 align-items-start" data-notif-id="<?= (int)$n['id'] ?>">
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

<?php if ($isLoggedIn): ?>
<!-- Real-Time Push Notification Engine (Workspace & Landing) -->
<div id="bpPushToastContainer" style="position: fixed; top: 24px; right: 24px; z-index: 999999; display: flex; flex-direction: column; gap: 12px; max-width: 380px; width: calc(100vw - 48px); pointer-events: none;"></div>

<script>
(function() {
    let lastUnreadCount = <?= (int) $userUnreadNotifCount ?>;
    let lastTopNotifId = <?= !empty($latestNotifs[0]['id']) ? (int) $latestNotifs[0]['id'] : 0 ?>;
    const originalDocumentTitle = document.title;

    // Harmonized Chime Audio Synthesizer (Zero External Dependencies)
    function playNotificationChime() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5 note
            osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.12); // A5 note
            gain.gain.setValueAtTime(0.15, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.35);
        } catch (e) {}
    }

    // Floating Push Toast Popup
    function showPushToast(notif) {
        const container = document.getElementById('bpPushToastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.style.cssText = "background: #ffffff; border-radius: 14px; box-shadow: 0 10px 30px rgba(8,47,92,0.18), 0 2px 8px rgba(0,0,0,0.06); border-left: 5px solid #0c5aa6; padding: 14px 16px; pointer-events: auto; cursor: pointer; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); transform: translateY(-20px); opacity: 0; display: flex; gap: 12px; align-items: start; font-family: inherit;";

        const iconClass = notif.fa || 'fas fa-bell text-primary';
        toast.innerHTML = `
            <span style="width: 36px; height: 36px; border-radius: 50%; background: rgba(12,90,166,0.08); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                <i class="${iconClass}" style="font-size: 15px;"></i>
            </span>
            <div style="flex-grow: 1; overflow: hidden; min-width: 0;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; margin-bottom: 2px;">
                    <strong style="font-size: 13px; color: #082f5c; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${escapeHtml(notif.title)}</strong>
                    <span style="font-size: 10px; color: #8c98a4; white-space: nowrap;">Baru saja</span>
                </div>
                <p style="font-size: 11.5px; color: #556270; margin: 0 0 6px 0; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${escapeHtml(notif.body || '')}</p>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span class="badge ${notif.badge || 'bg-primary-subtle text-primary'}" style="font-size: 9px; padding: 2px 6px; border-radius: 12px;">Notifikasi Baru</span>
                    <span style="font-size: 10.5px; color: #0c5aa6; font-weight: 600;">Lihat <i class="fas fa-chevron-right" style="font-size: 8px;"></i></span>
                </div>
            </div>
        `;

        toast.addEventListener('click', function() {
            if (notif.id) {
                markSingleAsRead(notif.id);
            }
            if (notif.url) {
                window.location.href = notif.url;
            }
        });

        container.appendChild(toast);

        requestAnimationFrame(() => {
            toast.style.transform = 'translateY(0)';
            toast.style.opacity = '1';
        });

        setTimeout(() => {
            toast.style.transform = 'translateY(-20px)';
            toast.style.opacity = '0';
            setTimeout(() => {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, 300);
        }, 6500);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function markSingleAsRead(notifId) {
        fetch('<?= site_url('workspace/notifikasi') ?>/' + notifId + '/baca', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: '<?= csrf_token() ?>=' + encodeURIComponent('<?= csrf_hash() ?>')
        }).catch(() => {});
    }

    // Real-Time Poller Function
    window.refreshLandingNotificationBell = function() {
        fetch('<?= site_url('workspace/notifikasi-api/feed') ?>', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (!data || !data.success) return;

            const unread = parseInt(data.unread, 10) || 0;
            const badgeEl = document.querySelector('.main-menu__notif-badge');
            const notifBtn = document.querySelector('.main-menu__notif-btn');

            if (unread > 0) {
                if (badgeEl) {
                    badgeEl.textContent = unread > 99 ? '99+' : unread;
                    badgeEl.style.display = 'inline-block';
                } else if (notifBtn) {
                    const newBadge = document.createElement('span');
                    newBadge.className = 'main-menu__notif-badge';
                    newBadge.textContent = unread > 99 ? '99+' : unread;
                    notifBtn.appendChild(newBadge);
                }
            } else if (badgeEl) {
                badgeEl.style.display = 'none';
            }

            // Check if there is an incoming new notification
            if (data.items && data.items.length > 0) {
                const newest = data.items[0];
                if (lastTopNotifId > 0 && newest.id > lastTopNotifId) {
                    playNotificationChime();
                    showPushToast(newest);
                }
                lastTopNotifId = newest.id;
            }

            // Tab title flashing if document is hidden and has unread
            if (unread > 0 && document.hidden) {
                document.title = `(${unread}) Notifikasi Baru | BegawiPengadaan`;
            } else if (!document.hidden) {
                document.title = originalDocumentTitle;
            }

            lastUnreadCount = unread;
        })
        .catch(() => {});
    };

    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                document.title = originalDocumentTitle;
                window.refreshLandingNotificationBell();
            }
        });

        // Intercept click on notification items in dropdown to mark as read immediately
        document.addEventListener('click', function(e) {
            const notifLink = e.target.closest('.landing-notif-link');
            if (notifLink) {
                const notifId = notifLink.getAttribute('data-notif-id');
                if (notifId) {
                    markSingleAsRead(notifId);
                }
            }
        });

        // Polling interval 30 detik
        setInterval(window.refreshLandingNotificationBell, 30000);
    });
})();
</script>
<?php endif; ?>