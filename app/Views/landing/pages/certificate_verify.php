<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<style>
    html {
        scroll-behavior: smooth;
    }

    /* Universal high-contrast text utilities */
    .text-slate-900 {
        color: #0f172a !important;
    }

    .text-slate-800 {
        color: #1e293b !important;
    }

    .text-slate-700 {
        color: #334155 !important;
    }

    .text-slate-600 {
        color: #475569 !important;
    }

    .text-slate-500 {
        color: #64748b !important;
    }

    /* Reliable Iconify display */
    iconify-icon {
        display: inline-block;
        vertical-align: middle;
        line-height: 1;
        width: 1.15em;
        height: 1.15em;
    }

    /* Hero & Base Styling - 215px padding top gives 55px+ safe clearance below 160px absolute menu bar */
    .cert-hero {
        position: relative;
        background: linear-gradient(135deg, #052244 0%, #0a3a6b 55%, #0c5aa6 100%);
        padding: 215px 0 90px;
        color: #ffffff;
        overflow: hidden;
    }

    .cert-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 85% 20%, rgba(245, 158, 11, 0.18) 0%, transparent 45%),
            radial-gradient(circle at 15% 80%, rgba(16, 185, 129, 0.14) 0%, transparent 50%);
        pointer-events: none;
    }

    .cert-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(245, 158, 11, 0.22);
        border: 1px solid rgba(245, 158, 11, 0.55);
        color: #fef3c7;
        padding: 8px 20px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 22px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
    }

    .cert-hero__title {
        font-size: 2.85rem;
        font-weight: 800;
        line-height: 1.25;
        color: #ffffff;
        margin-bottom: 18px;
        letter-spacing: -0.5px;
    }

    .cert-hero__title span {
        color: #f59e0b;
    }

    .cert-hero__subtitle {
        font-size: 1.15rem;
        color: #e2e8f0;
        line-height: 1.75;
        max-width: 820px;
        margin: 0 auto 35px;
    }

    /* Verification Box Card */
    .cert-search-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 36px 32px;
        box-shadow: 0 20px 45px rgba(5, 34, 68, 0.14);
        border: 1px solid #e2e8f0;
        margin-top: -50px;
        position: relative;
        z-index: 10;
    }

    .cert-search-input-wrap {
        position: relative;
        display: flex;
        gap: 12px;
        align-items: stretch;
    }

    .cert-search-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #0c5aa6;
        font-size: 20px;
        pointer-events: none;
    }

    .cert-search-input {
        width: 100%;
        padding: 16px 20px 16px 56px;
        border: 2px solid #cbd5e1;
        border-radius: 12px;
        font-size: 1.05rem;
        font-weight: 600;
        color: #0f172a;
        transition: all 0.25s ease;
        background-color: #ffffff;
    }

    .cert-search-input::placeholder {
        color: #94a3b8;
    }

    .cert-search-input:focus {
        border-color: #0c5aa6;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(12, 90, 166, 0.15);
        outline: none;
    }

    .cert-btn-verify {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: linear-gradient(135deg, #0c5aa6 0%, #083b70 100%);
        color: #ffffff;
        border: none;
        padding: 16px 32px;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
        box-shadow: 0 4px 14px rgba(12, 90, 166, 0.28);
    }

    .cert-btn-verify:hover {
        background: linear-gradient(135deg, #083b70 0%, #052244 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(12, 90, 166, 0.38);
        color: #ffffff;
    }

    /* Quick Sample Pills */
    .cert-quick-samples {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        margin-top: 18px;
        padding-top: 18px;
        border-top: 1px dashed #e2e8f0;
        font-size: 0.875rem;
    }

    .cert-quick-label {
        color: #475569;
        font-weight: 700;
    }

    .cert-sample-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        color: #0f172a;
        font-family: monospace;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .cert-sample-pill:hover {
        background: #0c5aa6;
        color: #ffffff;
        border-color: #0c5aa6;
    }

    /* KPI Stats Ribbon */
    .cert-stats-section {
        padding: 45px 0 20px;
    }

    .cert-stat-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 18px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(5, 34, 68, 0.04);
    }

    .cert-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(5, 34, 68, 0.1);
        border-color: #cbd5e1;
    }

    .cert-stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .cert-stat-icon--blue {
        background: #eff6ff;
        color: #0c5aa6;
        border: 1px solid #bfdbfe;
    }

    .cert-stat-icon--amber {
        background: #fffbeb;
        color: #d97706;
        border: 1px solid #fde68a;
    }

    .cert-stat-icon--emerald {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }

    .cert-stat-icon--purple {
        background: #f5f3ff;
        color: #7c3aed;
        border: 1px solid #ddd6fe;
    }

    .cert-stat-number {
        font-size: 1.95rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
        margin-bottom: 4px;
    }

    .cert-stat-label {
        font-size: 0.9rem;
        color: #334155;
        font-weight: 600;
        margin: 0;
    }

    /* Trust Feature Pillars */
    .cert-trust-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
        margin: 40px 0;
    }

    .cert-trust-item {
        background: #f8fafc;
        border-radius: 14px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        display: flex;
        gap: 16px;
    }

    .cert-trust-item__icon {
        font-size: 28px;
        color: #0c5aa6;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .cert-trust-item__title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .cert-trust-item__desc {
        font-size: 0.9rem;
        color: #334155;
        line-height: 1.65;
        margin: 0;
    }

    /* Directory / Ledger Section */
    .cert-directory-section {
        padding: 50px 0 80px;
    }

    .cert-section-header {
        margin-bottom: 30px;
    }

    .cert-section-title {
        font-size: 1.85rem;
        font-weight: 800;
        color: #052244;
        margin-bottom: 8px;
    }

    .cert-section-subtitle {
        color: #64748b;
        font-size: 1rem;
    }

    /* Filter Pills */
    .cert-filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 28px;
        padding: 16px;
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .cert-filter-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .cert-filter-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569;
        background: #f1f5f9;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .cert-filter-pill:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .cert-filter-pill.active {
        background: #0c5aa6;
        color: #ffffff;
    }

    /* Certificate Cards */
    .cert-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.3s ease;
        height: 100%;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
    }

    .cert-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(5, 34, 68, 0.08);
        border-color: #0c5aa6;
    }

    .cert-card__top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 14px;
    }

    .cert-card__status-badge {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 5px 10px;
        border-radius: 6px;
    }

    .badge-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-secondary {
        background: #f1f5f9;
        color: #475569;
    }

    .cert-card__number {
        font-family: monospace;
        font-size: 1.05rem;
        font-weight: 700;
        color: #0c5aa6;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 10px;
    }

    .cert-card__title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.45;
        margin-bottom: 10px;
    }

    .cert-card__user {
        font-size: 0.925rem;
        color: #334155;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
    }

    .cert-card__meta {
        font-size: 0.815rem;
        color: #64748b;
        display: flex;
        flex-direction: column;
        gap: 6px;
        padding-top: 14px;
        border-top: 1px dashed #e2e8f0;
        margin-bottom: 18px;
    }

    .cert-card__btn-view {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 10px 16px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        color: #0c5aa6;
        font-weight: 700;
        font-size: 0.875rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .cert-card__btn-view:hover {
        background: #0c5aa6;
        color: #ffffff;
        border-color: #0c5aa6;
    }

    /* Begawi Pagination */
    .begawi-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-top: 40px;
    }

    .begawi-pagination__link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 42px;
        height: 42px;
        padding: 0 12px;
        border-radius: 8px;
        font-size: 0.925rem;
        font-weight: 700;
        color: #475569;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .begawi-pagination__link:hover:not(.disabled) {
        background: #f1f5f9;
        color: #0c5aa6;
        border-color: #cbd5e1;
    }

    .begawi-pagination__link.active {
        background: #0c5aa6;
        color: #ffffff;
        border-color: #0c5aa6;
    }

    .begawi-pagination__link.disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .cert-hero {
            padding: 185px 0 75px;
        }

        .cert-hero__title {
            font-size: 2.35rem;
        }
    }

    @media (max-width: 768px) {
        .cert-hero {
            padding: 165px 0 65px;
        }

        .cert-hero__title {
            font-size: 2rem;
        }

        .cert-search-input-wrap {
            flex-direction: column;
        }

        .cert-btn-verify {
            width: 100%;
        }

        .cert-search-card {
            padding: 24px 18px;
            margin-top: -30px;
        }

        .cert-filter-bar {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="cert-hero text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="cert-badge-pill">
                    <i class="fas fa-shield-alt"></i> Portal Resmi Verifikasi Sertifikat Digital PBJ
                </div>
                <h1 class="cert-hero__title">
                    Verifikasi Keabsahan <span>Sertifikat Digital</span>
                </h1>
                <p class="cert-hero__subtitle">
                    Periksa otentisitas dan keabsahan sertifikat kompetensi pengadaan barang dan jasa Pemerintah Provinsi Lampung yang diterbitkan secara elektronik oleh Biro Pengadaan Barang dan Jasa Sekretariat Daerah.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Search Verification Box -->
<section class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="cert-search-card">
                <form action="<?= site_url('verifikasi-sertifikat/hasil') ?>" method="post" id="certVerificationForm">
                    <?= csrf_field() ?>
                    <label for="certNumberInput" class="form-label fw-bold text-dark mb-2">
                        <i class="fas fa-barcode text-primary me-1"></i> Masukkan Nomor Sertifikat atau Token Verifikasi
                    </label>
                    <div class="cert-search-input-wrap">
                        <i class="fas fa-search cert-search-icon"></i>
                        <input
                            type="text"
                            id="certNumberInput"
                            name="certificate_number"
                            class="cert-search-input"
                            placeholder="Contoh: SERT/2026/00003 atau masukkan token kriptografi..."
                            value="<?= esc($keyword) ?>"
                            required
                            autocomplete="off">
                        <button type="submit" class="cert-btn-verify">
                            <i class="fas fa-check-circle"></i> Verifikasi Sekarang
                        </button>
                    </div>
                </form>

                <!-- Quick Samples -->
                <?php if (! empty($sampleNumbers)): ?>
                    <div class="cert-quick-samples">
                        <span class="cert-quick-label"><i class="fas fa-lightbulb text-warning me-1"></i> Coba Nomor Terbit Resmi:</span>
                        <?php foreach ($sampleNumbers as $sample): ?>
                            <button type="button" class="cert-sample-pill btn-sample-fill" data-sample="<?= esc($sample) ?>">
                                <?= esc($sample) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Live KPI Statistics Ribbon -->
<section class="cert-stats-section">
    <div class="container">
        <div class="row g-3">
            <div class="col-xl-3 col-md-6">
                <div class="cert-stat-card">
                    <div class="cert-stat-icon cert-stat-icon--blue">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div>
                        <div class="cert-stat-number"><?= number_format($stats['total_issued'] ?? 0) ?></div>
                        <p class="cert-stat-label">Sertifikat Resmi Diterbitkan</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="cert-stat-card">
                    <div class="cert-stat-icon cert-stat-icon--amber">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <div class="cert-stat-number"><?= number_format($stats['total_learning'] ?? 0) ?></div>
                        <p class="cert-stat-label">Pelatihan Arena Belajar</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="cert-stat-card">
                    <div class="cert-stat-icon cert-stat-icon--emerald">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <div class="cert-stat-number"><?= number_format($stats['total_event'] ?? 0) ?></div>
                        <p class="cert-stat-label">Event &amp; Seminar PBJ</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="cert-stat-card">
                    <div class="cert-stat-icon cert-stat-icon--purple">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div>
                        <div class="cert-stat-number">100%</div>
                        <p class="cert-stat-label">Keabsahan Kriptografi SHA-256</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3 Security Pillars -->
<section class="container">
    <div class="cert-trust-grid">
        <div class="cert-trust-item">
            <div class="cert-trust-item__icon">
                <i class="fas fa-fingerprint"></i>
            </div>
            <div>
                <h4 class="cert-trust-item__title">Kriptografi Anti-Pemalsuan</h4>
                <p class="cert-trust-item__desc">
                    Setiap sertifikat dilengkapi token kriptografis unik berbasis algoritma SHA-256 yang mustahil dipalsukan atau diubah secara sepihak.
                </p>
            </div>
        </div>
        <div class="cert-trust-item">
            <div class="cert-trust-item__icon">
                <i class="fas fa-signature"></i>
            </div>
            <div>
                <h4 class="cert-trust-item__title">Tanda Tangan Digital Resmi</h4>
                <p class="cert-trust-item__desc">
                    Disahkan langsung oleh Kepala Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung melalui otentikasi sistem BegawiPengadaan.
                </p>
            </div>
        </div>
        <div class="cert-trust-item">
            <div class="cert-trust-item__icon">
                <i class="fas fa-history"></i>
            </div>
            <div>
                <h4 class="cert-trust-item__title">Jejak Audit Terverifikasi</h4>
                <p class="cert-trust-item__desc">
                    Riwayat penerbitan, tanggal kelulusan, dan status sertifikat (aktif atau dicabut) tercatat transparan dalam basis data pemerintah.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Directory / Ledger of Verifiable Certificates -->
<section class="cert-directory-section">
    <div class="container">
        <div class="cert-section-header d-flex flex-wrap justify-content-between align-items-end">
            <div>
                <h2 class="cert-section-title">Direktori Publik Sertifikat Terdaftar</h2>
                <p class="cert-section-subtitle mb-0">
                    Daftar publikasi sertifikat resmi yang telah diterbitkan untuk transparansi publik dan validasi pemangku kepentingan.
                </p>
            </div>
            <div class="mt-3 mt-md-0 text-muted small">
                Menampilkan <strong><?= count($certificates) ?></strong> dari <strong><?= number_format($pagerData['totalRows']) ?></strong> sertifikat
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="cert-filter-bar">
            <div class="cert-filter-pills">
                <a href="<?= site_url('verifikasi-sertifikat?' . http_build_query(array_merge($_GET, ['tipe' => 'all', 'page' => 1]))) ?>"
                    class="cert-filter-pill <?= $sourceFilter === 'all' || $sourceFilter === '' ? 'active' : '' ?>">
                    <i class="fas fa-th-large"></i> Semua Sumber
                </a>
                <a href="<?= site_url('verifikasi-sertifikat?' . http_build_query(array_merge($_GET, ['tipe' => 'learning_enrollment', 'page' => 1]))) ?>"
                    class="cert-filter-pill <?= $sourceFilter === 'learning_enrollment' ? 'active' : '' ?>">
                    <i class="fas fa-graduation-cap"></i> Pelatihan (Arena Belajar)
                </a>
                <a href="<?= site_url('verifikasi-sertifikat?' . http_build_query(array_merge($_GET, ['tipe' => 'event_registration', 'page' => 1]))) ?>"
                    class="cert-filter-pill <?= $sourceFilter === 'event_registration' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-alt"></i> Event &amp; Seminar PBJ
                </a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-bold">Status:</span>
                <a href="<?= site_url('verifikasi-sertifikat?' . http_build_query(array_merge($_GET, ['status' => 'all', 'page' => 1]))) ?>"
                    class="btn btn-sm <?= $statusFilter === 'all' || $statusFilter === '' ? 'btn-dark' : 'btn-outline-secondary' ?>">Semua</a>
                <a href="<?= site_url('verifikasi-sertifikat?' . http_build_query(array_merge($_GET, ['status' => 'issued', 'page' => 1]))) ?>"
                    class="btn btn-sm <?= $statusFilter === 'issued' ? 'btn-success' : 'btn-outline-success' ?>">Valid (Issued)</a>
                <a href="<?= site_url('verifikasi-sertifikat?' . http_build_query(array_merge($_GET, ['status' => 'revoked', 'page' => 1]))) ?>"
                    class="btn btn-sm <?= $statusFilter === 'revoked' ? 'btn-danger' : 'btn-outline-danger' ?>">Dicabut</a>
            </div>
        </div>

        <!-- Certificate Cards Grid -->
        <?php if (! empty($certificates)): ?>
            <div class="row g-4">
                <?php foreach ($certificates as $cert): ?>
                    <div class="col-xl-4 col-md-6">
                        <div class="cert-card">
                            <div>
                                <div class="cert-card__top">
                                    <span class="cert-card__status-badge <?= $cert['status_badge'] ?>">
                                        <i class="fas <?= $cert['is_valid'] ? 'fa-check-circle' : ($cert['is_revoked'] ? 'fa-ban' : 'fa-info-circle') ?> me-1"></i>
                                        <?= esc($cert['status_label']) ?>
                                    </span>
                                    <span class="badge bg-light text-dark border">
                                        <?= $cert['source_type'] === 'learning_enrollment' ? 'Arena Belajar' : 'Event PBJ' ?>
                                    </span>
                                </div>

                                <div class="cert-card__number">
                                    <i class="fas fa-stamp text-warning"></i> <?= esc($cert['certificate_number']) ?>
                                </div>

                                <h3 class="cert-card__title">
                                    <?= esc($cert['title']) ?>
                                </h3>

                                <div class="cert-card__user">
                                    <i class="fas fa-user-circle text-primary"></i>
                                    <span><?= esc($cert['user_name']) ?></span>
                                </div>

                                <div class="cert-card__meta">
                                    <div class="d-flex justify-content-between">
                                        <span><i class="far fa-calendar-alt me-1"></i> Diterbitkan:</span>
                                        <strong><?= esc($cert['issued_at_human']) ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span><i class="fas fa-key me-1"></i> Keamanan:</span>
                                        <span class="font-monospace text-muted"><?= esc($cert['security_code']) ?></span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <a href="<?= site_url('verifikasi-sertifikat/' . rawurlencode($cert['certificate_number'])) ?>" class="cert-card__btn-view">
                                    <i class="fas fa-shield-alt"></i> Cek Keabsahan Lengkap
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination Bar -->
            <?php if ($pagerData['totalPages'] > 1): ?>
                <div class="begawi-pagination">
                    <a href="<?= site_url('verifikasi-sertifikat?' . http_build_query(array_merge($_GET, ['page' => $pagerData['prevPage']]))) ?>"
                        class="begawi-pagination__link <?= ! $pagerData['hasPrev'] ? 'disabled' : '' ?>"
                        aria-label="Halaman Sebelumnya">
                        <i class="fas fa-chevron-left"></i>
                    </a>

                    <?php for ($p = 1; $p <= $pagerData['totalPages']; $p++): ?>
                        <?php if ($p === 1 || $p === $pagerData['totalPages'] || abs($p - $pagerData['page']) <= 2): ?>
                            <a href="<?= site_url('verifikasi-sertifikat?' . http_build_query(array_merge($_GET, ['page' => $p]))) ?>"
                                class="begawi-pagination__link <?= $p === $pagerData['page'] ? 'active' : '' ?>">
                                <?= $p ?>
                            </a>
                        <?php elseif (abs($p - $pagerData['page']) === 3): ?>
                            <span class="px-2 text-muted">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <a href="<?= site_url('verifikasi-sertifikat?' . http_build_query(array_merge($_GET, ['page' => $pagerData['nextPage']]))) ?>"
                        class="begawi-pagination__link <?= ! $pagerData['hasNext'] ? 'disabled' : '' ?>"
                        aria-label="Halaman Berikutnya">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="text-center py-5 bg-light rounded-4 border">
                <i class="fas fa-search-minus text-muted fa-3x mb-3"></i>
                <h4 class="fw-bold text-dark">Tidak Ada Data Sertifikat</h4>
                <p class="text-muted">Tidak ditemukan sertifikat dengan kriteria pencarian yang Anda tentukan.</p>
                <a href="<?= site_url('verifikasi-sertifikat') ?>" class="btn btn-outline-primary mt-2">
                    <i class="fas fa-sync-alt me-1"></i> Reset Pencarian
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('assets/dashboard/js/lib/iconify-icon.min.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quick sample fill handler
        const sampleButtons = document.querySelectorAll('.btn-sample-fill');
        const certInput = document.getElementById('certNumberInput');
        const certForm = document.getElementById('certVerificationForm');

        sampleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const sampleVal = this.getAttribute('data-sample');
                if (sampleVal && certInput) {
                    certInput.value = sampleVal;
                    certForm.submit();
                }
            });
        });
    });

    (function() {
        // Bulletproof zero-jitter clamping at scroll extremities for ultra-smooth scrolling
        var isClamping = false;
        window.addEventListener('scroll', function() {
            var maxScroll = Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
            if (window.scrollY > maxScroll) {
                if (!isClamping) {
                    isClamping = true;
                    window.scrollTo(0, maxScroll);
                    requestAnimationFrame(function() {
                        isClamping = false;
                    });
                }
            }
        }, {
            passive: true
        });
    })();
</script>
<?= $this->endSection() ?>