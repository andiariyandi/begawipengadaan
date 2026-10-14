<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Unit Organisasi</h6>
        <p class="text-secondary-light mb-0">ID Unit: <?= esc($unit['id'] ?? $organizationId ?? '-') ?></p>
    </div>
    <div class="d-flex align-items-center gap-12">
        <a href="<?= site_url('dashboard/akses/unit-organisasi') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
        <?php if (!empty($unit['id'])): ?>
            <a href="<?= site_url('dashboard/akses/unit-organisasi/' . (int) $unit['id'] . '/ubah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
                <iconify-icon icon="solar:pen-linear"></iconify-icon>
                <span>Ubah Unit</span>
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

<div class="card">
    <div class="card-body p-24">
        <div class="row gy-20">
            <div class="col-md-6">
                <p class="text-secondary-light mb-4 text-sm">Kode Unit</p>
                <h6 class="mb-0 text-primary-600 fw-bold"><?= esc($unit['code'] ?? '-') ?></h6>
            </div>
            <div class="col-md-6">
                <p class="text-secondary-light mb-4 text-sm">Tipe Unit</p>
                <h6 class="mb-0 text-dark"><?= esc($unit['type'] ?? '-') ?></h6>
            </div>
            <div class="col-md-6">
                <p class="text-secondary-light mb-4 text-sm">Nama Unit Organisasi / OPD</p>
                <h5 class="mb-0 text-dark"><?= esc($unit['name'] ?? '-') ?></h5>
            </div>
            <div class="col-md-6">
                <p class="text-secondary-light mb-4 text-sm">Unit Induk</p>
                <h6 class="mb-0 text-dark"><?= esc($parent['name'] ?? '— (Tingkat Utama)') ?></h6>
            </div>
            <div class="col-md-6">
                <p class="text-secondary-light mb-4 text-sm">Status Keaktifan</p>
                <div class="d-flex align-items-center gap-12">
                    <?= view('dashboard/components/status_badge', ['status' => !empty($unit['is_active']) ? 'active' : 'inactive']) ?>
                    <?php if (!empty($unit['id'])): ?>
                        <?php if (!empty($unit['is_active'])): ?>
                            <form action="<?= site_url('dashboard/akses/unit-organisasi/' . (int) $unit['id'] . '/nonaktifkan') ?>" method="post" class="d-inline" onsubmit="return confirm('Nonaktifkan unit organisasi ini? OPD tidak akan muncul pada pilihan pendaftaran ASN.');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger-600 radius-6 px-12 py-4">
                                    Nonaktifkan
                                </button>
                            </form>
                        <?php else: ?>
                            <form action="<?= site_url('dashboard/akses/unit-organisasi/' . (int) $unit['id'] . '/aktifkan') ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-success-main radius-6 px-12 py-4">
                                    Aktifkan
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <p class="text-secondary-light mb-4 text-sm">Urutan (Sort Order)</p>
                <h6 class="mb-0 text-dark"><?= esc($unit['sort_order'] ?? '0') ?></h6>
            </div>
            <?php if (!empty($unit['address'])): ?>
                <div class="col-12">
                    <p class="text-secondary-light mb-4 text-sm">Alamat Kantor / Lokasi</p>
                    <p class="mb-0 text-dark"><?= nl2br(esc($unit['address'])) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
