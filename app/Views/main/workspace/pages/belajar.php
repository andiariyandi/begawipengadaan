<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?php
$modules       = $modules ?? [];
$enrollment    = $enrollment ?? [];
$progressInfo  = $progressInfo ?? ['total' => 0, 'completed' => 0, 'in_progress' => 0, 'percent' => 0];
$classContext  = $classContext ?? [];
$quizzes       = $quizzes ?? [];
$lessonBadge = static fn (string $status): array => match ($status) {
    'completed' => ['label' => 'Selesai',      'class' => 'bg-success-subtle text-success-emphasis'],
    'in_progress' => ['label' => 'Sedang Belajar', 'class' => 'bg-warning-subtle text-warning-emphasis'],
    default     => ['label' => 'Belum Dimulai','class' => 'bg-light text-muted'],
};
$typeLabel = static fn (string $type): string => match ($type) {
    'pre_test'  => 'Pre-test',
    'post_test' => 'Post-test',
    default     => 'Quiz',
};
?>

<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2">Learning Room</p>
    <h1 class="mb-2"><?= esc($classContext['class_name'] ?? 'Belajar') ?></h1>
    <p class="text-muted mb-3">Course: <?= esc($classContext['course_title'] ?? '-') ?><?php if (! empty($classContext['program_title'])): ?> &middot; Program: <?= esc($classContext['program_title']) ?><?php endif; ?></p>
    <div class="progress" role="progressbar" aria-label="Progress" aria-valuenow="<?= (int) $progressInfo['percent'] ?>" aria-valuemin="0" aria-valuemax="100">
        <div class="progress-bar bg-primary" style="width: <?= (float) $progressInfo['percent'] ?>%"></div>
    </div>
    <p class="small text-muted mt-2 mb-0">
        <?= (int) $progressInfo['completed'] ?> dari <?= (int) $progressInfo['total'] ?> lesson selesai
        (<?= (float) $progressInfo['percent'] ?>%)
    </p>
</div>

<div class="bg-white border rounded p-4 mb-4">
    <h2 class="h4 mb-3">Struktur Pembelajaran</h2>

    <?php if ($modules === []): ?>
        <p class="text-muted mb-0">Kelas ini belum memiliki modul atau lesson.</p>
    <?php else: ?>
        <?php foreach ($modules as $index => $mod): ?>
            <section class="border rounded p-3 mb-3">
                <header class="d-flex align-items-baseline gap-2 mb-2">
                    <span class="badge bg-light text-body fw-semibold">Modul <?= esc((string) ($index + 1)) ?></span>
                    <h3 class="h5 mb-0"><?= esc($mod['title'] ?? 'Modul') ?></h3>
                </header>
                <?php if (empty($mod['lessons'])): ?>
                    <p class="text-muted small mb-0">Modul ini belum memiliki lesson.</p>
                <?php else: ?>
                    <ol class="list-group list-group-numbered list-group-flush">
                        <?php foreach ($mod['lessons'] as $lesson): ?>
                            <?php $p = $lesson['progress'] ?? null; $badge = $lessonBadge((string) ($p['status'] ?? 'not_started')); ?>
                            <li class="list-group-item bg-transparent px-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <a class="text-decoration-none" href="<?= route_to('workspace.participant.lesson.detail', $lesson['id']) ?>"><?= esc($lesson['title'] ?? '-') ?></a>
                                <span class="badge <?= $badge['class'] ?>"><?= esc($badge['label']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="bg-white border rounded p-4">
    <h2 class="h4 mb-3">Penilaian</h2>
    <?php if ($quizzes === []): ?>
        <p class="text-muted mb-0">Kelas ini belum memiliki quiz atau test.</p>
    <?php else: ?>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead><tr><th>#</th><th>Judul</th><th>Tipe</th><th>Percobaan</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($quizzes as $i => $q): ?>
                    <tr>
                        <td><?= (int) ($i + 1) ?></td>
                        <td><?= esc($q['title'] ?? '-') ?></td>
                        <td><?= esc($typeLabel((string) ($q['type'] ?? 'lesson_quiz'))) ?></td>
                        <td><?= (int) ($q['attempt_limit'] ?? 1) ?></td>
                        <td class="text-end">
                            <a class="btn btn-outline-primary btn-sm" href="<?= route_to('workspace.participant.quiz.take', (string) $q['id']) ?>">Buka</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>