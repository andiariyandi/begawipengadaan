<?= $this->extend('landing/layouts/public') ?>
<?= $this->section('content') ?>
<?= view('landing/components/page_header', ['page_title' => $pageTitle]) ?>
<?php
$sets = [
    'regulations' => [
        ['title' => 'Perpres Pengadaan Barang/Jasa', 'text' => 'Referensi kebijakan utama pengadaan pemerintah.', 'slug' => 'perpres-pengadaan-barang-jasa'],
        ['title' => 'Peraturan LKPP', 'text' => 'Pedoman teknis, tata cara, dan ketentuan pelaksanaan.', 'slug' => 'peraturan-lkpp'],
        ['title' => 'Pergub Lampung', 'text' => 'Kebijakan dan pedoman pengadaan di Provinsi Lampung.', 'slug' => 'pergub-lampung'],
        ['title' => 'Standar Dokumen', 'text' => 'Dokumen standar untuk mendukung proses yang seragam.', 'slug' => 'standar-dokumen'],
    ],
    'news' => [
        ['title' => 'Peluncuran Arena Belajar PBJ', 'text' => 'BegawiPBJ memperluas akses pembelajaran bagi ekosistem PBJ.', 'slug' => 'peluncuran-arena-belajar-pbj'],
        ['title' => 'Forum Kolaborasi Pengadaan', 'text' => 'Unit kerja berbagi praktik baik dan tantangan pengadaan.', 'slug' => 'forum-kolaborasi-pengadaan'],
        ['title' => 'Klinik Dokumen Pengadaan', 'text' => 'Pendampingan penyusunan dokumen untuk meningkatkan kualitas proses.', 'slug' => 'klinik-dokumen-pengadaan'],
        ['title' => 'Capaian Kompetensi Semester', 'text' => 'Ringkasan capaian pembelajaran dan sertifikasi peserta.', 'slug' => 'capaian-kompetensi-semester'],
    ],
    'events' => [
        ['title' => 'Webinar Strategi Pengadaan', 'text' => 'Diskusi daring bersama praktisi pengadaan.', 'slug' => 'webinar-strategi-pengadaan'],
        ['title' => 'Kelas Pengelolaan Kontrak', 'text' => 'Kelas praktik untuk mengawal pelaksanaan kontrak.', 'slug' => 'kelas-pengelolaan-kontrak'],
        ['title' => 'Forum PBJ Lampung', 'text' => 'Pertemuan kolaboratif antar pemangku kepentingan.', 'slug' => 'forum-pbj-lampung'],
        ['title' => 'Klinik Persiapan Sertifikasi', 'text' => 'Sesi pendampingan menuju uji kompetensi.', 'slug' => 'klinik-persiapan-sertifikasi'],
    ],
];
$items = $sets[$catalogType] ?? $sets['news'];
$prefix = $catalogType === 'regulations' ? 'regulasi' : ($catalogType === 'news' ? 'berita' : 'event');
?>
<section class="category-one knowledge-catalog">
    <div class="category-one__bg-shape" style="background-image: url(<?= base_url('assets/main/images/shapes/category-one-bg-shape.png') ?>);"></div>
    <div class="container">
        <div class="section-title text-center">
            <div class="section-title__tagline-box"><div class="section-title__tagline-shape"></div><p class="section-title__tagline">BegawiPBJ Publik</p></div>
            <h2 class="section-title__title"><?= esc($pageTitle) ?></h2>
            <p class="learning-catalog__intro"><?= esc($description) ?></p>
        </div>
        <?php if ($catalogType === 'events'): ?>
            <div class="row events-page knowledge-events">
                <?php foreach ($items as $index => $item): ?>
                    <div class="col-xl-4 col-lg-6 col-md-6"><article class="event-one__single">
                        <div class="event-one__img"><img src="<?= base_url('assets/main/images/event/event-1-' . (($index % 4) + 1) . '.jpg') ?>" alt="<?= esc($item['title']) ?>"><div class="event-one__date"><p><?= 12 + $index ?></p><span>Okt</span></div></div>
                        <div class="event-one__content"><p class="event-one__time"><span class="icon-clock"></span> 09.00 - 12.00 WIB</p><h4 class="event-one__title"><a href="<?= site_url('event/' . $item['slug']) ?>"><?= esc($item['title']) ?></a></h4><p class="event-one__loation-text"><span class="icon-location"></span> Bandar Lampung · Daring</p><a class="thm-btn" href="<?= site_url('event/' . $item['slug']) ?>"><span class="icon-angles-right"></span> Lihat Agenda</a></div>
                    </article></div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($catalogType === 'regulations'): ?>
            <div class="knowledge-document-list">
                <?php foreach ($items as $item): ?>
                    <article class="knowledge-document"><a class="knowledge-document__icon" href="<?= site_url('regulasi/' . $item['slug'] . '/preview') ?>"><span class="icon-file"></span></a><div><h3><?= esc($item['title']) ?></h3><p><?= esc($item['text']) ?></p><small>Dokumen publik · Versi 2026</small></div><a class="thm-btn" href="<?= site_url('regulasi/' . $item['slug'] . '/preview') ?>"><span class="icon-angles-right"></span> Pratinjau</a></article>
                <?php endforeach; ?>
            </div>
        <?php elseif ($catalogType === 'news'): ?>
            <div class="row blog-list knowledge-blog-list">
                <?php foreach ($items as $index => $item): ?>
                    <div class="col-xl-6 col-lg-6">
                        <article class="blog-list__single">
                            <div class="blog-list__img-box">
                                <div class="blog-list__img"><img src="<?= base_url('assets/main/images/blog/blog-list-1-' . ($index === 1 ? 3 : 1) . '.jpg') ?>" alt="<?= esc($item['title']) ?>"></div>
                                <div class="blog-list__date"><p><span class="icon-calendar"></span> <?= 12 + $index ?> Sep 2026</p></div>
                            </div>
                            <div class="blog-list__content">
                                <div class="blog-list__client-and-meta">
                                    <div class="blog-list__client-box"><div class="blog-list__client-img"><img src="<?= base_url('assets/main/images/blog/blog-list-client-img-' . (($index % 3) + 1) . '.jpg') ?>" alt=""></div><div class="blog-list__client-content"><p>Diterbitkan oleh</p><h4>BegawiPBJ</h4></div></div>
                                    <ul class="blog-list__client-meta list-unstyled"><li><div class="icon"><span class="icon-tags"></span></div><p>Berita PBJ</p></li><li><div class="icon"><span class="icon-clock"></span></div><p>5 menit</p></li></ul>
                                </div>
                                <h3 class="blog-list__title-1"><a href="<?= site_url('berita/' . $item['slug']) ?>"><?= esc($item['title']) ?></a></h3>
                                <p class="blog-list__text"><?= esc($item['text']) ?></p>
                                <div class="blog-list__btn-box"><a href="<?= site_url('berita/' . $item['slug']) ?>" class="thm-btn-two"><span>Baca Berita</span><i class="icon-angles-right"></i></a></div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($items as $item): ?>
                    <div class="col-xl-3 col-lg-6 col-md-6"><article class="category-one__single">
                        <div class="category-one__icon"><img src="<?= base_url('assets/main/images/icon/why-choose-one-icon-1.png') ?>" alt=""></div>
                        <h3 class="category-one__title"><a href="<?= site_url($prefix . '/' . $item['slug']) ?>"><?= esc($item['title']) ?></a></h3>
                        <p class="category-one__text"><?= esc($item['text']) ?></p>
                        <a class="category-one__btn" href="<?= site_url($prefix . '/' . $item['slug']) ?>"><span class="icon-angles-right"></span> Jelajahi</a>
                    </article></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?= view('landing/components/pagination', [
            'currentPage' => (int) (service('request')->getGet('page') ?? 1),
            'totalPages' => 3,
        ]) ?>
    </div>
</section>
<?= $this->endSection() ?>
