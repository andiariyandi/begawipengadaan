<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>
<?php
$quiz         = $quiz ?? [];
$questions    = $questions ?? [];
$optionsByQuestion = $optionsByQuestion ?? [];
$attempt      = $attempt ?? null;
$latest       = $latest ?? null;
$attemptCount = (int) ($attemptCount ?? 0);
$attemptLimit = (int) ($attemptLimit ?? 1);
$remaining    = (int) ($remaining ?? max(0, $attemptLimit - $attemptCount));
$typeLabel    = match ((string) ($quiz['type'] ?? 'lesson_quiz')) {
    'pre_test'    => 'Pre-test',
    'post_test'   => 'Post-test',
    default       => 'Quiz',
};
?>
<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2"><?= esc($typeLabel) ?></p>
    <h1 class="mb-2"><?= esc($quiz['title'] ?? 'Quiz') ?></h1>
    <p class="text-muted mb-2">Kelas: <?= esc($quiz['class_name'] ?? '-') ?></p>
    <?php if (! empty($quiz['instructions'])): ?>
        <p class="small text-muted mb-0"><?= nl2br(esc($quiz['instructions'])) ?></p>
    <?php endif; ?>
    <p class="small text-muted mt-2 mb-0">Percobaan: <?= $attemptCount ?> dari <?= $attemptLimit ?></p>
</div>

<?php if ($latest !== null && (string) ($latest['status'] ?? '') === 'graded'): ?>
    <div class="bg-white border rounded p-4 mb-4">
        <h2 class="h5 mb-2">Hasil Terakhir</h2>
        <p class="mb-1">Skor: <strong><?= (float) ($latest['score'] ?? 0) ?></strong></p>
        <p class="small text-muted mb-0">Disubmit: <?= esc($latest['submitted_at'] ?? '-') ?> &middot; Dinilai: <?= esc($latest['graded_at'] ?? '-') ?></p>
    </div>
<?php endif; ?>

<?php if ($attempt !== null && (string) ($attempt['status'] ?? '') === 'started' && $questions !== []): ?>
    <div class="bg-white border rounded p-4 mb-4">
        <h2 class="h5 mb-3">Soal Percobaan #<?= (int) ($attempt['attempt_number'] ?? 0) ?></h2>
        <form method="post" action="<?= route_to('workspace.participant.quiz.submit', (int) $attempt['id']) ?>">
            <?= csrf_field() ?>
            <?php foreach ($questions as $index => $q): ?>
                <?php $opts = $optionsByQuestion[(int) $q['id']] ?? []; ?>
                <fieldset class="mb-4">
                    <legend class="h6 mb-3"><?= (int) ($index + 1) ?>. <?= esc($q['prompt']) ?> <span class="badge bg-primary-subtle text-primary-emphasis ms-2"><?= (float) $q['points'] ?> pts</span></legend>
                    <?php foreach ($opts as $i => $opt): ?>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="answers[<?= (int) $q['id'] ?>]" id="opt-<?= (int) $q['id'] ?>-<?= (int) $opt['id'] ?>" value="<?= (int) $opt['id'] ?>" required>
                            <label class="form-check-label" for="opt-<?= (int) $q['id'] ?>-<?= (int) $opt['id'] ?>">
                                <?= chr(65 + (int) $i) ?>. <?= esc($opt['option_text']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </fieldset>
            <?php endforeach; ?>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary radius-8">Kirim Jawaban</button>
            </div>
        </form>
    </div>
<?php else: ?>
    <div class="bg-white border rounded p-4 mb-4">
        <?php if ($remaining <= 0): ?>
            <p class="mb-0 text-danger">Batas percobaan untuk quiz ini sudah tercapai.</p>
        <?php elseif ($questions === []): ?>
            <p class="mb-0 text-muted">Quiz belum memiliki soal.</p>
        <?php else: ?>
            <p class="mb-3">Klik "Mulai Quiz" untuk membuat attempt baru. Sisa percobaan: <strong><?= $remaining ?></strong>.</p>
            <form method="post" action="<?= route_to('workspace.participant.quiz.start', (int) $quiz['id']) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-primary radius-8">Mulai Quiz</button>
            </form>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>