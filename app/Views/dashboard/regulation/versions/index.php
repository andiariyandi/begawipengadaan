<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $versions = $versions ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Versi Dokumen</h6>
        <p class="text-secondary-light mb-0">Riwayat versi dan perubahan dokumen regulasi.</p>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success mb-16"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr><th>Regulasi</th><th>Versi</th><th>File</th><th>Ukuran</th><th>Tanggal</th><th class="text-center">Status</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($versions)): ?>
                        <tr><td colspan="6" class="text-center text-secondary-light">Belum ada versi.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($versions as $v): ?>
                        <tr>
                            <td>
                                <h6 class="text-md mb-0 fw-medium"><?= esc($v['regulation_title'] ?? '-') ?></h6>
                                <small class="text-secondary-light"><?= esc($v['document_number'] ?? '-') ?></small>
                            </td>
                            <td>v<?= esc($v['version_number']) ?><?= $v['is_current'] ? ' <span class="badge bg-success-50 text-success-600 ms-2">Aktif</span>' : '' ?></td>
                            <td><?= esc($v['file_original_name'] ?? '-') ?: '-' ?></td>
                            <td><?= isset($v['file_size']) ? esc(round($v['file_size'] / 1024)) . ' KB' : '-' ?></td>
                            <td><?= esc($v['created_at']) ?></td>
                            <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => $v['is_current'] ? 'current' : 'archived']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>