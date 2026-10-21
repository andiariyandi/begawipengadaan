<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?php
$quiz              = $quiz ?? [];
$questions         = $questions ?? [];
$optionsByQuestion = $optionsByQuestion ?? [];
$attempt           = $attempt ?? null;
$latest            = $latest ?? null;
$attemptCount      = (int) ($attemptCount ?? 0);
$attemptLimit      = (int) ($attemptLimit ?? 1);
$remaining         = (int) ($remaining ?? max(0, $attemptLimit - $attemptCount));
$classId           = (int) ($quiz['class_id'] ?? 0);
$totalQ            = count($questions);
$durationMinutes   = (int) ($durationMinutes ?? ($quiz['duration_minutes'] ?? 0));
$remainingSeconds  = $remainingSeconds ?? null;
$deadlineTimestamp = $deadlineTimestamp ?? null;

$allAttempts       = $allAttempts ?? [];
$highestScore      = $highestScore ?? null;
$bestAttempt       = $bestAttempt ?? null;
$reviewAttempt     = $reviewAttempt ?? null;
$answersByQuestion = $answersByQuestion ?? [];
$passingGrade      = (float) ($passingGrade ?? 70.0);

$typeBadge = match ((string) ($quiz['type'] ?? 'lesson_quiz')) {
    'pre_test'  => ['label' => 'Pre-Test',   'class' => 'bg-warning text-dark'],
    'post_test' => ['label' => 'Post-Test',  'class' => 'bg-danger text-white'],
    default     => ['label' => 'Quiz Modul', 'class' => 'bg-primary text-white'],
};
?>

<style>
.ws-quiz-timer-card {
    background: #ffffff;
    border-left: 5px solid #082f5c !important;
    transition: all 0.3s ease;
}
.ws-timer-icon-circle {
    width: 48px;
    height: 48px;
    background: rgba(8, 47, 92, 0.08);
    color: #082f5c;
    transition: all 0.3s ease;
}
.ws-palette-timer-box {
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
}
.timer-state-warning.ws-quiz-timer-card {
    border-left-color: #f59e0b !important;
    background: #fffbeb;
}
.timer-state-warning .ws-timer-icon-circle {
    background: #fef3c7;
    color: #d97706;
}
.timer-state-warning #bannerTimerDisplay,
.timer-state-warning #paletteTimerDisplay {
    color: #b45309 !important;
}
.timer-state-warning.ws-palette-timer-box {
    border-color: #fcd34d;
    background: #fffbeb;
}
.timer-state-danger.ws-quiz-timer-card {
    border-left-color: #ef4444 !important;
    background: #fef2f2;
    animation: quizTimerPulse 1.2s infinite;
}
.timer-state-danger .ws-timer-icon-circle {
    background: #fee2e2;
    color: #dc2626;
}
.timer-state-danger #bannerTimerDisplay,
.timer-state-danger #paletteTimerDisplay {
    color: #dc2626 !important;
}
.timer-state-danger.ws-palette-timer-box {
    border-color: #fca5a5;
    background: #fef2f2;
    animation: quizTimerPulse 1.2s infinite;
}
@keyframes quizTimerPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.015); }
    100% { transform: scale(1); }
}

