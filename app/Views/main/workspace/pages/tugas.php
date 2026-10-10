<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?= view('dashboard/components/flash_message') ?>

<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2"><?= esc($sectionContent['eyebrow'] ?? 'Aktivitas Belajar') ?></p>
    <h1 class="mb-3"><?= esc($pageTitle ?? 'Tugas Pembelajaran') ?></h1>
    <p class="mb-0"><?= esc($sectionContent['description'] ?? 'Kelola tugas aktif, pengumpulan, revisi, dan riwayat tugas pembelajaran.') ?></p>
</div>

<?php if (!empty($activeAssignment)): ?>
    <!-- Assignment Submission Form -->
    <div class="bg-white border rounded p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 mb-0"><?= esc($activeAssignment['title'] ?? 'Pengumpulan Tugas') ?></h2>
            <a href="<?= site_url('workspace/peserta/kelas/' . esc($activeAssignment['class_id']) . '/tugas') ?>" class="btn btn-outline-secondary btn-sm">Kembali ke Daftar Tugas</a>
        </div>
        <div class="p-3 bg-light rounded mb-4">
            <h6 class="text-muted small text-uppercase mb-2">Instruksi Tugas:</h6>
            <div class="mb-2"><?= nl2br(esc($activeAssignment['instructions'] ?? 'Tidak ada instruksi khusus.')) ?></div>
            <small class="text-muted">Batas Waktu: <strong><?= esc($activeAssignment['due_at'] ?? 'Tidak ditentukan') ?></strong> | Maks. Percobaan: <strong><?= esc($activeAssignment['max_attempts'] ?? 1) ?> kali</strong></small>
        </div>

        <?php if (!empty($latestSubmission)): ?>
            <div class="alert <?= ($latestSubmission['status'] ?? '') === 'graded' ? 'alert-success' : (($latestSubmission['status'] ?? '') === 'revision_required' ? 'alert-warning' : 'alert-info') ?> mb-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="alert-heading mb-1">Status Pengumpulan Terakhir: <span class="badge bg-dark"><?= esc($latestSubmission['status'] ?? 'submitted') ?></span></h6>
                        <small>Dikumpulkan pada: <?= esc($latestSubmission['submitted_at'] ?? '-') ?></small>
                        <?php if ($latestSubmission['score'] !== null): ?>
                            <div class="mt-2">Nilai Pengajar: <strong class="fs-5"><?= esc($latestSubmission['score']) ?></strong> / 100</div>
                        <?php endif; ?>
                        <?php if (!empty($latestSubmission['feedback'])): ?>
                            <div class="mt-2 p-2 bg-white rounded border">
                                <strong>Catatan / Feedback Pengajar:</strong>
                                <p class="mb-0 mt-1"><?= nl2br(esc($latestSubmission['feedback'])) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php
        $canSubmit = empty($latestSubmission) || ($latestSubmission['status'] ?? '') === 'revision_required' || (int) ($latestSubmission['attempt_number'] ?? 0) < (int) ($activeAssignment['max_attempts'] ?? 1);
        ?>

        <?php if ($canSubmit): ?>
            <h5 class="mb-3"><?= !empty($latestSubmission) ? 'Kirim Revisi Tugas' : 'Form Pengumpulan Tugas' ?></h5>
            <form method="post" action="<?= site_url('workspace/peserta/kelas/' . esc($activeAssignment['class_id']) . '/tugas/kirim') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="assignment_id" value="<?= esc($activeAssignment['id']) ?>">
                
                <div class="mb-3">
                    <label class="form-label fw-semibold">Jawaban / Laporan Tugas</label>
                    <textarea name="content" class="form-control" rows="6" placeholder="Ketik ringkasan, uraian hasil kerja, atau tautan berkas pendukung di sini..." required><?= esc($latestSubmission['content'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Unggah Berkas Pendukung (Opsional)</label>
                    <input type="file" name="submission_file" class="form-control">
                    <small class="text-muted">Format yang didukung: PDF, DOCX, XLSX, ZIP. Maks 10MB.</small>
                </div>
                <button type="submit" class="btn btn-primary px-4 py-2">Kirim Tugas Sekarang</button>
            </form>
        <?php else: ?>
            <div class="text-muted">Tugas ini telah dinilai dan kuota pengumpulan telah terpenuhi.</div>
        <?php endif; ?>
    </div>
<?php elseif (!empty($assignmentsList)): ?>
    <!-- Assignments Grid / Cards -->
    <div class="row g-4 mb-4">
        <?php foreach ($assignmentsList as $asg): ?>
            <div class="col-md-6 col-lg-4">
                <div class="bg-white border rounded p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge <?= ($asg['submission_status'] ?? '') === 'graded' ? 'bg-success' : (($asg['submission_status'] ?? '') === 'submitted' ? 'bg-primary' : (($asg['submission_status'] ?? '') === 'revision_required' ? 'bg-warning text-dark' : 'bg-secondary')) ?>">
                                <?= esc($asg['submission_status_label'] ?? 'Belum Dikumpulkan') ?>
                            </span>
                            <?php if ($asg['score'] !== null): ?>
                                <strong class="text-success fs-5"><?= esc($asg['score']) ?></strong>
                            <?php endif; ?>
                        </div>
                        <h2 class="h5 mb-2"><?= esc($asg['title'] ?? 'Tugas') ?></h2>
                        <p class="text-muted small mb-3"><?= esc(mb_strimwidth($asg['instructions'] ?? '', 0, 120, '...')) ?></p>
                        <small class="text-muted d-block mb-3">Batas Waktu: <?= esc($asg['due_at'] ?? 'Tidak ada') ?></small>
                    </div>
                    <div>
                        <a href="<?= site_url('workspace/peserta/kelas/' . esc($asg['class_id']) . '/tugas/kirim?assignment_id=' . esc($asg['id'])) ?>" class="btn btn-outline-primary btn-sm w-100">
                            <?= ($asg['submission_status'] ?? '') === 'submitted' ? 'Lihat Pengumpulan' : (($asg['submission_status'] ?? '') === 'revision_required' ? 'Kirim Revisi' : 'Kerjakan Tugas') ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php elseif (!empty($sectionContent['items'])): ?>
    <div class="row g-4">
        <?php foreach ($sectionContent['items'] as $index => $item): ?>
            <div class="col-md-4">
                <div class="bg-white border rounded p-4 h-100">
                    <span class="badge <?= $index === 0 ? 'bg-warning-subtle text-warning' : 'bg-light text-success' ?> mb-3"><?= $index === 0 ? 'Aktif' : 'Terjadwal' ?></span>
                    <h2 class="h5"><?= esc(is_array($item) ? ($item['title'] ?? '') : $item) ?></h2>
                    <p class="text-muted small">Detail tugas dan status pengumpulan.</p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="bg-white border rounded p-4 text-center text-muted">Belum ada tugas yang tersedia untuk kelas ini.</div>
<?php endif; ?>

<?= $this->endSection() ?>
