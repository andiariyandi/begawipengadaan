<?php
/**
 * Trainer - Materi Pembelajaran (Agregat)
 * FiStudy UI DNA
 */
$classes       = $classes ?? [];
$selectedClass = $selectedClass ?? null;
$modules       = $modules ?? [];
?>

<div class="trainer-materials-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-book me-1"></i> Manajemen Kurikulum
                </span>
                <h1 class="h3 fw-bold text-navy mb-1">Materi Pembelajaran</h1>
                <p class="text-muted small mb-0">Kelola silabus modul dan materi di setiap kelas yang Anda ampu.</p>
            </div>

            <!-- Class Filter Dropdown -->
            <?php if (! empty($classes)): ?>
                <form method="GET" action="" class="d-flex align-items-center gap-2">
                    <label class="small text-muted text-nowrap fw-semibold">Pilih Kelas:</label>
                    <select name="class_id" class="form-select form-select-sm rounded-pill pe-4" onchange="this.form.submit()">
                        <?php foreach ($classes as $c): ?>
                            <option value="<?= esc($c['id']) ?>" <?= ((int) ($selectedClass['id'] ?? 0) === (int) $c['id']) ? 'selected' : '' ?>>
                                <?= esc($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Active Class Overview & Modules -->
    <?php if ($selectedClass): ?>
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 border-bottom pb-3">
                <div>
                    <span class="badge bg-light text-navy border rounded-pill px-2 py-1 small me-2">Kode: <?= esc($selectedClass['code']) ?></span>
                    <?php if (! empty($selectedClass['learning_method'])): ?>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 small me-2">
                            <?= esc(strtoupper($selectedClass['learning_method'])) ?>
                        </span>
                    <?php endif; ?>
                    <h2 class="h5 fw-bold text-navy mb-0 d-inline"><?= esc($selectedClass['name']) ?></h2>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= route_to('workspace.trainer.class.detail', (string) $selectedClass['id']) ?>?tab=materi" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="fas fa-external-link-alt me-1"></i> Buka di Learning Room
                    </a>
                    <a href="<?= route_to('workspace.trainer.module.create', (string) $selectedClass['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                        <i class="fas fa-plus me-1"></i> Tambah Modul
                    </a>
                </div>
            </div>

            <!-- Modules Tree -->
            <?php if (empty($modules)): ?>
                <div class="text-center py-5 border rounded-4 bg-light">
                    <i class="fas fa-layer-group fa-3x text-muted mb-3 opacity-25"></i>
                    <h3 class="h6 fw-bold text-navy mb-1">Belum Ada Modul di Kelas Ini</h3>
                    <p class="text-muted small mb-3">Tambahkan modul pembelajaran pertama untuk kelas ini.</p>
                    <a href="<?= route_to('workspace.trainer.module.create', (string) $selectedClass['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-4">
                        + Tambah Modul
                    </a>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($modules as $m): ?>
                        <div class="border rounded-4 bg-white overflow-hidden shadow-sm">
                            <div class="bg-light p-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge bg-primary text-white rounded-pill px-3 py-1 small fw-semibold">
                                        Modul <?= esc($m['sort_order']) ?>
                                    </span>
                                    <strong class="text-navy"><?= esc($m['title']) ?></strong>
                                    <span class="badge <?= $m['status'] === 'published' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?> rounded-pill px-2 py-1 small">
                                        <?= esc(ucfirst($m['status'])) ?>
                                    </span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="<?= route_to('workspace.trainer.lesson.create', (string) $selectedClass['id'], (string) $m['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                                        <i class="fas fa-plus me-1"></i> Materi
                                    </a>
                                </div>
                            </div>
                            <div class="p-3 px-4">
                                <?php if (empty($m['lessons'])): ?>
                                    <p class="text-muted small mb-0 py-1">Belum ada materi pada modul ini.</p>
                                <?php else: ?>
                                    <div class="d-flex flex-column gap-2">
                                        <?php foreach ($m['lessons'] as $l): ?>
                                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <?php if ($l['content_type'] === 'video'): ?>
                                                        <i class="fas fa-play-circle text-danger"></i>
                                                    <?php elseif ($l['content_type'] === 'document'): ?>
                                                        <i class="fas fa-file-pdf text-danger"></i>
                                                    <?php elseif ($l['content_type'] === 'link'): ?>
                                                        <i class="fas fa-link text-info"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-file-alt text-primary"></i>
                                                    <?php endif; ?>
                                                    <span class="small text-navy fw-semibold"><?= esc($l['title']) ?></span>
                                                    <span class="badge bg-light text-muted border rounded-pill px-2 py-0" style="font-size: 0.72rem;"><?= esc(strtoupper($l['content_type'])) ?></span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge <?= $l['status'] === 'published' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?> rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                                                        <?= esc(ucfirst($l['status'])) ?>
                                                    </span>
                                                    <a href="<?= route_to('workspace.trainer.lesson.edit', (string) $selectedClass['id'], (string) $l['id']) ?>" class="btn btn-sm btn-light border py-0 px-2" title="Ubah">
                                                        <i class="fas fa-edit" style="font-size: 0.75rem;"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
