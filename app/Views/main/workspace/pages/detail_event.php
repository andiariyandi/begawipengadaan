<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?php
$now = time();
$materials = $materials ?? [];
$assessmentConfig = $assessmentConfig ?? [];
$registration = $registration ?? [];
$regStatusRaw = strtolower((string) ($registration['status'] ?? 'registered'));
if (! isset($isRegistrationApproved)) {
    $isRegistrationApproved = in_array($regStatusRaw, ['registered', 'confirmed', 'attended', 'present', 'completed', 'promoted'], true);
} else {
    $isRegistrationApproved = (bool) $isRegistrationApproved;
}

$preTest = $assessmentConfig['pre_test'] ?? [];
$postTest = $assessmentConfig['post_test'] ?? [];
$preTestAttempt = $preTestAttempt ?? null;
$postTestAttempt = $postTestAttempt ?? null;
$selfPacedProgress = $selfPacedProgress ?? [];

$hasMaterials = ! empty($materials);
$hasPreTest = (! empty($preTest['enabled']) && ! empty($preTest['questions']));
$hasPostTest = (! empty($postTest['enabled']) && ! empty($postTest['questions']));
$preAttempted = (! empty($preTestAttempt));
$prePassed = ($preAttempted && ! empty($preTestAttempt['is_passed']));
$postAttempted = (! empty($postTestAttempt));
$postPassed = ($postAttempted && ! empty($postTestAttempt['is_passed']));

$startTs = ! empty($event['start_at']) ? strtotime($event['start_at']) : $now;
$endTs = ! empty($event['end_at']) ? strtotime($event['end_at']) : $now;
$regDate = ! empty($registration['registered_at']) ? date('d M Y, H:i', strtotime($registration['registered_at'])) : '-';

// Mode and Location label
$modeLabel = match ($event['mode'] ?? 'online') {
    'offline' => 'Tatap Muka (Offline)',
    'hybrid'  => 'Hybrid (Online & Offline)',
    default   => 'Daring (Online)',
};

// Event State Badge
$eventBadgeClass = match ($eventState) {
    'finished' => 'bg-secondary-subtle text-secondary',
    'ongoing'  => 'bg-success-subtle text-success border border-success-subtle',
    default    => 'bg-info-subtle text-info',
};
$eventBadgeText = match ($eventState) {
    'finished' => 'Selesai',
    'ongoing'  => 'Sedang Berlangsung',
    default    => 'Belum Dimulai',
};

// Presensi State Badge
if ($hasAttended) {
    $presensiBadgeClass = 'bg-success text-white';
    $presensiBadgeText = 'Kehadiran Terverifikasi';
    $presensiIcon = 'fas fa-check-double';
} elseif (! $isRegistrationApproved) {
    $presensiBadgeClass = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
    $presensiBadgeText = 'Menunggu Approval Panitia';
    $presensiIcon = 'fas fa-hourglass-half';
} elseif ($isPresensiActive) {
    $presensiBadgeClass = 'bg-success-subtle text-success border border-success';
    $presensiBadgeText = 'Presensi Aktif';
    $presensiIcon = 'fas fa-door-open';
} elseif ($isPresensiClosed) {
    $presensiBadgeClass = 'bg-secondary-subtle text-secondary';
    $presensiBadgeText = 'Presensi Ditutup';
    $presensiIcon = 'fas fa-door-closed';
} else {
    $presensiBadgeClass = 'bg-light text-muted border';
    $presensiBadgeText = 'Presensi Belum Dibuka';
    $presensiIcon = 'fas fa-clock';
}
?>

<!-- Header Event Section -->
<div class="ws-page-header position-relative overflow-hidden">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill small fw-semibold">
                <i class="fas fa-tag me-1"></i> <?= esc(strtoupper($event['type'] ?? 'EVENT')) ?>
            </span>
            <span class="badge <?= $eventBadgeClass ?> px-3 py-1 rounded-pill small fw-semibold">
                <i class="fas fa-circle me-1 small"></i> <?= esc($eventBadgeText) ?>
            </span>
            <span class="badge <?= $presensiBadgeClass ?> px-3 py-1 rounded-pill small fw-semibold">
                <i class="<?= $presensiIcon ?> me-1"></i> <?= esc($presensiBadgeText) ?>
            </span>
            <?php if ($hasMaterials): ?>
                <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 rounded-pill small fw-semibold">
                    <i class="fas fa-folder-open me-1"></i> <?= count($materials) ?> Materi
                </span>
            <?php endif; ?>
            <?php if ($hasPreTest || $hasPostTest): ?>
                <span class="badge bg-purple-subtle text-primary border px-3 py-1 rounded-pill small fw-semibold">
                    <i class="fas fa-graduation-cap me-1"></i>
                    <?= $hasPreTest && $hasPostTest ? 'Pre-Test & Post-Test' : ($hasPreTest ? 'Pre-Test' : 'Post-Test') ?>
                </span>
            <?php endif; ?>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= route_to('workspace.participant.events') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Event Saya
            </a>
            <a href="<?= site_url('event') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="fas fa-search me-1"></i> Cari Event Baru
            </a>
        </div>
    </div>

    <h1 class="h3 fw-bold mb-3" style="color: #082f5c;">
        <?= esc($event['title']) ?>
    </h1>

    <div class="row g-3 text-muted small pt-2 border-top">
        <div class="col-sm-6 col-lg-3">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-warning" style="width: 32px; height: 32px;">
                    <i class="far fa-calendar-alt"></i>
                </div>
                <div>
                    <span class="d-block text-muted">Jadwal Pelaksanaan</span>
                    <strong class="text-dark"><?= esc(date('d M Y', $startTs)) ?></strong>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-primary" style="width: 32px; height: 32px;">
                    <i class="far fa-clock"></i>
                </div>
                <div>
                    <span class="d-block text-muted">Waktu</span>
                    <strong class="text-dark"><?= esc(date('H:i', $startTs)) ?> - <?= esc(date('H:i', $endTs)) ?> WIB</strong>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-danger" style="width: 32px; height: 32px;">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div>
                    <span class="d-block text-muted">Mode & Lokasi</span>
                    <strong class="text-dark"><?= esc($modeLabel) ?></strong>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-light <?= $isRegistrationApproved ? 'text-success' : 'text-warning' ?>" style="width: 32px; height: 32px;">
                    <i class="fas <?= $isRegistrationApproved ? 'fa-user-check' : 'fa-user-clock' ?>"></i>
                </div>
                <div>
                    <span class="d-block text-muted">Status Pendaftaran</span>
                    <?php if ($isRegistrationApproved): ?>
                        <strong class="text-success">Disetujui / Approved</strong>
                    <?php elseif ($regStatusRaw === 'cancelled'): ?>
                        <strong class="text-danger">Ditolak / Dibatalkan</strong>
                    <?php else: ?>
                        <strong class="text-warning-emphasis">Menunggu Persetujuan</strong>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- BANNER GATE APPROVAL JIKA BELUM DISETUJUI -->
