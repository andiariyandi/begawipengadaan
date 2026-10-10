<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $items = $items ?? []; $roles = $roles ?? []; $filters = $filters ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Penugasan Peran Pengadaan</h6>
        <p class="text-secondary-light mb-0">Assign pengguna ke peran pengadaan dan kelola status verifikasi.</p>
    </div>
</div>

<div class="row gy-4 mb-24">
    <div class="col-xl-5">
        <div class="card">
            <div class="card-body p-24">
                <h6 class="text-lg mb-16">Form Penugasan</h6>
                <form action="<?= site_url('dashboard/akses/penugasan-pengadaan') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">User ID</label>
                        <input type="number" min="1" class="form-control radius-8" name="user_id" placeholder="ID pengguna" required>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Peran Pengadaan</label>
                        <select class="form-select radius-8" name="procurement_role_id" required>
                            <option value="">Pilih peran</option>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= (int) $r['id'] ?>"><?= esc($r['name']) ?> (<?= esc($r['key']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Unit Organisasi ID (Opsional)</label>
                        <input type="number" min="1" class="form-control radius-8" name="organization_unit_id" placeholder="ID unit">
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-7">
        <div class="card">
            <div class="card-body p-24">
                <h6 class="text-lg mb-16">Daftar Penugasan</h6>
                <form action="<?= site_url('dashboard/akses/penugasan-pengadaan') ?>" method="get" class="mb-16">
                    <select class="form-select radius-8 w-auto d-inline-block" name="verification_status" onchange="this.form.submit()">
                        <option value="">Semua status</option>
                        <option value="self_declared" <?= ($filters['verification_status'] ?? '') === 'self_declared' ? 'selected' : '' ?>>Self-declared</option>
                        <option value="pending" <?= ($filters['verification_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="verified" <?= ($filters['verification_status'] ?? '') === 'verified' ? 'selected' : '' ?>>Verified</option>
                        <option value="rejected" <?= ($filters['verification_status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </form>
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Peran</th>
                                <th>Unit</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($items)): ?>
                                <tr><td colspan="5" class="text-center text-secondary-light">Belum ada penugasan.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <h6 class="text-md mb-0"><?= esc($item['user_name'] ?? '-') ?></h6>
                                        <small class="text-secondary-light"><?= esc($item['email'] ?? '-') ?></small>
                                    </td>
                                    <td>
                                        <strong><?= esc($item['role_name'] ?? '-') ?></strong><br>
                                        <small class="text-secondary-light"><?= esc($item['role_key'] ?? '-') ?></small>
                                    </td>
                                    <td><?= esc($item['unit_name'] ?? '-') ?></td>
                                    <td class="text-center">
                                        <?= view('dashboard/components/status_badge', ['status' => $item['verification_status']]) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (($item['verification_status'] ?? '') === 'pending'): ?>
                                            <form action="<?= site_url('dashboard/akses/penugasan-pengadaan/' . (int) $item['id'] . '/verifikasi') ?>" method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" name="decision" value="verify" class="btn btn-success-600 btn-sm radius-8">Verify</button>
                                                <button type="submit" name="decision" value="reject" class="btn btn-danger-600 btn-sm radius-8">Reject</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-secondary-light text-xs">Selesai</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>