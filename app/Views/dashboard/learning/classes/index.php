<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$classes = $classes ?? [];
$courses = $courses ?? [];
$filters = $filters ?? [];
$pager = $pager ?? null;
$stats = $stats ?? [
    'total'     => count($classes),
    'open'      => 0,
    'completed' => 0,
    'draft'     => 0,
];
?>

<!-- Sub Navigation -->
<?= view('dashboard/learning/_nav_learning', ['activeSub' => 'classes']) ?>

<!-- Summary Cards -->
<div class="row row-cols-xxxl-4 row-cols-lg-4 row-cols-sm-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Kelas</p>
                        <h6 class="mb-0"><?= number_format($stats['total'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:users-group-rounded-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Semua angkatan kelas</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Kelas Berjalan</p>
                        <h6 class="mb-0 text-success-main"><?= number_format($stats['open'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:play-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Aktif & pendaftaran buka</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Kelas Selesai</p>
                        <h6 class="mb-0 text-info-main"><?= number_format($stats['completed'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:check-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Selesai masa pelaksanaan</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Draft</p>
                        <h6 class="mb-0 text-warning-main"><?= number_format($stats['draft'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-warning-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:clock-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Belum dipublikasikan</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h6 class="text-lg mb-1">Pelaksanaan Kelas</h6>
            <p class="text-secondary-light text-sm mb-0">Kelola jadwal kelas, penugasan trainer pengampu, dan kuota peserta.</p>
        </div>
        <a href="<?= route_to('dashboard.learning.classes.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
            <span>Tambah Kelas</span>
        </a>
    </div>

    <div class="card-body p-24">
        <!-- Filter Form -->
        <form action="<?= route_to('dashboard.learning.classes') ?>" method="get" class="mb-20">
            <div class="row g-12 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pencarian</label>
                    <input type="text" class="form-control radius-8" name="keyword"
                           value="<?= esc($filters['keyword'] ?? '') ?>" placeholder="Cari nama kelas atau kode…">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Course</label>
                    <select class="form-select radius-8" name="course_id">
                        <option value="">Semua Course</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= (int) ($filters['course_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>>
                                <?= esc($c['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
                    <select class="form-select radius-8" name="status">
                        <option value="">Semua Status</option>
                        <option value="scheduled" <?= in_array($filters['status'] ?? '', ['scheduled', 'open'], true) ? 'selected' : '' ?>>Terjadwal / Pendaftaran Buka</option>
                        <option value="active" <?= in_array($filters['status'] ?? '', ['active', 'ongoing'], true) ? 'selected' : '' ?>>Sedang Berjalan / Aktif</option>
                        <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Selesai</option>
                        <option value="closed" <?= ($filters['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Ditutup</option>
                        <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 flex-grow-1 d-inline-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:magnifer-outline" class="text-lg"></iconify-icon>
                        <span>Filter</span>
                    </button>
                    <a href="<?= route_to('dashboard.learning.classes') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-11 d-inline-flex align-items-center justify-content-center" title="Reset Filter">
                        <iconify-icon icon="solar:restart-outline" class="text-xl"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 begawi-table" data-table-name="pelaksanaan-kelas" data-page-length="10">
                <thead>
                    <tr>
                        <th>Kelas & Kode</th>
                        <th>Course</th>
                        <th>Trainer</th>
                        <th class="text-center">Peserta</th>
                        <th class="text-center">Progress Selesai</th>
                        <th class="text-center">Status</th>
                        <th class="text-center no-sort no-export action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($classes)): ?>
                        <tr>
                            <td colspan="7" class="p-0">
                                <?= view('dashboard/components/empty_state', [
                                    'title'       => 'Kelas Tidak Ditemukan',
                                    'message'     => 'Belum ada data kelas pembelajaran yang sesuai dengan kriteria.',
                                    'actionUrl'   => route_to('dashboard.learning.classes.create'),
                                    'actionLabel' => 'Tambah Kelas Baru',
                                ]) ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($classes as $class): ?>
                            <?php
                            $students = (int) ($class['student_count'] ?? 0);
                            $completed = (int) ($class['completed_count'] ?? 0);
                            $classProg = $students > 0 ? round(($completed / $students) * 100) : 0;
                            ?>
                            <tr>
                                <td>
                                    <h6 class="text-md mb-0 fw-medium">
                                        <a href="<?= route_to('dashboard.learning.classes.detail', $class['id']) ?>" class="text-primary-600">
                                            <?= esc($class['name']) ?>
                                        </a>
                                    </h6>
                                    <span class="badge bg-neutral-100 text-primary-600 font-monospace px-8 py-2 radius-4 text-xs mt-4">
                                        <?= esc($class['code']) ?>
                                    </span>
                                    <div class="text-xs text-secondary-light mt-1">
                                        <?= ! empty($class['start_at']) ? date('d/m/Y', strtotime($class['start_at'])) : '-' ?> s/d <?= ! empty($class['end_at']) ? date('d/m/Y', strtotime($class['end_at'])) : '-' ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-medium text-sm"><?= esc($class['course_title'] ?? '-') ?></span>
                                </td>
                                <td>
                                    <?php if (! empty($class['trainer_names'])): ?>
                                        <span class="badge bg-primary-50 text-primary-600 px-8 py-4 radius-4 text-xs">
                                            <iconify-icon icon="solar:user-speak-rounded-outline" class="me-1"></iconify-icon>
                                            <?= esc($class['trainer_names']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-neutral-100 text-secondary-light px-8 py-4 radius-4 text-xs italic">Belum ada trainer</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-50 text-info-700 px-10 py-4 radius-4 fw-medium text-xs">
                                        <?= number_format($students) ?> / <?= ! empty($class['capacity']) ? number_format($class['capacity']) : '∞' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-2">
                                        <div class="progress w-60-px" style="height: 6px;">
                                            <div class="progress-bar bg-success-600" role="progressbar" style="width: <?= $classProg ?>%"></div>
                                        </div>
                                        <span class="text-xs fw-semibold text-secondary-light"><?= $classProg ?>%</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', ['status' => $class['status'] ?? 'draft']) ?>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-outline-neutral-200 btn-sm radius-8 px-8 py-4 text-secondary-light" data-bs-toggle="dropdown" aria-expanded="false">
                                            <iconify-icon icon="solar:menu-dots-bold" class="text-lg"></iconify-icon>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8 p-8 border">
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= route_to('dashboard.learning.classes.detail', $class['id']) ?>">
                                                    <iconify-icon icon="solar:eye-outline" class="text-lg text-primary-600"></iconify-icon>
                                                    <span>Detail Kelas</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= route_to('dashboard.learning.classes.edit', $class['id']) ?>">
                                                    <iconify-icon icon="solar:pen-new-square-outline" class="text-lg text-warning-main"></iconify-icon>
                                                    <span>Ubah Kelas</span>
                                                </a>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm w-100 border-0 bg-transparent" onclick="openDuplicateModal(<?= (int) $class['id'] ?>, '<?= esc($class['name'], 'js') ?>', '<?= esc($class['code'], 'js') ?>')">
                                                    <iconify-icon icon="solar:copy-outline" class="text-lg text-primary-600"></iconify-icon>
                                                    <span>Duplikasi Angkatan</span>
                                                </button>
                                            </li>
                                            <li><hr class="dropdown-divider my-4"></li>
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= site_url('dashboard/pembelajaran/kurikulum/kelas/' . $class['id']) ?>">
                                                    <iconify-icon icon="solar:list-check-outline" class="text-lg text-primary-600"></iconify-icon>
                                                    <span>Kelola Kurikulum</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= route_to('dashboard.learning.enrollments') ?>?class_id=<?= (int) $class['id'] ?>">
                                                    <iconify-icon icon="solar:users-group-two-rounded-outline" class="text-lg text-info-main"></iconify-icon>
                                                    <span>Daftar Peserta</span>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider my-4"></li>
                                            <li>
                                                <form action="<?= site_url('dashboard/pembelajaran/kelas/' . $class['id'] . '/hapus') ?>" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas ini?');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="dropdown-item px-12 py-8 radius-4 text-danger-main bg-hover-danger-50 d-flex align-items-center gap-2 text-sm w-100 border-0 bg-transparent">
                                                        <iconify-icon icon="solar:trash-bin-trash-outline" class="text-lg text-danger-main"></iconify-icon>
                                                        <span>Hapus Kelas</span>
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
            <div class="card-footer border-top bg-transparent pt-16 pb-0 px-0">
                <?= $pager->links('classes', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- Modal Wizard Duplikasi Angkatan -->
<div class="modal fade" id="duplicateBatchModal" tabindex="-1" aria-labelledby="duplicateBatchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="post" id="duplicateBatchForm" class="modal-content radius-16 border-0 shadow bg-base">
            <?= csrf_field() ?>
            <div class="modal-header border-bottom py-16 px-24 bg-base">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:copy-bold" class="text-primary-600 text-2xl"></iconify-icon>
                    <div>
                        <h6 class="modal-title fs-6 fw-bold mb-0" id="duplicateBatchModalLabel">Duplikasi Angkatan Kelas (Batch Duplication)</h6>
                        <span class="text-xs text-secondary-light">Gandakan seluruh kurikulum, modul, materi, dan soal kuis ke ID kelas baru tanpa input ulang</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <div class="alert alert-primary bg-primary-50 border-primary-200 radius-12 p-16 mb-20">
                    <div class="d-flex align-items-start gap-2 text-xs text-primary-900">
                        <iconify-icon icon="solar:info-circle-bold" class="text-primary-600 text-xl flex-shrink-0 mt-1"></iconify-icon>
                        <div>
                            <strong>Kelas Sumber Asal:</strong> <span id="duplicateSourceClassName">Nama Kelas</span>
                            <div class="text-secondary-light mt-1">Sistem akan secara otomatis menyalin seluruh modul, materi/lesson, dan soal kuis yang ada ke batch baru berstatus <code>Draft</code>.</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold text-primary-light text-xs mb-4">Nama Kelas Angkatan Baru <span class="text-danger">*</span></label>
                        <input type="text" name="new_name" id="dupNewName" class="form-control radius-8" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-xs mb-4">Kode Batch Baru (Unik) <span class="text-danger">*</span></label>
                        <input type="text" name="new_code" id="dupNewCode" class="form-control radius-8 font-monospace text-uppercase" required>
                        <span class="text-2xs text-secondary-light">Contoh: BGW-PBJ-B2</span>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-xs mb-4">Opsi Trainer</label>
                        <div class="form-check form-switch pt-8">
                            <input class="form-check-input" type="checkbox" name="copy_trainers" value="1" id="copyTrainersCheck" checked>
                            <label class="form-check-label text-xs fw-medium text-primary-light" for="copyTrainersCheck">
                                Salin penugasan pengajar / trainer aktif
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-xs mb-4">Tanggal Mulai Baru</label>
                        <input type="datetime-local" name="start_at" class="form-control radius-8 text-xs">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-xs mb-4">Tanggal Selesai Baru</label>
                        <input type="datetime-local" name="end_at" class="form-control radius-8 text-xs">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-16 px-24 bg-neutral-50 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary radius-8 px-16 py-8 text-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-8 text-sm d-inline-flex align-items-center gap-2">
                    <iconify-icon icon="solar:copy-bold" class="text-base"></iconify-icon>
                    <span>Mulai Kloning &amp; Duplikasi</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDuplicateModal(classId, className, classCode) {
        document.getElementById('duplicateBatchForm').action = '<?= site_url('dashboard/pembelajaran/kelas') ?>/' + classId + '/duplikasi';
        document.getElementById('duplicateSourceClassName').textContent = className + ' (' + classCode + ')';
        document.getElementById('dupNewName').value = className + ' - Batch Baru';
        document.getElementById('dupNewCode').value = classCode + '-B2';
        var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('duplicateBatchModal'));
        modal.show();
    }
</script>
<?= $this->endSection() ?>
