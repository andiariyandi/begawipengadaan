<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Breadcrumbs Header (WowDash Standard) -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <h6 class="fw-semibold mb-0 text-primary-light">Pusat Manajemen Carousel &amp; Sorotan</h6>
    <ul class="d-flex align-items-center gap-2">
        <li class="fw-medium">
            <a href="<?= site_url('dashboard') ?>" class="d-flex align-items-center gap-1 hover-text-primary text-secondary-light">
                <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                Dashboard
            </a>
        </li>
        <li>-</li>
        <li class="fw-medium text-secondary-light">Kelola Landing Page</li>
        <li>-</li>
        <li class="text-primary-600 fw-medium">Carousel &amp; Sorotan</li>
    </ul>
</div>

<!-- Reusable Tabbed Console Navigation -->
<?= view('dashboard/landing/_nav_landing', ['section' => 'promo', 'activeSub' => 'sorotan-carousel']) ?>

<!-- Top Action & Description Card -->
<div class="card mb-24">
    <div class="card-body p-20">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h6 class="text-lg fw-semibold text-primary-light mb-4">Pengendalian Hero Carousel &amp; Sorotan Portal Publik</h6>
                <p class="text-secondary-light mb-0 text-sm">
                    Kelola seluruh slide hero carousel, status unggulan (<code>is_featured</code>), dan urutan tayang slide untuk <strong>Arena Belajar, Bunga Rampai, Regulasi, Berita, dan Event &amp; Agenda</strong>.
                </p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn btn-primary-600 radius-8 px-16 py-10 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#discoveryModal">
                    <iconify-icon icon="solar:magnifer-zoom-in-bold" class="text-lg"></iconify-icon>
                    <span>Temukan &amp; Tambah Konten</span>
                </button>
                <button type="button" class="btn btn-outline-info-600 radius-8 px-16 py-10 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#simulatorModal">
                    <iconify-icon icon="solar:play-stream-bold" class="text-lg"></iconify-icon>
                    <span>Simulasi Hero Carousel</span>
                </button>
                <button type="button" class="btn btn-outline-primary-600 radius-8 px-16 py-10 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#settingsModal">
                    <iconify-icon icon="solar:settings-bold" class="text-lg"></iconify-icon>
                    <span>Konfigurasi Mode</span>
                </button>
                <div class="dropdown">
                    <button class="btn btn-neutral-200 text-secondary-light radius-8 px-14 py-10 dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <iconify-icon icon="solar:eye-bold" class="text-lg text-primary-600"></iconify-icon>
                        <span class="d-none d-sm-inline">Pratinjau Publik</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end radius-8 shadow border-0 py-8">
                        <li><h6 class="dropdown-header text-secondary-light text-2xs fw-semibold text-uppercase">Buka Portal Publik</h6></li>
                        <li><a class="dropdown-item py-8 text-sm fw-medium d-flex align-items-center gap-2" href="<?= site_url('arena-belajar') ?>" target="_blank"><iconify-icon icon="solar:graduation-cap-bold" class="text-primary-600"></iconify-icon> Hero Arena Belajar</a></li>
                        <li><a class="dropdown-item py-8 text-sm fw-medium d-flex align-items-center gap-2" href="<?= site_url('bunga-rampai') ?>" target="_blank"><iconify-icon icon="solar:library-bold" class="text-info-600"></iconify-icon> Hero Bunga Rampai</a></li>
                        <li><a class="dropdown-item py-8 text-sm fw-medium d-flex align-items-center gap-2" href="<?= site_url('regulasi') ?>" target="_blank"><iconify-icon icon="solar:document-text-bold" class="text-success-600"></iconify-icon> Hero Regulasi PBJ</a></li>
                        <li><a class="dropdown-item py-8 text-sm fw-medium d-flex align-items-center gap-2" href="<?= site_url('berita') ?>" target="_blank"><iconify-icon icon="solar:newspaper-bold" class="text-purple"></iconify-icon> Hero Warta Berita</a></li>
                        <li><a class="dropdown-item py-8 text-sm fw-medium d-flex align-items-center gap-2" href="<?= site_url('event') ?>" target="_blank"><iconify-icon icon="solar:calendar-bold" class="text-warning-main"></iconify-icon> Hero Event &amp; Agenda</a></li>
                        <li><hr class="dropdown-divider my-4"></li>
                        <li><a class="dropdown-item py-8 text-sm fw-medium d-flex align-items-center gap-2" href="<?= site_url('/') ?>" target="_blank"><iconify-icon icon="solar:home-smile-bold" class="text-cyan"></iconify-icon> Hero Slider Beranda Utama</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KPI Metrics (Native WowDash Gradient Cards) -->
<div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Aktif di Hero Slider</p>
                        <h6 class="mb-0 text-primary-light" id="kpiTotalCarousel"><?= number_format($stats['total_carousel_portal'] ?? 0) ?> Slide</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:slider-vertical-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center gap-1 text-success-main">
                        <iconify-icon icon="solar:check-circle-bold" class="text-xs"></iconify-icon> 5 Modul
                    </span>
                    Total slide di hero portal
                </p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Item Unggulan</p>
                        <h6 class="mb-0 text-primary-light" id="kpiTotalFeatured"><?= number_format($stats['total_featured_portal'] ?? 0) ?> Item</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:star-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center gap-1 text-warning-main">
                        <iconify-icon icon="solar:star-fall-bold" class="text-xs"></iconify-icon> Unggulan
                    </span>
                    Status featured prioritas
                </p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Cakupan Landing Page</p>
                        <h6 class="mb-0 text-primary-light">5 Modul Terintegrasi</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:layers-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center gap-1 text-info-main">
                        <iconify-icon icon="solar:check-circle-bold" class="text-xs"></iconify-icon> 8 Entitas
                    </span>
                    Program, Course, Kelas, Artikel, Media, Reg, News, Event
                </p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Sinkronisasi Indikator</p>
                        <h6 class="mb-0 text-primary-light">100% Real-Time</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:check-circle-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center gap-1 text-success-main">
                        <iconify-icon icon="solar:round-transfer-horizontal-bold" class="text-xs"></iconify-icon> Auto Sync
                    </span>
                    Titik carousel otomatis
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Main Tabs Navigation (Native WowDash Bordered Tab) -->
<div class="card p-0 radius-12 overflow-hidden mb-24">
    <div class="card-body p-16 pb-0">
        <ul class="nav bordered-tab border border-top-0 border-start-0 border-end-0 d-flex flex-wrap nav-pills mb-0" id="spotlightTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link px-20 py-12 <?= ($activeTab === 'overview' || empty($activeTab)) ? 'active' : '' ?>" id="tab-overview-btn" data-bs-toggle="pill" data-bs-target="#tab-overview" type="button" role="tab">
                    <span class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:widget-2-bold" class="text-lg"></iconify-icon>
                        <span>Ringkasan Semua Sorotan</span>
                    </span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-20 py-12 <?= $activeTab === 'arena' ? 'active' : '' ?>" id="tab-arena-btn" data-bs-toggle="pill" data-bs-target="#tab-arena" type="button" role="tab">
                    <span class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:graduation-cap-bold" class="text-lg"></iconify-icon>
                        <span>Arena Belajar</span>
                        <span class="badge text-xs px-8 py-2 rounded-pill bg-primary-100 text-primary-600 count-badge-arena"><?= (int) $stats['arena']['carousel_count'] ?></span>
                    </span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-20 py-12 <?= $activeTab === 'knowledge' ? 'active' : '' ?>" id="tab-knowledge-btn" data-bs-toggle="pill" data-bs-target="#tab-knowledge" type="button" role="tab">
                    <span class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:library-bold" class="text-lg"></iconify-icon>
                        <span>Bunga Rampai</span>
                        <span class="badge text-xs px-8 py-2 rounded-pill bg-info-100 text-info-600 count-badge-knowledge"><?= (int) $stats['knowledge']['carousel_count'] ?></span>
                    </span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-20 py-12 <?= $activeTab === 'regulation' ? 'active' : '' ?>" id="tab-regulation-btn" data-bs-toggle="pill" data-bs-target="#tab-regulation" type="button" role="tab">
                    <span class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:document-text-bold" class="text-lg"></iconify-icon>
                        <span>Regulasi PBJ</span>
                        <span class="badge text-xs px-8 py-2 rounded-pill bg-success-100 text-success-600 count-badge-regulation"><?= (int) $stats['regulation']['carousel_count'] ?></span>
                    </span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-20 py-12 <?= $activeTab === 'news' ? 'active' : '' ?>" id="tab-news-btn" data-bs-toggle="pill" data-bs-target="#tab-news" type="button" role="tab">
                    <span class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:newspaper-bold" class="text-lg"></iconify-icon>
                        <span>Berita &amp; Artikel</span>
                        <span class="badge text-xs px-8 py-2 rounded-pill bg-info-100 text-info-600 count-badge-news"><?= (int) $stats['news']['carousel_count'] ?></span>
                    </span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-20 py-12 <?= $activeTab === 'event' ? 'active' : '' ?>" id="tab-event-btn" data-bs-toggle="pill" data-bs-target="#tab-event" type="button" role="tab">
                    <span class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:calendar-bold" class="text-lg"></iconify-icon>
                        <span>Event &amp; Agenda</span>
                        <span class="badge text-xs px-8 py-2 rounded-pill bg-warning-100 text-warning-main count-badge-event"><?= (int) $stats['event']['carousel_count'] ?></span>
                    </span>
                </button>
            </li>
        </ul>
    </div>
</div>

