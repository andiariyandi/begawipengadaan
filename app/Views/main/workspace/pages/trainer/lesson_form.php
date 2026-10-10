<?php
/**
 * Trainer - Form Lesson / Materi
 * FiStudy UI DNA
 */
$mode       = $mode ?? 'create';
$classId    = $classId ?? 0;
$module     = $module ?? [];
$lesson     = $lesson ?? null;
$className  = $className ?? 'Kelas';
$targetUrl  = ($mode === 'edit')
    ? route_to('workspace.trainer.lesson.edit', (string) $classId, (string) ($lesson['id'] ?? 0))
    : route_to('workspace.trainer.lesson.create', (string) $classId, (string) ($module['id'] ?? 0));
?>

<div class="trainer-lesson-form-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-book-reader me-1"></i> Materi Pembelajaran
                </span>
                <h1 class="h3 fw-bold text-navy mb-1"><?= $mode === 'edit' ? 'Ubah Materi Pembelajaran' : 'Tambah Materi Baru' ?></h1>
                <p class="text-muted small mb-0">
                    Modul: <strong><?= esc($module['title'] ?? '-') ?></strong> &middot; Kelas: <strong><?= esc($className) ?></strong>
                </p>
            </div>
            <div>
                <a href="<?= route_to('workspace.trainer.class.detail', (string) $classId) ?>?tab=materi" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Silabus
                </a>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-lg-5">
        <?php if ($error = session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?= esc($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= $targetUrl ?>">
            <?= csrf_field() ?>
            <div class="row g-4">
                <div class="col-12 col-md-8">
                    <label class="form-label fw-semibold text-navy small mb-1">Judul Materi <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control rounded-3 py-2" placeholder="Contoh: Pengenalan Regulasi Pengadaan Perpres 12/2021" value="<?= esc($lesson['title'] ?? old('title')) ?>" required>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-navy small mb-1">Tipe Materi <span class="text-danger">*</span></label>
                    <select name="content_type" id="content_type_select" class="form-select rounded-3 py-2" required onchange="toggleContentFields(this.value)">
                        <?php foreach (['text' => 'Teks / Bacaan Artikel', 'video' => 'Video Pembelajaran (YouTube / Embed)', 'document' => 'Dokumen / Modul PDF', 'link' => 'Tautan / Referensi Luar'] as $key => $label): ?>
                            <option value="<?= $key ?>" <?= (($lesson['content_type'] ?? old('content_type') ?? 'text') === $key) ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-6 col-md-4">
                    <label class="form-label fw-semibold text-navy small mb-1">Urutan Pembelajaran</label>
                    <input type="number" name="sort_order" class="form-control rounded-3 py-2" value="<?= (int) ($lesson['sort_order'] ?? old('sort_order') ?? 0) ?>" min="0">
                    <small class="text-muted" style="font-size: 0.72rem;">0 = otomatis di urutan berikutnya</small>
                </div>

                <div class="col-6 col-md-4">
                    <label class="form-label fw-semibold text-navy small mb-1">Status Publikasi</label>
                    <select name="status" class="form-select rounded-3 py-2">
                        <option value="published" <?= (($lesson['status'] ?? old('status') ?? 'published') === 'published') ? 'selected' : '' ?>>Published (Langsung Dapat Diakses)</option>
                        <option value="draft" <?= (($lesson['status'] ?? old('status')) === 'draft') ? 'selected' : '' ?>>Draft (Belum Tampil di Peserta)</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-navy small mb-1">Kelayakan</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" name="is_required" value="1" id="is_required" class="form-check-input"
                            <?= ($mode === 'edit')
                                ? (((int) ($lesson['is_required'] ?? 0) === 1) ? 'checked' : '')
                                : 'checked' ?>>
                        <label class="form-check-label fw-semibold text-navy small" for="is_required">
                            Wajib Ditandai Selesai
                        </label>
                    </div>
                </div>

                <!-- Video / External URL field -->
                <div class="col-12" id="field_external_url" style="display: none;">
                    <label class="form-label fw-semibold text-navy small mb-1">URL Video / Tautan Eksternal <span class="text-danger">*</span></label>
                    <input type="url" name="external_url" class="form-control rounded-3 py-2" placeholder="https://www.youtube.com/watch?v=... atau https://vimeo.com/..." value="<?= esc($lesson['external_url'] ?? old('external_url')) ?>">
                    <small class="text-muted">Mendukung tautan YouTube, Vimeo, atau media online.</small>
                </div>

                <!-- Document label field -->
                <div class="col-12" id="field_document_label" style="display: none;">
                    <label class="form-label fw-semibold text-navy small mb-1">Nama / Label Dokumen Referensi</label>
                    <input type="text" name="document_label" class="form-control rounded-3 py-2" placeholder="Contoh: Modul-Pembelajaran-PBJ-2026.pdf" value="<?= esc($lesson['title'] ?? old('document_label')) ?>">
                    <small class="text-muted">Aset berkas terdaftar otomatis dalam repositori dokumen pelatihan.</small>
                </div>

                <!-- Text Content field -->
                <div class="col-12" id="field_content">
                    <label class="form-label fw-semibold text-navy small mb-1">Isi Konten Teks / Catatan Pembelajaran</label>
                    <textarea name="content" class="form-control rounded-3" rows="8" placeholder="Tuliskan uraian materi, penjelasan konsep, studi kasus, atau rangkuman materi di sini..."><?= esc($lesson['content'] ?? old('content')) ?></textarea>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="<?= route_to('workspace.trainer.class.detail', (string) $classId) ?>?tab=materi" class="btn btn-outline-secondary rounded-pill px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                        <i class="fas fa-save me-1"></i> <?= $mode === 'edit' ? 'Simpan Perubahan' : 'Tambahkan Materi' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleContentFields(type) {
    const urlField = document.getElementById('field_external_url');
    const docField = document.getElementById('field_document_label');
    const contentField = document.getElementById('field_content');

    if (type === 'video' || type === 'link') {
        urlField.style.display = 'block';
        docField.style.display = 'none';
    } else if (type === 'document') {
        urlField.style.display = 'none';
        docField.style.display = 'block';
    } else {
        urlField.style.display = 'none';
        docField.style.display = 'none';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    toggleContentFields(document.getElementById('content_type_select').value);
});
</script>