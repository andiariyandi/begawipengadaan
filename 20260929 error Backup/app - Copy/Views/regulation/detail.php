<?= $this->extend('layout/public'); ?>

<?= $this->section('content'); ?>

<?php $canManage = in_array('regulation.manage', session()->get('permissions') ?? [], true); ?>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success radius-12 mb-16"><?= esc(session()->getFlashdata('message')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card p-24 radius-12 mb-24">
    <div class="d-flex align-items-start justify-content-between">
        <div>
            <span class="bg-neutral-200 text-neutral-600 px-16 py-4 radius-4 text-sm mb-8 d-inline-block"><?= esc($regulation['category_name']) ?></span>
            <h5 class="mb-8"><?= esc($regulation['title']) ?></h5>
            <p class="text-secondary-light text-sm mb-0">
                Nomor <?= esc($regulation['nomor_regulasi']) ?> &middot; Tahun <?= esc($regulation['tahun']) ?> &middot;
                Diunggah oleh <?= esc($regulation['uploader_name']) ?>
            </p>
        </div>
        <?php $isActive = $regulation['status'] === 'berlaku'; ?>
        <div class="text-end">
            <span class="<?= $isActive ? 'bg-success-focus text-success-600 border-success-main' : 'bg-neutral-200 text-neutral-600 border-neutral-400' ?> border px-16 py-4 radius-4 text-sm fw-medium d-block mb-8">
                <?= $isActive ? 'Berlaku' : 'Tidak Berlaku' ?>
            </span>
            <?php if ($canManage): ?>
                <form action="<?= route_to('regulation.toggleStatus', $regulation['id']) ?>" method="post" onsubmit="return confirm('Ubah status regulasi ini?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-secondary btn-sm px-16 py-8 radius-8">
                        <?= $isActive ? 'Nonaktifkan' : 'Aktifkan' ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($canManage): ?>
    <div class="card p-24 radius-12 mb-24">
        <h6 class="mb-16">Tambah Versi Baru</h6>
        <form action="<?= route_to('regulation.versions.store', $regulation['id']) ?>" method="post" enctype="multipart/form-data" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-8">
                <label class="form-label">Catatan Perubahan</label>
                <input type="text" name="change_notes" class="form-control" placeholder="Ringkas apa yang berubah di versi ini">
            </div>
            <div class="col-md-4">
                <label class="form-label">File (PDF/Word)</label>
                <input type="file" name="file" class="form-control">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary px-24 py-12 radius-8">Unggah Versi Baru</button>
            </div>
        </form>
    </div>
<?php endif; ?>

<div class="card p-0 radius-12">
    <div class="card-header border-bottom bg-base py-16 px-24">
        <h6 class="mb-0">Riwayat Versi</h6>
    </div>
    <div class="table-responsive scroll-sm">
        <table class="table bordered-table sm-table mb-0">
            <thead>
                <tr>
                    <th>Versi</th>
                    <th>Catatan Perubahan</th>
                    <th>Diunggah Oleh</th>
                    <th class="text-center">Tanggal</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($versions as $version): ?>
                    <tr>
                        <td>v<?= esc($version['version_number']) ?></td>
                        <td><?= esc($version['change_notes']) ?></td>
                        <td><?= esc($version['uploader_name']) ?></td>
                        <td class="text-center"><?= esc(date('d M Y', strtotime($version['created_at']))) ?></td>
                        <td class="text-center">
                            <a href="<?= route_to('regulation.download', $version['id']) ?>" class="bg-info-focus text-info-600 w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle mx-auto">
                                <iconify-icon icon="mdi:download"></iconify-icon>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection(); ?>