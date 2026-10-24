<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-news.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-news.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-news.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-events.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-events.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-events.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-learning.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-learning.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-learning.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-knowledge.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-knowledge.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-knowledge.css') : '1.0' ?>" />
<style>
    .knowledge-catalog-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        border: 1px solid #edf2f7 !important;
    }

    .knowledge-catalog-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(8, 47, 92, 0.08) !important;
    }

    @media (max-width: 767.98px) {
        .insta-slide {
            height: 200px !important;
        }
    }
</style>
<?= $this->endSection() ?>

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
$featured       = $featured ?? [];
$articles       = $articles ?? [];
$caseStudies    = $caseStudies ?? [];
$templates      = $templates ?? [];
$faqs           = $faqs ?? [];
$mediaItems     = $mediaItems ?? [];
$carouselSlides = $carouselSlides ?? [];
$isLoggedIn     = session()->get('isLoggedIn') === true;

// Assemble Hero Slider Items (Layout Agenda & Arena Belajar Style)
$sliderItems = [];
$bgSliderCovers = [
    base_url('assets/main/images/backgrounds/slider-1-1.png'),
    base_url('assets/main/images/blog/blog-2-1.jpg'),
    base_url('assets/main/images/blog/blog-2-2.jpg'),
    base_url('assets/main/images/blog/blog-2-3.jpg'),
    base_url('assets/main/images/blog/blog-2-4.jpg'),
];

if (! empty($carouselSlides)) {
    foreach ($carouselSlides as $sIdx => $cSlide) {
        $cover = ! empty($cSlide['cover_image']) ? $cSlide['cover_image'] : $bgSliderCovers[$sIdx % count($bgSliderCovers)];
        $typeLabel = $cSlide['type_label'] ?? 'Artikel PBJ';
        $tagClass = match ($cSlide['type'] ?? '') {
            'case_study' => 'evt-badge-flag--coming',
            'template'   => 'evt-badge-flag--open',
            default      => 'evt-badge-flag--featured',
        };
        $statusTag = match ($cSlide['type'] ?? '') {
            'case_study' => '<iconify-icon icon="solar:danger-triangle-bold" class="align-middle"></iconify-icon> Studi Kasus',
            'template'   => '<iconify-icon icon="solar:file-check-bold" class="align-middle"></iconify-icon> Template Dokumen',
            default      => '<iconify-icon icon="solar:star-bold" class="align-middle"></iconify-icon> Sorotan Utama',
        };

        $sliderItems[] = [
            'title'       => $cSlide['title'],
            'url'         => $cSlide['url'],
            'cover_url'   => $cover,
            'tag_class'   => $tagClass,
            'status_tag'  => $statusTag,
            'type_label'  => $typeLabel,
            'badge_extra' => $cSlide['stage'] ?: 'Praktik Baik PBJ',
            'meta_1'      => '<iconify-icon icon="solar:user-circle-bold" class="text-warning align-middle"></iconify-icon> ' . esc($cSlide['author_name'] ?? 'Biro PBJ Lampung'),
            'meta_2'      => '<iconify-icon icon="solar:calendar-date-bold" class="text-info align-middle"></iconify-icon> ' . esc($cSlide['date'] ?? 'Terbaru'),
            'meta_3'      => '<iconify-icon icon="solar:clock-circle-bold" class="text-success align-middle"></iconify-icon> ' . (! empty($cSlide['reading_time']) ? (int) $cSlide['reading_time'] . ' Menit Baca' : 'Referensi Resmi'),
            'action_text' => ! empty($cSlide['is_media']) ? 'Lihat Media PBJ' : 'Baca Selengkapnya',
        ];
    }
}
?>

<!-- =========================================================================
     MAIN BUNGA RAMPAI SECTION — Clean Magazine Benchmark
     ========================================================================= -->
