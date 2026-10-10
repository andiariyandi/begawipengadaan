<?php
/**
 * Trainer - Form Modul
 * FiStudy UI DNA
 */
$mode       = $mode ?? 'create';
$classId    = $classId ?? 0;
$module     = $module ?? null;
$className  = $className ?? 'Kelas';
$targetUrl  = ($mode === 'edit')
    ? route_to('workspace.trainer.module.edit', (string) $classId, (string) ($module['id'] ?? 0))
    : route_to('workspace.trainer.module.create', (string) $classId);
?>

<div class="trainer-module-form-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-layer-group me-1"></i> Manajemen Modul
                </span>
                <h1 class="h3 fw-bold text-navy mb-1"><?= $mode === 'edit' ? 'Ubah Modul' : 'Tambah Modul Pembelajaran' ?></h1>
                <p class="text-muted small mb-0">Kelas: <strong><?= esc($className) ?></strong></p>
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
                    <label class="form-label fw-semibold text-navy small mb-1">Judul Modul <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control rounded-3 py-2" placeholder="Contoh: Modul 1 — Pengenalan Pengadaan Barang/Jasa" value="<?= esc($module['title'] ?? old('title')) ?>" required>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label fw-semibold text-navy small mb-1">Urutan Modul</label>
                    <input type="number" name="sort_order" class="form-control rounded-3 py-2" value="<?= (int) ($module['sort_order'] ?? (old('sort_order') ?: 0)) ?>" min="0">
                    <small class="text-muted" style="font-size: 0.72rem;">0 = otomatis di urutan terakhir</small>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label fw-semibold text-navy small mb-1">Status Publikasi</label>
                    <select name="status" class="form-select rounded-3 py-2">
                        <?php foreach (['published' => 'Published (Tersedia)', 'draft' => 'Draft (Draf)', 'archived' => 'Archived'] as $stKey => $stLabel): ?>
                            <option value="<?= $stKey ?>" <?= (($module['status'] ?? old('status') ?? 'published') === $stKey) ? 'selected' : '' ?>>
                                <?= $stLabel ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-navy small mb-1">Deskripsi Singkat / Tujuan Pembelajaran</label>
                    <textarea name="description" class="form-control rounded-3" rows="4" placeholder="Jelaskan ringkasan materi atau kompetensi yang akan dicapai pada modul ini..."><?= esc($module['description'] ?? old('description')) ?></textarea>
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="is_required" value="1" id="is_required" class="form-check-input"
                            <?= ($mode === 'edit')
                                ? (((int) ($module['is_required'] ?? 0) === 1) ? 'checked' : '')
                                : 'checked' ?>>
                        <label class="form-check-label fw-semibold text-navy small" for="is_required">
                            Modul Wajib Diselesaikan (harus dipelajari sebelum membuka modul berikutnya)
                        </label>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="<?= route_to('workspace.trainer.class.detail', (string) $classId) ?>?tab=materi" class="btn btn-outline-secondary rounded-pill px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                        <i class="fas fa-save me-1"></i> <?= $mode === 'edit' ? 'Simpan Perubahan' : 'Buat Modul' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>