<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$roles = $roles ?? [];
$pager = $pager ?? null;
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Role dan Permission</h6>
        <p class="text-secondary-light mb-0">Kelola hierarki role strategis dan matriks hak akses platform BegawiPBJ.</p>
    </div>
    <div class="d-flex gap-12">
        <a href="<?= site_url('dashboard/akses/role/matrix') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:grid-outline" class="text-lg"></iconify-icon>
            <span>Matriks Role</span>
        </a>
        <a href="<?= site_url('dashboard/akses/permission') ?>" class="btn btn-outline-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:key-minimalistic-square-outline" class="text-lg"></iconify-icon>
            <span>Daftar Permission</span>
        </a>
        <a href="<?= site_url('dashboard/akses/role/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline" class="text-lg"></iconify-icon>
            <span>Tambah Role</span>
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
        <h6 class="text-md mb-0">Daftar Role Terdaftar</h6>
        <span class="badge bg-primary-50 text-primary-600 px-12 py-6 radius-8 fw-semibold text-xs">
            Total <?= count($roles) ?> Role
        </span>
    </div>

    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Nama Role</th>
                        <th>Kode Kunci (Key)</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Jumlah Permission</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($roles)): ?>
                        <tr>
                            <td colspan="6" class="p-0">
                                <?= view('dashboard/components/empty_state', [
                                    'title'       => 'Belum Ada Role',
                                    'message'     => 'Belum ada role yang terdaftar di sistem.',
                                    'actionUrl'   => site_url('dashboard/akses/role/tambah'),
                                    'actionLabel' => 'Tambah Role Baru',
                                ]) ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($roles as $role): ?>
                            <tr>
                                <td>
                                    <h6 class="text-md mb-0 fw-medium">
                                        <a href="<?= site_url('dashboard/akses/role/' . (int) $role['id']) ?>" class="text-primary-600">
                                            <?= esc($role['name']) ?>
                                        </a>
                                    </h6>
                                </td>
                                <td>
                                    <span class="badge bg-neutral-100 text-secondary-light px-10 py-4 radius-4 fw-medium text-xs">
                                        <?= esc($role['key']) ?>
                                    </span>
                                </td>
                                <td><?= esc($role['description'] ?? '-') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-info-focus text-info-main px-12 py-4 rounded-pill fw-medium text-xs">
                                        <?= esc((string) ($role['permission_count'] ?? 0)) ?> izin
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', ['status' => 'active']) ?>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-outline-neutral-200 btn-sm radius-8 px-8 py-4 text-secondary-light" data-bs-toggle="dropdown" aria-expanded="false">
                                            <iconify-icon icon="solar:menu-dots-bold" class="text-lg"></iconify-icon>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8 p-8 border">
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= site_url('dashboard/akses/role/' . (int) $role['id']) ?>">
                                                    <iconify-icon icon="solar:eye-outline" class="text-lg text-primary-600"></iconify-icon>
                                                    <span>Detail Role</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= site_url('dashboard/akses/role/' . (int) $role['id'] . '/ubah') ?>">
                                                    <iconify-icon icon="solar:pen-new-square-outline" class="text-lg text-warning-main"></iconify-icon>
                                                    <span>Ubah Role</span>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider my-4"></li>
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= site_url('dashboard/akses/role/matriks') ?>">
                                                    <iconify-icon icon="solar:shield-keyhole-outline" class="text-lg text-info-main"></iconify-icon>
                                                    <span>Matriks Permission</span>
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
                <?= $pager->links('roles', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
