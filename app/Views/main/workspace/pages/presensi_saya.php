<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="ws-page-header">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="ws-eyebrow"><i class="fas fa-user-check"></i> <?= esc($sectionContent['eyebrow'] ?? 'Catatan Kehadiran') ?></span>
            <h1><?= esc($pageTitle ?? 'Presensi Saya') ?></h1>
            <p><?= esc($sectionContent['description'] ?? 'Rekap log presensi dan kehadiran Anda pada event serta sesi pembelajaran.') ?></p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= route_to('workspace.participant.events') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-calendar-alt me-1"></i> Event Saya
            </a>
            <a href="<?= site_url('event') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="fas fa-search me-1"></i> Cari Event Baru
            </a>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<?php if (!empty($sectionContent['cards'])): ?>
    <div class="row g-4 mb-4">
        <?php foreach ($sectionContent['cards'] as $card): ?>
            <div class="col-sm-6 col-lg-6">
                <div class="card ws-stat-card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <span class="text-muted small fw-semibold text-uppercase"><?= esc($card['title']) ?></span>
                    <div class="display-6 fw-bold my-2" style="color: #082f5c;"><?= esc($card['value']) ?></div>
                    <small class="text-muted"><?= esc($card['meta'] ?? '') ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Form Presensi Event -->
<div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4 bg-white">
    <div class="row align-items-center g-4">
        <div class="col-lg-6">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(8,47,92,0.08); color: #082f5c;">
                    <i class="fas fa-qrcode fs-4"></i>
                </div>
                <div>
                    <h2 class="h5 fw-bold mb-0" style="color: #082f5c;">Form Presensi Event</h2>
                    <small class="text-muted">Masukkan kode kehadiran yang dibagikan oleh panitia/penyelenggara</small>
                </div>
            </div>
            <p class="text-muted small mb-0">Pastikan Anda telah mendaftar pada event dan sesi presensi sedang dibuka oleh penyelenggara.</p>
        </div>
        <div class="col-lg-6">
            <form action="<?= route_to('workspace.participant.attendance.checkin') ?>" method="post" class="d-flex flex-column flex-sm-row gap-2">
                <?= csrf_field() ?>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-key"></i></span>
                    <input type="text" name="attendance_code" class="form-control text-uppercase font-monospace border-start-0" placeholder="Kode Kehadiran (Contoh: BGW-EVT-82K91)" required autocomplete="off" style="letter-spacing: 1px;">
                </div>
                <button type="submit" class="btn btn-primary px-4 rounded-3 text-nowrap fw-semibold">
                    <i class="fas fa-check-circle me-1"></i> Konfirmasi Kehadiran
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Attendance Table -->
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h2 class="h5 fw-bold mb-0" style="color: #082f5c;">Riwayat Presensi Saya</h2>
            <small class="text-muted">Daftar kehadiran yang telah terekam dan diverifikasi</small>
        </div>
        <span class="badge bg-light text-dark border"><?= count($attendances ?? []) ?> Catatan</span>
    </div>

    <?php if (empty($attendances)): ?>
        <div class="text-center py-5 text-muted">
            <i class="fas fa-user-check fs-1 text-muted opacity-50 mb-3"></i>
            <p class="mb-0">Belum ada catatan presensi yang terekam untuk akun Anda.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="small text-uppercase text-muted">Event / Sesi</th>
                        <th class="small text-uppercase text-muted">Mode</th>
                        <th class="small text-uppercase text-muted">Status Kehadiran</th>
                        <th class="small text-uppercase text-muted">Waktu Presensi</th>
                        <th class="small text-uppercase text-muted">Catatan</th>
                        <th class="small text-uppercase text-muted text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendances as $att): ?>
                        <tr>
                            <td>
                                <?php if (! empty($att['event_id'])): ?>
                                    <a href="<?= route_to('workspace.participant.event.detail', (string) $att['event_id']) ?>" class="fw-bold text-decoration-none" style="color: #082f5c;">
                                        <?= esc($att['event_title']) ?>
                                    </a>
                                <?php else: ?>
                                    <strong class="d-block" style="color: #082f5c;"><?= esc($att['event_title']) ?></strong>
                                <?php endif; ?>
                                <small class="text-muted d-block"><?= esc(date('d M Y', strtotime($att['start_at']))) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border small"><?= esc(ucfirst($att['event_mode'] ?? 'online')) ?></span>
                            </td>
                            <td>
                                <?php
                                $status = strtolower($att['status']);
                                $badge = match ($status) {
                                    'present', 'hadir' => 'bg-success-subtle text-success border border-success-subtle',
                                    'absent', 'alpa'   => 'bg-danger-subtle text-danger',
                                    'late', 'terlambat'=> 'bg-warning-subtle text-warning-emphasis',
                                    'excused', 'izin'  => 'bg-info-subtle text-info',
                                    default            => 'bg-secondary-subtle text-secondary',
                                };
                                ?>
                                <span class="badge <?= $badge ?> rounded-pill px-3 py-1 fw-semibold">
                                    <i class="fas fa-check-circle me-1"></i> <?= esc(ucfirst($status)) ?>
                                </span>
                            </td>
                            <td class="text-muted small">
                                <i class="far fa-clock me-1"></i>
                                <?= esc(date('d/m/Y H:i', strtotime($att['recorded_at']))) ?> WIB
                            </td>
                            <td class="small text-muted">
                                <?= esc($att['notes'] ?: '-') ?>
                            </td>
                            <td class="text-end">
                                <?php if (! empty($att['event_id'])): ?>
                                    <a href="<?= route_to('workspace.participant.event.detail', (string) $att['event_id']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                        Detail Event
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
