<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="ws-page-header">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="ws-eyebrow">
                <i class="fas fa-clipboard-check"></i> <?= esc($sectionContent['eyebrow'] ?? 'Evaluasi Pembelajaran') ?>
            </span>
            <h1><?= esc($pageTitle ?? 'Assessment Saya') ?></h1>
            <p><?= esc($sectionContent['description'] ?? 'Pre-test, Quiz berkala, dan Post-test untuk mengukur pemahaman materi.') ?></p>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<?php if (!empty($sectionContent['cards'])): ?>
    <div class="row g-2 g-md-3 mb-3">
        <?php foreach ($sectionContent['cards'] as $card): ?>
            <div class="col-sm-6 col-lg-4">
                <div class="card ws-stat-card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;"><?= esc($card['title']) ?></span>
                    <div class="display-6 fw-bold my-1" style="color: #082f5c; font-size: 1.25rem;"><?= esc($card['value']) ?></div>
                    <small class="text-muted" style="font-size: 0.68rem;"><?= esc($card['meta'] ?? '') ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Assessments Grid -->
<?php if (empty($assessmentsList)): ?>
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-center">
        <i class="fas fa-clipboard-list fs-2 text-muted opacity-50 mb-2"></i>
        <h2 class="h6 fw-bold mb-1" style="color: #082f5c;">Belum Ada Assessment</h2>
        <p class="text-muted small mb-0">Tidak ada quiz, pre-test, atau post-test yang tersedia di kelas pembelajaran aktif Anda.</p>
    </div>
<?php else: ?>
    <div class="row g-2 g-md-3 ws-paginated-container" data-page-size="6">
        <?php foreach ($assessmentsList as $qz): ?>
            <div class="col-md-6 col-lg-4 ws-paginated-item">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white d-flex flex-column justify-content-between ws-class-card">
                    <div>
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <?php
                            $typeLabel = match ($qz['type']) {
                                'pre_test'  => 'Pre-Test',
                                'post_test' => 'Post-Test',
                                default     => 'Quiz',
                            };
                            $typeBadge = match ($qz['type']) {
                                'pre_test'  => 'bg-info-subtle text-info',
                                'post_test' => 'bg-danger-subtle text-danger',
                                default     => 'bg-primary-subtle text-primary',
                            };
                            ?>
                            <div class="d-flex flex-wrap gap-1 align-items-center">
                                <span class="badge <?= $typeBadge ?> rounded-pill px-3 py-1 small fw-semibold">
                                    <?= esc($typeLabel) ?>
                                </span>
                                <?php if (isset($qz['is_unlocked']) && ! $qz['is_unlocked']): ?>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 small fw-semibold">
                                        <i class="fas fa-lock me-1"></i> Terkunci
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if ($qz['score'] !== null): ?>
                                <span class="badge bg-success-subtle text-success fs-6 fw-bold px-2 py-1">
                                    Nilai: <?= esc($qz['score']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Title & Class -->
                        <h2 class="h5 fw-bold mb-2" style="color: #082f5c;"><?= esc($qz['title']) ?></h2>
                        <span class="badge bg-light text-secondary border small mb-3"><?= esc($qz['class_name']) ?></span>

                        <!-- Lock Reason Notice (if locked) -->
                        <?php if (isset($qz['is_unlocked']) && ! $qz['is_unlocked']): ?>
                            <div class="alert alert-secondary py-2 px-3 small rounded-3 mb-3 d-flex align-items-center gap-2">
                                <i class="fas fa-lock text-muted flex-shrink-0"></i>
                                <span class="text-secondary"><?= esc($qz['lock_reason'] ?: 'Selesaikan materi prasyarat terlebih dahulu.') ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- Details -->
                        <ul class="list-unstyled text-muted small mb-4">
                            <li class="mb-1"><i class="fas fa-redo me-2 text-warning"></i> Batas Percobaan: <strong><?= (int) ($qz['attempt_limit'] ?? 1) ?> kali</strong></li>
                            <li class="mb-1"><i class="fas fa-exclamation-circle me-2 text-info"></i> Wajib: <strong><?= !empty($qz['is_required']) ? 'Ya' : 'Opsional' ?></strong></li>
                        </ul>
                    </div>

                    <!-- Action -->
                    <div class="pt-3 border-top">
                        <?php if (isset($qz['is_unlocked']) && ! $qz['is_unlocked']): ?>
                            <a href="<?= esc($qz['study_url'] ?? route_to('workspace.participant.study', (string) $qz['class_id'])) ?>" class="btn btn-outline-secondary btn-sm w-100 rounded-pill">
                                <i class="fas fa-arrow-right me-1"></i> Buka Ruang Belajar
                            </a>
                        <?php elseif ($qz['score'] !== null || ! empty($qz['is_completed'])): ?>
                            <a href="<?= esc($qz['take_url'] ?? route_to('workspace.participant.quiz.take', (string) $qz['id'])) ?>" class="btn btn-outline-success btn-sm w-100 rounded-pill">
                                <i class="fas fa-check-circle me-1"></i> Lihat Hasil <?= $qz['score'] !== null ? '(Nilai: ' . esc($qz['score']) . ')' : '' ?>
                            </a>
                        <?php else: ?>
                            <a href="<?= esc($qz['take_url'] ?? route_to('workspace.participant.quiz.take', (string) $qz['id'])) ?>" class="btn btn-primary btn-sm w-100 rounded-pill">
                                <i class="fas fa-play me-1"></i> Mulai Kerjakan
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
