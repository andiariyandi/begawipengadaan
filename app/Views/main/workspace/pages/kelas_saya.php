<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?php
$enrollments      = $enrollments ?? [];
$activeClasses    = $activeClasses ?? [];
$completedClasses = $completedClasses ?? [];
$pendingClasses   = $pendingClasses ?? [];
$summary          = $summary ?? ['pending' => 0, 'active' => 0, 'completed' => 0, 'cancelled' => 0, 'withdrawn' => 0, 'issued_certificates' => 0];
$fmt              = static fn(int $n): string => number_format($n, 0, ',', '.');

$statusBadge = static function (string $status): array {
    return match ($status) {
        'active'    => ['label' => 'Aktif',            'class' => 'bg-success-subtle text-success-emphasis border-success-subtle'],
        'completed' => ['label' => 'Selesai',          'class' => 'bg-primary-subtle text-primary-emphasis border-primary-subtle'],
        'pending'   => ['label' => 'Menunggu Approval', 'class' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle'],
        'withdrawn' => ['label' => 'Dibatalkan',       'class' => 'bg-secondary-subtle text-secondary-emphasis border-secondary-subtle'],
        default     => ['label' => 'Ditolak',          'class' => 'bg-danger-subtle text-danger-emphasis border-danger-subtle'],
    };
};

$levelLabel = static function (?string $level): string {
    return match (strtolower((string) $level)) {
        'basic', '1'         => 'Tingkat Dasar',
        'intermediate', '2'  => 'Tingkat Menengah',
        'advanced', '3'      => 'Tingkat Lanjutan',
        default              => 'Umum',
    };
};

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

$trainerRoleLabel = static function (?string $role): string {
    return match (strtolower((string) $role)) {
        'co_trainer' => 'Co-Trainer',
        'guest'      => 'Narasumber Tamu',
        default      => 'Pengajar Utama',
    };
};
?>

<!-- 1. Header Section -->
<div class="ws-page-header">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="ws-eyebrow">
                <i class="fas fa-graduation-cap"></i> <?= esc($sectionContent['eyebrow'] ?? 'Arena Belajar & Kompetensi') ?>
            </span>
            <h1><?= esc($pageTitle ?? 'Pembelajaran & Kelas Saya') ?></h1>
            <p><?= esc($sectionContent['description'] ?? 'Pantau progres belajar, akses ruang kelas interaktif, evaluasi materi & kuis, hingga perolehan sertifikat kelulusan resmi Anda.') ?></p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= site_url('arena-belajar/kelas') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="fas fa-search me-1"></i> Cari Kelas Baru
            </a>
            <a href="<?= route_to('workspace.participant.certificates') ?>" class="btn btn-outline-success btn-sm rounded-pill px-3">
                <i class="fas fa-award me-1"></i> Sertifikat Saya
            </a>
            <a href="<?= route_to('workspace.participant.all-assignments') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-tasks me-1"></i> Tugas Saya
            </a>
        </div>
    </div>
</div>

<!-- 2. KPI Summary Cards -->
<div class="row g-4 mb-4">
    <?php if (! empty($sectionContent['cards'])): ?>
        <?php foreach ($sectionContent['cards'] as $card): ?>
            <div class="col-sm-6 col-lg-3">
                <div class="card ws-stat-card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold text-uppercase"><?= esc($card['title']) ?></span>
                        <?php if (! empty($card['icon'])): ?>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(8,47,92,0.06); color: #082f5c;">
                                <i class="<?= esc($card['icon']) ?>"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="display-6 fw-bold my-2" style="color: #082f5c;"><?= esc($card['value']) ?></div>
                    <small class="text-muted"><?= esc($card['meta'] ?? '') ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-sm-6 col-lg-3">
            <div class="card ws-stat-card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <span class="text-muted small fw-semibold text-uppercase">Kelas Aktif</span>
                <div class="display-6 fw-bold my-2" style="color: #082f5c;"><?= $fmt((int) ($summary['active'] ?? 0)) ?></div>
                <small class="text-muted">sedang dipelajari</small>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card ws-stat-card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <span class="text-muted small fw-semibold text-uppercase">Kelas Selesai</span>
                <div class="display-6 fw-bold my-2" style="color: #082f5c;"><?= $fmt((int) ($summary['completed'] ?? 0)) ?></div>
                <small class="text-muted">tuntas & lulus</small>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card ws-stat-card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <span class="text-muted small fw-semibold text-uppercase">Sertifikat Terbit</span>
                <div class="display-6 fw-bold my-2" style="color: #082f5c;"><?= $fmt((int) ($summary['issued_certificates'] ?? 0)) ?></div>
                <small class="text-muted">resmi terverifikasi</small>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card ws-stat-card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <span class="text-muted small fw-semibold text-uppercase">Menunggu Approval</span>
                <div class="display-6 fw-bold my-2" style="color: #082f5c;"><?= $fmt((int) ($summary['pending'] ?? 0)) ?></div>
                <small class="text-muted">antrean verifikasi</small>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- 3. Nav Tabs: 4 Fungsi Utama Siklus Belajar -->
<div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
    <ul class="nav nav-pills gap-2 flex-wrap" id="kelasTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 py-2 small fw-semibold" id="tab-aktif-btn" data-bs-toggle="pill" data-bs-target="#tab-aktif" type="button" role="tab" aria-controls="tab-aktif" aria-selected="true">
                <i class="fas fa-book-reader me-1"></i> 1. Kelas Aktif & Berjalan (<?= count($activeClasses) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2 small fw-semibold" id="tab-selesai-btn" data-bs-toggle="pill" data-bs-target="#tab-selesai" type="button" role="tab" aria-controls="tab-selesai" aria-selected="false">
                <i class="fas fa-graduation-cap me-1"></i> 2. Kelas Selesai & Sertifikat (<?= count($completedClasses) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2 small fw-semibold" id="tab-pending-btn" data-bs-toggle="pill" data-bs-target="#tab-pending" type="button" role="tab" aria-controls="tab-pending" aria-selected="false">
                <i class="fas fa-clock me-1"></i> 3. Menunggu Persetujuan (<?= count($pendingClasses) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2 small fw-semibold" id="tab-riwayat-btn" data-bs-toggle="pill" data-bs-target="#tab-riwayat" type="button" role="tab" aria-controls="tab-riwayat" aria-selected="false">
                <i class="fas fa-history me-1"></i> 4. Riwayat Semua Kelas (<?= count($enrollments) ?>)
            </button>
        </li>
    </ul>
</div>

<!-- 4. Tab Content Areas -->
<div class="tab-content" id="kelasTabContent">

    <!-- TAB 1: KELAS AKTIF & BERJALAN -->
    <div class="tab-pane fade show active" id="tab-aktif" role="tabpanel" aria-labelledby="tab-aktif-btn">
        <?php if ($activeClasses === []): ?>
            <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white text-center">
                <div class="py-3">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; background: rgba(8,47,92,0.06); color: #082f5c;">
                        <i class="fas fa-book-reader fs-3 opacity-75"></i>
                    </div>
                    <h2 class="h5 fw-bold mb-2" style="color: #082f5c;">Belum Ada Kelas yang Sedang Berjalan</h2>
                    <p class="text-muted small mb-3 mx-auto" style="max-width: 540px;">
                        Anda belum memiliki kelas aktif saat ini. Jelajahi katalog Arena Belajar untuk mendaftarkan diri pada program kompetensi pengadaan barang dan jasa.
                    </p>
                    <a href="<?= site_url('arena-belajar/kelas') ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                        <i class="fas fa-search me-1"></i> Eksplorasi Katalog Kelas
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-3 ws-paginated-container" data-page-size="4">
                <?php foreach ($activeClasses as $item): ?>
                    <?php
                    $classId     = (int) ($item['class_id'] ?? 0);
                    $stateCode   = $item['state_code'] ?? 'in_learning';
                    $pct         = (float) ($item['overall_percent'] ?? 0);
                    $doneLessons = (int) ($item['completed_lessons_count'] ?? 0);
                    $totalLessons = (int) ($item['all_lessons_count'] ?? 0);
                    $cta         = $item['cta'] ?? [];
                    $preTest     = $item['pre_test'] ?? [];
                    $postTest    = $item['post_test'] ?? [];
                    $trainers    = $item['trainers'] ?? [];
                    ?>
                    <div class="col-12 ws-paginated-item">
                        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white">
                            <!-- Card Header: Badges & Class Code -->
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-2 rounded-pill fw-bold">
                                        <i class="fas fa-circle-play me-1"></i> Aktif
                                    </span>
                                    <?php $mBadge = $methodBadge($item['learning_method'] ?? 'online'); ?>
                                    <span class="badge <?= $mBadge['class'] ?> px-3 py-2 rounded-pill fw-semibold">
                                        <i class="<?= $mBadge['icon'] ?> me-1"></i> <?= esc($mBadge['label']) ?>
                                    </span>
                                    <?php if (! empty($item['program_title'])): ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                                            <i class="fas fa-layer-group me-1"></i> <?= esc($item['program_title']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-pill">
                                        <?= esc($levelLabel($item['course_level'] ?? 'basic')) ?>
                                    </span>
                                </div>
                                <div class="text-muted small">
                                    Kode Kelas: <code class="text-primary fw-bold font-monospace"><?= esc($item['class_code'] ?? '-') ?></code>
                                </div>
                            </div>

                            <!-- Card Body: Title, Course, & Schedule -->
                            <div class="row g-4 align-items-start mb-4">
                                <div class="col-md-3 col-xl-2">
                                    <div class="rounded-4 overflow-hidden shadow-xs border bg-light" style="aspect-ratio: 16/10;">
                                        <img src="<?= $fmtCover($item) ?>" alt="<?= esc($item['course_title'] ?? 'Cover') ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/resources/course-details-img-1.jpg') ?>';">
                                    </div>
                                </div>
                                <div class="col-md-9 col-xl-6">
                                    <h2 class="h4 fw-bold mb-2" style="color: #082f5c;"><?= esc($item['class_name'] ?? '-') ?></h2>
                                    <p class="text-muted fs-6 mb-2">
                                        <i class="fas fa-book-open text-primary me-1"></i> Course: <strong class="text-dark"><?= esc($item['course_title'] ?? '-') ?></strong>
                                    </p>
                                    <?php if (! empty($item['course_summary'])): ?>
                                        <p class="text-muted small mb-3"><?= esc($item['course_summary']) ?></p>
                                    <?php endif; ?>

                                    <div class="d-flex flex-wrap gap-3 text-muted small">
                                        <div>
                                            <i class="far fa-calendar-alt text-primary me-1"></i>
                                            Periode: <strong><?= ! empty($item['start_at']) ? date('d M Y', strtotime($item['start_at'])) : 'Fleksibel' ?></strong>
                                            &ndash;
                                            <strong><?= ! empty($item['end_at']) ? date('d M Y', strtotime($item['end_at'])) : 'Selesai' ?></strong>
                                        </div>
                                        <div>
                                            <i class="fas fa-bullseye text-primary me-1"></i>
                                            Passing Grade: <strong><?= (float) ($item['passing_grade'] ?? 70) ?>%</strong>
                                        </div>
                                        <?php if (! empty($item['location_or_link'])): ?>
                                            <div>
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                                Akses/Lokasi: <strong class="text-dark"><?= esc($item['location_or_link']) ?></strong>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (! empty($item['class_target_audience'])): ?>
                                            <div>
                                                <i class="fas fa-users text-info me-1"></i>
                                                Sasaran: <strong><?= esc($item['class_target_audience']) ?></strong>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (! empty($trainers)): ?>
                                            <div>
                                                <i class="fas fa-chalkboard-teacher text-primary me-1"></i>
                                                Pengajar: <strong><?= esc($trainers[0]['name'] ?? 'Trainer Begawi') ?></strong>
                                                <span class="badge bg-light text-muted border rounded-pill ms-1" style="font-size: 10px;">
                                                    <?= $trainerRoleLabel($trainers[0]['trainer_role'] ?? 'primary') ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Progress Gauge / Stats Box -->
                                <div class="col-lg-12 col-xl-4">
                                    <div class="p-3 bg-light rounded-4 border">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small fw-semibold text-muted text-uppercase">Capaian Kurikulum</span>
                                            <span class="fw-bold text-primary"><?= $pct ?>%</span>
                                        </div>
                                        <div class="progress mb-2" style="height: 10px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $pct ?>%" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="d-flex justify-content-between small text-muted">
                                            <span><i class="fas fa-check-circle text-success me-1"></i><?= $doneLessons ?> tuntas</span>
                                            <span><?= $totalLessons ?> total materi</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 5-Stage Roadmap Strip -->
                            <div class="p-2 p-md-3 rounded-3 bg-light mb-3 border">
                                <span class="d-block text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.72rem;">
                                    <i class="fas fa-route text-primary me-1"></i> Alur Tahapan Pembelajaran
                                </span>
                                <div class="ws-roadmap-strip">
                                    <div class="row g-1 g-md-2 text-center flex-nowrap flex-xl-wrap pb-1" style="min-width: 460px;">
                                        <!-- Step 1: Pre-Test -->
                                        <div class="col-auto col-md" style="min-width: 88px;">
                                            <div class="p-1 p-md-2 rounded-3 bg-white border h-100">
                                                <small class="d-block text-muted text-truncate" style="font-size: 0.7rem;">1. Pre-Test</small>
                                                <?php if (! empty($preTest['exists'])): ?>
                                                    <?php if (! empty($preTest['completed'])): ?>
                                                        <span class="badge bg-success-subtle text-success mt-1"><i class="fas fa-check me-1"></i>Selesai (<?= (float) ($preTest['attempt']['score'] ?? 0) ?>)</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning-subtle text-warning-emphasis mt-1"><i class="fas fa-exclamation-triangle me-1"></i>Wajib</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle mt-1">Opsional</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <!-- Step 2: Modul & Lessons -->
                                        <div class="col-auto col-md" style="min-width: 88px;">
                                            <div class="p-1 p-md-2 rounded-3 bg-white border h-100">
                                                <small class="d-block text-muted text-truncate" style="font-size: 0.7rem;">2. Materi</small>
                                                <span class="badge bg-primary-subtle text-primary mt-1"><?= $doneLessons ?>/<?= $totalLessons ?> Selesai</span>
                                            </div>
                                        </div>
                                        <!-- Step 3: Quiz Modul -->
                                        <div class="col-auto col-md" style="min-width: 88px;">
                                            <div class="p-1 p-md-2 rounded-3 bg-white border h-100">
                                                <small class="d-block text-muted text-truncate" style="font-size: 0.7rem;">3. Kuis</small>
                                                <span class="badge bg-info-subtle text-info-emphasis mt-1"><i class="fas fa-question-circle me-1"></i>Tersedia</span>
                                            </div>
                                        </div>
                                        <!-- Step 4: Tugas Mandiri -->
                                        <div class="col-auto col-md" style="min-width: 88px;">
                                            <div class="p-1 p-md-2 rounded-3 bg-white border h-100">
                                                <small class="d-block text-muted text-truncate" style="font-size: 0.7rem;">4. Tugas</small>
                                                <span class="badge bg-secondary-subtle text-secondary mt-1"><i class="fas fa-tasks me-1"></i>Praktik</span>
                                            </div>
                                        </div>
                                        <!-- Step 5: Post-Test -->
                                        <div class="col-auto col-md" style="min-width: 88px;">
                                            <div class="p-1 p-md-2 rounded-3 bg-white border h-100">
                                                <small class="d-block text-muted text-truncate" style="font-size: 0.7rem;">5. Post-Test</small>
                                                <?php if (! empty($postTest['exists'])): ?>
                                                    <?php if (! empty($postTest['completed'])): ?>
                                                        <span class="badge bg-success-subtle text-success mt-1"><i class="fas fa-check me-1"></i>Lulus</span>
                                                    <?php elseif (! empty($postTest['is_unlocked'])): ?>
                                                        <span class="badge bg-primary-subtle text-primary mt-1"><i class="fas fa-trophy me-1"></i>Siap</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle mt-1"><i class="fas fa-lock me-1"></i>Terkunci</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle mt-1">Opsional</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Next Action Indicator & CTA Actions -->
                            <div class="row align-items-center g-3 pt-3 border-top">
                                <div class="col-12 col-md-7 col-lg-8">
                                    <?php if ($stateCode === 'pre_test_required'): ?>
                                        <span class="text-warning-emphasis small fw-bold d-block"><i class="fas fa-exclamation-triangle me-1"></i> Langkah Wajib Pertama:</span>
                                        <span class="text-muted small">Kerjakan Pre-Test terlebih dahulu sebelum materi pembelajaran dibuka.</span>
                                    <?php elseif ($stateCode === 'post_test_required'): ?>
                                        <span class="text-primary small fw-bold d-block"><i class="fas fa-trophy me-1"></i> Tahap Kelulusan Akhir:</span>
                                        <span class="text-muted small">Seluruh modul telah selesai! Kerjakan Post-Test sebagai penentu kelulusan Anda.</span>
                                    <?php elseif ($stateCode === 'module_quiz_required'): ?>
                                        <span class="text-warning-emphasis small fw-bold d-block"><i class="fas fa-clock me-1"></i> Evaluasi Modul:</span>
                                        <span class="text-muted small">Materi modul telah diselesaikan. Kerjakan quiz modul untuk membuka bab berikutnya.</span>
                                    <?php elseif ($stateCode === 'assignment_required'): ?>
                                        <span class="text-info-emphasis small fw-bold d-block"><i class="fas fa-file-alt me-1"></i> Tugas Praktik Mandiri:</span>
                                        <span class="text-muted small">Kumpulkan lembar kerja tugas mandiri untuk dinilai oleh instruktur pengampu.</span>
                                    <?php elseif ($stateCode === 'waiting_certificate'): ?>
                                        <span class="text-success small fw-bold d-block"><i class="fas fa-check-circle me-1"></i> Pembelajaran Selesai:</span>
                                        <span class="text-muted small">Anda telah lulus! Menunggu verifikasi dan penerbitan sertifikat resmi oleh operator.</span>
                                    <?php else: ?>
                                        <span class="text-primary small fw-bold d-block"><i class="fas fa-play-circle me-1"></i> Lanjutkan Pembelajaran:</span>
                                        <span class="text-muted small">Masuk ke ruang belajar interaktif dan tuntaskan kurikulum kelas Anda.</span>
                                    <?php endif; ?>
                                </div>

                                <div class="col-12 col-md-5 col-lg-4 text-md-end">
                                    <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                                        <!-- Dynamic Primary Action -->
                                        <?php if (! empty($cta['url']) && empty($cta['disabled'])): ?>
                                            <a href="<?= esc($cta['url']) ?>" class="<?= esc($cta['class'] ?? 'btn btn-primary') ?> px-4 fw-semibold rounded-3 shadow-sm text-nowrap w-100 w-sm-auto">
                                                <i class="fas fa-arrow-right me-1"></i> <?= esc($cta['label'] ?? 'Lanjutkan') ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= route_to('workspace.participant.study', $classId) ?>" class="btn btn-primary px-4 fw-semibold rounded-3 shadow-sm text-nowrap w-100 w-sm-auto">
                                                <i class="fas fa-chalkboard me-1"></i> Ruang Belajar
                                            </a>
                                        <?php endif; ?>

                                        <!-- Secondary Actions -->
                                        <a href="<?= route_to('workspace.participant.class.detail', $classId) ?>" class="btn btn-outline-secondary px-3 rounded-3 text-nowrap w-100 w-sm-auto">
                                            <i class="fas fa-info-circle me-1"></i> Detail Kelas
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- TAB 2: KELAS SELESAI & SERTIFIKAT -->
    <div class="tab-pane fade" id="tab-selesai" role="tabpanel" aria-labelledby="tab-selesai-btn">
        <?php if ($completedClasses === []): ?>
            <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white text-center">
                <div class="py-3">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; background: rgba(8,47,92,0.06); color: #082f5c;">
                        <i class="fas fa-graduation-cap fs-3 opacity-75"></i>
                    </div>
                    <h2 class="h5 fw-bold mb-2" style="color: #082f5c;">Belum Ada Kelas yang Selesai</h2>
                    <p class="text-muted small mb-3 mx-auto" style="max-width: 540px;">
                        Tuntaskan seluruh rangkaian materi, evaluasi kuis, tugas mandiri, dan ujian akhir pada kelas aktif Anda untuk memperoleh kelulusan dan sertifikat resmi.
                    </p>
                    <button class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold" onclick="document.getElementById('tab-aktif-btn').click()">
                        <i class="fas fa-book-reader me-1"></i> Buka Kelas Aktif Saya
                    </button>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-3 ws-paginated-container" data-page-size="4">
                <?php foreach ($completedClasses as $item): ?>
                    <?php
                    $classId     = (int) ($item['class_id'] ?? 0);
                    $finalScore  = $item['final_score'] ?? null;
                    $passingGrade = (float) ($item['passing_grade'] ?? 70);
                    $certId      = $item['certificate_id'] ?? null;
                    $certNumber  = $item['certificate_number'] ?? null;
                    $certStatus  = $item['certificate_status'] ?? null;
                    $certEnabled = (int) ($item['certificate_enabled'] ?? 0) === 1;
                    ?>
                    <div class="col-12 ws-paginated-item">
                        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white border-start border-success border-4">
                            <!-- Card Header -->
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="badge bg-success text-white px-3 py-2 rounded-pill fw-bold shadow-sm">
                                        <i class="fas fa-check-circle me-1"></i> Lulus & Selesai
                                    </span>
                                    <?php $mBadge = $methodBadge($item['learning_method'] ?? 'online'); ?>
                                    <span class="badge <?= $mBadge['class'] ?> px-3 py-2 rounded-pill fw-semibold">
                                        <i class="<?= $mBadge['icon'] ?> me-1"></i> <?= esc($mBadge['label']) ?>
                                    </span>
                                    <?php if (! empty($item['program_title'])): ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                                            <i class="fas fa-layer-group me-1"></i> <?= esc($item['program_title']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-pill">
                                        <?= esc($levelLabel($item['course_level'] ?? 'basic')) ?>
                                    </span>
                                </div>
                                <div class="text-muted small">
                                    Kode Kelas: <code class="text-primary fw-bold font-monospace"><?= esc($item['class_code'] ?? '-') ?></code>
                                </div>
                            </div>

                            <!-- Card Body: Title & Evaluation Highlights -->
                            <div class="row g-4 align-items-start mb-4">
                                <div class="col-md-3 col-xl-2">
                                    <div class="rounded-4 overflow-hidden shadow-xs border bg-light" style="aspect-ratio: 16/10;">
                                        <img src="<?= $fmtCover($item) ?>" alt="<?= esc($item['course_title'] ?? 'Cover') ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/resources/course-details-img-1.jpg') ?>';">
                                    </div>
                                </div>
                                <div class="col-md-9 col-xl-6">
                                    <h2 class="h4 fw-bold mb-2" style="color: #082f5c;"><?= esc($item['class_name'] ?? '-') ?></h2>
                                    <p class="text-muted fs-6 mb-2">
                                        <i class="fas fa-book-open text-primary me-1"></i> Course: <strong class="text-dark"><?= esc($item['course_title'] ?? '-') ?></strong>
                                    </p>
                                    <div class="d-flex flex-wrap gap-3 text-muted small mb-2">
                                        <div>
                                            <i class="far fa-calendar-check text-success me-1"></i>
                                            Selesai pada: <strong><?= ! empty($item['completed_at']) ? date('d M Y H:i', strtotime($item['completed_at'])) : (! empty($item['passed_at']) ? date('d M Y H:i', strtotime($item['passed_at'])) : '-') ?></strong>
                                        </div>
                                        <?php if (! empty($item['location_or_link'])): ?>
                                            <div>
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                                <strong><?= esc($item['location_or_link']) ?></strong>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (! empty($item['trainers'])): ?>
                                            <div>
                                                <i class="fas fa-chalkboard-teacher text-primary me-1"></i>
                                                Pengajar: <strong><?= esc($item['trainers'][0]['name'] ?? 'Trainer') ?></strong>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Evaluation & Final Score Box -->
                                <div class="col-lg-12 col-xl-4">
                                    <div class="p-3 bg-success-subtle border border-success-subtle rounded-4">
                                        <div class="row align-items-center g-2">
                                            <div class="col-sm-6 text-center text-sm-start">
                                                <span class="small text-muted text-uppercase d-block fw-semibold">Nilai Akhir</span>
                                                <div class="display-6 fw-bold text-success my-1">
                                                    <?= $finalScore !== null ? number_format((float) $finalScore, 1) : '-' ?>
                                                </div>
                                                <small class="text-muted">Passing Grade: <?= $passingGrade ?>%</small>
                                            </div>
                                            <div class="col-sm-6 text-center text-sm-end">
                                                <span class="badge bg-success text-white py-2 px-3 rounded-pill fw-bold">
                                                    <i class="fas fa-award me-1"></i> LULUS
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Certificate Panel -->
                            <div class="p-3 p-md-4 rounded-4 bg-light mb-4 border">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="fas fa-certificate text-warning fs-5"></i>
                                            <strong class="text-dark">Sertifikat Kelulusan Resmi</strong>
                                        </div>
                                        <?php if ($certStatus === 'issued' && ! empty($certNumber)): ?>
                                            <div class="text-muted small">
                                                Nomor Sertifikat: <code class="font-monospace text-primary fw-bold px-2 py-1 bg-white border rounded"><?= esc($certNumber) ?></code>
                                                &middot; Terbit: <?= ! empty($item['certificate_issued_at']) ? date('d M Y', strtotime($item['certificate_issued_at'])) : date('d M Y') ?>
                                            </div>
                                        <?php elseif ($certEnabled): ?>
                                            <div class="text-warning-emphasis small">
                                                <i class="fas fa-hourglass-half me-1"></i> Data kelulusan telah diverifikasi. Sertifikat sedang dalam antrean penerbitan oleh Operator.
                                            </div>
                                        <?php else: ?>
                                            <div class="text-muted small">
                                                Kelas ini dirancang sebagai program pengayaan tanpa penerbitan sertifikat formal.
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div>
                                        <?php if ($certStatus === 'issued' && ! empty($certId)): ?>
                                            <a href="<?= route_to('workspace.participant.certificate.download', (string) $certId) ?>" class="btn btn-success fw-semibold px-4 rounded-3 shadow-sm">
                                                <i class="fas fa-file-download me-1"></i> Unduh Sertifikat (PDF)
                                            </a>
                                        <?php elseif ($certEnabled): ?>
                                            <span class="btn btn-secondary disabled rounded-3 px-3">
                                                <i class="fas fa-clock me-1"></i> Menunggu Terbit
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Actions: Tetap Bisa Masuk Belajar & Review Materi Kapan Saja! -->
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pt-3 border-top">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i> Seluruh materi dan arsip pembelajaran tetap dapat Anda akses untuk ditinjau kembali.
                                </small>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="<?= route_to('workspace.participant.study', $classId) ?>" class="btn btn-outline-primary px-3 rounded-3">
                                        <i class="fas fa-book-reader me-1"></i> Buka Materi & Review
                                    </a>
                                    <a href="<?= route_to('workspace.participant.class.detail', $classId) ?>" class="btn btn-outline-secondary px-3 rounded-3">
                                        <i class="fas fa-clipboard-list me-1"></i> Rincian Nilai & Kurikulum
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- TAB 3: MENUNGGU PERSETUJUAN -->
    <div class="tab-pane fade" id="tab-pending" role="tabpanel" aria-labelledby="tab-pending-btn">
        <?php if ($pendingClasses === []): ?>
            <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white text-center">
                <div class="py-3">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; background: rgba(8,47,92,0.06); color: #082f5c;">
                        <i class="fas fa-check-circle fs-3 opacity-75"></i>
                    </div>
                    <h2 class="h5 fw-bold mb-2" style="color: #082f5c;">Tidak Ada Pendaftaran Menunggu Persetujuan</h2>
                    <p class="text-muted small mb-0 mx-auto" style="max-width: 540px;">
                        Seluruh pendaftaran kelas Anda telah diproses oleh Operator. Anda dapat memeriksa kelas pada tab Kelas Aktif atau Riwayat Semua Kelas.
                    </p>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-3 ws-paginated-container" data-page-size="4">
                <?php foreach ($pendingClasses as $item): ?>
                    <?php $classId = (int) ($item['class_id'] ?? 0); ?>
                    <div class="col-12 ws-paginated-item">
                        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white border-start border-warning border-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill fw-bold">
                                        <i class="fas fa-clock me-1"></i> Menunggu Verifikasi Operator
                                    </span>
                                    <?php $mBadge = $methodBadge($item['learning_method'] ?? 'online'); ?>
                                    <span class="badge <?= $mBadge['class'] ?> px-3 py-2 rounded-pill fw-semibold">
                                        <i class="<?= $mBadge['icon'] ?> me-1"></i> <?= esc($mBadge['label']) ?>
                                    </span>
                                    <?php if (! empty($item['program_title'])): ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                                            <i class="fas fa-layer-group me-1"></i> <?= esc($item['program_title']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-muted small">
                                    Kode Kelas: <code class="text-primary fw-bold font-monospace"><?= esc($item['class_code'] ?? '-') ?></code>
                                </div>
                            </div>

                            <div class="row g-4 align-items-center mb-3">
                                <div class="col-md-3 col-xl-2">
                                    <div class="rounded-4 overflow-hidden shadow-xs border bg-light" style="aspect-ratio: 16/10;">
                                        <img src="<?= $fmtCover($item) ?>" alt="<?= esc($item['course_title'] ?? 'Cover') ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/resources/course-details-img-1.jpg') ?>';">
                                    </div>
                                </div>
                                <div class="col-md-9 col-xl-6">
                                    <h2 class="h4 fw-bold mb-2" style="color: #082f5c;"><?= esc($item['class_name'] ?? '-') ?></h2>
                                    <p class="text-muted fs-6 mb-2">
                                        <i class="fas fa-book-open text-primary me-1"></i> Course: <strong class="text-dark"><?= esc($item['course_title'] ?? '-') ?></strong>
                                    </p>
                                    <div class="d-flex flex-wrap gap-3 text-muted small">
                                        <div>
                                            <i class="fas fa-calendar-plus text-primary me-1"></i>
                                            Didaftarkan: <strong><?= ! empty($item['enrolled_at']) ? date('d M Y H:i', strtotime($item['enrolled_at'])) : '-' ?></strong>
                                        </div>
                                        <div>
                                            <i class="far fa-calendar-alt text-primary me-1"></i>
                                            Jadwal: <strong><?= ! empty($item['start_at']) ? date('d M Y', strtotime($item['start_at'])) : 'Fleksibel' ?></strong>
                                        </div>
                                        <?php if (! empty($item['location_or_link'])): ?>
                                            <div>
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                                <strong><?= esc($item['location_or_link']) ?></strong>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (! empty($item['capacity'])): ?>
                                            <div>
                                                <i class="fas fa-users text-primary me-1"></i>
                                                Kapasitas: <strong><?= (int) $item['capacity'] ?> peserta</strong>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-xl-4 text-xl-end">
                                    <a href="<?= route_to('workspace.participant.class.detail', $classId) ?>" class="btn btn-outline-primary px-3 rounded-3">
                                        <i class="fas fa-info-circle me-1"></i> Lihat Rincian Kelas
                                    </a>
                                </div>
                            </div>

                            <div class="p-3 bg-light rounded-3 text-muted small">
                                <i class="fas fa-info-circle text-primary me-1"></i> Permintaan pendaftaran Anda telah tersimpan di sistem BegawiPengadaan. Operator akan memverifikasi kelengkapan profil serta alokasi kuota sebelum mengaktifkan kelas Anda.
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- TAB 4: RIWAYAT SEMUA KELAS -->
    <div class="tab-pane fade" id="tab-riwayat" role="tabpanel" aria-labelledby="tab-riwayat-btn">
        <?php if ($enrollments === []): ?>
            <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white text-center">
                <div class="py-3">
                    <i class="fas fa-history fs-2 text-muted opacity-50 mb-2"></i>
                    <h2 class="h5 fw-bold mb-2" style="color: #082f5c;">Belum Ada Riwayat Kelas</h2>
                    <p class="text-muted small mb-3">Anda belum pernah mendaftar pada kelas manapun di sistem BegawiPengadaan.</p>
                    <a href="<?= site_url('arena-belajar/kelas') ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                        <i class="fas fa-search me-1"></i> Buka Arena Belajar
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-3 ws-paginated-container" data-page-size="6">
                <?php foreach ($enrollments as $item): ?>
                    <?php
                    $status   = (string) ($item['enrollment_status'] ?? 'pending');
                    $badge    = $statusBadge($status);
                    $classId  = (int) ($item['class_id'] ?? 0);
                    $pct      = (float) ($item['overall_percent'] ?? ($item['progress_summary']['percent'] ?? 0));
                    $finalScore = $item['final_score'] ?? null;
                    ?>
                    <div class="col-lg-6 ws-paginated-item">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="badge <?= $badge['class'] ?> border px-3 py-2 rounded-pill fw-semibold">
                                        <?= esc($badge['label']) ?>
                                    </span>
                                    <?php $mBadge = $methodBadge($item['learning_method'] ?? 'online'); ?>
                                    <span class="badge <?= $mBadge['class'] ?> px-2 py-1 rounded-pill small">
                                        <i class="<?= $mBadge['icon'] ?> me-1"></i> <?= esc($mBadge['label']) ?>
                                    </span>
                                </div>
                                <small class="text-muted font-monospace"><?= esc($item['class_code'] ?? '-') ?></small>
                            </div>

                            <div class="row g-3 align-items-start mb-2">
                                <div class="col-3 col-sm-2 col-lg-3">
                                    <div class="rounded-3 overflow-hidden border bg-light" style="aspect-ratio: 16/10;">
                                        <img src="<?= $fmtCover($item) ?>" alt="<?= esc($item['course_title'] ?? 'Cover') ?>" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/resources/course-details-img-1.jpg') ?>';">
                                    </div>
                                </div>
                                <div class="col-9 col-sm-10 col-lg-9">
                                    <h3 class="h6 fw-bold mb-1" style="color: #082f5c;"><?= esc($item['class_name'] ?? '-') ?></h3>
                                    <p class="text-muted small mb-0"><i class="fas fa-book text-primary me-1"></i> <?= esc($item['course_title'] ?? '-') ?></p>
                                    <?php if (! empty($item['program_title'])): ?>
                                        <small class="text-muted d-block"><i class="fas fa-layer-group text-muted me-1"></i> <?= esc($item['program_title']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mt-auto pt-3 border-top">
                                <?php if ($status === 'active'): ?>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between small text-muted mb-1">
                                            <span>Progres Belajar</span>
                                            <span class="fw-bold text-primary"><?= $pct ?>%</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $pct ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="<?= route_to('workspace.participant.study', $classId) ?>" class="btn btn-primary btn-sm rounded-3 px-3 fw-semibold">
                                            <i class="fas fa-arrow-right me-1"></i> Masuk Kelas
                                        </a>
                                        <a href="<?= route_to('workspace.participant.class.detail', $classId) ?>" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                                            Detail
                                        </a>
                                    </div>
                                <?php elseif ($status === 'completed'): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="small text-muted">Nilai Akhir: <strong class="text-success"><?= $finalScore !== null ? number_format((float) $finalScore, 1) : '-' ?></strong></span>
                                        <?php if (! empty($item['certificate_number'])): ?>
                                            <span class="badge bg-success-subtle text-success border"><i class="fas fa-award me-1"></i> Bersertifikat</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="<?= route_to('workspace.participant.study', $classId) ?>" class="btn btn-outline-primary btn-sm rounded-3 px-3">
                                            <i class="fas fa-book-reader me-1"></i> Review Materi
                                        </a>
                                        <?php if (! empty($item['certificate_id'])): ?>
                                            <a href="<?= route_to('workspace.participant.certificate.download', (string) $item['certificate_id']) ?>" class="btn btn-success btn-sm rounded-3 px-3">
                                                <i class="fas fa-download me-1"></i> Sertifikat
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= route_to('workspace.participant.class.detail', $classId) ?>" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                                            Detail
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="small text-muted">
                                            <?= match ($status) {
                                                'pending'   => 'Menunggu verifikasi operator',
                                                'withdrawn' => 'Dibatalkan oleh pengguna',
                                                'cancelled' => 'Pendaftaran ditolak',
                                                default     => 'Status tidak aktif',
                                            } ?>
                                        </span>
                                        <a href="<?= route_to('workspace.participant.class.detail', $classId) ?>" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                                            Detail
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>
<?= $this->endSection() ?>