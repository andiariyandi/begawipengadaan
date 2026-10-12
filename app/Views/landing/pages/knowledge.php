<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>

<?php
$stats = $stats ?? [
    'total_articles'  => 0,
    'total_faqs'      => 0,
    'total_cases'     => 0,
    'total_templates' => 0,
    'total_media'     => 0,
    'total_videos'    => 0,
    'total_gallery'   => 0,
    'total_all'       => 0,
];
$featured    = $featured ?? [];
$articles    = $articles ?? [];
$caseStudies = $caseStudies ?? [];
$templates   = $templates ?? [];
$faqs           = $faqs ?? [];
$mediaItems     = $mediaItems ?? [];
$carouselSlides = $carouselSlides ?? [];
$isLoggedIn     = session()->get('isLoggedIn') === true;
?>

<style>
/* Hero Carousel Styling */
.knowledge-carousel-slide {
    position: relative;
    height: 380px;
    border-radius: 16px;
    overflow: hidden;
    background: #082f5c;
}
@media (min-width: 992px) {
    .knowledge-carousel-slide {
        height: 420px;
    }
}
.knowledge-carousel-slide img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.38;
    transition: transform 0.6s ease;
}
.knowledge-carousel-slide:hover img {
    transform: scale(1.03);
}
.knowledge-carousel-gradient {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(8, 47, 92, 0.96) 0%, rgba(8, 47, 92, 0.72) 55%, rgba(8, 47, 92, 0.28) 100%);
}
.knowledge-carousel-content {
    position: relative;
    z-index: 2;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 36px 54px;
    color: #ffffff;
    max-width: 780px;
}
@media (max-width: 576px) {
    .knowledge-carousel-content {
        padding: 24px;
    }
}
.knowledge-carousel-control {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.4);
    backdrop-filter: blur(4px);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0.8;
    transition: all 0.2s ease;
}
.knowledge-carousel-control:hover {
    background: rgba(255, 255, 255, 0.95);
    color: #082f5c;
    opacity: 1;
}

