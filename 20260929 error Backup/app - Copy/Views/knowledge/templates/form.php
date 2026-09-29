<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="card p-24 radius-12">
    <h6 class="mb-24">Tambah Template</h6>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <form action="<?= route_to('knowledge.templates.store') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="mb-16">
            <label class="form-label">Kategori</label>
            <select name="category_id" class="form-select">
                <?php foreach ($categories as $category): ?>
                    <option value="<?= esc($category['id']) ?>"><?= esc($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-16">
            <label class="form-label">Judul</label>
            <input type="text" name="title" class="form-control" value="<?= esc(old('title')) ?>">
        </div>
        <div class="mb-16">
            <label class="form-label">File (PDF/Word/Excel, maks 10MB)</label>
            <input type="file" name="file" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary px-24 py-12 radius-8">Simpan</button>
    </form>
</div>

<?= $this->endSection(); ?>
