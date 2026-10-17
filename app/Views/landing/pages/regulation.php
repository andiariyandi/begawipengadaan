<?= $this->extend('landing/layouts/public') ?>

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

// Helper legal status badge (100% High Contrast Soft Badges)
$renderLegalStatusBadge = function (?string $status): string {
    $status = strtolower((string) $status);
    return match ($status) {
        'active', 'berlaku' => '<span class="badge badge-soft-success px-2 py-1 small radius-6 d-inline-flex align-items-center gap-1"><i class="fas fa-check-circle"></i> Berlaku</span>',
        'amended', 'diubah'  => '<span class="badge badge-soft-warning px-2 py-1 small radius-6 d-inline-flex align-items-center gap-1"><i class="fas fa-exclamation-circle"></i> Diubah</span>',
        'revoked', 'dicabut' => '<span class="badge badge-soft-danger px-2 py-1 small radius-6 d-inline-flex align-items-center gap-1"><i class="fas fa-times-circle"></i> Dicabut</span>',
        'expired', 'kedaluwarsa' => '<span class="badge badge-soft-secondary px-2 py-1 small radius-6 d-inline-flex align-items-center gap-1"><i class="fas fa-clock"></i> Kedaluwarsa</span>',
        default => '<span class="badge badge-soft-primary px-2 py-1 small radius-6 d-inline-flex align-items-center gap-1"><i class="fas fa-info-circle"></i> ' . esc(ucfirst($status ?: 'Resmi')) . '</span>',
    };
};
?>

