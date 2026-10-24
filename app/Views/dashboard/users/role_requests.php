<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/users/_nav_access', ['activeSub' => 'role-requests']) ?>
<?php $items = $items ?? []; $filters = $filters ?? []; $statuses = $statuses ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Permohonan Role</h6>
        <p class="text-secondary-light mb-0">Tinjau permintaan role Trainer atau Operator dari pengguna aktif.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/akses/role') ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:shield-user-outline" class="text-lg"></iconify-icon>
            <span>Daftar Role & Permission</span>
        </a>
    </div>
</div>

<form action="<?= site_url('dashboard/akses/permohonan-role') ?>" method="get" class="mb-24">
    <div class="row g-12 align-items-end">
        <div class="col-md-4">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
            <select class="form-select radius-8" name="status">
                <option value="">Semua</option>
                <?php foreach ($statuses as $s): ?>
                    <option value="<?= esc($s) ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= esc(ucwords(str_replace('_', ' ', $s))) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 w-100">Filter</button>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role Diminta</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Diajukan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="7" class="p-0">
                                <?= view('dashboard/components/empty_state', [
                                    'title'   => 'Tidak Ada Permohonan Role',
                                    'message' => 'Saat ini tidak ada permohonan role yang menunggu persetujuan.',
                                    'icon'    => 'solar:shield-check-outline',
                                ]) ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><h6 class="text-md mb-0"><?= esc($item['user_name'] ?? '-') ?></h6></td>
                            <td><?= esc($item['email'] ?? '-') ?></td>
                            <td>
                                <strong><?= esc($item['role_name'] ?? '-') ?></strong><br>
                                <small class="text-secondary-light"><?= esc($item['role_key'] ?? '-') ?></small>
                            </td>
                            <td><?= esc($item['reason'] ?? '-') ?: '-' ?></td>
                            <td><?= view('dashboard/components/status_badge', ['status' => $item['status'] ?? 'draft']) ?></td>
                            <td><?= esc(date('d M Y', strtotime($item['created_at']))) ?></td>
                            <td class="text-center"><a href="<?= site_url('dashboard/akses/permohonan-role/' . (int) ($item['id'] ?? 0)) ?>" class="text-primary-600">Tinjau</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>