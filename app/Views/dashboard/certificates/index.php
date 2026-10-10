<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$certificates = $certificates ?? [];
$stats        = $stats ?? ['total' => count($certificates), 'issued' => 0, 'revoked' => 0, 'pending_approval' => 0];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Manajemen Sertifikat PBJ</h6>
        <p class="text-secondary-light mb-0">Kelola penerbitan sertifikat kelulusan pelatihan, workshop, dan kompetensi PBJ.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/sertifikat/approval') ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:check-circle-outline" class="text-lg"></iconify-icon>
            Antrean Approval (<?= number_format($stats['pending_approval'] ?? 0) ?>)
        </a>
        <a href="<?= site_url('dashboard/sertifikat/pengajuan-event') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline" class="text-lg"></iconify-icon>
            Pengajuan Event
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3 mb-24">
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Total Sertifikat</span>
                    <h5 class="fw-semibold mb-0"><?= number_format($stats['total'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-info-50 text-info-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:diploma-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Diterbitkan</span>
                    <h5 class="fw-semibold text-success-main mb-0"><?= number_format($stats['issued'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-success-50 text-success-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:verified-check-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Menunggu Approval</span>
                    <h5 class="fw-semibold text-warning-main mb-0"><?= number_format($stats['pending_approval'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-warning-50 text-warning-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:clock-circle-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Sertifikat Dicabut</span>
                    <h5 class="fw-semibold text-danger-main mb-0"><?= number_format($stats['revoked'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-danger-50 text-danger-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:close-circle-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Toolbar -->
<div class="card radius-12 mb-24 border">
    <div class="card-body p-20">
        <form method="get" action="<?= site_url('dashboard/sertifikat') ?>">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label text-sm fw-medium mb-1">Pencarian</label>
                    <div class="position-relative">
                        <input type="text" name="keyword" class="form-control radius-8 ps-40" placeholder="Cari nomor sertifikat, nama penerima, atau judul program..." value="<?= esc($keyword ?? '') ?>">
                        <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light text-lg"></iconify-icon>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-sm fw-medium mb-1">Status Sertifikat</label>
                    <select name="status" class="form-select radius-8">
                        <option value="all" <?= ($statusFilter ?? 'all') === 'all' ? 'selected' : '' ?>>Semua Status</option>
                        <option value="issued"  <?= ($statusFilter ?? '') === 'issued'  ? 'selected' : '' ?>>Diterbitkan</option>
                        <option value="draft"   <?= ($statusFilter ?? '') === 'draft'   ? 'selected' : '' ?>>Draft</option>
                        <option value="revoked" <?= ($statusFilter ?? '') === 'revoked' ? 'selected' : '' ?>>Dicabut</option>
                        <option value="expired" <?= ($statusFilter ?? '') === 'expired' ? 'selected' : '' ?>>Kedaluwarsa</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                        <iconify-icon icon="solar:filter-outline"></iconify-icon> Filter
                    </button>
                    <a href="<?= site_url('dashboard/sertifikat') ?>" class="btn btn-outline-neutral-300 radius-8" title="Reset">
                        <iconify-icon icon="solar:restart-outline"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Datatable -->
<div class="card radius-12 border">
    <div class="card-body p-24">
        <?php if (empty($certificates)): ?>
            <?= view('dashboard/components/empty_state', [
                'icon'        => 'solar:diploma-outline',
                'title'       => 'Belum Ada Sertifikat',
                'message'     => 'Belum ada data sertifikat yang diterbitkan sesuai dengan filter saat ini.',
                'actionUrl'   => site_url('dashboard/sertifikat/approval'),
                'actionText'  => 'Periksa Antrean Approval',
            ]) ?>
        <?php else: ?>
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Nomor Kredensial</th>
                            <th>Penerima Sertifikat</th>
                            <th>Program / Acuan</th>
                            <th>Tanggal Terbit</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($certificates as $certificate): ?>
                            <?php
                                $userName = $certificate['user_name'] ?? 'Peserta PBJ';
                                $initials = strtoupper(substr($userName, 0, 2));
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="w-32-px h-32-px bg-primary-50 text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-sm flex-shrink-0">
                                            <iconify-icon icon="solar:diploma-verified-outline"></iconify-icon>
                                        </div>
                                        <span class="text-primary-600 fw-bold font-monospace text-sm"><?= esc($certificate['certificate_number']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="w-32-px h-32-px bg-neutral-100 text-neutral-800 rounded-circle d-flex align-items-center justify-content-center text-xs fw-bold flex-shrink-0">
                                            <?= esc($initials) ?>
                                        </div>
                                        <div>
                                            <h6 class="text-sm mb-0 fw-semibold text-neutral-900"><?= esc($userName) ?></h6>
                                            <span class="text-2xs text-secondary-light"><?= esc($certificate['user_email'] ?? '') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-medium text-sm text-neutral-800 d-block"><?= esc($certificate['source_label'] ?? '—') ?></span>
                                    <span class="text-2xs text-secondary-light"><?= esc($certificate['template_name'] ?? 'Format Standar') ?></span>
                                </td>
                                <td>
                                    <span class="text-xs text-neutral-800 fw-medium">
                                        <?= esc($certificate['issued_at'] ? date('d M Y', strtotime($certificate['issued_at'])) : '—') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', ['status' => $certificate['status']]) ?>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-outline-neutral-200 btn-sm radius-8 px-8 py-4 text-secondary-light" data-bs-toggle="dropdown" aria-expanded="false">
                                            <iconify-icon icon="solar:menu-dots-bold" class="text-lg"></iconify-icon>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8 p-8 border">
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= site_url('dashboard/sertifikat/' . $certificate['id']) ?>">
                                                    <iconify-icon icon="solar:eye-outline" class="text-lg text-primary-600"></iconify-icon>
                                                    <span>Lihat Kredensial</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= site_url('dashboard/sertifikat/' . $certificate['id'] . '/ubah') ?>">
                                                    <iconify-icon icon="solar:pen-new-square-outline" class="text-lg text-warning-main"></iconify-icon>
                                                    <span>Ubah Status</span>
                                                </a>
                                            </li>
                                            <?php if (($certificate['status'] ?? '') === 'issued'): ?>
                                                <li><hr class="dropdown-divider my-4"></li>
                                                <li>
                                                    <button type="button" class="dropdown-item px-12 py-8 radius-4 text-danger-600 bg-hover-danger-100 d-flex align-items-center gap-2 text-sm w-100 text-start border-0 bg-transparent"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#confirmationActionModal"
                                                            data-action="<?= site_url('dashboard/sertifikat/' . (int) $certificate['id'] . '/cabut') ?>"
                                                            data-method="POST"
                                                            data-title="Cabut Sertifikat"
                                                            data-message="Apakah Anda yakin ingin mencabut sertifikat nomor <?= esc($certificate['certificate_number']) ?>? Tindakan ini tidak dapat dibatalkan."
                                                            data-btn-text="Ya, Cabut"
                                                            data-btn-class="btn-danger-600"
                                                            data-icon="fluent:delete-24-regular"
                                                            data-icon-color="danger"
                                                            data-require-input="true"
                                                            data-input-label="Alasan Pencabutan *"
                                                            data-input-name="revocation_reason"
                                                            data-input-placeholder="Tuliskan alasan pencabutan sertifikat...">
                                                        <iconify-icon icon="solar:shield-cross-outline" class="text-lg"></iconify-icon>
                                                        <span>Cabut Sertifikat</span>
                                                    </button>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if (!empty($pager)): ?>
            <div class="card-footer border-top bg-transparent pt-16 pb-0 px-0">
                <?= $pager->links('certificates', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>