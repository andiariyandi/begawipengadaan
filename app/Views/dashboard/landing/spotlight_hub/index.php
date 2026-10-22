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
                    Kelola seluruh slide hero carousel, item unggulan (<code>is_featured</code>), dan urutan tayang slide untuk <strong>Arena Belajar, Bunga Rampai, Regulasi, Berita, dan Event &amp; Agenda</strong>.
                </p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-3">
                <button type="button" class="btn btn-outline-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#settingsModal">
                    <iconify-icon icon="solar:settings-bold" class="text-xl"></iconify-icon>
                    <span>Konfigurasi Mode Slider</span>
                </button>
                <div class="dropdown">
                    <button class="btn btn-primary-600 radius-8 px-20 py-11 dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <iconify-icon icon="solar:eye-bold" class="text-xl"></iconify-icon>
                        <span>Pratinjau Slider Publik</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end radius-8 shadow border-0 py-8">
                        <li><h6 class="dropdown-header text-secondary-light text-xs fw-semibold text-uppercase">Buka Halaman Publik</h6></li>
                        <li><a class="dropdown-item py-8 text-sm fw-medium d-flex align-items-center gap-2" href="<?= site_url('arena-belajar') ?>" target="_blank"><iconify-icon icon="solar:graduation-cap-bold" class="text-primary-600"></iconify-icon> Slider Arena Belajar</a></li>
                        <li><a class="dropdown-item py-8 text-sm fw-medium d-flex align-items-center gap-2" href="<?= site_url('bunga-rampai') ?>" target="_blank"><iconify-icon icon="solar:library-bold" class="text-info-600"></iconify-icon> Slider Bunga Rampai</a></li>
                        <li><a class="dropdown-item py-8 text-sm fw-medium d-flex align-items-center gap-2" href="<?= site_url('regulasi') ?>" target="_blank"><iconify-icon icon="solar:document-text-bold" class="text-success-600"></iconify-icon> Slider Regulasi PBJ</a></li>
                        <li><a class="dropdown-item py-8 text-sm fw-medium d-flex align-items-center gap-2" href="<?= site_url('berita') ?>" target="_blank"><iconify-icon icon="solar:newspaper-bold" class="text-purple"></iconify-icon> Slider Berita &amp; Warta</a></li>
                        <li><a class="dropdown-item py-8 text-sm fw-medium d-flex align-items-center gap-2" href="<?= site_url('event') ?>" target="_blank"><iconify-icon icon="solar:calendar-bold" class="text-warning-main"></iconify-icon> Slider Event &amp; Agenda</a></li>
                        <li><hr class="dropdown-divider my-4"></li>
                        <li><a class="dropdown-item py-8 text-sm fw-medium d-flex align-items-center gap-2" href="<?= site_url('/') ?>" target="_blank"><iconify-icon icon="solar:home-smile-bold" class="text-cyan"></iconify-icon> Hero Slider Beranda</a></li>
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
                        <h6 class="mb-0 text-primary-light"><?= number_format($stats['total_carousel_portal'] ?? 0) ?> Slide</h6>
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
                        <h6 class="mb-0 text-primary-light"><?= number_format($stats['total_featured_portal'] ?? 0) ?> Item</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:star-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center gap-1 text-warning-main">
                        <iconify-icon icon="solar:star-fall-bold" class="text-xs"></iconify-icon> Unggulan
                    </span>
                    Status featured / is_featured
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
                        <h6 class="mb-0 text-primary-light">5 Modul Portal</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:layers-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center gap-1 text-info-main">
                        <iconify-icon icon="solar:check-circle-bold" class="text-xs"></iconify-icon> Terhubung
                    </span>
                    Arena, Bunga, Reg, Berita, Event
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
                        <h6 class="mb-0 text-primary-light">100% Aktif</h6>
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
                        <span class="badge text-xs px-8 py-2 rounded-pill bg-primary-100 text-primary-600"><?= (int) $stats['arena']['carousel_count'] ?></span>
                    </span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-20 py-12 <?= $activeTab === 'knowledge' ? 'active' : '' ?>" id="tab-knowledge-btn" data-bs-toggle="pill" data-bs-target="#tab-knowledge" type="button" role="tab">
                    <span class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:library-bold" class="text-lg"></iconify-icon>
                        <span>Bunga Rampai</span>
                        <span class="badge text-xs px-8 py-2 rounded-pill bg-info-100 text-info-600"><?= (int) $stats['knowledge']['carousel_count'] ?></span>
                    </span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-20 py-12 <?= $activeTab === 'regulation' ? 'active' : '' ?>" id="tab-regulation-btn" data-bs-toggle="pill" data-bs-target="#tab-regulation" type="button" role="tab">
                    <span class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:document-text-bold" class="text-lg"></iconify-icon>
                        <span>Regulasi PBJ</span>
                        <span class="badge text-xs px-8 py-2 rounded-pill bg-success-100 text-success-600"><?= (int) $stats['regulation']['carousel_count'] ?></span>
                    </span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-20 py-12 <?= $activeTab === 'news' ? 'active' : '' ?>" id="tab-news-btn" data-bs-toggle="pill" data-bs-target="#tab-news" type="button" role="tab">
                    <span class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:newspaper-bold" class="text-lg"></iconify-icon>
                        <span>Berita &amp; Artikel</span>
                        <span class="badge text-xs px-8 py-2 rounded-pill bg-info-100 text-info-600"><?= (int) $stats['news']['carousel_count'] ?></span>
                    </span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-20 py-12 <?= $activeTab === 'event' ? 'active' : '' ?>" id="tab-event-btn" data-bs-toggle="pill" data-bs-target="#tab-event" type="button" role="tab">
                    <span class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:calendar-bold" class="text-lg"></iconify-icon>
                        <span>Event &amp; Agenda</span>
                        <span class="badge text-xs px-8 py-2 rounded-pill bg-warning-100 text-warning-main"><?= (int) $stats['event']['carousel_count'] ?></span>
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
                                    <span class="badge text-xs px-12 py-4 radius-4 bg-success-100 text-success-600 fw-medium">
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
                                    <div class="d-flex justify-content-between">
                                        <span class="text-secondary-light">Slide Aktif di Hero:</span>
                                        <span class="fw-semibold text-primary-600"><?= (int) $pf['carousel_cnt'] ?> Item</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary-600 btn-sm radius-8 w-100 fw-semibold text-xs py-8" onclick="document.querySelector('<?= $pf['target_tab'] ?>').click();">
                                    Kelola Konten
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
            <div class="card-header border-bottom bg-base py-16 px-24">
                <h6 class="text-lg fw-semibold text-primary-light mb-1 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:star-rainbow-bold" class="text-warning-main text-xl"></iconify-icon>
                    <span>Daftar Cepat Item Aktif di Hero Carousel Seluruh Portal</span>
                </h6>
                <p class="text-secondary-light text-xs mb-0">Item yang saat ini diprioritaskan tampil pada hero banner landing page.</p>
            </div>
            <div class="card-body p-24">
                <div class="table-responsive">
                    <table class="table bordered-table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Cover</th>
                                <th>Judul &amp; Informasi</th>
                                <th style="width: 150px;">Modul / Fitur</th>
                                <th style="width: 130px;" class="text-center">Status Slider</th>
                                <th style="width: 120px;" class="text-center">Unggulan</th>
                                <th style="width: 100px;" class="text-center">Urutan</th>
                                <th style="width: 100px;" class="text-end">Aksi</th>
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
                                <tr>
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
                <div class="d-flex align-items-center gap-2">
                    <input type="text" class="form-control form-control-sm radius-8 table-quick-search" placeholder="Cari di Arena Belajar..." style="width: 220px;">
                    <a href="<?= site_url('arena-belajar') ?>" target="_blank" class="btn btn-outline-primary-600 btn-sm radius-8 px-14 py-8 fw-semibold text-xs d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:link-external-linear"></iconify-icon> Preview Halaman
                    </a>
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
                                <th style="width: 100px;" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_merge($programs, $courses, $classes) as $item): ?>
                                <?= view('dashboard/landing/spotlight_hub/item_row', ['item' => $item]) ?>
                            <?php endforeach; ?>
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
                <div class="d-flex align-items-center gap-2">
                    <input type="text" class="form-control form-control-sm radius-8 table-quick-search" placeholder="Cari di Bunga Rampai..." style="width: 220px;">
                    <a href="<?= site_url('bunga-rampai') ?>" target="_blank" class="btn btn-outline-info-600 btn-sm radius-8 px-14 py-8 fw-semibold text-xs d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:link-external-linear"></iconify-icon> Preview Halaman
                    </a>
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
                                <th style="width: 100px;" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_merge($knowledgeArticles, $knowledgeMedia) as $item): ?>
                                <?= view('dashboard/landing/spotlight_hub/item_row', ['item' => $item]) ?>
                            <?php endforeach; ?>
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
                <div class="d-flex align-items-center gap-2">
                    <input type="text" class="form-control form-control-sm radius-8 table-quick-search" placeholder="Cari peraturan..." style="width: 220px;">
                    <a href="<?= site_url('regulasi') ?>" target="_blank" class="btn btn-outline-success-600 btn-sm radius-8 px-14 py-8 fw-semibold text-xs d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:link-external-linear"></iconify-icon> Preview Halaman
                    </a>
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
                                <th style="width: 100px;" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($regulations as $item): ?>
                                <?= view('dashboard/landing/spotlight_hub/item_row', ['item' => $item]) ?>
                            <?php endforeach; ?>
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
                        <span>Sorotan Hero Slider Berita &amp; Artikel</span>
                    </h5>
                    <p class="text-secondary-light text-xs mb-0">Pilih siaran pers atau warta prioritas yang ditampilkan pada slider headline majalah teratas <code>/berita</code>.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <input type="text" class="form-control form-control-sm radius-8 table-quick-search" placeholder="Cari warta berita..." style="width: 220px;">
                    <a href="<?= site_url('berita') ?>" target="_blank" class="btn btn-outline-primary-600 btn-sm radius-8 px-14 py-8 fw-semibold text-xs d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:link-external-linear"></iconify-icon> Preview Halaman
                    </a>
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
                                <th style="width: 100px;" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($news as $item): ?>
                                <?= view('dashboard/landing/spotlight_hub/item_row', ['item' => $item]) ?>
                            <?php endforeach; ?>
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
                <div class="d-flex align-items-center gap-2">
                    <input type="text" class="form-control form-control-sm radius-8 table-quick-search" placeholder="Cari kegiatan..." style="width: 220px;">
                    <a href="<?= site_url('event') ?>" target="_blank" class="btn btn-outline-warning-600 btn-sm radius-8 px-14 py-8 fw-semibold text-xs d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:link-external-linear"></iconify-icon> Preview Halaman
                    </a>
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
                                <th style="width: 100px;" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $item): ?>
                                <?= view('dashboard/landing/spotlight_hub/item_row', ['item' => $item]) ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal: Konfigurasi Mode & Batas Slide Lintas Fitur (Native WowDash Modal) -->
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
                                <form method="post" action="<?= site_url('dashboard/landing/sorotan-carousel/pengaturan') ?>">
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
                                <form method="post" action="<?= site_url('dashboard/landing/sorotan-carousel/pengaturan') ?>">
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
                                <form method="post" action="<?= site_url('dashboard/landing/sorotan-carousel/pengaturan') ?>">
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
                                <form method="post" action="<?= site_url('dashboard/landing/sorotan-carousel/pengaturan') ?>">
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
                                <form method="post" action="<?= site_url('dashboard/landing/sorotan-carousel/pengaturan') ?>">
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
            <div class="modal-footer py-16 px-24 border-top">
                <button type="button" class="btn btn-secondary-light text-sm px-20 py-11 radius-8" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Client-Side Quick Table Search Filtering
    const searchInputs = document.querySelectorAll('.table-quick-search');
    searchInputs.forEach(input => {
        input.addEventListener('keyup', function () {
            const query = this.value.toLowerCase().trim();
            const table = this.closest('.tab-pane').querySelector('.filterable-table');
            if (!table) return;

            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // 2. AJAX Toggle for Carousel Switch & Featured Switch
    document.addEventListener('change', function (e) {
        if (!e.target.matches('.spotlight-toggle-switch')) return;

        const toggleEl = e.target;
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
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            toggleEl.disabled = false;
            if (data.success) {
                showToast(data.message, 'success');
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

    // 3. AJAX Slide Order Input Update
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
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            inputEl.style.opacity = '1';
            if (data.success) {
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

    // Native WowDash Toast Notification Function
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
        toast.innerHTML = `<iconify-icon icon="${iconName}" class="text-xl"></iconify-icon><span>${message}</span>`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3200);
    }
});
</script>
<?= $this->endSection() ?>
