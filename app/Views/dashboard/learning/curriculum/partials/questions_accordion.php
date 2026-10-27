<?php
/**
 * View Partial: questions_accordion.php
 * Renders accordion / list of multiple-choice questions for Quizzes (Pre-Test, Post-Test, and Module Quizzes).
 *
 * @var array  $quiz
 * @var string $accordionId
 * @var int    $selectedClassId
 */
$questions = $quiz['questions'] ?? [];
$optionLetters = ['A', 'B', 'C', 'D', 'E', 'F'];
?>

<?php if (empty($questions)): ?>
    <div class="text-center py-20 px-16 border border-dashed radius-8 bg-neutral-25">
        <iconify-icon icon="solar:question-circle-outline" class="text-3xl text-secondary-light mb-2"></iconify-icon>
        <p class="text-xs text-secondary-light mb-2">Belum ada butir soal pilihan ganda pada evaluasi ini.</p>
        <button type="button" class="btn btn-xs btn-outline-primary-600 radius-6 px-12 py-5 text-xs d-inline-flex align-items-center gap-1"
                onclick="openAddQuestionModal(<?= (int) $quiz['id'] ?>, '<?= esc(addslashes($quiz['title'])) ?>')">
            <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
            <span>+ Tambah Butir Soal</span>
        </button>
    </div>
<?php else: ?>
    <div class="accordion curriculum-questions-accordion" id="<?= esc($accordionId) ?>">
        <?php foreach ($questions as $qIdx => $q): ?>
            <?php
                $collapseId = esc($accordionId) . '_collapse_' . $q['id'];
                $headingId  = esc($accordionId) . '_heading_' . $q['id'];
                $opts       = $q['options'] ?? [];
                $correctOptIndex = null;
                foreach ($opts as $oi => $o) {
                    if (! empty($o['is_correct'])) {
                        $correctOptIndex = $oi;
                        break;
                    }
                }
            ?>
            <div class="accordion-item border radius-8 mb-8 overflow-hidden">
                <h2 class="accordion-header" id="<?= $headingId ?>">
                    <div class="d-flex align-items-center justify-content-between p-12 bg-white gap-2 flex-wrap">
                        <button class="accordion-button collapsed flex-grow-1 p-0 bg-transparent shadow-none text-neutral-900 border-0"
                                type="button" data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>"
                                aria-expanded="false" aria-controls="<?= $collapseId ?>">
                            <div class="d-flex align-items-center gap-10 flex-grow-1 me-12">
                                <span class="badge bg-primary-50 text-primary-700 fw-bold font-monospace radius-4 px-8 py-3 text-xs">
                                    #<?= $qIdx + 1 ?>
                                </span>
                                <span class="text-xs fw-semibold text-neutral-800 text-truncate" style="max-width: 520px;" title="<?= esc($q['prompt']) ?>">
                                    <?= esc($q['prompt']) ?>
                                </span>
                            </div>
                        </button>

                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <span class="badge bg-neutral-100 text-secondary-light text-xxs radius-4 font-monospace">
                                <?= number_format((float) $q['points'], 1) ?> Poin
                            </span>
                            <?php if ($correctOptIndex !== null && isset($optionLetters[$correctOptIndex])): ?>
                                <span class="badge bg-success-50 text-success-700 text-xxs radius-4 fw-bold">
                                    Kunci: <?= $optionLetters[$correctOptIndex] ?>
                                </span>
                            <?php endif; ?>

                            <button type="button" class="btn btn-xs btn-outline-warning-600 radius-4 px-8 py-4"
                                    onclick="populateEditQuestion(<?= htmlspecialchars(json_encode($q), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($opts), ENT_QUOTES, 'UTF-8') ?>, '<?= esc(addslashes($quiz['title'])) ?>')"
                                    title="Ubah Soal">
                                <iconify-icon icon="solar:pen-new-square-outline"></iconify-icon>
                            </button>

                            <form action="<?= route_to('dashboard.learning.curriculum.question.delete', $q['id']) ?>" method="post"
                                  onsubmit="return confirm('Hapus soal #<?= $qIdx + 1 ?> ini beserta pilihan jawabannya?');" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-xs btn-outline-danger radius-4 px-8 py-4 text-danger-main" title="Hapus Soal">
                                    <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                </button>
                            </form>
                        </div>
                    </div>
                </h2>

                <div id="<?= $collapseId ?>" class="accordion-collapse collapse" aria-labelledby="<?= $headingId ?>" data-bs-parent="#<?= esc($accordionId) ?>">
                    <div class="accordion-body p-16 pt-0 border-top bg-neutral-25">
                        <div class="mb-12 pt-12">
                            <span class="text-xxs text-secondary-light d-block text-uppercase fw-semibold mb-1">Pertanyaan Lengkap:</span>
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
                                        $letter = $optionLetters[$oIdx] ?? (string) ($oIdx + 1);
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
                                    <strong class="d-block mb-1 text-info-700">Pembahasan / Penjelasan:</strong>
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