<?php if (! $isRegistrationApproved): ?>
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-3 bg-warning-subtle border-start border-5 border-warning">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-start gap-3">
                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                    <i class="fas fa-user-lock fs-4"></i>
                </div>
                <div>
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1 small fw-bold mb-1">
                        Status: <?= $regStatusRaw === 'cancelled' ? 'Pendaftaran Ditolak / Dibatalkan' : 'Menunggu Persetujuan Panitia / Operator (Pending Approval)' ?>
                    </span>
                    <h2 class="h5 fw-bold text-navy mb-1">
                        <?= $regStatusRaw === 'cancelled' ? 'Akses Ruang Kegiatan Tidak Tersedia' : 'Pendaftaran Anda Telah Tercatat dan Sedang Diverifikasi' ?>
                    </h2>
                    <p class="text-dark small mb-0">
                        <?php if ($regStatusRaw === 'cancelled'): ?>
                            Mohon maaf, pendaftaran Anda pada kegiatan ini telah dibatalkan atau tidak disetujui oleh panitia penyelenggara.
                        <?php else: ?>
                            Akses ke <strong>Tautan Ruang Sesi, Presensi Kehadiran, Unduhan Materi, serta Pre-Test/Post-Test</strong> akan terbuka secara otomatis segera setelah pendaftaran Anda berstatus <strong>Disetujui (Approved/Confirmed)</strong> oleh Operator atau Admin Event.
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- SECTION: STATUS KEIKUTSERTAAN (TIMELINE PESERTA / TRAINER) -->
<div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 mb-3 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <span class="text-uppercase text-muted small fw-semibold">Perjalanan Partisipasi</span>
            <h2 class="h5 fw-bold mb-0" style="color: #082f5c;">Status Keikutsertaan</h2>
        </div>
        <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill small">
            Terdaftar pada <?= esc($regDate) ?> WIB
        </span>
    </div>

    <?php
    $hasCert = (! empty($certificate) && ($certificate['status'] ?? '') === 'issued');
    $isCertEnabled = (bool) ($event['certificate_enabled'] ?? false);
    ?>

    <div class="workspace-timeline-steps">
        <div class="row g-3 position-relative">
            <!-- Step 1: Daftar Event -->
            <div class="col-12 col-md">
                <div class="p-3 rounded-4 h-100 bg-success-subtle border border-success-subtle d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                <i class="fas fa-check small"></i>
                            </span>
                            <small class="text-success fw-bold">Tahap 1</small>
                        </div>
                        <h3 class="h6 fw-bold mb-1 text-success-emphasis">Daftar Event</h3>
                        <p class="text-muted small mb-0">Pendaftaran berhasil diajukan.</p>
                    </div>
                    <small class="text-success fw-semibold mt-3 d-block">
                        <i class="far fa-clock me-1"></i> <?= esc($regDate) ?>
                    </small>
                </div>
            </div>

            <!-- Step 2: Persetujuan (Approval) -->
            <div class="col-12 col-md">
                <?php if ($isRegistrationApproved): ?>
                    <div class="p-3 rounded-4 h-100 bg-success-subtle border border-success-subtle d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                    <i class="fas fa-check small"></i>
                                </span>
                                <small class="text-success fw-bold">Tahap 2</small>
                            </div>
                            <h3 class="h6 fw-bold mb-1 text-success-emphasis">Disetujui (Approved)</h3>
                            <p class="text-muted small mb-0">Kepesertaan sah terkonfirmasi.</p>
                        </div>
                        <small class="text-success fw-semibold mt-3 d-block">
                            <i class="fas fa-id-badge me-1"></i> Reg #<?= (int) $registration['id'] ?> Aktif
                        </small>
                    </div>
                <?php else: ?>
                    <div class="p-3 rounded-4 h-100 bg-warning-subtle border border-warning d-flex flex-column justify-content-between shadow-sm">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-warning text-dark rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                    <i class="fas fa-hourglass-half small"></i>
                                </span>
                                <small class="text-warning-emphasis fw-bold">Tahap 2 (Aktif)</small>
                            </div>
                            <h3 class="h6 fw-bold mb-1 text-navy">Verifikasi Panitia</h3>
                            <p class="text-muted small mb-0">Menunggu persetujuan Operator/Admin.</p>
                        </div>
                        <small class="text-warning-emphasis fw-bold mt-3 d-block">
                            <i class="fas fa-user-clock me-1"></i> Pending Approval
                        </small>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Step 3: Menunggu Kehadiran -->
            <div class="col-12 col-md">
                <?php if ($hasAttended): ?>
                    <div class="p-3 rounded-4 h-100 bg-success-subtle border border-success-subtle d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                    <i class="fas fa-check small"></i>
                                </span>
                                <small class="text-success fw-bold">Tahap 3</small>
                            </div>
                            <h3 class="h6 fw-bold mb-1 text-success-emphasis">Sesi & Presensi</h3>
                            <p class="text-muted small mb-0">Sesi presensi telah diikuti.</p>
                        </div>
                        <small class="text-success fw-semibold mt-3 d-block">
                            <i class="fas fa-check me-1"></i> Telah check-in
                        </small>
                    </div>
                <?php elseif ($isRegistrationApproved): ?>
                    <div class="p-3 rounded-4 h-100 bg-primary-subtle border border-primary d-flex flex-column justify-content-between shadow-sm">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                    <i class="fas fa-hourglass-half small"></i>
                                </span>
                                <small class="text-primary fw-bold">Tahap 3 (Aktif)</small>
                            </div>
                            <h3 class="h6 fw-bold mb-1 text-primary">Sesi & Presensi</h3>
                            <p class="text-muted small mb-0">
                                <?= $isPresensiActive ? 'Sesi presensi aktif! Masukkan kode sandi sekarang.' : 'Menunggu panitia membuka kode kehadiran.' ?>
                            </p>
                        </div>
                        <small class="text-primary fw-bold mt-3 d-block">
                            <i class="fas fa-bullhorn me-1"></i> Ruang Terbuka
                        </small>
                    </div>
                <?php else: ?>
                    <div class="p-3 rounded-4 h-100 bg-light border d-flex flex-column justify-content-between opacity-75">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-secondary-subtle text-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                    <i class="fas fa-lock small"></i>
                                </span>
                                <small class="text-muted">Tahap 3</small>
                            </div>
                            <h3 class="h6 fw-bold mb-1 text-muted">Sesi & Presensi</h3>
                            <p class="text-muted small mb-0">Menunggu approval pendaftaran.</p>
                        </div>
                        <small class="text-muted mt-3 d-block"><i class="fas fa-lock me-1"></i> Terkunci</small>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Step 4: Hadir -->
            <div class="col-12 col-md">
                <?php if ($hasAttended): ?>
                    <div class="p-3 rounded-4 h-100 bg-success-subtle border border-success-subtle d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                    <i class="fas fa-check small"></i>
                                </span>
                                <small class="text-success fw-bold">Tahap 4</small>
                            </div>
                            <h3 class="h6 fw-bold mb-1 text-success-emphasis">Hadir</h3>
                            <p class="text-muted small mb-0">Kehadiran tercatat resmi.</p>
                        </div>
                        <small class="text-success fw-semibold mt-3 d-block">
                            <i class="far fa-clock me-1"></i> <?= ! empty($attendance['recorded_at']) ? esc(date('d M Y, H:i', strtotime($attendance['recorded_at']))) . ' WIB' : 'Tercatat' ?>
                        </small>
                    </div>
                <?php else: ?>
                    <div class="p-3 rounded-4 h-100 bg-light border d-flex flex-column justify-content-between opacity-75">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-secondary-subtle text-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                    <i class="fas fa-circle small"></i>
                                </span>
                                <small class="text-muted">Tahap 4</small>
                            </div>
                            <h3 class="h6 fw-bold mb-1 text-muted">Hadir</h3>
                            <p class="text-muted small mb-0">Belum melakukan presensi.</p>
                        </div>
                        <small class="text-muted mt-3 d-block">
                            <i class="fas fa-lock me-1"></i> Menunggu kode
                        </small>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Step 5: Sertifikat -->
            <div class="col-12 col-md">
                <?php if ($hasCert): ?>
                    <div class="p-3 rounded-4 h-100 bg-success-subtle border border-success-subtle d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                    <i class="fas fa-award small"></i>
                                </span>
                                <small class="text-success fw-bold">Tahap 5</small>
                            </div>
                            <h3 class="h6 fw-bold mb-1 text-success-emphasis">Sertifikat</h3>
                            <p class="text-muted small mb-0">Telah diterbitkan resmi.</p>
                        </div>
                        <small class="text-success fw-semibold mt-3 d-block">
                            <i class="fas fa-file-pdf me-1"></i> Siap diunduh
                        </small>
                    </div>
                <?php elseif ($isCertEnabled): ?>
                    <div class="p-3 rounded-4 h-100 bg-light border d-flex flex-column justify-content-between opacity-75">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-secondary-subtle text-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                    <i class="fas fa-award small"></i>
                                </span>
                                <small class="text-muted">Tahap 5</small>
                            </div>
                            <h3 class="h6 fw-bold mb-1 text-muted">Sertifikat</h3>
                            <p class="text-muted small mb-0">
                                <?= $hasAttended ? 'Menunggu verifikasi panitia.' : 'Tersedia setelah hadir.' ?>
                            </p>
                        </div>
                        <small class="text-muted mt-3 d-block">
                            <i class="fas fa-info-circle me-1"></i> Belum terbit
                        </small>
                    </div>
                <?php else: ?>
                    <div class="p-3 rounded-4 h-100 bg-light border d-flex flex-column justify-content-between opacity-50">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-secondary-subtle text-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                    <i class="fas fa-minus small"></i>
                                </span>
                                <small class="text-muted">Tahap 5</small>
                            </div>
                            <h3 class="h6 fw-bold mb-1 text-muted">Sertifikat</h3>
                            <p class="text-muted small mb-0">Tidak disediakan pada event ini.</p>
                        </div>
                        <small class="text-muted mt-3 d-block">-</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- SECTION: PRESENSI KEHADIRAN (CARD KHUSUS) -->
