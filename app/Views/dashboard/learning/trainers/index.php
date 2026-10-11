<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$assignments = $assignments ?? [];
$stats       = $stats ?? [];
$filters     = $filters ?? [];
$classes     = $classes ?? [];
$trainers    = $trainers ?? [];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Penugasan Trainer') ?></h6>
        <p class="text-secondary-light mb-0">Kelola dan pantau penugasan trainer (pengajar) ke setiap kelas pembelajaran aktif.</p>
    </div>
    <button type="button" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#assignTrainerModal">
        <iconify-icon icon="solar:user-plus-rounded-outline" class="text-xl"></iconify-icon>
        <span>Tugaskan Trainer</span>
    </button>
</div>

<!-- KPI Cards -->
<div class="row row-cols-xxxl-4 row-cols-lg-4 row-cols-sm-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Penugasan</p>
                        <h6 class="mb-0"><?= number_format($stats['total'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:user-speak-rounded-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Seluruh riwayat penugasan</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Penugasan Aktif</p>
                        <h6 class="mb-0 text-success-main"><?= number_format($stats['active'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:check-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Trainer aktif mengajar</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Kelas Terisi Trainer</p>
                        <h6 class="mb-0 text-info-main"><?= number_format($stats['unique_classes'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:users-group-rounded-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Kelas dengan trainer aktif</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Penugasan Dicabut</p>
                        <h6 class="mb-0 text-secondary-light"><?= number_format($stats['revoked'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-neutral-400 rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:close-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Status dinonaktifkan</p>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header border-bottom bg-base py-16 px-24">
        <form action="<?= site_url('dashboard/pembelajaran/trainer') ?>" method="get" class="row gy-3 gx-3 align-items-center">
            <div class="col-md-4">
                <div class="position-relative">
                    <input type="text" name="keyword" class="form-control radius-8 ps-40" placeholder="Cari nama trainer / kelas..." value="<?= esc($filters['keyword'] ?? '') ?>">
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
                    <option value="active" <?= (($filters['status'] ?? '') === 'active') ? 'selected' : '' ?>>Aktif</option>
                    <option value="revoked" <?= (($filters['status'] ?? '') === 'revoked') ? 'selected' : '' ?>>Dicabut (Revoked)</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary-600 radius-8 px-16 py-11 w-100">Filter</button>
                <a href="<?= site_url('dashboard/pembelajaran/trainer') ?>" class="btn btn-outline-neutral-300 radius-8 px-12 py-11" title="Reset">
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
                        <th>Trainer</th>
                        <th>Kelas & Batch</th>
                        <th>Course Induk</th>
                        <th>Ditugaskan Oleh</th>
                        <th>Tanggal Penugasan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($assignments)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-secondary-light py-32">
                                <?= view('dashboard/components/empty_state', [
                                    'title'   => 'Belum Ada Penugasan Trainer',
                                    'message' => 'Silakan klik tombol "Tugaskan Trainer" di atas untuk menghubungkan trainer dengan kelas pembelajaran.',
                                    'icon'    => 'solar:user-speak-outline',
                                ]) ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($assignments as $row): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-12">
                                        <div class="w-40-px h-40-px rounded-circle bg-primary-100 text-primary-600 d-flex justify-content-center align-items-center fw-semibold">
                                            <?= esc(strtoupper(substr($row['trainer_name'] ?? 'T', 0, 1))) ?>
                                        </div>
                                        <div>
                                            <h6 class="text-sm fw-semibold mb-1"><?= esc($row['trainer_name']) ?></h6>
                                            <span class="text-xs text-secondary-light"><?= esc($row['trainer_email']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="<?= site_url('dashboard/pembelajaran/kelas/' . $row['class_id']) ?>" class="fw-semibold text-primary-600">
                                        <?= esc($row['class_name']) ?>
                                    </a>
                                    <div class="text-xs text-secondary-light">Kode: <?= esc($row['class_code']) ?></div>
                                </td>
                                <td><?= esc($row['course_title'] ?? '—') ?></td>
                                <td><?= esc($row['assigner_name'] ?? 'Sistem') ?></td>
                                <td>
                                    <?= ! empty($row['assigned_at']) ? date('d M Y, H:i', strtotime($row['assigned_at'])) : '—' ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['status'] === 'active'): ?>
                                        <span class="badge bg-success-50 text-success-600 px-10 py-4 radius-4 text-xs fw-semibold">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-neutral-100 text-neutral-600 px-10 py-4 radius-4 text-xs fw-semibold">Dicabut</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['status'] === 'active'): ?>
                                        <form action="<?= site_url('dashboard/pembelajaran/trainer/' . $row['class_id'] . '/' . $row['user_id'] . '/cabut') ?>" method="post" onsubmit="return confirm('Apakah Anda yakin ingin mencabut penugasan trainer ini dari kelas?');" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger radius-4 px-12 py-6 d-inline-flex align-items-center gap-1" title="Cabut Penugasan">
                                                <iconify-icon icon="solar:close-circle-outline"></iconify-icon>
                                                <span>Cabut</span>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form action="<?= site_url('dashboard/pembelajaran/trainer/assign') ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="class_id" value="<?= esc($row['class_id']) ?>">
                                            <input type="hidden" name="trainer_user_id" value="<?= esc($row['user_id']) ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-success radius-4 px-12 py-6 d-inline-flex align-items-center gap-1" title="Tugaskan Kembali">
                                                <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                                                <span>Tugaskan Lagi</span>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (isset($pager)): ?>
            <div class="card-footer border-top bg-transparent pt-16 pb-0 px-24">
                <?= $pager->links('trainers', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Assign Trainer Modal -->
<div class="modal fade" id="assignTrainerModal" tabindex="-1" aria-labelledby="assignTrainerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16">
            <div class="modal-header border-bottom py-16 px-24">
                <h6 class="modal-title fw-semibold text-lg" id="assignTrainerModalLabel">Tugaskan Trainer ke Kelas</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('dashboard/pembelajaran/trainer/assign') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-24">
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-sm mb-8" for="modal_class_id">Pilih Kelas Pembelajaran <span class="text-danger-main">*</span></label>
                        <select id="modal_class_id" name="class_id" class="form-select radius-8" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($classes as $cls): ?>
                                <option value="<?= esc($cls['id']) ?>">
                                    <?= esc($cls['name']) ?> (<?= esc($cls['code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-sm mb-8" for="modal_trainer_id">Pilih Trainer / Pengajar <span class="text-danger-main">*</span></label>
                        <select id="modal_trainer_id" name="trainer_user_id" class="form-select radius-8" required>
                            <option value="">-- Pilih Trainer --</option>
                            <?php foreach ($trainers as $trn): ?>
                                <option value="<?= esc($trn['id']) ?>">
                                    <?= esc($trn['name']) ?> (<?= esc($trn['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-secondary-light mt-4 d-block">Hanya user aktif dengan role trainer yang ditampilkan.</small>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Penugasan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
