<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="card p-24 radius-12">
    <h6 class="mb-24"><?= $course ? 'Edit' : 'Tambah' ?> Program Pelatihan</h6>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <form action="<?= $course ? route_to('learning.courses.update', $course['id']) : route_to('learning.courses.store') ?>" method="post">
        <?= csrf_field() ?>

        <div class="mb-16">
            <label class="form-label">Judul Program</label>
            <input type="text" name="title" class="form-control" value="<?= esc($course['title'] ?? old('title')) ?>">
        </div>
        <div class="mb-16">
            <label class="form-label">Kategori</label>
            <input type="text" name="category" class="form-control" value="<?= esc($course['category'] ?? '') ?>">
        </div>
        <div class="mb-16">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="4"><?= esc($course['description'] ?? '') ?></textarea>
        </div>
        <?php if ($course): ?>
        <div class="mb-16">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <?php foreach (['draft', 'published', 'archived'] as $s): ?>
                    <option value="<?= $s ?>" <?= $course['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary px-24 py-12 radius-8">Simpan</button>
    </form>
</div>

<?= $this->endSection(); ?>
