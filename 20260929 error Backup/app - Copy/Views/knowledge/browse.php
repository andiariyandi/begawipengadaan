<?= $this->extend(in_array('kms.manage', session()->get('permissions') ?? [], true) ? 'layout/layout1' : 'layout/public'); ?>

<?= $this->section('content'); ?>

<div class="mb-24 d-flex align-items-center justify-content-between">
    <h6 class="mb-0">Template Dokumen PBJ</h6>
    <?php if (in_array('kms.manage', session()->get('permissions') ?? [], true)): ?>
        <a href="<?= route_to('knowledge.templates.create') ?>" class="btn btn-primary btn-sm px-16 py-8 radius-8">
            <iconify-icon icon="ic:baseline-plus"></iconify-icon> Tambah
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
            <input type="text" name="q" class="form-control" placeholder="Cari template..." value="<?= esc($keyword ?? '') ?>">
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

<?php if (empty($templates)): ?>
    <p class="text-secondary-light">Belum ada template.</p>
<?php else: ?>
<div class="card p-0 radius-12">
    <div class="table-responsive scroll-sm">
        <table class="table bordered-table sm-table mb-0">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($templates as $template): ?>
                <tr>
                    <td><?= esc($template['title']) ?></td>
                    <td><?= esc($template['category_name']) ?></td>
                    <td class="text-center">
                        <div class="d-flex align-items-center gap-10 justify-content-center">
                            <a href="<?= route_to('knowledge.templates.download', $template['id']) ?>" class="bg-info-focus text-info-600 w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                <iconify-icon icon="mdi:download"></iconify-icon>
                            </a>
                            <?php if (in_array('kms.manage', session()->get('permissions') ?? [], true)): ?>
                                <form action="<?= route_to('knowledge.templates.delete', $template['id']) ?>" method="post" onsubmit="return confirm('Hapus template ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="bg-danger-focus text-danger-600 w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle border-0">
                                        <iconify-icon icon="fluent:delete-24-regular"></iconify-icon>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection(); ?>
