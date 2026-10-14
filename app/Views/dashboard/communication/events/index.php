<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('pageStyles') ?>
<style>
/* Scoped Events Dashboard Styling for Maximum Contrast, Clean Alignment & Legibility */
.events-dashboard-container {
    color: #0f172a !important;
}
.events-dashboard-container * {
    box-sizing: border-box;
}

/* Force high-contrast on faint text classes */
.events-dashboard-container .text-secondary-light,
.events-dashboard-container .text-muted,
.events-dashboard-container .text-neutral-500 {
    color: #475569 !important;
}
.events-dashboard-container .text-neutral-600 {
    color: #334155 !important;
}
.events-dashboard-container .text-neutral-700 {
    color: #1e293b !important;
}
.events-dashboard-container .text-neutral-800,
.events-dashboard-container .text-neutral-900 {
    color: #0f172a !important;
}

/* Form Controls Contrast */
.events-dashboard-container .form-control,
.events-dashboard-container .form-select {
    color: #0f172a !important;
    background-color: #ffffff !important;
    border-color: #cbd5e1 !important;
    font-size: 13.5px;
    font-weight: 500;
}
.events-dashboard-container .form-control:focus,
.events-dashboard-container .form-select:focus {
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
}
.events-dashboard-container label {
    color: #0f172a !important;
    font-weight: 700 !important;
    font-size: 12px !important;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

/* KPI Cards */
.kpi-event-card {
    background: #ffffff;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px;
    padding: 16px 18px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.kpi-event-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 14px -2px rgba(0,0,0,0.06);
}

/* Action Dropdown Button */
.btn-action-dropdown {
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    color: #0f172a !important;
    border-radius: 8px !important;
    padding: 6px 10px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    transition: all 0.2s ease !important;
}
.btn-action-dropdown:hover,
.btn-action-dropdown:focus {
    background: #0284c7 !important;
    border-color: #0284c7 !important;
    color: #ffffff !important;
}
.btn-action-dropdown iconify-icon {
    color: #0f172a !important;
    font-size: 18px !important;
}
.btn-action-dropdown:hover iconify-icon,
.btn-action-dropdown:focus iconify-icon {
    color: #ffffff !important;
}

/* Event Grid Card */
.event-grid-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}
.event-grid-card:hover {
    border-color: #93c5fd;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -3px rgba(0,0,0,0.07);
}
.event-cover-wrapper {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9;
    background: #f1f5f9;
    overflow: hidden;
    flex-shrink: 0;
}
.event-cover-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.event-grid-card:hover .event-cover-img {
    transform: scale(1.02);
}
.event-cover-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 50%, #eff6ff 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #475569;
}
.event-grid-title-wrap {
    height: 48px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 12px;
}
.event-grid-info-box {
    min-height: 74px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 14px;
}

/* Type & Mode Badges */
.badge-type-pill {
    background: #ffffff;
    color: #0369a1;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    border: 1px solid #bae6fd;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.badge-mode-online {
    background: #ffffff;
    color: #047857;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    border: 1px solid #a7f3d0;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.badge-mode-offline {
    background: #ffffff;
    color: #c2410c;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    border: 1px solid #fed7aa;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.badge-mode-hybrid {
    background: #ffffff;
    color: #a21caf;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    border: 1px solid #f5d0fe;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Status Badges Override for High Legibility */
.events-dashboard-container .status-badge-custom {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
}
.status-badge-open {
    background-color: #dcfce7 !important;
    color: #15803d !important;
    border: 1px solid #bbf7d0 !important;
}
.status-badge-coming-soon {
    background-color: #fef3c7 !important;
    color: #92400e !important;
    border: 1px solid #fde68a !important;
}
.status-badge-published {
    background-color: #e0f2fe !important;
    color: #0369a1 !important;
    border: 1px solid #bae6fd !important;
}
.status-badge-full {
    background-color: #f3e8ff !important;
    color: #7e22ce !important;
    border: 1px solid #e9d5ff !important;
}
.status-badge-finished {
    background-color: #f1f5f9 !important;
    color: #334155 !important;
    border: 1px solid #cbd5e1 !important;
}
.status-badge-cancelled {
    background-color: #fee2e2 !important;
    color: #b91c1c !important;
    border: 1px solid #fecaca !important;
}
.status-badge-draft {
    background-color: #f8fafc !important;
    color: #475569 !important;
    border: 1px solid #cbd5e1 !important;
}

/* Table Style Polish */
.events-table-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}
.events-table th {
    background: #f8fafc !important;
    color: #0f172a !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 14px 16px !important;
    border-bottom: 2px solid #e2e8f0 !important;
}
.events-table td {
    padding: 14px 16px !important;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    color: #0f172a;
}
.events-table tbody tr:hover {
    background-color: #f8fafc;
}

