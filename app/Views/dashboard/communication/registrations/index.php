<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('pageStyles') ?>
<style>
/* Bulletproof Jitter-Free & Shake-Free Scroll at Viewport Extremities */
html {
    scroll-behavior: auto !important;
    overflow-x: clip !important;
    overflow-y: scroll !important;
    overscroll-behavior: none !important;
    overscroll-behavior-y: none !important;
    overflow-anchor: none !important;
}
body {
    overflow-x: clip !important;
    overflow-y: visible !important;
    overscroll-behavior: none !important;
    overscroll-behavior-y: none !important;
    overflow-anchor: none !important;
    min-height: 100vh;
}
.dashboard-main {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    transition: margin-inline-start 0.3s ease, margin-left 0.3s ease !important;
}
.dashboard-main-body {
    flex: 1 0 auto;
}
.d-footer {
    margin-block-start: 24px !important;
    position: relative;
    z-index: 1;
}

/* High-contrast and legibility overrides for Admin Dashboard */
.text-secondary-light, .text-muted, .text-neutral-500 {
    color: #475569 !important;
}
.text-neutral-600 {
    color: #334155 !important;
}
.text-neutral-700 {
    color: #1e293b !important;
}
.text-neutral-800, .text-neutral-900 {
    color: #0f172a !important;
}
.registrations-container {
    color: #1e293b;
    overflow-x: clip;
    padding-bottom: 24px;
}
.kpi-reg-card {
    background: #ffffff;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px;
    padding: 16px 18px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.kpi-reg-card:hover {
    border-color: #93c5fd !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}
.btn-action-dropdown {
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    color: #1e293b !important;
    border-radius: 8px !important;
    padding: 6px 10px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
}
.btn-action-dropdown:hover,
.btn-action-dropdown:focus {
    background: #0284c7 !important;
    border-color: #0284c7 !important;
    color: #ffffff !important;
}
.btn-action-dropdown:hover iconify-icon,
.btn-action-dropdown:focus iconify-icon {
    color: #ffffff !important;
}
.btn-action-dropdown iconify-icon {
    color: #1e293b !important;
    font-size: 18px !important;
}
.reg-table-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}
.reg-table th {
    background: #f8fafc !important;
    color: #334155 !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 14px 16px !important;
    border-bottom: 2px solid #e2e8f0 !important;
}
.reg-table td {
    padding: 14px 16px !important;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    color: #1e293b;
}
.reg-table tbody tr:hover {
    background-color: #f8fafc;
}
.reg-pagination .page-link {
    border-radius: 8px !important;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    transition: all 0.2s ease;
}
.reg-pagination .page-link:hover {
    background: #f1f5f9;
    color: #0284c7;
    border-color: #93c5fd;
}
.reg-pagination .page-item.active .page-link {
    background: #0284c7 !important;
    border-color: #0284c7 !important;
    color: #ffffff !important;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$items       = $items ?? [];
$stats       = $stats ?? ['total' => count($items), 'registered' => 0, 'attended' => 0, 'waiting' => 0, 'cancelled' => 0];
$eventsList  = $eventsList ?? [];
$filterEvent = $filterEventId ?? null;
$status      = $filterStatus ?? null;
$keyword     = $filterKeyword ?? null;
$pagination  = $pagination ?? ['page' => 1, 'perPage' => 15, 'total' => count($items), 'totalPages' => 1, 'from' => 1, 'to' => count($items)];

$buildFilterUrl = function (array $overrides = []) use ($filterEvent, $status, $keyword, $pagination): string {
    $params = [
        'event_id' => $filterEvent,
        'status'   => $status,
        'q'        => $keyword,
        'page'     => $pagination['page'],
    ];
    $merged = array_filter(array_merge($params, $overrides), fn($v) => $v !== null && $v !== '');
    return site_url('dashboard/komunikasi/registrasi-event') . (! empty($merged) ? '?' . http_build_query($merged) : '');
};
?>

<div class="registrations-container">
    <!-- Header Title & Action Toolbar -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-bold text-neutral-900 mb-1" style="color: #0f172a;">Registrasi &amp; Pendaftar Event PBJ</h6>
            <p class="text-neutral-600 mb-0 font-medium" style="color: #475569;">Pantau seluruh pendaftar kegiatan, status verifikasi pendaftaran, catatan kehadiran, dan tiket e-sertifikat.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= site_url('dashboard/komunikasi/event-agenda') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-10 d-flex align-items-center gap-2 text-sm fw-semibold text-neutral-700" style="background:#ffffff; border-color:#cbd5e1;">
                <iconify-icon icon="solar:calendar-bold" class="text-lg text-primary-600"></iconify-icon>
                Kelola Agenda
            </a>
            <a href="<?= site_url('dashboard/komunikasi/kehadiran') ?>" class="btn btn-primary-600 radius-8 px-18 py-10 d-flex align-items-center gap-2 fw-bold text-sm shadow-sm">
                <iconify-icon icon="solar:checklist-minimalistic-bold" class="text-lg"></iconify-icon>
                Log Kehadiran
            </a>
        </div>
    </div>

    <!-- KPI Metrics Ribbon (5 Uniform Cards) -->
    <div class="row row-cols-xxl-5 row-cols-md-3 row-cols-2 g-3 mb-24">
        <div class="col">
            <div class="kpi-reg-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-neutral-700" style="color: #475569;">Total Registrasi</span>
                    <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center text-lg" style="background: #e0f2fe; color: #0284c7;">
                        <iconify-icon icon="solar:users-group-rounded-bold"></iconify-icon>
                    </div>
                </div>
                <h4 class="fw-bold mb-0 text-neutral-900" style="color: #0f172a;"><?= number_format($stats['total'] ?? 0) ?></h4>
            </div>
        </div>
        <div class="col">
            <div class="kpi-reg-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-neutral-700" style="color: #475569;">Terdaftar (Confirmed)</span>
                    <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center text-lg" style="background: #dbeafe; color: #2563eb;">
                        <iconify-icon icon="solar:user-check-bold"></iconify-icon>
                    </div>
                </div>
                <h4 class="fw-bold mb-0 text-primary-600"><?= number_format($stats['registered'] ?? 0) ?></h4>
            </div>
        </div>
        <div class="col">
            <div class="kpi-reg-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-neutral-700" style="color: #475569;">Peserta Hadir</span>
                    <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center text-lg" style="background: #dcfce7; color: #16a34a;">
                        <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                    </div>
                </div>
                <h4 class="fw-bold mb-0 text-success-700"><?= number_format($stats['attended'] ?? 0) ?></h4>
            </div>
        </div>
        <div class="col">
            <div class="kpi-reg-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-neutral-700" style="color: #475569;">Waitlist</span>
                    <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center text-lg" style="background: #fef3c7; color: #d97706;">
                        <iconify-icon icon="solar:clock-circle-bold"></iconify-icon>
                    </div>
                </div>
                <h4 class="fw-bold mb-0" style="color: #b45309;"><?= number_format($stats['waiting'] ?? 0) ?></h4>
            </div>
        </div>
        <div class="col">
            <div class="kpi-reg-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-neutral-700" style="color: #475569;">Dibatalkan</span>
                    <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center text-lg" style="background: #fee2e2; color: #dc2626;">
                        <iconify-icon icon="solar:close-circle-bold"></iconify-icon>
                    </div>
                </div>
                <h4 class="fw-bold mb-0 text-danger-700"><?= number_format($stats['cancelled'] ?? 0) ?></h4>
            </div>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="card radius-12 mb-24 border shadow-none" style="background:#ffffff; border-color:#e2e8f0;">
        <div class="card-body p-20">
            <form method="get" action="<?= site_url('dashboard/komunikasi/registrasi-event') ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-xl-4 col-md-4">
                        <label class="form-label text-xs fw-bold text-neutral-800 text-uppercase mb-1" style="color:#1e293b;">Pilih Kegiatan / Agenda</label>
                        <select name="event_id" class="form-select radius-8 fw-semibold text-neutral-800" style="border-color: #cbd5e1;">
                            <option value="">Semua Agenda Kegiatan</option>
                            <?php foreach ($eventsList as $ev): ?>
                                <option value="<?= (int) $ev['id'] ?>" <?= (int) $filterEvent === (int) $ev['id'] ? 'selected' : '' ?>>
                                    <?= esc($ev['title']) ?> (<?= ! empty($ev['start_at']) ? esc(date('d/m/Y', strtotime($ev['start_at']))) : '-' ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <label class="form-label text-xs fw-bold text-neutral-800 text-uppercase mb-1" style="color:#1e293b;">Status Registrasi</label>
                        <select name="status" class="form-select radius-8 fw-semibold text-neutral-800" style="border-color: #cbd5e1;">
                            <option value="">Semua Status</option>
                            <option value="registered" <?= $status === 'registered' ? 'selected' : '' ?>>Terdaftar (Registered)</option>
                            <option value="attended" <?= $status === 'attended' ? 'selected' : '' ?>>Hadir (Attended)</option>
                            <option value="waiting" <?= $status === 'waiting' ? 'selected' : '' ?>>Waitlist (Antrian)</option>
                            <option value="promoted" <?= $status === 'promoted' ? 'selected' : '' ?>>Promoted (Dari Waitlist)</option>
                            <option value="no_show" <?= $status === 'no_show' ? 'selected' : '' ?>>Tidak Hadir (No-Show)</option>
                            <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <label class="form-label text-xs fw-bold text-neutral-800 text-uppercase mb-1" style="color:#1e293b;">Cari Peserta</label>
                        <div class="position-relative">
                            <input type="text" name="q" class="form-control radius-8 ps-40 font-medium" value="<?= esc($keyword ?? '') ?>" placeholder="Nama, email, atau telepon..." style="border-color: #cbd5e1; color: #0f172a;">
                            <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-16 text-neutral-500 text-lg"></iconify-icon>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-12 d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-primary-600 radius-8 flex-grow-1 d-flex align-items-center justify-content-center gap-2 fw-bold text-sm">
                            <iconify-icon icon="solar:filter-bold"></iconify-icon> Filter
                        </button>
                        <a href="<?= site_url('dashboard/komunikasi/registrasi-event') ?>" class="btn btn-outline-neutral-300 radius-8 text-neutral-700" title="Reset Filter" style="border-color: #cbd5e1; background:#ffffff;">
                            <iconify-icon icon="solar:restart-bold" class="text-lg"></iconify-icon>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <?php if (empty($items)): ?>
        <div class="card p-40 text-center radius-12 border bg-base shadow-none">
            <div class="w-64-px h-64-px rounded-circle bg-primary-50 text-primary-600 d-flex align-items-center justify-content-center mx-auto mb-16 text-3xl">
                <iconify-icon icon="solar:users-group-rounded-outline"></iconify-icon>
            </div>
            <h5 class="fw-bold text-neutral-900 mb-2" style="color: #0f172a;">Belum Ada Data Pendaftar</h5>
            <p class="text-neutral-600 mb-20 max-w-500-px mx-auto" style="color: #475569;">Tidak ada catatan registrasi yang sesuai dengan kriteria filter Anda.</p>
            <div>
                <a href="<?= site_url('dashboard/komunikasi/registrasi-event') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-8 text-sm fw-semibold">Reset Filter</a>
            </div>
        </div>
    <?php else: ?>
        <div class="reg-table-card mb-24">
            <div class="table-responsive">
                <table class="table bordered-table sm-table mb-0 align-middle reg-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#ID</th>
                            <th>Peserta</th>
                            <th>Agenda / Kegiatan</th>
                            <th class="text-center" style="width: 130px;">Status Reg</th>
                            <th class="text-center" style="width: 120px;">Kehadiran</th>
                            <th style="width: 150px;">Tanggal Daftar</th>
                            <th class="text-center" style="width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $r): ?>
                            <?php
                            $regStatus = $r['status'] ?? 'registered';
                            $attStatus = $r['attendance_status'] ?? null;
                            ?>
                            <tr>
                                <td class="text-neutral-500 text-xs font-monospace font-semibold">#<?= (int) $r['id'] ?></td>
                                <td>
                                    <strong class="d-block text-neutral-900 font-bold" style="color: #0f172a;">
                                        <?= esc($r['user_full_name'] ?? $r['username'] ?? '-') ?>
                                    </strong>
                                    <span class="text-xs text-neutral-600 font-medium d-block" style="color: #475569;"><?= esc($r['email'] ?? '-') ?></span>
                                    <?php if (! empty($r['phone'])): ?>
                                        <small class="text-neutral-500 text-xs font-mono">&bull; <?= esc($r['phone']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('dashboard/komunikasi/event-agenda/' . (int) $r['event_id']) ?>" class="fw-bold text-neutral-900 text-hover-primary-600 text-sm line-clamp-1 mb-1 d-block" style="color: #0f172a;">
                                        <?= esc($r['event_title'] ?? '-') ?>
                                    </a>
                                    <div class="d-flex align-items-center gap-2 text-xs text-neutral-600" style="color: #475569;">
                                        <iconify-icon icon="solar:calendar-date-bold" class="text-primary-600"></iconify-icon>
                                        <span><?= ! empty($r['event_start_at']) ? esc(date('d M Y, H:i', strtotime($r['event_start_at']))) : '-' ?></span>
                                        <?php if (! empty($r['event_mode'])): ?>
                                            <span class="badge bg-neutral-100 text-neutral-800 px-6 py-1 radius-4 text-xs"><?= esc(ucfirst($r['event_mode'])) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php if ($regStatus === 'registered'): ?>
                                        <span class="badge bg-primary-50 text-primary-700 border border-primary-200 px-8 py-4 radius-6 text-xs fw-bold">Terdaftar</span>
                                    <?php elseif ($regStatus === 'attended'): ?>
                                        <span class="badge bg-success-50 text-success-700 border border-success-200 px-8 py-4 radius-6 text-xs fw-bold">Hadir</span>
                                    <?php elseif ($regStatus === 'waiting'): ?>
                                        <span class="badge bg-warning-50 text-warning-700 border border-warning-200 px-8 py-4 radius-6 text-xs fw-bold">Waitlist</span>
                                    <?php elseif ($regStatus === 'promoted'): ?>
                                        <span class="badge bg-teal-50 text-teal-700 border border-teal-200 px-8 py-4 radius-6 text-xs fw-bold">Promoted</span>
                                    <?php elseif ($regStatus === 'cancelled'): ?>
                                        <span class="badge bg-danger-50 text-danger-700 border border-danger-200 px-8 py-4 radius-6 text-xs fw-bold">Dibatalkan</span>
                                    <?php else: ?>
                                        <span class="badge bg-neutral-100 text-neutral-700 border border-neutral-300 px-8 py-4 radius-6 text-xs fw-medium"><?= esc(ucfirst($regStatus)) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($attStatus === 'present'): ?>
                                        <span class="badge bg-success-50 text-success-700 border border-success-200 px-8 py-3 radius-6 text-xs fw-bold d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:check-circle-bold"></iconify-icon> Hadir
                                        </span>
                                    <?php elseif ($attStatus === 'excused'): ?>
                                        <span class="badge bg-info-50 text-info-700 border border-info-200 px-8 py-3 radius-6 text-xs fw-bold d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:info-circle-bold"></iconify-icon> Izin
                                        </span>
                                    <?php elseif ($attStatus === 'absent'): ?>
                                        <span class="badge bg-danger-50 text-danger-700 border border-danger-200 px-8 py-3 radius-6 text-xs fw-bold d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:close-circle-bold"></iconify-icon> Alfa
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-neutral-100 text-neutral-700 border border-neutral-300 px-8 py-3 radius-6 text-xs fw-medium d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:clock-circle-bold"></iconify-icon> Belum
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-xs text-neutral-700 font-medium" style="color: #475569;">
                                    <?= ! empty($r['registered_at']) ? esc(date('d M Y, H:i', strtotime($r['registered_at']))) : '-' ?>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn-action-dropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Aksi">
                                            <iconify-icon icon="solar:menu-dots-bold"></iconify-icon>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8 border-neutral-200 py-6" style="min-width: 180px;">
                                            <li>
                                                <a class="dropdown-item text-xs fw-semibold py-8 px-14 d-flex align-items-center gap-2 text-neutral-800" href="<?= route_to('dashboard.communication.event-registrations.detail', (string) $r['id']) ?>">
                                                    <iconify-icon icon="solar:eye-bold" class="text-primary-600 text-sm"></iconify-icon>
                                                    <span>Lihat Detail Registrasi</span>
                                                </a>
                                            </li>
                                            <?php if ($attStatus !== 'present'): ?>
                                                <li>
                                                    <form action="<?= site_url('dashboard/komunikasi/kehadiran/' . (int) $r['id'] . '/catatan') ?>" method="post">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="status" value="present">
                                                        <input type="hidden" name="notes" value="Presensi manual oleh admin">
                                                        <input type="hidden" name="redirect_to" value="<?= current_url() . '?' . http_build_query($_GET) ?>">
                                                        <button type="submit" class="dropdown-item text-xs fw-semibold py-8 px-14 d-flex align-items-center gap-2 text-success-700 border-0 bg-transparent w-100 text-start">
                                                            <iconify-icon icon="solar:check-circle-bold" class="text-sm"></iconify-icon>
                                                            <span>Tandai Hadir Sah</span>
                                                        </button>
                                                    </form>
                                                </li>
                                            <?php endif; ?>
                                            <?php if ($regStatus === 'waiting'): ?>
                                                <li>
                                                    <form action="<?= site_url('dashboard/komunikasi/registrasi-event/' . (int) $r['id'] . '/status') ?>" method="post">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="status" value="promoted">
                                                        <input type="hidden" name="redirect_to" value="<?= current_url() . '?' . http_build_query($_GET) ?>">
                                                        <button type="submit" class="dropdown-item text-xs fw-semibold py-8 px-14 d-flex align-items-center gap-2 text-teal-700 border-0 bg-transparent w-100 text-start">
                                                            <iconify-icon icon="solar:arrow-up-bold" class="text-sm"></iconify-icon>
                                                            <span>Promosikan ke Terdaftar</span>
                                                        </button>
                                                    </form>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Bar -->
        <?php if (($pagination['totalPages'] ?? 1) > 1): ?>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-16 bg-white radius-12 border mb-24" style="border-color: #e2e8f0;">
                <span class="text-xs fw-bold text-neutral-700" style="color: #475569;">
                    Menampilkan <strong><?= $pagination['from'] ?></strong> - <strong><?= $pagination['to'] ?></strong> dari <strong><?= number_format($pagination['total']) ?></strong> pendaftar
                </span>
                <nav aria-label="Navigasi Halaman Pendaftar">
                    <ul class="pagination reg-pagination mb-0">
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

<?= $this->section('pageScripts') ?>
<script>
(function() {
    // Bulletproof zero-jitter clamping at scroll extremities (especially for Windows mouse wheel)
    var isClamping = false;
    window.addEventListener('scroll', function() {
        var maxScroll = Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
        if (window.scrollY > maxScroll) {
            if (!isClamping) {
                isClamping = true;
                window.scrollTo(0, maxScroll);
                requestAnimationFrame(function() { isClamping = false; });
            }
        }
    }, { passive: true });
})();
</script>
<?= $this->endSection() ?>
