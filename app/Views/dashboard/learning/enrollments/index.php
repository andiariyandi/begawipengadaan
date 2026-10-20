<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$enrollments = $enrollments ?? [];
$filters = $filters ?? [];
$pager = $pager ?? null;
$classes = $classes ?? [];
$users = $users ?? [];
$stats = $stats ?? [
    'total'     => count($enrollments),
    'active'    => 0,
    'completed' => 0,
    'pending'   => 0,
];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Enrollment') ?></h6>
        <p class="text-secondary-light mb-0">Kelola dan pantau pendaftaran peserta di setiap kelas pembelajaran.</p>
    </div>
    <button type="button" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#enrollModal">
        <iconify-icon icon="solar:user-plus-rounded-outline" class="text-xl"></iconify-icon>
        <span>Daftarkan Peserta Manual</span>
    </button>
</div>

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
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Total seluruh pendaftaran</p>
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
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Menyelesaikan materi</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Menunggu Persetujuan</p>
                        <h6 class="mb-0 text-warning-main"><?= number_format($stats['pending'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-warning rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:clock-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Perlu aktivasi / approve</p>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header border-bottom bg-base py-16 px-24">
        <form action="<?= route_to('dashboard.learning.enrollments') ?>" method="get" class="row gy-3 gx-3 align-items-center">
            <div class="col-md-4">
                <div class="position-relative">
                    <input type="text" name="keyword" class="form-control radius-8 ps-40" placeholder="Cari nama peserta / username..." value="<?= esc($filters['keyword'] ?? '') ?>">
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
                    <option value="">Semua Status</option>
                    <option value="pending" <?= (($filters['status'] ?? '') === 'pending') ? 'selected' : '' ?>>Menunggu (Pending)</option>
                    <option value="active" <?= (($filters['status'] ?? '') === 'active') ? 'selected' : '' ?>>Aktif (Active)</option>
                    <option value="completed" <?= (($filters['status'] ?? '') === 'completed') ? 'selected' : '' ?>>Selesai (Completed)</option>
                    <option value="cancelled" <?= (($filters['status'] ?? '') === 'cancelled') ? 'selected' : '' ?>>Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary-600 radius-8 px-16 py-11 w-100">Filter</button>
                <a href="<?= route_to('dashboard.learning.enrollments') ?>" class="btn btn-outline-neutral-300 radius-8 px-12 py-11" title="Reset">
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
                        <th class="text-center">Progress Materi</th>
                        <th class="text-center">Nilai Akhir</th>
                        <th>Tanggal Daftar</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($enrollments)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-secondary-light py-32">
                                <?= view('dashboard/components/empty_state', [
                                    'title'   => 'Enrollment Tidak Ditemukan',
                                    'message' => 'Belum ada data pendaftaran peserta yang sesuai dengan kriteria.',
                                    'icon'    => 'solar:user-cross-outline',
                                ]) ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($enrollments as $enrollment): ?>
                            <?php
                            $progressPct = (int) ($enrollment['progress_pct'] ?? 0);
                            $completedLessons = (int) ($enrollment['completed_lessons'] ?? 0);
                            $totalLessons = (int) ($enrollment['total_lessons'] ?? 0);
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-12">
                                        <div class="w-40-px h-40-px rounded-circle bg-primary-100 text-primary-600 d-flex justify-content-center align-items-center fw-semibold">
                                            <?= esc(strtoupper(substr($enrollment['user_name'] ?? $enrollment['username'] ?? 'P', 0, 1))) ?>
                                        </div>
                                        <div>
                                            <h6 class="text-sm fw-semibold mb-1"><?= esc($enrollment['user_name'] ?? $enrollment['username'] ?? '-') ?></h6>
                                            <span class="text-xs text-secondary-light">@<?= esc($enrollment['username'] ?? '') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <h6 class="text-sm fw-semibold mb-1 text-primary-600">
                                        <?= esc($enrollment['class_name'] ?? '-') ?>
                                    </h6>
                                    <span class="text-xs text-secondary-light"><?= esc($enrollment['course_title'] ?? '-') ?></span>
                                    <div class="text-xs text-secondary-light font-monospace mt-1">Kode: <?= esc($enrollment['class_code'] ?? '') ?></div>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex flex-column align-items-center gap-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress w-80-px" style="height: 6px;">
                                                <div class="progress-bar <?= $progressPct >= 100 ? 'bg-success-600' : 'bg-primary-600' ?>" role="progressbar" style="width: <?= $progressPct ?>%"></div>
                                            </div>
                                            <span class="text-xs fw-semibold"><?= $progressPct ?>%</span>
                                        </div>
                                        <span class="text-xs text-secondary-light"><?= $completedLessons ?>/<?= $totalLessons ?> materi</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php if (! empty($enrollment['final_score'])): ?>
                                        <span class="badge bg-success-50 text-success-600 px-10 py-4 radius-4 text-xs fw-bold">
                                            <?= number_format((float) $enrollment['final_score'], 1) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs text-secondary-light">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="text-xs text-secondary-light">
                                        <?= ! empty($enrollment['enrolled_at']) ? date('d M Y, H:i', strtotime($enrollment['enrolled_at'])) : '—' ?>
                                    </span>
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
                                            <?php if ($enrollment['status'] === 'pending'): ?>
                                                <li>
                                                    <form action="<?= route_to('dashboard.learning.enrollments.approve', $enrollment['id']) ?>" method="post">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="dropdown-item px-12 py-8 radius-4 text-success-main bg-hover-success-50 d-flex align-items-center gap-2 text-sm w-100 border-0 bg-transparent">
                                                            <iconify-icon icon="solar:check-circle-outline" class="text-lg text-success-main"></iconify-icon>
                                                            <span>Setujui (Approve)</span>
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="<?= route_to('dashboard.learning.enrollments.reject', $enrollment['id']) ?>" method="post" onsubmit="return confirm('Tolak pendaftaran peserta ini?');">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="dropdown-item px-12 py-8 radius-4 text-danger-main bg-hover-danger-50 d-flex align-items-center gap-2 text-sm w-100 border-0 bg-transparent">
                                                            <iconify-icon icon="solar:close-circle-outline" class="text-lg text-danger-main"></iconify-icon>
                                                            <span>Tolak Pendaftaran</span>
                                                        </button>
                                                    </form>
                                                </li>
                                            <?php elseif ($enrollment['status'] === 'active'): ?>
                                                <li>
                                                    <form action="<?= route_to('dashboard.learning.enrollments.status', $enrollment['id']) ?>" method="post" onsubmit="return confirm('Keluarkan peserta ini dari kelas (status withdrawn)?');">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="status" value="withdrawn">
                                                        <button type="submit" class="dropdown-item px-12 py-8 radius-4 text-warning-main bg-hover-warning-50 d-flex align-items-center gap-2 text-sm w-100 border-0 bg-transparent">
                                                            <iconify-icon icon="solar:user-block-outline" class="text-lg text-warning-main"></iconify-icon>
                                                            <span>Keluarkan dari Kelas</span>
                                                        </button>
                                                    </form>
                                                </li>
                                            <?php elseif (in_array($enrollment['status'], ['withdrawn', 'cancelled'], true)): ?>
                                                <li>
                                                    <form action="<?= route_to('dashboard.learning.enrollments.status', $enrollment['id']) ?>" method="post" onsubmit="return confirm('Aktifkan kembali peserta ini ke dalam kelas?');">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="status" value="active">
                                                        <button type="submit" class="dropdown-item px-12 py-8 radius-4 text-success-main bg-hover-success-50 d-flex align-items-center gap-2 text-sm w-100 border-0 bg-transparent">
                                                            <iconify-icon icon="solar:user-check-rounded-outline" class="text-lg text-success-main"></iconify-icon>
                                                            <span>Aktifkan Kembali</span>
                                                        </button>
                                                    </form>
                                                </li>
                                            <?php endif; ?>
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= route_to('dashboard.learning.classes.detail', $enrollment['class_id'] ?? 0) ?>">
                                                    <iconify-icon icon="solar:chalkboard-outline" class="text-lg text-info-main"></iconify-icon>
                                                    <span>Lihat Kelas</span>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider my-4"></li>
                                            <li>
                                                <form action="<?= site_url('dashboard/pembelajaran/enrollment/' . $enrollment['id'] . '/hapus') ?>" method="post" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pendaftaran peserta ini?');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="dropdown-item px-12 py-8 radius-4 text-danger-main bg-hover-danger-50 d-flex align-items-center gap-2 text-sm w-100 border-0 bg-transparent">
                                                        <iconify-icon icon="solar:trash-bin-trash-outline" class="text-lg text-danger-main"></iconify-icon>
                                                        <span>Batalkan / Hapus</span>
                                                    </button>
                                                </form>
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
            <div class="card-footer border-top bg-transparent py-16 px-24">
                <?= $pager->links('enrollments', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Manual Enrollment Modal -->
<div class="modal fade" id="enrollModal" tabindex="-1" aria-labelledby="enrollModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16">
            <div class="modal-header border-bottom py-16 px-24">
                <h6 class="modal-title fw-semibold text-lg" id="enrollModalLabel">Daftarkan Peserta Manual</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('dashboard/pembelajaran/enrollment/tambah') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-24">
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-sm mb-8" for="modal_user_id">Pilih Pengguna / Peserta <span class="text-danger-main">*</span></label>
                        <select id="modal_user_id" name="user_id" class="form-select radius-8" required>
                            <option value="">-- Pilih Peserta --</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= esc($u['id']) ?>">
                                    <?= esc($u['name']) ?> (<?= esc($u['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-sm mb-8" for="modal_enroll_class_id">Pilih Kelas Pembelajaran <span class="text-danger-main">*</span></label>
                        <select id="modal_enroll_class_id" name="class_id" class="form-select radius-8" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($classes as $cls): ?>
                                <option value="<?= esc($cls['id']) ?>">
                                    <?= esc($cls['name']) ?> (<?= esc($cls['code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Daftarkan Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
