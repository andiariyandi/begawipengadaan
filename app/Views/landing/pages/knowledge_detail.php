<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>

<?php
$item         = $item ?? [];
$bookmarkType = $bookmarkType ?? 'knowledge_article';
$catalogType  = $catalogType ?? 'articles';
$fileMeta     = $fileMeta ?? null;
$related      = $related ?? [];
$stats        = $stats ?? [];

$catalogTitleMap = [
    'articles'  => 'Artikel PBJ',
    'cases'     => 'Studi Kasus',
    'templates' => 'Template Dokumen',
    'faqs'      => 'Tanya Jawab (FAQ)',
];
$catalogUrlMap = [
    'articles'  => site_url('bunga-rampai/artikel'),
    'cases'     => site_url('bunga-rampai/studi-kasus'),
    'templates' => site_url('bunga-rampai/template-dokumen'),
    'faqs'      => site_url('bunga-rampai/faq'),
];

$catalogTitle = $catalogTitleMap[$catalogType] ?? 'Artikel PBJ';
$catalogUrl   = $catalogUrlMap[$catalogType] ?? site_url('bunga-rampai/artikel');

$coverImg = ! empty($item['cover_file_id'])
    ? site_url('file/' . (int) $item['cover_file_id'])
    : null;

$readingTime = $item['reading_time'] ?? 5;
?>

<style>
    .knowledge-content {
        color: #334155;
        font-size: 16px;
        line-height: 1.85;
    }
    .knowledge-content p {
        margin-bottom: 1.25rem;
    }
    .knowledge-content h2, .knowledge-content h3, .knowledge-content h4 {
        color: #082f5c;
        font-weight: 700;
        margin-top: 1.75rem;
        margin-bottom: 0.75rem;
    }
    .knowledge-content blockquote {
        border-left: 4px solid #0d6efd;
        padding-left: 1rem;
        font-style: italic;
        color: #475569;
        margin: 1.5rem 0;
    }
    .hover-bg-light:hover {
        background-color: #f8fafc;
        color: #0d6efd !important;
    }
</style>

<!-- =========================================================================
     BREADCRUMB & HEADER
     ========================================================================= -->
<section class="py-3 bg-light border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="<?= site_url('/') ?>" class="text-decoration-none text-muted">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('bunga-rampai') ?>" class="text-decoration-none text-muted">Bunga Rampai</a></li>
                <li class="breadcrumb-item"><a href="<?= esc($catalogUrl) ?>" class="text-decoration-none text-muted"><?= esc($catalogTitle) ?></a></li>
                <li class="breadcrumb-item active text-truncate" aria-current="page" style="max-width: 320px;"><?= esc($itemTitle) ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Flash Alerts -->
<?php if ($msg = session()->getFlashdata('success')): ?>
    <div class="container mt-4">
        <div class="alert alert-success radius-8 mb-0"><?= esc($msg) ?></div>
    </div>
<?php endif; ?>
<?php if ($msg = session()->getFlashdata('error')): ?>
    <div class="container mt-4">
        <div class="alert alert-danger radius-8 mb-0"><?= esc($msg) ?></div>
    </div>
<?php endif; ?>

<!-- =========================================================================
     MAIN DETAIL CONTENT & SIDEBAR
     ========================================================================= -->
