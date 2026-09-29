<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="mb-24">
    <h6 class="mb-4">Kelola Soal — <?= esc($assessment['title'] ?? '-') ?></h6>
    <p class="text-secondary-light text-sm mb-0">
        Passing score: <?= esc($assessment['passing_score'] ?? '-') ?> &middot; Tipe: <?= esc(ucfirst($assessment['type'] ?? '-')) ?>
    </p>
</div>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success radius-12 mb-16"><?= esc(session()->getFlashdata('message')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card p-24 radius-12 mb-24">
    <h6 class="mb-16">Tambah Soal</h6>
    <form action="<?= route_to('learning.assessments.questions.store', $assessment['id']) ?>" method="post" class="row g-3 align-items-end">
        <?= csrf_field() ?>
        <div class="col-md-7">
            <label class="form-label">Teks Soal</label>
            <textarea name="question_text" class="form-control" rows="2"></textarea>
        </div>
        <div class="col-md-3">
            <label class="form-label">Tipe Soal</label>
            <select name="question_type" class="form-select">
                <option value="multiple_choice">Pilihan Ganda (auto-grading)</option>
                <option value="essay">Essay (belum ada penilaian otomatis)</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary px-24 py-12 radius-8 w-100">Tambah</button>
        </div>
    </form>
</div>

<?php if (empty($questions)): ?>
    <p class="text-secondary-light">Belum ada soal untuk assessment ini.</p>
<?php else: ?>
    <?php foreach ($questions as $index => $question): ?>
        <div class="card p-24 radius-12 mb-24">
            <h6 class="mb-8"><?= esc($index + 1) ?>. <?= esc($question['question_text']) ?></h6>
            <span class="bg-neutral-200 text-neutral-600 border border-neutral-400 px-16 py-4 radius-4 text-sm mb-16 d-inline-block">
                <?= esc(ucfirst($question['question_type'])) ?>
            </span>

            <?php if ($question['question_type'] === 'multiple_choice'): ?>
                <?php if (! empty($question['answers'])): ?>
                    <ul class="list-unstyled mb-16">
                        <?php foreach ($question['answers'] as $answer): ?>
                            <li class="mb-4">
                                <?php if ($answer['is_correct']): ?>
                                    <span class="text-success-600"><iconify-icon icon="mdi:check-circle-outline"></iconify-icon></span>
                                <?php else: ?>
                                    <span class="text-secondary-light"><iconify-icon icon="mdi:circle-outline"></iconify-icon></span>
                                <?php endif; ?>
                                <?= esc($answer['answer_text']) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-secondary-light text-sm">Belum ada opsi jawaban.</p>
                <?php endif; ?>

                <form action="<?= route_to('learning.assessments.answers.store', $assessment['id'], $question['id']) ?>" method="post" class="row g-3 align-items-end">
                    <?= csrf_field() ?>
                    <div class="col-md-7">
                        <label class="form-label">Opsi Jawaban Baru</label>
                        <input type="text" name="answer_text" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mt-32">
                            <input type="checkbox" name="is_correct" value="1" class="form-check-input" id="correct-<?= esc($question['id']) ?>">
                            <label class="form-check-label" for="correct-<?= esc($question['id']) ?>">Jawaban benar</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary px-16 py-8 radius-8 w-100">Tambah</button>
                    </div>
                </form>
            <?php else: ?>
                <p class="text-secondary-light text-sm mb-0">Soal essay dikirim peserta dan dinilai melalui antrean evaluator.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection(); ?>