<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="ws-page-header">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="ws-eyebrow"><i class="fas fa-award"></i> Pencapaian Kompetensi</span>
            <h1><?= esc($pageTitle ?? 'Sertifikat Saya') ?></h1>
            <p>Lihat sertifikat kelulusan yang telah diterbitkan serta verifikasi keabsahan dokumen.</p>
        </div>
        <div>
            <a href="<?= route_to('public.certificate.verify') ?>" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-shield-alt me-1"></i> Verifikasi Sertifikat
            </a>
        </div>
    </div>
</div>

<!-- Certificate List -->
<?php if (empty($certificates)): ?>
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-center mb-4">
        <div class="py-3">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; background: rgba(8, 47, 92, 0.08); color: #082f5c;">
                <i class="fas fa-award fs-2 text-primary"></i>
            </div>
            <h2 class="h5 fw-bold mb-2 text-navy">Belum Ada Sertifikat Resmi Diterbitkan</h2>
            <p class="text-muted small mb-3 mx-auto" style="max-width: 520px;">
                Sertifikat pelatihan akan diterbitkan secara otomatis setelah Anda menyelesaikan 100% materi silabus, lulus Post-Test evaluasi akhir dengan nilai mencapai batas minimum (Passing Grade), serta diverifikasi oleh Operator/Admin.
            </p>
            <a href="<?= route_to('workspace.participant.classes') ?>" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                <i class="bi bi-journal-bookmark me-1"></i> Lihat Pembelajaran Saya
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="row g-3 g-md-4 mb-4">
        <?php foreach ($certificates as $cert): ?>
            <div class="col-md-6">
                <div class="card ws-cert-card border-0 shadow-sm rounded-4 p-3 p-md-4 h-100 bg-white d-flex flex-column justify-content-between position-relative overflow-hidden">
                    <div>
                        <!-- Header Badge -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-1 rounded-pill small fw-semibold">
                                <i class="fas fa-award me-1"></i> Sertifikat Resmi
                            </span>
                            <?php if ($cert['status'] === 'pending'): ?>
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-1 small">
                                    <i class="bi bi-clock-history me-1"></i> Menunggu Verifikasi
                                </span>
                            <?php elseif ($cert['status'] === 'issued'): ?>
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 small fw-semibold">
                                    <i class="bi bi-check-circle-fill me-1"></i> Telah Diterbitkan
                                </span>
                            <?php elseif ($cert['status'] === 'revoked'): ?>
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1 small">Dicabut</span>
                            <?php endif; ?>
                        </div>

                        <!-- Certificate Title -->
                        <h2 class="h5 fw-bold mb-2 text-navy">
                            <?= esc($cert['event_title'] ?? $cert['course_title'] ?? $cert['class_name'] ?? 'Kegiatan / Pelatihan PBJ') ?>
                        </h2>
                        <p class="small text-muted mb-3"><?= esc(!empty($cert['event_title']) ? 'Kegiatan & Event PBJ' : ($cert['class_name'] ?? '')) ?></p>

                        <!-- Number & Info -->
                        <div class="p-3 bg-light rounded-3 mb-3">
                            <small class="text-muted d-block mb-1">Nomor Sertifikat:</small>
                            <code class="fw-bold text-dark fs-6"><?= esc($cert['certificate_number']) ?></code>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-3 border-top d-flex flex-wrap gap-2 justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="far fa-calendar-alt me-1"></i>
                            <?= !empty($cert['issued_at']) ? esc(date('d M Y', strtotime($cert['issued_at']))) : 'Belum terbit' ?>
                        </small>
                        <div class="d-flex gap-2">
                            <?php if ($cert['status'] === 'issued'): ?>
                                <a href="<?= route_to('workspace.participant.certificate.download', $cert['id']) ?>" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold">
                                    <i class="fas fa-download me-1"></i> Unduh PDF
                                </a>
                            <?php endif; ?>
                            <a href="<?= site_url('verifikasi-sertifikat/' . urlencode($cert['certificate_number'])) ?>" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                <i class="fas fa-shield-alt me-1"></i> Validasi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- INFORMASI & SYARAT ELIGIBILITAS SERTIFIKAT PEMBELAJARAN (KELAS YANG SEDANG DIIKUTI) -->
<?php
    $eligibilityList = $eligibilityList ?? [];
    $nonIssuedClasses = array_filter($eligibilityList, static fn($item) => empty($item['is_issued']));
