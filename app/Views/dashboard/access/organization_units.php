<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Unit Organisasi & Perangkat Daerah (OPD)</h6>
        <p class="text-secondary-light mb-0">Master data OPD dan unit organisasi yang terhubung ke pendaftaran ASN Provinsi Lampung.</p>
    </div>
    <a href="<?= site_url('dashboard/akses/unit-organisasi/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
        <iconify-icon icon="solar:add-circle-bold" class="text-lg"></iconify-icon>
        <span>Tambah Unit</span>
    </a>
</div>

<?php if ($message = session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-20 radius-8 py-12 px-16" role="alert">
        <iconify-icon icon="solar:check-circle-bold" class="text-xl text-success-main flex-shrink-0"></iconify-icon>
        <div class="flex-grow-1 text-sm"><?= esc($message) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($error = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-20 radius-8 py-12 px-16" role="alert">
        <iconify-icon icon="solar:danger-triangle-bold" class="text-xl text-danger-main flex-shrink-0"></iconify-icon>
        <div class="flex-grow-1 text-sm"><?= esc($error) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 110px;">Kode</th>
                        <th>Nama Unit / OPD</th>
                        <th style="width: 120px;">Tipe</th>
                        <th>Induk</th>
                        <th class="text-center" style="width: 100px;">Status</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($units)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-24 text-secondary-light">Belum ada unit organisasi / OPD.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($units as $unit): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-primary-50 text-primary-600 border border-primary-100 fw-semibold px-8 py-4 radius-4">
                                        <?= esc($unit['code']) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-dark d-block"><?= esc($unit['name']) ?></strong>
                                </td>
                                <td>
                                    <span class="text-secondary-light"><?= esc($unit['type']) ?></span>
                                </td>
                                <td>
                                    <small class="text-secondary-light"><?= esc($unit['parent_name'] ?? '—') ?></small>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', ['status' => !empty($unit['is_active']) ? 'active' : 'inactive']) ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-8">
                                        <a href="<?= site_url('dashboard/akses/unit-organisasi/' . (int) $unit['id']) ?>" class="btn btn-sm btn-outline-neutral-300 radius-6 px-8 py-4" title="Detail">
                                            <iconify-icon icon="solar:eye-linear"></iconify-icon>
                                        </a>
                                        <a href="<?= site_url('dashboard/akses/unit-organisasi/' . (int) $unit['id'] . '/ubah') ?>" class="btn btn-sm btn-outline-primary-600 radius-6 px-8 py-4" title="Ubah">
                                            <iconify-icon icon="solar:pen-linear"></iconify-icon>
                                        </a>
                                        <?php if (!empty($unit['is_active'])): ?>
                                            <form action="<?= site_url('dashboard/akses/unit-organisasi/' . (int) $unit['id'] . '/nonaktifkan') ?>" method="post" class="d-inline" onsubmit="return confirm('Nonaktifkan unit ini?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger-600 radius-6 px-8 py-4" title="Nonaktifkan">
                                                    <iconify-icon icon="solar:power-bold"></iconify-icon>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form action="<?= site_url('dashboard/akses/unit-organisasi/' . (int) $unit['id'] . '/aktifkan') ?>" method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-success-main radius-6 px-8 py-4" title="Aktifkan">
                                                    <iconify-icon icon="solar:restart-bold"></iconify-icon>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php if (($pager ?? null) && $pager->getPageCount('units') > 1): ?>
    <div class="card-footer mt-16"><?= $pager->links('units', 'default_full') ?></div>
<?php endif; ?>
<?= $this->endSection() ?>
