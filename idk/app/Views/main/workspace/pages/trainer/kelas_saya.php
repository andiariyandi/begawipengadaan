<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2">Trainer</p>
    <h1 class="mb-3">Kelas Saya</h1>
    <p class="mb-0">Kelas yang Anda ampu berdasarkan penugasan class_trainers.</p>
</div>

<div class="bg-white border rounded p-4">
    <h2 class="h4 mb-3">Daftar Kelas</h2>
    <?php if (empty($classes ?? [])): ?>
        <p class="text-muted mb-0">Anda belum memiliki kelas yang ditugaskan.</p>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($classes as $row): ?>
                <div class="col-lg-6">
                    <article class="border rounded p-3 h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                            <span class="badge bg-primary-subtle text-primary-emphasis"><?= esc(ucfirst($row['ct_status'] ?? 'active')) ?></span>
                            <small class="text-muted"><?= esc($row['code'] ?? '-') ?></small>
                        </div>
                        <h3 class="h5 mb-2"><?= esc($row['name'] ?? '-') ?></h3>
                        <p class="mb-1"><?= esc($row['course_title'] ?? '-') ?></p>
                        <?php if (! empty($row['program_title'])): ?>
                            <p class="small text-muted mb-1">Program: <?= esc($row['program_title']) ?></p>
                            <?php endif; ?>
                        <p class="small text-muted mb-3">Periode: <?= esc(($row['start_at'] ?? '-') . ' &ndash; ' . ($row['end_at'] ?? '-')) ?></p>
                        <div class="mt-auto d-flex gap-8">
                            <a class="btn btn-primary btn-sm" href="<?= route_to('workspace.trainer.class.detail', $row['class_id']) ?>">Kelola</a>
                            <a class="btn btn-outline-primary btn-sm" href="<?= route_to('workspace.trainer.modules', $row['class_id']) ?>">Modul</a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>