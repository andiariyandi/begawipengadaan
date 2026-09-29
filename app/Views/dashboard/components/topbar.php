<header class="navbar-header">
    <div class="row align-items-center justify-content-between">
        <div class="col-auto">
            <div class="d-flex flex-wrap align-items-center gap-4">
                <button type="button" class="sidebar-toggle"><iconify-icon icon="heroicons:bars-3-solid" class="icon text-2xl non-active"></iconify-icon><iconify-icon icon="iconoir:arrow-right" class="icon text-2xl active"></iconify-icon></button>
                <button type="button" class="sidebar-mobile-toggle"><iconify-icon icon="heroicons:bars-3-solid" class="icon"></iconify-icon></button>
            </div>
        </div>

        <div class="col-auto">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <button type="button" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Notifikasi" class="btn btn-light d-flex justify-content-center align-items-center rounded-circle"><iconify-icon icon="iconoir:bell" class="text-xl"></iconify-icon></button>
                <div class="dropdown">
                    <a class="d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?= base_url('assets/dashboard/images/user.png') ?>" alt="Administrator" class="w-40-px h-40-px rounded-circle">
                        <span class="ms-2 d-sm-block d-none"><span class="fw-semibold d-block">Administrator Demo</span><span class="text-secondary text-sm">Super Admin</span></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= site_url('dashboard/profil') ?>">Profil</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('dashboard/pengaturan') ?>">Pengaturan</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="<?= site_url('dashboard/keluar') ?>" method="post"><button class="dropdown-item text-danger" type="submit">Keluar</button></form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>