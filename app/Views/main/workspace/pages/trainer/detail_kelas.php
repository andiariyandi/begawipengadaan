<?php
/**
 * Trainer Learning Management Room
 * Detail Kelas & Tab Navigation FiStudy UI DNA - Elevated Unified Design
 */
$c           = $classCtx ?? [];
$classId     = (int) ($c['id'] ?? 0);
$trainers    = $trainers ?? [];
$activeTab   = $activeTab ?? 'overview';
$modules     = $modules ?? [];
$participants= $participants ?? [];
$quizzes     = $quizzes ?? [];
$assignments = $assignments ?? [];
$stats       = $stats ?? [];

$fmtCover = static function ($row): string {
    $candidates = [
        $row['class_cover_url'] ?? null,
        $row['cover_url'] ?? null,
        $row['course_cover_url'] ?? null,
        $row['cover_path'] ?? null,
    ];
    foreach ($candidates as $cand) {
        if (! empty($cand) && is_string($cand)) {
            $cand = trim($cand);
            if (str_starts_with($cand, 'http://') || str_starts_with($cand, 'https://')) {
                return $cand;
            }
            return base_url($cand);
        }
    }
    return base_url('assets/main/images/resources/course-details-img-1.jpg');
};

$methodBadge = static function (?string $method): array {
    return match (strtolower((string) $method)) {
        'offline', 'tatap_muka' => [
            'label' => 'Tatap Muka',
            'class' => 'bg-success text-white',
            'icon'  => 'fas fa-chalkboard-teacher',
        ],
        'hybrid' => [
            'label' => 'Hybrid',
            'class' => 'bg-purple text-white',
            'icon'  => 'fas fa-shuffle',
        ],
        default => [
            'label' => 'Online LMS',
            'class' => 'bg-primary text-white',
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

$levelLabel = static function (?string $lvl): string {
    return match (strtolower((string) $lvl)) {
        'dasar', 'beginner', '1' => 'Tingkat Dasar',
        'menengah', 'intermediate', '2' => 'Tingkat Menengah',
        'lanjutan', 'advanced', '3' => 'Tingkat Lanjutan',
        default => 'Semua Tingkat',
    };
};
?>

<div class="trainer-room-wrapper">
    <!-- Header Hero Room -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-4">
                <div class="flex-grow-1">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 small fw-semibold">
                            <?= esc($c['code'] ?? 'KELAS') ?>
                        </span>
                        <?php
                        $rawClassStatus = strtolower((string) ($c['status'] ?? 'active'));
                        $statusBadge = match ($rawClassStatus) {
                            'active', 'ongoing', 'berlangsung', 'aktif' => ['bg' => 'bg-success-subtle text-success border border-success-subtle', 'label' => 'Aktif / Berjalan (Tayang)'],
                            'scheduled', 'open', 'published', 'terbuka' => ['bg' => 'bg-primary-subtle text-primary border border-primary-subtle', 'label' => 'Jadwal Terbuka (Tayang)'],
                            'completed', 'closed', 'selesai'            => ['bg' => 'bg-secondary-subtle text-secondary border border-secondary-subtle', 'label' => 'Selesai'],
                            default                                     => ['bg' => 'bg-warning-subtle text-warning border border-warning-subtle', 'label' => 'Draft (Tidak Tayang)'],
                        };
                        ?>
                        <span class="badge rounded-pill px-3 py-1 small fw-semibold <?= $statusBadge['bg'] ?>">
                            <?= esc($statusBadge['label']) ?>
                        </span>
                        <?php $mb = $methodBadge($c['learning_method'] ?? null); ?>
                        <span class="badge rounded-pill px-3 py-1 small fw-semibold <?= $mb['class'] ?>">
                            <i class="<?= $mb['icon'] ?> me-1"></i><?= $mb['label'] ?>
                        </span>
                        <?php if (! empty($c['course_level'])): ?>
                            <span class="badge bg-info-subtle text-info rounded-pill px-3 py-1 small">
                                <?= $levelLabel($c['course_level']) ?>
                            </span>
                        <?php endif; ?>
                        <span class="badge bg-light text-navy border rounded-pill px-3 py-1 small">
                            Passing Grade: <?= esc($c['passing_grade'] ?? '70.00') ?>
                        </span>
                    </div>

                    <h1 class="h2 fw-bold text-navy mb-2"><?= esc($c['name'] ?? 'Ruang Belajar Pengajar') ?></h1>
                    
                    <p class="text-muted mb-2">
                        <i class="fas fa-book-open text-primary me-2"></i><strong><?= esc($c['course_title'] ?? '-') ?></strong>
                        <?php if (! empty($c['program_title'])): ?>
                            &nbsp;&middot;&nbsp;<span class="text-muted">Program: <?= esc($c['program_title']) ?></span>
                        <?php endif; ?>
                    </p>

                    <div class="d-flex flex-wrap align-items-center gap-3 text-muted small mb-0">
                        <span>
                            <i class="far fa-calendar-alt text-primary me-1"></i>
                            Periode: <?= esc(! empty($c['start_at']) ? date('d M Y', strtotime($c['start_at'])) : '-') ?> &ndash; <?= esc(! empty($c['end_at']) ? date('d M Y', strtotime($c['end_at'])) : '-') ?>
                        </span>
                        <?php if (! empty($c['location_or_link'])): ?>
                            <span>
                                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                <?= esc($c['location_or_link']) ?>
                            </span>
                        <?php endif; ?>
                        <?php if (! empty($c['duration_minutes'])): ?>
                            <span>
                                <i class="far fa-clock text-primary me-1"></i>
                                Estimasi: <?= format_learning_jp($c['duration_minutes']) ?>
                            </span>
                        <?php endif; ?>
                        <span>
                            <i class="fas fa-chalkboard-teacher text-warning me-1"></i>
                            Pengajar: 
                            <?php if (! empty($trainers)): ?>
                                <?= esc(implode(', ', array_column($trainers, 'name'))) ?>
                            <?php else: ?>
                                <?= esc($userName ?? 'Anda') ?>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="d-flex flex-row flex-lg-column gap-2 text-nowrap">
                    <a href="<?= route_to('workspace.trainer.class.edit', (string) $classId) ?>" class="btn btn-warning rounded-pill px-3 fw-semibold text-dark">
                        <i class="fas fa-edit me-1"></i> Ubah Detail Kelas
                    </a>
                    <a href="<?= route_to('workspace.trainer.classes') ?>" class="btn btn-outline-secondary rounded-pill px-3">
                        <i class="fas fa-arrow-left me-1"></i> Semua Kelas
                    </a>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs (FiStudy Pill Tab Design) -->
        <div class="px-4 border-top bg-light-subtle">
            <ul class="nav nav-pills flex-nowrap overflow-auto py-3 gap-2" role="tablist" style="white-space: nowrap;">
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-4 fw-semibold <?= $activeTab === 'overview' ? 'active bg-primary text-white shadow-sm' : 'text-navy bg-white border' ?>" href="?tab=overview">
                        <i class="fas fa-info-circle me-1"></i> Overview
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-4 fw-semibold <?= $activeTab === 'materi' ? 'active bg-primary text-white shadow-sm' : 'text-navy bg-white border' ?>" href="?tab=materi">
                        <i class="fas fa-layer-group me-1"></i> Materi & Modul
                        <span class="badge <?= $activeTab === 'materi' ? 'bg-white text-primary' : 'bg-primary-subtle text-primary' ?> rounded-pill ms-1"><?= (int) ($stats['moduleCount'] ?? 0) ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-4 fw-semibold <?= $activeTab === 'peserta' ? 'active bg-primary text-white shadow-sm' : 'text-navy bg-white border' ?>" href="?tab=peserta">
                        <i class="fas fa-users me-1"></i> Peserta
                        <span class="badge <?= $activeTab === 'peserta' ? 'bg-white text-primary' : 'bg-primary-subtle text-primary' ?> rounded-pill ms-1"><?= (int) ($stats['participantCount'] ?? 0) ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-4 fw-semibold <?= $activeTab === 'assessment' ? 'active bg-primary text-white shadow-sm' : 'text-navy bg-white border' ?>" href="?tab=assessment">
                        <i class="fas fa-clipboard-check me-1"></i> Assessment
                        <span class="badge <?= $activeTab === 'assessment' ? 'bg-white text-primary' : 'bg-primary-subtle text-primary' ?> rounded-pill ms-1"><?= (int) ($stats['quizCount'] ?? 0) ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-4 fw-semibold <?= $activeTab === 'assignment' ? 'active bg-primary text-white shadow-sm' : 'text-navy bg-white border' ?>" href="?tab=assignment">
                        <i class="fas fa-tasks me-1"></i> Assignment
                        <span class="badge <?= $activeTab === 'assignment' ? 'bg-white text-primary' : 'bg-primary-subtle text-primary' ?> rounded-pill ms-1"><?= (int) ($stats['assignmentCount'] ?? 0) ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-4 fw-semibold <?= $activeTab === 'progres' ? 'active bg-primary text-white shadow-sm' : 'text-navy bg-white border' ?>" href="?tab=progres">
                        <i class="fas fa-chart-line me-1"></i> Progress
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-4 fw-semibold <?= $activeTab === 'gradebook' ? 'active bg-primary text-white shadow-sm' : 'text-navy bg-white border' ?>" href="?tab=gradebook">
                        <i class="fas fa-graduation-cap me-1"></i> Buku Nilai (Gradebook)
                        <span class="badge <?= $activeTab === 'gradebook' ? 'bg-white text-primary' : 'bg-primary-subtle text-primary' ?> rounded-pill ms-1"><?= count($gradebook['rows'] ?? []) ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Flash Messages -->
    <!-- TAB 1: OVERVIEW -->
    <?php if ($activeTab === 'overview'): ?>
        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <!-- Cover Banner & Informasi Kelas -->
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
                    <div class="position-relative" style="height: 200px;">
                        <img src="<?= $fmtCover($c) ?>" alt="<?= esc($c['name'] ?? 'Cover') ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/resources/course-details-img-1.jpg') ?>';">
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(8, 47, 92, 0.3) 0%, rgba(8, 47, 92, 0.85) 100%);"></div>
                        <div class="position-absolute bottom-0 start-0 p-4 text-white">
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 small fw-bold mb-2">
                                <?= esc($c['code'] ?? 'KELAS') ?> &middot; <?= $mb['label'] ?>
                            </span>
                            <h2 class="h4 fw-bold text-white mb-1"><?= esc($c['name'] ?? '-') ?></h2>
                            <p class="small text-white-50 mb-0"><?= esc($c['course_title'] ?? '-') ?></p>
                        </div>
                    </div>
                    <?php
                    $parsedTrainerClassDesc = parse_class_batch_data($c['description'] ?? '');
                    if (! empty($parsedTrainerClassDesc['description'])):
                    ?>
                        <div class="p-4 border-bottom bg-light-subtle">
                            <h3 class="h6 fw-bold text-navy mb-2"><i class="fas fa-info-circle text-primary me-2"></i>Deskripsi Kelas</h3>
                            <div class="text-muted small lh-lg">
                                <?= nl2br(esc($parsedTrainerClassDesc['description'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Bobot Penilaian & Passing Grade -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                    <h2 class="h5 fw-bold text-navy mb-3"><i class="fas fa-sliders-h text-primary me-2"></i>Bobot Penilaian & Passing Grade</h2>
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="border rounded-3 p-3 text-center bg-light">
                                <small class="text-muted d-block mb-1">Passing Grade</small>
                                <strong class="h4 text-success mb-0"><?= esc($c['passing_grade'] ?? '70.00') ?></strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded-3 p-3 text-center bg-light">
                                <small class="text-muted d-block mb-1">Bobot Quiz</small>
                                <strong class="h4 text-navy mb-0"><?= esc($c['quiz_weight'] ?? '40.00') ?>%</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded-3 p-3 text-center bg-light">
                                <small class="text-muted d-block mb-1">Bobot Tugas</small>
                                <strong class="h4 text-navy mb-0"><?= esc($c['assignment_weight'] ?? '0.00') ?>%</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded-3 p-3 text-center bg-light">
                                <small class="text-muted d-block mb-1">Bobot Post-Test</small>
                                <strong class="h4 text-navy mb-0"><?= esc($c['post_test_weight'] ?? '60.00') ?>%</strong>
                            </div>
                        </div>
                    </div>

                    <?php if (! empty($c['course_desc'])): ?>
                        <h3 class="h6 fw-bold text-navy mb-2">Tentang Materi Pelatihan</h3>
                        <div class="text-muted small lh-lg">
                            <?= nl2br(esc($c['course_desc'])) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Kurikulum & Target Peserta Detail (Jika ada) -->
                <?php if (! empty($c['course_learning_objectives']) || ! empty($c['course_prerequisites']) || ! empty($c['target_audience']) || ! empty($c['course_target_audience'])): ?>
                    <div class="row g-3 mb-4">
                        <?php if (! empty($c['course_learning_objectives'])): ?>
                            <div class="col-12">
                                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                                    <h3 class="h6 fw-bold text-navy mb-2">
                                        <i class="fas fa-bullseye text-success me-2"></i>Tujuan Pembelajaran
                                    </h3>
                                    <div class="text-muted small lh-lg">
                                        <?= nl2br(esc($c['course_learning_objectives'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (! empty($c['course_prerequisites'])): ?>
                            <div class="col-12 col-md-6">
                                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                                    <h3 class="h6 fw-bold text-navy mb-2">
                                        <i class="fas fa-clipboard-list text-warning me-2"></i>Persyaratan & Prasyarat
                                    </h3>
                                    <div class="text-muted small lh-base">
                                        <?= nl2br(esc($c['course_prerequisites'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php $aud = $c['target_audience'] ?: ($c['course_target_audience'] ?? ''); ?>
                        <?php if (! empty($aud)): ?>
                            <div class="col-12 <?= ! empty($c['course_prerequisites']) ? 'col-md-6' : '' ?>">
                                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                                    <h3 class="h6 fw-bold text-navy mb-2">
                                        <i class="fas fa-users text-primary me-2"></i>Sasaran Peserta
                                    </h3>
                                    <div class="text-muted small lh-base">
                                        <?= nl2br(esc($aud)) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Aksi Cepat Pengajar -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <h2 class="h5 fw-bold text-navy mb-3"><i class="fas fa-tools text-primary me-2"></i>Alat Pengelolaan Kelas</h2>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <a href="<?= route_to('workspace.trainer.module.create', (string) $classId) ?>" class="card text-decoration-none border rounded-3 p-3 h-100 bg-light-hover">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-circle bg-primary text-white" style="width: 44px; height: 44px;">
                                        <i class="fas fa-folder-plus"></i>
                                    </div>
                                    <div>
                                        <strong class="text-navy d-block">Tambah Modul Baru</strong>
                                        <small class="text-muted">Buat bab materi pembelajaran baru</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-12 col-md-6">
                            <a href="<?= route_to('workspace.trainer.quiz.create', (string) $classId) ?>" class="card text-decoration-none border rounded-3 p-3 h-100 bg-light-hover">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-circle bg-warning text-dark" style="width: 44px; height: 44px;">
                                        <i class="fas fa-question-circle"></i>
                                    </div>
                                    <div>
                                        <strong class="text-navy d-block">Tambah Ujian / Kuis</strong>
                                        <small class="text-muted">Buat Pre-Test, Quiz Modul, atau Post-Test</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-12 col-md-6">
                            <a href="<?= route_to('workspace.trainer.assignment.create', (string) $classId) ?>" class="card text-decoration-none border rounded-3 p-3 h-100 bg-light-hover">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-circle bg-info text-white" style="width: 44px; height: 44px;">
                                        <i class="fas fa-tasks"></i>
                                    </div>
                                    <div>
                                        <strong class="text-navy d-block">Buat Tugas Baru</strong>
                                        <small class="text-muted">Berikan instruksi dan deadline tugas</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-12 col-md-6">
                            <a href="?tab=peserta" class="card text-decoration-none border rounded-3 p-3 h-100 bg-light-hover">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-circle bg-success text-white" style="width: 44px; height: 44px;">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                    <div>
                                        <strong class="text-navy d-block">Evaluasi Peserta</strong>
                                        <small class="text-muted">Pantau progres dan berikan feedback</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Info Pengajar & Statistik Ringkas -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                    <h2 class="h6 fw-bold text-navy mb-3"><i class="fas fa-chart-pie text-primary me-2"></i>Statistik Pembelajaran</h2>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                            <span class="text-muted small">Total Peserta Terdaftar</span>
                            <strong class="text-navy"><?= (int) ($stats['participantCount'] ?? 0) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                            <span class="text-muted small">Peserta Aktif Belajar</span>
                            <strong class="text-success"><?= (int) ($stats['activeParticipantCount'] ?? 0) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                            <span class="text-muted small">Peserta Lulus / Selesai</span>
                            <strong class="text-primary"><?= (int) ($stats['completedCount'] ?? 0) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                            <span class="text-muted small">Completion Rate</span>
                            <strong class="text-navy"><?= esc($stats['completionRate'] ?? 0.0) ?>%</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                            <span class="text-muted small">Total Modul</span>
                            <strong class="text-navy"><?= (int) ($stats['moduleCount'] ?? 0) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Total Materi / Lesson</span>
                            <strong class="text-navy"><?= (int) ($stats['lessonCount'] ?? 0) ?></strong>
                        </div>
                    </div>
                </div>

                <!-- Tim Pengajar -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <h2 class="h6 fw-bold text-navy mb-3"><i class="fas fa-users-cog text-primary me-2"></i>Tim Pengajar Kelas</h2>
                    <?php if (empty($trainers)): ?>
                        <p class="text-muted small mb-0">Hanya Anda yang ditugaskan pada kelas ini.</p>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($trainers as $t): ?>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-circle bg-primary-subtle text-primary fw-bold" style="width: 40px; height: 40px;">
                                        <?= strtoupper(substr($t['name'] ?? 'U', 0, 1)) ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong class="text-navy small d-block"><?= esc($t['name'] ?? '-') ?></strong>
                                            <?php if (! empty($t['trainer_role'])): ?>
                                                <span class="badge <?= ($t['trainer_role'] === 'primary') ? 'bg-primary text-white' : 'bg-secondary-subtle text-secondary' ?> rounded-pill px-2 py-0" style="font-size: 0.68rem;">
                                                    <?= $trainerRoleLabel($t['trainer_role']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <span class="text-muted" style="font-size: 0.76rem;"><?= esc($t['email'] ?? '-') ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    <!-- TAB 2: MATERI & MODUL -->
    <?php elseif ($activeTab === 'materi'): ?>
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h2 class="h4 fw-bold text-navy mb-1">Kurikulum Pembelajaran</h2>
                    <p class="text-muted small mb-0">Susun urutan modul, lesson, dan tentukan status publikasi materi kelas.</p>
                </div>
                <div>
                    <a href="<?= route_to('workspace.trainer.module.create', (string) $classId) ?>" class="btn btn-primary rounded-pill px-4 fw-semibold">
                        <i class="fas fa-plus me-1"></i> Tambah Modul
                    </a>
                </div>
            </div>

            <?php if (empty($modules)): ?>
                <div class="text-center py-5 border rounded-4 bg-light">
                    <i class="fas fa-layer-group fa-3x text-muted mb-3 opacity-25"></i>
                    <h3 class="h6 fw-bold text-navy mb-1">Belum Ada Modul Pembelajaran</h3>
                    <p class="text-muted small mb-3">Mulai buat modul pertama untuk menyusun materi pembelajaran bagi peserta.</p>
                    <a href="<?= route_to('workspace.trainer.module.create', (string) $classId) ?>" class="btn btn-sm btn-primary rounded-pill px-4">
                        Buat Modul Sekarang
                    </a>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-4">
                    <?php foreach ($modules as $mIndex => $m): ?>
                        <div class="border rounded-4 bg-white overflow-hidden shadow-sm">
                            <!-- Module Header -->
                            <div class="bg-light p-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 border-bottom">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge bg-primary text-white rounded-pill px-3 py-2 fw-semibold">
                                        Modul <?= esc($m['sort_order']) ?>
                                    </span>
                                    <div>
                                        <h3 class="h6 fw-bold text-navy mb-0"><?= esc($m['title']) ?></h3>
                                        <?php if (! empty($m['description'])): ?>
                                            <small class="text-muted"><?= esc($m['description']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge <?= $m['status'] === 'published' ? 'bg-success' : 'bg-secondary' ?> rounded-pill px-3 py-1 small">
                                        <?= esc(ucfirst($m['status'] ?? 'draft')) ?>
                                    </span>

                                    <!-- Quick Actions Modul -->
                                    <a href="<?= route_to('workspace.trainer.lesson.create', (string) $classId, (string) $m['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Tambah Materi">
                                        <i class="fas fa-plus me-1"></i> Materi
                                    </a>
                                    <a href="<?= route_to('workspace.trainer.module.edit', (string) $classId, (string) $m['id']) ?>" class="btn btn-sm btn-light border rounded-pill px-3" title="Ubah Modul">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?= route_to('workspace.trainer.module.toggle-status', (string) $classId, (string) $m['id']) ?>" method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-light border rounded-pill px-3" title="Toggle Status">
                                            <i class="fas <?= $m['status'] === 'published' ? 'fa-eye-slash text-warning' : 'fa-globe text-success' ?>"></i>
                                        </button>
                                    </form>
                                    <form action="<?= route_to('workspace.trainer.module.delete', (string) $classId, (string) $m['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus modul ini dan seluruh materi di dalamnya?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-light border text-danger rounded-pill px-3" title="Hapus Modul">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Lessons List -->
                            <div class="p-3 px-4">
                                <?php if (empty($m['lessons'])): ?>
                                    <p class="text-muted small mb-0 py-2">
                                        <i class="fas fa-info-circle me-1"></i> Belum ada materi di modul ini. Silakan klik tombol <strong>+ Materi</strong> di atas.
                                    </p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light small text-muted">
                                                <tr>
                                                    <th style="width: 60px;">Urutan</th>
                                                    <th>Judul Materi</th>
                                                    <th>Tipe</th>
                                                    <th>Status</th>
                                                    <th class="text-end" style="width: 160px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($m['lessons'] as $l): ?>
                                                    <tr>
                                                        <td class="fw-semibold text-center text-muted"><?= esc($l['sort_order']) ?></td>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <?php if ($l['content_type'] === 'video'): ?>
                                                                    <i class="fas fa-play-circle text-danger fs-5"></i>
                                                                <?php elseif ($l['content_type'] === 'document'): ?>
                                                                    <i class="fas fa-file-pdf text-danger fs-5"></i>
                                                                <?php elseif ($l['content_type'] === 'link'): ?>
                                                                    <i class="fas fa-link text-info fs-5"></i>
                                                                <?php else: ?>
                                                                    <i class="fas fa-file-alt text-primary fs-5"></i>
                                                                <?php endif; ?>
                                                                <strong class="text-navy small"><?= esc($l['title']) ?></strong>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-light text-muted border rounded-pill px-2 py-1 small">
                                                                <?= esc(strtoupper($l['content_type'])) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge <?= $l['status'] === 'published' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?> rounded-pill px-2 py-1 small">
                                                                <?= esc(ucfirst($l['status'])) ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="btn-group btn-group-sm">
                                                                <a href="<?= route_to('workspace.trainer.lesson.edit', (string) $classId, (string) $l['id']) ?>" class="btn btn-light border" title="Ubah">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <form action="<?= route_to('workspace.trainer.lesson.toggle-status', (string) $classId, (string) $l['id']) ?>" method="POST" class="d-inline">
                                                                    <?= csrf_field() ?>
                                                                    <button type="submit" class="btn btn-light border" title="Toggle Publish">
                                                                        <i class="fas <?= $l['status'] === 'published' ? 'fa-eye-slash text-warning' : 'fa-globe text-success' ?>"></i>
                                                                    </button>
                                                                </form>
                                                                <form action="<?= route_to('workspace.trainer.lesson.delete', (string) $classId, (string) $l['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus materi ini?');">
                                                                    <?= csrf_field() ?>
                                                                    <button type="submit" class="btn btn-light border text-danger" title="Hapus">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    <!-- TAB 3: PESERTA -->
    <?php elseif ($activeTab === 'peserta'): ?>
        <?php
        $pendingCount   = count(array_filter($participants, static fn ($p): bool => ($p['enrollment_status'] ?? '') === 'pending'));
        $activeCount    = count(array_filter($participants, static fn ($p): bool => ($p['enrollment_status'] ?? '') === 'active'));
        $completedCount = count(array_filter($participants, static fn ($p): bool => ($p['enrollment_status'] ?? '') === 'completed'));
        $otherCount     = count(array_filter($participants, static fn ($p): bool => in_array(($p['enrollment_status'] ?? ''), ['withdrawn', 'cancelled'], true)));
        ?>
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div>
                    <h2 class="h4 fw-bold text-navy mb-1">Daftar Peserta & Manajemen Akses Kelas</h2>
                    <p class="text-muted small mb-0">Kelola persetujuan pendaftar, pantau progres belajar, serta status kelulusan peserta.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-navy border px-3 py-2 rounded-pill fw-semibold">
                        Total: <?= count($participants) ?> Peserta
                    </span>
                    <?php if ($pendingCount > 0): ?>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold animate__animated animate__pulse animate__infinite">
                            <?= $pendingCount ?> Menunggu Persetujuan
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($pendingCount > 0): ?>
                <div class="alert alert-warning border-warning-subtle bg-warning-subtle d-flex align-items-center justify-content-between p-3 rounded-3 mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-user-clock text-warning fs-4"></i>
                        <div>
                            <strong class="text-dark d-block">Terdapat <?= $pendingCount ?> Pendaftaran Peserta Menunggu Persetujuan</strong>
                            <span class="text-muted small">Klik tombol <strong>Setujui</strong> pada kolom aksi untuk mengaktifkan akses peserta ke materi kelas, atau <strong>Tolak</strong> bila tidak memenuhi persyaratan.</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (empty($participants)): ?>
                <div class="text-center py-5 border rounded-4 bg-light">
                    <i class="fas fa-users-slash fa-3x text-muted mb-3 opacity-25"></i>
                    <h3 class="h6 fw-bold text-navy mb-1">Belum Ada Peserta Terdaftar</h3>
                    <p class="text-muted small mb-0">Belum ada peserta yang mendaftar atau disetujui pada kelas ini.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-muted">
                            <tr>
                                <th>Peserta</th>
                                <th>Status Pendaftaran</th>
                                <th>Tanggal Bergabung</th>
                                <th style="width: 200px;">Progres Belajar</th>
                                <th>Nilai Akhir</th>
                                <th class="text-end" style="min-width: 220px;">Aksi Manajemen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($participants as $p): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-circle bg-primary-subtle text-primary fw-bold" style="width: 40px; height: 40px;">
                                                <?= strtoupper(substr($p['name'] ?? 'U', 0, 1)) ?>
                                            </div>
                                            <div>
                                                <strong class="text-navy small d-block"><?= esc($p['name']) ?></strong>
                                                <span class="text-muted" style="font-size: 0.78rem;"><?= esc($p['email']) ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($p['enrollment_status'] === 'pending'): ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1 small fw-semibold">
                                                <i class="fas fa-clock me-1"></i> Menunggu Persetujuan
                                            </span>
                                        <?php elseif ($p['enrollment_status'] === 'active'): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 small fw-semibold">
                                                <i class="fas fa-check-circle me-1"></i> Aktif Belajar
                                            </span>
                                        <?php elseif ($p['enrollment_status'] === 'completed'): ?>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 small fw-semibold">
                                                <i class="fas fa-graduation-cap me-1"></i> Selesai
                                            </span>
                                        <?php elseif ($p['enrollment_status'] === 'withdrawn'): ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1 small fw-semibold">
                                                <i class="fas fa-user-slash me-1"></i> Dikeluarkan
                                            </span>
                                        <?php elseif ($p['enrollment_status'] === 'cancelled'): ?>
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1 small fw-semibold">
                                                <i class="fas fa-times-circle me-1"></i> Ditolak
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1 small">
                                                <?= esc(ucfirst($p['enrollment_status'])) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small text-muted">
                                        <?= esc(! empty($p['enrolled_at']) ? date('d M Y', strtotime($p['enrolled_at'])) : '-') ?>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-between align-items-center small text-muted mb-1">
                                            <span><?= (int) ($p['completed_lessons'] ?? 0) ?>/<?= (int) ($p['total_lessons'] ?? 0) ?> materi</span>
                                            <span class="fw-semibold text-navy"><?= esc($p['progress_percent'] ?? 0.0) ?>%</span>
                                        </div>
                                        <div class="progress rounded-pill" style="height: 6px;">
                                            <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: <?= esc($p['progress_percent'] ?? 0.0) ?>%"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (! empty($p['passed_at']) || ! empty($p['final_score'])): ?>
                                            <strong class="text-success"><?= esc($p['final_score'] ?? '100.00') ?></strong>
                                        <?php else: ?>
                                            <span class="text-muted small">&ndash;</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex align-items-center justify-content-end gap-1 flex-wrap">
                                            <?php if ($p['enrollment_status'] === 'pending'): ?>
                                                <form action="<?= route_to('workspace.trainer.participant.approve', (string) $classId, (string) $p['enrollment_id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Setujui pendaftaran peserta <?= esc($p['name']) ?> ke dalam kelas?');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-2 py-1" style="font-size: 0.78rem;" title="Setujui & Aktifkan Pendaftaran">
                                                        <i class="fas fa-check me-1"></i> Setujui
                                                    </button>
                                                </form>
                                                <form action="<?= route_to('workspace.trainer.participant.reject', (string) $classId, (string) $p['enrollment_id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Tolak pendaftaran peserta <?= esc($p['name']) ?>?');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" style="font-size: 0.78rem;" title="Tolak Pendaftaran">
                                                        <i class="fas fa-times me-1"></i> Tolak
                                                    </button>
                                                </form>
                                            <?php elseif ($p['enrollment_status'] === 'active'): ?>
                                                <form action="<?= route_to('workspace.trainer.participant.kick', (string) $classId, (string) $p['enrollment_id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin mengeluarkan peserta <?= esc($p['name']) ?> dari kelas ini?');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" style="font-size: 0.78rem;" title="Keluarkan Peserta dari Kelas">
                                                        <i class="fas fa-user-slash me-1"></i> Keluarkan
                                                    </button>
                                                </form>
                                            <?php elseif (in_array($p['enrollment_status'], ['withdrawn', 'cancelled'], true)): ?>
                                                <form action="<?= route_to('workspace.trainer.participant.reactivate', (string) $classId, (string) $p['enrollment_id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Aktifkan kembali peserta <?= esc($p['name']) ?> ke dalam kelas?');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2 py-1" style="font-size: 0.78rem;" title="Aktifkan Kembali Peserta">
                                                        <i class="fas fa-user-check me-1"></i> Aktifkan
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <a href="<?= route_to('workspace.trainer.participant.detail', (string) $classId, (string) $p['enrollment_id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1" style="font-size: 0.78rem;" title="Rincian & Catatan Evaluasi">
                                                <i class="fas fa-eye me-1"></i> Rincian
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    <!-- TAB 4: ASSESSMENT (QUIZ) -->
    <?php elseif ($activeTab === 'assessment'): ?>
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h2 class="h4 fw-bold text-navy mb-1">Ujian & Kuis Pembelajaran</h2>
                    <p class="text-muted small mb-0">Kelola Pre-Test pembuka, Kuis evaluasi per modul, serta Post-Test kelulusan.</p>
                </div>
                <div>
                    <a href="<?= route_to('workspace.trainer.quiz.create', (string) $classId) ?>" class="btn btn-primary rounded-pill px-4 fw-semibold">
                        <i class="fas fa-plus me-1"></i> Tambah Ujian
                    </a>
                </div>
            </div>

            <?php if (empty($quizzes)): ?>
                <div class="text-center py-5 border rounded-4 bg-light">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3 opacity-25"></i>
                    <h3 class="h6 fw-bold text-navy mb-1">Belum Ada Ujian Dibuat</h3>
                    <p class="text-muted small mb-3">Tambahkan Pre-Test, Quiz modul, atau Post-Test untuk menguji pemahaman peserta.</p>
                    <a href="<?= route_to('workspace.trainer.quiz.create', (string) $classId) ?>" class="btn btn-sm btn-primary rounded-pill px-4">
                        Buat Ujian Baru
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-muted">
                            <tr>
                                <th>Tipe Ujian</th>
                                <th>Judul Kuis</th>
                                <th>Batas Percobaan</th>
                                <th>Jumlah Soal</th>
                                <th>Percobaan Dikerjakan</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($quizzes as $q): ?>
                                <tr>
                                    <td>
                                        <?php if ($q['type'] === 'pre_test'): ?>
                                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 small fw-semibold">Pre-Test</span>
                                        <?php elseif ($q['type'] === 'post_test'): ?>
                                            <span class="badge bg-danger rounded-pill px-3 py-1 small fw-semibold">Post-Test</span>
                                        <?php else: ?>
                                            <span class="badge bg-info text-dark rounded-pill px-3 py-1 small fw-semibold">Quiz Modul</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong class="text-navy small d-block"><?= esc($q['title']) ?></strong>
                                    </td>
                                    <td class="small text-muted">
                                        <?= (int) ($q['attempt_limit'] ?? 1) ?> kali
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-navy border rounded-pill px-2 py-1 small">
                                            <?= (int) ($q['question_count'] ?? 0) ?> Soal
                                        </span>
                                    </td>
                                    <td class="small text-muted">
                                        <?= (int) ($q['attempts_count'] ?? 0) ?> submission
                                    </td>
                                    <td>
                                        <span class="badge <?= $q['status'] === 'published' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?> rounded-pill px-2 py-1 small">
                                            <?= esc(ucfirst($q['status'] ?? 'draft')) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2">
                                            <a href="<?= route_to('workspace.trainer.quiz.attempts', (string) $classId, (string) $q['id']) ?>" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                                <i class="fas fa-users-cog me-1"></i> Hasil Peserta
                                            </a>
                                            <a href="<?= route_to('workspace.trainer.quiz.edit', (string) $classId, (string) $q['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                                                <i class="fas fa-edit me-1"></i> Kelola Soal
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    <!-- TAB 5: ASSIGNMENT (TUGAS) -->
    <?php elseif ($activeTab === 'assignment'): ?>
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h2 class="h4 fw-bold text-navy mb-1">Manajemen Tugas & Praktik</h2>
                    <p class="text-muted small mb-0">Tugas bersifat opsional. Buat penugasan kasus atau dokumen untuk dievaluasi.</p>
                </div>
                <div>
                    <a href="<?= route_to('workspace.trainer.assignment.create', (string) $classId) ?>" class="btn btn-primary rounded-pill px-4 fw-semibold">
                        <i class="fas fa-plus me-1"></i> Buat Tugas Baru
                    </a>
                </div>
            </div>

            <?php if (empty($assignments)): ?>
                <div class="text-center py-5 border rounded-4 bg-light">
                    <i class="fas fa-tasks fa-3x text-muted mb-3 opacity-25"></i>
                    <h3 class="h6 fw-bold text-navy mb-1">Belum Ada Tugas Dibuat</h3>
                    <p class="text-muted small mb-3">Tugas opsional dan dapat dibuat jika kelas membutuhkan evaluasi praktikum.</p>
                    <a href="<?= route_to('workspace.trainer.assignment.create', (string) $classId) ?>" class="btn btn-sm btn-primary rounded-pill px-4">
                        Buat Tugas Baru
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-muted">
                            <tr>
                                <th>Judul Tugas</th>
                                <th>Materi Terkait</th>
                                <th>Batas Waktu (Deadline)</th>
                                <th>Pengumpulan</th>
                                <th>Perlu Review</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assignments as $asg): ?>
                                <tr>
                                    <td>
                                        <strong class="text-navy small d-block"><?= esc($asg['title']) ?></strong>
                                        <?php if (! empty($asg['guide']['guide_url'])): ?>
                                            <a href="<?= base_url($asg['guide']['guide_url']) ?>" target="_blank" class="badge bg-danger-subtle text-danger border border-danger-subtle text-decoration-none mt-1 d-inline-flex align-items-center gap-1" style="font-size: 0.72rem;" title="Buka File Petunjuk PDF">
                                                <i class="fas fa-file-pdf"></i> Petunjuk: <?= esc($asg['guide']['guide_filename'] ?? 'PDF') ?> (Lihat)
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small text-muted">
                                        <?= esc($asg['lesson_title'] ?? 'Semua Modul') ?>
                                    </td>
                                    <td class="small text-muted">
                                        <?= esc(! empty($asg['due_at']) ? date('d M Y, H:i', strtotime($asg['due_at'])) : 'Tanpa batas waktu') ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-navy border rounded-pill px-2 py-1 small">
                                            <?= (int) ($asg['total_submissions'] ?? 0) ?> submission
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ((int) ($asg['pending_reviews'] ?? 0) > 0): ?>
                                            <span class="badge bg-warning text-dark rounded-pill px-2 py-1 small fw-semibold">
                                                <i class="fas fa-clock me-1"></i> <?= (int) $asg['pending_reviews'] ?> perlu review
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small">
                                                Selesai direview
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $asg['status'] === 'published' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?> rounded-pill px-2 py-1 small">
                                            <?= esc(ucfirst($asg['status'] ?? 'published')) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= route_to('workspace.trainer.assignment.submissions', (string) $classId, (string) $asg['id']) ?>" class="btn btn-outline-primary" title="Lihat Submission">
                                                <i class="fas fa-eye me-1"></i> Submissions
                                            </a>
                                            <a href="<?= route_to('workspace.trainer.assignment.edit', (string) $classId, (string) $asg['id']) ?>" class="btn btn-light border" title="Ubah">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="<?= route_to('workspace.trainer.assignment.delete', (string) $classId, (string) $asg['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus penugasan ini?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-light border text-danger" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    <!-- TAB 6: PROGRESS MONITORING -->
    <?php elseif ($activeTab === 'progres'): ?>
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h2 class="h4 fw-bold text-navy mb-1">Monitoring Progres Kelas</h2>
                    <p class="text-muted small mb-0">Pantau tingkat kelulusan dan rincian penyelesaian modul per peserta.</p>
                </div>
            </div>

            <!-- Summary Bar -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="border rounded-3 p-3 bg-light text-center">
                        <small class="text-muted d-block mb-1">Total Peserta</small>
                        <strong class="h4 text-navy mb-0"><?= (int) ($stats['participantCount'] ?? 0) ?></strong>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="border rounded-3 p-3 bg-light text-center">
                        <small class="text-muted d-block mb-1">Peserta Aktif</small>
                        <strong class="h4 text-primary mb-0"><?= (int) ($stats['activeParticipantCount'] ?? 0) ?></strong>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="border rounded-3 p-3 bg-light text-center">
                        <small class="text-muted d-block mb-1">Lulus / Selesai</small>
                        <strong class="h4 text-success mb-0"><?= (int) ($stats['completedCount'] ?? 0) ?></strong>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="border rounded-3 p-3 bg-light text-center">
                        <small class="text-muted d-block mb-1">Completion Rate</small>
                        <strong class="h4 text-navy mb-0"><?= esc($stats['completionRate'] ?? 0.0) ?>%</strong>
                    </div>
                </div>
            </div>

            <!-- Participants Progress Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th>Peserta</th>
                            <th>Status Enrollment</th>
                            <th>Materi Selesai</th>
                            <th style="width: 250px;">Progres Pembelajaran</th>
                            <th>Nilai Akhir</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($participants)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted small">Belum ada peserta di kelas ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($participants as $p): ?>
                                <tr>
                                    <td>
                                        <strong class="text-navy small d-block"><?= esc($p['name']) ?></strong>
                                        <span class="text-muted" style="font-size: 0.78rem;"><?= esc($p['email']) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $p['enrollment_status'] === 'active' ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' ?> rounded-pill px-3 py-1 small">
                                            <?= esc(ucfirst($p['enrollment_status'])) ?>
                                        </span>
                                    </td>
                                    <td class="small text-muted">
                                        <?= (int) ($p['completed_lessons'] ?? 0) ?> / <?= (int) ($p['total_lessons'] ?? 0) ?> materi
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-between align-items-center small text-muted mb-1">
                                            <span>Penyelesaian</span>
                                            <span class="fw-semibold text-navy"><?= esc($p['progress_percent'] ?? 0.0) ?>%</span>
                                        </div>
                                        <div class="progress rounded-pill" style="height: 6px;">
                                            <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: <?= esc($p['progress_percent'] ?? 0.0) ?>%"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (! empty($p['passed_at']) || ! empty($p['final_score'])): ?>
                                            <strong class="text-success"><?= esc($p['final_score'] ?? '100.00') ?></strong>
                                        <?php else: ?>
                                            <span class="text-muted small">&ndash;</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= route_to('workspace.trainer.participant.detail', (string) $classId, (string) $p['enrollment_id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- TAB 7: BUKU NILAI KELAS (GRADEBOOK MATRIX) -->
    <?php if ($activeTab === 'gradebook'): ?>
        <?php
        $gb = $gradebook ?? [];
        $gbRows = $gb['rows'] ?? [];
        $gbStats = $gb['stats'] ?? [];
        $formativeQuizzes = $gb['formativeQuizzes'] ?? [];
        $assignmentsList = $gb['assignments'] ?? [];
        $passingGrade = (float) ($gbStats['passing_grade'] ?? 70.0);
        ?>
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
            <!-- Header Tab & Action Bar -->
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 pb-3 border-bottom mb-4">
                <div>
                    <h2 class="h5 fw-bold text-navy mb-1">
                        <i class="fas fa-graduation-cap text-primary me-2"></i>Buku Nilai & Rekapitulasi Kelulusan Kelas
                    </h2>
                    <p class="text-muted small mb-0">
                        Matriks evaluasi terpadu nilai pre-test, kuis modul, tugas praktik, post-test, skor terbobot, dan predikat resmi.
                    </p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="<?= route_to('workspace.trainer.class.gradebook.export', (string) $classId) ?>" class="btn btn-success rounded-pill px-3 shadow-sm fw-semibold">
                        <i class="fas fa-file-csv me-1"></i> Unduh Rekap (CSV)
                    </a>
                    <button type="button" onclick="window.print()" class="btn btn-outline-secondary rounded-pill px-3">
                        <i class="fas fa-print me-1"></i> Cetak Lembar Nilai
                    </button>
                </div>
            </div>

            <!-- KPI Metrik Buku Nilai -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3 col-xl">
                    <div class="card border-0 rounded-4 p-3 bg-light-subtle border h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-2 bg-primary-subtle text-primary fs-5 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.72rem;">Total Peserta</small>
                                <strong class="h5 fw-bold text-navy mb-0"><?= (int) ($gbStats['total'] ?? 0) ?> Peserta</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3 col-xl">
                    <div class="card border-0 rounded-4 p-3 bg-light-subtle border h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-2 bg-success-subtle text-success fs-5 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.72rem;">Tingkat Kelulusan</small>
                                <strong class="h5 fw-bold text-success mb-0"><?= (float) ($gbStats['pass_rate'] ?? 0) ?>%</strong>
                                <small class="text-muted" style="font-size: 0.7rem;">(<?= (int) ($gbStats['passed_count'] ?? 0) ?> Lulus)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3 col-xl">
                    <div class="card border-0 rounded-4 p-3 bg-light-subtle border h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-2 bg-info-subtle text-info fs-5 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.72rem;">Rata-rata Kelas</small>
                                <strong class="h5 fw-bold text-navy mb-0"><?= number_format((float) ($gbStats['avg'] ?? 0), 1) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3 col-xl">
                    <div class="card border-0 rounded-4 p-3 bg-light-subtle border h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-2 bg-warning-subtle text-warning fs-5 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.72rem;">Nilai Tertinggi</small>
                                <strong class="h5 fw-bold text-navy mb-0"><?= number_format((float) ($gbStats['max'] ?? 0), 1) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3 col-xl">
                    <div class="card border-0 rounded-4 p-3 bg-light-subtle border h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-2 bg-secondary-subtle text-secondary fs-5 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.72rem;">Passing Grade</small>
                                <strong class="h5 fw-bold text-navy mb-0"><?= number_format($passingGrade, 1) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Instant Search Tool -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div class="position-relative flex-grow-1" style="max-width: 400px;">
                    <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" id="wsGradebookSearch" class="form-control rounded-pill ps-5 py-2 small" placeholder="Cari nama atau NIP peserta...">
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label class="small text-muted mb-0 me-1">Status:</label>
                    <select id="wsGradebookStatusFilter" class="form-select form-select-sm rounded-pill px-3" style="width: auto;">
                        <option value="all">Semua Status (<?= count($gbRows) ?>)</option>
                        <option value="passed">Lulus Saja (<?= (int) ($gbStats['passed_count'] ?? 0) ?>)</option>
                        <option value="failed">Belum Memenuhi (<?= count($gbRows) - (int) ($gbStats['passed_count'] ?? 0) ?>)</option>
                    </select>
                </div>
            </div>

            <!-- Matrix Gradebook Table -->
            <?php if (empty($gbRows)): ?>
                <div class="text-center py-5 border rounded-4 bg-light">
                    <i class="fas fa-user-graduate fa-3x text-muted mb-3 opacity-25"></i>
                    <h3 class="h6 fw-bold text-navy mb-1">Belum Ada Peserta Terdaftar</h3>
                    <p class="text-muted small mb-0">Peserta yang mendaftar dan aktif di kelas ini akan otomatis tercatat di lembar buku nilai.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive border rounded-3 overflow-hidden">
                    <table class="table table-hover align-middle mb-0" id="wsGradebookTable" style="font-size: 0.82rem;">
                        <thead class="table-light text-navy text-nowrap">
                            <tr>
                                <th style="width: 40px;" class="text-center">#</th>
                                <th style="min-width: 180px;">Peserta</th>
                                <th style="min-width: 100px;">Progres</th>
                                <th class="text-center" style="min-width: 75px;">Pre-Test</th>
                                <?php foreach ($formativeQuizzes as $idx => $fq): ?>
                                    <th class="text-center" style="min-width: 70px;" title="<?= esc($fq['title'] ?? 'Kuis') ?>">
                                        <span class="d-inline-block text-truncate" style="max-width: 80px;">
                                            Q<?= $idx + 1 ?>
                                        </span>
                                    </th>
                                <?php endforeach; ?>
                                <?php foreach ($assignmentsList as $idx => $asg): ?>
                                    <th class="text-center" style="min-width: 70px;" title="<?= esc($asg['title'] ?? 'Tugas') ?>">
                                        <span class="d-inline-block text-truncate" style="max-width: 80px;">
                                            T<?= $idx + 1 ?>
                                        </span>
                                    </th>
                                <?php endforeach; ?>
                                <th class="text-center" style="min-width: 75px;">Post-Test</th>
                                <th class="text-center" style="min-width: 80px;">Nilai Akhir</th>
                                <th class="text-center" style="min-width: 65px;">Grade</th>
                                <th class="text-center" style="min-width: 110px;">Status</th>
                                <th class="text-end" style="min-width: 110px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($gbRows as $idx => $r): ?>
                                <?php
                                $finalSc = (float) $r['final_score'];
                                $isPassed = (bool) $r['is_passed'];
                                $rowStatusAttr = $isPassed ? 'passed' : 'failed';
                                $searchText = strtolower(($r['name'] ?? '') . ' ' . ($r['email'] ?? '') . ' ' . ($r['username'] ?? ''));
                                ?>
                                <tr class="ws-gb-row" data-search="<?= esc($searchText) ?>" data-status="<?= $rowStatusAttr ?>">
                                    <td class="text-center text-muted"><?= $idx + 1 ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-circle bg-primary-subtle text-primary fw-bold" style="width: 32px; height: 32px; font-size: 11px;">
                                                <?= strtoupper(substr($r['name'] ?? 'U', 0, 1)) ?>
                                            </div>
                                            <div>
                                                <strong class="text-navy d-block text-truncate" style="max-width: 170px;"><?= esc($r['name']) ?></strong>
                                                <small class="text-muted d-block text-truncate" style="max-width: 170px; font-size: 0.72rem;"><?= esc($r['email']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1 rounded-pill" style="height: 5px;">
                                                <div class="progress-bar <?= $r['progress_percent'] >= 100 ? 'bg-success' : 'bg-primary' ?> rounded-pill" style="width: <?= $r['progress_percent'] ?>%"></div>
                                            </div>
                                            <span class="small fw-semibold text-muted" style="font-size: 0.72rem;"><?= $r['progress_percent'] ?>%</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($r['pre_score'] !== null): ?>
                                            <span class="badge bg-light text-navy border px-2 py-1"><?= number_format((float) $r['pre_score'], 1) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">&ndash;</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php foreach ($formativeQuizzes as $fq): ?>
                                        <?php $sc = $r['quiz_details'][(int) $fq['id']] ?? null; ?>
                                        <td class="text-center">
                                            <?php if ($sc !== null): ?>
                                                <span class="badge <?= $sc >= $passingGrade ? 'bg-success-subtle text-success' : 'bg-light text-navy border' ?> px-2 py-1">
                                                    <?= number_format((float) $sc, 1) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">&ndash;</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <?php foreach ($assignmentsList as $asg): ?>
                                        <?php $sub = $r['asg_details'][(int) $asg['id']] ?? null; ?>
                                        <td class="text-center">
                                            <?php if ($sub !== null && $sub['score'] !== null): ?>
                                                <span class="badge <?= $sub['score'] >= $passingGrade ? 'bg-success-subtle text-success' : 'bg-light text-navy border' ?> px-2 py-1">
                                                    <?= number_format((float) $sub['score'], 1) ?>
                                                </span>
                                            <?php elseif ($sub !== null): ?>
                                                <span class="badge bg-warning-subtle text-warning px-2 py-1" title="Menunggu Penilaian">In Review</span>
                                            <?php else: ?>
                                                <span class="text-muted">&ndash;</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <td class="text-center">
                                        <?php if ($r['post_score'] !== null): ?>
                                            <span class="badge <?= $r['post_score'] >= $passingGrade ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger' ?> px-2 py-1 fw-bold">
                                                <?= number_format((float) $r['post_score'], 1) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">&ndash;</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <strong class="h6 mb-0 <?= $isPassed ? 'text-success fw-bold' : 'text-danger fw-bold' ?>">
                                            <?= number_format($finalSc, 1) ?>
                                        </strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-navy text-white rounded-pill px-2 py-1" title="<?= esc($r['predicate']) ?>">
                                            <?= esc($r['grade_letter']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($isPassed): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 fw-semibold">
                                                <i class="fas fa-check-circle me-1"></i>Lulus
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1">
                                                <i class="fas fa-hourglass-half me-1"></i>Belum Lulus
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <a href="<?= route_to('workspace.participant.transcript', (string) $classId, (string) $r['enrollment_id']) ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1" title="Lihat Rapor / Transkrip Resmi">
                                            <i class="fas fa-file-alt"></i> Transkrip
                                        </a>
                                        <a href="<?= route_to('workspace.trainer.participant.detail', (string) $classId, (string) $r['enrollment_id']) ?>" class="btn btn-sm btn-light text-navy border rounded-pill px-2 py-1 ms-1" title="Detail Riwayat Belajar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Client-Side Gradebook Filter Script -->
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.getElementById('wsGradebookSearch');
                    const statusFilter = document.getElementById('wsGradebookStatusFilter');
                    const rows = document.querySelectorAll('.ws-gb-row');

                    function filterGradebook() {
                        const q = (searchInput ? searchInput.value : '').toLowerCase().trim();
                        const st = (statusFilter ? statusFilter.value : 'all');

                        rows.forEach(function(row) {
                            const rowSearch = row.getAttribute('data-search') || '';
                            const rowStatus = row.getAttribute('data-status') || '';

                            const matchSearch = (q === '' || rowSearch.includes(q));
                            const matchStatus = (st === 'all' || rowStatus === st);

                            if (matchSearch && matchStatus) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    }

                    if (searchInput) searchInput.addEventListener('input', filterGradebook);
                    if (statusFilter) statusFilter.addEventListener('change', filterGradebook);
                });
                </script>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>