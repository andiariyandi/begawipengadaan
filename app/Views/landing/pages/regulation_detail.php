<?= $this->extend('landing/layouts/public') ?>

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
        'active', 'berlaku' => '<span class="badge badge-soft-success radius-6 px-3 py-2 fs-6 fw-semibold d-inline-flex align-items-center gap-2"><i class="fas fa-check-circle"></i> Status: Berlaku</span>',
        'amended', 'diubah'  => '<span class="badge badge-soft-warning radius-6 px-3 py-2 fs-6 fw-semibold d-inline-flex align-items-center gap-2"><i class="fas fa-exclamation-circle"></i> Status: Telah Diubah</span>',
        'revoked', 'dicabut' => '<span class="badge badge-soft-danger radius-6 px-3 py-2 fs-6 fw-semibold d-inline-flex align-items-center gap-2"><i class="fas fa-times-circle"></i> Status: Telah Dicabut</span>',
        'expired', 'kedaluwarsa' => '<span class="badge badge-soft-secondary radius-6 px-3 py-2 fs-6 fw-semibold d-inline-flex align-items-center gap-2"><i class="fas fa-clock"></i> Status: Kedaluwarsa</span>',
        default => '<span class="badge badge-soft-primary radius-6 px-3 py-2 fs-6 fw-semibold d-inline-flex align-items-center gap-2"><i class="fas fa-info-circle"></i> Status: ' . esc(ucfirst($status ?: 'Resmi')) . '</span>',
    };
};

$hasFile = ! empty($latestVersion['file_id']) || ! empty($regulation['current_file_id']);
$fileSize = ! empty($latestVersion['size_bytes']) ? number_format((float) $latestVersion['size_bytes'] / 1024, 0) . ' KB' : (! empty($regulation['current_file_size']) ? number_format((float) $regulation['current_file_size'] / 1024, 0) . ' KB' : 'PDF');
$downloadUrl = site_url('regulasi/' . esc($regulation['slug'] ?? '') . '/unduh');
$previewUrl  = site_url('regulasi/' . esc($regulation['slug'] ?? '') . '/preview');
?>

