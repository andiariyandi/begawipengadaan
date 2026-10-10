<?php
/**
 * Trainer - Kelas Saya
 * FiStudy UI DNA
 */
$classes = $classes ?? [];
?>

<div class="trainer-classes-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-chalkboard-teacher me-1"></i> Penugasan Aktif
                </span>
                <h1 class="h3 fw-bold text-navy mb-1">Kelas Saya</h1>
                <p class="text-muted mb-0">Daftar kelas pembelajaran yang ditugaskan kepada Anda sebagai pengajar.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-navy border px-3 py-2 rounded-pill fs-6 fw-semibold">
                    Total: <?= count($classes) ?> Kelas
                </span>
            </div>
        </div>
    </div>

    <!-- Alert notifications if any -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Class Cards Grid -->
    <?php if (empty($classes)): ?>
        <div class="card border-0 shadow-sm rounded-4 bg-white p-5 text-center">
            <div class="py-5">
                <i class="fas fa-graduation-cap fa-4x text-muted mb-3 opacity-25"></i>
                <h2 class="h5 fw-bold text-navy mb-2">Belum Ada Kelas yang Ditugaskan</h2>
                <p class="text-muted mb-0">Anda saat ini belum memiliki penugasan aktif sebagai trainer pada kelas manapun.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($classes as $c): ?>
                <div class="col-12 col-lg-6">
                    <article class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden transition-all bg-light-hover">
                        <div class="card-body p-4 d-flex flex-column">
                            <!-- Top Info -->
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                <div>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 small fw-semibold me-2">
                                        <?= esc($c['code'] ?? 'KELAS') ?>
                                    </span>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 small fw-medium">
                                        <?= esc(ucfirst($c['class_status'] ?? 'active')) ?>
                                    </span>
                                </div>
                                <span class="badge bg-light text-muted border rounded-pill px-2 py-1 small">
                                    PG: <?= esc($c['passing_grade'] ?? '70.00') ?>
                                </span>
                            </div>

                            <!-- Title & Course -->
                            <h2 class="h5 fw-bold text-navy mb-2"><?= esc($c['name'] ?? '-') ?></h2>
                            <p class="text-muted small mb-2">
                                <i class="fas fa-book-open text-primary me-1"></i> <?= esc($c['course_title'] ?? '-') ?>
                            </p>
                            <?php if (! empty($c['program_title'])): ?>
                                <p class="text-muted small mb-3">
                                    <i class="fas fa-award text-warning me-1"></i> Program: <?= esc($c['program_title']) ?>
                                </p>
                            <?php endif; ?>

                            <!-- Period -->
                            <div class="bg-light rounded-3 p-2 px-3 small text-muted mb-3 d-flex align-items-center gap-2">
                                <i class="far fa-calendar-alt text-primary"></i>
                                <span>Periode: <?= esc(! empty($c['start_at']) ? date('d M Y', strtotime($c['start_at'])) : '-') ?> &ndash; <?= esc(! empty($c['end_at']) ? date('d M Y', strtotime($c['end_at'])) : '-') ?></span>
                            </div>

                            <!-- Quick Stats Row -->
                            <div class="row g-2 text-center mb-3">
                                <div class="col-3">
                                    <div class="border rounded-3 py-2 bg-white">
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">Peserta</small>
                                        <strong class="text-navy"><?= (int) ($c['participant_count'] ?? 0) ?></strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded-3 py-2 bg-white">
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">Modul</small>
                                        <strong class="text-navy"><?= (int) ($c['modules_count'] ?? 0) ?></strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded-3 py-2 bg-white">
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">Ujian</small>
                                        <strong class="text-navy"><?= (int) ($c['quizzes_count'] ?? 0) ?></strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded-3 py-2 bg-white">
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">Tugas</small>
                                        <strong class="text-navy"><?= (int) ($c['assignments_count'] ?? 0) ?></strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center small text-muted mb-1">
                                    <span>Rata-rata Progres Kelas</span>
                                    <span class="fw-semibold text-navy"><?= esc($c['avg_progress'] ?? 0.0) ?>%</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 6px;">
                                    <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: <?= esc($c['avg_progress'] ?? 0.0) ?>%"></div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="mt-auto d-flex gap-2">
                                <a href="<?= route_to('workspace.trainer.class.detail', $c['class_id']) ?>" class="btn btn-primary rounded-pill px-4 flex-grow-1 fw-semibold">
                                    <i class="fas fa-door-open me-1"></i> Masuk Kelas
                                </a>
                                <a href="<?= route_to('workspace.trainer.class.detail', $c['class_id']) ?>?tab=materi" class="btn btn-outline-secondary rounded-pill px-3" title="Kelola Materi">
                                    <i class="fas fa-book me-1"></i> Materi
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>