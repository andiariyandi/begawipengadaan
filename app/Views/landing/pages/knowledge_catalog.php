<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-knowledge.css') ?>" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$items       = $items ?? [];
$catalogType = $catalogType ?? 'articles';
$totalItems  = $totalItems ?? count($items);
$pager       = $pager ?? service('pager');
$q           = $q ?? '';
$featuredFilter = $featuredFilter ?? '';
$stageFilter    = $stage ?? '';
$severityFilter = $severity ?? '';
$categoryFilter = $category ?? '';

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
];

$heroMeta = $heroHeadings[$catalogType] ?? [
    'tag'   => 'Pusat Pengetahuan PBJ Lampung',
    'title' => esc($pageTitle),
    'desc'  => esc($description),
    'searchPlaceholder' => 'Cari di repositori Bunga Rampai...',
    'searchUrl' => current_url(),
];

$buildFilterUrl = function (array $params) use ($heroMeta, $q, $featuredFilter, $stageFilter, $severityFilter, $categoryFilter) {
    $current = [];
    if ($q !== '') $current['q'] = $q;
    if ($featuredFilter === '1') $current['featured'] = '1';
    if ($stageFilter !== '') $current['stage'] = $stageFilter;
    if ($severityFilter !== '') $current['severity'] = $severityFilter;
    if ($categoryFilter !== '') $current['category'] = $categoryFilter;

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

$stats       = $stats ?? [
    'total_articles'  => 0,
    'total_faqs'      => 0,
    'total_cases'     => 0,
    'total_templates' => 0,
    'total_media'     => 0,
    'total_videos'    => 0,
    'total_gallery'   => 0,
    'total_all'       => 0,
];

$detailRouteMap = [
    'articles'  => 'public.knowledge.article.detail',
    'cases'     => 'public.knowledge.case-study.detail',
    'templates' => 'public.knowledge.templates.detail',
    'faqs'      => null,
    'media'     => null,
    'videos'    => null,
    'gallery'   => null,
];
$detailRoute = $detailRouteMap[$catalogType] ?? null;

$isVisualMediaCategory = in_array($catalogType, ['media', 'videos', 'gallery'], true);
?>

<!-- =========================================================================
     PAGE HERO BUNGA RAMPAI (UNIFIED FI-STUDY DNA)
     ========================================================================= -->
<section class="arena-hero position-relative">
    <div class="arena-hero__bg" style="background-image: url(<?= base_url('assets/main/images/backgrounds/slider-1-1.png') ?>);"></div>
    <div class="arena-hero__overlay"></div>
    <div class="container">
        <div class="arena-hero__inner text-center pt-2 pt-md-4">
            <span class="arena-section-head__tag text-white mb-3" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.32); backdrop-filter: blur(6px);">
                <i class="fas fa-book-reader me-1"></i> <?= esc($heroMeta['tag']) ?>
            </span>
            <h1 class="text-white mb-3" style="font-size: 38px; font-weight: 800; line-height: 1.25; text-shadow: 0 2px 10px rgba(0,0,0,0.4);">
                <?= $heroMeta['title'] ?>
            </h1>
            <p class="mx-auto mb-4" style="max-width: 720px; font-size: 16px; line-height: 1.6; color: rgba(255,255,255,0.92); text-shadow: 0 1px 5px rgba(0,0,0,0.35);">
                <?= $heroMeta['desc'] ?>
            </p>

            <!-- Search Bar -->
            <div class="arena-search-bar mb-3">
                <form action="<?= esc($heroMeta['searchUrl']) ?>" method="get" class="d-flex align-items-center">
                    <i class="fas fa-search text-muted me-2" style="font-size: 16px;"></i>
                    <input type="text" name="q" id="catalogHeroSearch" value="<?= esc($q) ?>" placeholder="<?= esc($heroMeta['searchPlaceholder']) ?>" autocomplete="off">
                    <?php if ($featuredFilter === '1'): ?>
                        <input type="hidden" name="featured" value="1">
                    <?php endif; ?>
                    <button type="submit">
                        <i class="fas fa-search me-1"></i>
                        <span>Cari</span>
                    </button>
                    <?php if ($q !== ''): ?>
                        <a href="<?= esc($heroMeta['searchUrl']) ?>" class="btn btn-sm btn-link text-muted ms-1" title="Reset Pencarian">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Contextual Quick Keyword Suggestion Chips -->
            <?php
            $catalogChips = [
                'articles'  => ['Perpres 12/2021', 'Penyusunan HPS', 'E-Katalog', 'Mitigasi Risiko', 'Evaluasi Teknis'],
                'faqs'      => ['Kualifikasi', 'Sanggahan', 'Uang Muka', 'Jaminan', 'Addendum'],
                'cases'     => ['Audit BPK', 'Keterlambatan', 'Wanprestasi', 'Sengketa Kontrak', 'Pemutusan'],
                'templates' => ['KAK', 'HPS', 'SPK', 'BAST', 'Surat Perjanjian'],
                'media'     => ['Infografik', 'Alur Pengadaan', 'Cheat Sheet', 'Diagram Siklus'],
                'videos'    => ['Tutorial SPSE', 'Webinar', 'Bimtek', 'E-Purchasing'],
                'gallery'   => ['Bimtek', 'Workshop', 'Rakor', 'Monitoring'],
            ];
            $chips = $catalogChips[$catalogType] ?? ['Perpres 12/2021', 'HPS', 'KAK'];
            ?>
            <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 mb-4 text-white-50 small">
                <span class="text-white opacity-75" style="font-size: 12.5px;"><i class="fas fa-bolt text-warning me-1"></i> Topik Populer:</span>
                <?php foreach ($chips as $chip): ?>
                    <button type="button" class="btn btn-sm text-white px-3 py-1 radius-20 border-0" style="background: rgba(255,255,255,0.15); font-size: 12px; backdrop-filter: blur(4px);" onclick="applyCatalogQuickSearch('<?= esc($chip) ?>')">
                        <?= esc($chip) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Sub-Navigation Pills -->
            <nav class="arena-nav-pills" aria-label="Kategori Bunga Rampai">
                <a href="<?= site_url('bunga-rampai') ?>" class="arena-nav-pill">
                    <i class="fas fa-th-large"></i>
                    <span>Semua Eksplorasi</span>
                    <span class="badge"><?= (int) ($stats['total_all'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/artikel') ?>" class="arena-nav-pill <?= $catalogType === 'articles' ? 'active' : '' ?>">
                    <i class="fas fa-newspaper"></i>
                    <span>Artikel PBJ</span>
                    <span class="badge"><?= (int) ($stats['total_articles'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/faq') ?>" class="arena-nav-pill <?= $catalogType === 'faqs' ? 'active' : '' ?>">
                    <i class="fas fa-question-circle"></i>
                    <span>Tanya Jawab (FAQ)</span>
                    <span class="badge"><?= (int) ($stats['total_faqs'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/studi-kasus') ?>" class="arena-nav-pill <?= $catalogType === 'cases' ? 'active' : '' ?>">
                    <i class="fas fa-briefcase"></i>
                    <span>Studi Kasus</span>
                    <span class="badge"><?= (int) ($stats['total_cases'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/template-dokumen') ?>" class="arena-nav-pill <?= $catalogType === 'templates' ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice"></i>
                    <span>Template Dokumen</span>
                    <span class="badge"><?= (int) ($stats['total_templates'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/media') ?>" class="arena-nav-pill <?= $catalogType === 'media' ? 'active' : '' ?>">
                    <i class="fas fa-photo-video"></i>
                    <span>Media &amp; Infografik</span>
                    <span class="badge"><?= (int) ($stats['total_media'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/video') ?>" class="arena-nav-pill <?= $catalogType === 'videos' ? 'active' : '' ?>">
                    <i class="fas fa-video"></i>
                    <span>Video PBJ</span>
                    <span class="badge"><?= (int) ($stats['total_videos'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/galeri') ?>" class="arena-nav-pill <?= $catalogType === 'gallery' ? 'active' : '' ?>">
                    <i class="fas fa-images"></i>
                    <span>Galeri Kegiatan</span>
                    <span class="badge"><?= (int) ($stats['total_gallery'] ?? 0) ?></span>
                </a>
            </nav>
        </div>
    </div>
</section>

<!-- =========================================================================
     FILTER & CONTROLS TOOLBAR
     ========================================================================= -->
<section class="py-3 bg-white border-bottom">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="text-muted small">
                    Menampilkan <strong><?= count($items) ?></strong> dari <strong><?= (int) $totalItems ?></strong> <?= esc($pageTitle) ?>
                </span>
                <?php if ($q !== ''): ?>
                    <span class="badge bg-light text-dark border">Kata kunci: "<?= esc($q) ?>"</span>
                <?php endif; ?>
                <?php if ($featuredFilter === '1'): ?>
                    <span class="badge bg-warning text-dark"><i class="fas fa-star me-1"></i> Pilihan Editor</span>
                <?php endif; ?>
                <?php if ($stageFilter !== ''): ?>
                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">Tahap: <?= esc($stageFilter) ?></span>
                <?php endif; ?>
                <?php if ($severityFilter !== ''): ?>
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Risiko: <?= esc(ucfirst($severityFilter)) ?></span>
                <?php endif; ?>
                <?php if ($categoryFilter !== ''): ?>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">Kategori: <?= esc($categoryFilter) ?></span>
                <?php endif; ?>
            </div>

            <!-- Visual Controls for Media & Gallery -->
            <?php if ($isVisualMediaCategory): ?>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small me-1">Mode Tampilan:</span>
                    <button type="button" class="btn btn-sm btn-primary radius-20 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1 active" id="btnModeGrid" onclick="toggleMediaLayout('grid')">
                        <i class="fas fa-th-large"></i> Grid Visual
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary radius-20 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1" id="btnModeFeed" onclick="toggleMediaLayout('feed')">
                        <i class="fas fa-stream"></i> Feed Sosial
                    </button>
                </div>
                <!-- Filter Pills for Articles -->
            <?php elseif ($catalogType === 'articles'): ?>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="text-muted small me-1 d-none d-md-inline">Tahapan PBJ:</span>
                    <a href="<?= $buildFilterUrl(['stage' => '']) ?>" class="btn btn-sm <?= $stageFilter === '' ? 'btn-primary' : 'btn-outline-secondary' ?> radius-20 px-3 py-1 small fw-semibold">
                        Semua
                    </a>
                    <?php foreach (['Perencanaan', 'Persiapan', 'Pemilihan', 'Pelaksanaan Kontrak', 'Serah Terima'] as $stg): ?>
                        <a href="<?= $buildFilterUrl(['stage' => $stg]) ?>" class="btn btn-sm <?= $stageFilter === $stg ? 'btn-primary' : 'btn-outline-secondary' ?> radius-20 px-3 py-1 small fw-semibold">
                            <?= esc($stg) ?>
                        </a>
                    <?php endforeach; ?>
                    <a href="<?= $buildFilterUrl(['featured' => $featuredFilter === '1' ? '' : '1']) ?>" class="btn btn-sm <?= $featuredFilter === '1' ? 'btn-warning text-dark' : 'btn-outline-secondary' ?> radius-20 px-3 py-1 small fw-semibold">
                        <i class="fas fa-star me-1"></i> Unggulan
                    </a>
                </div>
                <!-- Filter Pills for Case Studies -->
            <?php elseif ($catalogType === 'cases'): ?>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="text-muted small me-1 d-none d-md-inline">Tingkat Risiko:</span>
                    <a href="<?= $buildFilterUrl(['severity' => '']) ?>" class="btn btn-sm <?= $severityFilter === '' ? 'btn-primary' : 'btn-outline-secondary' ?> radius-20 px-3 py-1 small fw-semibold">
                        Semua
                    </a>
                    <a href="<?= $buildFilterUrl(['severity' => 'critical']) ?>" class="btn btn-sm <?= $severityFilter === 'critical' ? 'btn-danger' : 'btn-outline-secondary' ?> radius-20 px-3 py-1 small fw-semibold">
                        <i class="fas fa-fire me-1"></i> Kritis
                    </a>
                    <a href="<?= $buildFilterUrl(['severity' => 'high']) ?>" class="btn btn-sm <?= $severityFilter === 'high' ? 'btn-warning text-dark' : 'btn-outline-secondary' ?> radius-20 px-3 py-1 small fw-semibold">
                        Tinggi
                    </a>
                    <a href="<?= $buildFilterUrl(['severity' => 'medium']) ?>" class="btn btn-sm <?= $severityFilter === 'medium' ? 'btn-info text-dark' : 'btn-outline-secondary' ?> radius-20 px-3 py-1 small fw-semibold">
                        Sedang
                    </a>
                    <a href="<?= $buildFilterUrl(['severity' => 'low']) ?>" class="btn btn-sm <?= $severityFilter === 'low' ? 'btn-success' : 'btn-outline-secondary' ?> radius-20 px-3 py-1 small fw-semibold">
                        Rendah
                    </a>
                    <a href="<?= $buildFilterUrl(['featured' => $featuredFilter === '1' ? '' : '1']) ?>" class="btn btn-sm <?= $featuredFilter === '1' ? 'btn-warning text-dark' : 'btn-outline-secondary' ?> radius-20 px-3 py-1 small fw-semibold">
                        <i class="fas fa-star me-1"></i> Unggulan
                    </a>
                </div>
                <!-- Filter Pills for Templates -->
            <?php elseif ($catalogType === 'templates'): ?>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="text-muted small me-1 d-none d-md-inline">Kategori Berkas:</span>
                    <a href="<?= $buildFilterUrl(['category' => '']) ?>" class="btn btn-sm <?= $categoryFilter === '' ? 'btn-primary' : 'btn-outline-secondary' ?> radius-20 px-3 py-1 small fw-semibold">
                        Semua
                    </a>
                    <?php foreach (['KAK', 'HPS', 'Kontrak', 'BAST', 'Evaluasi'] as $cat): ?>
                        <a href="<?= $buildFilterUrl(['category' => $cat]) ?>" class="btn btn-sm <?= $categoryFilter === $cat ? 'btn-primary' : 'btn-outline-secondary' ?> radius-20 px-3 py-1 small fw-semibold">
                            <?= esc($cat) ?>
                        </a>
                    <?php endforeach; ?>
                    <a href="<?= $buildFilterUrl(['featured' => $featuredFilter === '1' ? '' : '1']) ?>" class="btn btn-sm <?= $featuredFilter === '1' ? 'btn-warning text-dark' : 'btn-outline-secondary' ?> radius-20 px-3 py-1 small fw-semibold">
                        <i class="fas fa-star me-1"></i> Unggulan
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- =========================================================================
     CATALOG CONTENT SECTION (TAILORED PER SUB-MENU)
     ========================================================================= -->
<section class="py-5" style="background: #f8fafc; min-height: 55vh;">
    <div class="container">
        <?php if ($msg = session()->getFlashdata('success')): ?>
            <div class="alert alert-success radius-8 mb-4"><?= esc($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = session()->getFlashdata('error')): ?>
            <div class="alert alert-danger radius-8 mb-4"><?= esc($msg) ?></div>
        <?php endif; ?>

        <?php if (empty($items)): ?>
            <!-- Polished Empty State -->
            <div class="card border-0 shadow-sm radius-16 p-5 text-center my-4 bg-white">
                <div class="d-inline-flex align-items-center justify-content-center mx-auto mb-3 radius-50" style="width: 72px; height: 72px; background: #f1f5f9; color: #94a3b8; font-size: 32px;">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h4 class="fw-bold mb-2" style="color: #082f5c;">Tidak Ada Konten Ditemukan</h4>
                <p class="text-muted mx-auto mb-4" style="max-width: 480px; font-size: 14.5px;">
                    <?php if ($q !== ''): ?>
                        Tidak ada konten yang sesuai dengan pencarian "<strong><?= esc($q) ?></strong>". Silakan coba kata kunci lain.
                    <?php else: ?>
                        Belum ada konten yang dipublikasikan pada kategori <?= esc($pageTitle) ?>.
                    <?php endif; ?>
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <?php if ($q !== '' || $featuredFilter !== ''): ?>
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

                <!-- MODE A: GRID VISUAL (INSTAGRAM STYLE GRID) -->
                <div id="mediaGridViewContainer" class="row g-4">
                    <?php foreach ($items as $item):
                        $previewImg = $item['preview_url'] ?? base_url('assets/main/images/gallery/gallery-page-1-1.jpg');
                        $isVideo    = ! empty($item['is_video']);
                        $embedUrl   = $item['embed_url'] ?? '';
                        $extUrl     = $item['external_url'] ?? '';
                        $modalData  = [
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
                        ];
                    ?>
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="card h-100 border-0 shadow-sm radius-16 overflow-hidden bg-white transition-hover position-relative" style="border: 1px solid #edf2f7 !important;">
                                <!-- Media Visual Preview -->
                                <div class="media-visual-container position-relative" style="height: 250px;" onclick="openMediaModal(<?= esc(json_encode($modalData)) ?>)">
                                    <img src="<?= esc($previewImg) ?>" alt="<?= esc($item['title'] ?? 'Media PBJ') ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/gallery/gallery-page-1-1.jpg') ?>';">

                                    <!-- Top Badges -->
                                    <div class="position-absolute top-0 start-0 m-3 z-2 d-flex flex-wrap gap-1">
                                        <?php if ($isVideo): ?>
                                            <span class="badge bg-danger text-white radius-6 px-2 py-1 small fw-semibold">
                                                <i class="fab fa-youtube me-1"></i> Video
                                            </span>
                                        <?php elseif ($catalogType === 'gallery'): ?>
                                            <span class="badge bg-dark bg-opacity-75 text-white radius-6 px-2 py-1 small fw-semibold">
                                                <i class="fas fa-camera me-1"></i> Galeri
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-info text-dark radius-6 px-2 py-1 small fw-semibold">
                                                <i class="fas fa-image me-1"></i> Infografik
                                            </span>
                                        <?php endif; ?>
                                        <?php if (! empty($item['location'])): ?>
                                            <span class="badge bg-dark bg-opacity-75 text-white radius-6 px-2 py-1 small fw-semibold">
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i> <?= esc($item['location']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Center Play Button for Video -->
                                    <?php if ($isVideo): ?>
                                        <div class="position-absolute top-50 start-50 translate-middle z-2">
                                            <div class="media-play-pulse">
                                                <i class="fas fa-play ms-1" style="font-size: 22px;"></i>
                                            </div>
                                        </div>
                                        <?php if (! empty($item['duration'])): ?>
                                            <div class="position-absolute bottom-0 end-0 m-2 z-2">
                                                <span class="badge bg-dark bg-opacity-75 text-white radius-6 px-2 py-1 small">
                                                    <i class="far fa-clock me-1 text-warning"></i> <?= esc($item['duration']) ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <!-- Hover Overlay (Instagram Visual Reveal) -->
                                    <div class="media-hover-overlay">
                                        <h5 class="fw-bold mb-1 text-white" style="font-size: 16px; line-height: 1.35;">
                                            <?= esc($item['title'] ?? '') ?>
                                        </h5>
                                        <p class="small text-white-80 mb-3" style="color: rgba(255,255,255,0.85); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 13px;">
                                            <?= esc($item['caption'] ?? '') ?>
                                        </p>
                                        <div class="d-flex align-items-center justify-content-between pt-2 border-top border-white border-opacity-25">
                                            <div class="d-flex align-items-center gap-3 small text-white">
                                                <span><i class="fas fa-heart text-danger me-1"></i> <?= (int) ($item['likes_count'] ?? 24) ?></span>
                                                <span><i class="fas fa-eye me-1"></i> <?= (int) ($item['views_count'] ?? 150) ?></span>
                                            </div>
                                            <span class="badge bg-white text-dark radius-20 px-3 py-1 small fw-bold">
                                                <i class="fas fa-expand me-1"></i> Preview
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Footer / Quick Info -->
                                <div class="p-3 bg-white d-flex align-items-center justify-content-between border-top">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?= base_url('assets/main/images/resources/logo-1.png') ?>" alt="Biro PBJ" class="radius-50 border p-1" style="width: 28px; height: 28px; object-fit: contain;">
                                        <span class="small fw-semibold text-dark text-truncate" style="max-width: 170px;">
                                            <?= esc($item['title'] ?? 'Media PBJ') ?>
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
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

                <!-- MODE B: FEED SOSIAL (SOCIAL MEDIA POST CARDS) -->
                <div id="mediaFeedViewContainer" class="row g-4 justify-content-center" style="display: none;">
                    <div class="col-xl-7 col-lg-8 col-md-10">
                        <?php foreach ($items as $item):
                            $previewImg = $item['preview_url'] ?? base_url('assets/main/images/gallery/gallery-page-1-1.jpg');
                            $isVideo    = ! empty($item['is_video']);
                            $embedUrl   = $item['embed_url'] ?? '';
                            $extUrl     = $item['external_url'] ?? '';
                            $modalData  = [
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
                            ];
                        ?>
                            <!-- Single Social Feed Post -->
                            <article class="card border-0 shadow-sm radius-16 overflow-hidden mb-4 bg-white" style="border: 1px solid #edf2f7 !important;">
                                <!-- Post Author Header -->
                                <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="media-avatar-ring">
                                            <img src="<?= base_url('assets/main/images/resources/logo-1.png') ?>" alt="Biro PBJ" class="radius-50" style="width: 38px; height: 38px; object-fit: contain; background: #f8fafc; padding: 2px;">
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-1">
                                                <strong class="text-dark" style="font-size: 14.5px;">Biro Pengadaan Barang &amp; Jasa</strong>
                                                <i class="fas fa-check-circle text-primary" style="font-size: 13px;" title="Akun Resmi Terverifikasi"></i>
                                            </div>
                                            <small class="text-muted" style="font-size: 11.5px;">
                                                Pemerintah Provinsi Lampung &middot; <?= ! empty($item['published_at']) ? esc(date('d M Y', strtotime($item['published_at']))) : 'Terbaru' ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="badge radius-6 px-2 py-1 small <?= $isVideo ? 'bg-danger text-white' : ($catalogType === 'gallery' ? 'bg-dark text-white' : 'bg-primary text-white') ?>">
                                            <?= $isVideo ? '<i class="fab fa-youtube me-1"></i> Video' : ($catalogType === 'gallery' ? '<i class="fas fa-camera me-1"></i> Galeri' : '<i class="fas fa-image me-1"></i> Infografik') ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Large Media Visual Preview Centerpiece -->
                                <div class="media-visual-container position-relative" style="height: 380px;" onclick="openMediaModal(<?= esc(json_encode($modalData)) ?>)">
                                    <img src="<?= esc($previewImg) ?>" alt="<?= esc($item['title'] ?? 'Media PBJ') ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/gallery/gallery-page-1-1.jpg') ?>';">

                                    <!-- Video Play Centerpiece Overlay -->
                                    <?php if ($isVideo): ?>
                                        <div class="position-absolute top-50 start-50 translate-middle z-2">
                                            <div class="media-play-pulse">
                                                <i class="fas fa-play ms-1" style="font-size: 24px;"></i>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Zoom Icon Pill -->
                                    <div class="position-absolute bottom-0 end-0 m-3 z-2">
                                        <span class="badge bg-dark bg-opacity-75 text-white radius-20 px-3 py-1 small">
                                            <i class="fas fa-expand-arrows-alt me-1"></i> Perbesar Media
                                        </span>
                                    </div>
                                </div>

                                <!-- Social Engagement & Action Bar -->
                                <div class="px-3 pt-3 pb-2 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <!-- Like Reaction Button -->
                                        <button type="button" class="btn btn-link text-decoration-none p-0 text-muted media-like-btn" onclick="toggleMediaLike(this)">
                                            <i class="far fa-heart text-danger" style="font-size: 20px;"></i>
                                            <span class="fw-semibold ms-1 small text-dark"><?= (int) ($item['likes_count'] ?? 24) ?></span>
                                        </button>
                                        <!-- Open Preview Button -->
                                        <button type="button" class="btn btn-link text-decoration-none p-0 text-muted" onclick="openMediaModal(<?= esc(json_encode($modalData)) ?>)" title="Buka Detail">
                                            <i class="far fa-comment-alt" style="font-size: 19px;"></i>
                                            <span class="fw-semibold ms-1 small text-dark"><?= (int) ($item['shares_count'] ?? 5) ?></span>
                                        </button>
                                        <!-- Share / Copy Link -->
                                        <button type="button" class="btn btn-link text-decoration-none p-0 text-muted" onclick="copyMediaShareUrl('<?= esc(addslashes($item['title'] ?? '')) ?>')" title="Salin Tautan">
                                            <i class="far fa-paper-plane" style="font-size: 19px;"></i>
                                        </button>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if (! empty($extUrl)): ?>
                                            <a href="<?= esc($extUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-danger radius-6 px-2 py-1" style="font-size: 12px;">
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

                                <!-- Post Caption & Tags -->
                                <div class="px-3 pb-3">
                                    <h4 class="fw-bold mb-1" style="font-size: 16px; color: #082f5c; line-height: 1.4;">
                                        <?= esc($item['title'] ?? '') ?>
                                    </h4>
                                    <p class="text-muted small mb-2" style="font-size: 13.5px; line-height: 1.55;">
                                        <?= nl2br(esc($item['caption'] ?? '')) ?>
                                    </p>
                                    <!-- Hashtags -->
                                    <div class="mb-2">
                                        <?php foreach (($item['hashtags'] ?? []) as $ht): ?>
                                            <span class="media-hashtag-pill"><?= esc($ht) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                    <small class="text-muted d-block pt-2 border-top" style="font-size: 11.5px;">
                                        <i class="fas fa-eye me-1"></i> <?= (int) ($item['views_count'] ?? 150) ?> tayangan &middot; Dokumentasi Resmi Biro Pengadaan Barang &amp; Jasa Provinsi Lampung
                                    </small>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- ===========================================================
                 OTHER CATALOG TYPES (ARTIKEL, FAQ, STUDI KASUS, TEMPLATE)
                 =========================================================== -->
            <?php elseif ($catalogType === 'articles'): ?>
                <div class="row g-4">
                    <?php foreach ($items as $index => $item):
                        $coverImg = ! empty($item['cover_file_id'])
                            ? site_url('file/' . (int) $item['cover_file_id'])
                            : base_url('assets/main/images/blog/blog-2-' . (((int) $item['id'] % 4) + 1) . '.jpg');
                        $detailUrl = route_to('public.knowledge.article.detail', $item['slug']);
                    ?>
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <article class="card h-100 border-0 shadow-sm radius-16 overflow-hidden d-flex flex-column transition-hover" style="background: #ffffff; border: 1px solid #edf2f7 !important;">
                                <div class="position-relative" style="height: 190px; overflow: hidden; background: #082f5c;">
                                    <img src="<?= esc($coverImg) ?>" alt="<?= esc($item['title']) ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/blog/blog-2-1.jpg') ?>';">
                                    <div class="position-absolute top-0 start-0 m-3">
                                        <span class="badge bg-primary text-white radius-6 px-2 py-1 small fw-semibold">
                                            <i class="fas fa-newspaper me-1"></i> Artikel PBJ
                                        </span>
                                    </div>
                                    <?php if ((int) ($item['featured'] ?? 0) === 1): ?>
                                        <div class="position-absolute top-0 end-0 m-3">
                                            <span class="badge bg-dark bg-opacity-75 text-white radius-6 px-2 py-1 small">
                                                <i class="fas fa-star text-warning me-1"></i> Unggulan
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="p-4 d-flex flex-column flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between text-muted small mb-2" style="font-size: 11.5px;">
                                        <span><i class="fas fa-user-circle me-1"></i> <?= esc($item['author_name'] ?? 'Biro PBJ') ?></span>
                                        <span><i class="far fa-clock me-1"></i> <?= esc($item['reading_time'] ?? 5) ?> mnt</span>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-1 mb-2">
                                        <?php if (! empty($item['procurement_stage'])): ?>
                                            <span class="badge radius-6 px-2 py-0 border" style="background: #e0f2fe; color: #0369a1; border-color: #bae6fd; font-size: 10.5px;">
                                                <i class="fas fa-layer-group me-1"></i> <?= esc($item['procurement_stage']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (! empty($item['organization_unit'])): ?>
                                            <span class="badge radius-6 px-2 py-0 border bg-light text-secondary" style="font-size: 10.5px;">
                                                <i class="fas fa-building me-1 text-primary"></i> <?= esc($item['organization_unit']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="badge radius-6 px-2 py-0 bg-light text-muted border" style="font-size: 10.5px;">
                                            <i class="far fa-calendar-alt me-1"></i> <?= ! empty($item['published_at']) ? esc(date('d M Y', strtotime($item['published_at']))) : 'Terbaru' ?>
                                        </span>
                                    </div>
                                    <h4 class="fw-bold mb-2" style="font-size: 17px; line-height: 1.45; color: #082f5c;">
                                        <a href="<?= esc($detailUrl) ?>" class="text-decoration-none text-reset hover-primary">
                                            <?= esc($item['title']) ?>
                                        </a>
                                    </h4>
                                    <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.55; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                        <?= esc($item['summary'] ?? '') ?>
                                    </p>
                                    <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                        <a href="<?= esc($detailUrl) ?>" class="btn btn-sm btn-outline-primary radius-8 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                                            <span>Baca Artikel</span>
                                            <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
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

            <?php elseif ($catalogType === 'faqs'): ?>
                <div class="row justify-content-center">
                    <div class="col-xl-10">
                        <div class="accordion d-flex flex-column gap-3" id="faqCatalogAccordion">
                            <?php foreach ($items as $index => $item):
                                $collapseId = 'catalogFaq' . (int) $item['id'];
                                $headingId  = 'catalogHeading' . (int) $item['id'];
                            ?>
                                <div class="card border-0 shadow-sm radius-12 overflow-hidden bg-white">
                                    <div class="card-header bg-white border-0 p-3 p-md-4" id="<?= esc($headingId) ?>">
                                        <button class="btn btn-link w-100 text-start text-decoration-none p-0 d-flex align-items-center justify-content-between <?= $index > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?= esc($collapseId) ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="<?= esc($collapseId) ?>">
                                            <span class="fw-bold pe-3" style="color: #082f5c; font-size: 16px; line-height: 1.45;">
                                                <i class="fas fa-question-circle text-primary me-2"></i> <?= esc($item['title']) ?>
                                            </span>
                                            <i class="fas fa-chevron-down text-muted" style="font-size: 14px;"></i>
                                        </button>
                                    </div>
                                    <div id="<?= esc($collapseId) ?>" class="collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="<?= esc($headingId) ?>" data-bs-parent="#faqCatalogAccordion">
                                        <div class="card-body pt-0 px-3 px-md-4 pb-3 pb-md-4 text-muted" style="font-size: 14.5px; line-height: 1.65; border-top: 1px dashed #edf2f7;">
                                            <div class="mb-3">
                                                <?= nl2br(esc($item['content'] ?? $item['summary'] ?? 'Belum ada penjelasan jawaban.')) ?>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 border-top">
                                                <div class="d-flex flex-wrap align-items-center gap-1">
                                                    <?php if (! empty($item['legal_basis'])): ?>
                                                        <span class="badge bg-light text-secondary border px-2 py-1 small">
                                                            <i class="fas fa-balance-scale me-1 text-primary"></i> <?= esc($item['legal_basis']) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-primary border px-2 py-1 small">
                                                            <i class="fas fa-check-circle me-1"></i> Regulasi PBJ
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if (! empty($item['procurement_stage'])): ?>
                                                        <span class="badge bg-light text-secondary border px-2 py-1 small">
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

                        <!-- FAQ Helpdesk Banner -->
                        <div class="card border-0 radius-16 p-4 mt-5 text-white" style="background: linear-gradient(135deg, #082f5c 0%, #1e3a8a 100%);">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div>
                                    <h4 class="text-white fw-bold mb-1" style="font-size: 18px;">Punya Pertanyaan Spesifik Terkait Pengadaan?</h4>
                                    <p class="mb-0 text-white-80 small" style="color: rgba(255,255,255,0.85);">Tim Pembinaan &amp; Advokasi PBJ Biro Pengadaan Provinsi Lampung siap membantu konsultasi Anda.</p>
                                </div>
                                <a href="<?= site_url('kontak') ?>" class="btn btn-warning text-dark fw-bold radius-8 px-4 py-2">
                                    <i class="fas fa-headset me-1"></i> Hubungi Konsultasi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($catalogType === 'cases'): ?>
                <div class="row g-4">
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
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <article class="card h-100 border-0 shadow-sm radius-16 overflow-hidden d-flex flex-column transition-hover" style="background: #ffffff; border: 1px solid #edf2f7 !important;">
                                <div class="p-4 d-flex flex-column flex-grow-1">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-2">
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge radius-6 px-2 py-1 small fw-semibold" style="background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3;">
                                                <i class="fas fa-exclamation-triangle me-1"></i> Kasus Lapangan
                                            </span>
                                            <?php if (! empty($item['case_severity'])): ?>
                                                <span class="badge radius-6 px-2 py-0 border" style="<?= $sevClass ?> font-size: 10.5px;">
                                                    <i class="fas fa-shield-alt me-1"></i> <?= $sevText ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <span class="text-muted small" style="font-size: 11.5px;">
                                            <i class="far fa-clock me-1"></i> <?= esc($item['reading_time'] ?? 5) ?> mnt
                                        </span>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-1 mb-2">
                                        <?php if (! empty($item['procurement_stage'])): ?>
                                            <span class="badge bg-light text-secondary border px-2 py-0" style="font-size: 10.5px;">
                                                <i class="fas fa-layer-group me-1"></i> <?= esc($item['procurement_stage']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (! empty($item['organization_unit'])): ?>
                                            <span class="badge bg-light text-muted border px-2 py-0" style="font-size: 10.5px;">
                                                <i class="fas fa-building me-1"></i> <?= esc($item['organization_unit']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <h4 class="fw-bold mb-2" style="font-size: 17px; line-height: 1.45; color: #082f5c;">
                                        <a href="<?= esc($detailUrl) ?>" class="text-decoration-none text-reset hover-primary">
                                            <?= esc($item['title']) ?>
                                        </a>
                                    </h4>
                                    <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.55; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                        <?= esc($item['summary'] ?? '') ?>
                                    </p>
                                    <?php if (! empty($item['resolution_summary'])): ?>
                                        <div class="p-2 radius-8 mb-3 small" style="background: #fff7ed; border-left: 3px solid #f97316; color: #9a3412; font-size: 11.5px; line-height: 1.45;">
                                            <strong>Pelajaran:</strong> <?= esc(mb_strimwidth($item['resolution_summary'], 0, 110, '...')) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                        <a href="<?= esc($detailUrl) ?>" class="btn btn-sm btn-outline-danger radius-8 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                                            <span>Pelajari Kasus</span>
                                            <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                                        </a>
                                        <?= view('landing/components/bookmark_button', [
                                            'type'  => 'knowledge_case',
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

            <?php elseif ($catalogType === 'templates'): ?>
                <div class="row g-4">
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
                        <div class="col-xl-6">
                            <article class="card h-100 border-0 shadow-sm radius-16 p-4 d-flex flex-column transition-hover bg-white" style="border: 1px solid #edf2f7 !important;">
                                <div class="d-flex align-items-start gap-3 mb-3">
                                    <div class="d-flex align-items-center justify-content-center radius-12 flex-shrink-0 fw-bold shadow-sm" style="width: 54px; height: 54px; <?= esc($extBadgeColor) ?>; font-size: 14px;">
                                        <?= esc($ext) ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                                            <span class="badge bg-light text-primary border px-2 py-0 small" style="font-size: 10.5px;">Standar PBJ</span>
                                            <?php if (! empty($item['document_category'])): ?>
                                                <span class="badge radius-6 px-2 py-0 border" style="background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; font-size: 10.5px;">
                                                    <i class="fas fa-tag me-1"></i> <?= esc($item['document_category']) ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if (! empty($item['document_version'])): ?>
                                                <span class="badge bg-light text-dark border px-2 py-0" style="font-size: 10.5px;">
                                                    <i class="fas fa-code-branch me-1 text-primary"></i> <?= esc($item['document_version']) ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if (! empty($item['cover_file_id'])): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-0 small" style="font-size: 10.5px;">
                                                    <i class="fas fa-check-circle me-1"></i> File Siap Unduh
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <h4 class="fw-bold mb-1" style="font-size: 16.5px; color: #082f5c; line-height: 1.4;">
                                            <a href="<?= esc($detailUrl) ?>" class="text-decoration-none text-reset hover-primary">
                                                <?= esc($item['title']) ?>
                                            </a>
                                        </h4>
                                    </div>
                                </div>
                                <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.55; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?= esc($item['summary'] ?? '') ?>
                                </p>
                                <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="<?= esc($downloadUrl) ?>" class="btn btn-sm btn-primary radius-8 px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1">
                                            <i class="fas fa-download"></i> Unduh Berkas
                                        </a>
                                        <a href="<?= esc($detailUrl) ?>" class="btn btn-sm btn-outline-secondary radius-8 px-3 py-2 fw-semibold">
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

            <!-- Pagination Bar -->
            <?php if ($pager): ?>
                <?php $links = $pager->links($catalogType, 'default_full') ?: $pager->links('default', 'default_full'); ?>
                <?php if ($links): ?>
                    <div class="row mt-5">
                        <div class="col-12 d-flex justify-content-center">
                            <?= $links ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        <?php endif; ?>
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
            btnGrid.classList.remove('btn-primary', 'active');
            btnGrid.classList.add('btn-outline-secondary');
            btnFeed.classList.remove('btn-outline-secondary');
            btnFeed.classList.add('btn-primary', 'active');
        } else {
            gridView.style.display = 'flex';
            feedView.style.display = 'none';
            btnFeed.classList.remove('btn-primary', 'active');
            btnFeed.classList.add('btn-outline-secondary');
            btnGrid.classList.remove('btn-outline-secondary');
            btnGrid.classList.add('btn-primary', 'active');
        }
    }

    // Lightbox Modal Data & Controller
    let currentModalShareTitle = '';

    function openMediaModal(data) {
        const modal = document.getElementById('mediaPreviewModal');
        if (!modal) return;

        currentModalShareTitle = data.title || '';

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

        // Toggle Image vs Video Container
        const imgBox = document.getElementById('modalImageContainer');
        const vidBox = document.getElementById('modalVideoContainer');
        const imgEl = document.getElementById('modalPreviewImage');
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

        // External Link Button
        if (data.external_url) {
            extBtn.href = data.external_url;
            extBtn.classList.remove('d-none');
        } else {
            extBtn.classList.add('d-none');
        }

        // Open
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