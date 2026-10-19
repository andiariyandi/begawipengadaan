<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-news.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-news.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-news.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-events.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-events.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-events.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-knowledge.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-knowledge.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-knowledge.css') : '1.0' ?>" />
<style>
    @media (max-width: 767.98px) {
        .evt-detail-hero {
            padding: 18px 14px !important;
            border-radius: 12px;
        }
        .evt-detail-hero h1 {
            font-size: 20px !important;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$item         = $item ?? [];
$bookmarkType = $bookmarkType ?? 'knowledge_article';
$catalogType  = $catalogType ?? 'articles';
$fileMeta     = $fileMeta ?? null;
$related      = $related ?? [];
$stats        = $stats ?? [];
$topArticles  = $topArticles ?? [];
$topCases     = $topCases ?? [];
$topTemplates = $topTemplates ?? [];

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

$isCoverImg = false;
if (! empty($item['file_original_name'])) {
    $cExt = strtolower(pathinfo($item['file_original_name'], PATHINFO_EXTENSION));
    $isCoverImg = in_array($cExt, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'], true);
}
$coverImg = ! empty($item['cover_url'])
    ? $item['cover_url']
    : (! empty($item['cover_file_id']) && $isCoverImg ? site_url('file/' . (int) $item['cover_file_id']) : null);

$readingTime = $item['reading_time'] ?? 5;

// Case severity styles
$sev = strtolower((string) ($item['case_severity'] ?? ''));
$sevClass = match ($sev) {
    'critical' => 'background: #fee2e2; color: #991b1b; border: 1px solid #f87171; font-weight: 700;',
    'high'     => 'background: #ffedd5; color: #9a3412; border: 1px solid #fb923c; font-weight: 700;',
    'low'      => 'background: #e0f2fe; color: #075985; border: 1px solid #38bdf8; font-weight: 700;',
    default    => 'background: #fef9c3; color: #713f12; border: 1px solid #facc15; font-weight: 700;',
};
$sevText = match ($sev) {
    'critical' => 'Risiko Kritis',
    'high'     => 'Risiko Tinggi',
    'low'      => 'Risiko Rendah',
    default    => 'Risiko Sedang',
};
?>

<!-- =========================================================================
     MAIN DETAIL BUNGA RAMPAI — Clean Magazine Benchmark
     ========================================================================= -->
<section class="berita-main-section evt-main-section pt-2 pt-md-3 pt-lg-4 pb-5" style="background: #fafbfc; min-height: 75vh;">
    <div class="container">

        <!-- Compact Breadcrumb Navigation -->
        <div class="arena-breadcrumb evt-breadcrumb mb-3">
            <a href="<?= site_url('/') ?>"><iconify-icon icon="solar:home-2-bold" class="align-middle"></iconify-icon> Beranda</a>
            <span class="sep">/</span>
            <a href="<?= site_url('bunga-rampai') ?>">Bunga Rampai</a>
            <span class="sep">/</span>
            <a href="<?= esc($catalogUrl) ?>"><?= esc($catalogTitle) ?></a>
            <span class="sep">/</span>
            <span class="current text-truncate" style="max-width: 320px;"><?= esc($itemTitle) ?></span>
        </div>

        <!-- Compact Detail Hero Banner -->
        <div class="arena-hero evt-detail-hero radius-16 p-3 p-md-4 mb-4 text-white position-relative" style="background: linear-gradient(135deg, #052244 0%, #0a3d74 100%); overflow: hidden;">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="badge bg-primary text-white px-2 py-1 radius-8 small text-uppercase" style="letter-spacing: 0.5px; font-size: 11.5px;">
                            <i class="fas fa-book-reader me-1"></i> <?= esc($catalogTitle) ?>
                        </span>
                        <?php if ((int) ($item['featured'] ?? 0) === 1): ?>
                            <span class="badge bg-warning text-dark px-2 py-1 radius-8 small fw-semibold" style="font-size: 11.5px;">
                                <i class="fas fa-star me-1"></i> Pilihan Editor
                            </span>
                        <?php endif; ?>
                        <?php if (! empty($item['procurement_stage'])): ?>
                            <span class="badge px-2 py-1 radius-8 border text-white" style="font-size: 11.5px; background: rgba(255,255,255,0.18); border-color: rgba(255,255,255,0.32);">
                                <i class="fas fa-layer-group me-1"></i> <?= esc($item['procurement_stage']) ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($catalogType === 'cases' && ! empty($item['case_severity'])): ?>
                            <span class="badge radius-8 px-2 py-1 small border" style="<?= $sevClass ?>">
                                <i class="fas fa-shield-alt me-1"></i> <?= $sevText ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($catalogType === 'templates'): ?>
                            <?php if (! empty($item['document_category'])): ?>
                                <span class="badge px-2 py-1 radius-8 text-white border" style="font-size: 11.5px; background: rgba(255,255,255,0.18); border-color: rgba(255,255,255,0.32);">
                                    <i class="fas fa-tag me-1"></i> <?= esc($item['document_category']) ?>
                                </span>
                            <?php endif; ?>
                            <?php if (! empty($item['document_version'])): ?>
                                <span class="badge px-2 py-1 radius-8 text-white border" style="font-size: 11.5px; background: rgba(255,255,255,0.18); border-color: rgba(255,255,255,0.32);">
                                    <i class="fas fa-code-branch me-1"></i> <?= esc($item['document_version']) ?>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>
                        <span class="badge px-2 py-1 radius-8 text-white" style="font-size: 11.5px; background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.32);">
                            <i class="far fa-clock me-1"></i> <?= esc($readingTime) ?> menit baca
                        </span>
                    </div>

                    <h1 class="text-white mb-2" style="font-size: clamp(20px, 2.5vw, 28px); font-weight: 800; line-height: 1.3;">
                        <?= esc($itemTitle) ?>
                    </h1>

                    <div class="d-flex flex-wrap align-items-center gap-3 text-white-80 small mb-3" style="font-size: 12.5px;">
                        <span><i class="fas fa-user-circle me-1 text-warning"></i> <?= esc($item['author_name'] ?? 'Biro Pengadaan Barang & Jasa') ?></span>
                        <span>&middot;</span>
                        <span><i class="far fa-calendar-alt me-1 text-info"></i> <?= ! empty($item['published_at']) ? esc(date('d F Y', strtotime($item['published_at']))) : 'Terbaru' ?></span>
                        <?php if (! empty($item['organization_unit'])): ?>
                            <span>&middot;</span>
                            <span><i class="fas fa-building me-1 text-warning"></i> <?= esc($item['organization_unit']) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Hero Quick Action Buttons -->
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <a href="<?= esc($catalogUrl) ?>" class="btn btn-outline-light btn-sm fw-semibold px-3 py-1 radius-8 d-inline-flex align-items-center gap-1" style="font-size: 12.5px;">
                            <i class="fas fa-arrow-left"></i>
                            <span>Kembali ke <?= esc($catalogTitle) ?></span>
                        </a>
                        <?php if ($catalogType === 'templates'): ?>
                            <a href="<?= route_to('public.knowledge.templates.download', $item['slug']) ?>" class="btn btn-warning text-dark fw-bold btn-sm px-3 py-1 radius-8 shadow-sm d-inline-flex align-items-center gap-1" style="font-size: 12.5px;">
                                <i class="fas fa-download"></i>
                                <span>Unduh Berkas</span>
                            </a>
                        <?php endif; ?>
                        <?php if ($bookmarkType !== null): ?>
                            <?= view('landing/components/bookmark_button', [
                                'type'  => $bookmarkType,
                                'id'    => (int) ($item['id'] ?? 0),
                                'title' => $item['title'] ?? null,
                                'size'  => 'sm',
                            ]) ?>
                        <?php endif; ?>
                        <button type="button" class="btn btn-sm text-white border border-white border-opacity-25 radius-8 px-3 py-1 fw-semibold" style="font-size: 12.5px; background: rgba(255,255,255,0.12);" onclick="navigator.clipboard.writeText(window.location.href); alert('Tautan berhasil disalin ke clipboard!');">
                            <i class="fas fa-share-alt me-1"></i> Bagikan
                        </button>
                    </div>
                </div>

                <div class="col-lg-4 d-none d-lg-block text-end">
                    <!-- Glass Quick Summary Card in Hero -->
                    <div class="p-3 radius-12 text-start" style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2);">
                        <span class="small fw-bold text-uppercase d-block mb-2" style="color: rgba(255,255,255,0.85); letter-spacing: 0.5px; font-size: 11.5px;">Ringkasan Informasi:</span>
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom text-white small" style="border-color: rgba(255,255,255,0.15) !important;">
                            <span><i class="fas fa-folder me-1 text-warning"></i> Kategori</span>
                            <span class="fw-bold"><?= esc($catalogTitle) ?></span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom text-white small" style="border-color: rgba(255,255,255,0.15) !important;">
                            <span><i class="fas fa-check-circle me-1 text-success"></i> Status</span>
                            <span class="badge bg-success text-white" style="font-size: 11px; font-weight: 600;">Terverifikasi</span>
                        </div>
                        <?php if (! empty($item['procurement_stage'])): ?>
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom text-white small" style="border-color: rgba(255,255,255,0.15) !important;">
                                <span><i class="fas fa-layer-group me-1 text-info"></i> Tahapan PBJ</span>
                                <span class="fw-bold text-truncate" style="max-width: 140px;"><?= esc($item['procurement_stage']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex align-items-center justify-content-between text-white small">
                            <span><i class="far fa-clock me-1 text-warning"></i> Estimasi Baca</span>
                            <span class="fw-bold"><?= esc($readingTime) ?> Menit</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Alerts -->
        <?php if ($msg = session()->getFlashdata('success')): ?>
            <div class="alert alert-success radius-8 mb-3"><?= esc($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = session()->getFlashdata('error')): ?>
            <div class="alert alert-danger radius-8 mb-3"><?= esc($msg) ?></div>
        <?php endif; ?>

        <!-- MAIN 2-COLUMN DETAIL LAYOUT -->
        <div class="row g-3 g-lg-4">
            <!-- Left / Main Article Body (col-xl-8 col-lg-8) -->
            <div class="col-xl-8 col-lg-8">
                <article class="card border-0 shadow-2xs radius-16 p-4 p-md-5 bg-white border">
                    <!-- Cover Image (if available) -->
                    <?php if (! empty($coverImg)): ?>
                        <div class="mb-4 radius-12 overflow-hidden shadow-xs" style="max-height: 380px;">
                            <img src="<?= esc($coverImg) ?>" alt="<?= esc($itemTitle) ?>" class="w-100 h-100 object-fit-cover">
                        </div>
                    <?php endif; ?>

                    <!-- Executive Summary Callout Box -->
                    <?php if (! empty($item['summary'])): ?>
                        <div class="p-4 radius-12 mb-4" style="background: #f8fafc; border-left: 4px solid #0284c7; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                            <h6 class="fw-bold mb-2 text-uppercase" style="font-size: 12.5px; letter-spacing: 0.5px; color: #0369a1;">
                                <i class="fas fa-info-circle me-1"></i> Ringkasan Materi
                            </h6>
                            <p class="mb-0" style="color: #0f172a; font-size: 15px; line-height: 1.7; font-weight: 500;">
                                <?= nl2br(esc($item['summary'])) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Detailed Content Body -->
                    <div class="knowledge-content text-dark mb-4" style="font-size: 15.5px; line-height: 1.85; color: #1e293b;">
                        <?php if (! empty($item['content'])): ?>
                            <?= nl2br(esc($item['content'])) ?>
                        <?php else: ?>
                            <p class="text-muted fst-italic">Uraian konten lengkap belum tersedia.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Callout: Case Study Pelajaran Berharga -->
                    <?php if ($catalogType === 'cases' && ! empty($item['resolution_summary'])): ?>
                        <div class="card border-0 radius-16 p-4 mb-4 shadow-xs" style="background: #fff7ed; border-left: 5px solid #ea580c !important; border-top: 1px solid #ffedd5; border-right: 1px solid #ffedd5; border-bottom: 1px solid #ffedd5;">
                            <div class="d-flex align-items-center gap-2 mb-2" style="color: #9a3412;">
                                <i class="fas fa-lightbulb" style="font-size: 20px;"></i>
                                <h5 class="fw-bold mb-0 text-uppercase" style="font-size: 14px; letter-spacing: 0.5px;">Pelajaran Berharga &amp; Solusi Rekomendasi</h5>
                            </div>
                            <p class="mb-0" style="font-size: 15px; line-height: 1.7; color: #7c2d12; font-weight: 500;">
                                <?= nl2br(esc($item['resolution_summary'])) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Callout: Dasar Hukum & Rujukan Regulasi -->
                    <?php if (! empty($item['legal_basis'])): ?>
                        <div class="card border-0 radius-12 p-3 mb-4 shadow-xs" style="background: #f8fafc; border-left: 4px solid #0284c7 !important; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                            <div class="d-flex align-items-center gap-2 fw-bold small mb-1" style="color: #0369a1; font-size: 13px;">
                                <i class="fas fa-balance-scale"></i>
                                <span>Dasar Hukum &amp; Rujukan Regulasi Terkait</span>
                            </div>
                            <div style="color: #0f172a; font-size: 14px; line-height: 1.65; font-weight: 500;">
                                <?= nl2br(esc($item['legal_basis'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Callout: External Source Reference URL -->
                    <?php if (! empty($item['source_url'])): ?>
                        <div class="alert border radius-12 d-flex flex-wrap align-items-center justify-content-between p-3 mb-4 gap-2" style="background: #f8fafc; border-color: #cbd5e1 !important;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-external-link-alt text-primary"></i>
                                <span class="small fw-semibold text-dark">Rujukan Dokumen Eksternal / Sumber:</span>
                            </div>
                            <a href="<?= esc($item['source_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary radius-6 fw-semibold">
                                Buka Dokumen / Sumber Asli <i class="fas fa-arrow-up-right-from-square ms-1" style="font-size: 11px;"></i>
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Template Specific: High-End Download Card Widget -->
                    <?php if ($catalogType === 'templates'): ?>
                        <div class="card border-0 radius-16 p-4 mb-4 text-white shadow-xs" style="background: linear-gradient(135deg, #082f5c 0%, #1e3a8a 100%);">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center justify-content-center radius-12 bg-white text-primary fw-bold shadow-xs" style="width: 54px; height: 54px; font-size: 22px;">
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
                                        <h5 class="text-white fw-bold mb-1" style="font-size: 16px;"><?= esc($itemTitle) ?></h5>
                                        <small class="text-white-80" style="color: rgba(255,255,255,0.85); font-size: 12px;">
                                            <?php if (! empty($fileMeta)): ?>
                                                <?= esc($fileMeta['original_name']) ?> &middot; <?= esc(strtoupper($fileMeta['extension'])) ?> &middot; <?= number_format((float) $fileMeta['size_bytes'] / 1024, 1) ?> KB
                                            <?php else: ?>
                                                Format Siap Pakai &middot; DOCX / XLSX / PDF
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="<?= route_to('public.knowledge.templates.download', $item['slug']) ?>" class="btn btn-warning text-dark fw-bold radius-8 px-4 py-2 d-inline-flex align-items-center gap-2 shadow-xs">
                                        <i class="fas fa-download"></i>
                                        <span>Unduh Berkas Sekarang</span>
                                    </a>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-top border-white border-opacity-10 small" style="color: rgba(255,255,255,0.95); font-size: 12.5px; font-weight: 500;">
                                <i class="fas fa-info-circle me-1 text-warning"></i> Catatan: Dokumen ini bebas diunduh dan disesuaikan dengan kebutuhan teknis paket pengadaan OPD Anda.
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Action Toolbar & Navigation -->
                    <div class="pt-4 border-top d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <a href="<?= esc($catalogUrl) ?>" class="btn btn-outline-secondary radius-8 px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2" style="font-size: 13px;">
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
                            <button type="button" class="btn btn-outline-primary radius-8 px-3 py-2 fw-semibold" style="font-size: 13px;" onclick="navigator.clipboard.writeText(window.location.href); alert('Tautan berhasil disalin ke clipboard!');">
                                <i class="fas fa-share-alt me-1"></i> Bagikan
                            </button>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Right Sidebar (col-xl-4 col-lg-4) -->
            <div class="col-xl-4 col-lg-4">
                <div class="d-flex flex-column gap-3">

                    <!-- Widget 1: Metadata Card (Informasi Materi - Required by Test 4) -->
                    <div class="berita-widget">
                        <h4 class="berita-widget__title">
                            <i class="fas fa-info-circle text-primary"></i>
                            <span>Informasi Materi</span>
                        </h4>
                        <div class="d-flex flex-column gap-2 small">
                            <div class="pb-2 border-bottom">
                                <span class="d-block mb-1" style="color: #475569; font-size: 12px; font-weight: 500;">Kategori Konten:</span>
                                <strong class="text-dark fw-semibold" style="font-size: 13.5px;"><?= esc($catalogTitle) ?></strong>
                            </div>
                            <div class="pb-2 border-bottom">
                                <span class="d-block mb-1" style="color: #475569; font-size: 12px; font-weight: 500;">Status Publikasi:</span>
                                <span class="badge radius-6 px-2 py-1" style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; font-size: 11.5px; font-weight: 600;">
                                    <i class="fas fa-check-circle me-1"></i> Terverifikasi Publik
                                </span>
                            </div>
                            <?php if (! empty($item['procurement_stage'])): ?>
                                <div class="pb-2 border-bottom">
                                    <span class="d-block mb-1" style="color: #475569; font-size: 12px; font-weight: 500;">Tahapan PBJ:</span>
                                    <span class="badge radius-6 px-2 py-1 small border" style="background: #e0f2fe; color: #0369a1; border-color: #7dd3fc; font-size: 11.5px; font-weight: 600;">
                                        <i class="fas fa-layer-group me-1"></i> <?= esc($item['procurement_stage']) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php if (! empty($item['organization_unit'])): ?>
                                <div class="pb-2 border-bottom">
                                    <span class="d-block mb-1" style="color: #475569; font-size: 12px; font-weight: 500;">Unit Kerja / OPD:</span>
                                    <strong class="text-dark fw-semibold"><i class="fas fa-building text-primary me-1"></i> <?= esc($item['organization_unit']) ?></strong>
                                </div>
                            <?php endif; ?>
                            <?php if ($catalogType === 'cases' && ! empty($item['case_severity'])): ?>
                                <div class="pb-2 border-bottom">
                                    <span class="d-block mb-1" style="color: #475569; font-size: 12px; font-weight: 500;">Tingkat Risiko:</span>
                                    <span class="badge radius-6 px-2 py-1 small border" style="<?= $sevClass ?>">
                                        <i class="fas fa-shield-alt me-1"></i> <?= $sevText ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php if ($catalogType === 'templates'): ?>
                                <?php if (! empty($item['document_category'])): ?>
                                    <div class="pb-2 border-bottom">
                                        <span class="d-block mb-1" style="color: #475569; font-size: 12px; font-weight: 500;">Kategori Format:</span>
                                        <span class="badge radius-6 px-2 py-1 small border" style="background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; font-size: 11.5px; font-weight: 600;">
                                            <i class="fas fa-tag me-1"></i> <?= esc($item['document_category']) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                <?php if (! empty($item['document_version'])): ?>
                                    <div class="pb-2 border-bottom">
                                        <span class="d-block mb-1" style="color: #475569; font-size: 12px; font-weight: 500;">Versi Dokumen:</span>
                                        <strong class="text-dark fw-semibold"><i class="fas fa-code-branch text-primary me-1"></i> <?= esc($item['document_version']) ?></strong>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <div class="pb-2 border-bottom">
                                <span class="d-block mb-1" style="color: #475569; font-size: 12px; font-weight: 500;">Estimasi Membaca:</span>
                                <strong class="text-dark fw-semibold"><i class="far fa-clock text-primary me-1"></i> <?= esc($readingTime) ?> Menit</strong>
                            </div>
                            <?php if (! empty($item['author_name'])): ?>
                                <div class="pb-2 border-bottom">
                                    <span class="d-block mb-1" style="color: #475569; font-size: 12px; font-weight: 500;">Penulis / Narasumber:</span>
                                    <strong class="text-dark fw-semibold"><?= esc($item['author_name']) ?></strong>
                                </div>
                            <?php endif; ?>
                            <?php if (! empty($item['published_at'])): ?>
                                <div class="pb-2">
                                    <span class="d-block mb-1" style="color: #475569; font-size: 12px; font-weight: 500;">Tanggal Terbit:</span>
                                    <strong class="text-dark fw-semibold"><?= esc(date('d F Y', strtotime($item['published_at']))) ?></strong>
                                </div>
                            <?php endif; ?>
                            <?php if (! empty($fileMeta)): ?>
                                <div class="pt-2 border-top">
                                    <span class="text-muted d-block mb-1" style="font-size: 11.5px;">Berkas Lampiran:</span>
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

                    <!-- Widget 2: Kategori Bunga Rampai (Nav Pills) -->
                    <div class="berita-widget">
                        <h4 class="berita-widget__title">
                            <i class="fas fa-th-large" style="background: #fef3c7; color: #d97706;"></i>
                            <span>Kategori Bunga Rampai</span>
                        </h4>
                        <nav class="arena-nav-pills berita-catlist" aria-label="Kategori Bunga Rampai">
                            <a href="<?= site_url('bunga-rampai') ?>" class="arena-nav-pill">
                                <span class="left"><span class="dot" style="background:#0284c7"></span>Semua Eksplorasi</span>
                                <span class="count"><?= (int) ($stats['total_all'] ?? 0) ?></span>
                            </a>
                            <a href="<?= site_url('bunga-rampai/artikel') ?>" class="arena-nav-pill <?= $catalogType === 'articles' ? 'is-active' : '' ?>">
                                <span class="left"><span class="dot" style="background:#0284c7"></span>Artikel PBJ</span>
                                <span class="count"><?= (int) ($stats['total_articles'] ?? 0) ?></span>
                            </a>
                            <a href="<?= site_url('bunga-rampai/faq') ?>" class="arena-nav-pill <?= $catalogType === 'faqs' ? 'is-active' : '' ?>">
                                <span class="left"><span class="dot" style="background:#16a34a"></span>Tanya Jawab (FAQ)</span>
                                <span class="count"><?= (int) ($stats['total_faqs'] ?? 0) ?></span>
                            </a>
                            <a href="<?= site_url('bunga-rampai/studi-kasus') ?>" class="arena-nav-pill <?= $catalogType === 'cases' ? 'is-active' : '' ?>">
                                <span class="left"><span class="dot" style="background:#dc2626"></span>Studi Kasus</span>
                                <span class="count"><?= (int) ($stats['total_cases'] ?? 0) ?></span>
                            </a>
                            <a href="<?= site_url('bunga-rampai/template-dokumen') ?>" class="arena-nav-pill <?= $catalogType === 'templates' ? 'is-active' : '' ?>">
                                <span class="left"><span class="dot" style="background:#d97706"></span>Template Dokumen</span>
                                <span class="count"><?= (int) ($stats['total_templates'] ?? 0) ?></span>
                            </a>
                            <a href="<?= site_url('bunga-rampai/multimedia') ?>" class="arena-nav-pill <?= in_array($catalogType, ['media', 'videos', 'gallery', 'multimedia'], true) ? 'is-active' : '' ?>">
                                <span class="left"><span class="dot" style="background:#0891b2"></span>Multimedia PBJ</span>
                                <span class="count"><?= (int) (($stats['total_media'] ?? 0) + ($stats['total_videos'] ?? 0) + ($stats['total_gallery'] ?? 0)) ?></span>
                            </a>
                        </nav>
                    </div>

                    <!-- Widget 3: Top 3 Artikel Terbaru -->
                    <?php if (! empty($topArticles)): ?>
                        <div class="berita-widget">
                            <h4 class="berita-widget__title">
                                <i class="fas fa-newspaper" style="background: #e0f2fe; color: #0284c7;"></i>
                                <span>Artikel PBJ Terbaru</span>
                            </h4>
                            <?php foreach (array_slice($topArticles, 0, 3) as $aRank => $aItem): ?>
                                <div class="berita-trend">
                                    <div class="berita-trend__rank is-top">
                                        <?= $aRank + 1 ?>
                                    </div>
                                    <div class="berita-trend__body">
                                        <h5 class="berita-trend__title">
                                            <a href="<?= route_to('public.knowledge.article.detail', $aItem['slug']) ?>">
                                                <?= esc($aItem['title']) ?>
                                            </a>
                                        </h5>
                                        <div class="berita-trend__meta">
                                            <span><i class="far fa-clock text-primary me-1"></i> <?= esc($aItem['reading_time'] ?? 5) ?> mnt</span>
                                            <span>&middot;</span>
                                            <a href="<?= route_to('public.knowledge.article.detail', $aItem['slug']) ?>" class="fw-bold text-primary text-decoration-none">Baca &rarr;</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Widget 4: CTA Arena Belajar -->
                    <div class="berita-widget" style="background: linear-gradient(135deg, #052244 0%, #0a3d74 100%); color: #ffffff;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="p-2 radius-8" style="background: rgba(255,255,255,0.15);">
                                <i class="fas fa-graduation-cap text-warning" style="font-size: 20px;"></i>
                            </div>
                            <h4 class="mb-0 text-white fw-bold" style="font-size: 15.5px;">Pelatihan Interaktif PBJ</h4>
                        </div>
                        <p class="small text-white-80 mb-3" style="font-size: 12.5px; line-height: 1.5; color: rgba(255,255,255,0.85);">
                            Daftarkan diri Anda ke program kurikulum dan kelas sertifikasi di Arena Belajar BegawiPengadaan.
                        </p>
                        <a href="<?= site_url('arena-belajar') ?>" class="btn btn-sm btn-warning text-dark fw-bold w-100 radius-8">
                            Buka Arena Belajar &rarr;
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
                    <h3 class="arena-section-head__title" style="font-size: 22px;">Materi &amp; Referensi Terkait</h3>
                    <p class="arena-section-head__desc mb-0" style="font-size: 14px;">
                        Kumpulan referensi relevan lainnya untuk memperkaya pemahaman Anda.
                    </p>
                </div>
                <a href="<?= esc($catalogUrl) ?>" class="btn btn-outline-primary radius-8 px-3 py-2 mt-3 mt-md-0 d-inline-flex align-items-center gap-2 fw-semibold" style="font-size: 13px;">
                    <span>Lihat Semua <?= esc($catalogTitle) ?></span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="row g-3">
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
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-2xs radius-12 p-3 d-flex flex-column bg-white border" style="border: 1px solid #edf2f7 !important;">
                            <div class="d-flex align-items-center gap-2 text-muted small mb-2" style="font-size: 11px;">
                                <span><i class="far fa-calendar-alt me-1"></i> <?= ! empty($rel['published_at']) ? esc(date('d M Y', strtotime($rel['published_at']))) : 'Terbaru' ?></span>
                                <span>&middot;</span>
                                <span><i class="far fa-clock me-1"></i> <?= esc($rel['reading_time'] ?? 5) ?> mnt</span>
                            </div>
                            <h5 class="fw-bold mb-2" style="font-size: 15px; line-height: 1.4; color: #082f5c;">
                                <a href="<?= esc($relUrl) ?>" class="text-decoration-none text-reset hover-primary">
                                    <?= esc($rel['title']) ?>
                                </a>
                            </h5>
                            <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; font-size: 12.5px;">
                                <?= esc($rel['summary'] ?? '') ?>
                            </p>
                            <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                                <a href="<?= esc($relUrl) ?>" class="small text-primary fw-semibold text-decoration-none hover-underline" style="font-size: 12px;">
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