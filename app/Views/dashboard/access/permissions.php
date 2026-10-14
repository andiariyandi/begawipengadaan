<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$permissions = $permissions ?? [];
$pager = $pager ?? null;
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Daftar Permission</h6>
        <p class="text-secondary-light mb-0">Daftar hak akses granular BegawiPengadaan untuk setiap modul dan aksi.</p>
    </div>
    <div class="d-flex gap-12">
        <a href="<?= site_url('dashboard/akses/role') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            <span>Kembali ke Role</span>
        </a>
        <a href="<?= site_url('dashboard/akses/permission/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline" class="text-lg"></iconify-icon>
            <span>Tambah Permission</span>
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
        <h6 class="text-md mb-0">Hak Akses Granular</h6>
        <span class="badge bg-primary-50 text-primary-600 px-12 py-6 radius-8 fw-semibold text-xs">
            Total <?= count($permissions) ?> Permission
        </span>
    </div>

    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Kode Kunci (Key)</th>
                        <th>Domain / Modul</th>
                        <th>Nama & Deskripsi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($permissions)): ?>
                        <tr>
                            <td colspan="4" class="p-0">
                                <?= view('dashboard/components/empty_state', [
                                    'title'       => 'Belum Ada Permission',
                                    'message'     => 'Belum ada permission yang terdaftar.',
                                    'actionUrl'   => site_url('dashboard/akses/permission/tambah'),
                                    'actionLabel' => 'Tambah Permission',
                                ]) ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($permissions as $permission): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-neutral-100 text-primary-600 font-monospace px-10 py-4 radius-4 fw-semibold text-xs">
                                        <?= esc($permission['key']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info-focus text-info-main px-12 py-4 rounded-pill fw-medium text-xs">
                                        <?= esc($permission['domain']) ?>
                                    </span>
                                </td>
                                <td>
                                    <h6 class="text-md mb-2 fw-medium"><?= esc($permission['name']) ?></h6>
                                    <span class="text-sm text-secondary-light"><?= esc($permission['description'] ?? '-') ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-outline-neutral-200 btn-sm radius-8 px-8 py-4 text-secondary-light" data-bs-toggle="dropdown" aria-expanded="false">
                                            <iconify-icon icon="solar:menu-dots-bold" class="text-lg"></iconify-icon>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8 p-8 border">
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= site_url('dashboard/akses/permission/' . (int) $permission['id']) ?>">
                                                    <iconify-icon icon="solar:eye-outline" class="text-lg text-primary-600"></iconify-icon>
                                                    <span>Detail Permission</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($pager): ?>
            <div class="card-footer border-top bg-transparent pt-16 pb-0 px-0">
                <?= $pager->links('permissions', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>