<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$row = $caseStudy ?? [];
$statusKey = $row['workflow_status'] ?? 'draft';
$statusLabel = [
    'draft' => 'Draft', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan',
    'submitted' => 'Diajukan', 'in_review' => 'Ditinjau', 'approved' => 'Disetujui',
    'revision_required' => 'Perlu Revisi', 'rejected' => 'Ditolak',
][$statusKey] ?? 'Draft';
$cats = array_column($row['categories'] ?? [], 'name');
$authorName = $row['author_name'] ?? 'Tim Ahli PBJ';
$initials = strtoupper(substr($authorName, 0, 2));
$coverImgUrl = ! empty($row['cover_file_id']) ? base_url('file/' . (int) $row['cover_file_id']) : null;
$sevBadge = match ($row['case_severity'] ?? 'medium') {
    'critical' => 'bg-danger text-white',
    'high'     => 'bg-danger-50 text-danger-700 border border-danger-200',
    'low'      => 'bg-success-50 text-success-700 border border-success-200',
    default    => 'bg-warning-50 text-warning-700 border border-warning-200',
};
$sevLabel = match ($row['case_severity'] ?? 'medium') {
    'critical' => 'Risiko Kritis',
    'high'     => 'Risiko Tinggi',
    'low'      => 'Risiko Rendah',
    default    => 'Risiko Sedang',
};
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Studi Kasus PBJ</h6>
        <p class="text-secondary-light mb-0">ID Kasus: #<?= (int) ($caseStudyId ?? ($row['id'] ?? 0)) ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= route_to('dashboard.knowledge.case-studies.edit', (string) ($caseStudyId ?? ($row['id'] ?? ''))) ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:pen-2-outline" class="text-lg"></iconify-icon>
            Ubah Studi Kasus
        </a>
        <a href="<?= route_to('dashboard.knowledge.case-studies') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            Kembali
        </a>
    </div>
</div>

<!-- Header Card with Cover -->
<div class="card radius-16 border mb-24 overflow-hidden bg-white shadow-sm">
    <?php if (! empty($coverImgUrl)): ?>
        <div class="position-relative" style="max-height: 280px; overflow: hidden; background: #082f5c;">
            <img src="<?= esc($coverImgUrl) ?>" alt="<?= esc($row['title'] ?? '') ?>" class="w-100 object-fit-cover" style="max-height: 280px;">
            <?php if ((int) ($row['featured'] ?? 0) === 1): ?>
                <div class="position-absolute top-0 end-0 m-3">
                    <span class="badge bg-warning text-dark radius-6 px-12 py-6 text-xs fw-bold shadow-sm">
                        <i class="fas fa-star me-1"></i> Kasus Unggulan
                    </span>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="card-body p-24">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-20">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge <?= esc($sevBadge) ?> radius-6 px-10 py-5 text-xs fw-bold">
                        <?= esc($sevLabel) ?>
                    </span>
                    <?php if (! empty($row['procurement_stage'])): ?>
                        <span class="badge bg-neutral-100 text-neutral-800 radius-6 px-10 py-5 text-xs text-capitalize">
                            Tahap: <?= esc(str_replace('_', ' ', $row['procurement_stage'])) ?>
                        </span>
                    <?php endif; ?>
                    <?php if (! empty($row['reading_time'])): ?>
                        <span class="badge bg-primary-50 text-primary-600 radius-6 px-10 py-5 text-xs">
                            <i class="far fa-clock me-1"></i><?= (int) $row['reading_time'] ?> Menit Telaah
                        </span>
                    <?php endif; ?>
                </div>
                <h4 class="fw-bold mb-2 text-primary-900"><?= esc($row['title'] ?? '-') ?></h4>
                <div class="d-flex flex-wrap align-items-center gap-3 text-secondary-light text-sm">
                    <?php if (! empty($row['organization_unit'])): ?>
                        <span><iconify-icon icon="solar:buildings-bold" class="me-1 text-primary-600"></iconify-icon><strong class="text-dark"><?= esc($row['organization_unit']) ?></strong></span>
                        <span>•</span>
                    <?php endif; ?>
                    <span><iconify-icon icon="solar:user-outline" class="me-1"></iconify-icon><?= esc($authorName) ?></span>
                    <span>•</span>
                    <span><iconify-icon icon="solar:calendar-date-outline" class="me-1"></iconify-icon>Diperbarui: <?= esc(date('d M Y, H:i', strtotime($row['updated_at'] ?? 'now'))) ?></span>
                    <span>•</span>
                    <span><iconify-icon icon="solar:eye-outline" class="me-1"></iconify-icon>Visibilitas: <?= ucfirst(esc($row['visibility'] ?? 'public')) ?></span>
                </div>
            </div>
            <div>
                <?= view('dashboard/components/status_badge', ['status' => $statusKey]) ?>
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 pt-16 border-top">
            <span class="text-secondary-light text-sm me-2">Kategori Pilar:</span>
            <?php if (!empty($cats)): ?>
                <?php foreach ($cats as $cat): ?>
                    <span class="badge bg-neutral-100 text-neutral-800 radius-6 px-12 py-6"><?= esc($cat) ?></span>
                <?php endforeach; ?>
            <?php else: ?>
                <span class="text-secondary-light text-sm">Umum</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Duduk Perkara, Konten & Lessons Learned -->
