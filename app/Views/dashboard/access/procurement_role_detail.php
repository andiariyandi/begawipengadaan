<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Peran Pengadaan</h6>
        <p class="text-secondary-light mb-0">ID Peran: <?= esc($role['id'] ?? $procurementRoleId ?? '-') ?></p>
    </div>
    <div class="d-flex align-items-center gap-12">
        <a href="<?= site_url('dashboard/akses/peran-pengadaan') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
        <?php if (!empty($role['id'])): ?>
            <a href="<?= site_url('dashboard/akses/peran-pengadaan/' . (int) $role['id'] . '/ubah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
                <iconify-icon icon="solar:pen-linear"></iconify-icon>
                <span>Ubah Peran</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if ($message = session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-20 radius-8 py-12 px-16" role="alert">
        <iconify-icon icon="solar:check-circle-bold" class="text-xl text-success-main flex-shrink-0"></iconify-icon>
        <div class="flex-grow-1 text-sm"><?= esc($message) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card mb-24">
    <div class="card-body p-24">
        <div class="row gy-20">
            <div class="col-md-6">
                <p class="text-secondary-light mb-4 text-sm">Kode Peran</p>
                <h6 class="mb-0 text-primary-600 fw-bold"><?= esc($role['key'] ?? '-') ?></h6>
            </div>
            <div class="col-md-6">
                <p class="text-secondary-light mb-4 text-sm">Status Keaktifan</p>
                <div class="d-flex align-items-center gap-12">
                    <?= view('dashboard/components/status_badge', ['status' => !empty($role['is_active']) ? 'active' : 'inactive']) ?>
                    <?php if (!empty($role['id'])): ?>
                        <?php if (!empty($role['is_active'])): ?>
                            <form action="<?= site_url('dashboard/akses/peran-pengadaan/' . (int) $role['id'] . '/nonaktifkan') ?>" method="post" class="d-inline" onsubmit="return confirm('Nonaktifkan peran ini?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger-600 radius-6 px-12 py-4">
                                    Nonaktifkan
                                </button>
                            </form>
                        <?php else: ?>
                            <form action="<?= site_url('dashboard/akses/peran-pengadaan/' . (int) $role['id'] . '/aktifkan') ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-success-main radius-6 px-12 py-4">
                                    Aktifkan
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-12">
                <p class="text-secondary-light mb-4 text-sm">Nama Peran Pengadaan</p>
                <h5 class="mb-0 text-dark"><?= esc($role['name'] ?? '-') ?></h5>
            </div>
            <div class="col-12">
                <p class="text-secondary-light mb-4 text-sm">Deskripsi & Tugas Pokok</p>
                <p class="mb-0 text-dark"><?= nl2br(esc($role['description'] ?? 'Tidak ada deskripsi.')) ?></p>
            </div>
        </div>
    </div>
</div>

<?php if (! empty($assignments)): ?>
    <div class="card">
        <div class="card-header bg-transparent border-bottom p-20">
            <h6 class="text-md fw-bold mb-0">Pengguna Terkait Peran Ini (<?= count($assignments) ?>)</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table bordered-table sm-table mb-0">
                    <thead>
                        <tr>
                            <th>Nama Pengguna</th>
                            <th>Email</th>
                            <th>Unit Organisasi</th>
                            <th class="text-center">Status Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assignments as $a): ?>
                            <tr>
                                <td><strong><?= esc($a['user_name'] ?? '-') ?></strong></td>
                                <td><?= esc($a['email'] ?? '-') ?></td>
                                <td><?= esc($a['unit_name'] ?? '-') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-neutral-100 text-dark border radius-4 px-2 py-1"><?= esc($a['verification_status'] ?? '-') ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
