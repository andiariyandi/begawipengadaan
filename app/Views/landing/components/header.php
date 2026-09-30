<?php
$currentPath = trim(service('uri')->getPath(), '/');
$isActive = static function (string $path) use ($currentPath): bool {
    return trim($path, '/') === $currentPath;
};
$isPrefixActive = static function (string $prefix) use ($currentPath): bool {
    $prefix = trim($prefix, '/');
    return $currentPath === $prefix || str_starts_with($currentPath, $prefix . '/');
};
?>
<header class="main-header">
    <div class="main-menu__top">
        <div class="container">
            <div class="main-menu__top-inner">
                <ul class="list-unstyled main-menu__contact-list">
                    <li>
                        <div class="icon"><i class="icon-email"></i></div>
                        <div class="text"><p><a href="mailto:pbj@lampungprov.go.id">pbj@lampungprov.go.id</a></p></div>
                    </li>
                    <li>
                        <div class="icon"><i class="icon-contact"></i></div>
                        <div class="text"><p><a href="tel:+62721123456">(0721) 123456</a></p></div>
                    </li>
                </ul>
                <ul class="list-unstyled main-menu__contact-list main-menu__location">
                    <li>
                        <div class="icon"><i class="icon-location"></i></div>
                        <div class="text"><p>Biro PBJ Pemerintah Provinsi Lampung</p></div>
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
                                <?php foreach ([
                                    ['icon' => 'categoyr-two-icon-1.png', 'title' => 'Arena<br>Belajar', 'subtitle' => 'Program dan kelas', 'url' => 'arena-belajar'],
                                    ['icon' => 'categoyr-two-icon-2.png', 'title' => 'Bunga<br>Rampai', 'subtitle' => 'Artikel dan referensi', 'url' => 'bunga-rampai'],
                                    ['icon' => 'categoyr-two-icon-3.png', 'title' => 'Regulasi<br>PBJ', 'subtitle' => 'Dokumen dan aturan', 'url' => 'regulasi'],
                                    ['icon' => 'categoyr-two-icon-4.png', 'title' => 'Event dan<br>Agenda', 'subtitle' => 'Kegiatan mendatang', 'url' => 'event'],
                                    ['icon' => 'categoyr-two-icon-5.png', 'title' => 'Statistik<br>Publik', 'subtitle' => 'Data dan capaian', 'url' => 'statistik'],
                                    ['icon' => 'categoyr-two-icon-6.png', 'title' => 'Verifikasi<br>Sertifikat', 'subtitle' => 'Cek keabsahan', 'url' => 'verifikasi-sertifikat'],
                                ] as $item): ?>
                                    <li>
                                        <a href="<?= site_url($item['url']) ?>">
                                            <div class="main-menu__category-icon"><img src="<?= base_url('assets/main/images/icon/' . $item['icon']) ?>" alt=""></div>
                                            <div class="main-menu__category-content"><h5><?= $item['title'] ?></h5><p><?= esc($item['subtitle']) ?></p></div>
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
                        <div class="main-menu__btn-boxes">
                            <div class="main-menu__btn-box-1"><a href="<?= site_url('dashboard/masuk') ?>" class="thm-btn">Masuk</a></div>
                            <div class="main-menu__btn-box-2"><a href="<?= site_url('dashboard/daftar') ?>" class="thm-btn">Daftar</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
