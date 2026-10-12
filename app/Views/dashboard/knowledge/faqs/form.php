<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$faq = $faq ?? [];
$categories = $categories ?? [];
$selectedCats = $selectedCategories ?? [];
$statusOptions = [
    'draft' => 'Draft', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan',
    'submitted' => 'Diajukan', 'in_review' => 'Ditinjau', 'approved' => 'Disetujui',
    'revision_required' => 'Perlu Revisi', 'rejected' => 'Ditolak',
];
$action = $isEdit ? route_to('dashboard.knowledge.faqs.edit', (string) ($faqId ?? '')) : route_to('dashboard.knowledge.faqs.create');
$currentStatus = $faq['workflow_status'] ?? 'published';
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Tanya Jawab (FAQ)') ?></h6>
        <p class="text-secondary-light mb-0">Kelola bank pertanyaan dan jawaban resmi regulasi pengadaan barang/jasa.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= route_to('dashboard.knowledge.faqs') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
            <span>Kembali ke Daftar</span>
        </a>
    </div>
</div>

<form method="post" action="<?= $action ?>">
    <?= csrf_field() ?>

    <div class="row gy-24">
        <!-- Kolom Kiri: Pertanyaan & Jawaban -->
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:question-circle-bold" class="text-primary-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Pertanyaan &amp; Jawaban Resmi</h6>
                </div>
                <div class="card-body p-24">
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pertanyaan <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control radius-8" value="<?= esc(old('title', $faq['title'] ?? '')) ?>" required maxlength="255" placeholder="Contoh: Bagaimana ketentuan penyesuaian harga (eskalasi) pada kontrak tahun jamak?">
                    </div>

                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Ringkasan Jawaban Singkat (Opsional)</label>
                        <textarea name="summary" class="form-control radius-8" rows="2" placeholder="Intisari jawaban 1-2 kalimat untuk pratinjau cepat..."><?= esc(old('summary', $faq['summary'] ?? '')) ?></textarea>
                    </div>

                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Uraian Jawaban Lengkap &amp; Penjelasan Regulasi <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control radius-8" rows="10" placeholder="Tuliskan jawaban lengkap dengan dasar peraturan dan tata cara pelaksanaannya..." required><?= esc(old('content', $faq['content'] ?? '')) ?></textarea>
                    </div>

                    <div>
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Dasar Hukum &amp; Referensi Regulasi</label>
                        <textarea name="legal_basis" class="form-control radius-8" rows="2" placeholder="Contoh: Pasal 54 Peraturan Presiden No. 16/2018 jo. Perpres No. 12/2021 dan Perlem LKPP No. 12/2021"><?= esc(old('legal_basis', $faq['legal_basis'] ?? '')) ?></textarea>
                        <span class="text-xs text-secondary-light mt-1 d-block">Akan ditampilkan sebagai rujukan regulasi resmi pada kartu FAQ.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Taksonomi & Pengaturan -->
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:settings-bold" class="text-primary-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Taksonomi &amp; Status</h6>
                </div>
                <div class="card-body p-24">
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kategori FAQ</label>
                        <select name="categories[]" class="form-select radius-8" multiple size="4">
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= (int) $c['id'] ?>" <?= in_array((int) $c['id'], array_map('intval', $selectedCats), true) ? 'selected' : '' ?>>
                                    <?= esc($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="text-xs text-secondary-light mt-1 d-block">Tahan Ctrl/Cmd untuk memilih lebih dari satu kategori.</span>
                    </div>

                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Urutan Prioritas Tampil</label>
                        <input type="number" name="sort_order" class="form-control radius-8" min="0" value="<?= esc(old('sort_order', $faq['sort_order'] ?? 0)) ?>" placeholder="0 = Urutan teratas">
                        <span class="text-xs text-secondary-light mt-1 d-block">Angka lebih kecil tampil lebih awal pada accordion.</span>
                    </div>

                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select radius-8" required>
                            <?php foreach ($statusOptions as $k => $label): ?>
                                <option value="<?= $k ?>" <?= $currentStatus === $k ? 'selected' : '' ?>><?= esc($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="card border-0 radius-16 shadow-sm bg-white p-20">
                <div class="d-flex flex-column gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-12 fw-semibold d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:disk-bold" class="text-lg"></iconify-icon>
                        <span><?= $isEdit ? 'Simpan Perubahan FAQ' : 'Simpan FAQ Baru' ?></span>
                    </button>
                    <a href="<?= route_to('dashboard.knowledge.faqs') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 text-center">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>