<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-news.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-news.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-news.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-events.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-events.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-events.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-learning.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-learning.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-learning.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-regulation.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-regulation.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-regulation.css') : '1.0' ?>" />
<style>
    /* High-Contrast Badges (100% Readable text across dark and light surfaces) */
    .reg-thumb-badge {
        font-size: 11px !important;
        font-weight: 700 !important;
        padding: 4px 10px !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        line-height: 1.25 !important;
        letter-spacing: 0.3px !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2) !important;
        text-transform: uppercase !important;
    }
    .reg-thumb-badge--berlaku {
        background: #15803d !important;
        color: #ffffff !important;
        border: 1px solid #166534 !important;
    }
    .reg-thumb-badge--diubah {
        background: #f59e0b !important;
        color: #0f172a !important;
        border: 1px solid #d97706 !important;
    }
    .reg-thumb-badge--dicabut {
        background: #dc2626 !important;
        color: #ffffff !important;
        border: 1px solid #b91c1c !important;
    }
    .reg-thumb-badge--year {
        background: rgba(15, 23, 42, 0.88) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(4px) !important;
    }

    /* Sidebar Category List Matching Arena Belajar & High Contrast */
    .berita-catlist {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .berita-catlist a {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 9px 12px !important;
        border-radius: 10px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #1e293b !important;
        background: #f8fafc !important;
        border: 1px solid #f1f5f9 !important;
        text-decoration: none !important;
        transition: all 0.2s ease !important;
    }
    .berita-catlist a:hover,
    .berita-catlist a.is-active {
        background: #eff6ff !important;
        color: #0284c7 !important;
        border-color: #bae6fd !important;
    }
    .berita-catlist a .left {
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        min-width: 0 !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }
    .berita-catlist a .dot {
        width: 8px !important;
        height: 8px !important;
        border-radius: 50% !important;
        flex-shrink: 0 !important;
    }
    .berita-catlist a .count {
        background: #ffffff !important;
        color: #475569 !important;
        border: 1px solid #e2e8f0 !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        padding: 2px 7px !important;
        border-radius: 999px !important;
        flex-shrink: 0 !important;
    }

    /* Ultra-Clean Compact Modern Dropdown Select Pill (Matches Arena Belajar / Bunga Rampai) */
    .reg-select-pill {
        position: relative;
        display: inline-flex;
        align-items: center;
        vertical-align: middle;
    }
    .reg-select-pill .nice-select {
        display: none !important;
    }
    .reg-select-pill select {
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        display: inline-block !important;
        height: 28px !important;
        line-height: 26px !important;
        padding: 0 22px 0 24px !important;
        font-size: 11.5px !important;
        font-weight: 600 !important;
        color: #334155 !important;
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 9999px !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        outline: none !important;
        max-width: 155px !important;
        white-space: nowrap !important;
        text-overflow: ellipsis !important;
    }
    .reg-select-pill select:hover {
        background: #f8fafc !important;
        border-color: #94a3b8 !important;
        color: #0f172a !important;
    }
    .reg-select-pill select:focus {
        background: #ffffff !important;
        border-color: #0284c7 !important;
        box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15) !important;
        color: #0284c7 !important;
    }
    .reg-select-pill__prefix {
        position: absolute;
        left: 8px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 10px;
        color: #64748b;
        pointer-events: none;
        transition: color 0.2s ease;
    }
    .reg-select-pill__suffix {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 8px;
        color: #94a3b8;
        pointer-events: none;
        transition: color 0.2s ease;
    }
    .reg-select-pill select:focus ~ .reg-select-pill__prefix,
    .reg-select-pill select:focus ~ .reg-select-pill__suffix {
        color: #0284c7;
    }
    .reg-select-pill--active select {
        background: #eff6ff !important;
        border-color: #38bdf8 !important;
        color: #0284c7 !important;
        font-weight: 700 !important;
    }
    .reg-select-pill--active .reg-select-pill__prefix,
    .reg-select-pill--active .reg-select-pill__suffix {
        color: #0284c7 !important;
    }

    @media (max-width: 767.98px) {
        .evt-hero-banner {
            padding: 18px 14px !important;
        }
        .evt-hero-title {
            font-size: 20px !important;
        }
    }
    @media (max-width: 575.98px) {
        .reg-select-pill {
            flex: 1 1 calc(50% - 4px);
        }
        .reg-select-pill select {
            width: 100%;
            max-width: 100%;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$regulations         = $regulations ?? [];
$categories          = $categories ?? [];
$stages              = $stages ?? [];
$competencies        = $competencies ?? [];
$stats               = $stats ?? ['total' => 0, 'categories' => 0, 'downloaded' => 0, 'published' => 0, 'active_count' => 0, 'amended_count' => 0, 'revoked_count' => 0];
$popular             = $popular ?? [];
$years               = $years ?? [];
$keyword             = $keyword ?? '';
$selectedCategory    = $selectedCategory ?? null;
$selectedCategoryObj = $selectedCategoryObj ?? null;
$selectedStatus      = $selectedStatus ?? null;
$selectedYear        = $selectedYear ?? null;
$selectedStage       = $selectedStage ?? null;
$selectedSort        = $selectedSort ?? 'newest';
$activeTab           = $activeTab ?? 'katalog';
$totalRegulations    = (int) ($totalRegulations ?? count($regulations));
$currentPage         = (int) ($currentPage ?? 1);
$totalPages          = (int) ($totalPages ?? 1);
$perPage             = (int) ($perPage ?? 9);

// Helper function to build faceted filter URLs preserving query params
$buildFilterUrl = function (array $newParams) use ($selectedCategory, $keyword, $selectedStatus, $selectedYear, $selectedStage, $selectedSort, $activeTab) {
    $params = [];
    if (! empty($keyword)) $params['q'] = $keyword;
    if (! empty($selectedCategory)) $params['category_id'] = $selectedCategory;
    if (! empty($selectedStatus)) $params['status'] = $selectedStatus;
    if (! empty($selectedYear)) $params['year'] = $selectedYear;
    if (! empty($selectedStage)) $params['stage_id'] = $selectedStage;
    if ($selectedSort !== 'newest') $params['sort'] = $selectedSort;
    if ($activeTab !== 'katalog') $params['tab'] = $activeTab;

    foreach ($newParams as $k => $v) {
        if ($v === null || $v === '') {
            unset($params[$k]);
        } else {
            $params[$k] = $v;
        }
    }

    $query = http_build_query($params);
    return site_url('regulasi') . ($query ? '?' . $query : '');
};

// Helper legal status badge (High Contrast Soft Badges)
$renderLegalStatusBadge = function (?string $status): string {
    $status = strtolower((string) $status);
    return match ($status) {
        'active', 'berlaku' => '<span class="badge radius-6 px-2 py-1 small fw-bold d-inline-flex align-items-center gap-1" style="background: #dcfce7; color: #15803d; border: 1px solid #86efac;"><i class="fas fa-check-circle"></i> Berlaku</span>',
        'amended', 'diubah'  => '<span class="badge radius-6 px-2 py-1 small fw-bold d-inline-flex align-items-center gap-1" style="background: #fef3c7; color: #9a3412; border: 1px solid #fde047;"><i class="fas fa-exclamation-circle"></i> Diubah</span>',
        'revoked', 'dicabut' => '<span class="badge radius-6 px-2 py-1 small fw-bold d-inline-flex align-items-center gap-1" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5;"><i class="fas fa-times-circle"></i> Dicabut</span>',
        'expired', 'kedaluwarsa' => '<span class="badge radius-6 px-2 py-1 small fw-bold d-inline-flex align-items-center gap-1" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;"><i class="fas fa-clock"></i> Kedaluwarsa</span>',
        default => '<span class="badge radius-6 px-2 py-1 small fw-bold d-inline-flex align-items-center gap-1" style="background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe;"><i class="fas fa-info-circle"></i> ' . esc(ucfirst($status ?: 'Resmi')) . '</span>',
    };
};

// Background covers for regulation cards & hero
$bgSliderCovers = [
    base_url('assets/main/images/backgrounds/slider-1-1.png'),
    base_url('assets/main/images/blog/blog-2-1.jpg'),
    base_url('assets/main/images/blog/blog-2-2.jpg'),
    base_url('assets/main/images/blog/blog-2-3.jpg'),
    base_url('assets/main/images/blog/blog-2-4.jpg'),
];

// Assemble Hero Slider Items (Layout Agenda, Arena Belajar & Bunga Rampai Style)
$sliderItems = ! empty($sliderItems) ? $sliderItems : [];
if (empty($sliderItems)) {
    $headlineSource = ! empty($popular) ? $popular : array_slice($regulations, 0, 4);
    foreach (array_slice($headlineSource, 0, 4) as $sIdx => $hItem) {
        if (! empty($hItem['cover_file_id'])) {
            $cover = site_url('file/' . (int) $hItem['cover_file_id']);
        } elseif (! empty($hItem['cover_image_path'])) {
            $cover = base_url($hItem['cover_image_path']);
        } else {
            $cover = $bgSliderCovers[$sIdx % count($bgSliderCovers)];
        }
        $sStatus = strtolower((string) ($hItem['legal_status'] ?? 'active'));
        $tagClass = match ($sStatus) {
            'amended', 'diubah'  => 'evt-badge-flag--coming',
            'revoked', 'dicabut' => 'evt-badge-flag--closed',
            default              => 'evt-badge-flag--open',
        };
        $statusTag = match ($sStatus) {
            'amended', 'diubah'  => '<iconify-icon icon="solar:danger-triangle-bold" class="align-middle"></iconify-icon> Diubah',
            'revoked', 'dicabut' => '<iconify-icon icon="solar:close-circle-bold" class="align-middle"></iconify-icon> Dicabut',
            default              => '<iconify-icon icon="solar:check-circle-bold" class="align-middle"></iconify-icon> Berlaku Resmi',
        };
        $sliderItems[] = [
            'id'          => (int) $hItem['id'],
            'title'       => ($hItem['document_number'] ?? 'Regulasi PBJ') . ' &mdash; ' . ($hItem['title'] ?? ''),
            'url'         => site_url('regulasi/' . esc($hItem['slug'] ?? '')),
            'cover_url'   => $cover,
            'tag_class'   => $tagClass,
            'status_tag'  => $statusTag,
            'type_label'  => $hItem['category_name'] ?? 'Regulasi PBJ',
            'badge_extra' => 'Tahun ' . ($hItem['document_year'] ?? date('Y')),
            'meta_1'      => '<iconify-icon icon="solar:buildings-bold" class="text-warning align-middle"></iconify-icon> ' . esc($hItem['issuer_name'] ?? 'Pemerintah'),
            'meta_2'      => '<iconify-icon icon="solar:calendar-date-bold" class="text-info align-middle"></iconify-icon> ' . (! empty($hItem['official_date']) ? date('d M Y', strtotime($hItem['official_date'])) : 'Terbaru'),
            'meta_3'      => '<iconify-icon icon="solar:download-square-bold" class="text-success align-middle"></iconify-icon> ' . number_format((int) ($hItem['download_count'] ?? 0)) . 'x Unduh',
            'action_text' => 'Baca Regulasi',
        ];
    }
}

// Reusable card renderer matching Bunga Rampai arena-card standard (High-Contrast Badges)
$renderArenaRegulationCard = function (array $reg, int $idx = 0) {
    $detailUrl = site_url('regulasi/' . esc($reg['slug']));
    $downloadUrl = site_url('regulasi/' . esc($reg['slug']) . '/unduh');
    $hasFile = ! empty($reg['current_file_id']);
    $fileSizeKb = ! empty($reg['file_size']) ? number_format((float) $reg['file_size'] / 1024, 0) : null;
    if (! empty($reg['cover_file_id'])) {
        $coverImg = site_url('file/' . (int) $reg['cover_file_id']);
    } elseif (! empty($reg['cover_image_path'])) {
        $coverImg = base_url($reg['cover_image_path']);
    } else {
        $coverImg = base_url('assets/main/images/blog/blog-2-' . (($idx % 4) + 1) . '.jpg');
    }
    $sStatus = strtolower((string) ($reg['legal_status'] ?? 'active'));

    $statusPill = match ($sStatus) {
        'amended', 'diubah'  => ['class' => 'reg-thumb-badge--diubah', 'icon' => 'fa-exclamation-circle', 'text' => 'Diubah'],
        'revoked', 'dicabut' => ['class' => 'reg-thumb-badge--dicabut', 'icon' => 'fa-times-circle', 'text' => 'Dicabut'],
        default              => ['class' => 'reg-thumb-badge--berlaku', 'icon' => 'fa-check-circle', 'text' => 'Berlaku'],
    };

    $modalData = [
        'document_number' => $reg['document_number'] ?? '',
        'title'           => $reg['title'] ?? '',
        'category_name'   => $reg['category_name'] ?? 'Regulasi',
        'issuer'          => $reg['issuer_name'] ?? 'Pemerintah',
        'year'            => $reg['document_year'] ?? '-',
        'official_date'   => ! empty($reg['official_date']) ? date('d F Y', strtotime($reg['official_date'])) : '-',
        'effective_date'  => ! empty($reg['effective_date']) ? date('d F Y', strtotime($reg['effective_date'])) : '-',
        'legal_status'    => $reg['legal_status'] ?? 'active',
        'summary'         => $reg['summary'] ?? '',
        'official_source' => $reg['official_source_url'] ?? '',
        'has_file'        => $hasFile,
        'file_size'       => $fileSizeKb ? $fileSizeKb . ' KB' : 'PDF',
        'detail_url'      => $detailUrl,
        'download_url'    => $downloadUrl,
    ];
?>
    <div class="col-md-6">
        <article class="arena-card h-100">
            <div class="arena-card__thumb">
                <img src="<?= esc($coverImg) ?>" alt="<?= esc($reg['document_number'] ?? 'Regulasi PBJ') ?>" loading="lazy" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/blog/blog-2-1.jpg') ?>';">
                <div class="arena-card__badge-row">
                    <span class="reg-thumb-badge <?= esc($statusPill['class']) ?>">
                        <i class="fas <?= esc($statusPill['icon']) ?>"></i> <?= esc($statusPill['text']) ?>
                    </span>
                    <span class="reg-thumb-badge reg-thumb-badge--year">
                        <i class="far fa-calendar-alt"></i> Tahun <?= esc($reg['document_year'] ?? '-') ?>
                    </span>
                </div>
                <div class="arena-card-floating-bookmark">
                    <?= view('landing/components/bookmark_button', [
                        'type'      => 'regulation',
                        'id'        => (int) $reg['id'],
                        'title'     => $reg['title'] ?? null,
                        'iconOnly'  => true,
                        'size'      => 'sm',
                    ]) ?>
                </div>
            </div>
            <div class="arena-card__body">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="arena-card__category mb-0 text-truncate" style="max-width: 170px; color: #0369a1; font-weight: 700;">
                        <i class="fas fa-layer-group me-1"></i> <?= esc($reg['category_name'] ?? 'Regulasi PBJ') ?>
                    </span>
                    <span class="badge border small px-2 py-1" style="font-size: 11px; background: #f8fafc; color: #0f172a; border-color: #cbd5e1; font-weight: 600;">
                        <i class="far fa-calendar-alt me-1 text-primary"></i> <?= ! empty($reg['effective_date']) ? esc(date('d M Y', strtotime($reg['effective_date']))) : (! empty($reg['official_date']) ? esc(date('d M Y', strtotime($reg['official_date']))) : '-') ?>
                    </span>
                </div>
                <h4 class="arena-card__title">
                    <a href="<?= esc($detailUrl) ?>" class="hover-primary" title="<?= esc($reg['document_number'] . ' — ' . $reg['title']) ?>">
                        <?= esc($reg['document_number'] ?? 'Dokumen Tanpa Nomor') ?>
                    </a>
                </h4>
                <p class="arena-card__text">
                    <?= esc(mb_strimwidth($reg['title'] ?? ($reg['summary'] ?? '-'), 0, 95, '…')) ?>
                </p>
                <div class="arena-card__meta">
                    <span class="text-truncate" style="max-width: 140px;" title="<?= esc($reg['issuer_name'] ?? 'Pemerintah') ?>">
                        <i class="fas fa-university text-primary"></i> <?= esc($reg['issuer_name'] ?? 'Pemerintah') ?>
                    </span>
                    <span>
                        <i class="fas fa-download text-success"></i> <?= number_format((int) ($reg['download_count'] ?? 0)) ?>x unduh
                    </span>
                </div>
                <div class="arena-card__actions mt-auto">
                    <a href="<?= esc($detailUrl) ?>" class="arena-btn-primary flex-grow-1 justify-content-center">
                        <span>Baca Regulasi</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-secondary radius-8 px-2 d-inline-flex align-items-center" onclick="openRegulationModal(<?= esc(json_encode($modalData)) ?>)" title="Pratinjau Ringkas">
                        <i class="fas fa-eye"></i>
                    </button>
                    <?php if ($hasFile): ?>
                        <a href="<?= esc($downloadUrl) ?>" class="btn btn-sm btn-outline-success radius-8 px-2 d-inline-flex align-items-center" title="Unduh Berkas PDF">
                            <i class="fas fa-download"></i>
                        </a>
                    <?php endif; ?>
                    <?= view('landing/components/bookmark_button', [
                        'type'  => 'regulation',
                        'id'    => (int) $reg['id'],
                        'title' => $reg['title'] ?? null,
                        'size'  => 'sm',
                    ]) ?>
                </div>
            </div>
        </article>
    </div>
<?php }; ?>

<!-- =========================================================================
     MAIN REGULASI SECTION — Clean Magazine Benchmark (Patokan Bunga Rampai)
     ========================================================================= -->
<section class="berita-main-section evt-main-section pt-2 pt-md-3 pt-lg-4 pb-4" id="kategori-liputan-section" style="background: #fafbfc;">
    <div class="container">

        <!-- Compact Breadcrumb + Header Title & Unified Search -->
        <div class="evt-hero-banner mb-2 mb-md-3">
            <div class="arena-breadcrumb evt-breadcrumb mb-2">
                <a href="<?= site_url('/') ?>"><iconify-icon icon="solar:home-2-bold" class="align-middle"></iconify-icon> Beranda</a>
                <span class="sep">/</span>
                <?php if (! empty($selectedCategoryObj)): ?>
                    <a href="<?= site_url('regulasi') ?>">Regulasi PBJ</a>
                    <span class="sep">/</span>
                    <span class="current"><?= esc($selectedCategoryObj['name']) ?></span>
                <?php else: ?>
                    <span class="current">Regulasi PBJ Lampung</span>
                <?php endif; ?>
            </div>

            <div class="row align-items-center justify-content-between g-3 mb-2">
                <div class="col-lg-7">
                    <h1 class="evt-hero-title mb-2">
                        <?= ! empty($selectedCategoryObj) ? esc($selectedCategoryObj['name']) : 'Pusat Regulasi &amp; Tata Kelola PBJ' ?> <br>
                        <span>Pemerintah Provinsi Lampung</span>
                    </h1>
                    <p class="evt-hero-desc mb-0">
                        <?= ! empty($selectedCategoryObj)
                            ? 'Kompilasi dokumen ' . esc($selectedCategoryObj['name']) . ' terverifikasi, status hukum resmi, riwayat versi, dan naskah digital siap unduh.'
                            : 'Kompilasi peraturan perundang-undangan PBJ, Perpres 12/2021, Peraturan LKPP, Peraturan Daerah &amp; Pergub Lampung, hierarki norma hukum, status keberlakuan resmi, serta dokumen pengadaan yang transparan dan akuntabel.'
                        ?>
                    </p>
                </div>
                <div class="col-lg-5">
                    <!-- Search Form -->
                    <form action="<?= site_url('regulasi') ?>" method="get" class="evt-search-box arena-search-bar ms-lg-auto">
                        <iconify-icon icon="solar:magnifer-linear" class="text-slate-500 text-xl"></iconify-icon>
                        <input type="text" name="q" id="regulationHeroSearch" value="<?= esc($keyword) ?>" placeholder="Cari nomor regulasi, topik, kata kunci..." autocomplete="off">
                        <?php if (! empty($selectedCategory)): ?>
                            <input type="hidden" name="category_id" value="<?= esc($selectedCategory) ?>">
                        <?php endif; ?>
                        <?php if (! empty($selectedStatus)): ?>
                            <input type="hidden" name="status" value="<?= esc($selectedStatus) ?>">
                        <?php endif; ?>
                        <?php if (! empty($selectedYear)): ?>
                            <input type="hidden" name="year" value="<?= esc($selectedYear) ?>">
                        <?php endif; ?>
                        <?php if (! empty($selectedStage)): ?>
                            <input type="hidden" name="stage_id" value="<?= esc($selectedStage) ?>">
                        <?php endif; ?>
                        <?php if ($selectedSort !== 'newest'): ?>
                            <input type="hidden" name="sort" value="<?= esc($selectedSort) ?>">
                        <?php endif; ?>
                        <button type="submit">
                            <span>Cari Regulasi</span>
                            <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                        </button>
                    </form>
                    <?php if (! empty($keyword)): ?>
                        <div class="text-end mt-1">
                            <a href="<?= $buildFilterUrl(['q' => null]) ?>" class="badge border text-decoration-none" style="background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; font-size: 11px; font-weight: 600;">
                                <i class="fas fa-times me-1 text-danger"></i> Reset Pencarian: "<?= esc($keyword) ?>"
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- HERO SLIDER (SEPERTI LAYOUT AGENDA, ARENA BELAJAR & BUNGA RAMPAI) -->
        <?php if (! empty($sliderItems)): ?>
            <div class="evt-headline mb-4">
                <div id="regulationHeadlineCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
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
                                            <span class="evt-badge-pill" style="background: rgba(15, 23, 42, 0.75); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.3);"><?= esc($sItem['type_label']) ?></span>
                                            <?php if (! empty($sItem['badge_extra'])): ?>
                                                <span class="evt-badge-pill evt-badge-pill--cert" style="background: rgba(14, 165, 233, 0.4); color: #ffffff; border-color: rgba(56, 189, 248, 0.6); font-weight: 700;">
                                                    <iconify-icon icon="solar:shield-check-bold" class="align-middle"></iconify-icon> <?= esc($sItem['badge_extra']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="evt-headline__counter">
                                            <span class="dot"></span>
                                            <span class="d-none d-sm-inline">Sorotan Regulasi </span><span>#<?= $sIndex + 1 ?> / <?= count($sliderItems) ?></span>
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
                        <button class="evt-headline__nav prev" type="button" data-bs-target="#regulationHeadlineCarousel" data-bs-slide="prev" aria-label="Sebelumnya">
                            <iconify-icon icon="solar:alt-arrow-left-linear"></iconify-icon>
                        </button>
                        <button class="evt-headline__nav next" type="button" data-bs-target="#regulationHeadlineCarousel" data-bs-slide="next" aria-label="Selanjutnya">
                            <iconify-icon icon="solar:alt-arrow-right-linear"></iconify-icon>
                        </button>
                        <div class="carousel-indicators evt-headline__indicators">
                            <?php foreach ($sliderItems as $sIndex => $sItem): ?>
                                <button type="button" data-bs-target="#regulationHeadlineCarousel" data-bs-slide-to="<?= $sIndex ?>" class="<?= $sIndex === 0 ? 'active' : '' ?>" aria-current="<?= $sIndex === 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $sIndex + 1 ?>"></button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- MAIN 2-COLUMN LAYOUT -->
        <div class="row g-3 g-lg-4">
            <!-- Left Column: Main Regulations Content (col-lg-8) -->
            <div class="col-lg-8">

                <!-- Showcase: Katalog Dokumen Regulasi PBJ / Hasil Pencarian -->
                <div class="mb-4 pb-2">
                    <!-- Filter Toolbar (Ultra-Compact, Sleek & Clean) -->
                    <div class="card border-0 shadow-2xs radius-12 p-2 px-3 mb-3 bg-white border" style="border-color: #e2e8f0 !important;">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <!-- Left: Status Filter Pills -->
                            <div class="d-flex flex-wrap align-items-center gap-1">
                                <span class="small fw-bold text-muted me-1" style="font-size: 12px;"><i class="fas fa-filter text-primary me-1"></i> Status:</span>
                                <a href="<?= $buildFilterUrl(['status' => null, 'page' => 1]) ?>" class="arena-filter-pill <?= empty($selectedStatus) ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>">
                                    Semua
                                </a>
                                <a href="<?= $buildFilterUrl(['status' => 'active', 'page' => 1]) ?>" class="arena-filter-pill <?= $selectedStatus === 'active' ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>" style="<?= $selectedStatus === 'active' ? 'background: #15803d !important; border-color: #166534 !important; color: #fff !important;' : '' ?>">
                                    <i class="fas fa-check-circle <?= $selectedStatus === 'active' ? 'text-white' : 'text-success' ?> me-1"></i> Berlaku
                                </a>
                                <a href="<?= $buildFilterUrl(['status' => 'amended', 'page' => 1]) ?>" class="arena-filter-pill <?= $selectedStatus === 'amended' ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>" style="<?= $selectedStatus === 'amended' ? 'background: #d97706 !important; border-color: #b45309 !important; color: #fff !important;' : '' ?>">
                                    <i class="fas fa-exclamation-circle <?= $selectedStatus === 'amended' ? 'text-white' : 'text-warning' ?> me-1"></i> Diubah
                                </a>
                                <a href="<?= $buildFilterUrl(['status' => 'revoked', 'page' => 1]) ?>" class="arena-filter-pill <?= $selectedStatus === 'revoked' ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>" style="<?= $selectedStatus === 'revoked' ? 'background: #dc2626 !important; border-color: #b91c1c !important; color: #fff !important;' : '' ?>">
                                    <i class="fas fa-times-circle <?= $selectedStatus === 'revoked' ? 'text-white' : 'text-danger' ?> me-1"></i> Dicabut
                                </a>
                            </div>

                            <!-- Right: Compact Dropdown Filters & Counter/Reset -->
                            <div class="d-flex flex-wrap align-items-center gap-2 ms-auto">
                                <!-- Dropdown Tahun -->
                                <div class="reg-select-pill <?= ! empty($selectedYear) ? 'reg-select-pill--active' : '' ?>">
                                    <i class="far fa-calendar-alt reg-select-pill__prefix"></i>
                                    <select class="ignore" onchange="window.location.href = this.value;" aria-label="Filter Tahun">
                                        <option value="<?= $buildFilterUrl(['year' => null, 'page' => 1]) ?>">Semua Tahun</option>
                                        <?php foreach ($years as $yr): ?>
                                            <option value="<?= $buildFilterUrl(['year' => $yr, 'page' => 1]) ?>" <?= (int) $selectedYear === (int) $yr ? 'selected' : '' ?>>
                                                Tahun <?= (int) $yr ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <i class="fas fa-chevron-down reg-select-pill__suffix"></i>
                                </div>

                                <!-- Dropdown Tahap Siklus PBJ -->
                                <div class="reg-select-pill <?= ! empty($selectedStage) ? 'reg-select-pill--active' : '' ?>">
                                    <i class="fas fa-project-diagram reg-select-pill__prefix"></i>
                                    <select class="ignore" onchange="window.location.href = this.value;" aria-label="Filter Tahap Pengadaan">
                                        <option value="<?= $buildFilterUrl(['stage_id' => null, 'page' => 1]) ?>">Semua Tahap</option>
                                        <?php foreach ($stages as $st):
                                            if (str_starts_with(strtoupper($st['name']), 'QA AUDIT') || str_starts_with(strtoupper($st['name']), 'TEST')) {
                                                continue;
                                            }
                                        ?>
                                            <option value="<?= $buildFilterUrl(['stage_id' => (int) $st['id'], 'page' => 1]) ?>" <?= (int) $selectedStage === (int) $st['id'] ? 'selected' : '' ?>>
                                                <?= esc($st['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <i class="fas fa-chevron-down reg-select-pill__suffix"></i>
                                </div>

                                <?php if (! empty($selectedStatus) || ! empty($selectedYear) || ! empty($selectedStage) || ! empty($keyword)): ?>
                                    <a href="<?= site_url('regulasi' . (! empty($selectedCategory) ? '?category_id=' . esc($selectedCategory) : '')) ?>" class="btn btn-sm btn-outline-danger radius-20 px-2 py-0 fw-semibold" style="font-size: 11px; height: 28px; line-height: 26px;" title="Reset Semua Filter">
                                        <i class="fas fa-times me-1"></i> Reset
                                    </a>
                                <?php endif; ?>

                                <span class="badge radius-20 px-2 py-1 fw-bold text-muted border bg-light d-none d-sm-inline-flex" style="font-size: 11px;">
                                    <?= $totalRegulations ?> Dokumen
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Regulations Grid (Using arena-card Component) -->
                    <?php if (empty($regulations)): ?>
                        <!-- Friendly Empty State -->
                        <div class="card border-0 shadow-2xs radius-16 p-5 text-center bg-white my-4" style="border: 1px solid #e2e8f0 !important;">
                            <div class="mb-3 text-muted">
                                <i class="fas fa-folder-open text-primary text-opacity-50" style="font-size: 64px;"></i>
                            </div>
                            <h4 class="fw-bold mb-2" style="color: #052143;">Regulasi Tidak Ditemukan</h4>
                            <p class="mx-auto mb-4" style="color: #475569; max-width: 500px;">
                                Tidak ada dokumen regulasi yang sesuai dengan kriteria filter atau pencarian Anda. Silakan ubah kata kunci atau setel ulang filter.
                            </p>
                            <div>
                                <a href="<?= site_url('regulasi') ?>" class="btn btn-primary radius-8 px-4 py-2 fw-semibold">
                                    <i class="fas fa-undo me-1"></i> Reset Semua Filter
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($regulations as $rIdx => $reg): ?>
                                <?php $renderArenaRegulationCard($reg, $rIdx); ?>
                            <?php endforeach; ?>
                        </div>

                        <!-- Smart Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <div class="mt-4 d-flex justify-content-center">
                                <?php
                                $pageParams = [];
                                if (! empty($keyword)) $pageParams['q'] = $keyword;
                                if (! empty($selectedCategory)) $pageParams['category_id'] = $selectedCategory;
                                if (! empty($selectedStatus)) $pageParams['status'] = $selectedStatus;
                                if (! empty($selectedYear)) $pageParams['year'] = $selectedYear;
                                if (! empty($selectedStage)) $pageParams['stage_id'] = $selectedStage;
                                if ($selectedSort !== 'newest') $pageParams['sort'] = $selectedSort;
                                $pageQuery = http_build_query($pageParams);
                                $paginationBase = site_url('regulasi') . ($pageQuery ? '?' . $pageQuery : '');
                                ?>
                                <?= view('landing/components/pagination', [
                                    'currentPage' => $currentPage,
                                    'totalPages'  => $totalPages,
                                    'baseUrl'     => $paginationBase,
                                ]) ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Showcase 3: HIERARKI TATA KELOLA HUKUM PBJ & RELASI KETERKAITAN -->
                <div id="hierarki-regulasi" class="mb-4 pb-2 pt-3 border-top">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="arena-section-head mb-0">
                            <span class="arena-section-head__tag text-danger" style="background: #fde8e8;">
                                <i class="fas fa-sitemap me-1"></i> Arsitektur Tata Norma PBJ
                            </span>
                            <h3 class="arena-section-head__title" style="font-size: 22px;">Hierarki Regulasi Pengadaan Nasional &amp; Daerah</h3>
                            <p class="arena-section-head__desc mb-0" style="font-size: 14.5px;">
                                Tata urutan peraturan perundang-undangan PBJ berjenjang dari Undang-Undang, Perpres Payung PBJ, Peraturan LKPP, hingga Peraturan Gubernur Lampung.
                            </p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Tier 1: UU -->
                        <div class="col-md-6">
                            <div class="norm-tier-card d-flex flex-column h-100 p-3 radius-12 bg-white" style="border-left: 4px solid #dc2626 !important; border: 1px solid #e2e8f0;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge radius-6 px-2 py-1 fw-bold" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; font-size: 11px;">
                                        Tier 1 &middot; UU RI
                                    </span>
                                    <i class="fas fa-gavel text-danger fs-5"></i>
                                </div>
                                <h5 class="fw-bold mb-1" style="font-size: 14.5px; color: #052143;">Undang-Undang Republik Indonesia</h5>
                                <p class="mb-2" style="font-size: 12.5px; line-height: 1.55; color: #475569;">
                                    Landasan konstitusional perbendaharaan negara, anti korupsi, dan cipta kerja pengelolaan belanja negara.
                                </p>
                                <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between">
                                    <small class="text-primary fw-semibold" style="font-size: 11.5px;">UU No. 1/2004, UU No. 6/2023</small>
                                    <a href="<?= site_url('regulasi?q=Undang-Undang') ?>" class="text-danger fw-bold text-decoration-none" style="font-size: 12px;">Lihat Regulasi &rarr;</a>
                                </div>
                            </div>
                        </div>

                        <!-- Tier 2: Perpres -->
                        <div class="col-md-6">
                            <div class="norm-tier-card d-flex flex-column h-100 p-3 radius-12 bg-white" style="border-left: 4px solid #0c5aa6 !important; border: 1px solid #e2e8f0;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge radius-6 px-2 py-1 fw-bold" style="background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; font-size: 11px;">
                                        Tier 2 &middot; Perpres Inti
                                    </span>
                                    <i class="fas fa-crown text-primary fs-5"></i>
                                </div>
                                <h5 class="fw-bold mb-1" style="font-size: 14.5px; color: #052143;">Peraturan Presiden Pengadaan</h5>
                                <p class="mb-2" style="font-size: 12.5px; line-height: 1.55; color: #475569;">
                                    Regulasi payung utama PBJ Pemerintah yang mengatur para pihak, kewenangan PPK/Pokja, dan kontrak.
                                </p>
                                <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between">
                                    <small class="text-primary fw-semibold" style="font-size: 11.5px;">Perpres 16/2018 jo 12/2021</small>
                                    <a href="<?= site_url('regulasi?q=Perpres') ?>" class="text-primary fw-bold text-decoration-none" style="font-size: 12px;">Lihat Regulasi &rarr;</a>
                                </div>
                            </div>
                        </div>

                        <!-- Tier 3: PerLKPP -->
                        <div class="col-md-6">
                            <div class="norm-tier-card d-flex flex-column h-100 p-3 radius-12 bg-white" style="border-left: 4px solid #0284c7 !important; border: 1px solid #e2e8f0;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge radius-6 px-2 py-1 fw-bold" style="background: #e0f2fe; color: #0369a1; border: 1px solid #7dd3fc; font-size: 11px;">
                                        Tier 3 &middot; PerLKPP
                                    </span>
                                    <i class="fas fa-landmark text-info fs-5"></i>
                                </div>
                                <h5 class="fw-bold mb-1" style="font-size: 14.5px; color: #052143;">Peraturan Lembaga (LKPP)</h5>
                                <p class="mb-2" style="font-size: 12.5px; line-height: 1.55; color: #475569;">
                                    Ketentuan pelaksanaan teknis: pengadaan penyedia, swakelola, e-katalog, dan perencanaan.
                                </p>
                                <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between">
                                    <small class="text-primary fw-semibold" style="font-size: 11.5px;">PerLKPP 12/2021, 9/2021, 3/2021</small>
                                    <a href="<?= site_url('regulasi?q=LKPP') ?>" class="text-info fw-bold text-decoration-none" style="font-size: 12px;">Lihat Regulasi &rarr;</a>
                                </div>
                            </div>
                        </div>

                        <!-- Tier 4: Pergub Lampung -->
                        <div class="col-md-6">
                            <div class="norm-tier-card d-flex flex-column h-100 p-3 radius-12 bg-white" style="border-left: 4px solid #16a34a !important; border: 1px solid #e2e8f0;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge radius-6 px-2 py-1 fw-bold" style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; font-size: 11px;">
                                        Tier 4 &middot; Pergub Lampung
                                    </span>
                                    <i class="fas fa-map-marked-alt text-success fs-5"></i>
                                </div>
                                <h5 class="fw-bold mb-1" style="font-size: 14.5px; color: #052143;">Peraturan Gubernur Lampung</h5>
                                <p class="mb-2" style="font-size: 12.5px; line-height: 1.55; color: #475569;">
                                    Implementasi lokal Pemprov Lampung: e-Katalog Lokal, Bela Pengadaan, dan afirmasi UMKM.
                                </p>
                                <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between">
                                    <small class="text-success fw-semibold" style="font-size: 11.5px;">Pergub Lampung No. 35/2022</small>
                                    <a href="<?= site_url('regulasi?q=Lampung') ?>" class="text-success fw-bold text-decoration-none" style="font-size: 12px;">Lihat Regulasi &rarr;</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Showcase 4: TAHAPAN SIKLUS PBJ -->
                <?php if (! empty($stages)): ?>
                    <div class="mb-4 pb-2 pt-3 border-top">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="arena-section-head mb-0">
                                <span class="arena-section-head__tag text-info" style="background: #e0f2fe;">
                                    <i class="fas fa-project-diagram me-1"></i> Siklus Pengadaan PBJ
                                </span>
                                <h3 class="arena-section-head__title" style="font-size: 22px;">Regulasi Berdasarkan Tahapan PBJ</h3>
                                <p class="arena-section-head__desc mb-0" style="font-size: 14.5px;">
                                    Rujukan hukum yang mengatur pelaksanaan setiap fase siklus pengadaan barang/jasa pemerintah.
                                </p>
                            </div>
                        </div>

                        <div class="row g-2">
                            <?php foreach ($stages as $stg):
                                if (str_starts_with(strtoupper($stg['name']), 'QA AUDIT') || str_starts_with(strtoupper($stg['name']), 'TEST')) {
                                    continue;
                                }
                                $stgId = (int) $stg['id'];
                                $isStgActive = (int) $selectedStage === $stgId;
                                $stgIcon = match (true) {
                                    str_contains(strtolower($stg['name']), 'perencanaan') => 'fa-clipboard-list',
                                    str_contains(strtolower($stg['name']), 'persiapan')   => 'fa-calculator',
                                    str_contains(strtolower($stg['name']), 'pemilihan')   => 'fa-users-cog',
                                    str_contains(strtolower($stg['name']), 'kontrak')     => 'fa-file-contract',
                                    str_contains(strtolower($stg['name']), 'serah')       => 'fa-handshake',
                                    default => 'fa-check-circle',
                                };
                            ?>
                                <div class="col-6 col-sm-4 col-md">
                                    <a href="<?= $buildFilterUrl(['stage_id' => $isStgActive ? null : $stgId, 'page' => 1]) ?>" class="text-decoration-none d-block h-100">
                                        <div class="card h-100 border-0 shadow-2xs radius-12 text-center p-2 transition-hover <?= $isStgActive ? 'bg-primary text-white' : 'bg-white' ?>" style="border: 1px solid #e2e8f0 !important;">
                                            <div class="mx-auto mb-1 rounded-circle d-flex align-items-center justify-content-center <?= $isStgActive ? 'bg-white text-primary' : '' ?>" style="width: 38px; height: 38px; <?= ! $isStgActive ? 'background: #eff6ff; color: #1d4ed8;' : '' ?>">
                                                <i class="fas <?= $stgIcon ?>" style="font-size: 14px;"></i>
                                            </div>
                                            <h6 class="fw-bold mb-0 <?= $isStgActive ? 'text-white' : '' ?>" style="<?= ! $isStgActive ? 'color: #052143;' : '' ?> font-size: 12px; line-height: 1.35;">
                                                <?= esc($stg['name']) ?>
                                            </h6>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Right Column: Sidebar (col-lg-4) — Modeled Cleanly after Arena Belajar -->
            <div class="col-lg-4">

                <!-- Sidebar Widget 1: Pencarian di Regulasi PBJ (Sesuai Benchmark Arena Belajar) -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-search"></i>
                        <span>Cari di Regulasi PBJ</span>
                    </h4>
                    <form action="<?= site_url('regulasi') ?>" method="get" class="berita-searchbox news-search-box">
                        <input type="text" name="q" placeholder="Cari nomor, judul, kata kunci..." value="<?= esc($keyword) ?>">
                        <button type="submit" aria-label="Cari"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <!-- Sidebar Widget 2: Kategori Regulasi PBJ (Nav List Sesuai Arena Belajar) -->
                <?php if (! empty($categories)): ?>
                    <div class="berita-widget">
                        <h4 class="berita-widget__title">
                            <i class="fas fa-folder-open" style="background: #fef3c7; color: #d97706;"></i>
                            <span>Kategori Regulasi PBJ</span>
                        </h4>
                        <nav class="berita-catlist" aria-label="Kategori Regulasi">
                            <a href="<?= site_url('regulasi') ?>" class="<?= empty($selectedCategory) ? 'is-active' : '' ?>">
                                <span class="left"><span class="dot" style="background:#0284c7"></span>Semua Regulasi</span>
                                <span class="count"><?= (int) ($stats['total'] ?? 0) ?></span>
                            </a>
                            <?php foreach ($categories as $catItem):
                                $cSlug = $catItem['slug'] ?? '';
                                $isCActive = (int) $selectedCategory === (int) $catItem['id'];
                                if ((str_starts_with(strtoupper($catItem['name']), 'QA AUDIT') || str_starts_with(strtoupper($catItem['name']), 'TEST')) && ! $isCActive) {
                                    continue;
                                }
                            ?>
                                <a href="<?= site_url('regulasi/kategori/' . esc($cSlug)) ?>" class="<?= $isCActive ? 'is-active' : '' ?>">
                                    <span class="left"><span class="dot" style="background:#0c5aa6"></span><?= esc($catItem['name']) ?></span>
                                    <span class="count"><?= (int) ($catItem['total_regulations'] ?? 0) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </nav>
                    </div>
                <?php endif; ?>

                <!-- Sidebar Widget 3: Statistik Regulasi PBJ (Clean List Sesuai Arena Belajar Widget 3) -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-chart-pie" style="background: #e0f2fe; color: #0284c7;"></i>
                        <span>Statistik Regulasi PBJ</span>
                    </h4>
                    <div class="d-flex flex-column gap-2">
                        <a href="<?= site_url('regulasi') ?>" class="text-decoration-none p-2 radius-8 border-start border-3 border-primary bg-light d-flex align-items-center justify-content-between hover-bg-light">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary text-white radius-12" style="font-size: 11px;"><i class="fas fa-book"></i></span>
                                <strong style="font-size: 13px; color: #0f172a;">Total Regulasi</strong>
                            </div>
                            <span class="badge bg-white text-dark border small fw-bold" style="font-size: 12px;"><?= number_format((int) ($stats['total'] ?? 0)) ?></span>
                        </a>
                        <a href="<?= site_url('regulasi?status=active') ?>" class="text-decoration-none p-2 radius-8 border-start border-3 border-success bg-light d-flex align-items-center justify-content-between hover-bg-light">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success text-white radius-12" style="font-size: 11px;"><i class="fas fa-check-circle"></i></span>
                                <strong style="font-size: 13px; color: #0f172a;">Regulasi Masih Berlaku</strong>
                            </div>
                            <span class="badge bg-white text-success border small fw-bold" style="font-size: 12px;"><?= number_format((int) ($stats['active_count'] ?? $stats['published'] ?? 0)) ?></span>
                        </a>
                        <a href="#hierarki-regulasi" class="text-decoration-none p-2 radius-8 border-start border-3 border-warning bg-light d-flex align-items-center justify-content-between hover-bg-light">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-warning text-dark radius-12" style="font-size: 11px;"><i class="fas fa-layer-group"></i></span>
                                <strong style="font-size: 13px; color: #0f172a;">Kategori &amp; Tingkatan</strong>
                            </div>
                            <span class="badge bg-white text-dark border small fw-bold" style="font-size: 12px;"><?= number_format((int) ($stats['categories'] ?? count($categories))) ?></span>
                        </a>
                        <a href="<?= site_url('regulasi?sort=downloads') ?>" class="text-decoration-none p-2 radius-8 border-start border-3 bg-light d-flex align-items-center justify-content-between hover-bg-light" style="border-left-color: #9333ea !important;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-purple text-white radius-12" style="font-size: 11px;"><i class="fas fa-download"></i></span>
                                <strong style="font-size: 13px; color: #0f172a;">Total Unduhan Dokumen</strong>
                            </div>
                            <span class="badge bg-white text-dark border small fw-bold" style="font-size: 12px;"><?= number_format((int) ($stats['downloaded'] ?? 0)) ?>x</span>
                        </a>
                    </div>
                </div>

                <!-- Sidebar Widget 4: Regulasi Paling Populer (Rankings 1 to 3 Sesuai Arena Belajar) -->
                <?php if (! empty($popular)): ?>
                    <div class="berita-widget">
                        <h4 class="berita-widget__title">
                            <i class="fas fa-fire" style="background: #fee2e2; color: #dc2626;"></i>
                            <span>Regulasi Paling Populer</span>
                        </h4>
                        <?php foreach (array_slice($popular, 0, 3) as $pRank => $pItem): ?>
                            <div class="berita-trend">
                                <div class="berita-trend__rank is-top" style="<?= $pRank === 0 ? 'background: #fee2e2; color: #dc2626;' : '' ?>">
                                    <?= $pRank + 1 ?>
                                </div>
                                <div class="berita-trend__body">
                                    <h5 class="berita-trend__title">
                                        <a href="<?= site_url('regulasi/' . esc($pItem['slug'])) ?>">
                                            <?= esc($pItem['document_number'] ?? 'Dokumen Regulasi') ?>
                                        </a>
                                    </h5>
                                    <div class="berita-trend__meta">
                                        <span><i class="fas fa-calendar-alt text-primary me-1"></i> Tahun <?= esc($pItem['document_year'] ?? '-') ?></span>
                                        <span>&middot;</span>
                                        <span class="text-success fw-semibold"><i class="fas fa-download me-1"></i> <?= number_format((int) ($pItem['download_count'] ?? 0)) ?>x unduh</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="<?= site_url('regulasi?sort=downloads') ?>" class="btn btn-sm btn-outline-primary w-100 radius-8 fw-semibold mt-3" style="font-size: 12.5px;">
                            Lihat Seluruh Peringkat &rarr;
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Sidebar Widget 5: Kepastian Hukum PBJ (Pilar Tata Kelola Sesuai Arena Belajar) -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-shield-alt" style="background: #dcfce7; color: #16a34a;"></i>
                        <span>Kepastian Hukum PBJ</span>
                    </h4>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge radius-6 px-2 py-1" style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; font-size: 11px; font-weight: 700;">JDIH Terpadu</span>
                        <span class="fw-bold text-dark" style="font-size: 13.5px;">Tata Kelola Akuntabel &amp; Taat Asas</span>
                    </div>
                    <div class="d-flex flex-column gap-2 pt-1">
                        <div class="d-flex align-items-start gap-2 p-2 radius-8 bg-light border">
                            <i class="fas fa-check-circle text-primary mt-1" style="font-size: 13px;"></i>
                            <div>
                                <strong class="d-block text-dark" style="font-size: 12.5px;">Standar LKPP RI &amp; Perpres PBJ</strong>
                                <small style="color: #475569; font-size: 12px; line-height: 1.45;">Seluruh dokumen diverifikasi selaras regulasi kepengadaan nasional terkini.</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-2 p-2 radius-8 bg-light border">
                            <i class="fas fa-check-circle text-success mt-1" style="font-size: 13px;"></i>
                            <div>
                                <strong class="d-block text-dark" style="font-size: 12.5px;">Status Keberlakuan Resmi</strong>
                                <small style="color: #475569; font-size: 12px; line-height: 1.45;">Transparansi status apakah suatu regulasi masih berlaku, diubah, atau dicabut.</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-2 p-2 radius-8 bg-light border">
                            <i class="fas fa-check-circle text-info mt-1" style="font-size: 13px;"></i>
                            <div>
                                <strong class="d-block text-dark" style="font-size: 12.5px;">Mitigasi Risiko Audit</strong>
                                <small style="color: #475569; font-size: 12px; line-height: 1.45;">Rujukan legal pencegah temuan BPK, BPKP, dan Inspektorat Daerah.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Widget 6: Layanan Advokasi Biro PBJ Lampung (Sesuai Arena Belajar) -->
                <div class="berita-widget p-4 text-white" style="background: linear-gradient(135deg, #052244 0%, #0a3d74 100%) !important; border-radius: 16px;">
                    <span class="badge bg-warning text-dark text-xs fw-bold px-2 py-1 mb-2">Biro PBJ Setda Lampung</span>
                    <h5 class="fw-bold text-white mb-2" style="font-size: 16px;">Bantuan &amp; Advokasi PBJ</h5>
                    <p class="text-white-80 mb-3" style="font-size: 13px; line-height: 1.6; color: rgba(255,255,255,0.85);">
                        Memiliki kendala interpretasi pasal atau telaah regulasi teknis? Tim pembina PBJ Provinsi Lampung siap membantu Anda.
                    </p>
                    <div class="text-warning fw-semibold d-flex align-items-center gap-2 mb-3" style="font-size: 13px;">
                        <i class="fas fa-phone-alt"></i>
                        <span>Helpdesk PBJ: +62 851-1777-9783</span>
                    </div>
                    <a href="<?= site_url('arena-belajar') ?>" class="btn btn-sm btn-outline-light w-100 radius-8 fw-semibold mb-2" style="font-size: 12.5px;">
                        <i class="fas fa-graduation-cap me-1"></i> Akses Arena Belajar PBJ
                    </a>
                    <a href="<?= site_url('bunga-rampai') ?>" class="btn btn-sm btn-warning text-dark w-100 radius-8 fw-bold" style="font-size: 12.5px;">
                        <i class="fas fa-book-reader me-1"></i> Bunga Rampai Pengetahuan
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
            <i class="fas fa-balance-scale me-1"></i> Advokasi &amp; Kepastian Hukum PBJ
        </span>
        <h2 class="text-white mb-2" style="font-size: 26px; font-weight: 800; line-height: 1.3;">
            Butuh Konsultasi Penerapan Regulasi Pengadaan?
        </h2>
        <p class="mx-auto mb-3 text-white-80" style="max-width: 620px; font-size: 14.5px; line-height: 1.65; color: rgba(255,255,255,0.85);">
            Biro Pengadaan Barang &amp; Jasa Sekretariat Daerah Provinsi Lampung menyediakan bimbingan teknis peraturan perundang-undangan dan telaah hukum pengadaan bagi seluruh Perangkat Daerah dan Penyedia.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href="<?= site_url('regulasi') ?>" class="btn btn-warning text-dark fw-bold px-3 py-2 radius-8 shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 13.5px;">
                <i class="fas fa-search"></i> Telusuri Seluruh Regulasi
            </a>
            <a href="<?= site_url('arena-belajar') ?>" class="btn btn-outline-light radius-8 px-3 py-2 d-inline-flex align-items-center gap-2 fw-semibold" style="font-size: 13.5px;">
                <i class="fas fa-graduation-cap"></i> Ikuti Arena Belajar
            </a>
        </div>
    </div>
</section>

<!-- =========================================================================
     MODAL QUICK PREVIEW DOKUMEN REGULASI
     ========================================================================= -->
<div class="modal fade" id="regulationQuickPreviewModal" tabindex="-1" aria-labelledby="regulationQuickPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-16 border-0 shadow-lg overflow-hidden">
            <div class="modal-header text-white p-3 px-4 border-0" style="background-color: var(--begawi-dark) !important;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-balance-scale text-warning fs-5"></i>
                    <h5 class="modal-title fw-bold text-white mb-0" id="regulationQuickPreviewModalLabel">Pratinjau Ringkas Regulasi</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background-color: #ffffff;">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span id="modalStatusBadge" class="badge radius-6 px-2 py-1 small fw-bold">Status</span>
                    <span id="modalCategoryBadge" class="badge radius-6 px-2 py-1 small fw-semibold" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">Kategori</span>
                </div>

                <div class="mb-3">
                    <h4 class="h5 fw-bold mb-1" style="color: #052143;" id="modalDocumentNumber">Nomor Dokumen</h4>
                    <p class="mb-0 small" style="color: #475569;" id="modalTitle">Tentang Regulasi</p>
                </div>

                <div class="row g-2 p-3 radius-12 mb-3 small" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="col-md-6">
                        <span style="color: #64748b;">Penerbit:</span> <strong id="modalIssuer" style="color: #0f172a;">-</strong>
                    </div>
                    <div class="col-md-6">
                        <span style="color: #64748b;">Tahun:</span> <strong id="modalYear" style="color: #0f172a;">-</strong>
                    </div>
                    <div class="col-md-6">
                        <span style="color: #64748b;">Tanggal Resmi:</span> <strong id="modalOfficialDate" style="color: #0f172a;">-</strong>
                    </div>
                    <div class="col-md-6">
                        <span style="color: #64748b;">Tanggal Berlaku:</span> <strong id="modalEffectiveDate" style="color: #0f172a;">-</strong>
                    </div>
                </div>

                <h6 class="fw-bold mb-2 small text-uppercase" style="color: #475569;">Ringkasan Materi Pokok:</h6>
                <div class="p-3 radius-12 small mb-3" style="background: #ffffff; border: 1px solid #cbd5e1; color: #1e293b; max-height: 200px; overflow-y: auto; line-height: 1.7;" id="modalSummary">
                    Ringkasan dokumen regulasi...
                </div>

                <div id="modalOfficialSourceBox" class="mb-2" style="display: none;">
                    <a href="#" id="modalOfficialSourceLink" target="_blank" rel="noopener noreferrer" class="small text-decoration-none text-primary fw-semibold d-inline-flex align-items-center gap-1">
                        <i class="fas fa-external-link-alt"></i> Buka Tautan Resmi JDIH / LKPP
                    </a>
                </div>
            </div>
            <div class="modal-footer p-3 px-4 d-flex justify-content-between" style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
                <button type="button" class="btn btn-light border radius-8 px-4 fw-semibold text-secondary" data-bs-dismiss="modal">Tutup</button>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-secondary radius-8 px-3 fw-semibold" onclick="copyRegulationModalUrl()">
                        <i class="fas fa-share-alt me-1"></i> Salin Tautan
                    </button>
                    <a href="#" id="modalDetailBtn" class="btn btn-outline-primary radius-8 px-3 fw-semibold">
                        Halaman Detail Lengkap
                    </a>
                    <a href="#" id="modalDownloadBtn" class="btn btn-primary radius-8 px-4 fw-semibold d-inline-flex align-items-center gap-1" style="background-color: var(--begawi-primary); border-color: var(--begawi-primary);">
                        <i class="fas fa-download"></i> Unduh Dokumen (<span id="modalFileSize">PDF</span>)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentModalDetailUrl = '';

    function openRegulationModal(data) {
        if (!data) return;
        currentModalDetailUrl = data.detail_url || window.location.href;

        document.getElementById('modalDocumentNumber').textContent = data.document_number || '-';
        document.getElementById('modalTitle').textContent = data.title || '-';
        document.getElementById('modalCategoryBadge').textContent = data.category_name || 'Regulasi';
        document.getElementById('modalIssuer').textContent = data.issuer || 'Pemerintah';
        document.getElementById('modalYear').textContent = data.year || '-';
        document.getElementById('modalOfficialDate').textContent = data.official_date || '-';
        document.getElementById('modalEffectiveDate').textContent = data.effective_date || '-';
        document.getElementById('modalSummary').textContent = data.summary || 'Tidak ada ringkasan materi.';
        document.getElementById('modalFileSize').textContent = data.file_size || 'PDF';

        // Status Badge (High Contrast Soft Badges)
        const stBadge = document.getElementById('modalStatusBadge');
        const status = (data.legal_status || '').toLowerCase();
        if (status === 'active' || status === 'berlaku') {
            stBadge.className = 'badge radius-6 px-2 py-1 small fw-bold';
            stBadge.style.cssText = 'background: #dcfce7; color: #15803d; border: 1px solid #86efac;';
            stBadge.textContent = 'Berlaku';
        } else if (status === 'amended' || status === 'diubah') {
            stBadge.className = 'badge radius-6 px-2 py-1 small fw-bold';
            stBadge.style.cssText = 'background: #fef3c7; color: #b45309; border: 1px solid #fde047;';
            stBadge.textContent = 'Diubah';
        } else if (status === 'revoked' || status === 'dicabut') {
            stBadge.className = 'badge radius-6 px-2 py-1 small fw-bold';
            stBadge.style.cssText = 'background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5;';
            stBadge.textContent = 'Dicabut';
        } else {
            stBadge.className = 'badge radius-6 px-2 py-1 small fw-bold';
            stBadge.style.cssText = 'background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;';
            stBadge.textContent = status || 'Resmi';
        }

        // Source link
        const srcBox = document.getElementById('modalOfficialSourceBox');
        const srcLink = document.getElementById('modalOfficialSourceLink');
        if (data.official_source) {
            srcLink.href = data.official_source;
            srcBox.style.display = 'block';
        } else {
            srcBox.style.display = 'none';
        }

        // Action buttons
        document.getElementById('modalDetailBtn').href = data.detail_url || '#';
        const dlBtn = document.getElementById('modalDownloadBtn');
        if (data.has_file && data.download_url) {
            dlBtn.href = data.download_url;
            dlBtn.style.display = 'inline-flex';
        } else {
            dlBtn.style.display = 'none';
        }

        const modalEl = document.getElementById('regulationQuickPreviewModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            try {
                const modal = (typeof bootstrap.Modal.getInstance === 'function' ? bootstrap.Modal.getInstance(modalEl) : null) || new bootstrap.Modal(modalEl);
                modal.show();
            } catch (e) {
                if (window.jQuery && typeof jQuery(modalEl).modal === 'function') {
                    jQuery(modalEl).modal('show');
                }
            }
        }
    }

    function copyRegulationModalUrl() {
        if (currentModalDetailUrl && navigator.clipboard) {
            navigator.clipboard.writeText(currentModalDetailUrl).then(() => {
                alert('Tautan dokumen regulasi berhasil disalin ke clipboard!');
            });
        } else {
            alert('Tautan berhasil disalin!');
        }
    }

    function applyRegulationQuickSearch(keyword) {
        const input = document.getElementById('regulationHeroSearch');
        if (input) {
            input.value = keyword;
            input.closest('form').submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (window.jQuery && typeof window.jQuery.fn.niceSelect === 'function') {
            window.jQuery('.reg-select-pill select').niceSelect('destroy');
        }

        // Initialize Carousel safely and synchronize indicator dots
        const carouselEl = document.getElementById('regulationHeadlineCarousel');
        if (carouselEl) {
            let myCarousel = null;
            if (typeof bootstrap !== 'undefined' && typeof bootstrap.Carousel !== 'undefined') {
                try {
                    myCarousel = (typeof bootstrap.Carousel.getInstance === 'function' ? bootstrap.Carousel.getInstance(carouselEl) : null) || new bootstrap.Carousel(carouselEl, {
                        interval: 6000,
                        ride: 'carousel',
                        wrap: true,
                        pause: 'hover'
                    });
                } catch (err) {}
            }

            const indicatorButtons = carouselEl.querySelectorAll('.evt-headline__indicators button, .carousel-indicators button');

            function syncIndicatorDots(targetIdx) {
                if (!indicatorButtons || indicatorButtons.length === 0) return;
                indicatorButtons.forEach(function (btn, idx) {
                    if (idx === targetIdx) {
                        btn.classList.add('active');
                        btn.setAttribute('aria-current', 'true');
                    } else {
                        btn.classList.remove('active');
                        btn.removeAttribute('aria-current');
                    }
                });
            }

            carouselEl.addEventListener('slide.bs.carousel', function (e) {
                if (typeof e.to !== 'undefined') {
                    syncIndicatorDots(e.to);
                }
            });

            carouselEl.addEventListener('slid.bs.carousel', function (e) {
                if (typeof e.to !== 'undefined') {
                    syncIndicatorDots(e.to);
                }
            });

            indicatorButtons.forEach(function (btn, idx) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (myCarousel && typeof myCarousel.to === 'function') {
                        myCarousel.to(idx);
                    } else if (window.jQuery && typeof jQuery(carouselEl).carousel === 'function') {
                        jQuery(carouselEl).carousel(idx);
                    }
                    syncIndicatorDots(idx);
                });
            });
        }
    });
</script>

<?= $this->endSection() ?>