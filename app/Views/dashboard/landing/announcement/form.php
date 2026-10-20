<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<?php
$isEdit = ! empty($announcement);
$actionUrl = $isEdit ? site_url('dashboard/landing/pengumuman/ubah/' . $announcement['id']) : site_url('dashboard/landing/pengumuman/simpan');
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Form Slide Pengumuman') ?></h6>
        <p class="text-secondary-light mb-0">Kelola gambar promosi, label kategori pill, teks pengumuman, dan tombol navigasi tujuan.</p>
    </div>
    <a href="<?= site_url('dashboard/landing/pengumuman') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= $actionUrl ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Judul Pengumuman <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control radius-8" value="<?= esc($announcement['title'] ?? '') ?>" placeholder="Misal: Bimbingan Teknis & Uji Sertifikasi PBJ Level-1 Terpadu" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Label Kategori Pill</label>
                    <input type="text" name="badge_label" class="form-control radius-8" value="<?= esc($announcement['badge_label'] ?? 'AGENDA & BIMTEK 2026') ?>" placeholder="AGENDA & BIMTEK 2026">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Uraian / Deskripsi Lengkap Pengumuman</label>
                    <textarea name="description" class="form-control radius-8" rows="3" placeholder="Uraikan detail pengumuman yang ingin disampaikan"><?= esc($announcement['description'] ?? '') ?></textarea>
                </div>

                <!-- Upload Gambar Header Pengumuman -->
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="image_file">
                        <iconify-icon icon="solar:upload-track-2-bold" class="text-primary me-1"></iconify-icon>
                        Unggah File Gambar Header Pengumuman (Horizontal)
                    </label>
                    <input type="file" name="image_file" id="image_file" class="form-control radius-8" accept="image/png,image/jpeg,image/jpg,image/webp" onchange="previewAnnouncementImage(this, 'announcement_preview_img')">
                    <small class="text-muted d-block mt-1">Format: JPG, PNG, atau WEBP. Rekomendasi 800x350px (maks. 5 MB).</small>

                    <!-- Accordion Path Alternatif -->
                    <div class="mt-12 p-12 radius-8 border bg-neutral-50">
                        <a class="text-xs fw-semibold text-primary-600 d-inline-flex align-items-center gap-1 text-decoration-none" data-bs-toggle="collapse" href="#collapseAnnounceUrl" role="button" aria-expanded="false" aria-controls="collapseAnnounceUrl">
                            <iconify-icon icon="solar:link-circle-outline"></iconify-icon>
                            <span>Atau gunakan path gambar statis yang sudah ada</span>
                            <iconify-icon icon="solar:alt-arrow-down-linear" class="text-xs"></iconify-icon>
                        </a>
                        <div class="collapse mt-8" id="collapseAnnounceUrl">
                            <input type="text" name="image_url" id="image_url" class="form-control radius-8 text-sm" value="<?= esc($announcement['image_url'] ?? 'assets/main/images/event/event-details-top-img-1.jpg') ?>" placeholder="assets/main/images/event/event-details-top-img-1.jpg">
                            <small class="text-muted text-xs">Kosongkan jika mengunggah file baru di atas.</small>
                        </div>
                    </div>
                </div>

                <!-- Pratinjau Gambar Pengumuman -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pratinjau Banner Popup</label>
                    <?php
                    $curAnnImg = $announcement['image_url'] ?? 'assets/main/images/event/event-details-top-img-1.jpg';
                    $curAnnSrc = str_starts_with($curAnnImg, 'http') ? $curAnnImg : base_url($curAnnImg);
                    ?>
                    <div class="p-8 radius-8 border bg-neutral-50 text-center">
                        <img id="announcement_preview_img" src="<?= esc($curAnnSrc) ?>" alt="Pratinjau Pengumuman" class="w-100 radius-6 shadow-xs" style="max-height: 130px; object-fit: cover;" onerror="this.src='<?= base_url('assets/main/images/event/event-details-top-img-1.jpg') ?>'">
                        <span class="badge bg-amber-subtle text-warning-main text-2xs mt-2 d-inline-block">Header Popup</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Urutan Slide</label>
                    <input type="number" name="sort_order" class="form-control radius-8" value="<?= esc($announcement['sort_order'] ?? 1) ?>" min="1" max="99">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Tayang</label>
                    <select name="is_active" class="form-select radius-8">
                        <option value="1" <?= ($announcement['is_active'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif (Ditampilkan)</option>
                        <option value="0" <?= ($announcement['is_active'] ?? 1) == 0 ? 'selected' : '' ?>>Nonaktif (Disembunyikan)</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Label Tombol Aksi (CTA)</label>
                    <input type="text" name="cta_label" class="form-control radius-8" value="<?= esc($announcement['cta_label'] ?? 'Lihat Jadwal Agenda') ?>" placeholder="Lihat Jadwal Agenda">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">URL Tujuan Tombol Aksi</label>
                    <input type="text" name="cta_url" class="form-control radius-8" value="<?= esc($announcement['cta_url'] ?? 'event') ?>" placeholder="event atau https://...">
                </div>

                <div class="col-12 d-flex justify-content-end gap-12 mt-12">
                    <a href="<?= site_url('dashboard/landing/pengumuman') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11"><?= $isEdit ? 'Perbarui Slide' : 'Simpan Slide' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
function previewAnnouncementImage(input, previewImgId) {
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
