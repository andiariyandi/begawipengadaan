<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<?php $article = $article ?? null; ?>

<div class="card p-24 radius-12">
    <h6 class="mb-24"><?= $article ? 'Edit' : 'Tambah' ?> Artikel</h6>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <form action="<?= $article ? route_to('knowledge.articles.update', $article['id']) : route_to('knowledge.articles.store') ?>" method="post">
        <?= csrf_field() ?>

        <div class="mb-16">
            <label class="form-label">Kategori</label>
            <select name="category_id" class="form-select">
                <?php foreach ($categories as $category): ?>
                    <option value="<?= esc($category['id']) ?>" <?= (isset($article['category_id']) && (string) $article['category_id'] === (string) $category['id']) ? 'selected' : '' ?>>
                        <?= esc($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-16">
            <label class="form-label">Judul</label>
            <input type="text" name="title" class="form-control" value="<?= esc($article['title'] ?? old('title')) ?>">
        </div>

        <div class="mb-16">
            <label class="form-label">Isi Artikel</label>
            <textarea name="content" class="form-control" rows="10"><?= esc($article['content'] ?? old('content')) ?></textarea>
        </div>

        <div class="mb-16">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <?php $currentStatus = $article['status'] ?? 'draft'; ?>
                <option value="draft" <?= $currentStatus === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= $currentStatus === 'published' ? 'selected' : '' ?>>Published</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary px-24 py-12 radius-8">Simpan</button>
    </form>
</div>

<?= $this->endSection(); ?>
