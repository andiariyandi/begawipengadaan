<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2">Trainer</p>
    <h1 class="mb-2"><?= esc($classCtx['name'] ?? 'Detail Kelas') ?></h1>
    <p class="text-muted mb-3">Kode: <?= esc($classCtx['code'] ?? '-') ?> &middot; Course: <?= esc($classCtx['course_title'] ?? '-') ?><?php if (! empty($classCtx['program_title'])): ?> &middot; Program: <?= esc($classCtx['program_title']) ?><?php endif; ?></p>
    <p class="small text-muted mb-0">Periode: <?= esc(($classCtx['start_at'] ?? '-') . ' &ndash; ' . ($classCtx['end_at'] ?? '-')) ?></p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="bg-white border rounded p-3 h-100">
            <p class="text-muted small mb-1">Peserta Aktif</p>
            <strong class="display-6"><?= (int) ($participantCount ?? 0) ?></strong>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="bg-white border rounded p-3 h-100">
            <p class="text-muted small mb-1">Modul</p>
            <strong class="display-6"><?= count($modules ?? []) ?></strong>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="bg-white border rounded p-3 h-100">
            <p class="text-muted small mb-1">Ujian</p>
            <strong class="display-6"><?= (int) ($quizCount ?? 0) ?></strong>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="bg-white border rounded p-3 h-100">
            <p class="text-muted small mb-1">Status</p>
            <strong class="display-6"><?= esc(ucfirst($classCtx['status'] ?? 'draft')) ?></strong>
        </div>
    </div>
</div>

<div class="bg-white border rounded p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">Aksi Cepat</h2>
    </div>
    <div class="d-flex flex-wrap gap-8">
        <a class="btn btn-outline-primary" href="<?= route_to('workspace.trainer.participants', $classCtx['id']) ?>">Peserta</a>
        <a class="btn btn-outline-primary" href="<?= route_to('workspace.trainer.modules', $classCtx['id']) ?>">Modul</a>
        <a class="btn btn-outline-primary" href="<?= route_to('workspace.trainer.materials', $classCtx['id']) ?>">Materi</a>
        <a class="btn btn-outline-primary" href="<?= route_to('workspace.trainer.quizzes', $classCtx['id']) ?>">Ujian</a>
        <a class="btn btn-outline-primary" href="<?= route_to('workspace.trainer.progress', $classCtx['id']) ?>">Progress</a>
    </div>
</div>