<?php
/**
 * Trainer - Review & Penilaian Submission Tugas
 * FiStudy UI DNA
 */
$submission  = $submission ?? [];
$files       = $files ?? [];
$allAttempts = $allAttempts ?? [];
$classId     = $classId ?? 0;
$className   = $className ?? 'Kelas';
?>

<div class="trainer-submission-review-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-check-circle me-1"></i> Formulir Evaluasi Tugas
                </span>
                <h1 class="h3 fw-bold text-navy mb-1">Review Submission: <?= esc($submission['user_name'] ?? 'Peserta') ?></h1>
                <p class="text-muted small mb-0">
                    Tugas: <strong><?= esc($submission['assignment_title'] ?? '-') ?></strong> &middot; Kelas: <strong><?= esc($className) ?></strong>
                </p>
            </div>
            <div>
                <a href="<?= route_to('workspace.trainer.assignment.submissions', (string) $classId, (string) $submission['assignment_id']) ?>" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Submissions
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Kolom Kiri: Jawaban Peserta -->
        <div class="col-12 col-lg-7">
            <!-- Instruksi Tugas Ringkas -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <h2 class="h6 fw-bold text-navy mb-2"><i class="fas fa-info-circle text-primary me-2"></i>Instruksi Tugas</h2>
                <div class="text-muted small lh-base bg-light rounded-3 p-3">
                    <?= nl2br(esc($submission['assignment_instructions'] ?? 'Tidak ada instruksi khusus.')) ?>
                </div>
            </div>

            <!-- Jawaban / Dokumen yang Dikumpulkan Peserta -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <?php if (! empty($allAttempts) && count($allAttempts) > 1): ?>
                    <div class="mb-3 p-2 px-3 bg-light rounded-3 border d-flex flex-wrap align-items-center gap-2">
                        <span class="small fw-bold text-navy me-1"><i class="fas fa-history text-primary me-1"></i>Riwayat Percobaan Peserta:</span>
                        <?php foreach ($allAttempts as $attRow): ?>
                            <?php
                                $isCurrent = (int) $attRow['id'] === (int) $submission['id'];
                                $attSc = $attRow['score'] !== null ? (float) $attRow['score'] : null;
                            ?>
                            <a href="<?= route_to('workspace.trainer.submission.review', (string) $attRow['id']) ?>" class="btn btn-sm <?= $isCurrent ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill py-0 px-2" style="font-size: 11px;">
                                #<?= (int) $attRow['attempt_number'] ?>: <?= $attSc !== null ? $attSc : 'Belum Dinilai' ?>
                                <?= $isCurrent ? '(Sedang Dilihat)' : '' ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 fw-bold text-navy mb-0"><i class="fas fa-file-alt text-primary me-2"></i>Jawaban Peserta</h2>
                    <span class="badge bg-light text-muted border rounded-pill px-2 py-1 small">
                        Percobaan ke-<?= (int) ($submission['attempt_number'] ?? 1) ?>
                    </span>
                </div>

                <div class="p-3 bg-light rounded-3 mb-4">
                    <small class="text-muted d-block mb-2">Teks Jawaban:</small>
                    <?php if (! empty($submission['content'])): ?>
                        <div class="text-navy lh-lg small">
                            <?= nl2br(esc($submission['content'])) ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small fst-italic mb-0">Peserta tidak menyertakan teks uraian tambahan.</p>
                    <?php endif; ?>
                </div>

                <!-- Berkas / File Lampiran -->
                <h3 class="h6 fw-bold text-navy mb-2"><i class="fas fa-paperclip text-primary me-2"></i>Berkas Lampiran</h3>
                <?php if (empty($files)): ?>
                    <p class="text-muted small mb-0">Tidak ada berkas file yang diunggah.</p>
                <?php else: ?>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($files as $f): ?>
                            <div class="border rounded-3 p-3 d-flex justify-content-between align-items-center bg-white">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-file-pdf text-danger fs-5"></i>
                                    <div>
                                        <strong class="text-navy small d-block"><?= esc($f['original_name'] ?? 'Berkas Tugas') ?></strong>
                                        <small class="text-muted"><?= round(((int) ($f['size_bytes'] ?? 0)) / 1024, 1) ?> KB</small>
                                    </div>
                                </div>
                                <span class="badge bg-light text-muted border rounded-pill px-3 py-1 small">
                                    Tersimpan di Sistem
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kolom Kanan: Form Penilaian & Feedback Pengajar -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h2 class="h5 fw-bold text-navy mb-3"><i class="fas fa-pen-fancy text-primary me-2"></i>Form Evaluasi & Nilai</h2>
                <form method="POST" action="<?= route_to('workspace.trainer.submission.review.submit', (string) $submission['id']) ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-navy small mb-1">Skor / Nilai (0 - 100) <span class="text-danger">*</span></label>
                        <input type="number" name="score" min="0" max="100" step="0.5" class="form-control rounded-3 py-2 fs-5 fw-bold text-success" value="<?= esc($submission['score'] ?? '') ?>" placeholder="0.0" required>
                        <small class="text-muted">Skor akan diakumulasikan ke dalam penilaian kelulusan peserta.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-navy small mb-1">Status Penilaian <span class="text-danger">*</span></label>
                        <select name="status" class="form-select rounded-3 py-2" required>
                            <option value="graded" <?= (($submission['status'] ?? 'submitted') === 'graded') ? 'selected' : '' ?>>
                                Selesai Dinilai (Graded)
                            </option>
                            <option value="revision_required" <?= (($submission['status'] ?? '') === 'revision_required') ? 'selected' : '' ?>>
                                Perlu Revisi (Peserta Diminta Mengulang)
                            </option>
                            <option value="in_review" <?= (($submission['status'] ?? '') === 'in_review') ? 'selected' : '' ?>>
                                Masih dalam Review (In Review)
                            </option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-semibold text-navy small mb-0">Catatan Feedback & Saran Konstruktif</label>
                            <span id="wsCannedFeedbackToast" class="badge bg-success-subtle text-success small d-none" style="font-size: 0.7rem;">
                                <i class="fas fa-check me-1"></i>Preset Disisipkan!
                            </span>
                        </div>

                        <!-- Canned Feedback Presets Toolbar -->
                        <div class="mb-2 p-2 bg-light rounded-3 border">
                            <div class="d-flex align-items-center gap-1 mb-1 text-muted" style="font-size: 0.72rem;">
                                <i class="fas fa-bolt text-warning"></i>
                                <span class="fw-semibold">Preset Komentar Cepat:</span>
                            </div>
                            <div class="d-flex flex-wrap gap-1">
                                <button type="button" class="btn btn-xs btn-white border rounded-pill px-2 py-1 small ws-canned-btn" style="font-size: 0.75rem;" data-text="Struktur dokumen spesifikasi teknis dan analisis kebutuhan pengadaan telah memenuhi ketentuan Perpres PBJ No. 12/2021. Analisis risiko sangat aplikatif dan terstruktur dengan baik." data-status="graded">
                                    <i class="fas fa-check-circle text-success me-1"></i>Sesuai Standar PBJ
                                </button>
                                <button type="button" class="btn btn-xs btn-white border rounded-pill px-2 py-1 small ws-canned-btn" style="font-size: 0.75rem;" data-text="Penyusunan tugas sangat komprehensif, metodologis, dan mencerminkan pemahaman mendalam tentang tata kelola pengadaan barang/jasa pemerintah. Sangat baik!" data-status="graded">
                                    <i class="fas fa-trophy text-warning me-1"></i>Apresiasi Sempurna
                                </button>
                                <button type="button" class="btn btn-xs btn-white border rounded-pill px-2 py-1 small ws-canned-btn" style="font-size: 0.75rem;" data-text="Metode penyusunan HPS perlu perbaikan pada komponen biaya umum & keuntungan (overhead & profit), serta kelengkapan bukti survei harga pasar terkini yang dapat dipertanggungjawabkan." data-status="graded">
                                    <i class="fas fa-calculator text-primary me-1"></i>Perbaiki HPS & Biaya
                                </button>
                                <button type="button" class="btn btn-xs btn-white border rounded-pill px-2 py-1 small ws-canned-btn" style="font-size: 0.75rem;" data-text="Uraian Kerangka Acuan Kerja (KAK) sudah cukup terstruktur, namun rincian deliverable dan kriteria penerimaan hasil pekerjaan perlu diperjelas agar tidak menimbulkan sengketa kontrak." data-status="graded">
                                    <i class="fas fa-file-contract text-info me-1"></i>Perjelas KAK
                                </button>
                                <button type="button" class="btn btn-xs btn-white border rounded-pill px-2 py-1 small ws-canned-btn" style="font-size: 0.75rem;" data-text="Tugas belum memenuhi standar minimum silabus. Harap lengkapi lampiran matriks penilaian risiko dan justifikasi penetapan metode pemilihan sebelum evaluasi akhir dapat diberikan." data-status="revision_required">
                                    <i class="fas fa-redo text-danger me-1"></i>Minta Revisi Tugas
                                </button>
                            </div>
                        </div>

                        <textarea id="wsFeedbackTextarea" name="feedback" class="form-control rounded-3" rows="6" placeholder="Berikan komentar penilaian, apresiasi analisis peserta, dan saran perbaikan kompetensi..."><?= esc($submission['feedback'] ?? '') ?></textarea>
                        <small class="text-muted">Catatan feedback langsung dapat dibaca oleh peserta pada ruang tugasnya.</small>
                    </div>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const feedbackArea = document.getElementById('wsFeedbackTextarea');
                        const statusSelect = document.querySelector('select[name="status"]');
                        const toastBadge = document.getElementById('wsCannedFeedbackToast');
                        const cannedBtns = document.querySelectorAll('.ws-canned-btn');

                        cannedBtns.forEach(function(btn) {
                            btn.addEventListener('click', function() {
                                const textToInsert = this.getAttribute('data-text') || '';
                                const targetStatus = this.getAttribute('data-status');

                                if (feedbackArea) {
                                    if (feedbackArea.value.trim() === '') {
                                        feedbackArea.value = textToInsert;
                                    } else {
                                        feedbackArea.value = feedbackArea.value.trim() + "\n\n" + textToInsert;
                                    }
                                    feedbackArea.focus();
                                }

                                if (targetStatus && statusSelect) {
                                    statusSelect.value = targetStatus;
                                }

                                if (toastBadge) {
                                    toastBadge.classList.remove('d-none');
                                    setTimeout(function() {
                                        toastBadge.classList.add('d-none');
                                    }, 2000);
                                }
                            });
                        });
                    });
                    </script>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill py-2 fw-semibold">
                            <i class="fas fa-save me-1"></i> Simpan Penilaian & Feedback
                        </button>
                        <a href="<?= route_to('workspace.trainer.assignment.submissions', (string) $classId, (string) $submission['assignment_id']) ?>" class="btn btn-outline-secondary rounded-pill py-2">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