<!-- Tab Content Container -->
<div class="tab-content" id="spotlightTabsContent">

    <!-- TAB 1: RINGKASAN SEMUA SOROTAN (OVERVIEW) -->
    <div class="tab-pane fade <?= ($activeTab === 'overview' || empty($activeTab)) ? 'show active' : '' ?>" id="tab-overview" role="tabpanel">
        
        <!-- Feature Status Grid -->
        <div class="row g-4 mb-24">
            <?php
            $portalFeatures = [
                [
                    'name'        => 'Arena Belajar',
                    'slug'        => 'arena',
                    'icon'        => 'solar:graduation-cap-bold',
                    'url'         => site_url('arena-belajar'),
                    'target_tab'  => '#tab-arena-btn',
                    'mode'        => $settings['learning']['mode'],
                    'limit'       => $settings['learning']['limit'],
                    'carousel_cnt'=> $stats['arena']['carousel_count'],
                    'breakdown'   => $stats['arena']['programs'] . ' Program, ' . $stats['arena']['courses'] . ' Course, ' . $stats['arena']['classes'] . ' Kelas',
                ],
                [
                    'name'        => 'Bunga Rampai',
                    'slug'        => 'knowledge',
                    'icon'        => 'solar:library-bold',
                    'url'         => site_url('bunga-rampai'),
                    'target_tab'  => '#tab-knowledge-btn',
                    'mode'        => $settings['knowledge']['mode'],
                    'limit'       => $settings['knowledge']['limit'],
                    'carousel_cnt'=> $stats['knowledge']['carousel_count'],
                    'breakdown'   => $stats['knowledge']['articles'] . ' Dokumen/Artikel, ' . $stats['knowledge']['media'] . ' Multimedia Slide',
                ],
                [
                    'name'        => 'Regulasi PBJ',
                    'slug'        => 'regulation',
                    'icon'        => 'solar:document-text-bold',
                    'url'         => site_url('regulasi'),
                    'target_tab'  => '#tab-regulation-btn',
                    'mode'        => $settings['regulation']['mode'],
                    'limit'       => $settings['regulation']['limit'],
                    'carousel_cnt'=> $stats['regulation']['carousel_count'],
                    'breakdown'   => 'Dokumen Hukum & Perpres',
                ],
                [
                    'name'        => 'Berita & Artikel',
                    'slug'        => 'news',
                    'icon'        => 'solar:newspaper-bold',
                    'url'         => site_url('berita'),
                    'target_tab'  => '#tab-news-btn',
                    'mode'        => $settings['news']['mode'],
                    'limit'       => $settings['news']['limit'],
                    'carousel_cnt'=> $stats['news']['carousel_count'],
                    'breakdown'   => 'Warta Publikasi & Rilis Pers',
                ],
                [
                    'name'        => 'Event & Agenda',
                    'slug'        => 'event',
                    'icon'        => 'solar:calendar-bold',
                    'url'         => site_url('event'),
                    'target_tab'  => '#tab-event-btn',
                    'mode'        => $settings['event']['mode'],
                    'limit'       => $settings['event']['limit'],
                    'carousel_cnt'=> $stats['event']['carousel_count'],
                    'breakdown'   => 'Webinar, Workshop & Bimtek',
                ],
            ];
            ?>
            <?php foreach ($portalFeatures as $pf): ?>
                <?php
                $pctUsed = min(100, round(($pf['carousel_cnt'] / max(1, $pf['limit'])) * 100));
                ?>
                <div class="col-xl-4 col-md-6">
                    <div class="card h-100">
                        <div class="card-body p-20 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-16">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="w-40-px h-40-px rounded-circle d-inline-flex align-items-center justify-content-center bg-primary-50 text-primary-600">
                                            <iconify-icon icon="<?= $pf['icon'] ?>" class="text-xl"></iconify-icon>
                                        </span>
                                        <div>
                                            <h6 class="text-md fw-semibold text-primary-light mb-1"><?= esc($pf['name']) ?></h6>
                                            <span class="text-secondary-light text-xs"><?= esc($pf['breakdown']) ?></span>
                                        </div>
                                    </div>
                                    <span class="badge text-xs px-10 py-4 radius-4 bg-success-100 text-success-600 fw-medium">
                                        Terhubung
                                    </span>
                                </div>
                                <div class="p-16 radius-8 bg-neutral-50 border border-neutral-200 mb-16 text-xs">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-secondary-light">Mode Tampilan:</span>
                                        <span class="fw-semibold text-primary-light text-uppercase"><?= esc($pf['mode']) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-secondary-light">Batas Maksimal Slide:</span>
                                        <span class="fw-semibold text-primary-light"><?= (int) $pf['limit'] ?> Slide</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-8">
                                        <span class="text-secondary-light">Slide Aktif di Hero:</span>
                                        <span class="fw-semibold text-primary-600"><?= (int) $pf['carousel_cnt'] ?> Item</span>
                                    </div>
                                    <div class="progress h-6-px radius-4 bg-neutral-200 overflow-hidden">
                                        <div class="progress-bar <?= $pctUsed > 80 ? 'bg-warning-main' : 'bg-primary-600' ?>" style="width: <?= $pctUsed ?>%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary-600 btn-sm radius-8 w-100 fw-semibold text-xs py-8" onclick="document.querySelector('<?= $pf['target_tab'] ?>').click();">
                                    Kelola Konten
                                </button>
                                <button type="button" class="btn btn-neutral-100 btn-sm radius-8 px-12 py-8 text-primary-600 text-xs fw-semibold hover-bg-primary-50"
                                        onclick="openSimulatorForPortal('<?= esc($pf['slug']) ?>')" title="Simulasi Tampilan Hero">
                                    <iconify-icon icon="solar:play-bold"></iconify-icon>
                                </button>
                                <a href="<?= esc($pf['url']) ?>" target="_blank" class="btn btn-neutral-200 btn-sm radius-8 px-12 py-8 text-secondary-light text-xs fw-semibold hover-text-primary" title="Lihat di Web">
                                    <iconify-icon icon="solar:link-external-linear"></iconify-icon>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Matrix Table of Active Carousel Items across Portal (Native WowDash Table) -->
        <div class="card basic-data-table mb-24">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h6 class="text-lg fw-semibold text-primary-light mb-1 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:star-rainbow-bold" class="text-warning-main text-xl"></iconify-icon>
                        <span>Daftar Cepat Item Aktif di Hero Carousel Seluruh Portal</span>
                    </h6>
                    <p class="text-secondary-light text-xs mb-0">Semua item yang saat ini aktif tampil di hero banner atau berstatus unggulan prioritas.</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <div class="position-relative">
                        <input type="text" class="form-control form-control-sm radius-8 table-quick-search pe-36" placeholder="Cari di semua slider..." style="width: 220px;">
                        <button type="button" class="btn p-0 position-absolute end-0 top-50 translate-middle-y me-10 text-secondary-light search-clear-btn d-none border-0 bg-transparent">
                            <iconify-icon icon="solar:close-circle-bold" class="text-sm"></iconify-icon>
                        </button>
                    </div>
                    <span class="text-xs text-secondary-light fw-medium search-count-badge">Menampilkan <strong class="text-primary-light count-num">0</strong> item</span>
                </div>
            </div>

            <!-- Filter Pills Toolbar for Overview -->
            <div class="card-body p-20 pt-16 border-bottom bg-neutral-50 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex flex-wrap align-items-center gap-2 filter-pills-bar">
                    <button type="button" class="btn btn-sm btn-primary-600 radius-8 px-12 py-6 text-xs fw-semibold filter-pill active" data-filter="all">Semua Portal</button>
                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold filter-pill" data-filter="arena">Arena Belajar</button>
                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold filter-pill" data-filter="knowledge">Bunga Rampai</button>
                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold filter-pill" data-filter="regulation">Regulasi PBJ</button>
                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold filter-pill" data-filter="news">Berita</button>
                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold filter-pill" data-filter="event">Event &amp; Agenda</button>
                </div>
            </div>

            <div class="card-body p-24">
                <div class="table-responsive">
                    <table class="table bordered-table mb-0 filterable-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Cover</th>
                                <th>Judul &amp; Informasi</th>
                                <th style="width: 150px;">Modul / Fitur</th>
                                <th style="width: 130px;" class="text-center">Status Slider</th>
                                <th style="width: 120px;" class="text-center">Unggulan</th>
                                <th style="width: 100px;" class="text-center">Urutan</th>
                                <th style="width: 110px;" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $allActive = array_merge(
                                array_filter($programs, fn($i) => ! empty($i['carousel_enabled']) || ! empty($i['is_featured'])),
                                array_filter($courses, fn($i) => ! empty($i['carousel_enabled']) || ! empty($i['is_featured'])),
                                array_filter($classes, fn($i) => ! empty($i['carousel_enabled']) || ! empty($i['is_featured'])),
                                array_filter($knowledgeArticles, fn($i) => ! empty($i['carousel_enabled']) || ! empty($i['is_featured'])),
                                array_filter($knowledgeMedia, fn($i) => ! empty($i['carousel_enabled']) || ! empty($i['is_featured'])),
                                array_filter($regulations, fn($i) => ! empty($i['carousel_enabled']) || ! empty($i['is_featured'])),
                                array_filter($news, fn($i) => ! empty($i['carousel_enabled']) || ! empty($i['is_featured'])),
                                array_filter($events, fn($i) => ! empty($i['carousel_enabled']) || ! empty($i['is_featured']))
                            );
                            ?>
                            <?php if (empty($allActive)): ?>
                                <tr class="empty-state-row">
                                    <td colspan="7" class="text-center py-40">
                                        <div class="text-secondary-light mb-8"><iconify-icon icon="solar:slider-vertical-line-duotone" class="text-4xl"></iconify-icon></div>
                                        <h6 class="fw-semibold text-primary-light mb-1">Belum Ada Item Terpilih di Hero Carousel</h6>
                                        <p class="text-secondary-light text-xs mb-0">Pilih tab modul di atas untuk mengaktifkan switch slider pada konten yang diinginkan.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($allActive as $item): ?>
                                    <?= view('dashboard/landing/spotlight_hub/item_row', ['item' => $item]) ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- TAB 2: ARENA BELAJAR -->
    <div class="tab-pane fade <?= $activeTab === 'arena' ? 'show active' : '' ?>" id="tab-arena" role="tabpanel">
        <div class="card basic-data-table mb-24">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h5 class="card-title text-md fw-semibold text-primary-light mb-1 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:graduation-cap-bold" class="text-primary-600 text-xl"></iconify-icon>
                        <span>Sorotan Hero Slider Arena Belajar</span>
                    </h5>
                    <p class="text-secondary-light text-xs mb-0">Pilih program, course mandiri, atau kelas batch yang ingin ditampilkan pada banner hero <code>/arena-belajar</code>.</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <div class="position-relative">
                        <input type="text" class="form-control form-control-sm radius-8 table-quick-search pe-36" placeholder="Cari di Arena Belajar..." style="width: 220px;">
                        <button type="button" class="btn p-0 position-absolute end-0 top-50 translate-middle-y me-10 text-secondary-light search-clear-btn d-none border-0 bg-transparent">
                            <iconify-icon icon="solar:close-circle-bold" class="text-sm"></iconify-icon>
                        </button>
                    </div>
                    <span class="text-xs text-secondary-light fw-medium search-count-badge">Menampilkan <strong class="text-primary-light count-num">0</strong> item</span>
                    <button type="button" class="btn btn-outline-primary-600 btn-sm radius-8 px-12 py-8 fw-semibold text-xs d-flex align-items-center gap-1"
                            onclick="openDiscoveryForFeature('arena')">
                        <iconify-icon icon="solar:add-circle-bold"></iconify-icon> Tambah Konten
                    </button>
                    <a href="<?= site_url('arena-belajar') ?>" target="_blank" class="btn btn-neutral-200 text-secondary-light btn-sm radius-8 px-12 py-8 fw-semibold text-xs d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:link-external-linear"></iconify-icon> Preview Web
                    </a>
                </div>
            </div>

            <!-- Filter Pills Toolbar for Arena -->
            <div class="card-body p-20 pt-16 border-bottom bg-neutral-50 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex flex-wrap align-items-center gap-2 filter-pills-bar">
                    <button type="button" class="btn btn-sm btn-primary-600 radius-8 px-12 py-6 text-xs fw-semibold filter-pill active" data-filter="all">Semua Konten</button>
                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold filter-pill" data-filter="carousel_only">Hanya Slider Aktif</button>
                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold filter-pill" data-filter="featured_only">Hanya Unggulan</button>
                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold filter-pill" data-filter="program">Program</button>
                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold filter-pill" data-filter="course">Course</button>
                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold filter-pill" data-filter="class">Kelas Batch</button>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown">
                        <iconify-icon icon="solar:tuning-bold"></iconify-icon> Aksi Modul
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end radius-8 shadow border-0 py-8 text-xs">
                        <li>
                            <button class="dropdown-item py-6 text-danger-600 d-flex align-items-center gap-2" onclick="triggerBulkAction('arena', 'disable_all_carousel')">
                                <iconify-icon icon="solar:close-circle-bold"></iconify-icon> Nonaktifkan Semua Slider
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item py-6 text-secondary-light d-flex align-items-center gap-2" onclick="triggerBulkAction('arena', 'reset_order')">
                                <iconify-icon icon="solar:restart-bold"></iconify-icon> Reset Nomor Urutan
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card-body p-24">
                <div class="table-responsive">
                    <table class="table bordered-table mb-0 filterable-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Cover</th>
                                <th>Judul &amp; Informasi Program / Course / Kelas</th>
                                <th style="width: 150px;">Tipe Konten</th>
                                <th style="width: 130px;" class="text-center">Tampil di Slider</th>
                                <th style="width: 120px;" class="text-center">Unggulan</th>
                                <th style="width: 100px;" class="text-center">Urutan</th>
                                <th style="width: 110px;" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $arenaRows = array_merge($programs, $courses, $classes); ?>
                            <?php if (empty($arenaRows)): ?>
                                <tr class="empty-state-row">
                                    <td colspan="7" class="text-center py-40">
                                        <div class="text-secondary-light mb-8"><iconify-icon icon="solar:graduation-cap-line-duotone" class="text-4xl"></iconify-icon></div>
                                        <h6 class="fw-semibold text-primary-light mb-1">Belum Ada Item Arena Belajar di Slider atau Unggulan</h6>
                                        <p class="text-secondary-light text-xs mb-3">Klik tombol "Tambah Konten" di atas untuk mencari dan mengaktifkan Slider atau Sorotan Unggulan.</p>
                                        <button type="button" class="btn btn-sm btn-primary-600 radius-8 px-14 py-6 text-xs fw-semibold" onclick="openDiscoveryForFeature('arena')">
                                            <iconify-icon icon="solar:add-circle-bold"></iconify-icon> Tambah Konten Arena Belajar
                                        </button>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($arenaRows as $item): ?>
                                    <?= view('dashboard/landing/spotlight_hub/item_row', ['item' => $item]) ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: BUNGA RAMPAI -->
    <div class="tab-pane fade <?= $activeTab === 'knowledge' ? 'show active' : '' ?>" id="tab-knowledge" role="tabpanel">
        <div class="card basic-data-table mb-24">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h5 class="card-title text-md fw-semibold text-primary-light mb-1 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:library-bold" class="text-info-600 text-xl"></iconify-icon>
                        <span>Sorotan Hero Slider Bunga Rampai</span>
                    </h5>
                    <p class="text-secondary-light text-xs mb-0">Pilih artikel PBJ, studi kasus, template dokumen, atau slide multimedia yang tampil pada hero banner <code>/bunga-rampai</code>.</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <div class="position-relative">
                        <input type="text" class="form-control form-control-sm radius-8 table-quick-search pe-36" placeholder="Cari di Bunga Rampai..." style="width: 220px;">
                        <button type="button" class="btn p-0 position-absolute end-0 top-50 translate-middle-y me-10 text-secondary-light search-clear-btn d-none border-0 bg-transparent">
                            <iconify-icon icon="solar:close-circle-bold" class="text-sm"></iconify-icon>
                        </button>
                    </div>
                    <span class="text-xs text-secondary-light fw-medium search-count-badge">Menampilkan <strong class="text-primary-light count-num">0</strong> item</span>
                    <button type="button" class="btn btn-outline-info-600 btn-sm radius-8 px-12 py-8 fw-semibold text-xs d-flex align-items-center gap-1"
                            onclick="openDiscoveryForFeature('knowledge')">
                        <iconify-icon icon="solar:add-circle-bold"></iconify-icon> Tambah Konten
                    </button>
                    <a href="<?= site_url('bunga-rampai') ?>" target="_blank" class="btn btn-neutral-200 text-secondary-light btn-sm radius-8 px-12 py-8 fw-semibold text-xs d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:link-external-linear"></iconify-icon> Preview Web
                    </a>
                </div>
            </div>

            <!-- Filter Pills Toolbar for Bunga Rampai -->
            <div class="card-body p-20 pt-16 border-bottom bg-neutral-50 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex flex-wrap align-items-center gap-2 filter-pills-bar">
                    <button type="button" class="btn btn-sm btn-primary-600 radius-8 px-12 py-6 text-xs fw-semibold filter-pill active" data-filter="all">Semua Konten</button>
                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold filter-pill" data-filter="carousel_only">Hanya Slider Aktif</button>
                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold filter-pill" data-filter="featured_only">Hanya Unggulan</button>
                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold filter-pill" data-filter="knowledge_article">Dokumen / Artikel</button>
                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold filter-pill" data-filter="knowledge_media">Multimedia Slide</button>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown">
                        <iconify-icon icon="solar:tuning-bold"></iconify-icon> Aksi Modul
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end radius-8 shadow border-0 py-8 text-xs">
                        <li>
                            <button class="dropdown-item py-6 text-danger-600 d-flex align-items-center gap-2" onclick="triggerBulkAction('knowledge', 'disable_all_carousel')">
                                <iconify-icon icon="solar:close-circle-bold"></iconify-icon> Nonaktifkan Semua Slider
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item py-6 text-secondary-light d-flex align-items-center gap-2" onclick="triggerBulkAction('knowledge', 'reset_order')">
                                <iconify-icon icon="solar:restart-bold"></iconify-icon> Reset Nomor Urutan
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card-body p-24">
                <div class="table-responsive">
                    <table class="table bordered-table mb-0 filterable-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Cover</th>
                                <th>Judul &amp; Informasi Dokumen / Multimedia</th>
                                <th style="width: 150px;">Tipe Konten</th>
                                <th style="width: 130px;" class="text-center">Tampil di Slider</th>
                                <th style="width: 120px;" class="text-center">Unggulan</th>
                                <th style="width: 100px;" class="text-center">Urutan</th>
                                <th style="width: 110px;" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $knowledgeRows = array_merge($knowledgeArticles, $knowledgeMedia); ?>
                            <?php if (empty($knowledgeRows)): ?>
                                <tr class="empty-state-row">
                                    <td colspan="7" class="text-center py-40">
                                        <div class="text-secondary-light mb-8"><iconify-icon icon="solar:library-line-duotone" class="text-4xl"></iconify-icon></div>
                                        <h6 class="fw-semibold text-primary-light mb-1">Belum Ada Item Bunga Rampai di Slider atau Unggulan</h6>
                                        <p class="text-secondary-light text-xs mb-3">Klik tombol "Tambah Konten" di atas untuk mencari artikel, studi kasus, template, atau multimedia.</p>
                                        <button type="button" class="btn btn-sm btn-outline-info-600 radius-8 px-14 py-6 text-xs fw-semibold" onclick="openDiscoveryForFeature('knowledge')">
                                            <iconify-icon icon="solar:add-circle-bold"></iconify-icon> Tambah Konten Bunga Rampai
                                        </button>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($knowledgeRows as $item): ?>
                                    <?= view('dashboard/landing/spotlight_hub/item_row', ['item' => $item]) ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 4: REGULASI PBJ -->
    <div class="tab-pane fade <?= $activeTab === 'regulation' ? 'show active' : '' ?>" id="tab-regulation" role="tabpanel">
        <div class="card basic-data-table mb-24">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h5 class="card-title text-md fw-semibold text-primary-light mb-1 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:document-text-bold" class="text-success-600 text-xl"></iconify-icon>
                        <span>Sorotan Hero Slider Regulasi PBJ</span>
                    </h5>
                    <p class="text-secondary-light text-xs mb-0">Pilih dokumen hukum, peraturan presiden, atau regulasi LKPP yang diutamakan pada headline carousel <code>/regulasi</code>.</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <div class="position-relative">
                        <input type="text" class="form-control form-control-sm radius-8 table-quick-search pe-36" placeholder="Cari peraturan..." style="width: 220px;">
                        <button type="button" class="btn p-0 position-absolute end-0 top-50 translate-middle-y me-10 text-secondary-light search-clear-btn d-none border-0 bg-transparent">
                            <iconify-icon icon="solar:close-circle-bold" class="text-sm"></iconify-icon>
                        </button>
                    </div>
                    <span class="text-xs text-secondary-light fw-medium search-count-badge">Menampilkan <strong class="text-primary-light count-num">0</strong> item</span>
                    <button type="button" class="btn btn-outline-success-600 btn-sm radius-8 px-12 py-8 fw-semibold text-xs d-flex align-items-center gap-1"
                            onclick="openDiscoveryForFeature('regulation')">
                        <iconify-icon icon="solar:add-circle-bold"></iconify-icon> Tambah Konten
                    </button>
                    <a href="<?= site_url('regulasi') ?>" target="_blank" class="btn btn-neutral-200 text-secondary-light btn-sm radius-8 px-12 py-8 fw-semibold text-xs d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:link-external-linear"></iconify-icon> Preview Web
                    </a>
                </div>
            </div>

            <!-- Filter Pills Toolbar for Regulation -->
            <div class="card-body p-20 pt-16 border-bottom bg-neutral-50 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex flex-wrap align-items-center gap-2 filter-pills-bar">
                    <button type="button" class="btn btn-sm btn-primary-600 radius-8 px-12 py-6 text-xs fw-semibold filter-pill active" data-filter="all">Semua Regulasi</button>
                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold filter-pill" data-filter="carousel_only">Hanya Slider Aktif</button>
                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold filter-pill" data-filter="featured_only">Hanya Sorotan Utama</button>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown">
                        <iconify-icon icon="solar:tuning-bold"></iconify-icon> Aksi Modul
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end radius-8 shadow border-0 py-8 text-xs">
                        <li>
                            <button class="dropdown-item py-6 text-danger-600 d-flex align-items-center gap-2" onclick="triggerBulkAction('regulation', 'disable_all_carousel')">
                                <iconify-icon icon="solar:close-circle-bold"></iconify-icon> Nonaktifkan Semua Slider
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item py-6 text-secondary-light d-flex align-items-center gap-2" onclick="triggerBulkAction('regulation', 'reset_order')">
                                <iconify-icon icon="solar:restart-bold"></iconify-icon> Reset Nomor Urutan
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card-body p-24">
                <div class="table-responsive">
                    <table class="table bordered-table mb-0 filterable-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Cover</th>
                                <th>Judul Peraturan &amp; Tentang</th>
                                <th style="width: 150px;">Kategori Hukum</th>
                                <th style="width: 130px;" class="text-center">Tampil di Slider</th>
                                <th style="width: 120px;" class="text-center">Sorotan Utama</th>
                                <th style="width: 100px;" class="text-center">Urutan</th>
                                <th style="width: 110px;" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($regulations)): ?>
                                <tr class="empty-state-row">
                                    <td colspan="7" class="text-center py-40">
                                        <div class="text-secondary-light mb-8"><iconify-icon icon="solar:document-text-line-duotone" class="text-4xl"></iconify-icon></div>
                                        <h6 class="fw-semibold text-primary-light mb-1">Belum Ada Item Regulasi di Slider atau Sorotan Utama</h6>
                                        <p class="text-secondary-light text-xs mb-3">Klik tombol "Tambah Konten" di atas untuk mencari dan mengaktifkan dokumen regulasi.</p>
                                        <button type="button" class="btn btn-sm btn-outline-success-600 radius-8 px-14 py-6 text-xs fw-semibold" onclick="openDiscoveryForFeature('regulation')">
                                            <iconify-icon icon="solar:add-circle-bold"></iconify-icon> Tambah Konten Regulasi
                                        </button>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($regulations as $item): ?>
                                    <?= view('dashboard/landing/spotlight_hub/item_row', ['item' => $item]) ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 5: BERITA & ARTIKEL -->
    <div class="tab-pane fade <?= $activeTab === 'news' ? 'show active' : '' ?>" id="tab-news" role="tabpanel">
        <div class="card basic-data-table mb-24">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h5 class="card-title text-md fw-semibold text-primary-light mb-1 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:newspaper-bold" class="text-purple text-xl"></iconify-icon>
                        <span>Sorotan Hero Slider Berita &amp; Warta</span>
                    </h5>
                    <p class="text-secondary-light text-xs mb-0">Pilih siaran pers atau warta prioritas yang ditampilkan pada slider headline majalah teratas <code>/berita</code>.</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <div class="position-relative">
                        <input type="text" class="form-control form-control-sm radius-8 table-quick-search pe-36" placeholder="Cari warta berita..." style="width: 220px;">
                        <button type="button" class="btn p-0 position-absolute end-0 top-50 translate-middle-y me-10 text-secondary-light search-clear-btn d-none border-0 bg-transparent">
                            <iconify-icon icon="solar:close-circle-bold" class="text-sm"></iconify-icon>
                        </button>
                    </div>
                    <span class="text-xs text-secondary-light fw-medium search-count-badge">Menampilkan <strong class="text-primary-light count-num">0</strong> item</span>
                    <button type="button" class="btn btn-outline-primary-600 btn-sm radius-8 px-12 py-8 fw-semibold text-xs d-flex align-items-center gap-1"
                            onclick="openDiscoveryForFeature('news')">
                        <iconify-icon icon="solar:add-circle-bold"></iconify-icon> Tambah Konten
                    </button>
                    <a href="<?= site_url('berita') ?>" target="_blank" class="btn btn-neutral-200 text-secondary-light btn-sm radius-8 px-12 py-8 fw-semibold text-xs d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:link-external-linear"></iconify-icon> Preview Web
                    </a>
                </div>
            </div>

            <!-- Filter Pills Toolbar for News -->
            <div class="card-body p-20 pt-16 border-bottom bg-neutral-50 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex flex-wrap align-items-center gap-2 filter-pills-bar">
                    <button type="button" class="btn btn-sm btn-primary-600 radius-8 px-12 py-6 text-xs fw-semibold filter-pill active" data-filter="all">Semua Berita</button>
                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold filter-pill" data-filter="carousel_only">Hanya Slider Aktif</button>
                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold filter-pill" data-filter="featured_only">Hanya Berita Utama</button>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown">
                        <iconify-icon icon="solar:tuning-bold"></iconify-icon> Aksi Modul
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end radius-8 shadow border-0 py-8 text-xs">
                        <li>
                            <button class="dropdown-item py-6 text-danger-600 d-flex align-items-center gap-2" onclick="triggerBulkAction('news', 'disable_all_carousel')">
                                <iconify-icon icon="solar:close-circle-bold"></iconify-icon> Nonaktifkan Semua Slider
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item py-6 text-secondary-light d-flex align-items-center gap-2" onclick="triggerBulkAction('news', 'reset_order')">
                                <iconify-icon icon="solar:restart-bold"></iconify-icon> Reset Nomor Urutan
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card-body p-24">
                <div class="table-responsive">
                    <table class="table bordered-table mb-0 filterable-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Cover</th>
                                <th>Judul Berita &amp; Ringkasan</th>
                                <th style="width: 150px;">Kategori Berita</th>
                                <th style="width: 130px;" class="text-center">Tampil di Slider</th>
                                <th style="width: 120px;" class="text-center">Berita Utama</th>
                                <th style="width: 100px;" class="text-center">Urutan</th>
                                <th style="width: 110px;" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($news)): ?>
                                <tr class="empty-state-row">
                                    <td colspan="7" class="text-center py-40">
                                        <div class="text-secondary-light mb-8"><iconify-icon icon="solar:newspaper-line-duotone" class="text-4xl"></iconify-icon></div>
                                        <h6 class="fw-semibold text-primary-light mb-1">Belum Ada Berita di Slider atau Berita Utama</h6>
                                        <p class="text-secondary-light text-xs mb-3">Klik tombol "Tambah Konten" di atas untuk mencari dan mengaktifkan warta berita.</p>
                                        <button type="button" class="btn btn-sm btn-outline-primary-600 radius-8 px-14 py-6 text-xs fw-semibold" onclick="openDiscoveryForFeature('news')">
                                            <iconify-icon icon="solar:add-circle-bold"></iconify-icon> Tambah Konten Berita
                                        </button>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($news as $item): ?>
                                    <?= view('dashboard/landing/spotlight_hub/item_row', ['item' => $item]) ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 6: EVENT & AGENDA -->
    <div class="tab-pane fade <?= $activeTab === 'event' ? 'show active' : '' ?>" id="tab-event" role="tabpanel">
        <div class="card basic-data-table mb-24">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h5 class="card-title text-md fw-semibold text-primary-light mb-1 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:calendar-bold" class="text-warning-main text-xl"></iconify-icon>
                        <span>Sorotan Hero Slider Event &amp; Agenda</span>
                    </h5>
                    <p class="text-secondary-light text-xs mb-0">Pilih agenda kegiatan, webinar nasional, atau workshop PBJ yang tampil pada headline carousel <code>/event</code>.</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <div class="position-relative">
                        <input type="text" class="form-control form-control-sm radius-8 table-quick-search pe-36" placeholder="Cari kegiatan..." style="width: 220px;">
                        <button type="button" class="btn p-0 position-absolute end-0 top-50 translate-middle-y me-10 text-secondary-light search-clear-btn d-none border-0 bg-transparent">
                            <iconify-icon icon="solar:close-circle-bold" class="text-sm"></iconify-icon>
                        </button>
                    </div>
                    <span class="text-xs text-secondary-light fw-medium search-count-badge">Menampilkan <strong class="text-primary-light count-num">0</strong> item</span>
                    <button type="button" class="btn btn-outline-warning-600 btn-sm radius-8 px-12 py-8 fw-semibold text-xs d-flex align-items-center gap-1"
                            onclick="openDiscoveryForFeature('event')">
                        <iconify-icon icon="solar:add-circle-bold"></iconify-icon> Tambah Konten
                    </button>
                    <a href="<?= site_url('event') ?>" target="_blank" class="btn btn-neutral-200 text-secondary-light btn-sm radius-8 px-12 py-8 fw-semibold text-xs d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:link-external-linear"></iconify-icon> Preview Web
                    </a>
                </div>
            </div>

            <!-- Filter Pills Toolbar for Event -->
            <div class="card-body p-20 pt-16 border-bottom bg-neutral-50 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex flex-wrap align-items-center gap-2 filter-pills-bar">
                    <button type="button" class="btn btn-sm btn-primary-600 radius-8 px-12 py-6 text-xs fw-semibold filter-pill active" data-filter="all">Semua Agenda</button>
                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold filter-pill" data-filter="carousel_only">Hanya Slider Aktif</button>
                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold filter-pill" data-filter="featured_only">Hanya Agenda Unggulan</button>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-12 py-6 text-xs fw-semibold dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown">
                        <iconify-icon icon="solar:tuning-bold"></iconify-icon> Aksi Modul
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end radius-8 shadow border-0 py-8 text-xs">
                        <li>
                            <button class="dropdown-item py-6 text-danger-600 d-flex align-items-center gap-2" onclick="triggerBulkAction('event', 'disable_all_carousel')">
                                <iconify-icon icon="solar:close-circle-bold"></iconify-icon> Nonaktifkan Semua Slider
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item py-6 text-secondary-light d-flex align-items-center gap-2" onclick="triggerBulkAction('event', 'reset_order')">
                                <iconify-icon icon="solar:restart-bold"></iconify-icon> Reset Nomor Urutan
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card-body p-24">
                <div class="table-responsive">
                    <table class="table bordered-table mb-0 filterable-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Banner</th>
                                <th>Nama Kegiatan &amp; Jadwal Pelaksanaan</th>
                                <th style="width: 150px;">Tipe &amp; Moda</th>
                                <th style="width: 130px;" class="text-center">Tampil di Slider</th>
                                <th style="width: 120px;" class="text-center">Agenda Unggulan</th>
                                <th style="width: 100px;" class="text-center">Urutan</th>
                                <th style="width: 110px;" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($events)): ?>
                                <tr class="empty-state-row">
                                    <td colspan="7" class="text-center py-40">
                                        <div class="text-secondary-light mb-8"><iconify-icon icon="solar:calendar-line-duotone" class="text-4xl"></iconify-icon></div>
                                        <h6 class="fw-semibold text-primary-light mb-1">Belum Ada Agenda di Slider atau Agenda Unggulan</h6>
                                        <p class="text-secondary-light text-xs mb-3">Klik tombol "Tambah Konten" di atas untuk mencari dan mengaktifkan event / agenda.</p>
                                        <button type="button" class="btn btn-sm btn-outline-warning-600 radius-8 px-14 py-6 text-xs fw-semibold" onclick="openDiscoveryForFeature('event')">
                                            <iconify-icon icon="solar:add-circle-bold"></iconify-icon> Tambah Konten Event &amp; Agenda
                                        </button>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($events as $item): ?>
                                    <?= view('dashboard/landing/spotlight_hub/item_row', ['item' => $item]) ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal 1: Temukan & Tambah Konten ke Slider (AJAX Discovery Modal) -->
