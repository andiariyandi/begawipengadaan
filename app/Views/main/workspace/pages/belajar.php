<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?php
$state       = $state ?? [];
$ctx         = $state['class'] ?? $classContext ?? [];
$trainers    = $state['trainers'] ?? [];
$enrollment  = $state['enrollment'] ?? [];
$modules     = $state['modules'] ?? [];
$cta         = $state['cta'] ?? [];
$stateCode   = $state['stateCode'] ?? 'in_learning';
$preTest     = $state['pre_test'] ?? [];
$postTest    = $state['post_test'] ?? [];
$activeType  = $activeType ?? 'lesson';
$activeItem  = $activeItem ?? null;
$pct         = (float) ($state['overall_percent'] ?? 0);
$finalScore  = $enrollment['final_score'] ?? null;
$fileAsset   = $fileAsset ?? null;
$prevLesson  = $prevLesson ?? null;
$nextLesson  = $nextLesson ?? null;

// Helper to determine YouTube/Vimeo embed
$embedUrl = null;
if (! empty($activeItem['external_url'])) {
    $rawUrl = trim($activeItem['external_url']);
    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|live|shorts)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $rawUrl, $matches)) {
        $embedUrl = 'https://www.youtube.com/embed/' . $matches[1] . '?rel=0&modestbranding=1';
    } elseif (preg_match('/vimeo\.com\/(?:video\/)?([0-9]+)/', $rawUrl, $matches)) {
        $embedUrl = 'https://player.vimeo.com/video/' . $matches[1];
    }
}
?>

<!-- 1. LEARNING ROOM HERO HEADER (FISTUDY DNA) -->
<div class="learning-header ws-learning-header-card mb-4 shadow-sm">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 position-relative" style="z-index: 2;">
        <div>
            <a href="<?= route_to('workspace.participant.class.detail', (string) ($ctx['id'] ?? 0)) ?>" class="btn btn-outline-light btn-sm rounded-pill px-3 mb-2 opacity-90">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Overview Kelas
            </a>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                <span class="badge bg-white text-dark rounded-pill px-3 py-1 fw-bold">
                    <i class="bi bi-mortarboard-fill text-primary me-1"></i> Ruang Belajar Interaktif
                </span>
                <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 py-1 font-monospace">
                    <?= esc($ctx['code'] ?? '-') ?>
                </span>
            </div>
            <h1 class="h3 fw-bold text-white mb-1"><?= esc($ctx['name'] ?? 'Learning Room') ?></h1>
            <p class="text-white text-opacity-75 small mb-0">
                Course: <strong class="text-white"><?= esc($ctx['course_title'] ?? '-') ?></strong>
            </p>
        </div>

        <!-- Trainer Info Pill -->
        <?php if (! empty($trainers)): ?>
            <?php $trainer = $trainers[0]; ?>
            <div class="d-flex align-items-center gap-3 p-2 px-3 rounded-4 bg-white bg-opacity-10 border border-white border-opacity-25 shadow-xs">
                <div class="avatar-circle bg-warning text-dark fw-bold d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 42px; height: 42px; font-size: 15px;">
                    <?= strtoupper(substr($trainer['name'] ?? 'T', 0, 2)) ?>
                </div>
                <div>
                    <span class="d-block text-white text-opacity-75" style="font-size: 11px;">Instruktur Pengajar</span>
                    <strong class="text-white small"><?= esc($trainer['name'] ?? 'Trainer') ?></strong>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Progress Bar -->
    <div class="pt-3 border-top border-white border-opacity-20 position-relative" style="z-index: 2;">
        <div class="d-flex align-items-center justify-content-between small text-white text-opacity-90 mb-2">
            <span>Capaian Pembelajaran: <strong class="text-warning fw-bold fs-6"><?= $pct ?>%</strong></span>
            <span><strong><?= (int) ($state['completed_lessons_count'] ?? 0) ?></strong> dari <?= (int) ($state['all_lessons_count'] ?? 0) ?> materi selesai</span>
        </div>
        <div class="progress rounded-pill bg-white bg-opacity-20" style="height: 10px;">
            <div class="progress-bar rounded-pill bg-warning" role="progressbar" style="width: <?= $pct ?>%" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
</div>

