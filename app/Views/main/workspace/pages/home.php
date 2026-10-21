<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('pageStyles') ?>
<style>
    .ws-hero-card {
        background:
            radial-gradient(1200px 600px at 80% -20%, rgba(56, 189, 248, 0.18) 0%, transparent 60%),
            radial-gradient(900px 500px at 10% 110%, rgba(217, 119, 6, 0.22) 0%, transparent 65%),
            linear-gradient(135deg, var(--ws-navy-900, #052244) 0%, var(--ws-navy-700, #082f5c) 55%, var(--ws-blue-600, #0c5aa6) 100%) !important;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 10px 30px rgba(5, 34, 68, 0.18);
    }

    .ws-hero-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.06) 1px, transparent 1px);
        background-size: 20px 20px;
        mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.8), transparent 90%);
        -webkit-mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.8), transparent 90%);
        pointer-events: none;
    }

    .ws-event-date-box {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background: #eff6ff;
        color: #0c5aa6;
        border: 1px solid #bfdbfe;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        line-height: 1.1;
        flex-shrink: 0;
    }

    .ws-event-date-box small {
        font-size: 10px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.04em;
        opacity: 0.85;
    }

    .ws-cert-card {
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #ffffff 0%, #fafcff 100%);
        transition: all 0.2s ease;
    }

    .ws-cert-card:hover {
        border-color: #f59e0b;
        box-shadow: 0 8px 24px rgba(245, 158, 11, 0.12);
        transform: translateY(-2px);
    }

    .text-navy {
        color: #082f5c !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
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
?>
<div class="participant-workspace-home">

    <!-- 1. HERO WELCOME & RESUME LEARNING BANNER (BERITA & EVENT BENCHMARK) -->
    <div class="ws-hero-card p-3 p-md-4 mb-3 text-white position-relative">
        <div class="row align-items-center g-3 g-md-4 position-relative" style="z-index: 2;">
            <div class="col-lg-7">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold" style="font-size: 11px; letter-spacing: 0.3px;">
                        <i class="fas fa-graduation-cap me-1"></i> Ruang Kerja Peserta
                    </span>
                    <span class="badge bg-white text-navy px-2 py-1 rounded-pill small fw-bold shadow-sm">
                        <i class="fas fa-check-circle text-success me-1"></i> Terverifikasi BegawiPengadaan
                    </span>
                </div>
                <h1 class="h4 text-white fw-bold mb-1" style="font-family: var(--fistudy-font, 'Outfit', sans-serif); letter-spacing: -0.02em;">
                    Halo, <?= esc($userName) ?>! 👋
                </h1>
                <p class="text-white-50 mb-0 lh-base" style="max-width: 620px; font-size: 0.82rem;">
                    Pusat aktivitas pembelajaran komprehensif Anda. Pantau kemajuan modul, kerjakan tugas & ujian kompetensi, hadiri agenda workshop, dan raih sertifikat resmi PBJ Pemerintah Provinsi Lampung.
                </p>
            </div>

            <!-- Quick Resume Learning Box -->
            <div class="col-lg-5">
                <?php if (!empty($resumeClass)): ?>
                    <div class="p-2 p-md-3 rounded-3" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.18);">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-warning small fw-bold text-uppercase" style="font-size: 0.72rem;">
                                <i class="fas fa-play-circle me-1"></i> Lanjutkan Belajar
                            </span>
                            <span class="badge bg-success text-white small px-2 py-0.5 rounded-pill">
                                <?= (int) ($resumeClass['progress_percent'] ?? 0) ?>% Tuntas
                            </span>
                        </div>
                        <h3 class="h6 text-white fw-bold text-truncate mb-1" style="font-size: 0.88rem;">
                            <?= esc($resumeClass['course_title'] ?? $resumeClass['class_name'] ?? 'Kelas Pembelajaran') ?>
                        </h3>
                        <p class="small text-white-50 text-truncate mb-2"><?= esc($resumeClass['class_name'] ?? '') ?></p>

                        <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                            <?php $mInfo = $methodBadge($resumeClass['learning_method'] ?? 'online'); ?>
                            <span class="badge bg-white text-navy small px-2 py-0.5 rounded-pill fw-semibold shadow-sm">
                                <i class="<?= $mInfo['icon'] ?> me-1"></i> <?= $mInfo['label'] ?>
                            </span>
                            <?php if (! empty($resumeClass['location_or_link'])): ?>
                                <span class="badge bg-white text-navy small px-2 py-0.5 rounded-pill fw-semibold shadow-sm text-truncate" style="max-width: 200px;">
                                    <i class="fas fa-map-marker-alt me-1"></i> <?= esc($resumeClass['location_or_link']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="progress rounded-pill mb-2" style="height: 6px; background: rgba(255, 255, 255, 0.2);">
                            <div class="progress-bar bg-warning rounded-pill" role="progressbar" style="width: <?= (int) ($resumeClass['progress_percent'] ?? 0) ?>%"></div>
                        </div>

                        <a href="<?= route_to('workspace.participant.study', $resumeClass['class_id']) ?>" class="btn btn-warning text-dark fw-bold btn-sm w-100 rounded-pill py-1.5 shadow-sm">
                            <i class="fas fa-arrow-right me-1"></i> Buka Ruang Belajar Kelas
                        </a>
                    </div>
                <?php else: ?>
                    <div class="p-3 rounded-3 text-center" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.15);">
                        <i class="fas fa-compass fa-2x text-warning mb-2"></i>
                        <h3 class="h6 text-white fw-bold mb-1">Mulai Perjalanan Belajar Anda</h3>
                        <p class="small text-white-50 mb-2">Pilih kelas dan kembangkan kompetensi pengadaan barang dan jasa sekarang.</p>
                        <a href="<?= site_url('arena-belajar/kelas') ?>" class="btn btn-warning text-dark fw-bold btn-sm rounded-pill px-3 py-1.5">
                            <i class="fas fa-search me-1"></i> Jelajahi Kelas Pembelajaran
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 2. 4 SUMMARY KPI STAT CARDS -->
    <?php if (!empty($kpiCards)): ?>
        <div class="row g-2 g-md-3 mb-3">
            <?php foreach ($kpiCards as $stat): ?>
                <div class="col-6 col-lg-3">
                    <div class="card ws-stat-card border-0 shadow-sm p-2 p-md-3 h-100 bg-white">
                        <div class="d-flex align-items-center gap-2 gap-md-3">
                            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; background: #eff6ff; color: #082f5c; font-size: 1.1rem;">
                                <i class="<?= esc($stat['icon'] ?? 'fas fa-chart-bar') ?>"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="text-muted small fw-medium d-block text-truncate" style="font-size: 0.72rem;"><?= esc($stat['title']) ?></span>
                                <div class="h5 fw-bold text-navy mb-0" style="font-size: 1.1rem;"><?= esc($stat['value']) ?></div>
                                <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;"><?= esc($stat['meta']) ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- 3. PEMBELAJARAN SEDANG BERJALAN -->
    <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
            <div>
                <h2 class="h5 fw-bold text-navy mb-0">
                    <i class="fas fa-book-reader text-primary me-2"></i>Pembelajaran Sedang Berjalan
                </h2>
                <small class="text-muted">Kelas yang Anda ikuti dan kelanjutan proses belajarnya</small>
            </div>
            <a href="<?= route_to('workspace.participant.classes') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                Lihat Semua Kelas (<?= count($recentClasses) ?>)
            </a>
        </div>

        <?php if (!empty($recentClasses)): ?>
            <div class="row g-3 ws-paginated-container" data-page-size="4">
                <?php foreach ($recentClasses as $cl): ?>
                    <div class="col-md-6 col-lg-3 ws-paginated-item">
                        <div class="ws-class-card h-100 d-flex flex-column justify-content-between overflow-hidden">
                            <div>
                                <div class="position-relative" style="height: 125px; overflow: hidden; background: #082f5c;">
                                    <img src="<?= $fmtCover($cl) ?>" alt="<?= esc($cl['course_title'] ?? 'Cover') ?>" class="w-100 h-100 object-fit-cover opacity-90" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/resources/course-details-img-1.jpg') ?>';">
                                    <div class="position-absolute top-0 start-0 m-2">
                                        <span class="badge bg-dark bg-opacity-75 text-white fw-bold small rounded-pill px-2 py-1">
                                            <?= esc($cl['class_code'] ?? 'PBJ') ?>
                                        </span>
                                    </div>
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <?php $mInfo = $methodBadge($cl['learning_method'] ?? 'online'); ?>
                                        <span class="badge bg-white text-dark small rounded-pill px-2 py-1 fw-semibold shadow-sm">
                                            <i class="<?= $mInfo['icon'] ?> me-1 text-primary"></i> <?= $mInfo['label'] ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="p-3">
                                    <h3 class="h6 fw-bold text-navy mb-1" style="min-height: 2.4rem; line-height: 1.3;">
                                        <?= esc($cl['course_title'] ?? $cl['class_name'] ?? 'Kelas Pembelajaran') ?>
                                    </h3>
                                    <p class="small text-muted mb-2 text-truncate"><?= esc($cl['class_name'] ?? '') ?></p>

                                    <?php if (! empty($cl['location_or_link'])): ?>
                                        <div class="text-muted small mb-2 text-truncate" style="font-size: 0.72rem;">
                                            <i class="fas fa-map-marker-alt text-danger me-1"></i> <?= esc($cl['location_or_link']) ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-flex flex-wrap gap-2 text-muted small mb-2" style="font-size: 0.72rem;">
                                        <span><i class="fas fa-layer-group me-1 text-primary"></i> <?= (int) ($cl['module_count'] ?? 0) ?> Modul</span>
                                        <span>&middot;</span>
                                        <span><i class="fas fa-check-circle me-1 text-success"></i> KKM <?= (int) ($cl['passing_grade'] ?? 70) ?>%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 pt-0">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted" style="font-size: 0.72rem;">Progres</small>
                                    <span class="badge <?= ($cl['enrollment_status'] ?? '') === 'completed' ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' ?> small rounded-pill">
                                        <?= (int) ($cl['progress_percent'] ?? 0) ?>% Selesai
                                    </span>
                                </div>
                                <div class="progress rounded-pill mb-3" style="height: 6px; background: #eef2f7;">
                                    <div class="progress-bar bg-warning rounded-pill" role="progressbar" style="width: <?= (int) ($cl['progress_percent'] ?? 0) ?>%"></div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="<?= route_to('workspace.participant.study', $cl['class_id']) ?>" class="btn btn-primary btn-sm flex-grow-1 rounded-pill fw-semibold">
                                        Belajar <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                    <a href="<?= route_to('workspace.participant.class.detail', $cl['class_id']) ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-2" title="Detail Silabus">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-graduation-cap fs-1 mb-3 text-secondary opacity-50"></i>
                <p class="mb-2 fw-medium">Belum ada kelas pembelajaran aktif saat ini.</p>
                <a href="<?= site_url('arena-belajar/kelas') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-4">
                    <i class="fas fa-search me-1"></i> Jelajahi Kelas Pembelajaran
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- 3.5 KALENDER PEMBELAJARAN & AGENDA PBJ INTERAKTIF -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
            <div>
                <h2 class="h5 fw-bold text-navy mb-0">
                    <i class="fas fa-calendar-alt text-primary me-2"></i>Kalender Pembelajaran & Agenda PBJ
                </h2>
                <small class="text-muted">Pantau tenggat kelas, batas tugas & kuis, serta jadwal event workshop PBJ Anda</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1" id="wsCalTodayBtn">
                    <i class="fas fa-calendar-day me-1"></i> Hari Ini
                </button>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-start-pill px-2 py-1" id="wsCalPrevBtn" title="Bulan Sebelumnya">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="btn btn-light btn-sm px-3 py-1 fw-bold text-navy border-top border-bottom" id="wsCalMonthYearLabel" style="min-width: 150px; pointer-events: none;">
                        Oktober 2026
                    </span>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-end-pill px-2 py-1" id="wsCalNextBtn" title="Bulan Berikutnya">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Calendar Grid Column -->
            <div class="col-lg-8">
                <!-- Day Headers (Min - Sab) -->
                <div class="ws-calendar-grid mb-1">
                    <div class="ws-calendar-day-header text-danger">Min</div>
                    <div class="ws-calendar-day-header">Sen</div>
                    <div class="ws-calendar-day-header">Sel</div>
                    <div class="ws-calendar-day-header">Rab</div>
                    <div class="ws-calendar-day-header">Kam</div>
                    <div class="ws-calendar-day-header">Jum</div>
                    <div class="ws-calendar-day-header text-primary">Sab</div>
                </div>

                <!-- Days Cells Grid -->
                <div class="ws-calendar-grid" id="wsCalCellsContainer">
                    <!-- Populated dynamically via JS -->
                </div>

                <!-- Calendar Legend -->
                <div class="d-flex flex-wrap align-items-center gap-3 mt-3 pt-2 border-top">
                    <span class="small text-muted fw-semibold">Keterangan:</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 small">
                        <span class="ws-cal-dot dot-class me-1"></span> Batas Akhir Kelas
                    </span>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1 small">
                        <span class="ws-cal-dot dot-task me-1"></span> Batas Akhir Tugas
                    </span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                        <span class="ws-cal-dot dot-event me-1"></span> Agenda Event / Pelatihan
                    </span>
                </div>
            </div>

            <!-- Day Agenda List Column -->
            <div class="col-lg-4">
                <div class="ws-cal-side-agenda d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <span class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Agenda Tanggal</span>
                            <h3 class="h6 fw-bold text-navy mb-0" id="wsCalSelectedDateLabel">-</h3>
                        </div>
                        <span class="badge bg-navy text-white rounded-pill px-2 py-1" id="wsCalAgendaCountBadge">0 Agenda</span>
                    </div>

                    <div class="flex-grow-1 overflow-auto" id="wsCalAgendaItemsList" style="max-height: 320px;">
                        <!-- Items rendered dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. 2 KOLOM: TUGAS & ASSESSMENT vs EVENT & AGENDA -->
    <div class="row g-4 mb-4">
        <!-- Kolom Kiri: Tugas & Evaluasi Pembelajaran -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                    <div>
                        <h2 class="h5 fw-bold text-navy mb-0">
                            <i class="fas fa-tasks text-warning me-2"></i>Tugas & Assessment
                        </h2>
                        <small class="text-muted">Aktivitas evaluasi dan ujian pembelajaran</small>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="<?= route_to('workspace.participant.all-assignments') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1 small">
                            Tugas
                        </a>
                        <a href="<?= route_to('workspace.participant.all-assessments') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1 small">
                            Assessment
                        </a>
                    </div>
                </div>

                <!-- List Tugas -->
                <?php if (!empty($recentTasks)): ?>
                    <div class="mb-3">
                        <span class="text-muted small fw-bold text-uppercase d-block mb-2" style="font-size: 0.72rem;">
                            <i class="fas fa-clipboard-list text-primary me-1"></i> Tugas Pembelajaran
                        </span>
                        <div class="list-group list-group-flush ws-paginated-container" data-page-size="4">
                            <?php foreach ($recentTasks as $task): ?>
                                <div class="list-group-item px-0 py-2 border-bottom ws-paginated-item">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <strong class="small text-truncate text-navy" style="max-width: 70%;"><?= esc($task['title']) ?></strong>
                                        <?php if (($task['submission_status'] ?? '') === 'graded'): ?>
                                            <span class="badge bg-success-subtle text-success small rounded-pill">Nilai: <?= (int) ($task['submission_score'] ?? 0) ?></span>
                                        <?php elseif (($task['submission_status'] ?? '') === 'submitted' || ($task['submission_status'] ?? '') === 'in_review'): ?>
                                            <span class="badge bg-info-subtle text-info small rounded-pill">Dalam Review</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning-emphasis small rounded-pill">Belum Dikirim</span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted d-block mb-1" style="font-size: 0.75rem;"><?= esc($task['class_name']) ?></small>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-danger small" style="font-size: 0.75rem;">
                                            <i class="far fa-clock me-1"></i> Batas: <?= esc(!empty($task['due_at']) ? date('d/m/Y H:i', strtotime($task['due_at'])) : 'Tidak ada batas') ?>
                                        </small>
                                        <a href="<?= route_to('workspace.participant.assignments', $task['class_id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                                            <?= ($task['submission_status'] ?? '') === 'graded' ? 'Lihat Nilai' : 'Kerjakan' ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- List Kuis / Assessment -->
                <?php if (!empty($recentQuizzes)): ?>
                    <div>
                        <span class="text-muted small fw-bold text-uppercase d-block mb-2" style="font-size: 0.72rem;">
                            <i class="fas fa-check-double text-success me-1"></i> Ujian & Kuis Modul
                        </span>
                        <div class="list-group list-group-flush ws-paginated-container" data-page-size="4">
                            <?php foreach ($recentQuizzes as $qz): ?>
                                <div class="list-group-item px-0 py-2 border-bottom ws-paginated-item">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <strong class="small text-truncate text-navy" style="max-width: 70%;"><?= esc($qz['title']) ?></strong>
                                        <span class="badge bg-secondary-subtle text-secondary small rounded-pill"><?= esc(strtoupper($qz['type'] ?? 'QUIZ')) ?></span>
                                    </div>
                                    <small class="text-muted d-block mb-1" style="font-size: 0.75rem;"><?= esc($qz['class_name']) ?></small>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted small" style="font-size: 0.75rem;">
                                            <i class="fas fa-check-circle me-1 text-success"></i> Standar Kelulusan: <?= (int) ($qz['passing_grade'] ?? 70) ?>%
                                            <?php if (isset($qz['highest_score']) && $qz['highest_score'] !== null): ?>
                                                &middot; <strong class="text-success">Skor: <?= (int) $qz['highest_score'] ?></strong>
                                            <?php endif; ?>
                                        </small>
                                        <a href="<?= route_to('workspace.participant.quiz', $qz['class_id']) ?>" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                                            Ikuti Ujian
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (empty($recentTasks) && empty($recentQuizzes)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-clipboard-check fs-2 mb-2 text-success opacity-50"></i>
                        <p class="mb-0 small fw-medium">Semua tugas dan kuis saat ini telah selesai dikerjakan!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kolom Kanan: Event & Agenda Saya -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                    <div>
                        <h2 class="h5 fw-bold text-navy mb-0">
                            <i class="fas fa-calendar-check text-info me-2"></i>Event & Agenda Terdaftar
                        </h2>
                        <small class="text-muted">Kegiatan pelatihan dan seminar yang diikuti</small>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="<?= route_to('workspace.participant.events') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1 small">
                            Semua Event
                        </a>
                        <a href="<?= route_to('workspace.participant.attendance') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 small">
                            Presensi
                        </a>
                    </div>
                </div>

                <?php if (!empty($myEvents)): ?>
                    <div class="list-group list-group-flush ws-paginated-container" data-page-size="3">
                        <?php foreach ($myEvents as $ev): ?>
                            <div class="list-group-item px-0 py-3 border-bottom d-flex align-items-start gap-3 ws-paginated-item">
                                <div class="ws-event-date-box">
                                    <span class="fs-5"><?= esc(date('d', strtotime($ev['start_at'] ?? 'now'))) ?></span>
                                    <small><?= esc(date('M', strtotime($ev['start_at'] ?? 'now'))) ?></small>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <strong class="small text-navy text-truncate" style="max-width: 75%;">
                                            <?= esc($ev['event_title'] ?? 'Event BegawiPengadaan') ?>
                                        </strong>
                                        <span class="badge <?= ($ev['attendance_status'] ?? '') === 'present' ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' ?> small rounded-pill">
                                            <?= ($ev['attendance_status'] ?? '') === 'present' ? 'Hadir' : 'Terdaftar' ?>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 text-muted small mb-2" style="font-size: 0.75rem;">
                                        <span><i class="fas fa-map-marker-alt me-1 text-danger"></i> <?= esc($ev['delivery_mode'] ?? $ev['location'] ?? 'Online') ?></span>
                                        <span>&middot;</span>
                                        <span><i class="far fa-clock me-1 text-primary"></i> <?= esc(date('H:i', strtotime($ev['start_at'] ?? 'now'))) ?> WIB</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted small" style="font-size: 0.75rem;">
                                            Presensi: <strong><?= ($ev['attendance_status'] ?? '') === 'present' ? 'Tercatat Hadir' : 'Belum Presensi' ?></strong>
                                        </small>
                                        <a href="<?= route_to('workspace.participant.attendance') ?>" class="btn btn-sm btn-link text-primary p-0 fw-semibold" style="font-size: 0.75rem;">
                                            Catat Kehadiran <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-calendar-times fs-2 mb-2 text-secondary opacity-50"></i>
                        <p class="mb-2 small fw-medium">Belum ada agenda event yang Anda daftarkan.</p>
                        <a href="<?= site_url('event') ?>" class="btn btn-sm btn-outline-info rounded-pill px-3">
                            <i class="fas fa-compass me-1"></i> Eksplorasi Event PBJ
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 5. SERTIFIKAT KELULUSAN & CAPAIAN KOMPETENSI RESMI -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom flex-wrap gap-2">
            <div>
                <h2 class="h5 fw-bold text-navy mb-0">
                    <i class="fas fa-award text-success me-2"></i>Sertifikat Kelulusan & Kompetensi Saya
                </h2>
                <small class="text-muted">Bukti capaian kompetensi resmi yang diterbitkan oleh BegawiPengadaan</small>
            </div>
            <a href="<?= route_to('workspace.participant.certificates') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                Lihat Semua Sertifikat
            </a>
        </div>

        <?php if (!empty($myCertificates)): ?>
            <div class="row g-3 ws-paginated-container" data-page-size="3">
                <?php foreach ($myCertificates as $cert): ?>
                    <div class="col-md-6 col-lg-4 ws-paginated-item">
                        <div class="ws-cert-card p-4 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-success-subtle text-success small rounded-pill px-3 py-1 fw-bold">
                                        <i class="fas fa-shield-alt me-1"></i> Resmi Terverifikasi
                                    </span>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <?= esc(date('d M Y', strtotime($cert['issued_at'] ?? 'now'))) ?>
                                    </small>
                                </div>
                                <h3 class="h6 fw-bold text-navy mb-1" style="min-height: 2.2rem;">
                                    <?= esc($cert['title'] ?? 'Sertifikat Kompetensi PBJ') ?>
                                </h3>
                                <p class="small text-muted mb-2 text-truncate">
                                    <?= esc($cert['class_name'] ?? $cert['event_title'] ?? 'Program Pembelajaran PBJ') ?>
                                </p>
                                <p class="small text-muted font-monospace mb-3 p-2 bg-light rounded-3" style="font-size: 0.75rem;">
                                    <i class="fas fa-barcode me-1 text-secondary"></i> <?= esc($cert['certificate_number'] ?? '-') ?>
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="<?= route_to('workspace.participant.certificate.detail', $cert['id']) ?>" class="btn btn-outline-primary btn-sm rounded-pill flex-grow-1 fw-semibold">
                                    <i class="fas fa-eye me-1"></i> Rincian
                                </a>
                                <a href="<?= route_to('workspace.participant.certificate.download', $cert['id']) ?>" class="btn btn-primary btn-sm rounded-pill flex-grow-1 fw-semibold">
                                    <i class="fas fa-download me-1"></i> Unduh PDF
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-4 text-muted">
                <i class="fas fa-award fs-2 mb-2 text-warning opacity-50"></i>
                <p class="mb-0 small">Sertifikat akan otomatis diterbitkan setelah Anda menyelesaikan seluruh modul dan lulus evaluasi kelas atau menghadiri event resmi.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- 6. CATATAN AKTIVITAS TERAKHIR SAYA -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
            <div>
                <h2 class="h5 fw-bold text-navy mb-0">
                    <i class="fas fa-history text-secondary me-2"></i>Aktivitas Terakhir Saya
                </h2>
                <small class="text-muted">Catatan jejak aktivitas pembelajaran dan interaksi Workspace Anda</small>
            </div>
            <a href="<?= route_to('workspace.activity') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                Riwayat Lengkap
            </a>
        </div>

        <?php if (!empty($recentActivities)): ?>
            <div class="table-responsive ws-paginated-container" data-page-size="5">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-uppercase text-muted">Aksi</th>
                            <th class="small text-uppercase text-muted">Modul / Objek</th>
                            <th class="small text-uppercase text-muted">Status</th>
                            <th class="small text-uppercase text-muted">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentActivities as $act): ?>
                            <tr class="ws-paginated-item">
                                <td class="fw-semibold text-navy">
                                    <?php
                                    $actionLabel = match ($act['action'] ?? '') {
                                        'login'               => 'Masuk ke Sistem',
                                        'enroll'              => 'Mendaftar Kelas Pembelajaran',
                                        'complete_lesson'     => 'Menyelesaikan Materi Pembelajaran',
                                        'submit_assignment'   => 'Mengumpulkan Tugas',
                                        'submit_quiz'         => 'Menyelesaikan Ujian / Kuis',
                                        'register_event'      => 'Mendaftar Event / Workshop',
                                        'submit_attendance'   => 'Mencatat Presensi Kehadiran',
                                        'issue_certificate'   => 'Penerbitan Sertifikat',
                                        default               => ucfirst(str_replace('_', ' ', (string) ($act['action'] ?? 'Aktivitas')))
                                    };
                                    ?>
                                    <?= esc($actionLabel) ?>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        <?= esc($act['resource_type'] ? ucfirst($act['resource_type']) . ' #' . $act['resource_id'] : '-') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= ($act['result'] ?? '') === 'success' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?> rounded-pill">
                                        <?= esc($act['result'] ?? 'selesai') ?>
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    <i class="far fa-clock me-1 text-muted"></i>
                                    <?= esc(date('d/m/Y H:i', strtotime($act['created_at']))) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-4 text-muted">
                <p class="mb-0 small">Belum ada catatan aktivitas baru yang tercatat.</p>
            </div>
        <?php endif; ?>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script id="wsCalendarData" type="application/json"><?= json_encode($calendarEvents ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dataEl = document.getElementById('wsCalendarData');
    if (!dataEl) return;

    let events = [];
    try {
        events = JSON.parse(dataEl.textContent || '[]');
    } catch (e) {
        events = [];
    }

    // Group events by YYYY-MM-DD
    const eventsByDate = {};
    events.forEach(function (ev) {
        if (!ev.date) return;
        if (!eventsByDate[ev.date]) {
            eventsByDate[ev.date] = [];
        }
        eventsByDate[ev.date].push(ev);
    });

    const monthNames = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    const today = new Date();
    let currentYear = today.getFullYear();
    let currentMonth = today.getMonth(); // 0-indexed

    function formatDateStr(y, m, d) {
        const mm = String(m + 1).padStart(2, '0');
        const dd = String(d).padStart(2, '0');
        return `${y}-${mm}-${dd}`;
    }

    const todayStr = formatDateStr(today.getFullYear(), today.getMonth(), today.getDate());
    let selectedDateStr = todayStr;

    // Check if any event exists this month; if not, and events exist elsewhere, navigate to closest
    const allDates = Object.keys(eventsByDate).sort();
    if (allDates.length > 0) {
        const hasCurrentMonthEvents = allDates.some(k => k.startsWith(`${currentYear}-${String(currentMonth + 1).padStart(2, '0')}`));
        if (!hasCurrentMonthEvents) {
            const firstEvDate = allDates[0];
            const parts = firstEvDate.split('-');
            if (parts.length === 3) {
                currentYear = parseInt(parts[0], 10);
                currentMonth = parseInt(parts[1], 10) - 1;
                selectedDateStr = firstEvDate;
            }
        }
    }

    const cellsContainer = document.getElementById('wsCalCellsContainer');
    const monthYearLabel = document.getElementById('wsCalMonthYearLabel');
    const selectedDateLabel = document.getElementById('wsCalSelectedDateLabel');
    const agendaCountBadge = document.getElementById('wsCalAgendaCountBadge');
    const agendaItemsList = document.getElementById('wsCalAgendaItemsList');
    const prevBtn = document.getElementById('wsCalPrevBtn');
    const nextBtn = document.getElementById('wsCalNextBtn');
    const todayBtn = document.getElementById('wsCalTodayBtn');

    function renderCalendar() {
        if (!cellsContainer || !monthYearLabel) return;
        monthYearLabel.textContent = `${monthNames[currentMonth]} ${currentYear}`;
        cellsContainer.innerHTML = '';

        const firstDayOfMonth = new Date(currentYear, currentMonth, 1).getDay(); // 0 (Sun) - 6 (Sat)
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const daysInPrevMonth = new Date(currentYear, currentMonth, 0).getDate();

        // 1. Prev month trailing days
        for (let i = firstDayOfMonth - 1; i >= 0; i--) {
            const dayNum = daysInPrevMonth - i;
            const prevMonthDate = new Date(currentYear, currentMonth - 1, dayNum);
            const dateStr = formatDateStr(prevMonthDate.getFullYear(), prevMonthDate.getMonth(), dayNum);
            renderCell(dayNum, dateStr, true);
        }

        // 2. Current month days
        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = formatDateStr(currentYear, currentMonth, day);
            renderCell(day, dateStr, false);
        }

        // 3. Next month leading days to complete grid
        const totalRendered = firstDayOfMonth + daysInMonth;
        const remaining = (7 - (totalRendered % 7)) % 7;
        for (let day = 1; day <= remaining; day++) {
            const nextMonthDate = new Date(currentYear, currentMonth + 1, day);
            const dateStr = formatDateStr(nextMonthDate.getFullYear(), nextMonthDate.getMonth(), day);
            renderCell(day, dateStr, true);
        }

        renderAgendaList(selectedDateStr);
    }

    function renderCell(dayNum, dateStr, isOtherMonth) {
        const cell = document.createElement('div');
        cell.className = 'ws-calendar-cell';
        if (isOtherMonth) cell.classList.add('is-other-month');
        if (dateStr === todayStr) cell.classList.add('is-today');
        if (dateStr === selectedDateStr) cell.classList.add('is-selected');

        const topWrap = document.createElement('div');
        topWrap.className = 'd-flex justify-content-between align-items-center';

        const numEl = document.createElement('span');
        numEl.className = 'ws-cal-date-num';
        numEl.textContent = dayNum;
        topWrap.appendChild(numEl);

        const dayEvents = eventsByDate[dateStr] || [];
        if (dayEvents.length > 0) {
            const countBadge = document.createElement('span');
            countBadge.className = 'badge bg-navy text-white rounded-pill px-1';
            countBadge.style.fontSize = '9px';
            countBadge.textContent = dayEvents.length;
            topWrap.appendChild(countBadge);
        }

        cell.appendChild(topWrap);

        // Dots wrapper
        const dotsWrap = document.createElement('div');
        dotsWrap.className = 'ws-cal-dots-wrap';
        dayEvents.slice(0, 3).forEach(function (ev) {
            const dot = document.createElement('span');
            dot.className = 'ws-cal-dot ' + (
                ev.type === 'class' ? 'dot-class' :
                ev.type === 'task' ? 'dot-task' : 'dot-event'
            );
            dot.title = ev.title;
            dotsWrap.appendChild(dot);
        });
        cell.appendChild(dotsWrap);

        cell.addEventListener('click', function () {
            selectedDateStr = dateStr;
            document.querySelectorAll('.ws-calendar-cell').forEach(c => c.classList.remove('is-selected'));
            cell.classList.add('is-selected');
            renderAgendaList(dateStr);
        });

        cellsContainer.appendChild(cell);
    }

    function renderAgendaList(dateStr) {
        if (!selectedDateLabel || !agendaItemsList) return;

        const parts = dateStr.split('-');
        if (parts.length === 3) {
            const y = parseInt(parts[0], 10);
            const m = parseInt(parts[1], 10) - 1;
            const d = parseInt(parts[2], 10);
            const dObj = new Date(y, m, d);
            const dayName = dayNames[dObj.getDay()] || '';
            selectedDateLabel.textContent = `${dayName}, ${d} ${monthNames[m]} ${y}`;
        } else {
            selectedDateLabel.textContent = dateStr;
        }

        const dayEvents = eventsByDate[dateStr] || [];
        if (agendaCountBadge) {
            agendaCountBadge.textContent = `${dayEvents.length} Agenda`;
        }

        agendaItemsList.innerHTML = '';
        if (dayEvents.length === 0) {
            agendaItemsList.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="far fa-calendar-check fs-2 mb-2 text-secondary opacity-50"></i>
                    <p class="small mb-0">Tidak ada agenda kegiatan pada tanggal ini.</p>
                </div>
            `;
            return;
        }

        dayEvents.forEach(function (ev) {
            const badgeClass = ev.type === 'class' ? 'bg-primary-subtle text-primary border-primary-subtle' :
                               (ev.type === 'task' ? 'bg-danger-subtle text-danger border-danger-subtle' : 'bg-success-subtle text-success border-success-subtle');
            const iconClass = ev.type === 'class' ? 'fas fa-graduation-cap' :
                              (ev.type === 'task' ? 'fas fa-clipboard-check' : 'fas fa-calendar-alt');
            const ctaText = ev.type === 'class' ? 'Buka Kelas' :
                            (ev.type === 'task' ? 'Kerjakan' : 'Detail Acara');

            const itemEl = document.createElement('div');
            itemEl.className = 'ws-cal-agenda-item';
            itemEl.innerHTML = `
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <span class="badge ${badgeClass} border rounded-pill px-2 py-0.5 small" style="font-size: 0.7rem;">
                        <i class="${iconClass} me-1"></i> ${ev.badge || 'Agenda'}
                    </span>
                    <span class="small text-muted fw-semibold" style="font-size: 0.75rem;">
                        <i class="far fa-clock me-1"></i> ${ev.time || '-'} WIB
                    </span>
                </div>
                <h4 class="h6 fw-bold text-navy mb-1" style="font-size: 0.85rem; line-height: 1.3;">
                    ${escapeHtml(ev.title)}
                </h4>
                ${ev.meta ? `<p class="small text-muted mb-2 text-truncate" style="font-size: 0.75rem;">${escapeHtml(ev.meta)}</p>` : ''}
                <div class="text-end">
                    <a href="${ev.url || '#'}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-0.5 small" style="font-size: 0.75rem;">
                        ${ctaText} <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            `;
            agendaItemsList.appendChild(itemEl);
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar();
        });
    }

    if (todayBtn) {
        todayBtn.addEventListener('click', function () {
            currentYear = today.getFullYear();
            currentMonth = today.getMonth();
            selectedDateStr = todayStr;
            renderCalendar();
        });
    }

    renderCalendar();
});
</script>
<?= $this->endSection() ?>