/* Pagination Bar */
.events-pagination {
    display: flex;
    align-items: center;
    gap: 4px;
}
.events-pagination .page-link {
    border-radius: 8px !important;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    transition: all 0.2s ease;
}
.events-pagination .page-link:hover {
    background: #f1f5f9;
    color: #0284c7;
    border-color: #93c5fd;
}
.events-pagination .page-item.active .page-link {
    background: #0284c7 !important;
    border-color: #0284c7 !important;
    color: #ffffff !important;
}
.events-pagination .page-item.disabled .page-link {
    color: #94a3b8;
    background: #f8fafc;
    border-color: #e2e8f0;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$items       = $items ?? [];
$status      = $filterStatus ?? null;
$keyword     = $filterKeyword ?? null;
$mode        = $filterMode ?? null;
$type        = $filterType ?? null;
$viewMode    = $viewMode ?? 'grid';
$pagination  = $pagination ?? ['page' => 1, 'perPage' => 12, 'total' => count($items), 'totalPages' => 1, 'from' => 1, 'to' => count($items)];
$stats       = $stats ?? ['total' => count($items), 'open' => 0, 'coming_soon' => 0, 'finished' => 0, 'total_registered' => 0, 'total_attended' => 0];

$buildFilterUrl = function (array $overrides = []) use ($status, $keyword, $mode, $type, $viewMode, $pagination): string {
    $params = [
        'q'      => $keyword,
        'status' => $status,
        'mode'   => $mode,
        'type'   => $type,
        'view'   => $viewMode,
        'page'   => $pagination['page'],
    ];
    $merged = array_filter(array_merge($params, $overrides), fn($v) => $v !== null && $v !== '');
    return site_url('dashboard/komunikasi/event-agenda') . (! empty($merged) ? '?' . http_build_query($merged) : '');
};
?>

<div class="events-dashboard-container">
    <!-- Header Title & Action Toolbar -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-bold text-neutral-900 mb-1" style="color: #0f172a;">Event &amp; Agenda PBJ</h6>
            <p class="text-neutral-600 mb-0 font-medium" style="color: #475569;">Kelola seminar pengadaan, bimbingan teknis, workshop regulasi, presensi kehadiran, dan batch sertifikat.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= site_url('dashboard/komunikasi/registrasi-event') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-10 d-flex align-items-center gap-2 text-sm fw-semibold text-neutral-700" style="background:#ffffff; border-color:#cbd5e1;">
                <iconify-icon icon="solar:users-group-rounded-bold" class="text-lg text-primary-600"></iconify-icon>
                <span>Data Pendaftar</span>
            </a>
            <a href="<?= route_to('dashboard.communication.events.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2 fw-bold text-sm shadow-sm">
                <iconify-icon icon="solar:add-circle-bold" class="text-xl"></iconify-icon>
                <span>Tambah Event Baru</span>
            </a>
        </div>
    </div>

    <!-- KPI Metrics Ribbon (6 Uniform Equal-Height Cards) -->
    <div class="row row-cols-xxl-6 row-cols-md-3 row-cols-2 g-3 mb-24">
        <div class="col">
            <div class="kpi-event-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-neutral-700" style="color: #475569;">Total Agenda</span>
                    <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center text-lg" style="background: #e0f2fe; color: #0284c7;">
                        <iconify-icon icon="solar:calendar-bold"></iconify-icon>
                    </div>
                </div>
                <h4 class="fw-bold mb-0 text-neutral-900" style="color: #0f172a;"><?= number_format($stats['total'] ?? 0) ?></h4>
            </div>
        </div>
        <div class="col">
            <div class="kpi-event-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-neutral-700" style="color: #475569;">Pendaftaran Buka</span>
                    <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center text-lg" style="background: #dcfce7; color: #16a34a;">
                        <iconify-icon icon="solar:user-plus-bold"></iconify-icon>
                    </div>
                </div>
                <h4 class="fw-bold mb-0" style="color: #15803d;"><?= number_format($stats['open'] ?? 0) ?></h4>
            </div>
        </div>
        <div class="col">
            <div class="kpi-event-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-neutral-700" style="color: #475569;">Segera Hadir</span>
                    <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center text-lg" style="background: #fef3c7; color: #d97706;">
                        <iconify-icon icon="solar:clock-circle-bold"></iconify-icon>
                    </div>
                </div>
                <h4 class="fw-bold mb-0" style="color: #b45309;"><?= number_format($stats['coming_soon'] ?? 0) ?></h4>
            </div>
        </div>
        <div class="col">
            <div class="kpi-event-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-neutral-700" style="color: #475569;">Telah Selesai</span>
                    <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center text-lg" style="background: #f1f5f9; color: #475569;">
                        <iconify-icon icon="solar:check-read-bold"></iconify-icon>
                    </div>
                </div>
                <h4 class="fw-bold mb-0 text-neutral-700" style="color: #334155;"><?= number_format($stats['finished'] ?? 0) ?></h4>
            </div>
        </div>
        <div class="col">
            <div class="kpi-event-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-neutral-700" style="color: #475569;">Total Pendaftar</span>
                    <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center text-lg" style="background: #ede9fe; color: #7c3aed;">
                        <iconify-icon icon="solar:users-group-two-rounded-bold"></iconify-icon>
                    </div>
                </div>
                <h4 class="fw-bold mb-0" style="color: #6d28d9;"><?= number_format($stats['total_registered'] ?? 0) ?></h4>
            </div>
        </div>
        <div class="col">
            <div class="kpi-event-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-neutral-700" style="color: #475569;">Presensi Sah</span>
                    <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center text-lg" style="background: #ccfbf1; color: #0d9488;">
                        <iconify-icon icon="solar:verified-check-bold"></iconify-icon>
                    </div>
                </div>
                <h4 class="fw-bold mb-0" style="color: #0f766e;"><?= number_format($stats['total_attended'] ?? 0) ?></h4>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar Card -->
    <div class="card radius-12 mb-24 border shadow-none" style="background:#ffffff; border-color:#e2e8f0;">
        <div class="card-body p-20">
            <form method="get" action="<?= site_url('dashboard/komunikasi/event-agenda') ?>">
                <input type="hidden" name="view" value="<?= esc($viewMode) ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-xl-4 col-lg-3 col-md-6">
                        <label class="form-label text-xs fw-bold text-neutral-800 text-uppercase mb-1" style="color:#0f172a;">Cari Agenda</label>
                        <div class="position-relative">
                            <input type="text" name="q" class="form-control radius-8 ps-40 font-medium" value="<?= esc($keyword ?? '') ?>" placeholder="Cari judul agenda, lokasi, materi..." style="border-color: #cbd5e1; color: #0f172a;">
                            <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-16 text-neutral-500 text-lg"></iconify-icon>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-6">
                        <label class="form-label text-xs fw-bold text-neutral-800 text-uppercase mb-1" style="color:#0f172a;">Status</label>
                        <select name="status" class="form-select radius-8 fw-semibold text-neutral-900" style="border-color: #cbd5e1;">
                            <option value="">Semua Status</option>
                            <option value="open_registration" <?= $status === 'open_registration' ? 'selected' : '' ?>>Pendaftaran Dibuka</option>
                            <option value="coming_soon" <?= $status === 'coming_soon' ? 'selected' : '' ?>>Segera Hadir</option>
                            <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Dipublikasikan</option>
                            <option value="full" <?= $status === 'full' ? 'selected' : '' ?>>Kuota Penuh</option>
                            <option value="finished" <?= $status === 'finished' ? 'selected' : '' ?>>Selesai</option>
                            <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                            <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-6">
                        <label class="form-label text-xs fw-bold text-neutral-800 text-uppercase mb-1" style="color:#0f172a;">Mode Pelaksanaan</label>
                        <select name="mode" class="form-select radius-8 fw-semibold text-neutral-900" style="border-color: #cbd5e1;">
                            <option value="">Semua Mode</option>
                            <option value="online" <?= $mode === 'online' ? 'selected' : '' ?>>Online (Daring)</option>
                            <option value="offline" <?= $mode === 'offline' ? 'selected' : '' ?>>Offline (Tatap Muka)</option>
                            <option value="hybrid" <?= $mode === 'hybrid' ? 'selected' : '' ?>>Hybrid</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-6">
                        <label class="form-label text-xs fw-bold text-neutral-800 text-uppercase mb-1" style="color:#0f172a;">Tipe Kegiatan</label>
                        <select name="type" class="form-select radius-8 fw-semibold text-neutral-900" style="border-color: #cbd5e1;">
                            <option value="">Semua Tipe</option>
                            <option value="webinar" <?= $type === 'webinar' ? 'selected' : '' ?>>Webinar</option>
                            <option value="workshop" <?= $type === 'workshop' ? 'selected' : '' ?>>Workshop</option>
                            <option value="seminar" <?= $type === 'seminar' ? 'selected' : '' ?>>Seminar</option>
                            <option value="bimtek" <?= $type === 'bimtek' ? 'selected' : '' ?>>Bimtek</option>
                            <option value="sosialisasi" <?= $type === 'sosialisasi' ? 'selected' : '' ?>>Sosialisasi</option>
                            <option value="pelatihan" <?= $type === 'pelatihan' ? 'selected' : '' ?>>Pelatihan</option>
                            <option value="rakor" <?= $type === 'rakor' ? 'selected' : '' ?>>Rakor</option>
                            <option value="klinik" <?= $type === 'klinik' ? 'selected' : '' ?>>Klinik</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-12 d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-primary-600 radius-8 flex-grow-1 d-flex align-items-center justify-content-center gap-2 fw-bold text-sm">
                            <iconify-icon icon="solar:filter-bold"></iconify-icon> Filter
                        </button>
                        <a href="<?= site_url('dashboard/komunikasi/event-agenda?view=' . esc($viewMode)) ?>" class="btn btn-outline-neutral-300 radius-8 text-neutral-700" title="Reset Filter" style="border-color: #cbd5e1; background:#ffffff;">
                            <iconify-icon icon="solar:restart-bold" class="text-lg"></iconify-icon>
                        </a>
                        <!-- View Mode Switcher -->
                        <div class="btn-group border radius-8 overflow-hidden" style="border-color:#cbd5e1 !important;">
                            <a href="<?= $buildFilterUrl(['view' => 'grid']) ?>" class="btn btn-sm <?= $viewMode === 'grid' ? 'btn-primary-600 text-white' : 'btn-light text-neutral-700 bg-white' ?>" title="Tampilan Grid">
                                <iconify-icon icon="solar:widget-4-bold"></iconify-icon>
                            </a>
                            <a href="<?= $buildFilterUrl(['view' => 'table']) ?>" class="btn btn-sm <?= $viewMode === 'table' ? 'btn-primary-600 text-white' : 'btn-light text-neutral-700 bg-white' ?>" title="Tampilan Tabel">
                                <iconify-icon icon="solar:list-bold"></iconify-icon>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Content Area -->
    <?php if (empty($items)): ?>
        <div class="card p-40 text-center radius-12 border bg-base shadow-none">
            <div class="w-64-px h-64-px rounded-circle bg-primary-50 text-primary-600 d-flex align-items-center justify-content-center mx-auto mb-16 text-3xl">
                <iconify-icon icon="solar:calendar-outline"></iconify-icon>
            </div>
            <h5 class="fw-bold text-neutral-900 mb-2" style="color: #0f172a;">Belum Ada Event / Agenda</h5>
            <p class="text-neutral-600 mb-20 max-w-500-px mx-auto" style="color: #475569;">Tidak ada kegiatan yang sesuai dengan kriteria filter pencarian Anda. Silakan ubah kata kunci atau tambahkan kegiatan baru.</p>
            <div class="d-flex justify-content-center gap-2">
                <a href="<?= site_url('dashboard/komunikasi/event-agenda') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-8 text-sm fw-semibold">Reset Filter</a>
                <a href="<?= route_to('dashboard.communication.events.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-8 text-sm fw-bold">Tambah Event Baru</a>
            </div>
        </div>
    <?php else: ?>

        <?php if ($viewMode === 'table'): ?>
            <!-- TABLE VIEW -->
            <div class="events-table-card mb-24">
                <div class="table-responsive">
                    <table class="table bordered-table sm-table mb-0 align-middle events-table">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Cover</th>
                                <th>Judul &amp; Tipe Kegiatan</th>
                                <th style="width: 220px;">Jadwal &amp; Lokasi</th>
                                <th style="width: 180px;">Kapasitas &amp; Pendaftar</th>
                                <th class="text-center" style="width: 140px;">Status</th>
                                <th class="text-center" style="width: 110px;">Presensi</th>
                                <th class="text-center" style="width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $e): ?>
                                <?php
                                    $coverUrl = ! empty($e['cover_file_id']) ? base_url('file/' . (int) $e['cover_file_id']) : null;
                                    $modeKey = strtolower($e['mode'] ?? 'online');
                                    $statusKey = $e['status'] ?? 'draft';
                                    $capacity = (int) ($e['capacity'] ?? 0);
                                    $registered = (int) ($e['registered_count'] ?? 0);
                                    $present = (int) ($e['present_count'] ?? 0);
                                    $pct = $capacity > 0 ? min(100, round(($registered / $capacity) * 100)) : 0;
                                ?>
                                <tr>
                                    <td>
                                        <div class="w-80-px h-48-px rounded-8 overflow-hidden bg-neutral-100 border d-flex align-items-center justify-content-center" style="border-color: #cbd5e1 !important;">
                                            <?php if ($coverUrl): ?>
                                                <img src="<?= esc($coverUrl) ?>" alt="Cover" style="width:100%;height:100%;object-fit:cover;">
                                            <?php else: ?>
                                                <iconify-icon icon="solar:gallery-wide-bold" class="text-neutral-500 text-xl"></iconify-icon>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                            <span class="badge-type-pill">
                                                <?= esc(ucfirst($e['type'] ?? 'Webinar')) ?>
                                            </span>
                                            <?php if ($modeKey === 'online'): ?>
                                                <span class="badge-mode-online"><iconify-icon icon="solar:videocamera-record-bold" class="text-xs"></iconify-icon> Online</span>
                                            <?php elseif ($modeKey === 'offline'): ?>
                                                <span class="badge-mode-offline"><iconify-icon icon="solar:map-point-bold" class="text-xs"></iconify-icon> Offline</span>
                                            <?php else: ?>
                                                <span class="badge-mode-hybrid"><iconify-icon icon="solar:transmission-bold" class="text-xs"></iconify-icon> Hybrid</span>
                                            <?php endif; ?>
                                            <?php if (! empty($e['certificate_enabled'])): ?>
                                                <span class="badge bg-success-50 text-success-800 text-xs px-6 py-2 rounded border border-success-200" title="Tersedia E-Sertifikat">
                                                    <iconify-icon icon="solar:diploma-bold"></iconify-icon> Sertifikat
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <a href="<?= route_to('dashboard.communication.events.detail', (string) $e['id']) ?>" class="fw-bold text-neutral-900 text-hover-primary-600 text-sm line-clamp-1 mb-1 d-block" style="color: #0f172a;">
                                            <?= esc($e['title'] ?? '(Tanpa judul event)') ?>
                                        </a>
                                        <small class="text-neutral-600 font-monospace text-xs" style="color: #64748b;">/event/<?= esc($e['slug']) ?></small>
                                    </td>
                                    <td>
                                        <div class="text-xs fw-bold text-neutral-900 mb-1 d-flex align-items-center gap-1" style="color: #0f172a;">
                                            <iconify-icon icon="solar:calendar-date-bold" class="text-primary-600 text-sm"></iconify-icon>
                                            <?= ! empty($e['start_at']) ? esc(date('d M Y, H:i', strtotime($e['start_at']))) : 'Belum ditentukan' ?>
                                        </div>
                                        <div class="text-xs text-neutral-700 line-clamp-1 d-flex align-items-center gap-1" style="color: #475569;">
                                            <iconify-icon icon="solar:map-point-bold" class="text-danger-600 text-xs flex-shrink-0"></iconify-icon>
                                            <span><?= esc($e['location'] ?: ($e['meeting_url'] ?: 'Daring / Online')) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-between align-items-center text-xs mb-1">
                                            <span class="fw-bold text-neutral-900" style="color:#0f172a;"><?= number_format($registered) ?> Peserta</span>
                                            <span class="text-neutral-600 font-medium">/ <?= $capacity > 0 ? number_format($capacity) : '&infin;' ?></span>
                                        </div>
                                        <?php if ($capacity > 0): ?>
                                            <div class="progress progress-xs rounded-pill bg-neutral-200" style="height: 6px;">
                                                <div class="progress-bar <?= $pct >= 90 ? 'bg-danger-600' : ($pct >= 70 ? 'bg-warning-600' : 'bg-primary-600') ?>" style="width: <?= $pct ?>%;"></div>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-xs text-neutral-500">Kuota Tak Terbatas</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($statusKey === 'open_registration'): ?>
                                            <span class="status-badge-custom status-badge-open"><iconify-icon icon="solar:check-circle-bold" class="text-xs"></iconify-icon> Buka</span>
                                        <?php elseif ($statusKey === 'coming_soon'): ?>
                                            <span class="status-badge-custom status-badge-coming-soon"><iconify-icon icon="solar:clock-circle-bold" class="text-xs"></iconify-icon> Segera</span>
                                        <?php elseif ($statusKey === 'published'): ?>
                                            <span class="status-badge-custom status-badge-published"><iconify-icon icon="solar:global-bold" class="text-xs"></iconify-icon> Publik</span>
                                        <?php elseif ($statusKey === 'full'): ?>
                                            <span class="status-badge-custom status-badge-full"><iconify-icon icon="solar:forbidden-circle-bold" class="text-xs"></iconify-icon> Penuh</span>
                                        <?php elseif ($statusKey === 'finished'): ?>
                                            <span class="status-badge-custom status-badge-finished"><iconify-icon icon="solar:flag-bold" class="text-xs"></iconify-icon> Selesai</span>
                                        <?php elseif ($statusKey === 'cancelled'): ?>
                                            <span class="status-badge-custom status-badge-cancelled"><iconify-icon icon="solar:close-circle-bold" class="text-xs"></iconify-icon> Batal</span>
                                        <?php else: ?>
                                            <span class="status-badge-custom status-badge-draft"><iconify-icon icon="solar:pen-2-bold" class="text-xs"></iconify-icon> Draft</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-teal-50 text-teal-800 border border-teal-200 fw-bold px-8 py-3 text-xs" style="background: #f0fdfa; color: #0f766e; border-color: #99f6e4;">
                                            <iconify-icon icon="solar:check-circle-bold" class="text-xs"></iconify-icon> <?= number_format($present) ?> Hadir
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button type="button" class="btn-action-dropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Tindakan">
                                                <iconify-icon icon="solar:menu-dots-bold"></iconify-icon>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8 border-neutral-200 py-6" style="min-width: 190px;">
                                                <li>
                                                    <a class="dropdown-item text-xs fw-semibold py-8 px-14 d-flex align-items-center gap-2 text-neutral-800" href="<?= route_to('dashboard.communication.events.detail', (string) $e['id']) ?>">
                                                        <iconify-icon icon="solar:eye-bold" class="text-primary-600 text-sm"></iconify-icon>
                                                        <span>Detail &amp; Peserta</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-xs fw-semibold py-8 px-14 d-flex align-items-center gap-2 text-neutral-800" href="<?= route_to('dashboard.communication.events.edit', (string) $e['id']) ?>">
                                                        <iconify-icon icon="solar:pen-2-bold" class="text-warning-600 text-sm"></iconify-icon>
                                                        <span>Ubah Event</span>
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider my-4"></li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-xs fw-semibold py-8 px-14 d-flex align-items-center gap-2 text-danger-600 border-0 bg-transparent w-100 text-start"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#confirmationActionModal"
                                                            data-action="<?= route_to('dashboard.communication.events.delete', (string) $e['id']) ?>"
                                                            data-method="POST"
                                                            data-title="Hapus Event / Agenda"
                                                            data-message="Apakah Anda yakin ingin menghapus agenda &quot;<?= esc($e['title'] ?? '') ?>&quot;?"
                                                            data-btn-text="Ya, Hapus"
                                                            data-btn-class="btn-danger-600"
                                                            data-icon="fluent:delete-24-regular"
                                                            data-icon-color="danger">
                                                        <iconify-icon icon="solar:trash-bin-trash-bold" class="text-sm"></iconify-icon>
                                                        <span>Hapus Agenda</span>
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else: ?>

            <!-- GRID VIEW (Cards Layout with Uniform Heights) -->
            <div class="row row-cols-xxl-3 row-cols-md-2 row-cols-1 g-4 mb-24">
                <?php foreach ($items as $e): ?>
                    <?php
                        $coverUrl = ! empty($e['cover_file_id']) ? base_url('file/' . (int) $e['cover_file_id']) : null;
                        $modeKey = strtolower($e['mode'] ?? 'online');
                        $statusKey = $e['status'] ?? 'draft';
                        $capacity = (int) ($e['capacity'] ?? 0);
                        $registered = (int) ($e['registered_count'] ?? 0);
                        $present = (int) ($e['present_count'] ?? 0);
                        $pct = $capacity > 0 ? min(100, round(($registered / $capacity) * 100)) : 0;
                    ?>
                    <div class="col">
                        <div class="event-grid-card">
                            <!-- Cover Banner (16:9) -->
                            <div class="event-cover-wrapper">
                                <?php if ($coverUrl): ?>
                                    <img src="<?= esc($coverUrl) ?>" alt="Cover" class="event-cover-img">
                                <?php else: ?>
                                    <div class="event-cover-placeholder">
                                        <iconify-icon icon="solar:calendar-mark-bold-duotone" class="text-4xl text-primary-500 mb-1"></iconify-icon>
                                        <span class="text-xs fw-bold text-neutral-800">BegawiPBJ Agenda</span>
                                    </div>
                                <?php endif; ?>

                                <!-- Floating Status Badge -->
                                <div class="position-absolute top-0 end-0 m-12">
                                    <?php if ($statusKey === 'open_registration'): ?>
                                        <span class="status-badge-custom status-badge-open shadow-sm"><iconify-icon icon="solar:check-circle-bold" class="text-xs"></iconify-icon> Buka</span>
                                    <?php elseif ($statusKey === 'coming_soon'): ?>
                                        <span class="status-badge-custom status-badge-coming-soon shadow-sm"><iconify-icon icon="solar:clock-circle-bold" class="text-xs"></iconify-icon> Segera</span>
                                    <?php elseif ($statusKey === 'published'): ?>
                                        <span class="status-badge-custom status-badge-published shadow-sm"><iconify-icon icon="solar:global-bold" class="text-xs"></iconify-icon> Publik</span>
                                    <?php elseif ($statusKey === 'full'): ?>
                                        <span class="status-badge-custom status-badge-full shadow-sm"><iconify-icon icon="solar:forbidden-circle-bold" class="text-xs"></iconify-icon> Penuh</span>
                                    <?php elseif ($statusKey === 'finished'): ?>
                                        <span class="status-badge-custom status-badge-finished shadow-sm"><iconify-icon icon="solar:flag-bold" class="text-xs"></iconify-icon> Selesai</span>
                                    <?php elseif ($statusKey === 'cancelled'): ?>
                                        <span class="status-badge-custom status-badge-cancelled shadow-sm"><iconify-icon icon="solar:close-circle-bold" class="text-xs"></iconify-icon> Batal</span>
                                    <?php else: ?>
                                        <span class="status-badge-custom status-badge-draft shadow-sm"><iconify-icon icon="solar:pen-2-bold" class="text-xs"></iconify-icon> Draft</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Floating Mode & Type Badge -->
                                <div class="position-absolute bottom-0 start-0 m-12 d-flex gap-1 flex-wrap">
                                    <span class="badge-type-pill shadow-sm">
                                        <?= esc(ucfirst($e['type'] ?? 'Workshop')) ?>
                                    </span>
                                    <?php if ($modeKey === 'online'): ?>
                                        <span class="badge-mode-online shadow-sm"><iconify-icon icon="solar:videocamera-record-bold" class="text-xs"></iconify-icon> Online</span>
                                    <?php elseif ($modeKey === 'offline'): ?>
                                        <span class="badge-mode-offline shadow-sm"><iconify-icon icon="solar:map-point-bold" class="text-xs"></iconify-icon> Offline</span>
                                    <?php else: ?>
                                        <span class="badge-mode-hybrid shadow-sm"><iconify-icon icon="solar:transmission-bold" class="text-xs"></iconify-icon> Hybrid</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="card-body p-20 d-flex flex-column flex-grow-1">
                                <!-- Title with clamped height -->
                                <div class="event-grid-title-wrap">
                                    <a href="<?= route_to('dashboard.communication.events.detail', (string) $e['id']) ?>" class="fw-bold text-neutral-900 text-hover-primary-600 text-base line-clamp-2 transition-2" style="color: #0f172a; line-height: 1.45;">
                                        <?= esc($e['title'] ?? '(Tanpa judul event)') ?>
                                    </a>
                                </div>

                                <!-- Schedule & Venue Details (Fixed uniform box) -->
                                <div class="event-grid-info-box">
                                    <div class="d-flex align-items-center gap-2 text-xs fw-bold text-neutral-900 mb-1" style="color: #0f172a;">
                                        <iconify-icon icon="solar:calendar-date-bold" class="text-primary-600 text-base flex-shrink-0"></iconify-icon>
                                        <span><?= ! empty($e['start_at']) ? esc(date('d M Y, H:i', strtotime($e['start_at']))) : 'Jadwal belum ditentukan' ?> WIB</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 text-xs text-neutral-700 line-clamp-1" style="color: #475569;">
                                        <iconify-icon icon="solar:map-point-bold" class="text-danger-600 text-base flex-shrink-0"></iconify-icon>
                                        <span title="<?= esc($e['location'] ?: ($e['meeting_url'] ?: 'Online / Daring')) ?>">
                                            <?= esc($e['location'] ?: ($e['meeting_url'] ?: 'Daring / Online Platform')) ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Registration Quota Progress -->
                                <div class="mb-16">
                                    <div class="d-flex justify-content-between align-items-center text-xs mb-1">
                                        <span class="text-neutral-700 font-semibold" style="color:#334155;">Pendaftar: <strong class="text-neutral-900" style="color:#0f172a;"><?= number_format($registered) ?></strong></span>
                                        <span class="text-neutral-600 font-medium" style="color:#475569;">Kuota: <strong><?= $capacity > 0 ? number_format($capacity) : '&infin;' ?></strong></span>
                                    </div>
                                    <?php if ($capacity > 0): ?>
                                        <div class="progress progress-xs rounded-pill bg-neutral-200" style="height: 6px;">
                                            <div class="progress-bar <?= $pct >= 90 ? 'bg-danger-600' : ($pct >= 70 ? 'bg-warning-600' : 'bg-primary-600') ?>" style="width: <?= $pct ?>%;"></div>
                                        </div>
                                    <?php else: ?>
                                        <div class="progress progress-xs rounded-pill bg-neutral-200" style="height: 6px;">
                                            <div class="progress-bar bg-success-600" style="width: 100%;"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Card Footer Actions -->
                                <div class="pt-14 border-top mt-auto d-flex align-items-center justify-content-between" style="border-color: #f1f5f9 !important;">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-teal-50 text-teal-800 border border-teal-200 text-xs px-8 py-3 fw-bold" style="background:#f0fdfa; color:#0f766e; border-color:#99f6e4;">
                                            <iconify-icon icon="solar:check-circle-bold"></iconify-icon> <?= number_format($present) ?> Hadir
                                        </span>
                                        <?php if (! empty($e['attendance_code'])): ?>
                                            <span class="badge bg-neutral-100 text-neutral-800 font-monospace text-xs px-6 py-3 border" style="border-color:#cbd5e1;" title="Kode Presensi">
                                                <?= esc($e['attendance_code']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="d-flex align-items-center gap-1">
                                        <a href="<?= route_to('dashboard.communication.events.detail', (string) $e['id']) ?>" class="btn btn-outline-primary-600 btn-sm radius-8 px-10 py-5 text-xs fw-bold d-inline-flex align-items-center gap-1">
                                            <span>Detail</span>
                                            <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                                        </a>

                                        <div class="dropdown">
                                            <button type="button" class="btn-action-dropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Lainnya">
                                                <iconify-icon icon="solar:menu-dots-bold"></iconify-icon>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8 border-neutral-200 py-6" style="min-width: 190px;">
                                                <li>
                                                    <a class="dropdown-item text-xs fw-semibold py-8 px-14 d-flex align-items-center gap-2 text-neutral-800" href="<?= route_to('dashboard.communication.events.edit', (string) $e['id']) ?>">
                                                        <iconify-icon icon="solar:pen-2-bold" class="text-warning-600 text-sm"></iconify-icon>
                                                        <span>Ubah Event</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-xs fw-semibold py-8 px-14 d-flex align-items-center gap-2 text-neutral-800" href="<?= site_url('dashboard/komunikasi/registrasi-event?event_id=' . (int) $e['id']) ?>">
                                                        <iconify-icon icon="solar:users-group-rounded-bold" class="text-primary-600 text-sm"></iconify-icon>
                                                        <span>Daftar Peserta</span>
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider my-4"></li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-xs fw-semibold py-8 px-14 d-flex align-items-center gap-2 text-danger-600 border-0 bg-transparent w-100 text-start"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#confirmationActionModal"
                                                            data-action="<?= route_to('dashboard.communication.events.delete', (string) $e['id']) ?>"
                                                            data-method="POST"
                                                            data-title="Hapus Event / Agenda"
                                                            data-message="Apakah Anda yakin ingin menghapus agenda &quot;<?= esc($e['title'] ?? '') ?>&quot;?"
                                                            data-btn-text="Ya, Hapus"
                                                            data-btn-class="btn-danger-600"
                                                            data-icon="fluent:delete-24-regular"
                                                            data-icon-color="danger">
                                                        <iconify-icon icon="solar:trash-bin-trash-bold" class="text-sm"></iconify-icon>
                                                        <span>Hapus Agenda</span>
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

        <!-- Pagination Bar -->
        <?php if (($pagination['totalPages'] ?? 1) > 1): ?>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-16 bg-white radius-12 border mb-24" style="border-color: #e2e8f0;">
                <span class="text-xs fw-bold text-neutral-700" style="color: #475569;">
                    Menampilkan <strong><?= $pagination['from'] ?></strong> - <strong><?= $pagination['to'] ?></strong> dari <strong><?= number_format($pagination['total']) ?></strong> event
                </span>
                <nav aria-label="Navigasi Halaman Event">
                    <ul class="pagination events-pagination mb-0">
                        <li class="page-item <?= $pagination['page'] <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $pagination['page'] > 1 ? $buildFilterUrl(['page' => $pagination['page'] - 1]) : '#' ?>" aria-label="Sebelumnya">
                                <iconify-icon icon="solar:alt-arrow-left-linear" class="align-middle"></iconify-icon>
                            </a>
                        </li>
                        <?php
                        $startPage = max(1, $pagination['page'] - 2);
                        $endPage   = min($pagination['totalPages'], $pagination['page'] + 2);
                        if ($startPage > 1) {
                            echo '<li class="page-item"><a class="page-link" href="' . $buildFilterUrl(['page' => 1]) . '">1</a></li>';
                            if ($startPage > 2) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                        }
                        for ($p = $startPage; $p <= $endPage; $p++):
                        ?>
                            <li class="page-item <?= $p === $pagination['page'] ? 'active' : '' ?>">
                                <a class="page-link" href="<?= $buildFilterUrl(['page' => $p]) ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php
                        if ($endPage < $pagination['totalPages']) {
                            if ($endPage < $pagination['totalPages'] - 1) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                            echo '<li class="page-item"><a class="page-link" href="' . $buildFilterUrl(['page' => $pagination['totalPages']]) . '">' . $pagination['totalPages'] . '</a></li>';
                        }
                        ?>
                        <li class="page-item <?= $pagination['page'] >= $pagination['totalPages'] ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $pagination['page'] < $pagination['totalPages'] ? $buildFilterUrl(['page' => $pagination['page'] + 1]) : '#' ?>" aria-label="Berikutnya">
                                <iconify-icon icon="solar:alt-arrow-right-linear" class="align-middle"></iconify-icon>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<?= $this->endSection() ?>