<section class="py-5" style="background: #f8fafc; min-height: 60vh;">
    <div class="container">
        <div class="row g-5">
            <!-- Left / Main Article Body -->
            <div class="col-xl-8 col-lg-8">
                <article class="card border-0 shadow-sm radius-16 p-4 p-md-5 bg-white">
                    <!-- Category & Meta Tags -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 pb-3 border-bottom">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="badge bg-primary text-white radius-6 px-3 py-1 small fw-semibold">
                                <?= esc($catalogTitle) ?>
                            </span>
                            <?php if ((int) ($item['featured'] ?? 0) === 1): ?>
                                <span class="badge bg-warning text-dark radius-6 px-2 py-1 small">
                                    <i class="fas fa-star me-1"></i> Pilihan Editor
                                </span>
                            <?php endif; ?>
                            <?php if (! empty($item['procurement_stage'])): ?>
                                <span class="badge radius-6 px-2 py-1 small border" style="background: #e0f2fe; color: #0369a1; border-color: #bae6fd;">
                                    <i class="fas fa-layer-group me-1"></i> <?= esc($item['procurement_stage']) ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($catalogType === 'cases' && ! empty($item['case_severity'])):
                                $sev = strtolower((string) $item['case_severity']);
                                $sevClass = match ($sev) {
                                    'critical' => 'background: #fee2e2; color: #b91c1c; border-color: #fca5a5;',
                                    'high'     => 'background: #ffedd5; color: #c2410c; border-color: #fdba74;',
                                    'low'      => 'background: #e0f2fe; color: #0369a1; border-color: #7dd3fc;',
                                    default    => 'background: #fef9c3; color: #854d0e; border-color: #fde047;',
                                };
                                $sevText = match ($sev) {
                                    'critical' => 'Risiko Kritis',
                                    'high'     => 'Risiko Tinggi',
                                    'low'      => 'Risiko Rendah',
                                    default    => 'Risiko Sedang',
                                };
                            ?>
                                <span class="badge radius-6 px-2 py-1 small border" style="<?= $sevClass ?>">
                                    <i class="fas fa-shield-alt me-1"></i> <?= $sevText ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($catalogType === 'templates'): ?>
                                <?php if (! empty($item['document_category'])): ?>
                                    <span class="badge radius-6 px-2 py-1 small border" style="background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe;">
                                        <i class="fas fa-tag me-1"></i> <?= esc($item['document_category']) ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (! empty($item['document_version'])): ?>
                                    <span class="badge bg-light text-dark border px-2 py-1 small">
                                        <i class="fas fa-code-branch me-1 text-primary"></i> <?= esc($item['document_version']) ?>
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex align-items-center gap-3 text-muted small">
                            <span><i class="far fa-clock me-1"></i> <?= esc($readingTime) ?> menit baca</span>
                            <?php if ($bookmarkType !== null): ?>
                                <?= view('landing/components/bookmark_button', [
                                    'type'  => $bookmarkType,
                                    'id'    => (int) ($item['id'] ?? 0),
                                    'title' => $item['title'] ?? null,
                                    'size'  => 'sm',
                                ]) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Article Title -->
                    <h1 class="fw-bold mb-3" style="font-size: 28px; line-height: 1.35; color: #082f5c;">
                        <?= esc($itemTitle) ?>
                    </h1>

                    <!-- Author & Date Bar -->
                    <div class="d-flex align-items-center gap-3 mb-4 text-muted small">
                        <div class="d-flex align-items-center gap-2">
                            <div class="d-flex align-items-center justify-content-center radius-50 bg-light border text-primary fw-bold" style="width: 36px; height: 36px;">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div>
                                <strong class="d-block text-dark" style="font-size: 13.5px;"><?= esc($item['author_name'] ?? 'Biro Pengadaan Barang & Jasa') ?></strong>
                                <span style="font-size: 12px;">
                                    <?= ! empty($item['organization_unit']) ? esc($item['organization_unit']) . ' &middot; ' : '' ?>Pemerintah Provinsi Lampung
                                </span>
                            </div>
                        </div>
                        <span class="text-muted">&middot;</span>
                        <div>
                            <i class="far fa-calendar-alt me-1"></i>
                            <?= ! empty($item['published_at']) ? esc(date('d F Y', strtotime($item['published_at']))) : 'Terbaru' ?>
                        </div>
                    </div>

                    <!-- Cover Image (if available) -->
                    <?php if (! empty($coverImg)): ?>
                        <div class="mb-4 radius-12 overflow-hidden shadow-sm" style="max-height: 380px;">
                            <img src="<?= esc($coverImg) ?>" alt="<?= esc($itemTitle) ?>" class="w-100 h-100 object-fit-cover">
                        </div>
                    <?php endif; ?>

                    <!-- Executive Summary Callout Box -->
                    <?php if (! empty($item['summary'])): ?>
                        <div class="p-4 radius-12 mb-4" style="background: #f1f5f9; border-left: 4px solid #082f5c;">
                            <h6 class="fw-bold mb-2 text-uppercase" style="font-size: 12px; letter-spacing: 0.5px; color: #082f5c;">
                                <i class="fas fa-info-circle me-1"></i> Ringkasan Materi
                            </h6>
                            <p class="mb-0 text-secondary" style="font-size: 15px; line-height: 1.65; font-style: italic;">
                                <?= nl2br(esc($item['summary'])) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Detailed Content Body -->
                    <div class="knowledge-content text-dark mb-4" style="font-size: 16px; line-height: 1.85; color: #334155;">
                        <?php if (! empty($item['content'])): ?>
                            <?= nl2br(esc($item['content'])) ?>
                        <?php else: ?>
                            <p class="text-muted fst-italic">Uraian konten lengkap belum tersedia.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Callout: Case Study Pelajaran Berharga -->
                    <?php if ($catalogType === 'cases' && ! empty($item['resolution_summary'])): ?>
                        <div class="card border-0 radius-16 p-4 mb-4 shadow-sm" style="background: #fff7ed; border-left: 5px solid #f97316 !important;">
                            <div class="d-flex align-items-center gap-2 mb-2" style="color: #c2410c;">
                                <i class="fas fa-lightbulb" style="font-size: 20px;"></i>
                                <h5 class="fw-bold mb-0 text-uppercase" style="font-size: 14px; letter-spacing: 0.5px;">Pelajaran Berharga &amp; Solusi Rekomendasi</h5>
                            </div>
                            <p class="mb-0" style="font-size: 15px; line-height: 1.7; color: #7c2d12;">
                                <?= nl2br(esc($item['resolution_summary'])) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Callout: Dasar Hukum & Rujukan Regulasi -->
                    <?php if (! empty($item['legal_basis'])): ?>
                        <div class="card border-0 radius-12 p-3 mb-4 bg-light border-start border-primary border-4 shadow-sm">
                            <div class="d-flex align-items-center gap-2 text-primary fw-bold small mb-1">
                                <i class="fas fa-balance-scale"></i>
                                <span>Dasar Hukum &amp; Rujukan Regulasi Terkait</span>
                            </div>
                            <div class="text-secondary small" style="line-height: 1.6;">
                                <?= nl2br(esc($item['legal_basis'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Callout: External Source Reference URL -->
                    <?php if (! empty($item['source_url'])): ?>
                        <div class="alert alert-light border radius-12 d-flex flex-wrap align-items-center justify-content-between p-3 mb-4 gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-external-link-alt text-primary"></i>
                                <span class="small text-muted">Rujukan Dokumen Eksternal / Sumber:</span>
                            </div>
                            <a href="<?= esc($item['source_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary radius-6 fw-semibold">
                                Buka Dokumen / Sumber Asli <i class="fas fa-arrow-up-right-from-square ms-1" style="font-size: 11px;"></i>
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Template Specific: High-End Download Card Widget -->
                    <?php if ($catalogType === 'templates'): ?>
                        <div class="card border-0 radius-16 p-4 mb-4 text-white shadow-sm" style="background: linear-gradient(135deg, #082f5c 0%, #1e3a8a 100%);">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center justify-content-center radius-12 bg-white text-primary fw-bold shadow-sm" style="width: 58px; height: 58px; font-size: 24px;">
                                        <i class="fas fa-file-download"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-warning text-dark radius-6 px-2 py-0 small">Dokumen Resmi PBJ</span>
                                            <?php if (! empty($item['document_category'])): ?>
                                                <span class="badge bg-white bg-opacity-25 text-white radius-6 px-2 py-0 small"><?= esc($item['document_category']) ?></span>
                                            <?php endif; ?>
                                            <?php if (! empty($item['document_version'])): ?>
                                                <span class="badge bg-info bg-opacity-25 text-white radius-6 px-2 py-0 small"><?= esc($item['document_version']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <h5 class="text-white fw-bold mb-1" style="font-size: 17px;"><?= esc($itemTitle) ?></h5>
                                        <small class="text-white-80" style="color: rgba(255,255,255,0.85);">
                                            <?php if (! empty($fileMeta)): ?>
                                                <?= esc($fileMeta['original_name']) ?> &middot; <?= esc(strtoupper($fileMeta['extension'])) ?> &middot; <?= number_format((float) $fileMeta['size_bytes'] / 1024, 1) ?> KB
                                            <?php else: ?>
                                                Format Siap Pakai &middot; DOCX / XLSX / PDF
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="<?= route_to('public.knowledge.templates.download', $item['slug']) ?>" class="btn btn-warning text-dark fw-bold radius-8 px-4 py-2 d-inline-flex align-items-center gap-2 shadow-sm">
                                        <i class="fas fa-download"></i>
                                        <span>Unduh Berkas Sekarang</span>
                                    </a>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-top border-white border-opacity-10 small text-white-80" style="color: rgba(255,255,255,0.75);">
                                <i class="fas fa-info-circle me-1"></i> Catatan: Dokumen ini bebas diunduh dan disesuaikan dengan kebutuhan teknis paket pengadaan OPD Anda.
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Action Toolbar & Navigation -->
                    <div class="pt-4 border-top d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <a href="<?= esc($catalogUrl) ?>" class="btn btn-outline-secondary radius-8 px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2">
                            <i class="fas fa-arrow-left"></i>
                            <span>Kembali ke <?= esc($catalogTitle) ?></span>
                        </a>
                        <div class="d-flex align-items-center gap-2">
                            <?php if ($bookmarkType !== null): ?>
                                <?= view('landing/components/bookmark_button', [
                                    'type'  => $bookmarkType,
                                    'id'    => (int) ($item['id'] ?? 0),
                                    'title' => $item['title'] ?? null,
                                    'size'  => 'md',
                                ]) ?>
                            <?php endif; ?>
                            <button type="button" class="btn btn-outline-primary radius-8 px-3 py-2 fw-semibold" onclick="navigator.clipboard.writeText(window.location.href); alert('Tautan berhasil disalin ke clipboard!');">
                                <i class="fas fa-share-alt me-1"></i> Bagikan
                            </button>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Right Sidebar -->
            <div class="col-xl-4 col-lg-4">
                <div class="d-flex flex-column gap-4 position-sticky" style="top: 90px;">
                    <!-- Widget 1: Metadata Card -->
                    <div class="card border-0 shadow-sm radius-16 p-4 bg-white">
                        <h4 class="fw-bold mb-3 pb-2 border-bottom" style="font-size: 17px; color: #082f5c;">
                            <i class="fas fa-info-circle text-primary me-2"></i> Informasi Materi
                        </h4>
                        <div class="d-flex flex-column gap-3 small">
                            <div>
                                <span class="text-muted d-block mb-1">Kategori:</span>
                                <strong class="text-dark fw-semibold"><?= esc($catalogTitle) ?></strong>
                            </div>
                            <div>
                                <span class="text-muted d-block mb-1">Status Publikasi:</span>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                    <i class="fas fa-check-circle me-1"></i> Terverifikasi Publik
                                </span>
                            </div>
                            <?php if (! empty($item['procurement_stage'])): ?>
                                <div>
                                    <span class="text-muted d-block mb-1">Tahapan PBJ:</span>
                                    <span class="badge radius-6 px-2 py-1 small border" style="background: #e0f2fe; color: #0369a1; border-color: #bae6fd;">
                                        <i class="fas fa-layer-group me-1"></i> <?= esc($item['procurement_stage']) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php if (! empty($item['organization_unit'])): ?>
                                <div>
                                    <span class="text-muted d-block mb-1">Unit Kerja / OPD:</span>
                                    <strong class="text-dark fw-semibold"><i class="fas fa-building text-primary me-1"></i> <?= esc($item['organization_unit']) ?></strong>
                                </div>
                            <?php endif; ?>
                            <?php if ($catalogType === 'cases' && ! empty($item['case_severity'])):
                                $sev = strtolower((string) $item['case_severity']);
                                $sevClass = match ($sev) {
                                    'critical' => 'background: #fee2e2; color: #b91c1c; border-color: #fca5a5;',
                                    'high'     => 'background: #ffedd5; color: #c2410c; border-color: #fdba74;',
                                    'low'      => 'background: #e0f2fe; color: #0369a1; border-color: #7dd3fc;',
                                    default    => 'background: #fef9c3; color: #854d0e; border-color: #fde047;',
                                };
                                $sevText = match ($sev) {
                                    'critical' => 'Risiko Kritis',
                                    'high'     => 'Risiko Tinggi',
                                    'low'      => 'Risiko Rendah',
                                    default    => 'Risiko Sedang',
                                };
                            ?>
                                <div>
                                    <span class="text-muted d-block mb-1">Tingkat Risiko:</span>
                                    <span class="badge radius-6 px-2 py-1 small border" style="<?= $sevClass ?>">
                                        <i class="fas fa-shield-alt me-1"></i> <?= $sevText ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php if ($catalogType === 'templates'): ?>
                                <?php if (! empty($item['document_category'])): ?>
                                    <div>
                                        <span class="text-muted d-block mb-1">Kategori Format:</span>
                                        <span class="badge radius-6 px-2 py-1 small border" style="background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe;">
                                            <i class="fas fa-tag me-1"></i> <?= esc($item['document_category']) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                <?php if (! empty($item['document_version'])): ?>
                                    <div>
                                        <span class="text-muted d-block mb-1">Versi Dokumen:</span>
                                        <strong class="text-dark fw-semibold"><i class="fas fa-code-branch text-primary me-1"></i> <?= esc($item['document_version']) ?></strong>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <div>
                                <span class="text-muted d-block mb-1">Estimasi Membaca:</span>
                                <strong class="text-dark fw-semibold"><i class="far fa-clock text-primary me-1"></i> <?= esc($readingTime) ?> Menit</strong>
                            </div>
                            <?php if (! empty($item['author_name'])): ?>
                                <div>
                                    <span class="text-muted d-block mb-1">Penulis / Narasumber:</span>
                                    <strong class="text-dark fw-semibold"><?= esc($item['author_name']) ?></strong>
                                </div>
                            <?php endif; ?>
                            <?php if (! empty($item['published_at'])): ?>
                                <div>
                                    <span class="text-muted d-block mb-1">Tanggal Terbit:</span>
                                    <strong class="text-dark fw-semibold"><?= esc(date('d F Y', strtotime($item['published_at']))) ?></strong>
                                </div>
                            <?php endif; ?>
                            <?php if (! empty($fileMeta)): ?>
                                <div class="pt-2 border-top">
                                    <span class="text-muted d-block mb-1">Berkas Lampiran:</span>
                                    <strong class="text-primary d-block text-truncate" title="<?= esc($fileMeta['original_name']) ?>">
                                        <?= esc($fileMeta['original_name']) ?>
                                    </strong>
                                    <small class="text-muted">
                                        <?= esc(strtoupper($fileMeta['extension'])) ?> &middot; <?= number_format((float) $fileMeta['size_bytes'] / 1024, 1) ?> KB
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Widget 2: Quick Sub-Menu Navigation -->
                    <div class="card border-0 shadow-sm radius-16 p-4 bg-white">
                        <h4 class="fw-bold mb-3 pb-2 border-bottom" style="font-size: 17px; color: #082f5c;">
                            <i class="fas fa-th-large text-primary me-2"></i> Eksplorasi Bunga Rampai
                        </h4>
                        <ul class="list-unstyled m-0 d-flex flex-column gap-2 small">
                            <li>
                                <a href="<?= site_url('bunga-rampai/artikel') ?>" class="text-decoration-none d-flex align-items-center justify-content-between p-2 radius-8 hover-bg-light text-secondary">
                                    <span><i class="fas fa-newspaper text-primary me-2"></i> Artikel PBJ</span>
                                    <span class="badge bg-light text-muted border"><?= (int) ($stats['total_articles'] ?? 0) ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= site_url('bunga-rampai/faq') ?>" class="text-decoration-none d-flex align-items-center justify-content-between p-2 radius-8 hover-bg-light text-secondary">
                                    <span><i class="fas fa-question-circle text-success me-2"></i> Tanya Jawab (FAQ)</span>
                                    <span class="badge bg-light text-muted border"><?= (int) ($stats['total_faqs'] ?? 0) ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= site_url('bunga-rampai/studi-kasus') ?>" class="text-decoration-none d-flex align-items-center justify-content-between p-2 radius-8 hover-bg-light text-secondary">
                                    <span><i class="fas fa-briefcase text-danger me-2"></i> Studi Kasus</span>
                                    <span class="badge bg-light text-muted border"><?= (int) ($stats['total_cases'] ?? 0) ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= site_url('bunga-rampai/template-dokumen') ?>" class="text-decoration-none d-flex align-items-center justify-content-between p-2 radius-8 hover-bg-light text-secondary">
                                    <span><i class="fas fa-file-invoice text-warning me-2"></i> Template Dokumen</span>
                                    <span class="badge bg-light text-muted border"><?= (int) ($stats['total_templates'] ?? 0) ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= site_url('bunga-rampai/media') ?>" class="text-decoration-none d-flex align-items-center justify-content-between p-2 radius-8 hover-bg-light text-secondary">
                                    <span><i class="fas fa-photo-video text-info me-2"></i> Media &amp; Infografik</span>
                                    <span class="badge bg-light text-muted border"><?= (int) ($stats['total_media'] ?? 0) ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= site_url('bunga-rampai/video') ?>" class="text-decoration-none d-flex align-items-center justify-content-between p-2 radius-8 hover-bg-light text-secondary">
                                    <span><i class="fas fa-video text-danger me-2"></i> Video Pembelajaran</span>
                                    <span class="badge bg-light text-muted border"><?= (int) ($stats['total_videos'] ?? 0) ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= site_url('bunga-rampai/galeri') ?>" class="text-decoration-none d-flex align-items-center justify-content-between p-2 radius-8 hover-bg-light text-secondary">
                                    <span><i class="fas fa-images text-purple me-2"></i> Galeri Dokumentasi</span>
                                    <span class="badge bg-light text-muted border"><?= (int) ($stats['total_gallery'] ?? 0) ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Widget 3: CTA Banner ke Arena Belajar -->
                    <div class="card border-0 radius-16 p-4 text-white text-center shadow-sm" style="background: #082f5c;">
                        <i class="fas fa-graduation-cap mx-auto mb-2 text-warning" style="font-size: 36px;"></i>
                        <h5 class="text-white fw-bold mb-2" style="font-size: 16px;">Ingin Meningkatkan Sertifikasi?</h5>
                        <p class="small text-white-80 mb-3" style="font-size: 13px; color: rgba(255,255,255,0.85);">
                            Daftarkan diri Anda ke program pembelajaran dan kelas interaktif di Arena Belajar BegawiPengadaan.
                        </p>
                        <a href="<?= site_url('arena-belajar') ?>" class="btn btn-warning text-dark fw-bold radius-8 btn-sm px-3 py-2">
                            Buka Arena Belajar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     RELATED CONTENT SHOWCASE
     ========================================================================= -->
<?php if (! empty($related)): ?>
    <section class="py-5 bg-white border-top">
        <div class="container">
            <div class="d-flex flex-wrap align-items-end justify-content-between mb-4">
                <div class="arena-section-head mb-0">
                    <span class="arena-section-head__tag">
                        <i class="fas fa-bookmark me-1"></i> Rekomendasi Lanjutan
                    </span>
                    <h3 class="arena-section-head__title">Materi &amp; Referensi Terkait</h3>
                    <p class="arena-section-head__desc mb-0">
                        Kumpulan referensi relevan lainnya untuk memperkaya pemahaman Anda.
                    </p>
                </div>
                <a href="<?= esc($catalogUrl) ?>" class="btn btn-outline-primary radius-8 px-4 py-2 mt-3 mt-md-0 d-inline-flex align-items-center gap-2 fw-semibold">
                    <span>Lihat Semua <?= esc($catalogTitle) ?></span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="row g-4">
                <?php foreach ($related as $rel):
                    $relRoute = match ($rel['type']) {
                        'case_study' => 'public.knowledge.case-study.detail',
                        'template'   => 'public.knowledge.templates.detail',
                        default      => 'public.knowledge.article.detail',
                    };
                    $relUrl = route_to($relRoute, $rel['slug']);
                    $relBookmarkType = match ($rel['type']) {
                        'case_study' => 'knowledge_case',
                        'template'   => 'knowledge_template',
                        default      => 'knowledge_article',
                    };
                ?>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="card h-100 border-0 shadow-sm radius-16 p-4 d-flex flex-column transition-hover" style="border: 1px solid #edf2f7 !important;">
                            <div class="d-flex align-items-center gap-2 text-muted small mb-2" style="font-size: 12px;">
                                <span><i class="far fa-calendar-alt me-1"></i> <?= ! empty($rel['published_at']) ? esc(date('d M Y', strtotime($rel['published_at']))) : 'Terbaru' ?></span>
                                <span>&middot;</span>
                                <span><i class="far fa-clock me-1"></i> <?= esc($rel['reading_time'] ?? 5) ?> mnt</span>
                            </div>
                            <h5 class="fw-bold mb-2" style="font-size: 16px; line-height: 1.45; color: #082f5c;">
                                <a href="<?= esc($relUrl) ?>" class="text-decoration-none text-reset hover-primary">
                                    <?= esc($rel['title']) ?>
                                </a>
                            </h5>
                            <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= esc($rel['summary'] ?? '') ?>
                            </p>
                            <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                <a href="<?= esc($relUrl) ?>" class="small text-primary fw-semibold text-decoration-none hover-underline">
                                    Baca Materi <i class="fas fa-chevron-right ms-1" style="font-size: 10px;"></i>
                                </a>
                                <?= view('landing/components/bookmark_button', [
                                    'type'  => $relBookmarkType,
                                    'id'    => (int) $rel['id'],
                                    'title' => $rel['title'] ?? null,
                                    'size'  => 'sm',
                                ]) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?= $this->endSection() ?>