<div id="presensi" class="mb-3">
    <?php if ($hasAttended): ?>
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white position-relative overflow-hidden" style="border-left: 6px solid #28a745 !important;">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white" style="width: 44px; height: 44px;">
                        <i class="fas fa-check-circle fs-4"></i>
                    </div>
                    <div>
                        <h2 class="h5 fw-bold text-success mb-1">✓ Kehadiran berhasil dicatat</h2>
                        <p class="text-muted mb-0 small">Data kehadiran Anda telah diverifikasi dan sah dalam sistem BegawiPengadaan.</p>
                    </div>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-semibold">
                    <i class="fas fa-user-check me-1"></i> Status: Hadir (Terverifikasi)
                </span>
            </div>

            <div class="row g-2 g-md-3 p-3 bg-light rounded-3 mt-2">
                <div class="col-sm-4">
                    <small class="text-muted d-block mb-1">Nama Event:</small>
                    <strong class="text-dark d-block"><?= esc($event['title']) ?></strong>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted d-block mb-1">Waktu Hadir (Check-in):</small>
                    <strong class="text-dark d-block">
                        <i class="far fa-clock text-success me-1"></i>
                        <?= ! empty($attendance['recorded_at']) ? esc(date('d M Y, H:i', strtotime($attendance['recorded_at']))) . ' WIB' : 'Tercatat' ?>
                    </strong>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted d-block mb-1">Metode Presensi:</small>
                    <span class="badge bg-white text-dark border px-2 py-1 rounded-pill small">
                        <i class="fas fa-key text-primary me-1"></i> <?= esc($attendance['notes'] ?? 'Presensi Kehadiran Sah') ?>
                    </span>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white">
            <div class="row align-items-center g-3">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(8,47,92,0.08); color: #082f5c;">
                            <i class="fas fa-qrcode fs-5"></i>
                        </div>
                        <div>
                            <h2 class="h6 fw-bold mb-0" style="color: #082f5c;">Presensi Kehadiran</h2>
                            <small class="text-muted">Masukkan kode sandi presensi yang dibagikan oleh panitia</small>
                        </div>
                    </div>

                    <?php if (! $isRegistrationApproved): ?>
                        <div class="alert alert-warning border border-warning-subtle rounded-3 py-2 px-3 small text-dark mb-0">
                            <i class="fas fa-lock me-1"></i> Presensi terkunci karena pendaftaran Anda masih berstatus <strong>Menunggu Persetujuan</strong>.
                        </div>
                    <?php elseif ($isPresensiActive): ?>
                        <div class="alert alert-success-subtle border border-success-subtle rounded-3 py-2 px-3 small text-success mb-0">
                            <i class="fas fa-info-circle me-1"></i> Sesi presensi sedang <strong>DIBUKA</strong>. Silakan masukkan kode presensi sekarang.
                        </div>
                    <?php elseif ($isPresensiClosed): ?>
                        <div class="alert alert-secondary border rounded-3 py-2 px-3 small text-secondary mb-0">
                            <i class="fas fa-lock me-1"></i> <strong>Presensi sudah ditutup</strong> oleh penyelenggara.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-light border rounded-3 py-2 px-3 small text-muted mb-0">
                            <i class="fas fa-clock me-1"></i> <strong>Presensi belum dibuka</strong>. Panitia akan mengumumkan kode saat sesi berlangsung.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-6">
                    <?php if ($isRegistrationApproved && $isPresensiActive): ?>
                        <form action="<?= route_to('workspace.participant.attendance.checkin') ?>" method="post" class="d-flex flex-column flex-sm-row gap-2">
                            <?= csrf_field() ?>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-key"></i></span>
                                <input type="text" name="attendance_code" class="form-control text-uppercase font-monospace border-start-0" placeholder="Contoh: BGW-EVT-82K91" required autocomplete="off" style="letter-spacing: 1px;">
                            </div>
                            <button type="submit" class="btn btn-primary px-4 rounded-3 text-nowrap fw-semibold">
                                <i class="fas fa-check-circle me-1"></i> Konfirmasi Kehadiran
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="p-3 bg-light rounded-3 text-center text-muted">
                            <small class="d-block mb-1">
                                <?php if (! $isRegistrationApproved): ?>
                                    Form presensi akan diaktifkan setelah pendaftaran Anda disetujui (Approved) oleh Operator/Panitia.
                                <?php elseif (! empty($selfPacedProgress['is_self_paced_enabled']) && ! $hasAttended): ?>
                                    Sesi presensi langsung telah ditutup. Kegiatan ini dibuka dalam <strong>Mode Pembelajaran Mandiri</strong>.
                                <?php else: ?>
                                    <?= $isPresensiClosed ? 'Form kehadiran dinonaktifkan karena waktu presensi telah berakhir.' : 'Form kehadiran akan otomatis aktif saat panitia membuka sesi presensi.' ?>
                                <?php endif; ?>
                            </small>
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">
                                <?= ! $isRegistrationApproved ? 'Menunggu Persetujuan' : ($isPresensiClosed ? 'Presensi Ditutup' : 'Menunggu Sesi Dibuka') ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if ($isRegistrationApproved && ! empty($selfPacedProgress['is_self_paced_enabled'])): ?>
