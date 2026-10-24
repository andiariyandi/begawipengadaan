<?php
$now = time();
$startAt = ! empty($ev['start_at']) ? strtotime($ev['start_at']) : $now;
$endAt = ! empty($ev['end_at']) ? strtotime($ev['end_at']) : $now;

$regStatus = strtolower((string) ($ev['registration_status'] ?? 'registered'));
$isApproved = $ev['is_approved'] ?? in_array($regStatus, ['registered', 'confirmed', 'attended', 'present', 'completed', 'promoted'], true);

// Execution status badge
if ($ev['is_finished'] ?? ($endAt < $now)) {
    $execBadgeClass = 'bg-secondary-subtle text-secondary';
    $execBadgeLabel = 'Selesai';
} elseif ($ev['is_ongoing'] ?? ($startAt <= $now && $endAt >= $now)) {
    $execBadgeClass = 'bg-success-subtle text-success border border-success-subtle';
    $execBadgeLabel = 'Berlangsung';
} else {
    $execBadgeClass = 'bg-info-subtle text-info';
    $execBadgeLabel = 'Belum Dimulai';
}

// Attendance / Presensi status badge
$attBadgeClass = null;
$attBadgeLabel = null;
$attBadgeIcon = null;

if (! empty($ev['has_attended'])) {
    $attBadgeClass = 'bg-success text-white';
    $attBadgeLabel = 'Telah Hadir';
    $attBadgeIcon = 'fas fa-check-circle';
} elseif ($isApproved && ! empty($ev['is_presensi_active'])) {
    $attBadgeClass = 'bg-success-subtle text-success border border-success';
    $attBadgeLabel = 'Presensi Aktif';
    $attBadgeIcon = 'fas fa-door-open';
} elseif (! empty($ev['is_presensi_closed'])) {
    $attBadgeClass = 'bg-secondary-subtle text-secondary';
    $attBadgeLabel = 'Presensi Ditutup';
    $attBadgeIcon = 'fas fa-door-closed';
}

$detailUrl = route_to('workspace.participant.event.detail', (string) $ev['event_id']);
?>

<div class="col-md-6">
    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white d-flex flex-column justify-content-between position-relative overflow-hidden">
        <!-- Cover Banner (If Available) -->
        <?php if (! empty($ev['cover_path'])): ?>
            <div style="height: 160px; overflow: hidden; background: #082f5c;">
                <img src="<?= base_url($ev['cover_path']) ?>" alt="<?= esc($ev['title']) ?>" class="w-100 h-100 object-fit-cover opacity-90">
            </div>
        <?php else: ?>
            <div class="p-3 text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #082f5c 0%, #1a4d80 100%); min-height: 80px;">
                <span class="badge bg-white text-dark px-3 py-1 rounded-pill small fw-semibold">
                    <i class="fas fa-tag me-1 text-primary"></i> <?= esc(strtoupper($ev['type'] ?? 'EVENT')) ?>
                </span>
                <span class="small opacity-75">
                    <i class="fas fa-video me-1"></i> <?= esc(ucfirst($ev['mode'] ?? 'online')) ?>
                </span>
            </div>
        <?php endif; ?>

        <div class="p-4 flex-grow-1 d-flex flex-column justify-content-between">
            <div>
                <!-- Status Badges Header -->
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                    <div class="d-flex flex-wrap gap-1 align-items-center">
                        <?php if ($isApproved): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small fw-semibold">
                                <i class="fas fa-check-circle me-1"></i> Disetujui (Approved)
                            </span>
                        <?php elseif ($regStatus === 'cancelled'): ?>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill small fw-semibold">
                                <i class="fas fa-times-circle me-1"></i> Ditolak / Dibatalkan
                            </span>
                        <?php else: ?>
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill small fw-semibold">
                                <i class="fas fa-hourglass-half me-1"></i> Menunggu Persetujuan
                            </span>
                        <?php endif; ?>

                        <span class="badge <?= $execBadgeClass ?> px-2 py-1 rounded-pill small fw-semibold">
                            <i class="fas fa-circle me-1 small"></i> <?= esc($execBadgeLabel) ?>
                        </span>
                        <?php if ($attBadgeLabel !== null): ?>
                            <span class="badge <?= $attBadgeClass ?> px-2 py-1 rounded-pill small fw-semibold">
                                <i class="<?= $attBadgeIcon ?> me-1"></i> <?= esc($attBadgeLabel) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <span class="badge bg-light text-muted border small">
                        Reg #<?= (int) $ev['registration_id'] ?>
                    </span>
                </div>

                <!-- Event Title -->
                <h2 class="h5 fw-bold mb-3" style="color: #082f5c;">
                    <a href="<?= $detailUrl ?>" class="text-decoration-none text-reset">
                        <?= esc($ev['title']) ?>
                    </a>
                </h2>

                <!-- Event Metadata -->
                <ul class="list-unstyled text-muted small mb-3">
                    <li class="mb-2 d-flex align-items-center gap-2">
                        <i class="far fa-calendar-alt text-warning fa-fw"></i>
                        <span><?= esc(date('d M Y', $startAt)) ?> &middot; <?= esc(date('H:i', $startAt)) ?> - <?= esc(date('H:i', $endAt)) ?> WIB</span>
                    </li>
                    <li class="mb-2 d-flex align-items-center gap-2">
                        <i class="fas fa-map-marker-alt text-danger fa-fw"></i>
                        <span>
                            <?php if (! empty($ev['location'])): ?>
                                <?= esc($ev['location']) ?> (<?= esc(ucfirst($ev['mode'])) ?>)
                            <?php else: ?>
                                <?= esc(ucfirst($ev['mode'])) ?> &middot; Sesi Terjadwal
                            <?php endif; ?>
                        </span>
                    </li>
                    <?php if (! empty($ev['certificate_enabled'])): ?>
                        <li class="d-flex align-items-center gap-2">
                            <i class="fas fa-award text-success fa-fw"></i>
                            <span>
                                <?php if (! empty($ev['certificate_status']) && $ev['certificate_status'] === 'issued'): ?>
                                    <strong class="text-success">Sertifikat Telah Terbit</strong>
                                <?php else: ?>
                                    Sertifikat Tersedia untuk Peserta Hadir
                                <?php endif; ?>
                            </span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Footer Actions -->
            <div class="pt-3 border-top d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="far fa-clock me-1"></i> Daftar: <?= esc(date('d/m/Y', strtotime($ev['registered_at']))) ?>
                </small>
                <div class="d-flex flex-wrap gap-2">
                    <?php if ($isApproved && ! empty($ev['is_presensi_active']) && empty($ev['has_attended'])): ?>
                        <a href="<?= $detailUrl ?>#presensi" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold">
                            <i class="fas fa-qrcode me-1"></i> Presensi Sekarang
                        </a>
                    <?php endif; ?>
                    <a href="<?= $detailUrl ?>" class="btn <?= $isApproved ? 'btn-primary' : 'btn-outline-secondary' ?> btn-sm rounded-pill px-3 fw-semibold">
                        <?php if ($isApproved): ?>
                            <i class="fas fa-door-open me-1"></i> Masuk Ruang Event
                        <?php else: ?>
                            <i class="fas fa-clock me-1"></i> Cek Status Approval
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
