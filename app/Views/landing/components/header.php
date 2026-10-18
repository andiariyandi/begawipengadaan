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
$isHomeView = !empty($isHome);
?>
<header class="main-header">

    <nav class="main-menu">
        <div class="main-menu__wrapper">
            <div class="container">
                <div class="main-menu__wrapper-inner">
                    <div class="main-menu__left">
                        <div class="main-menu__logo">
                            <a href="<?= site_url('/') ?>" aria-label="Beranda BegawiPengadaan">
                                <img src="<?= base_url('assets/main/images/resources/logo-1.png') ?>" alt="BegawiPengadaan" class="main-header__logo-light">
                                <img src="<?= base_url('assets/main/images/resources/logo-4.png') ?>" alt="BegawiPengadaan" class="main-header__logo-dark">
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