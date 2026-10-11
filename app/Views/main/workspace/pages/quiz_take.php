<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?php
$quiz         = $quiz ?? [];
$questions    = $questions ?? [];
$optionsByQuestion = $optionsByQuestion ?? [];
$attempt      = $attempt ?? null;
$latest       = $latest ?? null;
$attemptCount = (int) ($attemptCount ?? 0);
$attemptLimit = (int) ($attemptLimit ?? 1);
$remaining    = (int) ($remaining ?? max(0, $attemptLimit - $attemptCount));
$classId      = (int) ($quiz['class_id'] ?? 0);
$totalQ       = count($questions);

$typeBadge = match ((string) ($quiz['type'] ?? 'lesson_quiz')) {
    'pre_test'  => ['label' => 'Pre-Test',   'class' => 'bg-warning text-dark'],
    'post_test' => ['label' => 'Post-Test',  'class' => 'bg-danger text-white'],
    default     => ['label' => 'Quiz Modul', 'class' => 'bg-primary text-white'],
};
?>

<!-- 1. QUIZ HERO & INFO CARD (FISTUDY DNA) -->
<div class="card border-0 rounded-4 shadow-sm p-4 p-lg-5 mb-4 bg-white">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <a href="<?= route_to('workspace.participant.study', (string) $classId) ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 mb-2">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Learning Room
            </a>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                <span class="badge <?= $typeBadge['class'] ?> text-uppercase px-3 py-1 rounded-pill fw-bold">
                    <?= esc($typeBadge['label']) ?>
                </span>
                <span class="badge bg-light text-muted border px-3 py-1 rounded-pill">
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

        <?php if ($latest !== null && (string) ($latest['status'] ?? '') === 'graded'): ?>
            <div class="p-3 px-4 rounded-4 border bg-light text-center shadow-xs">
                <span class="d-block text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Skor Percobaan Terakhir</span>
                <strong class="display-6 fw-bold <?= (float) ($latest['score'] ?? 0) >= 70 ? 'text-success' : 'text-danger' ?>">
                    <?= (float) ($latest['score'] ?? 0) ?>
                </strong>
                <span class="d-block text-muted" style="font-size: 11px;">Skala 100</span>
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
    <div class="row g-4">
        <!-- LEFT: QUESTION LIST & CARDS -->
        <div class="col-lg-8 col-xl-8">
            <form id="quizAttemptForm" method="post" action="<?= route_to('workspace.participant.quiz.submit', (int) $attempt['id']) ?>">
                <?= csrf_field() ?>

                <div class="d-flex align-items-center justify-content-between p-3 px-4 bg-white border rounded-4 shadow-sm mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square text-primary fs-5"></i>
                        <span class="fw-bold text-dark">Lembar Pengerjaan Soal (Percobaan #<?= (int) ($attempt['attempt_number'] ?? 1) ?>)</span>
                    </div>
                    <span class="badge bg-primary text-white rounded-pill px-3 py-1">
                        <?= $totalQ ?> Butir Soal
                    </span>
                </div>

                <!-- LIST SOAL -->
                <?php foreach ($questions as $index => $q): ?>
                    <?php
                        $qIndex = $index + 1;
                        $opts = $optionsByQuestion[(int) $q['id']] ?? [];
                    ?>
                    <div class="ws-quiz-q-card p-4 p-lg-5 mb-4 shadow-sm" id="question-card-<?= $qIndex ?>" data-question-index="<?= $qIndex ?>">
                        <!-- Card Header Soal: Nomor X dari Total & Status -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pb-3 mb-3 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary text-white rounded-pill px-3 py-2 fw-bold fs-6">
                                    Soal <?= $qIndex ?> dari <?= $totalQ ?>
                                </span>
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2 fw-semibold q-status-pill" id="q-status-badge-<?= $qIndex ?>">
                                    <i class="bi bi-circle me-1"></i> Belum Terjawab
                                </span>
                            </div>
                            <span class="badge bg-light text-dark border rounded-pill px-3 py-2 fw-semibold">
                                <i class="bi bi-award me-1 text-primary"></i> <?= (float) $q['points'] ?> Poin
                            </span>
                        </div>

                        <!-- Prompt Soal -->
                        <div class="fs-6 fw-semibold text-dark lh-base mb-4">
                            <?= nl2br(esc($q['prompt'])) ?>
                        </div>

                        <!-- Pilihan Jawaban (Radio Options) -->
                        <div class="options-container mb-4">
                            <?php foreach ($opts as $i => $opt): ?>
                                <label class="ws-quiz-option" for="opt-<?= (int) $q['id'] ?>-<?= (int) $opt['id'] ?>">
                                    <input class="form-check-input quiz-radio" type="radio" name="answers[<?= (int) $q['id'] ?>]" id="opt-<?= (int) $q['id'] ?>-<?= (int) $opt['id'] ?>" value="<?= (int) $opt['id'] ?>" data-q-index="<?= $qIndex ?>">
                                    <span class="ws-quiz-opt-letter"><?= chr(65 + (int) $i) ?></span>
                                    <span class="text-dark fw-medium lh-base flex-grow-1">
                                        <?= esc($opt['option_text']) ?>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <!-- Card Footer: Navigasi Antar Soal -->
                        <div class="d-flex align-items-center justify-content-between pt-3 border-top gap-2">
                            <div>
                                <?php if ($qIndex > 1): ?>
                                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 btn-nav-step" data-target="<?= $qIndex - 1 ?>">
                                        <i class="bi bi-arrow-left me-1"></i> Soal Sebelumnya
                                    </button>
                                <?php endif; ?>
                            </div>
                            <div>
                                <?php if ($qIndex < $totalQ): ?>
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 btn-nav-step" data-target="<?= $qIndex + 1 ?>">
                                        Soal Selanjutnya <i class="bi bi-arrow-right ms-1"></i>
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3 btn-nav-review">
                                        <i class="bi bi-check2-all me-1"></i> Tinjau Ringkasan
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Form Bottom Action Bar -->
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <a href="<?= route_to('workspace.participant.study', (string) $classId) ?>" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-x-circle me-1"></i> Batal & Kembali
                        </a>
                        <button type="button" id="btnMainSubmit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm fw-bold">
                            <i class="bi bi-send-check me-1"></i> Kirim Jawaban Ujian &raquo;
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- RIGHT: QUESTION PALETTE & PROGRESS SIDEBAR (STICKY) -->
        <div class="col-lg-4 col-xl-4">
            <div class="ws-quiz-palette-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <h2 class="h6 fw-bold mb-0" style="color: #082f5c;">
                        <i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i> Navigasi Soal
                    </h2>
                    <span class="badge bg-light text-muted border small"><?= $totalQ ?> Butir</span>
                </div>

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

        let activeQuestionIndex = 1;

        // Function to scroll smoothly to a question card
        function scrollToQuestion(index) {
            const card = document.getElementById('question-card-' + index);
            if (!card) return;

            activeQuestionIndex = index;

            // Highlight in palette
            document.querySelectorAll('.ws-quiz-palette-btn').forEach(btn => btn.classList.remove('current'));
            const currentPaletteBtn = document.getElementById('palette-btn-' + index);
            if (currentPaletteBtn) currentPaletteBtn.classList.add('current');

            // Visual focus effect on card
            document.querySelectorAll('.ws-quiz-q-card').forEach(c => c.classList.remove('is-active-focus'));
            card.classList.add('is-active-focus');

            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // Palette buttons click
        document.querySelectorAll('.ws-quiz-palette-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const target = parseInt(this.getAttribute('data-target'), 10);
                if (!isNaN(target)) scrollToQuestion(target);
            });
        });

        // Step buttons click (Prev / Next inside question cards)
        document.querySelectorAll('.btn-nav-step').forEach(btn => {
            btn.addEventListener('click', function() {
                const target = parseInt(this.getAttribute('data-target'), 10);
                if (!isNaN(target)) scrollToQuestion(target);
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
            });
        });

        // Initial stat run
        updateStats();

        // Jump to first unanswered question
        if (btnJumpUnanswered) {
            btnJumpUnanswered.addEventListener('click', function() {
                const stats = updateStats();
                for (let i = 1; i <= totalQuestions; i++) {
                    if (!stats.answeredIndices.has(i)) {
                        scrollToQuestion(i);
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
                form.submit();
            });
        }
    });
    </script>

