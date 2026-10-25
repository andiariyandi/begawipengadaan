<style>
/* ========================================================================= */
/* RESPONSIVE MOBILE VIEW STYLING FOR NAVBAR HEADER                         */
/* ========================================================================= */
.w-38-px { width: 38px !important; }
.h-38-px { height: 38px !important; }

@media (min-width: 576px) {
    .w-sm-40-px { width: 40px !important; }
    .h-sm-40-px { height: 40px !important; }
}

/* Header dropdown adjustments */
.navbar-header .dropdown-menu.to-top {
    top: 100% !important;
    margin-top: 8px !important;
}

@media (max-width: 575.98px) {
    .navbar-header {
        padding: 0.625rem 0.875rem !important;
        height: auto !important;
        min-height: 3.75rem !important;
        overflow-x: clip !important;
    }

    /* Full-width responsive notifications dropdown for mobile screens */
    #headerNotifDropdown .dropdown-menu.dropdown-menu-lg {
        position: fixed !important;
        top: 4.125rem !important;
        left: 0.625rem !important;
        right: 0.625rem !important;
        width: auto !important;
        max-width: calc(100vw - 1.25rem) !important;
        transform: none !important;
        margin: 0 !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
    }

    /* Compact user profile dropdown for mobile screens */
    .navbar-header .dropdown-menu-sm {
        position: absolute !important;
        max-width: calc(100vw - 1.5rem) !important;
        width: 280px !important;
        right: 0 !important;
        left: auto !important;
    }
}
</style>

