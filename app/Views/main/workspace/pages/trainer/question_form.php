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
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button type="button" class="btn btn-outline-success rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#importQuestionModal">
                    <i class="fas fa-file-excel me-1"></i> Impor Soal (Excel/CSV)
                </button>
                <a href="<?= route_to('workspace.trainer.quiz.question.template', (string) $classId, (string) $quiz['id']) ?>" class="btn btn-outline-primary rounded-pill px-3">
                    <i class="fas fa-download me-1"></i> Unduh Template
                </a>
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

    <!-- Modal Impor Soal Excel/CSV -->
    <div class="modal fade" id="importQuestionModal" tabindex="-1" aria-labelledby="importQuestionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-navy" id="importQuestionModalLabel">
                        <i class="fas fa-file-excel text-success me-2"></i>Impor Soal Pilihan Ganda
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= route_to('workspace.trainer.quiz.question.import', (string) $classId, (string) $quiz['id']) ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="modal-body p-4">
                        <!-- Aturan Menimpa Ulang -->
                        <div class="alert alert-warning border-warning-subtle bg-warning-subtle d-flex align-items-start gap-2 p-3 rounded-3 mb-3">
                            <i class="fas fa-exclamation-triangle text-warning fs-5 mt-1 flex-shrink-0"></i>
                            <div class="small">
                                <strong class="text-dark d-block mb-1">Perhatian (Logika Impor):</strong>
                                Mengunggah berkas soal akan <strong>MENIMPA ULANG (hapus ganti baru)</strong> seluruh butir soal yang ada saat ini pada ujian ini. Jika Anda ingin menambah soal meneruskan nomor selanjutnya, silakan gunakan form input manual di halaman ini.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-navy small mb-1">Unduh Berkas Template</label>
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light border">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-file-csv fa-2x text-primary"></i>
                                    <div>
                                        <strong class="d-block text-navy small">Template Format Soal</strong>
                                        <small class="text-muted">Format standar CSV/Excel dengan kolom Pertanyaan, Pilihan A-E, dan Kunci</small>
                                    </div>
                                </div>
                                <a href="<?= route_to('workspace.trainer.quiz.question.template', (string) $classId, (string) $quiz['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-download me-1"></i> Unduh
                                </a>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-semibold text-navy small mb-1">Pilih Berkas Soal (.csv atau .xlsx) <span class="text-danger">*</span></label>
                            <input type="file" name="question_file" accept=".csv,.xlsx,.txt" class="form-control rounded-3 py-2" required>
                            <small class="text-muted d-block mt-1" style="font-size: 0.74rem;">Mendukung berkas Microsoft Excel (.xlsx) dan CSV dengan pemisah koma atau titik koma (;).</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-2 d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-sm rounded-pill px-4 fw-semibold" onclick="return confirm('Apakah Anda yakin ingin mengimpor dan MENIMPA seluruh butir soal lama pada ujian ini?');">
                            <i class="fas fa-upload me-1"></i> Unggah & Timpa Soal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>