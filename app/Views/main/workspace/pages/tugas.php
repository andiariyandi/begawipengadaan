<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?= view('dashboard/components/flash_message') ?>

<?php
$activeAssignment = $activeAssignment ?? null;
$latestSubmission = $latestSubmission ?? null;
$submittedFile    = $submittedFile ?? null;
$assignmentsList  = $assignmentsList ?? [];
$sectionContent   = $sectionContent ?? [];
?>

<!-- 1. HEADER SECTION (FISTUDY DNA) -->
<div class="ws-page-header">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="ws-eyebrow">
                <i class="fas fa-tasks"></i> <?= esc($sectionContent['eyebrow'] ?? 'Aktivitas Belajar & Penugasan') ?>
            </span>
            <h1><?= esc($pageTitle ?? 'Tugas Pembelajaran') ?></h1>
            <p><?= esc($sectionContent['description'] ?? 'Kelola tugas aktif, pengumpulan lembar kerja, berkas tugas, revisi, dan pemantauan feedback pengajar.') ?></p>
        </div>
        <?php if (! empty($activeAssignment)): ?>
            <div>
                <a href="<?= site_url('workspace/peserta/kelas/' . esc($activeAssignment['class_id']) . '/tugas') ?>" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Tugas
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- 2. SUMMARY KPI CARDS (JIKA DISEDIAKAN) -->
<?php if (empty($activeAssignment) && ! empty($sectionContent['cards'])): ?>
    <div class="row g-3 mb-4">
        <?php foreach ($sectionContent['cards'] as $card): ?>
            <div class="col-sm-6 col-lg-4">
                <div class="card ws-stat-card border-0 rounded-4 shadow-sm p-4 h-100 bg-white">
                    <span class="text-muted small fw-semibold text-uppercase"><?= esc($card['title']) ?></span>
                    <div class="display-6 fw-bold my-2" style="color: #082f5c;"><?= esc($card['value']) ?></div>
                    <small class="text-muted"><?= esc($card['meta'] ?? '') ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (! empty($activeAssignment)): ?>
    <!-- 3. ASSIGNMENT DETAIL & SUBMISSION FORM -->
    <div class="card border-0 rounded-4 shadow-sm p-3 p-md-4 mb-3 bg-white">
        <!-- Assignment Info Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 pb-2 mb-3 border-bottom">
            <div>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 mb-1 fw-semibold">
                    <i class="bi bi-file-earmark-text me-1"></i> Lembar Penugasan
                </span>
                <h2 class="h5 fw-bold mb-1" style="color: #082f5c;"><?= esc($activeAssignment['title'] ?? 'Pengumpulan Tugas') ?></h2>
                <div class="d-flex flex-wrap align-items-center gap-3 text-muted small mt-1">
                    <span><i class="bi bi-calendar-event me-1 text-primary"></i> Batas: <strong class="text-dark"><?= esc($activeAssignment['due_at'] ? date('d M Y, H:i', strtotime($activeAssignment['due_at'])) : 'Fleksibel') ?></strong></span>
                    <span><i class="bi bi-arrow-repeat me-1 text-primary"></i> Maksimal: <strong class="text-dark"><?= esc($activeAssignment['max_attempts'] ?? 1) ?>x</strong></span>
                </div>
            </div>
            <div>
                <a href="<?= route_to('workspace.participant.class.detail', (string) $activeAssignment['class_id']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Kelas
                </a>
            </div>
        </div>

        <!-- Instructions Box -->
        <div class="p-2 p-md-3 rounded-3 bg-light border mb-3">
            <h6 class="text-muted small text-uppercase fw-bold mb-1" style="letter-spacing: 0.5px; font-size: 0.72rem;">
                <i class="bi bi-info-circle-fill text-primary me-1"></i> Instruksi & Panduan Pengerjaan:
            </h6>
            <div class="text-dark lh-base small"><?= nl2br(esc($activeAssignment['instructions'] ?? 'Tidak ada instruksi khusus.')) ?></div>
        </div>

        <!-- Latest Submission Status Alert (Jika Sudah Pernah Submit) -->
        <?php if (! empty($latestSubmission)): ?>
            <?php
            $subStatus = (string) ($latestSubmission['status'] ?? 'submitted');
            $statusAlertClass = match ($subStatus) {
                'graded'            => 'alert-success border-success-subtle bg-success-subtle text-success-emphasis',
                'revision_required' => 'alert-warning border-warning-subtle bg-warning-subtle text-warning-emphasis',
                'in_review'         => 'alert-info border-info-subtle bg-info-subtle text-info-emphasis',
                default             => 'alert-primary border-primary-subtle bg-primary-subtle text-primary-emphasis',
            };
            $statusLabel = match ($subStatus) {
                'graded'            => 'Telah Dinilai Pengajar',
                'revision_required' => 'Perlu Perbaikan / Revisi',
                'in_review'         => 'Sedang Dalam Peninjauan',
                default             => 'Telah Dikumpulkan (Menunggu Penilaian)',
            };
            ?>
            <div class="alert <?= $statusAlertClass ?> rounded-4 p-3 mb-3 border">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                    <div>
                        <span class="badge bg-dark rounded-pill px-3 py-1 mb-2 fw-semibold">Percobaan #<?= (int) ($latestSubmission['attempt_number'] ?? 1) ?></span>
                        <h5 class="alert-heading fw-bold mb-1">
                            <i class="bi bi-patch-check-fill me-1"></i> Status: <?= esc($statusLabel) ?>
                        </h5>
                        <small class="text-muted d-block">Dikumpulkan pada: <strong><?= esc($latestSubmission['submitted_at'] ?? '-') ?></strong></small>
                    </div>

                    <?php if ($latestSubmission['score'] !== null): ?>
                        <div class="p-2 px-3 rounded-4 bg-white border text-center shadow-xs">
                            <span class="d-block text-muted small fw-semibold" style="font-size: 11px;">Nilai Tugas</span>
                            <span class="display-6 fw-bold text-success"><?= (float) $latestSubmission['score'] ?></span>
                            <span class="text-muted small">/ 100</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Instructor Feedback Box -->
                <?php if (! empty($latestSubmission['feedback'])): ?>
                    <div class="mt-3 p-3 bg-white rounded-3 border">
                        <strong class="text-dark small d-block mb-1">
                            <i class="bi bi-chat-left-quote-fill text-primary me-1"></i> Catatan & Feedback Pengajar:
                        </strong>
                        <p class="mb-0 text-muted lh-base"><?= nl2br(esc($latestSubmission['feedback'])) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Previously Uploaded File Info -->
                <?php if (! empty($submittedFile)): ?>
                    <div class="mt-3 p-3 bg-white rounded-3 border d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-paperclip text-primary fs-5"></i>
                            <div>
                                <strong class="small d-block text-dark"><?= esc($submittedFile['original_name'] ?? 'Berkas Tugas') ?></strong>
                                <span class="text-muted" style="font-size: 11px;"><?= round(($submittedFile['size_bytes'] ?? 0) / 1024, 1) ?> KB &middot; <?= esc($submittedFile['mime_type'] ?? 'file') ?></span>
                            </div>
                        </div>
                        <a href="<?= base_url('file/' . $submittedFile['id']) ?>?download=1" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                            <i class="bi bi-download me-1"></i> Unduh Berkas yang Dikumpulkan
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php
        $canSubmit = empty($latestSubmission) || ($latestSubmission['status'] ?? '') === 'revision_required' || (int) ($latestSubmission['attempt_number'] ?? 0) < (int) ($activeAssignment['max_attempts'] ?? 1);
        ?>

        <?php if ($canSubmit): ?>
            <div class="pt-3 border-top mt-2">
                <h4 class="h5 fw-bold mb-3" style="color: #082f5c;">
                    <i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i>
                    <?= ! empty($latestSubmission) ? 'Form Kirim Revisi Tugas' : 'Form Pengumpulan Tugas' ?>
                </h4>

                <form method="post" action="<?= site_url('workspace/peserta/kelas/' . esc($activeAssignment['class_id']) . '/tugas/kirim') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="assignment_id" value="<?= esc($activeAssignment['id']) ?>">

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">Uraian Jawaban / Ringkasan Laporan Tugas <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control rounded-3 p-3" rows="6" placeholder="Ketik ringkasan laporan tugas, penjelasan jawaban praktis, atau link dokumen pendukung di sini..." required><?= esc($latestSubmission['content'] ?? '') ?></textarea>
                        <small class="text-muted">Jelaskan hasil pengerjaan tugas Anda secara komprehensif.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">Unggah Berkas Pendukung (Opsional / Disarankan)</label>
                        <div class="ws-file-upload-box" onclick="document.getElementById('submissionFileInput').click();">
                            <i class="bi bi-cloud-arrow-up text-primary fs-1 mb-2 d-block"></i>
                            <strong class="d-block text-dark mb-1" id="fileUploadPrompt">Pilih atau seret berkas tugas Anda ke sini</strong>
                            <p class="text-muted small mb-0">Format yang didukung: <strong>PDF, DOCX, XLSX, ZIP</strong> (Maksimal 10MB)</p>
                            <input type="file" name="submission_file" id="submissionFileInput" class="d-none" onchange="handleFileSelected(this)">
                        </div>
                        <div id="fileSelectedBadge" class="mt-2 d-none">
                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-2 rounded-pill fs-6 fw-normal">
                                <i class="bi bi-file-earmark-check-fill me-1 text-success"></i> <span id="fileSelectedName">Nama Berkas</span>
                            </span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                        <a href="<?= site_url('workspace/peserta/kelas/' . esc($activeAssignment['class_id']) . '/tugas') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm fw-bold">
                            <i class="bi bi-send-check me-1"></i> Kirim Tugas Sekarang &raquo;
                        </button>
                    </div>
                </form>
            </div>

            <script>
            function handleFileSelected(input) {
                if (input.files && input.files[0]) {
                    var file = input.files[0];
                    document.getElementById('fileUploadPrompt').innerHTML = '<i class="bi bi-check-circle-fill text-success me-1"></i> Berkas terpilih: <strong>' + file.name + '</strong> (' + Math.round(file.size / 1024) + ' KB)';
                    document.getElementById('fileSelectedName').textContent = file.name + ' (' + Math.round(file.size / 1024) + ' KB)';
                    document.getElementById('fileSelectedBadge').classList.remove('d-none');
                }
            }
            </script>
        <?php else: ?>
            <div class="alert alert-secondary rounded-4 p-4 text-center mb-0 border">
                <i class="bi bi-lock-fill text-muted fs-3 d-block mb-2"></i>
                <h5 class="fw-bold text-dark mb-1">Pengumpulan Telah Ditutup</h5>
                <p class="text-muted mb-0">Tugas ini telah dinilai dan batas maksimal percobaan pengumpulan (<?= (int) ($activeAssignment['max_attempts'] ?? 1) ?>x) telah terpenuhi.</p>
            </div>
        <?php endif; ?>
    </div>

