<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="card p-24 radius-12">
    <?php $caseStudy = $caseStudy ?? null; ?>
    <h6 class="mb-24"><?= $caseStudy ? 'Edit' : 'Tambah' ?> Studi Kasus</h6>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <form action="<?= $caseStudy ? route_to('knowledge.caseStudies.update', $caseStudy['id']) : route_to('knowledge.caseStudies.store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="mb-16">
            <label class="form-label">Kategori</label>
            <select name="category_id" class="form-select">
                <?php foreach ($categories as $category): ?>
                    <option value="<?= esc($category['id']) ?>" <?= isset($caseStudy['category_id']) && (string) $caseStudy['category_id'] === (string) $category['id'] ? 'selected' : '' ?>><?= esc($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-16">
            <label class="form-label">Judul</label>
            <input type="text" name="title" class="form-control" value="<?= esc($caseStudy['title'] ?? old('title')) ?>">
        </div>
        <div class="mb-16">
            <label class="form-label">Isi</label>
            <textarea name="content" class="form-control" rows="8"><?= esc($caseStudy['content'] ?? old('content')) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary px-24 py-12 radius-8">Simpan</button>
    </form>
</div>

<?= $this->endSection(); ?>
