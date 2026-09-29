<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="card p-24 radius-12">
    <h6 class="mb-16"><?= esc($assessment['title']) ?></h6>

    <?php if (empty($questions)): ?>
        <p class="text-secondary-light">Quiz ini belum memiliki soal.</p>
    <?php else: ?>
        <form action="<?= route_to('learning.study.quiz.submit', $enrollmentId, $assessment['id']) ?>" method="post">
            <?= csrf_field() ?>

            <?php foreach ($questions as $number => $question): ?>
                <div class="border-bottom py-16">
                    <p class="fw-medium mb-12"><?= esc($number + 1) ?>. <?= esc($question['question_text']) ?></p>

                    <?php if ($question['question_type'] === 'multiple_choice'): ?>
                        <?php foreach ($question['answers'] as $answer): ?>
                            <div class="form-check mb-8">
                                <input class="form-check-input" type="radio"
                                    name="answers[<?= esc($question['id']) ?>]"
                                    id="answer-<?= esc($answer['id']) ?>"
                                    value="<?= esc($answer['id']) ?>">
                                <label class="form-check-label" for="answer-<?= esc($answer['id']) ?>">
                                    <?= esc($answer['answer_text']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-secondary-light text-sm mb-0">Soal essay tidak ikut dinilai otomatis pada tahap ini.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary px-24 py-12 radius-8 mt-16">Kumpulkan Jawaban</button>
        </form>
    <?php endif; ?>
</div>

<?= $this->endSection(); ?>