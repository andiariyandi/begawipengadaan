<?php
/**
 * Trainer - Assessment & Ujian (Agregat)
 * FiStudy UI DNA
 */
$classes       = $classes ?? [];
$selectedClass = $selectedClass ?? null;
$quizzes       = $quizzes ?? [];
?>

<div class="trainer-quizzes-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-clipboard-check me-1"></i> Evaluasi & Ujian
                </span>
                <h1 class="h3 fw-bold text-navy mb-1">Assessment</h1>
                <p class="text-muted small mb-0">Kelola soal ujian, bobot poin, dan hasil pengerjaan kuis peserta.</p>
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

    <!-- Active Class Quizzes -->
    <?php if ($selectedClass): ?>
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 border-bottom pb-3">
                <div>
                    <span class="badge bg-light text-navy border rounded-pill px-2 py-1 small me-2">Kode: <?= esc($selectedClass['code']) ?></span>
                    <?php if (! empty($selectedClass['learning_method'])): ?>
                        <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2 py-1 small me-2">
                            <?= esc(strtoupper($selectedClass['learning_method'])) ?>
                        </span>
                    <?php endif; ?>
                    <h2 class="h5 fw-bold text-navy mb-0 d-inline"><?= esc($selectedClass['name']) ?></h2>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= route_to('workspace.trainer.class.detail', (string) $selectedClass['id']) ?>?tab=assessment" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="fas fa-external-link-alt me-1"></i> Buka di Learning Room
                    </a>
                    <a href="<?= route_to('workspace.trainer.quiz.create', (string) $selectedClass['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                        <i class="fas fa-plus me-1"></i> Tambah Ujian
                    </a>
                </div>
            </div>

            <?php if (empty($quizzes)): ?>
                <div class="text-center py-5 border rounded-4 bg-light">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3 opacity-25"></i>
                    <h3 class="h6 fw-bold text-navy mb-1">Belum Ada Ujian di Kelas Ini</h3>
                    <p class="text-muted small mb-3">Buat Pre-Test, Quiz modul, atau Post-Test untuk kelas ini.</p>
                    <a href="<?= route_to('workspace.trainer.quiz.create', (string) $selectedClass['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-4">
                        + Tambah Ujian
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-muted">
                            <tr>
                                <th>Tipe</th>
                                <th>Judul Kuis</th>
                                <th>Batas Percobaan</th>
                                <th>Jumlah Soal</th>
                                <th>Total Dikerjakan</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($quizzes as $q): ?>
                                <tr>
                                    <td>
                                        <?php if ($q['type'] === 'pre_test'): ?>
                                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 small fw-semibold">Pre-Test</span>
                                        <?php elseif ($q['type'] === 'post_test'): ?>
                                            <span class="badge bg-danger rounded-pill px-3 py-1 small fw-semibold">Post-Test</span>
                                        <?php else: ?>
                                            <span class="badge bg-info text-dark rounded-pill px-3 py-1 small fw-semibold">Quiz Modul</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong class="text-navy small d-block"><?= esc($q['title']) ?></strong>
                                    </td>
                                    <td class="small text-muted">
                                        <?= (int) ($q['attempt_limit'] ?? 1) ?> kali
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-navy border rounded-pill px-2 py-1 small">
                                            <?= (int) ($q['question_count'] ?? 0) ?> Soal
                                        </span>
                                    </td>
                                    <td class="small text-muted">
                                        <?= (int) ($q['attempts_count'] ?? 0) ?> submission
                                    </td>
                                    <td>
                                        <span class="badge <?= $q['status'] === 'published' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?> rounded-pill px-2 py-1 small">
                                            <?= esc(ucfirst($q['status'] ?? 'draft')) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= route_to('workspace.trainer.quiz.edit', (string) $selectedClass['id'], (string) $q['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                                            <i class="fas fa-edit me-1"></i> Kelola Soal
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