<!-- 2. MAIN LEARNING ROOM LAYOUT: SIDEBAR + CONTENT -->
<div class="row g-4">
    <!-- LEFT SIDEBAR: POHON ALUR MODUL & KURIKULUM -->
    <div class="col-lg-4 col-xl-4">
        <div class="learning-sidebar ws-learning-sidebar p-3 sticky-top" style="top: 90px; max-height: calc(100vh - 110px); overflow-y: auto;">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <h2 class="h6 mb-0 fw-bold text-uppercase d-flex align-items-center" style="font-size: 12px; letter-spacing: 0.5px; color: #082f5c;">
                    <i class="bi bi-diagram-3-fill text-primary me-2"></i> Kurikulum Kelas
                </h2>
                <span class="badge bg-primary-subtle text-primary rounded-pill small"><?= count($modules) ?> Modul</span>
            </div>

            <!-- Pre-Test Item (Jika Ada) -->
            <?php if ($preTest['exists']): ?>
                <div class="p-2 px-3 border rounded-3 mb-2 transition-all <?= $activeType === 'pre_test' ? 'border-primary bg-primary-subtle' : ($preTest['completed'] ? 'border-success-subtle bg-success-subtle' : 'bg-light') ?>">
                    <div class="d-flex align-items-center justify-content-between gap-1">
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            <?php if ($preTest['completed']): ?>
                                <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <?php else: ?>
                                <i class="bi bi-pencil-square text-warning fs-6"></i>
                            <?php endif; ?>
                            <a href="<?= route_to('workspace.participant.quiz.take', (string) $preTest['quiz']['id']) ?>" class="small fw-semibold text-dark text-truncate text-decoration-none">
                                <?= esc($preTest['quiz']['title'] ?? 'Pre-Test') ?>
                            </a>
                        </div>
                        <?php if ($preTest['completed']): ?>
                            <span class="badge bg-success rounded-pill" style="font-size: 10px;">Skor: <?= (float) ($preTest['attempt']['score'] ?? 0) ?></span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark rounded-pill" style="font-size: 10px;">Wajib</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Modules List -->
            <?php foreach ($modules as $mIndex => $mod): ?>
                <div class="border rounded-4 mb-3 overflow-hidden shadow-xs <?= $mod['is_completed'] ? 'border-success-subtle' : ($mod['is_unlocked'] ? 'border-primary-subtle' : 'border-light bg-light') ?>">
                    <!-- Module Header -->
                    <div class="p-2 px-3 d-flex align-items-center justify-content-between <?= $mod['is_completed'] ? 'bg-success-subtle' : ($mod['is_unlocked'] ? 'bg-light border-bottom' : 'bg-light') ?>">
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            <?php if ($mod['is_completed']): ?>
                                <i class="bi bi-check2-circle text-success fw-bold"></i>
                            <?php elseif ($mod['is_unlocked']): ?>
                                <i class="bi bi-folder2-open text-primary"></i>
                            <?php else: ?>
                                <i class="bi bi-lock-fill text-muted"></i>
                            <?php endif; ?>
                            <span class="small fw-bold text-dark text-truncate" title="<?= esc($mod['title']) ?>">
                                M<?= ($mIndex + 1) ?>: <?= esc($mod['title']) ?>
                            </span>
                        </div>
                        <?php if ($mod['is_completed']): ?>
                            <span class="badge bg-success rounded-pill" style="font-size: 9px;">Selesai</span>
                        <?php elseif (! $mod['is_unlocked']): ?>
                            <span class="badge bg-secondary rounded-pill" style="font-size: 9px;">Terkunci</span>
                        <?php endif; ?>
                    </div>

                    <!-- Module Lessons -->
                    <?php if (! empty($mod['lessons'])): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($mod['lessons'] as $les): ?>
                                <?php $isCurrentActive = ($activeType === 'lesson' && ! empty($activeItem['id']) && (int) $activeItem['id'] === (int) $les['id']); ?>
                                <div class="list-group-item list-group-item-action p-2 px-3 border-0 d-flex align-items-center justify-content-between gap-2 <?= $isCurrentActive ? 'bg-primary-subtle text-primary border-start border-primary border-4 fw-bold' : '' ?>">
                                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                                        <?php if ($les['is_completed']): ?>
                                            <i class="bi bi-check-circle-fill text-success" style="font-size: 13px;"></i>
                                        <?php elseif ($isCurrentActive): ?>
                                            <i class="bi bi-play-circle-fill text-primary" style="font-size: 13px;"></i>
                                        <?php elseif ($les['is_unlocked']): ?>
                                            <i class="bi bi-circle text-muted" style="font-size: 13px;"></i>
                                        <?php else: ?>
                                            <i class="bi bi-lock text-muted" style="font-size: 13px;"></i>
                                        <?php endif; ?>

                                        <?php if ($les['is_unlocked']): ?>
                                            <a href="<?= route_to('workspace.participant.lesson.detail', (string) $les['id']) ?>" class="small text-decoration-none text-truncate <?= $isCurrentActive ? 'text-primary fw-bold' : 'text-dark' ?>">
                                                <?= esc($les['title']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="small text-muted text-truncate"><?= esc($les['title']) ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <span class="badge <?= $les['content_type'] === 'document' ? 'bg-danger-subtle text-danger' : ($les['content_type'] === 'video' ? 'bg-info-subtle text-info' : 'bg-light text-muted border') ?> rounded-pill text-uppercase" style="font-size: 9px;">
                                        <?= esc($les['content_type']) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Module Quiz (Jika Configured) -->
                    <?php if (! empty($mod['quiz'])): ?>
                        <div class="p-2 px-3 border-top bg-white d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2 overflow-hidden">
                                <i class="bi bi-patch-question text-warning"></i>
                                <?php if ($mod['quiz']['is_unlocked']): ?>
                                    <a href="<?= route_to('workspace.participant.quiz.take', (string) $mod['quiz']['id']) ?>" class="small text-decoration-none text-dark fw-semibold text-truncate">
                                        <?= esc($mod['quiz']['title']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="small text-muted text-truncate"><?= esc($mod['quiz']['title']) ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($mod['quiz']['is_completed']): ?>
                                <span class="badge bg-success rounded-pill" style="font-size: 9px;"><i class="bi bi-check"></i> Skor: <?= (float) ($mod['quiz']['score'] ?? 0) ?></span>
                            <?php elseif ($mod['quiz']['is_unlocked']): ?>
                                <span class="badge bg-warning text-dark rounded-pill" style="font-size: 9px;">Siap</span>
                            <?php else: ?>
                                <span class="badge bg-light text-muted border rounded-pill" style="font-size: 9px;"><i class="bi bi-lock"></i></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Module Assignment (Jika Configured) -->
                    <?php if (! empty($mod['assignment'])): ?>
                        <div class="p-2 px-3 border-top bg-white d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2 overflow-hidden">
                                <i class="bi bi-file-earmark-check text-info"></i>
                                <?php if ($mod['assignment']['is_unlocked']): ?>
                                    <a href="<?= route_to('workspace.participant.assignment.submission', (string) $ctx['id']) ?>?assignment_id=<?= (int) $mod['assignment']['id'] ?>" class="small text-decoration-none text-dark fw-semibold text-truncate">
                                        <?= esc($mod['assignment']['title']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="small text-muted text-truncate"><?= esc($mod['assignment']['title']) ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($mod['assignment']['is_submitted']): ?>
                                <span class="badge bg-info text-dark rounded-pill" style="font-size: 9px;">Terkumpul</span>
                            <?php elseif ($mod['assignment']['is_unlocked']): ?>
                                <span class="badge bg-primary text-white rounded-pill" style="font-size: 9px;">Tugas</span>
                            <?php else: ?>
                                <span class="badge bg-light text-muted border rounded-pill" style="font-size: 9px;"><i class="bi bi-lock"></i></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <!-- Post-Test Item (Jika Ada) -->
            <?php if ($postTest['exists']): ?>
                <div class="p-2 px-3 border rounded-3 mb-2 transition-all <?= $activeType === 'post_test' ? 'border-primary bg-primary-subtle' : ($postTest['completed'] ? 'border-success-subtle bg-success-subtle' : 'bg-light') ?>">
                    <div class="d-flex align-items-center justify-content-between gap-1">
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            <?php if ($postTest['completed']): ?>
                                <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <?php else: ?>
                                <i class="bi bi-award text-primary fs-6"></i>
                            <?php endif; ?>
                            <?php if ($postTest['is_unlocked']): ?>
                                <a href="<?= route_to('workspace.participant.quiz.take', (string) $postTest['quiz']['id']) ?>" class="small fw-semibold text-dark text-truncate text-decoration-none">
                                    <?= esc($postTest['quiz']['title'] ?? 'Post-Test') ?>
                                </a>
                            <?php else: ?>
                                <span class="small text-muted text-truncate"><?= esc($postTest['quiz']['title'] ?? 'Post-Test') ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($postTest['completed']): ?>
                            <span class="badge bg-success rounded-pill" style="font-size: 10px;">Skor: <?= (float) ($postTest['attempt']['score'] ?? 0) ?></span>
                        <?php elseif ($postTest['is_unlocked']): ?>
                            <span class="badge bg-primary rounded-pill" style="font-size: 10px;">Siap</span>
                        <?php else: ?>
                            <span class="badge bg-secondary rounded-pill" style="font-size: 10px;"><i class="bi bi-lock"></i></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Kelulusan & Sertifikat Item -->
            <div class="p-2 px-3 border rounded-3 <?= in_array($stateCode, ['certificate_issued', 'completed_passed'], true) ? 'bg-success-subtle border-success' : 'bg-light' ?>">
                <div class="d-flex align-items-center justify-content-between gap-1">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <i class="bi bi-patch-check text-success fs-6"></i>
                        <span class="small fw-semibold text-dark text-truncate">Kelulusan & Sertifikat</span>
                    </div>
                    <?php if ($stateCode === 'certificate_issued'): ?>
                        <span class="badge bg-success rounded-pill" style="font-size: 10px;">Terbit</span>
                    <?php elseif ($stateCode === 'waiting_certificate'): ?>
                        <span class="badge bg-warning text-dark rounded-pill" style="font-size: 10px;">Approval</span>
                    <?php elseif ($stateCode === 'completed_passed'): ?>
                        <span class="badge bg-success rounded-pill" style="font-size: 10px;">Lulus</span>
                    <?php else: ?>
                        <span class="badge bg-secondary rounded-pill" style="font-size: 10px;"><i class="bi bi-lock"></i></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT MAIN CONTENT: MATERI AKTIF / VIEWER -->
    <div class="col-lg-8 col-xl-8">
        <div class="card border-0 rounded-4 shadow-sm p-4 p-lg-5 bg-white">
            <?php if ($activeType === 'lesson' && ! empty($activeItem)): ?>
                <!-- HEADER MATERI -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4 pb-3 border-bottom">
                    <div>
                        <span class="text-uppercase text-muted small fw-bold">
                            <i class="bi bi-folder2 text-primary me-1"></i> <?= esc($activeItem['module_title'] ?? 'Modul Pembelajaran') ?>
                        </span>
                        <h2 class="h3 fw-bold mb-1" style="color: #082f5c;"><?= esc($activeItem['title'] ?? 'Materi') ?></h2>
                    </div>
                    <div>
                        <?php
                        $progStatus = (string) ($activeItem['progress']['status'] ?? ($activeItem['status'] ?? 'in_progress'));
                        ?>
                        <?php if ($progStatus === 'completed'): ?>
                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-2 rounded-pill fs-6 fw-bold">
                                <i class="bi bi-check-circle-fill me-1"></i> Selesai Dipelajari
                            </span>
                        <?php else: ?>
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill fs-6 fw-bold">
                                <i class="bi bi-play-circle me-1"></i> Sedang Dipelajari
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- CONTENT VIEWER BERDASARKAN TIPE MATERI -->
                <div class="learning-content-viewer mb-4">
                    <?php if (($activeItem['content_type'] ?? '') === 'video'): ?>
                        <!-- Video Player (In-App YouTube / Vimeo Embed) -->
                        <?php if ($embedUrl !== null): ?>
                            <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow mb-4 border">
                                <iframe src="<?= esc($embedUrl) ?>" title="<?= esc($activeItem['title']) ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        <?php elseif (! empty($activeItem['external_url'])): ?>
                            <div class="p-4 p-lg-5 border rounded-4 bg-light mb-4 text-center">
                                <i class="bi bi-play-circle-fill text-primary" style="font-size: 54px;"></i>
                                <h3 class="h5 mt-2 mb-1" style="color: #082f5c;"><?= esc($activeItem['title']) ?></h3>
                                <p class="text-muted small mt-2 mb-3">Materi video interaktif tersedia melalui platform media pembelajaran.</p>
                                <a href="<?= esc($activeItem['external_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka Video Pembelajaran
                                </a>
                            </div>
                        <?php endif; ?>

                        <!-- Deskripsi / Teks Pendukung Video -->
                        <?php if (! empty($activeItem['content'])): ?>
                            <div class="p-3 px-4 bg-light rounded-4 text-dark lh-base border">
                                <h3 class="h6 fw-bold mb-2" style="color: #082f5c;"><i class="bi bi-info-circle-fill text-primary me-1"></i> Ringkasan & Catatan Materi:</h3>
                                <?= nl2br(esc($activeItem['content'])) ?>
                            </div>
                        <?php endif; ?>

                    <?php elseif (($activeItem['content_type'] ?? '') === 'document'): ?>
                        <!-- Document Viewer (In-App PDF Viewer + Download Access Control) -->
                        <?php if ($fileAsset !== null): ?>
                            <div class="ws-media-card mb-4">
                                <div class="d-flex flex-wrap align-items-center justify-content-between p-3 px-4 bg-white border-bottom gap-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 p-2 bg-danger-subtle text-danger fs-4 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                            <i class="bi bi-file-earmark-pdf-fill"></i>
                                        </div>
                                        <div>
                                            <strong class="text-dark small d-block"><?= esc($fileAsset['original_name'] ?? 'Dokumen Materi Pembelajaran PDF') ?></strong>
                                            <span class="text-muted" style="font-size: 11px;">
                                                Ukuran: <?= round(($fileAsset['size_bytes'] ?? 0) / 1024, 1) ?> KB &middot; Format: PDF
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Tombol Fullscreen / Tab Baru -->
                                        <a href="<?= base_url('file/' . $fileAsset['id']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                            <i class="bi bi-arrows-fullscreen me-1"></i> Buka Penuh
                                        </a>

                                        <!-- Pengaturan Hak Unduh Peserta -->
                                        <?php if (($activeItem['visibility'] ?? 'internal') !== 'preview_only'): ?>
                                            <a href="<?= base_url('file/' . $fileAsset['id']) ?>?download=1" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-semibold">
                                                <i class="bi bi-download me-1"></i> Unduh PDF
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-3 py-2 small" title="Pengajar membatasi unduhan untuk materi ini">
                                                <i class="bi bi-shield-lock me-1"></i> Viewer Only
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <!-- In-App Iframe PDF Viewer -->
                                <div class="bg-dark" style="height: 650px;">
                                    <iframe src="<?= base_url('file/' . $fileAsset['id']) ?>#toolbar=1&navpanes=0" class="w-100 h-100 border-0" title="<?= esc($activeItem['title']) ?>"></iframe>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="p-4 p-lg-5 border rounded-4 bg-light mb-4 text-center">
                                <i class="bi bi-file-earmark-pdf-fill text-danger" style="font-size: 54px;"></i>
                                <h3 class="h5 mt-2 mb-1" style="color: #082f5c;"><?= esc($activeItem['title']) ?></h3>
                                <p class="text-muted small mb-3">Dokumen pembelajaran terintegrasi.</p>
                                <?php if (! empty($activeItem['external_url'])): ?>
                                    <a href="<?= esc($activeItem['external_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                        <i class="bi bi-download me-1"></i> Buka / Unduh Dokumen
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (! empty($activeItem['content'])): ?>
                            <div class="p-3 px-4 bg-light rounded-4 text-dark lh-base border">
                                <h3 class="h6 fw-bold mb-2" style="color: #082f5c;"><i class="bi bi-info-circle-fill text-primary me-1"></i> Panduan Membaca:</h3>
                                <?= nl2br(esc($activeItem['content'])) ?>
                            </div>
                        <?php endif; ?>

                    <?php elseif (($activeItem['content_type'] ?? '') === 'link'): ?>
                        <!-- External Resource Link -->
                        <div class="p-4 p-lg-5 border rounded-4 bg-light mb-4 text-center">
                            <i class="bi bi-link-45deg text-primary" style="font-size: 54px;"></i>
                            <h3 class="h5 mt-2 mb-1" style="color: #082f5c;"><?= esc($activeItem['title']) ?></h3>
                            <p class="text-muted small mb-3">Tautan referensi dan modul pembelajaran eksternal.</p>
                            <?php if (! empty($activeItem['external_url'])): ?>
                                <a href="<?= esc($activeItem['external_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka Tautan Eksternal
                                </a>
                            <?php endif; ?>
                        </div>

                        <?php if (! empty($activeItem['content'])): ?>
                            <div class="p-3 px-4 bg-light rounded-4 text-dark lh-base border">
                                <h3 class="h6 fw-bold mb-2" style="color: #082f5c;"><i class="bi bi-info-circle-fill text-primary me-1"></i> Catatan Materi:</h3>
                                <?= nl2br(esc($activeItem['content'])) ?>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <!-- Standard Rich Text Content -->
                        <?php if (! empty($activeItem['content'])): ?>
                            <div class="lesson-article text-dark lh-lg fs-6 p-2" style="min-height: 250px;">
                                <?= nl2br(esc($activeItem['content'])) ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted p-4 bg-light rounded-4 text-center">Isi materi teks belum diunggah oleh instruktur.</p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- 3. TOMBOL AKSI DI BAWAH MATERI [Tandai Selesai & Lanjut] & NAVIGASI -->
                <div class="pt-4 border-top d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <?php if ($prevLesson !== null): ?>
                            <a href="<?= route_to('workspace.participant.lesson.detail', (string) $prevLesson['id']) ?>" class="btn btn-outline-secondary rounded-pill px-3">
                                &laquo; <?= esc($prevLesson['title']) ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="text-center">
                        <?php if ($progStatus !== 'completed'): ?>
                            <form method="post" action="<?= route_to('workspace.participant.lesson.complete', (string) $activeItem['id']) ?>" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm fw-bold">
                                    <i class="bi bi-check2-circle me-1"></i> Tandai Selesai & Lanjut &raquo;
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle py-2 px-3 rounded-pill fs-6 fw-bold">
                                <i class="bi bi-check2-all me-1"></i> Materi Ini Telah Selesai
                            </span>
                        <?php endif; ?>
                    </div>

                    <div>
                        <?php if ($nextLesson !== null && $nextLesson['is_unlocked']): ?>
                            <a href="<?= route_to('workspace.participant.lesson.detail', (string) $nextLesson['id']) ?>" class="btn btn-outline-primary rounded-pill px-3">
                                <?= esc($nextLesson['title']) ?> &raquo;
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

            <?php else: ?>
                <!-- SUMMARY / COMPLETION OVERVIEW KETIKA SELURUH MATERI SELESAI -->
                <div class="text-center py-5">
                    <?php if (in_array($stateCode, ['certificate_issued', 'completed_passed'], true)): ?>
                        <div class="mb-3">
                            <i class="bi bi-patch-check-fill text-success" style="font-size: 68px;"></i>
                        </div>
                        <h2 class="h3 fw-bold mb-2" style="color: #082f5c;">Selamat! Anda Telah Lulus Pembelajaran</h2>
                        <p class="text-muted mb-4 mx-auto" style="max-width: 540px;">
                            Seluruh materi, modul, kuis, dan ujian akhir telah Anda selesaikan dengan nilai akhir <strong class="text-success fs-5"><?= (float) ($finalScore ?? 0) ?></strong>.
                        </p>
                        <?php if ($stateCode === 'certificate_issued'): ?>
                            <a href="<?= route_to('workspace.participant.certificate.download', (string) $state['certificate']['id']) ?>" class="btn btn-success btn-lg rounded-pill px-5 shadow-sm fw-bold">
                                <i class="bi bi-download me-1"></i> Unduh Sertifikat Resmi (PDF)
                            </a>
                        <?php else: ?>
                            <span class="badge bg-success px-4 py-2 rounded-pill fs-6 fw-bold">Status: Lulus</span>
                        <?php endif; ?>

                    <?php elseif ($stateCode === 'waiting_certificate'): ?>
                        <div class="mb-3">
                            <i class="bi bi-hourglass-split text-warning" style="font-size: 68px;"></i>
                        </div>
                        <h2 class="h3 fw-bold mb-2" style="color: #082f5c;">Menunggu Persetujuan Sertifikat</h2>
                        <p class="text-muted mb-4 mx-auto" style="max-width: 540px;">
                            Anda telah menuntaskan seluruh proses pembelajaran dengan nilai <strong class="text-dark"><?= (float) ($finalScore ?? 0) ?></strong>.<br>
                            Sertifikat Anda saat ini berada dalam antrean peninjauan & persetujuan Operator/Admin.
                        </p>
                        <span class="badge bg-warning text-dark px-4 py-2 rounded-pill fs-6 fw-bold">
                            <i class="bi bi-clock-history me-1"></i> Menunggu Persetujuan Sertifikat
                        </span>

                    <?php else: ?>
                        <div class="mb-3">
                            <i class="bi bi-journal-bookmark-fill text-primary" style="font-size: 68px;"></i>
                        </div>
                        <h2 class="h3 fw-bold mb-2" style="color: #082f5c;">Ruang Belajar Interaktif</h2>
                        <p class="text-muted mb-4 mx-auto" style="max-width: 540px;">
                            Pilih materi yang tersedia pada sidebar di sebelah kiri untuk melanjutkan proses belajar Anda.
                        </p>
                        <?php if (! empty($cta['url']) && empty($cta['disabled'])): ?>
                            <a href="<?= esc($cta['url']) ?>" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm fw-bold">
                                <?= esc($cta['label']) ?> &raquo;
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>