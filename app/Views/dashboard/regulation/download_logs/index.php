<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$logs    = $logs ?? [];
$filters = $filters ?? [];
$pager   = $pager ?? null;
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Log Unduhan Regulasi</h6>
        <p class="text-secondary-light mb-0">Riwayat audit jejak pengunduhan dokumen regulasi PBJ oleh publik dan internal.</p>
    </div>
</div>

<div class="card radius-12 mb-24 border bg-white shadow-sm">
    <div class="card-body p-20">
        <form action="<?= site_url('dashboard/regulasi/log-download') ?>" method="get">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-sm fw-medium mb-1">User ID</label>
                    <input type="number" min="1" class="form-control radius-8" name="user_id" value="<?= esc($filters['user_id'] ?? '') ?>" placeholder="ID pengguna...">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm fw-medium mb-1">Regulation ID</label>
                    <input type="number" min="1" class="form-control radius-8" name="regulation_id" value="<?= esc($filters['regulation_id'] ?? '') ?>" placeholder="ID regulasi...">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-sm fw-medium mb-1">Dari Tanggal</label>
                    <input type="date" class="form-control radius-8" name="from" value="<?= esc($filters['from'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-sm fw-medium mb-1">Sampai Tanggal</label>
                    <input type="date" class="form-control radius-8" name="to" value="<?= esc($filters['to'] ?? '') ?>">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                        <iconify-icon icon="solar:filter-outline"></iconify-icon> Filter
                    </button>
                    <a href="<?= site_url('dashboard/regulasi/log-download') ?>" class="btn btn-outline-neutral-300 radius-8" title="Reset Filter">
                        <iconify-icon icon="solar:restart-outline"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card radius-12 border bg-white shadow-sm">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="min-width: 160px;">Waktu Unduhan</th>
                        <th>Pengguna / Pengakses</th>
                        <th style="min-width: 280px;">Dokumen Regulasi</th>
                        <th class="text-center">Versi</th>
                        <th class="text-center">Tipe Akses</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-32 text-secondary-light">
                                <iconify-icon icon="solar:file-download-outline" class="text-4xl d-block mb-2 text-neutral-400"></iconify-icon>
                                Belum ada riwayat unduhan dokumen regulasi.
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td>
                                <span class="text-xs fw-semibold text-dark d-block">
                                    <?= esc($log['created_at'] ? date('d M Y H:i:s', strtotime((string) $log['created_at'])) : '-') ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="w-28-px h-28-px bg-neutral-100 text-neutral-700 rounded-circle d-flex align-items-center justify-content-center text-xs fw-bold">
                                        <iconify-icon icon="solar:user-outline"></iconify-icon>
                                    </div>
                                    <span class="text-sm fw-medium text-dark"><?= esc($log['username'] ?? $log['user_name'] ?? 'Publik (Tamu)') ?></span>
                                </div>
                            </td>
                            <td>
                                <a href="<?= site_url('dashboard/regulasi/dokumen/' . (int) ($log['regulation_id'] ?? 0)) ?>" class="fw-bold text-sm text-primary-700 text-hover-primary-900 d-block mb-1">
                                    <?= esc($log['document_number'] ?? '-') ?>
                                </a>
                                <span class="text-xs text-secondary-light text-line-2"><?= esc($log['regulation_title'] ?? '-') ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-neutral-100 text-neutral-800 radius-6 px-10 py-4 text-xs fw-bold">v<?= esc($log['version_number'] ?? '1.0') ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-50 text-primary-700 border border-primary-200 px-10 py-4 radius-6 text-xs fw-bold">
                                    <?= esc(ucwords(str_replace('_', ' ', $log['access_type'] ?? 'unduh'))) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (! empty($pager)): ?>
            <div class="card-footer border-top bg-transparent pt-16 pb-0 px-0">
                <?= $pager->links('logs', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>