<section class="berita-main-section evt-main-section pt-2 pt-md-3 pt-lg-4 pb-4" id="kategori-liputan-section" style="background: #fafbfc;">
    <div class="container">

        <!-- Compact Breadcrumb + Header Title & Unified Search -->
        <div class="evt-hero-banner mb-2 mb-md-3">
            <div class="arena-breadcrumb evt-breadcrumb">
                <a href="<?= site_url('/') ?>"><iconify-icon icon="solar:home-2-bold" class="align-middle"></iconify-icon> Beranda</a>
                <span class="sep">/</span>
                <span class="current">Bunga Rampai BegawiPengadaan Lampung</span>
            </div>

            <div class="row align-items-center justify-content-between g-3 mb-2">
                <div class="col-lg-7">
                    <h1 class="evt-hero-title mb-2">
                        Pusat Pengetahuan &amp; Praktik Baik Pengadaan <br>
                        <span>Pemerintah Provinsi Lampung</span>
                    </h1>
                    <p class="evt-hero-desc mb-0">
                        Kompilasi artikel teknis, studi kasus lapangan, standar template dokumen siap pakai, tanya jawab (FAQ), dan konten multimedia Pengadaan Barang/Jasa Pemerintah.
                    </p>
                </div>
                <div class="col-lg-5">
                    <!-- Search Form -->
                    <form action="<?= site_url('bunga-rampai/artikel') ?>" method="get" class="evt-search-box arena-search-bar ms-lg-auto">
                        <iconify-icon icon="solar:magnifer-linear" class="text-slate-500 text-xl"></iconify-icon>
                        <input type="text" name="q" id="knowledgeHeroSearch" placeholder="Cari artikel PBJ, format KAK, HPS, mitigasi risiko, studi kasus, atau FAQ...">
                        <button type="submit">
                            <span>Cari Pengetahuan</span>
                            <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- HERO SLIDER (SEPERTI LAYOUT AGENDA & ARENA BELAJAR) -->
        <?php if (! empty($sliderItems)): ?>
            <div class="evt-headline mb-4">
                <div id="knowledgeHeadlineCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
                    <div class="carousel-inner radius-16 overflow-hidden">
                        <?php foreach ($sliderItems as $sIndex => $sItem): ?>
                            <div class="carousel-item <?= $sIndex === 0 ? 'active' : '' ?>">
                                <div class="evt-headline__inner">
                                    <div class="evt-headline__media">
                                        <img src="<?= esc($sItem['cover_url']) ?>" alt="<?= esc($sItem['title']) ?>" loading="<?= $sIndex === 0 ? 'eager' : 'lazy' ?>">
                                        <div class="evt-headline__badges">
                                            <span class="evt-badge-flag <?= $sItem['tag_class'] ?>">
                                                <?= $sItem['status_tag'] ?>
                                            </span>
                                            <span class="evt-badge-pill"><?= esc($sItem['type_label']) ?></span>
                                            <?php if (! empty($sItem['badge_extra'])): ?>
                                                <span class="evt-badge-pill evt-badge-pill--cert">
                                                    <iconify-icon icon="solar:shield-check-bold" class="align-middle"></iconify-icon> <?= esc($sItem['badge_extra']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="evt-headline__counter">
                                            <span class="dot"></span>
                                            <span class="d-none d-sm-inline">Sorotan Utama </span><span>#<?= $sIndex + 1 ?> / <?= count($sliderItems) ?></span>
                                        </div>
                                        <div class="evt-headline__caption">
                                            <div class="evt-headline__meta-row">
                                                <span><?= $sItem['meta_1'] ?></span>
                                                <span>&bull;</span>
                                                <span><?= $sItem['meta_2'] ?></span>
                                                <span>&bull;</span>
                                                <span><?= $sItem['meta_3'] ?></span>
                                            </div>
                                            <h2 class="evt-headline__title">
                                                <a href="<?= esc($sItem['url']) ?>">
                                                    <?= esc($sItem['title']) ?>
                                                </a>
                                            </h2>
                                            <div class="evt-headline__action">
                                                <a href="<?= esc($sItem['url']) ?>" class="btn btn-sm btn-primary px-3 py-1 radius-8 fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                                                    <span><?= esc($sItem['action_text']) ?></span>
                                                    <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($sliderItems) > 1): ?>
                        <button class="evt-headline__nav prev" type="button" data-bs-target="#knowledgeHeadlineCarousel" data-bs-slide="prev" aria-label="Sebelumnya">
                            <iconify-icon icon="solar:alt-arrow-left-linear"></iconify-icon>
                        </button>
                        <button class="evt-headline__nav next" type="button" data-bs-target="#knowledgeHeadlineCarousel" data-bs-slide="next" aria-label="Selanjutnya">
                            <iconify-icon icon="solar:alt-arrow-right-linear"></iconify-icon>
                        </button>
                        <div class="carousel-indicators evt-headline__indicators">
                            <?php foreach ($sliderItems as $sIndex => $sItem): ?>
                                <button type="button" data-bs-target="#knowledgeHeadlineCarousel" data-bs-slide-to="<?= $sIndex ?>" class="<?= $sIndex === 0 ? 'active' : '' ?>" aria-current="<?= $sIndex === 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $sIndex + 1 ?>"></button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- MAIN 2-COLUMN LAYOUT -->
        <div class="row g-3 g-lg-4">
            <!-- Left Column: Main Knowledge Content (col-lg-8) -->
            <div class="col-lg-8">

                <!-- Showcase: Media & Video Pembelajaran (Visual & Multimedia - PALING ATAS) -->
                <?php if (! empty($mediaItems)): ?>
                    <div class="mb-4 pb-2">
                        <div class="d-flex flex-wrap align-items-end justify-content-between mb-3">
                            <div class="arena-section-head mb-0">
                                <span class="arena-section-head__tag" style="background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; font-weight: 600;">
                                    <i class="fas fa-photo-video me-1"></i> Multimedia PBJ
                                </span>
                                <h3 class="arena-section-head__title" style="font-size: 22px;">Media &amp; Video Pembelajaran</h3>
                                <p class="arena-section-head__desc mb-0" style="font-size: 14.5px;">
                                    Infografik alur pengadaan pemerintah, bagan regulasi, tutorial interaktif, dan galeri kegiatan.
                                </p>
                            </div>
                            <div class="d-flex gap-2 mt-2 mt-md-0">
                                <a href="<?= site_url('bunga-rampai/multimedia') ?>" class="btn btn-outline-primary btn-sm radius-8 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 12.5px;">
                                    <i class="fas fa-photo-video me-1"></i> Semua Multimedia PBJ &rarr;
                                </a>
                            </div>
                        </div>

                        <div class="row g-3">
                            <?php foreach (array_slice($mediaItems, 0, 4) as $med):
                                $previewImg  = $med['preview_url'] ?? base_url('assets/main/images/event/event-1-1.jpg');
                                $isVideo     = ! empty($med['is_video']);
                                $embedUrl    = $med['embed_url'] ?? '';
                                $extUrl      = $med['external_url'] ?? '';
                                $slides      = $med['slides'] ?? [];
                                if (empty($slides)) {
                                    $slides = [[
                                        'url'       => $previewImg,
                                        'is_video'  => $isVideo,
                                        'embed_url' => $embedUrl,
                                        'title'     => $med['title'] ?? '',
                                    ]];
                                }
                                $slidesCount = count($slides);
                                $modalData   = [
                                    'title'        => $med['title'] ?? '',
                                    'caption'      => $med['caption'] ?? '',
                                    'preview_url'  => $previewImg,
                                    'embed_url'    => $embedUrl,
                                    'external_url' => $extUrl,
                                    'is_video'     => $isVideo,
                                    'likes_count'  => (int) ($med['likes_count'] ?? 24),
                                    'views_count'  => (int) ($med['views_count'] ?? 150),
                                    'shares_count' => (int) ($med['shares_count'] ?? 5),
                                    'date'         => ! empty($med['published_at']) ? date('d M Y', strtotime($med['published_at'])) : 'Terbaru',
                                    'hashtags'     => $med['hashtags'] ?? [],
                                    'location'     => $med['location'] ?? '',
                                    'duration'     => $med['duration'] ?? '',
                                    'id'           => (int) $med['id'],
                                    'slides'       => $slides,
                                    'slides_count' => $slidesCount,
                                ];
                                $itemType = (string) ($med['type'] ?? '');
                                if ($isVideo || in_array($itemType, ['youtube', 'video_file'], true)) {
                                    $badgeClass = 'bg-danger text-white';
                                    $badgeIcon  = '<i class="fab fa-youtube me-1"></i> Video PBJ';
                                } elseif ($itemType === 'gallery') {
                                    $badgeClass = 'bg-dark text-white';
                                    $badgeIcon  = '<i class="fas fa-camera me-1"></i> Dokumentasi';
                                } else {
                                    $badgeClass = 'bg-primary text-white';
                                    $badgeIcon  = '<i class="fas fa-image me-1"></i> Infografik';
                                }
                            ?>
                                <div class="col-md-6">
                                    <article class="card h-100 border-0 shadow-2xs radius-12 overflow-hidden bg-white knowledge-catalog-card position-relative">
                                        <!-- Post Header (Instagram style) -->
                                        <div class="d-flex align-items-center justify-content-between p-2 px-3 border-bottom bg-light bg-opacity-50">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="media-avatar-ring">
                                                    <img src="<?= base_url('assets/main/images/resources/logo-1.png') ?>" alt="Biro PBJ" class="radius-50" style="width: 26px; height: 26px; object-fit: contain; background: #fff; padding: 1px;">
                                                </div>
                                                <div>
                                                    <strong class="text-dark d-block text-truncate" style="font-size: 12px; max-width: 170px;">Biro PBJ Lampung <i class="fas fa-check-circle text-primary" style="font-size: 10px;"></i></strong>
                                                    <small class="text-muted" style="font-size: 10.5px;"><?= ! empty($med['published_at']) ? esc(date('d M Y', strtotime($med['published_at']))) : 'Terbaru' ?></small>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-1">
                                                <?php if ((int) ($med['featured'] ?? 0) === 1): ?>
                                                    <span class="badge bg-warning text-dark px-2 py-1 radius-4 small fw-semibold" style="font-size: 10px;">
                                                        <i class="fas fa-star me-1"></i> Unggulan
                                                    </span>
                                                <?php endif; ?>
                                                <span class="badge radius-4 px-2 py-1 small fw-semibold <?= $badgeClass ?>" style="font-size: 10px;">
                                                    <?= $badgeIcon ?>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Instagram Carousel Slider Container -->
                                        <?php if ($slidesCount > 1): ?>
                                            <div class="insta-carousel position-relative" id="instaCarouselHome_<?= (int) $med['id'] ?>" data-item-id="<?= (int) $med['id'] ?>">
                                                <div class="insta-track" id="instaTrackHome_<?= (int) $med['id'] ?>" style="transform: translateX(0%);">
                                                    <?php foreach ($slides as $sIdx => $slide):
                                                        $isSlideVideo = ! empty($slide['is_video']);
                                                        $slideImg     = $slide['url'] ?? $previewImg;
                                                    ?>
                                                        <div class="insta-slide" style="height: 200px;" onclick="openMediaModalSlide(<?= esc(json_encode($modalData)) ?>, <?= $sIdx ?>)">
                                                            <img src="<?= esc($slideImg) ?>" alt="<?= esc($slide['title'] ?? ($med['title'] ?? 'Media PBJ')) ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/gallery/gallery-page-1-1.jpg') ?>';">

                                                            <?php if ($isSlideVideo): ?>
                                                                <div class="position-absolute top-50 start-50 translate-middle z-2">
                                                                    <div class="media-play-pulse" style="width: 44px; height: 44px;">
                                                                        <i class="fas fa-play ms-1" style="font-size: 16px;"></i>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>

                                                            <div class="position-absolute bottom-0 end-0 m-2 z-2">
                                                                <span class="badge bg-dark bg-opacity-75 text-white radius-20 px-2 py-0 small" style="font-size: 10.5px;">
                                                                    <i class="fas fa-expand-arrows-alt me-1"></i> Perbesar
                                                                </span>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>

                                                <!-- Instagram Counter Badge Top Right (e.g. 1/4) -->
                                                <div class="position-absolute top-0 end-0 m-2 z-3">
                                                    <span class="badge radius-20 px-2 py-1 insta-counter-badge" id="instaCounterHome_<?= (int) $med['id'] ?>" style="background: rgba(15, 23, 42, 0.72); backdrop-filter: blur(6px); color: #fff; font-size: 10.5px; font-weight: 600;">
                                                        <i class="fas fa-clone me-1 text-white-50"></i> <span class="current-slide">1</span>/<?= $slidesCount ?>
                                                    </span>
                                                </div>

                                                <!-- Circular Prev Arrow (Initially Hidden on First Slide) -->
                                                <button type="button" class="insta-arrow-btn insta-arrow-prev d-none" id="instaPrevHome_<?= (int) $med['id'] ?>" aria-label="Slide Sebelumnya" onclick="navigateInstaSlideHome(event, <?= (int) $med['id'] ?>, -1, <?= $slidesCount ?>)">
                                                    <i class="fas fa-chevron-left"></i>
                                                </button>

                                                <!-- Circular Next Arrow -->
                                                <button type="button" class="insta-arrow-btn insta-arrow-next" id="instaNextHome_<?= (int) $med['id'] ?>" aria-label="Slide Selanjutnya" onclick="navigateInstaSlideHome(event, <?= (int) $med['id'] ?>, 1, <?= $slidesCount ?>)">
                                                    <i class="fas fa-chevron-right"></i>
                                                </button>

                                                <!-- Instagram Dots Indicator Bottom Center -->
                                                <div class="insta-dots-indicator" id="instaDotsHome_<?= (int) $med['id'] ?>">
                                                    <?php for ($d = 0; $d < $slidesCount; $d++): ?>
                                                        <span class="insta-dot <?= $d === 0 ? 'active' : '' ?>" onclick="goToInstaSlideHome(event, <?= (int) $med['id'] ?>, <?= $d ?>, <?= $slidesCount ?>)"></span>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="media-visual-container position-relative" style="height: 200px; cursor: pointer;" onclick="openMediaModal(<?= esc(json_encode($modalData)) ?>)">
                                                <img src="<?= esc($previewImg) ?>" alt="<?= esc($med['title'] ?? 'Media PBJ') ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/gallery/gallery-page-1-1.jpg') ?>';">

                                                <?php if ($isVideo): ?>
                                                    <div class="position-absolute top-50 start-50 translate-middle z-2">
                                                        <div class="media-play-pulse" style="width: 44px; height: 44px;">
                                                            <i class="fas fa-play ms-1" style="font-size: 16px;"></i>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="position-absolute bottom-0 end-0 m-2 z-2">
                                                    <span class="badge bg-dark bg-opacity-75 text-white radius-20 px-2 py-0 small" style="font-size: 10.5px;">
                                                        <i class="fas fa-expand-arrows-alt me-1"></i> Perbesar
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Card Body -->
                                        <div class="p-3 d-flex flex-column flex-grow-1">
                                            <h4 class="fw-bold mb-1 text-truncate" style="font-size: 14.5px; color: #082f5c;">
                                                <?= esc($med['title'] ?? '(Tanpa judul)') ?>
                                            </h4>
                                            <p class="small text-muted mb-2 text-truncate" style="font-size: 12.5px;">
                                                <?= esc(mb_strimwidth($med['caption'] ?? '', 0, 75, '…')) ?>
                                            </p>
                                            <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                                                <div class="d-flex align-items-center gap-2 small text-muted" style="font-size: 11.5px;">
                                                    <span class="text-danger"><i class="fas fa-heart me-1"></i> <?= (int) ($med['likes_count'] ?? 24) ?></span>
                                                    <span><i class="fas fa-eye me-1"></i> <?= (int) ($med['views_count'] ?? 150) ?></span>
                                                    <?php if ($slidesCount > 1): ?>
                                                        <span class="text-primary"><i class="fas fa-clone me-1"></i> <?= $slidesCount ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="d-flex align-items-center gap-1">
                                                    <button type="button" class="btn btn-sm btn-outline-primary radius-6 px-2 py-1 fw-semibold" style="font-size: 11.5px;" onclick="openMediaModal(<?= esc(json_encode($modalData)) ?>)">
                                                        <i class="fas fa-expand me-1"></i> Buka
                                                    </button>
                                                    <?= view('landing/components/bookmark_button', [
                                                        'type'  => 'knowledge_media',
                                                        'id'    => (int) $med['id'],
                                                        'title' => $med['title'] ?? null,
                                                        'size'  => 'sm',
                                                        'iconOnly' => true,
                                                    ]) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Showcase 1: Rekomendasi Utama Pengetahuan (Pilihan Editor) -->
                <?php if (! empty($featured)): ?>
                    <div class="mb-4 pb-2">
                        <div class="d-flex flex-wrap align-items-end justify-content-between mb-3">
                            <div class="arena-section-head mb-0">
                                <span class="arena-section-head__tag">
                                    <i class="fas fa-star me-1 text-warning"></i> Konten Pilihan Editor
                                </span>
                                <h3 class="arena-section-head__title" style="font-size: 22px;">Rekomendasi Utama Pengetahuan</h3>
                                <p class="arena-section-head__desc mb-0" style="font-size: 14.5px;">
                                    Materi esensial yang direkomendasikan langsung oleh fasilitator dan tim teknis pengadaan daerah.
                                </p>
                            </div>
                            <a href="<?= site_url('bunga-rampai/artikel?featured=1') ?>" class="btn btn-outline-primary btn-sm radius-8 px-3 py-1 mt-2 mt-md-0 d-inline-flex align-items-center gap-2 fw-semibold" style="font-size: 13px;">
                                <span>Lihat Semua Rekomendasi</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="row g-3">
                            <?php foreach (array_slice($featured, 0, 4) as $index => $feat):
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
                                <div class="col-md-6">
                                    <article class="arena-card h-100">
                                        <div class="arena-card__thumb">
                                            <img src="<?= esc($coverImg) ?>" alt="<?= esc($feat['title']) ?>" loading="lazy" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/blog/blog-2-1.jpg') ?>';">
                                            <div class="arena-card__badge-row">
                                                <span class="arena-pill-badge <?= esc($badgeBg) ?>">
                                                    <?= esc($typeLabel) ?>
                                                </span>
                                            </div>
                                            <div class="arena-card-floating-bookmark">
                                                <?= view('landing/components/bookmark_button', [
                                                    'type'      => $fBookmarkType,
                                                    'id'        => (int) $feat['id'],
                                                    'title'     => $feat['title'] ?? null,
                                                    'iconOnly'  => true,
                                                    'size'      => 'sm',
                                                ]) ?>
                                            </div>
                                        </div>
                                        <div class="arena-card__body">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <span class="arena-card__category mb-0 text-truncate" style="max-width: 180px;">
                                                    <?= esc($feat['procurement_stage'] ?: 'Praktik PBJ') ?>
                                                </span>
                                                <span class="badge border small" style="font-size: 11px; background: #f8fafc; color: #0f172a; border-color: #cbd5e1; font-weight: 600;">
                                                    <i class="far fa-clock me-1 text-primary"></i> <?= esc($feat['reading_time'] ?? 5) ?> mnt
                                                </span>
                                            </div>
                                            <h4 class="arena-card__title">
                                                <a href="<?= esc($fUrl) ?>" class="hover-primary" title="<?= esc($feat['title']) ?>">
                                                    <?= esc($feat['title']) ?>
                                                </a>
                                            </h4>
                                            <p class="arena-card__text">
                                                <?= esc(mb_strimwidth($feat['summary'] ?? '', 0, 95, '…')) ?>
                                            </p>
                                            <div class="arena-card__meta">
                                                <span><i class="fas fa-user-circle text-primary"></i> <?= esc($feat['author_name'] ?? 'Biro PBJ') ?></span>
                                                <span><i class="fas fa-certificate text-success"></i> Standar</span>
                                            </div>
                                            <div class="arena-card__actions mt-auto">
                                                <a href="<?= esc($fUrl) ?>" class="arena-btn-primary flex-grow-1 justify-content-center">
                                                    <span>Baca Ulasan</span>
                                                    <i class="fas fa-arrow-right"></i>
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
                <?php endif; ?>

                <!-- Showcase 2: Artikel Pengetahuan Terbaru -->
                <?php if (! empty($articles)): ?>
                    <div class="mb-4 pb-2">
                        <div class="d-flex flex-wrap align-items-end justify-content-between mb-3">
                            <div class="arena-section-head mb-0">
                                <span class="arena-section-head__tag">
                                    <i class="fas fa-newspaper me-1"></i> Wawasan &amp; Regulasi
                                </span>
                                <h3 class="arena-section-head__title" style="font-size: 22px;">Artikel Pengetahuan Terbaru</h3>
                                <p class="arena-section-head__desc mb-0" style="font-size: 14.5px;">
                                    Ulasan analisis, panduan teknis, dan praktik terbaik pengadaan barang/jasa terkini.
                                </p>
                            </div>
                            <a href="<?= route_to('public.knowledge.articles') ?>" class="btn btn-outline-primary btn-sm radius-8 px-3 py-1 mt-2 mt-md-0 d-inline-flex align-items-center gap-2 fw-semibold" style="font-size: 13px;">
                                <span>Lihat Semua Artikel (<?= (int) ($stats['total_articles'] ?? 0) ?>)</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="row g-3">
                            <?php foreach (array_slice($articles, 0, 4) as $art):
                                $coverImg = ! empty($art['cover_file_id'])
                                    ? site_url('file/' . (int) $art['cover_file_id'])
                                    : base_url('assets/main/images/blog/blog-2-' . (((int) $art['id'] % 4) + 1) . '.jpg');
                            ?>
                                <div class="col-md-6">
                                    <article class="arena-card h-100">
                                        <div class="arena-card__thumb">
                                            <img src="<?= esc($coverImg) ?>" alt="<?= esc($art['title']) ?>" loading="lazy" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/blog/blog-2-2.jpg') ?>';">
                                            <div class="arena-card__badge-row">
                                                <?php if ((int) ($art['featured'] ?? 0) === 1): ?>
                                                    <span class="arena-pill-badge bg-warning text-dark">
                                                        <i class="fas fa-star me-1"></i> Unggulan
                                                    </span>
                                                <?php endif; ?>
                                                <span class="arena-pill-badge bg-primary text-white">
                                                    <i class="fas fa-newspaper me-1"></i> Artikel PBJ
                                                </span>
                                            </div>
                                            <div class="arena-card-floating-bookmark">
                                                <?= view('landing/components/bookmark_button', [
                                                    'type'      => 'knowledge_article',
                                                    'id'        => (int) $art['id'],
                                                    'title'     => $art['title'] ?? null,
                                                    'iconOnly'  => true,
                                                    'size'      => 'sm',
                                                ]) ?>
                                            </div>
                                        </div>
                                        <div class="arena-card__body">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <span class="arena-card__category mb-0 text-truncate" style="max-width: 170px;">
                                                    <?= esc($art['procurement_stage'] ?: 'Regulasi & Teknis') ?>
                                                </span>
                                                <span class="badge border small" style="font-size: 11px; background: #f8fafc; color: #0f172a; border-color: #cbd5e1; font-weight: 600;">
                                                    <i class="far fa-calendar-alt me-1 text-primary"></i> <?= ! empty($art['published_at']) ? esc(date('d M Y', strtotime($art['published_at']))) : 'Terbaru' ?>
                                                </span>
                                            </div>
                                            <h4 class="arena-card__title">
                                                <a href="<?= route_to('public.knowledge.article.detail', $art['slug']) ?>" class="hover-primary" title="<?= esc($art['title']) ?>">
                                                    <?= esc($art['title']) ?>
                                                </a>
                                            </h4>
                                            <p class="arena-card__text">
                                                <?= esc(mb_strimwidth($art['summary'] ?? '', 0, 95, '…')) ?>
                                            </p>
                                            <div class="arena-card__meta">
                                                <span><i class="fas fa-user-circle text-primary"></i> <?= esc($art['author_name'] ?? 'Biro PBJ') ?></span>
                                                <span><i class="far fa-clock text-warning"></i> <?= esc($art['reading_time'] ?? 5) ?> mnt</span>
                                            </div>
                                            <div class="arena-card__actions mt-auto">
                                                <a href="<?= route_to('public.knowledge.article.detail', $art['slug']) ?>" class="arena-btn-primary flex-grow-1 justify-content-center">
                                                    <span>Baca Artikel</span>
                                                    <i class="fas fa-arrow-right"></i>
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
                <?php endif; ?>

                <!-- Showcase 3: Studi Kasus Lapangan -->
                <div class="mb-4 pb-2">
                    <div class="d-flex flex-wrap align-items-end justify-content-between mb-3">
                        <div class="arena-section-head mb-0">
                            <span class="arena-section-head__tag text-danger" style="background: #fde8e8;">
                                <i class="fas fa-briefcase me-1"></i> Pelajaran Nyata &amp; Mitigasi Risiko
                            </span>
                            <h3 class="arena-section-head__title" style="font-size: 22px;">Studi Kasus Lapangan</h3>
                            <p class="arena-section-head__desc mb-0" style="font-size: 14.5px;">
                                Pelajaran berharga dari dinamika audit, sanggahan tender, dan mitigasi kontrak kritis.
                            </p>
                        </div>
                        <a href="<?= route_to('public.knowledge.case-studies') ?>" class="btn btn-outline-danger btn-sm radius-8 px-3 py-1 mt-2 mt-md-0 d-inline-flex align-items-center gap-2 fw-semibold" style="font-size: 13px;">
                            <span>Semua Kasus (<?= (int) ($stats['total_cases'] ?? 0) ?>)</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <?php if (empty($caseStudies)): ?>
                        <div class="alert alert-light text-center border p-4 radius-12">Belum ada studi kasus yang dipublikasikan.</div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach (array_slice($caseStudies, 0, 4) as $cs):
                                $sev = strtolower((string) ($cs['case_severity'] ?? ''));
                                $sevBadge = match ($sev) {
                                    'critical' => ['label' => 'Risiko Kritis', 'badge' => 'background: #fee2e2; color: #991b1b; border: 1px solid #f87171; font-weight: 700;', 'icon' => 'fa-exclamation-triangle'],
                                    'high'     => ['label' => 'Risiko Tinggi', 'badge' => 'background: #ffedd5; color: #9a3412; border: 1px solid #fb923c; font-weight: 700;', 'icon' => 'fa-exclamation-circle'],
                                    'low'      => ['label' => 'Risiko Rendah', 'badge' => 'background: #e0f2fe; color: #075985; border: 1px solid #38bdf8; font-weight: 700;', 'icon' => 'fa-info-circle'],
                                    default    => ['label' => 'Risiko Sedang', 'badge' => 'background: #fef9c3; color: #713f12; border: 1px solid #facc15; font-weight: 700;', 'icon' => 'fa-shield-alt'],
                                };
                            ?>
                                <div class="col-md-6">
                                    <article class="arena-card h-100 border-start border-3 border-danger">
                                        <div class="arena-card__body p-3">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="d-flex align-items-center gap-1">
                                                    <?php if ((int) ($cs['featured'] ?? 0) === 1): ?>
                                                        <span class="badge bg-warning text-dark px-2 py-1 radius-4 small fw-semibold" style="font-size: 11px;">
                                                            <i class="fas fa-star me-1"></i> Unggulan
                                                        </span>
                                                    <?php endif; ?>
                                                    <span class="badge px-2 py-1 radius-4 small" style="<?= $sevBadge['badge'] ?> font-size: 11px;">
                                                        <i class="fas <?= $sevBadge['icon'] ?> me-1"></i> <?= esc($sevBadge['label']) ?>
                                                    </span>
                                                </div>
                                                <span class="badge border px-2 py-1 radius-4 font-monospace" style="font-size: 11px; background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; font-weight: 600;">
                                                    <?= esc($cs['procurement_stage'] ?: 'Kasus PBJ') ?>
                                                </span>
                                            </div>
                                            <h4 class="fw-bold mb-2" style="font-size: 16px; line-height: 1.4;">
                                                <a href="<?= route_to('public.knowledge.case-study.detail', $cs['slug']) ?>" class="text-dark text-decoration-none hover-primary">
                                                    <?= esc($cs['title']) ?>
                                                </a>
                                            </h4>
                                            <p class="small text-muted mb-2 text-truncate" style="font-size: 13px;">
                                                <?= esc(mb_strimwidth($cs['summary'] ?? '', 0, 85, '…')) ?>
                                            </p>
                                            <?php if (! empty($cs['resolution_summary'])): ?>
                                                <div class="p-2 radius-6 mb-2 small text-truncate" style="background: #fff7ed; border-left: 3px solid #ea580c; color: #7c2d12; font-size: 12px; font-weight: 600;">
                                                    <strong>Pelajaran:</strong> <?= esc(mb_strimwidth($cs['resolution_summary'], 0, 85, '…')) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                                                <a href="<?= route_to('public.knowledge.case-study.detail', $cs['slug']) ?>" class="small text-danger fw-bold text-decoration-none hover-underline">
                                                    Pelajari Solusi &rarr;
                                                </a>
                                                <?= view('landing/components/bookmark_button', [
                                                    'type'  => 'knowledge_case',
                                                    'id'    => (int) $cs['id'],
                                                    'title' => $cs['title'] ?? null,
                                                    'size'  => 'sm',
                                                ]) ?>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Showcase 4: Template Dokumen PBJ -->
                <div class="mb-4 pb-2">
                    <div class="d-flex flex-wrap align-items-end justify-content-between mb-3">
                        <div class="arena-section-head mb-0">
                            <span class="arena-section-head__tag text-warning" style="background: #fef3c7;">
                                <i class="fas fa-file-invoice me-1"></i> Standar Berkas Siap Pakai
                            </span>
                            <h3 class="arena-section-head__title" style="font-size: 22px;">Template Dokumen PBJ</h3>
                            <p class="arena-section-head__desc mb-0" style="font-size: 14.5px;">
                                Standar formulir format KAK, HPS, SPK, kontrak, dan BAST siap unduh yang terstandardisasi.
                            </p>
                        </div>
                        <a href="<?= route_to('public.knowledge.templates') ?>" class="btn btn-outline-primary btn-sm radius-8 px-3 py-1 mt-2 mt-md-0 d-inline-flex align-items-center gap-2 fw-semibold" style="font-size: 13px;">
                            <span>Semua Template (<?= (int) ($stats['total_templates'] ?? 0) ?>)</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <?php if (empty($templates)): ?>
                        <div class="alert alert-light text-center border p-4 radius-12">Belum ada template dokumen yang dipublikasikan.</div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach (array_slice($templates, 0, 4) as $tpl):
                                $ext = strtoupper($tpl['file_extension'] ?? 'DOCX');
                                $extBadge = match ($ext) {
                                    'XLSX', 'XLS' => ['badge' => 'background: #dcfce7; color: #14532d; border: 1px solid #86efac;', 'icon' => 'fa-file-excel'],
                                    'PDF'         => ['badge' => 'background: #fee2e2; color: #7f1d1d; border: 1px solid #fca5a5;', 'icon' => 'fa-file-pdf'],
                                    default       => ['badge' => 'background: #e0e7ff; color: #312e81; border: 1px solid #a5b4fc;', 'icon' => 'fa-file-word'],
                                };
                            ?>
                                <div class="col-md-6">
                                    <article class="arena-card h-100">
                                        <div class="arena-card__body p-3">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="d-flex align-items-center gap-1">
                                                    <?php if ((int) ($tpl['featured'] ?? 0) === 1): ?>
                                                        <span class="badge bg-warning text-dark px-2 py-1 radius-4 small fw-semibold" style="font-size: 11px;">
                                                            <i class="fas fa-star me-1"></i> Unggulan
                                                        </span>
                                                    <?php endif; ?>
                                                    <span class="badge px-2 py-1 radius-4 font-monospace fw-bold" style="<?= $extBadge['badge'] ?> font-size: 11px;">
                                                        <i class="fas <?= $extBadge['icon'] ?> me-1"></i> <?= esc($ext) ?>
                                                    </span>
                                                </div>
                                                <span class="badge border px-2 py-1 radius-4 font-monospace" style="font-size: 11px; background: #f8fafc; color: #0f172a; border-color: #cbd5e1; font-weight: 600;">
                                                    <i class="fas fa-code-branch me-1 text-primary"></i> <?= esc($tpl['document_version'] ?? 'v1.0') ?>
                                                </span>
                                            </div>
                                            <h4 class="fw-bold mb-1" style="font-size: 15.5px; line-height: 1.4;">
                                                <a href="<?= route_to('public.knowledge.templates.detail', $tpl['slug']) ?>" class="text-dark text-decoration-none hover-primary">
                                                    <?= esc($tpl['title']) ?>
                                                </a>
                                            </h4>
                                            <p class="small text-muted mb-3 text-truncate" style="font-size: 12.5px;">
                                                <?= esc(mb_strimwidth($tpl['summary'] ?? '', 0, 80, '…')) ?>
                                            </p>
                                            <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                                                <div class="d-flex align-items-center gap-1">
                                                    <a href="<?= route_to('public.knowledge.templates.download', $tpl['slug']) ?>" class="btn btn-sm btn-primary radius-6 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 12px;">
                                                        <i class="fas fa-download"></i> Unduh
                                                    </a>
                                                    <a href="<?= route_to('public.knowledge.templates.detail', $tpl['slug']) ?>" class="btn btn-sm radius-6 px-2 py-1 fw-semibold" style="background: #f1f5f9; color: #0f172a; border: 1px solid #cbd5e1; font-size: 12px;">
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
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Showcase 6: FAQ Accordion -->
                <?php if (! empty($faqs)): ?>
                    <div class="card border-0 radius-16 p-4 shadow-sm bg-white mb-4">
                        <div class="arena-section-head mb-3">
                            <span class="arena-section-head__tag text-success" style="background: #ecfdf5;">
                                <i class="fas fa-question-circle me-1"></i> Tanya Jawab Pengadaan
                            </span>
                            <h3 class="arena-section-head__title" style="font-size: 20px;">Pertanyaan Sering Muncul (FAQ)</h3>
                            <p class="arena-section-head__desc mb-0" style="font-size: 14px;">
                                Dapatkan kepastian jawaban atas regulasi, tata cara pembuktian kualifikasi, dan penanganan kendala kontrak.
                            </p>
                        </div>
                        <div class="accordion border-0" id="knowledgeFaqAccordion">
                            <?php foreach (array_slice($faqs, 0, 4) as $index => $faq):
                                $collapseId = 'faqCollapse' . (int) $faq['id'];
                                $headingId  = 'faqHeading' . (int) $faq['id'];
                            ?>
                                <div class="accordion-item radius-10 border mb-2 overflow-hidden shadow-2xs">
                                    <h2 class="accordion-header" id="<?= esc($headingId) ?>">
                                        <button class="accordion-button fw-bold text-dark py-2 px-3 <?= $index === 0 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?= esc($collapseId) ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="<?= esc($collapseId) ?>" style="font-size: 14.5px;">
                                            <i class="fas fa-question-circle text-primary me-2"></i> <?= esc($faq['title']) ?>
                                        </button>
                                    </h2>
                                    <div id="<?= esc($collapseId) ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="<?= esc($headingId) ?>" data-bs-parent="#knowledgeFaqAccordion">
                                        <div class="accordion-body text-muted py-2 px-3" style="font-size: 13.5px; line-height: 1.6;">
                                            <?= nl2br(esc($faq['content'] ?? $faq['summary'] ?? '')) ?>
                                            <div class="mt-2 pt-2 d-flex flex-wrap align-items-center justify-content-between gap-2 border-top">
                                                <?php if (! empty($faq['legal_basis'])): ?>
                                                    <span class="badge border px-2 py-1 small" style="font-size: 11.5px; background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; font-weight: 600;">
                                                        <i class="fas fa-balance-scale me-1 text-primary"></i> <?= esc($faq['legal_basis']) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="small fw-semibold" style="font-size: 11.5px; color: #475569;">Sesuai regulasi PBJ terbaru</span>
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
                        <div class="text-center mt-3">
                            <a href="<?= route_to('public.knowledge.faqs') ?>" class="btn btn-sm btn-outline-primary radius-8 px-4 py-2 fw-semibold">
                                Lihat Seluruh Tanya Jawab (<?= (int) ($stats['total_faqs'] ?? 0) ?>) &rarr;
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Right Column: Sidebar (col-lg-4) — Modeled after Clean News Benchmark -->
            <div class="col-lg-4">

                <!-- Sidebar Widget 1: Quick Search Box -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-search"></i>
                        <span>Cari di Bunga Rampai</span>
                    </h4>
                    <form action="<?= site_url('bunga-rampai/artikel') ?>" method="get" class="berita-searchbox news-search-box">
                        <input type="text" name="q" placeholder="Cari artikel, kasus, template...">
                        <button type="submit" aria-label="Cari"><i class="fas fa-search"></i></button>
                    </form>
                    <div class="d-flex flex-wrap gap-1 mt-3">
                        <a href="<?= site_url('bunga-rampai/artikel?q=' . urlencode('Perpres 12/2021')) ?>" class="badge border text-decoration-none" style="background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; font-size: 11.5px; font-weight: 600;">Perpres 12/2021</a>
                        <a href="<?= site_url('bunga-rampai/artikel?q=' . urlencode('Penyusunan HPS')) ?>" class="badge border text-decoration-none" style="background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; font-size: 11.5px; font-weight: 600;">Penyusunan HPS</a>
                        <a href="<?= site_url('bunga-rampai/artikel?q=' . urlencode('Format KAK')) ?>" class="badge border text-decoration-none" style="background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; font-size: 11.5px; font-weight: 600;">Format KAK</a>
                        <a href="<?= site_url('bunga-rampai/artikel?q=' . urlencode('Mitigasi Risiko')) ?>" class="badge border text-decoration-none" style="background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; font-size: 11.5px; font-weight: 600;">Mitigasi Risiko</a>
                        <a href="<?= site_url('bunga-rampai/artikel?q=' . urlencode('Sanggahan')) ?>" class="badge border text-decoration-none" style="background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; font-size: 11.5px; font-weight: 600;">Sanggahan</a>
                    </div>
                </div>

                <!-- Sidebar Widget 2: Kategori Bunga Rampai -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-folder-open" style="background: #fef3c7; color: #d97706;"></i>
                        <span>Kategori Bunga Rampai</span>
                    </h4>
                    <nav class="arena-nav-pills berita-catlist" aria-label="Kategori Bunga Rampai">
                        <a href="<?= site_url('bunga-rampai') ?>" class="arena-nav-pill is-active">
                            <span class="left"><span class="dot" style="background:#0284c7"></span>Semua Eksplorasi</span>
                            <span class="count"><?= (int) ($stats['total_all'] ?? 0) ?></span>
                        </a>
                        <a href="<?= site_url('bunga-rampai/artikel') ?>" class="arena-nav-pill">
                            <span class="left"><span class="dot" style="background:#0284c7"></span>Artikel PBJ</span>
                            <span class="count"><?= (int) ($stats['total_articles'] ?? 0) ?></span>
                        </a>
                        <a href="<?= site_url('bunga-rampai/faq') ?>" class="arena-nav-pill">
                            <span class="left"><span class="dot" style="background:#16a34a"></span>Tanya Jawab (FAQ)</span>
                            <span class="count"><?= (int) ($stats['total_faqs'] ?? 0) ?></span>
                        </a>
                        <a href="<?= site_url('bunga-rampai/studi-kasus') ?>" class="arena-nav-pill">
                            <span class="left"><span class="dot" style="background:#dc2626"></span>Studi Kasus</span>
                            <span class="count"><?= (int) ($stats['total_cases'] ?? 0) ?></span>
                        </a>
                        <a href="<?= site_url('bunga-rampai/template-dokumen') ?>" class="arena-nav-pill">
                            <span class="left"><span class="dot" style="background:#d97706"></span>Template Dokumen</span>
                            <span class="count"><?= (int) ($stats['total_templates'] ?? 0) ?></span>
                        </a>
                        <a href="<?= site_url('bunga-rampai/multimedia') ?>" class="arena-nav-pill">
                            <span class="left"><span class="dot" style="background:#0891b2"></span>Multimedia PBJ</span>
                            <span class="count"><?= (int) (($stats['total_media'] ?? 0) + ($stats['total_videos'] ?? 0) + ($stats['total_gallery'] ?? 0)) ?></span>
                        </a>
                    </nav>
                </div>

                <!-- Sidebar Widget 3: Jelajahi Ekosistem Pengetahuan PBJ (Pilar Repositori) -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-th-large" style="background: #e0f2fe; color: #0284c7;"></i>
                        <span>Jelajahi Ekosistem Pengetahuan PBJ</span>
                    </h4>
                    <p class="text-muted small mb-3" style="font-size: 13px; line-height: 1.55;">
                        Pilar referensi pengadaan terlengkap dari analisis hukum hingga instrumen dokumen siap pakai:
                    </p>
                    <div class="d-flex flex-column gap-2">
                        <a href="<?= site_url('bunga-rampai/artikel') ?>" class="text-decoration-none p-2 radius-8 border-start border-3 border-primary bg-light d-flex align-items-center justify-content-between hover-bg-light">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary text-white radius-12" style="font-size: 11px;"><i class="fas fa-newspaper"></i></span>
                                <strong style="font-size: 13px; color: #0f172a;">Artikel PBJ</strong>
                            </div>
                            <span class="badge bg-white text-dark border small" style="font-size: 11px;"><?= (int) ($stats['total_articles'] ?? 0) ?></span>
                        </a>
                        <a href="<?= site_url('bunga-rampai/studi-kasus') ?>" class="text-decoration-none p-2 radius-8 border-start border-3 border-danger bg-light d-flex align-items-center justify-content-between hover-bg-light">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-danger text-white radius-12" style="font-size: 11px;"><i class="fas fa-briefcase"></i></span>
                                <strong style="font-size: 13px; color: #0f172a;">Studi Kasus Lapangan</strong>
                            </div>
                            <span class="badge bg-white text-dark border small" style="font-size: 11px;"><?= (int) ($stats['total_cases'] ?? 0) ?></span>
                        </a>
                        <a href="<?= site_url('bunga-rampai/template-dokumen') ?>" class="text-decoration-none p-2 radius-8 border-start border-3 border-warning bg-light d-flex align-items-center justify-content-between hover-bg-light">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-warning text-dark radius-12" style="font-size: 11px;"><i class="fas fa-file-invoice"></i></span>
                                <strong style="font-size: 13px; color: #0f172a;">Template Dokumen PBJ</strong>
                            </div>
                            <span class="badge bg-white text-dark border small" style="font-size: 11px;"><?= (int) ($stats['total_templates'] ?? 0) ?></span>
                        </a>
                        <a href="<?= site_url('bunga-rampai/faq') ?>" class="text-decoration-none p-2 radius-8 border-start border-3 border-success bg-light d-flex align-items-center justify-content-between hover-bg-light">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success text-white radius-12" style="font-size: 11px;"><i class="fas fa-question-circle"></i></span>
                                <strong style="font-size: 13px; color: #0f172a;">Tanya Jawab (FAQ)</strong>
                            </div>
                            <span class="badge bg-white text-dark border small" style="font-size: 11px;"><?= (int) ($stats['total_faqs'] ?? 0) ?></span>
                        </a>
                        <a href="<?= site_url('regulasi') ?>" class="text-decoration-none p-2 radius-8 border-start border-3 bg-light d-flex align-items-center justify-content-between hover-bg-light" style="border-left-color: #9333ea !important;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-purple text-white radius-12" style="font-size: 11px;"><i class="fas fa-balance-scale"></i></span>
                                <strong style="font-size: 13px; color: #0f172a;">Katalog Regulasi PBJ</strong>
                            </div>
                            <span class="badge bg-white text-success border small fw-bold" style="font-size: 11px;">Resmi</span>
                        </a>
                    </div>
                </div>

                <!-- Sidebar Widget 4: Artikel Pengetahuan Terbaru (Top 3) -->
                <?php if (! empty($articles)): ?>
                    <div class="berita-widget">
                        <h4 class="berita-widget__title">
                            <i class="fas fa-newspaper" style="background: #e0f2fe; color: #0284c7;"></i>
                            <span>Artikel Pengetahuan Terbaru</span>
                        </h4>
                        <?php foreach (array_slice($articles, 0, 3) as $aRank => $aItem): ?>
                            <div class="berita-trend">
                                <div class="berita-trend__rank is-top">
                                    <?= $aRank + 1 ?>
                                </div>
                                <div class="berita-trend__body">
                                    <h5 class="berita-trend__title">
                                        <a href="<?= route_to('public.knowledge.article.detail', $aItem['slug']) ?>">
                                            <?= esc($aItem['title']) ?>
                                        </a>
                                    </h5>
                                    <div class="berita-trend__meta">
                                        <span><i class="fas fa-user-circle text-primary me-1"></i> <?= esc($aItem['author_name'] ?? 'Biro PBJ') ?></span>
                                        <span>&middot;</span>
                                        <span><i class="far fa-clock me-1"></i> <?= esc($aItem['reading_time'] ?? 5) ?> mnt</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="<?= route_to('public.knowledge.articles') ?>" class="btn btn-sm btn-outline-primary w-100 radius-8 fw-semibold mt-3" style="font-size: 12.5px;">
                            Lihat Semua Artikel (<?= (int) ($stats['total_articles'] ?? 0) ?>) &rarr;
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Sidebar Widget 5: Studi Kasus Lapangan Pilihan (Top 3) -->
                <?php if (! empty($caseStudies)): ?>
                    <div class="berita-widget">
                        <h4 class="berita-widget__title">
                            <i class="fas fa-briefcase" style="background: #fee2e2; color: #dc2626;"></i>
                            <span>Studi Kasus Lapangan</span>
                        </h4>
                        <?php foreach (array_slice($caseStudies, 0, 3) as $cRank => $csItem): ?>
                            <div class="berita-trend">
                                <div class="berita-trend__rank" style="background: #fee2e2; color: #b91c1c;">
                                    <?= $cRank + 1 ?>
                                </div>
                                <div class="berita-trend__body">
                                    <h5 class="berita-trend__title">
                                        <a href="<?= route_to('public.knowledge.case-study.detail', $csItem['slug']) ?>">
                                            <?= esc($csItem['title']) ?>
                                        </a>
                                    </h5>
                                    <div class="berita-trend__meta">
                                        <span class="badge bg-danger-subtle text-danger" style="font-size: 10.5px;">Kasus</span>
                                        <span>&middot;</span>
                                        <a href="<?= route_to('public.knowledge.case-study.detail', $csItem['slug']) ?>" class="fw-bold text-danger text-decoration-none">Solusi &rarr;</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="<?= route_to('public.knowledge.case-studies') ?>" class="btn btn-sm btn-outline-danger w-100 radius-8 fw-semibold mt-3" style="font-size: 12.5px;">
                            Lihat Semua Kasus (<?= (int) ($stats['total_cases'] ?? 0) ?>) &rarr;
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Sidebar Widget 6: Template Dokumen Siap Unduh (Top 3) -->
                <?php if (! empty($templates)): ?>
                    <div class="berita-widget">
                        <h4 class="berita-widget__title">
                            <i class="fas fa-file-invoice" style="background: #fef3c7; color: #d97706;"></i>
                            <span>Template Dokumen PBJ</span>
                        </h4>
                        <?php foreach (array_slice($templates, 0, 3) as $tRank => $tItem):
                            $tExt = strtoupper($tItem['file_extension'] ?? 'DOCX');
                        ?>
                            <div class="berita-trend">
                                <div class="berita-trend__rank" style="background: #fef3c7; color: #b45309;">
                                    <?= $tRank + 1 ?>
                                </div>
                                <div class="berita-trend__body">
                                    <h5 class="berita-trend__title">
                                        <a href="<?= route_to('public.knowledge.templates.detail', $tItem['slug']) ?>">
                                            <?= esc($tItem['title']) ?>
                                        </a>
                                    </h5>
                                    <div class="berita-trend__meta">
                                        <span class="badge bg-light text-dark border font-monospace" style="font-size: 10.5px;"><?= esc($tExt) ?></span>
                                        <span>&middot;</span>
                                        <a href="<?= route_to('public.knowledge.templates.download', $tItem['slug']) ?>" class="fw-bold text-primary text-decoration-none">Unduh &rarr;</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="<?= route_to('public.knowledge.templates') ?>" class="btn btn-sm btn-outline-warning text-dark w-100 radius-8 fw-semibold mt-3" style="font-size: 12.5px;">
                            Lihat Semua Template (<?= (int) ($stats['total_templates'] ?? 0) ?>) &rarr;
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Sidebar Widget 7: Membangun Budaya Pengadaan Berintegritas & Akuntabel (Pillars of Integrity) -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-shield-alt" style="background: #dcfce7; color: #16a34a;"></i>
                        <span>Nilai Integritas PBJ</span>
                    </h4>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge radius-6 px-2 py-1" style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; font-size: 11px; font-weight: 700;">Pilar Tata Kelola</span>
                        <span class="fw-bold text-dark" style="font-size: 13.5px;">Membangun Budaya Pengadaan Berintegritas &amp; Akuntabel</span>
                    </div>
                    <div class="d-flex flex-column gap-2 pt-1">
                        <div class="d-flex align-items-start gap-2 p-2 radius-8 bg-light border">
                            <i class="fas fa-check-circle text-primary mt-1" style="font-size: 13px;"></i>
                            <div>
                                <strong class="d-block text-dark" style="font-size: 12.5px;">Standar LKPP RI &amp; Perpres PBJ</strong>
                                <small style="color: #334155; font-size: 12px; line-height: 1.45;">Diverifikasi selaras peraturan perundang-undangan pengadaan nasional terkini.</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-2 p-2 radius-8 bg-light border">
                            <i class="fas fa-check-circle text-success mt-1" style="font-size: 13px;"></i>
                            <div>
                                <strong class="d-block text-dark" style="font-size: 12.5px;">Template Bebas Akses</strong>
                                <small style="color: #334155; font-size: 12px; line-height: 1.45;">Standar format KAK, HPS, SPK, dan BAST siap pakai untuk seluruh perangkat daerah.</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-2 p-2 radius-8 bg-light border">
                            <i class="fas fa-check-circle text-info mt-1" style="font-size: 13px;"></i>
                            <div>
                                <strong class="d-block text-dark" style="font-size: 12.5px;">Praktik Nyata Antar-OPD</strong>
                                <small style="color: #334155; font-size: 12px; line-height: 1.45;">Kolaborasi pengalaman mitigasi risiko antara PPK, Pokja Pemilihan, dan Penyedia.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Widget 8: Bantuan & Konsultasi Biro PBJ -->
                <div class="berita-widget p-4 text-white" style="background: linear-gradient(135deg, #052244 0%, #0a3d74 100%) !important; border-radius: 16px;">
                    <span class="badge bg-warning text-dark text-xs fw-bold px-2 py-1 mb-2">Biro PBJ Setda Lampung</span>
                    <h5 class="fw-bold text-white mb-2" style="font-size: 16px;">Bantuan &amp; Konsultasi</h5>
                    <p class="text-white-80 mb-3" style="font-size: 13px; line-height: 1.6; color: rgba(255,255,255,0.85);">
                        Memiliki kendala teknis atau pertanyaan regulasi pengadaan? Tim pembina PBJ Provinsi Lampung siap membantu Anda.
                    </p>
                    <div class="text-warning fw-semibold d-flex align-items-center gap-2 mb-3" style="font-size: 13px;">
                        <i class="fas fa-phone-alt"></i>
                        <span>Helpdesk PBJ: +62 851-1777-9783</span>
                    </div>
                    <a href="<?= site_url('arena-belajar') ?>" class="btn btn-sm btn-outline-light w-100 radius-8 fw-semibold mb-2" style="font-size: 12.5px;">
                        <i class="fas fa-graduation-cap me-1"></i> Akses Arena Belajar PBJ
                    </a>
                    <a href="<?= site_url('regulasi') ?>" class="btn btn-sm btn-warning text-dark w-100 radius-8 fw-bold" style="font-size: 12.5px;">
                        <i class="fas fa-balance-scale me-1"></i> Katalog Regulasi Terlengkap
                    </a>
                </div>

            </div>
        </div>

    </div>
