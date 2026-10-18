<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-events.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-events.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-events.css') : '1.0' ?>" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- MAIN EVENT CONTENT SECTION — Following Landing News UI Benchmark -->
<section class="berita-main-section evt-main-section" id="kategori-liputan-section">
    <div class="container">
        <!-- Compact Breadcrumb + Header Title & Unified Search -->
        <div class="evt-hero-banner mb-3">
            <div class="evt-breadcrumb">
                <a href="<?= site_url('/') ?>"><iconify-icon icon="solar:home-2-bold" class="align-middle"></iconify-icon> Beranda</a>
                <span>/</span>
                <span>Event &amp; Agenda PBJ</span>
                <?php if (! empty($selectedType)): ?>
                    <span>/</span>
                    <span class="fw-bold text-dark"><?= ucfirst($selectedType) ?></span>
                <?php endif; ?>
            </div>

            <div class="row align-items-center justify-content-between g-3 mb-2">
                <div class="col-lg-7">
                    <h1 class="evt-hero-title mb-2">
                        Agenda Kegiatan &amp; Pembinaan <br>
                        <span>Pengadaan Barang/Jasa Provinsi Lampung</span>
                    </h1>
                    <p class="evt-hero-desc mb-0">
                        Ikuti webinar nasional, bimbingan teknis bersertifikat, rapat koordinasi terpadu, dan lokakarya mitigasi risiko pengadaan bersama instruktur ahli LKPP dan Biro PBJ Setda Provinsi Lampung.
                    </p>
                </div>
                <div class="col-lg-5">
                    <!-- Search Form -->
                    <form action="<?= site_url('event') ?>" method="get" class="evt-search-box ms-lg-auto">
                        <iconify-icon icon="solar:magnifer-linear" class="text-slate-500 text-xl"></iconify-icon>
                        <input type="text" name="q" value="<?= esc($keyword ?? '') ?>" placeholder="Cari webinar, bimtek kualifikasi, atau topik kegiatan...">
                        <?php if (! empty($selectedType)): ?>
                            <input type="hidden" name="tipe" value="<?= esc($selectedType) ?>">
                        <?php endif; ?>
                        <?php if (! empty($selectedMode)): ?>
                            <input type="hidden" name="mode" value="<?= esc($selectedMode) ?>">
                        <?php endif; ?>
                        <button type="submit">
                            <span>Cari Agenda</span>
                            <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <?php if (! empty($sliderEvents) && $currentPage === 1 && empty($keyword) && empty($selectedType) && empty($selectedMode)): ?>
            <!-- EVENT HEADLINE SLIDER (Agenda Utama / Pinned / Coming Soon Carousel) -->
            <div class="evt-headline mb-4">
                <div id="eventHeadlineCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
                    <div class="carousel-inner radius-16 overflow-hidden">
                        <?php foreach ($sliderEvents as $sIndex => $sItem):
                            $sCover = ! empty($sItem['cover_url']) ? $sItem['cover_url'] : base_url('assets/main/images/blog/blog-details-img-1.jpg');
                            $badgeFlagClass = match ($sItem['status']) {
                                'open_registration' => 'evt-badge-flag--open',
                                'coming_soon'       => 'evt-badge-flag--coming',
                                default             => 'evt-badge-flag--featured',
                            };
                            $badgeFlagText = match ($sItem['status']) {
                                'open_registration' => '<span class="dot"></span> Pendaftaran Dibuka',
                                'coming_soon'       => '<iconify-icon icon="solar:clock-circle-bold"></iconify-icon> Segera Hadir',
                                default             => '<iconify-icon icon="solar:star-bold"></iconify-icon> Agenda Sorotan',
                            };
                        ?>
                            <div class="carousel-item <?= $sIndex === 0 ? 'active' : '' ?>">
                                <div class="evt-headline__inner">
                                    <div class="evt-headline__media">
                                        <img src="<?= $sCover ?>" alt="<?= esc($sItem['title']) ?>" loading="<?= $sIndex === 0 ? 'eager' : 'lazy' ?>">
                                        <div class="evt-headline__badges">
                                            <span class="evt-badge-flag <?= $badgeFlagClass ?>">
                                                <?= $badgeFlagText ?>
                                            </span>
                                            <span class="evt-badge-pill"><?= esc($sItem['type_label']) ?></span>
                                            <span class="evt-badge-pill"><?= esc($sItem['mode_label']) ?></span>
                                            <?php if (! empty($sItem['certificate_enabled'])): ?>
                                                <span class="evt-badge-pill evt-badge-pill--cert">
                                                    <iconify-icon icon="solar:diploma-verified-bold" class="align-middle"></iconify-icon> E-Sertifikat
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="evt-headline__counter">
                                            <span class="dot"></span>
                                            <span class="d-none d-sm-inline">Agenda Sorotan </span><span>#<?= $sIndex + 1 ?> / <?= count($sliderEvents) ?></span>
                                        </div>
                                        <div class="evt-headline__caption">
                                            <div class="evt-headline__meta-row">
                                                <span><iconify-icon icon="solar:calendar-date-bold" class="text-warning align-middle"></iconify-icon> <?= esc($sItem['start_date']) ?></span>
                                                <span>&bull;</span>
                                                <span><iconify-icon icon="solar:clock-circle-bold" class="text-info align-middle"></iconify-icon> <?= esc($sItem['time_range']) ?></span>
                                                <span>&bull;</span>
                                                <span><iconify-icon icon="solar:map-point-bold" class="text-danger align-middle"></iconify-icon> <?= esc($sItem['location']) ?></span>
                                                <span>&bull;</span>
                                                <span>
                                                    <iconify-icon icon="solar:users-group-rounded-bold" class="text-success align-middle"></iconify-icon>
                                                    <?= $sItem['is_unlimited'] ? 'Kuota Terbuka' : 'Sisa ' . $sItem['remaining'] . ' Kursi' ?>
                                                </span>
                                            </div>
                                            <h2 class="evt-headline__title">
                                                <a href="<?= site_url('event/' . $sItem['slug']) ?>">
                                                    <?= esc($sItem['title']) ?>
                                                </a>
                                            </h2>
                                            <div class="evt-headline__action">
                                                <a href="<?= site_url('event/' . $sItem['slug']) ?>" class="btn btn-sm btn-primary px-3 py-1 radius-8 fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                                                    <span>Daftar / Detail Agenda</span>
                                                    <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($sliderEvents) > 1): ?>
                        <button class="evt-headline__nav prev" type="button" data-bs-target="#eventHeadlineCarousel" data-bs-slide="prev" aria-label="Sebelumnya">
                            <iconify-icon icon="solar:alt-arrow-left-linear"></iconify-icon>
                        </button>
                        <button class="evt-headline__nav next" type="button" data-bs-target="#eventHeadlineCarousel" data-bs-slide="next" aria-label="Selanjutnya">
                            <iconify-icon icon="solar:alt-arrow-right-linear"></iconify-icon>
                        </button>
                        <div class="evt-headline__indicators">
                            <?php foreach ($sliderEvents as $sIndex => $sItem): ?>
                                <button type="button" data-bs-target="#eventHeadlineCarousel" data-bs-slide-to="<?= $sIndex ?>" class="<?= $sIndex === 0 ? 'active' : '' ?>" aria-label="Slide <?= $sIndex + 1 ?>"></button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- 2. LIVE METRICS RIBBON (Strictly Real Data, Zero Dummy Fallback) -->
        <div class="evt-metrics-section mb-4">
            <div class="evt-metrics-card">
                <div class="row g-3">
                    <div class="col-lg-3 col-6">
                        <div class="evt-kpi-item">
                            <div class="evt-kpi-icon text-primary bg-primary-subtle">
                                <iconify-icon icon="solar:calendar-bold"></iconify-icon>
                            </div>
                            <div>
                                <div class="evt-kpi-num"><?= number_format($stats['total_events']) ?></div>
                                <div class="evt-kpi-label">Total Agenda Terdaftar</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="evt-kpi-item">
                            <div class="evt-kpi-icon text-success bg-success-subtle">
                                <iconify-icon icon="solar:clock-circle-bold"></iconify-icon>
                            </div>
                            <div>
                                <div class="evt-kpi-num"><?= number_format($stats['upcoming_events']) ?></div>
                                <div class="evt-kpi-label">Kegiatan Mendatang</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="evt-kpi-item">
                            <div class="evt-kpi-icon text-warning bg-warning-subtle">
                                <iconify-icon icon="solar:user-check-bold"></iconify-icon>
                            </div>
                            <div>
                                <div class="evt-kpi-num"><?= number_format($stats['open_registration']) ?></div>
                                <div class="evt-kpi-label">Pendaftaran Dibuka</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="evt-kpi-item">
                            <div class="evt-kpi-icon text-info bg-info-subtle">
                                <iconify-icon icon="solar:users-group-rounded-bold"></iconify-icon>
                            </div>
                            <div>
                                <div class="evt-kpi-num"><?= number_format($stats['total_attendees']) ?></div>
                                <div class="evt-kpi-label">Peserta Terdaftar</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Mode Navigation Tabs & Filters -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
            <div class="evt-view-tabs">
                <a href="<?= site_url('event') ?>" class="evt-view-tab <?= $viewMode === 'all' ? 'active' : '' ?>">
                    <iconify-icon icon="solar:widget-2-bold"></iconify-icon>
                    <span>Semua Kegiatan</span>
                </a>
                <a href="<?= site_url('event/agenda') ?>" class="evt-view-tab <?= $viewMode === 'upcoming' ? 'active' : '' ?>">
                    <iconify-icon icon="solar:clock-circle-bold"></iconify-icon>
                    <span>Agenda Mendatang</span>
                </a>
                <a href="<?= site_url('event/kalender') ?>" class="evt-view-tab <?= $viewMode === 'calendar' ? 'active' : '' ?>">
                    <iconify-icon icon="solar:calendar-date-bold"></iconify-icon>
                    <span>Kalender Kegiatan</span>
                </a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="text-xs text-slate-600 fw-bold">Moda:</span>
                <a href="<?= site_url('event' . ($selectedType ? '?tipe=' . $selectedType . '&' : '?') . 'mode=online') ?>" class="badge <?= $selectedMode === 'online' ? 'bg-success text-white' : 'bg-light text-dark border' ?> text-decoration-none p-2 radius-6">Online</a>
                <a href="<?= site_url('event' . ($selectedType ? '?tipe=' . $selectedType . '&' : '?') . 'mode=offline') ?>" class="badge <?= $selectedMode === 'offline' ? 'bg-primary text-white' : 'bg-light text-dark border' ?> text-decoration-none p-2 radius-6">Offline</a>
                <a href="<?= site_url('event' . ($selectedType ? '?tipe=' . $selectedType . '&' : '?') . 'mode=hybrid') ?>" class="badge <?= $selectedMode === 'hybrid' ? 'bg-warning text-dark' : 'bg-light text-dark border' ?> text-decoration-none p-2 radius-6">Hybrid</a>
                <?php if (! empty($selectedMode)): ?>
                    <a href="<?= site_url('event' . ($selectedType ? '?tipe=' . $selectedType : '')) ?>" class="text-xs text-danger text-decoration-none ms-1" title="Reset Mode">&times; Reset</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Filter Pills by Type (Supporting all 8 dashboard types) -->
        <div class="evt-filter-wrapper mb-4">
            <div class="evt-pill-group">
                <a href="<?= site_url('event') ?>" class="evt-pill <?= empty($selectedType) ? 'active' : '' ?>">
                    <span>Semua Tipe</span>
                    <span class="badge bg-light text-dark radius-999"><?= $stats['total_events'] ?></span>
                </a>
                <?php
                $dashboardTypes = [
                    'webinar'     => 'Webinar',
                    'bimtek'      => 'Bimtek',
                    'workshop'    => 'Workshop',
                    'sosialisasi' => 'Sosialisasi',
                    'seminar'     => 'Seminar',
                    'pelatihan'   => 'Pelatihan',
                    'rakor'       => 'Rapat Koordinasi',
                    'klinik'      => 'Klinik PBJ',
                ];
                ?>
                <?php foreach ($dashboardTypes as $tKey => $tLabel): ?>
                    <a href="<?= site_url('event?tipe=' . $tKey . (! empty($selectedMode) ? '&mode=' . $selectedMode : '')) ?>" class="evt-pill <?= $selectedType === $tKey ? 'active' : '' ?>">
                        <span><?= esc($tLabel) ?></span>
                        <span class="badge bg-light text-dark radius-999"><?= $typeCounts[$tKey] ?? 0 ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if (! empty($keyword)): ?>
                <div class="text-sm text-slate-700">
                    Pencarian: <strong>"<?= esc($keyword) ?>"</strong>
                    <a href="<?= site_url('event') ?>" class="text-danger ms-2"><iconify-icon icon="solar:close-circle-bold"></iconify-icon> Reset</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- CALENDAR VIEW MODE (if tab is calendar) -->
        <?php if ($viewMode === 'calendar'): ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <?php if (empty($calendarGrouped)): ?>
                        <div class="text-center p-5 bg-white border radius-18">
                            <h5 class="fw-bold mb-2 text-slate-900">Belum ada kalender agenda terjadwal</h5>
                            <p class="text-slate-600">Pantau terus pembaharuan agenda melalui kanal resmi BegawiPengadaan.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($calendarGrouped as $monthName => $cEvents): ?>
                            <h3 class="evt-calendar-month-header">
                                <iconify-icon icon="solar:calendar-mark-bold" class="text-primary"></iconify-icon>
                                <span><?= esc($monthName) ?></span>
                            </h3>
                            <div class="d-grid gap-2">
                                <?php foreach ($cEvents as $ce): ?>
                                    <?php
                                    $ceStart = ! empty($ce['start_at']) ? strtotime($ce['start_at']) : time();
                                    $ceEnd   = ! empty($ce['end_at']) ? strtotime($ce['end_at']) : $ceStart;
                                    ?>
                                    <div class="evt-timeline-item">
                                        <div class="d-flex align-items-center gap-3" style="min-width: 140px;">
                                            <div class="text-center p-2 bg-light border radius-10" style="min-width: 50px;">
                                                <strong class="d-block text-lg text-primary"><?= date('d', $ceStart) ?></strong>
                                                <small class="text-slate-600 text-uppercase text-xs fw-bold"><?= date('M', $ceStart) ?></small>
                                            </div>
                                            <div class="text-xs text-slate-600">
                                                <span class="d-block fw-semibold text-slate-900"><?= date('H:i', $ceStart) ?> WIB</span>
                                                <span class="badge bg-light text-dark border"><?= ucfirst($ce['mode']) ?></span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge bg-primary-subtle text-primary text-xs text-uppercase fw-bold"><?= esc($ce['type']) ?></span>
                                                <?php if (! empty($ce['certificate_enabled'])): ?>
                                                    <span class="badge bg-success-subtle text-success text-xs fw-bold"><iconify-icon icon="solar:diploma-verified-bold"></iconify-icon> E-Sertifikat</span>
                                                <?php endif; ?>
                                                <span class="text-xs text-slate-600"><iconify-icon icon="solar:map-point-linear" class="text-danger"></iconify-icon> <?= esc($ce['location'] ?: 'Online') ?></span>
                                            </div>
                                            <h5 class="h6 fw-bold mb-0">
                                                <a href="<?= site_url('event/' . $ce['slug']) ?>" class="text-slate-900 text-decoration-none">
                                                    <?= esc($ce['title']) ?>
                                                </a>
                                            </h5>
                                        </div>
                                        <div>
                                            <a href="<?= site_url('event/' . $ce['slug']) ?>" class="btn btn-sm btn-outline-primary radius-8 px-3 fw-bold">
                                                Detail Acara
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Calendar Sidebar -->
                <div class="col-lg-4">
                    <div class="evt-sidebar-widget">
                        <h4 class="evt-widget-title">
                            <iconify-icon icon="solar:bell-bold" class="text-warning"></iconify-icon>
                            <span>Informasi Jadwal Acara</span>
                        </h4>
                        <p class="text-xs text-slate-600 mb-3" style="line-height: 1.6;">
                            Jadwal pelaksanaan disusun secara berkala oleh Biro Pengadaan Barang dan Jasa Provinsi Lampung. Peserta yang terdaftar akan menerima notifikasi resmi.
                        </p>
                        <a href="<?= site_url('event') ?>" class="btn btn-primary w-100 radius-10 fw-bold">Kembali ke Tampilan Katalog</a>
                    </div>
                </div>
            </div>

            <!-- STANDARD CATALOG / AGENDA VIEW -->
        <?php else: ?>
            <!-- Main Grid & Sidebar Layout -->
            <div class="row g-4">
                <!-- Left: Events Grid -->
                <div class="col-lg-8">
                    <?php if (empty($items)): ?>
                        <div class="text-center p-5 bg-white border radius-18">
                            <div class="mb-3 text-slate-400" style="font-size: 48px;">
                                <iconify-icon icon="solar:calendar-minimalistic-linear"></iconify-icon>
                            </div>
                            <h4 class="fw-bold mb-2 text-slate-900">Tidak Ada Kegiatan yang Sesuai</h4>
                            <p class="text-slate-600 mb-4">Gunakan kata kunci atau kombinasi filter lain untuk menemukan agenda pengadaan yang Anda cari.</p>
                            <a href="<?= site_url('event') ?>" class="btn btn-primary px-4 py-2 radius-10 fw-bold">Lihat Semua Event</a>
                        </div>
                    <?php else: ?>
                        <div class="row g-4">
                            <?php foreach ($items as $item): ?>
                                <?php
                                $modeClass = match ($item['mode']) {
                                    'online'  => 'evt-mode-online',
                                    'offline' => 'evt-mode-offline',
                                    default   => 'evt-mode-hybrid',
                                };
                                ?>
                                <div class="col-md-6">
                                    <article class="evt-card">
                                        <div class="evt-card-header">
                                            <?php if (! empty($item['cover_url'])): ?>
                                                <img src="<?= $item['cover_url'] ?>" alt="<?= esc($item['title']) ?>" class="evt-card-img" loading="lazy">
                                            <?php else: ?>
                                                <div class="evt-card-placeholder">
                                                    <iconify-icon icon="solar:calendar-date-bold" style="font-size: 42px; color: var(--evt-gold); margin-bottom: 8px;"></iconify-icon>
                                                    <span class="badge bg-warning text-dark text-xs fw-bold px-2 py-1 radius-6 mb-1"><?= esc($item['type_label']) ?></span>
                                                    <small class="text-white-50 text-xs">BegawiPengadaan Lampung</small>
                                                </div>
                                            <?php endif; ?>
                                            <div class="evt-date-badge">
                                                <span class="evt-date-day"><?= esc($item['start_day']) ?></span>
                                                <span class="evt-date-month"><?= esc($item['start_month']) ?></span>
                                            </div>
                                            <span class="evt-mode-pill <?= $modeClass ?>">
                                                <?= esc($item['mode']) ?>
                                            </span>
                                        </div>
                                        <div class="evt-card-body">
                                            <div class="evt-card-meta">
                                                <span class="badge bg-primary-subtle text-primary text-xs text-uppercase fw-bold"><?= esc($item['type_label']) ?></span>
                                                <?php if (! empty($item['certificate_enabled'])): ?>
                                                    <span class="badge bg-success-subtle text-success text-xs fw-bold"><iconify-icon icon="solar:diploma-verified-bold"></iconify-icon> Sertifikat</span>
                                                <?php endif; ?>
                                                <span>&middot;</span>
                                                <span><iconify-icon icon="solar:clock-circle-linear" class="align-middle text-primary"></iconify-icon> <?= esc($item['time_range']) ?></span>
                                            </div>

                                            <h3 class="evt-card-title">
                                                <a href="<?= site_url('event/' . $item['slug']) ?>">
                                                    <?= esc($item['title']) ?>
                                                </a>
                                            </h3>

                                            <p class="evt-card-summary">
                                                <?= esc($item['summary']) ?>
                                            </p>

                                            <div class="evt-card-location">
                                                <iconify-icon icon="solar:map-point-bold" class="text-danger flex-shrink-0 text-base"></iconify-icon>
                                                <span><?= esc($item['location']) ?></span>
                                            </div>

                                            <div class="evt-card-footer">
                                                <div class="evt-seats-badge">
                                                    <?php if ($item['is_past']): ?>
                                                        <span class="badge bg-secondary-subtle text-secondary">Kegiatan Selesai</span>
                                                    <?php elseif ($item['is_unlimited']): ?>
                                                        <span class="text-slate-600">Kuota: <strong>Fleksibel</strong></span>
                                                    <?php elseif ($item['remaining'] > 0): ?>
                                                        Sisa: <strong><?= $item['remaining'] ?> kuota</strong>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning-subtle text-warning">Kuota Penuh <?= ! empty($item['waitlist_enabled']) ? '(Waitlist)' : '' ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <a href="<?= site_url('event/' . $item['slug']) ?>" class="btn btn-sm btn-outline-primary px-3 py-1 radius-8 fw-bold">
                                                    Detail &rarr;
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination Bar -->
                        <?php if ($totalPages > 1): ?>
                            <?php
                            $queryParams = [];
                            if (! empty($keyword)) $queryParams['q'] = $keyword;
                            if (! empty($selectedType)) $queryParams['tipe'] = $selectedType;
                            if (! empty($selectedMode)) $queryParams['mode'] = $selectedMode;
                            if (! empty($selectedTimeline)) $queryParams['timeline'] = $selectedTimeline;
                            $buildEvtUrl = function ($p) use ($queryParams) {
                                $params = array_merge($queryParams, ['page' => $p]);
                                return current_url() . '?' . http_build_query($params);
                            };
                            ?>
                            <nav class="evt-pagination-wrap" aria-label="Navigasi Halaman Event">
                                <a href="<?= $buildEvtUrl(max(1, $currentPage - 1)) ?>"
                                    class="evt-page-btn <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                    <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
                                    <span class="ms-1 d-none d-sm-inline">Sebelumnya</span>
                                </a>

                                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                    <?php if ($p === 1 || $p === $totalPages || ($p >= $currentPage - 1 && $p <= $currentPage + 1)): ?>
                                        <a href="<?= $buildEvtUrl($p) ?>"
                                            class="evt-page-btn <?= $p === $currentPage ? 'active' : '' ?>">
                                            <?= $p ?>
                                        </a>
                                    <?php elseif ($p === 2 && $currentPage > 3): ?>
                                        <span class="evt-page-btn disabled">...</span>
                                    <?php elseif ($p === $totalPages - 1 && $currentPage < $totalPages - 2): ?>
                                        <span class="evt-page-btn disabled">...</span>
                                    <?php endif; ?>
                                <?php endfor; ?>

                                <a href="<?= $buildEvtUrl(min($totalPages, $currentPage + 1)) ?>"
                                    class="evt-page-btn <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                    <span class="me-1 d-none d-sm-inline">Berikutnya</span>
                                    <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                                </a>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Right: Sidebar -->
                <div class="col-lg-4">
                    <!-- Quick Search Widget -->
                    <div class="evt-sidebar-widget">
                        <h4 class="evt-widget-title">
                            <iconify-icon icon="solar:magnifer-bold" class="text-primary"></iconify-icon>
                            <span>Cari Agenda PBJ</span>
                        </h4>
                        <form action="<?= site_url('event') ?>" method="get">
                            <div class="input-group">
                                <input type="text" name="q" class="form-control radius-8 border" placeholder="Topik atau narasumber..." value="<?= esc($keyword ?? '') ?>">
                                <button class="btn btn-primary px-3 radius-8" type="submit">
                                    <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Type Filter Widget (All 8 dashboard types) -->
                    <div class="evt-sidebar-widget">
                        <h4 class="evt-widget-title">
                            <iconify-icon icon="solar:layers-bold" class="text-warning"></iconify-icon>
                            <span>Tipe Kegiatan</span>
                        </h4>
                        <div class="d-grid gap-2">
                            <?php foreach ($dashboardTypes as $tKey => $tLabel): ?>
                                <a href="<?= site_url('event?tipe=' . $tKey . (! empty($selectedMode) ? '&mode=' . $selectedMode : '')) ?>" class="d-flex align-items-center justify-content-between p-2 radius-8 text-decoration-none <?= $selectedType === $tKey ? 'bg-primary-subtle text-primary fw-bold' : 'text-slate-700 bg-light' ?>">
                                    <span><?= esc($tLabel) ?></span>
                                    <span class="badge bg-white text-muted border"><?= $typeCounts[$tKey] ?? 0 ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Upcoming Top 3 Events -->
                    <div class="evt-sidebar-widget">
                        <h4 class="evt-widget-title">
                            <iconify-icon icon="solar:fire-bold" class="text-danger"></iconify-icon>
                            <span>Kegiatan Terdekat</span>
                        </h4>
                        <?php if (empty($upcomingSidebar)): ?>
                            <p class="text-xs text-slate-500 mb-0">Belum ada agenda mendatang dalam waktu dekat.</p>
                        <?php else: ?>
                            <?php foreach ($upcomingSidebar as $us): ?>
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <span class="badge bg-primary-subtle text-primary text-xs text-uppercase fw-bold"><?= esc($us['type']) ?></span>
                                        <?php if (! empty($us['certificate_enabled'])): ?>
                                            <span class="badge bg-success-subtle text-success text-xs fw-bold">Sertifikat</span>
                                        <?php endif; ?>
                                        <small class="text-slate-500 fw-semibold"><?= date('d M Y', strtotime($us['start_at'])) ?></small>
                                    </div>
                                    <h6 class="fw-bold mb-1">
                                        <a href="<?= site_url('event/' . $us['slug']) ?>" class="text-slate-900 text-decoration-none">
                                            <?= esc($us['title']) ?>
                                        </a>
                                    </h6>
                                    <small class="text-slate-600 d-block"><iconify-icon icon="solar:map-point-linear" class="text-danger"></iconify-icon> <?= esc($us['location'] ?: 'Online via Zoom') ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Host / Biro PBJ Notice Box -->
                    <div class="p-4 bg-dark text-white border radius-18 shadow-sm" style="background: linear-gradient(135deg, #052244 0%, #0a3d74 100%) !important;">
                        <span class="badge bg-warning text-dark text-xs fw-bold px-2 py-1 mb-2">Biro PBJ Setda Lampung</span>
                        <h5 class="fw-bold text-white mb-2">Layanan Pembinaan &amp; Advokasi</h5>
                        <p class="text-xs text-slate-300 mb-3" style="line-height: 1.65;">
                            Seluruh kegiatan peningkatan kapasitas diselenggarakan secara resmi untuk mendukung profesionalisme SDM pengadaan di Lingkungan Pemerintah Provinsi Lampung.
                        </p>
                        <div class="text-xs text-warning fw-semibold d-flex align-items-center gap-1">
                            <iconify-icon icon="solar:phone-bold"></iconify-icon>
                            <span>Helpdesk PBJ: +62 851-1777-9783</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('assets/dashboard/js/lib/iconify-icon.min.js') ?>"></script>
<script>
    (function() {
        // Bulletproof zero-jitter clamping at scroll extremities for ultra-smooth scrolling
        var isClamping = false;
        window.addEventListener('scroll', function() {
            var maxScroll = Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
            if (window.scrollY > maxScroll) {
                if (!isClamping) {
                    isClamping = true;
                    window.scrollTo(0, maxScroll);
                    requestAnimationFrame(function() {
                        isClamping = false;
                    });
                }
            }
        }, {
            passive: true
        });
    })();
</script>
<?= $this->endSection() ?>