<!-- SECTION: PEMBELAJARAN MANDIRI (SELF-PACED LEARNING) -->
<div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 mb-3 bg-white border-start border-4 border-success">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pb-3 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success" style="width: 48px; height: 48px;">
                <i class="fas fa-play-circle fs-4"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h2 class="h5 fw-bold mb-0" style="color: #082f5c;">Mode Pembelajaran Mandiri (Self-Paced Learning)</h2>
                    <span class="badge bg-success text-white rounded-pill px-2 py-1 small">Akses Terbuka</span>
                </div>
                <small class="text-muted">Akses rekaman sesi, unduh materi, dan selesaikan asesmen mandiri untuk mengklaim E-Sertifikat resmi.</small>
            </div>
        </div>
        <div>
            <?php if ($selfPacedProgress['has_certificate'] || $hasCert): ?>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill small fw-bold">
                    <i class="fas fa-award me-1"></i> Sertifikat Telah Terbit
                </span>
            <?php elseif ($selfPacedProgress['is_eligible_for_cert']): ?>
                <span class="badge bg-primary text-white px-3 py-2 rounded-pill small fw-bold">
                    <i class="fas fa-check-circle me-1"></i> Siap Diklaim
                </span>
            <?php else: ?>
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill small fw-semibold">
                    <i class="fas fa-tasks me-1"></i> Selesaikan Syarat
                </span>
            <?php endif; ?>
        </div>
    </div>

    <?php if (! empty($selfPacedProgress['instructions'])): ?>
        <div class="alert alert-info-subtle border border-info-subtle rounded-3 p-3 mb-3 small text-dark d-flex align-items-start gap-2">
            <i class="fas fa-info-circle text-info mt-1 flex-shrink-0"></i>
            <div>
                <strong>Petunjuk Pembelajaran Mandiri:</strong><br>
                <?= nl2br(esc($selfPacedProgress['instructions'])) ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Rekaman Video Pemateri -->
    <?php if (! empty($selfPacedProgress['recording_embed_url'])): ?>
        <div class="mb-4">
            <h3 class="h6 fw-bold mb-2 text-dark d-flex align-items-center gap-2">
                <i class="fab fa-youtube text-danger fs-5"></i>
                <span><?= esc($selfPacedProgress['recording_title'] ?: 'Video Rekaman Sesi Paparan') ?></span>
            </h3>
            <div class="ratio ratio-16x9 rounded-4 overflow-hidden border shadow-sm" style="max-height: 480px;">
                <iframe src="<?= esc($selfPacedProgress['recording_embed_url']) ?>" title="<?= esc($selfPacedProgress['recording_title']) ?>" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
            </div>
        </div>
    <?php elseif (! empty($selfPacedProgress['recording_url'])): ?>
        <div class="p-3 rounded-4 bg-light border mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fas fa-video fs-5"></i>
                </div>
                <div>
                    <h3 class="h6 fw-bold mb-0 text-dark"><?= esc($selfPacedProgress['recording_title'] ?: 'Video Rekaman Sesi Paparan') ?></h3>
                    <small class="text-muted">Simak rekaman sesi penjelasan narasumber melalui tautan eksternal.</small>
                </div>
            </div>
            <a href="<?= esc($selfPacedProgress['recording_url']) ?>" target="_blank" class="btn btn-danger rounded-3 px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm">
                <i class="fas fa-external-link-alt"></i> Buka Tautan Rekaman
            </a>
        </div>
    <?php endif; ?>

    <!-- Certificate Claim / Action Box -->
    <?php if ($selfPacedProgress['has_certificate'] || $hasCert): ?>
        <?php $issuedCert = $certificate ?? $selfPacedProgress['certificate'] ?? []; ?>
        <div class="alert alert-success border border-success rounded-4 p-3 mb-0 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="fas fa-award fs-5"></i>
                </div>
                <div>
                    <strong class="d-block text-dark">E-Sertifikat Pembelajaran Mandiri Anda Telah Terbit!</strong>
                    <small class="text-muted">Nomor Sertifikat: <code class="fw-bold text-dark"><?= esc($issuedCert['certificate_number'] ?? '-') ?></code></small>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= route_to('workspace.participant.certificate.download', $issuedCert['id']) ?>" class="btn btn-success fw-bold rounded-3 px-3 py-2 shadow-sm">
                    <i class="fas fa-download me-1"></i> Unduh E-Sertifikat
                </a>
            </div>
        </div>
    <?php elseif ($selfPacedProgress['is_eligible_for_cert']): ?>
        <form action="<?= site_url('workspace/peserta/event-saya/' . ($event['slug'] ?: $event['id']) . '/klaim-sertifikat-mandiri') ?>" method="post">
            <?= csrf_field() ?>
            <div class="alert alert-primary border border-primary rounded-4 p-3 mb-0 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="fas fa-check-circle fs-5"></i>
                    </div>
                    <div>
                        <strong class="d-block text-primary">Selamat! Seluruh Syarat Pembelajaran Mandiri Terpenuhi</strong>
                        <small class="text-muted">Klik tombol di samping untuk mengklaim dan menerbitkan sertifikat resmi Anda sekarang.</small>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary fw-bold rounded-3 px-4 py-2 shadow-sm">
                    <i class="fas fa-award me-1"></i> Klaim E-Sertifikat Mandiri Sekarang
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- SECTION: INFORMASI EVENT & TAUTAN SESI -->
<div class="row g-3 mb-3">
    <!-- Left: Deskripsi & Jadwal -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white h-100">
            <h2 class="h5 fw-bold mb-3" style="color: #082f5c;">
                <i class="fas fa-info-circle text-primary me-2"></i> Informasi Event
            </h2>

            <?php if (! empty($event['summary'])): ?>
                <div class="lead fs-6 text-muted mb-3 border-start border-3 border-primary ps-3">
                    <?= nl2br(esc($event['summary'])) ?>
                </div>
            <?php endif; ?>

            <?php if (! empty($event['description'])): ?>
                <div class="event-description text-secondary mb-3">
                    <?= nl2br(esc($event['description'])) ?>
                </div>
            <?php else: ?>
                <p class="text-muted small mb-3">Deskripsi rinci mengenai rundown dan materi akan diperbarui oleh panitia penyelenggara.</p>
            <?php endif; ?>

            <div class="p-3 rounded-3 bg-light">
                <h3 class="h6 fw-bold mb-1" style="color: #082f5c;">Penyelenggara & Narasumber</h3>
                <p class="text-muted small mb-0">
                    <i class="fas fa-building me-1 text-primary"></i>
                    <?= esc($event['creator_name'] ?? 'Biro Pengadaan Barang dan Jasa Provinsi Lampung') ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Right: Akses Sesi / Meeting & Sertifikat -->
    <div class="col-lg-4">
        <div class="d-flex flex-column gap-3 h-100">
            <!-- Akses Ruang Pertemuan -->
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <h2 class="h6 fw-bold mb-2" style="color: #082f5c;">
                    <i class="fas fa-video text-info me-2"></i> Akses Sesi Kegiatan
                </h2>

                <?php if (! $isRegistrationApproved): ?>
                    <div class="p-3 rounded-3 bg-light border text-muted small">
                        <i class="fas fa-lock text-warning me-1"></i> Tautan ruang sesi daring / detail akses lokasi akan tampil setelah pendaftaran Anda berstatus <strong>Disetujui (Approved)</strong>.
                    </div>
                <?php elseif (! empty($event['meeting_url'])): ?>
                    <p class="text-muted small mb-2">Tautan ruang sesi daring (Zoom / Google Meet) untuk peserta terdaftar:</p>
                    <a href="<?= esc($event['meeting_url']) ?>" target="_blank" class="btn btn-success w-100 rounded-3 py-2 fw-semibold">
                        <i class="fas fa-external-link-alt me-1"></i> Buka Ruang Sesi Daring
                    </a>
                <?php else: ?>
                    <p class="text-muted small mb-2">Mode Pelaksanaan: <strong><?= esc($modeLabel) ?></strong></p>
                    <p class="text-muted small mb-0">
                        <?php if (! empty($event['location'])): ?>
                            <i class="fas fa-map-marker-alt text-danger me-1"></i> Lokasi: <?= esc($event['location']) ?>
                        <?php else: ?>
                            <i class="fas fa-info-circle text-muted me-1"></i> Tautan / lokasi akan diinformasikan menjelang kegiatan dimulai.
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- SECTION: SERTIFIKAT EVENT -->
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white flex-grow-1 d-flex flex-column justify-content-between">
                <div>
                    <h2 class="h6 fw-bold mb-3" style="color: #082f5c;">
                        <i class="fas fa-award text-warning me-2"></i> Sertifikat Event
                    </h2>

                    <?php if (! empty($event['certificate_enabled'])): ?>
                        <?php if ($hasCert): ?>
                            <div class="alert alert-success-subtle border border-success-subtle rounded-3 p-3 mb-3">
                                <span class="badge bg-success text-white rounded-pill px-3 py-1 mb-2">Sertifikat Tersedia</span>
                                <small class="text-muted d-block mb-1">Nomor Sertifikat:</small>
                                <code class="fw-bold text-dark fs-6 d-block mb-2"><?= esc($certificate['certificate_number']) ?></code>
                                <small class="text-muted d-block">
                                    <i class="far fa-calendar-alt me-1"></i> Terbit: <?= esc(date('d M Y', strtotime($certificate['issued_at'] ?? 'now'))) ?>
                                </small>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-light border rounded-3 p-3 mb-3 text-muted">
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1 mb-2">Belum Tersedia</span>
                                <small class="d-block mb-0">
                                    <?= $hasAttended ? 'Kehadiran Anda telah dicatat. Sertifikat resmi sedang dalam antrean penerbitan panitia.' : 'Sertifikat resmi akan diterbitkan setelah Anda melakukan presensi kehadiran pada kegiatan ini.' ?>
                                </small>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-light border rounded-3 p-3 mb-3 text-muted">
                            <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 mb-2">Tidak Tersedia</span>
                            <small class="d-block mb-0">Kegiatan ini diselenggarakan tanpa penerbitan sertifikat formal.</small>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($hasCert): ?>
                    <div class="d-grid gap-2">
                        <a href="<?= route_to('workspace.participant.certificate.download', $certificate['id']) ?>" class="btn btn-primary rounded-3 py-2 fw-semibold">
                            <i class="fas fa-download me-1"></i> Unduh Sertifikat
                        </a>
                        <a href="<?= site_url('verifikasi-sertifikat/' . urlencode($certificate['certificate_number'])) ?>" target="_blank" class="btn btn-outline-secondary btn-sm rounded-3 py-2">
                            <i class="fas fa-shield-alt me-1"></i> Validasi Dokumen
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- KOMPONEN PEMBELAJARAN: MATERI & BAHAN TAYANGAN -->
<div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 mb-3 bg-white" id="sectionMaterials">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pb-3 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary" style="width: 44px; height: 44px;">
                <i class="fas fa-folder-open fs-5"></i>
            </div>
            <div>
                <h2 class="h5 fw-bold mb-0" style="color: #082f5c;">Materi &amp; Bahan Tayangan Presentasi</h2>
                <small class="text-muted">Akses dan unduh materi paparan narasumber (PDF, PPT, XLS, DOC)</small>
            </div>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small fw-semibold">
            <i class="fas fa-paperclip me-1 text-primary"></i> <?= count($materials) ?> Berkas Tersedia
        </span>
    </div>

    <?php if (! $isRegistrationApproved): ?>
        <div class="alert alert-warning border border-warning-subtle rounded-3 p-3 mb-0 small text-dark">
            <i class="fas fa-lock me-1"></i> Terdapat <strong><?= count($materials) ?> berkas materi</strong> pada event ini. Unduhan materi akan terbuka otomatis setelah pendaftaran Anda disetujui (Approved).
        </div>
    <?php elseif (! $hasMaterials): ?>
        <div class="p-3 text-center text-muted small bg-light rounded-3">
            <i class="fas fa-info-circle me-1"></i> Belum ada berkas materi tayangan yang diunggah oleh narasumber atau panitia untuk kegiatan ini.
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($materials as $mat): ?>
                <?php
                $ext = strtolower($mat['extension'] ?? 'pdf');
                $isPdf = ($ext === 'pdf');
                $badgeColor = match ($ext) {
                    'pdf'         => 'bg-danger-subtle text-danger border-danger-subtle',
                    'ppt', 'pptx' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                    'xls', 'xlsx' => 'bg-success-subtle text-success border-success-subtle',
                    'doc', 'docx' => 'bg-primary-subtle text-primary border-primary-subtle',
                    default       => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                };
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="p-3 rounded-4 border bg-white h-100 d-flex flex-column justify-content-between shadow-sm">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge <?= $badgeColor ?> border px-2 py-1 rounded-2 text-uppercase fw-bold small">
                                    <i class="<?= esc($mat['icon'] ?? 'fas fa-file') ?> me-1"></i> <?= esc($ext) ?>
                                </span>
                                <small class="text-muted font-monospace"><?= esc($mat['size_human']) ?></small>
                            </div>
                            <h3 class="h6 fw-bold text-dark mb-1 text-truncate" title="<?= esc($mat['title']) ?>">
                                <?= esc($mat['title']) ?>
                            </h3>
                            <?php if (! empty($mat['speaker'])): ?>
                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-user-tie text-primary me-1"></i> <?= esc($mat['speaker']) ?>
                                </small>
                            <?php endif; ?>
                            <?php if (! empty($mat['notes'])): ?>
                                <p class="text-muted small mb-2 text-truncate-2"><?= esc($mat['notes']) ?></p>
                            <?php endif; ?>
                            <small class="text-muted font-monospace text-xs d-block mb-3 text-truncate" title="<?= esc($mat['original_name']) ?>">
                                <?= esc($mat['original_name']) ?>
                            </small>
                        </div>

                        <div class="d-flex gap-2 pt-2 border-top">
                            <a href="<?= route_to('workspace.participant.event.material.download', $event['slug'] ?: $event['id'], $mat['id']) ?>" class="btn btn-primary btn-sm flex-grow-1 rounded-3 fw-semibold">
                                <i class="fas fa-download me-1"></i> Unduh File
                            </a>
                            <?php if ($isPdf): ?>
                                <a href="<?= esc($mat['url']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm rounded-3 px-3" title="Buka PDF di Tab Baru">
                                    <i class="fas fa-eye"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- KOMPONEN PEMBELAJARAN OPSIONAL 2: PRE-TEST & POST-TEST (HANYA DIRENDER JIKA KONFIGURASI EVENT MENGAKTIFKANNYA) -->
