<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?php
$enrollments = $enrollments ?? [];
$summary     = $summary ?? ['pending' => 0, 'active' => 0, 'completed' => 0, 'cancelled' => 0, 'withdrawn' => 0];
$fmt         = static fn (int $n): string => number_format($n, 0, ',', '.');
$statusBadge = static function (string $status): array {
    return match ($status) {
        'active'    => ['label' => 'Aktif',            'class' => 'bg-success-subtle text-success-emphasis'],
        'completed' => ['label' => 'Selesai',          'class' => 'bg-primary-subtle text-primary-emphasis'],
        'pending'   => ['label' => 'Menunggu Approval','class' => 'bg-warning-subtle text-warning-emphasis'],
        'withdrawn' => ['label' => 'Dibatalkan',       'class' => 'bg-secondary-subtle text-secondary-emphasis'],
        default     => ['label' => 'Ditolak',          'class' => 'bg-secondary-subtle text-secondary-emphasis'],
    };
};
?>

<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2">Workspace</p>
    <h1 class="mb-3">Kelas Saya</h1>
    <p class="mb-0">Riwayat enrollment Anda: pending, aktif, selesai, dibatalkan, dan ditolak.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="bg-white border rounded p-3 h-100">
            <p class="text-muted small mb-1">Menunggu Approval</p>
            <strong class="display-6"><?= $fmt((int) ($summary['pending'] ?? 0)) ?></strong>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="bg-white border rounded p-3 h-100">
            <p class="text-muted small mb-1">Aktif</p>
            <strong class="display-6"><?= $fmt((int) ($summary['active'] ?? 0)) ?></strong>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="bg-white border rounded p-3 h-100">
            <p class="text-muted small mb-1">Selesai</p>
            <strong class="display-6"><?= $fmt((int) ($summary['completed'] ?? 0)) ?></strong>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="bg-white border rounded p-3 h-100">
            <p class="text-muted small mb-1">Dibatalkan / Ditolak</p>
            <strong class="display-6"><?= $fmt((int) (($summary['cancelled'] ?? 0) + ($summary['withdrawn'] ?? 0))) ?></strong>
        </div>
    </div>
</div>

<div class="bg-white border rounded p-4">
    <h2 class="h4 mb-3">Riwayat Enrollment</h2>
    <?php if ($enrollments === []): ?>
        <p class="text-muted mb-0">Anda belum memiliki enrollment. Buka Arena Belajar untuk mendaftar kelas.</p>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($enrollments as $item): ?>
                <?php
                    $status   = (string) ($item['enrollment_status'] ?? 'pending');
                    $badge    = $statusBadge($status);
                    $classId  = (int) ($item['class_id'] ?? 0);
                    $ps       = $item['progress_summary'] ?? ['completed' => 0, 'total' => 0, 'percent' => 0.0];
                ?>
                <div class="col-lg-6">
                    <article class="border rounded p-3 h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                            <span class="badge <?= $badge['class'] ?>"><?= esc($badge['label']) ?></span>
                            <small class="text-muted"><?= esc($item['class_code'] ?? '-') ?></small>
                        </div>
                        <h3 class="h5 mb-2"><?= esc($item['class_name'] ?? '-') ?></h3>
                        <p class="mb-1"><?= esc($item['course_title'] ?? '-') ?></p>
                        <?php if (! empty($item['program_title'])): ?>
                            <p class="small text-muted mb-1">Program: <?= esc($item['program_title']) ?></p>
                        <?php endif; ?>
                        <p class="small text-muted mb-1">Periode: <?= esc(($item['start_at'] ?? '-') . ' &ndash; ' . ($item['end_at'] ?? '-')) ?></p>
                        <p class="small text-muted mb-3">Didaftar: <?= esc($item['enrolled_at'] ?? '-') ?></p>
                        <?php if ($status === 'active'): ?>
                            <div class="mb-2">
                                <div class="progress" role="progressbar" aria-label="Progress" aria-valuenow="<?= (int) $ps['percent'] ?>" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-primary" style="width: <?= (float) $ps['percent'] ?>%"></div>
                                </div>
                                <small class="text-muted"><?= (int) $ps['completed'] ?> dari <?= (int) $ps['total'] ?> lesson selesai (<?= (float) $ps['percent'] ?>%)</small>
                            </div>
                        <?php endif; ?>
                        <div class="mt-auto d-flex gap-8">
                            <?php if ($status === 'active'): ?>
                                <a class="btn btn-primary btn-sm" href="<?= route_to('workspace.participant.study', $classId) ?>">Masuk Kelas</a>
                            <?php else: ?>
                                <span class="btn btn-outline-secondary btn-sm disabled" aria-disabled="true">
                                    <?= match ($status) {
                                        'pending'   => 'Belum dapat masuk',
                                        'completed' => 'Selesai',
                                        'withdrawn' => 'Dibatalkan',
                                        'cancelled' => 'Ditolak',
                                        default     => 'Tidak dapat diikuti',
                                    } ?>
                                </span>
                            <?php endif; ?>
                            <a class="btn btn-outline-primary btn-sm" href="<?= route_to('workspace.participant.class.detail', $classId) ?>">Lihat Detail</a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>