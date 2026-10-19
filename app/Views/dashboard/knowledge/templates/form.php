<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$row = $template ?? [];
$categories = $categories ?? [];
$selectedCats = $selectedCategories ?? [];
$statusOptions = [
    'draft' => 'Draft', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan',
    'submitted' => 'Diajukan', 'in_review' => 'Ditinjau', 'approved' => 'Disetujui',
    'revision_required' => 'Perlu Revisi', 'rejected' => 'Ditolak',
];
$docCategoryOptions = [
    'kak'       => 'Kerangka Acuan Kerja (KAK / TOR)',
    'hps'       => 'Harga Perkiraan Sendiri (HPS / EE)',
    'kontrak'   => 'Rancangan & Standar Dokumen Kontrak (SSUK / SSKK)',
    'bast'      => 'Berita Acara Serah Terima (BAST / PHO)',
    'spk'       => 'Surat Perintah Kerja (SPK)',
    'checklist' => 'Checklist & Formulir Evaluasi Kualifikasi',
    'surat'     => 'Format Surat & Berita Acara Rapat',
];
$action = $isEdit ? route_to('dashboard.knowledge.document-templates.edit', (string) ($templateId ?? '')) : route_to('dashboard.knowledge.document-templates.create');
$currentStatus = $row['workflow_status'] ?? 'published';
$currentDocCat = $row['document_category'] ?? 'kak';
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Template Dokumen PBJ') ?></h6>
        <p class="text-secondary-light mb-0">Kelola master template dokumen standar kerja pengadaan barang/jasa yang siap diunduh pengguna.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= route_to('dashboard.knowledge.document-templates') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
            <span>Kembali ke Daftar</span>
        </a>
    </div>
</div>

