<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/components/flash_message') ?>

<?php
$isEdit = ! empty($category);
$actionUrl = $isEdit ? site_url('dashboard/landing/kategori-layanan/ubah/' . $category['id']) : site_url('dashboard/landing/kategori-layanan/simpan');
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Form Kategori Layanan PBJ') ?></h6>
        <p class="text-secondary-light mb-0">Kelola nama klasifikasi pengadaan, kode unik, dan uraian ringkas.</p>
    </div>
    <a href="<?= site_url('dashboard/landing/kategori-layanan') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= $actionUrl ?>">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-7">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Kategori Layanan <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control radius-8" value="<?= esc($category['name'] ?? '') ?>" placeholder="Misal: Pengadaan Barang, Pekerjaan Konstruksi" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kode / Slug Unik <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control radius-8 font-monospace" value="<?= esc($category['code'] ?? '') ?>" placeholder="pengadaan-barang" required>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi Komoditas / Ruang Lingkup</label>
                    <textarea name="description" class="form-control radius-8" rows="3" placeholder="Uraikan jenis barang/jasa yang termasuk dalam kategori ini"><?= esc($category['description'] ?? '') ?></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Urutan Tampil (Sort Order)</label>
                    <input type="number" name="sort_order" class="form-control radius-8" value="<?= esc($category['sort_order'] ?? 1) ?>" min="1" max="99">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Tayang</label>
                    <select name="is_active" class="form-select radius-8">
                        <option value="1" <?= ($category['is_active'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif (Ditampilkan)</option>
                        <option value="0" <?= ($category['is_active'] ?? 1) == 0 ? 'selected' : '' ?>>Nonaktif (Disembunyikan)</option>
                    </select>
                </div>

                <div class="col-12 d-flex justify-content-end gap-12 mt-12">
                    <a href="<?= site_url('dashboard/landing/kategori-layanan') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11"><?= $isEdit ? 'Perbarui Kategori' : 'Simpan Kategori' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