<div class="navbar-header">
    <div class="row align-items-center justify-content-between flex-nowrap g-2">
        <div class="col-auto">
            <div class="d-flex align-items-center gap-2 gap-sm-3 flex-nowrap">
                <button type="button" class="sidebar-toggle">
                    <iconify-icon icon="heroicons:bars-3-solid" class="icon text-2xl non-active"></iconify-icon>
                    <iconify-icon icon="iconoir:arrow-right" class="icon text-2xl active"></iconify-icon>
                </button>
                <button type="button" class="sidebar-mobile-toggle w-38-px h-38-px w-sm-40-px h-sm-40-px bg-neutral-100 rounded-circle d-flex d-xl-none justify-content-center align-items-center border-0 p-0 text-primary-light" aria-label="Buka Menu Sidebar">
                    <iconify-icon icon="heroicons:bars-3-solid" class="icon text-xl"></iconify-icon>
                </button>
                
                <!-- Quick Command Palette Trigger (Ctrl + K) - Desktop only (>= lg) -->
                <div class="navbar-search d-none d-lg-flex align-items-center position-relative cursor-pointer" data-bs-toggle="modal" data-bs-target="#adminSpotlightModal" title="Cari menu, data, atau aksi cepat (Ctrl+K)">
                    <input type="text" placeholder="Cari menu, fitur, atau data..." class="cursor-pointer pe-48" readonly>
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                    <kbd class="position-absolute end-0 top-50 translate-middle-y me-12 bg-neutral-200 text-neutral-600 border border-neutral-300 px-6 py-2 rounded text-2xs font-monospace" style="font-size: 10px;">Ctrl+K</kbd>
                </div>

                <!-- Quick Search Trigger - Mobile only (< lg) -->
                <button type="button" class="w-38-px h-38-px w-sm-40-px h-sm-40-px bg-neutral-100 text-secondary-light rounded-circle d-flex d-lg-none justify-content-center align-items-center border-0 p-0 hover-text-primary" data-bs-toggle="modal" data-bs-target="#adminSpotlightModal" title="Cari menu, fitur, atau data (Ctrl+K)">
                    <iconify-icon icon="ion:search-outline" class="text-xl"></iconify-icon>
                </button>
            </div>
        </div>

        <div class="col-auto">
            <div class="d-flex align-items-center gap-2 gap-sm-3 flex-nowrap">
                
                <!-- App Switcher Quick Links -->
                <a href="<?= site_url('workspace') ?>" class="btn btn-sm btn-outline-primary-600 radius-8 px-12 py-6 d-none d-lg-inline-flex align-items-center gap-6 text-xs fw-semibold" title="Buka Ruang Kerja (Workspace)">
                    <iconify-icon icon="solar:laptop-minimalistic-outline" class="text-base"></iconify-icon>
                    <span>Workspace</span>
                </a>
                <a href="<?= site_url('/') ?>" target="_blank" class="btn btn-sm btn-outline-primary-600 radius-8 px-12 py-6 d-none d-xl-inline-flex align-items-center gap-6 text-xs fw-bold header-portal-btn" title="Kunjungi Portal Publik BegawiPengadaan">
                    <iconify-icon icon="solar:globe-outline" class="text-base"></iconify-icon>
                    <span>Portal Publik</span>
                    <iconify-icon icon="solar:square-top-down-linear" class="text-xs"></iconify-icon>
                </a>

                <!-- Theme Toggle Button -->
                <button type="button" data-theme-toggle class="w-38-px h-38-px w-sm-40-px h-sm-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0" title="Beralih Tema Terang / Gelap"></button>

                <?php
                $headerUserId = (int) (session()->get('user_id') ?: 0);
                $authService  = new \App\Services\Identity\AuthorizationService();
                $isSuperAdmin = $authService->isSuperAdmin();
                $roles        = session()->get('roles') ?? [];
                $isAdmin      = $isSuperAdmin 
                    || in_array('super_administrator', $roles, true) 
                    || in_array('administrator', $roles, true);

                $headerNotifService = new \App\Services\NotificationService();
                // Di Lonceng Notifikasi Bar: HANYA tampilkan notifikasi yang BELUM dibaca (unread only)
                $headerNotifications = $isAdmin
                    ? $headerNotifService->getRecentNotifications(5, true)
                    : ($headerUserId > 0 ? $headerNotifService->getUserNotifications($headerUserId, 5, true) : $headerNotifService->getRecentNotifications(5, true));

                $headerUnreadCount = $isAdmin
                    ? $headerNotifService->unreadCountAll()
                    : ($headerUserId > 0 ? $headerNotifService->unreadCount($headerUserId) : $headerNotifService->unreadCountAll());

                $userName     = (string) (session()->get('name') ?: session()->get('username') ?: 'Administrator');
                $userRole     = 'Administrator PBJ';
                if ($isSuperAdmin) {
                    $userRole = 'Super Administrator';
                } else {
                    foreach ((array) $roles as $r) {
                        if (str_starts_with((string) $r, 'admin_')) {
                            $userRole = ucwords(str_replace(['_', '-'], ' ', (string) $r));
                            break;
                        }
                    }
                }
                $userInitials = mb_strtoupper(mb_substr($userName, 0, 2));
                ?>

                <!-- Notifications Dropdown (Live Sync & Newest First) -->
                <div class="dropdown flex-shrink-0" id="headerNotifDropdown">
                    <button id="headerNotifBtn" class="<?= $headerUnreadCount > 0 ? 'has-indicator' : '' ?> w-38-px h-38-px w-sm-40-px h-sm-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center position-relative border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Pemberitahuan Sistem">
                        <iconify-icon icon="iconoir:bell" class="text-primary-light text-xl"></iconify-icon>
                        <span id="headerNotifBadge" class="position-absolute top-0 end-0 badge rounded-pill bg-danger text-white px-6 py-2 <?= $headerUnreadCount > 0 ? '' : 'd-none' ?>" style="font-size: 10px; transform: translate(25%, -25%);">
                            <?= $headerUnreadCount > 99 ? '99+' : $headerUnreadCount ?>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end to-top dropdown-menu-lg p-0">
                        <div class="m-16 py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                            <h6 class="text-lg text-primary-light fw-semibold mb-0">Notifikasi</h6>
                            <div class="d-flex align-items-center gap-2">
                                <span id="headerNotifCountText" class="text-primary-600 fw-semibold text-xs px-8 py-3 rounded-pill bg-base d-flex justify-content-center align-items-center">
                                    <?= $headerUnreadCount ?> baru
                                </span>
                                <form id="headerNotifReadAllForm" action="<?= site_url('dashboard/sistem/notifikasi/baca-semua') ?>" method="post" class="m-0 <?= $headerUnreadCount > 0 ? '' : 'd-none' ?>">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-primary py-2 px-8 text-xs rounded-pill d-inline-flex align-items-center gap-1" title="Tandai semua notifikasi telah dibaca">
                                        <iconify-icon icon="solar:check-read-outline" class="text-xs"></iconify-icon>
                                        Baca Semua
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div id="dashBrowserPushBanner" class="p-2 px-3 bg-neutral-50 border-bottom border-neutral-200 text-xs">
                            <div class="d-flex align-items-center justify-content-between gap-2" id="dashPushBannerContent">
                                <span class="text-secondary-light"><iconify-icon icon="eos-icons:loading" class="align-middle me-1"></iconify-icon> Memeriksa status notifikasi...</span>
                            </div>
                        </div>
                        <div id="headerNotifList" class="max-h-400-px overflow-y-auto scroll-sm pe-4">
                            <?php if (empty($headerNotifications)): ?>
                                <div class="text-center py-24 px-16 text-secondary-light empty-notif-msg">
                                    <iconify-icon icon="solar:bell-off-outline" class="text-3xl text-neutral-400 mb-8 d-block mx-auto"></iconify-icon>
                                    <p class="text-sm mb-0">Belum ada notifikasi baru.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($headerNotifications as $hNotif):
                                    $isUnread   = empty($hNotif['read_at']);
                                    $hCategory = \App\Services\NotificationService::formatCategory($hNotif['type'] ?? 'system');
                                    $hIcon     = $hCategory['icon'] ?? 'solar:bell-bing-outline';
                                    $hData     = !empty($hNotif['data']) ? (is_array($hNotif['data']) ? $hNotif['data'] : json_decode($hNotif['data'], true)) : [];
                                    $hTargetUrl = !empty($hData['url']) ? $hData['url'] : site_url('dashboard/sistem/notifikasi/' . $hNotif['id']);
                                    $timeAgo   = \App\Services\NotificationService::timeAgo($hNotif['created_at'] ?? null);
                                ?>
                                <a href="<?= esc($hTargetUrl) ?>" class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between notif-item-link <?= $isUnread ? 'bg-primary-50/40' : 'bg-neutral-50/50' ?> hover-bg-neutral-100 transition-2" data-notif-id="<?= (int)$hNotif['id'] ?>" data-is-unread="<?= $isUnread ? '1' : '0' ?>">
                                    <span class="w-40-px h-40-px bg-primary-focus text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                        <iconify-icon icon="<?= esc($hIcon) ?>" class="text-xl"></iconify-icon>
                                    </span>
                                    <span class="flex-grow-1 overflow-hidden">
                                        <span class="d-block text-sm fw-semibold text-primary-light text-truncate mb-2"><?= esc($hNotif['title'] ?? 'Notifikasi') ?></span>
                                        <span class="d-block text-xs text-secondary-light line-clamp-2"><?= esc($hNotif['body'] ?? '') ?></span>
                                    </span>
                                    <span class="text-xs text-secondary-light flex-shrink-0 ms-2 text-nowrap"><?= $timeAgo ?></span>
                                </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="text-center py-12 px-16 border-top border-neutral-200">
                            <a href="<?= site_url('dashboard/sistem/notifikasi') ?>" class="text-primary-600 fw-semibold text-md d-inline-flex align-items-center gap-2">
                                <span>Lihat Semua Notifikasi</span>
                                <iconify-icon icon="iconoir:arrow-right" class="text-base"></iconify-icon>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown flex-shrink-0">
                    <button class="d-flex justify-content-center align-items-center rounded-circle border-0 bg-transparent p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="<?= esc($userName) ?>">
                        <span class="w-38-px h-38-px w-sm-40-px h-sm-40-px bg-primary-600 text-white rounded-circle d-flex justify-content-center align-items-center fw-semibold text-sm shadow-xs">
                            <?= esc($userInitials) ?>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end to-top dropdown-menu-sm">
                        <div class="py-12 px-16 radius-8 bg-primary-50 mb-12">
                            <h6 class="text-md text-primary-light fw-semibold mb-2 text-truncate" title="<?= esc($userName) ?>">
                                <?= esc($userName) ?>
                            </h6>
                            <span class="badge bg-primary-600 text-white px-8 py-3 radius-4 text-3xs fw-semibold">
                                <?= esc($userRole) ?>
                            </span>
                        </div>

                        <ul class="to-top-list">
                            <li>
                                <a class="dropdown-item text-primary-light px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3" href="<?= site_url('dashboard/profile') ?>">
                                    <iconify-icon icon="solar:user-linear" class="icon text-xl"></iconify-icon>
                                    Profil Saya
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-primary-light px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3" href="<?= site_url('dashboard/profile/preferences') ?>">
                                    <iconify-icon icon="solar:settings-outline" class="icon text-xl"></iconify-icon>
                                    Preferensi Saya
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-primary-light px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3" href="<?= site_url('dashboard/profile/password') ?>">
                                    <iconify-icon icon="solar:lock-password-outline" class="icon text-xl"></iconify-icon>
                                    Ubah Kata Sandi
                                </a>
                            </li>

                            <li><hr class="dropdown-divider my-8"></li>

                            <!-- Switch to Workspace & Public Portal -->
                            <li>
                                <a class="dropdown-item text-primary-light px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3" href="<?= site_url('workspace') ?>">
                                    <iconify-icon icon="solar:laptop-minimalistic-outline" class="icon text-xl text-primary-600"></iconify-icon>
                                    <span>Ruang Kerja Workspace</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-primary-light px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3" href="<?= site_url('/') ?>" target="_blank">
                                    <iconify-icon icon="solar:globe-outline" class="icon text-xl text-success-main"></iconify-icon>
                                    <span>Kunjungi Portal Publik</span>
                                </a>
                            </li>

                            <li><hr class="dropdown-divider my-8"></li>

                            <li>
                                <a class="dropdown-item text-primary-light px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3" href="<?= site_url('dashboard/sistem/pengaturan') ?>">
                                    <iconify-icon icon="icon-park-outline:setting-two" class="icon text-xl"></iconify-icon>
                                    Pengaturan Sistem
                                </a>
                            </li>

                            <li><hr class="dropdown-divider my-8"></li>

                            <li>
                                <a class="dropdown-item text-danger-main px-0 py-8 hover-bg-transparent hover-text-danger d-flex align-items-center gap-3" href="<?= site_url('dashboard/keluar') ?>">
                                    <iconify-icon icon="lucide:power" class="icon text-xl"></iconify-icon>
                                    Keluar
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Floating Real-Time Push Toast Container for Dashboard -->
<div id="dashPushToastContainer" style="position: fixed; top: 24px; right: 24px; z-index: 999999; display: flex; flex-direction: column; gap: 12px; max-width: 380px; width: calc(100vw - 48px); pointer-events: none;"></div>

