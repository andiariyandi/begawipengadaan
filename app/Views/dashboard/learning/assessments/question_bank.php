<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$questions = $questions ?? [];
$filters = $filters ?? [];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Bank Soal Evaluasi</h6>
        <p class="text-secondary-light mb-0">Kumpulan butir pertanyaan pilihan ganda dan esai untuk modul pembelajaran.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('dashboard/pembelajaran/assessment') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            <span>Kembali ke Assessment</span>
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
        <h6 class="text-md mb-0">Daftar Pertanyaan</h6>
        <span class="badge bg-primary-50 text-primary-600 px-12 py-6 radius-8 fw-semibold text-xs">
            Total <?= count($questions) ?> Butir Soal
        </span>
    </div>

    <div class="card-body p-24">
        <!-- Search Filter -->
        <form action="<?= site_url('dashboard/pembelajaran/assessment/bank-soal') ?>" method="get" class="mb-20">
            <div class="row g-12 align-items-end">
                <div class="col-md-9">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pencarian</label>
                    <input type="text" class="form-control radius-8" name="keyword"
                           value="<?= esc($filters['keyword'] ?? '') ?>" placeholder="Cari isi teks pertanyaan atau judul quiz…">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 flex-grow-1 d-inline-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:magnifer-outline" class="text-lg"></iconify-icon>
                        <span>Cari</span>
                    </button>
                    <a href="<?= site_url('dashboard/pembelajaran/assessment/bank-soal') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-11 d-inline-flex align-items-center justify-content-center" title="Reset">
                        <iconify-icon icon="solar:restart-outline" class="text-xl"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Pertanyaan</th>
                        <th>Terkait Assessment / Course</th>
                        <th class="text-center">Tipe</th>
                        <th class="text-center">Poin</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($questions)): ?>
                        <tr>
                            <td colspan="6" class="p-0">
                                <?= view('dashboard/components/empty_state', [
                                    'title'   => 'Belum Ada Soal',
                                    'message' => 'Belum ada butir pertanyaan yang terdaftar di bank soal.',
                                    'icon'    => 'solar:question-circle-outline',
                                ]) ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($questions as $idx => $q): ?>
                            <tr>
                                <td class="text-secondary-light"><?= $idx + 1 ?></td>
                                <td>
                                    <div class="fw-medium text-primary-light text-sm">
                                        <?= esc($q['question_text'] ?? $q['prompt'] ?? '-') ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm">
                                        <div class="fw-medium text-primary-600"><?= esc($q['quiz_title'] ?? '-') ?></div>
                                        <div class="text-secondary-light text-xs"><?= esc($q['course_title'] ?? '') ?></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-neutral-100 text-secondary-light px-10 py-4 radius-4 fw-medium text-xs">
                                        <?= esc(ucwords(str_replace('_', ' ', $q['question_type'] ?? 'multiple_choice'))) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold text-sm"><?= esc((string) ($q['points'] ?? 10)) ?></span>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', ['status' => 'active']) ?>
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
