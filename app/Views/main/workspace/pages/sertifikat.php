<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="bg-white border rounded-4 p-4 p-lg-5 mb-4 shadow-sm">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <p class="text-uppercase text-muted small fw-semibold mb-2">Pencapaian Kompetensi</p>
            <h1 class="h3 fw-bold mb-2" style="color: #082f5c;"><?= esc($pageTitle ?? 'Sertifikat Saya') ?></h1>
            <p class="text-muted mb-0">Lihat sertifikat kelulusan yang telah diterbitkan serta verifikasi keabsahan dokumen.</p>
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
    <div class="card border-0 shadow-sm rounded-4 p-5 bg-white text-center">
        <i class="fas fa-award fs-1 text-muted opacity-50 mb-3"></i>
        <h2 class="h5 fw-bold mb-2" style="color: #082f5c;">Belum Ada Sertifikat Terbit</h2>
        <p class="text-muted mb-3">Selesaikan seluruh materi pembelajaran, kuis, dan tugas kelas untuk memenuhi syarat penerbitan sertifikat.</p>
        <div>
            <a href="<?= route_to('workspace.participant.classes') ?>" class="btn btn-primary rounded-pill px-4">
                Lihat Pembelajaran Saya
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($certificates as $cert): ?>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white d-flex flex-column justify-content-between position-relative overflow-hidden">
                    <div>
                        <!-- Header Badge -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-1 rounded-pill small fw-semibold">
                                <i class="fas fa-award me-1"></i> Sertifikat Resmi
                            </span>
                            <?php if ($cert['status'] === 'pending'): ?>
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1 small">Sedang Diproses</span>
                            <?php elseif ($cert['status'] === 'issued'): ?>
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 small fw-semibold">Telah Diterbitkan</span>
                            <?php elseif ($cert['status'] === 'revoked'): ?>
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1 small">Dicabut</span>
                            <?php endif; ?>
                        </div>

                        <!-- Certificate Title -->
                        <h2 class="h5 fw-bold mb-2" style="color: #082f5c;">
                            <?= esc($cert['course_title'] ?? $cert['class_name'] ?? 'Pelatihan PBJ') ?>
                        </h2>
                        <p class="small text-muted mb-3"><?= esc($cert['class_name'] ?? '') ?></p>

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
                                <a href="<?= route_to('workspace.participant.certificate.download', $cert['id']) ?>" class="btn btn-primary btn-sm rounded-pill px-3">
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
<?= $this->endSection() ?>
