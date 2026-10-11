<?php
/**
 * Trainer Home - Learning Management Experience
 * FiStudy UI DNA - Elevated Unified Design
 */
$hasClasses         = $hasClasses ?? false;
$stats              = $stats ?? ['totalClasses' => 0, 'activeParticipants' => 0, 'avgProgress' => 0.0, 'pendingReviews' => 0, 'totalQuizzes' => 0];
$classes            = $assignedClasses ?? [];
$pendingSubmissions = $pendingSubmissionsList ?? [];
$activities         = $recentActivities ?? [];
$firstClass         = !empty($classes) ? $classes[0] : null;
$firstPending       = !empty($pendingSubmissions) ? $pendingSubmissions[0] : null;

$fmtCover = static function ($row): string {
    if (! empty($row['course_cover_url'])) {
        return esc($row['course_cover_url']);
    }
    if (! empty($row['cover_path'])) {
        return base_url(esc($row['cover_path']));
    }
    return base_url('assets/fistudy/images/resources/course-1-1.png');
};

$methodBadge = static function (?string $method): array {
    return match (strtolower((string) $method)) {
        'offline', 'tatap_muka' => [
            'label' => 'Tatap Muka',
            'class' => 'bg-success-subtle text-success border border-success-subtle',
            'icon'  => 'fas fa-chalkboard-teacher',
        ],
        'hybrid' => [
            'label' => 'Hybrid',
            'class' => 'bg-purple-subtle text-purple border border-purple-subtle',
            'icon'  => 'fas fa-shuffle',
        ],
        default => [
            'label' => 'Online LMS',
            'class' => 'bg-primary-subtle text-primary border border-primary-subtle',
            'icon'  => 'fas fa-laptop',
        ],
    };
};

$trainerRoleLabel = static function (?string $role): string {
    return match (strtolower((string) $role)) {
        'co_trainer' => 'Co-Trainer',
        'guest'      => 'Narasumber Tamu',
        default      => 'Pengajar Utama',
    };
};
?>

