<?php
$currentPath = trim(service('uri')->getPath(), '/');
$isActive = static function (string $path) use ($currentPath): bool {
    return trim($path, '/') === $currentPath;
};
$isPrefixActive = static function (string $prefix) use ($currentPath): bool {
    $prefix = trim($prefix, '/');
    return $currentPath === $prefix || str_starts_with($currentPath, $prefix . '/');
};

$roles = (array) session()->get('roles');
$userName = trim((string) (session()->get('name') ?: session()->get('username') ?: 'Pengguna'));
$userEmail = trim((string) session()->get('email'));
$workspaceHome = match (true) {
    in_array('super_admin', $roles, true) || in_array('admin', $roles, true) => route_to('workspace.admin.home'),
    in_array('trainer', $roles, true)                                        => route_to('workspace.trainer.home'),
    in_array('operator', $roles, true)                                       => route_to('workspace.operator.home'),
    default                                                                  => route_to('workspace.home'),
};
$roleLabel = match (true) {
    in_array('super_admin', $roles, true) => 'Super Admin',
    in_array('admin', $roles, true)       => 'Admin',
    in_array('operator', $roles, true)    => 'Operator',
    in_array('trainer', $roles, true)     => 'Pengajar',
    default                               => 'Peserta',
};
$accountInitial = strtoupper(mb_substr($userName, 0, 1, 'UTF-8')) ?: 'U';
$hasDashboardAccess = (bool) session()->get('dashboard_access')
    || in_array('admin', $roles, true)
    || in_array('super_admin', $roles, true);
