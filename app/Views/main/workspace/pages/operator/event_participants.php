<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>

<div class="operator-event-participants-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-primary text-white px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-calendar-alt me-1"></i> Operasional Event
                </span>
                <h1 class="h3 fw-bold text-navy mb-1"><?= esc($event['title'] ?? 'Peserta Event') ?></h1>
                <p class="text-muted small mb-0">
                    Tipe: <strong><?= esc($event['type'] ?? '-') ?></strong> &middot; Moda: <strong><?= esc($event['mode'] ?? '-') ?></strong> &middot; Total Peserta: <strong><?= count($participants) ?></strong>
                </p>
            </div>
            <div>
                <a href="<?= route_to('workspace.operator.events') ?>" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Event
                </a>
            </div>
        </div>
    </div>

    <!-- Participants Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-bold text-navy mb-0"><i class="fas fa-users text-primary me-2"></i>Daftar Peserta Terdaftar</h2>
            <span class="badge bg-light text-dark border px-3 py-2 small"><?= count($participants) ?> Peserta</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th>Peserta</th>
                        <th>Waktu Daftar</th>
                        <th>Status Registrasi</th>
                        <th>Kehadiran Presensi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($participants)): ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada peserta yang mendaftar pada event ini.</td></tr>
                    <?php else: ?>
                        <?php foreach ($participants as $p): ?>
                            <tr>
                                <td>
                                    <strong class="text-navy small d-block"><?= esc($p['user_name']) ?></strong>
                                    <span class="text-muted" style="font-size: 0.78rem;"><?= esc($p['user_email']) ?></span>
                                </td>
                                <td class="small text-muted">
                                    <?= !empty($p['registered_at']) ? date('d/m/Y H:i', strtotime($p['registered_at'])) : '-' ?>
                                </td>
                                <td>
                                    <span class="badge <?= ($p['status'] === 'confirmed') ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' ?> rounded-pill px-2 py-1 small">
                                        <?= esc(ucfirst($p['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($p['attendance_status'] === 'present'): ?>
                                        <span class="badge bg-success rounded-pill px-2 py-1 small"><i class="fas fa-check me-1"></i>Hadir</span>
                                    <?php elseif ($p['attendance_status'] === 'excused'): ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-2 py-1 small">Izin</span>
                                    <?php elseif ($p['attendance_status'] === 'absent'): ?>
                                        <span class="badge bg-danger rounded-pill px-2 py-1 small">Tidak Hadir</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-1 small">Belum Presensi</span>
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

<?= $this->endSection() ?>