</section>

<!-- =========================================================================
     CLOSING CALL TO ACTION BANNER (COMPACT)
     ========================================================================= -->
<section class="arena-cta-section py-4" style="background: linear-gradient(135deg, #031527 0%, #082f5c 50%, #0c5aa6 100%); color: #ffffff;">
    <div class="container text-center py-2">
        <span class="badge bg-warning text-dark px-3 py-1 radius-20 text-uppercase fw-bold mb-2" style="letter-spacing: 0.5px; font-size: 12.5px;">
            <i class="fas fa-book-reader me-1"></i> Kolaborasi Berbagi Pengetahuan
        </span>
        <h2 class="text-white mb-2" style="font-size: 26px; font-weight: 800; line-height: 1.3;">
            Punya Praktik Baik atau Template Dokumen untuk Dibagikan?
        </h2>
        <p class="mx-auto mb-3 text-white-80" style="max-width: 620px; font-size: 14.5px; line-height: 1.65; color: rgba(255,255,255,0.85);">
            Biro Pengadaan Barang &amp; Jasa Provinsi Lampung memfasilitasi publikasi studi kasus, artikel teknis, dan formulir pengadaan dari seluruh OPD dan pokja pemilihan daerah.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href="<?= site_url('bunga-rampai/artikel') ?>" class="btn btn-warning text-dark fw-bold px-3 py-2 radius-8 shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 13.5px;">
                <i class="fas fa-search"></i> Jelajahi Seluruh Repositori
            </a>
            <a href="<?= site_url('arena-belajar') ?>" class="btn btn-outline-light radius-8 px-3 py-2 d-inline-flex align-items-center gap-2 fw-semibold" style="font-size: 13.5px;">
                <i class="fas fa-graduation-cap"></i> Ikuti Arena Belajar
            </a>
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
        <div class="d-flex align-items-center justify-content-center p-0 flex-grow-1 position-relative" style="background: #000000; min-height: 380px; max-height: 80vh;">
            <!-- Modal Slide Counter Badge Top Left -->
            <div class="position-absolute top-0 start-0 m-3 z-3 d-none" id="modalSlideCounter">
                <span class="badge radius-20 px-2 py-1" style="background: rgba(15, 23, 42, 0.72); backdrop-filter: blur(6px); color: #fff; font-size: 11px; font-weight: 600;">
                    <i class="fas fa-clone me-1 text-white-50"></i> <span id="modalCurrentSlide">1</span>/<span id="modalTotalSlides">1</span>
                </span>
            </div>

            <!-- Slide Navigation Arrows -->
            <button type="button" class="modal-slide-arrow modal-slide-prev d-none" id="modalSlidePrev" aria-label="Slide Sebelumnya" onclick="navigateModalSlide(-1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button type="button" class="modal-slide-arrow modal-slide-next d-none" id="modalSlideNext" aria-label="Slide Selanjutnya" onclick="navigateModalSlide(1)">
                <i class="fas fa-chevron-right"></i>
            </button>

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
    let currentModalData = null;
    let currentModalSlideIndex = 0;

    function openMediaModal(data) {
        openMediaModalSlide(data, 0);
    }

    function openMediaModalSlide(data, initialSlideIndex) {
        const modal = document.getElementById('mediaPreviewModal');
        if (!modal) return;

        currentModalData = data;
        currentModalShareTitle = data.title || '';
        currentModalSlideIndex = initialSlideIndex || 0;

        // Set Text Content
        document.getElementById('modalTitle').textContent = data.title || '(Tanpa Judul)';
        document.getElementById('modalCaption').textContent = data.caption || 'Tidak ada deskripsi.';
        document.getElementById('modalDate').textContent = data.date || 'Terbaru';
        document.getElementById('modalLikes').innerHTML = '<i class="fas fa-heart text-danger me-1"></i> ' + (data.likes_count || 24) + ' Suka';
        document.getElementById('modalViews').innerHTML = '<i class="fas fa-eye me-1"></i> ' + (data.views_count || 150) + ' Dilihat';

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

        // Hashtags
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

        // External Link Button
        const extBtn = document.getElementById('modalExternalBtn');
        if (data.external_url) {
            extBtn.href = data.external_url;
            extBtn.classList.remove('d-none');
        } else {
            extBtn.classList.add('d-none');
        }

        // Render current slide
        renderModalSlide(currentModalSlideIndex);

        // Open
        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function renderModalSlide(slideIndex) {
        if (!currentModalData) return;
        const slides = Array.isArray(currentModalData.slides) && currentModalData.slides.length > 0 ?
            currentModalData.slides :
            [{
                url: currentModalData.preview_url,
                is_video: currentModalData.is_video,
                embed_url: currentModalData.embed_url,
                title: currentModalData.title
            }];

        const totalSlides = slides.length;
        currentModalSlideIndex = Math.max(0, Math.min(slideIndex, totalSlides - 1));
        const slide = slides[currentModalSlideIndex];

        const imgBox = document.getElementById('modalImageContainer');
        const vidBox = document.getElementById('modalVideoContainer');
        const imgEl = document.getElementById('modalPreviewImage');
        const vidIframe = document.getElementById('modalPreviewIframe');

        if (slide.is_video && slide.embed_url) {
            imgBox.classList.add('d-none');
            vidBox.classList.remove('d-none');
            vidIframe.src = slide.embed_url;
        } else {
            vidBox.classList.add('d-none');
            imgBox.classList.remove('d-none');
            vidIframe.src = '';
            imgEl.src = slide.url || currentModalData.preview_url || '';
        }

        // Navigation arrows & counter badge in modal
        const prevBtn = document.getElementById('modalSlidePrev');
        const nextBtn = document.getElementById('modalSlideNext');
        const counterWrap = document.getElementById('modalSlideCounter');
        const currentSpan = document.getElementById('modalCurrentSlide');
        const totalSpan = document.getElementById('modalTotalSlides');

        if (totalSlides > 1) {
            if (counterWrap) counterWrap.classList.remove('d-none');
            if (currentSpan) currentSpan.textContent = currentModalSlideIndex + 1;
            if (totalSpan) totalSpan.textContent = totalSlides;

            if (prevBtn) {
                if (currentModalSlideIndex > 0) prevBtn.classList.remove('d-none');
                else prevBtn.classList.add('d-none');
            }
            if (nextBtn) {
                if (currentModalSlideIndex < totalSlides - 1) nextBtn.classList.remove('d-none');
                else nextBtn.classList.add('d-none');
            }
        } else {
            if (counterWrap) counterWrap.classList.add('d-none');
            if (prevBtn) prevBtn.classList.add('d-none');
            if (nextBtn) nextBtn.classList.add('d-none');
        }
    }

    function navigateModalSlide(direction) {
        if (!currentModalData) return;
        renderModalSlide(currentModalSlideIndex + direction);
    }

    function closeMediaModal() {
        const modal = document.getElementById('mediaPreviewModal');
        if (!modal) return;
        modal.classList.remove('is-open');
        document.body.style.overflow = '';
        const vidIframe = document.getElementById('modalPreviewIframe');
        if (vidIframe) vidIframe.src = '';
        currentModalData = null;
    }

    function handleBackdropClick(e) {
        if (e.target && e.target.id === 'mediaPreviewModal') {
            closeMediaModal();
        }
    }

    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('mediaPreviewModal');
        if (modal && modal.classList.contains('is-open')) {
            if (e.key === 'Escape') {
                closeMediaModal();
            } else if (e.key === 'ArrowLeft') {
                navigateModalSlide(-1);
            } else if (e.key === 'ArrowRight') {
                navigateModalSlide(1);
            }
        }
    });

    // Instagram Slider in Home / Landing View Logic
    const instaSlideStatesHome = {};

    function navigateInstaSlideHome(event, itemId, direction, totalSlides) {
        if (event) event.stopPropagation();
        const current = instaSlideStatesHome[itemId] || 0;
        const target = Math.max(0, Math.min(current + direction, totalSlides - 1));
        goToInstaSlideHome(event, itemId, target, totalSlides);
    }

    function goToInstaSlideHome(event, itemId, targetIndex, totalSlides) {
        if (event) event.stopPropagation();
        instaSlideStatesHome[itemId] = targetIndex;

        const track = document.getElementById('instaTrackHome_' + itemId);
        if (track) {
            track.style.transform = 'translateX(-' + (targetIndex * 100) + '%)';
        }

        const counter = document.getElementById('instaCounterHome_' + itemId);
        if (counter) {
            const span = counter.querySelector('.current-slide');
            if (span) span.textContent = targetIndex + 1;
        }

        const prevBtn = document.getElementById('instaPrevHome_' + itemId);
        const nextBtn = document.getElementById('instaNextHome_' + itemId);

        if (prevBtn) {
            if (targetIndex > 0) prevBtn.classList.remove('d-none');
            else prevBtn.classList.add('d-none');
        }

        if (nextBtn) {
            if (targetIndex < totalSlides - 1) nextBtn.classList.remove('d-none');
            else nextBtn.classList.add('d-none');
        }

        const dotsWrap = document.getElementById('instaDotsHome_' + itemId);
        if (dotsWrap) {
            const dots = dotsWrap.querySelectorAll('.insta-dot');
            dots.forEach((dot, idx) => {
                if (idx === targetIndex) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }
    }

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

    function applyKnowledgeQuickSearch(keyword) {
        const input = document.getElementById('knowledgeHeroSearch');
        if (input) {
            input.value = keyword;
            input.closest('form').submit();
        }
    }
</script>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var carouselEl = document.getElementById('knowledgeHeadlineCarousel');
        if (!carouselEl) return;

        // Initialize or get Bootstrap carousel instance safely across all Bootstrap 5 versions (including 5.0.0-beta3)
        var myCarousel = null;
        if (window.bootstrap && bootstrap.Carousel) {
            if (typeof bootstrap.Carousel.getOrCreateInstance === 'function') {
                myCarousel = bootstrap.Carousel.getOrCreateInstance(carouselEl, {
                    interval: 6000,
                    ride: 'carousel',
                    wrap: true,
                    pause: 'hover'
                });
            } else if (typeof bootstrap.Carousel.getInstance === 'function') {
                myCarousel = bootstrap.Carousel.getInstance(carouselEl);
                if (!myCarousel) {
                    try {
                        myCarousel = new bootstrap.Carousel(carouselEl, {
                            interval: 6000,
                            ride: 'carousel',
                            wrap: true,
                            pause: 'hover'
                        });
                    } catch (err) {}
                }
            } else {
                try {
                    myCarousel = new bootstrap.Carousel(carouselEl, {
                        interval: 6000,
                        ride: 'carousel',
                        wrap: true,
                        pause: 'hover'
                    });
                } catch (err) {}
            }
        }

        var indicatorButtons = carouselEl.querySelectorAll('.evt-headline__indicators button, .carousel-indicators button');

        function syncIndicatorDots(targetIdx) {
            if (!indicatorButtons || indicatorButtons.length === 0) return;
            indicatorButtons.forEach(function(btn, idx) {
                if (idx === targetIdx) {
                    btn.classList.add('active');
                    btn.setAttribute('aria-current', 'true');
                } else {
                    btn.classList.remove('active');
                    btn.removeAttribute('aria-current');
                }
            });
        }

        // Listen for slide start and slide complete events to guarantee 100% synchronization
        carouselEl.addEventListener('slide.bs.carousel', function(e) {
            if (typeof e.to !== 'undefined') {
                syncIndicatorDots(e.to);
            }
        });

        carouselEl.addEventListener('slid.bs.carousel', function(e) {
            if (typeof e.to !== 'undefined') {
                syncIndicatorDots(e.to);
            }
        });

        // Click handler for dots to ensure explicit navigation
        indicatorButtons.forEach(function(btn, idx) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if (myCarousel && typeof myCarousel.to === 'function') {
                    myCarousel.to(idx);
                } else if (window.jQuery && typeof jQuery(carouselEl).carousel === 'function') {
                    jQuery(carouselEl).carousel(idx);
                }
                syncIndicatorDots(idx);
            });
        });
    });
</script>
<?= $this->endSection() ?>