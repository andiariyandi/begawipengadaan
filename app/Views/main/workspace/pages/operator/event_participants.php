<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>

<?php
$m = $attendanceMetrics ?? [
    'total'            => count($participants),
    'pending_approval' => 0,
    'approved'         => 0,
    'present'          => 0,
    'excused'          => 0,
    'absent'           => 0,
    'unrecorded'       => count($participants),
    'attendance_rate'  => 0.0,
    'quota'            => (int) ($event['quota'] ?? $event['capacity'] ?? 0),
];
$pendingApprovalCount = (int) ($m['pending_approval'] ?? 0);
?>

<div class="operator-event-participants-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-primary text-white px-3 py-1 rounded-pill small fw-semibold">
                        <i class="fas fa-calendar-alt me-1"></i> Operasional Event & Presensi
                    </span>
                    <span class="badge bg-light text-navy border px-3 py-1 rounded-pill small">
                        Moda: <strong><?= esc(ucfirst((string) ($event['mode'] ?? 'Online'))) ?></strong>
                    </span>
                    <span class="badge bg-light text-navy border px-3 py-1 rounded-pill small">
                        Tipe: <strong><?= esc(ucfirst((string) ($event['type'] ?? 'Webinar'))) ?></strong>
                    </span>
                    <?php if (! empty($event['attendance_code'])): ?>
                        <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 rounded-pill small font-monospace">
                            <i class="fas fa-key me-1"></i> Kode Presensi: <strong><?= esc($event['attendance_code']) ?></strong>
                        </span>
                    <?php endif; ?>
                </div>
                <h1 class="h3 fw-bold text-navy mb-1"><?= esc($event['title'] ?? 'Peserta Event') ?></h1>
                <p class="text-muted small mb-0">
                    <i class="far fa-clock me-1 text-primary"></i>
                    <?= !empty($event['start_at']) ? date('d F Y, H:i', strtotime($event['start_at'])) : '-' ?> s.d. <?= !empty($event['end_at']) ? date('H:i', strtotime($event['end_at'])) . ' WIB' : 'Selesai' ?>
                    &middot; Lokasi: <strong><?= esc($event['location'] ?? $event['location_or_link'] ?? '-') ?></strong>
                </p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <?php if ($pendingApprovalCount > 0): ?>
                    <form action="<?= route_to('workspace.operator.events.registration.bulk', (string) ($event['id'] ?? 0)) ?>" method="post" class="d-inline" onsubmit="return confirm('Setujui (Approve) seluruh <?= $pendingApprovalCount ?> pendaftaran yang masih menunggu persetujuan?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="bulk_action" value="approve_all_pending">
                        <button type="submit" class="btn btn-success rounded-pill px-3 shadow-sm fw-semibold">
                            <i class="fas fa-check-double me-1"></i> Setujui Semua Pending (<?= $pendingApprovalCount ?>)
                        </button>
                    </form>
                <?php endif; ?>
                <a href="<?= route_to('workspace.operator.events.roster.print', (string) ($event['id'] ?? 0)) ?>" target="_blank" class="btn btn-primary rounded-pill px-3 shadow-sm fw-semibold">
                    <i class="fas fa-print me-1"></i> Cetak Daftar Hadir Resmi
                </a>
                <a href="<?= route_to('workspace.operator.events') ?>" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Event
                </a>
            </div>
        </div>
    </div>

    <!-- Live Attendance & Approval KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-2 bg-primary-subtle text-primary fs-5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block" style="font-size: 0.7rem;">Total Pendaftar</span>
                        <strong class="h5 fw-bold text-navy mb-0"><?= (int) $m['total'] ?></strong>
                        <?php if ($m['quota'] > 0): ?>
                            <small class="text-muted" style="font-size: 0.68rem;">/ <?= $m['quota'] ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-2 bg-warning-subtle text-warning fs-5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block" style="font-size: 0.7rem;">Pending Approval</span>
                        <strong class="h5 fw-bold text-warning-emphasis mb-0"><?= $pendingApprovalCount ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-2 bg-info-subtle text-info fs-5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-id-badge"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block" style="font-size: 0.7rem;">Disetujui (Approved)</span>
                        <strong class="h5 fw-bold text-info mb-0"><?= (int) ($m['approved'] ?? 0) ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-2 bg-success-subtle text-success fs-5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block" style="font-size: 0.7rem;">Hadir (Check-In)</span>
                        <strong class="h5 fw-bold text-success mb-0"><?= (int) $m['present'] ?></strong>
                        <small class="text-success fw-semibold" style="font-size: 0.68rem;">(<?= (float) $m['attendance_rate'] ?>%)</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-2 bg-warning-subtle text-warning fs-5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block" style="font-size: 0.7rem;">Izin / Sakit</span>
                        <strong class="h5 fw-bold text-warning mb-0"><?= (int) $m['excused'] ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-2 bg-danger-subtle text-danger fs-5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block" style="font-size: 0.7rem;">Absen / Belum</span>
                        <strong class="h5 fw-bold text-navy mb-0"><?= (int) $m['absent'] ?> / <?= (int) $m['unrecorded'] ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Attendance Progress Gauge Banner -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-tachometer-alt text-primary fs-5"></i>
                <h6 class="fw-bold text-navy mb-0">Rasio Kehadiran Peserta & Trainer (Live Gauge)</h6>
            </div>
            <strong class="text-success fs-6"><?= (float) $m['attendance_rate'] ?>% Terverifikasi Hadir</strong>
        </div>
        <div class="progress rounded-pill" style="height: 10px;">
            <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: <?= (float) $m['attendance_rate'] ?>%" aria-valuenow="<?= (float) $m['attendance_rate'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>

    <!-- Participants Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <div>
                <h2 class="h5 fw-bold text-navy mb-0"><i class="fas fa-users text-primary me-2"></i>Daftar Pendaftar, Approval & Presensi Kehadiran</h2>
                <small class="text-muted">Kelola persetujuan pendaftaran (Peserta & Trainer) serta pencatatan kehadiran sesi secara real-time.</small>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" data-export-title="Daftar Peserta dan Presensi Event <?= esc($event['title'] ?? '') ?>" style="font-size: 0.84rem;">
                <thead class="table-light text-navy">
                    <tr>
                        <th style="width: 40px;" class="text-center">#</th>
                        <th>Peserta / Trainer</th>
                        <th>Peran</th>
                        <th>Instansi & Jabatan</th>
                        <th>Waktu Daftar</th>
                        <th>Approval Registrasi</th>
                        <th class="text-center">Status Presensi</th>
                        <th class="text-end" data-no-sort="true">Aksi Approval & Presensi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($participants)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada peserta atau trainer yang mendaftar pada event ini.</td></tr>
                    <?php else: ?>
                        <?php foreach ($participants as $idx => $p): ?>
                            <?php
                            $st = $p['attendance_status'] ?? null;
                            $regStatus = strtolower((string) ($p['status'] ?? 'registered'));
                            $isApproved = in_array($regStatus, ['registered', 'confirmed', 'attended', 'present', 'completed', 'promoted'], true);
                            $isPending  = in_array($regStatus, ['waitlisted', 'waitlist', 'waiting', 'pending'], true);
                            $roleBadge  = $p['role_badge'] ?? 'Peserta';
                            ?>
                            <tr>
                                <td class="text-center text-muted"><?= $idx + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle bg-primary-subtle text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 11px;">
                                            <?= strtoupper(substr($p['user_name'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong class="text-navy small d-block"><?= esc($p['user_name']) ?></strong>
                                            <span class="text-muted" style="font-size: 0.72rem;"><?= esc($p['user_email']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge <?= $roleBadge === 'Trainer' ? 'bg-info-subtle text-info border border-info-subtle' : 'bg-light text-dark border' ?> rounded-pill px-2 py-1 small">
                                        <i class="fas <?= $roleBadge === 'Trainer' ? 'fa-chalkboard-teacher' : 'fa-user-graduate' ?> me-1"></i><?= esc($roleBadge) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="d-block fw-semibold text-navy"><?= esc($p['institution'] ?? '-') ?></span>
                                    <small class="text-muted"><?= esc($p['position'] ?? '-') ?></small>
                                </td>
                                <td class="small text-muted">
                                    <?= !empty($p['registered_at']) ? date('d/m/Y H:i', strtotime($p['registered_at'])) : '-' ?>
                                </td>
                                <td>
                                    <?php if ($isApproved): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                                            <i class="fas fa-check-circle me-1"></i>Disetujui
                                        </span>
                                    <?php elseif ($regStatus === 'cancelled'): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1 small">
                                            <i class="fas fa-times-circle me-1"></i>Ditolak
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1 small">
                                            <i class="fas fa-hourglass-half me-1"></i>Menunggu Approval
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($st === 'present'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 small">
                                            <i class="fas fa-check me-1"></i>Hadir
                                        </span>
                                        <?php if (!empty($p['attendance_recorded_at'])): ?>
                                            <small class="d-block text-muted mt-1" style="font-size: 0.68rem;"><?= date('H:i', strtotime($p['attendance_recorded_at'])) ?> WIB</small>
                                        <?php endif; ?>
                                    <?php elseif ($st === 'excused'): ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1 small">
                                            <i class="fas fa-clock me-1"></i>Izin
                                        </span>
                                    <?php elseif ($st === 'absent'): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1 small">
                                            <i class="fas fa-times me-1"></i>Tidak Hadir
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border rounded-pill px-3 py-1 small">
                                            Belum Presensi
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex flex-wrap justify-content-end gap-1">
                                        <?php if ($isPending): ?>
                                            <form action="<?= route_to('workspace.operator.events.registration.approve', (string) $p['id']) ?>" method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-success py-0 px-2 rounded-pill" title="Setujui Pendaftaran (Approve)">
                                                    <i class="fas fa-check me-1"></i>Approve
                                                </button>
                                            </form>
                                            <form action="<?= route_to('workspace.operator.events.registration.reject', (string) $p['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Tolak pendaftaran peserta ini?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2 rounded-pill" title="Tolak Pendaftaran">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if ($regStatus !== 'cancelled'): ?>
                                            <div class="btn-group btn-group-sm" role="group" aria-label="Presensi Cepat">
                                                <form action="<?= route_to('workspace.operator.events.registration.attendance', (string) $p['id']) ?>" method="post" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="status" value="present">
                                                    <button type="submit" class="btn btn-sm <?= $st === 'present' ? 'btn-success' : 'btn-outline-success' ?> py-0 px-2" title="Catat Hadir">
                                                        Hadir
                                                    </button>
                                                </form>
                                                <form action="<?= route_to('workspace.operator.events.registration.attendance', (string) $p['id']) ?>" method="post" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="status" value="excused">
                                                    <button type="submit" class="btn btn-sm <?= $st === 'excused' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' ?> py-0 px-2" title="Catat Izin">
                                                        Izin
                                                    </button>
                                                </form>
                                                <form action="<?= route_to('workspace.operator.events.registration.attendance', (string) $p['id']) ?>" method="post" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="status" value="absent">
                                                    <button type="submit" class="btn btn-sm <?= $st === 'absent' ? 'btn-danger' : 'btn-outline-danger' ?> py-0 px-2" title="Catat Tidak Hadir (Absen)">
                                                        Absen
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
