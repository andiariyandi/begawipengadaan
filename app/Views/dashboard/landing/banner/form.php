<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/components/flash_message') ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Ubah Banner Halaman') ?></h6>
        <p class="text-secondary-light mb-0">Sesuaikan tajuk pembuka halaman hero dan banner ajakan bertindak (CTA) bagian penutup.</p>
    </div>
    <a href="<?= site_url('dashboard/landing/banner-halaman') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= site_url('dashboard/landing/banner-halaman/ubah/' . $banner['page_key']) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- Bagian 1: Hero Header Atas -->
            <h6 class="text-base fw-bold text-primary mb-16 d-flex align-items-center gap-2">
                <iconify-icon icon="solar:flag-2-bold" class="text-xl"></iconify-icon>
                1. Hero Header Halaman Pembuka
            </h6>
            <div class="row gy-20 mb-32">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Badge / Label Tag Pill</label>
                    <input type="text" name="hero_badge" class="form-control radius-8" value="<?= esc($banner['hero_badge'] ?? '') ?>" placeholder="Misal: Biro PBJ Setda Prov. Lampung">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Judul Utama Hero (H1 Headline)</label>
                    <input type="text" name="hero_title" class="form-control radius-8" value="<?= esc($banner['hero_title'] ?? '') ?>" placeholder="Gunakan tag <span>...</span> untuk warna kuning keemasan">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Subjudul / Uraian Hero</label>
                    <textarea name="hero_subtitle" class="form-control radius-8" rows="3"><?= esc($banner['hero_subtitle'] ?? '') ?></textarea>
                </div>

                <!-- Unggah Gambar Latar Hero Banner -->
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="hero_bg_file">
                        <iconify-icon icon="solar:gallery-bold" class="text-primary me-1"></iconify-icon>
                        Unggah File Gambar Latar Hero (Background Shape / Wallpaper)
                    </label>
                    <input type="file" name="hero_bg_file" id="hero_bg_file" class="form-control radius-8" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml" onchange="previewBannerBg(this, 'banner_bg_preview_img')">
                    <small class="text-muted d-block mt-1">Format: JPG, PNG, WEBP, atau SVG. Rasio lanskap lebar direkomendasikan (maks. 5 MB).</small>

                    <!-- Accordion Path Alternatif -->
                    <div class="mt-12 p-12 radius-8 border bg-neutral-50">
                        <a class="text-xs fw-semibold text-primary-600 d-inline-flex align-items-center gap-1 text-decoration-none" data-bs-toggle="collapse" href="#collapseHeroBgUrl" role="button" aria-expanded="false" aria-controls="collapseHeroBgUrl">
                            <iconify-icon icon="solar:link-circle-outline"></iconify-icon>
                            <span>Atau gunakan path gambar statis yang sudah ada</span>
                            <iconify-icon icon="solar:alt-arrow-down-linear" class="text-xs"></iconify-icon>
                        </a>
                        <div class="collapse mt-8" id="collapseHeroBgUrl">
                            <input type="text" name="hero_bg_image" id="hero_bg_image" class="form-control radius-8 text-sm" value="<?= esc($banner['hero_bg_image'] ?? '') ?>" placeholder="assets/main/images/shapes/banner-shape.png">
                            <small class="text-muted text-xs">Kosongkan jika mengunggah file baru di atas.</small>
                        </div>
                    </div>
                </div>

                <!-- Pratinjau Latar Hero -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pratinjau Gambar Latar</label>
                    <?php
                    $curBg = $banner['hero_bg_image'] ?? '';
                    $curBgSrc = ! empty($curBg) ? (str_starts_with($curBg, 'http') ? $curBg : base_url($curBg)) : base_url('assets/main/images/shapes/banner-shape.png');
                    ?>
                    <div class="p-12 radius-8 border bg-neutral-50 d-flex align-items-center gap-3">
                        <img id="banner_bg_preview_img" src="<?= esc($curBgSrc) ?>" alt="Banner Hero Background" class="radius-8 shadow-xs border" style="width: 100px; height: 60px; object-fit: cover;" onerror="this.src='<?= base_url('assets/main/images/shapes/banner-shape.png') ?>'">
                        <div>
                            <span class="badge bg-info-subtle text-info text-2xs mb-1 d-inline-block">Latar Hero</span>
                            <p class="text-2xs text-muted mb-0">Tampil di header pembuka halaman ini.</p>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Placeholder Kolom Pencarian (Bila Ada)</label>
                    <input type="text" name="search_placeholder" class="form-control radius-8" value="<?= esc($banner['search_placeholder'] ?? '') ?>" placeholder="Cari nama regulasi, materi kursus, atau kata kunci...">
                </div>
            </div>

            <hr class="my-24">

            <!-- Bagian 2: Closing CTA Banner Bawah -->
            <h6 class="text-base fw-bold text-success-main mb-16 d-flex align-items-center gap-2">
                <iconify-icon icon="solar:chat-round-call-bold" class="text-xl"></iconify-icon>
                2. Closing Call to Action (CTA) Banner Penutup Halaman
            </h6>
            <div class="row gy-20">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Badge / Tagline CTA</label>
                    <input type="text" name="cta_badge" class="form-control radius-8" value="<?= esc($banner['cta_badge'] ?? '') ?>" placeholder="Misal: Layanan Konsultasi & Pendampingan">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Judul Banner CTA</label>
                    <input type="text" name="cta_title" class="form-control radius-8" value="<?= esc($banner['cta_title'] ?? '') ?>" placeholder="Misal: Butuh Konsultasi atau Pendampingan PBJ?">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi Uraian CTA</label>
                    <textarea name="cta_lead" class="form-control radius-8" rows="2"><?= esc($banner['cta_lead'] ?? '') ?></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Label Tombol Pertama (Primary)</label>
                    <input type="text" name="cta_primary_btn_text" class="form-control radius-8" value="<?= esc($banner['cta_primary_btn_text'] ?? '') ?>" placeholder="Misal: Hubungi Helpdesk PBJ">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">URL Tombol Pertama</label>
                    <input type="text" name="cta_primary_btn_url" class="form-control radius-8" value="<?= esc($banner['cta_primary_btn_url'] ?? '') ?>" placeholder="kontak">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Label Tombol Kedua (Secondary)</label>
                    <input type="text" name="cta_secondary_btn_text" class="form-control radius-8" value="<?= esc($banner['cta_secondary_btn_text'] ?? '') ?>" placeholder="Misal: Cek Keabsahan Sertifikat">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">URL Tombol Kedua</label>
                    <input type="text" name="cta_secondary_btn_url" class="form-control radius-8" value="<?= esc($banner['cta_secondary_btn_url'] ?? '') ?>" placeholder="verifikasi-sertifikat">
                </div>

                <div class="col-12 d-flex justify-content-end gap-12 mt-20">
                    <a href="<?= site_url('dashboard/landing/banner-halaman') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Perbarui Banner Halaman</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
function previewBannerBg(input, previewImgId) {
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
