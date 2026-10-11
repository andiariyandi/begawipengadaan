<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$participants = $participants ?? [];
$stats        = $stats ?? [];
$filters      = $filters ?? [];
$classes      = $classes ?? [];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Monitoring Progres Pembelajaran') ?></h6>
        <p class="text-secondary-light mb-0">Pantau progres penyelesaian materi, kuis, tugas, dan kelulusan peserta di setiap kelas.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?= site_url('dashboard/pembelajaran/kelayakan-sertifikat') ?>" class="btn btn-outline-success radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:diploma-verified-outline" class="text-xl"></iconify-icon>
            <span>Kelayakan Sertifikat</span>
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row row-cols-xxxl-4 row-cols-lg-4 row-cols-sm-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Peserta Terdaftar</p>
                        <h6 class="mb-0"><?= number_format($stats['total'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:users-group-two-rounded-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Seluruh peserta terdata</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Peserta Lulus / Selesai</p>
                        <h6 class="mb-0 text-success-main"><?= number_format($stats['passed'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:diploma-verified-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Telah lulus evaluasi</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Sedang Belajar (Aktif)</p>
                        <h6 class="mb-0 text-info-main"><?= number_format($stats['ongoing'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:play-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Masih menempuh materi</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Eligible Sertifikat</p>
                        <h6 class="mb-0 text-warning-main"><?= number_format($stats['eligible'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-warning-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:check-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Siap penerbitan dokumen</p>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header border-bottom bg-base py-16 px-24">
        <form action="<?= site_url('dashboard/pembelajaran/progres') ?>" method="get" class="row gy-3 gx-3 align-items-center">
            <div class="col-md-4">
                <div class="position-relative">
                    <input type="text" name="keyword" class="form-control radius-8 ps-40" placeholder="Cari nama peserta / kelas..." value="<?= esc($filters['keyword'] ?? '') ?>">
                    <iconify-icon icon="solar:magnifer-outline" class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light"></iconify-icon>
                </div>
            </div>
            <div class="col-md-3">
                <select name="class_id" class="form-select radius-8">
                    <option value="">Semua Kelas</option>
                    <?php foreach ($classes as $cls): ?>
                        <option value="<?= esc($cls['id']) ?>" <?= ((string) ($filters['class_id'] ?? '') === (string) $cls['id']) ? 'selected' : '' ?>>
                            <?= esc($cls['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select radius-8">
                    <option value="">Semua Status Kelulusan</option>
                    <option value="active" <?= (($filters['status'] ?? '') === 'active') ? 'selected' : '' ?>>Sedang Berjalan (Active)</option>
                    <option value="completed" <?= (($filters['status'] ?? '') === 'completed') ? 'selected' : '' ?>>Selesai (Completed)</option>
                    <option value="passed" <?= (($filters['status'] ?? '') === 'passed') ? 'selected' : '' ?>>Lulus (Passed)</option>
                    <option value="pending" <?= (($filters['status'] ?? '') === 'pending') ? 'selected' : '' ?>>Menunggu Aktivasi</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary-600 radius-8 px-16 py-11 w-100">Filter</button>
                <a href="<?= site_url('dashboard/pembelajaran/progres') ?>" class="btn btn-outline-neutral-300 radius-8 px-12 py-11" title="Reset">
                    <iconify-icon icon="solar:restart-outline" class="text-lg"></iconify-icon>
                </a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table mb-0">
                <thead>
                    <tr>
                        <th>Peserta</th>
                        <th>Kelas & Course</th>
                        <th style="min-width: 200px;">Progres Materi</th>
                        <th class="text-center">Kuis Selesai</th>
                        <th class="text-center">Tugas Dinilai</th>
                        <th class="text-center">Nilai Akhir</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Sertifikat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($participants)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-secondary-light py-32">
                                <?= view('dashboard/components/empty_state', [
                                    'title'   => 'Belum Ada Data Progres',
                                    'message' => 'Tidak ditemukan data aktivitas pembelajaran sesuai kriteria filter.',
                                    'icon'    => 'solar:chart-square-outline',
                                ]) ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($participants as $part): ?>
                            <?php
                            $progressPct = (int) ($part['progress_pct'] ?? 0);
                            $completedLessons = (int) ($part['completed_lessons'] ?? 0);
                            $totalLessons = (int) ($part['total_lessons'] ?? 0);
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-12">
                                        <div class="w-40-px h-40-px rounded-circle bg-primary-100 text-primary-600 d-flex justify-content-center align-items-center fw-semibold">
                                            <?= esc(strtoupper(substr($part['student_name'] ?? 'P', 0, 1))) ?>
                                        </div>
                                        <div>
                                            <h6 class="text-sm fw-semibold mb-1"><?= esc($part['student_name']) ?></h6>
                                            <span class="text-xs text-secondary-light">@<?= esc($part['username']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <h6 class="text-sm fw-semibold mb-1 text-primary-600">
                                        <?= esc($part['class_name']) ?>
                                    </h6>
                                    <span class="text-xs text-secondary-light"><?= esc($part['course_title'] ?? '—') ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar <?= $progressPct >= 100 ? 'bg-success-600' : 'bg-primary-600' ?>" role="progressbar" style="width: <?= $progressPct ?>%"></div>
                                        </div>
                                        <span class="text-xs fw-semibold"><?= $progressPct ?>%</span>
                                    </div>
                                    <span class="text-xs text-secondary-light">
                                        <?= $completedLessons ?> dari <?= $totalLessons ?> materi selesai
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-neutral-100 text-neutral-800 px-8 py-4 radius-4 text-xs">
                                        <?= (int) ($part['attempted_quizzes'] ?? 0) ?>/<?= (int) ($part['total_quizzes'] ?? 0) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-neutral-100 text-neutral-800 px-8 py-4 radius-4 text-xs">
                                        <?= (int) ($part['graded_assignments'] ?? 0) ?>/<?= (int) ($part['total_assignments'] ?? 0) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($part['final_score'] !== null): ?>
                                        <span class="badge bg-success-50 text-success-600 font-monospace px-10 py-4 radius-4 text-xs fw-bold">
                                            <?= number_format((float) $part['final_score'], 1) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs text-secondary-light">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', ['status' => $part['status']]) ?>
                                </td>
                                <td class="text-center">
                                    <?php if (! empty($part['has_certificate'])): ?>
                                        <span class="badge bg-success-50 text-success-600 px-8 py-4 radius-4 text-xs fw-semibold">
                                            <iconify-icon icon="solar:diploma-verified-outline" class="me-1"></iconify-icon>
                                            Terbit
                                        </span>
                                    <?php elseif (! empty($part['certificate_eligible_at']) || in_array($part['status'], ['completed', 'passed'], true)): ?>
                                        <span class="badge bg-warning-50 text-warning-600 px-8 py-4 radius-4 text-xs fw-semibold">
                                            Eligible
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-neutral-100 text-secondary-light px-8 py-4 radius-4 text-xs">
                                            Belum
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('dashboard/pembelajaran/progres/detail/' . $part['id']) ?>" class="btn btn-sm btn-outline-primary-600 radius-4 px-12 py-6 d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (isset($pager)): ?>
            <div class="card-footer border-top bg-transparent pt-16 pb-0 px-24">
                <?= $pager->links('progress', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
