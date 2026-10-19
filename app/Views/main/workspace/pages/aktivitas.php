<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="ws-page-header">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="ws-eyebrow"><i class="fas fa-history"></i> Riwayat & Jejak Audit</span>
            <h1><?= esc($pageTitle ?? 'Aktivitas Saya') ?></h1>
            <p>Rekap seluruh interaksi, aktivitas pembelajaran, dan riwayat sistem pada akun Anda.</p>
        </div>
        <div>
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold">
                <i class="fas fa-list-check me-1"></i> <?= count($logs ?? []) ?> Aktivitas Terkini
            </span>
        </div>
    </div>
</div>

<!-- Log Table -->
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <h2 class="h5 fw-bold mb-0" style="color: #082f5c;">Catatan Aktivitas Akun</h2>
        <span class="badge bg-light text-dark border"><?= count($logs ?? []) ?> Aktivitas Terkini</span>
    </div>

    <?php if (empty($logs)): ?>
        <div class="text-center py-5 text-muted">
            <i class="fas fa-history fs-1 text-muted opacity-50 mb-3"></i>
            <p class="mb-0">Belum ada catatan aktivitas yang terekam pada akun Anda.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="small text-uppercase text-muted">Aktivitas / Aksi</th>
                        <th class="small text-uppercase text-muted">Objek / Modul</th>
                        <th class="small text-uppercase text-muted">Hasil</th>
                        <th class="small text-uppercase text-muted">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td>
                                <strong class="d-block text-dark"><?= esc($log['action']) ?></strong>
                                <?php if (!empty($log['reason'])): ?>
                                    <small class="text-muted"><?= esc($log['reason']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border small">
                                    <?= esc($log['resource_type'] ? $log['resource_type'] . ' #' . $log['resource_id'] : 'Sistem') ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $result = strtolower($log['result'] ?? 'success');
                                $badge = match ($result) {
                                    'success' => 'bg-success-subtle text-success',
                                    'failure', 'failed' => 'bg-danger-subtle text-danger',
                                    default   => 'bg-info-subtle text-info',
                                };
                                ?>
                                <span class="badge <?= $badge ?> rounded-pill px-3 py-1">
                                    <?= esc(ucfirst($result)) ?>
                                </span>
                            </td>
                            <td class="text-muted small">
                                <?= esc(date('d/m/Y H:i', strtotime($log['created_at']))) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
