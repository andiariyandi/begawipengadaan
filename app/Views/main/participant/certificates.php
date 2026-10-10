<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>
<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2">Pencapaian Pembelajaran</p>
    <h1 class="h3 mb-2">Sertifikat Saya</h1>
    <p class="text-muted mb-0">Lihat status penerbitan sertifikat pembelajaran Anda.</p>
</div>
<?php if (empty($certificates)): ?>
    <div class="bg-white border rounded p-4"><p class="mb-0 text-muted">Belum ada sertifikat pembelajaran.</p></div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($certificates as $certificate): ?>
            <div class="col-md-6">
                <article class="bg-white border rounded p-4 h-100">
                    <p class="small text-uppercase text-muted mb-2">Sertifikat Pembelajaran</p>
                    <h2 class="h5"><?= esc($certificate['course_title'] ?? $certificate['class_name'] ?? '-') ?></h2>
                    <p class="text-muted mb-3"><?= esc($certificate['certificate_number']) ?></p>
                    <?php if ($certificate['status'] === 'pending'): ?>
                        <span class="badge bg-warning-subtle text-warning-emphasis">Sedang diproses</span>
                    <?php elseif (empty($certificate['pdf_file_id'])): ?>
                        <span class="badge bg-success-subtle text-success-emphasis">Telah diterbitkan</span>
                        <p class="small text-muted mt-3 mb-0">File sertifikat sedang diproses untuk diunduh.</p>
                    <?php else: ?>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="badge bg-success-subtle text-success-emphasis">Telah diterbitkan</span>
                        </div>
                        <div class="mt-3 d-flex flex-wrap gap-2">
                            <a href="<?= route_to('workspace.participant.certificate.download', $certificate['id']) ?>" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                                <i class="bi bi-download"></i> Unduh PDF
                            </a>
                            <a href="<?= site_url('sertifikat/verifikasi/' . urlencode($certificate['certificate_number'])) ?>" target="_blank" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1">
                                <i class="bi bi-shield-check"></i> Verifikasi
                            </a>
                        </div>
                    <?php endif; ?>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
