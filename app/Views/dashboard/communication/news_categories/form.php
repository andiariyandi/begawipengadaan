<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$item = $item ?? [];
?>

<!-- Header & Breadcrumbs -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="<?= site_url('dashboard/komunikasi/kategori-berita') ?>" class="text-secondary-light text-xs hover-primary-600">
                <iconify-icon icon="solar:folders-outline" class="align-middle me-1"></iconify-icon> Kategori Berita
            </a>
            <span class="text-secondary-light text-xs">/ <?= $isEdit ? 'Ubah Kategori' : 'Tambah Kategori' ?></span>
        </div>
        <h5 class="fw-bold mb-1 text-primary-light"><?= $isEdit ? 'Ubah Kategori Berita' : 'Tambah Kategori Berita Baru' ?></h5>
        <p class="text-secondary-light text-sm mb-0">Lengkapi informasi taksonomi untuk mempermudah pengelompokan berita pada portal BegawiPBJ.</p>
    </div>
    <a href="<?= site_url('dashboard/komunikasi/kategori-berita') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-10 d-inline-flex align-items-center gap-2 text-sm fw-medium">
        <iconify-icon icon="solar:arrow-left-linear" class="text-lg"></iconify-icon>
        <span>Kembali ke Daftar</span>
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border radius-12 shadow-sm overflow-hidden">
            <div class="card-header bg-transparent border-bottom py-16 px-24 d-flex align-items-center gap-2">
                <iconify-icon icon="<?= $isEdit ? 'solar:pen-2-bold' : 'solar:folder-with-files-bold' ?>" class="text-xl text-primary-600"></iconify-icon>
                <h6 class="fw-bold mb-0 text-base"><?= $isEdit ? 'Form Sunting Kategori' : 'Form Pembuatan Kategori' ?></h6>
            </div>
            <div class="card-body p-24">
                <form action="<?= $isEdit ? site_url('dashboard/komunikasi/kategori-berita/' . (int) ($catId ?? 0) . '/ubah') : site_url('dashboard/komunikasi/kategori-berita/tambah') ?>" method="post" id="categoryForm">
                    <?= csrf_field() ?>
                    
                    <div class="row gy-20">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Nama Kategori <span class="text-danger-600">*</span>
                            </label>
                            <input type="text" name="name" id="categoryName" class="form-control radius-8 py-10" 
                                   value="<?= esc(old('name', $item['name'] ?? '')) ?>" 
                                   placeholder="Contoh: Pengadaan Barang & Jasa, Regulasi, Tips & Trik" 
                                   required maxlength="255">
                            <span class="text-xs text-secondary-light mt-4 d-block">Nama kategori akan tampil sebagai label taksonomi pada portal publik berita.</span>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                URL Slug <span class="text-danger-600">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-neutral-100 text-secondary-light text-xs font-monospace border-end-0 radius-8-start">
                                    /berita?category=
                                </span>
                                <input type="text" name="slug" id="categorySlug" class="form-control font-monospace text-sm radius-8-end py-10" 
                                       value="<?= esc(old('slug', $item['slug'] ?? '')) ?>" 
                                       placeholder="pengadaan-barang-jasa" 
                                       required maxlength="255" pattern="[a-z0-9-]+">
                            </div>
                            <span class="text-xs text-secondary-light mt-4 d-block">Gunakan huruf kecil, angka, dan tanda hubung (-). Dibuat otomatis dari nama kategori jika kosong.</span>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi Singkat</label>
                            <textarea name="description" class="form-control radius-8 py-10" rows="3" 
                                      placeholder="Penjelasan singkat tentang lingkup topik artikel dalam kategori ini..."><?= esc(old('description', $item['description'] ?? '')) ?></textarea>
                            <span class="text-xs text-secondary-light mt-4 d-block">Opsional. Memberikan konteks ringkas bagi administrator dan pembaca.</span>
                        </div>

                        <div class="col-12">
                            <div class="p-16 radius-8 bg-neutral-50 border border-neutral-200">
                                <div class="form-check form-switch d-flex align-items-center gap-3 ps-0 mb-0">
                                    <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" name="is_active" id="is_active" value="1" 
                                           <?= (old('is_active', $item['is_active'] ?? 1) == 1) ? 'checked' : '' ?> style="width: 2.25rem; height: 1.25rem; cursor: pointer;">
                                    <div>
                                        <label class="form-check-label fw-semibold text-primary-light text-sm d-block mb-1 cursor-pointer" for="is_active">
                                            Status Kategori Aktif
                                        </label>
                                        <span class="text-xs text-secondary-light d-block">
                                            Jika dinonaktifkan, kategori tidak akan muncul sebagai pilihan filter pada portal landing page berita.
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 pt-12 border-top d-flex align-items-center justify-content-between gap-3">
                            <a href="<?= site_url('dashboard/komunikasi/kategori-berita') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-10 text-sm fw-medium">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-10 text-sm fw-medium d-inline-flex align-items-center gap-2 shadow-sm">
                                <iconify-icon icon="solar:check-circle-bold" class="text-base"></iconify-icon>
                                <span><?= $isEdit ? 'Simpan Perubahan' : 'Buat Kategori' ?></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('categoryName');
    const slugInput = document.getElementById('categorySlug');
    const isEdit = <?= $isEdit ? 'true' : 'false' ?>;

    if (nameInput && slugInput && !isEdit) {
        nameInput.addEventListener('input', function() {
            if (!slugInput.dataset.manual) {
                slugInput.value = nameInput.value
                    .toLowerCase()
                    .replace(/[^a-z0-9 -]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
        });

        slugInput.addEventListener('input', function() {
            slugInput.dataset.manual = 'true';
        });
    }
});
</script>
<?= $this->endSection() ?>