?>
<header class="main-header workspace-header">
    <!-- Top Contact Bar (Identical to FiStudy Landing) -->
    <div class="main-menu__top">
        <div class="container">
            <div class="main-menu__top-inner">
                <ul class="list-unstyled main-menu__contact-list">
                    <li>
                        <div class="icon"><i class="icon-email"></i></div>
                        <div class="text">
                            <p><a href="mailto:biropbj@lampungprov.go.id">biropbj@lampungprov.go.id</a></p>
                        </div>
                    </li>
                    <li>
                        <div class="icon"><i class="icon-contact"></i></div>
                        <div class="text">
                            <p><a href="tel:+62721123456">+62 851-1777-9783</a></p>
                        </div>
                    </li>
                </ul>
                <ul class="list-unstyled main-menu__contact-list main-menu__location">
                    <li>
                        <div class="icon"><i class="icon-location"></i></div>
                        <div class="text">
                            <p>Biro PBJ Setda Provinsi Lampung</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar (Cloned from FiStudy Landing Foundation) -->
    <nav class="main-menu">
        <div class="main-menu__wrapper">
            <div class="container">
                <div class="main-menu__wrapper-inner">
                    <!-- Left: Logo & Category Box Eksplorasi -->
                    <div class="main-menu__left">
                        <div class="main-menu__logo">
                            <a href="<?= site_url('/') ?>" aria-label="Beranda BegawiPengadaan">
                                <img src="<?= base_url('assets/main/images/resources/logo-1.png') ?>" alt="BegawiPengadaan">
                            </a>
                        </div>

                        <div class="main-menu__category-box">
                            <div class="main-menu__category-btn">
                                <i class="fas fa-th"></i>
                                <p>Eksplorasi</p>
                                <span class="icon-down-arrow"></span>
                            </div>
                            <ul class="list-unstyled main-menu__category-sub-menu">
                                <?php foreach (
                                    [
                                        ['icon' => 'categoyr-two-icon-1.png', 'title' => 'Arena<br>Belajar', 'subtitle' => 'Program dan kelas', 'url' => 'arena-belajar'],
                                        ['icon' => 'categoyr-two-icon-2.png', 'title' => 'Bunga<br>Rampai', 'subtitle' => 'Artikel dan referensi', 'url' => 'bunga-rampai'],
                                        ['icon' => 'categoyr-two-icon-3.png', 'title' => 'Regulasi<br>PBJ', 'subtitle' => 'Dokumen dan aturan', 'url' => 'regulasi'],
                                        ['icon' => 'categoyr-two-icon-4.png', 'title' => 'Event dan<br>Agenda', 'subtitle' => 'Kegiatan mendatang', 'url' => 'event'],
                                        ['icon' => 'categoyr-two-icon-5.png', 'title' => 'Statistik<br>Publik', 'subtitle' => 'Data dan capaian', 'url' => 'statistik'],
                                        ['icon' => 'categoyr-two-icon-6.png', 'title' => 'Verifikasi<br>Sertifikat', 'subtitle' => 'Cek keabsahan', 'url' => 'verifikasi-sertifikat'],
                                    ] as $item
                                ): ?>
                                    <li>
                                        <a href="<?= site_url($item['url']) ?>">
                                            <div class="main-menu__category-icon"><img src="<?= base_url('assets/main/images/icon/' . $item['icon']) ?>" alt=""></div>
                                            <div class="main-menu__category-content">
                                                <h5><?= $item['title'] ?></h5>
                                                <p><?= esc($item['subtitle']) ?></p>
                                            </div>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Center: Main Navigation List (Identical to FiStudy Landing) -->
                    <div class="main-menu__main-menu-box">
                        <a href="#" class="mobile-nav__toggler" aria-label="Toggle Mobile Navigation"><i class="fa fa-bars"></i></a>
                        <ul class="main-menu__list">
                            <li class="<?= $isActive('') ? 'current' : '' ?>"><a href="<?= site_url('/') ?>">Beranda</a></li>
                            <li class="<?= $isActive('tentang') ? 'current' : '' ?>"><a href="<?= site_url('tentang') ?>">Tentang</a></li>
                            <li class="dropdown <?= $isPrefixActive('arena-belajar') ? 'current' : '' ?>">
                                <a href="<?= site_url('arena-belajar') ?>">Arena Belajar</a>
                                <ul class="shadow-box">
                                    <li><a href="<?= site_url('arena-belajar') ?>">Beranda Arena Belajar</a></li>
                                    <li><a href="<?= site_url('arena-belajar/program') ?>">Program Pembelajaran</a></li>
                                    <li><a href="<?= site_url('arena-belajar/course') ?>">Course</a></li>
                                    <li><a href="<?= site_url('arena-belajar/kelas') ?>">Kelas</a></li>
                                    <li><a href="<?= site_url('arena-belajar/pengajar') ?>">Pengajar</a></li>
                                </ul>
                            </li>
                            <li class="dropdown <?= $isPrefixActive('bunga-rampai') ? 'current' : '' ?>">
                                <a href="<?= site_url('bunga-rampai') ?>">Bunga Rampai</a>
                                <ul class="shadow-box">
                                    <li><a href="<?= site_url('bunga-rampai') ?>">Beranda Bunga Rampai</a></li>
                                    <li><a href="<?= site_url('bunga-rampai/artikel') ?>">Artikel</a></li>
                                    <li><a href="<?= site_url('bunga-rampai/faq') ?>">FAQ</a></li>
                                    <li><a href="<?= site_url('bunga-rampai/studi-kasus') ?>">Studi Kasus</a></li>
                                    <li><a href="<?= site_url('bunga-rampai/template-dokumen') ?>">Template Dokumen</a></li>
                                    <li><a href="<?= site_url('bunga-rampai/media') ?>">Media</a></li>
                                    <li><a href="<?= site_url('bunga-rampai/video') ?>">Video</a></li>
                                    <li><a href="<?= site_url('bunga-rampai/galeri') ?>">Galeri</a></li>
                                </ul>
                            </li>
                            <li class="<?= $isPrefixActive('regulasi') ? 'current' : '' ?>"><a href="<?= site_url('regulasi') ?>">Regulasi</a></li>
                            <li class="<?= $isPrefixActive('berita') ? 'current' : '' ?>"><a href="<?= site_url('berita') ?>">Berita</a></li>
                            <li class="<?= $isPrefixActive('event') ? 'current' : '' ?>"><a href="<?= site_url('event') ?>">Event</a></li>
                            <li class="dropdown <?= $isPrefixActive('statistik') || $isPrefixActive('verifikasi-sertifikat') || $isActive('kontak') ? 'current' : '' ?>">
                                <a href="#">Lainnya</a>
                                <ul class="shadow-box">
                                    <li><a href="<?= site_url('statistik') ?>">Statistik</a></li>
                                    <li><a href="<?= site_url('verifikasi-sertifikat') ?>">Verifikasi Sertifikat</a></li>
                                    <li><a href="<?= site_url('kontak') ?>">Kontak</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>

                    <!-- Right: Search Box + Authenticated Workspace User Profile Dropdown -->
                    <div class="main-menu__right">
                        <div class="main-menu__search-cart-box">
                            <div class="main-menu__search-box">
                                <a href="#" class="main-menu__search searcher-toggler-box icon-search" aria-label="Cari"></a>
                            </div>
                        </div>

                        <!-- Workspace Authenticated User Profile Menu (Replaces Login/Register) -->
                        <div class="main-menu__user" id="workspaceUserDropdown">
                            <a href="<?= esc($workspaceHome) ?>" class="main-menu__user-trigger" id="workspaceUserTrigger" aria-haspopup="true" aria-expanded="false" role="button">
                                <span class="main-menu__user-avatar" aria-hidden="true"><?= esc($accountInitial) ?></span>
                                <span class="main-menu__user-name"><?= esc(mb_strimwidth($userName, 0, 16, '…')) ?></span>
                                <span class="icon-down-arrow" aria-hidden="true"></span>
                            </a>

                            <ul class="main-menu__user-menu shadow-box" role="menu" aria-labelledby="workspaceUserTrigger">
                                <li class="main-menu__user-meta">
                                    <strong><?= esc($userName) ?></strong>
                                    <small><?= esc($roleLabel) ?></small>
                                    <?php if (! empty($userEmail)): ?>
                                        <p><?= esc($userEmail) ?></p>
                                    <?php endif; ?>
                                </li>
                                <li role="none">
                                    <a role="menuitem" href="<?= esc($workspaceHome) ?>">
                                        <i class="fas fa-th-large"></i> Dashboard Workspace
                                    </a>
                                </li>
                                <li role="none">
                                    <a role="menuitem" href="<?= route_to('workspace.bookmarks.index') ?>">
                                        <i class="fas fa-bookmark"></i> Bookmark
                                    </a>
                                </li>
                                <li role="none">
                                    <a role="menuitem" href="<?= route_to('workspace.notifications') ?>">
                                        <i class="fas fa-bell"></i> Notifikasi
                                    </a>
                                </li>
                                <li role="none">
                                    <a role="menuitem" href="<?= route_to('workspace.profile.index') ?>">
                                        <i class="fas fa-user-circle"></i> Profil Saya
                                    </a>
                                </li>
                                <?php if ($hasDashboardAccess): ?>
                                    <li role="none">
                                        <a role="menuitem" href="<?= route_to('dashboard.home') ?>">
                                            <i class="fas fa-tachometer-alt"></i> Dashboard Administrasi
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li class="main-menu__user-divider" aria-hidden="true"></li>
                                <li role="none">
                                    <form action="<?= route_to('dashboard.auth.logout') ?>" method="post">
                                        <?php if (! is_cli()): ?>
                                            <?= csrf_field() ?>
                                        <?php else: ?>
                                            <input type="hidden" name="csrf_test_name" value="cli-test-token">
                                        <?php endif; ?>
                                        <button type="submit" role="menuitem" class="main-menu__user-logout-btn">
                                            <i class="fas fa-sign-out-alt"></i> Keluar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var userEl = document.getElementById('workspaceUserDropdown');
        var trigger = document.getElementById('workspaceUserTrigger');
        if (!userEl || !trigger) return;

        // Toggle on click / tap
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var isOpen = userEl.classList.toggle('is-open');
            trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        // Dismiss on click outside
        document.addEventListener('click', function(e) {
            if (!userEl.contains(e.target)) {
                userEl.classList.remove('is-open');
                trigger.setAttribute('aria-expanded', 'false');
            }
        });

        // Dismiss on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && userEl.classList.contains('is-open')) {
                userEl.classList.remove('is-open');
                trigger.setAttribute('aria-expanded', 'false');
                trigger.focus();
            }
        });
    });
</script>