<div class="row gy-24">
    <div class="col-xl-8">
        <!-- Latar Belakang & Kronologi -->
        <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
            <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                <iconify-icon icon="solar:notes-bold" class="text-primary-600 text-xl"></iconify-icon>
                <h6 class="text-base fw-bold mb-0">Duduk Perkara &amp; Kronologi Kasus</h6>
            </div>
            <div class="card-body p-24">
                <?php if (! empty($row['summary'])): ?>
                    <div class="p-16 radius-12 bg-neutral-50 border border-neutral-200 mb-20">
                        <span class="fw-bold text-neutral-900 d-block mb-1 text-sm">Ringkasan Fakta Lapangan:</span>
                        <p class="text-secondary-light mb-0 text-sm" style="line-height: 1.6;"><?= nl2br(esc($row['summary'])) ?></p>
                    </div>
                <?php endif; ?>

                <div>
                    <h6 class="text-sm fw-bold text-primary-900 mb-12">Uraian &amp; Analisis Kasus Lengkap:</h6>
                    <div class="p-20 radius-12 border bg-base text-neutral-800" style="line-height: 1.7; font-size: 14.5px;">
                        <?= nl2br(esc($row['content'] ?? '(Belum ada naskah konten studi kasus)')) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lessons Learned & Solusi -->
        <?php if (! empty($row['resolution_summary'])): ?>
            <div class="card border-0 radius-16 shadow-sm bg-success-50 border-success-200 mb-24">
                <div class="card-header border-bottom border-success-200 p-20 bg-transparent d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:check-circle-bold" class="text-success-700 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0 text-success-900">Solusi Penyelesaian &amp; Pelajaran (Lessons Learned)</h6>
                </div>
                <div class="card-body p-24 text-success-900" style="line-height: 1.65; font-size: 14px;">
                    <?= nl2br(esc($row['resolution_summary'])) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar Metadata -->
    <div class="col-xl-4">
        <div class="card border-0 radius-16 shadow-sm bg-white mb-24">
            <div class="card-header border-bottom p-20 bg-white">
                <h6 class="text-base fw-bold mb-0">Parameter Studi Kasus</h6>
            </div>
            <div class="card-body p-20">
                <div class="mb-16 pb-16 border-bottom">
                    <span class="text-secondary-light text-xs d-block mb-1">Tingkat Risiko</span>
                    <span class="badge <?= esc($sevBadge) ?> radius-6 px-10 py-5 text-xs fw-bold">
                        <?= esc($sevLabel) ?>
                    </span>
                </div>
                <div class="mb-16 pb-16 border-bottom">
                    <span class="text-secondary-light text-xs d-block mb-1">Tahap Pengadaan</span>
                    <strong class="text-dark text-sm text-capitalize"><?= esc(str_replace('_', ' ', $row['procurement_stage'] ?? 'Umum')) ?></strong>
                </div>
                <div class="mb-16 pb-16 border-bottom">
                    <span class="text-secondary-light text-xs d-block mb-1">Instansi / Unit Kerja</span>
                    <span class="text-dark text-sm fw-medium"><?= esc($row['organization_unit'] ?? 'Pemerintah Provinsi Lampung') ?></span>
                </div>
                <div class="mb-16 pb-16 border-bottom">
                    <span class="text-secondary-light text-xs d-block mb-1">Slug URL</span>
                    <span class="font-monospace text-xs text-secondary-light"><?= esc($row['slug'] ?? '-') ?></span>
                </div>
                <div>
                    <span class="text-secondary-light text-xs d-block mb-1">Waktu Publikasi</span>
                    <span class="text-dark text-sm"><?= ! empty($row['published_at']) ? esc(date('d M Y, H:i', strtotime($row['published_at']))) : 'Belum dipublikasikan' ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>