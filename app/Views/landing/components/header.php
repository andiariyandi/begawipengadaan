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
$workspaceHome = route_to('workspace.home');
$roleLabel = match (true) {
    in_array('super_admin', $roles, true) => 'Super Admin',
    in_array('admin', $roles, true) => 'Admin',
    in_array('operator', $roles, true) => 'Operator',
    in_array('evaluator', $roles, true) => 'Evaluator',
    in_array('trainer', $roles, true) => 'Pengajar',
    default => 'Peserta',
};
$accountInitial = strtoupper(substr($userName, 0, 1));
$hasDashboardAccess = (bool) session()->get('dashboard_access')
    || in_array('admin', $roles, true)
    || in_array('super_admin', $roles, true);
?>
<header class="main-header">
    <style>
        .main-menu__user {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .main-menu__user-trigger {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(8, 47, 92, .08);
            color: #082f5c;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            line-height: 1;
            transition: background-color .15s ease;
            white-space: nowrap;
        }

        .main-menu__user-trigger:hover,
        .main-menu__user:hover .main-menu__user-trigger,
        .main-menu__user.is-open .main-menu__user-trigger {
            background: rgba(8, 47, 92, .16);
            color: #082f5c;
        }

        .main-menu__user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #ff6b35;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            line-height: 1;
        }

        .main-menu__user-name {
            max-width: 130px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
        }

        .main-menu__user-trigger .icon-down-arrow {
            font-size: 11px;
            opacity: .7;
        }

        .main-menu__user-menu {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            min-width: 240px;
            margin: 0;
            padding: 6px;
            list-style: none;
            background: #ffffff;
            border: 1px solid #e6ebf2;
            border-radius: 12px;
            box-shadow: 0 14px 40px rgba(8, 47, 92, .12);
            visibility: hidden;
            opacity: 0;
            transform: translateY(-4px);
            transition: opacity .15s ease, transform .15s ease, visibility 0s linear .15s;
            z-index: 1000;
        }

        .main-menu__user:hover .main-menu__user-menu,
        .main-menu__user.is-open .main-menu__user-menu {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
            transition: opacity .15s ease, transform .15s ease, visibility 0s;
        }

        .main-menu__user-menu>li {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .main-menu__user-menu a,
        .main-menu__user-menu button {
            display: block;
            width: 100%;
            text-align: left;
            background: transparent;
            border: 0;
            padding: 9px 14px;
            font-size: 14px;
            color: #42526b;
            text-decoration: none;
            border-radius: 6px;
            cursor: pointer;
            font-family: inherit;
        }

        .main-menu__user-menu a:hover,
        .main-menu__user-menu button:hover {
            background: #eef6ff;
            color: #082f5c;
        }

        .main-menu__user-meta {
            padding: 12px 14px 10px;
            border-bottom: 1px solid #edf0f5;
            margin: 0 0 6px;
            list-style: none;
        }

        .main-menu__user-meta strong {
            display: block;
            color: #082f5c;
            font-size: 14px;
            font-weight: 700;
        }

        .main-menu__user-meta small {
            display: block;
            color: #8390a3;
            font-size: 12px;
            margin-top: 2px;
        }

        .main-menu__user-divider {
            height: 1px;
            background: #edf0f5;
            margin: 6px 8px;
            list-style: none;
        }

        .main-menu__user-menu form {
            margin: 0;
            padding: 0;
        }

        @media (max-width: 991px) {
            .main-menu__user-name {
                display: none;
            }

            .main-menu__user-trigger {
                padding: 4px;
            }

            .main-menu__user-menu {
                right: 0;
            }
        }
    </style>
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

    <nav class="main-menu">
        <div class="main-menu__wrapper">
            <div class="container">
                <div class="main-menu__wrapper-inner">
                    <div class="main-menu__left">
                        <div class="main-menu__logo">
                            <a href="<?= site_url('/') ?>" aria-label="Beranda BegawiPBJ">
                                <img src="<?= base_url('assets/main/images/resources/logo-1.png') ?>" alt="BegawiPBJ">
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

                    <div class="main-menu__main-menu-box">
                        <a href="#" class="mobile-nav__toggler"><i class="fa fa-bars"></i></a>
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

                    <div class="main-menu__right">
                        <div class="main-menu__search-cart-box">
                            <div class="main-menu__search-box"><a href="#" class="main-menu__search searcher-toggler-box icon-search" aria-label="Cari"></a></div>
                        </div>
                        <?php if (! $isLoggedIn): ?>
                            <div class="main-menu__btn-boxes">
                                <div class="main-menu__btn-box-1"><a href="<?= route_to('dashboard.auth.login') ?>" class="thm-btn">Masuk</a></div>
                                <div class="main-menu__btn-box-2"><a href="<?= route_to('dashboard.auth.register') ?>" class="thm-btn">Daftar</a></div>
                            </div>
                        <?php else: ?>
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
                                    <li role="none"><a role="menuitem" href="<?= route_to('workspace.bookmarks.index') ?>">Bookmark</a></li>
                                    <li role="none"><a role="menuitem" href="<?= route_to('workspace.notifications') ?>">Notifikasi</a></li>
                                    <li role="none"><a role="menuitem" href="<?= route_to('profile.index') ?>">Profil Saya</a></li>
                                    <?php if ($hasDashboardAccess): ?>
                                        <li role="none"><a role="menuitem" href="<?= route_to('dashboard.home') ?>">Dashboard Administrasi</a></li>
                                    <?php endif; ?>
                                    <li class="main-menu__user-divider" aria-hidden="true"></li>
                                    <li role="none">
                                        <form action="<?= route_to('dashboard.auth.logout') ?>" method="post">
                                            <?= csrf_field() ?>
                                            <button type="submit" role="menuitem">Keluar</button>
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