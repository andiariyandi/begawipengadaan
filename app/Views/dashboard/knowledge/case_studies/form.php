<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$row = $caseStudy ?? [];
$categories = $categories ?? [];
$selectedCats = $selectedCategories ?? [];
$statusOptions = [
    'draft' => 'Draft', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan',
    'submitted' => 'Diajukan', 'in_review' => 'Ditinjau', 'approved' => 'Disetujui',
    'revision_required' => 'Perlu Revisi', 'rejected' => 'Ditolak',
];
$severityOptions = [
    'low'      => 'Rendah (Penyimpangan Administratif Ringan)',
    'medium'   => 'Sedang (Keterlambatan / Kendala Teknis)',
    'high'     => 'Tinggi (Sengketa Kontrak / Potensi Kerugian)',
    'critical' => 'Kritis (Pemutusan Kontrak / Masalah Hukum)',
];
$stageOptions = [
    'perencanaan'   => 'Perencanaan Pengadaan (Identifikasi & RUP)',
    'persiapan'     => 'Persiapan Pengadaan (HPS, KAK, Spesifikasi)',
    'pemilihan'     => 'Pemilihan Penyedia (Tender / E-Katalog)',
    'pelaksanaan'   => 'Pelaksanaan Kontrak (SPK, Progres Fisik)',
    'serah_terima'  => 'Serah Terima Hasil Pekerjaan (BAST / PHO)',
];
$visibilityOptions = ['public' => 'Publik (Akses Terbuka)', 'internal' => 'Internal (Aparatur PBJ)', 'restricted' => 'Terbatas (Inspektorat/APIP)'];
$action = $isEdit ? route_to('dashboard.knowledge.case-studies.edit', (string) ($caseStudyId ?? '')) : route_to('dashboard.knowledge.case-studies.create');
$currentStatus   = $row['workflow_status'] ?? 'draft';
$currentVis      = $row['visibility'] ?? 'public';
$currentSeverity = $row['case_severity'] ?? 'medium';
$currentStage    = $row['procurement_stage'] ?? 'pelaksanaan';
$coverImgUrl     = ! empty($row['cover_file_id']) ? base_url('file/' . (int) $row['cover_file_id']) : null;
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Studi Kasus PBJ') ?></h6>
        <p class="text-secondary-light mb-0">Dokumentasikan studi kasus lapangan, mitigasi risiko pengadaan, dan pembelajaran praktis.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= route_to('dashboard.knowledge.case-studies') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
            <span>Kembali ke Daftar</span>
        </a>
    </div>
</div>

