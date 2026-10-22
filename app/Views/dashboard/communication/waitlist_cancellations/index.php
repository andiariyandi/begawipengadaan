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
.waitlist-container {
    color: #1e293b;
    overflow-x: clip;
    padding-bottom: 24px;
}
.kpi-wc-card {
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
.kpi-wc-card:hover {
    border-color: #93c5fd !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}
.wc-nav-tabs .nav-link {
    font-size: 14px;
    font-weight: 700;
    color: #475569;
    padding: 12px 20px;
    border: none;
    border-bottom: 2px solid transparent;
    transition: all 0.2s ease;
}
.wc-nav-tabs .nav-link.active {
    color: #0284c7;
    border-bottom-color: #0284c7;
    background: transparent;
}
.wc-table th {
    background: #f8fafc !important;
    color: #334155 !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 14px 16px !important;
    border-bottom: 2px solid #e2e8f0 !important;
}
.wc-table td {
    padding: 14px 16px !important;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    color: #1e293b;
}
.wc-table tbody tr:hover {
    background-color: #f8fafc;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= view('dashboard/communication/_nav_communication', ['activeSub' => 'waitlist']) ?>
<?php
$waiting     = $waiting   ?? [];
$cancelled   = $cancelled ?? [];
$promoted    = $promoted  ?? [];
$eventsList  = $eventsList ?? [];
$filterEvent = $filterEventId ?? null;
$keyword     = $filterKeyword ?? null;
$activeTab   = $activeTab ?? 'waiting';

$totalWaiting   = count($waiting);
$totalCancelled = count($cancelled);
$totalPromoted  = count($promoted);
?>

<div class="waitlist-container">
    <!-- Header Title & Action Toolbar -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-bold text-neutral-900 mb-1" style="color: #0f172a;">Waitlist &amp; Pembatalan Registrasi</h6>
            <p class="text-neutral-600 mb-0 font-medium" style="color: #475569;">Kelola antrian peserta cadangan saat kuota agenda penuh, promosi peserta ke status terdaftar, serta pelacakan pembatalan.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= site_url('dashboard/komunikasi/registrasi-event') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-10 d-flex align-items-center gap-2 text-sm fw-semibold text-neutral-700" style="background:#ffffff; border-color:#cbd5e1;">
                <iconify-icon icon="solar:users-group-rounded-bold" class="text-lg text-primary-600"></iconify-icon>
                Semua Pendaftar
            </a>
            <a href="<?= site_url('dashboard/komunikasi/event-agenda') ?>" class="btn btn-primary-600 radius-8 px-18 py-10 d-flex align-items-center gap-2 fw-bold text-sm shadow-sm">
                <iconify-icon icon="solar:calendar-bold" class="text-lg"></iconify-icon>
                Daftar Agenda
            </a>
        </div>
    </div>

    <!-- KPI Metrics Ribbon (3 Uniform Cards) -->
    <div class="row row-cols-xxl-3 row-cols-md-3 row-cols-1 g-3 mb-24">
        <div class="col">
            <div class="kpi-wc-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-neutral-700" style="color: #475569;">Antrian Waitlist (Cadangan)</span>
                    <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center text-lg" style="background: #fef3c7; color: #d97706;">
                        <iconify-icon icon="solar:clock-circle-bold"></iconify-icon>
                    </div>
                </div>
                <h4 class="fw-bold mb-0 text-warning-700" style="color: #b45309;"><?= number_format($totalWaiting) ?></h4>
            </div>
        </div>
        <div class="col">
            <div class="kpi-wc-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-neutral-700" style="color: #475569;">Total Dibatalkan</span>
                    <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center text-lg" style="background: #fee2e2; color: #dc2626;">
                        <iconify-icon icon="solar:close-circle-bold"></iconify-icon>
                    </div>
                </div>
                <h4 class="fw-bold mb-0 text-danger-700"><?= number_format($totalCancelled) ?></h4>
            </div>
        </div>
        <div class="col">
            <div class="kpi-wc-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-neutral-700" style="color: #475569;">Berhasil Dipromosikan</span>
                    <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center text-lg" style="background: #dcfce7; color: #16a34a;">
                        <iconify-icon icon="solar:arrow-up-bold"></iconify-icon>
                    </div>
                </div>
                <h4 class="fw-bold mb-0 text-success-700"><?= number_format($totalPromoted) ?></h4>
            </div>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="card radius-12 mb-24 border shadow-none" style="background:#ffffff; border-color:#e2e8f0;">
        <div class="card-body p-20">
            <form method="get" action="<?= site_url('dashboard/komunikasi/waitlist-pembatalan') ?>">
                <input type="hidden" name="tab" value="<?= esc($activeTab) ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label text-xs fw-bold text-neutral-800 text-uppercase mb-1" style="color:#1e293b;">Pilih Agenda Kegiatan</label>
                        <select name="event_id" class="form-select radius-8 fw-semibold text-neutral-800" style="border-color: #cbd5e1;">
                            <option value="">Semua Agenda Kegiatan</option>
                            <?php foreach ($eventsList as $ev): ?>
                                <option value="<?= (int) $ev['id'] ?>" <?= (int) $filterEvent === (int) $ev['id'] ? 'selected' : '' ?>>
                                    <?= esc($ev['title']) ?> (<?= ! empty($ev['start_at']) ? esc(date('d/m/Y', strtotime($ev['start_at']))) : '-' ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-xs fw-bold text-neutral-800 text-uppercase mb-1" style="color:#1e293b;">Cari Peserta</label>
                        <div class="position-relative">
                            <input type="text" name="q" class="form-control radius-8 ps-40 font-medium" value="<?= esc($keyword ?? '') ?>" placeholder="Nama, email, atau agenda..." style="border-color: #cbd5e1; color: #0f172a;">
                            <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-16 text-neutral-500 text-lg"></iconify-icon>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-primary-600 radius-8 flex-grow-1 d-flex align-items-center justify-content-center gap-2 fw-bold text-sm">
                            <iconify-icon icon="solar:filter-bold"></iconify-icon> Filter
                        </button>
                        <a href="<?= site_url('dashboard/komunikasi/waitlist-pembatalan?tab=' . esc($activeTab)) ?>" class="btn btn-outline-neutral-300 radius-8 text-neutral-700" title="Reset Filter" style="border-color: #cbd5e1; background:#ffffff;">
                            <iconify-icon icon="solar:restart-bold" class="text-lg"></iconify-icon>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabbed Content Card -->
    <div class="card radius-12 border bg-white shadow-none" style="border-color: #e2e8f0;">
        <div class="card-header bg-base p-0 border-bottom">
            <ul class="nav nav-tabs wc-nav-tabs px-16" id="waitlistTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?= $activeTab === 'waiting' ? 'active' : '' ?>" href="<?= site_url('dashboard/komunikasi/waitlist-pembatalan?tab=waiting' . ($filterEvent ? '&event_id=' . $filterEvent : '') . ($keyword ? '&q=' . urlencode($keyword) : '')) ?>">
                        <iconify-icon icon="solar:clock-circle-bold" class="me-1 text-warning-600"></iconify-icon>
                        Antrian Waitlist (<?= $totalWaiting ?>)
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?= $activeTab === 'cancelled' ? 'active' : '' ?>" href="<?= site_url('dashboard/komunikasi/waitlist-pembatalan?tab=cancelled' . ($filterEvent ? '&event_id=' . $filterEvent : '') . ($keyword ? '&q=' . urlencode($keyword) : '')) ?>">
                        <iconify-icon icon="solar:close-circle-bold" class="me-1 text-danger-600"></iconify-icon>
                        Daftar Pembatalan (<?= $totalCancelled ?>)
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?= $activeTab === 'promoted' ? 'active' : '' ?>" href="<?= site_url('dashboard/komunikasi/waitlist-pembatalan?tab=promoted' . ($filterEvent ? '&event_id=' . $filterEvent : '') . ($keyword ? '&q=' . urlencode($keyword) : '')) ?>">
                        <iconify-icon icon="solar:check-circle-bold" class="me-1 text-success-600"></iconify-icon>
                        Riwayat Promosi (<?= $totalPromoted ?>)
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <?php if ($activeTab === 'waiting'): ?>
                <!-- TAB 1: WAITING / WAITLIST QUEUE -->
                <div class="table-responsive">
                    <table class="table bordered-table sm-table mb-0 align-middle wc-table">
                        <thead>
                            <tr>
                                <th>Peserta Cadangan</th>
                                <th>Agenda yang Didaftarkan</th>
                                <th style="width: 170px;">Waktu Masuk Antrian</th>
                                <th class="text-center" style="width: 140px;">Status</th>
                                <th class="text-center" style="width: 180px;">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($waiting)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-32 text-neutral-500 font-medium">
                                        <iconify-icon icon="solar:check-circle-bold" class="text-3xl text-success-500 d-block mx-auto mb-2"></iconify-icon>
                                        Tidak ada peserta dalam antrian waitlist saat ini.
                                    </td>
                                </tr>
                            <?php else: foreach ($waiting as $r): ?>
                                <tr>
                                    <td>
                                        <strong class="d-block text-neutral-900 font-bold" style="color: #0f172a;">
                                            <?= esc($r['user_full_name'] ?? $r['username'] ?? '-') ?>
                                        </strong>
                                        <span class="text-xs text-neutral-600 font-medium" style="color: #475569;"><?= esc($r['email'] ?? '-') ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('dashboard/komunikasi/event-agenda/' . (int) $r['event_id']) ?>" class="fw-bold text-neutral-900 text-hover-primary-600 text-sm line-clamp-1 mb-1 d-block" style="color: #0f172a;">
                                            <?= esc($r['event_title'] ?? '-') ?>
                                        </a>
                                        <span class="text-xs text-neutral-600" style="color: #475569;">
                                            <?= ! empty($r['event_start_at']) ? esc(date('d M Y, H:i', strtotime($r['event_start_at']))) : '-' ?>
                                        </span>
                                    </td>
                                    <td class="text-xs text-neutral-700 font-medium" style="color: #475569;">
                                        <?= ! empty($r['registered_at']) ? esc(date('d M Y, H:i', strtotime($r['registered_at']))) : '-' ?> WIB
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning-50 text-warning-700 border border-warning-200 px-10 py-5 radius-6 text-xs fw-bold d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:clock-circle-bold"></iconify-icon> Waitlist
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <form action="<?= site_url('dashboard/komunikasi/registrasi-event/' . (int) $r['id'] . '/status') ?>" method="post" class="d-inline" onsubmit="return confirm('Promosikan peserta ini ke status terdaftar (confirmed)?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="status" value="promoted">
                                            <input type="hidden" name="redirect_to" value="<?= current_url() . '?' . http_build_query($_GET) ?>">
                                            <button type="submit" class="btn btn-success-600 btn-sm radius-6 px-12 py-6 text-xs fw-bold shadow-sm d-inline-flex align-items-center gap-1">
                                                <iconify-icon icon="solar:arrow-up-bold"></iconify-icon> Promosikan
                                            </button>
                                        </form>
                                        <a href="<?= route_to('dashboard.communication.event-registrations.detail', (string) $r['id']) ?>" class="btn btn-outline-neutral-300 btn-sm radius-6 px-10 py-6 text-xs fw-semibold text-neutral-700" style="border-color:#cbd5e1;">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($activeTab === 'cancelled'): ?>
                <!-- TAB 2: CANCELLED REGISTRATIONS -->
                <div class="table-responsive">
                    <table class="table bordered-table sm-table mb-0 align-middle wc-table">
                        <thead>
                            <tr>
                                <th>Peserta</th>
                                <th>Agenda Kegiatan</th>
                                <th style="width: 170px;">Waktu Batal</th>
                                <th>Alasan Pembatalan</th>
                                <th class="text-center" style="width: 140px;">Status</th>
                                <th class="text-center" style="width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($cancelled)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-32 text-neutral-500 font-medium">Tidak ada data pembatalan registrasi.</td>
                                </tr>
                            <?php else: foreach ($cancelled as $r): ?>
                                <tr>
                                    <td>
                                        <strong class="d-block text-neutral-900 font-bold" style="color: #0f172a;">
                                            <?= esc($r['user_full_name'] ?? $r['username'] ?? '-') ?>
                                        </strong>
                                        <span class="text-xs text-neutral-600 font-medium" style="color: #475569;"><?= esc($r['email'] ?? '-') ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('dashboard/komunikasi/event-agenda/' . (int) $r['event_id']) ?>" class="fw-bold text-neutral-900 text-hover-primary-600 text-sm line-clamp-1 mb-1 d-block" style="color: #0f172a;">
                                            <?= esc($r['event_title'] ?? '-') ?>
                                        </a>
                                        <span class="text-xs text-neutral-600" style="color: #475569;">
                                            <?= ! empty($r['event_start_at']) ? esc(date('d M Y, H:i', strtotime($r['event_start_at']))) : '-' ?>
                                        </span>
                                    </td>
                                    <td class="text-xs text-neutral-700 font-medium" style="color: #475569;">
                                        <?= ! empty($r['cancelled_at']) ? esc(date('d M Y, H:i', strtotime($r['cancelled_at']))) : '-' ?> WIB
                                    </td>
                                    <td class="text-xs text-neutral-700 font-medium" style="color: #475569;">
                                        <?= esc($r['status_reason'] ?? '-') ?: '<span class="text-neutral-400">Tidak ada catatan</span>' ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger-50 text-danger-700 border border-danger-200 px-10 py-5 radius-6 text-xs fw-bold d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:close-circle-bold"></iconify-icon> Dibatalkan
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= route_to('dashboard.communication.event-registrations.detail', (string) $r['id']) ?>" class="btn btn-outline-primary-600 btn-sm radius-6 px-10 py-5 text-xs fw-bold">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php else: ?>
                <!-- TAB 3: PROMOTED FROM WAITLIST -->
                <div class="table-responsive">
                    <table class="table bordered-table sm-table mb-0 align-middle wc-table">
                        <thead>
                            <tr>
                                <th>Peserta</th>
                                <th>Agenda Kegiatan</th>
                                <th style="width: 170px;">Waktu Promosi</th>
                                <th class="text-center" style="width: 140px;">Status</th>
                                <th class="text-center" style="width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($promoted)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-32 text-neutral-500 font-medium">Belum ada peserta yang dipromosikan dari waitlist.</td>
                                </tr>
                            <?php else: foreach ($promoted as $r): ?>
                                <tr>
                                    <td>
                                        <strong class="d-block text-neutral-900 font-bold" style="color: #0f172a;">
                                            <?= esc($r['user_full_name'] ?? $r['username'] ?? '-') ?>
                                        </strong>
                                        <span class="text-xs text-neutral-600 font-medium" style="color: #475569;"><?= esc($r['email'] ?? '-') ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('dashboard/komunikasi/event-agenda/' . (int) $r['event_id']) ?>" class="fw-bold text-neutral-900 text-hover-primary-600 text-sm line-clamp-1 mb-1 d-block" style="color: #0f172a;">
                                            <?= esc($r['event_title'] ?? '-') ?>
                                        </a>
                                        <span class="text-xs text-neutral-600" style="color: #475569;">
                                            <?= ! empty($r['event_start_at']) ? esc(date('d M Y, H:i', strtotime($r['event_start_at']))) : '-' ?>
                                        </span>
                                    </td>
                                    <td class="text-xs text-neutral-700 font-medium" style="color: #475569;">
                                        <?= ! empty($r['promoted_at']) ? esc(date('d M Y, H:i', strtotime($r['promoted_at']))) : (! empty($r['updated_at']) ? esc(date('d M Y, H:i', strtotime($r['updated_at']))) : '-') ?> WIB
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-teal-50 text-teal-700 border border-teal-200 px-10 py-5 radius-6 text-xs fw-bold d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:check-circle-bold"></iconify-icon> Promoted
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= route_to('dashboard.communication.event-registrations.detail', (string) $r['id']) ?>" class="btn btn-outline-primary-600 btn-sm radius-6 px-10 py-5 text-xs fw-bold">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
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
