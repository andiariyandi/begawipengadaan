<?php
/**
 * Trainer - Progress Peserta (Agregat)
 * FiStudy UI DNA
 */
$classes        = $classes ?? [];
$classSummaries = $classSummaries ?? [];
?>

<div class="trainer-progress-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-chart-line me-1"></i> Monitoring Pembelajaran
                </span>
                <h1 class="h3 fw-bold text-navy mb-1">Progress Peserta</h1>
                <p class="text-muted small mb-0">Pemantauan tingkat penyelesaian materi dan kelulusan di seluruh kelas.</p>
            </div>
            <div>
                <a href="<?= route_to('workspace.trainer.classes') ?>" class="btn btn-outline-primary rounded-pill px-3">
                    <i class="fas fa-graduation-cap me-1"></i> Kelas Saya
                </a>
            </div>
        </div>
    </div>

    <!-- Class Progress Summaries Grid -->
    <?php if (empty($classSummaries)): ?>
        <div class="card border-0 shadow-sm rounded-4 bg-white p-5 text-center">
            <i class="fas fa-chart-bar fa-3x text-muted mb-3 opacity-25"></i>
            <h3 class="h6 fw-bold text-navy mb-1">Belum Ada Data Progres</h3>
            <p class="text-muted small mb-0">Belum ada kelas atau aktivitas peserta yang tercatat.</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($classSummaries as $cs): ?>
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100 bg-light-hover transition-all">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 small fw-semibold">
                                <?= esc($cs['code'] ?? 'KELAS') ?>
                            </span>
                            <span class="badge bg-light text-navy border rounded-pill px-2 py-1 small">
                                Completion: <?= esc($cs['completion_rate'] ?? 0.0) ?>%
                            </span>
                        </div>

                        <h2 class="h5 fw-bold text-navy mb-1"><?= esc($cs['name'] ?? '-') ?></h2>
                        <p class="text-muted small mb-3"><?= esc($cs['course_title'] ?? '-') ?></p>

                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center small text-muted mb-1">
                                <span>Rata-rata Progres Kelas</span>
                                <strong class="text-navy"><?= esc($cs['avg_progress'] ?? 0.0) ?>%</strong>
                            </div>
                            <div class="progress rounded-pill" style="height: 8px;">
                                <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: <?= esc($cs['avg_progress'] ?? 0.0) ?>%"></div>
                            </div>
                        </div>

                        <!-- 3 Stats Block -->
                        <div class="row g-2 text-center mb-4">
                            <div class="col-4">
                                <div class="border rounded-3 p-2 bg-light">
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Total Peserta</small>
                                    <strong class="text-navy"><?= (int) ($cs['total_enrollments'] ?? 0) ?></strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded-3 p-2 bg-light">
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Aktif Belajar</small>
                                    <strong class="text-primary"><?= (int) ($cs['active_enrollments'] ?? 0) ?></strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded-3 p-2 bg-light">
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Lulus / Selesai</small>
                                    <strong class="text-success"><?= (int) ($cs['completed_enrollments'] ?? 0) ?></strong>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto">
                            <a href="<?= route_to('workspace.trainer.class.detail', (string) $cs['id']) ?>?tab=progres" class="btn btn-outline-primary rounded-pill w-100 fw-semibold">
                                <i class="fas fa-search me-1"></i> Lihat Rincian Progres Kelas
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
