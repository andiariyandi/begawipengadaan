<?php
/**
 * Trainer Home - Learning Management Experience
 * FiStudy UI DNA
 */
$hasClasses = $hasClasses ?? false;
$stats      = $stats ?? ['totalClasses' => 0, 'activeParticipants' => 0, 'avgProgress' => 0.0, 'pendingReviews' => 0];
$classes    = $assignedClasses ?? [];
$activities = $recentActivities ?? [];
?>

<div class="trainer-home-wrapper">
    <!-- Hero Banner -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0d3b66 0%, #001f3f 100%);">
        <div class="card-body p-4 p-lg-5 text-white position-relative">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold mb-2">
                        <i class="fas fa-chalkboard-teacher me-1"></i> Ruang Kerja Pengajar
                    </span>
                    <h1 class="h2 text-white fw-bold mb-2">Selamat Datang, <?= esc($userName ?? 'Pengajar') ?>!</h1>
                    <p class="text-white-50 mb-0 fs-6">
                        Kelola silabus modul, pantau aktivitas peserta, dan lakukan evaluasi tugas pada kelas yang Anda ampu.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= route_to('workspace.trainer.classes') ?>" class="btn btn-light fw-semibold px-4 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-graduation-cap text-primary me-2"></i> Kelas Saya
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary-subtle text-primary fs-4 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Kelas Diampu</span>
                        <span class="h4 fw-bold text-navy mb-0"><?= (int) ($stats['totalClasses'] ?? 0) ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-info-subtle text-info fs-4 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Peserta Aktif</span>
                        <span class="h4 fw-bold text-navy mb-0"><?= (int) ($stats['activeParticipants'] ?? 0) ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success-subtle text-success fs-4 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Rata-rata Progres</span>
                        <span class="h4 fw-bold text-navy mb-0"><?= esc($stats['avgProgress'] ?? 0.0) ?>%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-warning-subtle text-warning fs-4 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Tugas Perlu Review</span>
                        <span class="h4 fw-bold text-navy mb-0"><?= (int) ($stats['pendingReviews'] ?? 0) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Banner -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <h2 class="h5 fw-bold text-navy mb-3"><i class="fas fa-bolt text-warning me-2"></i>Aksi Cepat</h2>
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <a href="<?= route_to('workspace.trainer.all-materials') ?>" class="card text-decoration-none border rounded-3 p-3 h-100 bg-light-hover">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-primary text-white" style="width: 44px; height: 44px;">
                            <i class="fas fa-book"></i>
                        </div>
                        <div>
                            <strong class="text-navy d-block">Kelola Materi</strong>
                            <small class="text-muted">Susun modul, lesson, dokumen & video</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-4">
                <a href="<?= route_to('workspace.trainer.all-participants') ?>" class="card text-decoration-none border rounded-3 p-3 h-100 bg-light-hover">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-info text-white" style="width: 44px; height: 44px;">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <strong class="text-navy d-block">Lihat Peserta</strong>
                            <small class="text-muted">Cek status dan progress belajar peserta</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-4">
                <a href="<?= route_to('workspace.trainer.all-progress') ?>" class="card text-decoration-none border rounded-3 p-3 h-100 bg-light-hover">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-warning text-dark" style="width: 44px; height: 44px;">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div>
                            <strong class="text-navy d-block">Review Submission</strong>
                            <small class="text-muted">Evaluasi tugas & berikan feedback</small>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- 2 Column Layout: Classes List vs Activity Timeline -->
    <div class="row g-4">
        <!-- Kolom Kiri: Kelas Aktif Saya -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="h5 fw-bold text-navy mb-0">Kelas Aktif Saya</h2>
                        <small class="text-muted">Kelas yang ditugaskan pada Anda</small>
                    </div>
                    <a href="<?= route_to('workspace.trainer.classes') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body px-4 py-3">
                    <?php if (empty($classes)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 opacity-50"></i>
                            <h3 class="h6 text-muted mb-0">Belum ada kelas yang ditugaskan.</h3>
                        </div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($classes as $c): ?>
                                <div class="border rounded-4 p-3 bg-light-hover transition-all">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                        <div>
                                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 small fw-semibold me-2">
                                                <?= esc($c['code'] ?? 'KELAS') ?>
                                            </span>
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 small">
                                                <?= esc(ucfirst($c['class_status'] ?? 'draft')) ?>
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-users me-1 text-primary"></i> <?= (int) ($c['participant_count'] ?? 0) ?> Peserta
                                        </small>
                                    </div>
                                    <h3 class="h6 fw-bold text-navy mb-1"><?= esc($c['name'] ?? '-') ?></h3>
                                    <p class="small text-muted mb-3"><?= esc($c['course_title'] ?? '-') ?></p>

                                    <!-- Progress Bar -->
                                    <div class="d-flex justify-content-between align-items-center small text-muted mb-1">
                                        <span>Progres Belajar Kelas</span>
                                        <span class="fw-semibold text-navy"><?= esc($c['avg_progress'] ?? 0.0) ?>%</span>
                                    </div>
                                    <div class="progress rounded-pill mb-3" style="height: 6px;">
                                        <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: <?= esc($c['avg_progress'] ?? 0.0) ?>%"></div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-layer-group me-1"></i> <?= (int) ($c['module_count'] ?? 0) ?> Modul
                                            &nbsp;&middot;&nbsp;
                                            <i class="fas fa-tasks me-1"></i> <?= (int) ($c['assignment_count'] ?? 0) ?> Tugas
                                        </small>
                                        <a href="<?= route_to('workspace.trainer.class.detail', $c['class_id']) ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                                            Masuk Kelas <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Aktivitas Pembelajaran Terkini -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                    <h2 class="h5 fw-bold text-navy mb-0">Aktivitas Terkini</h2>
                    <small class="text-muted">Pembaruan pembelajaran dari peserta kelas Anda</small>
                </div>
                <div class="card-body px-4 py-3">
                    <?php if (empty($activities)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-history fa-3x text-muted mb-3 opacity-50"></i>
                            <p class="text-muted small mb-0">Belum ada aktivitas tercatat baru-baru ini.</p>
                        </div>
                    <?php else: ?>
                        <div class="timeline position-relative ps-3" style="border-left: 2px solid #e9ecef;">
                            <?php foreach ($activities as $act): ?>
                                <div class="timeline-item mb-4 position-relative">
                                    <div class="position-absolute rounded-circle bg-white border border-2 border-primary d-flex align-items-center justify-content-center" style="width: 14px; height: 14px; left: -24px; top: 3px;"></div>
                                    <div>
                                        <?php if ($act['type'] === 'enrollment'): ?>
                                            <p class="mb-1 small">
                                                <strong class="text-navy"><?= esc($act['user_name'] ?? 'Peserta') ?></strong> bergabung ke kelas <span class="text-primary"><?= esc($act['class_name'] ?? '-') ?></span>.
                                            </p>
                                        <?php elseif ($act['type'] === 'lesson_completed'): ?>
                                            <p class="mb-1 small">
                                                <strong class="text-navy"><?= esc($act['user_name'] ?? 'Peserta') ?></strong> menyelesaikan materi <em><?= esc($act['lesson_title'] ?? 'Materi') ?></em>.
                                            </p>
                                        <?php elseif ($act['type'] === 'submission'): ?>
                                            <p class="mb-1 small">
                                                <strong class="text-navy"><?= esc($act['user_name'] ?? 'Peserta') ?></strong> mengumpulkan tugas <em><?= esc($act['assignment_title'] ?? 'Tugas') ?></em>.
                                            </p>
                                        <?php endif; ?>
                                        <small class="text-muted d-block" style="font-size: 0.78rem;">
                                            <i class="far fa-clock me-1"></i> <?= esc(! empty($act['time']) ? date('d M Y, H:i', strtotime($act['time'])) : '-') ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>