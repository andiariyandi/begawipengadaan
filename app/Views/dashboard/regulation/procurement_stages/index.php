<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $stages = $stages ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Tahapan PBJ</h6>
        <p class="text-secondary-light mb-0">Master data tahapan Pengadaan Barang/Jasa.</p>
    </div>
    <a href="<?= site_url('dashboard/regulasi/tahapan-pbj/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Tahapan</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr><th class="text-center">Urutan</th><th>Key</th><th>Nama Tahapan</th><th>Deskripsi</th><th class="text-center">Status</th><th class="text-center">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($stages)): ?>
                        <tr><td colspan="6" class="text-center text-secondary-light">Belum ada tahapan.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($stages as $s): ?>
                        <tr>
                            <td class="text-center fw-semibold"><?= (int) ($s['sort_order'] ?? 0) ?></td>
                            <td><span class="text-primary-600 fw-semibold"><?= esc($s['key'] ?? '-') ?></span></td>
                            <td><h6 class="text-md mb-0 fw-medium"><?= esc($s['name'] ?? '-') ?></h6></td>
                            <td><?= esc($s['description'] ?? '-') ?: '-' ?></td>
                            <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => ($s['is_active'] ?? 0) ? 'active' : 'inactive']) ?></td>
                            <td class="text-center">
                                <a href="<?= site_url('dashboard/regulasi/tahapan-pbj/' . (int) $s['id'] . '/ubah') ?>" class="w-32-px h-32-px bg-warning-focus text-warning-main rounded-circle d-inline-flex align-items-center justify-content-center" title="Ubah">
                                    <iconify-icon icon="solar:pen-new-square-outline"></iconify-icon>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>