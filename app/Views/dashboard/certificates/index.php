<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$certificates   = $certificates ?? [];
$stats          = $stats ?? ['total' => count($certificates), 'issued' => 0, 'revoked' => 0, 'pending_approval' => 0];
$categoryFilter = $categoryFilter ?? 'all';
$statusFilter   = $statusFilter ?? 'all';
$dateFrom       = $dateFrom ?? '';
$dateTo         = $dateTo ?? '';
?>

<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Riwayat & Arsip Sertifikat PBJ</h6>
        <p class="text-secondary-light mb-0">Arsip seluruh sertifikat yang pernah diterbitkan untuk kegiatan pembelajaran dan event BegawiPBJ.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/sertifikat') ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-9 d-flex align-items-center gap-2 text-sm">
            <iconify-icon icon="solar:chart-2-outline" class="text-lg"></iconify-icon>
            Overview
        </a>
        <a href="<?= site_url('dashboard/sertifikat/batch') ?>" class="btn btn-primary-600 radius-8 px-16 py-9 d-flex align-items-center gap-2 text-sm">
            <iconify-icon icon="solar:layers-minimalistic-outline" class="text-lg"></iconify-icon>
            Batch Generator
        </a>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3 mb-24">
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Total Arsip Sertifikat</span>
                    <h5 class="fw-semibold mb-0"><?= number_format($stats['total'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-primary-50 text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:diploma-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Sertifikat Aktif</span>
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
                    <span class="text-secondary-light text-sm mb-1 d-block">Sertifikat Dicabut</span>
                    <h5 class="fw-semibold text-danger-main mb-0"><?= number_format($stats['revoked'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-danger-50 text-danger-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:close-circle-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Antrean Approval</span>
                    <h5 class="fw-semibold text-warning-main mb-0"><?= number_format($stats['pending_approval'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-warning-50 text-warning-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:clock-circle-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Toolbar -->
<div class="card radius-12 mb-24 border">
    <div class="card-body p-20">
        <form method="get" action="<?= site_url('dashboard/sertifikat/riwayat') ?>">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-sm fw-medium mb-1">Pencarian</label>
                    <div class="position-relative">
                        <input type="text" name="keyword" class="form-control radius-8 ps-40" placeholder="Nomor, nama penerima..." value="<?= esc($keyword ?? '') ?>">
                        <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light text-lg"></iconify-icon>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-sm fw-medium mb-1">Kategori Sumber</label>
                    <select name="category" class="form-select radius-8">
                        <option value="all" <?= $categoryFilter === 'all' ? 'selected' : '' ?>>Semua Kategori</option>
                        <option value="learning" <?= $categoryFilter === 'learning' ? 'selected' : '' ?>>Learning (LMS)</option>
                        <option value="event" <?= $categoryFilter === 'event' ? 'selected' : '' ?>>Event / Agenda</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-sm fw-medium mb-1">Status Sertifikat</label>
                    <select name="status" class="form-select radius-8">
                        <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>Semua Status</option>
                        <option value="issued"  <?= $statusFilter === 'issued'  ? 'selected' : '' ?>>Aktif (Issued)</option>
                        <option value="revoked" <?= $statusFilter === 'revoked' ? 'selected' : '' ?>>Dicabut (Revoked)</option>
                        <option value="expired" <?= $statusFilter === 'expired' ? 'selected' : '' ?>>Kedaluwarsa</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm fw-medium mb-1">Tanggal Terbit (Dari - Sampai)</label>
                    <div class="d-flex gap-2">
                        <input type="date" name="date_from" class="form-control radius-8 px-8 py-6 text-xs" value="<?= esc($dateFrom) ?>" title="Dari tanggal">
                        <input type="date" name="date_to" class="form-control radius-8 px-8 py-6 text-xs" value="<?= esc($dateTo) ?>" title="Sampai tanggal">
                    </div>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 flex-grow-1 d-flex align-items-center justify-content-center gap-1 text-sm py-10">
                        <iconify-icon icon="solar:filter-outline"></iconify-icon> Filter
                    </button>
                    <a href="<?= site_url('dashboard/sertifikat/riwayat') ?>" class="btn btn-outline-neutral-300 radius-8 px-12 py-10" title="Reset filter">
                        <iconify-icon icon="solar:restart-outline"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table of Certificates -->
<div class="card radius-12 border">
    <div class="card-body p-0">
        <?php if (empty($certificates)): ?>
            <div class="p-32 text-center text-secondary-light">
                <iconify-icon icon="solar:diploma-outline" class="text-5xl text-secondary-light mb-2 d-block mx-auto"></iconify-icon>
                <h6 class="fw-semibold mb-1">Tidak Ada Data Sertifikat</h6>
                <p class="text-xs mb-0">Belum ada sertifikat yang cocok dengan kriteria pencarian dan filter saat ini.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table bordered-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="text-secondary-light text-xs fw-semibold">NOMOR SERTIFIKAT</th>
                            <th class="text-secondary-light text-xs fw-semibold">PENERIMA</th>
                            <th class="text-secondary-light text-xs fw-semibold">SUMBER / KEGIATAN</th>
                            <th class="text-secondary-light text-xs fw-semibold">TANGGAL TERBIT</th>
                            <th class="text-secondary-light text-xs fw-semibold text-center">STATUS</th>
                            <th class="text-secondary-light text-xs fw-semibold text-center">AKSI</th>
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
                                    <a href="<?= site_url('dashboard/sertifikat/' . $certificate['id']) ?>" class="fw-bold font-monospace text-sm text-primary-600 hover-underline d-block">
                                        <?= esc($certificate['certificate_number']) ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="w-32-px h-32-px bg-primary-50 text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-xs fw-bold flex-shrink-0">
                                            <?= esc($initials) ?>
                                        </div>
                                        <div>
                                            <span class="d-block fw-semibold text-sm text-primary-light"><?= esc($userName) ?></span>
                                            <span class="d-block text-xs text-secondary-light"><?= esc($certificate['user_email'] ?? '') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (($certificate['source_type'] ?? '') === 'learning_enrollment'): ?>
                                        <span class="badge bg-primary-50 text-primary-600 px-8 py-2 radius-4 text-xs mb-1 d-inline-block">Learning</span>
                                    <?php else: ?>
                                        <span class="badge bg-info-50 text-info-600 px-8 py-2 radius-4 text-xs mb-1 d-inline-block">Event</span>
                                    <?php endif; ?>
                                    <span class="d-block text-xs text-primary-light fw-medium text-truncate" style="max-width: 240px;">
                                        <?= esc($certificate['source_label'] ?? '—') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-xs text-secondary-light">
                                        <?= esc($certificate['issued_at'] ? date('d M Y, H:i', strtotime($certificate['issued_at'])) : '—') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($certificate['status'] === 'issued'): ?>
                                        <span class="badge bg-success-50 text-success-600 px-10 py-4 radius-4 fw-medium text-xs">Aktif</span>
                                    <?php elseif ($certificate['status'] === 'revoked'): ?>
                                        <span class="badge bg-danger-50 text-danger-600 px-10 py-4 radius-4 fw-medium text-xs" title="<?= esc($certificate['revocation_reason'] ?? '') ?>">
                                            Dicabut
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-50 text-warning-600 px-10 py-4 radius-4 fw-medium text-xs">
                                            <?= esc(ucfirst($certificate['status'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-2">
                                        <a href="<?= site_url('dashboard/sertifikat/' . $certificate['id']) ?>" class="w-32-px h-32-px bg-primary-50 text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-md hover-scale" title="Detail Sertifikat">
                                            <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                        </a>
                                        <a href="<?= site_url('dashboard/sertifikat/' . $certificate['id'] . '/unduh') ?>" class="w-32-px h-32-px bg-success-50 text-success-600 rounded-circle d-flex align-items-center justify-content-center text-md hover-scale" title="Unduh PDF">
                                            <iconify-icon icon="solar:download-minimalistic-outline"></iconify-icon>
                                        </a>
                                        <?php if ($certificate['status'] === 'issued'): ?>
                                            <button type="button" class="w-32-px h-32-px bg-danger-50 text-danger-600 rounded-circle d-flex align-items-center justify-content-center text-md border-0"
                                                    title="Cabut Sertifikat"
                                                    onclick="handleRevoke(<?= (int) $certificate['id'] ?>, '<?= esc($certificate['certificate_number']) ?>')">
                                                <iconify-icon icon="solar:close-circle-outline"></iconify-icon>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if (! empty($pager)): ?>
            <div class="p-20 border-top">
                <?= $pager->links('certificates', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Revoke Confirmation Modal -->
<div class="modal fade" id="revokeModal" tabindex="-1" aria-labelledby="revokeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" id="revokeForm" class="modal-content radius-12 border-0">
            <?= csrf_field() ?>
            <div class="modal-header border-bottom py-16 px-24">
                <h6 class="modal-title fw-semibold text-danger-main" id="revokeModalLabel">
                    <iconify-icon icon="solar:danger-triangle-bold" class="me-1"></iconify-icon>
                    Cabut Sertifikat
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <p class="text-sm text-secondary-light mb-16">
                    Apakah Anda yakin ingin mencabut sertifikat nomor <strong id="revokeCertNumber" class="text-primary-light font-monospace"></strong>? Tindakan pencabutan ini permanen dan mengubah status dokumen menjadi tidak berlaku.
                </p>
                <div>
                    <label class="form-label text-sm fw-medium mb-1">Alasan Pencabutan <span class="text-danger-main">*</span></label>
                    <textarea name="revocation_reason" class="form-control radius-8 py-10" rows="3" placeholder="Tuliskan alasan resmi pencabutan sertifikat..." required minlength="5"></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-12 px-24">
                <button type="button" class="btn btn-outline-secondary radius-8 px-16 py-8 text-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger-600 radius-8 px-16 py-8 text-sm">Ya, Cabut Sertifikat</button>
            </div>
        </form>
    </div>
</div>

<script>
function handleRevoke(certId, certNumber) {
    document.getElementById('revokeCertNumber').innerText = certNumber;
    document.getElementById('revokeForm').action = '<?= site_url('dashboard/sertifikat') ?>/' + certId + '/cabut';
    var myModal = new bootstrap.Modal(document.getElementById('revokeModal'));
    myModal.show();
}
</script>

<?= $this->endSection() ?>