<div class="modal fade" id="discoveryModal" tabindex="-1" aria-labelledby="discoveryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="w-40-px h-40-px rounded-circle bg-primary-50 text-primary-600 d-flex align-items-center justify-content-center">
                        <iconify-icon icon="solar:magnifer-zoom-in-bold" class="text-xl"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="modal-title text-md fw-semibold text-primary-light mb-0" id="discoveryModalLabel">Temukan &amp; Tambah Konten ke Slider</h6>
                        <span class="text-secondary-light text-xs">Cari konten terpublikasi lintas modul untuk langsung ditayangkan pada carousel portal.</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <div class="row g-3 mb-20">
                    <div class="col-md-5">
                        <label class="form-label text-xs fw-semibold text-primary-light mb-6">Pilih Kategori Konten:</label>
                        <select class="form-select form-select-sm radius-8" id="discoveryFeatureSelect">
                            <option value="all">Semua Modul Portal</option>
                            <option value="arena">Semua Arena Belajar (Program, Course, Kelas)</option>
                            <optgroup label="Arena Belajar Rinci">
                                <option value="program">Program Pembelajaran</option>
                                <option value="course">Course Mandiri</option>
                                <option value="class">Kelas / Batch Aktif</option>
                            </optgroup>
                            <option value="knowledge">Semua Bunga Rampai (Artikel, Kasus, Template, Media)</option>
                            <optgroup label="Bunga Rampai Rinci">
                                <option value="knowledge_article">Dokumen / Artikel PBJ</option>
                                <option value="knowledge_media">Multimedia Edukasi</option>
                            </optgroup>
                            <option value="regulation">Regulasi PBJ</option>
                            <option value="news">Warta Berita</option>
                            <option value="event">Event &amp; Agenda</option>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label text-xs fw-semibold text-primary-light mb-6">Kata Kunci Pencarian:</label>
                        <div class="position-relative">
                            <input type="text" class="form-control form-control-sm radius-8 pe-36" id="discoverySearchInput" placeholder="Ketik judul materi, nomor perpres, atau nama kegiatan...">
                            <iconify-icon icon="solar:magnifer-linear" class="position-absolute end-0 top-50 translate-middle-y me-10 text-secondary-light"></iconify-icon>
                        </div>
                    </div>
                </div>

                <div id="discoveryLoading" class="text-center py-32 d-none">
                    <div class="spinner-border text-primary-600 mb-8" role="status" style="width: 2rem; height: 2rem;"></div>
                    <p class="text-secondary-light text-xs mb-0">Mencari konten terpublikasi...</p>
                </div>

                <div id="discoveryResults" class="d-flex flex-column gap-2" style="max-height: 400px; overflow-y: auto;">
                    <div class="text-center py-32 text-secondary-light">
                        <iconify-icon icon="solar:magnifer-linear" class="text-4xl mb-8"></iconify-icon>
                        <p class="text-sm mb-0">Ketik kata kunci atau pilih modul untuk mencari konten.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-14 px-24 border-top">
                <button type="button" class="btn btn-outline-neutral-300 text-sm px-20 py-10 radius-8" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: Simulasi Tampilan Hero Carousel Portal Publik -->
