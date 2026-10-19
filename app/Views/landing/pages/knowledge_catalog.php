<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-news.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-news.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-news.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-events.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-events.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-events.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-knowledge.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-knowledge.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-knowledge.css') : '1.0' ?>" />
<style>
    /* Responsive tweaks for Bunga Rampai Catalog */
    .knowledge-catalog-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        border: 1px solid #edf2f7 !important;
    }
    .knowledge-catalog-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(8, 47, 92, 0.08) !important;
    }

    /* Instagram Carousel Slider Styling */
    .insta-carousel {
        position: relative;
        overflow: hidden;
        user-select: none;
        background: #0b1329;
    }
    .insta-track {
        display: flex;
        width: 100%;
        transition: transform 0.32s cubic-bezier(0.25, 1, 0.5, 1);
    }
    .insta-slide {
        flex: 0 0 100%;
        width: 100%;
        position: relative;
        cursor: pointer;
    }
    .insta-arrow-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.92);
        color: #0f172a;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.28);
        z-index: 10;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 0;
    }
    .insta-arrow-btn:hover {
        background: #ffffff;
        color: #0284c7;
        transform: translateY(-50%) scale(1.08);
    }
    .insta-arrow-prev { left: 10px; }
    .insta-arrow-next { right: 10px; }
    .insta-dots-indicator {
        position: absolute;
        bottom: 12px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        align-items: center;
        gap: 5px;
        z-index: 10;
        pointer-events: auto;
    }
    .insta-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.5);
        transition: all 0.2s ease;
        cursor: pointer;
        display: inline-block;
    }
    .insta-dot.active {
        width: 8px;
        height: 8px;
        background: #38bdf8;
        box-shadow: 0 0 8px rgba(56, 189, 248, 0.9);
    }

    /* Modal Stage Slider Controls */
    .modal-slide-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: rgba(15, 23, 42, 0.72);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(6px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        z-index: 15;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .modal-slide-arrow:hover {
        background: rgba(2, 132, 199, 0.92);
        border-color: #38bdf8;
        transform: translateY(-50%) scale(1.08);
    }
    .modal-slide-prev { left: 14px; }
    .modal-slide-next { right: 14px; }

    @media (max-width: 767.98px) {
        .evt-hero-banner {
            padding: 16px 14px !important;
            border-radius: 12px;
        }
        .evt-hero-title {
            font-size: 20px !important;
        }
        .evt-search-box {
            margin-top: 10px;
            width: 100% !important;
        }
        .insta-slide {
            height: 300px !important;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$items          = $items ?? [];
$catalogType    = $catalogType ?? 'articles';
$totalItems     = $totalItems ?? count($items);
$pager          = $pager ?? service('pager');
$q              = $q ?? '';
$featuredFilter = $featuredFilter ?? '';
$stageFilter    = $extraFilters['stage'] ?? ($stage ?? '');
$severityFilter = $extraFilters['severity'] ?? ($severity ?? '');
$categoryFilter = $extraFilters['category'] ?? ($category ?? '');
$topArticles    = $topArticles ?? [];
$topCases       = $topCases ?? [];
$topTemplates   = $topTemplates ?? [];

// Contextual Hero Text per Sub-Menu
$heroHeadings = [
    'articles' => [
        'tag'   => 'Wawasan & Analisis Regulasi Pengadaan',
        'title' => 'Katalog Artikel PBJ',
        'desc'  => 'Kumpulan artikel analisis regulasi, tata kelola pengadaan, mitigasi risiko audit, dan kiat teknis dari praktisi kepengadaan.',
        'searchPlaceholder' => 'Cari judul artikel, topik regulasi, atau isi konten...',
        'searchUrl' => site_url('bunga-rampai/artikel'),
    ],
    'faqs' => [
        'tag'   => 'Pusat Tanya Jawab Pengadaan Pemerintah',
        'title' => 'Tanya Jawab (FAQ) PBJ',
        'desc'  => 'Jawaban ringkas dan berlandaskan regulasi LKPP atas problematika teknis yang sering dihadapi PPK, Pokja, dan penyedia.',
        'searchPlaceholder' => 'Cari pertanyaan atau kata kunci regulasi...',
        'searchUrl' => site_url('bunga-rampai/faq'),
    ],
    'cases' => [
        'tag'   => 'Pembelajaran Berharga dari Dinamika Riil',
        'title' => 'Studi Kasus Lapangan',
        'desc'  => 'Kajian studi kasus penanganan sanggahan tender, pengendalian kontrak kritis, sengketa konstruksi, dan mitigasi temuan audit.',
        'searchPlaceholder' => 'Cari kasus pengadaan atau topik sengketa...',
        'searchUrl' => site_url('bunga-rampai/studi-kasus'),
    ],
    'templates' => [
        'tag'   => 'Format Baku & Formulir Siap Pakai',
        'title' => 'Template Dokumen PBJ',
        'desc'  => 'Standar format KAK, kertas kerja penyusunan HPS, surat perjanjian/SPK, formulir evaluasi pokja, dan BAST siap unduh.',
        'searchPlaceholder' => 'Cari nama dokumen atau jenis formulir...',
        'searchUrl' => site_url('bunga-rampai/template-dokumen'),
    ],
    'media' => [
        'tag'   => 'Feed Visual & Infografik Interaktif',
        'title' => 'Media &amp; Infografik PBJ',
        'desc'  => 'Galeri visual edukatif, diagram alir proses pengadaan, dan infografik modern yang terfokus pada preview materi kepengadaan.',
        'searchPlaceholder' => 'Cari infografik atau materi visual...',
        'searchUrl' => site_url('bunga-rampai/media'),
    ],
    'videos' => [
        'tag'   => 'Feed Video Pembelajaran & Webinar',
        'title' => 'Video Pembelajaran PBJ',
        'desc'  => 'Kompilasi video tutorial aplikasi SPSE & e-Katalog, rekaman webinar bedah regulasi, dan materi bimbingan teknis interaktif.',
        'searchPlaceholder' => 'Cari video tutorial atau webinar...',
        'searchUrl' => site_url('bunga-rampai/video'),
    ],
    'gallery' => [
        'tag'   => 'Feed Dokumentasi Sosial Ekosistem PBJ',
        'title' => 'Galeri Dokumentasi Kegiatan',
        'desc'  => 'Dokumentasi visual kolaboratif kegiatan bimbingan teknis, workshop pengadaan, rapat koordinasi, dan monitoring se-Provinsi Lampung.',
        'searchPlaceholder' => 'Cari dokumentasi kegiatan atau agenda...',
        'searchUrl' => site_url('bunga-rampai/galeri'),
    ],
    'multimedia' => [
        'tag'   => 'Visual, Video Edukasi & Dokumentasi Sosial',
        'title' => 'Multimedia PBJ',
        'desc'  => 'Pusat visual edukasi terintegrasi: infografik alur pengadaan, video tutorial aplikasi SPSE, dan galeri dokumentasi kegiatan Biro PBJ Provinsi Lampung.',
        'searchPlaceholder' => 'Cari infografik, video tutorial, atau dokumentasi...',
        'searchUrl' => site_url('bunga-rampai/multimedia'),
    ],
];

$heroMeta = $heroHeadings[$catalogType] ?? [
    'tag'   => 'Pusat Pengetahuan PBJ Lampung',
    'title' => esc($pageTitle),
    'desc'  => esc($description),
    'searchPlaceholder' => 'Cari di repositori Bunga Rampai...',
    'searchUrl' => current_url(),
];

$mediaTypeFilter = $extraFilters['media_type'] ?? '';

$buildFilterUrl = function (array $params) use ($heroMeta, $q, $featuredFilter, $stageFilter, $severityFilter, $categoryFilter, $mediaTypeFilter) {
    $current = [];
    if ($q !== '') $current['q'] = $q;
    if ($featuredFilter === '1') $current['featured'] = '1';
    if ($stageFilter !== '') $current['stage'] = $stageFilter;
    if ($severityFilter !== '') $current['severity'] = $severityFilter;
    if ($categoryFilter !== '') $current['category'] = $categoryFilter;
    if ($mediaTypeFilter !== '') $current['type'] = $mediaTypeFilter;

    foreach ($params as $k => $v) {
        if ($v === null || $v === '') {
            unset($current[$k]);
        } else {
            $current[$k] = $v;
        }
    }
    $query = http_build_query($current);
    return esc($heroMeta['searchUrl']) . ($query ? '?' . $query : '');
};

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

$isVisualMediaCategory = in_array($catalogType, ['media', 'videos', 'gallery', 'multimedia'], true);

$catalogChips = [
    'articles'   => ['Perpres 12/2021', 'Penyusunan HPS', 'E-Katalog', 'Mitigasi Risiko', 'Evaluasi Teknis'],
    'faqs'       => ['Kualifikasi', 'Sanggahan', 'Uang Muka', 'Jaminan', 'Addendum'],
    'cases'      => ['Audit BPK', 'Keterlambatan', 'Wanprestasi', 'Sengketa Kontrak', 'Pemutusan'],
    'templates'  => ['KAK', 'HPS', 'SPK', 'BAST', 'Surat Perjanjian'],
    'media'      => ['Infografik', 'Alur Pengadaan', 'Cheat Sheet', 'Diagram Siklus'],
    'videos'     => ['Tutorial SPSE', 'Webinar', 'Bimtek', 'E-Purchasing'],
    'gallery'    => ['Bimtek', 'Workshop', 'Rakor', 'Monitoring'],
    'multimedia' => ['Infografik PBJ', 'Video Tutorial', 'Bimtek', 'Galeri Dokumentasi', 'SPSE'],
];
$chips = $catalogChips[$catalogType] ?? ['Perpres 12/2021', 'HPS', 'KAK'];
?>

<!-- =========================================================================
     MAIN BUNGA RAMPAI CATALOG SECTION — Clean Magazine Benchmark
     ========================================================================= -->
<section class="berita-main-section evt-main-section pt-2 pt-md-3 pt-lg-4 pb-4" style="background: #fafbfc;">
    <div class="container">

        <!-- Compact Breadcrumb + Header Title & Unified Search -->
        <div class="evt-hero-banner mb-3 mb-md-4">
            <div class="arena-breadcrumb evt-breadcrumb mb-2">
                <a href="<?= site_url('/') ?>"><iconify-icon icon="solar:home-2-bold" class="align-middle"></iconify-icon> Beranda</a>
                <span class="sep">/</span>
                <a href="<?= site_url('bunga-rampai') ?>">Bunga Rampai</a>
                <span class="sep">/</span>
                <span class="current"><?= $heroMeta['title'] ?></span>
            </div>

            <div class="row align-items-center justify-content-between g-3 mb-2">
                <div class="col-lg-7">
                    <span class="arena-section-head__tag mb-1 d-inline-block">
                        <i class="fas fa-book-reader me-1"></i> <?= esc($heroMeta['tag']) ?>
                    </span>
                    <h1 class="evt-hero-title mb-2">
                        <?= $heroMeta['title'] ?> <br>
                        <span>Pemerintah Provinsi Lampung</span>
                    </h1>
                    <p class="evt-hero-desc mb-0">
                        <?= $heroMeta['desc'] ?>
                    </p>
                </div>
                <div class="col-lg-5">
                    <!-- Search Form -->
                    <form action="<?= esc($heroMeta['searchUrl']) ?>" method="get" class="evt-search-box arena-search-bar ms-lg-auto">
                        <iconify-icon icon="solar:magnifer-linear" class="text-slate-500 text-xl"></iconify-icon>
                        <input type="text" name="q" id="catalogHeroSearch" value="<?= esc($q) ?>" placeholder="<?= esc($heroMeta['searchPlaceholder']) ?>" autocomplete="off">
                        <?php if ($featuredFilter === '1'): ?>
                            <input type="hidden" name="featured" value="1">
                        <?php endif; ?>
                        <button type="submit">
                            <span>Cari</span>
                            <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                        </button>
                    </form>
                    <?php if ($q !== ''): ?>
                        <div class="text-end mt-1">
                            <a href="<?= esc($heroMeta['searchUrl']) ?>" class="badge border text-decoration-none" style="background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; font-size: 11px; font-weight: 600;">
                                <i class="fas fa-times me-1 text-danger"></i> Reset Pencarian: "<?= esc($q) ?>"
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contextual Quick Keyword Suggestion Chips -->
            <div class="d-flex flex-wrap align-items-center gap-1 mt-2 pt-2 border-top border-white border-opacity-10" style="font-size: 12px;">
                <span class="text-white-50 small me-1"><i class="fas fa-bolt text-warning me-1"></i> Topik Populer:</span>
                <?php foreach ($chips as $chip): ?>
                    <button type="button" class="btn btn-sm text-white px-2 py-0 radius-20 border-0" style="background: rgba(255,255,255,0.18); font-size: 11.5px; backdrop-filter: blur(4px);" onclick="applyCatalogQuickSearch('<?= esc($chip) ?>')">
                        <?= esc($chip) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- MAIN 2-COLUMN LAYOUT -->
        <div class="row g-3 g-lg-4">
            <!-- Left Column: Catalog Content & Filter Pills (col-lg-8) -->
            <div class="col-lg-8">

                <!-- Filter & Controls Toolbar -->
                <div class="card border-0 shadow-2xs radius-12 p-3 mb-3 bg-white border">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="text-muted small">
                                Menampilkan <strong><?= count($items) ?></strong> dari <strong><?= (int) $totalItems ?></strong> <?= esc($pageTitle) ?>
                            </span>
                            <?php if ($q !== ''): ?>
                                <span class="badge border" style="background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; font-weight: 600;">Kata kunci: "<?= esc($q) ?>"</span>
                            <?php endif; ?>
                            <?php if ($featuredFilter === '1'): ?>
                                <span class="badge bg-warning text-dark font-semibold"><i class="fas fa-star me-1"></i> Pilihan Editor</span>
                            <?php endif; ?>
                            <?php if ($stageFilter !== ''): ?>
                                <span class="badge radius-6 px-2 py-1" style="background: #e0f2fe; color: #0369a1; border: 1px solid #7dd3fc; font-weight: 600;">Tahap: <?= esc($stageFilter) ?></span>
                            <?php endif; ?>
                            <?php if ($severityFilter !== ''): ?>
                                <span class="badge radius-6 px-2 py-1" style="background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-weight: 700;">Risiko: <?= esc(ucfirst($severityFilter)) ?></span>
                            <?php endif; ?>
                            <?php if ($categoryFilter !== ''): ?>
                                <span class="badge radius-6 px-2 py-1" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-weight: 600;">Kategori: <?= esc($categoryFilter) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Visual Controls for Media & Gallery & Multimedia -->
                        <?php if ($isVisualMediaCategory): ?>
                            <div class="d-flex flex-wrap align-items-center justify-content-between w-100 gap-2">
                                <?php if ($catalogType === 'multimedia'): ?>
                                    <div class="d-flex flex-wrap align-items-center gap-1">
                                        <a href="<?= $buildFilterUrl(['type' => '']) ?>" class="btn btn-sm <?= empty($mediaTypeFilter) ? 'btn-primary' : 'btn-outline-secondary' ?> radius-20 px-2 py-0 fw-semibold" style="font-size: 11.5px;">
                                            Semua
                                        </a>
                                        <a href="<?= $buildFilterUrl(['type' => 'image']) ?>" class="btn btn-sm <?= in_array($mediaTypeFilter, ['image', 'infografik', 'infografis'], true) ? 'btn-primary' : 'btn-outline-secondary' ?> radius-20 px-2 py-0 fw-semibold" style="font-size: 11.5px;">
                                            <i class="fas fa-image me-1"></i> Infografik
                                        </a>
                                        <a href="<?= $buildFilterUrl(['type' => 'video']) ?>" class="btn btn-sm <?= in_array($mediaTypeFilter, ['video', 'videos', 'youtube'], true) ? 'btn-danger text-white' : 'btn-outline-secondary' ?> radius-20 px-2 py-0 fw-semibold" style="font-size: 11.5px;">
                                            <i class="fab fa-youtube me-1"></i> Video PBJ
                                        </a>
                                        <a href="<?= $buildFilterUrl(['type' => 'gallery']) ?>" class="btn btn-sm <?= in_array($mediaTypeFilter, ['gallery', 'galeri'], true) ? 'btn-dark text-white' : 'btn-outline-secondary' ?> radius-20 px-2 py-0 fw-semibold" style="font-size: 11.5px;">
                                            <i class="fas fa-camera me-1"></i> Galeri Kegiatan
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <div class="d-flex align-items-center gap-1 ms-auto">
                                    <button type="button" class="btn btn-sm btn-primary radius-20 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1 active" id="btnModeGrid" onclick="toggleMediaLayout('grid')" style="font-size: 12px;">
                                        <i class="fas fa-th-large"></i> Grid Visual
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary radius-20 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1" id="btnModeFeed" onclick="toggleMediaLayout('feed')" style="font-size: 12px;">
                                        <i class="fas fa-stream"></i> Feed Instagram
                                    </button>
                                </div>
                            </div>
                        <!-- Filter Pills for Articles -->
                        <?php elseif ($catalogType === 'articles'): ?>
                            <div class="d-flex flex-wrap align-items-center gap-1">
                                <a href="<?= $buildFilterUrl(['stage' => '']) ?>" class="btn btn-sm <?= $stageFilter === '' ? 'btn-primary' : 'btn-outline-secondary' ?> radius-20 px-2 py-0 fw-semibold" style="font-size: 11.5px;">
                                    Semua
                                </a>
                                <?php foreach (['Perencanaan', 'Persiapan', 'Pemilihan', 'Pelaksanaan Kontrak', 'Serah Terima'] as $stg): ?>
                                    <a href="<?= $buildFilterUrl(['stage' => $stg]) ?>" class="btn btn-sm <?= $stageFilter === $stg ? 'btn-primary' : 'btn-outline-secondary' ?> radius-20 px-2 py-0 fw-semibold" style="font-size: 11.5px;">
                                        <?= esc($stg) ?>
                                    </a>
                                <?php endforeach; ?>
                                <a href="<?= $buildFilterUrl(['featured' => $featuredFilter === '1' ? '' : '1']) ?>" class="btn btn-sm <?= $featuredFilter === '1' ? 'btn-warning text-dark' : 'btn-outline-secondary' ?> radius-20 px-2 py-0 fw-semibold" style="font-size: 11.5px;">
                                    <i class="fas fa-star me-1"></i> Unggulan
                                </a>
                            </div>
                        <!-- Filter Pills for Case Studies -->
                        <?php elseif ($catalogType === 'cases'): ?>
                            <div class="d-flex flex-wrap align-items-center gap-1">
                                <a href="<?= $buildFilterUrl(['severity' => '']) ?>" class="btn btn-sm <?= $severityFilter === '' ? 'btn-primary' : 'btn-outline-secondary' ?> radius-20 px-2 py-0 fw-semibold" style="font-size: 11.5px;">
                                    Semua
                                </a>
                                <a href="<?= $buildFilterUrl(['severity' => 'critical']) ?>" class="btn btn-sm <?= $severityFilter === 'critical' ? 'btn-danger' : 'btn-outline-secondary' ?> radius-20 px-2 py-0 fw-semibold" style="font-size: 11.5px;">
                                    <i class="fas fa-fire me-1"></i> Kritis
                                </a>
                                <a href="<?= $buildFilterUrl(['severity' => 'high']) ?>" class="btn btn-sm <?= $severityFilter === 'high' ? 'btn-warning text-dark' : 'btn-outline-secondary' ?> radius-20 px-2 py-0 fw-semibold" style="font-size: 11.5px;">
                                    Tinggi
                                </a>
                                <a href="<?= $buildFilterUrl(['severity' => 'medium']) ?>" class="btn btn-sm <?= $severityFilter === 'medium' ? 'btn-info text-dark' : 'btn-outline-secondary' ?> radius-20 px-2 py-0 fw-semibold" style="font-size: 11.5px;">
                                    Sedang
                                </a>
                                <a href="<?= $buildFilterUrl(['severity' => 'low']) ?>" class="btn btn-sm <?= $severityFilter === 'low' ? 'btn-success' : 'btn-outline-secondary' ?> radius-20 px-2 py-0 fw-semibold" style="font-size: 11.5px;">
                                    Rendah
                                </a>
                                <a href="<?= $buildFilterUrl(['featured' => $featuredFilter === '1' ? '' : '1']) ?>" class="btn btn-sm <?= $featuredFilter === '1' ? 'btn-warning text-dark' : 'btn-outline-secondary' ?> radius-20 px-2 py-0 fw-semibold" style="font-size: 11.5px;">
                                    <i class="fas fa-star me-1"></i> Unggulan
                                </a>
                            </div>
                        <!-- Filter Pills for Templates -->
                        <?php elseif ($catalogType === 'templates'): ?>
                            <div class="d-flex flex-wrap align-items-center gap-1">
                                <a href="<?= $buildFilterUrl(['category' => '']) ?>" class="btn btn-sm <?= $categoryFilter === '' ? 'btn-primary' : 'btn-outline-secondary' ?> radius-20 px-2 py-0 fw-semibold" style="font-size: 11.5px;">
                                    Semua
                                </a>
                                <?php foreach (['KAK', 'HPS', 'Kontrak', 'BAST', 'Evaluasi'] as $cat): ?>
                                    <a href="<?= $buildFilterUrl(['category' => $cat]) ?>" class="btn btn-sm <?= $categoryFilter === $cat ? 'btn-primary' : 'btn-outline-secondary' ?> radius-20 px-2 py-0 fw-semibold" style="font-size: 11.5px;">
                                        <?= esc($cat) ?>
                                    </a>
                                <?php endforeach; ?>
                                <a href="<?= $buildFilterUrl(['featured' => $featuredFilter === '1' ? '' : '1']) ?>" class="btn btn-sm <?= $featuredFilter === '1' ? 'btn-warning text-dark' : 'btn-outline-secondary' ?> radius-20 px-2 py-0 fw-semibold" style="font-size: 11.5px;">
                                    <i class="fas fa-star me-1"></i> Unggulan
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Flash Message Alerts -->
                <?php if ($msg = session()->getFlashdata('success')): ?>
                    <div class="alert alert-success radius-8 mb-3"><?= esc($msg) ?></div>
                <?php endif; ?>
                <?php if ($msg = session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger radius-8 mb-3"><?= esc($msg) ?></div>
                <?php endif; ?>

                <!-- CONTENT LISTING OR EMPTY STATE -->
                <?php if (empty($items)): ?>
                    <div class="card border-0 shadow-2xs radius-16 p-5 text-center my-2 bg-white border">
                        <div class="d-inline-flex align-items-center justify-content-center mx-auto mb-3 radius-50" style="width: 64px; height: 64px; background: #f1f5f9; color: #94a3b8; font-size: 28px;">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <h4 class="fw-bold mb-2" style="color: #082f5c; font-size: 18px;">Tidak Ada Konten Ditemukan</h4>
                        <p class="text-muted mx-auto mb-4" style="max-width: 480px; font-size: 14px;">
                            <?php if ($q !== ''): ?>
                                Tidak ada konten yang sesuai dengan pencarian "<strong><?= esc($q) ?></strong>". Silakan coba kata kunci lain.
                            <?php else: ?>
                                Belum ada konten yang dipublikasikan pada kategori <?= esc($pageTitle) ?>.
                            <?php endif; ?>
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <?php if ($q !== '' || $featuredFilter !== '' || $stageFilter !== '' || $severityFilter !== '' || $categoryFilter !== ''): ?>
                                <a href="<?= esc($heroMeta['searchUrl']) ?>" class="btn btn-primary radius-8 px-4 py-2 fw-semibold">
                                    <i class="fas fa-undo me-1"></i> Reset Filter
                                </a>
                            <?php endif; ?>
                            <a href="<?= site_url('bunga-rampai') ?>" class="btn btn-outline-secondary radius-8 px-4 py-2 fw-semibold">
                                Kembali ke Beranda Bunga Rampai
                            </a>
                        </div>
                    </div>

                <?php else: ?>

                    <!-- ===========================================================
                         VISUAL-FIRST SOCIAL MEDIA PRESENTATION (MEDIA / VIDEO / GALERI)
                         =========================================================== -->
                    <?php if ($isVisualMediaCategory): ?>

                        <!-- MODE A: GRID VISUAL -->
                        <div id="mediaGridViewContainer" class="row g-3">
                            <?php foreach ($items as $item):
                                $previewImg  = $item['preview_url'] ?? base_url('assets/main/images/gallery/gallery-page-1-1.jpg');
                                $isVideo     = ! empty($item['is_video']);
                                $embedUrl    = $item['embed_url'] ?? '';
                                $extUrl      = $item['external_url'] ?? '';
                                $slidesCount = (int) ($item['slides_count'] ?? 1);
                                $modalData   = [
                                    'title'        => $item['title'] ?? '',
                                    'caption'      => $item['caption'] ?? '',
                                    'preview_url'  => $previewImg,
                                    'embed_url'    => $embedUrl,
                                    'external_url' => $extUrl,
                                    'is_video'     => $isVideo,
                                    'likes_count'  => (int) ($item['likes_count'] ?? 24),
                                    'views_count'  => (int) ($item['views_count'] ?? 150),
                                    'shares_count' => (int) ($item['shares_count'] ?? 5),
                                    'date'         => ! empty($item['published_at']) ? date('d M Y', strtotime($item['published_at'])) : 'Terbaru',
                                    'hashtags'     => $item['hashtags'] ?? [],
                                    'location'     => $item['location'] ?? '',
                                    'duration'     => $item['duration'] ?? '',
                                    'event_date'   => ! empty($item['event_date']) ? date('d M Y', strtotime($item['event_date'])) : '',
                                    'id'           => (int) $item['id'],
                                    'slides'       => $item['slides'] ?? [],
                                    'slides_count' => $slidesCount,
                                ];
                            ?>
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-2xs radius-12 overflow-hidden bg-white knowledge-catalog-card position-relative">
                                        <!-- Media Visual Preview -->
                                        <div class="media-visual-container position-relative" style="height: 220px; cursor: pointer;" onclick="openMediaModal(<?= esc(json_encode($modalData)) ?>)">
                                            <img src="<?= esc($previewImg) ?>" alt="<?= esc($item['title'] ?? 'Media PBJ') ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/gallery/gallery-page-1-1.jpg') ?>';">

                                            <!-- Top Badges -->
                                            <div class="position-absolute top-0 start-0 m-2 z-2 d-flex flex-wrap gap-1">
                                                <?php
                                                    $itemType = (string) ($item['type'] ?? '');
                                                    if ($isVideo || in_array($itemType, ['youtube', 'video_file'], true)):
                                                ?>
                                                    <span class="badge bg-danger text-white radius-6 px-2 py-1 small fw-semibold">
                                                        <i class="fab fa-youtube me-1"></i> Video PBJ
                                                    </span>
                                                <?php elseif ($itemType === 'gallery' || $catalogType === 'gallery'): ?>
                                                    <span class="badge bg-dark bg-opacity-75 text-white radius-6 px-2 py-1 small fw-semibold">
                                                        <i class="fas fa-camera me-1"></i> Dokumentasi
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-info text-dark radius-6 px-2 py-1 small fw-semibold">
                                                        <i class="fas fa-image me-1"></i> Infografik PBJ
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (! empty($item['location'])): ?>
                                                    <span class="badge bg-dark bg-opacity-75 text-white radius-6 px-2 py-1 small fw-semibold">
                                                        <i class="fas fa-map-marker-alt text-danger me-1"></i> <?= esc($item['location']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Top Right Multi-slide Counter Badge if > 1 -->
                                            <?php if ($slidesCount > 1): ?>
                                                <div class="position-absolute top-0 end-0 m-2 z-2">
                                                    <span class="badge radius-6 px-2 py-1 small fw-semibold" style="background: rgba(15, 23, 42, 0.78); color: #fff; backdrop-filter: blur(4px);">
                                                        <i class="fas fa-clone me-1 text-white-50"></i> <?= $slidesCount ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Center Play Button for Video -->
                                            <?php if ($isVideo): ?>
                                                <div class="position-absolute top-50 start-50 translate-middle z-2">
                                                    <div class="media-play-pulse">
                                                        <i class="fas fa-play ms-1" style="font-size: 20px;"></i>
                                                    </div>
                                                </div>
                                                <?php if (! empty($item['duration'])): ?>
                                                    <div class="position-absolute bottom-0 end-0 m-2 z-2">
                                                        <span class="badge bg-dark bg-opacity-75 text-white radius-6 px-2 py-0 small">
                                                            <i class="far fa-clock me-1 text-warning"></i> <?= esc($item['duration']) ?>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <!-- Hover Overlay -->
                                            <div class="media-hover-overlay">
                                                <h5 class="fw-bold mb-1 text-white" style="font-size: 15px; line-height: 1.35;">
                                                    <?= esc($item['title'] ?? '') ?>
                                                </h5>
                                                <p class="small text-white-80 mb-2" style="color: rgba(255,255,255,0.85); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 12.5px;">
                                                    <?= esc($item['caption'] ?? '') ?>
                                                </p>
                                                <div class="d-flex align-items-center justify-content-between pt-2 border-top border-white border-opacity-25">
                                                    <div class="d-flex align-items-center gap-2 small text-white" style="font-size: 11px;">
                                                        <span><i class="fas fa-heart text-danger me-1"></i> <?= (int) ($item['likes_count'] ?? 24) ?></span>
                                                        <span><i class="fas fa-eye me-1"></i> <?= (int) ($item['views_count'] ?? 150) ?></span>
                                                        <?php if ($slidesCount > 1): ?>
                                                            <span><i class="fas fa-clone me-1 text-white-50"></i> <?= $slidesCount ?> Slide</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <span class="badge bg-white text-dark radius-20 px-2 py-1 small fw-bold" style="font-size: 11px;">
                                                        <i class="fas fa-expand me-1"></i> Preview
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Card Footer / Quick Info -->
                                        <div class="p-3 bg-white d-flex align-items-center justify-content-between border-top">
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="<?= base_url('assets/main/images/resources/logo-1.png') ?>" alt="Biro PBJ" class="radius-50 border p-1" style="width: 26px; height: 26px; object-fit: contain;">
                                                <span class="small fw-semibold text-dark text-truncate" style="max-width: 180px; font-size: 12.5px;">
                                                    <?= esc($item['title'] ?? 'Media PBJ') ?>
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center gap-1">
                                                <?= view('landing/components/bookmark_button', [
                                                    'type'  => 'knowledge_media',
                                                    'id'    => (int) $item['id'],
                                                    'title' => $item['title'] ?? null,
                                                    'size'  => 'sm',
                                                    'iconOnly' => true,
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- MODE B: FEED SOSIAL (INSTAGRAM SLIDER SUPPORT) -->
                        <div id="mediaFeedViewContainer" class="row g-3 justify-content-center" style="display: none;">
                            <div class="col-12">
                                <?php foreach ($items as $item):
                                    $previewImg  = $item['preview_url'] ?? base_url('assets/main/images/gallery/gallery-page-1-1.jpg');
                                    $isVideo     = ! empty($item['is_video']);
                                    $embedUrl    = $item['embed_url'] ?? '';
                                    $extUrl      = $item['external_url'] ?? '';
                                    $slides      = $item['slides'] ?? [];
                                    if (empty($slides)) {
                                        $slides = [[
                                            'url'       => $previewImg,
                                            'is_video'  => $isVideo,
                                            'embed_url' => $embedUrl,
                                            'title'     => $item['title'] ?? '',
                                        ]];
                                    }
                                    $slidesCount = count($slides);
                                    $modalData   = [
                                        'title'        => $item['title'] ?? '',
                                        'caption'      => $item['caption'] ?? '',
                                        'preview_url'  => $previewImg,
                                        'embed_url'    => $embedUrl,
                                        'external_url' => $extUrl,
                                        'is_video'     => $isVideo,
                                        'likes_count'  => (int) ($item['likes_count'] ?? 24),
                                        'views_count'  => (int) ($item['views_count'] ?? 150),
                                        'shares_count' => (int) ($item['shares_count'] ?? 5),
                                        'date'         => ! empty($item['published_at']) ? date('d M Y', strtotime($item['published_at'])) : 'Terbaru',
                                        'hashtags'     => $item['hashtags'] ?? [],
                                        'location'     => $item['location'] ?? '',
                                        'duration'     => $item['duration'] ?? '',
                                        'event_date'   => ! empty($item['event_date']) ? date('d M Y', strtotime($item['event_date'])) : '',
                                        'id'           => (int) $item['id'],
                                        'slides'       => $slides,
                                        'slides_count' => $slidesCount,
                                    ];
                                    $itemType = (string) ($item['type'] ?? '');
                                    if ($isVideo || in_array($itemType, ['youtube', 'video_file'], true)) {
                                        $feedBadgeClass = 'bg-danger text-white';
                                        $feedBadgeIcon  = '<i class="fab fa-youtube me-1"></i> Video PBJ';
                                    } elseif ($itemType === 'gallery' || $catalogType === 'gallery') {
                                        $feedBadgeClass = 'bg-dark text-white';
                                        $feedBadgeIcon  = '<i class="fas fa-camera me-1"></i> Dokumentasi';
                                    } else {
                                        $feedBadgeClass = 'bg-primary text-white';
                                        $feedBadgeIcon  = '<i class="fas fa-image me-1"></i> Infografik PBJ';
                                    }
                                ?>
                                    <article class="card border-0 shadow-2xs radius-12 overflow-hidden mb-3 bg-white border">
                                        <!-- Post Author Header -->
                                        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="media-avatar-ring">
                                                    <img src="<?= base_url('assets/main/images/resources/logo-1.png') ?>" alt="Biro PBJ" class="radius-50" style="width: 34px; height: 34px; object-fit: contain; background: #f8fafc; padding: 2px;">
                                                </div>
                                                <div>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <strong class="text-dark" style="font-size: 13.5px;">Biro Pengadaan Barang &amp; Jasa</strong>
                                                        <i class="fas fa-check-circle text-primary" style="font-size: 12px;" title="Akun Resmi Terverifikasi"></i>
                                                    </div>
                                                    <small class="text-muted" style="font-size: 11px;">
                                                        Pemerintah Provinsi Lampung &middot; <?= ! empty($item['published_at']) ? esc(date('d M Y', strtotime($item['published_at']))) : 'Terbaru' ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="badge radius-6 px-2 py-1 small <?= $feedBadgeClass ?>" style="font-size: 11px;">
                                                    <?= $feedBadgeIcon ?>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Media Visual Preview or Instagram Slider -->
                                        <?php if ($slidesCount > 1): ?>
                                            <div class="insta-carousel position-relative" id="instaCarousel_<?= (int) $item['id'] ?>" data-item-id="<?= (int) $item['id'] ?>" data-total-slides="<?= $slidesCount ?>">
                                                <div class="insta-track" id="instaTrack_<?= (int) $item['id'] ?>" style="transform: translateX(0%);">
                                                    <?php foreach ($slides as $sIdx => $slide):
                                                        $isSlideVideo = ! empty($slide['is_video']);
                                                        $slideImg     = $slide['url'] ?? $previewImg;
                                                    ?>
                                                        <div class="insta-slide" style="height: 380px;" onclick="openMediaModalSlide(<?= esc(json_encode($modalData)) ?>, <?= $sIdx ?>)">
                                                            <img src="<?= esc($slideImg) ?>" alt="<?= esc($slide['title'] ?? ($item['title'] ?? 'Media PBJ')) ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/gallery/gallery-page-1-1.jpg') ?>';">

                                                            <?php if ($isSlideVideo): ?>
                                                                <div class="position-absolute top-50 start-50 translate-middle z-2">
                                                                    <div class="media-play-pulse">
                                                                        <i class="fas fa-play ms-1" style="font-size: 22px;"></i>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>

                                                            <div class="position-absolute bottom-0 end-0 m-2 z-2">
                                                                <span class="badge bg-dark bg-opacity-75 text-white radius-20 px-2 py-1 small" style="font-size: 11px;">
                                                                    <i class="fas fa-expand-arrows-alt me-1"></i> Perbesar Media
                                                                </span>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>

                                                <!-- Instagram Counter Badge Top Right (e.g. 1/4) -->
                                                <div class="position-absolute top-0 end-0 m-2 z-3">
                                                    <span class="badge radius-20 px-2 py-1 insta-counter-badge" id="instaCounter_<?= (int) $item['id'] ?>" style="background: rgba(15, 23, 42, 0.72); backdrop-filter: blur(6px); color: #fff; font-size: 11px; font-weight: 600;">
                                                        <i class="fas fa-clone me-1 text-white-50"></i> <span class="current-slide">1</span>/<?= $slidesCount ?>
                                                    </span>
                                                </div>

                                                <!-- Circular Prev Arrow (Initially Hidden on First Slide) -->
                                                <button type="button" class="insta-arrow-btn insta-arrow-prev d-none" id="instaPrev_<?= (int) $item['id'] ?>" aria-label="Slide Sebelumnya" onclick="navigateInstaSlide(event, <?= (int) $item['id'] ?>, -1, <?= $slidesCount ?>)">
                                                    <i class="fas fa-chevron-left"></i>
                                                </button>

                                                <!-- Circular Next Arrow -->
                                                <button type="button" class="insta-arrow-btn insta-arrow-next" id="instaNext_<?= (int) $item['id'] ?>" aria-label="Slide Selanjutnya" onclick="navigateInstaSlide(event, <?= (int) $item['id'] ?>, 1, <?= $slidesCount ?>)">
                                                    <i class="fas fa-chevron-right"></i>
                                                </button>

                                                <!-- Instagram Dots Indicator Bottom Center -->
                                                <div class="insta-dots-indicator" id="instaDots_<?= (int) $item['id'] ?>">
                                                    <?php for ($d = 0; $d < $slidesCount; $d++): ?>
                                                        <span class="insta-dot <?= $d === 0 ? 'active' : '' ?>" onclick="goToInstaSlide(event, <?= (int) $item['id'] ?>, <?= $d ?>, <?= $slidesCount ?>)"></span>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="media-visual-container position-relative" style="height: 380px; cursor: pointer;" onclick="openMediaModal(<?= esc(json_encode($modalData)) ?>)">
                                                <img src="<?= esc($previewImg) ?>" alt="<?= esc($item['title'] ?? 'Media PBJ') ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/gallery/gallery-page-1-1.jpg') ?>';">

                                                <?php if ($isVideo): ?>
                                                    <div class="position-absolute top-50 start-50 translate-middle z-2">
                                                        <div class="media-play-pulse">
                                                            <i class="fas fa-play ms-1" style="font-size: 22px;"></i>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="position-absolute bottom-0 end-0 m-2 z-2">
                                                    <span class="badge bg-dark bg-opacity-75 text-white radius-20 px-2 py-1 small" style="font-size: 11px;">
                                                        <i class="fas fa-expand-arrows-alt me-1"></i> Perbesar Media
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Social Engagement Bar -->
                                        <div class="px-3 pt-2 pb-2 d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-3">
                                                <button type="button" class="btn btn-link text-decoration-none p-0 text-muted media-like-btn" onclick="toggleMediaLike(this)">
                                                    <i class="far fa-heart text-danger" style="font-size: 18px;"></i>
                                                    <span class="fw-semibold ms-1 small text-dark"><?= (int) ($item['likes_count'] ?? 24) ?></span>
                                                </button>
                                                <button type="button" class="btn btn-link text-decoration-none p-0 text-muted" onclick="openMediaModal(<?= esc(json_encode($modalData)) ?>)" title="Buka Detail">
                                                    <i class="far fa-comment-alt" style="font-size: 17px;"></i>
                                                    <span class="fw-semibold ms-1 small text-dark"><?= (int) ($item['shares_count'] ?? 5) ?></span>
                                                </button>
                                                <button type="button" class="btn btn-link text-decoration-none p-0 text-muted" onclick="copyMediaShareUrl('<?= esc(addslashes($item['title'] ?? '')) ?>')" title="Salin Tautan">
                                                    <i class="far fa-paper-plane" style="font-size: 17px;"></i>
                                                </button>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <?php if (! empty($extUrl)): ?>
                                                    <a href="<?= esc($extUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-danger radius-6 px-2 py-1" style="font-size: 11.5px;">
                                                        <i class="fab fa-youtube me-1"></i> Buka Tautan
                                                    </a>
                                                <?php endif; ?>
                                                <?= view('landing/components/bookmark_button', [
                                                    'type'  => 'knowledge_media',
                                                    'id'    => (int) $item['id'],
                                                    'title' => $item['title'] ?? null,
                                                    'size'  => 'sm',
                                                ]) ?>
                                            </div>
                                        </div>

                                        <!-- Post Caption -->
                                        <div class="px-3 pb-3">
                                            <h4 class="fw-bold mb-1" style="font-size: 15px; color: #082f5c; line-height: 1.4;">
                                                <?= esc($item['title'] ?? '') ?>
                                            </h4>
                                            <p class="text-muted small mb-2" style="font-size: 13px; line-height: 1.55;">
                                                <?= nl2br(esc($item['caption'] ?? '')) ?>
                                            </p>
                                            <div class="mb-2">
                                                <?php foreach (($item['hashtags'] ?? []) as $ht): ?>
                                                    <span class="media-hashtag-pill"><?= esc($ht) ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    <!-- ===========================================================
                         ARTIKEL PBJ CATALOG
                         =========================================================== -->
                    <?php elseif ($catalogType === 'articles'): ?>
                        <div class="row g-3">
                            <?php foreach ($items as $item):
                                $coverImg = ! empty($item['cover_file_id'])
                                    ? site_url('file/' . (int) $item['cover_file_id'])
                                    : base_url('assets/main/images/blog/blog-2-' . (((int) $item['id'] % 4) + 1) . '.jpg');
                                $detailUrl = route_to('public.knowledge.article.detail', $item['slug']);
                            ?>
                                <div class="col-md-6">
                                    <article class="card h-100 border-0 shadow-2xs radius-12 overflow-hidden d-flex flex-column knowledge-catalog-card bg-white">
                                        <div class="position-relative" style="height: 175px; overflow: hidden; background: #082f5c;">
                                            <img src="<?= esc($coverImg) ?>" alt="<?= esc($item['title']) ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/blog/blog-2-1.jpg') ?>';">
                                            <div class="position-absolute top-0 start-0 m-2">
                                                <span class="badge bg-primary text-white radius-6 px-2 py-1 small fw-semibold" style="font-size: 11px;">
                                                    <i class="fas fa-newspaper me-1"></i> Artikel PBJ
                                                </span>
                                            </div>
                                            <?php if ((int) ($item['featured'] ?? 0) === 1): ?>
                                                <div class="position-absolute top-0 end-0 m-2">
                                                    <span class="badge bg-dark bg-opacity-75 text-white radius-6 px-2 py-1 small" style="font-size: 11px;">
                                                        <i class="fas fa-star text-warning me-1"></i> Unggulan
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="p-3 d-flex flex-column flex-grow-1">
                                            <div class="d-flex align-items-center justify-content-between text-muted small mb-2" style="font-size: 11px;">
                                                <span><i class="fas fa-user-circle me-1"></i> <?= esc($item['author_name'] ?? 'Biro PBJ') ?></span>
                                                <span><i class="far fa-clock me-1"></i> <?= esc($item['reading_time'] ?? 5) ?> mnt</span>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center gap-1 mb-2">
                                                <?php if (! empty($item['procurement_stage'])): ?>
                                                    <span class="badge radius-6 px-2 py-0 border" style="background: #e0f2fe; color: #0369a1; border-color: #7dd3fc; font-size: 11px; font-weight: 600;">
                                                        <i class="fas fa-layer-group me-1"></i> <?= esc($item['procurement_stage']) ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (! empty($item['organization_unit'])): ?>
                                                    <span class="badge radius-6 px-2 py-0 border" style="background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; font-size: 11px; font-weight: 600;">
                                                        <i class="fas fa-building me-1 text-primary"></i> <?= esc($item['organization_unit']) ?>
                                                    </span>
                                                <?php endif; ?>
                                                <span class="badge radius-6 px-2 py-0 border" style="background: #f8fafc; color: #1e293b; border-color: #cbd5e1; font-size: 11px; font-weight: 600;">
                                                    <i class="far fa-calendar-alt me-1 text-primary"></i> <?= ! empty($item['published_at']) ? esc(date('d M Y', strtotime($item['published_at']))) : 'Terbaru' ?>
                                                </span>
                                            </div>
                                            <h4 class="fw-bold mb-2" style="font-size: 15.5px; line-height: 1.4; color: #082f5c;">
                                                <a href="<?= esc($detailUrl) ?>" class="text-decoration-none text-reset hover-primary">
                                                    <?= esc($item['title']) ?>
                                                </a>
                                            </h4>
                                            <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; font-size: 12.5px;">
                                                <?= esc($item['summary'] ?? '') ?>
                                            </p>
                                            <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                                                <a href="<?= esc($detailUrl) ?>" class="btn btn-sm btn-outline-primary radius-8 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 12px;">
                                                    <span>Baca Artikel</span>
                                                    <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
                                                </a>
                                                <?= view('landing/components/bookmark_button', [
                                                    'type'  => 'knowledge_article',
                                                    'id'    => (int) $item['id'],
                                                    'title' => $item['title'] ?? null,
                                                    'size'  => 'sm',
                                                ]) ?>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    <!-- ===========================================================
                         FAQ CATALOG
                         =========================================================== -->
                    <?php elseif ($catalogType === 'faqs'): ?>
                        <div class="accordion d-flex flex-column gap-2" id="faqCatalogAccordion">
                            <?php foreach ($items as $index => $item):
                                $collapseId = 'catalogFaq' . (int) $item['id'];
                                $headingId  = 'catalogHeading' . (int) $item['id'];
                            ?>
                                <div class="card border-0 shadow-2xs radius-12 overflow-hidden bg-white border">
                                    <div class="card-header bg-white border-0 p-3" id="<?= esc($headingId) ?>">
                                        <button class="btn btn-link w-100 text-start text-decoration-none p-0 d-flex align-items-center justify-content-between <?= $index > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?= esc($collapseId) ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="<?= esc($collapseId) ?>">
                                            <span class="fw-bold pe-2" style="color: #082f5c; font-size: 15px; line-height: 1.45;">
                                                <i class="fas fa-question-circle text-primary me-2"></i> <?= esc($item['title']) ?>
                                            </span>
                                            <i class="fas fa-chevron-down text-muted" style="font-size: 13px;"></i>
                                        </button>
                                    </div>
                                    <div id="<?= esc($collapseId) ?>" class="collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="<?= esc($headingId) ?>" data-bs-parent="#faqCatalogAccordion">
                                        <div class="card-body pt-0 px-3 pb-3 text-muted" style="font-size: 14px; line-height: 1.6; border-top: 1px dashed #edf2f7;">
                                            <div class="mb-3">
                                                <?= nl2br(esc($item['content'] ?? $item['summary'] ?? 'Belum ada penjelasan jawaban.')) ?>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 border-top">
                                                <div class="d-flex flex-wrap align-items-center gap-1">
                                                    <?php if (! empty($item['legal_basis'])): ?>
                                                        <span class="badge border px-2 py-1 small" style="background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; font-size: 11.5px; font-weight: 600;">
                                                            <i class="fas fa-balance-scale me-1 text-primary"></i> <?= esc($item['legal_basis']) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge border px-2 py-1 small" style="background: #e0f2fe; color: #0369a1; border-color: #7dd3fc; font-size: 11.5px; font-weight: 600;">
                                                            <i class="fas fa-check-circle me-1"></i> Regulasi PBJ
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if (! empty($item['procurement_stage'])): ?>
                                                        <span class="badge border px-2 py-1 small" style="background: #f8fafc; color: #1e293b; border-color: #cbd5e1; font-size: 11.5px; font-weight: 600;">
                                                            <?= esc($item['procurement_stage']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <?= view('landing/components/bookmark_button', [
                                                    'type'  => 'knowledge_faq',
                                                    'id'    => (int) $item['id'],
                                                    'title' => $item['title'] ?? null,
                                                    'size'  => 'sm',
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    <!-- ===========================================================
                         STUDI KASUS CATALOG
                         =========================================================== -->
                    <?php elseif ($catalogType === 'cases'): ?>
                        <div class="row g-3">
                            <?php foreach ($items as $item):
                                $detailUrl = route_to('public.knowledge.case-study.detail', $item['slug']);
                                $sev = strtolower((string) ($item['case_severity'] ?? ''));
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
                                <div class="col-md-6">
                                    <article class="card h-100 border-0 shadow-2xs radius-12 overflow-hidden d-flex flex-column knowledge-catalog-card bg-white p-3">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-2">
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="badge radius-6 px-2 py-1 small fw-semibold" style="background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; font-size: 11px;">
                                                    <i class="fas fa-exclamation-triangle me-1"></i> Kasus Lapangan
                                                </span>
                                                <?php if (! empty($item['case_severity'])): ?>
                                                    <span class="badge radius-6 px-2 py-0 border" style="<?= $sevClass ?> font-size: 10px;">
                                                        <i class="fas fa-shield-alt me-1"></i> <?= $sevText ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <span class="text-muted small" style="font-size: 11px;">
                                                <i class="far fa-clock me-1"></i> <?= esc($item['reading_time'] ?? 5) ?> mnt
                                            </span>
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center gap-1 mb-2">
                                            <?php if (! empty($item['procurement_stage'])): ?>
                                                <span class="badge radius-6 px-2 py-0 border" style="background: #e0f2fe; color: #0369a1; border-color: #7dd3fc; font-size: 11px; font-weight: 600;">
                                                    <i class="fas fa-layer-group me-1"></i> <?= esc($item['procurement_stage']) ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if (! empty($item['organization_unit'])): ?>
                                                <span class="badge radius-6 px-2 py-0 border" style="background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; font-size: 11px; font-weight: 600;">
                                                    <i class="fas fa-building me-1 text-primary"></i> <?= esc($item['organization_unit']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <h4 class="fw-bold mb-2" style="font-size: 15.5px; line-height: 1.4; color: #082f5c;">
                                            <a href="<?= esc($detailUrl) ?>" class="text-decoration-none text-reset hover-primary">
                                                <?= esc($item['title']) ?>
                                            </a>
                                        </h4>
                                        <p class="text-muted small mb-2 flex-grow-1" style="line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; font-size: 12.5px;">
                                            <?= esc($item['summary'] ?? '') ?>
                                        </p>
                                        <?php if (! empty($item['resolution_summary'])): ?>
                                            <div class="p-2 radius-8 mb-3" style="background: #fff7ed; border-left: 3px solid #ea580c; color: #7c2d12; font-size: 12px; font-weight: 600; line-height: 1.5;">
                                                <strong>Pelajaran:</strong> <?= esc(mb_strimwidth($item['resolution_summary'], 0, 95, '...')) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                                            <a href="<?= esc($detailUrl) ?>" class="btn btn-sm btn-outline-danger radius-8 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 12px;">
                                                <span>Pelajari Kasus</span>
                                                <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
                                            </a>
                                            <?= view('landing/components/bookmark_button', [
                                                'type'  => 'knowledge_case',
                                                'id'    => (int) $item['id'],
                                                'title' => $item['title'] ?? null,
                                                'size'  => 'sm',
                                            ]) ?>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    <!-- ===========================================================
                         TEMPLATE DOKUMEN CATALOG
                         =========================================================== -->
                    <?php elseif ($catalogType === 'templates'): ?>
                        <div class="row g-3">
                            <?php foreach ($items as $item):
                                $ext = strtoupper($item['file_extension'] ?? 'DOCX');
                                $extBadgeColor = match ($ext) {
                                    'XLSX', 'XLS' => 'background: #dcfce7; color: #166534;',
                                    'PDF'         => 'background: #fee2e2; color: #991b1b;',
                                    default       => 'background: #e0e7ff; color: #3730a3;',
                                };
                                $detailUrl   = route_to('public.knowledge.templates.detail', $item['slug']);
                                $downloadUrl = route_to('public.knowledge.templates.download', $item['slug']);
                            ?>
                                <div class="col-12">
                                    <article class="card h-100 border-0 shadow-2xs radius-12 p-3 d-flex flex-column knowledge-catalog-card bg-white border">
                                        <?php
                                            $isCoverImg = false;
                                            if (! empty($item['file_original_name'])) {
                                                $cExt = strtolower(pathinfo($item['file_original_name'], PATHINFO_EXTENSION));
                                                $isCoverImg = in_array($cExt, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'], true);
                                            }
                                            $tmplCover = ! empty($item['cover_url']) ? $item['cover_url'] : (! empty($item['cover_file_id']) && $isCoverImg ? site_url('file/' . (int) $item['cover_file_id']) : null);
                                        ?>
                                        <div class="d-flex align-items-start gap-3 mb-2">
                                            <?php if ($tmplCover): ?>
                                                <img src="<?= esc($tmplCover) ?>" alt="<?= esc($item['title']) ?>" class="radius-10 flex-shrink-0 object-fit-cover shadow-xs border" style="width: 52px; height: 52px;">
                                            <?php else: ?>
                                                <div class="d-flex align-items-center justify-content-center radius-10 flex-shrink-0 fw-bold shadow-xs" style="width: 46px; height: 46px; <?= esc($extBadgeColor) ?>; font-size: 13px;">
                                                    <?= esc($ext) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="flex-grow-1">
                                                <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                                                    <span class="badge border px-2 py-0 small" style="background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; font-size: 11px; font-weight: 600;">Standar PBJ</span>
                                                    <?php if (! empty($item['document_category'])): ?>
                                                        <span class="badge radius-6 px-2 py-0 border" style="background: #e0f2fe; color: #0369a1; border-color: #7dd3fc; font-size: 11px; font-weight: 600;">
                                                            <i class="fas fa-tag me-1"></i> <?= esc($item['document_category']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if (! empty($item['document_version'])): ?>
                                                        <span class="badge border px-2 py-0" style="background: #f8fafc; color: #0f172a; border-color: #cbd5e1; font-size: 11px; font-weight: 600;">
                                                            <i class="fas fa-code-branch me-1 text-primary"></i> <?= esc($item['document_version']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <h4 class="fw-bold mb-1" style="font-size: 15.5px; color: #082f5c; line-height: 1.4;">
                                                    <a href="<?= esc($detailUrl) ?>" class="text-decoration-none text-reset hover-primary">
                                                        <?= esc($item['title']) ?>
                                                    </a>
                                                </h4>
                                            </div>
                                        </div>
                                        <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 12.5px;">
                                            <?= esc($item['summary'] ?? '') ?>
                                        </p>
                                        <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="<?= esc($downloadUrl) ?>" class="btn btn-sm btn-primary radius-8 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 12px;">
                                                    <i class="fas fa-download"></i> Unduh Berkas
                                                </a>
                                                <a href="<?= esc($detailUrl) ?>" class="btn btn-sm btn-outline-secondary radius-8 px-3 py-1 fw-semibold" style="font-size: 12px;">
                                                    <i class="fas fa-info-circle me-1"></i> Detail
                                                </a>
                                            </div>
                                            <?= view('landing/components/bookmark_button', [
                                                'type'  => 'knowledge_template',
                                                'id'    => (int) $item['id'],
                                                'title' => $item['title'] ?? null,
                                                'size'  => 'sm',
                                            ]) ?>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Pagination Links -->
                    <?php if ($pager): ?>
                        <?php $links = $pager->links($catalogType, 'default_full') ?: $pager->links('default', 'default_full'); ?>
                        <?php if ($links): ?>
                            <div class="pagination-wrapper row mt-4">
                                <div class="col-12 d-flex justify-content-center">
                                    <?= $links ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                <?php endif; ?>
            </div>

            <!-- Right Column: Clean Structured Sidebar (col-lg-4) -->
            <div class="col-lg-4">

                <!-- Sidebar Widget 1: Quick Search Box -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-search"></i>
                        <span>Cari di Bunga Rampai</span>
                    </h4>
                    <form action="<?= esc($heroMeta['searchUrl']) ?>" method="get" class="berita-searchbox news-search-box">
                        <input type="text" name="q" placeholder="Cari artikel, kasus, template...">
                        <button type="submit" aria-label="Cari"><i class="fas fa-search"></i></button>
                    </form>
                    <div class="d-flex flex-wrap gap-1 mt-3">
                        <a href="<?= site_url('bunga-rampai/artikel?q=' . urlencode('Perpres 12/2021')) ?>" class="badge border text-decoration-none" style="background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; font-size: 11.5px; font-weight: 600;">Perpres 12/2021</a>
                        <a href="<?= site_url('bunga-rampai/artikel?q=' . urlencode('Penyusunan HPS')) ?>" class="badge border text-decoration-none" style="background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; font-size: 11.5px; font-weight: 600;">Penyusunan HPS</a>
                        <a href="<?= site_url('bunga-rampai/artikel?q=' . urlencode('E-Katalog')) ?>" class="badge border text-decoration-none" style="background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; font-size: 11.5px; font-weight: 600;">E-Katalog</a>
                        <a href="<?= site_url('bunga-rampai/template-dokumen?q=' . urlencode('KAK')) ?>" class="badge border text-decoration-none" style="background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; font-size: 11.5px; font-weight: 600;">Template KAK</a>
                        <a href="<?= site_url('bunga-rampai/studi-kasus?q=' . urlencode('Audit BPK')) ?>" class="badge border text-decoration-none" style="background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; font-size: 11.5px; font-weight: 600;">Audit BPK</a>
                    </div>
                </div>

                <!-- Sidebar Widget 2: Kategori Bunga Rampai (Nav Pills with Live Count) -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-folder-open" style="background: #fef3c7; color: #d97706;"></i>
                        <span>Kategori Bunga Rampai</span>
                    </h4>
                    <nav class="arena-nav-pills berita-catlist" aria-label="Kategori Bunga Rampai">
                        <a href="<?= site_url('bunga-rampai') ?>" class="arena-nav-pill">
                            <span class="left"><span class="dot" style="background:#0284c7"></span>Semua Eksplorasi</span>
                            <span class="count"><?= (int) ($stats['total_all'] ?? 0) ?></span>
                        </a>
                        <a href="<?= site_url('bunga-rampai/artikel') ?>" class="arena-nav-pill <?= $catalogType === 'articles' ? 'is-active' : '' ?>">
                            <span class="left"><span class="dot" style="background:#0284c7"></span>Artikel PBJ</span>
                            <span class="count"><?= (int) ($stats['total_articles'] ?? 0) ?></span>
                        </a>
                        <a href="<?= site_url('bunga-rampai/faq') ?>" class="arena-nav-pill <?= $catalogType === 'faqs' ? 'is-active' : '' ?>">
                            <span class="left"><span class="dot" style="background:#16a34a"></span>Tanya Jawab (FAQ)</span>
                            <span class="count"><?= (int) ($stats['total_faqs'] ?? 0) ?></span>
                        </a>
                        <a href="<?= site_url('bunga-rampai/studi-kasus') ?>" class="arena-nav-pill <?= $catalogType === 'cases' ? 'is-active' : '' ?>">
                            <span class="left"><span class="dot" style="background:#dc2626"></span>Studi Kasus Lapangan</span>
                            <span class="count"><?= (int) ($stats['total_cases'] ?? 0) ?></span>
                        </a>
                        <a href="<?= site_url('bunga-rampai/template-dokumen') ?>" class="arena-nav-pill <?= $catalogType === 'templates' ? 'is-active' : '' ?>">
                            <span class="left"><span class="dot" style="background:#d97706"></span>Template Dokumen</span>
                            <span class="count"><?= (int) ($stats['total_templates'] ?? 0) ?></span>
                        </a>
                        <a href="<?= site_url('bunga-rampai/multimedia') ?>" class="arena-nav-pill <?= in_array($catalogType, ['multimedia', 'media', 'videos', 'gallery'], true) ? 'is-active' : '' ?>">
                            <span class="left"><span class="dot" style="background:#0891b2"></span>Multimedia PBJ</span>
                            <span class="count"><?= (int) ($stats['total_media'] ?? 0) ?></span>
                        </a>
                    </nav>
                </div>

                <!-- Sidebar Widget 3: Jelajahi Ekosistem Pengetahuan PBJ -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-layer-group" style="background: #e0f2fe; color: #0284c7;"></i>
                        <span>Jelajahi Ekosistem Pengetahuan</span>
                    </h4>
                    <p class="text-muted small mb-3" style="font-size: 13px; line-height: 1.55;">
                        Pusat referensi dan repositori resmi Pengadaan Barang/Jasa Pemerintah Provinsi Lampung:
                    </p>
                    <div class="d-flex flex-column gap-2">
                        <div class="p-2 radius-8 border-start border-3 border-primary bg-light">
                            <strong style="font-size: 13px; color: #0f172a;">Rujukan Praktik Baik</strong>
                            <p class="text-muted small mb-0" style="font-size: 12px; line-height: 1.45;">Analisis dan tips mitigasi temuan pemeriksaan LKPP &amp; BPK.</p>
                        </div>
                        <div class="p-2 radius-8 border-start border-3 border-success bg-light">
                            <strong style="font-size: 13px; color: #0f172a;">Template Baku Teruji</strong>
                            <p class="text-muted small mb-0" style="font-size: 12px; line-height: 1.45;">Format KAK, SPK, kertas kerja HPS dan BAST siap pakai.</p>
                        </div>
                        <div class="p-2 radius-8 border-start border-3 border-warning bg-light">
                            <strong style="font-size: 13px; color: #0f172a;">Multimedia Edukatif</strong>
                            <p class="text-muted small mb-0" style="font-size: 12px; line-height: 1.45;">Infografik alur dan rekaman webinar tutorial SPSE.</p>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Widget 4: Top 3 Artikel PBJ Terbaru -->
                <?php if (! empty($topArticles)): ?>
                    <div class="berita-widget">
                        <h4 class="berita-widget__title">
                            <i class="fas fa-newspaper" style="background: #e0f2fe; color: #0284c7;"></i>
                            <span>Artikel PBJ Terbaru</span>
                        </h4>
                        <?php foreach (array_slice($topArticles, 0, 3) as $aRank => $aItem): ?>
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
                                        <span><i class="far fa-clock text-primary me-1"></i> <?= esc($aItem['reading_time'] ?? 5) ?> mnt</span>
                                        <span>&middot;</span>
                                        <a href="<?= route_to('public.knowledge.article.detail', $aItem['slug']) ?>" class="fw-bold text-primary text-decoration-none">Baca &rarr;</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="<?= site_url('bunga-rampai/artikel') ?>" class="btn btn-sm btn-outline-primary w-100 radius-8 fw-semibold mt-3" style="font-size: 12px;">
                            Lihat Semua Artikel (<?= (int) ($stats['total_articles'] ?? 0) ?>) &rarr;
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Sidebar Widget 5: Top 3 Studi Kasus Lapangan -->
                <?php if (! empty($topCases)): ?>
                    <div class="berita-widget">
                        <h4 class="berita-widget__title">
                            <i class="fas fa-briefcase" style="background: #fee2e2; color: #dc2626;"></i>
                            <span>Studi Kasus Lapangan</span>
                        </h4>
                        <?php foreach (array_slice($topCases, 0, 3) as $cRank => $cItem): ?>
                            <div class="berita-trend">
                                <div class="berita-trend__rank" style="background: #fee2e2; color: #dc2626;">
                                    <?= $cRank + 1 ?>
                                </div>
                                <div class="berita-trend__body">
                                    <h5 class="berita-trend__title">
                                        <a href="<?= route_to('public.knowledge.case-study.detail', $cItem['slug']) ?>">
                                            <?= esc($cItem['title']) ?>
                                        </a>
                                    </h5>
                                    <div class="berita-trend__meta">
                                        <span class="badge radius-6 px-2 py-0 border" style="background: #fee2e2; color: #991b1b; border-color: #fca5a5; font-size: 11px; font-weight: 700;">Kasus</span>
                                        <span>&middot;</span>
                                        <a href="<?= route_to('public.knowledge.case-study.detail', $cItem['slug']) ?>" class="fw-bold text-danger text-decoration-none">Pelajari &rarr;</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="<?= site_url('bunga-rampai/studi-kasus') ?>" class="btn btn-sm btn-outline-danger w-100 radius-8 fw-semibold mt-3" style="font-size: 12px;">
                            Lihat Semua Kasus (<?= (int) ($stats['total_cases'] ?? 0) ?>) &rarr;
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Sidebar Widget 6: Top 3 Template Dokumen PBJ -->
                <?php if (! empty($topTemplates)): ?>
                    <div class="berita-widget">
                        <h4 class="berita-widget__title">
                            <i class="fas fa-file-invoice" style="background: #fef3c7; color: #d97706;"></i>
                            <span>Template Dokumen Siap Unduh</span>
                        </h4>
                        <?php foreach (array_slice($topTemplates, 0, 3) as $tRank => $tItem):
                            $tExt = strtoupper($tItem['file_extension'] ?? 'DOCX');
                        ?>
                            <div class="berita-trend">
                                <div class="berita-trend__rank" style="background: #fef3c7; color: #d97706;">
                                    <?= $tRank + 1 ?>
                                </div>
                                <div class="berita-trend__body">
                                    <h5 class="berita-trend__title">
                                        <a href="<?= route_to('public.knowledge.templates.detail', $tItem['slug']) ?>">
                                             <?= esc($tItem['title']) ?>
                                        </a>
                                    </h5>
                                    <div class="berita-trend__meta">
                                        <span class="badge radius-6 px-2 py-0 border" style="background: #f8fafc; color: #0f172a; border-color: #cbd5e1; font-size: 11px; font-weight: 700;"><?= esc($tExt) ?></span>
                                        <span>&middot;</span>
                                        <a href="<?= route_to('public.knowledge.templates.download', $tItem['slug']) ?>" class="fw-bold text-primary text-decoration-none">Unduh &rarr;</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="<?= site_url('bunga-rampai/template-dokumen') ?>" class="btn btn-sm btn-outline-warning text-dark w-100 radius-8 fw-semibold mt-3" style="font-size: 12px;">
                            Lihat Semua Template (<?= (int) ($stats['total_templates'] ?? 0) ?>) &rarr;
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Sidebar Widget 7: Nilai Integritas PBJ -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-shield-alt" style="background: #e0f2fe; color: #0284c7;"></i>
                        <span>Nilai Integritas PBJ Lampung</span>
                    </h4>
                    <p class="text-muted small mb-3" style="font-size: 13px; line-height: 1.55;">
                        Pemerintah Provinsi Lampung berkomitmen mengedepankan prinsip pengadaan berintegritas tinggi:
                    </p>
                    <div class="d-flex flex-column gap-2">
                        <div class="p-2 radius-8 border-start border-3 border-primary bg-light">
                            <strong style="font-size: 12.5px; color: #0f172a;">Transparan &amp; Terbuka</strong>
                            <p class="text-muted small mb-0" style="font-size: 11.5px; line-height: 1.4;">Akses informasi terbuka bagi seluruh pelaku usaha.</p>
                        </div>
                        <div class="p-2 radius-8 border-start border-3 border-success bg-light">
                            <strong style="font-size: 12.5px; color: #0f172a;">Akuntabel &amp; Bebas KKN</strong>
                            <p class="text-muted small mb-0" style="font-size: 11.5px; line-height: 1.4;">Dapat dipertanggungjawabkan secara hukum dan administratif.</p>
                        </div>
                        <div class="p-2 radius-8 border-start border-3 border-warning bg-light">
                            <strong style="font-size: 12.5px; color: #0f172a;">Efisien &amp; Efektif</strong>
                            <p class="text-muted small mb-0" style="font-size: 11.5px; line-height: 1.4;">Optimalisasi value for money APBD Provinsi Lampung.</p>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Widget 8: Bantuan & Konsultasi Biro PBJ -->
                <div class="berita-widget" style="background: linear-gradient(135deg, #052244 0%, #0a3d74 100%); color: #ffffff;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="p-2 radius-8" style="background: rgba(255,255,255,0.15);">
                            <i class="fas fa-headset text-warning" style="font-size: 20px;"></i>
                        </div>
                        <h4 class="mb-0 text-white fw-bold" style="font-size: 16px;">Butuh Konsultasi PBJ?</h4>
                    </div>
                    <p class="small text-white-80 mb-3" style="font-size: 12.5px; line-height: 1.5; color: rgba(255,255,255,0.85);">
                        Tim Pembinaan &amp; Advokasi Pengadaan Barang/Jasa Pemerintah Provinsi Lampung siap memberikan bimbingan teknis dan konsultasi.
                    </p>
                    <a href="<?= site_url('kontak') ?>" class="btn btn-sm btn-warning text-dark fw-bold w-100 radius-8">
                        <i class="fas fa-comments me-1"></i> Hubungi Layanan Konsultasi
                    </a>
                </div>

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

        <!-- Left: Big Media Stage with Slider Arrows -->
        <div class="position-relative d-flex align-items-center justify-content-center p-0 flex-grow-1" style="background: #000000; min-height: 380px; max-height: 80vh;">
            <!-- Modal Slide Counter Badge (e.g. 1/4) -->
            <div id="modalSlideCounter" class="position-absolute top-0 start-0 m-3 z-3 d-none">
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

<!-- =========================================================================
     JAVASCRIPT LOGIC FOR SOCIAL MEDIA INTERACTION & LIGHTBOX
     ========================================================================= -->
<script>
    // Switch layout between Grid Visual and Feed Sosial
    function toggleMediaLayout(mode) {
        const gridView = document.getElementById('mediaGridViewContainer');
        const feedView = document.getElementById('mediaFeedViewContainer');
        const btnGrid = document.getElementById('btnModeGrid');
        const btnFeed = document.getElementById('btnModeFeed');

        if (!gridView || !feedView) return;

        if (mode === 'feed') {
            gridView.style.display = 'none';
            feedView.style.display = 'flex';
            if (btnGrid) {
                btnGrid.classList.remove('btn-primary', 'active');
                btnGrid.classList.add('btn-outline-secondary');
            }
            if (btnFeed) {
                btnFeed.classList.remove('btn-outline-secondary');
                btnFeed.classList.add('btn-primary', 'active');
            }
        } else {
            gridView.style.display = 'flex';
            feedView.style.display = 'none';
            if (btnFeed) {
                btnFeed.classList.remove('btn-primary', 'active');
                btnFeed.classList.add('btn-outline-secondary');
            }
            if (btnGrid) {
                btnGrid.classList.remove('btn-outline-secondary');
                btnGrid.classList.add('btn-primary', 'active');
            }
        }
    }

    // Lightbox Modal Data & Controller
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
        const slides = Array.isArray(currentModalData.slides) && currentModalData.slides.length > 0
            ? currentModalData.slides
            : [{
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

    // Instagram Slider in Feed View Logic
    const instaSlideStates = {};

    function navigateInstaSlide(event, itemId, direction, totalSlides) {
        if (event) event.stopPropagation();
        const current = instaSlideStates[itemId] || 0;
        const target = Math.max(0, Math.min(current + direction, totalSlides - 1));
        goToInstaSlide(event, itemId, target, totalSlides);
    }

    function goToInstaSlide(event, itemId, targetIndex, totalSlides) {
        if (event) event.stopPropagation();
        instaSlideStates[itemId] = targetIndex;

        const track = document.getElementById('instaTrack_' + itemId);
        if (track) {
            track.style.transform = 'translateX(-' + (targetIndex * 100) + '%)';
        }

        const counter = document.getElementById('instaCounter_' + itemId);
        if (counter) {
            const span = counter.querySelector('.current-slide');
            if (span) span.textContent = targetIndex + 1;
        }

        const prevBtn = document.getElementById('instaPrev_' + itemId);
        const nextBtn = document.getElementById('instaNext_' + itemId);
        if (prevBtn) {
            if (targetIndex > 0) prevBtn.classList.remove('d-none');
            else prevBtn.classList.add('d-none');
        }
        if (nextBtn) {
            if (targetIndex < totalSlides - 1) nextBtn.classList.remove('d-none');
            else nextBtn.classList.add('d-none');
        }

        const dotsContainer = document.getElementById('instaDots_' + itemId);
        if (dotsContainer) {
            const dots = dotsContainer.querySelectorAll('.insta-dot');
            dots.forEach((dot, idx) => {
                if (idx === targetIndex) dot.classList.add('active');
                else dot.classList.remove('active');
            });
        }
    }

    // Like Button Interactive Toggle
    function toggleMediaLike(btn) {
        const icon = btn.querySelector('i');
        const counter = btn.querySelector('span');
        let count = parseInt(counter.textContent || '0', 10);

        if (btn.classList.contains('is-active')) {
            btn.classList.remove('is-active');
            icon.classList.remove('fas');
            icon.classList.add('far');
            counter.textContent = Math.max(0, count - 1);
        } else {
            btn.classList.add('is-active');
            icon.classList.remove('far');
            icon.classList.add('fas');
            counter.textContent = count + 1;
        }
    }

    // Share URL to clipboard
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

    function applyCatalogQuickSearch(keyword) {
        const input = document.getElementById('catalogHeroSearch');
        if (input) {
            input.value = keyword;
            input.closest('form').submit();
        }
    }
</script>

<?= $this->endSection() ?>