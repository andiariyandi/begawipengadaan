<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$users = $users ?? [];
$filters = $filters ?? [];
$roles = $roles ?? [];
$profileTypes = $profileTypes ?? [];
$statuses = $statuses ?? [];
$pager = $pager ?? null;
?>

<div class="card">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <h6 class="text-lg mb-0">Daftar Pengguna</h6>
        <a href="<?= site_url('dashboard/akses/pengguna/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">
            <iconify-icon icon="solar:add-circle-outline" class="text-xl me-4"></iconify-icon> Tambah Pengguna
        </a>
    </div>

    <div class="card-body p-24">
        <form action="<?= site_url('dashboard/akses/pengguna') ?>" method="get" class="mb-16">
            <div class="row g-12 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kata Kunci</label>
                    <input type="text" class="form-control radius-8" name="keyword"
                           value="<?= esc($filters['keyword'] ?? '') ?>" placeholder="Nama, username, email…">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tipe Profil</label>
                    <select class="form-select radius-8" name="profile_type">
                        <option value="">Semua</option>
                        <?php foreach ($profileTypes as $pt): ?>
                            <option value="<?= esc($pt) ?>" <?= ($filters['profile_type'] ?? '') === $pt ? 'selected' : '' ?>><?= esc(ucfirst($pt)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Akun</label>
                    <select class="form-select radius-8" name="account_status">
                        <option value="">Semua</option>
                        <?php foreach ($statuses as $st): ?>
                            <option value="<?= esc($st) ?>" <?= ($filters['account_status'] ?? '') === $st ? 'selected' : '' ?>><?= esc(ucwords(str_replace('_', ' ', $st))) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Role</label>
                    <select class="form-select radius-8" name="role_id">
                        <option value="">Semua</option>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= (int) $r['id'] ?>" <?= (int) ($filters['role_id'] ?? 0) === (int) $r['id'] ? 'selected' : '' ?>><?= esc($r['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 w-100">Filter</button>
                </div>
            </div>
        </form>

        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Tipe Profil</th>
                        <th>Role</th>
                        <th>Terdaftar</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="6" class="text-center text-secondary-light">Tidak ada data pengguna.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="w-40-px h-40-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0 me-12 fw-semibold">
                                        <?= esc(strtoupper(substr($user['name'] ?? $user['username'] ?? 'U', 0, 1))) ?>
                                    </span>
                                    <div>
                                        <h6 class="text-md mb-0 fw-medium"><?= esc($user['name'] ?? $user['username'] ?? '-') ?></h6>
                                        <span class="text-sm text-secondary-light fw-medium"><?= esc($user['email'] ?? '-') ?></span>
                                    </div>
                                </div>
                            </td>
                            <td><?= esc(ucfirst($user['profile_type'] ?? '-')) ?></td>
                            <td><?= esc($user['roles'] ?? '-') ?></td>
                            <td><?= esc(date('d M Y', strtotime($user['created_at'] ?? 'now'))) ?></td>
                            <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => $user['account_status'] ?? 'draft']) ?></td>
                            <td class="text-center">
                                <a href="<?= site_url('dashboard/akses/pengguna/' . (int) $user['id']) ?>" class="w-32-px h-32-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center">
                                    <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                </a>
                                <a href="<?= site_url('dashboard/akses/pengguna/' . (int) $user['id'] . '/ubah') ?>" class="w-32-px h-32-px bg-warning-focus text-warning-main rounded-circle d-inline-flex align-items-center justify-content-center ms-6">
                                    <iconify-icon icon="solar:pen-new-square-outline"></iconify-icon>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($pager && $pager->getPageCount('users') > 1): ?>
            <div class="card-footer"><?= $pager->links('users', 'default_full') ?></div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>