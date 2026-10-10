<?php
/**
 * Trainer - Daftar Pengumpulan Tugas (Assignment Submissions)
 * FiStudy UI DNA
 */
$classId     = $classId ?? 0;
$assignment  = $assignment ?? [];
$submissions = $submissions ?? [];
$className   = $className ?? 'Kelas';
?>

<div class="trainer-assignment-submissions-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-info text-dark px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-file-invoice me-1"></i> Evaluasi Tugas
                </span>
                <h1 class="h3 fw-bold text-navy mb-1"><?= esc($assignment['title'] ?? 'Daftar Pengumpulan Tugas') ?></h1>
                <p class="text-muted small mb-0">Kelas: <strong><?= esc($className) ?></strong> &middot; Total Pengumpulan: <strong><?= count($submissions) ?></strong></p>
            </div>
            <div>
                <a href="<?= route_to('workspace.trainer.class.detail', (string) $classId) ?>?tab=assignment" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Assignment
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Submissions Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <?php if (empty($submissions)): ?>
            <div class="text-center py-5 border rounded-4 bg-light">
                <i class="fas fa-inbox fa-3x text-muted mb-3 opacity-25"></i>
                <h2 class="h6 fw-bold text-navy mb-1">Belum Ada Pengumpulan Tugas</h2>
                <p class="text-muted small mb-0">Peserta belum mengumpulkan jawaban atau dokumen untuk tugas ini.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th>Peserta</th>
                            <th>Percobaan</th>
                            <th>Waktu Submit</th>
                            <th>Status Review</th>
                            <th>Nilai</th>
                            <th>Feedback Pengajar</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $sub): ?>
                            <tr>
                                <td>
                                    <strong class="text-navy small d-block"><?= esc($sub['user_name']) ?></strong>
                                    <span class="text-muted" style="font-size: 0.76rem;"><?= esc($sub['user_email']) ?></span>
                                    <?php if (! empty($sub['assignment_title'])): ?>
                                        <div class="mt-1" style="font-size: 0.78rem;">
                                            <span class="text-primary fw-medium"><i class="fas fa-file-alt me-1"></i><?= esc($sub['assignment_title']) ?></span>
                                            <?php if (! empty($sub['class_name'])): ?>
                                                <span class="text-muted">&middot; <?= esc($sub['class_name']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted">
                                    Ke-<?= (int) ($sub['attempt_number'] ?? 1) ?>
                                </td>
                                <td class="small text-muted">
                                    <?= esc(! empty($sub['submitted_at']) ? date('d M Y, H:i', strtotime($sub['submitted_at'])) : '-') ?>
                                </td>
                                <td>
                                    <?php if ($sub['status'] === 'submitted' || $sub['status'] === 'in_review'): ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1 small fw-semibold">
                                            Perlu Review
                                        </span>
                                    <?php elseif ($sub['status'] === 'graded'): ?>
                                        <span class="badge bg-success rounded-pill px-3 py-1 small fw-semibold">
                                            Selesai Dinilai
                                        </span>
                                    <?php elseif ($sub['status'] === 'revision_required'): ?>
                                        <span class="badge bg-danger rounded-pill px-3 py-1 small fw-semibold">
                                            Perlu Revisi
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 small">
                                            <?= esc(ucfirst($sub['status'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($sub['score'] !== null): ?>
                                        <strong class="text-success"><?= esc($sub['score']) ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted small">&ndash;</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted" style="max-width: 250px;">
                                    <?php if (! empty($sub['feedback'])): ?>
                                        <span class="text-truncate d-inline-block" style="max-width: 240px;" title="<?= esc($sub['feedback']) ?>">
                                            <?= esc($sub['feedback']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic">Belum ada catatan</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?= route_to('workspace.trainer.submission.review', (string) $sub['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                                        <i class="fas fa-check-double me-1"></i> Review & Nilai
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
