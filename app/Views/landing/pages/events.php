<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-news.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-news.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-news.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-events.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-events.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-events.css') : '1.0' ?>" />
<style>
    .evt-headline__meta-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

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

<!-- MAIN EVENT CONTENT SECTION — Fully Harmonized with Berita PBJ UI -->
<section class="berita-main-section evt-main-section" id="kategori-liputan-section">
    <div class="container">

        <!-- Breadcrumb Navigation & Magazine Title (Following Landing News Benchmark) -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <nav class="news-detail-breadcrumb-clean m-0" aria-label="Breadcrumb">
                <a href="<?= site_url('/') ?>"><i class="fas fa-home"></i> Beranda</a>
                <span class="sep">/</span>
                <span class="current">Agenda Kegiatan &amp; Pembinaan &middot; Pengadaan Barang/Jasa Provinsi Lampung</span>
                <?php if (! empty($selectedType)): ?>
                    <span class="sep">/</span>
                    <span class="badge bg-primary text-white text-uppercase px-2 py-1 radius-6 text-xs fw-bold"><?= ucfirst($selectedType) ?></span>
                <?php endif; ?>
            </nav>
        </div>

        <?php if (! empty($keyword) || ! empty($selectedType) || ! empty($selectedMode)): ?>
            <!-- Filter Active Notification (Compact & Clean) -->
            <div class="card border-0 shadow-2xs radius-12 p-2 px-3 mb-4 bg-white border" style="border-color: #e2e8f0 !important;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <i class="fas fa-filter text-primary" style="font-size: 13px;"></i>
                        <span class="text-muted small fw-semibold" style="font-size: 12.5px;">Filter Aktif:</span>
                        <?php if (! empty($selectedType)): ?>
                            <span class="badge bg-primary text-white fw-bold px-2 py-1 radius-6" style="font-size: 12px;"><?= esc($dashboardTypes[$selectedType] ?? ucfirst($selectedType)) ?></span>
                        <?php endif; ?>
                        <?php if (! empty($selectedMode)): ?>
                            <span class="badge bg-info text-white fw-bold px-2 py-1 radius-6" style="font-size: 12px;">Moda: <?= ucfirst($selectedMode) ?></span>
                        <?php endif; ?>
                        <?php if (! empty($keyword)): ?>
                            <span class="badge bg-warning text-dark fw-bold px-2 py-1 radius-6" style="font-size: 12px;">"<?= esc($keyword) ?>"</span>
                        <?php endif; ?>
                    </div>
                    <a href="<?= site_url('event') ?>" class="btn btn-sm btn-outline-secondary radius-8 fw-bold py-1 px-2" style="font-size: 12px;">
                        <i class="fas fa-times me-1"></i> Reset Filter
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (! empty($sliderEvents) && $currentPage === 1 && empty($keyword) && empty($selectedType) && empty($selectedMode)): ?>
            <!-- EVENT HEADLINE SLIDER (Exact Berita Magazine Slider Format) -->
            <div class="berita-headline mb-4">
                <div id="eventHeadlineCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
                    <div class="carousel-inner radius-16 overflow-hidden">
                        <?php foreach ($sliderEvents as $sIndex => $sItem):
                            $sCover = ! empty($sItem['cover_url']) ? $sItem['cover_url'] : base_url('assets/main/images/blog/blog-details-img-1.jpg');
                        ?>
                            <div class="carousel-item <?= $sIndex === 0 ? 'active' : '' ?>">
                                <div class="berita-headline__inner news-headline-card">
                                    <div class="berita-headline__media">
                                        <img src="<?= $sCover ?>" alt="<?= esc($sItem['title']) ?>" loading="<?= $sIndex === 0 ? 'eager' : 'lazy' ?>">
                                        <div class="berita-headline__badges">
                                            <span class="berita-headline__badge">
                                                <i class="fas fa-star text-warning me-1"></i> Agenda Sorotan
                                            </span>
                                            <?php if (! empty($sItem['certificate_enabled'])): ?>
                                                <span class="badge bg-success text-white px-2 py-1 radius-6 text-xs fw-bold">
                                                    <i class="fas fa-certificate me-1"></i> E-Sertifikat
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="berita-headline__counter">
                                            <span class="dot"></span>
                                            <span>Sorotan #<?= $sIndex + 1 ?> / <?= count($sliderEvents) ?></span>
                                        </div>
                                        <div class="berita-headline__caption">
                                            <span class="berita-headline__cat">
                                                <i class="fas fa-calendar-alt"></i> <?= esc($sItem['type_label']) ?> &middot; <?= esc($sItem['mode_label']) ?>
                                            </span>
                                            <h2 class="berita-headline__title">
                                                <a href="<?= site_url('event/' . $sItem['slug']) ?>">
                                                    <?= esc($sItem['title']) ?>
                                                </a>
                                            </h2>
                                            <div class="berita-headline__meta">
                                                <span><i class="far fa-calendar-alt"></i> <?= esc($sItem['start_date']) ?></span>
                                                <span><i class="far fa-clock"></i> <?= esc($sItem['time_range']) ?></span>
                                                <span><i class="fas fa-map-marker-alt text-danger"></i> <?= esc($sItem['location']) ?></span>
                                                <span>
                                                    <i class="fas fa-users text-success"></i>
                                                    <?= $sItem['is_unlimited'] ? 'Kuota Terbuka' : 'Sisa ' . $sItem['remaining'] . ' Kursi' ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($sliderEvents) > 1): ?>
                        <button class="berita-headline__nav prev" type="button" data-bs-target="#eventHeadlineCarousel" data-bs-slide="prev" aria-label="Sebelumnya">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="berita-headline__nav next" type="button" data-bs-target="#eventHeadlineCarousel" data-bs-slide="next" aria-label="Selanjutnya">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <div class="carousel-indicators berita-headline__indicators evt-headline__indicators mb-0">
                            <?php foreach ($sliderEvents as $sIndex => $sItem): ?>
                                <button type="button" data-bs-target="#eventHeadlineCarousel" data-bs-slide-to="<?= $sIndex ?>"
                                    class="<?= $sIndex === 0 ? 'active is-active' : '' ?>"
                                    <?= $sIndex === 0 ? 'aria-current="true"' : '' ?>
                                    aria-label="Slide <?= $sIndex + 1 ?>"></button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- View Mode Navigation Tabs & Filters (Clean Compact Toolbar) -->
        <div class="card border-0 shadow-2xs radius-12 p-2 px-3 mb-3 bg-white border" style="border-color: #e2e8f0 !important;">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="evt-view-tabs d-flex flex-wrap align-items-center gap-1 p-0 bg-transparent">
                    <span class="small fw-bold text-muted me-1 d-none d-sm-inline" style="font-size: 12px;"><i class="fas fa-layer-group text-primary me-1"></i> Tampilan:</span>
                    <a href="<?= site_url('event') ?>" class="evt-view-tab <?= $viewMode === 'all' ? 'active' : '' ?>">
                        <i class="fas fa-th-large me-1"></i>
                        <span>Semua Kegiatan</span>
                    </a>
                    <a href="<?= site_url('event/agenda') ?>" class="evt-view-tab <?= $viewMode === 'upcoming' ? 'active' : '' ?>">
                        <i class="far fa-clock me-1"></i>
                        <span>Agenda Mendatang</span>
                    </a>
                    <a href="<?= site_url('event/kalender') ?>" class="evt-view-tab <?= $viewMode === 'calendar' ? 'active' : '' ?>">
                        <i class="far fa-calendar-alt me-1"></i>
                        <span>Kalender Kegiatan</span>
                    </a>
                </div>

                <div class="d-flex align-items-center gap-1 ms-auto">
                    <span class="text-xs text-slate-600 fw-bold me-1"><i class="fas fa-broadcast-tower text-info me-1"></i> Moda:</span>
                    <a href="<?= site_url('event' . ($selectedType ? '?tipe=' . $selectedType . '&' : '?') . 'mode=online') ?>" class="badge <?= $selectedMode === 'online' ? 'bg-success text-white' : 'bg-light text-dark border' ?> text-decoration-none px-2 py-1 radius-6" style="font-size: 11.5px;">Online</a>
                    <a href="<?= site_url('event' . ($selectedType ? '?tipe=' . $selectedType . '&' : '?') . 'mode=offline') ?>" class="badge <?= $selectedMode === 'offline' ? 'bg-primary text-white' : 'bg-light text-dark border' ?> text-decoration-none px-2 py-1 radius-6" style="font-size: 11.5px;">Offline</a>
                    <a href="<?= site_url('event' . ($selectedType ? '?tipe=' . $selectedType . '&' : '?') . 'mode=hybrid') ?>" class="badge <?= $selectedMode === 'hybrid' ? 'bg-warning text-dark' : 'bg-light text-dark border' ?> text-decoration-none px-2 py-1 radius-6" style="font-size: 11.5px;">Hybrid</a>
                    <?php if (! empty($selectedMode)): ?>
                        <a href="<?= site_url('event' . ($selectedType ? '?tipe=' . $selectedType : '')) ?>" class="text-xs text-danger text-decoration-none ms-1" title="Reset Mode">&times; Reset</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Filter Pills by Type (Supporting all 8 dashboard types) -->
        <div class="card border-0 shadow-2xs radius-12 p-2 px-3 mb-4 bg-white border" style="border-color: #e2e8f0 !important;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="evt-pill-group">
                    <span class="small fw-bold text-muted me-1 d-none d-sm-inline" style="font-size: 12px;"><i class="fas fa-tags text-warning me-1"></i> Tipe:</span>
                    <a href="<?= site_url('event' . (! empty($selectedMode) ? '?mode=' . $selectedMode : '')) ?>" class="evt-pill <?= empty($selectedType) ? 'active' : '' ?>">
                        <span>Semua Tipe</span>
                        <span class="badge bg-light text-dark radius-999"><?= $stats['total_events'] ?></span>
                    </a>
                    <?php foreach ($dashboardTypes as $tKey => $tLabel): ?>
                        <a href="<?= site_url('event?tipe=' . $tKey . (! empty($selectedMode) ? '&mode=' . $selectedMode : '')) ?>" class="evt-pill <?= $selectedType === $tKey ? 'active' : '' ?>">
                            <span><?= esc($tLabel) ?></span>
                            <span class="badge bg-light text-dark radius-999"><?= $typeCounts[$tKey] ?? 0 ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>

                <?php if (! empty($keyword)): ?>
                    <div class="text-sm text-slate-700 bg-light px-2 py-1 radius-8 border">
                        Pencarian: <strong>"<?= esc($keyword) ?>"</strong>
                        <a href="<?= site_url('event') ?>" class="text-danger ms-2"><i class="fas fa-times-circle"></i> Reset</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- CALENDAR VIEW MODE (if tab is calendar) -->
        <?php if ($viewMode === 'calendar'): ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <?php if (empty($calendarGrouped)): ?>
                        <div class="text-center p-5 bg-white border radius-16 shadow-2xs">
                            <h5 class="fw-bold mb-2 text-dark">Belum ada kalender agenda terjadwal</h5>
                            <p class="text-muted small">Pantau terus pembaharuan agenda melalui kanal resmi BegawiPengadaan.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($calendarGrouped as $monthName => $cEvents): ?>
                            <h3 class="evt-calendar-month-header mb-3">
                                <i class="fas fa-calendar-check text-primary me-2"></i>
                                <span><?= esc($monthName) ?></span>
                            </h3>
                            <div class="d-grid gap-2 mb-4">
                                <?php foreach ($cEvents as $ce): ?>
                                    <?php
                                    $ceStart = ! empty($ce['start_at']) ? strtotime($ce['start_at']) : time();
                                    $ceEnd   = ! empty($ce['end_at']) ? strtotime($ce['end_at']) : $ceStart;
                                    ?>
                                    <div class="evt-timeline-item p-3 bg-white border radius-12 shadow-2xs d-flex align-items-center gap-3">
                                        <div class="d-flex align-items-center gap-3" style="min-width: 140px;">
                                            <div class="text-center p-2 bg-light border radius-10" style="min-width: 50px;">
                                                <strong class="d-block text-lg text-primary"><?= date('d', $ceStart) ?></strong>
                                                <small class="text-muted text-uppercase text-xs fw-bold"><?= date('M', $ceStart) ?></small>
                                            </div>
                                            <div class="text-xs text-muted">
                                                <span class="d-block fw-semibold text-dark"><?= date('H:i', $ceStart) ?> WIB</span>
                                                <span class="badge bg-light text-dark border"><?= ucfirst($ce['mode']) ?></span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge bg-primary-subtle text-primary text-xs text-uppercase fw-bold"><?= esc($ce['type']) ?></span>
                                                <?php if (! empty($ce['certificate_enabled'])): ?>
                                                    <span class="badge bg-success-subtle text-success text-xs fw-bold"><i class="fas fa-certificate me-1"></i> E-Sertifikat</span>
                                                <?php endif; ?>
                                                <span class="text-xs text-muted"><i class="fas fa-map-marker-alt text-danger me-1"></i> <?= esc($ce['location'] ?: 'Online') ?></span>
                                            </div>
                                            <h5 class="h6 fw-bold mb-0">
                                                <a href="<?= site_url('event/' . $ce['slug']) ?>" class="text-dark text-decoration-none">
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

                <!-- Calendar Sidebar (Using identical Berita Widget style) -->
                <div class="col-lg-4">
                    <div class="berita-widget">
                        <h4 class="berita-widget__title accent-gold">
                            <i class="fas fa-info-circle text-warning"></i>
                            <span>Informasi Jadwal Acara</span>
                        </h4>
                        <p class="text-muted small mb-3" style="font-size: 13.5px; line-height: 1.6;">
                            Jadwal pelaksanaan disusun secara berkala oleh Biro Pengadaan Barang dan Jasa Provinsi Lampung. Peserta yang terdaftar akan menerima notifikasi resmi.
                        </p>
                        <a href="<?= site_url('event') ?>" class="btn btn-primary w-100 radius-8 fw-bold" style="font-size: 13px;">
                            <i class="fas fa-th-large me-1"></i> Kembali ke Katalog Acara
                        </a>
                    </div>

                    <!-- Statistik Agenda PBJ in Calendar view -->
                    <div class="berita-widget berita-stats-widget">
                        <h4 class="berita-widget__title accent-blue">
                            <i class="fas fa-chart-line text-primary"></i>
                            <span>Statistik Agenda PBJ</span>
                        </h4>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="<?= site_url('event') ?>" class="text-decoration-none">
                                    <div class="p-2 radius-8 border bg-light h-100 transition-all hover-shadow-sm text-center">
                                        <div class="fw-bold text-dark" style="font-size: 18px;"><?= number_format($stats['total_events']) ?></div>
                                        <small class="text-muted d-block fw-semibold" style="font-size: 11px; line-height: 1.25;">Total Agenda Terdaftar</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="<?= site_url('event/agenda') ?>" class="text-decoration-none">
                                    <div class="p-2 radius-8 border bg-light h-100 transition-all hover-shadow-sm text-center">
                                        <div class="fw-bold text-warning-emphasis" style="font-size: 18px;"><?= number_format($stats['upcoming_events']) ?></div>
                                        <small class="text-muted d-block fw-semibold" style="font-size: 11px; line-height: 1.25;">Kegiatan Mendatang</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="<?= site_url('event') ?>" class="text-decoration-none">
                                    <div class="p-2 radius-8 border bg-light h-100 transition-all hover-shadow-sm text-center">
                                        <div class="fw-bold text-success" style="font-size: 18px;"><?= number_format($stats['open_registration']) ?></div>
                                        <small class="text-muted d-block fw-semibold" style="font-size: 11px; line-height: 1.25;">Pendaftaran Dibuka</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="<?= site_url('event') ?>" class="text-decoration-none">
                                    <div class="p-2 radius-8 border bg-light h-100 transition-all hover-shadow-sm text-center">
                                        <div class="fw-bold text-primary" style="font-size: 18px;"><?= number_format($stats['total_attendees']) ?></div>
                                        <small class="text-muted d-block fw-semibold" style="font-size: 11px; line-height: 1.25;">Peserta Terdaftar</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STANDARD CATALOG / AGENDA VIEW -->
        <?php else: ?>
            <!-- Main Grid & Sidebar Layout (Matching Berita Layout) -->
            <div class="row g-4">
                <!-- Left: Events Grid -->
                <div class="col-lg-8">
                    <?php if (empty($items)): ?>
                        <div class="berita-empty">
                            <div class="berita-empty__icon"><i class="fas fa-calendar-times"></i></div>
                            <h3 class="berita-empty__title">Tidak Ada Kegiatan yang Sesuai</h3>
                            <p class="berita-empty__desc">
                                Tidak ditemukan agenda untuk kata kunci atau kombinasi filter yang Anda pilih. Coba gunakan kata kunci lain atau tampilkan seluruh agenda pembinaan PBJ Lampung.
                            </p>
                            <a href="<?= site_url('event') ?>" class="berita-cta__btn" style="background: var(--berita-blue-500); color: #ffffff;">
                                <i class="fas fa-arrow-left"></i>
                                <span>Lihat Semua Kegiatan</span>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="row g-4" id="eventGridView">
                            <?php foreach ($items as $item): ?>
                                <div class="col-md-6">
                                    <article class="berita-card news-grid-card">
                                        <a href="<?= site_url('event/' . $item['slug']) ?>" class="berita-card__media d-block text-decoration-none position-relative">
                                            <?php if (! empty($item['cover_url'])): ?>
                                                <img src="<?= $item['cover_url'] ?>" alt="<?= esc($item['title']) ?>" loading="lazy">
                                            <?php else: ?>
                                                <div class="d-flex flex-column align-items-center justify-content-center h-100 bg-dark text-white p-3 text-center" style="background: linear-gradient(135deg, #052244 0%, #0a3d74 100%) !important;">
                                                    <i class="fas fa-calendar-alt fa-3x text-warning mb-2"></i>
                                                    <span class="badge bg-warning text-dark text-xs fw-bold"><?= esc($item['type_label']) ?></span>
                                                    <small class="text-white-50 text-xs mt-1">BegawiPengadaan Lampung</small>
                                                </div>
                                            <?php endif; ?>
                                            <span class="berita-card__cat" style="background: <?= $item['mode'] === 'online' ? '#16a34a' : ($item['mode'] === 'offline' ? '#0c5aa6' : '#d97706') ?>">
                                                <i class="fas fa-broadcast-tower me-1"></i> <?= esc(ucfirst($item['mode'])) ?>
                                            </span>
                                            <span class="berita-card__readtime">
                                                <i class="far fa-calendar-alt"></i> <?= esc($item['start_day']) ?> <?= esc($item['start_month']) ?>
                                            </span>
                                        </a>

                                        <div class="berita-card__body">
                                            <div class="berita-card__meta mb-2">
                                                <?php if (! empty($item['is_featured'])): ?>
                                                    <span class="badge bg-warning text-dark border border-warning px-2 py-1 text-xs fw-bold"><i class="fas fa-star me-1"></i> Unggulan</span>
                                                <?php endif; ?>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 text-xs fw-bold"><?= esc($item['type_label']) ?></span>
                                                <?php if (! empty($item['certificate_enabled'])): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 text-xs fw-bold"><i class="fas fa-certificate me-1"></i> Sertifikat</span>
                                                <?php endif; ?>
                                                <span>&middot;</span>
                                                <span class="text-muted"><i class="far fa-clock text-primary"></i> <?= esc($item['time_range']) ?></span>
                                            </div>

                                            <h3 class="berita-card__title">
                                                <a href="<?= site_url('event/' . $item['slug']) ?>">
                                                    <?= esc($item['title']) ?>
                                                </a>
                                            </h3>

                                            <p class="berita-card__summary">
                                                <?= esc($item['summary']) ?>
                                            </p>

                                            <div class="small text-muted mb-3 d-flex align-items-center gap-1" style="font-size: 12px;">
                                                <i class="fas fa-map-marker-alt text-danger"></i>
                                                <span class="text-truncate"><?= esc($item['location']) ?></span>
                                            </div>

                                            <div class="berita-card__footer mt-auto">
                                                <div class="text-xs fw-semibold">
                                                    <?php if ($item['is_past']): ?>
                                                        <span class="badge bg-secondary-subtle text-secondary">Kegiatan Selesai</span>
                                                    <?php elseif ($item['is_unlimited']): ?>
                                                        <span class="text-success"><i class="fas fa-check-circle me-1"></i> Kuota Fleksibel</span>
                                                    <?php elseif ($item['remaining'] > 0): ?>
                                                        <span class="text-primary"><i class="fas fa-user-check me-1"></i> Sisa <?= $item['remaining'] ?> Kursi</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning-subtle text-warning">Kuota Penuh</span>
                                                    <?php endif; ?>
                                                </div>
                                                <a href="<?= site_url('event/' . $item['slug']) ?>" class="berita-card__cta">
                                                    <span>Detail Acara</span>
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination Bar (Harmonized with Berita Pagination) -->
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
                            <div class="berita-pagination mt-4">
                                <div class="berita-pagination__info">
                                    Halaman <strong><?= $currentPage ?></strong> dari <strong><?= $totalPages ?></strong> &middot; <strong><?= number_format($stats['total_events'] ?? 0) ?></strong> total kegiatan
                                </div>
                                <nav class="berita-pagination__list" aria-label="Navigasi Halaman Event">
                                    <a href="<?= $buildEvtUrl(max(1, $currentPage - 1)) ?>"
                                        class="berita-pgbtn <?= $currentPage <= 1 ? 'is-disabled' : '' ?>"
                                        aria-label="Sebelumnya">
                                        <i class="fas fa-chevron-left"></i>
                                        <span class="d-none d-sm-inline">Sebelumnya</span>
                                    </a>

                                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                        <?php if ($p === 1 || $p === $totalPages || ($p >= $currentPage - 1 && $p <= $currentPage + 1)): ?>
                                            <a href="<?= $buildEvtUrl($p) ?>"
                                                class="berita-pgbtn <?= $p === $currentPage ? 'is-active' : '' ?>">
                                                <?= $p ?>
                                            </a>
                                        <?php elseif ($p === 2 && $currentPage > 3): ?>
                                            <span class="berita-pgbtn is-ellipsis">&hellip;</span>
                                        <?php elseif ($p === $totalPages - 1 && $currentPage < $totalPages - 2): ?>
                                            <span class="berita-pgbtn is-ellipsis">&hellip;</span>
                                        <?php endif; ?>
                                    <?php endfor; ?>

                                    <a href="<?= $buildEvtUrl(min($totalPages, $currentPage + 1)) ?>"
                                        class="berita-pgbtn <?= $currentPage >= $totalPages ? 'is-disabled' : '' ?>"
                                        aria-label="Berikutnya">
                                        <span class="d-none d-sm-inline">Berikutnya</span>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </nav>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Right: Sidebar (100% Matching Berita Widgets Structure) -->
                <div class="col-lg-4">
                    <!-- 1. Search Widget -->
                    <div class="berita-widget">
                        <h4 class="berita-widget__title">
                            <i class="fas fa-search"></i>
                            <span>Cari Agenda PBJ</span>
                        </h4>
                        <form action="<?= site_url('event') ?>" method="get" class="berita-searchbox evt-search-box" id="eventSidebarSearchForm">
                            <input type="text" id="eventSidebarSearchInput" name="q" placeholder="Cari webinar, bimtek, topik..." value="<?= esc($keyword ?? '') ?>">
                            <?php if (! empty($selectedType)): ?>
                                <input type="hidden" name="tipe" value="<?= esc($selectedType) ?>">
                            <?php endif; ?>
                            <?php if (! empty($selectedMode)): ?>
                                <input type="hidden" name="mode" value="<?= esc($selectedMode) ?>">
                            <?php endif; ?>
                            <button type="submit" aria-label="Cari"><i class="fas fa-search"></i></button>
                        </form>
                    </div>

                    <!-- 2. Category / Tipe Kegiatan Directory Widget -->
                    <div class="berita-widget">
                        <h4 class="berita-widget__title accent-gold">
                            <i class="fas fa-folder-open"></i>
                            <span>Kategori Liputan &amp; Tipe</span>
                        </h4>
                        <ul class="berita-catlist">
                            <li>
                                <a href="<?= site_url('event' . (! empty($selectedMode) ? '?mode=' . $selectedMode : '')) ?>" class="news-cat-pill <?= empty($selectedType) ? 'is-active' : '' ?>">
                                    <span class="left"><span class="dot" style="background:#0284c7"></span>Semua Tipe</span>
                                    <span class="count"><?= (int) ($stats['total_events'] ?? 0) ?></span>
                                </a>
                            </li>
                            <?php foreach ($dashboardTypes as $tKey => $tLabel):
                                $dotColor = match ($tKey) {
                                    'webinar'     => '#0284c7',
                                    'bimtek'      => '#16a34a',
                                    'workshop'    => '#d97706',
                                    'sosialisasi' => '#9333ea',
                                    'seminar'     => '#dc2626',
                                    'pelatihan'   => '#0891b2',
                                    'rakor'       => '#db2777',
                                    'klinik'      => '#65a30d',
                                    default       => '#475569',
                                };
                            ?>
                                <li>
                                    <a href="<?= site_url('event?tipe=' . $tKey . (! empty($selectedMode) ? '&mode=' . $selectedMode : '')) ?>"
                                        class="news-cat-pill <?= $selectedType === $tKey ? 'is-active' : '' ?>"
                                        style="--cat-accent: <?= $dotColor ?>">
                                        <span class="left"><span class="dot" style="background: <?= $dotColor ?>"></span><?= esc($tLabel) ?></span>
                                        <span class="count"><?= (int) ($typeCounts[$tKey] ?? 0) ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- 3. Trending / Upcoming Events Widget -->
                    <div class="berita-widget">
                        <h4 class="berita-widget__title accent-red">
                            <i class="fas fa-fire"></i>
                            <span>Kegiatan Terdekat</span>
                        </h4>
                        <?php if (empty($upcomingSidebar)): ?>
                            <p class="text-sm text-muted mb-0">Belum ada kegiatan terdekat.</p>
                        <?php else: ?>
                            <?php foreach ($upcomingSidebar as $rank => $us): ?>
                                <div class="berita-trend">
                                    <div class="berita-trend__rank <?= $rank < 3 ? 'is-top' : '' ?>">
                                        <?= $rank + 1 ?>
                                    </div>
                                    <div class="berita-trend__body">
                                        <h5 class="berita-trend__title">
                                            <a href="<?= site_url('event/' . $us['slug']) ?>">
                                                <?= esc($us['title']) ?>
                                            </a>
                                        </h5>
                                        <div class="berita-trend__meta">
                                            <i class="far fa-calendar-alt"></i>
                                            <span><?= date('d M Y', strtotime($us['start_at'])) ?></span>
                                            <span>&middot;</span>
                                            <i class="fas fa-map-marker-alt text-danger"></i>
                                            <span><?= esc($us['location'] ?: 'Online via Zoom') ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- 4. Informasi Statistik Agenda PBJ (4-Box Grid) -->
                    <div class="berita-widget berita-stats-widget">
                        <h4 class="berita-widget__title accent-blue">
                            <i class="fas fa-chart-line text-primary"></i>
                            <span>Statistik Agenda PBJ</span>
                        </h4>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="<?= site_url('event') ?>" class="text-decoration-none">
                                    <div class="p-2 radius-8 border bg-light h-100 transition-all hover-shadow-sm text-center">
                                        <div class="fw-bold text-dark" style="font-size: 18px;"><?= number_format($stats['total_events']) ?></div>
                                        <small class="text-muted d-block fw-semibold" style="font-size: 11px; line-height: 1.25;">Total Agenda Terdaftar</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="<?= site_url('event/agenda') ?>" class="text-decoration-none">
                                    <div class="p-2 radius-8 border bg-light h-100 transition-all hover-shadow-sm text-center">
                                        <div class="fw-bold text-warning-emphasis" style="font-size: 18px;"><?= number_format($stats['upcoming_events']) ?></div>
                                        <small class="text-muted d-block fw-semibold" style="font-size: 11px; line-height: 1.25;">Kegiatan Mendatang</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="<?= site_url('event') ?>" class="text-decoration-none">
                                    <div class="p-2 radius-8 border bg-light h-100 transition-all hover-shadow-sm text-center">
                                        <div class="fw-bold text-success" style="font-size: 18px;"><?= number_format($stats['open_registration']) ?></div>
                                        <small class="text-muted d-block fw-semibold" style="font-size: 11px; line-height: 1.25;">Pendaftaran Dibuka</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="<?= site_url('event') ?>" class="text-decoration-none">
                                    <div class="p-2 radius-8 border bg-light h-100 transition-all hover-shadow-sm text-center">
                                        <div class="fw-bold text-primary" style="font-size: 18px;"><?= number_format($stats['total_attendees']) ?></div>
                                        <small class="text-muted d-block fw-semibold" style="font-size: 11px; line-height: 1.25;">Peserta Terdaftar</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Official Penyelenggara & Biro PBJ Bulletin -->
                    <div class="berita-widget">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="width: 44px; height: 44px; border-radius: 10px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                                <i class="fas fa-landmark"></i>
                            </div>
                            <div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small fw-bold" style="font-size: 11.5px;">PENYELENGGARA RESMI</span>
                                <h5 class="fw-bold mb-0 text-dark" style="font-size: 14.5px; line-height: 1.35;">Biro Pengadaan Barang &amp; Jasa Setda Provinsi Lampung</h5>
                            </div>
                        </div>
                        <p class="text-muted small mb-3" style="font-size: 13.5px; line-height: 1.6;">
                            Seluruh kegiatan peningkatan kapasitas diselenggarakan secara resmi untuk mendukung profesionalisme SDM pengadaan di Lingkungan Pemerintah Provinsi Lampung.
                        </p>
                        <a href="<?= site_url('kontak') ?>" class="btn btn-sm btn-outline-primary w-100 radius-8 fw-bold" style="font-size: 13px;">
                            <i class="fas fa-headset me-1"></i> Helpdesk PBJ: +62 851-1777-9783
                        </a>
                    </div>

                    <!-- 6. Tag Cloud Widget -->
                    <div class="berita-widget">
                        <h4 class="berita-widget__title accent-cyan">
                            <i class="fas fa-tags"></i>
                            <span>Topik Populer</span>
                        </h4>
                        <div class="berita-tags">
                            <a href="<?= site_url('event?q=Webinar') ?>" class="berita-tag">#WebinarNasional</a>
                            <a href="<?= site_url('event?q=Bimtek') ?>" class="berita-tag">#BimtekSPSE</a>
                            <a href="<?= site_url('event?q=Sertifikasi') ?>" class="berita-tag">#SertifikasiPBJ</a>
                            <a href="<?= site_url('event?q=Mitigasi') ?>" class="berita-tag">#MitigasiRisiko</a>
                            <a href="<?= site_url('event?q=E-Katalog') ?>" class="berita-tag">#E-KatalogV6</a>
                            <a href="<?= site_url('event?q=P3DN') ?>" class="berita-tag">#P3DNLampung</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Closing CTA Banner (Matching Berita CTA Style) -->
        <div class="berita-cta mt-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="berita-cta__eyebrow">
                        <i class="fas fa-calendar-check"></i> Agenda Pembinaan Berkelanjutan
                    </span>
                    <h3 class="berita-cta__title text-white">Tingkatkan Wawasan &amp; Pengetahuan Pengadaan Anda Bersama Tim Ahli UKPBJ Lampung</h3>
                    <p class="berita-cta__lead text-white" style="opacity: 0.92;">
                        Ingin mengadakan bimbingan teknis khusus, klinik pengadaan, atau konsultasi pendampingan untuk OPD/Instansi Anda? Hubungi Tim Pembinaan Biro PBJ Provinsi Lampung.
                    </p>
                </div>
                <div class="col-lg-4">
                    <div class="berita-cta__actions">
                        <a href="<?= site_url('kontak') ?>" class="berita-cta__btn berita-cta__btn--primary">
                            <i class="fas fa-envelope-open-text"></i>
                            <span>Hubungi Tim Pembinaan</span>
                        </a>
                        <a href="<?= site_url('arena-belajar') ?>" class="berita-cta__btn berita-cta__btn--ghost">
                            <i class="fas fa-graduation-cap"></i>
                            <span>Arena Belajar</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
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

    // Initialize Event Headline Carousel safely and synchronize indicator dots
    document.addEventListener('DOMContentLoaded', function () {
        var carouselEl = document.getElementById('eventHeadlineCarousel');
        if (carouselEl) {
            var myCarousel = null;
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

            var indicatorButtons = carouselEl.querySelectorAll('.berita-headline__indicators button, .evt-headline__indicators button, .carousel-indicators button');

            function syncIndicatorDots(targetIdx) {
                if (!indicatorButtons || indicatorButtons.length === 0) return;
                indicatorButtons.forEach(function (btn, idx) {
                    if (idx === targetIdx) {
                        btn.classList.add('active', 'is-active');
                        btn.setAttribute('aria-current', 'true');
                    } else {
                        btn.classList.remove('active', 'is-active');
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