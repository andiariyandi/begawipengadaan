<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<?php
$isEdit = ! empty($portal);
$actionUrl = $isEdit ? site_url('dashboard/landing/portal-eksternal/ubah/' . $portal['id']) : site_url('dashboard/landing/portal-eksternal/simpan');
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Form Portal Ekosistem PBJ') ?></h6>
        <p class="text-secondary-light mb-0">Kelola nama sistem pengadaan, tautan langsung ke portal resmi, kategori, dan ikon representasi.</p>
    </div>
    <a href="<?= site_url('dashboard/landing/portal-eksternal') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= $actionUrl ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Portal Resmi <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control radius-8" value="<?= esc($portal['name'] ?? '') ?>" placeholder="Misal: LPSE Provinsi Lampung" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Akronim / Singkatan</label>
                    <input type="text" name="acronym" class="form-control radius-8" value="<?= esc($portal['acronym'] ?? '') ?>" placeholder="Misal: SPSE, SiRUP">
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">URL Tautan Resmi (dengan https://) <span class="text-danger">*</span></label>
                    <input type="url" name="url" class="form-control radius-8" value="<?= esc($portal['url'] ?? '') ?>" placeholder="https://lpse.lampungprov.go.id" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kategori Ekosistem</label>
                    <select name="category" class="form-select radius-8">
                        <option value="nasional" <?= ($portal['category'] ?? 'nasional') === 'nasional' ? 'selected' : '' ?>>Nasional (LKPP RI)</option>
                        <option value="daerah" <?= ($portal['category'] ?? '') === 'daerah' ? 'selected' : '' ?>>Daerah (Pemprov Lampung)</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi Ringkas Layanan</label>
                    <textarea name="description" class="form-control radius-8" rows="2" placeholder="Uraikan fungsi sistem ini secara singkat"><?= esc($portal['description'] ?? '') ?></textarea>
                </div>

                <!-- Unggah File Logo / Lambang Portal -->
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="logo_file">
                        <iconify-icon icon="solar:camera-bold" class="text-primary me-1"></iconify-icon>
                        Unggah File Logo / Lambang Portal
                    </label>
                    <input type="file" name="logo_file" id="logo_file" class="form-control radius-8" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml" onchange="previewPortalLogo(this, 'portal_logo_preview_img')">
                    <small class="text-muted d-block mt-1">Format: PNG, SVG, JPG, atau WEBP. Disarankan logo transparan atau persegi (maks. 5 MB).</small>

                    <!-- Accordion Path Alternatif / Icon Name -->
                    <div class="mt-12 p-12 radius-8 border bg-neutral-50">
                        <a class="text-xs fw-semibold text-primary-600 d-inline-flex align-items-center gap-1 text-decoration-none" data-bs-toggle="collapse" href="#collapsePortalLogoUrl" role="button" aria-expanded="false" aria-controls="collapsePortalLogoUrl">
                            <iconify-icon icon="solar:link-circle-outline"></iconify-icon>
                            <span>Atau gunakan nama icon Iconify / path statis yang sudah ada</span>
                            <iconify-icon icon="solar:alt-arrow-down-linear" class="text-xs"></iconify-icon>
                        </a>
                        <div class="collapse mt-8" id="collapsePortalLogoUrl">
                            <input type="text" name="logo_url" id="logo_url" class="form-control radius-8 text-sm" value="<?= esc($portal['logo_url'] ?? 'solar:server-square-bold') ?>" placeholder="solar:server-square-bold atau uploads/...">
                            <small class="text-muted text-xs">Kosongkan jika mengunggah file gambar baru di atas.</small>
                        </div>
                    </div>
                </div>

                <!-- Pratinjau Logo Portal -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pratinjau Logo / Lambang</label>
                    <?php
                    $curLogo = $portal['logo_url'] ?? 'solar:server-square-bold';
                    $isImg = str_contains($curLogo, '/') || str_contains($curLogo, '.');
                    $curLogoSrc = $isImg ? (str_starts_with($curLogo, 'http') ? $curLogo : base_url($curLogo)) : '';
                    ?>
                    <div class="p-12 radius-8 border bg-neutral-50 d-flex align-items-center gap-3">
                        <div id="portal_preview_wrap" class="d-flex align-items-center justify-content-center radius-8 bg-white border" style="width: 56px; height: 56px;">
                            <img id="portal_logo_preview_img" src="<?= esc($curLogoSrc) ?>" alt="Logo Portal" class="<?= $isImg ? '' : 'd-none' ?>" style="max-width: 44px; max-height: 44px; object-fit: contain;">
                            <iconify-icon id="portal_logo_preview_icon" icon="<?= esc($curLogo) ?>" class="text-2xl text-primary <?= $isImg ? 'd-none' : '' ?>"></iconify-icon>
                        </div>
                        <div>
                            <span class="badge bg-success-subtle text-success text-2xs mb-1 d-inline-block">Ekosistem PBJ</span>
                            <p class="text-2xs text-muted mb-0">Ikon di tautan cepat portal eksternal.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Urutan Tampil</label>
                    <input type="number" name="sort_order" class="form-control radius-8" value="<?= esc($portal['sort_order'] ?? 1) ?>" min="1" max="99">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Tayang</label>
                    <select name="is_active" class="form-select radius-8">
                        <option value="1" <?= ($portal['is_active'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif (Ditampilkan)</option>
                        <option value="0" <?= ($portal['is_active'] ?? 1) == 0 ? 'selected' : '' ?>>Nonaktif (Disembunyikan)</option>
                    </select>
                </div>

                <div class="col-12 d-flex justify-content-end gap-12 mt-12">
                    <a href="<?= site_url('dashboard/landing/portal-eksternal') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11"><?= $isEdit ? 'Perbarui Portal' : 'Simpan Portal' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
function previewPortalLogo(input, previewImgId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById(previewImgId);
            const icon = document.getElementById('portal_logo_preview_icon');
            if (img) {
                img.src = e.target.result;
                img.classList.remove('d-none');
            }
            if (icon) {
                icon.classList.add('d-none');
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?= $this->endSection() ?>