.media-avatar-ring {
    display: inline-block;
    padding: 2px;
    border-radius: 50%;
    background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
}
.media-visual-container {
    position: relative;
    overflow: hidden;
    background: #082f5c;
    border-radius: 0;
}
.media-visual-container img {
    transition: transform 0.35s ease;
}
.media-visual-container:hover img {
    transform: scale(1.04);
}
.media-hover-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(8, 47, 92, 0.95) 0%, rgba(8, 47, 92, 0.45) 55%, transparent 100%);
    opacity: 0;
    transition: opacity 0.25s ease;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 16px;
    color: #ffffff;
}
.media-visual-container:hover .media-hover-overlay {
    opacity: 1;
}
.media-play-pulse {
    border-radius: 50%;
    background: rgba(220, 53, 69, 0.92);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    animation: mediaPulseHome 1.8s infinite;
}
@keyframes mediaPulseHome {
    0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
    70% { box-shadow: 0 0 0 14px rgba(220, 53, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
}
.media-visual-container {
    cursor: pointer;
}
.media-hashtag-pill {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 20px;
    background: #f1f5f9;
    color: #0d6efd;
    font-size: 11px;
    font-weight: 600;
    margin-right: 3px;
    margin-bottom: 3px;
    text-decoration: none;
}
/* Lightbox Modal */
.media-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(5, 15, 35, 0.92);
    backdrop-filter: blur(10px);
    z-index: 99999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    opacity: 0;
    transition: opacity 0.25s ease;
}
.media-modal-backdrop.is-open {
    display: flex;
    opacity: 1;
}
.media-modal-card {
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    max-width: 1050px;
    width: 100%;
    max-height: 90vh;
    display: flex;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    position: relative;
    animation: mediaModalPopHome 0.25s ease;
}
@keyframes mediaModalPopHome {
    from { transform: scale(0.96); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
@media (max-width: 991.98px) {
    .media-modal-card {
        flex-direction: column;
        max-height: 94vh;
        overflow-y: auto;
    }
}
.media-modal-close-btn {
    position: absolute;
    top: 14px;
    right: 16px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.65);
    color: #ffffff;
    border: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    font-size: 18px;
    transition: background 0.15s ease;
}
.media-modal-close-btn:hover {
    background: rgba(220, 53, 69, 0.9);
}
</style>

<!-- =========================================================================
     PAGE HERO BUNGA RAMPAI (UNIFIED FI-STUDY DNA)
     ========================================================================= -->
<section class="arena-hero position-relative">
    <div class="arena-hero__bg" style="background-image: url(<?= base_url('assets/main/images/backgrounds/slider-1-1.jpg') ?>);"></div>
    <div class="arena-hero__overlay"></div>
    <div class="container">
        <div class="arena-hero__inner text-center pt-2 pt-md-4">
            <span class="arena-section-head__tag text-white mb-3" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.32); backdrop-filter: blur(6px);">
                <i class="fas fa-book-reader me-1"></i> Bunga Rampai BegawiPBJ Lampung
            </span>
            <h1 class="text-white mb-3" style="font-size: 38px; font-weight: 800; line-height: 1.25; text-shadow: 0 2px 10px rgba(0,0,0,0.4);">
                Pusat Pengetahuan &amp; Praktik Baik Pengadaan
            </h1>
            <p class="mx-auto mb-4" style="max-width: 720px; font-size: 16px; line-height: 1.6; color: rgba(255,255,255,0.92); text-shadow: 0 1px 5px rgba(0,0,0,0.35);">
                Kompilasi artikel teknis, studi kasus lapangan, standar template dokumen siap pakai, tanya jawab (FAQ), dan konten multimedia Pengadaan Barang/Jasa Pemerintah.
            </p>

            <!-- Search Bar -->
            <div class="arena-search-bar mb-4">
                <form action="<?= site_url('bunga-rampai/artikel') ?>" method="get" class="d-flex align-items-center">
                    <i class="fas fa-search text-muted me-2" style="font-size: 16px;"></i>
                    <input type="text" name="q" placeholder="Cari artikel PBJ, format KAK, HPS, mitigasi risiko, studi kasus, atau FAQ..." autocomplete="off">
                    <button type="submit">
                        <i class="fas fa-search me-1"></i>
                        <span>Cari</span>
                    </button>
                </form>
            </div>

            <!-- Sub-Navigation Pills -->
            <nav class="arena-nav-pills" aria-label="Kategori Bunga Rampai">
                <a href="<?= site_url('bunga-rampai') ?>" class="arena-nav-pill active">
                    <i class="fas fa-th-large"></i>
                    <span>Semua Eksplorasi</span>
                    <span class="badge"><?= (int) ($stats['total_all'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/artikel') ?>" class="arena-nav-pill">
                    <i class="fas fa-newspaper"></i>
                    <span>Artikel PBJ</span>
                    <span class="badge"><?= (int) ($stats['total_articles'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/faq') ?>" class="arena-nav-pill">
                    <i class="fas fa-question-circle"></i>
                    <span>Tanya Jawab (FAQ)</span>
                    <span class="badge"><?= (int) ($stats['total_faqs'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/studi-kasus') ?>" class="arena-nav-pill">
                    <i class="fas fa-briefcase"></i>
                    <span>Studi Kasus</span>
                    <span class="badge"><?= (int) ($stats['total_cases'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/template-dokumen') ?>" class="arena-nav-pill">
                    <i class="fas fa-file-invoice"></i>
                    <span>Template Dokumen</span>
                    <span class="badge"><?= (int) ($stats['total_templates'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/media') ?>" class="arena-nav-pill">
                    <i class="fas fa-photo-video"></i>
                    <span>Media &amp; Infografik</span>
                    <span class="badge"><?= (int) ($stats['total_media'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/video') ?>" class="arena-nav-pill">
                    <i class="fas fa-video"></i>
                    <span>Video PBJ</span>
                    <span class="badge"><?= (int) ($stats['total_videos'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/galeri') ?>" class="arena-nav-pill">
                    <i class="fas fa-images"></i>
                    <span>Galeri Kegiatan</span>
                    <span class="badge"><?= (int) ($stats['total_gallery'] ?? 0) ?></span>
                </a>
            </nav>
        </div>
    </div>
</section>

<!-- Flash Alerts -->
<?php if ($msg = session()->getFlashdata('success')): ?>
    <div class="container mt-4"><div class="alert alert-success radius-8 mb-0"><?= esc($msg) ?></div></div>
<?php endif; ?>
<?php if ($msg = session()->getFlashdata('error')): ?>
    <div class="container mt-4"><div class="alert alert-danger radius-8 mb-0"><?= esc($msg) ?></div></div>
<?php endif; ?>

<!-- =========================================================================
     HERO SPOTLIGHT CAROUSEL (PANORAMIC FEATURED SLIDER)
     ========================================================================= -->
<?php if (! empty($carouselSlides)): ?>
<section class="py-4 bg-white border-bottom">
    <div class="container">
        <div id="knowledgeHeroCarousel" class="carousel slide carousel-fade shadow-sm radius-16 overflow-hidden position-relative" data-bs-ride="carousel" data-bs-interval="6000">
            <div class="carousel-indicators mb-3">
                <?php foreach ($carouselSlides as $idx => $slide): ?>
                    <button type="button" data-bs-target="#knowledgeHeroCarousel" data-bs-slide-to="<?= $idx ?>" class="<?= $idx === 0 ? 'active' : '' ?>" aria-current="<?= $idx === 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $idx + 1 ?>"></button>
                <?php endforeach; ?>
            </div>
            <div class="carousel-inner">
                <?php foreach ($carouselSlides as $idx => $slide): ?>
                    <div class="carousel-item knowledge-carousel-slide <?= $idx === 0 ? 'active' : '' ?>">
                        <img src="<?= esc($slide['cover_image']) ?>" alt="<?= esc($slide['title']) ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/backgrounds/slider-1-1.jpg') ?>';">
                        <div class="knowledge-carousel-gradient"></div>
                        <div class="knowledge-carousel-content">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                <span class="badge radius-6 px-3 py-1 small fw-semibold bg-warning text-dark">
                                    <i class="fas fa-star me-1"></i> Sorotan Utama
                                </span>
                                <span class="badge radius-6 px-3 py-1 small fw-semibold bg-white bg-opacity-25 text-white">
                                    <?= esc($slide['type_label']) ?>
                                </span>
                                <?php if (! empty($slide['stage'])): ?>
                                    <span class="badge radius-6 px-2 py-1 small bg-info bg-opacity-25 text-white border border-info border-opacity-25">
                                        <i class="fas fa-layer-group me-1"></i> <?= esc($slide['stage']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <h2 class="text-white fw-bold mb-2" style="font-size: 24px; line-height: 1.35; text-shadow: 0 2px 8px rgba(0,0,0,0.5);">
                                <a href="<?= esc($slide['url']) ?>" class="text-white text-decoration-none hover-underline">
                                    <?= esc($slide['title']) ?>
                                </a>
                            </h2>
                            <p class="text-white-80 mb-3 small d-none d-md-block" style="color: rgba(255,255,255,0.88); font-size: 14px; line-height: 1.55; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; max-width: 680px;">
                                <?= esc($slide['summary']) ?>
                            </p>
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <a href="<?= esc($slide['url']) ?>" class="btn btn-primary radius-8 px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm">
                                    <span><?= ! empty($slide['is_media']) ? 'Lihat Media' : 'Baca Selengkapnya' ?></span>
                                    <i class="fas fa-arrow-right" style="font-size: 12px;"></i>
                                </a>
                                <div class="d-flex align-items-center gap-2 text-white small" style="font-size: 12px; color: rgba(255,255,255,0.8);">
                                    <span><i class="fas fa-user-circle me-1"></i> <?= esc($slide['author_name']) ?></span>
                                    <?php if (! empty($slide['reading_time'])): ?>
                                        <span>&middot;</span>
                                        <span><i class="far fa-clock me-1"></i> <?= esc($slide['reading_time']) ?> mnt</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($carouselSlides) > 1): ?>
                <button class="carousel-control-prev knowledge-carousel-control ms-3" type="button" data-bs-target="#knowledgeHeroCarousel" data-bs-slide="prev">
                    <i class="fas fa-chevron-left"></i>
                    <span class="visually-hidden">Sebelumnya</span>
                </button>
                <button class="carousel-control-next knowledge-carousel-control me-3" type="button" data-bs-target="#knowledgeHeroCarousel" data-bs-slide="next">
                    <i class="fas fa-chevron-right"></i>
                    <span class="visually-hidden">Selanjutnya</span>
                </button>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- =========================================================================
     SECTION 1: PILAR EKSPLORASI BUNGA RAMPAI (CATEGORY GRID)
     ========================================================================= -->
<section class="py-5" style="background: #ffffff;">
    <div class="container">
        <div class="section-title text-center mb-4">
            <div class="section-title__tagline-box">
                <div class="section-title__tagline-shape"></div>
                <p class="section-title__tagline">Pilar Repositori Pengetahuan</p>
            </div>
            <h2 class="section-title__title" style="font-size: 30px; font-weight: 800; color: #082f5c;">Jelajahi Ekosistem Pengetahuan PBJ</h2>
            <p class="text-muted mx-auto" style="max-width: 640px; font-size: 15px;">
                Temukan referensi komprehensif mulai dari pemahaman regulasi, template berkas operasional, kajian risiko lapangan, hingga media visual interaktif.
            </p>
        </div>

        <div class="row g-4">
            <?php
            $pillars = [
                [
                    'title' => 'Artikel PBJ',
                    'desc'  => 'Wawasan analisis regulasi LKPP, opini keahlian, dan praktik baik pengadaan.',
                    'url'   => site_url('bunga-rampai/artikel'),
                    'icon'  => 'fas fa-newspaper',
                    'color' => '#0d6efd',
                    'count' => (int) ($stats['total_articles'] ?? 0),
                    'label' => 'Artikel',
                ],
                [
                    'title' => 'Tanya Jawab (FAQ)',
                    'desc'  => 'Solusi ringkas dan tepat sasaran atas problematika pengadaan sehari-hari.',
                    'url'   => site_url('bunga-rampai/faq'),
                    'icon'  => 'fas fa-question-circle',
                    'color' => '#198754',
                    'count' => (int) ($stats['total_faqs'] ?? 0),
                    'label' => 'Tanya Jawab',
                ],
                [
                    'title' => 'Studi Kasus Lapangan',
                    'desc'  => 'Pelajaran berharga dari dinamika audit, kontrak kritis, dan sanggahan tender.',
                    'url'   => site_url('bunga-rampai/studi-kasus'),
                    'icon'  => 'fas fa-briefcase',
                    'color' => '#d63384',
                    'count' => (int) ($stats['total_cases'] ?? 0),
                    'label' => 'Kasus PBJ',
                ],
                [
                    'title' => 'Template Dokumen',
                    'desc'  => 'Standar format KAK, HPS, SPK, kontrak, dan BAST siap unduh yang terstandardisasi.',
                    'url'   => site_url('bunga-rampai/template-dokumen'),
                    'icon'  => 'fas fa-file-invoice',
                    'color' => '#fd7e14',
                    'count' => (int) ($stats['total_templates'] ?? 0),
                    'label' => 'Template Berkas',
                ],
                [
                    'title' => 'Media & Infografik',
                    'desc'  => 'Diagram alir, cheat sheet, dan infografik visual siklus pengadaan pemerintah.',
                    'url'   => site_url('bunga-rampai/media'),
                    'icon'  => 'fas fa-photo-video',
                    'color' => '#0dcaf0',
                    'count' => (int) ($stats['total_media'] ?? 0),
                    'label' => 'Media Visual',
                ],
                [
                    'title' => 'Video Pembelajaran',
                    'desc'  => 'Tutorial aplikasi SPSE, rekaman webinar, dan sesi bimbingan teknis daring.',
                    'url'   => site_url('bunga-rampai/video'),
                    'icon'  => 'fas fa-video',
                    'color' => '#dc3545',
                    'count' => (int) ($stats['total_videos'] ?? 0),
                    'label' => 'Video',
                ],
                [
                    'title' => 'Galeri Dokumentasi',
                    'desc'  => 'Rekam jejak kegiatan kolaboratif ekosistem pengadaan se-Provinsi Lampung.',
                    'url'   => site_url('bunga-rampai/galeri'),
                    'icon'  => 'fas fa-images',
                    'color' => '#6f42c1',
                    'count' => (int) ($stats['total_gallery'] ?? 0),
                    'label' => 'Dokumentasi',
                ],
                [
                    'title' => 'Regulasi Terkait',
                    'desc'  => 'Basis peraturan perundang-undangan PBJ, Perpres, Perka LKPP, dan Pergub Lampung.',
                    'url'   => site_url('regulasi'),
                    'icon'  => 'fas fa-balance-scale',
                    'color' => '#20c997',
                    'count' => 'Resmi',
                    'label' => 'Peraturan',
                ],
            ];
            foreach ($pillars as $p): ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm radius-16 p-4 d-flex flex-column transition-hover" style="background: #ffffff; border: 1px solid #edf2f7 !important;">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center radius-12" style="width: 52px; height: 52px; background: <?= esc($p['color']) ?>15; color: <?= esc($p['color']) ?>; font-size: 22px;">
                                <i class="<?= esc($p['icon']) ?>"></i>
                            </div>
                            <span class="badge radius-20 px-3 py-1 small fw-semibold" style="background: #f1f5f9; color: #475569;">
                                <?= esc($p['count']) ?> <?= is_numeric($p['count']) ? 'Item' : '' ?>
                            </span>
                        </div>
                        <h4 class="fw-bold mb-2" style="font-size: 17px; color: #082f5c;">
                            <a href="<?= esc($p['url']) ?>" class="text-decoration-none text-reset hover-primary"><?= esc($p['title']) ?></a>
                        </h4>
                        <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.55;">
                            <?= esc($p['desc']) ?>
                        </p>
                        <a href="<?= esc($p['url']) ?>" class="btn btn-sm btn-outline-primary radius-8 d-inline-flex align-items-center justify-content-between px-3 py-2 fw-semibold" style="margin-top: auto;">
                            <span>Jelajahi <?= esc($p['label']) ?></span>
                            <i class="fas fa-arrow-right ms-2" style="font-size: 12px;"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- =========================================================================
     SECTION 2: PILIHAN EDITOR (FEATURED CONTENT)
     ========================================================================= -->
<?php if (! empty($featured)): ?>
<section class="py-5" style="background: #f8fafc; border-top: 1px solid #eef2f6;">
    <div class="container">
        <div class="d-flex flex-wrap align-items-end justify-content-between mb-4">
            <div class="arena-section-head mb-0">
                <span class="arena-section-head__tag">
                    <i class="fas fa-star me-1 text-warning"></i> Konten Pilihan Editor
                </span>
                <h3 class="arena-section-head__title">Rekomendasi Utama Pengetahuan</h3>
                <p class="arena-section-head__desc mb-0">
                    Materi esensial yang direkomendasikan langsung oleh fasilitator dan tim teknis pengadaan daerah.
                </p>
            </div>
            <a href="<?= site_url('bunga-rampai/artikel?featured=1') ?>" class="btn btn-outline-primary radius-8 px-4 py-2 mt-3 mt-md-0 d-inline-flex align-items-center gap-2 fw-semibold">
                <span>Lihat Semua Rekomendasi</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php foreach ($featured as $index => $feat):
                $fType = $feat['type'] ?? 'article';
                $fRoute = match ($fType) {
                    'case_study' => 'public.knowledge.case-study.detail',
                    'template'   => 'public.knowledge.templates.detail',
                    default      => 'public.knowledge.article.detail',
                };
                $fUrl = route_to($fRoute, $feat['slug']);
                $fBookmarkType = match ($fType) {
                    'case_study' => 'knowledge_case',
                    'template'   => 'knowledge_template',
                    default      => 'knowledge_article',
                };
                $typeLabel = match ($fType) {
                    'case_study' => 'Studi Kasus',
                    'template'   => 'Template Dokumen',
                    default      => 'Artikel PBJ',
                };
                $badgeBg = match ($fType) {
                    'case_study' => 'bg-danger text-white',
                    'template'   => 'bg-warning text-dark',
                    default      => 'bg-primary text-white',
                };

                $coverImg = ! empty($feat['cover_file_id'])
                    ? site_url('file/' . (int) $feat['cover_file_id'])
                    : base_url('assets/main/images/blog/blog-2-' . (((int) $feat['id'] % 4) + 1) . '.jpg');
            ?>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <article class="card h-100 border-0 shadow-sm radius-16 overflow-hidden d-flex flex-column transition-hover" style="background: #ffffff;">
                        <div class="position-relative" style="height: 180px; overflow: hidden; background: #082f5c;">
                            <img src="<?= esc($coverImg) ?>" alt="<?= esc($feat['title']) ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/blog/blog-2-1.jpg') ?>';">
                            <div class="position-absolute top-0 start-0 m-3">
                                <span class="badge radius-6 px-2 py-1 small fw-semibold <?= esc($badgeBg) ?>">
                                    <?= esc($typeLabel) ?>
                                </span>
                            </div>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-dark bg-opacity-75 text-white radius-6 px-2 py-1 small">
                                    <i class="fas fa-star text-warning me-1"></i> Unggulan
                                </span>
                            </div>
                        </div>
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between text-muted small mb-2" style="font-size: 11.5px;">
                                <span><i class="fas fa-user-circle me-1"></i> <?= esc($feat['author_name'] ?? 'Biro PBJ') ?></span>
                                <span><i class="far fa-clock me-1"></i> <?= esc($feat['reading_time'] ?? 5) ?> mnt</span>
                            </div>
                            <!-- Metadata tags -->
                            <div class="d-flex flex-wrap align-items-center gap-1 mb-2">
                                <?php if ($fType === 'case_study' && ! empty($feat['case_severity'])):
                                    $sev = strtolower((string) $feat['case_severity']);
                                    $sevClass = match ($sev) {
                                        'critical' => 'background: #fee2e2; color: #b91c1c; border-color: #fca5a5;',
                                        'high'     => 'background: #ffedd5; color: #c2410c; border-color: #fdba74;',
                                        'low'      => 'background: #e0f2fe; color: #0369a1; border-color: #7dd3fc;',
                                        default    => 'background: #fef9c3; color: #854d0e; border-color: #fde047;',
                                    };
                                    $sevText = match ($sev) {
                                        'critical' => 'Risiko Kritis',
                                        'high'     => 'Risiko Tinggi',
                                        'low'      => 'Risiko Rendah',
                                        default    => 'Risiko Sedang',
                                    };
                                ?>
                                    <span class="badge radius-6 px-2 py-0 border" style="<?= $sevClass ?> font-size: 10.5px;">
                                        <i class="fas fa-shield-alt me-1"></i> <?= $sevText ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($fType === 'template' && ! empty($feat['document_version'])): ?>
                                    <span class="badge bg-light text-dark border px-2 py-0" style="font-size: 10.5px;">
                                        <i class="fas fa-code-branch me-1 text-primary"></i> <?= esc($feat['document_version']) ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (! empty($feat['procurement_stage'])): ?>
                                    <span class="badge bg-light text-secondary border px-2 py-0" style="font-size: 10.5px;">
                                        <?= esc($feat['procurement_stage']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <h4 class="fw-bold mb-2" style="font-size: 16px; line-height: 1.45; color: #082f5c;">
                                <a href="<?= esc($fUrl) ?>" class="text-decoration-none text-reset hover-primary"><?= esc($feat['title']) ?></a>
                            </h4>
                            <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= esc($feat['summary'] ?? '') ?>
                            </p>
                            <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                <a href="<?= esc($fUrl) ?>" class="btn btn-sm btn-primary radius-8 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                                    <span>Buka</span>
                                    <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                                </a>
                                <?= view('landing/components/bookmark_button', [
                                    'type'  => $fBookmarkType,
                                    'id'    => (int) $feat['id'],
                                    'title' => $feat['title'] ?? null,
                                    'size'  => 'sm',
                                ]) ?>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- =========================================================================
     SECTION 3: ARTIKEL TERBARU
     ========================================================================= -->
<?php if (! empty($articles)): ?>
<section class="py-5" style="background: #ffffff;">
    <div class="container">
        <div class="d-flex flex-wrap align-items-end justify-content-between mb-4">
            <div class="arena-section-head mb-0">
                <span class="arena-section-head__tag">
                    <i class="fas fa-newspaper me-1"></i> Wawasan &amp; Regulasi
                </span>
                <h3 class="arena-section-head__title">Artikel Pengetahuan Terbaru</h3>
                <p class="arena-section-head__desc mb-0">
                    Ulasan analisis, panduan teknis, dan praktik terbaik pengadaan barang/jasa terkini.
                </p>
            </div>
            <a href="<?= route_to('public.knowledge.articles') ?>" class="btn btn-outline-primary radius-8 px-4 py-2 mt-3 mt-md-0 d-inline-flex align-items-center gap-2 fw-semibold">
                <span>Lihat Semua Artikel (<?= (int) ($stats['total_articles'] ?? 0) ?>)</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php foreach (array_slice($articles, 0, 3) as $art):
                $coverImg = ! empty($art['cover_file_id'])
                    ? site_url('file/' . (int) $art['cover_file_id'])
                    : base_url('assets/main/images/blog/blog-2-' . (((int) $art['id'] % 4) + 1) . '.jpg');
            ?>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <article class="card h-100 border-0 shadow-sm radius-16 overflow-hidden d-flex flex-column transition-hover" style="background: #ffffff; border: 1px solid #edf2f7 !important;">
                        <div class="position-relative" style="height: 190px; overflow: hidden; background: #082f5c;">
                            <img src="<?= esc($coverImg) ?>" alt="<?= esc($art['title']) ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/blog/blog-2-2.jpg') ?>';">
                            <div class="position-absolute top-0 start-0 m-3">
                                <span class="badge bg-primary text-white radius-6 px-2 py-1 small fw-semibold">
                                    <i class="fas fa-newspaper me-1"></i> Artikel PBJ
                                </span>
                            </div>
                        </div>
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between text-muted small mb-2" style="font-size: 11.5px;">
                                <span><i class="fas fa-user-circle me-1"></i> <?= esc($art['author_name'] ?? 'Biro PBJ') ?></span>
                                <span><i class="far fa-clock me-1"></i> <?= esc($art['reading_time'] ?? 5) ?> mnt baca</span>
                            </div>
                            <div class="d-flex flex-wrap align-items-center gap-1 mb-2">
                                <?php if (! empty($art['procurement_stage'])): ?>
                                    <span class="badge radius-6 px-2 py-0 border" style="background: #e0f2fe; color: #0369a1; border-color: #bae6fd; font-size: 10.5px;">
                                        <i class="fas fa-layer-group me-1"></i> <?= esc($art['procurement_stage']) ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (! empty($art['organization_unit'])): ?>
                                    <span class="badge radius-6 px-2 py-0 border bg-light text-secondary" style="font-size: 10.5px;">
                                        <i class="fas fa-building me-1 text-primary"></i> <?= esc($art['organization_unit']) ?>
                                    </span>
                                <?php endif; ?>
                                <span class="badge radius-6 px-2 py-0 bg-light text-muted border" style="font-size: 10.5px;">
                                    <i class="far fa-calendar-alt me-1"></i> <?= ! empty($art['published_at']) ? esc(date('d M Y', strtotime($art['published_at']))) : 'Terbaru' ?>
                                </span>
                            </div>
                            <h4 class="fw-bold mb-2" style="font-size: 17px; line-height: 1.45; color: #082f5c;">
                                <a href="<?= route_to('public.knowledge.article.detail', $art['slug']) ?>" class="text-decoration-none text-reset hover-primary">
                                    <?= esc($art['title']) ?>
                                </a>
                            </h4>
                            <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.55; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= esc($art['summary'] ?? '') ?>
                            </p>
                            <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                <a href="<?= route_to('public.knowledge.article.detail', $art['slug']) ?>" class="btn btn-sm btn-outline-primary radius-8 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                                    <span>Baca Artikel</span>
                                    <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                                </a>
                                <?= view('landing/components/bookmark_button', [
                                    'type'  => 'knowledge_article',
                                    'id'    => (int) $art['id'],
                                    'title' => $art['title'] ?? null,
                                    'size'  => 'sm',
                                ]) ?>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- =========================================================================
     SECTION 4: STUDI KASUS & TEMPLATE DOKUMEN (SPLIT SHOWCASE)
     ========================================================================= -->
<section class="py-5" style="background: #f8fafc; border-top: 1px solid #eef2f6;">
    <div class="container">
        <div class="row g-5">
            <!-- Studi Kasus -->
            <div class="col-xl-6">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <span class="arena-section-head__tag text-danger" style="background: #fde8e8;">
                            <i class="fas fa-briefcase me-1"></i> Pelajaran Nyata
                        </span>
                        <h3 class="fw-bold mt-2 mb-0" style="color: #082f5c; font-size: 22px;">Studi Kasus Lapangan</h3>
                    </div>
                    <a href="<?= route_to('public.knowledge.case-studies') ?>" class="btn btn-sm btn-outline-secondary radius-8 px-3 fw-semibold">
                        Semua Kasus (<?= (int) ($stats['total_cases'] ?? 0) ?>)
                    </a>
                </div>

                <div class="d-flex flex-column gap-3">
                    <?php if (empty($caseStudies)): ?>
                        <div class="p-4 text-center bg-white radius-12 text-muted">Belum ada studi kasus yang dipublikasikan.</div>
                    <?php else: ?>
                        <?php foreach (array_slice($caseStudies, 0, 3) as $cs): ?>
                            <div class="card border-0 shadow-sm radius-12 p-3 transition-hover bg-white">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="d-flex align-items-center justify-content-center radius-10 flex-shrink-0" style="width: 44px; height: 44px; background: #fff1f2; color: #e11d48; font-size: 18px;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                                            <span class="badge bg-light text-danger border px-2 py-0 small" style="font-size: 10.5px;">Studi Kasus</span>
                                            <?php
                                            $sev = strtolower((string) ($cs['case_severity'] ?? ''));
                                            $sevBadge = match($sev) {
                                                'critical' => '<span class="badge radius-6 px-2 py-0 border" style="background:#fee2e2;color:#b91c1c;border-color:#fca5a5;font-size:10.5px;"><i class="fas fa-shield-alt me-1"></i>Risiko Kritis</span>',
                                                'high'     => '<span class="badge radius-6 px-2 py-0 border" style="background:#ffedd5;color:#c2410c;border-color:#fdba74;font-size:10.5px;"><i class="fas fa-shield-alt me-1"></i>Risiko Tinggi</span>',
                                                'low'      => '<span class="badge radius-6 px-2 py-0 border" style="background:#e0f2fe;color:#0369a1;border-color:#7dd3fc;font-size:10.5px;"><i class="fas fa-shield-alt me-1"></i>Risiko Rendah</span>',
                                                'medium'   => '<span class="badge radius-6 px-2 py-0 border" style="background:#fef9c3;color:#854d0e;border-color:#fde047;font-size:10.5px;"><i class="fas fa-shield-alt me-1"></i>Risiko Sedang</span>',
                                                default    => '',
                                            };
                                            echo $sevBadge;
                                            ?>
                                            <?php if (! empty($cs['procurement_stage'])): ?>
                                                <span class="badge bg-light text-secondary border px-2 py-0" style="font-size: 10.5px;">
                                                    <?= esc($cs['procurement_stage']) ?>
                                                </span>
                                            <?php endif; ?>
                                            <span class="text-muted small ms-auto" style="font-size: 11px;"><i class="far fa-clock me-1"></i> <?= esc($cs['reading_time'] ?? 5) ?> mnt</span>
                                        </div>
                                        <h5 class="fw-bold mb-1" style="font-size: 15px; color: #082f5c; line-height: 1.4;">
                                            <a href="<?= route_to('public.knowledge.case-study.detail', $cs['slug']) ?>" class="text-decoration-none text-reset hover-primary">
                                                <?= esc($cs['title']) ?>
                                            </a>
                                        </h5>
                                        <p class="text-muted small mb-2" style="font-size: 13px; line-height: 1.45; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            <?= esc($cs['summary'] ?? '') ?>
                                        </p>
                                        <?php if (! empty($cs['resolution_summary'])): ?>
                                            <div class="p-2 radius-8 mb-2 small" style="background: #fff7ed; border-left: 3px solid #f97316; color: #9a3412; font-size: 11.5px; line-height: 1.4;">
                                                <strong>Pelajaran:</strong> <?= esc(mb_strimwidth($cs['resolution_summary'], 0, 95, '...')) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <a href="<?= route_to('public.knowledge.case-study.detail', $cs['slug']) ?>" class="small text-danger fw-semibold text-decoration-none hover-underline">
                                                Pelajari Kasus &amp; Solusi <i class="fas fa-chevron-right ms-1" style="font-size: 10px;"></i>
                                            </a>
                                            <?= view('landing/components/bookmark_button', [
                                                'type'  => 'knowledge_case',
                                                'id'    => (int) $cs['id'],
                                                'title' => $cs['title'] ?? null,
                                                'size'  => 'sm',
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Template Dokumen -->
            <div class="col-xl-6">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <span class="arena-section-head__tag text-warning" style="background: #fef3c7;">
                            <i class="fas fa-file-invoice me-1"></i> Siap Unduh
                        </span>
                        <h3 class="fw-bold mt-2 mb-0" style="color: #082f5c; font-size: 22px;">Template Dokumen PBJ</h3>
                    </div>
                    <a href="<?= route_to('public.knowledge.templates') ?>" class="btn btn-sm btn-outline-secondary radius-8 px-3 fw-semibold">
                        Semua Template (<?= (int) ($stats['total_templates'] ?? 0) ?>)
                    </a>
                </div>

                <div class="d-flex flex-column gap-3">
                    <?php if (empty($templates)): ?>
                        <div class="p-4 text-center bg-white radius-12 text-muted">Belum ada template dokumen yang dipublikasikan.</div>
                    <?php else: ?>
                        <?php foreach (array_slice($templates, 0, 3) as $tpl):
                            $ext = strtoupper($tpl['file_extension'] ?? 'DOCX');
                            $extBadgeColor = match ($ext) {
                                'XLSX', 'XLS' => 'background: #dcfce7; color: #166534;',
                                'PDF'         => 'background: #fee2e2; color: #991b1b;',
                                default       => 'background: #e0e7ff; color: #3730a3;',
                            };
                        ?>
                            <div class="card border-0 shadow-sm radius-12 p-3 transition-hover bg-white">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="d-flex align-items-center justify-content-center radius-10 flex-shrink-0 fw-bold" style="width: 44px; height: 44px; <?= esc($extBadgeColor) ?>; font-size: 13px;">
                                        <?= esc($ext) ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                                            <span class="badge bg-light text-primary border px-2 py-0 small" style="font-size: 10.5px;">Format Resmi</span>
                                            <?php if (! empty($tpl['document_category'])): ?>
                                                <span class="badge radius-6 px-2 py-0 border" style="background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; font-size: 10.5px;">
                                                    <i class="fas fa-tag me-1"></i> <?= esc($tpl['document_category']) ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if (! empty($tpl['document_version'])): ?>
                                                <span class="badge bg-light text-dark border px-2 py-0" style="font-size: 10.5px;">
                                                    <i class="fas fa-code-branch me-1 text-primary"></i> <?= esc($tpl['document_version']) ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if (! empty($tpl['cover_file_id'])): ?>
                                                <span class="text-success small ms-auto" style="font-size: 11px;"><i class="fas fa-check-circle me-1"></i> Siap Unduh</span>
                                            <?php endif; ?>
                                        </div>
                                        <h5 class="fw-bold mb-1" style="font-size: 15px; color: #082f5c; line-height: 1.4;">
                                            <a href="<?= route_to('public.knowledge.templates.detail', $tpl['slug']) ?>" class="text-decoration-none text-reset hover-primary">
                                                <?= esc($tpl['title']) ?>
                                            </a>
                                        </h5>
                                        <p class="text-muted small mb-2" style="font-size: 13px; line-height: 1.45; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            <?= esc($tpl['summary'] ?? '') ?>
                                        </p>
                                        <div class="d-flex align-items-center justify-content-between pt-1">
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="<?= route_to('public.knowledge.templates.download', $tpl['slug']) ?>" class="btn btn-sm btn-primary radius-6 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 12px;">
                                                    <i class="fas fa-download"></i> Unduh
                                                </a>
                                                <a href="<?= route_to('public.knowledge.templates.detail', $tpl['slug']) ?>" class="btn btn-sm btn-outline-secondary radius-6 px-2 py-1" style="font-size: 12px;">
                                                    Petunjuk
                                                </a>
                                            </div>
                                            <?= view('landing/components/bookmark_button', [
                                                'type'  => 'knowledge_template',
                                                'id'    => (int) $tpl['id'],
                                                'title' => $tpl['title'] ?? null,
                                                'size'  => 'sm',
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     SECTION 5: MEDIA & VIDEO PBJ SHOWCASE
     ========================================================================= -->
<?php if (! empty($mediaItems)): ?>
<section class="py-5" style="background: #ffffff;">
    <div class="container">
        <div class="d-flex flex-wrap align-items-end justify-content-between mb-4">
            <div class="arena-section-head mb-0">
                <span class="arena-section-head__tag">
                    <i class="fas fa-photo-video me-1"></i> Visual &amp; Multimedia
                </span>
                <h3 class="arena-section-head__title">Media &amp; Video Pembelajaran</h3>
                <p class="arena-section-head__desc mb-0">
                    Infografik ringkas, alur diagram pengadaan, serta sajian video pembelajaran interaktif.
                </p>
            </div>
            <div class="d-flex gap-2 mt-3 mt-md-0">
                <a href="<?= route_to('public.knowledge.videos') ?>" class="btn btn-sm btn-outline-danger radius-8 px-3 py-2 fw-semibold">
                    <i class="fab fa-youtube me-1"></i> Video PBJ (<?= (int) ($stats['total_videos'] ?? 0) ?>)
                </a>
                <a href="<?= route_to('public.knowledge.media') ?>" class="btn btn-sm btn-outline-primary radius-8 px-3 py-2 fw-semibold">
                    <i class="fas fa-images me-1"></i> Semua Media (<?= (int) ($stats['total_media'] ?? 0) ?>)
                </a>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach (array_slice($mediaItems, 0, 3) as $med):
                $previewImg = $med['preview_url'] ?? base_url('assets/main/images/event/event-1-1.jpg');
                $isVideo    = ! empty($med['is_video']);
                $embedUrl   = $med['embed_url'] ?? '';
                $extUrl     = $med['external_url'] ?? '';
                $modalData  = [
                    'title'       => $med['title'] ?? '',
                    'caption'     => $med['caption'] ?? '',
                    'preview_url' => $previewImg,
                    'embed_url'   => $embedUrl,
                    'external_url'=> $extUrl,
                    'is_video'    => $isVideo,
                    'likes_count' => (int) ($med['likes_count'] ?? 24),
                    'views_count' => (int) ($med['views_count'] ?? 150),
                    'date'        => ! empty($med['published_at']) ? date('d M Y', strtotime($med['published_at'])) : 'Terbaru',
                    'hashtags'    => $med['hashtags'] ?? [],
                    'id'          => (int) $med['id'],
                ];
            ?>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <article class="card h-100 border-0 shadow-sm radius-16 overflow-hidden d-flex flex-column transition-hover bg-white" style="border: 1px solid #edf2f7 !important;">
                        <!-- Social Post Header -->
                        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <div class="media-avatar-ring">
                                    <img src="<?= base_url('assets/main/images/resources/logo-1.png') ?>" alt="Biro PBJ" class="radius-50" style="width: 34px; height: 34px; object-fit: contain; background: #f8fafc; padding: 2px;">
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-1">
                                        <strong class="text-dark" style="font-size: 13.5px;">Biro PBJ Lampung</strong>
                                        <i class="fas fa-check-circle text-primary" style="font-size: 12px;"></i>
                                    </div>
                                    <small class="text-muted" style="font-size: 11px;">
                                        <?= ! empty($med['published_at']) ? esc(date('d M Y', strtotime($med['published_at']))) : 'Terbaru' ?>
                                    </small>
                                </div>
                            </div>
                            <span class="badge radius-6 px-2 py-1 small <?= $isVideo ? 'bg-danger text-white' : 'bg-primary text-white' ?>">
                                <?= $isVideo ? '<i class="fab fa-youtube me-1"></i> Video' : '<i class="fas fa-image me-1"></i> Infografik' ?>
                            </span>
                        </div>

                        <!-- Edge-to-Edge Media Visual Preview -->
                        <div class="media-visual-container position-relative" style="height: 220px;" onclick="openMediaModal(<?= esc(json_encode($modalData)) ?>)">
                            <img src="<?= esc($previewImg) ?>" alt="<?= esc($med['title'] ?? 'Media PBJ') ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/gallery/gallery-page-1-1.jpg') ?>';">

                            <!-- Top Tag Badges -->
                            <div class="position-absolute top-0 start-0 m-3 z-2 d-flex flex-column gap-1">
                                <?php if (! empty($med['location'])): ?>
                                    <span class="badge bg-dark bg-opacity-75 text-white radius-6 px-2 py-1 small">
                                        <i class="fas fa-map-marker-alt text-danger me-1"></i> <?= esc($med['location']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Video Duration Pill or Play Overlay -->
                            <?php if ($isVideo): ?>
                                <div class="position-absolute top-50 start-50 translate-middle z-2">
                                    <div class="media-play-pulse" style="width: 52px; height: 52px;">
                                        <i class="fas fa-play ms-1" style="font-size: 18px;"></i>
                                    </div>
                                </div>
                                <?php if (! empty($med['duration'])): ?>
                                    <div class="position-absolute bottom-0 end-0 m-2 z-2">
                                        <span class="badge bg-dark bg-opacity-75 text-white radius-6 px-2 py-1 small">
                                            <i class="far fa-clock me-1 text-warning"></i> <?= esc($med['duration']) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- Hover Overlay -->
                            <div class="media-hover-overlay">
                                <div class="d-flex align-items-center justify-content-between text-white small">
                                    <span><i class="fas fa-heart text-danger me-1"></i> <?= (int) ($med['likes_count'] ?? 24) ?> Suka</span>
                                    <span><i class="fas fa-eye me-1"></i> <?= (int) ($med['views_count'] ?? 150) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Content & Engagement Bar -->
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <h4 class="fw-bold mb-1" style="font-size: 15.5px; line-height: 1.4; color: #082f5c;">
                                <?= esc($med['title'] ?? '(Tanpa judul)') ?>
                            </h4>
                            <p class="text-muted small mb-2 flex-grow-1" style="line-height: 1.45; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 12.5px;">
                                <?= esc($med['caption'] ?? '') ?>
                            </p>

                            <!-- Hashtags -->
                            <div class="mb-3">
                                <?php foreach (($med['hashtags'] ?? []) as $ht): ?>
                                    <span class="media-hashtag-pill" style="font-size: 11px;"><?= esc($ht) ?></span>
                                <?php endforeach; ?>
                            </div>

                            <!-- Social Action Footer -->
                            <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary radius-6 px-2 py-1 fw-semibold" style="font-size: 12px;" onclick="openMediaModal(<?= esc(json_encode($modalData)) ?>)">
                                        <i class="fas fa-expand me-1"></i> Lihat Media
                                    </button>
                                    <?php if (! empty($extUrl)): ?>
                                        <a href="<?= esc($extUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-danger radius-6 px-2 py-1 fw-semibold" style="font-size: 12px;">
                                            <i class="fab fa-youtube me-1"></i> Tonton
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <?= view('landing/components/bookmark_button', [
                                    'type'  => 'knowledge_media',
                                    'id'    => (int) $med['id'],
                                    'title' => $med['title'] ?? null,
                                    'size'  => 'sm',
                                ]) ?>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- =========================================================================
     SECTION 6: PREVIEW FAQ & TANYA JAWAB
     ========================================================================= -->
<?php if (! empty($faqs)): ?>
<section class="py-5" style="background: #f8fafc; border-top: 1px solid #eef2f6;">
    <div class="container">
        <div class="section-title text-center mb-4">
            <div class="section-title__tagline-box">
                <div class="section-title__tagline-shape"></div>
                <p class="section-title__tagline">Tanya Jawab Pengadaan</p>
            </div>
            <h2 class="section-title__title" style="font-size: 28px; font-weight: 800; color: #082f5c;">Pertanyaan Sering Muncul (FAQ)</h2>
            <p class="text-muted mx-auto" style="max-width: 600px; font-size: 14.5px;">
                Dapatkan kepastian jawaban atas regulasi, tata cara pembuktian kualifikasi, dan penanganan kendala kontrak.
            </p>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-9">
                <div class="accordion d-flex flex-column gap-3" id="knowledgeFaqAccordion">
                    <?php foreach (array_slice($faqs, 0, 4) as $index => $faq):
                        $collapseId = 'faqCollapse' . (int) $faq['id'];
                        $headingId  = 'faqHeading' . (int) $faq['id'];
                    ?>
                        <div class="card border-0 shadow-sm radius-12 overflow-hidden bg-white">
                            <div class="card-header bg-white border-0 p-3 p-md-4" id="<?= esc($headingId) ?>">
                                <button class="btn btn-link w-100 text-start text-decoration-none p-0 d-flex align-items-center justify-content-between collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= esc($collapseId) ?>" aria-expanded="false" aria-controls="<?= esc($collapseId) ?>">
                                    <span class="fw-bold pe-3" style="color: #082f5c; font-size: 16px; line-height: 1.4;">
                                        <i class="fas fa-question-circle text-primary me-2"></i> <?= esc($faq['title']) ?>
                                    </span>
                                    <i class="fas fa-chevron-down text-muted" style="font-size: 14px;"></i>
                                </button>
                            </div>
                            <div id="<?= esc($collapseId) ?>" class="collapse" aria-labelledby="<?= esc($headingId) ?>" data-bs-parent="#knowledgeFaqAccordion">
                                <div class="card-body pt-0 px-3 px-md-4 pb-3 pb-md-4 text-muted" style="font-size: 14.5px; line-height: 1.65; border-top: 1px dashed #edf2f7;">
                                    <?= nl2br(esc($faq['content'] ?? $faq['summary'] ?? '')) ?>
                                    <div class="mt-3 pt-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
                                        <?php if (! empty($faq['legal_basis'])): ?>
                                            <span class="badge bg-light text-secondary border px-2 py-1 small">
                                                <i class="fas fa-balance-scale me-1 text-primary"></i> <?= esc($faq['legal_basis']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small" style="font-size: 12px;">Sesuai regulasi PBJ terbaru</span>
                                        <?php endif; ?>
                                        <?= view('landing/components/bookmark_button', [
                                            'type'  => 'knowledge_faq',
                                            'id'    => (int) $faq['id'],
                                            'title' => $faq['title'] ?? null,
                                            'size'  => 'sm',
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="text-center mt-4">
                    <a href="<?= route_to('public.knowledge.faqs') ?>" class="btn btn-outline-primary radius-8 px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2">
                        <span>Lihat Seluruh Tanya Jawab (<?= (int) ($stats['total_faqs'] ?? 0) ?>)</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- =========================================================================
     SECTION 7: PILAR NILAI EKOSISTEM PENGETAHUAN
     ========================================================================= -->
<section class="py-5" style="background: #082f5c; color: #ffffff;">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-xl-6">
                <span class="arena-section-head__tag text-white mb-2" style="background: rgba(255,255,255,0.18);">
                    <i class="fas fa-shield-alt me-1 text-warning"></i> Nilai Strategis Bunga Rampai
                </span>
                <h2 class="text-white mb-3" style="font-size: 32px; font-weight: 800; line-height: 1.3;">
                    Membangun Budaya Pengadaan Berintegritas &amp; Akuntabel
                </h2>
                <p class="mb-4" style="font-size: 15px; line-height: 1.7; color: rgba(255,255,255,0.85);">
                    Bunga Rampai BegawiPBJ hadir sebagai jembatan pengetahuan untuk menyamakan persepsi regulasi, mempercepat adopsi digitalisasi pengadaan, dan melindungi aparatur pelaksana dari risiko hukum melalui pertukaran praktik terbaik.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?= site_url('bunga-rampai/artikel') ?>" class="thm-btn">
                        <i class="fas fa-search me-1"></i> Mulai Jelajahi Repositori
                    </a>
                    <a href="<?= site_url('arena-belajar') ?>" class="btn btn-outline-light radius-8 px-4 py-2 d-inline-flex align-items-center gap-2 fw-semibold">
                        <i class="fas fa-graduation-cap"></i> Ikuti Arena Belajar
                    </a>
                </div>
            </div>
            <div class="col-xl-6">
                <ul class="list-unstyled m-0 d-flex flex-column gap-3">
                    <li class="d-flex gap-3 p-3 radius-12" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
                        <span class="text-warning" style="font-size: 24px; flex-shrink: 0;"><i class="fas fa-check-circle"></i></span>
                        <div>
                            <h4 class="text-white fw-bold mb-1" style="font-size: 16px;">Selaras Standar LKPP &amp; Ketentuan Perpres</h4>
                            <p class="mb-0" style="font-size: 13px; color: rgba(255,255,255,0.75);">Setiap artikel, petunjuk teknis, dan formulir diverifikasi berdasarkan peraturan perundang-undangan PBJ mutakhir.</p>
                        </div>
                    </li>
                    <li class="d-flex gap-3 p-3 radius-12" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
                        <span class="text-warning" style="font-size: 24px; flex-shrink: 0;"><i class="fas fa-check-circle"></i></span>
                        <div>
                            <h4 class="text-white fw-bold mb-1" style="font-size: 16px;">Template Dokumen Siap Pakai &amp; Bebas Akses</h4>
                            <p class="mb-0" style="font-size: 13px; color: rgba(255,255,255,0.75);">Unduh berkas format KAK, HPS, SPK, dan BAST untuk mempercepat administrasi pengadaan di OPD masing-masing.</p>
                        </div>
                    </li>
                    <li class="d-flex gap-3 p-3 radius-12" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
                        <span class="text-warning" style="font-size: 24px; flex-shrink: 0;"><i class="fas fa-check-circle"></i></span>
                        <div>
                            <h4 class="text-white fw-bold mb-1" style="font-size: 16px;">Pembelajaran Berkelanjutan Antar-OPD</h4>
                            <p class="mb-0" style="font-size: 13px; color: rgba(255,255,255,0.75);">Media berbagi pengalaman antar Pejabat Pembuat Komitmen, Pokja Pemilihan, dan Pejabat Pengadaan se-Lampung.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     INTERACTIVE LIGHTBOX / PREVIEW MODAL (VANILLA JAVASCRIPT)
     ========================================================================= -->
<div id="mediaPreviewModal" class="media-modal-backdrop" onclick="handleBackdropClick(event)">
    <div class="media-modal-card">
        <button type="button" class="media-modal-close-btn" onclick="closeMediaModal()" aria-label="Tutup Preview">&times;</button>
        
        <!-- Left: Big Media Stage -->
        <div class="d-flex align-items-center justify-content-center p-0 flex-grow-1" style="background: #000000; min-height: 380px; max-height: 80vh;">
            <div id="modalImageContainer" class="w-100 h-100 d-flex align-items-center justify-content-center">
                <img id="modalPreviewImage" src="" alt="Preview Media" class="w-100 h-100 object-fit-contain" style="max-height: 80vh;">
            </div>
            <div id="modalVideoContainer" class="w-100 h-100 ratio ratio-16x9 d-none" style="min-height: 380px;">
                <iframe id="modalPreviewIframe" src="" title="Video Preview" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="border: 0;"></iframe>
            </div>
        </div>

        <!-- Right: Social Sidebar -->
        <div class="d-flex flex-column justify-content-between p-4 bg-white" style="width: 360px; min-width: 320px; max-height: 80vh; overflow-y: auto;">
            <div>
                <!-- Author Header -->
                <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom">
                    <img src="<?= base_url('assets/main/images/resources/logo-1.png') ?>" alt="Biro PBJ" class="radius-50 border p-1" style="width: 36px; height: 36px; object-fit: contain;">
                    <div>
                        <div class="d-flex align-items-center gap-1">
                            <strong class="text-dark" style="font-size: 13.5px;">Biro PBJ Lampung</strong>
                            <i class="fas fa-check-circle text-primary" style="font-size: 12px;"></i>
                        </div>
                        <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 11px;">
                            <span id="modalDate">Terbaru</span>
                            <span id="modalLocationWrap" class="d-none">&middot; <i class="fas fa-map-marker-alt text-danger"></i> <span id="modalLocation"></span></span>
                        </div>
                    </div>
                </div>

                <!-- Title & Caption -->
                <h4 id="modalTitle" class="fw-bold mb-2 text-dark" style="font-size: 16px; line-height: 1.4;"></h4>
                <p id="modalCaption" class="text-muted small mb-3" style="font-size: 13.5px; line-height: 1.6;"></p>

                <!-- Hashtags -->
                <div id="modalHashtags" class="mb-3"></div>
            </div>

            <!-- Bottom Engagement & Action Bar -->
            <div class="pt-3 border-top">
                <div class="d-flex align-items-center justify-content-between mb-3 small text-muted">
                    <span id="modalLikes"><i class="fas fa-heart text-danger me-1"></i> 24 Suka</span>
                    <span id="modalViews"><i class="fas fa-eye me-1"></i> 180 Dilihat</span>
                </div>
                <div class="d-flex flex-column gap-2">
                    <a id="modalExternalBtn" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-danger radius-8 fw-semibold w-100 d-none">
                        <i class="fab fa-youtube me-1"></i> Buka di Platform Asli
                    </a>
                    <button type="button" class="btn btn-sm btn-primary radius-8 fw-semibold w-100" onclick="copyModalLink()">
                        <i class="fas fa-share-alt me-1"></i> Salin Tautan Konten
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary radius-8 fw-semibold w-100" onclick="closeMediaModal()">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentModalShareTitle = '';

function openMediaModal(data) {
    const modal = document.getElementById('mediaPreviewModal');
    if (!modal) return;

    currentModalShareTitle = data.title || '';

    document.getElementById('modalTitle').textContent   = data.title || '(Tanpa Judul)';
    document.getElementById('modalCaption').textContent = data.caption || 'Tidak ada deskripsi.';
    document.getElementById('modalDate').textContent    = data.date || 'Terbaru';
    document.getElementById('modalLikes').innerHTML    = '<i class="fas fa-heart text-danger me-1"></i> ' + (data.likes_count || 24) + ' Suka';
    document.getElementById('modalViews').innerHTML    = '<i class="fas fa-eye me-1"></i> ' + (data.views_count || 150) + ' Dilihat';

    const locWrap = document.getElementById('modalLocationWrap');
    const locSpan = document.getElementById('modalLocation');
    if (locWrap && locSpan) {
        if (data.location) {
            locSpan.textContent = data.location;
            locWrap.classList.remove('d-none');
        } else {
            locWrap.classList.add('d-none');
        }
    }

    const htContainer = document.getElementById('modalHashtags');
    htContainer.innerHTML = '';
    if (Array.isArray(data.hashtags)) {
        data.hashtags.forEach(tag => {
            const pill = document.createElement('span');
            pill.className = 'media-hashtag-pill';
            pill.textContent = tag;
            htContainer.appendChild(pill);
        });
    }

    const imgBox = document.getElementById('modalImageContainer');
    const vidBox = document.getElementById('modalVideoContainer');
    const imgEl  = document.getElementById('modalPreviewImage');
    const vidIframe = document.getElementById('modalPreviewIframe');
    const extBtn = document.getElementById('modalExternalBtn');

    if (data.is_video && data.embed_url) {
        imgBox.classList.add('d-none');
        vidBox.classList.remove('d-none');
        vidIframe.src = data.embed_url;
    } else {
        vidBox.classList.add('d-none');
        imgBox.classList.remove('d-none');
        vidIframe.src = '';
        imgEl.src = data.preview_url || '';
    }

    if (data.external_url) {
        extBtn.href = data.external_url;
        extBtn.classList.remove('d-none');
    } else {
        extBtn.classList.add('d-none');
    }

    modal.classList.add('is-open');
    document.body.style.overflow = 'hidden';
}

function closeMediaModal() {
    const modal = document.getElementById('mediaPreviewModal');
    if (!modal) return;
    modal.classList.remove('is-open');
    document.body.style.overflow = '';
    const vidIframe = document.getElementById('modalPreviewIframe');
    if (vidIframe) vidIframe.src = '';
}

function handleBackdropClick(e) {
    if (e.target && e.target.id === 'mediaPreviewModal') {
        closeMediaModal();
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeMediaModal();
    }
});

function copyMediaShareUrl(title) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Tautan ' + (title ? '"' + title + '" ' : '') + 'telah disalin ke clipboard!');
        });
    } else {
        alert('Tautan disalin ke clipboard!');
    }
}

function copyModalLink() {
    copyMediaShareUrl(currentModalShareTitle);
}
</script>

<?= $this->endSection() ?>