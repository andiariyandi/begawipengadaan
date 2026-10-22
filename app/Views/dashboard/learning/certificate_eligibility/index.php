<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$records = $records ?? [];
$stats   = $stats ?? [];
$classes = $classes ?? [];
$filters = $filters ?? [];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Kelayakan Sertifikat') ?></h6>
        <p class="text-secondary-light mb-0">Verifikasi kelulusan dan terbitkan sertifikat resmi bagi peserta yang telah memenuhi seluruh syarat pembelajaran.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <?php if (! empty($stats['eligible'])): ?>
            <form action="<?= site_url('dashboard/pembelajaran/kelayakan-sertifikat/batch-generate') ?>" method="post" onsubmit="return confirm('Terbitkan sertifikat sekaligus untuk semua peserta yang eligible saat ini?');">
                <?= csrf_field() ?>
                <?php if (! empty($filters['class_id'])): ?>
                    <input type="hidden" name="class_id" value="<?= esc($filters['class_id']) ?>">
                <?php endif; ?>
                <button type="submit" class="btn btn-success radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
                    <iconify-icon icon="solar:diploma-verified-outline" class="text-xl"></iconify-icon>
                    <span>Terbitkan Semua yang Eligible (<?= number_format($stats['eligible']) ?>)</span>
                </button>
            </form>
        <?php endif; ?>
        <a href="<?= site_url('dashboard/sertifikat') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:folder-with-files-outline" class="text-lg"></iconify-icon>
            <span>Arsip Sertifikat</span>
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
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Total entitas enrollment</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Eligible (Siap Terbit)</p>
                        <h6 class="mb-0 text-warning-main"><?= number_format($stats['eligible'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-warning-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:check-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Memenuhi syarat kelulusan</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Sudah Diterbitkan</p>
                        <h6 class="mb-0 text-success-main"><?= number_format($stats['issued'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:diploma-verified-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Memiliki nomor resmi & QR</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Belum Memenuhi</p>
                        <h6 class="mb-0 text-secondary-light"><?= number_format($stats['not_eligible'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-neutral-400 rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:close-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Sedang menempuh materi</p>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header border-bottom bg-base py-16 px-24">
        <form action="<?= site_url('dashboard/pembelajaran/kelayakan-sertifikat') ?>" method="get" class="row gy-3 gx-3 align-items-center">
            <div class="col-md-4">
                <div class="position-relative">
                    <input type="text" name="keyword" class="form-control radius-8 ps-40" placeholder="Cari peserta / kelas / nomor sertifikat..." value="<?= esc($filters['keyword'] ?? '') ?>">
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
                <select name="eligibility_status" class="form-select radius-8">
                    <option value="">Semua Status Kelayakan</option>
                    <option value="eligible" <?= (($filters['eligibility_status'] ?? '') === 'eligible') ? 'selected' : '' ?>>Eligible (Belum Diterbitkan)</option>
                    <option value="issued" <?= (($filters['eligibility_status'] ?? '') === 'issued') ? 'selected' : '' ?>>Sudah Diterbitkan (Issued)</option>
                    <option value="not_eligible" <?= (($filters['eligibility_status'] ?? '') === 'not_eligible') ? 'selected' : '' ?>>Belum Memenuhi Syarat</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary-600 radius-8 px-16 py-11 w-100">Filter</button>
                <a href="<?= site_url('dashboard/pembelajaran/kelayakan-sertifikat') ?>" class="btn btn-outline-neutral-300 radius-8 px-12 py-11" title="Reset">
                    <iconify-icon icon="solar:restart-outline" class="text-lg"></iconify-icon>
                </a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table mb-0 begawi-table" data-table-name="kelayakan-sertifikat" data-page-length="10">
                <thead>
                    <tr>
                        <th>Peserta</th>
                        <th>Kelas & Batch</th>
                        <th class="text-center">Nilai Akhir</th>
                        <th class="text-center">Status Kelayakan</th>
                        <th>Nomor Sertifikat</th>
                        <th>Tanggal Terbit / Selesai</th>
                        <th class="text-center no-sort no-export action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($records)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-secondary-light py-32">
                                <?= view('dashboard/components/empty_state', [
                                    'title'   => 'Data Tidak Ditemukan',
                                    'message' => 'Tidak ada data peserta yang sesuai dengan kriteria filter kelayakan sertifikat.',
                                    'icon'    => 'solar:diploma-outline',
                                ]) ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($records as $row): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-12">
                                        <div class="w-40-px h-40-px rounded-circle bg-primary-100 text-primary-600 d-flex justify-content-center align-items-center fw-semibold">
                                            <?= esc(strtoupper(substr($row['student_name'] ?? 'P', 0, 1))) ?>
                                        </div>
                                        <div>
                                            <h6 class="text-sm fw-semibold mb-1"><?= esc($row['student_name']) ?></h6>
                                            <span class="text-xs text-secondary-light">@<?= esc($row['username']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <h6 class="text-sm fw-semibold mb-1 text-primary-600">
                                        <?= esc($row['class_name']) ?>
                                    </h6>
                                    <div class="text-xs text-secondary-light font-monospace">Kode: <?= esc($row['class_code']) ?></div>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['final_score'] !== null): ?>
                                        <span class="badge bg-success-50 text-success-600 font-monospace px-10 py-4 radius-4 text-xs fw-bold">
                                            <?= number_format((float) $row['final_score'], 1) ?>
                                        </span>
                                        <div class="text-xxs text-secondary-light mt-1">Min: <?= number_format((float) ($row['passing_grade'] ?? 70), 0) ?></div>
                                    <?php else: ?>
                                        <span class="text-xs text-secondary-light">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['calculated_status'] === 'issued'): ?>
                                        <span class="badge bg-success-50 text-success-600 px-10 py-4 radius-4 text-xs fw-semibold d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                                            Telah Diterbitkan
                                        </span>
                                    <?php elseif ($row['calculated_status'] === 'eligible'): ?>
                                        <span class="badge bg-warning-50 text-warning-600 px-10 py-4 radius-4 text-xs fw-semibold d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:clock-circle-bold"></iconify-icon>
                                            Eligible (Siap Terbit)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-neutral-100 text-secondary-light px-10 py-4 radius-4 text-xs">
                                            Belum Memenuhi
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (! empty($row['certificate_number'])): ?>
                                        <span class="badge bg-neutral-100 text-neutral-900 font-monospace px-10 py-4 radius-4 text-xs fw-semibold">
                                            <?= esc($row['certificate_number']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs text-secondary-light italic">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (! empty($row['cert_issued_at'])): ?>
                                        <span class="text-xs fw-medium text-neutral-900"><?= date('d M Y, H:i', strtotime($row['cert_issued_at'])) ?></span>
                                    <?php elseif (! empty($row['completed_at'])): ?>
                                        <span class="text-xs text-secondary-light"><?= date('d M Y, H:i', strtotime($row['completed_at'])) ?></span>
                                    <?php else: ?>
                                        <span class="text-xs text-secondary-light">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['calculated_status'] === 'eligible'): ?>
                                        <form action="<?= site_url('dashboard/pembelajaran/kelayakan-sertifikat/generate/' . $row['id']) ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-success radius-4 px-12 py-6 d-inline-flex align-items-center gap-1" title="Terbitkan Sertifikat">
                                                <iconify-icon icon="solar:diploma-verified-outline"></iconify-icon>
                                                <span>Terbitkan</span>
                                            </button>
                                        </form>
                                    <?php elseif ($row['calculated_status'] === 'issued'): ?>
                                        <a href="<?= site_url('dashboard/sertifikat/' . $row['certificate_id']) ?>" class="btn btn-sm btn-outline-primary-600 radius-4 px-12 py-6 d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                            <span>Lihat</span>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= site_url('dashboard/pembelajaran/progres/detail/' . $row['id']) ?>" class="btn btn-sm btn-outline-neutral-300 radius-4 px-12 py-6 text-secondary-light">
                                            Cek Progres
                                        </a>
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
                <?= $pager->links('eligibility', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