<div class="modal fade" id="simulatorModal" tabindex="-1" aria-labelledby="simulatorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="w-40-px h-40-px rounded-circle bg-info-50 text-info-600 d-flex align-items-center justify-content-center">
                        <iconify-icon icon="solar:play-stream-bold" class="text-xl"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="modal-title text-md fw-semibold text-primary-light mb-0" id="simulatorModalLabel">Simulasi Tampilan Hero Carousel Portal</h6>
                        <span class="text-secondary-light text-xs">Pratinjau langsung slide hero yang sedang aktif pada setiap modul publik.</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-20 pb-16 border-bottom">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <label class="text-xs fw-semibold text-primary-light mb-0 me-2">Pilih Portal:</label>
                        <button type="button" class="btn btn-sm btn-primary-600 radius-8 px-14 py-6 text-xs fw-semibold sim-portal-pill active" data-portal="arena">Arena Belajar</button>
                        <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-14 py-6 text-xs fw-semibold sim-portal-pill" data-portal="knowledge">Bunga Rampai</button>
                        <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-14 py-6 text-xs fw-semibold sim-portal-pill" data-portal="regulation">Regulasi PBJ</button>
                        <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-14 py-6 text-xs fw-semibold sim-portal-pill" data-portal="news">Berita</button>
                        <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-14 py-6 text-xs fw-semibold sim-portal-pill" data-portal="event">Event &amp; Agenda</button>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-xs text-secondary-light">Format Layar:</span>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-neutral-300 active sim-device-btn" data-device="desktop" title="Desktop"><iconify-icon icon="solar:laptop-bold"></iconify-icon></button>
                            <button type="button" class="btn btn-outline-neutral-300 sim-device-btn" data-device="tablet" title="Tablet"><iconify-icon icon="solar:tablet-bold"></iconify-icon></button>
                            <button type="button" class="btn btn-outline-neutral-300 sim-device-btn" data-device="mobile" title="Mobile"><iconify-icon icon="solar:smartphone-bold"></iconify-icon></button>
                        </div>
                    </div>
                </div>

                <!-- Simulation Frame -->
                <div class="d-flex justify-content-center">
                    <div id="simulatorStage" class="w-100 position-relative radius-16 overflow-hidden shadow-lg border border-neutral-300 transition-2" style="min-height: 380px; max-width: 100%; background: #0f172a;">
                        
                        <div id="simSlideContainer" class="w-100 h-100 d-flex flex-column justify-content-end p-32 p-sm-40 position-relative" style="min-height: 380px;">
                            <img id="simSlideImg" src="<?= base_url('assets/main/images/event/event-details-top-img-1.jpg') ?>" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover opacity-40" alt="Slide Preview">
                            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(15,23,42,0.3) 0%, rgba(15,23,42,0.95) 100%);"></div>

                            <div class="position-relative z-1 text-white" style="max-width: 680px;">
                                <div class="d-flex align-items-center gap-2 mb-10">
                                    <span id="simSlideBadge" class="badge bg-primary-600 text-white text-xs px-12 py-6 radius-6 fw-bold text-uppercase">PROGRAM</span>
                                    <span id="simSlideSubtitle" class="text-xs text-neutral-300 fw-medium">Modul Unggulan</span>
                                </div>
                                <h3 id="simSlideTitle" class="text-xl text-sm-2xl fw-bold text-white mb-10 text-truncate-2">Judul Hero Slide</h3>
                                <p id="simSlideSummary" class="text-xs text-sm-sm text-neutral-300 mb-16 text-truncate-2" style="max-width: 580px;">Ringkasan konten slide carousel yang ditampilkan kepada pengunjung publik.</p>
                                <div class="d-flex align-items-center gap-3">
                                    <button class="btn btn-primary-600 radius-8 px-20 py-8 text-xs fw-semibold d-inline-flex align-items-center gap-2" id="simSlideCta">
                                        <span>Buka Halaman</span>
                                        <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                                    </button>
                                </div>
                            </div>

                            <!-- Carousel Nav Arrows -->
                            <button type="button" class="btn btn-dark bg-opacity-60 text-white position-absolute top-50 start-0 translate-middle-y ms-16 radius-circle p-10 d-flex align-items-center justify-content-center z-2 hover-bg-opacity-90" id="simPrevBtn">
                                <iconify-icon icon="solar:alt-arrow-left-bold" class="text-lg"></iconify-icon>
                            </button>
                            <button type="button" class="btn btn-dark bg-opacity-60 text-white position-absolute top-50 end-0 translate-middle-y me-16 radius-circle p-10 d-flex align-items-center justify-content-center z-2 hover-bg-opacity-90" id="simNextBtn">
                                <iconify-icon icon="solar:alt-arrow-right-bold" class="text-lg"></iconify-icon>
                            </button>

                            <!-- Dots Indicator -->
                            <div class="position-absolute bottom-0 start-50 translate-middle-x mb-16 d-flex align-items-center gap-2 z-2" id="simDotsContainer">
                            </div>
                        </div>

                        <!-- Empty State in Simulation -->
                        <div id="simEmptyState" class="position-absolute top-0 start-0 w-100 h-100 d-none flex-column align-items-center justify-content-center text-center p-32 text-white">
                            <iconify-icon icon="solar:slider-vertical-bold" class="text-5xl text-neutral-500 mb-12"></iconify-icon>
                            <h6 class="text-white fw-semibold mb-4">Tidak Ada Slide Aktif untuk Modul Ini</h6>
                            <p class="text-neutral-400 text-xs mb-16">Aktifkan switch "Tampil di Slider" pada modul ini agar slide muncul di simulasi.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-14 px-24 border-top">
                <button type="button" class="btn btn-outline-neutral-300 text-sm px-20 py-10 radius-8" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 3: Lightbox Image Preview Modal -->
