<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>

<?= view('landing/components/page_header', ['page_title' => 'Regulasi PBJ']) ?>

<?php
$regulations       = $regulations ?? [];
$categories        = $categories ?? [];
$stages            = $stages ?? [];
$competencies      = $competencies ?? [];
$stats             = $stats ?? ['total' => 0, 'categories' => 0, 'downloaded' => 0, 'published' => 0];
$keyword           = $keyword ?? '';
$selectedCategory  = $selectedCategory ?? null;
$totalRegulations  = (int) ($totalRegulations ?? count($regulations));
$currentPage       = (int) ($currentPage ?? 1);
$totalPages        = (int) ($totalPages ?? 1);
$perPage           = (int) ($perPage ?? 9);
$popular           = $popular ?? [];
?>

<?php if ($msg = session()->getFlashdata('success')): ?>
    <div class="alert alert-success radius-8 mb-3 container"><?= esc($msg) ?></div>
<?php endif; ?>

<!-- Hero Regulasi PBJ -->
<section class="about-three regulation-hero" style="background-image: url(<?= base_url('assets/main/images/shapes/about-three-bg-shape-1.png') ?>);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-8">
                <div class="about-three__left">
                    <div class="section-title-two text-left">
                        <div class="section-title-two__tagline-box"><span class="section-title-two__tagline">Regulasi PBJ</span></div>
                        <h2 class="section-title-two__title">Referensi Regulasi Pengadaan Barang/Jasa Pemerintah</h2>
                    </div>
                    <p class="about-three__text-1">
                        Akses kumpulan regulasi, pedoman, dan standar pengadaan barang/jasa yang berlaku di lingkungan
                        Pemerintah Provinsi Lampung dan nasional. Gunakan sebagai acuan dalam perencanaan, pemilihan
                        penyedia, pelaksanaan kontrak, dan serah terima.
                    </p>
                    <ul class="list-unstyled about-three__points">
                        <li><div class="icon"><span class="icon-check"></span></div><p>Regulasi tingkat Presiden, Menteri, dan Daerah</p></li>
                        <li><div class="icon"><span class="icon-check"></span></div><p>Peraturan LKPP dan Surat Edaran Gubernur</p></li>
                        <li><div class="icon"><span class="icon-check"></span></div><p>Standar dokumen dan pedoman teknis PBJ</p></li>
                    </ul>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="about-three__img">
                    <img src="<?= base_url('assets/main/images/resources/about-three-img-1.jpg') ?>" alt="Regulasi PBJ">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search regulasi -->
