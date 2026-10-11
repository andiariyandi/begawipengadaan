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

    <!-- Alert Notifications -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Pengaturan Ujian Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
        <h2 class="h5 fw-bold text-navy mb-3"><i class="fas fa-cog text-primary me-2"></i>Pengaturan Ujian</h2>
        <form method="POST" action="<?= route_to('workspace.trainer.quiz.edit', (string) $classId, (string) $quiz['id']) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="update_meta">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-navy small mb-1">Judul Ujian <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control rounded-3 py-2" value="<?= esc($quiz['title']) ?>" required>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label fw-semibold text-navy small mb-1">Batas Percobaan (Attempt Limit)</label>
                    <input type="number" name="attempt_limit" min="1" value="<?= (int) ($quiz['attempt_limit'] ?? 1) ?>" class="form-control rounded-3 py-2">
                </div>
                <div class="col-6 col-md-3">
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
            <div>
                <a href="<?= route_to('workspace.trainer.question.create', (string) $classId, (string) $quiz['id']) ?>" class="btn btn-primary rounded-pill px-4 fw-semibold">
                    <i class="fas fa-plus me-1"></i> Tambah Soal
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
</div>