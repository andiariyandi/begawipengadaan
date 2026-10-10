<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$users = $users ?? [];
$filters = $filters ?? [];
$roles = $roles ?? [];
$profileTypes = $profileTypes ?? [];
$statuses = $statuses ?? [];
$pager = $pager ?? null;
$stats = $stats ?? [
    'total'   => count($users),
    'active'  => 0,
    'pending' => 0,
    'asn'     => 0,
];
?>

<!-- Summary Cards -->
<div class="row row-cols-xxxl-4 row-cols-lg-4 row-cols-sm-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Pengguna</p>
                        <h6 class="mb-0"><?= number_format($stats['total'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:users-group-two-rounded-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Semua akun terdaftar</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Pengguna Aktif</p>
                        <h6 class="mb-0 text-success-main"><?= number_format($stats['active'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:user-check-rounded-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Status akun aktif</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Menunggu Review</p>
                        <h6 class="mb-0 text-warning-main"><?= number_format($stats['pending'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:clock-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Perlu persetujuan / verifikasi</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Aparatur Sipil Negara</p>
                        <h6 class="mb-0 text-info-main"><?= number_format($stats['asn'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:shield-user-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Profil ASN Pengadaan</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h6 class="text-lg mb-1">Manajemen Pengguna</h6>
            <p class="text-secondary-light text-sm mb-0">Kelola akun, profil pengadaan, dan status akses platform.</p>
        </div>
        <a href="<?= site_url('dashboard/akses/pengguna/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
            <span>Tambah Pengguna</span>
        </a>
    </div>

    <div class="card-body p-24">
        <!-- Filter Form -->
        <form action="<?= site_url('dashboard/akses/pengguna') ?>" method="get" class="mb-20">
            <div class="row g-12 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pencarian</label>
                    <div class="position-relative">
                        <input type="text" class="form-control radius-8 ps-40" name="keyword"
                               value="<?= esc($filters['keyword'] ?? '') ?>" placeholder="Nama, username, email…">
                        <iconify-icon icon="solar:magnifer-outline" class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light text-lg"></iconify-icon>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tipe Profil</label>
                    <select class="form-select radius-8" name="profile_type">
                        <option value="">Semua Profil</option>
                        <?php foreach ($profileTypes as $pt): ?>
                            <option value="<?= esc($pt) ?>" <?= ($filters['profile_type'] ?? '') === $pt ? 'selected' : '' ?>><?= esc(strtoupper($pt)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Akun</label>
                    <select class="form-select radius-8" name="account_status">
                        <option value="">Semua Status</option>
                        <?php foreach ($statuses as $st): ?>
                            <option value="<?= esc($st) ?>" <?= ($filters['account_status'] ?? '') === $st ? 'selected' : '' ?>><?= esc(ucwords(str_replace('_', ' ', $st))) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Role</label>
                    <select class="form-select radius-8" name="role_id">
                        <option value="">Semua Role</option>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= (int) $r['id'] ?>" <?= (int) ($filters['role_id'] ?? 0) === (int) $r['id'] ? 'selected' : '' ?>><?= esc($r['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-1 col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Baris</label>
                    <select class="form-select radius-8" name="per_page">
                        <?php foreach ([10, 15, 25, 50] as $pp): ?>
                            <option value="<?= $pp ?>" <?= (int) ($filters['per_page'] ?? 15) === $pp ? 'selected' : '' ?>><?= $pp ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-16 py-11 flex-grow-1 d-inline-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:magnifer-outline" class="text-lg"></iconify-icon>
                        <span>Filter</span>
                    </button>
                    <a href="<?= site_url('dashboard/akses/pengguna') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-11 d-inline-flex align-items-center justify-content-center" title="Reset Filter">
                        <iconify-icon icon="solar:restart-outline" class="text-xl"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>

        <!-- Table / Empty State -->
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>
                            <a href="<?= site_url('dashboard/akses/pengguna?' . http_build_query(array_merge($filters, ['sort' => 'name', 'dir' => ($filters['sort'] ?? '') === 'name' && ($filters['dir'] ?? '') === 'asc' ? 'desc' : 'asc']))) ?>" class="text-secondary-light d-inline-flex align-items-center gap-1">
                                <span>Pengguna</span>
                                <iconify-icon icon="solar:sort-vertical-outline" class="text-xs"></iconify-icon>
                            </a>
                        </th>
                        <th>
                            <a href="<?= site_url('dashboard/akses/pengguna?' . http_build_query(array_merge($filters, ['sort' => 'profile_type', 'dir' => ($filters['sort'] ?? '') === 'profile_type' && ($filters['dir'] ?? '') === 'asc' ? 'desc' : 'asc']))) ?>" class="text-secondary-light d-inline-flex align-items-center gap-1">
                                <span>Tipe Profil</span>
                                <iconify-icon icon="solar:sort-vertical-outline" class="text-xs"></iconify-icon>
                            </a>
                        </th>
                        <th>Role Akses</th>
                        <th>
                            <a href="<?= site_url('dashboard/akses/pengguna?' . http_build_query(array_merge($filters, ['sort' => 'created_at', 'dir' => ($filters['sort'] ?? '') === 'created_at' && ($filters['dir'] ?? '') === 'asc' ? 'desc' : 'asc']))) ?>" class="text-secondary-light d-inline-flex align-items-center gap-1">
                                <span>Terdaftar</span>
                                <iconify-icon icon="solar:sort-vertical-outline" class="text-xs"></iconify-icon>
                            </a>
                        </th>
                        <th class="text-center">
                            <a href="<?= site_url('dashboard/akses/pengguna?' . http_build_query(array_merge($filters, ['sort' => 'account_status', 'dir' => ($filters['sort'] ?? '') === 'account_status' && ($filters['dir'] ?? '') === 'asc' ? 'desc' : 'asc']))) ?>" class="text-secondary-light d-inline-flex align-items-center gap-1">
                                <span>Status</span>
                                <iconify-icon icon="solar:sort-vertical-outline" class="text-xs"></iconify-icon>
                            </a>
                        </th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="p-0">
                                <?= view('dashboard/components/empty_state', [
                                    'title'       => 'Pengguna Tidak Ditemukan',
                                    'message'     => 'Tidak ada data pengguna yang sesuai dengan kriteria filter.',
                                    'actionUrl'   => site_url('dashboard/akses/pengguna/tambah'),
                                    'actionLabel' => 'Tambah Pengguna Baru',
                                ]) ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="w-40-px h-40-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0 me-12 fw-semibold">
                                            <?= esc(strtoupper(substr($user['name'] ?? $user['username'] ?? 'U', 0, 1))) ?>
                                        </span>
                                        <div>
                                            <h6 class="text-md mb-0 fw-medium">
                                                <a href="<?= site_url('dashboard/akses/pengguna/' . (int) $user['id']) ?>" class="text-primary-600">
                                                    <?= esc($user['name'] ?? $user['username'] ?? '-') ?>
                                                </a>
                                            </h6>
                                            <span class="text-sm text-secondary-light fw-normal"><?= esc($user['email'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-neutral-100 text-secondary-light px-10 py-4 radius-4 fw-medium text-xs">
                                        <?= esc(strtoupper($user['profile_type'] ?? '-')) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (! empty($user['roles'])): ?>
                                        <span class="fw-medium text-sm"><?= esc($user['roles']) ?></span>
                                    <?php else: ?>
                                        <span class="text-secondary-light text-sm italic">Belum ada role</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc(date('d M Y', strtotime($user['created_at'] ?? 'now'))) ?></td>
                                <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => $user['account_status'] ?? 'draft']) ?></td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-outline-neutral-200 btn-sm radius-8 px-8 py-4 text-secondary-light" data-bs-toggle="dropdown" aria-expanded="false">
                                            <iconify-icon icon="solar:menu-dots-bold" class="text-lg"></iconify-icon>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8 p-8 border">
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= site_url('dashboard/akses/pengguna/' . (int) $user['id']) ?>">
                                                    <iconify-icon icon="solar:eye-outline" class="text-lg text-primary-600"></iconify-icon>
                                                    <span>Lihat Detail</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= site_url('dashboard/akses/pengguna/' . (int) $user['id'] . '/ubah') ?>">
                                                    <iconify-icon icon="solar:pen-new-square-outline" class="text-lg text-warning-main"></iconify-icon>
                                                    <span>Ubah Data</span>
                                                </a>
                                            </li>
                                            <?php if (($user['account_status'] ?? '') === 'active'): ?>
                                                <li><hr class="dropdown-divider my-4"></li>
                                                <li>
                                                    <button type="button" class="dropdown-item px-12 py-8 radius-4 text-danger-600 bg-hover-danger-100 d-flex align-items-center gap-2 text-sm w-100 text-start border-0 bg-transparent"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#confirmationActionModal"
                                                            data-action="<?= site_url('dashboard/akses/pengguna/' . (int) $user['id'] . '/suspend') ?>"
                                                            data-method="POST"
                                                            data-title="Tangguhkan Akun Pengguna"
                                                            data-message="Apakah Anda yakin ingin menangguhkan akun <?= esc($user['name'] ?? $user['username']) ?>? Pengguna tidak akan dapat masuk ke sistem."
                                                            data-btn-text="Ya, Tangguhkan"
                                                            data-btn-class="btn-danger-600"
                                                            data-icon="solar:user-block-rounded-bold"
                                                            data-icon-color="danger">
                                                        <iconify-icon icon="solar:user-block-rounded-outline" class="text-lg"></iconify-icon>
                                                        <span>Tangguhkan</span>
                                                    </button>
                                                </li>
                                            <?php elseif (($user['account_status'] ?? '') === 'suspended'): ?>
                                                <li><hr class="dropdown-divider my-4"></li>
                                                <li>
                                                    <button type="button" class="dropdown-item px-12 py-8 radius-4 text-success-600 bg-hover-success-100 d-flex align-items-center gap-2 text-sm w-100 text-start border-0 bg-transparent"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#confirmationActionModal"
                                                            data-action="<?= site_url('dashboard/akses/pengguna/' . (int) $user['id'] . '/activate') ?>"
                                                            data-method="POST"
                                                            data-title="Aktifkan Akun Pengguna"
                                                            data-message="Apakah Anda yakin ingin mengaktifkan kembali akun <?= esc($user['name'] ?? $user['username']) ?>?"
                                                            data-btn-text="Ya, Aktifkan"
                                                            data-btn-class="btn-success-600"
                                                            data-icon="solar:user-check-rounded-bold"
                                                            data-icon-color="success">
                                                        <iconify-icon icon="solar:user-check-rounded-outline" class="text-lg"></iconify-icon>
                                                        <span>Aktifkan</span>
                                                    </button>
                                                </li>
                                            <?php endif; ?>
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
                <?= $pager->links('users', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>