<?php
/**
 * Trainer - Kelola Soal & Pengaturan Ujian
 * FiStudy UI DNA
 */
$classId           = $classId ?? 0;
$className         = $className ?? 'Kelas';
$quiz              = $quiz ?? [];
$questions         = $questions ?? [];
$optionsByQuestion = $optionsByQuestion ?? [];
?>

<div class="trainer-quiz-edit-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-clipboard-check me-1"></i> <?= esc(strtoupper(str_replace('_', ' ', (string) ($quiz['type'] ?? 'QUIZ')))) ?>
                </span>
                <h1 class="h3 fw-bold text-navy mb-1"><?= esc($quiz['title'] ?? 'Kelola Soal Ujian') ?></h1>
                <p class="text-muted small mb-0">Kelas: <strong><?= esc($className) ?></strong> &middot; Total Soal: <strong><?= count($questions) ?></strong> Butir</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= route_to('workspace.trainer.quiz.attempts', (string) $classId, (string) $quiz['id']) ?>" class="btn btn-outline-info rounded-pill px-3">
                    <i class="fas fa-users-cog me-1"></i> Hasil Peserta
                </a>
                <a href="<?= route_to('workspace.trainer.class.detail', (string) $classId) ?>?tab=assessment" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Assessment
                </a>
            </div>
        </div>
    </div>

    <!-- Pengaturan Ujian Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
        <h2 class="h5 fw-bold text-navy mb-3"><i class="fas fa-cog text-primary me-2"></i>Pengaturan Ujian</h2>
        <form method="POST" action="<?= route_to('workspace.trainer.quiz.edit', (string) $classId, (string) $quiz['id']) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="update_meta">
            <div class="row g-3">
                <div class="col-12 col-md-5">
                    <label class="form-label fw-semibold text-navy small mb-1">Judul Ujian <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control rounded-3 py-2" value="<?= esc($quiz['title']) ?>" required>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label fw-semibold text-navy small mb-1">Durasi (Menit)</label>
                    <div class="input-group">
                        <input type="number" name="duration_minutes" min="0" value="<?= esc($quiz['duration_minutes'] ?? '') ?>" class="form-control rounded-start-3 py-2" placeholder="0">
                        <span class="input-group-text bg-light text-muted small">Mnt</span>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label fw-semibold text-navy small mb-1">Attempt Limit</label>
                    <input type="number" name="attempt_limit" min="1" value="<?= (int) ($quiz['attempt_limit'] ?? 1) ?>" class="form-control rounded-3 py-2">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-navy small mb-1">Status Publikasi</label>
                    <select name="status" class="form-select rounded-3 py-2">
                        <option value="published" <?= (($quiz['status'] ?? 'draft') === 'published') ? 'selected' : '' ?>>Published (Aktif)</option>
                        <option value="draft" <?= (($quiz['status'] ?? 'draft') === 'draft') ? 'selected' : '' ?>>Draft (Draf)</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-navy small mb-1">Petunjuk & Instruksi Pengerjaan</label>
                    <textarea name="instructions" class="form-control rounded-3" rows="3"><?= esc($quiz['instructions'] ?? '') ?></textarea>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                        <i class="fas fa-save me-1"></i> Simpan Pengaturan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Daftar Soal Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 border-bottom pb-3">
            <div>
                <h2 class="h5 fw-bold text-navy mb-1">Daftar Butir Pertanyaan</h2>
                <small class="text-muted">Kelola soal pilihan ganda, kunci jawaban, dan bobot poin.</small>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button type="button" class="btn btn-outline-success rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#importQuestionModal">
                    <i class="fas fa-file-excel me-1"></i> Impor Soal (Excel/CSV)
                </button>
                <a href="<?= route_to('workspace.trainer.quiz.question.template', (string) $classId, (string) $quiz['id']) ?>" class="btn btn-outline-primary rounded-pill px-3">
                    <i class="fas fa-download me-1"></i> Unduh Template
                </a>
                <a href="<?= route_to('workspace.trainer.question.create', (string) $classId, (string) $quiz['id']) ?>" class="btn btn-primary rounded-pill px-4 fw-semibold">
                    <i class="fas fa-plus me-1"></i> Tambah Soal Manual
                </a>
            </div>
        </div>

        <?php if (empty($questions)): ?>
            <div class="text-center py-5 border rounded-4 bg-light">
                <i class="fas fa-question fa-3x text-muted mb-3 opacity-25"></i>
                <h3 class="h6 fw-bold text-navy mb-1">Belum Ada Soal</h3>
                <p class="text-muted small mb-3">Klik tombol "+ Tambah Soal" di atas untuk menambahkan butir pertanyaan kuis.</p>
                <a href="<?= route_to('workspace.trainer.question.create', (string) $classId, (string) $quiz['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-4">
                    + Tambah Soal
                </a>
            </div>
        <?php else: ?>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($questions as $qIdx => $q): ?>
                    <?php $opts = $optionsByQuestion[(int) $q['id']] ?? []; ?>
                    <div class="border rounded-4 p-4 bg-white shadow-sm">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div class="d-flex align-items-start gap-2">
                                <span class="badge bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                    <?= $qIdx + 1 ?>
                                </span>
                                <h3 class="h6 fw-bold text-navy mb-0 pt-1"><?= esc($q['prompt']) ?></h3>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 small fw-semibold">
                                    <?= (float) ($q['points'] ?? 1.0) ?> Poin
                                </span>
                                <form action="<?= route_to('workspace.trainer.question.delete', (string) $classId, (string) $quiz['id'], (string) $q['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus pertanyaan ini beserta pilihan jawabannya?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-light border text-danger rounded-pill px-3" title="Hapus Soal">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Options List -->
                        <div class="ps-4">
                            <div class="row g-2">
                                <?php foreach ($opts as $oIdx => $o): ?>
                                    <div class="col-12 col-md-6">
                                        <div class="p-2 px-3 rounded-3 border d-flex align-items-center gap-2 <?= ((int) $o['is_correct'] === 1) ? 'bg-success-subtle border-success text-success fw-semibold' : 'bg-light text-muted' ?>">
                                            <span class="badge <?= ((int) $o['is_correct'] === 1) ? 'bg-success text-white' : 'bg-secondary text-white' ?> rounded-circle" style="width: 22px; height: 22px; font-size: 0.7rem;">
                                                <?= chr(65 + $oIdx) ?>
                                            </span>
                                            <span class="small"><?= esc($o['option_text']) ?></span>
                                            <?php if ((int) $o['is_correct'] === 1): ?>
                                                <i class="fas fa-check-circle ms-auto text-success"></i>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if (! empty($q['explanation'])): ?>
                                <div class="bg-light-subtle border rounded-3 p-2 px-3 mt-3 text-muted small">
                                    <i class="fas fa-info-circle text-primary me-1"></i>
                                    <strong>Penjelasan:</strong> <?= esc($q['explanation']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
                                Mengunggah berkas soal akan <strong>MENIMPA ULANG (hapus ganti baru)</strong> seluruh butir soal yang ada saat ini pada ujian ini. Jika Anda ingin menambah soal meneruskan nomor selanjutnya, silakan gunakan tombol <strong>Tambah Soal Manual</strong>.
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
