<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$submissions = $submissions ?? [];
$stats = $stats ?? [
    'total'     => count($submissions),
    'graded'    => 0,
    'submitted' => 0,
    'revision'  => 0,
];
$filters = $filters ?? [];
?>

<!-- Summary Cards -->
<div class="row row-cols-xxxl-4 row-cols-lg-4 row-cols-sm-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Submission</p>
                        <h6 class="mb-0"><?= number_format($stats['total'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:document-text-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Semua tugas dikumpulkan</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Sudah Dinilai</p>
                        <h6 class="mb-0 text-success-main"><?= number_format($stats['graded'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:check-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Mendapat skor & feedback</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Menunggu Review</p>
                        <h6 class="mb-0 text-warning-main"><?= number_format($stats['submitted'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:clock-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Antrean review penilaian (Operator / Trainer)</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Perlu Revisi</p>
                        <h6 class="mb-0 text-danger-main"><?= number_format($stats['revision'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-warning-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:danger-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Dikembalikan ke peserta</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h6 class="text-lg mb-1">Penilaian dan Evaluasi Tugas</h6>
            <p class="text-secondary-light text-sm mb-0">Pantau proses evaluasi lembar kerja dan tugas peserta kelas.</p>
        </div>
    </div>

    <div class="card-body p-24">
        <!-- Filter Form -->
        <form action="<?= route_to('dashboard.learning.gradings') ?>" method="get" class="mb-20">
            <div class="row g-12 align-items-end">
                <div class="col-lg-6 col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pencarian</label>
                    <input type="text" class="form-control radius-8" name="keyword"
                           value="<?= esc($filters['keyword'] ?? '') ?>" placeholder="Cari nama peserta, kelas, atau judul tugas…">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
                    <select class="form-select radius-8" name="status">
                        <option value="">Semua Status</option>
                        <option value="submitted" <?= ($filters['status'] ?? '') === 'submitted' ? 'selected' : '' ?>>Menunggu Review (Submitted)</option>
                        <option value="graded" <?= ($filters['status'] ?? '') === 'graded' ? 'selected' : '' ?>>Sudah Dinilai (Graded)</option>
                        <option value="revision" <?= ($filters['status'] ?? '') === 'revision' ? 'selected' : '' ?>>Perlu Revisi (Revision)</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 flex-grow-1 d-inline-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:magnifer-outline" class="text-lg"></iconify-icon>
                        <span>Filter</span>
                    </button>
                    <a href="<?= route_to('dashboard.learning.gradings') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-11 d-inline-flex align-items-center justify-content-center" title="Reset">
                        <iconify-icon icon="solar:restart-outline" class="text-xl"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Peserta</th>
                        <th>Kelas</th>
                        <th>Tugas / Komponen</th>
                        <th class="text-center">Nilai</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($submissions)): ?>
                        <tr>
                            <td colspan="6" class="p-0">
                                <?= view('dashboard/components/empty_state', [
                                    'title'   => 'Belum Ada Penilaian',
                                    'message' => 'Belum ada data pengumpulan tugas dari peserta.',
                                    'icon'    => 'solar:document-add-outline',
                                ]) ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($submissions as $sub): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="w-36-px h-36-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0 me-12 fw-semibold text-xs">
                                            <?= esc(strtoupper(substr($sub['student_name'] ?? $sub['username'] ?? 'P', 0, 1))) ?>
                                        </span>
                                        <div>
                                            <h6 class="text-md mb-0 fw-medium"><?= esc($sub['student_name'] ?? $sub['username'] ?? '-') ?></h6>
                                            <span class="text-xs text-secondary-light"><?= esc($sub['username'] ?? '') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-medium text-sm"><?= esc($sub['class_name'] ?? '-') ?></span>
                                </td>
                                <td>
                                    <h6 class="text-md mb-0 fw-medium"><?= esc($sub['assignment_title'] ?? '-') ?></h6>
                                    <span class="text-xs text-secondary-light">Maks: <?= esc((string) ($sub['max_score'] ?? 100)) ?> | Lulus: <?= esc((string) ($sub['passing_score'] ?? 70)) ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($sub['score'] !== null): ?>
                                        <span class="badge bg-success-focus text-success-main font-monospace px-12 py-4 rounded-pill fw-semibold text-sm">
                                            <?= esc((string) $sub['score']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-secondary-light text-sm italic">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', ['status' => $sub['status']]) ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= route_to('dashboard.learning.gradings.detail', $sub['id']) ?>" class="w-32-px h-32-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center" title="Detail Penilaian">
                                        <iconify-icon icon="solar:eye-outline"></iconify-icon>
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
<?= $this->endSection() ?>