/* Single Question Card Focus Mode */
.ws-quiz-q-card {
    display: none;
    border-radius: 18px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
}
.ws-quiz-q-card.active-card {
    display: block !important;
    animation: quizCardFadeIn 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes quizCardFadeIn {
    from {
        opacity: 0;
        transform: translateY(6px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<!-- FLOATING STICKY BANNER FOR ZEN / FOCUS MODE -->
<div class="ws-focus-banner" id="wsFocusFloatingBannerQuiz">
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-warning text-dark rounded-pill px-2 py-0.5 fw-bold">
            <i class="fas fa-eye me-1"></i> Mode Zen / Ujian Fokus
        </span>
        <span class="small text-white text-truncate fw-medium" style="max-width: 450px;">
            <?= esc($quiz['title'] ?? 'Ujian Komprehensif') ?>
        </span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-sm btn-light rounded-pill px-3 py-1 fw-bold text-navy" id="btnExitFocusModeQuiz">
            <i class="fas fa-compress me-1"></i> Keluar Mode Zen (Esc)
        </button>
    </div>
</div>

<!-- 1. QUIZ HERO & INFO CARD (FISTUDY DNA) -->
<div class="ws-page-header">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <a href="<?= route_to('workspace.participant.class.detail', (string) $classId) ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 mb-2">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Kelas
            </a>
            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 mb-2 ms-1" id="btnToggleFocusModeQuiz">
                <i class="fas fa-expand me-1"></i> Mode Zen / Fokus
            </button>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                <span class="badge <?= $typeBadge['class'] ?> text-uppercase px-3 py-1 rounded-pill fw-bold">
                    <?= esc($typeBadge['label']) ?>
                </span>
                <?php if ($durationMinutes > 0): ?>
                    <span class="badge bg-warning-subtle text-dark border border-warning px-3 py-1 rounded-pill fw-semibold">
                        <i class="bi bi-clock-history me-1 text-warning"></i> Waktu: <?= $durationMinutes ?> Menit
                    </span>
                <?php else: ?>
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill">
                        <i class="bi bi-infinity me-1"></i> Tanpa Batas Waktu
                    </span>
                <?php endif; ?>
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill">
                    Percobaan: <strong class="text-dark"><?= $attemptCount ?></strong> dari <strong class="text-dark"><?= $attemptLimit ?></strong>
                </span>
                <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill">
                    Total: <strong class="text-primary"><?= $totalQ ?> Soal</strong>
                </span>
            </div>
            <h1 class="h3 fw-bold mb-1" style="color: #082f5c;"><?= esc($quiz['title'] ?? 'Evaluasi Pembelajaran') ?></h1>
            <p class="text-muted small mb-0">
                Kelas: <strong class="text-dark"><?= esc($quiz['class_name'] ?? '-') ?></strong> &middot;
                Kode: <code class="text-primary fw-semibold"><?= esc($quiz['class_code'] ?? '-') ?></code>
            </p>
        </div>

        <?php if ($highestScore !== null || ($latest !== null && (string) ($latest['status'] ?? '') === 'graded')): ?>
            <?php
                $dispScore = $highestScore !== null ? (float) $highestScore : (float) ($latest['score'] ?? 0);
                $isPass = $dispScore >= $passingGrade;
            ?>
            <div class="p-3 px-4 rounded-4 border bg-light text-center shadow-xs">
                <span class="badge <?= $isPass ? 'bg-success' : 'bg-danger' ?> rounded-pill mb-1 px-3 py-1 fw-bold" style="font-size: 10px;">
                    <i class="bi <?= $isPass ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill' ?> me-1"></i>
                    <?= $isPass ? 'Lulus Passing Grade' : 'Belum Memenuhi' ?>
                </span>
                <span class="d-block text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Nilai Tertinggi (Digunakan)</span>
                <strong class="display-6 fw-bold <?= $isPass ? 'text-success' : 'text-danger' ?>">
                    <?= $dispScore ?>
                </strong>
                <span class="d-block text-muted" style="font-size: 11px;">Skala 100 &middot; Min. <?= (float) $passingGrade ?></span>
                <?php if (count($allAttempts) > 1 && $latest !== null): ?>
                    <small class="text-muted d-block mt-1" style="font-size: 10px;">
                        Percobaan Terakhir: <strong><?= (float) ($latest['score'] ?? 0) ?></strong>
                    </small>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (! empty($quiz['instructions'])): ?>
        <div class="p-3 px-4 rounded-3 text-muted small border-start border-4 border-primary bg-light mt-2">
            <strong class="d-block text-dark mb-1"><i class="bi bi-info-circle-fill text-primary me-1"></i> Petunjuk & Panduan Ujian:</strong>
            <?= nl2br(esc($quiz['instructions'])) ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($attempt !== null && (string) ($attempt['status'] ?? '') === 'started' && $questions !== []): ?>
    <!-- 2. ACTIVE QUIZ ATTEMPT INTERACTIVE ENGINE -->
    <div class="row g-3 g-md-4">
        <!-- LEFT: QUESTION LIST & CARDS -->
        <div class="col-lg-8 col-xl-8">
            <form id="quizAttemptForm" method="post" action="<?= route_to('workspace.participant.quiz.submit', (int) $attempt['id']) ?>">
                <?= csrf_field() ?>

                <?php if ($durationMinutes > 0 && $remainingSeconds !== null): ?>
                    <!-- STICKY TIMER BANNER -->
                    <div class="card border-0 rounded-4 shadow-sm mb-3 ws-quiz-timer-card sticky-top" id="quizStickyTimerBanner" style="top: 70px; z-index: 1020;">
                        <div class="card-body p-2 p-md-3 px-md-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="ws-timer-icon-circle rounded-circle d-flex align-items-center justify-content-center" id="timerIconCircle">
                                    <i class="bi bi-clock-history fs-4"></i>
                                </div>
                                <div>
                                    <span class="d-block text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Sisa Waktu Pengerjaan</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fs-4 fs-md-3 fw-bold font-monospace text-dark" id="bannerTimerDisplay">--:--</span>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle small" id="bannerTimerStatus">Timer Berjalan</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end text-muted small d-none d-md-block">
                                <span>Alokasi Waktu: <strong><?= $durationMinutes ?> Menit</strong></span><br>
                                <span class="text-xs text-secondary-light">Jawaban tersimpan otomatis saat reload</span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="d-flex flex-wrap align-items-center justify-content-between p-2 p-md-3 px-md-4 bg-white border rounded-3 rounded-md-4 shadow-sm mb-3 gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square text-primary fs-5"></i>
                        <span class="fw-bold text-dark">Lembar Pengerjaan Soal (Mode Fokus)</span>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 small fw-semibold" id="topFocusQuestionIndicator">
                            Soal 1 dari <?= $totalQ ?>
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1">
                            Percobaan #<?= (int) ($attempt['attempt_number'] ?? 1) ?>
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 btn-nav-review" title="Tinjau daftar soal">
                            <i class="bi bi-grid-3x3 me-1"></i> Tinjau Ringkasan
                        </button>
                    </div>
                </div>

                <!-- LIST SOAL (1 SOAL PER TAMPILAN - FOCUS MODE) -->
                <?php foreach ($questions as $index => $q): ?>
                    <?php
                        $qIndex = $index + 1;
                        $opts = $optionsByQuestion[(int) $q['id']] ?? [];
                        $isFirst = ($qIndex === 1);
                    ?>
                    <div class="ws-quiz-q-card p-3 p-md-4 mb-3 shadow-sm <?= $isFirst ? 'active-card' : '' ?>" id="question-card-<?= $qIndex ?>" data-question-index="<?= $qIndex ?>" style="<?= $isFirst ? 'display: block;' : 'display: none;' ?>">
                        <!-- Card Header Soal: Nomor X dari Total & Status -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pb-2 mb-3 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary text-white rounded-pill px-2 py-1 fw-bold">
                                    Soal <?= $qIndex ?> dari <?= $totalQ ?>
                                </span>
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 fw-semibold q-status-pill" id="q-status-badge-<?= $qIndex ?>">
                                    <i class="bi bi-circle me-1"></i> Belum Terjawab
                                </span>
                            </div>
                            <span class="badge bg-light text-dark border rounded-pill px-2 py-1 fw-semibold">
                                <i class="bi bi-award me-1 text-primary"></i> <?= (float) $q['points'] ?> Poin
                            </span>
                        </div>

                        <!-- Prompt Soal -->
                        <div class="fw-semibold text-dark lh-base mb-3" style="font-size: 0.95rem;">
                            <?= nl2br(esc($q['prompt'])) ?>
                        </div>

                        <!-- Pilihan Jawaban (Radio Options) -->
                        <div class="options-container mb-3">
                            <?php foreach ($opts as $i => $opt): ?>
                                <label class="ws-quiz-option mb-2" for="opt-<?= (int) $q['id'] ?>-<?= (int) $opt['id'] ?>">
                                    <input class="form-check-input quiz-radio" type="radio" name="answers[<?= (int) $q['id'] ?>]" id="opt-<?= (int) $q['id'] ?>-<?= (int) $opt['id'] ?>" value="<?= (int) $opt['id'] ?>" data-q-index="<?= $qIndex ?>">
                                    <span class="ws-quiz-opt-letter"><?= chr(65 + (int) $i) ?></span>
                                    <span class="text-dark fw-medium lh-base flex-grow-1" style="font-size: 0.88rem;">
                                        <?= esc($opt['option_text']) ?>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <!-- Card Footer: Navigasi Antar Soal (Focus Mode Controls) -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between pt-3 border-top gap-2 mt-3">
                            <div>
                                <?php if ($qIndex > 1): ?>
                                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3 btn-nav-step" data-target="<?= $qIndex - 1 ?>">
                                        <i class="bi bi-arrow-left me-1"></i> Sebelumnya
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3 disabled" disabled style="opacity: 0.4;">
                                        <i class="bi bi-arrow-left me-1"></i> Sebelumnya
                                    </button>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-outline-info rounded-pill px-3 btn-nav-review" title="Tinjau status seluruh soal">
                                    <i class="bi bi-grid-3x3-gap me-1"></i> Tinjau Ringkasan
                                </button>
                                <?php if ($qIndex < $totalQ): ?>
                                    <button type="button" class="btn btn-primary rounded-pill px-3 px-md-4 btn-nav-step fw-semibold" data-target="<?= $qIndex + 1 ?>">
                                        Selanjutnya <i class="bi bi-arrow-right ms-1"></i>
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-success rounded-pill px-3 px-md-4 btn-nav-review fw-bold shadow-sm">
                                        <i class="bi bi-check2-all me-1"></i> Selesaikan Ujian
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Form Bottom Action Bar -->
                <div class="card border-0 rounded-4 shadow-sm p-3 p-md-4 bg-white mb-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <a href="<?= route_to('workspace.participant.study', (string) $classId) ?>" class="btn btn-outline-secondary rounded-pill px-3">
                            <i class="bi bi-x-circle me-1"></i> Batal & Kembali
                        </a>
                        <button type="button" id="btnMainSubmit" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-bold">
                            <i class="bi bi-send-check me-1"></i> Kirim Jawaban Ujian &raquo;
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- RIGHT: QUESTION PALETTE & PROGRESS SIDEBAR (STICKY) -->
        <div class="col-lg-4 col-xl-4">
            <div class="ws-quiz-palette-card p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <h2 class="h6 fw-bold mb-0" style="color: #082f5c;">
                        <i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i> Navigasi Soal
                    </h2>
                    <span class="badge bg-light text-muted border small"><?= $totalQ ?> Butir</span>
                </div>

                <?php if ($durationMinutes > 0 && $remainingSeconds !== null): ?>
                    <!-- SIDEBAR PALETTE TIMER BOX -->
                    <div class="p-3 rounded-4 mb-3 text-center ws-palette-timer-box" id="paletteTimerBox">
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                            <i class="bi bi-stopwatch text-primary fs-5" id="paletteTimerIcon"></i>
                            <span class="small fw-semibold text-muted text-uppercase" style="font-size: 11px;">Sisa Waktu</span>
                        </div>
                        <div class="display-6 fw-bold font-monospace text-dark" id="paletteTimerDisplay">--:--</div>
                        <small class="text-muted d-block mt-1" style="font-size: 11px;" id="paletteTimerNotice">Otomatis submit saat 00:00</small>
                    </div>
                <?php endif; ?>

                <!-- Real-time Counters -->
                <div class="row g-2 mb-3 text-center">
                    <div class="col-6">
                        <div class="p-2 px-3 rounded-3 bg-success-subtle border border-success-subtle">
                            <span class="d-block text-success small fw-semibold" style="font-size: 11px;">Terjawab</span>
                            <span class="fs-5 fw-bold text-success" id="statAnswered">0</span>
                            <span class="text-muted small">/ <?= $totalQ ?></span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 px-3 rounded-3 bg-secondary-subtle border border-secondary-subtle">
                            <span class="d-block text-secondary small fw-semibold" style="font-size: 11px;">Belum Terjawab</span>
                            <span class="fs-5 fw-bold text-secondary" id="statUnanswered"><?= $totalQ ?></span>
                            <span class="text-muted small">soal</span>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between small text-muted mb-1">
                        <span>Kelengkapan Jawaban</span>
                        <strong id="statPercent" class="text-dark">0%</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" id="quizProgressBar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <!-- Palette Grid Numbers -->
                <p class="small text-muted mb-2">Klik nomor untuk lompat langsung ke butir soal:</p>
                <div class="ws-quiz-palette-grid mb-3">
                    <?php for ($idx = 1; $idx <= $totalQ; $idx++): ?>
                        <button type="button" class="ws-quiz-palette-btn <?= $idx === 1 ? 'current' : '' ?>" id="palette-btn-<?= $idx ?>" data-target="<?= $idx ?>" title="Soal <?= $idx ?>">
                            <?= $idx ?>
                        </button>
                    <?php endfor; ?>
                </div>

                <!-- Legend Status -->
                <div class="p-2 px-3 rounded bg-light border small text-muted mb-3 d-flex flex-wrap align-items-center justify-content-around gap-2" style="font-size: 11px;">
                    <span class="d-flex align-items-center gap-1">
                        <span class="d-inline-block rounded-circle bg-success" style="width: 8px; height: 8px;"></span> Terjawab
                    </span>
                    <span class="d-flex align-items-center gap-1">
                        <span class="d-inline-block rounded-circle bg-secondary" style="width: 8px; height: 8px;"></span> Belum
                    </span>
                    <span class="d-flex align-items-center gap-1">
                        <span class="d-inline-block rounded border border-primary" style="width: 8px; height: 8px;"></span> Aktif
                    </span>
                </div>

                <!-- Action Shortcut: Lompat ke Soal Belum Terjawab -->
                <button type="button" id="btnJumpUnanswered" class="btn btn-outline-warning w-100 rounded-pill py-2 small fw-semibold text-dark mb-2">
                    <i class="bi bi-arrow-down-right-circle me-1"></i> Lompat ke Soal Belum Terjawab
                </button>

                <!-- Action Shortcut: Selesaikan & Kirim Ujian -->
                <button type="button" id="btnSidebarSubmit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">
                    <i class="bi bi-send-check me-1"></i> Selesaikan & Kirim
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL KONFIRMASI PENGUMPULAN UJIAN -->
    <div class="modal fade" id="quizConfirmModal" tabindex="-1" aria-labelledby="quizConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-navy" id="quizConfirmModalLabel">
                        <i class="bi bi-question-circle-fill text-primary me-2"></i> Konfirmasi Pengiriman Ujian
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div id="confirmAlertContainer"></div>
                    <p class="mb-0 text-dark">
                        Setelah lembar jawaban dikirimkan, hasil pengerjaan Anda akan langsung dihitung dan dicatat ke sistem.
                    </p>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        Periksa Kembali
                    </button>
                    <button type="button" id="btnFinalConfirmSubmit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        Ya, Kirim Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- CLIENT SCRIPT: INTERACTIVE QUIZ PALETTE & VALIDATION -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalQuestions = <?= $totalQ ?>;
        const form = document.getElementById('quizAttemptForm');
        const radios = document.querySelectorAll('.quiz-radio');
        const statAnswered = document.getElementById('statAnswered');
        const statUnanswered = document.getElementById('statUnanswered');
        const statPercent = document.getElementById('statPercent');
        const progressBar = document.getElementById('quizProgressBar');
        const btnMainSubmit = document.getElementById('btnMainSubmit');
        const btnSidebarSubmit = document.getElementById('btnSidebarSubmit');
        const btnJumpUnanswered = document.getElementById('btnJumpUnanswered');
        const confirmModalEl = document.getElementById('quizConfirmModal');
        if (confirmModalEl && confirmModalEl.parentElement !== document.body) {
            document.body.appendChild(confirmModalEl);
        }
        const btnFinalConfirmSubmit = document.getElementById('btnFinalConfirmSubmit');
        const confirmAlertContainer = document.getElementById('confirmAlertContainer');
        const bsModal = new bootstrap.Modal(confirmModalEl);

        const attemptId = <?= (int) ($attempt['id'] ?? 0) ?>;
        const storageKey = 'bp_quiz_answers_' + attemptId;

        // 1. Restore previously selected answers from localStorage (resilience against reload/accidental refresh)
        if (attemptId > 0) {
            try {
                const savedRaw = localStorage.getItem(storageKey);
                if (savedRaw) {
                    const savedAnswers = JSON.parse(savedRaw);
                    if (typeof savedAnswers === 'object' && savedAnswers !== null) {
                        Object.keys(savedAnswers).forEach(function(qId) {
                            const optId = savedAnswers[qId];
                            const radio = document.querySelector(`input.quiz-radio[name="answers[${qId}]"][value="${optId}"]`);
                            if (radio) {
                                radio.checked = true;
                            }
                        });
                    }
                }
            } catch (e) {
                console.warn('Gagal membaca jawaban localStorage', e);
            }
        }

        function persistAnswers() {
            if (attemptId <= 0) return;
            const currentAnswers = {};
            radios.forEach(function(r) {
                if (r.checked) {
                    const nameMatch = r.name.match(/answers\[(\d+)\]/);
                    if (nameMatch) {
                        currentAnswers[nameMatch[1]] = r.value;
                    }
                }
            });
            try {
                localStorage.setItem(storageKey, JSON.stringify(currentAnswers));
            } catch (e) {
                console.warn('Gagal menyimpan jawaban ke localStorage', e);
            }
        }

        function clearPersistedAnswers() {
            try {
                localStorage.removeItem(storageKey);
                localStorage.removeItem('bp_quiz_active_q_' + attemptId);
            } catch (e) {}
        }

        let activeQuestionIndex = 1;

        // Function to show ONLY 1 requested question card and hide all others (Focus Mode)
        function showQuestion(index) {
            index = parseInt(index, 10);
            if (isNaN(index) || index < 1 || index > totalQuestions) return;

            const targetCard = document.getElementById('question-card-' + index);
            if (!targetCard) return;

            activeQuestionIndex = index;

            // 1. Hide all question cards, display only target card
            document.querySelectorAll('.ws-quiz-q-card').forEach(c => {
                c.classList.remove('active-card');
                c.style.display = 'none';
            });
            targetCard.classList.add('active-card');
            targetCard.style.display = 'block';

            // 2. Update palette active button
            document.querySelectorAll('.ws-quiz-palette-btn').forEach(btn => btn.classList.remove('current'));
            const currentPaletteBtn = document.getElementById('palette-btn-' + index);
            if (currentPaletteBtn) currentPaletteBtn.classList.add('current');

            // 3. Update top indicator badge if present
            const topIndicator = document.getElementById('topFocusQuestionIndicator');
            if (topIndicator) {
                topIndicator.textContent = `Soal ${index} dari ${totalQuestions}`;
            }

            // 4. Save active question to localStorage for reload resilience
            try {
                localStorage.setItem('bp_quiz_active_q_' + attemptId, index);
            } catch (e) {}

            // 5. Scroll smoothly up to the card
            targetCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Palette buttons click
        document.querySelectorAll('.ws-quiz-palette-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const target = parseInt(this.getAttribute('data-target'), 10);
                if (!isNaN(target)) showQuestion(target);
            });
        });

        // Step buttons click (Prev / Next inside question cards)
        document.querySelectorAll('.btn-nav-step').forEach(btn => {
            btn.addEventListener('click', function() {
                const target = parseInt(this.getAttribute('data-target'), 10);
                if (!isNaN(target)) showQuestion(target);
            });
        });

        // Review button
        document.querySelectorAll('.btn-nav-review').forEach(btn => {
            btn.addEventListener('click', function() {
                openSubmitConfirmation();
            });
        });

        // Radio change handler to update status and palette
        function updateStats() {
            let answeredCount = 0;
            const answeredIndices = new Set();

            radios.forEach(radio => {
                const qIndex = parseInt(radio.getAttribute('data-q-index'), 10);
                if (radio.checked) {
                    answeredIndices.add(qIndex);
                }
            });

            answeredCount = answeredIndices.size;
            const unansweredCount = totalQuestions - answeredCount;
            const percent = totalQuestions > 0 ? Math.round((answeredCount / totalQuestions) * 100) : 0;

            if (statAnswered) statAnswered.textContent = answeredCount;
            if (statUnanswered) statUnanswered.textContent = unansweredCount;
            if (statPercent) statPercent.textContent = percent + '%';
            if (progressBar) {
                progressBar.style.width = percent + '%';
                progressBar.setAttribute('aria-valuenow', percent);
            }

            // Update each question's status badge & palette button
            for (let i = 1; i <= totalQuestions; i++) {
                const badge = document.getElementById('q-status-badge-' + i);
                const paletteBtn = document.getElementById('palette-btn-' + i);

                if (answeredIndices.has(i)) {
                    if (badge) {
                        badge.className = 'badge bg-success text-white rounded-pill px-3 py-2 fw-semibold q-status-pill';
                        badge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Terjawab';
                    }
                    if (paletteBtn) {
                        paletteBtn.classList.add('answered');
                    }
                } else {
                    if (badge) {
                        badge.className = 'badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2 fw-semibold q-status-pill';
                        badge.innerHTML = '<i class="bi bi-circle me-1"></i> Belum Terjawab';
                    }
                    if (paletteBtn) {
                        paletteBtn.classList.remove('answered');
                    }
                }
            }

            return {
                answered: answeredCount,
                unanswered: unansweredCount,
                answeredIndices: answeredIndices
            };
        }

        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                updateStats();
                persistAnswers();
            });
        });

        // Initial stat run (reflects any restored answers from localStorage)
        updateStats();

        // Initialize Focus Mode with active question
        let initialQIndex = 1;
        try {
            const savedQ = parseInt(localStorage.getItem('bp_quiz_active_q_' + attemptId), 10);
            if (!isNaN(savedQ) && savedQ >= 1 && savedQ <= totalQuestions) {
                initialQIndex = savedQ;
            }
        } catch (e) {}
        showQuestion(initialQIndex);

        // Jump to first unanswered question
        if (btnJumpUnanswered) {
            btnJumpUnanswered.addEventListener('click', function() {
                const stats = updateStats();
                for (let i = 1; i <= totalQuestions; i++) {
                    if (!stats.answeredIndices.has(i)) {
                        showQuestion(i);
                        return;
                    }
                }
                alert('Hebat! Seluruh soal telah Anda jawab.');
            });
        }

        // Open Confirmation Modal
        function openSubmitConfirmation() {
            const stats = updateStats();
            confirmAlertContainer.innerHTML = '';

            if (stats.unanswered > 0) {
                const missingList = [];
                for (let i = 1; i <= totalQuestions; i++) {
                    if (!stats.answeredIndices.has(i)) missingList.push(i);
                }

                confirmAlertContainer.innerHTML = `
                    <div class="alert alert-warning rounded-3 border-0 mb-3">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-exclamation-triangle-fill fs-5 text-warning flex-shrink-0 mt-1"></i>
                            <div>
                                <strong class="d-block text-dark">Perhatian: Masih ada ${stats.unanswered} soal belum terjawab!</strong>
                                <span class="small text-muted">Nomor soal: <strong>#${missingList.join(', #')}</strong></span>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                confirmAlertContainer.innerHTML = `
                    <div class="alert alert-success rounded-3 border-0 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                            <strong class="text-dark">Luar biasa! Seluruh ${totalQuestions} soal telah lengkap terjawab.</strong>
                        </div>
                    </div>
                `;
            }

            bsModal.show();
        }

        if (btnMainSubmit) {
            btnMainSubmit.addEventListener('click', openSubmitConfirmation);
        }
        if (btnSidebarSubmit) {
            btnSidebarSubmit.addEventListener('click', openSubmitConfirmation);
        }

        if (btnFinalConfirmSubmit) {
            btnFinalConfirmSubmit.addEventListener('click', function() {
                btnFinalConfirmSubmit.disabled = true;
                btnFinalConfirmSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengirimkan...';
                clearPersistedAnswers();
                form.submit();
            });
        }

        <?php if ($durationMinutes > 0 && $remainingSeconds !== null): ?>
        // COUNTDOWN TIMER ENGINE (Reload & Expiration resilient)
        (function() {
            const serverRemainingSec = <?= (int) $remainingSeconds ?>;
            const clientEndTime = Date.now() + (serverRemainingSec * 1000);
            const bannerDisplay = document.getElementById('bannerTimerDisplay');
            const paletteDisplay = document.getElementById('paletteTimerDisplay');
            const bannerCard = document.getElementById('quizStickyTimerBanner');
            const paletteBox = document.getElementById('paletteTimerBox');
            const bannerStatus = document.getElementById('bannerTimerStatus');
            let hasAutoSubmitted = false;

            function formatTime(totalSeconds) {
                const h = Math.floor(totalSeconds / 3600);
                const m = Math.floor((totalSeconds % 3600) / 60);
                const s = totalSeconds % 60;
                const pad = (n) => String(n).padStart(2, '0');
                if (h > 0) {
                    return `${pad(h)}:${pad(m)}:${pad(s)}`;
                }
                return `${pad(m)}:${pad(s)}`;
            }

            function tickTimer() {
                if (hasAutoSubmitted) return;
                const now = Date.now();
                const distance = Math.max(0, Math.floor((clientEndTime - now) / 1000));
                const formatted = formatTime(distance);

                if (bannerDisplay) bannerDisplay.textContent = formatted;
                if (paletteDisplay) paletteDisplay.textContent = formatted;

                // Visual alerts
                if (distance <= 60) {
                    if (bannerCard && !bannerCard.classList.contains('timer-state-danger')) {
                        bannerCard.className = 'card border-0 rounded-4 shadow-sm mb-4 ws-quiz-timer-card sticky-top timer-state-danger';
                    }
                    if (paletteBox && !paletteBox.classList.contains('timer-state-danger')) {
                        paletteBox.className = 'p-3 rounded-4 mb-3 text-center ws-palette-timer-box timer-state-danger';
                    }
                    if (bannerStatus) {
                        bannerStatus.className = 'badge bg-danger text-white small';
                        bannerStatus.textContent = 'Waktu Kritis!';
                    }
                } else if (distance <= 300) {
                    if (bannerCard && !bannerCard.classList.contains('timer-state-warning')) {
                        bannerCard.className = 'card border-0 rounded-4 shadow-sm mb-4 ws-quiz-timer-card sticky-top timer-state-warning';
                    }
                    if (paletteBox && !paletteBox.classList.contains('timer-state-warning')) {
                        paletteBox.className = 'p-3 rounded-4 mb-3 text-center ws-palette-timer-box timer-state-warning';
                    }
                    if (bannerStatus) {
                        bannerStatus.className = 'badge bg-warning text-dark small';
                        bannerStatus.textContent = 'Sisa < 5 Menit';
                    }
                }

                // Time expired -> Auto submit
                if (distance <= 0) {
                    hasAutoSubmitted = true;
                    clearInterval(timerInterval);
                    if (bannerDisplay) bannerDisplay.textContent = '00:00';
                    if (paletteDisplay) paletteDisplay.textContent = '00:00';

                    // Lock interactions
                    radios.forEach(r => r.disabled = true);
                    if (btnMainSubmit) btnMainSubmit.disabled = true;
                    if (btnSidebarSubmit) btnSidebarSubmit.disabled = true;
                    if (btnFinalConfirmSubmit) btnFinalConfirmSubmit.disabled = true;

                    if (bsModal) {
                        try { bsModal.hide(); } catch (e) {}
                    }

                    // Floating Notification
                    const alertEl = document.createElement('div');
                    alertEl.style.cssText = 'position:fixed;top:24px;left:50%;transform:translateX(-50%);z-index:99999;min-width:340px;max-width:92%;box-shadow:0 12px 30px rgba(0,0,0,0.3);';
                    alertEl.className = 'alert alert-danger rounded-4 text-center fw-bold py-3 px-4';
                    alertEl.innerHTML = '<div class="d-flex align-items-center justify-content-center gap-2"><span class="spinner-border spinner-border-sm"></span> <span>Waktu pengerjaan telah habis! Mengumpulkan jawaban Anda secara otomatis...</span></div>';
                    document.body.appendChild(alertEl);

                    clearPersistedAnswers();
                    setTimeout(function() {
                        form.submit();
                    }, 1200);
                }
            }

            tickTimer();
            const timerInterval = setInterval(tickTimer, 1000);
        })();
        <?php endif; ?>
    });
    </script>

<?php else: ?>
    <!-- 3. ASSESSMENT DASHBOARD, ATTEMPT HISTORY & DETAILED REVIEW SHEET -->
    <?php if ($questions === []): ?>
        <div class="card border-0 rounded-4 shadow-sm p-4 mb-3 text-center bg-white">
            <div class="py-4">
                <i class="bi bi-file-earmark-x text-muted" style="font-size: 56px;"></i>
                <h2 class="h4 fw-bold text-dark mt-3 mb-2" style="color: #082f5c;">Soal Belum Tersedia</h2>
                <p class="text-muted mb-4 mx-auto" style="max-width: 480px;">
                    Ujian / kuis ini belum memiliki daftar pertanyaan aktif dari instruktur. Silakan periksa kembali nanti atau hubungi pengajar Anda.
                </p>
                <a href="<?= route_to('workspace.participant.class.detail', (string) $classId) ?>" class="btn btn-outline-secondary rounded-pill px-4">
                    Kembali ke Kelas
                </a>
            </div>
        </div>

    <?php elseif ($allAttempts === []): ?>
        <!-- FIRST TIME TAKING QUIZ (NO ATTEMPTS YET) -->
        <div class="card border-0 rounded-4 shadow-sm p-4 mb-3 text-center bg-white">
            <div class="py-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 76px; height: 76px; background: rgba(8, 47, 92, 0.08); color: #082f5c;">
                    <i class="bi bi-mortarboard fs-1"></i>
                </div>
                <h2 class="h4 fw-bold mb-2" style="color: #082f5c;">Siap Memulai <?= esc($typeBadge['label']) ?>?</h2>
                <p class="text-muted mb-4 mx-auto" style="max-width: 540px;">
                    Terdapat <strong><?= $totalQ ?></strong> butir soal pertanyaan.
                    <?php if ($durationMinutes > 0): ?>
                        Alokasi waktu pengerjaan: <strong class="text-warning-emphasis"><i class="bi bi-clock-history me-1"></i><?= $durationMinutes ?> Menit</strong>.
                    <?php else: ?>
                        Alokasi waktu: <strong class="text-dark">Tanpa Batas Waktu</strong>.
                    <?php endif; ?>
                    Total kesempatan pengerjaan yang tersedia: <strong class="text-primary"><?= $attemptLimit ?> kali</strong>. Pastikan koneksi internet Anda stabil sebelum memulai.
                </p>
                <form method="post" action="<?= route_to('workspace.participant.quiz.start', (int) $quiz['id']) ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm fw-bold">
                        <i class="bi bi-play-circle-fill me-1"></i> Mulai Kerjakan Sekarang &raquo;
                    </button>
                </form>
            </div>
        </div>

    <?php else: ?>
        <!-- ATTEMPTS EXIST: SUMMARY BANNER, ATTEMPT HISTORY & REVIEW SHEET -->
        <?php
            $isPassingMet = ($highestScore !== null && (float) $highestScore >= $passingGrade);
            $bestScoreDisplay = $highestScore !== null ? (float) $highestScore : 0.0;
        ?>

        <!-- 3.1 ASSESSMENT STATUS & RE-ATTEMPT ACTION CARD -->
        <div class="card border-0 rounded-4 shadow-sm p-3 p-md-4 mb-4 bg-white">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle p-3 d-flex align-items-center justify-content-center flex-shrink-0 <?= $isPassingMet ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis' ?>" style="width: 58px; height: 58px;">
                            <i class="bi <?= $isPassingMet ? 'bi-patch-check-fill' : 'bi-exclamation-triangle-fill' ?> fs-3"></i>
                        </div>
                        <div>
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <h2 class="h5 fw-bold mb-0 text-navy">
                                    Status Evaluasi:
                                    <span class="<?= $isPassingMet ? 'text-success' : 'text-danger' ?>">
                                        <?= $isPassingMet ? 'Lulus Passing Grade' : 'Belum Memenuhi Passing Grade' ?>
                                    </span>
                                </h2>
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1 small">
                                    Min. <?= (float) $passingGrade ?>
                                </span>
                            </div>
                            <p class="text-muted small mb-2">
                                Nilai resmi yang diambil untuk kelulusan dan penerbitan sertifikat adalah <strong>Nilai Tertinggi</strong> (Best Attempt): <strong class="fs-6 <?= $isPassingMet ? 'text-success' : 'text-danger' ?>"><?= $bestScoreDisplay ?></strong>.
                            </p>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="badge bg-light text-dark border px-3 py-1 rounded-pill small">
                                    Percobaan Digunakan: <strong><?= $attemptCount ?></strong> dari <strong><?= $attemptLimit ?>x</strong>
                                </span>
                                <?php if ($remaining > 0): ?>
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-3 py-1 rounded-pill small fw-semibold">
                                        <i class="bi bi-arrow-repeat me-1"></i> Sisa Kesempatan: <?= $remaining ?>x
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill small">
                                        Seluruh Kesempatan Telah Digunakan
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 text-lg-end">
                    <div class="d-flex flex-column flex-sm-row flex-lg-column gap-2 justify-content-end">
                        <?php if ($remaining > 0): ?>
                            <form method="post" action="<?= route_to('workspace.participant.quiz.start', (int) $quiz['id']) ?>" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-4 py-2 w-100 shadow-sm">
                                    <i class="bi bi-arrow-repeat me-1"></i> Mulai Percobaan ke-<?= $attemptCount + 1 ?> &raquo;
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if ((string) ($quiz['type'] ?? '') === 'post_test'): ?>
                            <a href="<?= route_to('workspace.participant.study', (string) $classId) ?>" class="btn btn-outline-primary rounded-pill px-4 py-2 w-100">
                                <i class="bi bi-journal-text me-1"></i> Kembali ke Ruang Belajar
                            </a>
                        <?php elseif ((string) ($quiz['type'] ?? '') === 'pre_test'): ?>
                            <a href="<?= route_to('workspace.participant.study', (string) $classId) ?>" class="btn btn-primary rounded-pill px-4 py-2 w-100 fw-semibold">
                                Lanjut ke Materi Belajar <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        <?php else: ?>
                            <a href="<?= route_to('workspace.participant.study', (string) $classId) ?>" class="btn btn-outline-primary rounded-pill px-4 py-2 w-100">
                                <i class="bi bi-arrow-left me-1"></i> Kembali ke Ruang Belajar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3.2 RIWAYAT ATTEMPT (TABEL PERCOBAAN DENGAN BADGE NILAI TERTINGGI) -->
        <div class="card border-0 rounded-4 shadow-sm p-3 p-md-4 mb-4 bg-white">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
                <div>
                    <h2 class="h6 fw-bold mb-1 text-navy">
                        <i class="bi bi-clock-history text-primary me-2"></i> Riwayat Percobaan Ujian (<?= count($allAttempts) ?> Percobaan)
                    </h2>
                    <span class="text-muted small">Pilih salah satu percobaan untuk membuka lembar review soal, kunci jawaban, dan pembahasan.</span>
                </div>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 small fw-semibold">
                    Aturan: Nilai Tertinggi yang Diambil
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 130px;">Percobaan</th>
                            <th>Waktu Pengerjaan</th>
                            <th class="text-center" style="width: 140px;">Skor</th>
                            <th class="text-center" style="width: 160px;">Status Nilai</th>
                            <th class="text-end pe-3" style="width: 160px;">Aksi Review</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allAttempts as $att): ?>
                            <?php
                                $attId = (int) $att['id'];
                                $attScore = (float) ($att['score'] ?? 0);
                                $isThisHighest = ($bestAttempt !== null && (int) $bestAttempt['id'] === $attId);
                                $isThisActiveReview = ($reviewAttempt !== null && (int) $reviewAttempt['id'] === $attId);
                                $isPassThis = ($attScore >= $passingGrade);
                            ?>
                            <tr class="<?= $isThisActiveReview ? 'table-primary-subtle' : '' ?>">
                                <td class="ps-3">
                                    <strong class="text-navy">Percobaan #<?= (int) ($att['attempt_number'] ?? 1) ?></strong>
                                    <?php if ($isThisActiveReview): ?>
                                        <span class="badge bg-primary ms-1" style="font-size: 9px;">Sedang Ditinjau</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="d-block text-dark fw-medium">
                                        <?= ! empty($att['submitted_at']) ? esc(date('d M Y, H:i', strtotime($att['submitted_at']))) : (! empty($att['created_at']) ? esc(date('d M Y, H:i', strtotime($att['created_at']))) : '-') ?> WIB
                                    </span>
                                    <small class="text-muted">
                                        Status: <span class="badge bg-secondary-subtle text-secondary py-0" style="font-size: 10px;"><?= esc($att['status'] ?? 'graded') ?></span>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <span class="fs-5 fw-bold <?= $isPassThis ? 'text-success' : 'text-danger' ?>">
                                        <?= $attScore ?>
                                    </span>
                                    <span class="text-muted small">/ 100</span>
                                </td>
                                <td class="text-center">
                                    <?php if ($isThisHighest): ?>
                                        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold shadow-xs">
                                            <i class="bi bi-trophy-fill me-1"></i> Nilai Tertinggi
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill small">
                                            Arsip Percobaan
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="<?= route_to('workspace.participant.quiz.take', (string) $quiz['id']) ?>?attempt=<?= $attId ?>#reviewSheetSection" class="btn btn-sm <?= $isThisActiveReview ? 'btn-primary' : 'btn-outline-primary' ?> rounded-pill px-3 fw-semibold">
                                        <i class="bi bi-eye me-1"></i> <?= $isThisActiveReview ? 'Sedang Ditinjau' : 'Lihat Review' ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3.3 LEMBAR REVIEW SOAL, JAWABAN PESERTA, KUNCI & PEMBAHASAN -->
        <?php if ($reviewAttempt !== null): ?>
            <div id="reviewSheetSection" class="mb-4">
                <div class="card border-0 rounded-4 shadow-sm p-3 p-md-4 bg-white mb-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pb-3 border-bottom">
                        <div>
                            <span class="badge bg-primary text-white rounded-pill px-3 py-1 small fw-bold text-uppercase mb-1">
                                Lembar Review Hasil Ujian
                            </span>
                            <h2 class="h5 fw-bold mb-1 text-navy">
                                Review Soal Percobaan #<?= (int) ($reviewAttempt['attempt_number'] ?? 1) ?>
                            </h2>
                            <span class="text-muted small">
                                Skor Percobaan Ini: <strong class="<?= (float) ($reviewAttempt['score'] ?? 0) >= $passingGrade ? 'text-success' : 'text-danger' ?> fs-6"><?= (float) ($reviewAttempt['score'] ?? 0) ?> / 100</strong>
                                &middot; Selesai pada: <?= ! empty($reviewAttempt['submitted_at']) ? esc(date('d M Y H:i', strtotime($reviewAttempt['submitted_at']))) : '-' ?> WIB
                            </span>
                        </div>

                        <!-- Stat Breakdown Mini Pills -->
                        <?php
                            $cCount = 0;
                            $wCount = 0;
                            $uCount = 0;
                            foreach ($questions as $qItem) {
                                $ans = $answersByQuestion[(int) $qItem['id']] ?? null;
                                if (! $ans || $ans['selected_option_id'] === null) {
                                    $uCount++;
                                } elseif (! empty($ans['is_correct'])) {
                                    $cCount++;
                                } else {
                                    $wCount++;
                                }
                            }
                        ?>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill small fw-semibold">
                                <i class="bi bi-check-circle-fill me-1"></i> Benar: <?= $cCount ?>
                            </span>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill small fw-semibold">
                                <i class="bi bi-x-circle-fill me-1"></i> Salah: <?= $wCount ?>
                            </span>
                            <?php if ($uCount > 0): ?>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill small fw-semibold">
                                    <i class="bi bi-dash-circle-fill me-1"></i> Kosong: <?= $uCount ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Petunjuk Tanda Visual -->
                    <div class="p-2 px-3 rounded-3 bg-light border small text-muted mt-3 d-flex flex-wrap align-items-center gap-3" style="font-size: 11px;">
                        <span class="fw-bold text-dark"><i class="bi bi-info-circle text-primary me-1"></i> Keterangan Tanda:</span>
                        <span class="d-flex align-items-center gap-1">
                            <span class="badge bg-success py-1 px-2 rounded"><i class="bi bi-check-circle-fill"></i></span> Jawaban Anda Benar
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <span class="badge bg-danger py-1 px-2 rounded"><i class="bi bi-x-circle-fill"></i></span> Jawaban Anda Salah
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <span class="badge bg-success-subtle text-success border border-success py-1 px-2 rounded"><i class="bi bi-key-fill"></i></span> Kunci Jawaban Benar
                        </span>
                    </div>
                </div>

                <!-- DAFTAR SELURUH SOAL & REVIEW LENGKAP -->
                <?php foreach ($questions as $index => $q): ?>
                    <?php
                        $qIndex = $index + 1;
                        $qid = (int) $q['id'];
                        $opts = $optionsByQuestion[$qid] ?? [];
                        $ans = $answersByQuestion[$qid] ?? null;
                        $userSelectedOptId = $ans ? ($ans['selected_option_id'] ?? null) : null;
                        $isAnsCorrect = $ans ? ((int) ($ans['is_correct'] ?? 0) === 1) : false;
                        $isUnanswered = ($userSelectedOptId === null);
                    ?>
                    <div class="card border-0 rounded-4 shadow-sm p-3 p-md-4 mb-3 bg-white">
                        <!-- Header Butir Soal -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pb-2 mb-3 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary text-white rounded-pill px-3 py-1 fw-bold">
                                    Soal #<?= $qIndex ?> dari <?= $totalQ ?>
                                </span>
                                <?php if ($isAnsCorrect): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold">
                                        <i class="bi bi-check-circle-fill me-1"></i> Jawaban Anda Benar (+<?= (float) ($ans['score_awarded'] ?? $q['points']) ?> Poin)
                                    </span>
                                <?php elseif ($isUnanswered): ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill fw-semibold">
                                        <i class="bi bi-dash-circle-fill me-1"></i> Tidak Dijawab (0 Poin)
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-bold">
                                        <i class="bi bi-x-circle-fill me-1"></i> Jawaban Anda Salah (0 Poin)
                                    </span>
                                <?php endif; ?>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill px-2 py-1 small">
                                Bobot Soal: <?= (float) $q['points'] ?> Poin
                            </span>
                        </div>

                        <!-- Prompt Soal -->
                        <div class="fw-semibold text-dark lh-base mb-3" style="font-size: 0.95rem;">
                            <?= nl2br(esc($q['prompt'])) ?>
                        </div>

                        <!-- Pilihan Jawaban dengan Pembeda Visual -->
                        <div class="options-container mb-3">
                            <?php foreach ($opts as $i => $opt): ?>
                                <?php
                                    $optId = (int) $opt['id'];
                                    $isCorrectKey = ((int) ($opt['is_correct'] ?? 0) === 1);
                                    $isChosenByUser = ($userSelectedOptId !== null && (int) $userSelectedOptId === $optId);

                                    // Tentukan CSS box styling pembeda
                                    if ($isChosenByUser && $isCorrectKey) {
                                        $boxClass = 'border-success bg-success-subtle text-success-emphasis';
                                        $iconBadge = '<span class="badge bg-success ms-auto rounded-pill px-3 py-1 small fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Jawaban Anda (Benar)</span>';
                                    } elseif ($isChosenByUser && ! $isCorrectKey) {
                                        $boxClass = 'border-danger bg-danger-subtle text-danger-emphasis';
                                        $iconBadge = '<span class="badge bg-danger ms-auto rounded-pill px-3 py-1 small fw-bold"><i class="bi bi-x-circle-fill me-1"></i> Jawaban Anda (Salah)</span>';
                                    } elseif (! $isChosenByUser && $isCorrectKey) {
                                        $boxClass = 'border-success bg-success-subtle text-success-emphasis border-2';
                                        $iconBadge = '<span class="badge bg-success-subtle text-success border border-success ms-auto rounded-pill px-3 py-1 small fw-bold"><i class="bi bi-key-fill me-1"></i> Kunci Jawaban Benar</span>';
                                    } else {
                                        $boxClass = 'border-secondary-subtle bg-light text-muted opacity-75';
                                        $iconBadge = '';
                                    }
                                ?>
                                <div class="p-2 p-md-3 rounded-3 mb-2 border transition-all d-flex align-items-center gap-2 <?= $boxClass ?>">
                                    <span class="badge rounded-circle bg-white text-dark border d-flex align-items-center justify-content-center flex-shrink-0 fw-bold" style="width: 28px; height: 28px;">
                                        <?= chr(65 + (int) $i) ?>
                                    </span>
                                    <span class="fw-medium flex-grow-1" style="font-size: 0.88rem;">
                                        <?= esc($opt['option_text']) ?>
                                    </span>
                                    <?= $iconBadge ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Kotak Pembahasan & Penjelasan Soal -->
                        <div class="mt-2 p-3 rounded-3 border-start border-4 border-primary bg-primary-subtle">
                            <div class="fw-bold text-navy small mb-1">
                                <i class="bi bi-lightbulb-fill text-warning me-1"></i> Pembahasan / Penjelasan Materi:
                            </div>
                            <div class="small text-dark lh-base">
                                <?php if (! empty($q['explanation'])): ?>
                                    <?= nl2br(esc($q['explanation'])) ?>
                                <?php else: ?>
                                    Kunci jawaban yang benar untuk pertanyaan ini adalah opsi yang ditandai dengan label <strong class="text-success">Kunci Jawaban Benar</strong>.
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Back to Top / Bottom Nav -->
                <div class="card border-0 rounded-4 shadow-sm p-3 p-md-4 bg-white text-center">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <a href="<?= route_to('workspace.participant.class.detail', (string) $classId) ?>" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Kelas
                        </a>
                        <div class="d-flex flex-wrap gap-2">
                            <?php if ($remaining > 0): ?>
                                <form method="post" action="<?= route_to('workspace.participant.quiz.start', (int) $quiz['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm">
                                        <i class="bi bi-arrow-repeat me-1"></i> Coba Ulang Ujian (Sisa <?= $remaining ?>x)
                                    </button>
                                </form>
                            <?php endif; ?>
                            <a href="<?= route_to('workspace.participant.study', (string) $classId) ?>" class="btn btn-primary rounded-pill px-4 fw-semibold">
                                Lanjut Belajar <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnToggle = document.getElementById('btnToggleFocusModeQuiz');
    const btnExit = document.getElementById('btnExitFocusModeQuiz');

    function setFocusMode(enable) {
        if (enable) {
            document.body.classList.add('ws-focus-mode');
            localStorage.setItem('ws_quiz_zen_mode', 'true');
            if (btnToggle) {
                btnToggle.innerHTML = '<i class="fas fa-compress me-1"></i> Keluar Zen';
                btnToggle.classList.replace('btn-outline-primary', 'btn-light');
            }
        } else {
            document.body.classList.remove('ws-focus-mode');
            localStorage.setItem('ws_quiz_zen_mode', 'false');
            if (btnToggle) {
                btnToggle.innerHTML = '<i class="fas fa-expand me-1"></i> Mode Zen / Fokus';
                btnToggle.classList.replace('btn-light', 'btn-outline-primary');
            }
        }
    }

    if (localStorage.getItem('ws_quiz_zen_mode') === 'true') {
        setFocusMode(true);
    }

    if (btnToggle) {
        btnToggle.addEventListener('click', function () {
            const isCurrently = document.body.classList.contains('ws-focus-mode');
            setFocusMode(!isCurrently);
        });
    }

    if (btnExit) {
        btnExit.addEventListener('click', function () {
            setFocusMode(false);
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && document.body.classList.contains('ws-focus-mode')) {
            setFocusMode(false);
        }
    });
});
</script>

<?= $this->endSection() ?>