<?php if ($hasPreTest || $hasPostTest): ?>
<?php $testColClass = ($hasPreTest && $hasPostTest) ? 'col-lg-6' : 'col-12'; ?>
<div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 mb-4 bg-white" id="sectionAssessments">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pb-3 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success" style="width: 44px; height: 44px;">
                <i class="fas fa-graduation-cap fs-4"></i>
            </div>
            <div>
                <h2 class="h5 fw-bold mb-0" style="color: #082f5c;">
                    Aktivitas Pembelajaran Event: <?= $hasPreTest && $hasPostTest ? 'Pre-Test &amp; Post-Test' : ($hasPreTest ? 'Pre-Test Sesi' : 'Post-Test Evaluasi') ?>
                </h2>
                <small class="text-muted">Asesmen interaktif untuk mengukur dan mengevaluasi pemahaman materi kegiatan</small>
            </div>
        </div>
    </div>

    <?php if (! $isRegistrationApproved): ?>
        <div class="alert alert-warning border border-warning-subtle rounded-3 p-3 mb-0 small text-dark">
            <i class="fas fa-lock me-1"></i> Asesmen kegiatan ini akan dapat dikerjakan setelah status pendaftaran Anda <strong>Disetujui (Approved)</strong> oleh Panitia/Operator.
        </div>
    <?php else: ?>
        <div class="row g-3">
            <!-- Pre-Test Card (Only rendered if $hasPreTest) -->
            <?php if ($hasPreTest): ?>
                <div class="<?= $testColClass ?>">
                    <div class="p-3 p-md-4 rounded-4 border bg-white h-100 d-flex flex-column justify-content-between shadow-sm">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill small fw-bold">
                                    <i class="fas fa-file-alt me-1"></i> Pre-Test (Awal Sesi)
                                </span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                                    <?= count($preTest['questions'] ?? []) ?> Butir Soal
                                </span>
                            </div>

                            <h3 class="h6 fw-bold text-dark mb-1">
                                <?= esc($preTest['title'] ?? 'Pre-Test Pemahaman Materi') ?>
                            </h3>

                            <p class="text-muted small mb-3">
                                <?= ! empty($preTest['instructions']) ? esc($preTest['instructions']) : 'Kerjakan pre-test ini untuk mengukur tingkat pemahaman awal Anda terhadap materi kegiatan.' ?>
                            </p>

                            <div class="row g-2 mb-3 small text-muted">
                                <div class="col-6">
                                    <i class="far fa-clock me-1 text-primary"></i> Durasi: <strong><?= (int) ($preTest['duration_minutes'] ?? 15) ?> Menit</strong>
                                </div>
                                <div class="col-6">
                                    <i class="fas fa-bullseye me-1 text-success"></i> Passing Score: <strong><?= (int) ($preTest['passing_score'] ?? 70) ?> / 100</strong>
                                </div>
                            </div>

                            <?php if ($preAttempted): ?>
                                <div class="p-3 rounded-3 <?= $prePassed ? 'bg-success-subtle border border-success-subtle text-success-emphasis' : 'bg-warning-subtle border border-warning-subtle text-warning-emphasis' ?> mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-bold">Hasil Pengerjaan Anda:</span>
                                        <span class="badge <?= $prePassed ? 'bg-success text-white' : 'bg-warning text-dark' ?> rounded-pill px-2 py-1 small">
                                            <?= $prePassed ? 'LULUS' : 'BELUM MEMENUHI' ?>
                                        </span>
                                    </div>
                                    <div class="display-6 fw-bold mb-1"><?= (int) $preTestAttempt['score'] ?> <span class="fs-6 fw-normal">/ 100</span></div>
                                    <small class="d-block text-muted">
                                        Benar <?= (int) $preTestAttempt['correct_count'] ?> dari <?= (int) $preTestAttempt['total_questions'] ?> soal &bull; Diserahkan pada <?= esc(date('d M Y, H:i', strtotime($preTestAttempt['submitted_at']))) ?> WIB
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div>
                            <?php if ($preAttempted): ?>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm flex-grow-1 rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalPreTestReview">
                                        <i class="fas fa-list-check me-1"></i> Pembahasan Soal
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#modalTakePreTest" title="Kerjakan Ulang Pre-Test">
                                        <i class="fas fa-redo me-1"></i> Ulang
                                    </button>
                                </div>
                            <?php else: ?>
                                <button type="button" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTakePreTest">
                                    <i class="fas fa-play me-1"></i> Mulai Pre-Test Sekarang
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Post-Test Card (Only rendered if $hasPostTest) -->
            <?php if ($hasPostTest): ?>
                <div class="<?= $testColClass ?>">
                    <div class="p-3 p-md-4 rounded-4 border bg-white h-100 d-flex flex-column justify-content-between shadow-sm">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 rounded-pill small fw-bold">
                                    <i class="fas fa-clipboard-check me-1"></i> Post-Test (Akhir Sesi)
                                </span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                                    <?= count($postTest['questions'] ?? []) ?> Butir Soal
                                </span>
                            </div>

                            <h3 class="h6 fw-bold text-dark mb-1">
                                <?= esc($postTest['title'] ?? 'Post-Test Evaluasi Sesi') ?>
                            </h3>

                            <p class="text-muted small mb-3">
                                <?= ! empty($postTest['instructions']) ? esc($postTest['instructions']) : 'Evaluasi pemahaman akhir Anda setelah menyimak seluruh pemaparan materi dari narasumber.' ?>
                            </p>

                            <div class="row g-2 mb-3 small text-muted">
                                <div class="col-6">
                                    <i class="far fa-clock me-1 text-info"></i> Durasi: <strong><?= (int) ($postTest['duration_minutes'] ?? 15) ?> Menit</strong>
                                </div>
                                <div class="col-6">
                                    <i class="fas fa-bullseye me-1 text-success"></i> Passing Score: <strong><?= (int) ($postTest['passing_score'] ?? 70) ?> / 100</strong>
                                </div>
                            </div>

                            <?php if ($postAttempted): ?>
                                <div class="p-3 rounded-3 <?= $postPassed ? 'bg-success-subtle border border-success-subtle text-success-emphasis' : 'bg-warning-subtle border border-warning-subtle text-warning-emphasis' ?> mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-bold">Hasil Pengerjaan Anda:</span>
                                        <span class="badge <?= $postPassed ? 'bg-success text-white' : 'bg-warning text-dark' ?> rounded-pill px-2 py-1 small">
                                            <?= $postPassed ? 'LULUS' : 'BELUM MEMENUHI' ?>
                                        </span>
                                    </div>
                                    <div class="display-6 fw-bold mb-1"><?= (int) $postTestAttempt['score'] ?> <span class="fs-6 fw-normal">/ 100</span></div>
                                    <small class="d-block text-muted">
                                        Benar <?= (int) $postTestAttempt['correct_count'] ?> dari <?= (int) $postTestAttempt['total_questions'] ?> soal &bull; Diserahkan pada <?= esc(date('d M Y, H:i', strtotime($postTestAttempt['submitted_at']))) ?> WIB
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div>
                            <?php if ($postAttempted): ?>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-info btn-sm flex-grow-1 rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalPostTestReview">
                                        <i class="fas fa-list-check me-1"></i> Pembahasan Soal
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#modalTakePostTest" title="Kerjakan Ulang Post-Test">
                                        <i class="fas fa-redo me-1"></i> Ulang
                                    </button>
                                </div>
                            <?php else: ?>
                                <button type="button" class="btn btn-info text-white w-100 rounded-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTakePostTest">
                                    <i class="fas fa-play me-1"></i> Mulai Post-Test Sekarang
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- ================= MODALS TAKE PRE-TEST & POST-TEST ================= -->

