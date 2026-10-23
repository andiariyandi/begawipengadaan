<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/users/_nav_access', ['activeSub' => 'verification']) ?>
<?php $items = $items ?? []; $filters = $filters ?? []; $statuses = $statuses ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Antrean Verifikasi Profil</h6>
        <p class="text-secondary-light mb-0">Tinjau kelengkapan dan validitas profil pengguna.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/akses/pengguna?tab=verification') ?>" class="btn btn-primary-600 radius-8 px-16 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:users-group-rounded-outline" class="text-lg"></iconify-icon>
            <span>Buka di Pengguna Terpadu</span>
        </a>
        <a href="<?= site_url('dashboard/akses/permohonan-perubahan-profil') ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:user-hand-up-outline" class="text-lg"></iconify-icon>
            <span>Permohonan Perubahan Profil</span>
        </a>
    </div>
</div>

<div class="alert alert-info bg-info-50 text-info-900 border-info-100 radius-8 p-16 mb-24 d-flex flex-wrap align-items-center justify-content-between gap-12">
    <div class="d-flex align-items-center gap-2">
        <iconify-icon icon="solar:info-circle-outline" class="text-info-600 text-xl flex-shrink-0"></iconify-icon>
        <span class="text-sm">
            <strong>Pemberitahuan Terpadu:</strong> Verifikasi profil kini telah disatukan secara terpadu di menu <strong>Pengguna &amp; Verifikasi</strong>. Menyetujui akun pengguna otomatis menyetujui profil terkait.
        </span>
    </div>
    <a href="<?= site_url('dashboard/akses/pengguna?tab=verification') ?>" class="btn btn-sm btn-primary-600 radius-6 px-12 py-6 text-xs d-inline-flex align-items-center gap-1">
        <span>Lihat Tab Antrean Verifikasi</span>
        <iconify-icon icon="solar:arrow-right-outline"></iconify-icon>
    </a>
</div>

<form action="<?= site_url('dashboard/akses/verifikasi-profil') ?>" method="get" class="mb-24">
    <div class="row g-12 align-items-end">
        <div class="col-md-4">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Verifikasi</label>
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
                        <th>Pemohon</th>
                        <th>Email</th>
                        <th>Tipe Profil</th>
                        <th>Status</th>
                        <th>Diajukan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="6" class="p-0">
                                <?= view('dashboard/components/empty_state', [
                                    'title'   => 'Tidak Ada Antrean Verifikasi',
                                    'message' => 'Saat ini tidak ada permohonan verifikasi profil yang menunggu tindakan.',
                                    'icon'    => 'solar:user-check-rounded-outline',
                                ]) ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><h6 class="text-md mb-0"><?= esc($item['user_name'] ?? $item['full_name'] ?? '-') ?></h6></td>
                            <td><?= esc($item['email'] ?? '-') ?></td>
                            <td><?= esc(ucfirst($item['profile_type'] ?? $item['profile_category'] ?? '-')) ?></td>
                            <td><?= view('dashboard/components/status_badge', ['status' => $item['status'] ?? $item['account_status'] ?? 'pending']) ?></td>
                            <td><?= esc(date('d M Y H:i', strtotime($item['created_at']))) ?></td>
                            <td class="text-center"><a href="<?= site_url('dashboard/akses/verifikasi-profil/' . (int) ($item['id'] ?? 0)) ?>" class="text-primary-600">Tinjau</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>