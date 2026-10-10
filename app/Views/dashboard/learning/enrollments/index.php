<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$enrollments = $enrollments ?? [];
$filters = $filters ?? [];
$pager = $pager ?? null;
$stats = $stats ?? [
    'total'     => count($enrollments),
    'active'    => 0,
    'completed' => 0,
    'pending'   => 0,
];
?>

<!-- Summary Cards -->
<div class="row row-cols-xxxl-4 row-cols-lg-4 row-cols-sm-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Pendaftaran</p>
                        <h6 class="mb-0"><?= number_format($stats['total'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:user-hand-up-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Total peserta terdaftar</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Peserta Aktif</p>
                        <h6 class="mb-0 text-success-main"><?= number_format($stats['active'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:user-check-rounded-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Sedang mengikuti kelas</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Lulus / Selesai</p>
                        <h6 class="mb-0 text-info-main"><?= number_format($stats['completed'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:diploma-verified-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Telah menyelesaikan kelas</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Menunggu Konfirmasi</p>
                        <h6 class="mb-0 text-warning-main"><?= number_format($stats['pending'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-warning-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:clock-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Perlu aktivasi admin</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h6 class="text-lg mb-1">Pendaftaran Peserta (Enrollment)</h6>
            <p class="text-secondary-light text-sm mb-0">Monitor aktivitas pendaftaran, status kepesertaan, dan kelulusan.</p>
        </div>
    </div>

    <div class="card-body p-24">
        <!-- Filter Form -->
        <form action="<?= route_to('dashboard.learning.enrollments') ?>" method="get" class="mb-20">
            <div class="row g-12 align-items-end">
                <div class="col-lg-6 col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pencarian</label>
                    <input type="text" class="form-control radius-8" name="keyword"
                           value="<?= esc($filters['keyword'] ?? '') ?>" placeholder="Cari nama peserta, kelas, atau course…">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Enrollment</label>
                    <select class="form-select radius-8" name="status">
                        <option value="">Semua Status</option>
                        <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Aktif (Enrolled)</option>
                        <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Selesai</option>
                        <option value="passed" <?= ($filters['status'] ?? '') === 'passed' ? 'selected' : '' ?>>Lulus</option>
                        <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Menunggu</option>
                        <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 flex-grow-1 d-inline-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:magnifer-outline" class="text-lg"></iconify-icon>
                        <span>Filter</span>
                    </button>
                    <a href="<?= route_to('dashboard.learning.enrollments') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-11 d-inline-flex align-items-center justify-content-center" title="Reset Filter">
                        <iconify-icon icon="solar:restart-outline" class="text-xl"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Peserta</th>
                        <th>Kelas</th>
                        <th>Course</th>
                        <th>Tanggal Daftar</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($enrollments)): ?>
                        <tr>
                            <td colspan="6" class="p-0">
                                <?= view('dashboard/components/empty_state', [
                                    'title'   => 'Enrollment Tidak Ditemukan',
                                    'message' => 'Belum ada data pendaftaran peserta yang sesuai dengan filter.',
                                    'icon'    => 'solar:user-cross-outline',
                                ]) ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($enrollments as $enrollment): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="w-36-px h-36-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0 me-12 fw-semibold text-xs">
                                            <?= esc(strtoupper(substr($enrollment['user_name'] ?? $enrollment['username'] ?? 'P', 0, 1))) ?>
                                        </span>
                                        <div>
                                            <h6 class="text-md mb-0 fw-medium"><?= esc($enrollment['user_name'] ?? $enrollment['username'] ?? '-') ?></h6>
                                            <span class="text-xs text-secondary-light"><?= esc($enrollment['username'] ?? '') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <h6 class="text-md mb-0 fw-medium"><?= esc($enrollment['class_name'] ?? '-') ?></h6>
                                    <span class="badge bg-neutral-100 text-secondary-light font-monospace px-8 py-2 radius-4 text-xs">
                                        <?= esc($enrollment['class_code'] ?? '') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-medium text-sm"><?= esc($enrollment['course_title'] ?? '-') ?></span>
                                </td>
                                <td>
                                    <?= esc(date('d M Y H:i', strtotime($enrollment['enrolled_at'] ?? 'now'))) ?>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', ['status' => $enrollment['status']]) ?>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-outline-neutral-200 btn-sm radius-8 px-8 py-4 text-secondary-light" data-bs-toggle="dropdown" aria-expanded="false">
                                            <iconify-icon icon="solar:menu-dots-bold" class="text-lg"></iconify-icon>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8 p-8 border">
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= route_to('dashboard.learning.enrollments.detail', $enrollment['id']) ?>">
                                                    <iconify-icon icon="solar:eye-outline" class="text-lg text-primary-600"></iconify-icon>
                                                    <span>Detail Progress</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= route_to('dashboard.learning.classes.detail', $enrollment['class_id'] ?? 0) ?>">
                                                    <iconify-icon icon="solar:chalkboard-outline" class="text-lg text-info-main"></iconify-icon>
                                                    <span>Lihat Kelas</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($pager): ?>
            <div class="card-footer border-top bg-transparent pt-16 pb-0 px-0">
                <?= $pager->links('enrollments', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