<div class="trainer-workspace-home">

    <!-- 1. HERO WELCOME & CONTROL SPOTLIGHT BANNER -->
    <div class="ws-hero-card shadow-sm p-4 p-lg-5 mb-4 text-white">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                        <i class="fas fa-chalkboard-teacher me-1"></i> Ruang Kerja Pengajar & Instruktur
                    </span>
                    <span class="badge bg-white-50 text-white px-3 py-2 rounded-pill small">
                        <i class="fas fa-shield-alt text-success me-1"></i> Instruktur Terverifikasi BegawiPBJ
                    </span>
                </div>
                <h1 class="h2 text-white fw-bold mb-2">
                    Halo, <?= esc($userName ?? 'Pengajar') ?>! 👋
                </h1>
                <p class="text-white-50 mb-0 fs-6" style="max-width: 620px;">
                    Pusat kendali instruktur kelas pengadaan barang dan jasa. Susun kurikulum modul & materi pembelajaran, kelola kuis evaluasi, berikan feedback submisi peserta, dan pantau ketuntasan belajar secara real-time.
                </p>
            </div>

            <!-- Spotlight Box (Glassmorphism DNA) -->
            <div class="col-lg-5">
                <?php if (!empty($firstPending)): ?>
                    <div class="ws-hero-glass-box">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-warning small fw-bold text-uppercase">
                                <i class="fas fa-inbox me-1"></i> Antrean Penilaian Mendesak
                            </span>
                            <span class="badge bg-danger text-white small px-2 py-1 rounded-pill">
                                <?= (int) ($stats['pendingReviews'] ?? 0) ?> Menunggu
                            </span>
                        </div>
                        <h3 class="h6 text-white fw-bold text-truncate mb-1">
                            <?= esc($firstPending['assignment_title'] ?? 'Submisi Tugas Siswa') ?>
                        </h3>
                        <p class="small text-white-50 text-truncate mb-3">
                            Peserta: <strong><?= esc($firstPending['student_name'] ?? 'Siswa') ?></strong> &middot; <?= esc($firstPending['class_name'] ?? '-') ?>
                        </p>
                        <a href="<?= route_to('workspace.trainer.submission.review', $firstPending['submission_id']) ?>" class="btn btn-warning text-dark fw-bold btn-sm w-100 rounded-pill py-2 shadow-sm">
                            <i class="fas fa-edit me-1"></i> Tinjau & Beri Nilai Sekarang
                        </a>
                    </div>
                <?php elseif (!empty($firstClass)): ?>
                    <div class="ws-hero-glass-box">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-1">
                                <span class="text-warning small fw-bold text-uppercase">
                                    <i class="fas fa-layer-group me-1"></i> Kelas Utama Anda
                                </span>
                                <?php if (! empty($firstClass['learning_method'])): ?>
                                    <?php $fmb = $methodBadge($firstClass['learning_method']); ?>
                                    <span class="badge bg-white-50 text-white rounded-pill px-2 py-0" style="font-size: 0.68rem;">
                                        <?= $fmb['label'] ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <span class="badge bg-success text-white small px-2 py-1 rounded-pill">
                                <?= esc($firstClass['avg_progress'] ?? 0.0) ?>% Progres
                            </span>
                        </div>
                        <h3 class="h6 text-white fw-bold text-truncate mb-1">
                            <?= esc($firstClass['name'] ?? 'Kelas Pembelajaran') ?>
                        </h3>
                        <p class="small text-white-50 text-truncate mb-2"><?= esc($firstClass['course_title'] ?? '') ?></p>
                        
                        <?php if (! empty($firstClass['location_or_link'])): ?>
                            <p class="small text-white-50 text-truncate mb-2" style="font-size: 0.75rem;">
                                <i class="fas fa-map-marker-alt text-warning me-1"></i> <?= esc($firstClass['location_or_link']) ?>
                            </p>
                        <?php endif; ?>

                        <div class="progress rounded-pill mb-3" style="height: 6px; background: rgba(255, 255, 255, 0.2);">
                            <div class="progress-bar bg-warning rounded-pill" role="progressbar" style="width: <?= esc($firstClass['avg_progress'] ?? 0.0) ?>%"></div>
                        </div>

                        <a href="<?= route_to('workspace.trainer.class.detail', $firstClass['class_id']) ?>" class="btn btn-warning text-dark fw-bold btn-sm w-100 rounded-pill py-2 shadow-sm">
                            <i class="fas fa-arrow-right me-1"></i> Buka Ruang Kendali Kelas
                        </a>
                    </div>
                <?php else: ?>
                    <div class="ws-hero-glass-box text-center">
                        <i class="fas fa-chalkboard-teacher fa-2x text-warning mb-2"></i>
                        <h3 class="h6 text-white fw-bold mb-1">Siap Memulai Pengajaran?</h3>
                        <p class="small text-white-50 mb-3">Lihat katalog kelas dan koordinasikan dengan operator sistem untuk penugasan kelas.</p>
                        <a href="<?= route_to('workspace.trainer.classes') ?>" class="btn btn-warning text-dark fw-bold btn-sm rounded-pill px-4 py-2">
                            <i class="fas fa-search me-1"></i> Lihat Daftar Kelas
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 2. 4 SUMMARY KPI STAT CARDS (FiStudy DNA) -->
    <div class="row g-3 mb-4">
        <!-- 1. Kelas Diampu -->
        <div class="col-6 col-lg-3">
            <div class="card ws-stat-card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="ws-stat-icon" style="background: #eff6ff; color: #082f5c;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.78rem;">Kelas Diampu</span>
                        <div class="h4 fw-bold text-navy mb-0"><?= number_format((int) ($stats['totalClasses'] ?? 0)) ?></div>
                        <small class="text-muted d-block" style="font-size: 0.72rem;">penugasan aktif</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Peserta Aktif -->
        <div class="col-6 col-lg-3">
            <div class="card ws-stat-card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="ws-stat-icon" style="background: #e0f2fe; color: #0284c7;">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.78rem;">Peserta Aktif</span>
                        <div class="h4 fw-bold text-navy mb-0"><?= number_format((int) ($stats['activeParticipants'] ?? 0)) ?></div>
                        <small class="text-muted d-block" style="font-size: 0.72rem;">siswa dalam kelas</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Rata-rata Progres -->
        <div class="col-6 col-lg-3">
            <div class="card ws-stat-card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="ws-stat-icon" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.78rem;">Rata-rata Ketuntasan</span>
                        <div class="h4 fw-bold text-navy mb-0"><?= esc($stats['avgProgress'] ?? 0.0) ?>%</div>
                        <small class="text-success d-block" style="font-size: 0.72rem;">progres seluruh kelas</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Antrean Review -->
        <div class="col-6 col-lg-3">
            <div class="card ws-stat-card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="ws-stat-icon" style="background: #fefce8; color: #ca8a04;">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.78rem;">Tugas Perlu Review</span>
                        <div class="h4 fw-bold text-navy mb-0"><?= (int) ($stats['pendingReviews'] ?? 0) ?></div>
                        <small class="text-warning-emphasis d-block" style="font-size: 0.72rem;"><?= (int) ($stats['totalQuizzes'] ?? 0) ?> assessment aktif</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. PUSAT AKSI CEPAT PENGAJAR (Elevated Action Cards) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
            <div>
                <h2 class="h5 fw-bold text-navy mb-0">
                    <i class="fas fa-bolt text-warning me-2"></i>Pusat Aksi Pengajar
                </h2>
                <small class="text-muted">Akses kilat pengelolaan konten materi, ujian, evaluasi, dan peserta</small>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-6 col-lg-3">
                <a href="<?= route_to('workspace.trainer.all-materials') ?>" class="card ws-action-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-primary text-white" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            <i class="fas fa-book-reader"></i>
                        </div>
                        <div>
                            <strong class="text-navy d-block small">Kelola Materi</strong>
                            <small class="text-muted" style="font-size: 0.72rem;">Modul, PDF & video</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-lg-3">
                <a href="<?= route_to('workspace.trainer.all-quizzes') ?>" class="card ws-action-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-success text-white" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div>
                            <strong class="text-navy d-block small">Assessment & Kuis</strong>
                            <small class="text-muted" style="font-size: 0.72rem;">Pre-test, kuis, post-test</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-lg-3">
                <a href="<?= route_to('workspace.trainer.all-progress') ?>" class="card ws-action-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-warning text-dark" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            <i class="fas fa-stamp"></i>
                        </div>
                        <div>
                            <strong class="text-navy d-block small">Review Submission</strong>
                            <small class="text-muted" style="font-size: 0.72rem;">Evaluasi & beri feedback</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-lg-3">
                <a href="<?= route_to('workspace.trainer.all-participants') ?>" class="card ws-action-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-info text-white" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div>
                            <strong class="text-navy d-block small">Peserta & Progres</strong>
                            <small class="text-muted" style="font-size: 0.72rem;">Pantau kemajuan siswa</small>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- 4. ANTREAN TUGAS SISWA MENUNGGU EVALUASI (Jika ada) -->
    <?php if (!empty($pendingSubmissions)): ?>
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                <div>
                    <h2 class="h5 fw-bold text-navy mb-0">
                        <i class="fas fa-inbox text-warning me-2"></i>Antrean Tugas Menunggu Evaluasi
                    </h2>
                    <small class="text-muted">Submisi tugas peserta yang siap dinilai dan diberi catatan masukan</small>
                </div>
                <a href="<?= route_to('workspace.trainer.all-progress') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    Lihat Semua Submisi (<?= count($pendingSubmissions) ?>)
                </a>
            </div>
            <div class="table-responsive ws-paginated-container" data-page-size="5">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-uppercase text-muted">Peserta</th>
                            <th class="small text-uppercase text-muted">Tugas & Kelas</th>
                            <th class="small text-uppercase text-muted">Waktu Kumpul</th>
                            <th class="small text-uppercase text-muted text-end">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingSubmissions as $sub): ?>
                            <tr class="ws-paginated-item">
                                <td>
                                    <div class="fw-semibold text-navy"><?= esc($sub['student_name'] ?? 'Peserta') ?></div>
                                    <span class="badge bg-warning-subtle text-warning-emphasis small rounded-pill">Menunggu Review</span>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark"><?= esc($sub['assignment_title'] ?? 'Tugas') ?></div>
                                    <small class="text-muted"><?= esc($sub['class_name'] ?? '-') ?></small>
                                </td>
                                <td class="text-muted small">
                                    <i class="far fa-clock me-1"></i>
                                    <?= esc(!empty($sub['submitted_at']) ? date('d/m/Y H:i', strtotime($sub['submitted_at'])) : '-') ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?= route_to('workspace.trainer.submission.review', $sub['submission_id']) ?>" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold">
                                        <i class="fas fa-edit me-1"></i> Review & Nilai
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- 5. 2 KOLOM: KELAS AKTIF SAYA vs AKTIVITAS TERKINI -->
    <div class="row g-4 mb-4">
        <!-- Kolom Kiri: Kelas Aktif Saya -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                    <div>
                        <h2 class="h5 fw-bold text-navy mb-0">
                            <i class="fas fa-layer-group text-primary me-2"></i>Kelas Aktif Saya
                        </h2>
                        <small class="text-muted">Kelas yang ditugaskan kepada Anda sebagai pengajar</small>
                    </div>
                    <a href="<?= route_to('workspace.trainer.classes') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                        Semua Kelas (<?= count($classes) ?>)
                    </a>
                </div>

                <?php if (empty($classes)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-chalkboard fa-3x mb-3 text-secondary opacity-50"></i>
                        <h3 class="h6 text-muted mb-0">Belum ada kelas yang ditugaskan kepada Anda saat ini.</h3>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3 ws-paginated-container" data-page-size="4">
                        <?php foreach ($classes as $c): ?>
                            <div class="ws-class-card p-3 ws-paginated-item rounded-4 border bg-white shadow-xs">
                                <div class="row g-3 align-items-center">
                                    <div class="col-12 col-sm-4 col-md-3">
                                        <div class="rounded-3 overflow-hidden border bg-light position-relative" style="aspect-ratio: 16/10;">
                                            <img src="<?= $fmtCover($c) ?>" alt="<?= esc($c['name'] ?? 'Cover') ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/fistudy/images/resources/course-1-1.png') ?>';">
                                            <?php $mb = $methodBadge($c['learning_method'] ?? null); ?>
                                            <span class="badge position-absolute top-0 start-0 m-1 rounded-pill small px-2 py-0 <?= $mb['class'] ?>" style="font-size: 0.65rem;">
                                                <i class="<?= $mb['icon'] ?> me-1"></i><?= $mb['label'] ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-8 col-md-9">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                            <div class="d-flex flex-wrap align-items-center gap-1">
                                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 small fw-bold">
                                                    <?= esc($c['code'] ?? 'KELAS') ?>
                                                </span>
                                                <span class="badge <?= ($c['class_status'] ?? '') === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?> rounded-pill px-2 py-1 small">
                                                    <?= esc(ucfirst($c['class_status'] ?? 'draft')) ?>
                                                </span>
                                                <?php if (! empty($c['trainer_role'])): ?>
                                                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2 py-1 small">
                                                        <i class="fas fa-id-badge me-1"></i><?= $trainerRoleLabel($c['trainer_role']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <small class="text-muted text-nowrap">
                                                <i class="fas fa-users me-1 text-primary"></i> <?= (int) ($c['participant_count'] ?? 0) ?> Peserta
                                            </small>
                                        </div>
                                        <h3 class="h6 fw-bold text-navy mb-1"><?= esc($c['name'] ?? '-') ?></h3>
                                        <p class="small text-muted mb-2 text-truncate"><?= esc($c['course_title'] ?? '-') ?></p>

                                        <?php if (! empty($c['location_or_link'])): ?>
                                            <div class="small text-muted mb-2 text-truncate" style="font-size: 0.72rem;">
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i> <?= esc($c['location_or_link']) ?>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Progress Bar Ketuntasan -->
                                        <div class="d-flex justify-content-between align-items-center small text-muted mb-1" style="font-size: 0.72rem;">
                                            <span>Rata-rata Progres Kelas</span>
                                            <span class="fw-semibold text-navy"><?= esc($c['avg_progress'] ?? 0.0) ?>%</span>
                                        </div>
                                        <div class="progress rounded-pill mb-2" style="height: 5px; background: #eef2f7;">
                                            <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: <?= esc($c['avg_progress'] ?? 0.0) ?>%"></div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-1">
                                            <small class="text-muted" style="font-size: 0.72rem;">
                                                <span><i class="fas fa-layer-group me-1 text-primary"></i> <?= (int) ($c['module_count'] ?? 0) ?> Modul</span>
                                                &nbsp;&middot;&nbsp;
                                                <span><i class="fas fa-tasks me-1 text-warning"></i> <?= (int) ($c['assignment_count'] ?? 0) ?> Tugas</span>
                                                &nbsp;&middot;&nbsp;
                                                <span><i class="fas fa-check-double me-1 text-success"></i> <?= (int) ($c['quiz_count'] ?? 0) ?> Kuis</span>
                                            </small>
                                            <a href="<?= route_to('workspace.trainer.class.detail', $c['class_id']) ?>" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
                                                Ruang Kendali Kelas <i class="fas fa-arrow-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kolom Kanan: Aktivitas Pembelajaran Terkini -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                    <div>
                        <h2 class="h5 fw-bold text-navy mb-0">
                            <i class="fas fa-history text-secondary me-2"></i>Aktivitas Pembelajaran Terkini
                        </h2>
                        <small class="text-muted">Pembaruan aktivitas real-time dari siswa kelas Anda</small>
                    </div>
                </div>

                <?php if (empty($activities)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-history fa-3x mb-3 text-secondary opacity-50"></i>
                        <p class="text-muted small mb-0">Belum ada aktivitas tercatat baru-baru ini.</p>
                    </div>
                <?php else: ?>
                    <div class="timeline position-relative ps-3 ws-paginated-container" style="border-left: 2px solid #e9ecef;" data-page-size="5">
                        <?php foreach ($activities as $act): ?>
                            <div class="timeline-item mb-4 position-relative ws-paginated-item">
                                <div class="position-absolute rounded-circle bg-white border border-2 border-primary d-flex align-items-center justify-content-center" style="width: 14px; height: 14px; left: -24px; top: 3px;"></div>
                                <div>
                                    <?php if ($act['type'] === 'enrollment'): ?>
                                        <p class="mb-1 small">
                                            <strong class="text-navy"><?= esc($act['user_name'] ?? 'Peserta') ?></strong> bergabung ke kelas <span class="text-primary fw-medium"><?= esc($act['class_name'] ?? '-') ?></span>.
                                        </p>
                                    <?php elseif ($act['type'] === 'lesson_completed'): ?>
                                        <p class="mb-1 small">
                                            <strong class="text-navy"><?= esc($act['user_name'] ?? 'Peserta') ?></strong> menyelesaikan materi <em><?= esc($act['lesson_title'] ?? 'Materi') ?></em>.
                                        </p>
                                    <?php elseif ($act['type'] === 'submission'): ?>
                                        <p class="mb-1 small">
                                            <strong class="text-navy"><?= esc($act['user_name'] ?? 'Peserta') ?></strong> mengumpulkan tugas <em><?= esc($act['assignment_title'] ?? 'Tugas') ?></em>.
                                        </p>
                                    <?php elseif ($act['type'] === 'quiz_completed'): ?>
                                        <p class="mb-1 small">
                                            <strong class="text-navy"><?= esc($act['user_name'] ?? 'Peserta') ?></strong> menyelesaikan pengerjaan ujian/kuis <span class="text-success fw-medium"><?= esc($act['quiz_title'] ?? 'Kuis') ?></span>.
                                        </p>
                                    <?php endif; ?>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                                        <i class="far fa-clock me-1 text-muted"></i> <?= esc(! empty($act['time']) ? date('d M Y, H:i', strtotime($act['time'])) : '-') ?> WIB
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