<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="mb-24">
    <h6 class="mb-4">Kategori KMS</h6>
</div>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success radius-12 mb-16"><?= esc(session()->getFlashdata('message')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card p-24 radius-12 mb-24">
    <h6 class="mb-16">Tambah Kategori</h6>
    <form action="<?= route_to('knowledge.categories.store') ?>" method="post" class="d-flex gap-3">
        <?= csrf_field() ?>
        <input type="text" name="name" class="form-control" placeholder="mis. Perencanaan Pengadaan">
        <button type="submit" class="btn btn-primary px-24 py-12 radius-8 text-nowrap">Tambah</button>
    </form>
</div>

<?php if (empty($categories)): ?>
    <p class="text-secondary-light">Belum ada kategori.</p>
<?php else: ?>
    <div class="card p-0 radius-12">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?= esc($category['name']) ?></td>
                            <td class="text-center">
                                <form action="<?= route_to('knowledge.categories.delete', $category['id']) ?>" method="post" onsubmit="return confirm('Hapus kategori ini?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-danger btn-sm px-16 py-8 radius-8">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection(); ?>