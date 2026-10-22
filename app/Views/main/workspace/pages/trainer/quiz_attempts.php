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
$itemAnalysis = $itemAnalysis ?? [];
$mostDifficultQuestion = $mostDifficultQuestion ?? null;
$easiestQuestion       = $easiestQuestion ?? null;
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

    <!-- Analisis Butir Soal & Tingkat Kesulitan (Item Analytics) -->
    <?php if (! empty($itemAnalysis)): ?>
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 pb-3 border-bottom mb-4">
                <div>
                    <h2 class="h5 fw-bold text-navy mb-1">
                        <i class="fas fa-chart-pie text-primary me-2"></i>Analisis Butir Soal & Tingkat Kesulitan
                    </h2>
                    <p class="text-muted small mb-0">
                        Evaluasi diagnostik pemahaman peserta: mengidentifikasi materi yang sudah dikuasai dan topik yang memerlukan penguatan.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 small fw-semibold">
                        <?= count($itemAnalysis) ?> Butir Soal Ujian
                    </span>
                </div>
            </div>

            <!-- Spotlight Card: Soal Paling Menantang vs Paling Dikuasai -->
            <?php if ($mostDifficultQuestion !== null || $easiestQuestion !== null): ?>
                <div class="row g-3 mb-4">
                    <?php if ($mostDifficultQuestion !== null && $mostDifficultQuestion['total_answered'] > 0): ?>
                        <div class="col-12 col-md-6">
                            <div class="card border-0 rounded-4 p-3 bg-danger-subtle border border-danger-subtle h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="rounded-circle bg-danger text-white p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="badge bg-danger text-white rounded-pill px-2 py-1 small fw-bold">
                                                Soal Paling Menantang (#<?= $mostDifficultQuestion['sort_order'] ?>)
                                            </span>
                                            <span class="fw-bold text-danger small">
                                                Tingkat Error: <?= $mostDifficultQuestion['error_rate'] ?>%
                                            </span>
                                        </div>
                                        <p class="small text-navy fw-semibold mb-1 line-clamp-2" style="font-size: 0.82rem;">
                                            <?= esc(strip_tags($mostDifficultQuestion['prompt'])) ?>
                                        </p>
                                        <small class="text-muted d-block" style="font-size: 0.72rem;">
                                            Hanya <?= $mostDifficultQuestion['accuracy_rate'] ?>% peserta (<?= $mostDifficultQuestion['total_correct'] ?> dari <?= $mostDifficultQuestion['total_answered'] ?>) yang menjawab benar.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($easiestQuestion !== null && $easiestQuestion['total_answered'] > 0): ?>
                        <div class="col-12 col-md-6">
                            <div class="card border-0 rounded-4 p-3 bg-success-subtle border border-success-subtle h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="rounded-circle bg-success text-white p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                        <i class="fas fa-check-double"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="badge bg-success text-white rounded-pill px-2 py-1 small fw-bold">
                                                Soal Paling Dikuasai (#<?= $easiestQuestion['sort_order'] ?>)
                                            </span>
                                            <span class="fw-bold text-success small">
                                                Ketepatan: <?= $easiestQuestion['accuracy_rate'] ?>%
                                            </span>
                                        </div>
                                        <p class="small text-navy fw-semibold mb-1 line-clamp-2" style="font-size: 0.82rem;">
                                            <?= esc(strip_tags($easiestQuestion['prompt'])) ?>
                                        </p>
                                        <small class="text-muted d-block" style="font-size: 0.72rem;">
                                            <?= $easiestQuestion['total_correct'] ?> dari <?= $easiestQuestion['total_answered'] ?> peserta berhasil menjawab dengan benar.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Tabel Butir Soal -->
            <div class="table-responsive border rounded-3 overflow-hidden">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.84rem;">
                    <thead class="table-light text-navy">
                        <tr>
                            <th style="width: 50px;" class="text-center">No.</th>
                            <th style="min-width: 280px;">Butir Soal & Pertanyaan</th>
                            <th style="min-width: 130px;">Tingkat Ketepatan</th>
                            <th style="min-width: 140px;" class="text-center">Tingkat Kesulitan</th>
                            <th style="min-width: 80px;" class="text-center">Total Respon</th>
                            <th style="min-width: 70px;" class="text-center text-success">Benar</th>
                            <th style="min-width: 70px;" class="text-center text-danger">Salah</th>
                            <th style="width: 80px;" class="text-end">Rincian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itemAnalysis as $qIdx => $item): ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= $item['sort_order'] ?></td>
                                <td>
                                    <div class="fw-semibold text-navy mb-1" style="line-height: 1.4;">
                                        <?= esc(strip_tags($item['prompt'])) ?>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.72rem;">
                                        Bobot: <strong><?= number_format($item['points'], 1) ?> poin</strong>
                                        <?php if (! empty($item['explanation'])): ?>
                                            &middot; <span class="text-primary"><i class="fas fa-info-circle me-1"></i>Tersedia Pembahasan</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1 rounded-pill" style="height: 6px;">
                                            <?php
                                            $barColor = 'bg-danger';
                                            if ($item['accuracy_rate'] >= 80.0) {
                                                $barColor = 'bg-success';
                                            } elseif ($item['accuracy_rate'] >= 50.0) {
                                                $barColor = 'bg-info';
                                            }
                                            ?>
                                            <div class="progress-bar <?= $barColor ?> rounded-pill" style="width: <?= $item['accuracy_rate'] ?>%"></div>
                                        </div>
                                        <strong class="small text-navy" style="font-size: 0.75rem;"><?= $item['accuracy_rate'] ?>%</strong>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= $item['difficulty_badge'] ?> rounded-pill px-3 py-1 small">
                                        <?= esc($item['difficulty']) ?>
                                    </span>
                                </td>
                                <td class="text-center text-muted small"><?= $item['total_answered'] ?> kali</td>
                                <td class="text-center fw-bold text-success small"><?= $item['total_correct'] ?></td>
                                <td class="text-center fw-bold text-danger small"><?= $item['total_incorrect'] ?></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2 py-1" data-bs-toggle="collapse" data-bs-target="#wsItemCollapse_<?= $item['id'] ?>" title="Buka Opsi & Kunci Jawaban">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr class="collapse bg-light-subtle" id="wsItemCollapse_<?= $item['id'] ?>">
                                <td colspan="8" class="p-3">
                                    <div class="card border-0 shadow-none bg-white rounded-3 p-3 mb-0">
                                        <h6 class="fw-bold text-navy small mb-2"><i class="fas fa-list-check text-primary me-2"></i>Pilihan Jawaban:</h6>
                                        <div class="d-flex flex-column gap-2 mb-3">
                                            <?php if (empty($item['options'])): ?>
                                                <span class="text-muted small">Tidak ada opsi pilihan ganda terdaftar.</span>
                                            <?php else: ?>
                                                <?php foreach ($item['options'] as $optIdx => $opt): ?>
                                                    <?php $isKey = (bool) ($opt['is_correct'] ?? false); ?>
                                                    <div class="d-flex align-items-center gap-2 p-2 rounded-2 <?= $isKey ? 'bg-success-subtle text-success border border-success-subtle fw-semibold' : 'bg-light text-navy' ?>" style="font-size: 0.8rem;">
                                                        <span class="badge <?= $isKey ? 'bg-success text-white' : 'bg-secondary text-white' ?> rounded-circle" style="width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">
                                                            <?= chr(65 + $optIdx) ?>
                                                        </span>
                                                        <span class="flex-grow-1"><?= esc($opt['option_text'] ?? '') ?></span>
                                                        <?php if ($isKey): ?>
                                                            <span class="badge bg-success text-white rounded-pill px-2 py-1 small"><i class="fas fa-check me-1"></i>Kunci Benar</span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (! empty($item['explanation'])): ?>
                                            <div class="p-2 rounded-2 bg-info-subtle border border-info-subtle small text-navy">
                                                <strong><i class="fas fa-lightbulb text-info me-1"></i>Penjelasan / Pembahasan:</strong>
                                                <p class="mb-0 mt-1"><?= nl2br(esc($item['explanation'])) ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

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