<script>
    // Audio Synthesizer for Dashboard Notifications
    function playAdminChime() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.12);
            gain.gain.setValueAtTime(0.15, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.35);
        } catch (e) {}
    }

    // Floating Push Toast for Dashboard
    function showAdminPushToast(notif) {
        const container = document.getElementById('dashPushToastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.style.cssText = "background: #ffffff; border-radius: 14px; box-shadow: 0 10px 30px rgba(15,23,42,0.18), 0 2px 8px rgba(0,0,0,0.06); border-left: 5px solid #487fff; padding: 14px 16px; pointer-events: auto; cursor: pointer; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); transform: translateY(-20px); opacity: 0; display: flex; gap: 12px; align-items: start; font-family: inherit;";

        const safeTitle = (notif.title || 'Notifikasi').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        const safeBody = (notif.body || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        const targetUrl = notif.url || notif.detail_url || '<?= site_url('dashboard/sistem/notifikasi') ?>';

        toast.innerHTML = `
            <span style="width: 36px; height: 36px; border-radius: 50%; background: rgba(72,127,255,0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                <iconify-icon icon="${notif.icon || 'solar:bell-bing-bold'}" style="font-size: 18px; color: #487fff;"></iconify-icon>
            </span>
            <div style="flex-grow: 1; overflow: hidden; min-width: 0;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; margin-bottom: 2px;">
                    <strong style="font-size: 13px; color: #1e293b; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${safeTitle}</strong>
                    <span style="font-size: 10px; color: #94a3b8; white-space: nowrap;">Baru saja</span>
                </div>
                <p style="font-size: 11.5px; color: #475569; margin: 0 0 6px 0; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${safeBody}</p>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span class="badge bg-primary-50 text-primary-600" style="font-size: 9px; padding: 2px 6px; border-radius: 12px;">Notifikasi Baru</span>
                    <span style="font-size: 10.5px; color: #487fff; font-weight: 600;">Lihat <iconify-icon icon="solar:alt-arrow-right-linear" style="font-size: 10px; vertical-align: middle;"></iconify-icon></span>
                </div>
            </div>
        `;

        toast.addEventListener('click', function() {
            if (targetUrl) {
                window.location.href = targetUrl;
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

    function triggerAdminDesktopNotification(item) {
        if (!('Notification' in window) || Notification.permission !== 'granted') return;

        const iconUrl = '<?= base_url('assets/main/images/resources/logo-1.png') ?>';
        const title = item.title || 'BegawiPengadaan Admin';
        const targetUrl = item.url || item.detail_url || '<?= site_url('dashboard/sistem/notifikasi') ?>';
        const options = {
            body: item.body || 'Pemberitahuan baru pada sistem.',
            icon: iconUrl,
            badge: iconUrl,
            tag: 'bp-dash-notif-' + (item.id || Date.now()),
            renotify: true,
            data: { id: item.id, url: targetUrl }
        };

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.ready.then(function(reg) {
                return reg.showNotification(title, options);
            }).catch(function() {
                spawnAdminDesktopNotification(title, options, item, targetUrl);
            });
        } else {
            spawnAdminDesktopNotification(title, options, item, targetUrl);
        }
    }

    function spawnAdminDesktopNotification(title, options, item, targetUrl) {
        try {
            const n = new Notification(title, options);
            n.onclick = function(e) {
                e.preventDefault();
                window.focus();
                if (targetUrl) window.location.href = targetUrl;
                this.close();
            };
        } catch (e) {
            console.debug('Dashboard Notification instantiation fallback:', e);
        }
    }

    // Dynamic UI Update for Dashboard Header Dropdown Banner
    window.updateDashPushBannerUI = function() {
        const container = document.getElementById('dashPushBannerContent');
        if (!container) return;

        if (!('Notification' in window)) {
            container.innerHTML = `
                <span class="text-secondary-light"><iconify-icon icon="solar:volume-loud-outline" class="align-middle me-1 text-info"></iconify-icon> Mode In-App Toast &amp; Suara</span>
                <button type="button" class="btn btn-outline-primary btn-xs py-1 px-8 rounded-pill" onclick="window.dashTestNotification()">Uji</button>
            `;
            return;
        }

        if (Notification.permission === 'granted') {
            container.innerHTML = `
                <span class="text-success-main fw-medium"><iconify-icon icon="solar:check-circle-bold" class="align-middle me-1 text-success-main"></iconify-icon> Notifikasi desktop aktif</span>
                <button type="button" class="btn btn-outline-primary btn-xs py-1 px-8 rounded-pill" onclick="window.dashTestNotification()">Uji</button>
            `;
        } else if (Notification.permission === 'denied') {
            container.innerHTML = `
                <span class="text-danger-main" title="Izin notifikasi diblokir di setelan browser"><iconify-icon icon="solar:close-circle-bold" class="align-middle me-1 text-danger-main"></iconify-icon> Izin diblokir browser</span>
                <button type="button" class="btn btn-outline-secondary btn-xs py-1 px-8 rounded-pill" onclick="window.dashTestNotification()">Uji In-App</button>
            `;
        } else {
            // default
            container.innerHTML = `
                <span class="text-primary-600"><iconify-icon icon="solar:laptop-outline" class="align-middle me-1"></iconify-icon> Notifikasi desktop</span>
                <div class="d-flex gap-1">
                    <button type="button" id="dashBtnEnablePush" class="btn btn-primary btn-xs py-1 px-8 rounded-pill" onclick="window.dashRequestPushPermission()">Aktifkan</button>
                    <button type="button" class="btn btn-outline-secondary btn-xs py-1 px-8 rounded-pill" onclick="window.dashTestNotification()">Uji</button>
                </div>
            `;
        }
    };

    window.dashRequestPushPermission = function() {
        if (!('Notification' in window)) {
            alert('Browser ini tidak mendukung Web Notification API native. Sistem tetap aktif menggunakan In-App Real-Time Toast & Audio.');
            return;
        }

        Notification.requestPermission().then(function(perm) {
            window.updateDashPushBannerUI();
            if (typeof window.updateDashboardPushCard === 'function') {
                window.updateDashboardPushCard();
            }
            if (perm === 'granted') {
                window.dashTestNotification({
                    id: 999999,
                    title: 'Notifikasi Desktop Admin Diaktifkan!',
                    body: 'BegawiPengadaan sekarang dapat mengirimkan pemberitahuan sistem langsung ke desktop Anda.',
                    icon: 'solar:check-circle-bold',
                    url: '<?= site_url('dashboard/sistem/notifikasi') ?>'
                });
            }
        }).catch(function(err) {
            console.warn('Gagal meminta izin notifikasi admin:', err);
        });
    };

    window.dashTestNotification = function(customData) {
        const sample = customData || {
            id: 999999,
            title: 'Uji Coba Notifikasi Dashboard',
            body: 'Selamat! Audio chime harmonik, in-app floating toast, dan notifikasi desktop dashboard berhasil berjalan.',
            icon: 'solar:tuning-square-2-bold',
            url: '<?= site_url('dashboard/sistem/notifikasi') ?>'
        };
        playAdminChime();
        showAdminPushToast(sample);
        triggerAdminDesktopNotification(sample);
    };

window.refreshNotificationBell = function () {
    fetch('<?= site_url('dashboard/sistem/notifikasi-api/feed') ?>', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (!data || !data.success) return;
        
        const count = parseInt(data.unread, 10) || 0;
        const badge = document.getElementById('headerNotifBadge');
        const countText = document.getElementById('headerNotifCountText');
        const btn = document.getElementById('headerNotifBtn');
        const readAllForm = document.getElementById('headerNotifReadAllForm');
        const listContainer = document.getElementById('headerNotifList');

        // Update badge
        if (badge) {
            badge.textContent = count > 99 ? '99+' : count;
            if (count > 0) {
                badge.classList.remove('d-none');
                btn?.classList.add('has-indicator');
            } else {
                badge.classList.add('d-none');
                btn?.classList.remove('has-indicator');
            }
        }

        // Update text counter inside dropdown
        if (countText) {
            countText.textContent = count + ' baru';
        }

        // Update read all button
        if (readAllForm) {
            if (count > 0) {
                readAllForm.classList.remove('d-none');
            } else {
                readAllForm.classList.add('d-none');
            }
        }

        // Render newest items
        if (listContainer && Array.isArray(data.items)) {
            if (data.items.length === 0) {
                listContainer.innerHTML = `
                    <div class="text-center py-24 px-16 text-secondary-light empty-notif-msg">
                        <iconify-icon icon="solar:bell-off-outline" class="text-3xl text-neutral-400 mb-8 d-block mx-auto"></iconify-icon>
                        <p class="text-sm mb-0">Belum ada notifikasi baru.</p>
                    </div>
                `;
            } else {
                if (window.__lastAdminTopNotifId > 0 && data.items[0].id > window.__lastAdminTopNotifId) {
                    try {
                        const AudioCtx = window.AudioContext || window.webkitAudioContext;
                        if (AudioCtx) {
                            const ctx = new AudioCtx();
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();
                            osc.type = 'sine';
                            osc.frequency.setValueAtTime(587.33, ctx.currentTime);
                            osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.12);
                            gain.gain.setValueAtTime(0.15, ctx.currentTime);
                            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
                            osc.connect(gain);
                            gain.connect(ctx.destination);
                            osc.start();
                            osc.stop(ctx.currentTime + 0.35);
                        }
                    } catch (e) {}

                    // Trigger In-App Floating Toast & Native Browser Desktop Notification
                    showAdminPushToast(data.items[0]);
                    triggerAdminDesktopNotification(data.items[0]);
                }
                window.__lastAdminTopNotifId = data.items[0].id;

                let html = '';
                data.items.forEach(item => {
                    const bgClass = item.is_read ? 'bg-neutral-50/50' : 'bg-primary-50/40';
                    const safeTitle = (item.title || 'Notifikasi').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    const safeBody = (item.body || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    const targetUrl = item.url || item.detail_url;
                    html += `
                        <a href="${targetUrl}" class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between notif-item-link ${bgClass} hover-bg-neutral-100 transition-2" data-notif-id="${item.id}" data-is-unread="${item.is_read ? '0' : '1'}">
                            <span class="w-40-px h-40-px bg-primary-focus text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                <iconify-icon icon="${item.icon || 'solar:bell-bing-outline'}" class="text-xl"></iconify-icon>
                            </span>
                            <span class="flex-grow-1 overflow-hidden">
                                <span class="d-block text-sm fw-semibold text-primary-light text-truncate mb-2">${safeTitle}</span>
                                <span class="d-block text-xs text-secondary-light line-clamp-2">${safeBody}</span>
                            </span>
                            <span class="text-xs text-secondary-light flex-shrink-0 ms-2 text-nowrap">${item.time_ago || '-'}</span>
                        </a>
                    `;
                });
                listContainer.innerHTML = html;
            }
        }
    })
    .catch(err => console.error('Gagal memperbarui notifikasi bell:', err));
};

document.addEventListener('DOMContentLoaded', function () {
    // Registrasi Service Worker untuk browser desktop notification
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('<?= base_url('sw.js') ?>', { scope: '/' })
            .catch(function(err) { console.debug('SW registration skipped:', err); });
    }

    // Inisialisasi status banner notifikasi dropdown
    window.updateDashPushBannerUI();

    const dropdownEl = document.getElementById('headerNotifDropdown');
    if (dropdownEl) {
        dropdownEl.addEventListener('show.bs.dropdown', function () {
            window.refreshNotificationBell();
        });
    }

    // Tangani klik pada salah satu notifikasi di bar dropdown: auto mark as read
    document.addEventListener('click', function (e) {
        const notifLink = e.target.closest('#headerNotifList .notif-item-link');
        if (notifLink) {
            const notifId = notifLink.dataset.notifId;
            if (notifId) {
                fetch('<?= site_url('dashboard/sistem/notifikasi') ?>/' + notifId + '/baca', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: '<?= csrf_token() ?>=' + encodeURIComponent('<?= csrf_hash() ?>')
                }).catch(() => {});
            }
        }
    });

    // Tangani aksi 'Baca Semua' di header dropdown secara instan via AJAX
    const readAllForm = document.getElementById('headerNotifReadAllForm');
    if (readAllForm) {
        readAllForm.addEventListener('submit', function (e) {
            e.preventDefault();
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: '<?= csrf_token() ?>=' + encodeURIComponent('<?= csrf_hash() ?>')
            })
            .then(res => res.json())
            .then(() => {
                window.refreshNotificationBell();
                // Jika sedang di halaman log notifikasi, refresh halaman agar tabel sinkron
                if (window.location.pathname.includes('/notifikasi')) {
                    window.location.reload();
                }
            })
            .catch(() => {
                readAllForm.submit();
            });
        });
    }

    // Polling setiap 30 detik untuk memastikan bar notifikasi selalu ter-update terbaru
    setInterval(function () {
        window.refreshNotificationBell();
    }, 30000);
});
</script>
