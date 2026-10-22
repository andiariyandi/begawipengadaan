<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$summary        = $summary ?? [];
$classReports   = $classReports ?? [];
$trainerReports = $trainerReports ?? [];
$programReports = $programReports ?? [];
$programs       = $programs ?? [];
$filters        = $filters ?? [];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Laporan & Analitik Pembelajaran') ?></h6>
        <p class="text-secondary-light mb-0">Laporan komprehensif metrik pembelajaran, performa kelas batch, capaian trainer, dan statistik kelulusan.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?= site_url('dashboard/pembelajaran/laporan?export=classes' . ($filters['program_id'] ? '&program_id=' . $filters['program_id'] : '') . ($filters['status'] ? '&status=' . $filters['status'] : '')) ?>" class="btn btn-outline-success radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:file-download-outline" class="text-xl"></iconify-icon>
            <span>Export CSV Kelas</span>
        </a>
        <a href="<?= site_url('dashboard/pembelajaran/laporan?export=trainers') ?>" class="btn btn-outline-primary radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:file-download-outline" class="text-xl"></iconify-icon>
            <span>Export CSV Trainer</span>
        </a>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row row-cols-xxxl-6 row-cols-lg-3 row-cols-sm-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Pendaftar</p>
                        <h6 class="mb-0"><?= number_format($summary['total_enrollments'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:users-group-two-rounded-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-xs text-primary-light mt-12 mb-0">Peserta di seluruh batch</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Tingkat Penyelesaian</p>
                        <h6 class="mb-0 text-success-main"><?= esc($summary['overall_completion_rate'] ?? 0) ?>%</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:chart-2-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-xs text-primary-light mt-12 mb-0"><?= number_format($summary['completed_enrollments'] ?? 0) ?> peserta selesai</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Rata-rata Nilai</p>
                        <h6 class="mb-0 text-info-main"><?= esc($summary['average_score'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:star-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-xs text-primary-light mt-12 mb-0">Skor evaluasi akhir</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Sertifikat Terbit</p>
                        <h6 class="mb-0 text-warning-main"><?= number_format($summary['issued_certificates'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-warning-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:diploma-verified-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-xs text-primary-light mt-12 mb-0">Sertifikat terverifikasi</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-5 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Sedang Belajar</p>
                        <h6 class="mb-0 text-primary-600"><?= number_format($summary['active_enrollments'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-primary-600 rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:play-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-xs text-primary-light mt-12 mb-0">Status aktif belajar</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Trainer Aktif</p>
                        <h6 class="mb-0"><?= number_format($summary['active_trainers'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:user-speak-rounded-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-xs text-primary-light mt-12 mb-0"><?= number_format($summary['total_classes'] ?? 0) ?> total kelas</p>
            </div>
        </div>
    </div>
</div>

<!-- Tabs for Analytical Views -->
<div class="card border-0 shadow-none mb-24">
    <div class="card-header bg-transparent border-bottom p-20 pb-0">
        <ul class="nav nav-tabs border-0 gap-3" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active pb-16 px-16 fw-semibold d-inline-flex align-items-center gap-2" id="classes-tab" data-bs-toggle="tab" data-bs-target="#classes-pane" type="button" role="tab" aria-selected="true">
                    <iconify-icon icon="solar:buildings-2-outline" class="text-lg"></iconify-icon>
                    <span>Kinerja Kelas / Batch (<?= count($classReports) ?>)</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link pb-16 px-16 fw-semibold d-inline-flex align-items-center gap-2" id="trainers-tab" data-bs-toggle="tab" data-bs-target="#trainers-pane" type="button" role="tab" aria-selected="false">
                    <iconify-icon icon="solar:user-speak-rounded-outline" class="text-lg"></iconify-icon>
                    <span>Kinerja Trainer (<?= count($trainerReports) ?>)</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link pb-16 px-16 fw-semibold d-inline-flex align-items-center gap-2" id="programs-tab" data-bs-toggle="tab" data-bs-target="#programs-pane" type="button" role="tab" aria-selected="false">
                    <iconify-icon icon="solar:folder-with-files-outline" class="text-lg"></iconify-icon>
                    <span>Popularitas Program (<?= count($programReports) ?>)</span>
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body p-20">
        <div class="tab-content" id="reportTabsContent">

            <!-- TAB 1: Kinerja Kelas / Batch -->
            <div class="tab-pane fade show active" id="classes-pane" role="tabpanel" aria-labelledby="classes-tab">
                <!-- Filter form -->
                <form method="get" action="<?= site_url('dashboard/pembelajaran/laporan') ?>" class="row g-3 mb-20">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 text-secondary-light">
                                <iconify-icon icon="solar:magnifer-linear"></iconify-icon>
                            </span>
                            <input type="text" name="keyword" class="form-control border-start-0 ps-0" placeholder="Cari nama atau kode kelas..." value="<?= esc($filters['keyword'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="program_id" class="form-select">
                            <option value="">Semua Program</option>
                            <?php foreach ($programs as $prog): ?>
                                <option value="<?= esc($prog['id']) ?>" <?= ((int) ($filters['program_id'] ?? 0) === (int) $prog['id']) ? 'selected' : '' ?>>
                                    <?= esc($prog['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Semua Status Kelas</option>
                            <option value="open" <?= (($filters['status'] ?? '') === 'open') ? 'selected' : '' ?>>Open</option>
                            <option value="active" <?= (($filters['status'] ?? '') === 'active') ? 'selected' : '' ?>>Active</option>
                            <option value="ongoing" <?= (($filters['status'] ?? '') === 'ongoing') ? 'selected' : '' ?>>Ongoing</option>
                            <option value="completed" <?= (($filters['status'] ?? '') === 'completed') ? 'selected' : '' ?>>Completed</option>
                            <option value="closed" <?= (($filters['status'] ?? '') === 'closed') ? 'selected' : '' ?>>Closed</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary radius-8 px-16 flex-grow-1">Filter</button>
                        <a href="<?= site_url('dashboard/pembelajaran/laporan') ?>" class="btn btn-outline-secondary radius-8 px-16">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table basic-border-table mb-0 align-middle">
                        <thead>
                            <tr class="text-secondary-light">
                                <th>Kelas / Batch</th>
                                <th>Program & Kursus</th>
                                <th>Kapasitas & Peserta</th>
                                <th>Penyelesaian</th>
                                <th>Kelulusan</th>
                                <th>Rata-rata Nilai</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($classReports)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-secondary-light">
                                        <iconify-icon icon="solar:box-minimalistic-outline" class="text-4xl mb-2"></iconify-icon>
                                        <p class="mb-0">Tidak ada data kelas yang sesuai dengan filter.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($classReports as $cr): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold text-primary-light"><?= esc($cr['name']) ?></span>
                                                <span class="text-xs text-secondary-light">Kode: <?= esc($cr['code'] ?? '-') ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-sm fw-medium text-primary-light"><?= esc($cr['course_title'] ?? '-') ?></span>
                                                <span class="text-xs text-secondary-light"><?= esc($cr['program_title'] ?? '-') ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-semibold text-primary-light"><?= number_format($cr['total_enrolled']) ?></span>
                                                <span class="text-xs text-secondary-light">/ <?= ((int)($cr['capacity'] ?? 0) > 0) ? number_format($cr['capacity']) : '∞' ?> kuota</span>
                                            </div>
                                        </td>
                                        <td style="min-width: 140px;">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <span class="text-xs text-secondary-light"><?= number_format($cr['completed_count']) ?> selesai</span>
                                                <span class="text-xs fw-semibold text-primary-light"><?= esc($cr['completion_rate']) ?>%</span>
                                            </div>
                                            <div class="progress progress-sm w-100" style="height: 6px;">
                                                <div class="progress-bar bg-success-main" role="progressbar" style="width: <?= min(100, $cr['completion_rate']) ?>%"></div>
                                            </div>
                                        </td>
                                        <td style="min-width: 140px;">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <span class="text-xs text-secondary-light"><?= number_format($cr['passed_count']) ?> lulus</span>
                                                <span class="text-xs fw-semibold text-info-main"><?= esc($cr['passing_rate']) ?>%</span>
                                            </div>
                                            <div class="progress progress-sm w-100" style="height: 6px;">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: <?= min(100, $cr['passing_rate']) ?>%"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($cr['avg_final_score'] > 0): ?>
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="fw-bold <?= $cr['avg_final_score'] >= ($cr['passing_grade'] ?? 70) ? 'text-success-main' : 'text-danger-main' ?>">
                                                        <?= esc($cr['avg_final_score']) ?>
                                                    </span>
                                                    <span class="text-xs text-secondary-light">/ min <?= esc($cr['passing_grade'] ?? 70) ?></span>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-xs text-secondary-light">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = match ($cr['status'] ?? '') {
                                                'open'      => 'bg-success-focus text-success-main',
                                                'active'    => 'bg-primary-focus text-primary-600',
                                                'ongoing'   => 'bg-info-focus text-info-main',
                                                'completed' => 'bg-secondary-focus text-secondary-light',
                                                default     => 'bg-neutral-100 text-neutral-900',
                                            };
                                            ?>
                                            <span class="badge text-xs radius-4 px-8 py-4 <?= $badgeClass ?>">
                                                <?= strtoupper(esc($cr['status'] ?? '-')) ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">
                                                <a href="<?= site_url('dashboard/pembelajaran/progres?class_id=' . $cr['id']) ?>" class="btn btn-sm btn-outline-info radius-8 px-10 py-6" title="Monitoring Progres Peserta">
                                                    <iconify-icon icon="solar:chart-2-outline"></iconify-icon>
                                                </a>
                                                <a href="<?= site_url('dashboard/pembelajaran/kelas/' . $cr['id']) ?>" class="btn btn-sm btn-outline-primary radius-8 px-10 py-6" title="Detail Kelas">
                                                    <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (isset($pager)): ?>
                    <div class="pt-16 pb-0">
                        <?= $pager->links('classes_report', 'default_full') ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- TAB 2: Kinerja Trainer -->
            <div class="tab-pane fade" id="trainers-pane" role="tabpanel" aria-labelledby="trainers-tab">
                <div class="table-responsive">
                    <table class="table basic-border-table mb-0 align-middle">
                        <thead>
                            <tr class="text-secondary-light">
                                <th>Trainer</th>
                                <th>Kelas Diampu</th>
                                <th>Kelas Aktif</th>
                                <th>Total Peserta Dibina</th>
                                <th>Peserta Selesai</th>
                                <th>Tingkat Keberhasilan</th>
                                <th>Rata-rata Nilai Peserta</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($trainerReports)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-secondary-light">
                                        <iconify-icon icon="solar:box-minimalistic-outline" class="text-4xl mb-2"></iconify-icon>
                                        <p class="mb-0">Belum ada data trainer yang ditugaskan pada kelas.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($trainerReports as $tr): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="w-40-px h-40-px rounded-circle bg-primary-100 text-primary-600 d-flex justify-content-center align-items-center fw-semibold">
                                                    <?= esc(strtoupper(substr($tr['name'] ?? 'T', 0, 1))) ?>
                                                </div>
                                                <div>
                                                    <h6 class="text-sm fw-medium mb-0 text-primary-light"><?= esc($tr['name']) ?></h6>
                                                    <span class="text-xs text-secondary-light"><?= esc($tr['email']) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-focus text-primary-600 radius-4 px-10 py-4 fw-semibold text-xs">
                                                <?= number_format($tr['total_classes']) ?> Kelas
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success-focus text-success-main radius-4 px-10 py-4 fw-semibold text-xs">
                                                <?= number_format($tr['active_classes']) ?> Aktif
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-primary-light"><?= number_format($tr['total_students']) ?></span>
                                            <span class="text-xs text-secondary-light">peserta</span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-success-main"><?= number_format($tr['completed_students']) ?></span>
                                            <span class="text-xs text-secondary-light">lulus/selesai</span>
                                        </td>
                                        <td style="min-width: 140px;">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <span class="text-xs fw-semibold text-primary-light"><?= esc($tr['completion_rate']) ?>%</span>
                                            </div>
                                            <div class="progress progress-sm w-100" style="height: 6px;">
                                                <div class="progress-bar bg-success-main" role="progressbar" style="width: <?= min(100, $tr['completion_rate']) ?>%"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($tr['avg_student_score'] > 0): ?>
                                                <span class="fw-bold text-primary-600"><?= esc($tr['avg_student_score']) ?></span>
                                            <?php else: ?>
                                                <span class="text-xs text-secondary-light">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= site_url('dashboard/pembelajaran/trainer?keyword=' . urlencode($tr['name'])) ?>" class="btn btn-sm btn-outline-primary radius-8 px-12 py-6">
                                                <span>Detail Penugasan</span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: Popularitas Program -->
            <div class="tab-pane fade" id="programs-pane" role="tabpanel" aria-labelledby="programs-tab">
                <div class="table-responsive">
                    <table class="table basic-border-table mb-0 align-middle">
                        <thead>
                            <tr class="text-secondary-light">
                                <th>Program Pembelajaran</th>
                                <th>Ringkasan</th>
                                <th>Total Kursus</th>
                                <th>Total Kelas / Batch</th>
                                <th>Total Peserta</th>
                                <th>Tingkat Penyelesaian</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($programReports)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-secondary-light">
                                        <iconify-icon icon="solar:box-minimalistic-outline" class="text-4xl mb-2"></iconify-icon>
                                        <p class="mb-0">Belum ada program pembelajaran yang terdaftar.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($programReports as $pr): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold text-primary-light"><?= esc($pr['title']) ?></span>
                                                <span class="text-xs text-secondary-light">Slug: <?= esc($pr['slug'] ?? '-') ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-sm text-secondary-light"><?= esc($pr['summary'] ?? '-') ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-cyan-focus text-cyan radius-4 px-10 py-4 fw-semibold text-xs">
                                                <?= number_format($pr['total_courses']) ?> Course
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-focus text-primary-600 radius-4 px-10 py-4 fw-semibold text-xs">
                                                <?= number_format($pr['total_classes']) ?> Kelas
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-primary-light"><?= number_format($pr['total_enrollments']) ?></span>
                                            <span class="text-xs text-secondary-light">pendaftar</span>
                                        </td>
                                        <td style="min-width: 140px;">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <span class="text-xs text-secondary-light"><?= number_format($pr['total_completed']) ?> selesai</span>
                                                <span class="text-xs fw-semibold text-primary-light"><?= esc($pr['completion_rate']) ?>%</span>
                                            </div>
                                            <div class="progress progress-sm w-100" style="height: 6px;">
                                                <div class="progress-bar bg-success-main" role="progressbar" style="width: <?= min(100, $pr['completion_rate']) ?>%"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge text-xs radius-4 px-8 py-4 <?= ($pr['status'] ?? '') === 'active' ? 'bg-success-focus text-success-main' : 'bg-neutral-100 text-neutral-900' ?>">
                                                <?= strtoupper(esc($pr['status'] ?? '-')) ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= site_url('dashboard/pembelajaran/program/' . $pr['id']) ?>" class="btn btn-sm btn-outline-primary radius-8 px-12 py-6">
                                                <span>Detail Program</span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