<section class="contact-one regulation-search">
    <div class="container">
        <div class="contact-one__bg" style="background-image: url(<?= base_url('assets/main/images/shapes/contact-one-bg.png') ?>);"></div>
        <div class="row">
            <div class="col-xl-12">
                <div class="contact-one__inner">
                    <h2 class="contact-one__title">Cari Regulasi</h2>
                    <form action="<?= site_url('regulasi') ?>" method="get" class="contact-one__form">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6">
                                <div class="contact-one__input-box">
                                    <input type="text" name="q" value="<?= esc($keyword) ?>" placeholder="Kata kunci (judul atau nomor dokumen)">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <div class="contact-one__input-box">
                                    <select name="category_id" class="form-select">
                                        <option value="">Semua Kategori</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= (int) $cat['id'] ?>" <?= (int) $selectedCategory === (int) $cat['id'] ? 'selected' : '' ?>>
                                                <?= esc($cat['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-12">
                                <button type="submit" class="thm-btn contact-one__btn">Cari</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistik regulasi -->
<section class="counter-one regulation-counter">
    <div class="container">
        <div class="counter-one__bg" style="background-image: url(<?= base_url('assets/main/images/shapes/counter-one-bg-shape.png') ?>);"></div>
        <div class="row">
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="counter-one__single">
                    <div class="counter-one__icon"><span class="icon-document"></span></div>
                    <div class="counter-one__count-box">
                        <p class="counter-one__count-text">Total Regulasi</p>
                        <h3 class="odometer" data-count="<?= (int) $stats['total'] ?>">00</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="counter-one__single">
                    <div class="counter-one__icon"><span class="icon-folder"></span></div>
                    <div class="counter-one__count-box">
                        <p class="counter-one__count-text">Kategori</p>
                        <h3 class="odometer" data-count="<?= (int) $stats['categories'] ?>">00</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="counter-one__single">
                    <div class="counter-one__icon"><span class="icon-check"></span></div>
                    <div class="counter-one__count-box">
                        <p class="counter-one__count-text">Dipublikasikan</p>
                        <h3 class="odometer" data-count="<?= (int) $stats['published'] ?>">00</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="counter-one__single">
                    <div class="counter-one__icon"><span class="icon-download"></span></div>
                    <div class="counter-one__count-box">
                        <p class="counter-one__count-text">Total Unduhan</p>
                        <h3 class="odometer" data-count="<?= (int) $stats['downloaded'] ?>">00</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Regulasi Terbaru -->
<section class="course-one regulation-list">
    <div class="container">
        <div class="section-title text-center">
            <div class="section-title__tagline-box"><div class="section-title__tagline-shape"></div><p class="section-title__tagline">Daftar Regulasi</p></div>
            <h2 class="section-title__title">Regulasi Terbaru</h2>
            <p class="learning-catalog__intro">Telusuri regulasi yang tersedia dan unduh dokumen versi berlaku.</p>
        </div>

        <?php if (empty($regulations)): ?>
            <div class="alert alert-info radius-8 text-center">
                Belum ada regulasi yang dipublikasikan.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($regulations as $reg): ?>
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <article class="course-one__single h-100">
                            <div class="course-one__img-box">
                                <div class="course-one__img"><img src="<?= base_url('assets/main/images/course/course-1-' . (((int) $reg['id'] % 4) + 1) . '.jpg') ?>" alt="<?= esc($reg['title'] ?? '') ?>"></div>
                                <div class="course-one__category"><span><?= esc($reg['category_name'] ?? 'Umum') ?></span></div>
                            </div>
                            <div class="course-one__content">
                                <div class="course-one__meta">
                                    <div class="course-one__meta-item"><span><?= esc($reg['document_year'] ?? '-') ?></span></div>
                                    <div class="course-one__meta-item"><span>v<?= esc($reg['current_version_number'] ?? '0') ?></span></div>
                                </div>
                                <h3 class="course-one__title">
                                    <a href="<?= site_url('regulasi/' . esc($reg['slug'] ?? '')) ?>"><?= esc($reg['document_number'] ?? '-') ?></a>
                                </h3>
                                <p class="course-one__text"><?= esc($reg['title'] ?? '-') ?></p>
                                <p class="course-one__text text-secondary-light small mb-16">
                                    <?= esc($reg['issuer_name'] ?? 'Tanpa penerbit') ?>
                                </p>
                                <div class="course-one__btn-box">
                                    <a class="thm-btn-two" href="<?= site_url('regulasi/' . esc($reg['slug'] ?? '')) ?>"><span>Lihat</span><i class="icon-angles-right"></i></a>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($totalPages > 1): ?>
            <?= view('landing/components/pagination', [
                'currentPage' => $currentPage,
                'totalPages'  => $totalPages,
                'baseUrl'     => site_url('regulasi'),
            ]) ?>
        <?php endif; ?>
    </div>
</section>

<!-- Kategori regulasi -->
<section class="category-one regulation-categories" style="background-image: url(<?= base_url('assets/main/images/shapes/category-one-bg-shape.png') ?>);">
    <div class="container">
        <div class="section-title text-center">
            <div class="section-title__tagline-box"><div class="section-title__tagline-shape"></div><p class="section-title__tagline">Kategori</p></div>
            <h2 class="section-title__title">Kategori Regulasi</h2>
            <p class="learning-catalog__intro">Telusuri regulasi berdasarkan kategori.</p>
        </div>
        <?php if (empty($categories)): ?>
            <p class="text-center text-secondary-light">Belum ada kategori.</p>
        <?php else: ?>
            <div class="row">
                <?php foreach ($categories as $cat): ?>
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <article class="category-one__single">
                            <div class="category-one__icon">
                                <img src="<?= base_url('assets/main/images/icon/why-choose-one-icon-' . (((int) $cat['id'] % 4) + 1) . '.png') ?>" alt="">
                            </div>
                            <h3 class="category-one__title">
                                <a href="<?= site_url('regulasi/kategori/' . esc($cat['slug'])) ?>"><?= esc($cat['name']) ?></a>
                            </h3>
                            <p class="category-one__text"><?= esc($cat['description'] ?? 'Kategori regulasi PBJ.') ?></p>
                            <a class="category-one__btn" href="<?= site_url('regulasi/kategori/' . esc($cat['slug'])) ?>"><span class="icon-angles-right"></span> Jelajahi</a>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Tahapan PBJ -->
<section class="why-choose-one regulation-stages">
    <div class="container">
        <div class="section-title text-center">
            <div class="section-title__tagline-box"><div class="section-title__tagline-shape"></div><p class="section-title__tagline">Tahapan PBJ</span></div>
            <h2 class="section-title__title">Tahapan Pengadaan Barang/Jasa</h2>
            <p class="learning-catalog__intro">Urutan tahapan dalam proses PBJ.</p>
        </div>
        <?php if (empty($stages)): ?>
            <p class="text-center text-secondary-light">Belum ada tahapan.</p>
        <?php else: ?>
            <ul class="why-choose-one__list list-unstyled">
                <?php foreach ($stages as $stage): ?>
                    <li>
                        <div class="why-choose-one__icon"><span class="icon-check"></span></div>
                        <div class="why-choose-one__content">
                            <div class="why-choose-one__count"></div>
                            <h3><?= esc($stage['name']) ?></h3>
                            <p><?= esc($stage['description'] ?? '') ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</section>

<!-- Kompetensi Terkait -->
<section class="course-one regulation-competencies">
    <div class="container">
        <div class="section-title text-center">
            <div class="section-title__tagline-box"><div class="section-title__tagline-shape"></div><p class="section-title__tagline">Kompetensi</p></div>
            <h2 class="section-title__title">Kompetensi Terkait</h2>
            <p class="learning-catalog__intro">Kompetensi yang diasosiasikan dengan regulasi PBJ.</p>
        </div>
        <?php if (empty($competencies)): ?>
            <p class="text-center text-secondary-light">Belum ada kompetensi terkait.</p>
        <?php else: ?>
            <div class="row">
                <?php foreach ($competencies as $comp): ?>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <article class="course-one__single h-100">
                            <div class="course-one__img-box">
                                <div class="course-one__img"><img src="<?= base_url('assets/main/images/course/course-1-' . (((int) $comp['id'] % 4) + 1) . '.jpg') ?>" alt="<?= esc($comp['name']) ?>"></div>
                                <div class="course-one__category"><span><?= esc($comp['key']) ?></span></div>
                            </div>
                            <div class="course-one__content">
                                <h3 class="course-one__title"><?= esc($comp['name']) ?></h3>
                                <p class="course-one__text"><?= esc($comp['description'] ?? 'Kompetensi terkait regulasi PBJ.') ?></p>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Regulasi Populer -->
<section class="event-one regulation-popular">
    <div class="container">
        <div class="section-title text-center">
            <div class="section-title__tagline-box"><div class="section-title__tagline-shape"></div><p class="section-title__tagline">Populer</p></div>
            <h2 class="section-title__title">Regulasi Populer</h2>
            <p class="learning-catalog__intro">Regulasi yang paling banyak diakses dan diunduh.</p>
        </div>
        <?php if (empty($popular ?? [])): ?>
            <p class="text-center text-secondary-light">Belum ada data.</p>
        <?php else: ?>
            <div class="row">
                <?php foreach (($popular ?? []) as $reg): ?>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <article class="event-one__single">
                            <div class="event-one__img"><img src="<?= base_url('assets/main/images/event/event-1-' . (((int) $reg['id'] % 4) + 1) . '.jpg') ?>" alt="<?= esc($reg['title']) ?>"></div>
                            <div class="event-one__content">
                                <p class="event-one__time"><span class="icon-download"></span> <?= (int) ($reg['download_count'] ?? 0) ?> unduhan</p>
                                <h4 class="event-one__title"><a href="<?= site_url('regulasi/' . esc($reg['slug'] ?? '')) ?>"><?= esc($reg['title'] ?? '-') ?></a></h4>
                                <p class="event-one__text"><?= esc($reg['document_number'] ?? '-') ?></p>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>