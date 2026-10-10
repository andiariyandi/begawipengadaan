<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$submission = $submission ?? [];
$gradingId = $gradingId ?? ($submission['id'] ?? '-');
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Penilaian Tugas</h6>
        <p class="text-secondary-light mb-0">ID Submission: #<?= esc($gradingId) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('dashboard/pembelajaran/penilaian') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            <span>Kembali</span>
        </a>
    </div>
</div>

<div class="row gy-4">
    <!-- Main Info -->
    <div class="col-xl-8">
        <div class="card mb-24">
            <div class="card-header border-bottom bg-base py-16 px-24">
                <h6 class="text-md mb-0"><?= esc($submission['assignment_title'] ?? 'Tugas Pembelajaran') ?></h6>
            </div>
            <div class="card-body p-24">
                <div class="row gy-20">
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Nama Peserta</p>
                        <h6 class="mb-0 text-primary-600"><?= esc($submission['student_name'] ?? $submission['username'] ?? '-') ?></h6>
                        <span class="text-xs text-secondary-light"><?= esc($submission['student_email'] ?? '') ?></span>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Kelas Pembelajaran</p>
                        <h6 class="mb-0"><?= esc($submission['class_name'] ?? '-') ?></h6>
                    </div>
                    <div class="col-md-4">
                        <p class="text-secondary-light mb-4">Nilai Diperoleh</p>
                        <h5 class="mb-0 text-success-main font-monospace">
                            <?= $submission['score'] !== null ? esc((string) $submission['score']) : '—' ?>
                            <span class="text-sm fw-normal text-secondary-light">/ <?= esc((string) ($submission['max_score'] ?? 100)) ?></span>
                        </h5>
                    </div>
                    <div class="col-md-4">
                        <p class="text-secondary-light mb-4">Passing Grade</p>
                        <h6 class="mb-0"><?= esc((string) ($submission['passing_score'] ?? 70)) ?></h6>
                    </div>
                    <div class="col-md-4">
                        <p class="text-secondary-light mb-4">Status Penilaian</p>
                        <?= view('dashboard/components/status_badge', ['status' => $submission['status'] ?? 'submitted']) ?>
                    </div>
                    <div class="col-12 border-top pt-16">
                        <p class="text-secondary-light mb-4">Catatan / Feedback Evaluator</p>
                        <div class="p-16 bg-neutral-50 radius-8 border">
                            <p class="mb-0 text-secondary-light">
                                <?= ! empty($submission['feedback']) ? nl2br(esc($submission['feedback'])) : '<em>Belum ada catatan umpan balik dari evaluator.</em>' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Evaluator & Meta Sidebar -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header border-bottom bg-base py-16 px-24">
                <h6 class="text-md mb-0">Informasi Evaluator</h6>
            </div>
            <div class="card-body p-24">
                <p class="text-secondary-light mb-4">Penilai</p>
                <h6 class="text-md mb-16"><?= esc($submission['evaluator_name'] ?? 'Belum Ditugaskan') ?></h6>

                <p class="text-secondary-light mb-4">Tanggal Pengumpulan</p>
                <h6 class="text-sm mb-16"><?= ! empty($submission['created_at']) ? esc(date('d M Y H:i', strtotime($submission['created_at']))) : '-' ?></h6>

                <p class="text-secondary-light mb-4">Tanggal Penilaian</p>
                <h6 class="text-sm mb-0"><?= ! empty($submission['graded_at']) ? esc(date('d M Y H:i', strtotime($submission['graded_at']))) : '-' ?></h6>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
