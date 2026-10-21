<?php
/**
 * Trainer - Kelas Saya
 * FiStudy UI DNA - Elevated Unified Design
 */
$classes = $classes ?? [];

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

<div class="trainer-classes-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill small fw-semibold">
                        <i class="fas fa-chalkboard-teacher me-1"></i> Penugasan Aktif
                    </span>
                    <span class="badge bg-light text-navy border px-3 py-1 rounded-pill small fw-semibold">
                        Total: <?= count($classes) ?> Kelas
                    </span>
                </div>
                <h1 class="h3 fw-bold text-navy mb-1">Kelas Saya</h1>
                <p class="text-muted mb-0">Daftar kelas pembelajaran yang ditugaskan kepada Anda sebagai pengajar dan instruktur.</p>
            </div>
            
            <?php if (! empty($classes)): ?>
                <!-- Search & Filter Controls -->
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <div class="input-group input-group-sm" style="max-width: 260px;">
                        <span class="input-group-text bg-light border-end-0 rounded-start-pill"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="classSearchInput" class="form-control bg-light border-start-0 rounded-end-pill" placeholder="Cari nama kelas...">
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Alert notifications if any -->
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
        <div class="row g-4 ws-paginated-container" data-page-size="6" id="classListContainer">
            <?php foreach ($classes as $c): ?>
                <div class="col-12 col-lg-6 ws-paginated-item class-searchable-item" data-class-name="<?= esc(strtolower($c['name'] ?? '')) ?>" data-course-title="<?= esc(strtolower($c['course_title'] ?? '')) ?>">
                    <article class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden transition-all bg-light-hover d-flex flex-column">
                        <!-- Top Cover Banner -->
                        <div class="position-relative overflow-hidden bg-light" style="height: 180px;">
                            <img src="<?= $fmtCover($c) ?>" alt="<?= esc($c['name'] ?? 'Cover') ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/resources/course-details-img-1.jpg') ?>';">
                            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(8, 47, 92, 0.4) 0%, rgba(8, 47, 92, 0.8) 100%);"></div>
                            
                            <!-- Badges Overlay -->
                            <div class="position-absolute top-0 start-0 end-0 p-3 d-flex justify-content-between align-items-center">
                                <div class="d-flex flex-wrap gap-1">
                                    <?php $mb = $methodBadge($c['learning_method'] ?? null); ?>
                                    <span class="badge rounded-pill px-3 py-1 small fw-semibold shadow-sm <?= $mb['class'] ?>">
                                        <i class="<?= $mb['icon'] ?> me-1"></i><?= $mb['label'] ?>
                                    </span>
                                    <span class="badge bg-white text-navy rounded-pill px-2 py-1 small fw-bold shadow-sm">
                                        <?= esc($c['code'] ?? 'KELAS') ?>
                                    </span>
                                </div>
                                <?php
                                $cStat = strtolower((string) ($c['class_status'] ?? 'active'));
                                $cBadge = match ($cStat) {
                                    'active', 'ongoing', 'berlangsung', 'aktif' => ['class' => 'bg-success text-white', 'label' => 'Aktif'],
                                    'scheduled', 'open', 'published', 'terbuka' => ['class' => 'bg-primary text-white', 'label' => 'Terbuka'],
                                    'completed', 'closed', 'selesai'            => ['class' => 'bg-secondary text-white', 'label' => 'Selesai'],
                                    default                                     => ['class' => 'bg-warning text-dark', 'label' => 'Draft'],
                                };
                                ?>
                                <span class="badge <?= $cBadge['class'] ?> rounded-pill px-2 py-1 small shadow-sm">
                                    <?= esc($cBadge['label']) ?>
                                </span>
                            </div>

                            <!-- Bottom Overlay: Trainer Role & Level -->
                            <div class="position-absolute bottom-0 start-0 end-0 p-3 d-flex justify-content-between align-items-end text-white">
                                <div>
                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-1 small fw-bold shadow-sm">
                                        <i class="fas fa-id-badge me-1"></i><?= $trainerRoleLabel($c['trainer_role'] ?? 'primary') ?>
                                    </span>
                                    <?php if (! empty($c['course_level'])): ?>
                                        <span class="badge bg-white-50 text-white rounded-pill px-2 py-1 small shadow-sm ms-1">
                                            <?= $levelLabel($c['course_level']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-dark bg-opacity-75 text-white rounded-pill px-2 py-1 small">
                                    PG: <?= esc($c['passing_grade'] ?? '70.00') ?>
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body p-4 d-flex flex-column flex-grow-1">
                            <!-- Title & Course -->
                            <h2 class="h5 fw-bold text-navy mb-2"><?= esc($c['name'] ?? '-') ?></h2>
                            <p class="text-muted small mb-2">
                                <i class="fas fa-book-open text-primary me-1"></i> <?= esc($c['course_title'] ?? '-') ?>
                            </p>
                            <?php if (! empty($c['program_title'])): ?>
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-award text-warning me-1"></i> Program: <?= esc($c['program_title']) ?>
                                </p>
                            <?php endif; ?>

                            <!-- Location or link chip if available -->
                            <?php if (! empty($c['location_or_link'])): ?>
                                <div class="bg-light rounded-3 p-2 px-3 small text-muted mb-2 d-flex align-items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                    <span class="text-truncate"><?= esc($c['location_or_link']) ?></span>
                                </div>
                            <?php endif; ?>

                            <!-- Target Audience chip if available -->
                            <?php if (! empty($c['class_target_audience'])): ?>
                                <div class="small text-muted mb-3 text-truncate" style="font-size: 0.75rem;">
                                    <i class="fas fa-bullseye text-primary me-1"></i> Sasaran: <?= esc($c['class_target_audience']) ?>
                                </div>
                            <?php endif; ?>

                            <!-- Period -->
                            <div class="bg-light rounded-3 p-2 px-3 small text-muted mb-3 d-flex align-items-center gap-2">
                                <i class="far fa-calendar-alt text-primary"></i>
                                <span>Periode: <?= esc(! empty($c['start_at']) ? date('d M Y', strtotime($c['start_at'])) : '-') ?> &ndash; <?= esc(! empty($c['end_at']) ? date('d M Y', strtotime($c['end_at'])) : '-') ?></span>
                            </div>

                            <!-- Quick Stats Row -->
                            <div class="row g-2 text-center mb-3">
                                <div class="col-3">
                                    <div class="border rounded-3 py-2 bg-light">
                                        <small class="text-muted d-block" style="font-size: 0.72rem;">Peserta</small>
                                        <strong class="text-navy"><?= (int) ($c['participant_count'] ?? 0) ?></strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded-3 py-2 bg-light">
                                        <small class="text-muted d-block" style="font-size: 0.72rem;">Modul</small>
                                        <strong class="text-navy"><?= (int) ($c['modules_count'] ?? 0) ?></strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded-3 py-2 bg-light">
                                        <small class="text-muted d-block" style="font-size: 0.72rem;">Ujian</small>
                                        <strong class="text-navy"><?= (int) ($c['quizzes_count'] ?? 0) ?></strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded-3 py-2 bg-light">
                                        <small class="text-muted d-block" style="font-size: 0.72rem;">Tugas</small>
                                        <strong class="text-navy"><?= (int) ($c['assignments_count'] ?? 0) ?></strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar (Contains 'Progres Kelas' anchor) -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center small text-muted mb-1">
                                    <span>Rata-rata Progres Kelas</span>
                                    <span class="fw-semibold text-navy"><?= esc($c['avg_progress'] ?? 0.0) ?>%</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 6px; background: #eef2f7;">
                                    <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: <?= esc($c['avg_progress'] ?? 0.0) ?>%"></div>
                                </div>
                            </div>

                            <!-- Actions (Contains 'Masuk Kelas' anchor) -->
                            <div class="mt-auto d-flex gap-2">
                                <a href="<?= route_to('workspace.trainer.class.detail', $c['class_id']) ?>" class="btn btn-primary rounded-pill px-4 flex-grow-1 fw-semibold shadow-xs">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('classSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const items = document.querySelectorAll('.class-searchable-item');
            items.forEach(function(item) {
                const className = item.getAttribute('data-class-name') || '';
                const courseTitle = item.getAttribute('data-course-title') || '';
                if (className.includes(query) || courseTitle.includes(query)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>