<?php else: ?>
    <!-- 3. START ATTEMPT / LIMIT REACHED / EMPTY QUESTIONS STATE -->
    <div class="card border-0 rounded-4 shadow-sm p-4 p-lg-5 mb-4 text-center bg-white">
        <?php if ($questions === []): ?>
            <div class="py-4">
                <i class="bi bi-file-earmark-x text-muted" style="font-size: 56px;"></i>
                <h2 class="h4 fw-bold text-dark mt-3 mb-2" style="color: #082f5c;">Soal Belum Tersedia</h2>
                <p class="text-muted mb-4 mx-auto" style="max-width: 480px;">
                    Ujian / kuis ini belum memiliki daftar pertanyaan aktif dari instruktur. Silakan periksa kembali nanti atau hubungi pengajar Anda.
                </p>
                <a href="<?= route_to('workspace.participant.study', (string) $classId) ?>" class="btn btn-outline-secondary rounded-pill px-4">
                    Kembali ke Ruang Belajar
                </a>
            </div>
        <?php elseif ($remaining <= 0): ?>
            <div class="py-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 76px; height: 76px; background: rgba(220, 53, 69, 0.1); color: #dc3545;">
                    <i class="bi bi-x-circle-fill fs-1"></i>
                </div>
                <h2 class="h4 fw-bold text-danger mb-2">Batas Percobaan Telah Tercapai</h2>
                <p class="text-muted mb-4 mx-auto" style="max-width: 480px;">
                    Anda telah menggunakan seluruh kuota kesempatan pengerjaan (<strong><?= $attemptLimit ?>x</strong>) untuk evaluasi ini.
                </p>
                <a href="<?= route_to('workspace.participant.study', (string) $classId) ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                    Kembali ke Ruang Belajar
                </a>
            </div>
        <?php else: ?>
            <div class="py-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 76px; height: 76px; background: rgba(8, 47, 92, 0.08); color: #082f5c;">
                    <i class="bi bi-mortarboard fs-1"></i>
                </div>
                <h2 class="h4 fw-bold mb-2" style="color: #082f5c;">Siap Memulai <?= esc($typeBadge['label']) ?>?</h2>
                <p class="text-muted mb-4 mx-auto" style="max-width: 520px;">
                    Terdapat <strong><?= $totalQ ?></strong> butir soal pertanyaan. Anda memiliki sisa kesempatan pengerjaan: <strong class="text-primary"><?= $remaining ?> kali</strong>. Pastikan koneksi internet Anda stabil sebelum memulai.
                </p>
                <form method="post" action="<?= route_to('workspace.participant.quiz.start', (int) $quiz['id']) ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm fw-bold">
                        <i class="bi bi-play-circle-fill me-1"></i> Mulai Kerjakan Sekarang &raquo;
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>