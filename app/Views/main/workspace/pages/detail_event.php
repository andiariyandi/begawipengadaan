<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?php
$now = time();
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
<div class="bg-white border rounded-4 p-4 p-lg-5 mb-4 shadow-sm position-relative overflow-hidden">
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
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-success" style="width: 32px; height: 32px;">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <span class="d-block text-muted">Status Terdaftar</span>
                    <strong class="text-dark">Aktif (<?= esc(ucfirst($registration['status'])) ?>)</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECTION: STATUS KEIKUTSERTAAN (TIMELINE PESERTA) -->
<div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="text-uppercase text-muted small fw-semibold">Perjalanan Partisipasi</span>
            <h2 class="h5 fw-bold mb-0" style="color: #082f5c;">Status Keikutsertaan</h2>
        </div>
        <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill small">
            Terdaftar pada <?= esc($regDate) ?> WIB
        </span>
    </div>

    <?php
    // Timeline steps state definition
    // Step 1: Daftar Event (always completed)
    // Step 2: Terdaftar (completed)
    // Step 3: Menunggu Kehadiran (completed if attended, active if not attended)
    // Step 4: Hadir (completed if attended, pending otherwise)
    // Step 5: Sertifikat (completed if cert issued, pending if enabled, disabled if not enabled)
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

            <!-- Step 2: Terdaftar -->
            <div class="col-12 col-md">
                <div class="p-3 rounded-4 h-100 bg-success-subtle border border-success-subtle d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                <i class="fas fa-check small"></i>
                            </span>
                            <small class="text-success fw-bold">Tahap 2</small>
                        </div>
                        <h3 class="h6 fw-bold mb-1 text-success-emphasis">Terdaftar</h3>
                        <p class="text-muted small mb-0">Peserta sah terkonfirmasi.</p>
                    </div>
                    <small class="text-success fw-semibold mt-3 d-block">
                        <i class="fas fa-id-badge me-1"></i> Kuota terisi (ID #<?= (int) $registration['id'] ?>)
                    </small>
                </div>
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
                            <h3 class="h6 fw-bold mb-1 text-success-emphasis">Menunggu Kehadiran</h3>
                            <p class="text-muted small mb-0">Sesi presensi telah dilewati.</p>
                        </div>
                        <small class="text-success fw-semibold mt-3 d-block">
                            <i class="fas fa-check me-1"></i> Telah check-in
                        </small>
                    </div>
                <?php else: ?>
                    <div class="p-3 rounded-4 h-100 bg-primary-subtle border border-primary d-flex flex-column justify-content-between shadow-sm">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                    <i class="fas fa-hourglass-half small"></i>
                                </span>
                                <small class="text-primary fw-bold">Tahap 3 (Aktif)</small>
                            </div>
                            <h3 class="h6 fw-bold mb-1 text-primary">Menunggu Kehadiran</h3>
                            <p class="text-muted small mb-0">
                                <?= $isPresensiActive ? 'Sesi presensi aktif! Masukkan kode sandi sekarang.' : 'Menunggu panitia membuka kode kehadiran.' ?>
                            </p>
                        </div>
                        <small class="text-primary fw-bold mt-3 d-block">
                            <i class="fas fa-bullhorn me-1"></i> Sesi berlangsung
                        </small>
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
                            <i class="far fa-clock me-1"></i> <?= esc(date('d M Y, H:i', strtotime($attendance['recorded_at']))) ?> WIB
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
<div id="presensi" class="mb-4">
    <?php if ($hasAttended): ?>
        <!-- HASIL PRESENSI: SUDAH HADIR / BERHASIL -->
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white position-relative overflow-hidden" style="border-left: 6px solid #28a745 !important;">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white" style="width: 54px; height: 54px;">
                        <i class="fas fa-check-circle fs-3"></i>
                    </div>
                    <div>
                        <h2 class="h4 fw-bold text-success mb-1">✓ Kehadiran berhasil dicatat</h2>
                        <p class="text-muted mb-0 small">Data kehadiran Anda telah diverifikasi dan sah dalam sistem BegawiPBJ.</p>
                    </div>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold">
                    <i class="fas fa-user-check me-1"></i> Status: Hadir (Terverifikasi)
                </span>
            </div>

            <div class="row g-3 p-3 bg-light rounded-4 mt-2">
                <div class="col-sm-4">
                    <small class="text-muted d-block mb-1">Nama Event:</small>
                    <strong class="text-dark d-block"><?= esc($event['title']) ?></strong>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted d-block mb-1">Waktu Hadir (Check-in):</small>
                    <strong class="text-dark d-block">
                        <i class="far fa-clock text-success me-1"></i>
                        <?= esc(date('d M Y, H:i', strtotime($attendance['recorded_at']))) ?> WIB
                    </strong>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted d-block mb-1">Metode Presensi:</small>
                    <span class="badge bg-white text-dark border px-3 py-1 rounded-pill small">
                        <i class="fas fa-key text-primary me-1"></i> Kode Kehadiran Mandiri
                    </span>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- FORM PRESENSI KEHADIRAN (BELUM HADIR) -->
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(8,47,92,0.08); color: #082f5c;">
                            <i class="fas fa-qrcode fs-4"></i>
                        </div>
                        <div>
                            <h2 class="h5 fw-bold mb-0" style="color: #082f5c;">Presensi Kehadiran</h2>
                            <small class="text-muted">Masukkan kode sandi presensi yang dibagikan oleh panitia</small>
                        </div>
                    </div>

                    <?php if ($isPresensiActive): ?>
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
                    <?php if ($isPresensiActive): ?>
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
                                <?= $isPresensiClosed ? 'Form kehadiran dinonaktifkan karena waktu presensi telah berakhir.' : 'Form kehadiran akan otomatis aktif saat panitia membuka sesi presensi.' ?>
                            </small>
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">
                                <?= $isPresensiClosed ? 'Presensi Ditutup' : 'Menunggu Sesi Dibuka' ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- SECTION: INFORMASI EVENT & TAUTAN SESI -->
<div class="row g-4 mb-4">
    <!-- Left: Deskripsi & Jadwal -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100">
            <h2 class="h5 fw-bold mb-3" style="color: #082f5c;">
                <i class="fas fa-info-circle text-primary me-2"></i> Informasi Event
            </h2>

            <?php if (! empty($event['summary'])): ?>
                <div class="lead fs-6 text-muted mb-4 border-start border-3 border-primary ps-3">
                    <?= nl2br(esc($event['summary'])) ?>
                </div>
            <?php endif; ?>

            <?php if (! empty($event['description'])): ?>
                <div class="event-description text-secondary mb-4">
                    <?= nl2br(esc($event['description'])) ?>
                </div>
            <?php else: ?>
                <p class="text-muted mb-4">Deskripsi rinci mengenai rundown dan materi akan diperbarui oleh panitia penyelenggara.</p>
            <?php endif; ?>

            <div class="p-3 rounded-4 bg-light">
                <h3 class="h6 fw-bold mb-2" style="color: #082f5c;">Penyelenggara & Narasumber</h3>
                <p class="text-muted small mb-0">
                    <i class="fas fa-building me-1 text-primary"></i>
                    <?= esc($event['creator_name'] ?? 'Biro Pengadaan Barang dan Jasa Provinsi Lampung') ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Right: Akses Sesi / Meeting & Sertifikat -->
    <div class="col-lg-4">
        <div class="d-flex flex-column gap-4 h-100">
            <!-- Akses Ruang Pertemuan -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h2 class="h6 fw-bold mb-3" style="color: #082f5c;">
                    <i class="fas fa-video text-info me-2"></i> Akses Sesi Kegiatan
                </h2>

                <?php if (! empty($event['meeting_url'])): ?>
                    <p class="text-muted small mb-3">Tautan ruang sesi daring (Zoom / Google Meet) untuk peserta terdaftar:</p>
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
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white flex-grow-1 d-flex flex-column justify-content-between">
                <div>
                    <h2 class="h6 fw-bold mb-3" style="color: #082f5c;">
                        <i class="fas fa-award text-warning me-2"></i> Sertifikat Event
                    </h2>

                    <?php if (! empty($event['certificate_enabled'])): ?>
                        <?php if ($hasCert): ?>
                            <!-- Sertifikat Tersedia -->
                            <div class="alert alert-success-subtle border border-success-subtle rounded-3 p-3 mb-3">
                                <span class="badge bg-success text-white rounded-pill px-3 py-1 mb-2">Sertifikat Tersedia</span>
                                <small class="text-muted d-block mb-1">Nomor Sertifikat:</small>
                                <code class="fw-bold text-dark fs-6 d-block mb-2"><?= esc($certificate['certificate_number']) ?></code>
                                <small class="text-muted d-block">
                                    <i class="far fa-calendar-alt me-1"></i> Terbit: <?= esc(date('d M Y', strtotime($certificate['issued_at'] ?? 'now'))) ?>
                                </small>
                            </div>
                        <?php else: ?>
                            <!-- Sertifikat Belum Tersedia -->
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

                <!-- Action Button Sertifikat -->
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

<?= $this->endSection() ?>
