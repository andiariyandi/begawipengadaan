<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Peran Pengadaan</h6>
        <p class="text-secondary-light mb-0">Master data peran pengadaan barang/jasa yang terhubung ke pendaftaran ASN dan penugasan peran.</p>
    </div>
    <a href="<?= site_url('dashboard/akses/peran-pengadaan/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
        <iconify-icon icon="solar:add-circle-bold" class="text-lg"></iconify-icon>
        <span>Tambah Peran</span>
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
                        <th style="width: 120px;">Kode</th>
                        <th>Nama Peran Pengadaan</th>
                        <th>Deskripsi</th>
                        <th class="text-center" style="width: 100px;">Status</th>
                        <th class="text-center" style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($roles)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-24 text-secondary-light">Belum ada peran pengadaan yang terdaftar.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($roles as $role): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-primary-50 text-primary-600 border border-primary-100 fw-semibold px-8 py-4 radius-4">
                                        <?= esc($role['key']) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-primary-light d-block"><?= esc($role['name']) ?></strong>
                                </td>
                                <td>
                                    <small class="text-secondary-light"><?= esc(mb_strimwidth((string) ($role['description'] ?? '-'), 0, 80, '...')) ?></small>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', ['status' => !empty($role['is_active']) ? 'active' : 'inactive']) ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-8">
                                        <a href="<?= site_url('dashboard/akses/peran-pengadaan/' . (int) $role['id']) ?>" class="btn btn-sm btn-outline-neutral-300 radius-6 px-8 py-4" title="Detail">
                                            <iconify-icon icon="solar:eye-linear"></iconify-icon>
                                        </a>
                                        <a href="<?= site_url('dashboard/akses/peran-pengadaan/' . (int) $role['id'] . '/ubah') ?>" class="btn btn-sm btn-outline-primary-600 radius-6 px-8 py-4" title="Ubah">
                                            <iconify-icon icon="solar:pen-linear"></iconify-icon>
                                        </a>
                                        <?php if (!empty($role['is_active'])): ?>
                                            <form action="<?= site_url('dashboard/akses/peran-pengadaan/' . (int) $role['id'] . '/nonaktifkan') ?>" method="post" class="d-inline" onsubmit="return confirm('Nonaktifkan peran ini? Peran tidak akan muncul di form registrasi ASN.');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger-600 radius-6 px-8 py-4" title="Nonaktifkan">
                                                    <iconify-icon icon="solar:power-bold"></iconify-icon>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form action="<?= site_url('dashboard/akses/peran-pengadaan/' . (int) $role['id'] . '/aktifkan') ?>" method="post" class="d-inline">
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
<?php if (($pager ?? null) && $pager->getPageCount('procurement_roles') > 1): ?>
    <div class="card-footer mt-16"><?= $pager->links('procurement_roles', 'default_full') ?></div>
<?php endif; ?>
<?= $this->endSection() ?>
