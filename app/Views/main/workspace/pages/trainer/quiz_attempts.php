<?php
/**
 * Trainer - Rekap Hasil Pengerjaan Ujian / Kuis
 * FiStudy UI DNA
 */
$classId      = $classId ?? 0;
$className    = $className ?? 'Kelas';
$class        = $class ?? [];
$quiz         = $quiz ?? [];
$attempts     = $attempts ?? [];
$passingGrade = (float) ($passingGrade ?? 70.0);
$stats        = $stats ?? [];
?>

<div class="trainer-quiz-attempts-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <?php if (($quiz['type'] ?? '') === 'pre_test'): ?>
                        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill small fw-semibold">
                            <i class="fas fa-play-circle me-1"></i> Pre-Test
                        </span>
                    <?php elseif (($quiz['type'] ?? '') === 'post_test'): ?>
                        <span class="badge bg-danger px-3 py-1 rounded-pill small fw-semibold">
                            <i class="fas fa-award me-1"></i> Post-Test Kelulusan
                        </span>
                    <?php else: ?>
                        <span class="badge bg-info text-dark px-3 py-1 rounded-pill small fw-semibold">
                            <i class="fas fa-question-circle me-1"></i> Quiz Modul
                        </span>
                    <?php endif; ?>
                    <span class="badge bg-light text-navy border px-3 py-1 rounded-pill small">
                        Passing Grade: <strong><?= number_format($passingGrade, 1) ?></strong>
                    </span>
                    <span class="badge bg-light text-navy border px-3 py-1 rounded-pill small">
                        Batas Attempt: <strong><?= (int) ($quiz['attempt_limit'] ?? 1) ?> kali</strong>
                    </span>
                </div>

                <h1 class="h3 fw-bold text-navy mb-1"><?= esc($quiz['title'] ?? 'Hasil Pengerjaan Ujian') ?></h1>
                <p class="text-muted small mb-0">
                    Kelas: <strong><?= esc($className) ?></strong> &middot; Kode: <code class="text-primary"><?= esc($class['code'] ?? '-') ?></code>
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= route_to('workspace.trainer.class.detail', (string) $classId) ?>?tab=assessment" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Assessment
                </a>
                <a href="<?= route_to('workspace.trainer.quiz.edit', (string) $classId, (string) $quiz['id']) ?>" class="btn btn-outline-primary rounded-pill px-3">
                    <i class="fas fa-edit me-1"></i> Kelola Butir Soal
                </a>
            </div>
        </div>
    </div>

    <!-- KPI Summary Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 col-xl">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-primary-subtle text-primary fs-4 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.75rem;">Total Percobaan</span>
                        <strong class="h4 fw-bold text-navy mb-0"><?= (int) ($stats['totalAttempts'] ?? 0) ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3 col-xl">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-info-subtle text-info fs-4 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.75rem;">Peserta Mengikuti</span>
                        <strong class="h4 fw-bold text-navy mb-0"><?= (int) ($stats['uniqueUsers'] ?? 0) ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3 col-xl">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-warning-subtle text-warning fs-4 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.75rem;">Rata-rata Nilai</span>
                        <strong class="h4 fw-bold text-navy mb-0"><?= number_format((float) ($stats['avgScore'] ?? 0), 1) ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3 col-xl">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-success-subtle text-success fs-4 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.75rem;">Nilai Tertinggi</span>
                        <strong class="h4 fw-bold text-navy mb-0"><?= number_format((float) ($stats['maxScore'] ?? 0), 1) ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3 col-xl">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-primary-subtle text-primary fs-4 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.75rem;">Memenuhi Passing</span>
                        <strong class="h4 fw-bold text-navy mb-0"><?= (int) ($stats['passedCount'] ?? 0) ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Attempts Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h2 class="h5 fw-bold text-navy mb-1"><i class="fas fa-list-ol text-primary me-2"></i>Rincian Pengerjaan Peserta</h2>
                <p class="text-muted small mb-0">Catatan setiap submisi ujian beserta skor yang didapat.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-navy border px-3 py-2 rounded-pill small">
                    Total: <?= count($attempts) ?> Submisi
                </span>
            </div>
        </div>

        <?php if (empty($attempts)): ?>
            <div class="text-center py-5 border rounded-4 bg-light">
                <i class="fas fa-user-clock fa-3x text-muted mb-3 opacity-25"></i>
                <h3 class="h6 fw-bold text-navy mb-1">Belum Ada Peserta yang Mengerjakan</h3>
                <p class="text-muted small mb-0">Peserta yang telah terdaftar di kelas ini belum memulai pengerjaan ujian.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Peserta</th>
                            <th>Attempt Ke-</th>
                            <th>Mulai & Selesai</th>
                            <th>Durasi</th>
                            <th>Nilai</th>
                            <th>Status Kelulusan</th>
                            <th class="text-end">Status Attempt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attempts as $idx => $att): ?>
                            <?php
                            $score = (float) ($att['score'] ?? 0.0);
                            $isPassed = $score >= $passingGrade;

                            $durationStr = '-';
                            if (! empty($att['started_at']) && ! empty($att['submitted_at'])) {
                                $startT = strtotime($att['started_at']);
                                $endT   = strtotime($att['submitted_at']);
                                $diff   = max(0, $endT - $startT);
                                $mins   = floor($diff / 60);
                                $secs   = $diff % 60;
                                $durationStr = $mins . 'm ' . $secs . 's';
                            }
                            ?>
                            <tr>
                                <td class="small text-muted"><?= $idx + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle bg-primary-subtle text-primary fw-bold" style="width: 36px; height: 36px; font-size: 13px;">
                                            <?= strtoupper(substr($att['participant_name'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong class="text-navy small d-block"><?= esc($att['participant_name'] ?? '-') ?></strong>
                                            <span class="text-muted" style="font-size: 0.75rem;"><?= esc($att['participant_email'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-navy border rounded-pill px-2 py-1 small">
                                        Percobaan ke-<?= (int) ($att['attempt_number'] ?? 1) ?>
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    <div><i class="far fa-play-circle text-primary me-1"></i><?= ! empty($att['started_at']) ? date('d/m/Y H:i', strtotime($att['started_at'])) : '-' ?></div>
                                    <div><i class="far fa-check-circle text-success me-1"></i><?= ! empty($att['submitted_at']) ? date('d/m/Y H:i', strtotime($att['submitted_at'])) : '-' ?></div>
                                </td>
                                <td class="small text-muted">
                                    <i class="far fa-clock me-1"></i><?= esc($durationStr) ?>
                                </td>
                                <td>
                                    <span class="h6 fw-bold mb-0 <?= $isPassed ? 'text-success' : 'text-danger' ?>">
                                        <?= number_format($score, 2) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($att['status'] === 'graded' || $att['status'] === 'submitted'): ?>
                                        <?php if ($isPassed): ?>
                                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 small">
                                                <i class="fas fa-check-circle me-1"></i> Memenuhi Passing
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1 small">
                                                <i class="fas fa-times-circle me-1"></i> Belum Memenuhi
                                            </span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1 small">
                                            Sedang Mengerjakan
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <span class="badge <?= $att['status'] === 'graded' ? 'bg-primary' : ($att['status'] === 'submitted' ? 'bg-info text-dark' : 'bg-secondary') ?> rounded-pill px-3 py-1 small">
                                        <?= esc(ucfirst($att['status'] ?? 'in_progress')) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
