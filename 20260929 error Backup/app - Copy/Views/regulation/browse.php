<?= $this->extend('layout/public'); ?>

<?= $this->section('content'); ?>

<div class="mb-24 d-flex align-items-center justify-content-between">
    <h6 class="mb-0">Repository Regulasi PBJ</h6>
    <?php if (in_array('regulation.manage', session()->get('permissions') ?? [], true)): ?>
        <a href="<?= route_to('regulation.create') ?>" class="btn btn-primary btn-sm px-16 py-8 radius-8">
            <iconify-icon icon="ic:baseline-plus"></iconify-icon> Tambah Regulasi
        </a>
    <?php endif; ?>
</div>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success radius-12 mb-16"><?= esc(session()->getFlashdata('message')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card p-24 radius-12 mb-24">
    <form method="get" class="row g-3">
        <div class="col-md-6">
            <input type="text" name="q" class="form-control" placeholder="Cari judul atau nomor regulasi..." value="<?= esc($keyword ?? '') ?>">
        </div>
        <div class="col-md-4">
            <select name="category_id" class="form-select">
                <option value="">Semua Kategori</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= esc($category['id']) ?>" <?= ((string) $categoryId === (string) $category['id']) ? 'selected' : '' ?>>
                        <?= esc($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100 px-24 py-12 radius-8">Cari</button>
        </div>
    </form>
</div>

<?php if (empty($regulations)): ?>
    <p class="text-secondary-light">Tidak ada regulasi ditemukan.</p>
<?php else: ?>
    <div class="card p-0 radius-12">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Nomor</th>
                        <th class="text-center">Kategori</th>
                        <th class="text-center">Tahun</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($regulations as $reg): ?>
                        <tr>
                            <td><a href="<?= route_to('regulation.detail', $reg['id']) ?>" class="text-primary-light fw-medium"><?= esc($reg['title']) ?></a></td>
                            <td><?= esc($reg['nomor_regulasi']) ?></td>
                            <td class="text-center"><?= esc($reg['category_name']) ?></td>
                            <td class="text-center"><?= esc($reg['tahun']) ?></td>
                            <td class="text-center">
                                <?php $isActive = $reg['status'] === 'berlaku'; ?>
                                <span class="<?= $isActive ? 'bg-success-focus text-success-600 border-success-main' : 'bg-neutral-200 text-neutral-600 border-neutral-400' ?> border px-16 py-4 radius-4 text-sm fw-medium">
                                    <?= $isActive ? 'Berlaku' : 'Tidak Berlaku' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection(); ?>