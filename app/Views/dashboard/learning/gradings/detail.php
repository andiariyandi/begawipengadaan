<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$submission = $submission ?? [];
$files      = $files ?? [];
$gradingId  = $gradingId ?? ($submission['id'] ?? '-');
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail & Evaluasi Penilaian Tugas</h6>
        <p class="text-secondary-light mb-0">ID Submission: #<?= esc($gradingId) ?> &bull; Peserta: <?= esc($submission['student_name'] ?? '-') ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('dashboard/pembelajaran/assessment?tab=submissions') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            <span>Kembali ke Assessment</span>
        </a>
    </div>
</div>

<div class="row gy-4">
    <!-- Main Info & Submission Content -->
    <div class="col-xl-8">
        <!-- Card 1: Informasi Tugas & Submisi -->
        <div class="card border radius-12 shadow-none mb-24">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xxs text-secondary-light text-uppercase fw-semibold d-block">Judul Penugasan:</span>
                    <h6 class="text-md fw-bold mb-0 text-neutral-900"><?= esc($submission['assignment_title'] ?? 'Tugas Pembelajaran') ?></h6>
                </div>
                <?= view('dashboard/components/status_badge', ['status' => $submission['status'] ?? 'submitted']) ?>
            </div>
            <div class="card-body p-24">
                <div class="row gy-20 mb-20">
                    <div class="col-md-6">
                        <span class="text-xs text-secondary-light d-block mb-1">Peserta Pembelajaran:</span>
                        <h6 class="mb-0 text-sm fw-bold text-neutral-900"><?= esc($submission['student_name'] ?? $submission['username'] ?? '-') ?></h6>
                        <span class="text-xs text-secondary-light">@<?= esc($submission['username'] ?? '') ?> &bull; <?= esc($submission['student_email'] ?? '') ?></span>
                    </div>
                    <div class="col-md-6">
                        <span class="text-xs text-secondary-light d-block mb-1">Kelas Pembelajaran:</span>
                        <h6 class="mb-0 text-sm fw-bold text-primary-600"><?= esc($submission['class_name'] ?? '-') ?></h6>
                    </div>
                </div>

                <?php if (! empty($submission['assignment_description'])): ?>
                    <div class="mb-20 p-12 radius-8 bg-neutral-50 border">
                        <span class="text-xxs text-secondary-light text-uppercase fw-semibold d-block mb-1">Instruksi Tugas:</span>
                        <p class="text-xs text-neutral-800 mb-0"><?= nl2br(esc($submission['assignment_description'])) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Isi Jawaban Peserta -->
                <div class="mb-20">
                    <span class="text-xs text-secondary-light text-uppercase fw-semibold d-block mb-2">Jawaban / Catatan Peserta:</span>
                    <div class="p-16 radius-8 bg-neutral-25 border text-sm text-neutral-900 fw-medium">
                        <?php if (! empty($submission['content'])): ?>
                            <?= nl2br(esc($submission['content'])) ?>
                        <?php else: ?>
                            <em class="text-secondary-light">Tidak ada catatan teks tertulis yang dikirimkan.</em>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Berkas Lampiran Pendukung -->
                <div>
                    <span class="text-xs text-secondary-light text-uppercase fw-semibold d-block mb-2">Berkas Lampiran Jawaban (<?= count($files) ?> File):</span>
                    <?php if (empty($files)): ?>
                        <div class="p-12 radius-8 bg-white border text-xs text-secondary-light italic">
                            Tidak ada berkas file yang diunggah pada submisi ini.
                        </div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach ($files as $f): ?>
                                <div class="p-12 radius-8 bg-white border d-flex align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-10">
                                        <div class="w-36-px h-36-px rounded-circle bg-primary-50 text-primary-600 d-flex justify-content-center align-items-center">
                                            <iconify-icon icon="solar:document-outline" class="text-lg"></iconify-icon>
                                        </div>
                                        <div>
                                            <h6 class="text-sm fw-semibold mb-0 text-neutral-900"><?= esc($f['original_name'] ?? 'Berkas Tugas') ?></h6>
                                            <span class="text-xxs text-secondary-light">
                                                <?= number_format(((int) ($f['size_bytes'] ?? 0)) / 1024, 1) ?> KB &bull; <?= esc($f['mime_type'] ?? '') ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php if (! empty($f['path'])): ?>
                                        <a href="<?= base_url($f['path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary-600 radius-6 px-12 py-6 text-xs d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:download-outline"></iconify-icon>
                                            <span>Unduh File</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Penilai (Operator / Trainer) & Grading Form Sidebar -->
    <div class="col-xl-4">
        <!-- Form Penilaian Langsung -->
        <div class="card border radius-12 shadow-none mb-24">
            <div class="card-header border-bottom bg-base py-16 px-24">
                <h6 class="text-md fw-semibold mb-0 text-neutral-900">Form Evaluasi & Penilaian</h6>
            </div>
            <div class="card-body p-24">
                <form action="<?= route_to('dashboard.learning.gradings.grade', $submission['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="inp_score">Skor Nilai (0 - 100) <span class="text-danger-main">*</span></label>
                        <input id="inp_score" type="number" step="0.1" min="0" max="100" name="score"
                               class="form-control radius-8 font-monospace fw-bold text-lg"
                               value="<?= $submission['score'] !== null ? esc((string) $submission['score']) : '' ?>"
                               required placeholder="Contoh: 85.0">
                        <span class="text-xxs text-secondary-light">Nilai akan memperbarui kalkulasi kelulusan peserta.</span>
                    </div>

                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="inp_status">Status Hasil Evaluasi</label>
                        <select id="inp_status" name="status" class="form-select radius-8">
                            <option value="graded" <?= ($submission['status'] ?? '') === 'graded' ? 'selected' : '' ?>>Graded (Tuntas & Selesai Dinilai)</option>
                            <option value="revision_required" <?= in_array(($submission['status'] ?? ''), ['revision_required', 'revision'], true) ? 'selected' : '' ?>>Perlu Revisi (Belum Memenuhi Syarat)</option>
                            <option value="in_review" <?= ($submission['status'] ?? '') === 'in_review' ? 'selected' : '' ?>>Sedang Ditinjau (In Review)</option>
                        </select>
                    </div>

                    <div class="mb-20">
                        <label class="form-label fw-semibold text-sm mb-6" for="inp_feedback">Catatan Evaluasi / Feedback</label>
                        <textarea id="inp_feedback" name="feedback" class="form-control radius-8" rows="4" placeholder="Tuliskan apresiasi, koreksi analisis, atau arahan revisi bagi peserta..."><?= esc($submission['feedback'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary-600 radius-8 w-100 py-11 d-inline-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:check-circle-bold" class="text-lg"></iconify-icon>
                        <span>Simpan Penilaian</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Meta History Info -->
        <div class="card border radius-12 shadow-none">
            <div class="card-header border-bottom bg-base py-16 px-24">
                <h6 class="text-md fw-semibold mb-0 text-neutral-900">Riwayat Penilaian</h6>
            </div>
            <div class="card-body p-24">
                <div class="mb-16">
                    <span class="text-xs text-secondary-light d-block mb-1">Penilai (Operator / Trainer):</span>
                    <h6 class="text-sm fw-bold mb-0 text-neutral-900"><?= esc($submission['evaluator_name'] ?? 'Belum Dinilai') ?></h6>
                </div>

                <div class="mb-16">
                    <span class="text-xs text-secondary-light d-block mb-1">Waktu Pengumpulan:</span>
                    <h6 class="text-sm fw-medium mb-0">
                        <?= ! empty($submission['submitted_at']) ? esc(date('d M Y, H:i', strtotime($submission['submitted_at']))) : (! empty($submission['created_at']) ? esc(date('d M Y, H:i', strtotime($submission['created_at']))) : '-') ?>
                    </h6>
                </div>

                <div>
                    <span class="text-xs text-secondary-light d-block mb-1">Terakhir Dinilai:</span>
                    <h6 class="text-sm fw-medium mb-0">
                        <?= ! empty($submission['graded_at']) ? esc(date('d M Y, H:i', strtotime($submission['graded_at']))) : 'Belum Pernah' ?>
                    </h6>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
