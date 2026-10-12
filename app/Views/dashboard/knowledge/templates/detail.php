<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$row = $template ?? [];
$statusKey = $row['workflow_status'] ?? 'draft';
$statusLabel = [
    'draft' => 'Draft', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan',
    'submitted' => 'Diajukan', 'in_review' => 'Ditinjau', 'approved' => 'Disetujui',
    'revision_required' => 'Perlu Revisi', 'rejected' => 'Ditolak',
][$statusKey] ?? 'Draft';
$fileMeta = $fileMeta ?? null;
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Template Dokumen PBJ</h6>
        <p class="text-secondary-light mb-0">ID Template: #<?= (int) ($templateId ?? ($row['id'] ?? 0)) ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= route_to('dashboard.knowledge.document-templates.edit', (string) ($templateId ?? ($row['id'] ?? ''))) ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:pen-2-outline" class="text-lg"></iconify-icon>
            Ubah Template
        </a>
        <a href="<?= route_to('dashboard.knowledge.document-templates') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            Kembali
        </a>
    </div>
</div>

<div class="card radius-16 border mb-24 bg-white shadow-sm">
    <div class="card-body p-24">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-20">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-primary-50 text-primary-700 radius-6 px-10 py-5 text-xs text-uppercase fw-semibold">
                        <?= esc($row['document_category'] ?? 'Template PBJ') ?>
                    </span>
                    <span class="badge bg-neutral-100 text-neutral-800 radius-6 px-10 py-5 text-xs font-monospace">
                        Versi: <?= esc($row['document_version'] ?? 'v1.0') ?>
                    </span>
                </div>
                <h4 class="fw-bold mb-2 text-primary-900"><?= esc($row['title'] ?? '-') ?></h4>
                <div class="d-flex flex-wrap align-items-center gap-3 text-secondary-light text-sm">
                    <span><iconify-icon icon="solar:calendar-date-outline" class="me-1"></iconify-icon>Diperbarui: <?= esc(date('d M Y, H:i', strtotime($row['updated_at'] ?? 'now'))) ?></span>
                    <span>•</span>
                    <span><iconify-icon icon="solar:eye-outline" class="me-1"></iconify-icon>Visibilitas: <?= ucfirst(esc($row['visibility'] ?? 'public')) ?></span>
                </div>
            </div>
            <div>
                <?= view('dashboard/components/status_badge', ['status' => $statusKey]) ?>
            </div>
        </div>

        <div class="pt-16 border-top mb-24">
            <h6 class="text-sm fw-bold text-primary-900 mb-8">Deskripsi &amp; Petunjuk Penggunaan:</h6>
            <div class="p-16 radius-12 bg-neutral-50 border border-neutral-200 text-secondary-light" style="line-height: 1.6;">
                <?= nl2br(esc($row['summary'] ?? '(Belum ada deskripsi)')) ?>
            </div>
        </div>

        <?php if (! empty($row['content'])): ?>
            <div class="mb-24">
                <h6 class="text-sm fw-bold text-primary-900 mb-8">Catatan Klausul / Panduan Lampiran:</h6>
                <div class="p-16 radius-12 border bg-base text-neutral-800" style="line-height: 1.65;">
                    <?= nl2br(esc($row['content'])) ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Berkas File Download Widget -->
        <div class="p-20 radius-12 bg-success-50 border border-success-200 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="w-48-px h-48-px bg-success-600 text-white radius-10 d-flex align-items-center justify-content-center text-xl flex-shrink-0">
                    <iconify-icon icon="solar:file-check-bold"></iconify-icon>
                </div>
                <div>
                    <?php if ($fileMeta !== null): ?>
                        <strong class="d-block text-dark fw-bold mb-1"><?= esc($fileMeta['original_name']) ?></strong>
                        <span class="text-xs text-secondary-light">
                            Format: <strong><?= strtoupper(esc($fileMeta['extension'])) ?></strong> &middot; Ukuran: <strong><?= number_format(((int) ($fileMeta['size_bytes'] ?? 0)) / 1024, 1) ?> KB</strong>
                        </span>
                    <?php else: ?>
                        <strong class="d-block text-dark">Belum Ada File Terlampir</strong>
                        <span class="text-xs text-secondary-light">Silakan ubah data untuk mengunggah file template.</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (! empty($row['slug'])): ?>
                <a href="<?= route_to('public.knowledge.templates.download', $row['slug']) ?>" class="btn btn-success-600 text-white radius-8 px-16 py-8 text-sm fw-semibold d-inline-flex align-items-center gap-2">
                    <iconify-icon icon="solar:download-minimalistic-bold"></iconify-icon>
                    Unduh Berkas Template
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>