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

                <div class="dropdown">
                    <button class="has-indicator w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center" type="button" data-bs-toggle="dropdown">
                        <iconify-icon icon="iconoir:bell" class="text-primary-light text-xl"></iconify-icon>
                    </button>
                    <div class="dropdown-menu to-top dropdown-menu-lg p-0">
                        <div class="m-16 py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                            <h6 class="text-lg text-primary-light fw-semibold mb-0">Notifikasi</h6>
                            <span class="text-primary-600 fw-semibold text-lg w-40-px h-40-px rounded-circle bg-base d-flex justify-content-center align-items-center">3</span>
                        </div>
                        <div class="max-h-400-px overflow-y-auto scroll-sm pe-4">
                            <a href="#" class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                                <span class="w-44-px h-44-px bg-warning-focus text-warning-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                    <iconify-icon icon="solar:clock-circle-outline" class="icon text-xxl"></iconify-icon>
                                </span>
                                <span class="flex-grow-1">
                                    <span class="d-block text-md fw-semibold mb-4">Persetujuan menunggu ditinjau</span>
                                    <span class="d-block text-sm text-secondary-light">Terdapat data yang memerlukan tindak lanjut.</span>
                                </span>
                                <span class="text-sm text-secondary-light flex-shrink-0">Baru</span>
                            </a>
                            <a href="#" class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between bg-neutral-50">
                                <span class="w-44-px h-44-px bg-success-focus text-success-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                    <iconify-icon icon="solar:verified-check-outline" class="icon text-xxl"></iconify-icon>
                                </span>
                                <span class="flex-grow-1">
                                    <span class="d-block text-md fw-semibold mb-4">Profil berhasil diverifikasi</span>
                                    <span class="d-block text-sm text-secondary-light">Satu profil pengguna telah diperbarui.</span>
                                </span>
                                <span class="text-sm text-secondary-light flex-shrink-0">10 mnt</span>
                            </a>
                        </div>
                        <div class="text-center py-12 px-16">
                            <a href="<?= site_url('dashboard/sistem/notifikasi') ?>" class="text-primary-600 fw-semibold text-md">Lihat Semua Notifikasi</a>
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