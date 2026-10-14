<div class="navbar-header">
    <div class="row align-items-center justify-content-between">
        <div class="col-auto">
            <div class="d-flex flex-wrap align-items-center gap-4">
                <button type="button" class="sidebar-toggle">
                    <iconify-icon icon="heroicons:bars-3-solid" class="icon text-2xl non-active"></iconify-icon>
                    <iconify-icon icon="iconoir:arrow-right" class="icon text-2xl active"></iconify-icon>
                </button>
                <button type="button" class="sidebar-mobile-toggle">
                    <iconify-icon icon="heroicons:bars-3-solid" class="icon"></iconify-icon>
                </button>
                <form class="navbar-search" action="#" method="get">
                    <input type="text" name="search" placeholder="Cari menu atau data">
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
        </div>

        <div class="col-auto">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <button type="button" data-theme-toggle class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"></button>

                <?php
                $headerUserId = (int) (session()->get('user_id') ?: 0);
                $headerNotifService = new \App\Services\NotificationService();
                $headerNotifications = $headerUserId > 0
                    ? $headerNotifService->getUserNotifications($headerUserId, 5)
                    : [];
                if (empty($headerNotifications)) {
                    $headerNotifications = $headerNotifService->getRecentNotifications(5);
                }
                $headerUnreadCount = $headerUserId > 0
                    ? $headerNotifService->unreadCount($headerUserId)
                    : $headerNotifService->unreadCountAll();
                ?>
                <div class="dropdown">
                    <button class="<?= $headerUnreadCount > 0 ? 'has-indicator' : '' ?> w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Pemberitahuan Sistem">
                        <iconify-icon icon="iconoir:bell" class="text-primary-light text-xl"></iconify-icon>
                        <?php if ($headerUnreadCount > 0): ?>
                            <span class="position-absolute top-0 end-0 badge rounded-pill bg-danger text-white px-6 py-2" style="font-size: 10px; transform: translate(25%, -25%);">
                                <?= $headerUnreadCount > 99 ? '99+' : $headerUnreadCount ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    <div class="dropdown-menu to-top dropdown-menu-lg p-0">
                        <div class="m-16 py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                            <h6 class="text-lg text-primary-light fw-semibold mb-0">Notifikasi</h6>
                            <span class="text-primary-600 fw-semibold text-sm px-10 py-4 rounded-pill bg-base d-flex justify-content-center align-items-center">
                                <?= $headerUnreadCount ?> belum dibaca
                            </span>
                        </div>
                        <div class="max-h-400-px overflow-y-auto scroll-sm pe-4">
                            <?php if (empty($headerNotifications)): ?>
                                <div class="text-center py-24 px-16 text-secondary-light">
                                    <iconify-icon icon="solar:bell-off-outline" class="text-3xl text-neutral-400 mb-8 d-block mx-auto"></iconify-icon>
                                    <p class="text-sm mb-0">Belum ada notifikasi baru.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($headerNotifications as $hNotif):
                                    $isUnread = empty($hNotif['read_at']);
                                    $iconData = match ($hNotif['type'] ?? 'system') {
                                        'broadcast'          => ['icon' => 'solar:megaphone-outline', 'bg' => 'bg-primary-focus text-primary-600'],
                                        'certificate_issued' => ['icon' => 'solar:verified-check-outline', 'bg' => 'bg-success-focus text-success-main'],
                                        'account_approved'   => ['icon' => 'solar:user-check-outline', 'bg' => 'bg-success-focus text-success-main'],
                                        'account_rejected'   => ['icon' => 'solar:user-cross-outline', 'bg' => 'bg-danger-focus text-danger-main'],
                                        'assignment_graded'  => ['icon' => 'solar:diploma-outline', 'bg' => 'bg-info-focus text-info-main'],
                                        default              => ['icon' => 'solar:bell-bing-outline', 'bg' => 'bg-warning-focus text-warning-main'],
                                    };
                                    $timeAgo = \App\Services\NotificationService::timeAgo($hNotif['created_at'] ?? null);
                                ?>
                                <a href="<?= site_url('dashboard/sistem/notifikasi') ?>" class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between <?= $isUnread ? 'bg-primary-50/40' : 'bg-neutral-50/50' ?> hover-bg-neutral-100 transition-2">
                                    <span class="w-40-px h-40-px <?= $iconData['bg'] ?> rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                        <iconify-icon icon="<?= $iconData['icon'] ?>" class="text-xl"></iconify-icon>
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

                <div class="dropdown">
                    <button class="d-flex justify-content-center align-items-center rounded-circle" type="button" data-bs-toggle="dropdown">
                        <img src="<?= base_url('assets/dashboard/images/user.png') ?>" alt="Admin BegawiPengadaan" class="w-40-px h-40-px object-fit-cover rounded-circle">
                    </button>
                    <div class="dropdown-menu to-top dropdown-menu-sm">
                        <div class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                            <div>
                                <h6 class="text-lg text-primary-light fw-semibold mb-2">
                                    Administrator
                                </h6>
                                <span class="text-secondary-light fw-medium text-sm">
                                    BegawiPengadaan
                                </span>
                            </div>
                        </div>

                        <ul class="to-top-list">
                            <li>
                                <a
                                    class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3"
                                    href="<?= site_url('dashboard/profile') ?>">
                                    <iconify-icon
                                        icon="solar:user-linear"
                                        class="icon text-xl"></iconify-icon>
                                    Profil Saya
                                </a>
                            </li>

                            <li>
                                <a
                                    class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3"
                                    href="<?= site_url('dashboard/profile/preferences') ?>">
                                    <iconify-icon
                                        icon="solar:settings-outline"
                                        class="icon text-xl"></iconify-icon>
                                    Preferensi Saya
                                </a>
                            </li>

                            <li>
                                <a
                                    class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3"
                                    href="<?= site_url('dashboard/profile/password') ?>">
                                    <iconify-icon
                                        icon="solar:lock-password-outline"
                                        class="icon text-xl"></iconify-icon>
                                    Ubah Kata Sandi
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider my-8">
                            </li>

                            <li>
                                <a
                                    class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3"
                                    href="<?= site_url('dashboard/sistem/pengaturan') ?>">
                                    <iconify-icon
                                        icon="icon-park-outline:setting-two"
                                        class="icon text-xl"></iconify-icon>
                                    Pengaturan Sistem
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider my-8">
                            </li>

                            <li>
                                <a
                                    class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-danger d-flex align-items-center gap-3"
                                    href="<?= site_url('dashboard/keluar') ?>">
                                    <iconify-icon
                                        icon="lucide:power"
                                        class="icon text-xl"></iconify-icon>
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