<?php
/**
 * Trainer - Rincian Progres & Feedback Peserta
 * FiStudy UI DNA
 */
$enrollment            = $enrollment ?? [];
$lessonsProgress       = $lessonsProgress ?? [];
$quizAttempts          = $quizAttempts ?? [];
$assignmentSubmissions = $assignmentSubmissions ?? [];
$classId               = $classId ?? 0;
$className             = $className ?? 'Kelas';
?>

<div class="trainer-participant-detail-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-circle bg-primary text-white fw-bold fs-4" style="width: 56px; height: 56px;">
                    <?= strtoupper(substr($enrollment['name'] ?? 'U', 0, 1)) ?>
                </div>
                <div>
                    <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill small fw-semibold mb-1">
                        Profil & Riwayat Belajar Peserta
                    </span>
                    <h1 class="h3 fw-bold text-navy mb-0"><?= esc($enrollment['name'] ?? 'Peserta') ?></h1>
                    <p class="text-muted small mb-0">
                        Email: <?= esc($enrollment['email'] ?? '-') ?> &middot; Username: @<?= esc($enrollment['username'] ?? '-') ?> &middot; Kelas: <strong><?= esc($className) ?></strong>
                    </p>
                </div>
            </div>
            <div>
                <a href="<?= route_to('workspace.trainer.class.detail', (string) $classId) ?>?tab=peserta" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Peserta
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

    <div class="row g-4">
        <!-- Kolom Kiri: Checklist Lesson & Hasil Kuis -->
        <div class="col-12 col-lg-8">
            <!-- 1. Lesson Completion Checklist -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <h2 class="h5 fw-bold text-navy mb-3"><i class="fas fa-check-square text-primary me-2"></i>Status Penyelesaian Materi Pembelajaran</h2>
                <?php if (empty($lessonsProgress)): ?>
                    <p class="text-muted small mb-0">Belum ada materi terdaftar pada kelas ini.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-muted">
                                <tr>
                                    <th>Modul & Materi</th>
                                    <th>Tipe</th>
                                    <th>Status Peserta</th>
                                    <th>Waktu Selesai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lessonsProgress as $lp): ?>
                                    <tr>
                                        <td>
                                            <small class="text-muted d-block">Modul <?= esc($lp['module_order']) ?>: <?= esc($lp['module_title']) ?></small>
                                            <strong class="text-navy small"><?= esc($lp['lesson_title']) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-muted border rounded-pill px-2 py-0" style="font-size: 0.72rem;">
                                                <?= esc(strtoupper($lp['content_type'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($lp['status'] === 'completed'): ?>
                                                <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small fw-semibold">
                                                    <i class="fas fa-check me-1"></i> Selesai
                                                </span>
                                            <?php elseif ($lp['status'] === 'in_progress'): ?>
                                                <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1 small fw-semibold">
                                                    Sedang Belajar
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-muted border rounded-pill px-2 py-1 small">
                                                    Belum Mulai
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small text-muted">
                                            <?= esc(! empty($lp['completed_at']) ? date('d M Y, H:i', strtotime($lp['completed_at'])) : '-') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 2. Hasil Ujian & Kuis -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <h2 class="h5 fw-bold text-navy mb-3"><i class="fas fa-clipboard-check text-warning me-2"></i>Hasil Percobaan Kuis & Ujian</h2>
                <?php if (empty($quizAttempts)): ?>
                    <p class="text-muted small mb-0">Peserta belum pernah mengerjakan kuis atau ujian.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-muted">
                                <tr>
                                    <th>Kuis</th>
                                    <th>Tipe</th>
                                    <th>Percobaan</th>
                                    <th>Skor Nilai</th>
                                    <th>Status</th>
                                    <th>Tanggal Submit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($quizAttempts as $qa): ?>
                                    <tr>
                                        <td>
                                            <strong class="text-navy small"><?= esc($qa['quiz_title']) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-muted border rounded-pill px-2 py-1 small">
                                                <?= esc(str_replace('_', ' ', (string) $qa['quiz_type'])) ?>
                                            </span>
                                        </td>
                                        <td class="small text-muted">
                                            Ke-<?= (int) ($qa['attempt_number'] ?? 1) ?>
                                        </td>
                                        <td>
                                            <strong class="text-success"><?= esc($qa['score'] ?? '-') ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small">
                                                <?= esc(ucfirst($qa['status'])) ?>
                                            </span>
                                        </td>
                                        <td class="small text-muted">
                                            <?= esc(! empty($qa['submitted_at']) ? date('d M Y, H:i', strtotime($qa['submitted_at'])) : '-') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 3. Riwayat Tugas Praktik -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h2 class="h5 fw-bold text-navy mb-3"><i class="fas fa-tasks text-info me-2"></i>Penugasan & Praktik yang Dikumpulkan</h2>
                <?php if (empty($assignmentSubmissions)): ?>
                    <p class="text-muted small mb-0">Peserta belum mengumpulkan tugas apapun.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-muted">
                                <tr>
                                    <th>Tugas</th>
                                    <th>Status</th>
                                    <th>Nilai</th>
                                    <th>Feedback Pengajar</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignmentSubmissions as $as): ?>
                                    <tr>
                                        <td>
                                            <strong class="text-navy small d-block"><?= esc($as['assignment_title']) ?></strong>
                                            <small class="text-muted">Percobaan ke-<?= (int) ($as['attempt_number'] ?? 1) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge <?= $as['status'] === 'graded' ? 'bg-success' : 'bg-warning text-dark' ?> rounded-pill px-2 py-1 small">
                                                <?= esc(ucfirst($as['status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong class="text-success"><?= esc($as['score'] ?? '-') ?></strong>
                                        </td>
                                        <td class="small text-muted" style="max-width: 200px;">
                                            <?= esc($as['feedback'] ?? '-') ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= route_to('workspace.trainer.submission.review', (string) $as['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                Review
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

        <!-- Kolom Kanan: Catatan Pengajar untuk Peserta -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <h2 class="h6 fw-bold text-navy mb-3"><i class="fas fa-comment-dots text-primary me-2"></i>Catatan Evaluasi Pengajar</h2>
                <p class="text-muted small mb-3">Tuliskan catatan kemajuan belajar atau evaluasi kompetensi khusus untuk peserta ini.</p>

                <form method="POST" action="<?= route_to('workspace.trainer.participant.feedback', (string) $classId, (string) $enrollment['id']) ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <textarea name="trainer_note" class="form-control rounded-3" rows="5" placeholder="Contoh: Peserta menunjukkan pemahaman yang sangat baik pada Modul 1 dan 2. Disarankan memperdalam analisis HPS pada studi kasus..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill w-100 fw-semibold">
                        <i class="fas fa-save me-1"></i> Simpan Catatan Evaluasi
                    </button>
                </form>
            </div>

            <!-- Enrollment Status Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h2 class="h6 fw-bold text-navy mb-3"><i class="fas fa-id-card text-primary me-2"></i>Informasi Enrollment</h2>
                <div class="d-flex flex-column gap-2 text-muted small">
                    <div class="d-flex justify-content-between border-bottom pb-2">
                        <span>Status Pendaftaran:</span>
                        <strong class="text-navy"><?= esc(ucfirst($enrollment['status'] ?? '-')) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-2">
                        <span>Tanggal Terdaftar:</span>
                        <span><?= esc(! empty($enrollment['enrolled_at']) ? date('d M Y', strtotime($enrollment['enrolled_at'])) : '-') ?></span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-2">
                        <span>Tanggal Diaktifkan:</span>
                        <span><?= esc(! empty($enrollment['activated_at']) ? date('d M Y', strtotime($enrollment['activated_at'])) : '-') ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Kelulusan:</span>
                        <strong class="<?= ! empty($enrollment['passed_at']) ? 'text-success' : 'text-muted' ?>">
                            <?= ! empty($enrollment['passed_at']) ? 'Lulus (' . date('d M Y', strtotime($enrollment['passed_at'])) . ')' : 'Belum Selesai' ?>
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
