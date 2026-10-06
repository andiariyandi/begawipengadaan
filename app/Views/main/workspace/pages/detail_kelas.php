<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?php
$ctx      = $classContext ?? [];
$mods     = $modules ?? [];
$title    = $pageTitle ?? ($ctx['name'] ?? 'Detail Kelas');
$enrollSt = (string) (($enrollment ?? [])['status'] ?? '');
$canEnter = $enrollSt === 'active';
$lessonBadge = static fn (string $status): array => match ($status) {
    'completed'   => ['label' => 'Selesai',        'class' => 'bg-success-subtle text-success-emphasis'],
    'in_progress' => ['label' => 'Sedang Belajar', 'class' => 'bg-warning-subtle text-warning-emphasis'],
    default       => ['label' => 'Belum Dimulai',  'class' => 'bg-light text-muted'],
};
?>
<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2">Detail Kelas</p>
    <h1 class="mb-2"><?= esc($title) ?></h1>
    <p class="text-muted mb-3">Kode: <?= esc($ctx['code'] ?? '-') ?></p>

    <?php if (! empty($ctx['course_title']) || !empty($ctx['program_title'])): ?>
        <ul class="list-unstyled small text-muted mb-0">
            <?php if (!empty($ctx['course_title'])): ?>
                <li>Course: <strong class="text-body"><?= esc($ctx['course_title']) ?></strong></li>
            <?php endif; ?>
            <?php if (!empty($ctx['program_title'])): ?>
                <li>Program: <strong class="text-body"><?= esc($ctx['program_title']) ?></strong></li>
            <?php endif; ?>
            <?php if (!empty($ctx['start_at']) || !empty($ctx['end_at'])): ?>
                <li>Periode: <?= esc($ctx['start_at'] ?? '-') ?> &ndash; <?= esc($ctx['end_at'] ?? '-') ?></li>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
</div>

<div class="bg-white border rounded p-4">
    <h2 class="h4 mb-3">Struktur Pembelajaran</h2>

    <?php if (empty($mods)): ?>
        <p class="text-muted mb-0">Kelas ini belum memiliki modul atau lesson.</p>
    <?php else: ?>
        <?php foreach ($mods as $index => $mod): ?>
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
                                <?php if ($canEnter): ?>
                                    <a class="text-decoration-none" href="<?= route_to('workspace.participant.lesson.detail', $lesson['id']) ?>"><?= esc($lesson['title'] ?? '-') ?></a>
                                <?php else: ?>
                                    <span><?= esc($lesson['title'] ?? '-') ?></span>
                                <?php endif; ?>
                                <?php if ($p !== null): ?>
                                    <span class="badge <?= $badge['class'] ?>"><?= esc($badge['label']) ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>