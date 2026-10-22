<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/learning/_nav_learning', ['activeSub' => 'assessments']) ?>
<?php
$quizzes = $quizzes ?? [];
$stats = $stats ?? [
    'total'          => count($quizzes),
    'totalQuestions' => 0,
    'totalAttempts'  => 0,
];
$filters = $filters ?? [];
?>

<!-- Summary Cards -->
<div class="row row-cols-xxxl-3 row-cols-md-3 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Assessment / Quiz</p>
                        <h6 class="mb-0"><?= number_format($stats['total'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:clipboard-check-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Pre-test, quiz, dan post-test</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Bank Soal Terdaftar</p>
                        <h6 class="mb-0 text-success-main"><?= number_format($stats['totalQuestions'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:question-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Pertanyaan pilihan ganda/esai</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Percobaan Pengerjaan</p>
                        <h6 class="mb-0 text-info-main"><?= number_format($stats['totalAttempts'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:history-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Riwayat submit peserta</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h6 class="text-lg mb-1">Assessment dan Evaluasi Belajar</h6>
            <p class="text-secondary-light text-sm mb-0">Kelola pre-test, kuis modul pemahaman, dan post-test kelulusan.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= site_url('dashboard/pembelajaran/assessment/bank-soal') ?>" class="btn btn-outline-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
                <iconify-icon icon="solar:library-outline" class="text-lg"></iconify-icon>
                <span>Kelola Bank Soal</span>
            </a>
        </div>
    </div>

    <div class="card-body p-24">
        <!-- Search Filter -->
        <form action="<?= site_url('dashboard/pembelajaran/assessment') ?>" method="get" class="mb-20">
            <div class="row g-12 align-items-end">
                <div class="col-md-9">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pencarian</label>
                    <input type="text" class="form-control radius-8" name="keyword"
                           value="<?= esc($filters['keyword'] ?? '') ?>" placeholder="Cari judul assessment, course, atau materi modul…">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 flex-grow-1 d-inline-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:magnifer-outline" class="text-lg"></iconify-icon>
                        <span>Cari</span>
                    </button>
                    <a href="<?= site_url('dashboard/pembelajaran/assessment') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-11 d-inline-flex align-items-center justify-content-center" title="Reset">
                        <iconify-icon icon="solar:restart-outline" class="text-xl"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Assessment</th>
                        <th>Course & Modul</th>
                        <th class="text-center">Jumlah Soal</th>
                        <th class="text-center">Ambang Lulus</th>
                        <th class="text-center">Durasi</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($quizzes)): ?>
                        <tr>
                            <td colspan="6" class="p-0">
                                <?= view('dashboard/components/empty_state', [
                                    'title'   => 'Assessment Tidak Ditemukan',
                                    'message' => 'Belum ada data assessment atau kuis evaluasi yang dibuat.',
                                    'icon'    => 'solar:clipboard-text-outline',
                                ]) ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($quizzes as $quiz): ?>
                            <tr>
                                <td>
                                    <h6 class="text-md mb-0 fw-medium"><?= esc($quiz['title']) ?></h6>
                                    <span class="text-xs text-secondary-light"><?= esc($quiz['description'] ?? '') ?></span>
                                </td>
                                <td>
                                    <div class="text-sm">
                                        <div class="fw-medium text-primary-600"><?= esc($quiz['course_title'] ?? 'Mandiri') ?></div>
                                        <div class="text-secondary-light text-xs"><?= esc($quiz['module_title'] ?? '-') ?></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-neutral-100 text-secondary-light px-10 py-4 radius-4 fw-medium text-xs">
                                        <?= (int) ($quiz['question_count'] ?? 0) ?> butir
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold text-sm"><?= esc((string) ($quiz['passing_grade'] ?? 70)) ?>%</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-sm"><?= ! empty($quiz['duration_minutes']) ? esc((string) $quiz['duration_minutes']) . ' mnt' : 'Tanpa batas' ?></span>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', ['status' => ! empty($quiz['status']) ? $quiz['status'] : 'active']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