<style>
    /* =========================================================================
   REGULASI DETAIL DNA & HIGH-CONTRAST ACCESSIBILITY
   ========================================================================= */
    :root {
        --begawi-primary: #0c5aa6;
        --begawi-dark: #082f5c;
        --begawi-accent: #f3a20b;
    }

    /* High Contrast Soft Badges (100% Readable) */
    .badge-soft-success {
        background-color: #dcfce7 !important;
        color: #15803d !important;
        border: 1px solid #bbf7d0 !important;
        font-weight: 600;
    }

    .badge-soft-warning {
        background-color: #fef3c7 !important;
        color: #b45309 !important;
        border: 1px solid #fde68a !important;
        font-weight: 600;
    }

    .badge-soft-danger {
        background-color: #fee2e2 !important;
        color: #b91c1c !important;
        border: 1px solid #fecaca !important;
        font-weight: 600;
    }

    .badge-soft-info {
        background-color: #e0f2fe !important;
        color: #0369a1 !important;
        border: 1px solid #bae6fd !important;
        font-weight: 600;
    }

    .badge-soft-primary {
        background-color: #e0e7ff !important;
        color: #3730a3 !important;
        border: 1px solid #c7d2fe !important;
        font-weight: 600;
    }

    .badge-soft-secondary {
        background-color: #f1f5f9 !important;
        color: #334155 !important;
        border: 1px solid #e2e8f0 !important;
        font-weight: 600;
    }

    .badge-soft-dark {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        border: 1px solid #cbd5e1 !important;
        font-weight: 600;
    }

    /* Full compensation for absolute fixed header menu bar */
    .regulation-detail-hero {
        position: relative;
        padding-top: 220px;
        padding-bottom: 75px;
        background: linear-gradient(135deg, #041830 0%, #082f5c 45%, #0c5aa6 85%, #07274c 100%);
        overflow: hidden;
        color: #ffffff;
        z-index: 1;
    }

    @media (max-width: 1199px) {
        .regulation-detail-hero {
            padding-top: 195px;
            padding-bottom: 60px;
        }
    }

    @media (max-width: 767px) {
        .regulation-detail-hero {
            padding-top: 155px;
            padding-bottom: 50px;
        }
    }

    .regulation-detail-hero__overlay {
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 50% 30%, rgba(12, 90, 166, 0.45) 0%, rgba(4, 24, 48, 0.92) 80%);
        z-index: 2;
    }

    .regulation-detail-hero .container {
        position: relative;
        z-index: 3;
    }

    /* Action Buttons */
    .btn-hero-cta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--begawi-accent);
        color: var(--begawi-dark) !important;
        font-weight: 700;
        font-size: 14px;
        border-radius: 10px;
        padding: 12px 24px;
        border: none;
        box-shadow: 0 4px 15px rgba(243, 162, 11, 0.35);
        text-decoration: none;
        transition: all 0.25s ease;
    }

    .btn-hero-cta:hover {
        background: #e09405;
        color: #031427 !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(243, 162, 11, 0.45);
    }

    .btn-hero-glass {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.35);
        color: #ffffff !important;
        font-weight: 600;
        font-size: 14px;
        border-radius: 10px;
        padding: 12px 20px;
        text-decoration: none;
        backdrop-filter: blur(8px);
        transition: all 0.25s ease;
    }

    .btn-hero-glass:hover {
        background: #ffffff;
        color: var(--begawi-primary) !important;
        border-color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
    }

    .btn-reg-download {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        background: var(--begawi-primary);
        color: #ffffff !important;
        border: 1px solid var(--begawi-primary);
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-reg-download:hover {
        background: var(--begawi-dark);
        border-color: var(--begawi-dark);
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(8, 47, 92, 0.2);
    }

    .btn-reg-detail {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        background: #ffffff;
        color: var(--begawi-primary) !important;
        border: 1px solid #cbd5e1;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-reg-detail:hover {
        background: #f0f7ff;
        border-color: var(--begawi-primary);
        color: var(--begawi-primary) !important;
        transform: translateY(-1px);
    }

    /* Tab Navigation */
    .regulation-tab-nav {
        display: flex;
        gap: 8px;
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 24px;
        overflow-x: auto;
        scrollbar-width: none;
    }

    .regulation-tab-nav::-webkit-scrollbar {
        display: none;
    }

    .regulation-tab-btn {
        padding: 12px 20px;
        font-weight: 600;
        font-size: 14.5px;
        color: #64748b;
        text-decoration: none;
        border: none;
        background: none;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        white-space: nowrap;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .regulation-tab-btn:hover {
        color: var(--begawi-primary);
    }

    .regulation-tab-btn.active {
        color: var(--begawi-primary);
        border-bottom-color: var(--begawi-primary);
        font-weight: 700;
    }

    /* Sidebar metadata rows (High contrast) */
    .meta-info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
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
</style>

<!-- =========================================================================
     HERO & BREADCRUMB SECTION (UNIFIED WITH COMPENSATED PADDING)
     ========================================================================= -->
<section class="regulation-detail-hero">
    <div class="position-absolute inset-0 w-100 h-100" style="background-image: url('<?= base_url('assets/main/images/backgrounds/slider-1-1.png') ?>'); background-size: cover; background-position: center; opacity: 0.15;"></div>
    <div class="regulation-detail-hero__overlay"></div>

    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="<?= site_url('/') ?>" class="text-white text-opacity-85 text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('regulasi') ?>" class="text-white text-opacity-85 text-decoration-none">Regulasi PBJ</a></li>
                <?php if (! empty($regulation['category_name'])): ?>
                    <li class="breadcrumb-item"><a href="<?= site_url('regulasi/kategori/' . esc($regulation['category_slug'] ?? '')) ?>" class="text-white text-opacity-85 text-decoration-none"><?= esc($regulation['category_name']) ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active text-white fw-bold" aria-current="page"><?= esc($regulation['document_number'] ?? 'Detail') ?></li>
            </ol>
        </nav>

        <!-- Document Badges & Title (High Contrast Soft Badges) -->
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <?= $renderLegalStatusBadge($legalStatus) ?>
            <span class="badge badge-soft-primary px-3 py-2 fs-6">
                <i class="fas fa-layer-group me-1"></i> <?= esc($regulation['category_name'] ?? 'Umum') ?>
            </span>
            <span class="badge badge-soft-dark px-3 py-2 fs-6">
                <i class="fas fa-calendar me-1"></i> Tahun <?= esc($regulation['document_year'] ?? '-') ?>
            </span>
            <span class="badge badge-soft-info px-3 py-2 fs-6">
                <i class="fas fa-code-branch me-1"></i> v<?= esc($regulation['current_version_number'] ?? '1') ?>
            </span>
        </div>

        <h1 class="h2 fw-bold text-white mb-2 leading-tight">
            <?= esc($regulation['document_number'] ?? 'Regulasi Tanpa Nomor') ?>
        </h1>
        <p class="text-white fs-5 mb-4" style="line-height: 1.6; max-width: 960px; opacity: 0.95;">
            <?= esc($regulation['title'] ?? '-') ?>
        </p>

        <!-- Consistent Action Buttons in Hero -->
        <div class="d-flex flex-wrap align-items-center gap-3">
            <?php if ($hasFile): ?>
                <a href="<?= $downloadUrl ?>" class="btn-hero-cta">
                    <i class="fas fa-download"></i>
                    <span>Unduh Dokumen Resmi</span>
                    <small class="opacity-75">(<?= esc($fileSize) ?>)</small>
                </a>
                <a href="<?= $previewUrl ?>" target="_blank" rel="noopener noreferrer" class="btn-hero-glass">
                    <i class="fas fa-eye"></i>
                    <span>Pratinjau PDF</span>
                </a>
            <?php else: ?>
                <span class="badge bg-white bg-opacity-25 text-white p-2 px-3 radius-8">
                    <i class="fas fa-info-circle me-1"></i> Berkas digital sedang dalam proses verifikasi arsip
                </span>
            <?php endif; ?>

            <?php if (! empty($regulation['official_source_url'])): ?>
                <a href="<?= esc($regulation['official_source_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn-hero-glass">
                    <i class="fas fa-external-link-alt"></i>
                    <span>Sumber Resmi (JDIH/LKPP)</span>
                </a>
            <?php endif; ?>

            <button type="button" class="btn-hero-glass ms-sm-auto" onclick="navigator.clipboard.writeText(window.location.href); alert('Tautan regulasi berhasil disalin ke clipboard!');" title="Salin Tautan">
                <i class="fas fa-share-alt"></i>
                <span>Bagikan</span>
            </button>
        </div>
    </div>
</section>

<!-- =========================================================================
     MAIN BODY & TABS CONTENT
     ========================================================================= -->
<section class="py-5" style="background-color: #f8fafc;">
    <div class="container">
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

        <div class="row g-4">
            <!-- Left Main Column (8 cols) -->
            <div class="col-xl-8 col-lg-7">
                <!-- Tab Navigation Bar -->
                <div class="regulation-tab-nav">
                    <button type="button" class="regulation-tab-btn <?= $viewMode === 'detail' ? 'active' : '' ?>" onclick="switchTab('tab-ringkasan', this)">
                        <i class="fas fa-align-left"></i> Ringkasan & Pokok
                    </button>
                    <button type="button" class="regulation-tab-btn <?= $viewMode === 'relations' ? 'active' : '' ?>" onclick="switchTab('tab-relasi', this)">
                        <i class="fas fa-project-diagram"></i> Relasi Regulasi (<?= count($relations) ?>)
                    </button>
                    <button type="button" class="regulation-tab-btn <?= $viewMode === 'versions' ? 'active' : '' ?>" onclick="switchTab('tab-versi', this)">
                        <i class="fas fa-history"></i> Riwayat Versi (<?= count($versions) ?>)
                    </button>
                    <button type="button" class="regulation-tab-btn" onclick="switchTab('tab-tahapan', this)">
                        <i class="fas fa-tasks"></i> Tahapan & Kompetensi
                    </button>
                </div>

                <!-- TAB 1: RINGKASAN & POKOK PENGATURAN -->
                <div id="tab-ringkasan" class="tab-pane-content" style="<?= $viewMode === 'detail' ? '' : 'display: none;' ?>">
                    <div class="card border-0 shadow-sm radius-16 p-4 bg-white mb-4" style="border: 1px solid #e2e8f0 !important;">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h3 class="h5 fw-bold mb-0 d-flex align-items-center gap-2" style="color: #052143;">
                                <i class="fas fa-file-alt text-primary"></i>
                                Ringkasan Materi & Pokok Pengaturan
                            </h3>
                            <span class="badge badge-soft-secondary px-2 py-1 small">
                                Dokumen Resmi
                            </span>
                        </div>

                        <?php if (! empty($regulation['summary'])): ?>
                            <div class="mb-4" style="line-height: 1.8; font-size: 15px; color: #1e293b;">
                                <?= nl2br(esc($regulation['summary'])) ?>
                            </div>
                        <?php else: ?>
                            <p class="fst-italic mb-4" style="color: #64748b;">
                                Tidak ada ringkasan materi khusus untuk regulasi ini. Silakan unduh naskah PDF lengkap untuk membaca pasal dan lampiran resmi.
                            </p>
                        <?php endif; ?>

                        <!-- Callout Box Dasar Keberlakuan (100% Readable) -->
                        <div class="p-3 radius-12 d-flex align-items-start gap-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="rounded-circle badge-soft-primary d-flex align-items-center justify-content-center p-3 flex-shrink-0" style="width: 44px; height: 44px;">
                                <i class="fas fa-balance-scale fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1" style="color: #052143;">Kepastian Hukum & Landasan Pengadaan</h6>
                                <p class="small mb-0" style="color: #334155;">
                                    Regulasi ini diterbitkan oleh <strong style="color: #0f172a;"><?= esc($regulation['issuer_name'] ?? 'Pemerintah') ?></strong>
                                    pada tanggal <?= ! empty($regulation['official_date']) ? date('d M Y', strtotime($regulation['official_date'])) : '-' ?>
                                    dan berlaku efektif sejak <?= ! empty($regulation['effective_date']) ? date('d M Y', strtotime($regulation['effective_date'])) : '-' ?>.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Callout Download Widget -->
                    <?php if ($hasFile): ?>
                        <div class="card border-0 shadow-sm radius-16 p-4 mb-4 text-white" style="background: linear-gradient(135deg, #082f5c 0%, #0c5aa6 100%);">
                            <div class="row align-items-center">
                                <div class="col-md-8 mb-3 mb-md-0">
                                    <span class="badge bg-warning text-dark radius-6 px-2 py-1 small fw-semibold mb-2">Berkas Siap Unduh</span>
                                    <h4 class="h5 fw-bold text-white mb-1"><?= esc($regulation['document_number'] ?? 'Dokumen PDF') ?></h4>
                                    <p class="text-white text-opacity-90 small mb-0">
                                        Naskah resmi format PDF lengkap dengan konsiderans menimbang, mengingat, pasal-pasal, dan lampiran teknis (Ukuran: <?= esc($fileSize) ?>).
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <a href="<?= $downloadUrl ?>" class="btn-hero-cta">
                                        <i class="fas fa-download"></i>
                                        <span>Unduh Sekarang</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- TAB 2: MATRIKS RELASI REGULASI -->
                <div id="tab-relasi" class="tab-pane-content" style="<?= $viewMode === 'relations' ? '' : 'display: none;' ?>">
                    <div class="card border-0 shadow-sm radius-16 p-4 bg-white mb-4" style="border: 1px solid #e2e8f0 !important;">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h3 class="h5 fw-bold mb-0 d-flex align-items-center gap-2" style="color: #052143;">
                                <i class="fas fa-project-diagram text-primary"></i>
                                Matriks Hubungan & Keterkaitan Regulasi
                            </h3>
                            <span class="badge badge-soft-primary radius-6 px-2 py-1 small fw-semibold">
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
                                <?php foreach ($relations as $rel): ?>
                                    <?php
                                    $relType = $rel['relation_type'] ?? '';
                                    $direction = $rel['direction'] ?? 'outgoing';
                                    $relTitle = $rel['related_title'] ?? '-';
                                    $relDocNumber = $rel['related_document_number'] ?? $relTitle;
                                    $relSlug = $rel['related_slug'] ?? '';
                                    $relStatus = $rel['related_workflow_status'] ?? 'published';

                                    // Styling per relationship type with high-contrast badges
                                    $typeConfig = match ($relType) {
                                        'amends', 'amended_by'    => ['label' => 'Mengubah / Diubah', 'bg' => 'badge-soft-warning', 'icon' => 'fa-pen'],
                                        'revokes', 'revoked_by'   => ['label' => 'Mencabut / Dicabut', 'bg' => 'badge-soft-danger', 'icon' => 'fa-ban'],
                                        'implements', 'implemented_by' => ['label' => 'Melaksanakan', 'bg' => 'badge-soft-success', 'icon' => 'fa-check-circle'],
                                        'references', 'referenced_by' => ['label' => 'Merujuk / Dirujuk', 'bg' => 'badge-soft-info', 'icon' => 'fa-book-reader'],
                                        default         => ['label' => ucwords(str_replace('_', ' ', $relType)), 'bg' => 'badge-soft-secondary', 'icon' => 'fa-link'],
                                    };
                                    ?>
                                    <div class="p-3 radius-12 d-flex flex-wrap align-items-center justify-content-between gap-3 transition-hover" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="badge radius-8 px-3 py-2 small fw-semibold <?= $typeConfig['bg'] ?> text-nowrap">
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
                                                <a href="<?= site_url('regulasi/' . esc($relSlug)) ?>" class="btn-reg-detail">
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
                </div>

                <!-- TAB 3: RIWAYAT VERSI DOKUMEN -->
                <div id="tab-versi" class="tab-pane-content" style="<?= $viewMode === 'versions' ? '' : 'display: none;' ?>">
                    <div class="card border-0 shadow-sm radius-16 p-4 bg-white mb-4" style="border: 1px solid #e2e8f0 !important;">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h3 class="h5 fw-bold mb-0 d-flex align-items-center gap-2" style="color: #052143;">
                                <i class="fas fa-history text-primary"></i>
                                Riwayat Versi & Perubahan Dokumen
                            </h3>
                            <span class="badge badge-soft-primary radius-6 px-2 py-1 small fw-semibold">
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
                                        <?php foreach ($versions as $ver): ?>
                                            <?php
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
                                                        <span class="badge badge-soft-success radius-6 px-2 py-1 small fw-semibold">
                                                            <i class="fas fa-check-circle me-1"></i> Aktif Berlaku
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-soft-secondary radius-6 px-2 py-1 small">
                                                            Arsip
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end">
                                                    <?php if ($hasFile): ?>
                                                        <a href="<?= $downloadUrl ?>" class="btn-reg-download py-1 px-3" title="Unduh Versi Ini">
                                                            <i class="fas fa-download"></i>
                                                            <span>Unduh</span>
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
                </div>

                <!-- TAB 4: TAHAPAN PBJ & KOMPETENSI -->
                <div id="tab-tahapan" class="tab-pane-content" style="display: none;">
                    <div class="card border-0 shadow-sm radius-16 p-4 bg-white mb-4" style="border: 1px solid #e2e8f0 !important;">
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
                                            <div class="rounded-circle badge-soft-primary d-flex align-items-center justify-content-center p-2 flex-shrink-0" style="width: 40px; height: 40px;">
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

                        <!-- Kompetensi Terkait -->
                        <?php if (! empty($competencies)): ?>
                            <h4 class="h6 fw-bold mb-3 pt-3 border-top d-flex align-items-center gap-2" style="color: #052143; border-color: #e2e8f0 !important;">
                                <i class="fas fa-user-graduate text-info"></i>
                                Kompetensi Keahlian Pengadaan Terkait
                            </h4>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($competencies as $comp): ?>
                                    <span class="badge badge-soft-info radius-8 px-3 py-2 small fw-semibold">
                                        <strong><?= esc($comp['key'] ?? '') ?></strong> · <?= esc($comp['name'] ?? '') ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar Column (4 cols) -->
            <div class="col-xl-4 col-lg-5">
                <!-- Metadata Summary Card -->
                <div class="card border-0 shadow-sm radius-16 p-4 bg-white mb-4" style="border: 1px solid #e2e8f0 !important;">
                    <h3 class="h6 fw-bold mb-3 pb-2 border-bottom d-flex align-items-center gap-2" style="color: #052143; border-color: #e2e8f0 !important;">
                        <i class="fas fa-info-circle text-primary"></i> Informasi Dokumen Regulasi
                    </h3>

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
                        <span class="badge badge-soft-primary px-2 py-1"><?= esc($regulation['category_name'] ?? 'Umum') ?></span>
                    </div>

                    <div class="meta-info-row">
                        <span class="meta-info-label">Penerbit</span>
                        <strong class="meta-info-value text-end ms-2" style="max-width: 190px;" title="<?= esc($regulation['issuer_name'] ?? '-') ?>">
                            <?= esc($regulation['issuer_name'] ?? '-') ?>
                        </strong>
                    </div>

                    <div class="meta-info-row">
                        <span class="meta-info-label">Tanggal Ditetapkan</span>
                        <span class="meta-info-value"><?= ! empty($regulation['official_date']) ? date('d M Y', strtotime($regulation['official_date'])) : '-' ?></span>
                    </div>

                    <div class="meta-info-row">
                        <span class="meta-info-label">Tanggal Berlaku</span>
                        <span class="meta-info-value"><?= ! empty($regulation['effective_date']) ? date('d M Y', strtotime($regulation['effective_date'])) : '-' ?></span>
                    </div>

                    <div class="meta-info-row">
                        <span class="meta-info-label">Status Hukum</span>
                        <div><?= $renderLegalStatusBadge($legalStatus) ?></div>
                    </div>

                    <div class="meta-info-row">
                        <span class="meta-info-label">Versi Aktif</span>
                        <span class="badge badge-soft-info px-2 py-1">v<?= esc($regulation['current_version_number'] ?? '1') ?></span>
                    </div>

                    <div class="meta-info-row">
                        <span class="meta-info-label">Total Unduhan</span>
                        <strong class="text-success"><i class="fas fa-download me-1"></i> <?= number_format((int) ($regulation['download_count'] ?? 0)) ?> kali</strong>
                    </div>

                    <?php if ($hasFile): ?>
                        <div class="mt-4 pt-3 border-top" style="border-color: #e2e8f0 !important;">
                            <a href="<?= $downloadUrl ?>" class="btn-reg-download w-100 justify-content-center py-2 fs-6">
                                <i class="fas fa-download"></i>
                                <span>Unduh Naskah Resmi</span>
                                <small class="opacity-75">(<?= esc($fileSize) ?>)</small>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Related Regulations Card -->
                <?php if (! empty($related)): ?>
                    <div class="card border-0 shadow-sm radius-16 p-4 bg-white mb-4" style="border: 1px solid #e2e8f0 !important;">
                        <h3 class="h6 fw-bold mb-3 pb-2 border-bottom d-flex align-items-center gap-2" style="color: #052143; border-color: #e2e8f0 !important;">
                            <i class="fas fa-bookmark text-primary"></i> Regulasi Sejenis
                        </h3>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($related as $relReg): ?>
                                <div class="p-2 radius-8 transition-hover" style="border: 1px solid #f1f5f9;">
                                    <h6 class="fw-bold mb-1" style="font-size: 14px;">
                                        <a href="<?= site_url('regulasi/' . esc($relReg['slug'] ?? '')) ?>" class="text-decoration-none hover-primary" style="color: #052143;">
                                            <?= esc($relReg['document_number'] ?? '-') ?>
                                        </a>
                                    </h6>
                                    <p class="small mb-0" style="color: #475569; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.5;">
                                        <?= esc($relReg['title'] ?? '-') ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Help & Consultation Callout -->
                <div class="card border-0 shadow-sm radius-16 p-4 mb-4" style="background: #f8fafc; border: 1px solid #e2e8f0 !important;">
                    <div class="d-flex align-items-center gap-2 mb-2 text-primary">
                        <i class="fas fa-headset fs-5"></i>
                        <h4 class="h6 fw-bold mb-0" style="color: #052143;">Konsultasi Hukum PBJ</h4>
                    </div>
                    <p class="small mb-3" style="color: #334155;">
                        Memerlukan bantuan interpretasi pasal atau telaah regulasi teknis? Hubungi tim pendamping Biro PBJ Setda Provinsi Lampung.
                    </p>
                    <a href="<?= site_url('kontak') ?>" class="btn-reg-detail">
                        <span>Hubungi Biro PBJ Lampung</span>
                        <i class="fas fa-chevron-right small"></i>
                    </a>
                </div>

                <!-- Ecosystem Linkage Widget (Arena Belajar & Bunga Rampai) -->
                <div class="card border-0 radius-16 p-4 mb-4 text-white shadow-sm" style="background: linear-gradient(135deg, #082f5c 0%, #1e3a8a 100%);">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-graduation-cap text-warning fs-5"></i>
                        <h4 class="h6 fw-bold mb-0 text-white">Praktik &amp; Studi Kasus</h4>
                    </div>
                    <p class="small text-white-80 mb-3" style="color: rgba(255,255,255,0.85); font-size: 13px; line-height: 1.55;">
                        Pelajari implementasi regulasi ini lewat studi kasus audit, template dokumen siap pakai, dan kelas kurikulum di BegawiPengadaan.
                    </p>
                    <div class="d-flex flex-column gap-2">
                        <a href="<?= site_url('arena-belajar') ?>" class="btn btn-warning text-dark fw-bold radius-8 btn-sm px-3 py-2 text-center">
                            <i class="fas fa-graduation-cap me-1"></i> Buka Arena Belajar
                        </a>
                        <a href="<?= site_url('bunga-rampai') ?>" class="btn btn-outline-light radius-8 btn-sm px-3 py-2 text-center" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.3);">
                            <i class="fas fa-book-reader me-1"></i> Bunga Rampai Pengetahuan
                        </a>
                    </div>
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