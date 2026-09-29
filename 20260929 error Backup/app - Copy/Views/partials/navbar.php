<?php $isLoggedIn = (bool) session()->get('isLoggedIn'); ?>
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
            </div>
        </div>
        <div class="col-auto">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <button type="button" data-theme-toggle class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center">
                    <iconify-icon icon="solar:sun-2-outline" class="text-primary-light text-xl"></iconify-icon>
                </button>

                <?php if ($isLoggedIn): ?>
                <div class="dropdown">
                    <button class="d-flex justify-content-center align-items-center rounded-circle" type="button" data-bs-toggle="dropdown">
                        <img src="<?= base_url('assets/images/user.png') ?>" alt="foto profil" class="w-40-px h-40-px object-fit-cover rounded-circle">
                    </button>
                    <div class="dropdown-menu to-top dropdown-menu-sm">
                        <div class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                            <div>
                                <h6 class="text-lg text-primary-light fw-semibold mb-2"><?= esc(session()->get('username') ?? '-') ?></h6>
                                <span class="text-secondary-light fw-medium text-sm">
                                    <?= esc(implode(', ', array_map(
                                        static fn($slug) => ucwords(str_replace('_', ' ', $slug)),
                                        session()->get('roles') ?? []
                                    ))) ?>
                                </span>
                            </div>
                            <button type="button" class="hover-text-danger">
                                <iconify-icon icon="radix-icons:cross-1" class="icon text-xl"></iconify-icon>
                            </button>
                        </div>
                        <ul class="to-top-list">
                            <li>
                                <a href="<?= route_to('profile') ?>" class="dropdown-item text-black px-0 py-8 hover-bg-transparent d-flex align-items-center gap-3">
                                    <iconify-icon icon="lucide:user-round" class="icon text-xl"></iconify-icon> Profil Saya
                                </a>
                            </li>
                            <li>
                                <form action="<?= route_to('logout') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-danger d-flex align-items-center gap-3 border-0 bg-transparent">
                                    <iconify-icon icon="lucide:power" class="icon text-xl"></iconify-icon> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div><!-- Profile dropdown end -->
                <?php else: ?>
                    <a href="<?= route_to('signin') ?>" class="btn btn-outline-primary btn-sm px-16 py-8 radius-8">Masuk</a>
                    <a href="<?= route_to('signup') ?>" class="btn btn-primary btn-sm px-16 py-8 radius-8">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>