<?php
/**
 * Trainer - Form Tambah Ujian / Quiz
 * FiStudy UI DNA
 */
$classId   = $classId ?? 0;
$className = $className ?? 'Kelas';
$lessons   = $lessons ?? [];
?>

<div class="trainer-quiz-form-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-clipboard-check me-1"></i> Assessment Builder
                </span>
                <h1 class="h3 fw-bold text-navy mb-1">Tambah Ujian Baru</h1>
                <p class="text-muted small mb-0">Kelas: <strong><?= esc($className) ?></strong></p>
            </div>
            <div>
                <a href="<?= route_to('workspace.trainer.class.detail', (string) $classId) ?>?tab=assessment" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Assessment
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

        <form method="POST" action="<?= route_to('workspace.trainer.quiz.create', (string) $classId) ?>">
            <?= csrf_field() ?>
            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-navy small mb-1">Tipe Ujian <span class="text-danger">*</span></label>
                    <select name="type" id="quiz_type_select" class="form-select rounded-3 py-2" required onchange="toggleQuizLessonSelect(this.value)">
                        <option value="pre_test">Pre-Test (Ujian Awal Kelas)</option>
                        <option value="lesson_quiz" selected>Quiz Modul (Evaluasi Modul Pembelajaran)</option>
                        <option value="post_test">Post-Test (Ujian Akhir Kelulusan)</option>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-navy small mb-1">Judul Ujian <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control rounded-3 py-2" placeholder="Contoh: Kuis Modul 1 — Konsep Dasar PBJ" required>
                </div>

                <div class="col-12" id="lesson_select_wrapper">
                    <label class="form-label fw-semibold text-navy small mb-1">Kaitkan dengan Materi / Modul</label>
                    <select name="lesson_id" class="form-select rounded-3 py-2">
                        <option value="">-- Pilih Materi / Lesson Terkait --</option>
                        <?php foreach ($lessons as $ls): ?>
                            <option value="<?= esc($ls['id']) ?>">
                                <?= esc(($ls['module_title'] ?? 'Modul') . ' &middot; ' . ($ls['title'] ?? 'Materi')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Kuis modul akan terbuka bagi peserta setelah seluruh materi pada modul terkait selesai dipelajari.</small>
                </div>

                <div class="col-6 col-md-4">
                    <label class="form-label fw-semibold text-navy small mb-1">Batas Percobaan (Attempt Limit)</label>
                    <input type="number" name="attempt_limit" min="1" value="1" class="form-control rounded-3 py-2">
                    <small class="text-muted" style="font-size: 0.72rem;">Jumlah maksimum peserta dapat mengulang kuis</small>
                </div>

                <div class="col-6 col-md-4">
                    <label class="form-label fw-semibold text-navy small mb-1">Status Publikasi</label>
                    <select name="status" class="form-select rounded-3 py-2">
                        <option value="published">Published (Aktifkan)</option>
                        <option value="draft">Draft (Draf)</option>
                    </select>
                </div>

                <div class="col-12 col-md-4 d-flex align-items-center">
                    <div class="form-check mt-3">
                        <input type="checkbox" name="is_required" value="1" id="is_required" class="form-check-input" checked>
                        <label class="form-check-label fw-semibold text-navy small" for="is_required">
                            Wajib Dikerjakan untuk Kelulusan
                        </label>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-navy small mb-1">Petunjuk & Instruksi Pengerjaan</label>
                    <textarea name="instructions" class="form-control rounded-3" rows="4" placeholder="Tuliskan petunjuk pengerjaan soal, jumlah butir pertanyaan, dan tata cara menjawab..."></textarea>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="<?= route_to('workspace.trainer.class.detail', (string) $classId) ?>?tab=assessment" class="btn btn-outline-secondary rounded-pill px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                        <i class="fas fa-save me-1"></i> Simpan & Lanjut Tambah Soal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleQuizLessonSelect(type) {
    const wrapper = document.getElementById('lesson_select_wrapper');
    if (type === 'lesson_quiz') {
        wrapper.style.display = 'block';
    } else {
        wrapper.style.display = 'none';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    toggleQuizLessonSelect(document.getElementById('quiz_type_select').value);
});
</script>