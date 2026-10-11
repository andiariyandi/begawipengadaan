<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$questions = $questions ?? [];
$classes   = $classes ?? [];
$filters   = $filters ?? [];
$optLetters = ['A', 'B', 'C', 'D', 'E'];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Bank Soal Evaluasi Pembelajaran') ?></h6>
        <p class="text-secondary-light mb-0">Bank data butir pertanyaan pilihan ganda, kunci jawaban, dan pembahasan materi pembelajaran.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('dashboard/pembelajaran/kurikulum') ?>" class="btn btn-outline-info radius-8 px-16 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:structure-outline" class="text-lg"></iconify-icon>
            <span>Roadmap Kurikulum</span>
        </a>
        <a href="<?= site_url('dashboard/pembelajaran/assessment') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            <span>Kembali ke Assessment</span>
        </a>
    </div>
</div>

<div class="card border radius-12 shadow-none">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <iconify-icon icon="solar:checklist-minimalistic-outline" class="text-xl text-primary-600"></iconify-icon>
            <h6 class="text-md mb-0">Daftar Butir Soal Terdaftar</h6>
        </div>
        <span class="badge bg-primary-50 text-primary-700 px-12 py-6 radius-8 fw-semibold text-xs">
            Total <?= count($questions) ?> Butir Ditampilkan
        </span>
    </div>

    <div class="card-body p-24">
        <!-- Search & Filter Form -->
        <form action="<?= site_url('dashboard/pembelajaran/assessment/bank-soal') ?>" method="get" class="mb-20">
            <div class="row g-12 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="filter_class">Filter Kelas</label>
                    <select id="filter_class" name="class_id" class="form-select radius-8">
                        <option value="">Semua Kelas Pembelajaran</option>
                        <?php foreach ($classes as $cls): ?>
                            <option value="<?= esc($cls['id']) ?>" <?= ((string) ($filters['class_id'] ?? '') === (string) $cls['id']) ? 'selected' : '' ?>>
                                <?= esc($cls['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="filter_keyword">Pencarian Teks Soal / Kuis</label>
                    <input id="filter_keyword" type="text" class="form-control radius-8" name="keyword"
                           value="<?= esc($filters['keyword'] ?? '') ?>" placeholder="Ketik kata kunci pertanyaan, judul kuis, atau nama kelas...">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-10 flex-grow-1 d-inline-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:magnifer-outline" class="text-lg"></iconify-icon>
                        <span>Filter</span>
                    </button>
                    <?php if (! empty($filters['keyword']) || ! empty($filters['class_id'])): ?>
                        <a href="<?= site_url('dashboard/pembelajaran/assessment/bank-soal') ?>" class="btn btn-outline-neutral-300 radius-8 px-14 py-10 d-inline-flex align-items-center justify-content-center" title="Reset Filter">
                            <iconify-icon icon="solar:restart-outline" class="text-lg"></iconify-icon>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <?php if (empty($questions)): ?>
            <div class="text-center py-32 text-secondary-light">
                <iconify-icon icon="solar:question-circle-outline" class="text-4xl mb-2 text-primary-main"></iconify-icon>
                <p class="mb-0 text-sm">Tidak ada butir soal yang sesuai dengan kriteria pencarian.</p>
            </div>
        <?php else: ?>
            <div class="accordion" id="accordionBankSoalFull">
                <?php foreach ($questions as $qIdx => $q): ?>
                    <?php
                    $collapseId = 'collapseQ_' . $q['id'];
                    $headingId  = 'headingQ_' . $q['id'];
                    $opts       = $q['options'] ?? [];
                    $correctLetter = null;
                    foreach ($opts as $oi => $o) {
                        if (! empty($o['is_correct'])) {
                            $correctLetter = $optLetters[$oi] ?? (string) ($oi + 1);
                            break;
                        }
                    }
                    ?>
                    <div class="accordion-item border radius-8 mb-10 overflow-hidden">
                        <h2 class="accordion-header" id="<?= $headingId ?>">
                            <div class="d-flex align-items-center justify-content-between p-14 bg-white gap-2 flex-wrap">
                                <button class="accordion-button collapsed flex-grow-1 p-0 bg-transparent shadow-none text-neutral-900 border-0"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>"
                                        aria-expanded="false" aria-controls="<?= $collapseId ?>">
                                    <div class="d-flex align-items-center gap-10 flex-grow-1 me-12">
                                        <span class="badge bg-primary-50 text-primary-700 fw-bold font-monospace radius-4 px-8 py-4 text-xs">
                                            #<?= $q['id'] ?>
                                        </span>
                                        <span class="text-xs fw-semibold text-neutral-800 text-truncate" style="max-width: 540px;" title="<?= esc($q['prompt']) ?>">
                                            <?= esc($q['prompt']) ?>
                                        </span>
                                    </div>
                                </button>

                                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                    <span class="badge bg-neutral-100 text-neutral-800 text-xxs radius-4 font-monospace">
                                        <?= number_format((float) $q['points'], 1) ?> Poin
                                    </span>
                                    <span class="badge bg-info-50 text-info-700 text-xxs radius-4">
                                        <?= esc($q['quiz_title']) ?>
                                    </span>
                                    <?php if (! empty($q['class_name'])): ?>
                                        <span class="badge bg-neutral-50 text-secondary-light text-xxs radius-4 border">
                                            <?= esc($q['class_name']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($correctLetter !== null): ?>
                                        <span class="badge bg-success-50 text-success-700 text-xxs radius-4 fw-bold">
                                            Kunci: <?= $correctLetter ?>
                                        </span>
                                    <?php endif; ?>

                                    <?php if (! empty($q['class_id'])): ?>
                                        <a href="<?= site_url('dashboard/pembelajaran/kurikulum/kelas/' . $q['class_id']) ?>" class="btn btn-xs btn-outline-primary-600 radius-4 px-8 py-4" title="Kelola di Kurikulum">
                                            <iconify-icon icon="solar:arrow-right-up-outline"></iconify-icon>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </h2>

                        <div id="<?= $collapseId ?>" class="accordion-collapse collapse" aria-labelledby="<?= $headingId ?>" data-bs-parent="#accordionBankSoalFull">
                            <div class="accordion-body p-16 pt-0 border-top bg-neutral-25">
                                <div class="mb-12 pt-12">
                                    <span class="text-xxs text-secondary-light d-block text-uppercase fw-semibold mb-1">Pertanyaan Soal Lengkap:</span>
                                    <div class="p-12 radius-6 bg-white border text-sm text-neutral-900 fw-medium">
                                        <?= nl2br(esc($q['prompt'])) ?>
                                    </div>
                                </div>

                                <span class="text-xxs text-secondary-light d-block text-uppercase fw-semibold mb-6">Pilihan Jawaban (A-D):</span>
                                <div class="row g-8">
                                    <?php if (empty($opts)): ?>
                                        <div class="col-12">
                                            <span class="text-xs text-danger-main italic">Belum ada pilihan jawaban untuk soal ini.</span>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($opts as $oIdx => $opt): ?>
                                            <?php
                                            $letter = $optLetters[$oIdx] ?? (string) ($oIdx + 1);
                                            $isCorrect = ! empty($opt['is_correct']);
                                            ?>
                                            <div class="col-md-6">
                                                <div class="p-10 radius-6 border <?= $isCorrect ? 'bg-success-50 border-success-subtle text-success-900' : 'bg-white border-neutral-200 text-neutral-800' ?> d-flex align-items-center justify-content-between gap-2">
                                                    <div class="d-flex align-items-center gap-8">
                                                        <span class="w-24-px h-24-px rounded-circle <?= $isCorrect ? 'bg-success-600 text-white' : 'bg-neutral-100 text-neutral-700' ?> fw-bold text-xs d-flex justify-content-center align-items-center flex-shrink-0">
                                                            <?= $letter ?>
                                                        </span>
                                                        <span class="text-xs <?= $isCorrect ? 'fw-bold' : '' ?>">
                                                            <?= esc($opt['option_text']) ?>
                                                        </span>
                                                    </div>
                                                    <?php if ($isCorrect): ?>
                                                        <span class="badge bg-success-600 text-white text-xxs radius-4 flex-shrink-0 d-inline-flex align-items-center gap-1">
                                                            <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                                                            Kunci Benar
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                                <?php if (! empty($q['explanation'])): ?>
                                    <div class="mt-12 p-10 radius-6 bg-info-50 border border-info-200 text-info-900 text-xs d-flex align-items-start gap-2">
                                        <iconify-icon icon="solar:info-circle-bold" class="text-base text-info-main mt-1 flex-shrink-0"></iconify-icon>
                                        <div>
                                            <strong class="d-block mb-1 text-info-700">Pembahasan / Penjelasan Jawaban:</strong>
                                            <span><?= nl2br(esc($q['explanation'])) ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($pager)): ?>
            <div class="mt-20 pt-16 border-top">
                <?= $pager->links('question_bank', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
