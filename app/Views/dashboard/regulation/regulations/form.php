<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$formMode             = $formMode ?? 'create';
$isEdit               = $formMode === 'edit';
$regulation           = $regulation ?? [];
$regId                = $regId ?? null;
$categories           = $categories ?? [];
$stages               = $stages ?? [];
$competencies         = $competencies ?? [];
$selectedStages       = array_map('intval', $selectedStages ?? []);
$selectedCompetencies = array_map('intval', $selectedCompetencies ?? []);
$workflowStatuses     = $workflowStatuses ?? [];
$legalStatuses        = $legalStatuses ?? ['active', 'amended', 'revoked', 'expired'];
$visibilities         = $visibilities ?? ['public', 'internal', 'restricted'];
$coverImgUrl          = $coverImgUrl ?? (! empty($regulation['cover_file_id']) ? base_url('file/' . (int) $regulation['cover_file_id']) : null);
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Regulasi PBJ') ?></h6>
        <p class="text-secondary-light mb-0"><?= $isEdit ? 'Perbarui data naskah hukum, metadata, tahapan, dan berkas regulasi.' : 'Tambahkan dokumen regulasi Pengadaan Barang/Jasa baru ke dalam sistem.' ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <?php if ($isEdit && ! empty($regulation['id'])): ?>
            <a href="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id']) ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-10 d-flex align-items-center gap-2">
                <iconify-icon icon="solar:eye-outline" class="text-lg"></iconify-icon>
                <span>Lihat Detail</span>
            </a>
        <?php endif; ?>
        <a href="<?= site_url('dashboard/regulasi/dokumen') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-10 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            <span>Kembali ke Daftar</span>
        </a>
    </div>