<?php if ($isRegistrationApproved && $hasPreTest): ?>
<!-- Modal Kerjakan Pre-Test -->
<div class="modal fade" id="modalTakePreTest" tabindex="-1" aria-labelledby="modalTakePreTestLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-primary text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-edit fs-5"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalTakePreTestLabel"><?= esc($preTest['title']) ?></h5>
                        <small class="opacity-75">Durasi: <?= (int) ($preTest['duration_minutes'] ?? 15) ?> Menit &bull; Passing Score: <?= (int) ($preTest['passing_score'] ?? 70) ?>%</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= route_to('workspace.participant.event.quiz.submit', $event['slug'] ?: $event['id']) ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="quiz_type" value="pre_test">

                <div class="modal-body p-4 bg-light">
                    <?php if (! empty($preTest['instructions'])): ?>
                        <div class="alert alert-info border-0 rounded-3 small mb-3">
                            <i class="fas fa-info-circle me-1"></i> <?= nl2br(esc($preTest['instructions'])) ?>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($preTest['questions'] as $qIdx => $q): ?>
                            <div class="card border rounded-3 p-3 bg-white shadow-none">
                                <div class="d-flex align-items-start gap-2 mb-2">
                                    <span class="badge bg-primary text-white rounded-pill px-2 py-1 small"><?= $qIdx + 1 ?></span>
                                    <div class="fw-semibold text-dark"><?= nl2br(esc($q['question'])) ?></div>
                                </div>
                                <div class="ms-4 d-flex flex-column gap-2 mt-2">
                                    <?php foreach ($q['options'] as $optIdx => $optText): ?>
                                        <label class="form-check p-2 rounded-2 border bg-light d-flex align-items-center gap-2 cursor-pointer mb-0">
                                            <input class="form-check-input ms-1" type="radio" name="answers[<?= esc($q['id']) ?>]" value="<?= $optIdx ?>" required>
                                            <span class="small text-dark font-medium"><?= esc($optText) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer bg-white py-3 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold" onclick="return confirm('Apakah Anda yakin ingin menyelesaikan dan mengirimkan jawaban Pre-Test ini?');">
                        <i class="fas fa-paper-plane me-1"></i> Kirim Jawaban Pre-Test
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($isRegistrationApproved && $preAttempted && ! empty($preTestAttempt['review'])): ?>
<!-- Modal Review Pembahasan Pre-Test -->
<div class="modal fade" id="modalPreTestReview" tabindex="-1" aria-labelledby="modalPreTestReviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-light py-3 px-4 border-bottom">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalPreTestReviewLabel">Hasil &amp; Pembahasan Pre-Test</h5>
                    <small class="text-muted">Skor Anda: <strong class="<?= $prePassed ? 'text-success' : 'text-danger' ?>"><?= (int) $preTestAttempt['score'] ?> / 100</strong> (<?= (int) $preTestAttempt['correct_count'] ?> dari <?= (int) $preTestAttempt['total_questions'] ?> Benar)</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($preTestAttempt['review'] as $rIdx => $rev): ?>
                        <div class="card border rounded-3 p-3 bg-white shadow-none <?= $rev['is_correct'] ? 'border-success' : 'border-danger' ?>">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-start gap-2">
                                    <span class="badge <?= $rev['is_correct'] ? 'bg-success' : 'bg-danger' ?> rounded-pill px-2 py-1 small"><?= $rIdx + 1 ?></span>
                                    <strong class="text-dark"><?= nl2br(esc($rev['question'])) ?></strong>
                                </div>
                                <span class="badge <?= $rev['is_correct'] ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> rounded-pill px-2 py-1 small">
                                    <?= $rev['is_correct'] ? 'Jawaban Benar' : 'Salah' ?>
                                </span>
                            </div>

                            <div class="ms-4 d-flex flex-column gap-2 mt-2">
                                <?php foreach ($rev['options'] as $oIdx => $oText): ?>
                                    <?php
                                    $isUserChoice = ($rev['user_answer'] === $oIdx);
                                    $isCorrectChoice = ($rev['correct_answer'] === $oIdx);

                                    $bgClass = 'bg-light';
                                    if ($isCorrectChoice) {
                                        $bgClass = 'bg-success-subtle border-success text-success-emphasis fw-bold';
                                    } elseif ($isUserChoice && ! $rev['is_correct']) {
                                        $bgClass = 'bg-danger-subtle border-danger text-danger-emphasis';
                                    }
                                    ?>
                                    <div class="p-2 rounded-2 border <?= $bgClass ?> small d-flex justify-content-between align-items-center">
                                        <span><?= esc($oText) ?></span>
                                        <?php if ($isCorrectChoice): ?>
                                            <span class="badge bg-success text-white small"><i class="fas fa-check"></i> Kunci Jawaban</span>
                                        <?php elseif ($isUserChoice): ?>
                                            <span class="badge bg-danger text-white small"><i class="fas fa-times"></i> Jawaban Anda</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if (! empty($rev['explanation'])): ?>
                                <div class="p-2 mt-2 ms-4 rounded-2 bg-light border text-muted small">
                                    <i class="fas fa-lightbulb text-warning me-1"></i> <strong>Pembahasan:</strong> <?= esc($rev['explanation']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer bg-white py-3 px-4">
                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Tutup Pembahasan</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($isRegistrationApproved && $hasPostTest): ?>
<!-- Modal Kerjakan Post-Test -->
<div class="modal fade" id="modalTakePostTest" tabindex="-1" aria-labelledby="modalTakePostTestLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-info text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-edit fs-5"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalTakePostTestLabel"><?= esc($postTest['title']) ?></h5>
                        <small class="opacity-75">Durasi: <?= (int) ($postTest['duration_minutes'] ?? 15) ?> Menit &bull; Passing Score: <?= (int) ($postTest['passing_score'] ?? 70) ?>%</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= route_to('workspace.participant.event.quiz.submit', $event['slug'] ?: $event['id']) ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="quiz_type" value="post_test">

                <div class="modal-body p-4 bg-light">
                    <?php if (! empty($postTest['instructions'])): ?>
                        <div class="alert alert-info border-0 rounded-3 small mb-3">
                            <i class="fas fa-info-circle me-1"></i> <?= nl2br(esc($postTest['instructions'])) ?>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($postTest['questions'] as $qIdx => $q): ?>
                            <div class="card border rounded-3 p-3 bg-white shadow-none">
                                <div class="d-flex align-items-start gap-2 mb-2">
                                    <span class="badge bg-info text-white rounded-pill px-2 py-1 small"><?= $qIdx + 1 ?></span>
                                    <div class="fw-semibold text-dark"><?= nl2br(esc($q['question'])) ?></div>
                                </div>
                                <div class="ms-4 d-flex flex-column gap-2 mt-2">
                                    <?php foreach ($q['options'] as $optIdx => $optText): ?>
                                        <label class="form-check p-2 rounded-2 border bg-light d-flex align-items-center gap-2 cursor-pointer mb-0">
                                            <input class="form-check-input ms-1" type="radio" name="answers[<?= esc($q['id']) ?>]" value="<?= $optIdx ?>" required>
                                            <span class="small text-dark font-medium"><?= esc($optText) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer bg-white py-3 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white px-4 rounded-3 fw-semibold" onclick="return confirm('Apakah Anda yakin ingin menyelesaikan dan mengirimkan jawaban Post-Test ini?');">
                        <i class="fas fa-paper-plane me-1"></i> Kirim Jawaban Post-Test
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($isRegistrationApproved && $postAttempted && ! empty($postTestAttempt['review'])): ?>
<!-- Modal Review Pembahasan Post-Test -->
<div class="modal fade" id="modalPostTestReview" tabindex="-1" aria-labelledby="modalPostTestReviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-light py-3 px-4 border-bottom">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalPostTestReviewLabel">Hasil &amp; Pembahasan Post-Test</h5>
                    <small class="text-muted">Skor Anda: <strong class="<?= $postPassed ? 'text-success' : 'text-danger' ?>"><?= (int) $postTestAttempt['score'] ?> / 100</strong> (<?= (int) $postTestAttempt['correct_count'] ?> dari <?= (int) $postTestAttempt['total_questions'] ?> Benar)</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($postTestAttempt['review'] as $rIdx => $rev): ?>
                        <div class="card border rounded-3 p-3 bg-white shadow-none <?= $rev['is_correct'] ? 'border-success' : 'border-danger' ?>">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-start gap-2">
                                    <span class="badge <?= $rev['is_correct'] ? 'bg-success' : 'bg-danger' ?> rounded-pill px-2 py-1 small"><?= $rIdx + 1 ?></span>
                                    <strong class="text-dark"><?= nl2br(esc($rev['question'])) ?></strong>
                                </div>
                                <span class="badge <?= $rev['is_correct'] ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> rounded-pill px-2 py-1 small">
                                    <?= $rev['is_correct'] ? 'Jawaban Benar' : 'Salah' ?>
                                </span>
                            </div>

                            <div class="ms-4 d-flex flex-column gap-2 mt-2">
                                <?php foreach ($rev['options'] as $oIdx => $oText): ?>
                                    <?php
                                    $isUserChoice = ($rev['user_answer'] === $oIdx);
                                    $isCorrectChoice = ($rev['correct_answer'] === $oIdx);

                                    $bgClass = 'bg-light';
                                    if ($isCorrectChoice) {
                                        $bgClass = 'bg-success-subtle border-success text-success-emphasis fw-bold';
                                    } elseif ($isUserChoice && ! $rev['is_correct']) {
                                        $bgClass = 'bg-danger-subtle border-danger text-danger-emphasis';
                                    }
                                    ?>
                                    <div class="p-2 rounded-2 border <?= $bgClass ?> small d-flex justify-content-between align-items-center">
                                        <span><?= esc($oText) ?></span>
                                        <?php if ($isCorrectChoice): ?>
                                            <span class="badge bg-success text-white small"><i class="fas fa-check"></i> Kunci Jawaban</span>
                                        <?php elseif ($isUserChoice): ?>
                                            <span class="badge bg-danger text-white small"><i class="fas fa-times"></i> Jawaban Anda</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if (! empty($rev['explanation'])): ?>
                                <div class="p-2 mt-2 ms-4 rounded-2 bg-light border text-muted small">
                                    <i class="fas fa-lightbulb text-warning me-1"></i> <strong>Pembahasan:</strong> <?= esc($rev['explanation']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer bg-white py-3 px-4">
                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Tutup Pembahasan</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
