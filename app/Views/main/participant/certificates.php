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
                    <?php elseif ($certificate['pdf_file_id'] === null): ?>
                        <span class="badge bg-success-subtle text-success-emphasis">Telah diterbitkan</span>
                        <p class="small text-muted mt-3 mb-0">File sertifikat belum tersedia untuk diunduh.</p>
                    <?php else: ?>
                        <span class="badge bg-success-subtle text-success-emphasis">Telah diterbitkan</span>
                    <?php endif; ?>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