<form method="post" action="<?= $action ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row gy-24">
        <!-- Kolom Kiri: Metadata & Deskripsi Template -->
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:file-download-bold" class="text-success-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Identitas &amp; Deskripsi Template</h6>
                </div>
                <div class="card-body p-24">
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Template Dokumen <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control radius-8" value="<?= esc(old('title', $row['title'] ?? '')) ?>" required maxlength="255" placeholder="Contoh: Template Kerangka Acuan Kerja (KAK) Pengadaan Jasa Konsultansi">
                    </div>

                    <div class="row g-3 mb-20">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kelompok Dokumen PBJ</label>
                            <select name="document_category" class="form-select radius-8">
                                <?php foreach ($docCategoryOptions as $dKey => $dLabel): ?>
                                    <option value="<?= $dKey ?>" <?= $currentDocCat === $dKey ? 'selected' : '' ?>><?= esc($dLabel) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Versi Dokumen</label>
                            <input type="text" name="document_version" class="form-control radius-8" value="<?= esc(old('document_version', $row['document_version'] ?? 'v1.0')) ?>" placeholder="v1.0 atau 2026.1">
                        </div>
                    </div>

                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi &amp; Petunjuk Penggunaan Singkat</label>
                        <textarea name="summary" class="form-control radius-8" rows="3" placeholder="Tuliskan petunjuk singkat atau instruksi pengisian dokumen template ini..."><?= esc(old('summary', $row['summary'] ?? '')) ?></textarea>
                    </div>

                    <div>
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Catatan Klausul / Panduan Lampiran</label>
                        <textarea name="content" class="form-control radius-8" rows="6" placeholder="Catatan klausul wajib, regulasi acuan, atau checklist kelengkapan dokumen..."><?= esc(old('content', $row['content'] ?? '')) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Kategori -->
            <div class="card border-0 radius-16 shadow-sm bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:layers-bold" class="text-warning-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Pilar Kategori Terkait</h6>
                </div>
                <div class="card-body p-24">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kategori</label>
                    <select name="categories[]" class="form-select radius-8" multiple size="4">
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= in_array((int) $c['id'], array_map('intval', $selectedCats), true) ? 'selected' : '' ?>>
                                <?= esc($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="text-xs text-secondary-light mt-1 d-block">Tahan Ctrl/Cmd untuk memilih lebih dari satu kategori.</span>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Upload Berkas Dokumen & Status -->
        <div class="col-xl-4 col-lg-5">
            <!-- Berkas File Template -->
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:folder-with-files-bold" class="text-success-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Berkas File Template</h6>
                </div>
                <div class="card-body p-24">
                    <!-- Rekomendasi Format File Alert -->
                    <div class="alert alert-success bg-success-50 border-success-200 radius-12 p-16 mb-16">
                        <div class="d-flex align-items-start gap-2">
                            <iconify-icon icon="solar:info-circle-bold" class="text-success-600 text-xl flex-shrink-0 mt-1"></iconify-icon>
                            <div class="text-xs">
                                <strong class="d-block text-success-900 mb-1">Ketentuan Berkas Template:</strong>
                                <ul class="mb-0 ps-16 text-secondary-light">
                                    <li>Format Didukung: <strong class="text-success-800">DOCX, XLSX, PDF, PPTX</strong></li>
                                    <li>Maksimal Ukuran File: <strong>25 MB</strong></li>
                                    <li>Gunakan format standar formulir kerja Biro PBJ</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <?php if (! empty($row['cover_file_id'])): ?>
                        <div class="p-16 radius-12 bg-neutral-50 border border-neutral-200 mb-16 d-flex align-items-center gap-3">
                            <div class="w-44-px h-44-px bg-success-100 text-success-700 radius-10 d-flex align-items-center justify-content-center fw-bold fs-5">
                                <iconify-icon icon="solar:file-check-bold"></iconify-icon>
                            </div>
                            <div class="overflow-hidden">
                                <span class="d-block text-xs text-success-700 fw-bold">Berkas Siap Unduh</span>
                                <span class="text-xs text-secondary-light d-block text-truncate">Asset ID #<?= (int) $row['cover_file_id'] ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            <?= ! empty($row['cover_file_id']) ? 'Ganti Berkas Template' : 'Pilih Berkas Template' ?>
                        </label>
                        <input type="file" name="template_file" class="form-control radius-8" accept=".docx,.doc,.xlsx,.xls,.pdf,.pptx,.ppt,.zip">
                        <span class="text-xs text-secondary-light mt-1 d-block">
                            <?= ! empty($row['cover_file_id']) ? 'Biarkan kosong jika tidak ingin mengubah berkas saat ini.' : 'Wajib diunggah agar pengguna dapat mengunduh formulir.' ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Cover Gambar Template -->
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:camera-bold" class="text-primary-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Cover / Poster Template</h6>
                </div>
                <div class="card-body p-24">
                    <div class="alert alert-info bg-info-50 border-info-200 radius-12 p-16 mb-16">
                        <div class="d-flex align-items-start gap-2">
                            <iconify-icon icon="solar:info-circle-bold" class="text-info-600 text-xl flex-shrink-0 mt-1"></iconify-icon>
                            <div class="text-xs">
                                <strong class="d-block text-info-900 mb-1">Rekomendasi Cover Template:</strong>
                                <ul class="mb-0 ps-16 text-secondary-light">
                                    <li>Ukuran: <strong class="text-info-800">1200 x 630 px</strong> (Landscape 16:9)</li>
                                    <li>Format: <strong>JPG, PNG, WEBP</strong> (Maks 3MB)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="border radius-12 p-12 text-center bg-neutral-50 mb-16 position-relative" style="min-height: 140px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <?php
                            $coverUrl = ! empty($row['cover_url']) ? (str_starts_with($row['cover_url'], 'http') ? $row['cover_url'] : base_url($row['cover_url'])) : null;
                        ?>
                        <img id="templateCoverPreview" src="<?= esc($coverUrl ?? '') ?>" alt="Preview Cover" class="w-100 radius-8 object-fit-cover <?= empty($coverUrl) ? 'd-none' : '' ?>" style="max-height: 180px;">
                        <div id="templateCoverPlaceholder" class="<?= ! empty($coverUrl) ? 'd-none' : '' ?> text-secondary-light py-20">
                            <iconify-icon icon="solar:gallery-broken" class="text-4xl mb-1 text-neutral-400"></iconify-icon>
                            <span class="d-block text-xs">Belum ada cover dipilih</span>
                        </div>
                    </div>

                    <div>
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pilih Berkas Cover (Gambar)</label>
                        <input type="file" name="cover" class="form-control radius-8" accept="image/jpeg,image/png,image/webp" onchange="previewImageFile(this, 'templateCoverPreview', 'templateCoverPlaceholder')">
                    </div>
                </div>
            </div>

            <!-- Status Alur Kerja -->
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:settings-bold" class="text-primary-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Status Publikasi &amp; Sorotan</h6>
                </div>
                <div class="card-body p-24">
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select radius-8" required>
                            <?php foreach ($statusOptions as $k => $label): ?>
                                <option value="<?= $k ?>" <?= $currentStatus === $k ? 'selected' : '' ?>><?= esc($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="p-16 border radius-12 bg-neutral-50">
                        <div class="form-check form-switch mb-12">
                            <input class="form-check-input" type="checkbox" name="featured" value="1" id="featured" <?= ! empty($row['featured']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold text-sm text-dark" for="featured">
                                <i class="fas fa-star text-warning me-1"></i> Template Pilihan / Unggulan
                            </label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="carousel_enabled" value="1" id="carousel_enabled" <?= ! empty($row['carousel_enabled']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold text-sm text-dark" for="carousel_enabled">
                                Tampilkan di Slider Sorotan Beranda
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="card border-0 radius-16 shadow-sm bg-white p-20">
                <div class="d-flex flex-column gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-12 fw-semibold d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:disk-bold" class="text-lg"></iconify-icon>
                        <span><?= $isEdit ? 'Simpan Perubahan Template' : 'Unggah Template Dokumen' ?></span>
                    </button>
                    <a href="<?= route_to('dashboard.knowledge.document-templates') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 text-center">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function previewImageFile(input, imgId, placeholderId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById(imgId);
            const placeholder = document.getElementById(placeholderId);
            if (img) {
                img.src = e.target.result;
                img.classList.remove('d-none');
            }
            if (placeholder) {
                placeholder.classList.add('d-none');
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?= $this->endSection() ?>