<div class="modal fade" id="spotlightImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="card radius-16 overflow-hidden bg-base border border-neutral-300">
                <div class="card-header py-12 px-20 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <span id="spotlightImgBadge" class="badge bg-primary-100 text-primary-600 text-xs px-10 py-4 radius-4 fw-medium mb-1">Cover</span>
                        <h6 id="spotlightImgTitle" class="text-sm fw-semibold text-primary-light mb-0">Judul Cover</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="card-body p-0 bg-dark text-center" style="max-height: 520px; overflow: hidden;">
                    <img id="spotlightImgTag" src="" alt="Cover Full" class="w-100 h-100 object-fit-contain" style="max-height: 520px;">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal 4: Konfigurasi Mode & Batas Slide Lintas Fitur (Native WowDash Modal) -->
<div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="w-40-px h-40-px rounded-circle bg-primary-50 text-primary-600 d-flex align-items-center justify-content-center">
                        <iconify-icon icon="solar:settings-bold" class="text-xl"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="modal-title text-md fw-semibold text-primary-light mb-0" id="settingsModalLabel">Konfigurasi Mode Slider &amp; Kuota Hero</h6>
                        <span class="text-secondary-light text-xs">Atur kurasi default dan batas slide maksimal per modul landing page.</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <div class="accordion" id="settingsAccordion">
                    
                    <!-- 1. Arena Belajar -->
                    <div class="accordion-item border border-neutral-200 radius-8 mb-12 overflow-hidden">
                        <h2 class="accordion-header" id="headingArena">
                            <button class="accordion-button fw-semibold text-primary-light py-14 px-18 bg-neutral-50" type="button" data-bs-toggle="collapse" data-bs-target="#collapseArena" aria-expanded="true">
                                <iconify-icon icon="solar:graduation-cap-bold" class="text-primary-600 me-2 text-lg"></iconify-icon>
                                Mode Slider Arena Belajar
                            </button>
                        </h2>
                        <div id="collapseArena" class="accordion-collapse collapse show">
                            <div class="accordion-body p-18 bg-base">
                                <form method="post" action="<?= site_url('dashboard/landing/sorotan-carousel/pengaturan') ?>" class="ajax-settings-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="group" value="learning">
                                    <div class="row gy-3">
                                        <div class="col-md-7">
                                            <label class="form-label text-xs fw-semibold text-primary-light">Mode Kurasi Sorotan:</label>
                                            <select name="spotlight_mode" class="form-select form-select-sm radius-8">
                                                <option value="all" <?= ($settings['learning']['mode'] === 'all') ? 'selected' : '' ?>>Semua (Kombinasi Program, Course, dan Kelas)</option>
                                                <option value="program" <?= ($settings['learning']['mode'] === 'program') ? 'selected' : '' ?>>Khusus Program Pembelajaran</option>
                                                <option value="course" <?= ($settings['learning']['mode'] === 'course') ? 'selected' : '' ?>>Khusus Course Mandiri</option>
                                                <option value="class" <?= ($settings['learning']['mode'] === 'class') ? 'selected' : '' ?>>Khusus Kelas Aktif</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label text-xs fw-semibold text-primary-light">Batas Slide (Limit):</label>
                                            <input type="number" min="3" max="15" name="spotlight_limit" class="form-control form-control-sm radius-8" value="<?= (int) $settings['learning']['limit'] ?>" required>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary-600 btn-sm radius-8 w-100 py-8 fw-semibold">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Bunga Rampai -->
                    <div class="accordion-item border border-neutral-200 radius-8 mb-12 overflow-hidden">
                        <h2 class="accordion-header" id="headingKnowledge">
                            <button class="accordion-button collapsed fw-semibold text-primary-light py-14 px-18 bg-neutral-50" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKnowledge">
                                <iconify-icon icon="solar:library-bold" class="text-info-600 me-2 text-lg"></iconify-icon>
                                Mode Slider Bunga Rampai
                            </button>
                        </h2>
                        <div id="collapseKnowledge" class="accordion-collapse collapse">
                            <div class="accordion-body p-18 bg-base">
                                <form method="post" action="<?= site_url('dashboard/landing/sorotan-carousel/pengaturan') ?>" class="ajax-settings-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="group" value="knowledge">
                                    <div class="row gy-3">
                                        <div class="col-md-7">
                                            <label class="form-label text-xs fw-semibold text-primary-light">Mode Kurasi Sorotan:</label>
                                            <select name="spotlight_mode" class="form-select form-select-sm radius-8">
                                                <option value="all" <?= ($settings['knowledge']['mode'] === 'all') ? 'selected' : '' ?>>Semua (Artikel, Studi Kasus, Template, &amp; Multimedia)</option>
                                                <option value="article" <?= ($settings['knowledge']['mode'] === 'article') ? 'selected' : '' ?>>Khusus Artikel PBJ</option>
                                                <option value="case_study" <?= ($settings['knowledge']['mode'] === 'case_study') ? 'selected' : '' ?>>Khusus Studi Kasus</option>
                                                <option value="template" <?= ($settings['knowledge']['mode'] === 'template') ? 'selected' : '' ?>>Khusus Template Dokumen</option>
                                                <option value="multimedia" <?= ($settings['knowledge']['mode'] === 'multimedia') ? 'selected' : '' ?>>Khusus Slide Multimedia</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label text-xs fw-semibold text-primary-light">Batas Slide (Limit):</label>
                                            <input type="number" min="3" max="15" name="spotlight_limit" class="form-control form-control-sm radius-8" value="<?= (int) $settings['knowledge']['limit'] ?>" required>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary-600 btn-sm radius-8 w-100 py-8 fw-semibold">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Regulasi PBJ -->
                    <div class="accordion-item border border-neutral-200 radius-8 mb-12 overflow-hidden">
                        <h2 class="accordion-header" id="headingReg">
                            <button class="accordion-button collapsed fw-semibold text-primary-light py-14 px-18 bg-neutral-50" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReg">
                                <iconify-icon icon="solar:document-text-bold" class="text-success-600 me-2 text-lg"></iconify-icon>
                                Mode Slider Regulasi PBJ
                            </button>
                        </h2>
                        <div id="collapseReg" class="accordion-collapse collapse">
                            <div class="accordion-body p-18 bg-base">
                                <form method="post" action="<?= site_url('dashboard/landing/sorotan-carousel/pengaturan') ?>" class="ajax-settings-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="group" value="regulation">
                                    <div class="row gy-3">
                                        <div class="col-md-7">
                                            <label class="form-label text-xs fw-semibold text-primary-light">Mode Kurasi Sorotan:</label>
                                            <select name="spotlight_mode" class="form-select form-select-sm radius-8">
                                                <option value="all" <?= ($settings['regulation']['mode'] === 'all') ? 'selected' : '' ?>>Semua Kategori (Prioritas Sorotan &amp; Populer)</option>
                                                <option value="featured" <?= ($settings['regulation']['mode'] === 'featured') ? 'selected' : '' ?>>Khusus Regulasi Sorotan Utama</option>
                                                <option value="peraturan-presiden" <?= ($settings['regulation']['mode'] === 'peraturan-presiden') ? 'selected' : '' ?>>Khusus Perpres</option>
                                                <option value="peraturan-lkpp" <?= ($settings['regulation']['mode'] === 'peraturan-lkpp') ? 'selected' : '' ?>>Khusus Peraturan LKPP</option>
                                                <option value="peraturan-daerah-gubernur" <?= ($settings['regulation']['mode'] === 'peraturan-daerah-gubernur') ? 'selected' : '' ?>>Khusus Perda / Pergub Lampung</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label text-xs fw-semibold text-primary-light">Batas Slide (Limit):</label>
                                            <input type="number" min="3" max="15" name="spotlight_limit" class="form-control form-control-sm radius-8" value="<?= (int) $settings['regulation']['limit'] ?>" required>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary-600 btn-sm radius-8 w-100 py-8 fw-semibold">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Berita & Artikel -->
                    <div class="accordion-item border border-neutral-200 radius-8 mb-12 overflow-hidden">
                        <h2 class="accordion-header" id="headingNews">
                            <button class="accordion-button collapsed fw-semibold text-primary-light py-14 px-18 bg-neutral-50" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNews">
                                <iconify-icon icon="solar:newspaper-bold" class="text-purple me-2 text-lg"></iconify-icon>
                                Mode Slider Berita &amp; Artikel
                            </button>
                        </h2>
                        <div id="collapseNews" class="accordion-collapse collapse">
                            <div class="accordion-body p-18 bg-base">
                                <form method="post" action="<?= site_url('dashboard/landing/sorotan-carousel/pengaturan') ?>" class="ajax-settings-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="group" value="news">
                                    <div class="row gy-3">
                                        <div class="col-md-7">
                                            <label class="form-label text-xs fw-semibold text-primary-light">Mode Kurasi Sorotan:</label>
                                            <select name="spotlight_mode" class="form-select form-select-sm radius-8">
                                                <option value="all" <?= ($settings['news']['mode'] === 'all') ? 'selected' : '' ?>>Semua Berita (Prioritas Sorotan &amp; Populer)</option>
                                                <option value="featured" <?= ($settings['news']['mode'] === 'featured') ? 'selected' : '' ?>>Khusus Berita Utama (is_featured = 1)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label text-xs fw-semibold text-primary-light">Batas Slide (Limit):</label>
                                            <input type="number" min="3" max="15" name="spotlight_limit" class="form-control form-control-sm radius-8" value="<?= (int) $settings['news']['limit'] ?>" required>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary-600 btn-sm radius-8 w-100 py-8 fw-semibold">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Event & Agenda -->
                    <div class="accordion-item border border-neutral-200 radius-8 overflow-hidden">
                        <h2 class="accordion-header" id="headingEvent">
                            <button class="accordion-button collapsed fw-semibold text-primary-light py-14 px-18 bg-neutral-50" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEvent">
                                <iconify-icon icon="solar:calendar-bold" class="text-warning-main me-2 text-lg"></iconify-icon>
                                Mode Slider Event &amp; Agenda
                            </button>
                        </h2>
                        <div id="collapseEvent" class="accordion-collapse collapse">
                            <div class="accordion-body p-18 bg-base">
                                <form method="post" action="<?= site_url('dashboard/landing/sorotan-carousel/pengaturan') ?>" class="ajax-settings-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="group" value="event">
                                    <div class="row gy-3">
                                        <div class="col-md-7">
                                            <label class="form-label text-xs fw-semibold text-primary-light">Mode Kurasi Sorotan:</label>
                                            <select name="spotlight_mode" class="form-select form-select-sm radius-8">
                                                <option value="all" <?= ($settings['event']['mode'] === 'all') ? 'selected' : '' ?>>Semua Agenda Aktif (Prioritas Sorotan &amp; Terdekat)</option>
                                                <option value="featured" <?= ($settings['event']['mode'] === 'featured') ? 'selected' : '' ?>>Khusus Agenda Unggulan (is_featured = 1)</option>
                                                <option value="upcoming" <?= ($settings['event']['mode'] === 'upcoming') ? 'selected' : '' ?>>Khusus Agenda Mendatang</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label text-xs fw-semibold text-primary-light">Batas Slide (Limit):</label>
                                            <input type="number" min="3" max="15" name="spotlight_limit" class="form-control form-control-sm radius-8" value="<?= (int) $settings['event']['limit'] ?>" required>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary-600 btn-sm radius-8 w-100 py-8 fw-semibold">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer py-14 px-24 border-top">
                <button type="button" class="btn btn-outline-neutral-300 text-sm px-20 py-10 radius-8" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
