<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$formMode   = $formMode ?? 'create';
$isEdit     = $formMode === 'edit';
$competency = $competency ?? [];
$compId     = $compId ?? null;
?>

<?php if (! empty($errors ?? [])): ?>
    <div class="alert alert-danger radius-12 p-16 mb-20">
        <ul class="mb-0 ps-16 text-sm">
            <?php foreach ($errors as $e): ?>
                <li><?= esc($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Kompetensi Terkait') ?></h6>
        <p class="text-secondary-light mb-0"><?= $isEdit ? 'Ubah data standar kompetensi Pengadaan Barang/Jasa.' : 'Tambahkan unit kompetensi PBJ baru.' ?></p>
    </div>
    <a href="<?= site_url('dashboard/regulasi/kompetensi-terkait') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-10 d-flex align-items-center gap-2">
        <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
        <span>Kembali ke Daftar</span>
    </a>
</div>

<div class="card border-0 radius-16 shadow-sm bg-white">
    <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
        <iconify-icon icon="solar:medal-star-bold" class="text-primary-600 text-xl"></iconify-icon>
        <h6 class="text-base fw-bold mb-0">Formulir Kompetensi PBJ</h6>
    </div>
    <div class="card-body p-24">
        <form action="<?= $isEdit ? site_url('dashboard/regulasi/kompetensi/' . (int) $compId . '/ubah') : site_url('dashboard/regulasi/kompetensi/tambah') ?>" method="post">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kode / Key Kompetensi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control radius-8" name="key" value="<?= esc(old('key', $competency['key'] ?? '')) ?>" required maxlength="64" pattern="[A-Z0-9_]+" placeholder="KOMP_PERENCANAAN">
                    <span class="text-xs text-secondary-light mt-1 d-block">Gunakan huruf kapital & underscore.</span>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Standar Kompetensi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control radius-8" name="name" value="<?= esc(old('name', $competency['name'] ?? '')) ?>" placeholder="Contoh: Perencanaan Pengadaan Barang/Jasa Pemerintah" required maxlength="255">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi &amp; Indikator Kompetensi</label>
                    <textarea class="form-control radius-8" name="description" rows="3" placeholder="Uraian ruang lingkup keahlian dan pengetahuan yang dicakup..."><?= esc(old('description', $competency['description'] ?? '')) ?></textarea>
                </div>
                <div class="col-12">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= ($competency['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold text-sm text-dark cursor-pointer ms-2" for="is_active">
                            Kompetensi Aktif
                        </label>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-12 pt-12 border-top">
                    <a href="<?= site_url('dashboard/regulasi/kompetensi-terkait') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-11 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:disk-bold" class="text-lg"></iconify-icon>
                        <span>Simpan Kompetensi</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>