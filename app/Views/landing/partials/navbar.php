<?php
$currentUri = service('uri')->getPath();
$isActive = static function (string $path) use ($currentUri): bool {
    return trim($currentUri, '/') === trim($path, '/');
};
$isPrefixActive = static function (string $prefix) use ($currentUri): bool {
    return str_starts_with(trim($currentUri, '/'), trim($prefix, '/'));
};
?>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top py-14">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-10 me-xl-5" href="<?= site_url('/') ?>">
            <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-3 fw-bold" style="width: 42px; height: 42px;">B</span>
            <span class="lh-sm"><span class="d-block font-heading fw-bold text-dark">BegawiPBJ</span><small class="text-muted-landing">Provinsi Lampung</small></span>
        </a>

        <button class="btn d-lg-none border-0 p-2" type="button" data-mobile-menu-toggle aria-label="Buka menu">
            <iconify-icon icon="solar:hamburger-menu-linear" class="text-3xl"></iconify-icon>
        </button>

        <div class="collapse navbar-collapse" id="landingNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link px-lg-12 <?= $isActive('') ? 'text-primary fw-semibold' : '' ?>" href="<?= site_url('/') ?>">Beranda</a></li>
                <li class="nav-item"><a class="nav-link px-lg-12 <?= $isActive('tentang') ? 'text-primary fw-semibold' : '' ?>" href="<?= site_url('tentang') ?>">Tentang</a></li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-lg-12 <?= $isPrefixActive('arena-belajar') ? 'text-primary fw-semibold' : '' ?>" href="#" role="button" data-bs-toggle="dropdown">Arena Belajar</a>
                    <ul class="dropdown-menu border-0 shadow-sm rounded-3 p-2">
                        <li><a class="dropdown-item rounded-2 py-2" href="<?= site_url('arena-belajar') ?>">Beranda Arena Belajar</a></li>
                        <li><a class="dropdown-item rounded-2 py-2" href="<?= site_url('arena-belajar/program') ?>">Program Pembelajaran</a></li>
                        <li><a class="dropdown-item rounded-2 py-2" href="<?= site_url('arena-belajar/course') ?>">Course</a></li>
                        <li><a class="dropdown-item rounded-2 py-2" href="<?= site_url('arena-belajar/kelas') ?>">Kelas</a></li>
                        <li><a class="dropdown-item rounded-2 py-2" href="<?= site_url('arena-belajar/pengajar') ?>">Pengajar</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-lg-12 <?= $isPrefixActive('bunga-rampai') ? 'text-primary fw-semibold' : '' ?>" href="#" role="button" data-bs-toggle="dropdown">Bunga Rampai</a>
                    <ul class="dropdown-menu border-0 shadow-sm rounded-3 p-2">
                        <li><a class="dropdown-item rounded-2 py-2" href="<?= site_url('bunga-rampai') ?>">Beranda Bunga Rampai</a></li>
                        <li><a class="dropdown-item rounded-2 py-2" href="<?= site_url('bunga-rampai/artikel') ?>">Artikel</a></li>
                        <li><a class="dropdown-item rounded-2 py-2" href="<?= site_url('bunga-rampai/faq') ?>">FAQ</a></li>
                        <li><a class="dropdown-item rounded-2 py-2" href="<?= site_url('bunga-rampai/studi-kasus') ?>">Studi Kasus</a></li>
                        <li><a class="dropdown-item rounded-2 py-2" href="<?= site_url('bunga-rampai/template-dokumen') ?>">Template Dokumen</a></li>
                        <li><a class="dropdown-item rounded-2 py-2" href="<?= site_url('bunga-rampai/media') ?>">Media & Galeri</a></li>
                    </ul>
                </li>

                <li class="nav-item"><a class="nav-link px-lg-12 <?= $isPrefixActive('regulasi') ? 'text-primary fw-semibold' : '' ?>" href="<?= site_url('regulasi') ?>">Regulasi</a></li>
                <li class="nav-item"><a class="nav-link px-lg-12 <?= $isPrefixActive('berita') ? 'text-primary fw-semibold' : '' ?>" href="<?= site_url('berita') ?>">Berita</a></li>
                <li class="nav-item"><a class="nav-link px-lg-12 <?= $isPrefixActive('event') ? 'text-primary fw-semibold' : '' ?>" href="<?= site_url('event') ?>">Event</a></li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-lg-12 <?= $isPrefixActive('statistik') ? 'text-primary fw-semibold' : '' ?>" href="#" role="button" data-bs-toggle="dropdown">Lainnya</a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3 p-2">
                        <li><a class="dropdown-item rounded-2 py-2" href="<?= site_url('statistik') ?>">Statistik</a></li>
                        <li><a class="dropdown-item rounded-2 py-2" href="<?= site_url('verifikasi-sertifikat') ?>">Verifikasi Sertifikat</a></li>
                        <li><a class="dropdown-item rounded-2 py-2" href="<?= site_url('kontak') ?>">Kontak</a></li>
                    </ul>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-10 ms-lg-20">
                <a href="<?= site_url('masuk') ?>" class="btn btn-landing-outline rounded-pill px-16 py-9">Masuk</a>
                <a href="<?= site_url('daftar') ?>" class="btn btn-landing-primary rounded-pill px-16 py-9">Daftar</a>
            </div>
        </div>
    </div>
</nav>
