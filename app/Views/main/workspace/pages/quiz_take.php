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
$classId      = (int) ($quiz['class_id'] ?? 0);

$typeBadge = match ((string) ($quiz['type'] ?? 'lesson_quiz')) {
    'pre_test'  => ['label' => 'Pre-Test',   'class' => 'bg-warning text-dark'],
    'post_test' => ['label' => 'Post-Test',  'class' => 'bg-danger text-white'],
    default     => ['label' => 'Quiz Modul', 'class' => 'bg-primary text-white'],
};
?>

<div class="bg-white border rounded shadow-sm p-4 p-lg-5 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <a href="<?= route_to('workspace.participant.study', (string) $classId) ?>" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Learning Room
            </a>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge <?= $typeBadge['class'] ?> text-uppercase px-3 py-1">
                    <?= esc($typeBadge['label']) ?>
                </span>
                <span class="text-muted small">Percobaan: <strong><?= $attemptCount ?></strong> dari <strong><?= $attemptLimit ?></strong></span>
            </div>
            <h1 class="h3 text-navy mb-1"><?= esc($quiz['title'] ?? 'Quiz') ?></h1>
            <p class="text-muted small mb-0">Kelas: <strong class="text-dark"><?= esc($quiz['class_name'] ?? '-') ?></strong></p>
        </div>

        <?php if ($latest !== null && (string) ($latest['status'] ?? '') === 'graded'): ?>
            <div class="p-3 bg-light rounded border text-center">
                <span class="d-block text-muted small" style="font-size: 11px;">Skor Terakhir</span>
                <strong class="fs-4 <?= (float) ($latest['score'] ?? 0) >= 70 ? 'text-success' : 'text-danger' ?>">
                    <?= (float) ($latest['score'] ?? 0) ?>
                </strong>
            </div>
        <?php endif; ?>
    </div>

    <?php if (! empty($quiz['instructions'])): ?>
        <div class="p-3 bg-light rounded text-muted small border mt-3">
            <strong class="d-block text-dark mb-1"><i class="bi bi-info-circle me-1"></i> Petunjuk Pengerjaan:</strong>
            <?= nl2br(esc($quiz['instructions'])) ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($attempt !== null && (string) ($attempt['status'] ?? '') === 'started' && $questions !== []): ?>
    <!-- ACTIVE QUIZ ATTEMPT FORM -->
    <div class="bg-white border rounded shadow-sm p-4 p-lg-5 mb-4">
        <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
            <h2 class="h5 text-navy mb-0">
                <i class="bi bi-ui-checks me-1"></i> Soal Ujian (Percobaan #<?= (int) ($attempt['attempt_number'] ?? 1) ?>)
            </h2>
            <span class="badge bg-primary-subtle text-primary"><?= count($questions) ?> Pertanyaan</span>
        </div>

        <form method="post" action="<?= route_to('workspace.participant.quiz.submit', (int) $attempt['id']) ?>">
            <?= csrf_field() ?>

            <?php foreach ($questions as $index => $q): ?>
                <?php $opts = $optionsByQuestion[(int) $q['id']] ?? []; ?>
                <div class="mb-4 pb-4 border-bottom">
                    <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                        <h3 class="h6 text-dark mb-0 fw-semibold">
                            <span class="badge bg-light text-dark border me-2"><?= (int) ($index + 1) ?></span>
                            <?= esc($q['prompt']) ?>
                        </h3>
                        <span class="badge bg-secondary-subtle text-secondary flex-shrink-0">
                            <?= (float) $q['points'] ?> pts
                        </span>
                    </div>

                    <div class="options-container ps-md-4">
                        <?php foreach ($opts as $i => $opt): ?>
                            <div class="form-check p-3 border rounded mb-2 bg-light-hover transition-all">
                                <input class="form-check-input" type="radio" name="answers[<?= (int) $q['id'] ?>]" id="opt-<?= (int) $q['id'] ?>-<?= (int) $opt['id'] ?>" value="<?= (int) $opt['id'] ?>" required>
                                <label class="form-check-label w-100 cursor-pointer text-dark" for="opt-<?= (int) $q['id'] ?>-<?= (int) $opt['id'] ?>">
                                    <strong class="text-muted me-1"><?= chr(65 + (int) $i) ?>.</strong> <?= esc($opt['option_text']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="d-flex align-items-center justify-content-between pt-3">
                <a href="<?= route_to('workspace.participant.study', (string) $classId) ?>" class="btn btn-outline-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm fw-semibold">
                    <i class="bi bi-send-check me-1"></i> Kirim Jawaban
                </button>
            </div>
        </form>
    </div>

<?php else: ?>
    <!-- START ATTEMPT OR LIMIT REACHED -->
    <div class="bg-white border rounded shadow-sm p-4 p-lg-5 mb-4 text-center">
        <?php if ($questions === []): ?>
            <i class="bi bi-file-earmark-x text-muted" style="font-size: 48px;"></i>
            <h3 class="h5 text-dark mt-3 mb-2">Soal Belum Tersedia</h3>
            <p class="text-muted mb-0">Quiz ini belum memiliki daftar pertanyaan dari instruktur.</p>
        <?php elseif ($remaining <= 0): ?>
            <i class="bi bi-x-circle-fill text-danger" style="font-size: 48px;"></i>
            <h3 class="h5 text-danger mt-3 mb-2">Batas Percobaan Tercapai</h3>
            <p class="text-muted mb-4">Anda telah menggunakan seluruh batas percobaan (<?= $attemptLimit ?>x) untuk quiz ini.</p>
            <a href="<?= route_to('workspace.participant.study', (string) $classId) ?>" class="btn btn-outline-primary px-4">
                Kembali ke Ruang Belajar
            </a>
        <?php else: ?>
            <i class="bi bi-mortarboard text-primary" style="font-size: 48px;"></i>
            <h3 class="h5 text-navy mt-3 mb-2">Siap Mengerjakan <?= esc($typeBadge['label']) ?>?</h3>
            <p class="text-muted mb-4">
                Terdapat <strong><?= count($questions) ?></strong> soal. Sisa kesempatan percobaan: <strong><?= $remaining ?></strong> kali.
            </p>
            <form method="post" action="<?= route_to('workspace.participant.quiz.start', (int) $quiz['id']) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm fw-semibold">
                    <i class="bi bi-play-circle me-1"></i> Mulai Kerjakan Sekarang &raquo;
                </button>
            </form>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>