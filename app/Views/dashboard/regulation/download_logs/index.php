<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $logs = $logs ?? []; $filters = $filters ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Log Download Regulasi</h6>
        <p class="text-secondary-light mb-0">Riwayat unduh dokumen regulasi oleh pengguna.</p>
    </div>
</div>

<form action="<?= site_url('dashboard/regulasi/log-download') ?>" method="get" class="mb-24">
    <div class="row g-12 align-items-end">
        <div class="col-md-3">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">User ID</label>
            <input type="number" min="1" class="form-control radius-8" name="user_id" value="<?= esc($filters['user_id'] ?? '') ?>" placeholder="ID pengguna">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Regulation ID</label>
            <input type="number" min="1" class="form-control radius-8" name="regulation_id" value="<?= esc($filters['regulation_id'] ?? '') ?>" placeholder="ID regulasi">
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Dari</label>
            <input type="date" class="form-control radius-8" name="from" value="<?= esc($filters['from'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Sampai</label>
            <input type="date" class="form-control radius-8" name="to" value="<?= esc($filters['to'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 w-100">Filter</button>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr><th>Waktu</th><th>User</th><th>Regulasi</th><th>Versi</th><th>Tipe Akses</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr><td colspan="5" class="text-center text-secondary-light">Belum ada log download.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= esc($log['created_at'] ?? '-') ?></td>
                            <td><?= esc($log['username'] ?? $log['user_name'] ?? '-') ?></td>
                            <td>
                                <h6 class="text-md mb-0 fw-medium"><?= esc($log['regulation_title'] ?? '-') ?></h6>
                                <small class="text-secondary-light"><?= esc($log['document_number'] ?? '-') ?></small>
                            </td>
                            <td>v<?= esc($log['version_number'] ?? '0') ?></td>
                            <td><span class="badge bg-primary-50 text-primary-600 px-12 py-8 radius-8"><?= esc($log['access_type'] ?? '-') ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>