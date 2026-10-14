<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('pageStyles') ?>
<style>
.event-detail-banner {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    background: #0f172a;
    min-height: 220px;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 24px;
    margin-bottom: 24px;
}
.event-detail-banner-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.35;
}
.event-detail-banner-content {
    position: relative;
    z-index: 2;
    color: #ffffff;
}
.presence-code-display {
    background: #f8fafc;
    border: 2px dashed #93c5fd;
    border-radius: 12px;
    padding: 16px;
    text-align: center;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$eventId = (int) ($eventId ?? ($item['id'] ?? 0));
$code = (string) ($item['attendance_code'] ?? '');
$openAt = ! empty($item['attendance_open_at']) ? strtotime($item['attendance_open_at']) : null;
$closeAt = ! empty($item['attendance_close_at']) ? strtotime($item['attendance_close_at']) : null;
$now = time();
$coverUrl = ! empty($item['cover_file_id']) ? base_url('file/' . (int) $item['cover_file_id']) : null;

if (empty($openAt) || empty($code)) {
    $presensiStatus = 'belum_dibuka';
    $presensiBadge = '<span class="badge bg-neutral-200 text-neutral-800 px-12 py-6 radius-6 fw-bold border border-neutral-300"><i class="fas fa-clock me-1"></i> Belum Dibuka</span>';
} elseif ($closeAt !== null && $closeAt <= $now) {
    $presensiStatus = 'ditutup';
    $presensiBadge = '<span class="badge bg-danger-50 text-danger-700 px-12 py-6 radius-6 fw-bold border border-danger-200"><i class="fas fa-times-circle me-1"></i> Sesi Ditutup</span>';
} else {
    $presensiStatus = 'aktif';
    $presensiBadge = '<span class="badge bg-success-50 text-success-700 px-12 py-6 radius-6 fw-bold border border-success-200"><i class="fas fa-check-circle me-1"></i> Sesi Aktif</span>';
}

$statusKey = $item['status'] ?? 'draft';
$modeKey   = strtolower($item['mode'] ?? 'online');
$capacity  = (int) ($item['capacity'] ?? 0);
$registeredCount = (int) ($registeredCount ?? 0);
$presentCount    = (int) ($presentCount ?? 0);
$certifiedCount  = (int) ($certifiedCount ?? 0);
$percentCapacity = $capacity > 0 ? min(100, round(($registeredCount / $capacity) * 100)) : 0;
?>

<!-- Header Breadcrumb & Actions -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-bold text-neutral-900 mb-1" style="color: #0f172a;"><?= esc($item['title'] ?? 'Detail Event') ?></h6>
        <p class="text-neutral-600 mb-0 font-medium" style="color: #475569;">
            ID Agenda: <strong>#<?= esc($eventId) ?></strong> &bull; Tipe: <strong><?= esc(ucfirst($item['type'] ?? 'webinar')) ?></strong> &bull; Mode: <strong><?= esc(ucfirst($modeKey)) ?></strong>
        </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?= site_url('dashboard/komunikasi/event-agenda') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-10 fw-semibold text-sm text-neutral-700 d-flex align-items-center gap-2" style="background:#ffffff; border-color:#cbd5e1;">
            <iconify-icon icon="solar:arrow-left-linear" class="text-lg"></iconify-icon>
            <span>Daftar Agenda</span>
        </a>
        <a href="<?= site_url('dashboard/komunikasi/event-agenda/' . $eventId . '/ubah') ?>" class="btn btn-primary-600 radius-8 px-18 py-10 fw-bold text-sm d-flex align-items-center gap-2 shadow-sm">
            <iconify-icon icon="solar:pen-new-square-bold" class="text-lg"></iconify-icon>
            <span>Ubah Agenda</span>
        </a>
    </div>
</div>

<!-- Banner Header Card -->
<div class="event-detail-banner shadow-sm">
    <?php if ($coverUrl): ?>
        <img src="<?= esc($coverUrl) ?>" alt="Banner Background" class="event-detail-banner-bg">
    <?php endif; ?>
    <div class="event-detail-banner-content">
        <div class="d-flex align-items-center gap-2 mb-12 flex-wrap">
            <span class="badge bg-primary-500 text-white px-10 py-4 radius-6 text-xs fw-bold">
                <?= esc(ucfirst($item['type'] ?? 'Webinar')) ?>
            </span>
            <span class="badge bg-white text-neutral-900 px-10 py-4 radius-6 text-xs fw-bold">
                <i class="fas fa-satellite-dish me-1"></i> <?= esc(ucfirst($modeKey)) ?>
            </span>
            <span class="badge bg-neutral-800 text-white px-10 py-4 radius-6 text-xs fw-medium border border-neutral-600">
                <?= esc(ucfirst($item['visibility'] ?? 'public')) ?>
            </span>
        </div>
        <h4 class="fw-bold text-white mb-2"><?= esc($item['title'] ?? '-') ?></h4>
        <div class="d-flex flex-wrap align-items-center gap-3 text-xs text-neutral-200">
            <span><i class="far fa-calendar-alt me-1 text-primary-300"></i> <?= ! empty($item['start_at']) ? esc(date('d M Y, H:i', strtotime($item['start_at']))) : '-' ?> WIB</span>
            <span>&bull;</span>
            <span><i class="fas fa-map-marker-alt me-1 text-danger-400"></i> <?= esc($item['location'] ?: ($item['meeting_url'] ?: 'Online / Daring')) ?></span>
        </div>
    </div>
</div>

<div class="row gy-4">
    <!-- Left Column: Details, Presensi & Batch Certificate -->
    <div class="col-xl-8">
        <!-- Event Overview Card -->
        <div class="card mb-24 radius-12 border bg-white shadow-none" style="border-color: #e2e8f0;">
            <div class="card-header bg-white p-20 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-neutral-900" style="color: #0f172a;">Deskripsi &amp; Informasi Kegiatan</h6>
                <div>
                    <?php if ($statusKey === 'open_registration'): ?>
                        <span class="badge bg-success-50 text-success-700 px-10 py-4 radius-6 fw-bold border border-success-200">Pendaftaran Buka</span>
                    <?php elseif ($statusKey === 'coming_soon'): ?>
                        <span class="badge bg-warning-50 text-warning-700 px-10 py-4 radius-6 fw-bold border border-warning-200">Segera Hadir</span>
                    <?php elseif ($statusKey === 'published'): ?>
                        <span class="badge bg-info-50 text-info-700 px-10 py-4 radius-6 fw-bold border border-info-200">Dipublikasikan</span>
                    <?php elseif ($statusKey === 'full'): ?>
                        <span class="badge bg-purple-50 text-purple-700 px-10 py-4 radius-6 fw-bold border border-purple-200">Kuota Penuh</span>
                    <?php elseif ($statusKey === 'finished'): ?>
                        <span class="badge bg-neutral-100 text-neutral-700 px-10 py-4 radius-6 fw-bold border border-neutral-300">Selesai</span>
                    <?php elseif ($statusKey === 'cancelled'): ?>
                        <span class="badge bg-danger-50 text-danger-700 px-10 py-4 radius-6 fw-bold border border-danger-200">Dibatalkan</span>
                    <?php else: ?>
                        <span class="badge bg-neutral-100 text-neutral-600 px-10 py-4 radius-6 fw-bold border border-neutral-300">Draft</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-24">
                <div class="row gy-3 mb-20 text-sm">
                    <div class="col-sm-6">
                        <div class="p-12 radius-8 border bg-light mb-2" style="border-color: #e2e8f0;">
                            <span class="text-xs fw-bold text-neutral-700 d-block mb-1" style="color: #475569;">WAKTU PELAKSANAAN:</span>
                            <div class="fw-semibold text-neutral-900" style="color: #0f172a;">
                                <?= ! empty($item['start_at']) ? esc(date('d M Y, H:i', strtotime($item['start_at']))) : '-' ?> s/d
                                <?= ! empty($item['end_at']) ? esc(date('d M Y, H:i', strtotime($item['end_at']))) : '-' ?> WIB
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-12 radius-8 border bg-light mb-2" style="border-color: #e2e8f0;">
                            <span class="text-xs fw-bold text-neutral-700 d-block mb-1" style="color: #475569;">PERIODE PENDAFTARAN:</span>
                            <div class="fw-semibold text-neutral-900" style="color: #0f172a;">
                                <?= ! empty($item['registration_open_at']) ? esc(date('d M Y H:i', strtotime($item['registration_open_at']))) : 'Buka Langsung' ?> s/d
                                <?= ! empty($item['registration_close_at']) ? esc(date('d M Y H:i', strtotime($item['registration_close_at']))) : 'Sampai Hari H' ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-12 radius-8 border bg-light" style="border-color: #e2e8f0;">
                            <span class="text-xs fw-bold text-neutral-700 d-block mb-1" style="color: #475569;">LOKASI &amp; MEETING URL:</span>
                            <div class="fw-semibold text-neutral-900 mb-1" style="color: #0f172a;">
                                <iconify-icon icon="solar:map-point-bold" class="text-danger-600 me-1"></iconify-icon>
                                <?= esc($item['location'] ?: 'Lokasi fisik belum ditentukan / Sepenuhnya Daring') ?>
                            </div>
                            <?php if (! empty($item['meeting_url'])): ?>
                                <div class="text-xs">
                                    <iconify-icon icon="solar:videocamera-record-bold" class="text-primary-600 me-1"></iconify-icon>
                                    Tautan: <a href="<?= esc($item['meeting_url']) ?>" target="_blank" class="fw-bold text-primary-600"><?= esc($item['meeting_url']) ?></a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if (! empty($item['summary'])): ?>
                    <div class="p-16 radius-8 mb-20 border" style="background: #f8fafc; border-color: #e2e8f0;">
                        <h6 class="text-xs fw-bold text-neutral-700 text-uppercase mb-1" style="color: #475569;">Ringkasan Singkat:</h6>
                        <p class="text-neutral-800 text-sm mb-0 font-medium" style="color: #334155; line-height: 1.6;"><?= nl2br(esc($item['summary'])) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (! empty($item['description'])): ?>
                    <div>
                        <h6 class="text-xs fw-bold text-neutral-700 text-uppercase mb-2" style="color: #475569;">Deskripsi Lengkap &amp; Rundown Acara:</h6>
                        <div class="text-neutral-800 text-sm font-medium" style="color: #334155; line-height: 1.7;"><?= nl2br(esc($item['description'])) ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Section: PRESENSI EVENT -->
        <div class="card mb-24 radius-12 border shadow-sm" style="border-color: #93c5fd; background: #ffffff;">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 p-20 border-bottom" style="background: #e0f2fe; border-color: #bae6fd;">
                <div class="d-flex align-items-center gap-12">
                    <div class="w-40-px h-40-px rounded-circle bg-primary-600 text-white d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:qr-code-bold" class="fs-4"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-neutral-900" style="color: #0c4a6e;">Presensi &amp; Kode Sandi Kehadiran Peserta</h6>
                        <p class="text-xs text-neutral-700 mb-0 font-medium" style="color: #0369a1;">Mekanisme presensi mandiri peserta via portal menggunakan kode sandi unik.</p>
                    </div>
                </div>
                <div>
                    <?= $presensiBadge ?>
                </div>
            </div>

            <div class="card-body p-24">
                <div class="row gy-4 align-items-center">
                    <!-- Kode Presensi Display -->
                    <div class="col-md-7">
                        <label class="form-label text-xs fw-bold text-neutral-700 text-uppercase mb-2" style="color: #475569;">KODE SANDI PRESENSI EVENT:</label>
                        <?php if (! empty($code)): ?>
                            <div class="presence-code-display mb-12">
                                <span class="text-xs text-neutral-600 d-block mb-1 font-semibold" style="color: #64748b;">KODE VERIFIKASI KEHADIRAN</span>
                                <div class="display-6 fw-bold text-primary-600 font-monospace letter-spacing-2 user-select-all" id="attendanceCodeText"><?= esc($code) ?></div>
                                <small class="text-neutral-600 d-block mt-1" style="color: #64748b;">Umumkan kode ini di layar webinar atau ruang kelas saat absensi dimulai.</small>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-primary-600 btn-sm radius-8 px-14 py-8 fw-semibold" onclick="copyCode()">
                                    <iconify-icon icon="solar:copy-bold" class="me-1"></iconify-icon> Salin Kode
                                </button>
                                <form action="<?= site_url('dashboard/komunikasi/event-agenda/' . $eventId . '/generate-kode-presensi') ?>" method="post" class="d-inline" onsubmit="return confirm('Regenerate akan membuat kode sandi baru. Kode sebelumnya tidak dapat digunakan lagi. Lanjutkan?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-neutral-300 btn-sm radius-8 px-14 py-8 fw-semibold text-neutral-700" style="border-color:#cbd5e1;">
                                        <iconify-icon icon="solar:restart-bold" class="me-1"></iconify-icon> Regenerate Kode
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="presence-code-display mb-12">
                                <iconify-icon icon="solar:lock-keyhole-bold" class="fs-1 text-neutral-400 mb-2"></iconify-icon>
                                <p class="text-neutral-600 mb-3 text-sm fw-medium" style="color: #475569;">Kode presensi belum dibuat untuk agenda ini.</p>
                                <form action="<?= site_url('dashboard/komunikasi/event-agenda/' . $eventId . '/generate-kode-presensi') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-9 text-sm fw-bold">
                                        <iconify-icon icon="solar:key-bold" class="me-1"></iconify-icon> Buat Kode Presensi
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Status Kontrol Presensi -->
                    <div class="col-md-5 border-start ps-md-4" style="border-color: #e2e8f0;">
                        <h6 class="text-xs fw-bold text-neutral-700 text-uppercase mb-12" style="color: #475569;">Kontrol Sesi Presensi</h6>
                        <ul class="list-unstyled text-xs mb-16">
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-neutral-600" style="color: #64748b;">Mulai Dibuka:</span>
                                <strong class="text-neutral-900" style="color: #0f172a;"><?= ! empty($item['attendance_open_at']) ? esc(date('d/m/Y H:i', strtotime($item['attendance_open_at']))) : 'Belum Pernah' ?></strong>
                            </li>
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-neutral-600" style="color: #64748b;">Waktu Ditutup:</span>
                                <strong class="text-neutral-900" style="color: #0f172a;"><?= ! empty($item['attendance_close_at']) ? esc(date('d/m/Y H:i', strtotime($item['attendance_close_at']))) : 'Masih Terbuka / -' ?></strong>
                            </li>
                            <li class="d-flex justify-content-between pt-2 border-top" style="border-color: #f1f5f9;">
                                <span class="text-neutral-600" style="color: #64748b;">Kehadiran Sah:</span>
                                <strong class="text-success-700 fw-bold"><?= (int) $presentCount ?> / <?= (int) $registeredCount ?> Peserta</strong>
                            </li>
                        </ul>

                        <div class="d-grid gap-2">
                            <?php if ($presensiStatus === 'aktif'): ?>
                                <form action="<?= site_url('dashboard/komunikasi/event-agenda/' . $eventId . '/tutup-presensi') ?>" method="post" onsubmit="return confirm('Tutup sesi presensi sekarang? Peserta tidak akan bisa check-in.')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger-600 w-100 radius-8 py-9 fw-bold text-sm shadow-sm">
                                        <iconify-icon icon="solar:close-circle-bold" class="me-1"></iconify-icon> Tutup Sesi Presensi
                                    </button>
                                </form>
                            <?php else: ?>
                                <form action="<?= site_url('dashboard/komunikasi/event-agenda/' . $eventId . '/buka-presensi') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-success-600 w-100 radius-8 py-9 fw-bold text-sm shadow-sm">
                                        <iconify-icon icon="solar:play-circle-bold" class="me-1"></iconify-icon> Buka Sesi Presensi
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: BATCH CERTIFICATE EVENT -->
        <div class="card mb-24 radius-12 border bg-white shadow-none" style="border-color: #e2e8f0;">
            <div class="card-header bg-white d-flex flex-wrap align-items-center justify-content-between gap-3 p-20 border-bottom">
                <div class="d-flex align-items-center gap-12">
                    <div class="w-40-px h-40-px rounded-circle bg-success-50 text-success-600 d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:diploma-verified-bold" class="fs-4"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-neutral-900" style="color: #0f172a;">Penerbitan E-Sertifikat Event (Batch)</h6>
                        <p class="text-xs text-neutral-600 mb-0 font-medium" style="color: #475569;">Terbitkan sertifikat kelulusan kehadiran secara serentak bagi seluruh peserta yang telah terkonfirmasi hadir.</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-24">
                <div class="row align-items-center gy-3">
                    <div class="col-md-7">
                        <div class="d-flex align-items-center gap-4">
                            <div>
                                <span class="text-neutral-600 text-xs fw-bold d-block mb-1" style="color: #64748b;">PESERTA HADIR:</span>
                                <h4 class="mb-0 fw-bold text-success-700"><?= (int) $presentCount ?> <span class="text-xs text-neutral-500 fw-normal">Orang</span></h4>
                            </div>
                            <div class="border-start ps-4" style="border-color: #cbd5e1;">
                                <span class="text-neutral-600 text-xs fw-bold d-block mb-1" style="color: #64748b;">TELAH TERBIT:</span>
                                <h4 class="mb-0 fw-bold text-primary-600"><?= (int) $certifiedCount ?> <span class="text-xs text-neutral-500 fw-normal">Sertifikat</span></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-end">
                        <form action="<?= site_url('dashboard/komunikasi/event-agenda/' . $eventId . '/batch-certificates') ?>" method="post" onsubmit="return confirm('Terbitkan sertifikat batch untuk seluruh peserta yang hadir pada event ini?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success-600 radius-8 px-20 py-10 fw-bold text-sm shadow-sm" <?= $presentCount === 0 ? 'disabled' : '' ?>>
                                <iconify-icon icon="solar:diploma-bold" class="me-1"></iconify-icon> Terbitkan Sertifikat Batch
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Participant List Table Card -->
        <div class="card radius-12 border bg-white shadow-none" style="border-color: #e2e8f0;">
            <div class="card-header bg-white p-20 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h6 class="mb-0 fw-bold text-neutral-900" style="color: #0f172a;">Daftar Peserta &amp; Status Presensi</h6>
                    <small class="text-neutral-600 font-medium" style="color: #475569;">Total <?= count($participants ?? []) ?> pendaftar terdaftar pada agenda ini.</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <input type="text" id="participantSearchInput" class="form-control form-control-sm radius-8" placeholder="Cari nama peserta..." style="width: 200px; border-color: #cbd5e1;">
                    <a href="<?= site_url('dashboard/komunikasi/registrasi-event?event_id=' . $eventId) ?>" class="btn btn-outline-neutral-300 btn-sm radius-8 px-12 py-6 text-neutral-700 fw-semibold" style="border-color: #cbd5e1;">
                        Semua Pendaftar
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-sm" id="participantsTable">
                        <thead style="background: #f8fafc;">
                            <tr>
                                <th class="text-uppercase text-xs fw-bold text-neutral-800 ps-20 py-12" style="color: #1e293b;">Peserta</th>
                                <th class="text-uppercase text-xs fw-bold text-neutral-800 py-12" style="color: #1e293b;">Status Reg</th>
                                <th class="text-uppercase text-xs fw-bold text-neutral-800 py-12" style="color: #1e293b;">Kehadiran</th>
                                <th class="text-uppercase text-xs fw-bold text-neutral-800 py-12" style="color: #1e293b;">Waktu Hadir</th>
                                <th class="text-uppercase text-xs fw-bold text-neutral-800 py-12" style="color: #1e293b;">Sertifikat</th>
                                <th class="text-center text-uppercase text-xs fw-bold text-neutral-800 pe-20 py-12" style="color: #1e293b;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($participants)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-32 text-neutral-500 font-medium">Belum ada peserta yang mendaftar pada event ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($participants as $p): ?>
                                    <tr class="participant-row">
                                        <td class="ps-20">
                                            <strong class="d-block text-neutral-900 participant-name" style="color: #0f172a;"><?= esc($p['user_name'] ?? 'Peserta') ?></strong>
                                            <span class="text-xs text-neutral-600 font-medium" style="color: #475569;"><?= esc($p['user_email'] ?? '-') ?></span>
                                            <?php if (! empty($p['user_phone'])): ?>
                                                <small class="text-neutral-500 d-block text-xs">&bull; <?= esc($p['user_phone']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-50 text-primary-700 radius-6 px-8 py-3 text-xs fw-bold border border-primary-200">
                                                <?= esc(ucfirst($p['registration_status'] ?? 'registered')) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (($p['attendance_status'] ?? '') === 'present'): ?>
                                                <span class="badge bg-success-50 text-success-700 radius-6 px-8 py-4 text-xs fw-bold border border-success-200">
                                                    <i class="fas fa-check-circle me-1"></i> Hadir
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-neutral-100 text-neutral-700 radius-6 px-8 py-4 text-xs fw-semibold border border-neutral-300">
                                                    <i class="far fa-clock me-1"></i> Belum Hadir
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-neutral-700 text-xs font-medium" style="color: #475569;">
                                            <?= ! empty($p['attendance_recorded_at']) ? esc(date('d/m/Y H:i', strtotime($p['attendance_recorded_at']))) : '-' ?>
                                        </td>
                                        <td>
                                            <?php if (! empty($p['certificate_number'])): ?>
                                                <span class="badge bg-teal-50 text-teal-700 radius-6 px-8 py-4 text-xs font-monospace fw-bold border border-teal-200">
                                                    <?= esc($p['certificate_number']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-xs text-neutral-400 font-medium">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-20 text-center">
                                            <?php if (($p['attendance_status'] ?? '') !== 'present'): ?>
                                                <form action="<?= site_url('dashboard/komunikasi/kehadiran/' . (int) $p['registration_id'] . '/catatan') ?>" method="post" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="status" value="present">
                                                    <input type="hidden" name="notes" value="Check-in manual oleh Admin">
                                                    <input type="hidden" name="redirect_to" value="<?= current_url() ?>">
                                                    <button type="submit" class="btn btn-outline-success btn-sm radius-6 px-8 py-4 text-xs fw-bold" title="Tandai Hadir Manual">
                                                        <i class="fas fa-check me-1"></i> Hadir
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <a href="<?= route_to('dashboard.communication.event-registrations.detail', (string) $p['registration_id']) ?>" class="btn btn-outline-neutral-300 btn-sm radius-6 px-8 py-4 text-xs text-neutral-700 fw-semibold" style="border-color:#cbd5e1;">
                                                    Detail
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Registration Summary & Capacities -->
    <div class="col-xl-4">
        <!-- Capacity Metric Card -->
        <div class="card mb-24 radius-12 border bg-white shadow-none" style="border-color: #e2e8f0;">
            <div class="card-body p-24">
                <span class="text-xs fw-bold text-neutral-700 text-uppercase d-block mb-1" style="color: #475569;">KAPASITAS PENDAFTARAN</span>
                <h3 class="mb-8 fw-bold text-neutral-900" style="color: #0f172a;">
                    <?= number_format($registeredCount) ?> 
                    <span class="text-neutral-500 text-base fw-normal">/ <?= $capacity > 0 ? number_format($capacity) : '&infin;' ?> Peserta</span>
                </h3>
                <div class="progress progress-sm rounded-pill mb-12 bg-neutral-200" style="height: 8px;">
                    <div class="progress-bar <?= $percentCapacity >= 90 ? 'bg-danger-600' : ($percentCapacity >= 70 ? 'bg-warning-600' : 'bg-primary-600') ?>" style="width: <?= $percentCapacity ?>%;"></div>
                </div>
                <p class="text-neutral-600 text-xs mb-20 font-medium" style="color: #475569;"><?= $percentCapacity ?>% dari total kapasitas telah terisi.</p>

                <div class="d-flex justify-content-between py-10 border-top text-xs" style="border-color: #f1f5f9;">
                    <span class="text-neutral-600 font-medium" style="color: #475569;">Status Kegiatan:</span>
                    <strong class="text-neutral-900" style="color: #0f172a;"><?= esc(ucfirst($item['status'] ?? 'draft')) ?></strong>
                </div>
                <div class="d-flex justify-content-between py-10 border-top text-xs" style="border-color: #f1f5f9;">
                    <span class="text-neutral-600 font-medium" style="color: #475569;">E-Sertifikat Tersedia:</span>
                    <strong class="text-neutral-900" style="color: #0f172a;"><?= ! empty($item['certificate_enabled']) ? 'Aktif (Ya)' : 'Tidak Aktif' ?></strong>
                </div>
                <div class="d-flex justify-content-between py-10 border-top text-xs" style="border-color: #f1f5f9;">
                    <span class="text-neutral-600 font-medium" style="color: #475569;">Fitur Waitlist:</span>
                    <strong class="text-neutral-900" style="color: #0f172a;"><?= ! empty($item['waitlist_enabled']) ? 'Aktif' : 'Tidak' ?></strong>
                </div>
                <div class="d-flex justify-content-between py-10 border-top text-xs" style="border-color: #f1f5f9;">
                    <span class="text-neutral-600 font-medium" style="color: #475569;">Presensi Hadir:</span>
                    <strong class="text-success-700 fw-bold"><?= number_format($presentCount) ?> Peserta</strong>
                </div>
                <div class="d-flex justify-content-between py-10 border-top text-xs" style="border-color: #f1f5f9;">
                    <span class="text-neutral-600 font-medium" style="color: #475569;">Sertifikat Diterbitkan:</span>
                    <strong class="text-primary-600 fw-bold"><?= number_format($certifiedCount) ?> Lembar</strong>
                </div>
            </div>
        </div>

        <!-- Quick Links Card -->
        <div class="card radius-12 border bg-white shadow-none" style="border-color: #e2e8f0;">
            <div class="card-header bg-white p-20 border-bottom">
                <h6 class="mb-0 fw-bold text-neutral-900" style="color: #0f172a;">Tindakan Cepat</h6>
            </div>
            <div class="card-body p-20 d-grid gap-2">
                <a href="<?= site_url('dashboard/komunikasi/registrasi-event?event_id=' . $eventId) ?>" class="btn btn-outline-primary-600 radius-8 py-9 text-xs fw-semibold d-flex align-items-center justify-content-center gap-2">
                    <iconify-icon icon="solar:users-group-rounded-bold" class="text-base"></iconify-icon>
                    <span>Kelola Pendaftar Event Ini</span>
                </a>
                <a href="<?= site_url('dashboard/komunikasi/kehadiran?event_id=' . $eventId) ?>" class="btn btn-outline-neutral-300 radius-8 py-9 text-xs fw-semibold text-neutral-800 d-flex align-items-center justify-content-center gap-2" style="border-color:#cbd5e1;">
                    <iconify-icon icon="solar:checklist-minimalistic-bold" class="text-base text-success-600"></iconify-icon>
                    <span>Lihat Log Kehadiran Event</span>
                </a>
                <a href="<?= site_url('dashboard/komunikasi/waitlist-pembatalan?event_id=' . $eventId) ?>" class="btn btn-outline-neutral-300 radius-8 py-9 text-xs fw-semibold text-neutral-800 d-flex align-items-center justify-content-center gap-2" style="border-color:#cbd5e1;">
                    <iconify-icon icon="solar:clock-circle-bold" class="text-base text-warning-600"></iconify-icon>
                    <span>Waitlist &amp; Pembatalan</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function copyCode() {
    const codeEl = document.getElementById('attendanceCodeText');
    if (codeEl) {
        navigator.clipboard.writeText(codeEl.innerText.trim()).then(function() {
            alert('Kode presensi ' + codeEl.innerText.trim() + ' berhasil disalin ke clipboard!');
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('participantSearchInput');
    const table = document.getElementById('participantsTable');
    if (searchInput && table) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const rows = table.querySelectorAll('.participant-row');
            rows.forEach(function(row) {
                const name = row.querySelector('.participant-name') ? row.querySelector('.participant-name').textContent.toLowerCase() : '';
                if (name.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>

<?= $this->endSection() ?>