<style>
    /* =========================================================================
   REGULASI PBJ LANDING DESIGN DNA & HIGH-CONTRAST ACCESSIBILITY
   ========================================================================= */
    :root {
        --begawi-primary: #0c5aa6;
        --begawi-dark: #082f5c;
        --begawi-accent: #f3a20b;
    }

    /* =========================================================================
   HIGH CONTRAST SOFT BADGES (Fix for Bootstrap 5.0 missing bg-opacity)
   All badges have explicit dark text on soft pastel background for 100% readability
   ========================================================================= */
    .badge-soft-success {
        background-color: #dcfce7 !important;
        color: #15803d !important;
        border: 1px solid #bbf7d0 !important;
        font-weight: 600;
    }

    .badge-soft-warning {
        background-color: #fef3c7 !important;
        color: #b45309 !important;
        border: 1px solid #fde68a !important;
        font-weight: 600;
    }

    .badge-soft-danger {
        background-color: #fee2e2 !important;
        color: #b91c1c !important;
        border: 1px solid #fecaca !important;
        font-weight: 600;
    }

    .badge-soft-info {
        background-color: #e0f2fe !important;
        color: #0369a1 !important;
        border: 1px solid #bae6fd !important;
        font-weight: 600;
    }

    .badge-soft-primary {
        background-color: #e0e7ff !important;
        color: #3730a3 !important;
        border: 1px solid #c7d2fe !important;
        font-weight: 600;
    }

    .badge-soft-secondary {
        background-color: #f1f5f9 !important;
        color: #334155 !important;
        border: 1px solid #e2e8f0 !important;
        font-weight: 600;
    }

    .badge-soft-dark {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        border: 1px solid #cbd5e1 !important;
        font-weight: 600;
    }

    /* =========================================================================
   FILTER PILLS (HIGH CONTRAST & UNIFIED STYLING)
   ========================================================================= */
    .btn-filter-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155 !important;
        line-height: 1.3;
    }

    .btn-filter-pill:hover {
        background: #f1f5f9;
        color: var(--begawi-primary) !important;
        border-color: #94a3b8;
        transform: translateY(-1px);
    }

    .btn-filter-pill.active {
        background: var(--begawi-primary) !important;
        color: #ffffff !important;
        border-color: var(--begawi-primary) !important;
        box-shadow: 0 4px 10px rgba(12, 90, 166, 0.25);
    }

    .btn-filter-pill.active-success {
        background: #16a34a !important;
        color: #ffffff !important;
        border-color: #16a34a !important;
        box-shadow: 0 4px 10px rgba(22, 163, 74, 0.25);
    }

    .btn-filter-pill.active-warning {
        background: #d97706 !important;
        color: #ffffff !important;
        border-color: #d97706 !important;
        box-shadow: 0 4px 10px rgba(217, 119, 6, 0.25);
    }

    .btn-filter-pill.active-danger {
        background: #dc2626 !important;
        color: #ffffff !important;
        border-color: #dc2626 !important;
        box-shadow: 0 4px 10px rgba(220, 38, 38, 0.25);
    }

    /* =========================================================================
   CARD ELEMENTS & HIGH-CONTRAST METADATA BOX
   ========================================================================= */
    .regulation-card {
        background: #ffffff;
        border: 1px solid #e2e8f0 !important;
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .regulation-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 32px rgba(8, 47, 92, 0.1) !important;
        border-color: #cbd5e1 !important;
    }

    .reg-card-title {
        color: #052143 !important;
        font-weight: 700;
        font-size: 16.5px;
        line-height: 1.4;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .reg-card-title:hover {
        color: var(--begawi-primary) !important;
    }

    .reg-card-excerpt {
        color: #334155 !important;
        font-size: 13.5px;
        line-height: 1.6;
        margin-bottom: 12px;
    }

    /* Box info penerbit & tanggal (100% readable text) */
    .reg-meta-box {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 13px;
    }

    .reg-meta-label {
        color: #475569 !important;
        font-weight: 500;
    }

    .reg-meta-value {
        color: #0f172a !important;
        font-weight: 600;
    }

    /* Consistent Action Buttons on Cards */
    .btn-reg-download {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        background: var(--begawi-primary);
        color: #ffffff !important;
        border: 1px solid var(--begawi-primary);
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-reg-download:hover {
        background: var(--begawi-dark);
        border-color: var(--begawi-dark);
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(8, 47, 92, 0.2);
    }

    .btn-reg-detail {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        background: #ffffff;
        color: var(--begawi-primary) !important;
        border: 1px solid #cbd5e1;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-reg-detail:hover {
        background: #f0f7ff;
        border-color: var(--begawi-primary);
        color: var(--begawi-primary) !important;
        transform: translateY(-1px);
    }

    .btn-reg-preview {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 13px;
        background: #f8fafc;
        color: #475569;
        border: 1px solid #cbd5e1;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .btn-reg-preview:hover {
        background: #ffffff;
        color: var(--begawi-primary);
        border-color: var(--begawi-primary);
        transform: translateY(-1px);
    }

    /* Norm Hierarchy Card */
    .norm-tier-card {
        border-radius: 14px;
        padding: 22px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        transition: all 0.25s ease;
    }

    .norm-tier-card:hover {
        border-color: var(--begawi-primary);
        box-shadow: 0 10px 24px rgba(12, 90, 166, 0.08);
    }

    .norm-tier-card h4 {
        color: #052143 !important;
        font-weight: 700;
    }

    .norm-tier-card p {
        color: #475569 !important;
        font-size: 13.5px;
        line-height: 1.6;
    }

    /* Stat strip text colors */
    .arena-stat-box {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
    }

    .arena-stat-content h4 {
        color: #052143 !important;
        font-size: 24px;
        font-weight: 800;
    }

    .arena-stat-content p {
        color: #475569 !important;
        font-size: 13px;
        font-weight: 500;
    }
</style>

<!-- =========================================================================
     PAGE HERO REGULASI PBJ (UNIFIED ARENA & BUNGA RAMPAI DNA)
     Full navbar height compensation (padding-top: 225px)
     ========================================================================= -->
<section class="arena-hero position-relative">
    <div class="arena-hero__bg" style="background-image: url('<?= base_url('assets/main/images/backgrounds/slider-1-1.png') ?>');"></div>
    <div class="arena-hero__overlay"></div>

    <div class="container">
        <div class="arena-hero__inner text-center pt-2 pt-md-4">
            <span class="arena-section-head__tag text-white mb-3" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.32); backdrop-filter: blur(6px);">
                <i class="fas fa-balance-scale me-1 text-warning"></i> Jaringan Dokumentasi & Informasi Hukum PBJ Provinsi Lampung
            </span>
            <h1 class="text-white mb-3" style="font-size: 38px; font-weight: 800; line-height: 1.25; text-shadow: 0 2px 10px rgba(0,0,0,0.4);">
                Pusat Regulasi & Tata Kelola Pengadaan
            </h1>
            <p class="mx-auto mb-4" style="max-width: 760px; font-size: 16px; line-height: 1.6; color: rgba(255,255,255,0.92); text-shadow: 0 1px 5px rgba(0,0,0,0.35);">
                Kompilasi peraturan perundang-undangan PBJ, Perpres 12/2021, Peraturan LKPP, Peraturan Daerah & Pergub Lampung,
                hierarki norma hukum, status keberlakuan resmi, serta dokumen pengadaan yang transparan dan akuntabel.
            </p>

            <!-- Search Bar (Unified BegawiPengadaan Design System) -->
            <div class="arena-search-bar mb-3">
                <form action="<?= site_url('regulasi') ?>" method="get" class="d-flex align-items-center">
                    <i class="fas fa-search text-muted me-2" style="font-size: 16px;"></i>
                    <input type="text" name="q" id="regulationHeroSearch" value="<?= esc($keyword) ?>" placeholder="Cari nomor regulasi, topik, kata kunci (contoh: Perpres 12, HPS, Katalog)..." autocomplete="off">
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
                        <i class="fas fa-search me-1"></i>
                        <span>Cari</span>
                    </button>
                    <?php if (! empty($keyword)): ?>
                        <a href="<?= $buildFilterUrl(['q' => null]) ?>" class="btn btn-sm btn-link text-muted ms-1" title="Hapus Pencarian">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Quick Search Suggestion Chips -->
            <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 mb-4 text-white-50 small">
                <span class="text-white opacity-75" style="font-size: 12.5px;"><i class="fas fa-bolt text-warning me-1"></i> Topik Populer:</span>
                <button type="button" class="btn btn-sm text-white px-3 py-1 radius-20 border-0" style="background: rgba(255,255,255,0.15); font-size: 12px; backdrop-filter: blur(4px);" onclick="applyRegulationQuickSearch('Perpres 12/2021')">Perpres 12/2021</button>
                <button type="button" class="btn btn-sm text-white px-3 py-1 radius-20 border-0" style="background: rgba(255,255,255,0.15); font-size: 12px; backdrop-filter: blur(4px);" onclick="applyRegulationQuickSearch('Pedoman HPS')">Pedoman HPS</button>
                <button type="button" class="btn btn-sm text-white px-3 py-1 radius-20 border-0" style="background: rgba(255,255,255,0.15); font-size: 12px; backdrop-filter: blur(4px);" onclick="applyRegulationQuickSearch('E-Katalog')">E-Katalog</button>
                <button type="button" class="btn btn-sm text-white px-3 py-1 radius-20 border-0" style="background: rgba(255,255,255,0.15); font-size: 12px; backdrop-filter: blur(4px);" onclick="applyRegulationQuickSearch('Pergub Lampung')">Pergub Lampung</button>
                <button type="button" class="btn btn-sm text-white px-3 py-1 radius-20 border-0" style="background: rgba(255,255,255,0.15); font-size: 12px; backdrop-filter: blur(4px);" onclick="applyRegulationQuickSearch('Swakelola')">Swakelola</button>
                <button type="button" class="btn btn-sm text-white px-3 py-1 radius-20 border-0" style="background: rgba(255,255,255,0.15); font-size: 12px; backdrop-filter: blur(4px);" onclick="applyRegulationQuickSearch('Tender Cepat')">Tender Cepat</button>
            </div>

            <!-- Sub-Navigation Category Pills (Unified with Arena Belajar & Bunga Rampai) -->
            <nav class="arena-nav-pills" aria-label="Kategori Regulasi PBJ">
                <a href="<?= site_url('regulasi') ?>" class="arena-nav-pill <?= empty($selectedCategory) && $activeTab !== 'hierarki' ? 'active' : '' ?>">
                    <i class="fas fa-th-large"></i>
                    <span>Semua Regulasi</span>
                    <span class="badge"><?= (int) ($stats['total'] ?? 0) ?></span>
                </a>

                <?php foreach ($categories as $c): ?>
                    <?php
                    $catSlug = $c['slug'] ?? '';
                    $isCatActive = (int) $selectedCategory === (int) $c['id'];
                    $catIcon = match (true) {
                        str_contains($catSlug, 'presiden') => 'fa-crown',
                        str_contains($catSlug, 'lkpp') => 'fa-landmark',
                        str_contains($catSlug, 'daerah'), str_contains($catSlug, 'gubernur') => 'fa-map-marked-alt',
                        str_contains($catSlug, 'edaran') => 'fa-envelope-open-text',
                        str_contains($catSlug, 'standar'), str_contains($catSlug, 'sdp') => 'fa-file-signature',
                        str_contains($catSlug, 'undang') => 'fa-gavel',
                        default => 'fa-balance-scale',
                    };
                    $catUrl = site_url('regulasi/kategori/' . esc($catSlug));
                    ?>
                    <a href="<?= $catUrl ?>" class="arena-nav-pill <?= $isCatActive ? 'active' : '' ?>">
                        <i class="fas <?= $catIcon ?>"></i>
                        <span><?= esc($c['name']) ?></span>
                        <?php if (isset($c['total_regulations'])): ?>
                            <span class="badge"><?= (int) $c['total_regulations'] ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>

                <a href="#hierarki-regulasi" class="arena-nav-pill">
                    <i class="fas fa-sitemap"></i>
                    <span>Hierarki Hukum</span>
                </a>
            </nav>
        </div>
    </div>
</section>

<!-- =========================================================================
     STAT COUNTER STRIP (UNIFIED ARENA BELAJAR DNA - OVERLAPPING HERO)
     ========================================================================= -->
<div class="arena-stats-strip">
    <div class="container">
        <div class="row g-3">
            <div class="col-6 col-lg-3">
                <a href="<?= site_url('regulasi') ?>" class="text-decoration-none">
                    <div class="arena-stat-box">
                        <div class="arena-stat-icon arena-stat-icon--blue">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="arena-stat-content">
                            <h4><?= number_format((int) ($stats['total'] ?? 0)) ?></h4>
                            <p>Total Regulasi PBJ</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-lg-3">
                <a href="<?= site_url('regulasi?status=active') ?>" class="text-decoration-none">
                    <div class="arena-stat-box">
                        <div class="arena-stat-icon arena-stat-icon--green">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="arena-stat-content">
                            <h4><?= number_format((int) ($stats['active_count'] ?? $stats['published'] ?? 0)) ?></h4>
                            <p>Regulasi Masih Berlaku</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-lg-3">
                <a href="#hierarki-regulasi" class="text-decoration-none">
                    <div class="arena-stat-box">
                        <div class="arena-stat-icon arena-stat-icon--amber">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="arena-stat-content">
                            <h4><?= number_format((int) ($stats['categories'] ?? count($categories))) ?></h4>
                            <p>Kategori & Tingkatan</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-lg-3">
                <a href="<?= site_url('regulasi?sort=downloads') ?>" class="text-decoration-none">
                    <div class="arena-stat-box">
                        <div class="arena-stat-icon arena-stat-icon--purple">
                            <i class="fas fa-download"></i>
                        </div>
                        <div class="arena-stat-content">
                            <h4><?= number_format((int) ($stats['downloaded'] ?? 0)) ?></h4>
                            <p>Total Unduhan Warga</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     MAIN CATALOG & FACETED FILTERS SECTION
     ========================================================================= -->
<section class="py-4" style="background-color: #f8fafc;">
    <div class="container">

        <!-- Toolbar Header & Controls -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div>
                <h2 class="h4 fw-bold mb-1" style="color: #052143;">
                    <?php if (! empty($selectedCategoryObj)): ?>
                        <?= esc($selectedCategoryObj['name']) ?>
                    <?php else: ?>
                        Katalog Dokumen Regulasi
                    <?php endif; ?>
                </h2>
                <p class="small mb-0" style="color: #475569;">
                    Menampilkan <strong style="color: #0f172a;"><?= count($regulations) ?></strong> dari <strong style="color: #0f172a;"><?= $totalRegulations ?></strong> dokumen resmi
                </p>
            </div>

            <!-- Sort Selector & Reset Button -->
            <div class="d-flex flex-wrap align-items-center gap-2">
                <?php if (! empty($keyword) || ! empty($selectedCategory) || ! empty($selectedStatus) || ! empty($selectedYear) || ! empty($selectedStage)): ?>
                    <a href="<?= site_url('regulasi') ?>" class="btn btn-sm btn-outline-danger radius-50 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                        <i class="fas fa-undo"></i> Reset Filter
                    </a>
                <?php endif; ?>

                <div class="d-flex align-items-center gap-2">
                    <label class="small text-nowrap mb-0 d-none d-sm-inline" style="color: #475569;">Urutkan:</label>
                    <select class="form-select form-select-sm radius-8 fw-semibold" style="color: #0f172a; border-color: #cbd5e1;" onchange="window.location.href = this.value;">
                        <option value="<?= $buildFilterUrl(['sort' => 'newest']) ?>" <?= $selectedSort === 'newest' ? 'selected' : '' ?>>Tanggal Terbit (Terbaru)</option>
                        <option value="<?= $buildFilterUrl(['sort' => 'year_desc']) ?>" <?= $selectedSort === 'year_desc' ? 'selected' : '' ?>>Tahun Regulasi (Terbaru)</option>
                        <option value="<?= $buildFilterUrl(['sort' => 'downloads']) ?>" <?= $selectedSort === 'downloads' ? 'selected' : '' ?>>Paling Sering Diunduh</option>
                        <option value="<?= $buildFilterUrl(['sort' => 'title']) ?>" <?= $selectedSort === 'title' ? 'selected' : '' ?>>Judul Regulasi (A-Z)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Faceted Filter Toolbar Card -->
        <div class="card border-0 shadow-sm radius-16 p-3 mb-4 bg-white" style="border: 1px solid #e2e8f0 !important;">
            <div class="row g-3 align-items-center">
                <!-- Status Legal Filter Pills (Unified Style) -->
                <div class="col-xl-6 col-lg-7">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="small fw-semibold me-1" style="color: #475569;"><i class="fas fa-filter text-primary me-1"></i> Status:</span>
                        <a href="<?= $buildFilterUrl(['status' => null, 'page' => 1]) ?>" class="btn-filter-pill <?= empty($selectedStatus) ? 'active' : '' ?>">
                            Semua
                        </a>
                        <a href="<?= $buildFilterUrl(['status' => 'active', 'page' => 1]) ?>" class="btn-filter-pill <?= $selectedStatus === 'active' ? 'active-success' : '' ?>">
                            <i class="fas fa-check-circle text-success me-1"></i> Berlaku
                        </a>
                        <a href="<?= $buildFilterUrl(['status' => 'amended', 'page' => 1]) ?>" class="btn-filter-pill <?= $selectedStatus === 'amended' ? 'active-warning' : '' ?>">
                            <i class="fas fa-exclamation-circle text-warning me-1"></i> Diubah
                        </a>
                        <a href="<?= $buildFilterUrl(['status' => 'revoked', 'page' => 1]) ?>" class="btn-filter-pill <?= $selectedStatus === 'revoked' ? 'active-danger' : '' ?>">
                            <i class="fas fa-times-circle text-danger me-1"></i> Dicabut
                        </a>
                    </div>
                </div>

                <!-- Dropdown Year & Stage Filter -->
                <div class="col-xl-6 col-lg-5">
                    <div class="d-flex flex-wrap align-items-center justify-content-lg-end gap-2">
                        <!-- Tahun -->
                        <select class="form-select form-select-sm radius-8 w-auto fw-semibold" style="color: #0f172a; border-color: #cbd5e1;" onchange="window.location.href = this.value;">
                            <option value="<?= $buildFilterUrl(['year' => null, 'page' => 1]) ?>">Semua Tahun</option>
                            <?php foreach ($years as $yr): ?>
                                <option value="<?= $buildFilterUrl(['year' => $yr, 'page' => 1]) ?>" <?= (int) $selectedYear === (int) $yr ? 'selected' : '' ?>>
                                    Tahun <?= (int) $yr ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Tahapan PBJ -->
                        <select class="form-select form-select-sm radius-8 w-auto fw-semibold" style="color: #0f172a; border-color: #cbd5e1;" onchange="window.location.href = this.value;">
                            <option value="<?= $buildFilterUrl(['stage_id' => null, 'page' => 1]) ?>">Semua Tahapan PBJ</option>
                            <?php foreach ($stages as $st): ?>
                                <option value="<?= $buildFilterUrl(['stage_id' => (int) $st['id'], 'page' => 1]) ?>" <?= (int) $selectedStage === (int) $st['id'] ? 'selected' : '' ?>>
                                    <?= esc($st['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Search Chips (If Any) -->
        <?php if (! empty($keyword)): ?>
            <div class="p-3 mb-4 radius-12 d-flex align-items-center justify-content-between shadow-sm" style="background: #e0e7ff; border: 1px solid #c7d2fe; color: #1e1b4b;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-search fs-5" style="color: #4338ca;"></i>
                    <span>Hasil pencarian untuk kata kunci: <strong style="color: #312e81;">"<?= esc($keyword) ?>"</strong> (<?= $totalRegulations ?> ditemukan)</span>
                </div>
                <a href="<?= $buildFilterUrl(['q' => null]) ?>" class="btn btn-sm btn-outline-primary radius-8 px-3 fw-semibold">Hapus Pencarian</a>
            </div>
        <?php endif; ?>

        <!-- Regulations Grid -->
        <?php if (empty($regulations)): ?>
            <!-- Friendly Empty State -->
            <div class="card border-0 shadow-sm radius-16 p-5 text-center bg-white my-4" style="border: 1px solid #e2e8f0 !important;">
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
            <div class="row g-4">
                <?php foreach ($regulations as $reg): ?>
                    <?php
                    $detailUrl = site_url('regulasi/' . esc($reg['slug']));
                    $downloadUrl = site_url('regulasi/' . esc($reg['slug']) . '/unduh');
                    $hasFile = ! empty($reg['current_file_id']);
                    $fileSizeKb = ! empty($reg['file_size']) ? number_format((float) $reg['file_size'] / 1024, 0) : null;

                    // Data for JavaScript Quick Preview Modal
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
                    <div class="col-xl-4 col-md-6">
                        <article class="regulation-card p-4">
                            <!-- Card Header -->
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                <?= $renderLegalStatusBadge($reg['legal_status'] ?? 'active') ?>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge badge-soft-primary px-2 py-1 radius-6">
                                        <?= esc($reg['category_name'] ?? 'Umum') ?>
                                    </span>
                                    <span class="badge badge-soft-dark px-2 py-1 radius-6">
                                        <?= esc($reg['document_year'] ?? '-') ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Document Number (Nomor Regulasi - Crystal Clear Text) -->
                            <h3 class="h5 fw-bold mb-2 leading-snug">
                                <a href="<?= $detailUrl ?>" class="reg-card-title">
                                    <?= esc($reg['document_number'] ?? 'Dokumen Tanpa Nomor') ?>
                                </a>
                            </h3>

                            <!-- Title (Tentang - Crystal Clear Text) -->
                            <p class="reg-card-excerpt flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= esc($reg['title'] ?? '-') ?>
                            </p>

                            <!-- Issuer & Date Meta Box (100% Readable High Contrast) -->
                            <div class="reg-meta-box mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="reg-meta-label"><i class="fas fa-university me-1 text-primary"></i> Penerbit:</span>
                                    <span class="reg-meta-value text-truncate ms-2" style="max-width: 170px;" title="<?= esc($reg['issuer_name'] ?? '-') ?>">
                                        <?= esc($reg['issuer_name'] ?? 'Pemerintah') ?>
                                    </span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="reg-meta-label"><i class="fas fa-calendar-check me-1 text-success"></i> Berlaku:</span>
                                    <span class="reg-meta-value">
                                        <?= ! empty($reg['effective_date']) ? date('d M Y', strtotime($reg['effective_date'])) : (! empty($reg['official_date']) ? date('d M Y', strtotime($reg['official_date'])) : '-') ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Procurement Stages Badges (High Contrast Soft Primary) -->
                            <?php if (! empty($reg['stages'])): ?>
                                <div class="d-flex flex-wrap gap-1 mb-3">
                                    <?php foreach (array_slice($reg['stages'], 0, 3) as $stg): ?>
                                        <a href="<?= $buildFilterUrl(['stage_id' => (int) $stg['id'], 'page' => 1]) ?>" class="badge badge-soft-primary text-decoration-none radius-6 px-2 py-1" style="font-size: 11px;">
                                            <i class="fas fa-layer-group me-1"></i> <?= esc($stg['name']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                    <?php if (count($reg['stages']) > 3): ?>
                                        <span class="badge badge-soft-secondary radius-6 px-1 py-1" style="font-size: 11px;">+<?= count($reg['stages']) - 3 ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Bottom Action Bar (Unified Consistent Button Styles) -->
                            <div class="pt-3 border-top border-light-subtle d-flex align-items-center justify-content-between gap-2 mt-auto" style="border-color: #e2e8f0 !important;">
                                <div class="d-flex align-items-center gap-1">
                                    <button type="button" class="btn-reg-preview" onclick="openRegulationModal(<?= esc(json_encode($modalData)) ?>)" title="Pratinjau Ringkas">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="<?= $detailUrl ?>" class="btn-reg-detail">
                                        <span>Detail</span>
                                        <i class="fas fa-chevron-right small"></i>
                                    </a>
                                </div>

                                <div>
                                    <?php if ($hasFile): ?>
                                        <a href="<?= $downloadUrl ?>" class="btn-reg-download" title="Unduh Berkas PDF">
                                            <i class="fas fa-download"></i>
                                            <span>Unduh</span>
                                            <?php if ($fileSizeKb): ?>
                                                <small class="opacity-75 ps-1 d-none d-md-inline">(<?= $fileSizeKb ?>KB)</small>
                                            <?php endif; ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="badge badge-soft-secondary px-2 py-2 small" title="Naskah digital belum diunggah">
                                            <i class="fas fa-file-alt me-1"></i> Fisik
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Smart Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="mt-5 d-flex justify-content-center">
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
</section>

<!-- =========================================================================
     HIERARKI TATA KELOLA HUKUM PBJ & RELASI KETERKAITAN
     ========================================================================= -->
<section id="hierarki-regulasi" class="py-5 bg-white border-top border-bottom" style="border-color: #e2e8f0 !important;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge badge-soft-primary radius-6 px-3 py-2 fw-semibold mb-2">
                <i class="fas fa-sitemap me-1"></i> Arsitektur Hukum PBJ
            </span>
            <h2 class="fw-bold mb-2" style="color: #052143;">Hierarki Regulasi Pengadaan Barang/Jasa</h2>
            <p class="mx-auto" style="color: #475569; max-width: 720px;">
                Struktur hierarkis peraturan perundang-undangan PBJ di Indonesia, mulai dari norma undang-undang hingga pedoman teknis operasional di Pemerintah Provinsi Lampung.
            </p>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Tier 1: UU -->
            <div class="col-xl-4 col-md-6">
                <div class="norm-tier-card d-flex flex-column h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge badge-soft-danger px-2 py-1 radius-6">
                            Tier 1 · UU
                        </span>
                        <i class="fas fa-gavel text-danger fs-4"></i>
                    </div>
                    <h4 class="h6 fw-bold mb-2">Undang-Undang Republik Indonesia</h4>
                    <p class="mb-3">
                        Landasan konstitusional perbendaharaan negara, anti korupsi, dan cipta kerja yang mendasari tata kelola belanja negara.
                    </p>
                    <div class="small fw-semibold text-primary mb-3">
                        <i class="fas fa-check text-success me-1"></i> UU No. 1/2004, UU No. 6/2023
                    </div>
                    <div class="mt-auto pt-2 border-top" style="border-color: #f1f5f9 !important;">
                        <a href="<?= site_url('regulasi?q=Undang-Undang') ?>" class="btn btn-sm btn-outline-danger radius-8 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                            <span>Jelajahi UU</span>
                            <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tier 2: Perpres -->
            <div class="col-xl-4 col-md-6">
                <div class="norm-tier-card d-flex flex-column h-100" style="border-left: 4px solid var(--begawi-primary) !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge badge-soft-primary px-2 py-1 radius-6">
                            Tier 2 · Perpres (Inti)
                        </span>
                        <i class="fas fa-crown text-primary fs-4"></i>
                    </div>
                    <h4 class="h6 fw-bold mb-2">Peraturan Presiden Pengadaan</h4>
                    <p class="mb-3">
                        Regulasi payung utama PBJ Pemerintah yang mengatur para pihak, kewenangan PPK/Pokja, metode pemilihan, dan kontrak.
                    </p>
                    <div class="small fw-semibold text-primary mb-3">
                        <i class="fas fa-check text-success me-1"></i> Perpres No. 16/2018 jo Perpres 12/2021
                    </div>
                    <div class="mt-auto pt-2 border-top" style="border-color: #f1f5f9 !important;">
                        <a href="<?= site_url('regulasi?q=Perpres') ?>" class="btn btn-sm btn-outline-primary radius-8 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                            <span>Jelajahi Perpres</span>
                            <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tier 3: PerLKPP -->
            <div class="col-xl-4 col-md-6">
                <div class="norm-tier-card d-flex flex-column h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge badge-soft-info px-2 py-1 radius-6">
                            Tier 3 · PerLKPP
                        </span>
                        <i class="fas fa-landmark text-info fs-4"></i>
                    </div>
                    <h4 class="h6 fw-bold mb-2">Peraturan Lembaga (LKPP)</h4>
                    <p class="mb-3">
                        Ketentuan pelaksanaan teknis: pengadaan melalui penyedia, swakelola, e-katalog, toko daring, dan perencanaan.
                    </p>
                    <div class="small fw-semibold text-primary mb-3">
                        <i class="fas fa-check text-success me-1"></i> PerLKPP No. 12/2021, No. 9/2021, No. 3/2021
                    </div>
                    <div class="mt-auto pt-2 border-top" style="border-color: #f1f5f9 !important;">
                        <a href="<?= site_url('regulasi?q=LKPP') ?>" class="btn btn-sm btn-outline-info radius-8 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                            <span>Jelajahi PerLKPP</span>
                            <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tier 4: Pergub Lampung -->
            <div class="col-xl-4 col-md-6">
                <div class="norm-tier-card d-flex flex-column h-100" style="border-left: 4px solid #16a34a !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge badge-soft-success px-2 py-1 radius-6">
                            Tier 4 · Daerah
                        </span>
                        <i class="fas fa-map-marked-alt text-success fs-4"></i>
                    </div>
                    <h4 class="h6 fw-bold mb-2">Peraturan Daerah & Pergub Lampung</h4>
                    <p class="mb-3">
                        Implementasi lokal Pemerintah Provinsi Lampung: pemanfaatan e-Katalog Lokal, Bela Pengadaan, dan afirmasi UMKM.
                    </p>
                    <div class="small fw-semibold text-primary mb-3">
                        <i class="fas fa-check text-success me-1"></i> Pergub Lampung No. 35/2022
                    </div>
                    <div class="mt-auto pt-2 border-top" style="border-color: #f1f5f9 !important;">
                        <a href="<?= site_url('regulasi?q=Lampung') ?>" class="btn btn-sm btn-outline-success radius-8 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                            <span>Jelajahi Pergub</span>
                            <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tier 5: Surat Edaran -->
            <div class="col-xl-4 col-md-6">
                <div class="norm-tier-card d-flex flex-column h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge badge-soft-warning px-2 py-1 radius-6">
                            Tier 5 · Surat Edaran
                        </span>
                        <i class="fas fa-envelope-open-text text-warning fs-4"></i>
                    </div>
                    <h4 class="h6 fw-bold mb-2">Surat Edaran & Instruksi</h4>
                    <p class="mb-3">
                        Pedoman percepatan pelaksanaan tender, penegasan perhitungan HPS yang akuntabel, dan mitigasi risiko audit.
                    </p>
                    <div class="small fw-semibold text-primary mb-3">
                        <i class="fas fa-check text-success me-1"></i> SE Kepala LKPP No. 3/2023
                    </div>
                    <div class="mt-auto pt-2 border-top" style="border-color: #f1f5f9 !important;">
                        <a href="<?= site_url('regulasi?q=Surat+Edaran') ?>" class="btn btn-sm btn-outline-warning text-dark radius-8 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                            <span>Jelajahi SE</span>
                            <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tier 6: Standar Dokumen Pemilihan -->
            <div class="col-xl-4 col-md-6">
                <div class="norm-tier-card d-flex flex-column h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge badge-soft-secondary px-2 py-1 radius-6">
                            Tier 6 · SDP & Juknis
                        </span>
                        <i class="fas fa-file-signature text-secondary fs-4"></i>
                    </div>
                    <h4 class="h6 fw-bold mb-2">Standar Dokumen Pemilihan (SDP)</h4>
                    <p class="mb-3">
                        Format baku model dokumen tender barang, konstruksi, konsultansi, dan jasa lainnya yang disahkan oleh LKPP.
                    </p>
                    <div class="small fw-semibold text-primary mb-3">
                        <i class="fas fa-check text-success me-1"></i> Model SDP Standar LKPP
                    </div>
                    <div class="mt-auto pt-2 border-top" style="border-color: #f1f5f9 !important;">
                        <a href="<?= site_url('regulasi?q=Standar+Dokumen') ?>" class="btn btn-sm btn-outline-secondary radius-8 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                            <span>Jelajahi SDP</span>
                            <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     TAHAPAN PENGADAAN & INTEGRASI REGULASI
     ========================================================================= -->
<?php if (! empty($stages)): ?>
    <section class="py-5 bg-light border-bottom" style="border-color: #e2e8f0 !important;">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge badge-soft-primary radius-6 px-3 py-2 fw-semibold mb-2">
                    <i class="fas fa-project-diagram me-1"></i> Siklus Pengadaan PBJ
                </span>
                <h2 class="fw-bold mb-2" style="color: #052143;">Jelajahi Regulasi Berdasarkan Tahapan PBJ</h2>
                <p class="mx-auto" style="color: #475569; max-width: 680px;">
                    Pahami aturan dan dasar hukum spesifik pada setiap fase siklus pengadaan barang/jasa pemerintah.
                </p>
            </div>

            <div class="row g-3 justify-content-center">
                <?php foreach ($stages as $stg): ?>
                    <?php
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
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                        <a href="<?= $buildFilterUrl(['stage_id' => $isStgActive ? null : $stgId, 'page' => 1]) ?>" class="text-decoration-none d-block h-100">
                            <div class="card h-100 border-0 shadow-sm radius-16 text-center p-3 transition-hover <?= $isStgActive ? 'bg-primary text-white' : 'bg-white' ?>" style="border: 1px solid #e2e8f0 !important;">
                                <div class="mx-auto mb-2 rounded-circle d-flex align-items-center justify-content-center <?= $isStgActive ? 'bg-white text-primary' : 'badge-soft-primary' ?>" style="width: 48px; height: 48px;">
                                    <i class="fas <?= $stgIcon ?> fs-5"></i>
                                </div>
                                <h6 class="fw-bold mb-1 <?= $isStgActive ? 'text-white' : '' ?>" style="<?= ! $isStgActive ? 'color: #052143;' : '' ?> font-size: 14px;">
                                    <?= esc($stg['name']) ?>
                                </h6>
                                <small class="<?= $isStgActive ? 'text-white text-opacity-75' : '' ?>" style="<?= ! $isStgActive ? 'color: #64748b;' : '' ?> font-size: 12px;">
                                    <?= $isStgActive ? 'Filter Aktif' : 'Lihat Aturan' ?>
                                </small>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- =========================================================================
     REGULASI PALING POPULER & SERING DIUNDUH
     ========================================================================= -->
<?php if (! empty($popular)): ?>
    <section class="py-5 bg-white">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                <div>
                    <span class="badge badge-soft-warning radius-6 px-3 py-1 fw-semibold mb-2">
                        <i class="fas fa-fire me-1 text-danger"></i> Paling Banyak Diakses
                    </span>
                    <h3 class="fw-bold mb-0" style="color: #052143;">Regulasi Populer PBJ</h3>
                </div>
                <a href="<?= site_url('regulasi?sort=downloads') ?>" class="btn btn-outline-primary radius-8 px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1">
                    <span>Lihat Seluruh Peringkat</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="row g-4">
                <?php foreach ($popular as $pop): ?>
                    <?php
                    $popUrl = site_url('regulasi/' . esc($pop['slug']));
                    ?>
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card h-100 border-0 shadow-sm radius-16 p-3 bg-white transition-hover" style="border: 1px solid #e2e8f0 !important;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge badge-soft-primary radius-6 px-2 py-1 small">
                                    <?= esc($pop['category_name'] ?? 'Regulasi') ?>
                                </span>
                                <span class="badge badge-soft-success radius-6 px-2 py-1 small fw-semibold">
                                    <i class="fas fa-download me-1"></i> <?= number_format((int) ($pop['download_count'] ?? 0)) ?>x
                                </span>
                            </div>
                            <h4 class="h6 fw-bold mb-2">
                                <a href="<?= $popUrl ?>" class="text-decoration-none hover-primary" style="color: #052143;">
                                    <?= esc($pop['document_number'] ?? 'Dokumen') ?>
                                </a>
                            </h4>
                            <p class="small mb-3 flex-grow-1" style="color: #475569; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= esc($pop['title'] ?? '-') ?>
                            </p>
                            <div class="pt-2 border-top d-flex align-items-center justify-content-between small" style="border-color: #e2e8f0 !important;">
                                <span style="color: #64748b;">Tahun <?= esc($pop['document_year'] ?? '-') ?></span>
                                <a href="<?= $popUrl ?>" class="text-primary fw-semibold text-decoration-none">
                                    Baca <i class="fas fa-chevron-right small"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- =========================================================================
     CLOSING CALL TO ACTION BANNER (CONSULTATION & ADVOCACY)
     ========================================================================= -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container">
        <div class="card border-0 radius-20 shadow-sm p-4 p-md-5 overflow-hidden position-relative" style="background: linear-gradient(135deg, #082f5c 0%, #0c5aa6 55%, #198754 100%); color: #ffffff;">
            <div class="position-absolute end-0 top-0 bottom-0 opacity-10 d-none d-lg-flex align-items-center me-5" style="font-size: 260px; pointer-events: none;">
                <i class="fas fa-balance-scale"></i>
            </div>
            <div class="row align-items-center position-relative z-1">
                <div class="col-lg-8">
                    <span class="badge radius-8 px-3 py-2 mb-3 fw-semibold" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(6px); font-size: 12.5px;">
                        <i class="fas fa-shield-alt text-warning me-1"></i> Advokasi &amp; Kepatuhan Regulasi
                    </span>
                    <h3 class="fw-bold mb-2 text-white" style="font-size: 28px; line-height: 1.3;">
                        Memerlukan Telaah Hukum atau Advokasi Dokumen Pengadaan?
                    </h3>
                    <p class="text-white-80 mb-4 mb-lg-0" style="color: rgba(255,255,255,0.9); font-size: 15px; line-height: 1.6; max-width: 680px;">
                        Biro Pengadaan Barang &amp; Jasa Provinsi Lampung menyediakan layanan konsultasi kepatuhan hukum, pendampingan penyusunan HPS/KAK, dan mediasi sanggahan untuk seluruh OPD dan Pokja Pemilihan se-Provinsi Lampung.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="d-flex flex-column flex-sm-row flex-lg-column gap-2 justify-content-lg-end">
                        <a href="<?= site_url('kontak') ?>" class="btn btn-warning text-dark fw-bold radius-8 px-4 py-3 shadow-sm d-inline-flex align-items-center justify-content-center gap-2">
                            <i class="fas fa-headset"></i>
                            <span>Hubungi Advokasi PBJ</span>
                        </a>
                        <a href="<?= site_url('bunga-rampai') ?>" class="btn btn-outline-light radius-8 px-4 py-2 fw-semibold d-inline-flex align-items-center justify-content-center gap-2" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.3);">
                            <i class="fas fa-book-reader"></i>
                            <span>Buka Bunga Rampai PBJ</span>
                        </a>
                    </div>
                </div>
            </div>
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
                    <span id="modalStatusBadge" class="badge radius-6 px-2 py-1 small">Status</span>
                    <span id="modalCategoryBadge" class="badge badge-soft-primary px-2 py-1 small">Kategori</span>
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
            stBadge.className = 'badge badge-soft-success radius-6 px-2 py-1 small';
            stBadge.textContent = 'Berlaku';
        } else if (status === 'amended' || status === 'diubah') {
            stBadge.className = 'badge badge-soft-warning radius-6 px-2 py-1 small';
            stBadge.textContent = 'Diubah';
        } else if (status === 'revoked' || status === 'dicabut') {
            stBadge.className = 'badge badge-soft-danger radius-6 px-2 py-1 small';
            stBadge.textContent = 'Dicabut';
        } else {
            stBadge.className = 'badge badge-soft-secondary radius-6 px-2 py-1 small';
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
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
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
</script>

<?= $this->endSection() ?>