<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$participants = $participants ?? [];
$stats = $stats ?? [
    'total'    => count($participants),
    'passed'   => 0,
    'ongoing'  => 0,
    'eligible' => 0,
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
                        <p class="fw-medium text-primary-light mb-1">Total Peserta</p>
                        <h6 class="mb-0"><?= number_format($stats['total'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:users-group-two-rounded-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Peserta terdaftar kelas</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Peserta Lulus</p>
                        <h6 class="mb-0 text-success-main"><?= number_format($stats['passed'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:diploma-verified-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Telah dinyatakan lulus</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Sedang Belajar</p>
                        <h6 class="mb-0 text-info-main"><?= number_format($stats['ongoing'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:play-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Progres kelas aktif</p>
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
                        <iconify-icon icon="solar:medal-ribbon-star-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Memenuhi syarat terbit</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h6 class="text-lg mb-1">Monitoring Progres dan Kelulusan</h6>
            <p class="text-secondary-light text-sm mb-0">Pantau ketuntasan materi lesson, nilai evaluasi akhir, dan sertifikasi.</p>
        </div>
    </div>

    <div class="card-body p-24">
        <!-- Filter Form -->
        <form action="<?= site_url('dashboard/pembelajaran/progres-kelulusan') ?>" method="get" class="mb-20">
            <div class="row g-12 align-items-end">
                <div class="col-lg-6 col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pencarian</label>
                    <input type="text" class="form-control radius-8" name="keyword"
                           value="<?= esc($filters['keyword'] ?? '') ?>" placeholder="Cari nama peserta, kelas, atau course…">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
                    <select class="form-select radius-8" name="status">
                        <option value="">Semua Status</option>
                        <option value="passed" <?= ($filters['status'] ?? '') === 'passed' ? 'selected' : '' ?>>Lulus (Passed)</option>
                        <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Selesai</option>
                        <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Aktif Belajar</option>
                        <option value="failed" <?= ($filters['status'] ?? '') === 'failed' ? 'selected' : '' ?>>Tidak Lulus</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 flex-grow-1 d-inline-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:magnifer-outline" class="text-lg"></iconify-icon>
                        <span>Filter</span>
                    </button>
                    <a href="<?= site_url('dashboard/pembelajaran/progres-kelulusan') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-11 d-inline-flex align-items-center justify-content-center" title="Reset">
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
                        <th>Kelas & Course</th>
                        <th style="min-width: 220px;">Progres Materi</th>
                        <th class="text-center">Nilai Akhir</th>
                        <th class="text-center">Kelulusan</th>
                        <th class="text-center">Sertifikat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($participants)): ?>
                        <tr>
                            <td colspan="6" class="p-0">
                                <?= view('dashboard/components/empty_state', [
                                    'title'   => 'Belum Ada Data Progres',
                                    'message' => 'Tidak ada aktivitas progres pembelajaran yang ditemukan.',
                                    'icon'    => 'solar:chart-square-outline',
                                ]) ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($participants as $part): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="w-36-px h-36-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0 me-12 fw-semibold text-xs">
                                            <?= esc(strtoupper(substr($part['student_name'] ?? $part['username'] ?? 'P', 0, 1))) ?>
                                        </span>
                                        <div>
                                            <h6 class="text-md mb-0 fw-medium"><?= esc($part['student_name'] ?? $part['username'] ?? '-') ?></h6>
                                            <span class="text-xs text-secondary-light"><?= esc($part['username'] ?? '') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <h6 class="text-md mb-0 fw-medium"><?= esc($part['class_name'] ?? '-') ?></h6>
                                    <span class="text-xs text-secondary-light"><?= esc($part['course_title'] ?? '') ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-10">
                                        <div class="progress flex-grow-1 progress-sm rounded-pill bg-neutral-200">
                                            <div class="progress-bar <?= (int) $part['progress_pct'] >= 100 ? 'bg-success-main' : 'bg-primary-600' ?> rounded-pill"
                                                 style="width: <?= min(100, max(0, (int) $part['progress_pct'])) ?>%;"></div>
                                        </div>
                                        <span class="fw-semibold text-xs text-primary-light"><?= (int) $part['progress_pct'] ?>%</span>
                                    </div>
                                    <span class="text-xs text-secondary-light">
                                        <?= (int) ($part['completed_lessons'] ?? 0) ?> dari <?= (int) ($part['total_lessons'] ?? 0) ?> materi selesai
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($part['final_score'] !== null): ?>
                                        <span class="badge bg-neutral-100 text-primary-600 font-monospace px-12 py-4 rounded-pill fw-bold text-sm">
                                            <?= esc((string) $part['final_score']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-secondary-light text-sm italic">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', ['status' => $part['status']]) ?>
                                </td>
                                <td class="text-center">
                                    <?php if (! empty($part['certificate_eligible_at'])): ?>
                                        <span class="badge bg-success-focus text-success-main px-10 py-4 rounded-pill fw-medium text-xs">
                                            Eligible
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-neutral-200 text-secondary-light px-10 py-4 rounded-pill fw-medium text-xs">
                                            Belum
                                        </span>
                                    <?php endif; ?>
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
