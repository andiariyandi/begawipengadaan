<?php
/**
 * Trainer - Form Tugas / Assignment
 * FiStudy UI DNA
 */
$mode          = $mode ?? 'create';
$classId       = $classId ?? 0;
$assignment    = $assignment ?? null;
$lessons       = $lessons ?? [];
$className     = $className ?? 'Kelas';
$targetUrl     = ($mode === 'edit')
    ? route_to('workspace.trainer.assignment.edit', (string) $classId, (string) ($assignment['id'] ?? 0))
    : route_to('workspace.trainer.assignment.create', (string) $classId);

$existingGuide = $existingGuide ?? parse_assignment_guide($assignment['instructions'] ?? '');
$cleanInstructions = $existingGuide['clean_instructions'] ?? ($assignment['instructions'] ?? old('instructions') ?? '');
?>

<div class="trainer-assignment-form-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-info text-dark px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-tasks me-1"></i> Penugasan Kasus & Praktik
                </span>
                <h1 class="h3 fw-bold text-navy mb-1"><?= $mode === 'edit' ? 'Ubah Penugasan' : 'Buat Tugas Baru' ?></h1>
                <p class="text-muted small mb-0">Kelas: <strong><?= esc($className) ?></strong></p>
            </div>
            <div>
                <a href="<?= route_to('workspace.trainer.class.detail', (string) $classId) ?>?tab=assignment" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Assignment
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

        <form method="POST" action="<?= $targetUrl ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row g-4">
                <div class="col-12 col-md-8">
                    <label class="form-label fw-semibold text-navy small mb-1">Judul Penugasan <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control rounded-3 py-2" placeholder="Contoh: Studi Kasus Penyusunan Dokumen HPS dan Spesifikasi Teknis" value="<?= esc($assignment['title'] ?? old('title')) ?>" required>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-navy small mb-1">Kaitkan dengan Materi / Modul</label>
                    <select name="lesson_id" class="form-select rounded-3 py-2">
                        <option value="">-- Tugas Umum Kelas --</option>
                        <?php foreach ($lessons as $ls): ?>
                            <option value="<?= esc($ls['id']) ?>" <?= ((int) ($assignment['lesson_id'] ?? old('lesson_id') ?? 0) === (int) $ls['id']) ? 'selected' : '' ?>>
                                <?= esc(($ls['module_title'] ?? 'Modul') . ' &middot; ' . ($ls['title'] ?? 'Materi')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-navy small mb-1">Batas Waktu Pengumpulan (Deadline)</label>
                    <input type="datetime-local" name="due_at" class="form-control rounded-3 py-2" value="<?= ! empty($assignment['due_at']) ? date('Y-m-d\TH:i', strtotime($assignment['due_at'])) : old('due_at') ?>">
                    <small class="text-muted" style="font-size: 0.72rem;">Kosongkan jika tidak ada batas waktu kaku</small>
                </div>

                <div class="col-6 col-md-4">
                    <label class="form-label fw-semibold text-navy small mb-1">Batas Percobaan Submit</label>
                    <input type="number" name="max_attempts" min="1" value="<?= (int) ($assignment['max_attempts'] ?? old('max_attempts') ?? 1) ?>" class="form-control rounded-3 py-2">
                </div>

                <div class="col-6 col-md-4">
                    <label class="form-label fw-semibold text-navy small mb-1">Status Publikasi</label>
                    <select name="status" class="form-select rounded-3 py-2">
                        <option value="published" <?= (($assignment['status'] ?? old('status') ?? 'published') === 'published') ? 'selected' : '' ?>>Published (Tampil di Peserta)</option>
                        <option value="draft" <?= (($assignment['status'] ?? old('status')) === 'draft') ? 'selected' : '' ?>>Draft (Draf)</option>
                    </select>
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="is_required" value="1" id="is_required" class="form-check-input"
                            <?= ($mode === 'edit')
                                ? (((int) ($assignment['is_required'] ?? 0) === 1) ? 'checked' : '')
                                : 'checked' ?>>
                        <label class="form-check-label fw-semibold text-navy small" for="is_required">
                            Tugas Wajib Diselesaikan (harus dikumpulkan untuk syarat pembukaan modul/kelulusan)
                        </label>
                    </div>
                </div>

                <!-- Upload File Petunjuk Tugas (PDF) -->
                <div class="col-12">
                    <div class="p-3 rounded-3 border bg-light">
                        <label class="form-label fw-semibold text-navy small mb-1 d-flex align-items-center gap-2">
                            <i class="fas fa-file-pdf text-danger fs-5"></i>
                            <span>File Petunjuk / Panduan Tugas (PDF)</span>
                        </label>
                        <input type="file" name="guide_file" accept=".pdf,application/pdf" class="form-control bg-white rounded-3 py-2">
                        <small class="text-muted d-block mt-1" style="font-size: 0.74rem;">
                            Format PDF (.pdf) maksimal 10MB. File petunjuk teknis/studi kasus ini dapat diunduh dan dilihat langsung (View PDF) oleh peserta di ruang belajar.
                        </small>

                        <?php if (! empty($existingGuide['guide_url'])): ?>
                            <div class="mt-3 p-3 rounded-3 bg-white border d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                    <div>
                                        <strong class="d-block small text-navy"><?= esc($existingGuide['guide_filename'] ?? 'Petunjuk_Tugas.pdf') ?></strong>
                                        <span class="text-muted" style="font-size: 0.72rem;">File petunjuk aktif saat ini</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <a href="<?= base_url($existingGuide['guide_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="fas fa-eye me-1"></i> Lihat PDF
                                    </a>
                                    <div class="form-check form-check-inline mb-0">
                                        <input class="form-check-input" type="checkbox" name="remove_guide_file" value="1" id="remove_guide_file">
                                        <label class="form-check-label text-danger small" for="remove_guide_file">Hapus file saat ini</label>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-navy small mb-1">Instruksi & Lembar Kerja Tugas</label>
                    <textarea name="instructions" class="form-control rounded-3" rows="6" placeholder="Jelaskan skenario kasus, petunjuk analisis, format pengerjaan, dan rubrik penilaian tugas..."><?= esc($cleanInstructions) ?></textarea>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="<?= route_to('workspace.trainer.class.detail', (string) $classId) ?>?tab=assignment" class="btn btn-outline-secondary rounded-pill px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                        <i class="fas fa-save me-1"></i> <?= $mode === 'edit' ? 'Simpan Perubahan' : 'Buat Tugas' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
