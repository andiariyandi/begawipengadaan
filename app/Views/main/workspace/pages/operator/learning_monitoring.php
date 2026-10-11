<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>

<div class="operator-learning-monitoring-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-info text-dark px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-chart-line me-1"></i> Pengawasan Operasional
                </span>
                <h1 class="h3 fw-bold text-navy mb-1">Monitoring Pembelajaran Kelas</h1>
                <p class="text-muted small mb-0">
                    Pantau jalannya proses pembelajaran, keaktifan trainer, serta progres dan status kelulusan peserta pada seluruh kelas PBJ.
                </p>
            </div>
            <div>
                <a href="<?= route_to('workspace.operator.class-trainers') ?>" class="btn btn-outline-primary rounded-pill px-3">
                    <i class="fas fa-chalkboard-teacher me-1"></i> Kelola Penugasan Trainer
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Counters -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary-subtle text-primary fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Kelas Berjalan</span>
                        <strong class="h4 fw-bold text-navy mb-0"><?= count($monitoring) ?></strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-info-subtle text-info fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Total Peserta</span>
                        <strong class="h4 fw-bold text-navy mb-0"><?= array_sum(array_column($monitoring, 'student_count')) ?></strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success-subtle text-success fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Peserta Lulus</span>
                        <strong class="h4 fw-bold text-navy mb-0"><?= array_sum(array_column($monitoring, 'passed_count')) ?></strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-warning-subtle text-warning fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Rata-rata Progres</span>
                        <?php
                        $avgAll = !empty($monitoring) ? round(array_sum(array_column($monitoring, 'avg_progress')) / count($monitoring), 1) : 0.0;
                        ?>
                        <strong class="h4 fw-bold text-navy mb-0"><?= $avgAll ?>%</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monitoring Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-bold text-navy mb-0"><i class="fas fa-chalkboard text-primary me-2"></i>Status Pembelajaran Lintas Kelas</h2>
            <span class="badge bg-light text-dark border px-3 py-2 small"><?= count($monitoring) ?> Kelas Terdata</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th>Kelas & Course</th>
                        <th>Trainer Bertugas</th>
                        <th>Periode</th>
                        <th>Peserta</th>
                        <th>Rata-rata Progres</th>
                        <th>Lulus</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($monitoring)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada kelas pembelajaran aktif.</td></tr>
                    <?php else: ?>
                        <?php foreach ($monitoring as $item): ?>
                            <tr>
                                <td>
                                    <strong class="text-navy small d-block"><?= esc($item['name']) ?></strong>
                                    <span class="text-muted" style="font-size: 0.78rem;"><?= esc($item['course_title'] ?? 'Course PBJ') ?></span>
                                    <span class="badge bg-light text-dark border font-monospace mt-1" style="font-size: 0.7rem;"><?= esc($item['code']) ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($item['trainers_text'])): ?>
                                        <div class="d-flex align-items-center gap-1">
                                            <i class="fas fa-chalkboard-teacher text-primary"></i>
                                            <strong class="text-navy small"><?= esc($item['trainers_text']) ?></strong>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small fst-italic">Belum ditugaskan</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted">
                                    <?= !empty($item['start_at']) ? date('d/m/Y', strtotime($item['start_at'])) : '-' ?> &mdash; <?= !empty($item['end_at']) ? date('d/m/Y', strtotime($item['end_at'])) : '-' ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 small">
                                        <?= (int) ($item['student_count'] ?? 0) ?> Peserta
                                    </span>
                                </td>
                                <td style="width: 160px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar bg-success rounded-pill" style="width: <?= min(100, max(0, (float) ($item['avg_progress'] ?? 0))) ?>%;"></div>
                                        </div>
                                        <span class="small fw-semibold text-navy"><?= number_format((float) ($item['avg_progress'] ?? 0), 1) ?>%</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small">
                                        <?= (int) ($item['passed_count'] ?? 0) ?> Lulus
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= ($item['status'] === 'active') ? 'bg-success' : 'bg-secondary' ?> rounded-pill px-2 py-1 small">
                                        <?= esc(ucfirst($item['status'])) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="<?= route_to('workspace.trainer.class.detail', (string) $item['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                                            <i class="fas fa-chalkboard me-1"></i> Kelola Pembelajaran
                                        </a>
                                        <a href="<?= route_to('workspace.operator.class-trainers') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" style="font-size: 0.78rem;">
                                            <i class="fas fa-chalkboard-teacher me-1"></i> Penugasan
                                        </a>
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
