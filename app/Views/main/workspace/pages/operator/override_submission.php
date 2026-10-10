<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>

<div class="operator-override-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-shield-alt me-1"></i> Operator Evaluation Override
                </span>
                <h1 class="h3 fw-bold text-navy mb-1">Override Penilaian: <?= esc($submission['participant_name'] ?? 'Peserta') ?></h1>
                <p class="text-muted small mb-0">
                    Tugas: <strong><?= esc($submission['assignment_title'] ?? '-') ?></strong> &middot; Kelas: <strong><?= esc($submission['class_name'] ?? '-') ?></strong>
                </p>
            </div>
            <div>
                <a href="<?= route_to('workspace.operator.evaluations') ?>" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Antrean Evaluasi
                </a>
            </div>
        </div>
    </div>

    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= esc(session()->getFlashdata('message')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Kolom Kiri: Jawaban Peserta & Riwayat Penilaian Awal -->
        <div class="col-12 col-lg-7">
            <!-- Instruksi Tugas Ringkas -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <h2 class="h6 fw-bold text-navy mb-2"><i class="fas fa-info-circle text-primary me-2"></i>Instruksi Tugas</h2>
                <div class="text-muted small lh-base bg-light rounded-3 p-3">
                    <?= nl2br(esc($submission['assignment_instructions'] ?? 'Tidak ada instruksi khusus.')) ?>
                </div>
            </div>

            <!-- Jawaban Peserta -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 fw-bold text-navy mb-0"><i class="fas fa-file-alt text-primary me-2"></i>Jawaban Peserta</h2>
                    <span class="badge bg-light text-muted border rounded-pill px-2 py-1 small">
                        Percobaan ke-<?= (int) ($submission['attempt_number'] ?? 1) ?>
                    </span>
                </div>

                <div class="p-3 bg-light rounded-3 mb-4">
                    <small class="text-muted d-block mb-2">Teks Jawaban / Laporan:</small>
                    <?php if (! empty($submission['content'])): ?>
                        <div class="text-navy lh-lg small">
                            <?= nl2br(esc($submission['content'])) ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small fst-italic mb-0">Peserta tidak menyertakan teks uraian tambahan.</p>
                    <?php endif; ?>
                </div>

                <!-- Berkas Lampiran -->
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
                                    Tersimpan
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Penilaian Awal oleh Trainer -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h2 class="h6 fw-bold text-navy mb-3"><i class="fas fa-user-tie text-info me-2"></i>Riwayat Penilaian Trainer Sebelumnya</h2>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Penilai (Trainer):</small>
                        <strong class="text-navy"><?= esc($submission['grader_name'] ?? 'Belum Dinilai / Sistem') ?></strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Waktu Penilaian:</small>
                        <span class="text-dark"><?= ! empty($submission['graded_at']) ? esc(date('d/m/Y H:i', strtotime($submission['graded_at']))) : '-' ?></span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Nilai Terdaftar:</small>
                        <strong class="fs-5 <?= $submission['score'] !== null ? 'text-success' : 'text-muted' ?>">
                            <?= $submission['score'] !== null ? esc($submission['score']) : '-' ?> / 100
                        </strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Status Evaluasi:</small>
                        <span class="badge bg-light text-dark border"><?= esc($submission['status'] ?? 'submitted') ?></span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block mb-1">Catatan Feedback Sebelumnya:</small>
                        <div class="p-3 bg-light rounded-3 text-muted small">
                            <?= ! empty($submission['feedback']) ? nl2br(esc($submission['feedback'])) : '<span class="fst-italic">Tidak ada catatan feedback.</span>' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Form Override Operator -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 sticky-top" style="top: 2rem;">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-circle bg-warning text-dark p-2 rounded-circle" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h2 class="h5 fw-bold text-navy mb-0">Form Override Evaluasi</h2>
                </div>

                <div class="alert alert-warning-subtle text-dark border-0 rounded-3 p-3 mb-3 small">
                    <i class="fas fa-exclamation-circle text-warning me-1"></i>
                    <strong>Kewenangan Operator/Admin:</strong> Nilai atau status yang di-override di sini akan menjadi acuan final dan memicu rekalkulasi kelulusan peserta otomatis.
                </div>

                <form method="POST" action="<?= route_to('workspace.operator.submission.override', (string) $submission['id']) ?>">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-navy small mb-1">Skor / Nilai Override (0 - 100) <span class="text-danger">*</span></label>
                        <input type="number" name="score" min="0" max="100" step="0.5" class="form-control rounded-3 py-2 fs-5 fw-bold text-primary" value="<?= esc($submission['score'] ?? '') ?>" placeholder="0.0" required>
                        <small class="text-muted">Masukkan nilai penyesuaian sesuai kewenangan operasional.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-navy small mb-1">Status Hasil Override <span class="text-danger">*</span></label>
                        <select name="status" class="form-select rounded-3 py-2" required>
                            <option value="graded" <?= (($submission['status'] ?? '') === 'graded') ? 'selected' : '' ?>>
                                Selesai Dinilai (Graded)
                            </option>
                            <option value="revision_required" <?= (($submission['status'] ?? '') === 'revision_required') ? 'selected' : '' ?>>
                                Perlu Revisi (Revision Required)
                            </option>
                            <option value="in_review" <?= (($submission['status'] ?? '') === 'in_review') ? 'selected' : '' ?>>
                                Ditinjau Ulang (In Review)
                            </option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-navy small mb-1">Alasan / Catatan Override Operator <span class="text-danger">*</span></label>
                        <textarea name="override_reason" class="form-control rounded-3" rows="5" placeholder="Contoh: Disetujui penyesuaian nilai berdasarkan dispensasi tugas atau peninjauan ulang berkas dokumen..." required></textarea>
                        <small class="text-muted">Catatan override akan dicatat pada audit log dan dilampirkan ke feedback peserta.</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning rounded-pill py-2 fw-bold text-dark shadow-sm">
                            <i class="fas fa-check me-1"></i> Terapkan Hak Override
                        </button>
                        <a href="<?= route_to('workspace.operator.evaluations') ?>" class="btn btn-outline-secondary rounded-pill py-2">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
