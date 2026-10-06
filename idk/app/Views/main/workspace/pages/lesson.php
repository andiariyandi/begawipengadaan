<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?php
$lesson    = $lesson ?? [];
$progress  = $progress ?? [];
$status    = (string) ($progress['status'] ?? 'not_started');
$badge     = match ($status) {
    'completed'   => ['label' => 'Selesai',        'class' => 'bg-success-subtle text-success-emphasis'],
    'in_progress' => ['label' => 'Sedang Belajar', 'class' => 'bg-warning-subtle text-warning-emphasis'],
    default       => ['label' => 'Belum Dimulai',  'class' => 'bg-light text-muted'],
};
$contentType = (string) ($lesson['content_type'] ?? 'text');
$classId     = (int) ($lesson['class_id'] ?? 0);
$lessonId    = (int) ($lesson['id'] ?? 0);
?>

<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2">Lesson</p>
    <h1 class="mb-2"><?= esc($lesson['title'] ?? 'Lesson') ?></h1>
    <p class="text-muted mb-3">Modul: <?= esc($lesson['module_title'] ?? '-') ?> &middot; Tipe: <?= esc(ucfirst($contentType)) ?></p>
    <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
        <span class="badge <?= $badge['class'] ?>"><?= esc($badge['label']) ?></span>
        <small class="text-muted"><?= esc((string) ($progress['progress_percent'] ?? '0.00')) ?>%</small>
    </div>
</div>

<div class="bg-white border rounded p-4 mb-4">
    <h2 class="h5 mb-3">Materi</h2>
    <?php if ($contentType === 'document' && ! empty($lesson['file_id'])): ?>
        <p class="small text-muted mb-2">File materi (id: <?= (int) $lesson['file_id'] ?>) tersedia pada sistem file.</p>
    <?php elseif ($contentType === 'video' && ! empty($lesson['external_url'])): ?>
        <p><a href="<?= esc($lesson['external_url']) ?>" target="_blank" rel="noopener">Buka video</a></p>
    <?php elseif ($contentType === 'link' && ! empty($lesson['external_url'])): ?>
        <p><a href="<?= esc($lesson['external_url']) ?>" target="_blank" rel="noopener">Buka tautan materi</a></p>
    <?php else: ?>
        <?php if (! empty($lesson['content'])): ?>
            <div class="lesson-content"><?= nl2br(esc($lesson['content'])) ?></div>
        <?php else: ?>
            <p class="text-muted mb-0">Materi lesson belum tersedia.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<div class="bg-white border rounded p-4 mb-4 d-flex flex-wrap gap-8 justify-content-between align-items-center">
    <?php if (! empty($prev)): ?>
        <a class="btn btn-outline-secondary" href="<?= route_to('workspace.participant.lesson.detail', $prev['id']) ?>">&laquo; <?= esc($prev['title'] ?? 'Sebelumnya') ?></a>
    <?php else: ?>
        <span></span>
    <?php endif; ?>
    <?php if ($status !== 'completed'): ?>
        <form method="post" action="<?= route_to('workspace.participant.lesson.complete', $lessonId) ?>" class="m-0">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-primary">Tandai Selesai</button>
        </form>
    <?php else: ?>
        <span class="text-success fw-semibold">Lesson telah selesai.</span>
    <?php endif; ?>
    <?php if (! empty($next)): ?>
        <a class="btn btn-outline-primary" href="<?= route_to('workspace.participant.lesson.detail', $next['id']) ?>"><?= esc($next['title'] ?? 'Berikutnya') ?> &raquo;</a>
    <?php else: ?>
        <span></span>
    <?php endif; ?>
</div>

<div class="bg-white border rounded p-3 text-center">
    <a class="small text-decoration-none" href="<?= route_to('workspace.participant.study', $classId) ?>">Kembali ke Learning Room</a>
</div>
<?= $this->endSection() ?>