<form method="post" action="<?= $action ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row gy-24">
        <!-- Kolom Kiri: Kasus & Analisis -->
        <div class="col-xl-8 col-lg-7">
            <!-- Informasi Pokok Kasus -->
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:shield-warning-bold" class="text-danger-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Informasi Kasus &amp; Masalah Lapangan</h6>
                </div>
                <div class="card-body p-24">
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Judul Studi Kasus <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control radius-8" value="<?= esc(old('title', $row['title'] ?? '')) ?>" required maxlength="255" placeholder="Contoh: Sengketa Perubahan Kontrak (Addendum) Pembangunan Jembatan Way Sekampung">
                    </div>

                    <div class="row g-3 mb-20">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Satuan Kerja / Instansi Terkait</label>
                            <input type="text" name="organization_unit" class="form-control radius-8" value="<?= esc(old('organization_unit', $row['organization_unit'] ?? '')) ?>" placeholder="Contoh: Dinas Bina Marga & Bina Konstruksi Provinsi Lampung">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tahapan Pengadaan (PBJ)</label>
                            <select name="procurement_stage" class="form-select radius-8">
                                <?php foreach ($stageOptions as $stgKey => $stgLabel): ?>
                                    <option value="<?= $stgKey ?>" <?= $currentStage === $stgKey ? 'selected' : '' ?>><?= esc($stgLabel) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-20">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tingkat Risiko / Dampak Kasus</label>
                            <select name="case_severity" class="form-select radius-8">
                                <?php foreach ($severityOptions as $sevKey => $sevLabel): ?>
                                    <option value="<?= $sevKey ?>" <?= $currentSeverity === $sevKey ? 'selected' : '' ?>><?= esc($sevLabel) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Estimasi Waktu Telaah</label>
                            <div class="input-group">
                                <input type="number" name="reading_time" class="form-control radius-8" min="1" max="120" value="<?= esc(old('reading_time', $row['reading_time'] ?? 5)) ?>">
                                <span class="input-group-text bg-neutral-100 text-secondary-light">Menit</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Ringkasan Latar Belakang &amp; Duduk Perkara</label>
                        <textarea name="summary" class="form-control radius-8" rows="3" placeholder="Jelaskan ringkasan fakta kasus secara objektif untuk pengantar telaah..."><?= esc(old('summary', $row['summary'] ?? '')) ?></textarea>
                    </div>

                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kronologi &amp; Pembahasan Masalah Lengkap <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control radius-8" rows="10" placeholder="Uraikan alur kejadian, temuan audit/lapangan, dan dasar analisis klausul kontrak..." required><?= esc(old('content', $row['content'] ?? '')) ?></textarea>
                    </div>

                    <div>
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Solusi Penyelesaian &amp; Pelajaran (Lessons Learned)</label>
                        <textarea name="resolution_summary" class="form-control radius-8" rows="4" placeholder="Tuliskan rekomendasi tindakan korektif dan mitigasi pencegahan agar tidak terulang..."><?= esc(old('resolution_summary', $row['resolution_summary'] ?? '')) ?></textarea>
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
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kategori Studi Kasus</label>
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

        <!-- Kolom Kanan: Cover Upload & Status -->
        <div class="col-xl-4 col-lg-5">
            <!-- Cover Gambar Kasus -->
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:camera-bold" class="text-primary-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Cover Studi Kasus</h6>
                </div>
                <div class="card-body p-24">
                    <!-- Ukuran Size Recommendation Alert -->
                    <div class="alert alert-danger bg-danger-50 border-danger-200 radius-12 p-16 mb-16">
                        <div class="d-flex align-items-start gap-2">
                            <iconify-icon icon="solar:info-circle-bold" class="text-danger-600 text-xl flex-shrink-0 mt-1"></iconify-icon>
                            <div class="text-xs">
                                <strong class="d-block text-danger-900 mb-1">Rekomendasi Format Gambar:</strong>
                                <ul class="mb-0 ps-16 text-secondary-light">
                                    <li>Ukuran Resolusi: <strong class="text-danger-700">1200 x 630 px</strong> (Landscape 16:9)</li>
                                    <li>Format: <strong>JPG, PNG, WEBP</strong></li>
                                    <li>Maksimal Ukuran File: <strong>3 MB</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Live Image Preview Box -->
                    <div class="border radius-12 p-12 text-center bg-neutral-50 mb-16 position-relative" style="min-height: 160px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <img id="caseCoverPreview" src="<?= esc($coverImgUrl ?? '') ?>" alt="Preview Cover" class="w-100 radius-8 object-fit-cover <?= empty($coverImgUrl) ? 'd-none' : '' ?>" style="max-height: 200px;">
                        <div id="caseCoverPlaceholder" class="<?= ! empty($coverImgUrl) ? 'd-none' : '' ?> text-secondary-light py-20">
                            <iconify-icon icon="solar:gallery-broken" class="text-4xl mb-1 text-neutral-400"></iconify-icon>
                            <span class="d-block text-xs">Belum ada cover kasus dipilih</span>
                        </div>
                    </div>

                    <div>
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pilih Berkas Cover</label>
                        <input type="file" name="cover" class="form-control radius-8" accept="image/jpeg,image/png,image/webp,image/jpg" onchange="previewImageFile(this, 'caseCoverPreview', 'caseCoverPlaceholder')">
                        <?php if (! empty($row['cover_file_id'])): ?>
                            <span class="text-xs text-success mt-1 d-block">
                                <i class="fas fa-check-circle me-1"></i> File cover aktif: Asset #<?= (int) $row['cover_file_id'] ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Workflow & Unggulan -->
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:settings-bold" class="text-primary-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Status &amp; Visibilitas</h6>
                </div>
                <div class="card-body p-24">
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Kasus <span class="text-danger">*</span></label>
                        <select name="status" class="form-select radius-8" required>
                            <?php foreach ($statusOptions as $k => $label): ?>
                                <option value="<?= $k ?>" <?= $currentStatus === $k ? 'selected' : '' ?>><?= esc($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Hak Akses Visibilitas</label>
                        <select name="visibility" class="form-select radius-8">
                            <?php foreach ($visibilityOptions as $k => $l): ?>
                                <option value="<?= $k ?>" <?= $currentVis === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="p-16 border radius-12 bg-neutral-50">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="featured" value="1" id="featured" <?= ! empty($row['featured']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold text-sm text-primary-light" for="featured">
                                <i class="fas fa-star text-warning me-1"></i> Jadikan Kasus Unggulan
                            </label>
                        </div>
                        <span class="text-xs text-secondary-light mt-1 d-block">Ditampilkan di baris teratas modul Studi Kasus Bunga Rampai.</span>
                    </div>
                </div>
            </div>

            <!-- Action Submit -->
            <div class="card border-0 radius-16 shadow-sm bg-white p-20">
                <div class="d-flex flex-column gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-12 fw-semibold d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:disk-bold" class="text-lg"></iconify-icon>
                        <span><?= $isEdit ? 'Simpan Perubahan Kasus' : 'Simpan Studi Kasus Baru' ?></span>
                    </button>
                    <a href="<?= route_to('dashboard.knowledge.case-studies') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 text-center">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function previewImageFile(input, imgElementId, placeholderId) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
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
</script>
<?= $this->endSection() ?>