<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>

<?php
$m = $attendanceMetrics ?? [
    'total'           => count($participants),
    'present'         => 0,
    'excused'         => 0,
    'absent'          => 0,
    'unrecorded'      => count($participants),
    'attendance_rate' => 0.0,
    'quota'           => (int) ($event['quota'] ?? 0),
];
?>

<div class="operator-event-participants-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-primary text-white px-3 py-1 rounded-pill small fw-semibold">
                        <i class="fas fa-calendar-alt me-1"></i> Operasional Event
                    </span>
                    <span class="badge bg-light text-navy border px-3 py-1 rounded-pill small">
                        Moda: <strong><?= esc(ucfirst((string) ($event['mode'] ?? 'Online'))) ?></strong>
                    </span>
                    <span class="badge bg-light text-navy border px-3 py-1 rounded-pill small">
                        Tipe: <strong><?= esc(ucfirst((string) ($event['type'] ?? 'Webinar'))) ?></strong>
                    </span>
                </div>
                <h1 class="h3 fw-bold text-navy mb-1"><?= esc($event['title'] ?? 'Peserta Event') ?></h1>
                <p class="text-muted small mb-0">
                    <i class="far fa-clock me-1 text-primary"></i>
                    <?= !empty($event['start_at']) ? date('d F Y, H:i', strtotime($event['start_at'])) : '-' ?> s.d. <?= !empty($event['end_at']) ? date('H:i', strtotime($event['end_at'])) . ' WIB' : 'Selesai' ?>
                    &middot; Lokasi: <strong><?= esc($event['location_or_link'] ?? '-') ?></strong>
                </p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="<?= route_to('workspace.operator.events.roster.print', (string) ($event['id'] ?? 0)) ?>" target="_blank" class="btn btn-primary rounded-pill px-3 shadow-sm fw-semibold">
                    <i class="fas fa-print me-1"></i> Cetak Daftar Hadir Resmi
                </a>
                <a href="<?= route_to('workspace.operator.events') ?>" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Event
                </a>
            </div>
        </div>
    </div>

    <!-- Live Attendance Gauge & KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-primary-subtle text-primary fs-4 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block" style="font-size: 0.72rem;">Peserta Terdaftar</span>
                        <strong class="h4 fw-bold text-navy mb-0"><?= (int) $m['total'] ?></strong>
                        <?php if ($m['quota'] > 0): ?>
                            <small class="text-muted" style="font-size: 0.7rem;">/ Kuota: <?= $m['quota'] ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-success-subtle text-success fs-4 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block" style="font-size: 0.72rem;">Telah Hadir (Check-In)</span>
                        <strong class="h4 fw-bold text-success mb-0"><?= (int) $m['present'] ?></strong>
                        <small class="text-success fw-semibold" style="font-size: 0.7rem;">(<?= (float) $m['attendance_rate'] ?>%)</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-warning-subtle text-warning fs-4 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block" style="font-size: 0.72rem;">Izin / Sakit</span>
                        <strong class="h4 fw-bold text-warning mb-0"><?= (int) $m['excused'] ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-secondary-subtle text-secondary fs-4 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block" style="font-size: 0.72rem;">Belum Presensi</span>
                        <strong class="h4 fw-bold text-navy mb-0"><?= (int) $m['unrecorded'] ?></strong>
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
                <h6 class="fw-bold text-navy mb-0">Rasio Kehadiran Peserta (Live Gauge)</h6>
            </div>
            <strong class="text-success fs-6"><?= (float) $m['attendance_rate'] ?>% Terverifikasi Hadir</strong>
        </div>
        <div class="progress rounded-pill" style="height: 10px;">
            <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: <?= (float) $m['attendance_rate'] ?>%" aria-valuenow="<?= (float) $m['attendance_rate'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-2 small text-muted">
            <span>0% (Mulai Acara)</span>
            <span>Target Administrasi: Minimal 75% Hadir Sah</span>
            <span>100% Kuota</span>
        </div>
    </div>

    <!-- Participants Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <div>
                <h2 class="h5 fw-bold text-navy mb-0"><i class="fas fa-users text-primary me-2"></i>Daftar Peserta Terdaftar</h2>
                <small class="text-muted">Rincian data identitas instansi, jabatan, dan catatan presensi kegiatan.</small>
            </div>
            <div class="position-relative" style="min-width: 280px;">
                <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" id="wsParticipantSearchInput" class="form-control form-control-sm rounded-pill ps-5 py-2" placeholder="Cari nama atau instansi peserta...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="wsParticipantTable" style="font-size: 0.84rem;">
                <thead class="table-light text-navy">
                    <tr>
                        <th style="width: 40px;" class="text-center">#</th>
                        <th>Peserta</th>
                        <th>Instansi & Jabatan</th>
                        <th>Waktu Registrasi</th>
                        <th>Status Registrasi</th>
                        <th class="text-center">Status Presensi</th>
                        <th class="text-center">Waktu Check-In</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($participants)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada peserta yang mendaftar pada event ini.</td></tr>
                    <?php else: ?>
                        <?php foreach ($participants as $idx => $p): ?>
                            <?php
                            $st = $p['attendance_status'] ?? null;
                            $searchQuery = strtolower(($p['user_name'] ?? '') . ' ' . ($p['user_email'] ?? '') . ' ' . ($p['institution'] ?? '') . ' ' . ($p['position'] ?? ''));
                            ?>
                            <tr class="ws-participant-row" data-search="<?= esc($searchQuery) ?>">
                                <td class="text-center text-muted"><?= $idx + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle bg-primary-subtle text-primary fw-bold" style="width: 32px; height: 32px; font-size: 11px;">
                                            <?= strtoupper(substr($p['user_name'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong class="text-navy small d-block"><?= esc($p['user_name']) ?></strong>
                                            <span class="text-muted" style="font-size: 0.72rem;"><?= esc($p['user_email']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="d-block fw-semibold text-navy"><?= esc($p['institution'] ?? '-') ?></span>
                                    <small class="text-muted"><?= esc($p['position'] ?? '-') ?></small>
                                </td>
                                <td class="small text-muted">
                                    <?= !empty($p['registered_at']) ? date('d/m/Y H:i', strtotime($p['registered_at'])) : '-' ?>
                                </td>
                                <td>
                                    <span class="badge <?= ($p['status'] === 'confirmed') ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' ?> rounded-pill px-2 py-1 small">
                                        <?= esc(ucfirst((string) $p['status'])) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($st === 'present'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 small">
                                            <i class="fas fa-check me-1"></i>Hadir
                                        </span>
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
                                <td class="text-center text-muted small">
                                    <?= !empty($p['attendance_recorded_at']) ? date('H:i:s', strtotime($p['attendance_recorded_at'])) : '-' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sInput = document.getElementById('wsParticipantSearchInput');
            const rows = document.querySelectorAll('.ws-participant-row');

            if (sInput) {
                sInput.addEventListener('input', function() {
                    const q = this.value.toLowerCase().trim();
                    rows.forEach(function(r) {
                        const sData = r.getAttribute('data-search') || '';
                        if (q === '' || sData.includes(q)) {
                            r.style.display = '';
                        } else {
                            r.style.display = 'none';
                        }
                    });
                });
            }
        });
        </script>
    </div>
</div>

<?= $this->endSection() ?>
