<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="mb-24">
    <h6 class="mb-4">Materi Pelatihan</h6>
    <p class="text-secondary-light text-sm mb-0">
        Status kelas:
        <?php if ($enrollment['final_status'] === 'passed'): ?>
            <span class="text-success-600 fw-semibold">Lulus</span>
        <?php else: ?>
            <span class="text-warning-600 fw-semibold">Sedang Berjalan</span>
        <?php endif; ?>
    </p>
</div>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success radius-12 mb-16"><?= esc(session()->getFlashdata('message')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<?php if (empty($modules)): ?>file_path
<p class="text-secondary-light">Belum ada modul untuk kelas ini.</p>
<?php else: ?>
    <?php foreach ($modules as $module): ?>
        <div class="card p-0 radius-12 mb-24">
            <div class="card-header border-bottom bg-base py-16 px-24">
                <h6 class="mb-0">Modul <?= esc($module['sequence_order']) ?>: <?= esc($module['title']) ?></h6>
            </div>
            <div class="card-body p-24">

                <?php if (empty($module['lessons'])): ?>
                    <p class="text-secondary-light text-sm">Belum ada materi di modul ini.</p>
                <?php else: ?>
                    <ul class="list-unstyled mb-24">
                        <?php foreach ($module['lessons'] as $lesson): ?>
                            <li class="d-flex align-items-center justify-content-between border-bottom py-12">
                                <div>
                                    <span class="fw-medium"><?= esc($lesson['title']) ?></span>
                                    <span class="bg-neutral-200 text-neutral-600 border border-neutral-400 px-16 py-4 radius-4 text-sm ms-8">
                                        <?= esc(ucfirst($lesson['content_type'])) ?>
                                    </span>
                                    <?php if ($lesson['content_type'] === 'text'): ?>
                                        <p class="text-secondary-light text-sm mt-8 mb-0"><?= nl2br(esc($lesson['content_text'])) ?></p>
                                    <?php elseif (! empty($lesson['file_path'])): ?>
                                        <a href="<?= route_to('learning.study.lessons.download', $enrollmentId, $lesson['id']) ?>" target="_blank" class="text-sm d-block mt-8">Buka File Materi</a>
                                    <?php endif; ?>
                                </div>
                                <div class="text-nowrap ms-16">
                                    <?php if ($lesson['is_completed']): ?>
                                        <span class="text-success-600 text-sm"><iconify-icon icon="mdi:check-circle-outline"></iconify-icon> Selesai</span>
                                    <?php else: ?>
                                        <form action="<?= route_to('learning.study.completeLesson', $enrollmentId, $lesson['id']) ?>" method="post">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-primary btn-sm px-16 py-8 radius-8">Tandai Selesai</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if (! empty($module['quizzes'])): ?>
                    <div class="border-top pt-16">
                        <h6 class="text-sm mb-12">Quiz Modul Ini</h6>
                        <?php foreach ($module['quizzes'] as $quiz): ?>
                            <div class="d-flex align-items-center justify-content-between mb-8">
                                <div>
                                    <span><?= esc($quiz['title']) ?></span>
                                    <?php if ($quiz['is_passed'] === null): ?>
                                        <span class="text-secondary-light text-sm ms-8">Belum dikerjakan</span>
                                    <?php elseif ($quiz['is_passed']): ?>
                                        <span class="text-success-600 text-sm ms-8">Lulus (nilai <?= esc($quiz['last_score']) ?>)</span>
                                    <?php else: ?>
                                        <span class="text-danger-600 text-sm ms-8">Belum lulus (nilai <?= esc($quiz['last_score']) ?>)</span>
                                    <?php endif; ?>
                                </div>
                                <a href="<?= route_to('learning.study.quiz.take', $enrollmentId, $quiz['id']) ?>" class="btn btn-primary btn-sm px-16 py-8 radius-8">
                                    <?= $quiz['is_passed'] === null ? 'Kerjakan' : 'Ulangi' ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (! empty($module['manual_assessments'])): ?>
                    <div class="border-top pt-16">
                        <h6 class="text-sm mb-12">Exam / Assignment</h6>
                        <?php foreach ($module['manual_assessments'] as $assessment): ?>
                            <div class="d-flex align-items-center justify-content-between mb-8">
                                <div>
                                    <span><?= esc($assessment['title']) ?></span>
                                    <?php if ($assessment['evaluation_status'] === 'pending'): ?>
                                        <span class="text-warning-600 text-sm ms-8">Menunggu penilaian</span>
                                    <?php elseif ($assessment['evaluation_status'] === 'evaluated'): ?>
                                        <span class="<?= $assessment['is_passed'] ? 'text-success-600' : 'text-danger-600' ?> text-sm ms-8">
                                            <?= $assessment['is_passed'] ? 'Lulus' : 'Belum lulus' ?> (nilai <?= esc($assessment['last_score']) ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="text-secondary-light text-sm ms-8">Belum dikirim</span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($assessment['evaluation_status'] !== 'pending'): ?>
                                    <a href="<?= route_to('learning.study.assessment.show', $enrollmentId, $assessment['id']) ?>" class="btn btn-primary btn-sm px-16 py-8 radius-8">
                                        <?= $assessment['evaluation_status'] === 'evaluated' ? 'Kirim Ulang' : 'Kirim Submission' ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection(); ?>