<?php elseif (! empty($assignmentsList)): ?>
    <!-- 4. ASSIGNMENTS GRID WITH MODULAR PAGINATION -->
    <div class="row g-4 mb-4 ws-paginated-container" data-page-size="6">
        <?php foreach ($assignmentsList as $asg): ?>
            <?php
            $subStatus = (string) ($asg['submission_status'] ?? 'not_submitted');
            $statusBadgeClass = match ($subStatus) {
                'graded'            => 'bg-success-subtle text-success-emphasis border border-success-subtle',
                'submitted'         => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
                'revision_required' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                'in_review'         => 'bg-info-subtle text-info-emphasis border border-info-subtle',
                default             => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
            };
            ?>
            <div class="col-md-6 col-lg-4 ws-paginated-item">
                <div class="card border-0 rounded-4 shadow-sm p-4 h-100 d-flex flex-column justify-content-between bg-white ws-class-card">
                    <div>
                        <!-- Header Card: Status & Score -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex flex-wrap gap-1 align-items-center">
                                <span class="badge <?= $statusBadgeClass ?> px-3 py-2 rounded-pill small fw-semibold">
                                    <?= esc($asg['submission_status_label'] ?? 'Belum Dikumpulkan') ?>
                                </span>
                                <?php if (isset($asg['is_unlocked']) && ! $asg['is_unlocked']): ?>
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle px-2 py-1 rounded-pill small fw-semibold">
                                        <i class="fas fa-lock me-1"></i> Terkunci
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if ($asg['score'] !== null): ?>
                                <span class="badge bg-success text-white px-3 py-2 rounded-pill fw-bold">
                                    <i class="bi bi-star-fill me-1"></i> <?= (float) $asg['score'] ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Title & Meta -->
                        <?php if (! empty($asg['class_name'])): ?>
                            <span class="badge bg-light text-muted border small rounded-pill mb-2"><?= esc($asg['class_name']) ?></span>
                        <?php endif; ?>
                        <h2 class="h5 fw-bold mb-2" style="color: #082f5c;"><?= esc($asg['title'] ?? 'Tugas') ?></h2>
                        <p class="text-muted small mb-3 lh-base"><?= esc(mb_strimwidth($asg['instructions'] ?? '', 0, 130, '...')) ?></p>

                        <!-- Lock Reason Notice (if locked) -->
                        <?php if (isset($asg['is_unlocked']) && ! $asg['is_unlocked']): ?>
                            <div class="alert alert-secondary py-2 px-3 small rounded-3 mb-3 d-flex align-items-center gap-2">
                                <i class="fas fa-lock text-muted flex-shrink-0"></i>
                                <span class="text-secondary"><?= esc($asg['lock_reason'] ?: 'Selesaikan materi pembelajaran modul terlebih dahulu.') ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="pt-3 border-top mt-3">
                        <div class="d-flex align-items-center justify-content-between text-muted small mb-3">
                            <span><i class="bi bi-calendar-event me-1"></i> Batas Waktu:</span>
                            <strong class="text-dark"><?= esc($asg['due_at'] ? date('d M Y', strtotime($asg['due_at'])) : 'Fleksibel') ?></strong>
                        </div>
                        <?php if (isset($asg['is_unlocked']) && ! $asg['is_unlocked']): ?>
                            <a href="<?= esc($asg['study_url'] ?? route_to('workspace.participant.study', (string) $asg['class_id'])) ?>" class="btn btn-outline-secondary rounded-pill btn-sm w-100 fw-semibold">
                                <i class="fas fa-arrow-right me-1"></i> Buka Materi Modul
                            </a>
                        <?php else: ?>
                            <a href="<?= esc($asg['submit_url'] ?? site_url('workspace/peserta/kelas/' . esc($asg['class_id']) . '/tugas/kirim?assignment_id=' . esc($asg['id']))) ?>" class="btn btn-outline-primary rounded-pill btn-sm w-100 fw-semibold">
                                <?php if ($subStatus === 'submitted'): ?>
                                    <i class="bi bi-eye me-1"></i> Lihat Pengumpulan
                                <?php elseif ($subStatus === 'revision_required'): ?>
                                    <i class="bi bi-arrow-repeat me-1"></i> Kirim Revisi
                                <?php elseif ($subStatus === 'graded'): ?>
                                    <i class="bi bi-check2-circle me-1"></i> Lihat Nilai & Hasil
                                <?php else: ?>
                                    <i class="bi bi-pencil me-1"></i> Kerjakan Tugas
                                <?php endif; ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php else: ?>
    <!-- 5. EMPTY STATE -->
    <div class="card border-0 rounded-4 shadow-sm p-3 p-md-4 bg-white text-center">
        <div class="py-3">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; background: rgba(8,47,92,0.06); color: #082f5c;">
                <i class="bi bi-file-earmark-check fs-3 opacity-75"></i>
            </div>
            <h2 class="h5 fw-bold mb-2" style="color: #082f5c;">Belum Ada Tugas Pembelajaran</h2>
            <p class="text-muted small mb-3 mx-auto" style="max-width: 480px;">
                Saat ini belum ada tugas mandiri atau tugas akhir yang perlu Anda kumpulkan untuk kelas ini. Silakan lanjutkan modul pembelajaran Anda.
            </p>
            <a href="<?= route_to('workspace.participant.classes') ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                Kembali ke Kelas Saya
            </a>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