// JSON Snapshot of all portal items for the live interactive simulator
window.SPOTLIGHT_DATA = {
    arena: <?= json_encode(array_values(array_filter(array_merge($programs, $courses, $classes), fn($i) => !empty($i['carousel_enabled'])))) ?>,
    knowledge: <?= json_encode(array_values(array_filter(array_merge($knowledgeArticles, $knowledgeMedia), fn($i) => !empty($i['carousel_enabled'])))) ?>,
    regulation: <?= json_encode(array_values(array_filter($regulations, fn($i) => !empty($i['carousel_enabled'])))) ?>,
    news: <?= json_encode(array_values(array_filter($news, fn($i) => !empty($i['carousel_enabled'])))) ?>,
    event: <?= json_encode(array_values(array_filter($events, fn($i) => !empty($i['carousel_enabled'])))) ?>
};

document.addEventListener('DOMContentLoaded', function () {
    // -------------------------------------------------------------------------
    // 1. Table Quick Search & Clear Button
    // -------------------------------------------------------------------------
    function updateTableCounts(tabPane) {
        if (!tabPane) return;
        const table = tabPane.querySelector('.filterable-table');
        if (!table) return;
        const visibleRows = Array.from(table.querySelectorAll('tbody tr.spotlight-row')).filter(r => r.style.display !== 'none');
        const countNum = tabPane.querySelector('.search-count-badge .count-num');
        if (countNum) {
            countNum.textContent = visibleRows.length;
        }

        // Show/hide empty state row
        let emptyRow = table.querySelector('.empty-state-row');
        if (visibleRows.length === 0) {
            if (!emptyRow) {
                emptyRow = document.createElement('tr');
                emptyRow.className = 'empty-state-row';
                emptyRow.innerHTML = `
                    <td colspan="7" class="text-center py-32 text-secondary-light">
                        <iconify-icon icon="solar:magnifer-linear" class="text-3xl mb-8"></iconify-icon>
                        <p class="text-sm fw-medium mb-0">Tidak ada konten yang sesuai dengan filter atau kata kunci Anda.</p>
                    </td>`;
                table.querySelector('tbody').appendChild(emptyRow);
            }
            emptyRow.style.display = '';
        } else if (emptyRow) {
            emptyRow.style.display = 'none';
        }
    }

    // Initialize counts for all tabs
    document.querySelectorAll('.tab-pane').forEach(tp => updateTableCounts(tp));

    const searchInputs = document.querySelectorAll('.table-quick-search');
    searchInputs.forEach(input => {
        const clearBtn = input.closest('div').querySelector('.search-clear-btn');

        input.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            if (clearBtn) {
                if (query.length > 0) {
                    clearBtn.classList.remove('d-none');
                } else {
                    clearBtn.classList.add('d-none');
                }
            }
            filterTableRows(this.closest('.tab-pane'));
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                input.value = '';
                clearBtn.classList.add('d-none');
                filterTableRows(input.closest('.tab-pane'));
                input.focus();
            });
        }
    });

    // -------------------------------------------------------------------------
    // 2. Filter Pills Handling
    // -------------------------------------------------------------------------
    document.querySelectorAll('.filter-pill').forEach(pill => {
        pill.addEventListener('click', function () {
            const bar = this.closest('.filter-pills-bar');
            bar.querySelectorAll('.filter-pill').forEach(p => {
                p.classList.remove('active', 'btn-primary-600');
                p.classList.add('btn-outline-neutral-300', 'bg-white', 'text-secondary-light');
            });
            this.classList.add('active', 'btn-primary-600');
            this.classList.remove('btn-outline-neutral-300', 'bg-white', 'text-secondary-light');

            filterTableRows(this.closest('.tab-pane'));
        });
    });

    function filterTableRows(tabPane) {
        if (!tabPane) return;
        const searchInput = tabPane.querySelector('.table-quick-search');
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

        const activePill = tabPane.querySelector('.filter-pill.active');
        const filterVal = activePill ? activePill.dataset.filter : 'all';

        const table = tabPane.querySelector('.filterable-table');
        if (!table) return;

        const rows = table.querySelectorAll('tbody tr.spotlight-row');
        rows.forEach(row => {
            const textMatch = query === '' || row.textContent.toLowerCase().includes(query);
            let pillMatch = true;

            if (filterVal === 'carousel_only') {
                pillMatch = row.dataset.carousel === '1';
            } else if (filterVal === 'featured_only') {
                pillMatch = row.dataset.featured === '1';
            } else if (filterVal !== 'all') {
                // Feature or Feature Group match
                pillMatch = (row.dataset.feature === filterVal || row.dataset.featureGroup === filterVal);
            }

            row.style.display = (textMatch && pillMatch) ? '' : 'none';
        });

        updateTableCounts(tabPane);
    }

    // -------------------------------------------------------------------------
    // 3. AJAX Toggle for Carousel Switch & Featured Switch
    // -------------------------------------------------------------------------
    document.addEventListener('change', function (e) {
        if (!e.target.matches('.spotlight-toggle-switch')) return;

        const toggleEl = e.target;
        const row      = toggleEl.closest('tr');
        const feature  = toggleEl.dataset.feature;
        const id       = toggleEl.dataset.id;
        const field    = toggleEl.dataset.field;
        const val      = toggleEl.checked ? 1 : 0;

        const formData = new FormData();
        formData.append('feature', feature);
        formData.append('id', id);
        formData.append('field', field);
        formData.append('value', val);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        toggleEl.disabled = true;

        fetch('<?= site_url('dashboard/landing/sorotan-carousel/toggle') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            toggleEl.disabled = false;
            if (data.success) {
                showToast(data.message, 'success');
                if (row) {
                    if (field === 'carousel_enabled') {
                        row.dataset.carousel = val ? '1' : '0';
                    } else {
                        row.dataset.featured = val ? '1' : '0';
                    }
                }
            } else {
                toggleEl.checked = !toggleEl.checked;
                showToast(data.message || 'Gagal mengubah status.', 'danger');
            }
        })
        .catch(err => {
            toggleEl.disabled = false;
            toggleEl.checked = !toggleEl.checked;
            showToast('Terjadi kesalahan koneksi server.', 'danger');
        });
    });

    // -------------------------------------------------------------------------
    // 4. AJAX Slide Order Input Update
    // -------------------------------------------------------------------------
    document.addEventListener('change', function (e) {
        if (!e.target.matches('.spotlight-order-input')) return;

        const inputEl = e.target;
        const feature = inputEl.dataset.feature;
        const id      = inputEl.dataset.id;
        const order   = inputEl.value;

        const formData = new FormData();
        formData.append('feature', feature);
        formData.append('id', id);
        formData.append('order', order);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        inputEl.style.opacity = '0.5';

        fetch('<?= site_url('dashboard/landing/sorotan-carousel/update-order') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            inputEl.style.opacity = '1';
            if (data.success) {
                inputEl.classList.add('bg-success-100', 'border-success-400');
                setTimeout(() => inputEl.classList.remove('bg-success-100', 'border-success-400'), 1200);
                showToast(data.message, 'success');
            } else {
                showToast(data.message || 'Gagal memperbarui urutan.', 'danger');
            }
        })
        .catch(err => {
            inputEl.style.opacity = '1';
            showToast('Terjadi kesalahan koneksi server.', 'danger');
        });
    });

    // -------------------------------------------------------------------------
    // 5. Quick Actions ("Jadikan Slide #1", "Keluarkan dari Slider", dll)
    // -------------------------------------------------------------------------
    document.addEventListener('click', function (e) {
        const topBtn = e.target.closest('.btn-quick-top-slide');
        if (topBtn) {
            const feature = topBtn.dataset.feature;
            const id = topBtn.dataset.id;
            const row = topBtn.closest('tr');
            const orderInput = row.querySelector('.spotlight-order-input');
            const toggleSwitch = row.querySelector('.spotlight-toggle-switch[data-field="carousel_enabled"]');

            if (toggleSwitch && !toggleSwitch.checked) {
                toggleSwitch.checked = true;
                toggleSwitch.dispatchEvent(new Event('change'));
            }
            if (orderInput) {
                orderInput.value = '1';
                orderInput.dispatchEvent(new Event('change'));
            }
            return;
        }

        const removeBtn = e.target.closest('.btn-quick-remove-slide');
        if (removeBtn) {
            const row = removeBtn.closest('tr');
            const toggleSwitch = row.querySelector('.spotlight-toggle-switch[data-field="carousel_enabled"]');
            if (toggleSwitch && toggleSwitch.checked) {
                toggleSwitch.checked = false;
                toggleSwitch.dispatchEvent(new Event('change'));
            }
            return;
        }

        const addBtn = e.target.closest('.btn-quick-add-slide');
        if (addBtn) {
            const row = addBtn.closest('tr');
            const toggleSwitch = row.querySelector('.spotlight-toggle-switch[data-field="carousel_enabled"]');
            if (toggleSwitch && !toggleSwitch.checked) {
                toggleSwitch.checked = true;
                toggleSwitch.dispatchEvent(new Event('change'));
            }
            return;
        }
    });

    // -------------------------------------------------------------------------
    // 6. AJAX Settings Forms Submission
    // -------------------------------------------------------------------------
    document.querySelectorAll('.ajax-settings-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (submitBtn) submitBtn.disabled = false;
                if (data.success) {
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'Gagal menyimpan pengaturan.', 'danger');
                }
            })
            .catch(err => {
                if (submitBtn) submitBtn.disabled = false;
                showToast('Terjadi kesalahan koneksi server.', 'danger');
            });
        });
    });

    // -------------------------------------------------------------------------
    // 7. Discovery & Quick Add Modal Logic
    // -------------------------------------------------------------------------
    const discoverySelect = document.getElementById('discoveryFeatureSelect');
    const discoveryInput  = document.getElementById('discoverySearchInput');
    const discoveryLoader = document.getElementById('discoveryLoading');
    const discoveryResults= document.getElementById('discoveryResults');

    let discoveryDebounce = null;

    function runDiscoverySearch() {
        const feature = discoverySelect.value;
        const q       = discoveryInput.value.trim();

        discoveryLoader.classList.remove('d-none');
        discoveryResults.innerHTML = '';

        fetch(`<?= site_url('dashboard/landing/sorotan-carousel/search-available') ?>?feature=${encodeURIComponent(feature)}&q=${encodeURIComponent(q)}`)
            .then(res => res.json())
            .then(data => {
                discoveryLoader.classList.add('d-none');
                if (!data.results || data.results.length === 0) {
                    discoveryResults.innerHTML = `
                        <div class="text-center py-32 text-secondary-light">
                            <iconify-icon icon="solar:magnifer-linear" class="text-3xl mb-8"></iconify-icon>
                            <p class="text-sm mb-0">Tidak ada konten terpublikasi ditemukan.</p>
                        </div>`;
                    return;
                }

                let html = '';
                data.results.forEach(item => {
                    const isCover = item.cover_url && item.cover_url !== '';
                    const fallback = '<?= base_url('assets/main/images/event/event-details-top-img-1.jpg') ?>';
                    const coverSrc = isCover ? item.cover_url : fallback;
                    const isCarousel = item.carousel_enabled == 1;
                    const isFeatured = item.is_featured == 1;

                    html += `
                        <div class="p-12 radius-8 border border-neutral-200 d-flex flex-wrap align-items-center justify-content-between gap-3 bg-white hover-bg-neutral-50 transition-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="w-48-px h-36-px radius-6 overflow-hidden bg-neutral-100 flex-shrink-0 border border-neutral-200">
                                    <img src="${coverSrc}" class="w-100 h-100 object-fit-cover" onerror="this.src='${fallback}'">
                                </div>
                                <div>
                                    <h6 class="text-xs fw-semibold text-primary-light mb-1">${escapeHtml(item.title)}</h6>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="badge text-2xs bg-primary-50 text-primary-600 radius-4 px-6 py-2 fw-medium">${escapeHtml(item.type_label || item.subtitle)}</span>
                                        ${isCarousel ? '<span class="badge text-2xs bg-success-50 text-success-600 border border-success-200 radius-4 px-6 py-2"><span class="w-6-px h-6-px bg-success-main rounded-circle d-inline-block me-1"></span>Slider Aktif</span>' : ''}
                                        ${isFeatured ? '<span class="badge text-2xs bg-warning-50 text-warning-main border border-warning-200 radius-4 px-6 py-2"><iconify-icon icon="solar:star-bold" class="text-xs me-1"></iconify-icon>Unggulan</span>' : ''}
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <button type="button" class="btn btn-sm ${isCarousel ? 'btn-success-100 text-success-600' : 'btn-outline-primary-600'} radius-8 px-10 py-5 text-xs fw-semibold btn-modal-toggle"
                                        data-feature="${item.feature}" data-id="${item.id}" data-field="carousel_enabled" data-current="${isCarousel ? '1' : '0'}"
                                        title="Aktifkan / Nonaktifkan di Hero Slider">
                                    <iconify-icon icon="${isCarousel ? 'solar:check-circle-bold' : 'solar:slider-vertical-bold'}" class="text-sm"></iconify-icon>
                                    <span>${isCarousel ? 'Slider Aktif' : '+ Slider'}</span>
                                </button>
                                <button type="button" class="btn btn-sm ${isFeatured ? 'btn-warning-100 text-warning-main' : 'btn-outline-warning-600'} radius-8 px-10 py-5 text-xs fw-semibold btn-modal-toggle"
                                        data-feature="${item.feature}" data-id="${item.id}" data-field="is_featured" data-current="${isFeatured ? '1' : '0'}"
                                        title="Tandai sebagai Item Unggulan (Hanya Badge)">
                                    <iconify-icon icon="${isFeatured ? 'solar:star-bold' : 'solar:star-outline'}" class="text-sm"></iconify-icon>
                                    <span>${isFeatured ? 'Unggulan' : '+ Unggulan'}</span>
                                </button>
                            </div>
                        </div>`;
                });
                discoveryResults.innerHTML = html;
            })
            .catch(err => {
                discoveryLoader.classList.add('d-none');
                discoveryResults.innerHTML = `<p class="text-danger-600 text-center py-20 text-xs">Gagal memuat data pencarian.</p>`;
            });
    }

    if (discoverySelect) {
        discoverySelect.addEventListener('change', runDiscoverySearch);
    }
    if (discoveryInput) {
        discoveryInput.addEventListener('input', function () {
            clearTimeout(discoveryDebounce);
            discoveryDebounce = setTimeout(runDiscoverySearch, 300);
        });
    }

    let hasChangedInModal = false;
    const discoveryModalEl = document.getElementById('discoveryModal');
    if (discoveryModalEl) {
        discoveryModalEl.addEventListener('hidden.bs.modal', function () {
            if (hasChangedInModal) {
                window.location.reload();
            }
        });
    }

    document.addEventListener('click', function (e) {
        const toggleBtn = e.target.closest('.btn-modal-toggle, .btn-add-spotlight');
        if (!toggleBtn) return;

        const feature = toggleBtn.dataset.feature;
        const id      = toggleBtn.dataset.id;
        const field   = toggleBtn.dataset.field || 'carousel_enabled';
        const current = toggleBtn.dataset.current;
        const newVal  = current === '1' ? 0 : 1;

        toggleBtn.disabled = true;

        const formData = new FormData();
        formData.append('feature', feature);
        formData.append('id', id);
        formData.append('field', field);
        formData.append('value', newVal);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        fetch('<?= site_url('dashboard/landing/sorotan-carousel/toggle') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            toggleBtn.disabled = false;
            if (data.success) {
                hasChangedInModal = true;
                toggleBtn.dataset.current = newVal ? '1' : '0';
                if (field === 'carousel_enabled') {
                    if (newVal === 1) {
                        toggleBtn.className = 'btn btn-sm btn-success-100 text-success-600 radius-8 px-10 py-5 text-xs fw-semibold btn-modal-toggle';
                        toggleBtn.innerHTML = '<iconify-icon icon="solar:check-circle-bold" class="text-sm"></iconify-icon> <span>Slider Aktif</span>';
                    } else {
                        toggleBtn.className = 'btn btn-sm btn-outline-primary-600 radius-8 px-10 py-5 text-xs fw-semibold btn-modal-toggle';
                        toggleBtn.innerHTML = '<iconify-icon icon="solar:slider-vertical-bold" class="text-sm"></iconify-icon> <span>+ Slider</span>';
                    }
                } else {
                    if (newVal === 1) {
                        toggleBtn.className = 'btn btn-sm btn-warning-100 text-warning-main radius-8 px-10 py-5 text-xs fw-semibold btn-modal-toggle';
                        toggleBtn.innerHTML = '<iconify-icon icon="solar:star-bold" class="text-sm"></iconify-icon> <span>Unggulan</span>';
                    } else {
                        toggleBtn.className = 'btn btn-sm btn-outline-warning-600 radius-8 px-10 py-5 text-xs fw-semibold btn-modal-toggle';
                        toggleBtn.innerHTML = '<iconify-icon icon="solar:star-outline" class="text-sm"></iconify-icon> <span>+ Unggulan</span>';
                    }
                }
                showToast(data.message, 'success');
            } else {
                showToast(data.message || 'Gagal mengubah status.', 'danger');
            }
        })
        .catch(err => {
            toggleBtn.disabled = false;
            showToast('Terjadi kesalahan koneksi server.', 'danger');
        });
    });

    window.openDiscoveryForFeature = function (featureKey) {
        if (discoverySelect) {
            discoverySelect.value = featureKey;
        }
        const modalEl = document.getElementById('discoveryModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
            runDiscoverySearch();
        }
    };

    // -------------------------------------------------------------------------
    // 8. Interactive Live Carousel Simulator
    // -------------------------------------------------------------------------
    let simPortal = 'arena';
    let simSlideIndex = 0;

    function renderSimulatorSlide() {
        const slides = window.SPOTLIGHT_DATA[simPortal] || [];
        const stage = document.getElementById('simulatorStage');
        const slideContainer = document.getElementById('simSlideContainer');
        const emptyState = document.getElementById('simEmptyState');
        const dotsContainer = document.getElementById('simDotsContainer');

        if (slides.length === 0) {
            slideContainer.classList.add('d-none');
            emptyState.classList.remove('d-none');
            emptyState.classList.add('d-flex');
            dotsContainer.innerHTML = '';
            return;
        }

        slideContainer.classList.remove('d-none');
        emptyState.classList.add('d-none');
        emptyState.classList.remove('d-flex');

        if (simSlideIndex >= slides.length) simSlideIndex = 0;
        if (simSlideIndex < 0) simSlideIndex = slides.length - 1;

        const current = slides[simSlideIndex];
        const fallback = '<?= base_url('assets/main/images/event/event-details-top-img-1.jpg') ?>';

        document.getElementById('simSlideImg').src = current.cover_url || fallback;
        document.getElementById('simSlideBadge').textContent = current.type_label || 'HERO SLIDE';
        document.getElementById('simSlideSubtitle').textContent = current.subtitle || 'Modul Unggulan';
        document.getElementById('simSlideTitle').textContent = current.title || 'Judul Konten';
        document.getElementById('simSlideSummary').textContent = current.summary || 'Ringkasan konten publikasi portal Begawi.';
        
        const ctaBtn = document.getElementById('simSlideCta');
        if (ctaBtn) {
            ctaBtn.onclick = () => window.open(current.public_url || '#', '_blank');
        }

        // Render Dots
        let dotsHtml = '';
        slides.forEach((_, idx) => {
            const isActive = idx === simSlideIndex;
            dotsHtml += `<button type="button" class="p-0 border-0 radius-circle ${isActive ? 'bg-white w-20-px' : 'bg-white bg-opacity-40 w-8-px'} h-8-px transition-2" onclick="jumpSimSlide(${idx})"></button>`;
        });
        dotsContainer.innerHTML = dotsHtml;
    }

    window.jumpSimSlide = function (idx) {
        simSlideIndex = idx;
        renderSimulatorSlide();
    };

    document.getElementById('simPrevBtn').addEventListener('click', () => {
        simSlideIndex--;
        renderSimulatorSlide();
    });
    document.getElementById('simNextBtn').addEventListener('click', () => {
        simSlideIndex++;
        renderSimulatorSlide();
    });

    document.querySelectorAll('.sim-portal-pill').forEach(pill => {
        pill.addEventListener('click', function () {
            document.querySelectorAll('.sim-portal-pill').forEach(p => {
                p.classList.remove('active', 'btn-primary-600');
                p.classList.add('btn-outline-neutral-300', 'bg-white', 'text-secondary-light');
            });
            this.classList.add('active', 'btn-primary-600');
            this.classList.remove('btn-outline-neutral-300', 'bg-white', 'text-secondary-light');

            simPortal = this.dataset.portal;
            simSlideIndex = 0;
            renderSimulatorSlide();
        });
    });

    document.querySelectorAll('.sim-device-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.sim-device-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const device = this.dataset.device;
            const stage = document.getElementById('simulatorStage');
            if (device === 'mobile') {
                stage.style.maxWidth = '375px';
            } else if (device === 'tablet') {
                stage.style.maxWidth = '768px';
            } else {
                stage.style.maxWidth = '100%';
            }
        });
    });

    window.openSimulatorForPortal = function (portalSlug) {
        const portalPill = document.querySelector(`.sim-portal-pill[data-portal="${portalSlug}"]`);
        if (portalPill) portalPill.click();

        const simModalEl = document.getElementById('simulatorModal');
        if (simModalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(simModalEl);
            modal.show();
        }
    };

    // Initial render when modal opens
    const simulatorModalEl = document.getElementById('simulatorModal');
    if (simulatorModalEl) {
        simulatorModalEl.addEventListener('shown.bs.modal', renderSimulatorSlide);
    }

    // -------------------------------------------------------------------------
    // 9. Lightbox Image Viewer
    // -------------------------------------------------------------------------
    window.openSpotlightImagePreview = function (imgUrl, title, typeLabel) {
        document.getElementById('spotlightImgTag').src = imgUrl;
        document.getElementById('spotlightImgTitle').textContent = title || 'Pratinjau Cover';
        document.getElementById('spotlightImgBadge').textContent = typeLabel || 'Cover';

        const modalEl = document.getElementById('spotlightImageModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    };

    // -------------------------------------------------------------------------
    // 10. Bulk Actions per Module
    // -------------------------------------------------------------------------
    window.triggerBulkAction = function (featureGroup, action) {
        const actionText = action === 'disable_all_carousel' 
            ? 'menonaktifkan seluruh slide hero pada modul ini'
            : 'mereset seluruh urutan nomor slide';

        if (!confirm(`Apakah Anda yakin ingin ${actionText}?`)) {
            return;
        }

        const formData = new FormData();
        formData.append('feature_group', featureGroup);
        formData.append('action', action);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        fetch('<?= site_url('dashboard/landing/sorotan-carousel/bulk-action') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message || 'Gagal memproses aksi masal.', 'danger');
            }
        })
        .catch(err => {
            showToast('Terjadi kesalahan koneksi server.', 'danger');
        });
    };

    // -------------------------------------------------------------------------
    // 11. Native Toast Notification System
    // -------------------------------------------------------------------------
    function showToast(message, type) {
        let container = document.getElementById('spotlightToastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'spotlightToastContainer';
            container.style.position = 'fixed';
            container.style.bottom = '24px';
            container.style.right = '24px';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        const isSuccess = type === 'success';
        const alertClass = isSuccess ? 'bg-success-100 text-success-600 border-success-200' : 'bg-danger-100 text-danger-600 border-danger-200';
        const iconName = isSuccess ? 'solar:check-circle-bold' : 'solar:danger-triangle-bold';

        const toast = document.createElement('div');
        toast.className = `alert ${alertClass} border shadow-lg py-12 px-16 radius-8 d-flex align-items-center gap-2 mb-2 text-sm fw-medium`;
        toast.style.minWidth = '280px';
        toast.innerHTML = `<iconify-icon icon="${iconName}" class="text-xl"></iconify-icon><span>${escapeHtml(message)}</span>`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3200);
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, m => map[m]);
    }
});
</script>
<?= $this->endSection() ?>