</div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger radius-12 p-16 mb-20">
        <div class="d-flex align-items-start gap-2">
            <iconify-icon icon="solar:danger-circle-bold" class="text-danger text-xl flex-shrink-0 mt-1"></iconify-icon>
            <div>
                <strong class="d-block mb-1">Periksa kembali data formulir:</strong>
                <ul class="mb-0 ps-16 text-sm">
                    <?php foreach ((array) session()->getFlashdata('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>

<form action="<?= $isEdit ? site_url('dashboard/regulasi/dokumen/' . (int) $regId . '/ubah') : site_url('dashboard/regulasi/dokumen') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row g-24">
        <!-- Kolom Kiri: Informasi Utama & Konten Regulasi -->
        <div class="col-xl-8 col-lg-7">
            <!-- Informasi Utama Dokumen -->
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:document-text-bold" class="text-primary-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Informasi Pokok Regulasi</h6>
                </div>
                <div class="card-body p-24">
                    <div class="row gy-20">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Judul Regulasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control radius-8" name="title" value="<?= esc(old('title', $regulation['title'] ?? '')) ?>" placeholder="Contoh: Peraturan Presiden tentang Pengadaan Barang/Jasa Pemerintah" required maxlength="255">
                            <span class="text-xs text-secondary-light mt-1 d-block">Judul resmi atau nama lengkap dokumen perundang-undangan.</span>
                        </div>

                        <div class="col-md-7">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nomor Dokumen <span class="text-danger">*</span></label>
                            <input type="text" class="form-control radius-8" name="document_number" value="<?= esc(old('document_number', $regulation['document_number'] ?? '')) ?>" placeholder="Contoh: Perpres No. 12 Tahun 2021" required maxlength="255">
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tahun Naskah <span class="text-danger">*</span></label>
                            <input type="number" min="1945" max="2100" class="form-control radius-8" name="document_year" value="<?= esc(old('document_year', $regulation['document_year'] ?? date('Y'))) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kategori Dokumen <span class="text-danger">*</span></label>
                            <select class="form-select radius-8" name="category_id" required>
                                <option value="">-- Pilih Kategori Regulasi --</option>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?= (int) $c['id'] ?>" <?= (int) old('category_id', $regulation['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>>
                                        <?= esc($c['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Instansi / Lembaga Penerbit</label>
                            <input type="text" class="form-control radius-8" name="issuer_name" value="<?= esc(old('issuer_name', $regulation['issuer_name'] ?? '')) ?>" placeholder="Contoh: Presiden Republik Indonesia / LKPP">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Penetapan / Pengundangan</label>
                            <input type="date" class="form-control radius-8" name="official_date" value="<?= esc(old('official_date', isset($regulation['official_date']) && $regulation['official_date'] ? substr((string) $regulation['official_date'], 0, 10) : '')) ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Berlaku Efektif</label>
                            <input type="date" class="form-control radius-8" name="effective_date" value="<?= esc(old('effective_date', isset($regulation['effective_date']) && $regulation['effective_date'] ? substr((string) $regulation['effective_date'], 0, 10) : '')) ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">URL Sumber Resmi / JDIH</label>
                            <div class="input-group">
                                <span class="input-group-text bg-neutral-100 text-secondary-light"><iconify-icon icon="solar:link-broken"></iconify-icon></span>
                                <input type="url" class="form-control radius-8" name="official_source_url" value="<?= esc(old('official_source_url', $regulation['official_source_url'] ?? '')) ?>" placeholder="https://jdih.lkpp.go.id/...">
                            </div>
                            <span class="text-xs text-secondary-light mt-1 d-block">Tautan eksternal ke website JDIH pusat atau lembaran daerah.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ringkasan, Abstrak & Kata Kunci -->
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:notes-bold" class="text-primary-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Ringkasan Substansi &amp; Kata Kunci</h6>
                </div>
                <div class="card-body p-24">
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Ringkasan / Abstrak Pokok Regulasi</label>
                        <textarea class="form-control radius-8" name="summary" rows="5" placeholder="Tuliskan ringkasan pokok materi, latar belakang pertimbangan, atau substansi perubahan yang diatur..."><?= esc(old('summary', $regulation['summary'] ?? '')) ?></textarea>
                        <span class="text-xs text-secondary-light mt-1 d-block">Ringkasan ini akan tampil pada landing page pencarian publik dan cuplikan katalog.</span>
                    </div>

                    <div>
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kata Kunci / Tag Pencarian</label>
                        <input type="text" class="form-control radius-8" name="keywords" value="<?= esc(old('keywords', $regulation['keywords'] ?? '')) ?>" placeholder="Contoh: E-Purchasing, Pengadaan Langsung, HPS, Katalog Elektronik, Swakelola">
                        <span class="text-xs text-secondary-light mt-1 d-block">Pisahkan setiap kata kunci dengan tanda koma untuk mengoptimalkan penelusuran.</span>
                    </div>
                </div>
            </div>

            <!-- Pemetaan Tahapan PBJ & Kompetensi -->
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:route-bold" class="text-info-600 text-xl"></iconify-icon>
                        <h6 class="text-base fw-bold mb-0">Pemetaan Tahapan PBJ &amp; Kompetensi Terkait</h6>
                    </div>
                    <span class="badge bg-info-50 text-info-600 radius-6 px-10 py-4 text-xs fw-semibold">Pencarian &amp; Filter Publik</span>
                </div>
                <div class="card-body p-24">
                    <!-- Tahapan PBJ Checkboxes -->
                    <div class="mb-24">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8 d-block">
                            Tahapan Siklus PBJ yang Diatur:
                        </label>
                        <?php if (empty($stages)): ?>
                            <p class="text-secondary-light text-xs mb-0">Belum ada master tahapan PBJ terdaftar.</p>
                        <?php else: ?>
                            <div class="row g-2">
                                <?php foreach ($stages as $stg): ?>
                                    <div class="col-md-6">
                                        <div class="form-check p-12 border radius-8 bg-neutral-50 h-100 d-flex align-items-center gap-2">
                                            <input class="form-check-input ms-0 flex-shrink-0" type="checkbox" name="stages[]" value="<?= (int) $stg['id'] ?>" id="stage_<?= (int) $stg['id'] ?>" <?= in_array((int) $stg['id'], $selectedStages, true) ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-medium text-sm text-dark cursor-pointer flex-grow-1" for="stage_<?= (int) $stg['id'] ?>">
                                                <span class="fw-bold text-primary-700 me-1">[<?= esc($stg['key']) ?>]</span>
                                                <?= esc($stg['name']) ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <span class="text-xs text-secondary-light mt-2 d-block">Centang satu atau lebih tahapan agar dokumen dapat difilter di tab siklus PBJ landing page.</span>
                    </div>

                    <!-- Kompetensi Terkait Checkboxes -->
                    <div>
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8 d-block">
                            Kompetensi SDM Terkait:
                        </label>
                        <?php if (empty($competencies)): ?>
                            <p class="text-secondary-light text-xs mb-0">Belum ada master kompetensi terdaftar.</p>
                        <?php else: ?>
                            <div class="row g-2">
                                <?php foreach ($competencies as $comp): ?>
                                    <div class="col-md-6">
                                        <div class="form-check p-12 border radius-8 bg-neutral-50 h-100 d-flex align-items-center gap-2">
                                            <input class="form-check-input ms-0 flex-shrink-0" type="checkbox" name="competencies[]" value="<?= (int) $comp['id'] ?>" id="comp_<?= (int) $comp['id'] ?>" <?= in_array((int) $comp['id'], $selectedCompetencies, true) ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-medium text-sm text-dark cursor-pointer flex-grow-1" for="comp_<?= (int) $comp['id'] ?>">
                                                <?= esc($comp['name']) ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Upload Berkas Naskah Dokumen -->
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:file-download-bold" class="text-success-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Berkas Dokumen Resmi (PDF / Naskah Asli)</h6>
                </div>
                <div class="card-body p-24">
                    <!-- Format Berkas Alert -->
                    <div class="alert alert-success bg-success-50 border-success-200 radius-12 p-16 mb-16">
                        <div class="d-flex align-items-start gap-2">
                            <iconify-icon icon="solar:info-circle-bold" class="text-success-600 text-xl flex-shrink-0 mt-1"></iconify-icon>
                            <div class="text-xs">
                                <strong class="d-block text-success-900 mb-1">Rekomendasi Dokumen Resmi:</strong>
                                <ul class="mb-0 ps-16 text-secondary-light">
                                    <li>Format Berkas: <strong class="text-success-800">PDF</strong> (Sangat disarankan dengan OCR teks), DOC, DOCX</li>
                                    <li>Ukuran Maksimal Berkas: <strong>20 MB</strong></li>
                                    <li>Pastikan naskah lengkap termasuk Lampiran jika ada.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="mb-16">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            <?= $isEdit ? 'Unggah Berkas Baru (Membuat Versi Baru)' : 'Pilih Berkas Dokumen Versi Awal (v1.0)' ?>
                        </label>
                        <input type="file" class="form-control radius-8" name="file" id="regulationDocFileInput" accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" onchange="validateDocFileSize(this, 20)">
                        <span id="docFileFeedback" class="text-xs text-secondary-light mt-1 d-block">Maksimal 20 MB. Dokumen resmi format PDF / DOC / DOCX.</span>
                    </div>

                    <?php if ($isEdit): ?>
                        <div class="mb-0">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Catatan Perubahan Versi (Changelog)</label>
                            <input type="text" class="form-control radius-8" name="changelog" placeholder="Contoh: Pembaruan naskah salinan resmi beserta lampiran tabel...">
                            <span class="text-xs text-secondary-light mt-1 d-block">Kosongkan jika tidak sedang mengunggah berkas baru.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Cover Image, Status & Visibilitas -->
        <div class="col-xl-4 col-lg-5">
            <!-- Upload Cover Image / Banner Dokumen -->
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:camera-bold" class="text-primary-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Cover Banner Regulasi</h6>
                </div>
                <div class="card-body p-24">
                    <!-- Ukuran Size Recommendation Alert -->
                    <div class="alert alert-primary bg-primary-50 border-primary-200 radius-12 p-16 mb-16">
                        <div class="d-flex align-items-start gap-2">
                            <iconify-icon icon="solar:info-circle-bold" class="text-primary-600 text-xl flex-shrink-0 mt-1"></iconify-icon>
                            <div class="text-xs">
                                <strong class="d-block text-primary-900 mb-1">Rekomendasi Ukuran Banner:</strong>
                                <ul class="mb-0 ps-16 text-secondary-light">
                                    <li>Resolusi Ideal: <strong class="text-primary-700">1200 x 630 px</strong> (Rasio 16:9 / Landscape)</li>
                                    <li>Format Didukung: <strong>JPG, PNG, WEBP</strong></li>
                                    <li>Maksimal Ukuran File: <strong>3 MB</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Live Image Preview Box -->
                    <div class="border radius-12 p-12 text-center bg-neutral-50 mb-16 position-relative" style="min-height: 160px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <img id="regulationCoverPreview" src="<?= esc($coverImgUrl ?? '') ?>" alt="Preview Cover Regulasi" class="w-100 radius-8 object-fit-cover <?= empty($coverImgUrl) ? 'd-none' : '' ?>" style="max-height: 220px;">
                        <div id="regulationCoverPlaceholder" class="<?= ! empty($coverImgUrl) ? 'd-none' : '' ?> text-secondary-light py-20">
                            <iconify-icon icon="solar:gallery-broken" class="text-4xl mb-1 text-neutral-400"></iconify-icon>
                            <span class="d-block text-xs">Belum ada gambar cover banner</span>
                        </div>
                    </div>

                    <div class="mb-12">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pilih Berkas Gambar</label>
                        <input type="file" name="cover_image" id="regulationCoverInput" class="form-control radius-8" accept="image/jpeg,image/png,image/webp,image/jpg" onchange="previewImageFile(this, 'regulationCoverPreview', 'regulationCoverPlaceholder')">
                    </div>

                    <?php if ($isEdit && ! empty($regulation['cover_file_id'])): ?>
                        <div class="form-check mt-12 pt-12 border-top">
                            <input class="form-check-input" type="checkbox" name="remove_cover" value="1" id="remove_cover">
                            <label class="form-check-label text-xs text-danger fw-semibold" for="remove_cover">
                                <iconify-icon icon="solar:trash-bin-trash-outline" class="align-middle me-1"></iconify-icon>
                                Hapus cover saat ini
                            </label>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Status Hukum, Visibilitas & Sorotan -->
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:settings-bold" class="text-warning-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Status &amp; Pengaturan Akses</h6>
                </div>
                <div class="card-body p-24">
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Keberlakuan Hukum <span class="text-danger">*</span></label>
                        <select class="form-select radius-8" name="legal_status" required>
                            <option value="active" <?= ($regulation['legal_status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Berlaku (Active)</option>
                            <option value="amended" <?= ($regulation['legal_status'] ?? '') === 'amended' ? 'selected' : '' ?>>Diubah (Amended)</option>
                            <option value="revoked" <?= ($regulation['legal_status'] ?? '') === 'revoked' ? 'selected' : '' ?>>Dicabut (Revoked)</option>
                            <option value="expired" <?= ($regulation['legal_status'] ?? '') === 'expired' ? 'selected' : '' ?>>Kedaluwarsa (Expired)</option>
                        </select>
                        <span class="text-xs text-secondary-light mt-1 d-block">Status ini menentukan badge legalitas di halaman publik.</span>
                    </div>

                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Akses Visibilitas</label>
                        <select class="form-select radius-8" name="visibility">
                            <option value="public" <?= ($regulation['visibility'] ?? 'public') === 'public' ? 'selected' : '' ?>>Publik (Dapat diakses umum)</option>
                            <option value="internal" <?= ($regulation['visibility'] ?? '') === 'internal' ? 'selected' : '' ?>>Internal (Hanya pengguna login)</option>
                            <option value="restricted" <?= ($regulation['visibility'] ?? '') === 'restricted' ? 'selected' : '' ?>>Terbatas (Hanya admin & pengelola)</option>
                        </select>
                    </div>

                    <div class="p-16 radius-12 border bg-neutral-50 mb-16">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" <?= ! empty($regulation['is_featured']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold text-sm text-primary-light cursor-pointer ms-2" for="is_featured">
                                Regulasi Unggulan / Sorotan Utama
                            </label>
                            <span class="text-xs text-secondary-light mt-1 d-block">Dokumen sorotan akan tampil di highlight beranda dan daftar unggulan.</span>
                        </div>
                    </div>

                    <div class="p-16 radius-12 border bg-neutral-50 mb-16">
                        <div class="form-check form-switch mb-12">
                            <input class="form-check-input" type="checkbox" name="carousel_enabled" value="1" id="carousel_enabled" <?= ! empty($regulation['carousel_enabled']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold text-sm text-primary-light cursor-pointer ms-2" for="carousel_enabled">
                                Tampilkan di Slider Sorotan (Hero Carousel)
                            </label>
                            <span class="text-xs text-secondary-light mt-1 d-block">Regulasi akan diprioritaskan tampil pada slider hero di landing page <code>/regulasi</code>.</span>
                        </div>
                        <div>
                            <label class="form-label fw-semibold text-primary-light text-xs mb-4" for="carousel_order">Urutan Prioritas Slider (Opsional)</label>
                            <input type="number" min="1" max="99" class="form-control form-control-sm radius-8" name="carousel_order" id="carousel_order" value="<?= esc(old('carousel_order', $regulation['carousel_order'] ?? '')) ?>" placeholder="Contoh: 1, 2, 3...">
                            <span class="text-2xs text-secondary-light mt-1 d-block">Urutan terkecil akan tampil paling awal dalam rotasi slider.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi Simpan & Batal -->
            <div class="card border-0 radius-16 shadow-sm bg-base p-20">
                <div class="d-flex flex-column gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-12 fw-semibold d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:disk-bold" class="text-lg"></iconify-icon>
                        <span><?= $isEdit ? 'Simpan Perubahan Regulasi' : 'Simpan & Daftarkan Regulasi' ?></span>
                    </button>
                    <a href="<?= site_url('dashboard/regulasi/dokumen') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 text-center">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function validateDocFileSize(input, maxMb) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const maxBytes = maxMb * 1024 * 1024;
        const feedback = document.getElementById('docFileFeedback');
        if (file.size > maxBytes) {
            const actualMb = (file.size / (1024 * 1024)).toFixed(2);
            alert(`Peringatan: Ukuran berkas "${file.name}" (${actualMb} MB) melebihi batas maksimal yang diperbolehkan (${maxMb} MB).\n\nSilakan kompres berkas PDF Anda atau gunakan naskah berukuran di bawah ${maxMb} MB.`);
            input.value = '';
            if (feedback) {
                feedback.className = 'text-xs text-danger fw-bold mt-1 d-block';
                feedback.innerHTML = `<iconify-icon icon="solar:danger-circle-bold" class="align-middle me-1"></iconify-icon> Berkas "${file.name}" (${actualMb} MB) dibatalkan karena melebihi batas ${maxMb} MB.`;
            }
            return false;
        } else {
            if (feedback) {
                feedback.className = 'text-xs text-success fw-semibold mt-1 d-block';
                feedback.innerHTML = `<iconify-icon icon="solar:check-circle-bold" class="align-middle me-1"></iconify-icon> Berkas siap diunggah: ${file.name} (${(file.size / (1024 * 1024)).toFixed(2)} MB)`;
            }
        }
    }
    return true;
}

function previewImageFile(input, imgElementId, placeholderId) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const maxBytes = 5 * 1024 * 1024; // 5 MB
        if (file.size > maxBytes) {
            const actualMb = (file.size / (1024 * 1024)).toFixed(2);
            alert(`Peringatan: Ukuran gambar cover (${actualMb} MB) melebihi batas 5 MB. Silakan pilih gambar yang lebih kecil.`);
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById(imgElementId);
            const ph = document.getElementById(placeholderId);
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

document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function (e) {
            const docInput = document.getElementById('regulationDocFileInput');
            if (docInput && docInput.files && docInput.files[0]) {
                if (!validateDocFileSize(docInput, 20)) {
                    e.preventDefault();
                    return false;
                }
            }
            const coverInput = document.getElementById('regulationCoverInput');
            if (coverInput && coverInput.files && coverInput.files[0]) {
                if (coverInput.files[0].size > 5 * 1024 * 1024) {
                    alert('Ukuran gambar cover melebihi batas maksimal 5 MB.');
                    e.preventDefault();
                    return false;
                }
            }
        });
    }
});
</script>
<?= $this->endSection() ?>