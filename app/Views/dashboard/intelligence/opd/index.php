<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/intelligence/_nav_intelligence', ['activeSub' => 'opd']) ?>
<?php
$flashMsg = session()->getFlashdata('message');
$flashErr = session()->getFlashdata('error');
?>

<!-- Alerts -->
<?php if ($flashMsg): ?>
    <div class="alert alert-success bg-success-50 text-success-600 border-success-200 px-24 py-11 mb-24 radius-8 d-flex align-items-center justify-content-between" role="alert">
        <div class="d-flex align-items-center gap-12">
            <iconify-icon icon="solar:check-circle-bold" class="text-xl"></iconify-icon>
            <span class="text-md fw-medium"><?= esc($flashMsg) ?></span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($flashErr): ?>
    <div class="alert alert-danger bg-danger-50 text-danger-600 border-danger-200 px-24 py-11 mb-24 radius-8 d-flex align-items-center justify-content-between" role="alert">
        <div class="d-flex align-items-center gap-12">
            <iconify-icon icon="solar:danger-circle-bold" class="text-xl"></iconify-icon>
            <span class="text-md fw-medium"><?= esc($flashErr) ?></span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'OPD dan Unit Kerja') ?></h6>
        <p class="text-secondary-light mb-0">Kelola pemetaan OPD dan unit organisasi untuk analisis Government Intelligence Pemerintah Provinsi Lampung.</p>
    </div>
    <a href="<?= site_url('dashboard/intelligence/opd-unit/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-8">
        <iconify-icon icon="solar:add-circle-bold" class="text-lg"></iconify-icon>
        <span>Tambah OPD / Unit</span>
    </a>
</div>

<!-- Filter Toolbar -->
<div class="card mb-24">
    <div class="card-body p-20">
        <form method="get" action="<?= site_url('dashboard/intelligence/opd-unit') ?>" class="row gy-3 align-items-center">
            <div class="col-md-3">
                <select name="type" class="form-select form-select-sm radius-8" onchange="this.form.submit()">
                    <option value="all">Semua Tipe Unit</option>
                    <option value="Biro" <?= (($filters['type'] ?? '') === 'Biro') ? 'selected' : '' ?>>Biro</option>
                    <option value="Dinas" <?= (($filters['type'] ?? '') === 'Dinas') ? 'selected' : '' ?>>Dinas</option>
                    <option value="Badan" <?= (($filters['type'] ?? '') === 'Badan') ? 'selected' : '' ?>>Badan</option>
                </select>
            </div>
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <input type="search" name="q" value="<?= esc($filters['q'] ?? '') ?>" class="form-control radius-8" placeholder="Cari nama unit atau kode singkatan...">
                    <button class="btn btn-primary-600 px-12" type="submit">
                        <iconify-icon icon="solar:magnifer-outline"></iconify-icon>
                    </button>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <?php if (! empty($filters['type']) || ! empty($filters['q'])): ?>
                    <a href="<?= site_url('dashboard/intelligence/opd-unit') ?>" class="btn btn-sm btn-outline-neutral-300 radius-8 px-12 py-6">
                        Reset Filter
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-body p-24">
        <div class="d-flex justify-content-between align-items-center mb-16">
            <h6 class="text-lg mb-0">Daftar OPD / Unit Organisasi</h6>
            <span class="text-sm text-secondary-light">Total <?= count($opds ?? []) ?> unit terdaftar</span>
        </div>

        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 100px;">Kode</th>
                        <th>Nama OPD / Satuan Kerja</th>
                        <th>Tipe</th>
                        <th class="text-center">Personel JF</th>
                        <th class="text-center">Sertifikasi</th>
                        <th class="text-center">Defisit Gap</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($opds)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-40">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <span class="w-56-px h-56-px bg-neutral-100 text-secondary-light rounded-circle d-inline-flex align-items-center justify-content-center mb-12">
                                        <iconify-icon icon="solar:buildings-2-outline" class="text-2xl"></iconify-icon>
                                    </span>
                                    <h6 class="text-md fw-semibold mb-4">Tidak Ada Data Unit Kerja</h6>
                                    <p class="text-secondary-light text-sm mb-0">Tidak ditemukan OPD yang sesuai dengan filter.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($opds as $opd): ?>
                            <tr>
                                <td>
                                    <span class="text-primary-600 fw-bold font-monospace"><?= esc($opd['code'] ?: 'OPD') ?></span>
                                </td>
                                <td>
                                    <a href="<?= site_url('dashboard/intelligence/opd-unit/' . $opd['id']) ?>" class="fw-semibold text-primary-light">
                                        <?= esc($opd['name']) ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-neutral-100 text-primary-light px-8 py-4 radius-4 text-xs">
                                        <?= esc($opd['type'] ?: 'Dinas') ?>
                                    </span>
                                </td>
                                <td class="text-center fw-medium">
                                    <?= (int) ($opd['personnel_count'] ?? 0) ?> Org
                                </td>
                                <td class="text-center">
                                    <span class="text-xs fw-semibold text-success-main">
                                        <?= (int) ($opd['certificates_count'] ?? 0) ?> Sertifikat
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php $gap = (int) ($opd['gap_count'] ?? 0); ?>
                                    <span class="fw-bold <?= $gap > 0 ? 'text-danger-main' : 'text-success-main' ?>">
                                        <?= $gap > 0 ? "-{$gap} Org" : 'Terpenuhi' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('dashboard/intelligence/opd-unit/' . $opd['id']) ?>" class="btn btn-sm btn-outline-primary-600 radius-6 px-12 py-4">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
