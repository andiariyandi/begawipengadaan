<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/components/flash_message') ?>

<?php
$isEdit = ! empty($testimonial);
$actionUrl = $isEdit ? site_url('dashboard/landing/testimoni/ubah/' . $testimonial['id']) : site_url('dashboard/landing/testimoni/simpan');
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Form Testimoni Pengguna') ?></h6>
        <p class="text-secondary-light mb-0">Kelola ulasan, nama tokoh, foto, rating bintang, dan capaian dampak nyata terukur.</p>
    </div>
    <a href="<?= site_url('dashboard/landing/testimoni') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= $actionUrl ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control radius-8" value="<?= esc($testimonial['name'] ?? '') ?>" placeholder="Misal: Dr. Ir. Fahrizal Darminto, M.A." required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jabatan / Posisi</label>
                    <input type="text" name="role_title" class="form-control radius-8" value="<?= esc($testimonial['role_title'] ?? '') ?>" placeholder="Misal: Sekretaris Daerah / Pejabat Pembuat Komitmen (PPK)">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Instansi / Organisasi / Perusahaan</label>
                    <input type="text" name="organization" class="form-control radius-8" value="<?= esc($testimonial['organization'] ?? '') ?>" placeholder="Misal: Pemerintah Provinsi Lampung / PT Lampung Jaya">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kategori Responden</label>
                    <select name="category" class="form-select radius-8">
                        <option value="pimpinan" <?= ($testimonial['category'] ?? '') === 'pimpinan' ? 'selected' : '' ?>>Pimpinan Daerah / UKPBJ</option>
                        <option value="opd" <?= ($testimonial['category'] ?? 'opd') === 'opd' ? 'selected' : '' ?>>Perangkat Daerah (PPK/PA)</option>
                        <option value="pokja" <?= ($testimonial['category'] ?? '') === 'pokja' ? 'selected' : '' ?>>Pokja Pemilihan</option>
                        <option value="penyedia" <?= ($testimonial['category'] ?? '') === 'penyedia' ? 'selected' : '' ?>>Pelaku Usaha (Penyedia)</option>
                        <option value="auditor" <?= ($testimonial['category'] ?? '') === 'auditor' ? 'selected' : '' ?>>Auditor / Pengawas</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Rating Bintang (1 - 5)</label>
                    <select name="rating" class="form-select radius-8">
                        <option value="5" <?= ($testimonial['rating'] ?? 5) == 5 ? 'selected' : '' ?>>5 Bintang (Sangat Puas)</option>
                        <option value="4" <?= ($testimonial['rating'] ?? 5) == 4 ? 'selected' : '' ?>>4 Bintang (Puas)</option>
                        <option value="3" <?= ($testimonial['rating'] ?? 5) == 3 ? 'selected' : '' ?>>3 Bintang (Cukup)</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kutipan Testimoni / Pengalaman <span class="text-danger">*</span></label>
                    <textarea name="quote" class="form-control radius-8" rows="3" placeholder="Tuliskan ulasan atau pengalaman responden" required><?= esc($testimonial['quote'] ?? '') ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pernyataan Dampak Nyata Terukur (Measurable Impact)</label>
                    <input type="text" name="impact_statement" class="form-control radius-8" value="<?= esc($testimonial['impact_statement'] ?? '') ?>" placeholder="Misal: Efisiensi waktu penyusunan dokumen tender hingga 40%">
                </div>

                <!-- Unggah Foto Avatar -->
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="avatar_file">
                        <iconify-icon icon="solar:camera-bold" class="text-primary me-1"></iconify-icon>
                        Unggah File Foto Avatar Responden
                    </label>
                    <input type="file" name="avatar_file" id="avatar_file" class="form-control radius-8" accept="image/png,image/jpeg,image/jpg,image/webp" onchange="previewAvatar(this, 'avatar_preview_img')">
                    <small class="text-muted d-block mt-1">Format: JPG, PNG, atau WEBP. Disarankan rasio 1:1 persegi (maks. 5 MB).</small>

                    <!-- Accordion Path Alternatif -->
                    <div class="mt-12 p-12 radius-8 border bg-neutral-50">
                        <a class="text-xs fw-semibold text-primary-600 d-inline-flex align-items-center gap-1 text-decoration-none" data-bs-toggle="collapse" href="#collapseAvatarUrl" role="button" aria-expanded="false" aria-controls="collapseAvatarUrl">
                            <iconify-icon icon="solar:link-circle-outline"></iconify-icon>
                            <span>Atau gunakan path gambar statis yang sudah ada</span>
                            <iconify-icon icon="solar:alt-arrow-down-linear" class="text-xs"></iconify-icon>
                        </a>
                        <div class="collapse mt-8" id="collapseAvatarUrl">
                            <input type="text" name="avatar_url" id="avatar_url" class="form-control radius-8 text-sm" value="<?= esc($testimonial['avatar_url'] ?? 'assets/main/images/testimonial/testimonial-1-1.jpg') ?>" placeholder="assets/main/images/testimonial/testimonial-1-1.jpg">
                            <small class="text-muted text-xs">Kosongkan jika mengunggah file baru di atas.</small>
                        </div>
                    </div>
                </div>

                <!-- Pratinjau Foto Avatar -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pratinjau Foto Avatar</label>
                    <?php
                    $curAv = $testimonial['avatar_url'] ?? 'assets/main/images/testimonial/testimonial-1-1.jpg';
                    $curAvSrc = str_starts_with($curAv, 'http') ? $curAv : base_url($curAv);
                    ?>
                    <div class="p-12 radius-8 border bg-neutral-50 d-flex align-items-center gap-3">
                        <img id="avatar_preview_img" src="<?= esc($curAvSrc) ?>" alt="Avatar" class="radius-50 shadow-xs border" style="width: 64px; height: 64px; object-fit: cover;" onerror="this.src='<?= base_url('assets/dashboard/images/users/user1.png') ?>'">
                        <div>
                            <span class="badge bg-success-subtle text-success text-2xs mb-1 d-inline-block">Foto Spotlight</span>
                            <p class="text-2xs text-muted mb-0">Tampil melingkar pada carousel testimoni.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Urutan Tampil (Sort Order)</label>
                    <input type="number" name="sort_order" class="form-control radius-8" value="<?= esc($testimonial['sort_order'] ?? 1) ?>" min="1" max="99">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Tayang</label>
                    <select name="is_active" class="form-select radius-8">
                        <option value="1" <?= ($testimonial['is_active'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif (Ditampilkan)</option>
                        <option value="0" <?= ($testimonial['is_active'] ?? 1) == 0 ? 'selected' : '' ?>>Nonaktif (Disembunyikan)</option>
                    </select>
                </div>

                <div class="col-12 d-flex justify-content-end gap-12 mt-12">
                    <a href="<?= site_url('dashboard/landing/testimoni') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11"><?= $isEdit ? 'Perbarui Testimoni' : 'Simpan Testimoni' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
function previewAvatar(input, previewImgId) {
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
