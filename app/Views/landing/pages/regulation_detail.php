<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-news.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-news.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-news.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-events.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-events.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-events.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-learning.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-learning.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-learning.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-regulation.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-regulation.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-regulation.css') : '1.0' ?>" />
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
    .meta-info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 11px 0;
        border-bottom: 1px solid #edf2f7;
        font-size: 13.5px;
    }
    .meta-info-row:last-child {
        border-bottom: none;
    }
    .meta-info-label {
        color: #475569 !important;
        font-weight: 500;
    }
    .meta-info-value {
        color: #052143 !important;
        font-weight: 600;
    }
    /* Sidebar Category List Matching Arena Belajar & High Contrast */
    .berita-catlist {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .berita-catlist a {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 9px 12px !important;
        border-radius: 10px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #1e293b !important;
        background: #f8fafc !important;
        border: 1px solid #f1f5f9 !important;
        text-decoration: none !important;
        transition: all 0.2s ease !important;
    }
    .berita-catlist a:hover,
    .berita-catlist a.is-active {
        background: #eff6ff !important;
        color: #0284c7 !important;
        border-color: #bae6fd !important;
    }
    .berita-catlist a .left {
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        min-width: 0 !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }
    .berita-catlist a .dot {
        width: 8px !important;
        height: 8px !important;
        border-radius: 50% !important;
        flex-shrink: 0 !important;
    }
    .berita-catlist a .count {
        background: #ffffff !important;
        color: #475569 !important;
        border: 1px solid #e2e8f0 !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        padding: 2px 7px !important;
        border-radius: 999px !important;
        flex-shrink: 0 !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$regulation    = $regulation ?? null;
$versions      = $versions ?? [];
$relations     = $relations ?? [];
$competencies  = $competencies ?? [];
$related       = $related ?? [];
$categories    = $categories ?? [];
$stats         = $stats ?? [];
$viewMode      = $viewMode ?? 'detail';
$latestVersion = ! empty($versions) ? $versions[0] : null;

$legalStatus = strtolower((string) ($regulation['legal_status'] ?? 'active'));
$renderLegalStatusBadge = function (string $status): string {
    return match ($status) {
        'active', 'berlaku' => '<span class="badge radius-6 px-2 py-1 small fw-bold d-inline-flex align-items-center gap-1" style="background: #dcfce7; color: #15803d; border: 1px solid #86efac;"><i class="fas fa-check-circle"></i> Status: Berlaku</span>',
        'amended', 'diubah'  => '<span class="badge radius-6 px-2 py-1 small fw-bold d-inline-flex align-items-center gap-1" style="background: #fef3c7; color: #b45309; border: 1px solid #fde047;"><i class="fas fa-exclamation-circle"></i> Status: Telah Diubah</span>',
        'revoked', 'dicabut' => '<span class="badge radius-6 px-2 py-1 small fw-bold d-inline-flex align-items-center gap-1" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5;"><i class="fas fa-times-circle"></i> Status: Telah Dicabut</span>',
        'expired', 'kedaluwarsa' => '<span class="badge radius-6 px-2 py-1 small fw-bold d-inline-flex align-items-center gap-1" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;"><i class="fas fa-clock"></i> Status: Kedaluwarsa</span>',
        default => '<span class="badge radius-6 px-2 py-1 small fw-bold d-inline-flex align-items-center gap-1" style="background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe;"><i class="fas fa-info-circle"></i> Status: ' . esc(ucfirst($status ?: 'Resmi')) . '</span>',
    };
};

$hasFile = ! empty($latestVersion['file_id']) || ! empty($regulation['current_file_id']);
$fileSize = ! empty($latestVersion['size_bytes']) ? number_format((float) $latestVersion['size_bytes'] / 1024, 0) . ' KB' : (! empty($regulation['current_file_size']) ? number_format((float) $regulation['current_file_size'] / 1024, 0) . ' KB' : 'PDF');
$downloadUrl = site_url('regulasi/' . esc($regulation['slug'] ?? '') . '/unduh');
$previewUrl  = site_url('regulasi/' . esc($regulation['slug'] ?? '') . '/preview');
?>

<!-- =========================================================================
     MAIN DETAIL REGULASI — Clean Magazine Benchmark (Patokan Bunga Rampai)
     ========================================================================= -->
<section class="berita-main-section evt-main-section pt-2 pt-md-3 pt-lg-4 pb-5" style="background: #fafbfc; min-height: 75vh;">
    <div class="container">

        <!-- Compact Breadcrumb Navigation -->
        <div class="arena-breadcrumb evt-breadcrumb mb-3">
            <a href="<?= site_url('/') ?>"><iconify-icon icon="solar:home-2-bold" class="align-middle"></iconify-icon> Beranda</a>
            <span class="sep">/</span>
            <a href="<?= site_url('regulasi') ?>">Regulasi PBJ</a>
            <?php if (! empty($regulation['category_name'])): ?>
                <span class="sep">/</span>
                <a href="<?= site_url('regulasi/kategori/' . esc($regulation['category_slug'] ?? '')) ?>"><?= esc($regulation['category_name']) ?></a>
            <?php endif; ?>
            <span class="sep">/</span>
            <span class="current text-truncate" style="max-width: 320px;"><?= esc($regulation['document_number'] ?? 'Detail') ?></span>
        </div>

        <!-- Compact Detail Hero Banner with Glass Quick Summary Card (Exact Bunga Rampai Detail Style) -->
        <div class="arena-hero evt-detail-hero radius-16 p-3 p-md-4 mb-4 text-white position-relative" style="background: linear-gradient(135deg, #052244 0%, #0a3d74 100%); overflow: hidden;">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <?= $renderLegalStatusBadge($legalStatus) ?>
                        <span class="badge radius-6 px-2 py-1 small fw-semibold" style="background: rgba(255,255,255,0.18); color: #fff; border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(4px);">
                            <i class="fas fa-layer-group me-1"></i> <?= esc($regulation['category_name'] ?? 'Umum') ?>
                        </span>
                        <span class="badge radius-6 px-2 py-1 small fw-semibold" style="background: rgba(255,255,255,0.18); color: #fff; border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(4px);">
                            <i class="fas fa-calendar me-1"></i> Tahun <?= esc($regulation['document_year'] ?? '-') ?>
                        </span>
                        <span class="badge radius-6 px-2 py-1 small fw-semibold" style="background: rgba(56, 189, 248, 0.22); color: #7dd3fc; border: 1px solid rgba(56, 189, 248, 0.4);">
                            <i class="fas fa-code-branch me-1"></i> v<?= esc($regulation['current_version_number'] ?? '1') ?>
                        </span>
                    </div>

                    <h1 class="text-white mb-2" style="font-size: clamp(20px, 2.5vw, 26px); font-weight: 800; line-height: 1.3;">
                        <?= esc($regulation['document_number'] ?? 'Regulasi Tanpa Nomor') ?>
                    </h1>
                    <p class="text-white text-opacity-90 fs-6 mb-3" style="line-height: 1.6; max-width: 960px;">
                        <?= esc($regulation['title'] ?? '-') ?>
                    </p>

                    <div class="d-flex flex-wrap align-items-center gap-3 text-white-80 small mb-3" style="font-size: 12.5px;">
                        <span><i class="fas fa-university me-1 text-warning"></i> <?= esc($regulation['issuer_name'] ?? 'Pemerintah') ?></span>
                        <span>&middot;</span>
                        <span><i class="fas fa-calendar-alt me-1 text-info"></i> Ditetapkan: <?= ! empty($regulation['official_date']) ? date('d F Y', strtotime($regulation['official_date'])) : '-' ?></span>
                        <span>&middot;</span>
                        <span><i class="fas fa-calendar-check me-1 text-success"></i> Berlaku: <?= ! empty($regulation['effective_date']) ? date('d F Y', strtotime($regulation['effective_date'])) : '-' ?></span>
                    </div>

                    <!-- Action Buttons in Hero -->
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <a href="<?= site_url('regulasi') ?>" class="btn btn-outline-light btn-sm fw-semibold px-3 py-1 radius-8 d-inline-flex align-items-center gap-1" style="font-size: 12.5px;">
                            <i class="fas fa-arrow-left"></i>
                            <span>Semua Regulasi</span>
                        </a>

                        <?php if ($hasFile): ?>
                            <a href="<?= $downloadUrl ?>" class="btn btn-warning text-dark fw-bold btn-sm radius-8 px-3 py-1 d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 12.5px;">
                                <i class="fas fa-download"></i>
                                <span>Unduh Dokumen</span>
                                <small class="opacity-75">(<?= esc($fileSize) ?>)</small>
                            </a>
                            <a href="<?= $previewUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-light btn-sm radius-8 px-3 py-1 d-inline-flex align-items-center gap-1 fw-semibold" style="background: rgba(255,255,255,0.12); border-color: rgba(255,255,255,0.3); font-size: 12.5px;">
                                <i class="fas fa-eye"></i>
                                <span>Pratinjau PDF</span>
                            </a>
                        <?php else: ?>
                            <span class="badge bg-white bg-opacity-20 text-white p-2 px-3 radius-8" style="font-size: 12px;">
                                <i class="fas fa-info-circle me-1"></i> Berkas digital fisik/sedang diverifikasi
                            </span>
                        <?php endif; ?>

                        <?php if (! empty($regulation['official_source_url'])): ?>
                            <a href="<?= esc($regulation['official_source_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-light btn-sm radius-8 px-3 py-1 d-inline-flex align-items-center gap-1 fw-semibold" style="background: rgba(255,255,255,0.12); border-color: rgba(255,255,255,0.3); font-size: 12.5px;">
                                <i class="fas fa-external-link-alt"></i>
                                <span>Sumber Resmi (JDIH/LKPP)</span>
                            </a>
                        <?php endif; ?>

                        <?= view('landing/components/bookmark_button', [
                            'type'     => 'regulation',
                            'id'       => (int) $regulation['id'],
                            'title'    => $regulation['title'] ?? null,
                            'size'     => 'sm',
                            'iconOnly' => false,
                        ]) ?>

                        <button type="button" class="btn btn-outline-light btn-sm radius-8 px-3 py-1 d-inline-flex align-items-center gap-1 fw-semibold" style="background: rgba(255,255,255,0.12); border-color: rgba(255,255,255,0.3); font-size: 12.5px;" onclick="navigator.clipboard.writeText(window.location.href); alert('Tautan regulasi berhasil disalin ke clipboard!');" title="Salin Tautan">
                            <i class="fas fa-share-alt"></i>
                            <span>Bagikan</span>
                        </button>
                    </div>
                </div>

                <!-- Glass Quick Summary Card in Hero -->
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <div class="p-3 radius-12 text-start" style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2);">
                        <span class="small fw-bold text-uppercase d-block mb-2" style="color: rgba(255,255,255,0.85); letter-spacing: 0.5px; font-size: 11.5px;">Ringkasan Status Legal:</span>
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom text-white small" style="border-color: rgba(255,255,255,0.15) !important;">
                            <span><i class="fas fa-layer-group me-1 text-warning"></i> Kategori</span>
                            <span class="fw-bold"><?= esc($regulation['category_name'] ?? 'Umum') ?></span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom text-white small" style="border-color: rgba(255,255,255,0.15) !important;">
                            <span><i class="fas fa-balance-scale me-1 text-success"></i> Keberlakuan</span>
                            <div><?= $renderLegalStatusBadge($legalStatus) ?></div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom text-white small" style="border-color: rgba(255,255,255,0.15) !important;">
                            <span><i class="fas fa-code-branch me-1 text-info"></i> Versi Naskah</span>
                            <span class="fw-bold">v<?= esc($regulation['current_version_number'] ?? '1') ?></span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between text-white small">
                            <span><i class="fas fa-download me-1 text-warning"></i> Total Unduhan</span>
                            <span class="fw-bold"><?= number_format((int) ($regulation['download_count'] ?? 0)) ?>x</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash messages -->
        <?php if ($msg = session()->getFlashdata('success')): ?>
            <div class="alert alert-success radius-12 p-3 mb-4 shadow-sm border-0 d-flex align-items-center gap-2">
                <i class="fas fa-check-circle text-success fs-5"></i>
                <div><?= esc($msg) ?></div>
            </div>
        <?php endif; ?>
        <?php if ($msg = session()->getFlashdata('error')): ?>
            <div class="alert alert-danger radius-12 p-3 mb-4 shadow-sm border-0 d-flex align-items-center gap-2">
                <i class="fas fa-exclamation-triangle text-danger fs-5"></i>
                <div><?= esc($msg) ?></div>
            </div>
        <?php endif; ?>

        <!-- MAIN 2-COLUMN LAYOUT -->
        <div class="row g-4">
            <!-- Left Main Column (8 cols) -->
            <div class="col-xl-8 col-lg-7">
                <article class="card border-0 shadow-2xs radius-16 p-4 p-md-5 bg-white border">
                    <!-- Tab Navigation Bar -->
                    <div class="regulation-tab-nav mb-4">
                        <button type="button" class="regulation-tab-btn <?= $viewMode === 'detail' ? 'active' : '' ?>" onclick="switchTab('tab-ringkasan', this)">
                            <i class="fas fa-align-left"></i> Ringkasan &amp; Pokok
                        </button>
                        <button type="button" class="regulation-tab-btn <?= $viewMode === 'relations' ? 'active' : '' ?>" onclick="switchTab('tab-relasi', this)">
                            <i class="fas fa-project-diagram"></i> Relasi Regulasi (<?= count($relations) ?>)
                        </button>
                        <button type="button" class="regulation-tab-btn <?= $viewMode === 'versions' ? 'active' : '' ?>" onclick="switchTab('tab-versi', this)">
                            <i class="fas fa-history"></i> Riwayat Versi (<?= count($versions) ?>)
                        </button>
                        <button type="button" class="regulation-tab-btn" onclick="switchTab('tab-tahapan', this)">
                            <i class="fas fa-tasks"></i> Tahapan &amp; Bidang PBJ
                        </button>
                    </div>

                    <!-- TAB 1: RINGKASAN & POKOK PENGATURAN -->
                    <div id="tab-ringkasan" class="tab-pane-content" style="<?= $viewMode === 'detail' ? '' : 'display: none;' ?>">
                        <!-- Executive Summary Callout Box -->
                        <?php if (! empty($regulation['summary'])): ?>
                            <div class="p-4 radius-12 mb-4" style="background: #f8fafc; border-left: 4px solid #0284c7; border: 1px solid #e2e8f0;">
                                <h6 class="fw-bold mb-2 text-uppercase" style="font-size: 12.5px; letter-spacing: 0.5px; color: #0369a1;">
                                    <i class="fas fa-info-circle me-1"></i> Ringkasan Materi &amp; Pokok Pengaturan
                                </h6>
                                <p class="mb-0" style="color: #0f172a; font-size: 15px; line-height: 1.7; font-weight: 500;">
                                    <?= nl2br(esc($regulation['summary'])) ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="p-4 radius-12 mb-4" style="background: #f8fafc; border-left: 4px solid #94a3b8; border: 1px solid #e2e8f0;">
                                <p class="fst-italic mb-0" style="color: #64748b; font-size: 14.5px;">
                                    Ringkasan khusus belum tersedia untuk regulasi ini. Silakan unduh naskah PDF lengkap untuk membaca konsiderans, pasal, dan lampiran resmi.
                                </p>
                            </div>
                        <?php endif; ?>

                        <!-- Callout Box Dasar Keberlakuan Hukum -->
                        <div class="p-4 radius-12 mb-4 d-flex align-items-start gap-3" style="background: #f8fafc; border-left: 4px solid #16a34a; border: 1px solid #e2e8f0;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: #dcfce7; color: #15803d;">
                                <i class="fas fa-balance-scale fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1" style="color: #052143; font-size: 15px;">Kepastian Hukum &amp; Landasan Pengadaan</h6>
                                <p class="small mb-0" style="color: #334155; font-size: 13.5px; line-height: 1.6;">
                                    Regulasi ini diterbitkan secara sah oleh <strong style="color: #0f172a;"><?= esc($regulation['issuer_name'] ?? 'Pemerintah') ?></strong>
                                    pada tanggal <?= ! empty($regulation['official_date']) ? date('d F Y', strtotime($regulation['official_date'])) : '-' ?>
                                    dan berlaku efektif sejak <?= ! empty($regulation['effective_date']) ? date('d F Y', strtotime($regulation['effective_date'])) : '-' ?>.
                                </p>
                            </div>
                        </div>

                        <!-- Callout Box Sumber Resmi Eksternal -->
                        <?php if (! empty($regulation['official_source_url'])): ?>
                            <div class="alert border radius-12 d-flex flex-wrap align-items-center justify-content-between p-3 mb-4 gap-2" style="background: #f8fafc; border-color: #cbd5e1 !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-external-link-alt text-primary"></i>
                                    <span class="small fw-semibold text-dark">Rujukan Naskah Resmi Asli (JDIH / LKPP):</span>
                                </div>
                                <a href="<?= esc($regulation['official_source_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary radius-6 fw-semibold">
                                    Buka Portal Sumber Asli <i class="fas fa-arrow-up-right-from-square ms-1" style="font-size: 11px;"></i>
                                </a>
                            </div>
                        <?php endif; ?>

                        <!-- High-End Navy Download Card Widget (Like Template Download Widget) -->
                        <?php if ($hasFile): ?>
                            <div class="card border-0 radius-16 p-4 mb-4 text-white shadow-xs" style="background: linear-gradient(135deg, #082f5c 0%, #1e3a8a 100%);">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-flex align-items-center justify-content-center radius-12 bg-white text-primary fw-bold shadow-xs" style="width: 54px; height: 54px; font-size: 22px;">
                                            <i class="fas fa-file-pdf text-danger"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge bg-warning text-dark radius-6 px-2 py-0 small">Naskah Resmi PBJ</span>
                                                <span class="badge bg-white bg-opacity-25 text-white radius-6 px-2 py-0 small"><?= esc($regulation['category_name'] ?? 'Regulasi') ?></span>
                                                <span class="badge bg-info bg-opacity-25 text-white radius-6 px-2 py-0 small">v<?= esc($regulation['current_version_number'] ?? '1') ?></span>
                                            </div>
                                            <h5 class="text-white fw-bold mb-1" style="font-size: 16px;"><?= esc($regulation['document_number'] ?? 'Naskah Regulasi PDF') ?></h5>
                                            <small class="text-white-80" style="color: rgba(255,255,255,0.85); font-size: 12px;">
                                                Format Resmi PDF &middot; Ukuran: <?= esc($fileSize) ?> &middot; Diverifikasi JDIH
                                            </small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="<?= $downloadUrl ?>" class="btn btn-warning text-dark fw-bold radius-8 px-4 py-2 d-inline-flex align-items-center gap-2 shadow-xs">
                                            <i class="fas fa-download"></i>
                                            <span>Unduh Dokumen Sekarang</span>
                                        </a>
                                        <a href="<?= $previewUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-light radius-8 px-3 py-2 d-inline-flex align-items-center gap-1 fw-semibold" style="background: rgba(255,255,255,0.12); border-color: rgba(255,255,255,0.3);">
                                            <i class="fas fa-eye"></i>
                                            <span>Pratinjau</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="mt-3 pt-3 border-top border-white border-opacity-10 small" style="color: rgba(255,255,255,0.95); font-size: 12.5px; font-weight: 500;">
                                    <i class="fas fa-info-circle me-1 text-warning"></i> Catatan: Dokumen ini bersumber dari JDIH resmi dan dapat dijadikan dasar hukum dalam perencanaan, pemilihan, maupun pelaksanaan kontrak pengadaan.
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Action Toolbar & Navigation -->
                        <div class="pt-4 border-top d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <a href="<?= site_url('regulasi') ?>" class="btn btn-outline-secondary radius-8 px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2" style="font-size: 13px;">
                                <i class="fas fa-arrow-left"></i>
                                <span>Kembali ke Katalog Regulasi</span>
                            </a>
                            <div class="d-flex align-items-center gap-2">
                                <?= view('landing/components/bookmark_button', [
                                    'type'  => 'regulation',
                                    'id'    => (int) $regulation['id'],
                                    'title' => $regulation['title'] ?? null,
                                    'size'  => 'md',
                                ]) ?>
                                <button type="button" class="btn btn-outline-primary radius-8 px-3 py-2 fw-semibold" style="font-size: 13px;" onclick="navigator.clipboard.writeText(window.location.href); alert('Tautan berhasil disalin ke clipboard!');">
                                    <i class="fas fa-share-alt me-1"></i> Bagikan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: MATRIKS RELASI REGULASI -->
                    <div id="tab-relasi" class="tab-pane-content" style="<?= $viewMode === 'relations' ? '' : 'display: none;' ?>">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h3 class="h5 fw-bold mb-0 d-flex align-items-center gap-2" style="color: #052143;">
                                <i class="fas fa-project-diagram text-primary"></i>
                                Matriks Hubungan &amp; Keterkaitan Regulasi
                            </h3>
                            <span class="badge radius-6 px-2 py-1 small fw-semibold" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">
                                <?= count($relations) ?> Hubungan
                            </span>
                        </div>
                        <p class="small mb-4" style="color: #475569;">
                            Daftar peraturan yang mengubah, mencabut, melaksanakan, atau dirujuk oleh dokumen ini dalam hierarki regulasi PBJ.
                        </p>

                        <?php if (empty($relations)): ?>
                            <div class="p-4 radius-12 text-center" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <i class="fas fa-link text-muted mb-2 fs-3"></i>
                                <p class="mb-0 small" style="color: #64748b;">Belum ada relasi khusus yang terdaftar untuk regulasi ini.</p>
                            </div>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($relations as $rel):
                                    $relType = $rel['relation_type'] ?? '';
                                    $relTitle = $rel['related_title'] ?? '-';
                                    $relDocNumber = $rel['related_document_number'] ?? $relTitle;
                                    $relSlug = $rel['related_slug'] ?? '';

                                    $typeConfig = match ($relType) {
                                        'amends', 'amended_by'         => ['label' => 'Mengubah / Diubah', 'bg' => 'background: #fef3c7; color: #b45309; border: 1px solid #fde047;', 'icon' => 'fa-pen'],
                                        'revokes', 'revoked_by'        => ['label' => 'Mencabut / Dicabut', 'bg' => 'background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5;', 'icon' => 'fa-ban'],
                                        'implements', 'implemented_by' => ['label' => 'Melaksanakan', 'bg' => 'background: #dcfce7; color: #15803d; border: 1px solid #86efac;', 'icon' => 'fa-check-circle'],
                                        'references', 'referenced_by' => ['label' => 'Merujuk / Dirujuk', 'bg' => 'background: #e0f2fe; color: #0369a1; border: 1px solid #7dd3fc;', 'icon' => 'fa-book-reader'],
                                        default                        => ['label' => ucwords(str_replace('_', ' ', $relType)), 'bg' => 'background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;', 'icon' => 'fa-link'],
                                    };
                                ?>
                                    <div class="p-3 radius-12 d-flex flex-wrap align-items-center justify-content-between gap-3 transition-hover" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="badge radius-8 px-3 py-2 small fw-bold text-nowrap" style="<?= $typeConfig['bg'] ?>">
                                                <i class="fas <?= $typeConfig['icon'] ?> me-1"></i> <?= $typeConfig['label'] ?>
                                            </span>
                                            <div>
                                                <h6 class="fw-bold mb-1">
                                                    <?php if (! empty($relSlug)): ?>
                                                        <a href="<?= site_url('regulasi/' . esc($relSlug)) ?>" class="text-decoration-none hover-primary" style="color: #052143;">
                                                            <?= esc($relDocNumber) ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span style="color: #052143;"><?= esc($relDocNumber) ?></span>
                                                    <?php endif; ?>
                                                </h6>
                                                <small class="d-block" style="color: #334155;"><?= esc($relTitle) ?></small>
                                                <?php if (! empty($rel['notes'])): ?>
                                                    <small class="d-block fst-italic mt-1" style="color: #64748b;"><i class="fas fa-info-circle me-1"></i> Catatan: <?= esc($rel['notes']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <?php if (! empty($relSlug)): ?>
                                            <div>
                                                <a href="<?= site_url('regulasi/' . esc($relSlug)) ?>" class="btn btn-sm btn-outline-primary radius-8 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                                                    <span>Buka Regulasi</span>
                                                    <i class="fas fa-chevron-right small"></i>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 3: RIWAYAT VERSI DOKUMEN -->
                    <div id="tab-versi" class="tab-pane-content" style="<?= $viewMode === 'versions' ? '' : 'display: none;' ?>">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h3 class="h5 fw-bold mb-0 d-flex align-items-center gap-2" style="color: #052143;">
                                <i class="fas fa-history text-primary"></i>
                                Riwayat Versi &amp; Perubahan Dokumen
                            </h3>
                            <span class="badge radius-6 px-2 py-1 small fw-semibold" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">
                                <?= count($versions) ?> Versi
                            </span>
                        </div>
                        <p class="small mb-4" style="color: #475569;">
                            Catatan versi dan log pembaruan dokumen digital regulasi untuk transparansi audit.
                        </p>

                        <?php if (empty($versions)): ?>
                            <p class="fst-italic mb-0" style="color: #64748b;">Tidak ada riwayat versi khusus.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="small" style="background: #f8fafc; color: #475569;">
                                        <tr>
                                            <th style="color: #475569;">Versi</th>
                                            <th style="color: #475569;">Tanggal Rilis</th>
                                            <th style="color: #475569;">Catatan Perubahan</th>
                                            <th style="color: #475569;">Status</th>
                                            <th class="text-end" style="color: #475569;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($versions as $ver):
                                            $isCurrent = (int) ($ver['is_current'] ?? 0) === 1;
                                        ?>
                                            <tr>
                                                <td>
                                                    <span class="fw-bold <?= $isCurrent ? 'text-primary' : 'text-muted' ?>">
                                                        v<?= (int) ($ver['version_number'] ?? 1) ?>
                                                    </span>
                                                </td>
                                                <td class="small" style="color: #475569;">
                                                    <?= ! empty($ver['created_at']) ? date('d M Y, H:i', strtotime($ver['created_at'])) : '-' ?>
                                                </td>
                                                <td class="small" style="color: #1e293b; max-width: 280px;">
                                                    <?= esc($ver['changelog'] ?? 'Rilis regulasi resmi.') ?>
                                                </td>
                                                <td>
                                                    <?php if ($isCurrent): ?>
                                                        <span class="badge radius-6 px-2 py-1 small fw-bold" style="background: #dcfce7; color: #15803d; border: 1px solid #86efac;">
                                                            <i class="fas fa-check-circle me-1"></i> Aktif Berlaku
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge radius-6 px-2 py-1 small" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">
                                                            Arsip
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end">
                                                    <?php if ($hasFile): ?>
                                                        <a href="<?= $downloadUrl ?>" class="btn btn-sm btn-outline-primary radius-8 px-3 py-1" title="Unduh Versi Ini">
                                                            <i class="fas fa-download me-1"></i> Unduh
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 4: TAHAPAN & BIDANG PENGADAAN -->
                    <div id="tab-tahapan" class="tab-pane-content" style="display: none;">
                        <h3 class="h5 fw-bold mb-3 d-flex align-items-center gap-2" style="color: #052143;">
                            <i class="fas fa-tasks text-primary"></i>
                            Relevansi Tahapan Siklus Pengadaan (PBJ)
                        </h3>
                        <p class="small mb-4" style="color: #475569;">
                            Tahapan dalam siklus PBJ di mana regulasi ini menjadi rujukan hukum operasional.
                        </p>

                        <?php if (! empty($regulation['stages'])): ?>
                            <div class="row g-3 mb-4">
                                <?php foreach ($regulation['stages'] as $stg): ?>
                                    <div class="col-md-6">
                                        <div class="p-3 radius-12 d-flex align-items-center gap-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center p-2 flex-shrink-0" style="width: 40px; height: 40px; background: #eff6ff; color: #1d4ed8;">
                                                <i class="fas fa-layer-group"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-0" style="color: #052143;"><?= esc($stg['name']) ?></h6>
                                                <small style="color: #64748b;">Tahapan Siklus PBJ</small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="fst-italic mb-4" style="color: #64748b;">Belum ada pemetaan tahapan spesifik.</p>
                        <?php endif; ?>

                        <!-- Bidang Keahlian Terkait -->
                        <?php if (! empty($competencies)): ?>
                            <h4 class="h6 fw-bold mb-3 pt-3 border-top d-flex align-items-center gap-2" style="color: #052143; border-color: #e2e8f0 !important;">
                                <i class="fas fa-user-graduate text-info"></i>
                                Bidang Pengadaan &amp; Wawasan Terkait
                            </h4>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($competencies as $comp): ?>
                                    <span class="badge radius-8 px-3 py-2 small fw-semibold" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">
                                        <strong><?= esc($comp['key'] ?? '') ?></strong> &middot; <?= esc($comp['name'] ?? '') ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            </div>

            <!-- Right Sidebar Column (4 cols) — Exact Bunga Rampai Benchmark -->
            <div class="col-xl-4 col-lg-5">
                <!-- Sidebar Widget 1: Metadata Summary Card -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-info-circle text-primary"></i>
                        <span>Informasi Dokumen Regulasi</span>
                    </h4>

                    <div class="meta-info-row">
                        <span class="meta-info-label">Nomor Regulasi</span>
                        <strong class="meta-info-value text-end"><?= esc($regulation['document_number'] ?? '-') ?></strong>
                    </div>

                    <div class="meta-info-row">
                        <span class="meta-info-label">Tahun Penerbitan</span>
                        <strong class="meta-info-value"><?= esc($regulation['document_year'] ?? '-') ?></strong>
                    </div>

                    <div class="meta-info-row">
                        <span class="meta-info-label">Jenis / Kategori</span>
                        <span class="badge radius-6 px-2 py-1 small fw-semibold" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;"><?= esc($regulation['category_name'] ?? 'Umum') ?></span>
                    </div>

                    <div class="meta-info-row">
                        <span class="meta-info-label">Penerbit</span>
                        <strong class="meta-info-value text-end ms-2" style="max-width: 190px;" title="<?= esc($regulation['issuer_name'] ?? '-') ?>">
                            <?= esc($regulation['issuer_name'] ?? '-') ?>
                        </strong>
                    </div>

                    <div class="meta-info-row">
                        <span class="meta-info-label">Tanggal Ditetapkan</span>
                        <span class="meta-info-value"><?= ! empty($regulation['official_date']) ? date('d F Y', strtotime($regulation['official_date'])) : '-' ?></span>
                    </div>

                    <div class="meta-info-row">
                        <span class="meta-info-label">Tanggal Berlaku</span>
                        <span class="meta-info-value"><?= ! empty($regulation['effective_date']) ? date('d F Y', strtotime($regulation['effective_date'])) : '-' ?></span>
                    </div>

                    <div class="meta-info-row">
                        <span class="meta-info-label">Status Hukum</span>
                        <div><?= $renderLegalStatusBadge($legalStatus) ?></div>
                    </div>

                    <div class="meta-info-row">
                        <span class="meta-info-label">Versi Aktif</span>
                        <span class="badge radius-6 px-2 py-1 small fw-semibold" style="background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;">v<?= esc($regulation['current_version_number'] ?? '1') ?></span>
                    </div>

                    <div class="meta-info-row">
                        <span class="meta-info-label">Total Unduhan</span>
                        <strong class="text-success"><i class="fas fa-download me-1"></i> <?= number_format((int) ($regulation['download_count'] ?? 0)) ?> kali</strong>
                    </div>

                    <?php if ($hasFile): ?>
                        <div class="mt-4 pt-3 border-top" style="border-color: #e2e8f0 !important;">
                            <a href="<?= $downloadUrl ?>" class="btn btn-primary w-100 justify-content-center py-2 fw-semibold radius-8 d-inline-flex align-items-center gap-2">
                                <i class="fas fa-download"></i>
                                <span>Unduh Naskah Resmi</span>
                                <small class="opacity-75">(<?= esc($fileSize) ?>)</small>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar Widget 2: Kategori Regulasi PBJ (Sesuai Arena Belajar) -->
                <?php if (! empty($categories)): ?>
                    <div class="berita-widget">
                        <h4 class="berita-widget__title">
                            <i class="fas fa-folder-open" style="background: #fef3c7; color: #d97706;"></i>
                            <span>Kategori Regulasi PBJ</span>
                        </h4>
                        <nav class="berita-catlist" aria-label="Kategori Regulasi">
                            <a href="<?= site_url('regulasi') ?>">
                                <span class="left"><span class="dot" style="background:#0284c7"></span>Semua Regulasi</span>
                                <span class="count"><?= (int) ($stats['total'] ?? 0) ?></span>
                            </a>
                            <?php foreach ($categories as $catItem):
                                $cSlug = $catItem['slug'] ?? '';
                                $isCActive = (int) ($regulation['category_id'] ?? 0) === (int) $catItem['id'];
                                if ((str_starts_with(strtoupper($catItem['name']), 'QA AUDIT') || str_starts_with(strtoupper($catItem['name']), 'TEST')) && ! $isCActive) {
                                    continue;
                                }
                            ?>
                                <a href="<?= site_url('regulasi/kategori/' . esc($cSlug)) ?>" class="<?= $isCActive ? 'is-active' : '' ?>">
                                    <span class="left"><span class="dot" style="background:#0c5aa6"></span><?= esc($catItem['name']) ?></span>
                                    <span class="count"><?= (int) ($catItem['total_regulations'] ?? 0) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </nav>
                    </div>
                <?php endif; ?>

                <!-- Sidebar Widget 3: Related Regulations Card -->
                <?php if (! empty($related)): ?>
                    <div class="berita-widget">
                        <h4 class="berita-widget__title">
                            <i class="fas fa-bookmark" style="background: #e0f2fe; color: #0284c7;"></i>
                            <span>Regulasi Sejenis</span>
                        </h4>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach (array_slice($related, 0, 4) as $relReg): ?>
                                <div class="p-2 radius-8 transition-hover bg-light" style="border: 1px solid #e2e8f0;">
                                    <h6 class="fw-bold mb-1" style="font-size: 13.5px;">
                                        <a href="<?= site_url('regulasi/' . esc($relReg['slug'] ?? '')) ?>" class="text-decoration-none hover-primary" style="color: #052143;">
                                            <?= esc($relReg['document_number'] ?? '-') ?>
                                        </a>
                                    </h6>
                                    <p class="small mb-0" style="color: #475569; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.5; font-size: 12px;">
                                        <?= esc($relReg['title'] ?? '-') ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Sidebar Widget 4: Nilai Kepastian Hukum PBJ -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-shield-alt" style="background: #dcfce7; color: #16a34a;"></i>
                        <span>Nilai Integritas PBJ</span>
                    </h4>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge radius-6 px-2 py-1" style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; font-size: 11px; font-weight: 700;">Kepastian Hukum</span>
                        <span class="fw-bold text-dark" style="font-size: 13.5px;">Pengadaan Bersih &amp; Akuntabel</span>
                    </div>
                    <div class="d-flex flex-column gap-2 pt-1">
                        <div class="d-flex align-items-start gap-2 p-2 radius-8 bg-light border">
                            <i class="fas fa-check-circle text-primary mt-1" style="font-size: 13px;"></i>
                            <div>
                                <strong class="d-block text-dark" style="font-size: 12.5px;">JDIH &amp; Regulasi Resmi PBJ</strong>
                                <small style="color: #334155; font-size: 12px; line-height: 1.45;">Naskah resmi dihimpun dan diverifikasi oleh Biro PBJ Provinsi Lampung.</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-2 p-2 radius-8 bg-light border">
                            <i class="fas fa-check-circle text-success mt-1" style="font-size: 13px;"></i>
                            <div>
                                <strong class="d-block text-dark" style="font-size: 12.5px;">Transparansi Perubahan</strong>
                                <small style="color: #334155; font-size: 12px; line-height: 1.45;">Matriks keterkaitan jika regulasi mengubah atau dicabut norma lain.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Widget 5: Help & Consultation Callout -->
                <div class="berita-widget p-4 text-white" style="background: linear-gradient(135deg, #052244 0%, #0a3d74 100%) !important; border-radius: 16px;">
                    <span class="badge bg-warning text-dark text-xs fw-bold px-2 py-1 mb-2">Biro PBJ Setda Lampung</span>
                    <h5 class="fw-bold text-white mb-2" style="font-size: 16px;">Bantuan &amp; Advokasi PBJ</h5>
                    <p class="text-white-80 mb-3" style="font-size: 13px; line-height: 1.6; color: rgba(255,255,255,0.85);">
                        Memerlukan bantuan interpretasi pasal atau telaah regulasi teknis? Hubungi tim pendamping Biro PBJ Setda Provinsi Lampung.
                    </p>
                    <div class="text-warning fw-semibold d-flex align-items-center gap-2 mb-3" style="font-size: 13px;">
                        <i class="fas fa-phone-alt"></i>
                        <span>Helpdesk PBJ: +62 851-1777-9783</span>
                    </div>
                    <a href="<?= site_url('arena-belajar') ?>" class="btn btn-sm btn-outline-light w-100 radius-8 fw-semibold mb-2" style="font-size: 12.5px;">
                        <i class="fas fa-graduation-cap me-1"></i> Buka Arena Belajar PBJ
                    </a>
                    <a href="<?= site_url('bunga-rampai') ?>" class="btn btn-sm btn-warning text-dark w-100 radius-8 fw-bold" style="font-size: 12.5px;">
                        <i class="fas fa-book-reader me-1"></i> Bunga Rampai Pengetahuan
                    </a>
                </div>

            </div>
        </div>

    </div>
</section>

<script>
    function switchTab(tabId, btn) {
        document.querySelectorAll('.tab-pane-content').forEach(el => {
            el.style.display = 'none';
        });
        document.querySelectorAll('.regulation-tab-btn').forEach(el => {
            el.classList.remove('active');
        });

        const target = document.getElementById(tabId);
        if (target) {
            target.style.display = 'block';
        }
        if (btn) {
            btn.classList.add('active');
        }
    }
</script>

<?= $this->endSection() ?>