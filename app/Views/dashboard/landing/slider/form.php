<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/components/flash_message') ?>

<?php
$isEdit = ! empty($slider);
$actionUrl = $isEdit ? site_url('dashboard/landing/slider/ubah/' . $slider['id']) : site_url('dashboard/landing/slider/simpan');
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Form Slide Hero') ?></h6>
        <p class="text-secondary-light mb-0">Atur konten slide, headline dengan sorotan warna, gambar latar, serta tombol navigasi.</p>
    </div>
    <a href="<?= site_url('dashboard/landing/slider') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= $actionUrl ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Judul Slide (Headline) <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control radius-8" value="<?= esc($slider['title'] ?? '') ?>" placeholder="Gunakan tag <span>...</span> untuk teks warna kuning" required>
                    <small class="text-muted d-block mt-1">Tip: Bungkus kata kunci dalam <code>&lt;span&gt;...&lt;/span&gt;</code> untuk memberi aksen warna kuning keemasan khas BegawiPBJ.</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Badge / Sub-Title Kecil</label>
                    <input type="text" name="badge_text" class="form-control radius-8" value="<?= esc($slider['badge_text'] ?? '') ?>" placeholder="Pemerintah Provinsi Lampung · Biro PBJ Setda">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi / Paragraf Singkat</label>
                    <textarea name="subtitle" class="form-control radius-8" rows="3" placeholder="Uraian singkat 1-2 kalimat mengenai fitur atau manfaat yang ditampilkan"><?= esc($slider['subtitle'] ?? '') ?></textarea>
                </div>

                <!-- Bagian Upload Gambar Banner Hero -->
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="image_file">
                        <iconify-icon icon="solar:upload-track-2-bold" class="text-primary me-1"></iconify-icon>
                        Unggah File Gambar Banner Hero (Rekomendasi 1920x500 px)
                    </label>
                    <input type="file" name="image_file" id="image_file" class="form-control radius-8" accept="image/png,image/jpeg,image/jpg,image/webp" onchange="previewHeroImage(this, 'slider_preview_img', 'slider_preview_box')">
                    <small class="text-muted d-block mt-1">Format: JPG, PNG, atau WEBP. Maksimal ukuran <strong>5 MB</strong>.</small>

                    <!-- Accordion URL / Path Alternatif -->
                    <div class="mt-12 p-12 radius-8 border bg-neutral-50">
                        <a class="text-xs fw-semibold text-primary-600 d-inline-flex align-items-center gap-1 text-decoration-none" data-bs-toggle="collapse" href="#collapseSliderUrl" role="button" aria-expanded="false" aria-controls="collapseSliderUrl">
                            <iconify-icon icon="solar:link-circle-outline"></iconify-icon>
                            <span>Atau gunakan path gambar statis yang sudah ada</span>
                            <iconify-icon icon="solar:alt-arrow-down-linear" class="text-xs"></iconify-icon>
                        </a>
                        <div class="collapse mt-8" id="collapseSliderUrl">
                            <input type="text" name="image_url" id="image_url" class="form-control radius-8 text-sm" value="<?= esc($slider['image_url'] ?? 'assets/main/images/backgrounds/slider-1-1.png') ?>" placeholder="assets/main/images/backgrounds/slider-1-1.png">
                            <small class="text-muted text-xs">Kosongkan jika mengunggah file baru di atas.</small>
                        </div>
                    </div>
                </div>

                <!-- Pratinjau Banner Hero -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pratinjau Banner Hero</label>
                    <?php
                    $curImg = $slider['image_url'] ?? 'assets/main/images/backgrounds/slider-1-1.png';
                    $curImgSrc = str_starts_with($curImg, 'http') ? $curImg : base_url($curImg);
                    ?>
                    <div id="slider_preview_box" class="p-8 radius-8 border bg-neutral-50 text-center">
                        <img id="slider_preview_img" src="<?= esc($curImgSrc) ?>" alt="Pratinjau Slide" class="w-100 radius-6 shadow-xs" style="max-height: 140px; object-fit: cover;" onerror="this.src='<?= base_url('assets/main/images/backgrounds/slider-1-1.png') ?>'">
                        <span class="badge bg-primary-subtle text-primary text-2xs mt-2 d-inline-block">Banner Terpilih</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Urutan Tampil (Sort Order)</label>
                    <input type="number" name="sort_order" class="form-control radius-8" value="<?= esc($slider['sort_order'] ?? 1) ?>" min="1" max="99">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Tayang</label>
                    <select name="is_active" class="form-select radius-8">
                        <option value="1" <?= ($slider['is_active'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif (Ditampilkan)</option>
                        <option value="0" <?= ($slider['is_active'] ?? 1) == 0 ? 'selected' : '' ?>>Nonaktif (Disembunyikan)</option>
                    </select>
                </div>

                <div class="col-12"><hr class="my-2"></div>
                <div class="col-12">
                    <h6 class="text-sm fw-semibold text-primary-light mb-2">Konfigurasi Tombol Aksi (Call To Action Buttons)</h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Label Tombol Utama (Primary)</label>
                    <input type="text" name="button_text" class="form-control radius-8" value="<?= esc($slider['button_text'] ?? 'Pusat Regulasi PBJ') ?>" placeholder="Pusat Regulasi PBJ">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">URL Tombol Utama</label>
                    <input type="text" name="button_url" class="form-control radius-8" value="<?= esc($slider['button_url'] ?? 'regulasi') ?>" placeholder="regulasi atau https://...">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Label Tombol Kedua (Secondary)</label>
                    <input type="text" name="secondary_button_text" class="form-control radius-8" value="<?= esc($slider['secondary_button_text'] ?? 'Arena Belajar') ?>" placeholder="Arena Belajar">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">URL Tombol Kedua</label>
                    <input type="text" name="secondary_button_url" class="form-control radius-8" value="<?= esc($slider['secondary_button_url'] ?? 'arena-belajar') ?>" placeholder="arena-belajar atau https://...">
                </div>

                <div class="col-12 d-flex justify-content-end gap-12 mt-12">
                    <a href="<?= site_url('dashboard/landing/slider') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11"><?= $isEdit ? 'Perbarui Slide' : 'Simpan Slide' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
function previewHeroImage(input, previewImgId, previewBoxId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById(previewImgId);
            if (img) {
                img.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?= $this->endSection() ?>