?>
<?php if (! empty($nonIssuedClasses)): ?>
    <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white mt-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
            <div>
                <h2 class="h6 fw-bold mb-1 text-navy">
                    <i class="bi bi-info-circle-fill text-primary me-2"></i> Status Eligibilitas Sertifikat Kelas Pembelajaran
                </h2>
                <span class="text-muted small">
                    Berikut adalah status pemenuhan kriteria kelulusan dan eligibilitas sertifikat pada kelas PBJ yang Anda ikuti:
                </span>
            </div>
            <span class="badge bg-light text-muted border px-3 py-1 rounded-pill small">
                <?= count($nonIssuedClasses) ?> Kelas Aktif / Belum Terbit
            </span>
        </div>

        <div class="row g-3">
            <?php foreach ($nonIssuedClasses as $enr): ?>
                <?php
                    $isPending = ! empty($enr['is_pending_approval']);
                    $isEligible = ! empty($enr['is_eligible']);
                ?>
                <div class="col-lg-6">
                    <div class="p-3 rounded-4 border h-100 d-flex flex-column justify-content-between <?= $isPending ? 'border-warning bg-warning-subtle' : ($isEligible ? 'border-success bg-success-subtle' : 'border-light-subtle bg-light') ?>">
                        <div>
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div>
                                    <span class="badge bg-white text-dark border rounded-pill px-2 py-1 small fw-semibold mb-1">
                                        <?= esc($enr['class_code']) ?>
                                    </span>
                                    <h3 class="h6 fw-bold mb-1 text-navy">
                                        <?= esc($enr['class_name']) ?>
                                    </h3>
                                    <small class="text-muted"><?= esc($enr['course_title']) ?></small>
                                </div>
                                <div>
                                    <?php if ($isPending): ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1 small fw-bold">
                                            <i class="bi bi-clock-history me-1"></i> Menunggu Approval
                                        </span>
                                    <?php elseif ($isEligible): ?>
                                        <span class="badge bg-success text-white rounded-pill px-3 py-1 small fw-bold">
                                            <i class="bi bi-check-circle-fill me-1"></i> Memenuhi Syarat
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1 small fw-bold">
                                            <i class="bi bi-x-circle-fill me-1"></i> Belum Eligible
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Progress & Score Indicators -->
                            <div class="row g-2 mb-3 mt-1">
                                <div class="col-6">
                                    <div class="p-2 rounded-3 bg-white border small">
                                        <span class="text-muted d-block" style="font-size: 11px;">Kurikulum Selesai</span>
                                        <strong class="fs-6 <?= $enr['progress_percent'] >= 100 ? 'text-success' : 'text-dark' ?>"><?= $enr['progress_percent'] ?>%</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 rounded-3 bg-white border small">
                                        <span class="text-muted d-block" style="font-size: 11px;">Nilai Akhir (Tertinggi)</span>
                                        <strong class="fs-6 <?= $enr['final_score'] >= $enr['passing_grade'] ? 'text-success' : 'text-danger' ?>"><?= $enr['final_score'] ?></strong>
                                        <span class="text-muted" style="font-size: 11px;">/ Min. <?= $enr['passing_grade'] ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Unmet Criteria Checklist -->
                            <?php if (! empty($enr['unmet_reasons'])): ?>
                                <div class="p-2 px-3 rounded-3 bg-white border border-danger-subtle small mb-3">
                                    <span class="d-block text-danger fw-bold mb-1" style="font-size: 11px;">
                                        <i class="bi bi-shield-exclamation me-1"></i> Syarat yang Belum Terpenuhi:
                                    </span>
                                    <ul class="mb-0 ps-3 text-muted" style="font-size: 11px;">
                                        <?php foreach ($enr['unmet_reasons'] as $reason): ?>
                                            <li class="text-danger"><?= esc($reason) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php elseif ($isPending): ?>
                                <div class="p-2 px-3 rounded-3 bg-white border border-warning small mb-3">
                                    <span class="d-block text-warning-emphasis fw-semibold" style="font-size: 11px;">
                                        <i class="bi bi-hourglass-split me-1"></i> Anda telah menuntaskan seluruh syarat kelulusan. Sertifikat sedang dalam antrean verifikasi operator.
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Action Link -->
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                            <span class="text-muted" style="font-size: 11px;">
                                Passing Grade: <strong><?= $enr['passing_grade'] ?></strong>
                            </span>
                            <a href="<?= route_to('workspace.participant.study', (string) $enr['class_id']) ?>" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold">
                                Buka Ruang Belajar <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
