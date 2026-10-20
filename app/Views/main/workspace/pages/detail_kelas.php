<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?php
$state       = $state ?? [];
$ctx         = $state['class'] ?? $classContext ?? [];
$trainers    = $state['trainers'] ?? [];
$enrollment  = $state['enrollment'] ?? [];
$modules     = $state['modules'] ?? [];
$cta         = $state['cta'] ?? [];
$stateCode   = $state['stateCode'] ?? 'in_learning';
$preTest     = $state['pre_test'] ?? [];
$postTest    = $state['post_test'] ?? [];
$pct         = (float) ($state['overall_percent'] ?? 0);
$finalScore  = $enrollment['final_score'] ?? null;

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
            'class' => 'bg-success-subtle text-success border border-success-subtle',
            'icon'  => 'bi bi-person-video3',
        ],
        'hybrid' => [
            'label' => 'Hybrid',
            'class' => 'bg-purple-subtle text-purple border border-purple-subtle',
            'icon'  => 'bi bi-arrow-repeat',
        ],
        default => [
            'label' => 'Online LMS',
            'class' => 'bg-primary-subtle text-primary border border-primary-subtle',
            'icon'  => 'bi bi-laptop',
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

<!-- 1. Hero Card: Informasi Kelas & Konteks Pembelajaran (FiStudy DNA) -->
<div class="ws-page-header">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-2 rounded-pill text-uppercase">
                <i class="bi bi-book-half me-1"></i> <?= esc($ctx['program_title'] ?? 'Program Pembelajaran') ?>
            </span>
            <?php $mBadge = $methodBadge($ctx['learning_method'] ?? 'online'); ?>
            <span class="badge <?= $mBadge['class'] ?> px-3 py-2 rounded-pill fw-semibold">
                <i class="<?= $mBadge['icon'] ?> me-1"></i> <?= esc($mBadge['label']) ?>
            </span>
            <?php if (! empty($ctx['course_level'])): ?>
                <span class="badge bg-secondary-subtle text-secondary fw-semibold px-3 py-2 rounded-pill text-capitalize">
                    <?= esc($ctx['course_level']) ?>
                </span>
            <?php endif; ?>
        </div>
        <div class="text-muted small">
            <i class="bi bi-calendar3 me-1 text-primary"></i>
            Periode: <strong><?= esc($ctx['start_at'] ? date('d M Y', strtotime($ctx['start_at'])) : 'Fleksibel') ?></strong>
            &ndash;
            <strong><?= esc($ctx['end_at'] ? date('d M Y', strtotime($ctx['end_at'])) : 'Selesai') ?></strong>
        </div>
    </div>

    <div class="row g-4 align-items-center mb-3">
        <div class="col-md-3 col-xl-2">
            <div class="rounded-4 overflow-hidden shadow-xs border bg-light" style="aspect-ratio: 16/10;">
                <img src="<?= $fmtCover($ctx) ?>" alt="<?= esc($ctx['course_title'] ?? 'Cover') ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/resources/course-details-img-1.jpg') ?>';">
            </div>
        </div>
        <div class="col-md-9 col-xl-10">
            <h1 class="h2 fw-bold mb-2" style="color: #082f5c;"><?= esc($ctx['name'] ?? 'Detail Kelas') ?></h1>
            <p class="text-muted fs-6 mb-2">
                Course: <strong class="text-dark"><?= esc($ctx['course_title'] ?? '-') ?></strong> &middot; 
                Kode Kelas: <code class="text-primary fw-bold font-monospace"><?= esc($ctx['code'] ?? '-') ?></code>
            </p>
            <div class="d-flex flex-wrap gap-3 text-muted small">
                <?php if (! empty($ctx['location_or_link'])): ?>
                    <div>
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                        Akses/Lokasi: <strong class="text-dark"><?= esc($ctx['location_or_link']) ?></strong>
                    </div>
                <?php endif; ?>
                <?php if (! empty($ctx['target_audience'])): ?>
                    <div>
                        <i class="bi bi-people-fill text-info me-1"></i>
                        Sasaran: <strong><?= esc($ctx['target_audience']) ?></strong>
                    </div>
                <?php endif; ?>
                <?php if (! empty($ctx['duration_minutes'])): ?>
                    <div>
                        <i class="bi bi-clock-history text-primary me-1"></i>
                        Estimasi: <strong><?= format_learning_jp($ctx['duration_minutes']) ?></strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Component Evaluation Weights & Passing Grade -->
    <div class="row g-3 pt-3 border-top mt-3">
        <div class="col-6 col-md-3">
            <div class="p-3 bg-light rounded-4 text-center border">
                <span class="d-block text-muted small mb-1">Passing Grade</span>
                <strong class="fs-5 text-dark"><?= (float) ($ctx['passing_grade'] ?? 70.0) ?>%</strong>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-light rounded-4 text-center border">
                <span class="d-block text-muted small mb-1">Bobot Quiz</span>
                <strong class="fs-5 text-dark"><?= (float) ($ctx['quiz_weight'] ?? 40.0) ?>%</strong>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-light rounded-4 text-center border">
                <span class="d-block text-muted small mb-1">Bobot Tugas</span>
                <strong class="fs-5 text-dark"><?= (float) ($ctx['assignment_weight'] ?? 0.0) ?>%</strong>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-light rounded-4 text-center border">
                <span class="d-block text-muted small mb-1">Bobot Post-Test</span>
                <strong class="fs-5 text-dark"><?= (float) ($ctx['post_test_weight'] ?? 60.0) ?>%</strong>
            </div>
        </div>
    </div>
</div>

<!-- 2. STATE-DRIVEN ACTION CARD (Kondisional & Responsif Berdasarkan Status Peserta) -->
<div class="card border-0 rounded-4 shadow-sm mb-3 overflow-hidden">
    <div class="card-body p-3 p-md-4 <?= in_array($stateCode, ['certificate_issued', 'completed_passed'], true) ? 'bg-success-subtle text-success-emphasis' : (in_array($stateCode, ['pre_test_required', 'module_quiz_required'], true) ? 'bg-warning-subtle text-warning-emphasis' : ($stateCode === 'completed_failed' ? 'bg-danger-subtle text-danger-emphasis' : 'bg-primary-subtle text-primary-emphasis')) ?>">
        <div class="row align-items-center g-3 g-md-4">
            <div class="col-12 col-md-8 col-xl-9">
                <?php if ($stateCode === 'not_enrolled'): ?>
                    <span class="badge bg-primary text-white rounded-pill mb-2 px-3 py-1">Pendaftaran Tersedia</span>
                    <h3 class="h5 fw-bold mb-1" style="color: #082f5c;">Mulai Kelas Pembelajaran Ini</h3>
                    <p class="mb-0 text-muted">Daftarkan diri Anda untuk mengakses seluruh modul pembelajaran, materi interaktif, kuis, dan ujian akhir.</p>
                <?php elseif ($stateCode === 'enrollment_pending'): ?>
                    <span class="badge bg-secondary text-white rounded-pill mb-2 px-3 py-1">Verifikasi Pendaftaran</span>
                    <h3 class="h5 fw-bold mb-1" style="color: #082f5c;">Pendaftaran Menunggu Persetujuan</h3>
                    <p class="mb-0 text-muted">Permintaan pendaftaran Anda telah tersimpan dan sedang menunggu konfirmasi dari Operator/Admin.</p>
                <?php elseif ($stateCode === 'pre_test_required'): ?>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill mb-2 px-3 py-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Tahap Wajib</span>
                    <h3 class="h5 fw-bold mb-1">Selesaikan Pre-Test Terlebih Dahulu</h3>
                    <p class="mb-0 text-dark">Kelas ini menerapkan Pre-Test. Anda wajib mengerjakan Pre-Test untuk membuka akses ke modul pembelajaran pertama.</p>
                <?php elseif ($stateCode === 'in_learning'): ?>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary text-white rounded-pill px-3 py-1">Sedang Belajar</span>
                        <span class="text-muted small"><?= (int) ($state['completed_lessons_count'] ?? 0) ?> dari <?= (int) ($state['all_lessons_count'] ?? 0) ?> materi selesai</span>
                    </div>
                    <h3 class="h5 fw-bold mb-1" style="color: #082f5c;">Lanjutkan Progres Belajar Anda</h3>
                    <div class="progress mb-2 rounded-pill" style="height: 8px; max-width: 480px;">
                        <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: <?= $pct ?>%" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="mb-0 text-muted small">Capaian kurikulum: <strong><?= $pct ?>%</strong>.</p>
                <?php elseif ($stateCode === 'module_quiz_required'): ?>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill mb-2 px-3 py-1"><i class="bi bi-clock-history me-1"></i> Evaluasi Modul</span>
                    <h3 class="h5 fw-bold mb-1">Kerjakan Quiz Modul</h3>
                    <p class="mb-0 text-dark">Seluruh materi pada modul ini telah selesai. Selesaikan quiz modul untuk membuka materi pada modul berikutnya.</p>
                <?php elseif ($stateCode === 'assignment_required'): ?>
                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill mb-2 px-3 py-1"><i class="bi bi-file-earmark-text me-1"></i> Penugasan</span>
                    <h3 class="h5 fw-bold mb-1">Kumpulkan Tugas Mandiri</h3>
                    <p class="mb-0 text-dark">Terdapat tugas wajib yang perlu Anda kumpulkan untuk dinilai oleh instruktur.</p>
                <?php elseif ($stateCode === 'post_test_required'): ?>
                    <span class="badge bg-primary text-white rounded-pill mb-2 px-3 py-1"><i class="bi bi-trophy me-1"></i> Tahap Akhir</span>
                    <h3 class="h5 fw-bold mb-1">Semua Modul Selesai! Kerjakan Post-Test</h3>
                    <p class="mb-0 text-dark">Selamat! Anda telah menuntaskan seluruh modul. Kerjakan Post-Test sebagai penentu kelulusan kelas.</p>
                <?php elseif ($stateCode === 'waiting_certificate'): ?>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill mb-2 px-3 py-1"><i class="bi bi-hourglass-split me-1"></i> Antrean Approval</span>
                    <h3 class="h5 fw-bold mb-1">Selamat, Anda Dinyatakan Lulus!</h3>
                    <p class="mb-0 text-dark">Nilai Akhir: <strong><?= (float) ($finalScore ?? 0) ?></strong> (Passing Grade: <?= (float) ($ctx['passing_grade'] ?? 70) ?>). Pengajuan sertifikat Anda sedang menunggu verifikasi dan persetujuan Operator/Admin.</p>
                <?php elseif ($stateCode === 'certificate_issued'): ?>
                    <span class="badge bg-success text-white rounded-pill mb-2 px-3 py-1"><i class="bi bi-patch-check-fill me-1"></i> Sertifikat Terbit</span>
                    <h3 class="h5 fw-bold mb-1">Selamat! Sertifikat Resmi Anda Telah Terbit</h3>
                    <p class="mb-0 text-dark">Nomor Sertifikat: <strong class="font-monospace"><?= esc($state['certificate']['certificate_number'] ?? '-') ?></strong> &middot; Nilai Akhir: <strong><?= (float) ($finalScore ?? 0) ?></strong>.</p>
                <?php elseif ($stateCode === 'completed_passed'): ?>
                    <span class="badge bg-success text-white rounded-pill mb-2 px-3 py-1"><i class="bi bi-check-circle-fill me-1"></i> Lulus</span>
                    <h3 class="h5 fw-bold mb-1">Selamat, Anda Telah Lulus Pembelajaran!</h3>
                    <p class="mb-0 text-dark">Seluruh kurikulum telah diselesaikan dengan Nilai Akhir: <strong><?= (float) ($finalScore ?? 0) ?></strong>.</p>
                <?php elseif ($stateCode === 'completed_failed'): ?>
                    <span class="badge bg-danger text-white rounded-pill mb-2 px-3 py-1">Belum Lulus</span>
                    <h3 class="h5 fw-bold mb-1">Belum Memenuhi Passing Grade</h3>
                    <p class="mb-0 text-dark">Nilai akhir Anda <strong><?= (float) ($finalScore ?? 0) ?></strong> belum mencapai passing grade <strong><?= (float) ($ctx['passing_grade'] ?? 70) ?></strong>.</p>
                <?php endif; ?>
            </div>

            <!-- Dynamic Action Button CTA -->
            <div class="col-12 col-md-4 col-xl-3 text-md-end">
                <?php if ($stateCode === 'not_enrolled'): ?>
                    <form method="post" action="<?= esc($cta['url'] ?? '#') ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-bold w-100 w-md-auto text-nowrap">
                            <i class="bi bi-plus-circle me-1"></i> <?= esc($cta['label'] ?? 'Daftar Kelas Ini') ?>
                        </button>
                    </form>
                <?php elseif (! empty($cta['disabled'])): ?>
                    <button class="<?= esc($cta['class'] ?? 'btn btn-secondary') ?> rounded-pill px-4 py-2 w-100 w-md-auto text-nowrap" disabled>
                        <?= esc($cta['label'] ?? 'Status') ?>
                    </button>
                <?php else: ?>
                    <a href="<?= esc($cta['url'] ?? '#') ?>" class="<?= esc($cta['class'] ?? 'btn btn-primary') ?> rounded-pill px-4 py-2 shadow-sm fw-bold w-100 w-md-auto text-nowrap">
                        <?= esc($cta['label'] ?? 'Mulai Belajar') ?> &raquo;
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- 3. Profil Trainer & Pengajar -->
<div class="card border-0 rounded-4 shadow-sm p-3 p-md-4 mb-3 bg-white">
    <h2 class="h6 mb-3 fw-bold d-flex align-items-center gap-2" style="color: #082f5c;">
        <i class="bi bi-person-badge text-primary"></i> Instruktur & Trainer Kelas
    </h2>
    <?php if (empty($trainers)): ?>
        <p class="text-muted mb-0 small">Belum ada instruktur/trainer yang ditugaskan secara aktif pada kelas ini.</p>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($trainers as $t): ?>
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3 p-2 p-md-3 rounded-3 border bg-light">
                        <div class="avatar-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 40px; height: 40px; font-size: 15px;">
                            <?= strtoupper(substr($t['name'] ?? 'T', 0, 2)) ?>
                        </div>
                        <div class="overflow-hidden">
                            <h3 class="h6 mb-0 text-dark fw-bold text-truncate" style="font-size: 0.9rem;"><?= esc($t['name'] ?? 'Trainer') ?></h3>
                            <span class="badge bg-primary-subtle text-primary small rounded-pill mb-1">
                                <?= $trainerRoleLabel($t['trainer_role'] ?? 'primary') ?>
                            </span>
                            <?php if (! empty($t['email'])): ?>
                                <p class="text-muted small mb-0 text-truncate"><?= esc($t['email']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- 4. Deskripsi & Silabus Kurikulum Lengkap -->
<div class="card border-0 rounded-4 shadow-sm p-3 p-md-4 mb-3 bg-white">
    <h2 class="h5 mb-3 fw-bold d-flex align-items-center gap-2" style="color: #082f5c;">
        <i class="bi bi-card-text text-primary"></i> Deskripsi Kursus
    </h2>
    <?php
    $parsedCtxDesc = parse_class_batch_data($ctx['description'] ?? '');
    if (! empty($parsedCtxDesc['description'])):
    ?>
        <div class="p-3 bg-light rounded-4 border mb-3">
            <strong class="d-block small text-primary fw-bold text-uppercase mb-1">Tentang Batch Kelas Ini:</strong>
            <div class="text-dark small lh-base"><?= nl2br(esc($parsedCtxDesc['description'])) ?></div>
        </div>
    <?php endif; ?>

    <?php if (! empty($ctx['course_description'])): ?>
        <div class="text-muted lh-base mb-3">
            <?= nl2br(esc($ctx['course_description'])) ?>
        </div>
    <?php elseif (! empty($ctx['course_summary'])): ?>
        <div class="text-muted lh-base mb-3">
            <?= nl2br(esc($ctx['course_summary'])) ?>
        </div>
    <?php else: ?>
        <p class="text-muted mb-3 small">Deskripsi materi kursus belum tersedia.</p>
    <?php endif; ?>

    <?php if (! empty($ctx['course_learning_objectives'])): ?>
        <div class="p-3 bg-light rounded-4 border mb-3">
            <h6 class="small text-uppercase fw-bold text-dark mb-2">
                <i class="bi bi-check2-circle text-success me-1"></i> Tujuan Pembelajaran:
            </h6>
            <div class="text-muted small lh-base"><?= nl2br(esc($ctx['course_learning_objectives'])) ?></div>
        </div>
    <?php endif; ?>

    <?php if (! empty($ctx['course_prerequisites'])): ?>
        <div class="p-3 bg-light rounded-4 border mb-4">
            <h6 class="small text-uppercase fw-bold text-dark mb-2">
                <i class="bi bi-shield-check text-info me-1"></i> Prasyarat Kompetensi:
            </h6>
            <div class="text-muted small lh-base"><?= nl2br(esc($ctx['course_prerequisites'])) ?></div>
        </div>
    <?php endif; ?>

    <h2 class="h5 mb-3 fw-bold d-flex align-items-center gap-2 pt-3 border-top" style="color: #082f5c;">
        <i class="bi bi-signpost-split text-primary"></i> Silabus & Roadmap Pembelajaran
    </h2>

    <!-- Step 1: Pre-Test (Jika Ada) -->
    <?php if ($preTest['exists']): ?>
        <div class="border rounded-4 p-3 mb-3 <?= $preTest['completed'] ? 'bg-success-subtle border-success' : ($stateCode === 'pre_test_required' ? 'border-primary bg-primary-subtle' : 'bg-light') ?>">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2 min-w-0 flex-grow-1">
                    <?php if ($preTest['completed']): ?>
                        <i class="bi bi-check-circle-fill text-success fs-5 flex-shrink-0"></i>
                    <?php else: ?>
                        <i class="bi bi-pencil-square text-primary fs-5 flex-shrink-0"></i>
                    <?php endif; ?>
                    <div class="min-w-0">
                        <strong class="d-block text-dark text-truncate"><?= esc($preTest['quiz']['title'] ?? 'Pre-Test') ?></strong>
                        <span class="text-muted small">Baseline assessment sebelum modul dimulai</span>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <?php if ($preTest['completed']): ?>
                        <span class="badge bg-success text-white rounded-pill px-3 py-2">Selesai (Skor: <?= (float) ($preTest['attempt']['score'] ?? 0) ?>)</span>
                    <?php elseif ($state['enrollmentStatus'] === 'active'): ?>
                        <a href="<?= route_to('workspace.participant.quiz.take', (string) $preTest['quiz']['id']) ?>" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold">Mulai Pre-Test</a>
                    <?php else: ?>
                        <span class="badge bg-secondary text-white rounded-pill px-3 py-2"><i class="bi bi-lock-fill me-1"></i> Terkunci</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Step 2..N: Modul Pembelajaran (Dengan Pagination jika banyak modul) -->
    <?php if (empty($modules)): ?>
        <p class="text-muted mb-0 small">Belum ada modul atau materi yang dipublikasikan pada kelas ini.</p>
    <?php else: ?>
        <div class="modules-accordion ws-paginated-container" data-page-size="5">
            <?php foreach ($modules as $mIndex => $mod): ?>
                <div class="border rounded-4 p-3 mb-3 ws-paginated-item <?= $mod['is_completed'] ? 'border-success' : ($mod['is_unlocked'] ? 'border-primary' : 'bg-light') ?>">
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-2">
                        <div class="d-flex align-items-center gap-2 min-w-0 flex-grow-1">
                            <span class="badge bg-light text-dark border fw-bold rounded-pill flex-shrink-0">Modul <?= esc((string) ($mIndex + 1)) ?></span>
                            <h3 class="h6 mb-0 text-dark fw-bold text-truncate"><?= esc($mod['title']) ?></h3>
                        </div>
                        <div class="flex-shrink-0">
                            <?php if ($mod['is_completed']): ?>
                                <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-3 py-1"><i class="bi bi-check2 me-1"></i> Modul Selesai</span>
                            <?php elseif ($mod['is_unlocked']): ?>
                                <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-1">Sedang Berjalan</span>
                            <?php else: ?>
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-1"><i class="bi bi-lock-fill me-1 text-muted"></i> Terkunci</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Daftar Lessons Dalam Modul -->
                    <?php if (! empty($mod['lessons'])): ?>
                        <ul class="list-group list-group-flush mb-2 border rounded-3 bg-white overflow-hidden">
                            <?php foreach ($mod['lessons'] as $les): ?>
                                <li class="list-group-item d-flex align-items-center justify-content-between gap-2 py-2 px-3">
                                    <div class="d-flex align-items-center gap-2 min-w-0 flex-grow-1">
                                        <?php if ($les['is_completed']): ?>
                                            <i class="bi bi-check-circle-fill text-success flex-shrink-0"></i>
                                        <?php elseif ($les['is_unlocked']): ?>
                                            <i class="bi bi-play-circle-fill text-primary flex-shrink-0"></i>
                                        <?php else: ?>
                                            <i class="bi bi-lock text-muted flex-shrink-0"></i>
                                        <?php endif; ?>
                                        
                                        <?php if ($les['is_unlocked']): ?>
                                            <a href="<?= route_to('workspace.participant.lesson.detail', (string) $les['id']) ?>" class="text-decoration-none fw-medium text-dark text-truncate">
                                                <?= esc($les['title']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted text-truncate"><?= esc($les['title']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-shrink-0 d-flex align-items-center gap-1">
                                        <span class="badge bg-light text-dark border small text-uppercase rounded-pill"><?= esc($les['content_type']) ?></span>
                                        <?php if ($les['is_completed']): ?>
                                            <span class="badge bg-success-subtle text-success-emphasis rounded-pill">Selesai</span>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <!-- Quiz Modul (Jika Configured) -->
                    <?php if (! empty($mod['quiz'])): ?>
                        <div class="p-2 px-3 border rounded-3 bg-white d-flex align-items-center justify-content-between gap-3 mt-2">
                            <div class="d-flex align-items-center gap-2 min-w-0 flex-grow-1">
                                <i class="bi bi-patch-question text-warning fs-5 flex-shrink-0"></i>
                                <div class="min-w-0">
                                    <strong class="small d-block text-dark text-truncate"><?= esc($mod['quiz']['title']) ?></strong>
                                    <span class="text-muted small">Quiz Evaluasi Modul</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <?php if ($mod['quiz']['is_completed']): ?>
                                    <span class="badge bg-success text-white rounded-pill px-3 py-1"><i class="bi bi-check-circle me-1"></i> Skor: <?= (float) ($mod['quiz']['score'] ?? 0) ?></span>
                                <?php elseif ($mod['quiz']['is_unlocked']): ?>
                                    <a href="<?= route_to('workspace.participant.quiz.take', (string) $mod['quiz']['id']) ?>" class="btn btn-warning btn-sm text-dark fw-bold rounded-pill px-3">Kerjakan Quiz</a>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark border rounded-pill px-2 py-1"><i class="bi bi-lock me-1 text-muted"></i> Terkunci</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Assignment Modul (Jika Configured) -->
                    <?php if (! empty($mod['assignment'])): ?>
                        <div class="p-2 px-3 border rounded-3 bg-white d-flex align-items-center justify-content-between gap-3 mt-2">
                            <div class="d-flex align-items-center gap-2 min-w-0 flex-grow-1">
                                <i class="bi bi-file-earmark-check text-info fs-5 flex-shrink-0"></i>
                                <div class="min-w-0">
                                    <strong class="small d-block text-dark text-truncate"><?= esc($mod['assignment']['title']) ?></strong>
                                    <span class="text-muted small">Tugas Mandiri</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <?php if ($mod['assignment']['is_submitted']): ?>
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-3 py-1"><i class="bi bi-check me-1"></i> Terkumpul</span>
                                <?php elseif ($mod['assignment']['is_unlocked']): ?>
                                    <a href="<?= route_to('workspace.participant.assignment.submission', (string) $ctx['id']) ?>?assignment_id=<?= (int) $mod['assignment']['id'] ?>" class="btn btn-outline-primary btn-sm fw-semibold rounded-pill px-3">Submit Tugas</a>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark border rounded-pill px-2 py-1"><i class="bi bi-lock me-1 text-muted"></i> Terkunci</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Step Next: Post-Test (Jika Ada) -->
    <?php if ($postTest['exists']): ?>
        <div class="border rounded-4 p-3 mb-3 <?= $postTest['completed'] ? 'bg-success-subtle border-success' : ($postTest['is_unlocked'] ? 'border-primary bg-primary-subtle' : 'bg-light') ?>">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3 min-w-0 flex-grow-1">
                    <?php if ($postTest['completed']): ?>
                        <i class="bi bi-check-circle-fill text-success fs-5 flex-shrink-0"></i>
                    <?php else: ?>
                        <i class="bi bi-award text-primary fs-5 flex-shrink-0"></i>
                    <?php endif; ?>
                    <div class="min-w-0">
                        <strong class="d-block text-dark text-truncate"><?= esc($postTest['quiz']['title'] ?? 'Post-Test') ?></strong>
                        <span class="text-muted small">Evaluasi akhir kelulusan kelas</span>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <?php if ($postTest['completed']): ?>
                        <span class="badge bg-success text-white rounded-pill px-3 py-2">Selesai (Skor: <?= (float) ($postTest['attempt']['score'] ?? 0) ?>)</span>
                    <?php elseif ($postTest['is_unlocked']): ?>
                        <a href="<?= route_to('workspace.participant.quiz.take', (string) $postTest['quiz']['id']) ?>" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold">Mulai Post-Test</a>
                    <?php else: ?>
                        <span class="badge bg-secondary text-white rounded-pill px-3 py-2"><i class="bi bi-lock-fill me-1"></i> Terkunci (Selesaikan Semua Modul)</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Step Final: Kelulusan & Sertifikat -->
    <div class="border rounded-4 p-3 bg-light">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2 min-w-0 flex-grow-1">
                <i class="bi bi-patch-check text-success fs-5 flex-shrink-0"></i>
                <div class="min-w-0">
                    <strong class="d-block text-dark text-truncate">Kelulusan & Sertifikat</strong>
                    <span class="text-muted small">Evaluasi nilai akhir & penerbitan sertifikat resmi</span>
                </div>
            </div>
            <div class="flex-shrink-0">
                <?php if ($stateCode === 'certificate_issued'): ?>
                    <a href="<?= route_to('workspace.participant.certificate.download', (string) $state['certificate']['id']) ?>" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold">
                        <i class="bi bi-download me-1"></i> Unduh Sertifikat
                    </a>
                <?php elseif ($stateCode === 'waiting_certificate'): ?>
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><i class="bi bi-clock me-1"></i> Menunggu Persetujuan Operator</span>
                <?php elseif ($stateCode === 'completed_passed'): ?>
                    <span class="badge bg-success text-white rounded-pill px-3 py-2">Lulus</span>
                <?php else: ?>
                    <span class="badge bg-secondary text-white rounded-pill px-3 py-2"><i class="bi bi-lock-fill me-1"></i> Belum Memenuhi Syarat</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>