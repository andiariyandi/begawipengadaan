<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$formMode = $formMode ?? 'create';
$isEdit   = $formMode === 'edit';
$category = $category ?? [];
$catId    = $catId ?? null;
$iconUrl  = $iconUrl ?? (! empty($category['icon_file_id']) ? base_url('file/' . (int) $category['icon_file_id']) : null);
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
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Kategori Regulasi') ?></h6>
        <p class="text-secondary-light mb-0"><?= $isEdit ? 'Perbarui klasifikasi dan identitas kategori regulasi.' : 'Tambahkan klasifikasi kategori naskah regulasi baru.' ?></p>
    </div>
    <a href="<?= site_url('dashboard/regulasi/kategori') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-10 d-flex align-items-center gap-2">
        <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
        <span>Kembali ke Daftar</span>
    </a>
</div>

<form action="<?= $isEdit ? site_url('dashboard/regulasi/kategori/' . (int) $catId . '/ubah') : site_url('dashboard/regulasi/kategori/tambah') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row g-24">
        <!-- Kolom Kiri: Informasi Kategori -->
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:folder-with-files-bold" class="text-primary-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Informasi Kategori Regulasi</h6>
                </div>
                <div class="card-body p-24">
                    <div class="row gy-20">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" class="form-control radius-8" name="name" value="<?= esc(old('name', $category['name'] ?? '')) ?>" placeholder="Contoh: Peraturan Presiden" required maxlength="255">
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Slug URL <span class="text-danger">*</span></label>
                            <input type="text" class="form-control radius-8" name="slug" value="<?= esc(old('slug', $category['slug'] ?? '')) ?>" required maxlength="255" pattern="[a-z0-9-]+" placeholder="peraturan-presiden">
                            <span class="text-xs text-secondary-light mt-1 d-block">Gunakan huruf kecil dan tanda hubung (-).</span>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Ikon Class (Iconify)</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-neutral-100 text-primary-600"><iconify-icon icon="<?= esc($category['icon_class'] ?? 'solar:document-outline') ?>" id="iconClassPreview" class="text-lg"></iconify-icon></span>
                                <input type="text" class="form-control radius-8" name="icon_class" id="iconClassInput" value="<?= esc(old('icon_class', $category['icon_class'] ?? 'solar:document-outline')) ?>" placeholder="solar:document-outline" oninput="updateIconClassPreview(this.value)">
                            </div>
                            <div class="d-flex flex-wrap gap-2 align-items-center mt-2">
                                <span class="text-xs text-secondary-light">Pilihan Cepat:</span>
                                <button type="button" class="btn btn-outline-neutral-200 btn-xs radius-4 px-8 py-2 text-xs" onclick="setIconClass('solar:diploma-verified-outline')">Perpres / UU</button>
                                <button type="button" class="btn btn-outline-neutral-200 btn-xs radius-4 px-8 py-2 text-xs" onclick="setIconClass('solar:document-text-outline')">Perka LKPP</button>
                                <button type="button" class="btn btn-outline-neutral-200 btn-xs radius-4 px-8 py-2 text-xs" onclick="setIconClass('solar:buildings-2-outline')">Perda / Pergub</button>
                                <button type="button" class="btn btn-outline-neutral-200 btn-xs radius-4 px-8 py-2 text-xs" onclick="setIconClass('solar:scale-bold')">Instruksi / SE</button>
                                <button type="button" class="btn btn-outline-neutral-200 btn-xs radius-4 px-8 py-2 text-xs" onclick="setIconClass('solar:folder-with-files-outline')">Kompilasi</button>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi Kategori</label>
                            <textarea class="form-control radius-8" name="description" rows="4" placeholder="Deskripsi mengenai kedudukan hukum atau cakupan regulasi dalam kategori ini..."><?= esc(old('description', $category['description'] ?? '')) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Upload Ikon & Status -->
        <div class="col-xl-4 col-lg-5">
            <!-- Upload Ikon / Ilustrasi Kategori -->
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:gallery-bold" class="text-primary-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Ikon / Gambar Kategori</h6>
                </div>
                <div class="card-body p-24">
                    <!-- Ukuran Size Recommendation Alert -->
                    <div class="alert alert-primary bg-primary-50 border-primary-200 radius-12 p-16 mb-16">
                        <div class="d-flex align-items-start gap-2">
                            <iconify-icon icon="solar:info-circle-bold" class="text-primary-600 text-xl flex-shrink-0 mt-1"></iconify-icon>
                            <div class="text-xs">
                                <strong class="d-block text-primary-900 mb-1">Rekomendasi Format Ikon:</strong>
                                <ul class="mb-0 ps-16 text-secondary-light">
                                    <li>Ukuran Resolusi: <strong class="text-primary-700">200 x 200 px</strong> (Rasio 1:1 / Persegi)</li>
                                    <li>Format: <strong>PNG (Transparan), SVG, WEBP</strong></li>
                                    <li>Maksimal Ukuran File: <strong>1 MB</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Live Image Preview Box -->
                    <div class="border radius-12 p-12 text-center bg-neutral-50 mb-16 position-relative" style="min-height: 140px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <img id="catIconPreview" src="<?= esc($iconUrl ?? '') ?>" alt="Preview Ikon Kategori" class="radius-8 object-fit-contain <?= empty($iconUrl) ? 'd-none' : '' ?>" style="max-height: 120px; max-width: 120px;">
                        <div id="catIconPlaceholder" class="<?= ! empty($iconUrl) ? 'd-none' : '' ?> text-secondary-light py-16">
                            <iconify-icon icon="solar:gallery-broken" class="text-4xl mb-1 text-neutral-400"></iconify-icon>
                            <span class="d-block text-xs">Belum ada berkas ikon diunggah</span>
                        </div>
                    </div>

                    <div>
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pilih Berkas Ikon</label>
                        <input type="file" name="icon_image" id="catIconInput" class="form-control radius-8" accept="image/jpeg,image/png,image/webp,image/svg+xml" onchange="previewCatIcon(this)">
                    </div>

                    <?php if ($isEdit && ! empty($category['icon_file_id'])): ?>
                        <div class="form-check mt-12 pt-12 border-top">
                            <input class="form-check-input" type="checkbox" name="remove_icon" value="1" id="remove_icon">
                            <label class="form-check-label text-xs text-danger fw-semibold" for="remove_icon">
                                <iconify-icon icon="solar:trash-bin-trash-outline" class="align-middle me-1"></iconify-icon>
                                Hapus ikon saat ini
                            </label>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pengaturan Status Aktif -->
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:settings-bold" class="text-warning-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Status Publikasi</h6>
                </div>
                <div class="card-body p-24">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= ($category['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold text-sm text-dark cursor-pointer ms-2" for="is_active">
                            Kategori Aktif
                        </label>
                        <span class="text-xs text-secondary-light mt-1 d-block">Kategori yang tidak aktif tidak akan muncul di filter halaman publik.</span>
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="card border-0 radius-16 shadow-sm bg-white p-20">
                <div class="d-flex flex-column gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-12 fw-semibold d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:disk-bold" class="text-lg"></iconify-icon>
                        <span><?= $isEdit ? 'Simpan Perubahan Kategori' : 'Simpan Kategori Baru' ?></span>
                    </button>
                    <a href="<?= site_url('dashboard/regulasi/kategori') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 text-center">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function previewCatIcon(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('catIconPreview');
            const ph = document.getElementById('catIconPlaceholder');
            if (img) {
                img.src = e.target.result;
                img.classList.remove('d-none');
            }
            if (ph) {
                ph.classList.add('d-none');
            }
        };
        reader.readAsDataURL(file);
    }
}

function updateIconClassPreview(className) {
    const preview = document.getElementById('iconClassPreview');
    if (preview && className) {
        preview.setAttribute('icon', className);
    }
}

function setIconClass(className) {
    const input = document.getElementById('iconClassInput');
    if (input) {
        input.value = className;
        updateIconClassPreview(className);
    }
}
</script>
<?= $this->endSection() ?>