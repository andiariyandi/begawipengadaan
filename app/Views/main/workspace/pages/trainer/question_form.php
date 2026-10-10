<?php
/**
 * Trainer - Form Tambah Butir Soal Ujian
 * FiStudy UI DNA
 */
$classId   = $classId ?? 0;
$quiz      = $quiz ?? [];
$className = $className ?? 'Kelas';
?>

<div class="trainer-question-form-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-question-circle me-1"></i> Butir Soal Baru
                </span>
                <h1 class="h3 fw-bold text-navy mb-1">Tambah Pertanyaan</h1>
                <p class="text-muted small mb-0">
                    Ujian: <strong><?= esc($quiz['title'] ?? '-') ?></strong> &middot; Kelas: <strong><?= esc($className) ?></strong>
                </p>
            </div>
            <div>
                <a href="<?= route_to('workspace.trainer.quiz.edit', (string) $classId, (string) $quiz['id']) ?>" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Soal
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

        <form method="POST" action="<?= route_to('workspace.trainer.question.create', (string) $classId, (string) $quiz['id']) ?>">
            <?= csrf_field() ?>
            <div class="row g-4">
                <div class="col-12 col-md-9">
                    <label class="form-label fw-semibold text-navy small mb-1">Teks Pertanyaan <span class="text-danger">*</span></label>
                    <textarea name="prompt" class="form-control rounded-3" rows="4" placeholder="Tuliskan pertanyaan soal pilihan ganda di sini..." required></textarea>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-navy small mb-1">Bobot Poin</label>
                    <input type="number" name="points" min="0.01" step="0.01" value="1.00" class="form-control rounded-3 py-2">
                    <small class="text-muted" style="font-size: 0.72rem;">Nilai skor jika menjawab benar</small>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-navy small mb-1">Pembahasan & Penjelasan Jawaban (Opsional)</label>
                    <textarea name="explanation" class="form-control rounded-3" rows="3" placeholder="Penjelasan mengenai alasan mengapa pilihan tersebut benar (tampil setelah ujian selesai dievaluasi)..."></textarea>
                </div>

                <!-- Pilihan Jawaban Section -->
                <div class="col-12">
                    <h3 class="h6 fw-bold text-navy mb-3"><i class="fas fa-list-ul text-primary me-2"></i>Pilihan Jawaban & Kunci Benar</h3>
                    <p class="text-muted small mb-3">Tuliskan opsi jawaban dan tandai bulatan <strong>Kunci Benar</strong> pada jawaban yang tepat.</p>

                    <div class="d-flex flex-column gap-3">
                        <!-- Option A -->
                        <div class="border rounded-3 p-3 bg-light d-flex align-items-center gap-3">
                            <span class="badge bg-primary rounded-circle p-2" style="width: 28px; height: 28px;">A</span>
                            <input type="text" name="options[]" class="form-control rounded-3 bg-white" placeholder="Pilihan jawaban A..." required>
                            <div class="form-check text-nowrap ms-2">
                                <input type="radio" name="correct_option" value="0" id="correct_0" class="form-check-input" required checked>
                                <label class="form-check-label fw-semibold text-success small" for="correct_0">Kunci Benar</label>
                            </div>
                        </div>

                        <!-- Option B -->
                        <div class="border rounded-3 p-3 bg-light d-flex align-items-center gap-3">
                            <span class="badge bg-primary rounded-circle p-2" style="width: 28px; height: 28px;">B</span>
                            <input type="text" name="options[]" class="form-control rounded-3 bg-white" placeholder="Pilihan jawaban B..." required>
                            <div class="form-check text-nowrap ms-2">
                                <input type="radio" name="correct_option" value="1" id="correct_1" class="form-check-input">
                                <label class="form-check-label fw-semibold text-success small" for="correct_1">Kunci Benar</label>
                            </div>
                        </div>

                        <!-- Option C -->
                        <div class="border rounded-3 p-3 bg-light d-flex align-items-center gap-3">
                            <span class="badge bg-primary rounded-circle p-2" style="width: 28px; height: 28px;">C</span>
                            <input type="text" name="options[]" class="form-control rounded-3 bg-white" placeholder="Pilihan jawaban C...">
                            <div class="form-check text-nowrap ms-2">
                                <input type="radio" name="correct_option" value="2" id="correct_2" class="form-check-input">
                                <label class="form-check-label fw-semibold text-success small" for="correct_2">Kunci Benar</label>
                            </div>
                        </div>

                        <!-- Option D -->
                        <div class="border rounded-3 p-3 bg-light d-flex align-items-center gap-3">
                            <span class="badge bg-primary rounded-circle p-2" style="width: 28px; height: 28px;">D</span>
                            <input type="text" name="options[]" class="form-control rounded-3 bg-white" placeholder="Pilihan jawaban D...">
                            <div class="form-check text-nowrap ms-2">
                                <input type="radio" name="correct_option" value="3" id="correct_3" class="form-check-input">
                                <label class="form-check-label fw-semibold text-success small" for="correct_3">Kunci Benar</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="<?= route_to('workspace.trainer.quiz.edit', (string) $classId, (string) $quiz['id']) ?>" class="btn btn-outline-secondary rounded-pill px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                        <i class="fas fa-save me-1"></i> Simpan Pertanyaan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>