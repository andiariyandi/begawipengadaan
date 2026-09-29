<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="card p-24 radius-12">
    <h6 class="mb-24">Tambah Regulasi</h6>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <form action="<?= route_to('regulation.store') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="mb-16">
            <label class="form-label">Kategori</label>
            <select name="regulation_category_id" class="form-select">
                <?php foreach ($categories as $category): ?>
                    <option value="<?= esc($category['id']) ?>"><?= esc($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-16">
            <label class="form-label">Judul</label>
            <input type="text" name="title" class="form-control" value="<?= esc(old('title')) ?>">
        </div>
        <div class="row">
            <div class="col-md-6 mb-16">
                <label class="form-label">Nomor Regulasi</label>
                <input type="text" name="nomor_regulasi" class="form-control" value="<?= esc(old('nomor_regulasi')) ?>">
            </div>
            <div class="col-md-6 mb-16">
                <label class="form-label">Tahun</label>
                <input type="number" name="tahun" class="form-control" value="<?= esc(old('tahun')) ?>">
            </div>
        </div>
        <div class="mb-16">
            <label class="form-label">File (PDF/Word, maks 10MB)</label>
            <input type="file" name="file" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary px-24 py-12 radius-8">Simpan</button>
    </form>
</div>

<?= $this->endSection(); ?>
