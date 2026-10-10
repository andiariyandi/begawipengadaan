<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="bg-white border rounded-4 p-4 p-lg-5 mb-4 shadow-sm">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <p class="text-uppercase text-muted small fw-semibold mb-2"><?= esc($sectionContent['eyebrow'] ?? 'Catatan Kehadiran') ?></p>
            <h1 class="h3 fw-bold mb-2" style="color: #082f5c;"><?= esc($pageTitle ?? 'Presensi Saya') ?></h1>
            <p class="text-muted mb-0"><?= esc($sectionContent['description'] ?? 'Rekap log presensi dan kehadiran Anda pada event serta sesi pembelajaran.') ?></p>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<?php if (!empty($sectionContent['cards'])): ?>
    <div class="row g-4 mb-4">
        <?php foreach ($sectionContent['cards'] as $card): ?>
            <div class="col-sm-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <span class="text-muted small fw-semibold text-uppercase"><?= esc($card['title']) ?></span>
                    <div class="display-6 fw-bold my-2" style="color: #082f5c;"><?= esc($card['value']) ?></div>
                    <small class="text-muted"><?= esc($card['meta'] ?? '') ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Attendance Table -->
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <h2 class="h5 fw-bold mb-0" style="color: #082f5c;">Riwayat Presensi</h2>
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendances as $att): ?>
                        <tr>
                            <td>
                                <strong class="d-block" style="color: #082f5c;"><?= esc($att['event_title']) ?></strong>
                                <small class="text-muted"><?= esc(date('d M Y', strtotime($att['start_at']))) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border small"><?= esc(ucfirst($att['event_mode'] ?? 'online')) ?></span>
                            </td>
                            <td>
                                <?php
                                $status = strtolower($att['status']);
                                $badge = match ($status) {
                                    'present', 'hadir' => 'bg-success-subtle text-success',
                                    'absent', 'alpa'   => 'bg-danger-subtle text-danger',
                                    'late', 'terlambat'=> 'bg-warning-subtle text-warning-emphasis',
                                    'excused', 'izin'  => 'bg-info-subtle text-info',
                                    default            => 'bg-secondary-subtle text-secondary',
                                };
                                ?>
                                <span class="badge <?= $badge ?> rounded-pill px-3 py-1">
                                    <?= esc(ucfirst($status)) ?>
                                </span>
                            </td>
                            <td class="text-muted small">
                                <?= esc(date('d/m/Y H:i', strtotime($att['recorded_at']))) ?>
                            </td>
                            <td class="small text-muted">
                                <?= esc($att['notes'] ?: '-') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
