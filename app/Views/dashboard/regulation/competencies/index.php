<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $competencies = $competencies ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Kompetensi Terkait</h6>
        <p class="text-secondary-light mb-0">Master kompetensi yang terkait dengan regulasi dan pembelajaran PBJ.</p>
    </div>
    <a href="<?= site_url('dashboard/regulasi/kompetensi/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Kompetensi</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Kompetensi</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Jumlah Regulasi</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($competencies)): ?>
                        <tr><td colspan="6" class="text-center text-secondary-light">Belum ada kompetensi.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($competencies as $c): ?>
                        <tr>
                            <td><span class="text-primary-600 fw-semibold"><?= esc($c['key']) ?></span></td>
                            <td><h6 class="text-md mb-0 fw-medium"><?= esc($c['name']) ?></h6></td>
                            <td><?= esc($c['description'] ?? '-') ?: '-' ?></td>
                            <td class="text-center"><?= (int) ($c['regulation_count'] ?? 0) ?></td>
                            <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => ($c['is_active'] ?? 0) ? 'active' : 'inactive']) ?></td>
                            <td class="text-center">
                                <a href="<?= site_url('dashboard/regulasi/kompetensi/' . (int) $c['id'] . '/ubah') ?>" class="w-32-px h-32-px bg-warning-focus text-warning-main rounded-circle d-inline-flex align-items-center justify-content-center" title="Ubah">
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