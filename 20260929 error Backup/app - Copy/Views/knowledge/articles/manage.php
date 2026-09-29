<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="mb-24 d-flex align-items-center justify-content-between">
    <h6 class="mb-0">Kelola Artikel</h6>
    <a href="<?= route_to('knowledge.articles.create') ?>" class="btn btn-primary px-24 py-12 radius-8">Tulis Artikel</a>
</div>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success radius-12 mb-16"><?= esc(session()->getFlashdata('message')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card p-16 radius-12 mb-24">
    <form method="get" class="row g-3">
        <div class="col-md-8">
            <input type="search" name="q" class="form-control" placeholder="Cari judul atau isi artikel..." value="<?= esc($keyword ?? '') ?>">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="draft" <?= ($status ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= ($status ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
            </select>
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary w-100">Cari</button>
        </div>
    </form>
</div>

<?php if (empty($articles)): ?>
    <p class="text-secondary-light">Belum ada artikel.</p>
<?php else: ?>
    <div class="card p-0 radius-12">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th class="text-center">Kategori</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Dibaca</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                        <tr>
                            <td><?= esc($article['title']) ?></td>
                            <td class="text-center"><?= esc($article['category_name']) ?></td>
                            <td class="text-center">
                                <?php if ($article['status'] === 'published'): ?>
                                    <span class="badge bg-success-focus text-success-600 px-16 py-6 radius-4">Published</span>
                                <?php else: ?>
                                    <span class="badge bg-neutral-200 text-neutral-600 px-16 py-6 radius-4">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?= esc($article['view_count']) ?></td>
                            <td class="text-center">
                                <a href="<?= route_to('knowledge.articles.edit', $article['id']) ?>" class="btn btn-outline-primary btn-sm px-16 py-8 radius-8">Edit</a>
                                <form action="<?= route_to('knowledge.articles.delete', $article['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus artikel ini?');">
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