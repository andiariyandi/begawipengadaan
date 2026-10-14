<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<style>
    :root {
        --evt-navy-dark: #052244;
        --evt-navy: #082f5c;
        --evt-blue: #0c5aa6;
        --evt-blue-light: #eff6ff;
        --evt-gold: #f59e0b;
        --evt-gold-light: #fef3c7;
        --evt-emerald: #10b981;
        --evt-slate-900: #0f172a;
        --evt-slate-800: #1e293b;
        --evt-slate-700: #334155;
        --evt-slate-600: #475569;
        --evt-slate-500: #64748b;
        --evt-slate-400: #94a3b8;
        --evt-slate-200: #e2e8f0;
        --evt-slate-50: #f8fafc;
    }

    /* Reliable Iconify display */
    iconify-icon {
        display: inline-block;
        vertical-align: middle;
        line-height: 1;
        width: 1.15em;
        height: 1.15em;
    }

    /* Universal high-contrast text and border utilities */
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

    .text-slate-400 {
        color: #94a3b8 !important;
    }

    .text-slate-300 {
        color: #cbd5e1 !important;
    }

    .text-xs {
        font-size: 12px !important;
        line-height: 1.45;
    }

    .text-sm {
        font-size: 14px !important;
        line-height: 1.5;
    }

    .text-base {
        font-size: 16px !important;
    }

    .text-lg {
        font-size: 18px !important;
    }

    .text-xl {
        font-size: 20px !important;
    }

    .text-2xl {
        font-size: 24px !important;
    }

    .radius-6 {
        border-radius: 6px !important;
    }

    .radius-8 {
        border-radius: 8px !important;
    }

    .radius-10 {
        border-radius: 10px !important;
    }

    .radius-12 {
        border-radius: 12px !important;
    }

    .radius-14 {
        border-radius: 14px !important;
    }

    .radius-16 {
        border-radius: 16px !important;
    }

    .radius-18 {
        border-radius: 18px !important;
    }

    .radius-999 {
        border-radius: 9999px !important;
    }

    /* High-contrast sub badges */
    .bg-primary-subtle {
        background-color: #eff6ff !important;
        color: #0c5aa6 !important;
        border: 1px solid #bfdbfe;
    }

    .bg-success-subtle {
        background-color: #ecfdf5 !important;
        color: #059669 !important;
        border: 1px solid #a7f3d0;
    }

    .bg-warning-subtle {
        background-color: #fffbeb !important;
        color: #b45309 !important;
        border: 1px solid #fde68a;
    }

    .bg-info-subtle {
        background-color: #f0f9ff !important;
        color: #0284c7 !important;
        border: 1px solid #bae6fd;
    }

    .bg-secondary-subtle {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1;
    }

    /* Page Hero Banner - 215px gives safe clearance below absolute header */
    .evt-hero-banner {
        background: linear-gradient(135deg, var(--evt-navy-dark) 0%, var(--evt-navy) 60%, var(--evt-blue) 100%);
        padding: 215px 0 85px;
        position: relative;
        overflow: hidden;
        color: #ffffff;
    }

    .evt-hero-banner::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -10%;
        width: 600px;
        height: 600px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.15) 0%, transparent 70%);
        pointer-events: none;
    }

    .evt-hero-banner::after {
        content: '';
        position: absolute;
        bottom: -20%;
        left: -10%;
        width: 500px;
        height: 500px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(12, 90, 166, 0.3) 0%, transparent 70%);
        pointer-events: none;
    }

    .evt-breadcrumb {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.14);
        backdrop-filter: blur(8px);
        padding: 6px 16px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        color: #f1f5f9;
        margin-bottom: 18px;
        border: 1px solid rgba(255, 255, 255, 0.25);
    }

    .evt-breadcrumb a {
        color: #fde68a;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .evt-breadcrumb a:hover {
        color: #ffffff;
    }

    .evt-hero-title {
        font-size: 40px;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.25;
        margin-bottom: 16px;
        letter-spacing: -0.5px;
    }

    .evt-hero-title span {
        color: var(--evt-gold);
    }

    .evt-hero-desc {
        font-size: 16px;
        color: #e2e8f0;
        line-height: 1.65;
        max-width: 740px;
        margin-bottom: 32px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    /* Hero Search Bar */
    .evt-search-box {
        background: #ffffff;
        border-radius: 16px;
        padding: 8px 10px 8px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        max-width: 680px;
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.28);
        border: 2px solid rgba(255, 255, 255, 0.9);
    }

    .evt-search-box input {
        border: none;
        outline: none;
        font-size: 15px;
        color: var(--evt-slate-900);
        font-weight: 500;
        flex: 1;
        background: transparent;
    }

    .evt-search-box input::placeholder {
        color: #64748b;
    }

    .evt-search-box button {
        background: linear-gradient(135deg, var(--evt-blue) 0%, var(--evt-navy) 100%);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 700;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.25s ease;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(12, 90, 166, 0.3);
    }

    .evt-search-box button:hover {
        background: linear-gradient(135deg, var(--evt-gold) 0%, #d97706 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(245, 158, 11, 0.4);
        color: #0f172a;
    }

    /* Metrics Ribbon */
    .evt-metrics-section {
        margin-top: -45px;
        position: relative;
        z-index: 10;
        margin-bottom: 50px;
    }

    .evt-metrics-card {
        background: #ffffff;
        border: 1px solid var(--evt-slate-200);
        border-radius: 18px;
        padding: 24px 28px;
        box-shadow: 0 14px 35px rgba(8, 47, 92, 0.08);
    }

    .evt-kpi-item {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .evt-kpi-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        flex-shrink: 0;
    }

    .evt-kpi-num {
        font-size: 26px;
        font-weight: 800;
        color: var(--evt-navy-dark);
        line-height: 1.1;
    }

    .evt-kpi-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--evt-slate-600);
        margin-top: 3px;
    }

    /* Navigation & Tabs */
    .evt-view-tabs {
        display: inline-flex;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 12px;
        gap: 4px;
    }

    .evt-view-tab {
        padding: 8px 18px;
        border-radius: 9px;
        font-size: 13.5px;
        font-weight: 700;
        color: var(--evt-slate-700);
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .evt-view-tab:hover {
        color: var(--evt-blue);
    }

    .evt-view-tab.active {
        background: #ffffff;
        color: var(--evt-navy-dark);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    /* Filters Row */
    .evt-filter-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 36px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--evt-slate-200);
    }

    .evt-pill-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .evt-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        color: var(--evt-slate-700);
        background: #ffffff;
        border: 1px solid var(--evt-slate-200);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .evt-pill:hover {
        background: var(--evt-blue-light);
        color: var(--evt-blue);
        border-color: #bfdbfe;
    }

    .evt-pill.active {
        background: var(--evt-navy-dark);
        color: #ffffff;
        border-color: var(--evt-navy-dark);
        box-shadow: 0 4px 14px rgba(5, 34, 68, 0.2);
    }

    .evt-pill.active .badge {
        background: rgba(255, 255, 255, 0.2) !important;
        color: #ffffff !important;
    }

    /* Spotlight Event Hero Card */
    .evt-spotlight-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #bfdbfe;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 40px;
        box-shadow: 0 16px 36px rgba(8, 47, 92, 0.08);
        transition: all 0.3s ease;
        position: relative;
    }

    .evt-spotlight-card:hover {
        box-shadow: 0 22px 48px rgba(8, 47, 92, 0.14);
        border-color: #93c5fd;
    }

    .evt-spotlight-badge {
        position: absolute;
        top: 20px;
        left: 20px;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #ffffff;
        font-size: 11.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 5px 14px;
        border-radius: 8px;
        z-index: 5;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.35);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .evt-spotlight-inner {
        padding: 36px 32px;
    }

    .evt-spotlight-title {
        font-size: 26px;
        font-weight: 800;
        line-height: 1.35;
        color: var(--evt-navy-dark);
        margin-bottom: 14px;
        letter-spacing: -0.3px;
    }

    .evt-spotlight-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .evt-spotlight-title a:hover {
        color: var(--evt-blue);
    }

    /* Event Grid Card */
    .evt-card {
        background: #ffffff;
        border: 1px solid var(--evt-slate-200);
        border-radius: 18px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 14px rgba(8, 47, 92, 0.03);
    }

    .evt-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 36px rgba(8, 47, 92, 0.1);
        border-color: #cbd5e1;
    }

    .evt-card-header {
        position: relative;
        height: 195px;
        background: linear-gradient(135deg, var(--evt-navy-dark) 0%, var(--evt-blue) 100%);
        overflow: hidden;
    }

    .evt-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    .evt-card:hover .evt-card-img {
        transform: scale(1.05);
    }

    /* Dynamic Brand SVG Cover when no image uploaded */
    .evt-card-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #052244 0%, #082f5c 50%, #0c5aa6 100%);
        color: #ffffff;
        position: relative;
        overflow: hidden;
        text-align: center;
        padding: 20px;
    }

    .evt-card-placeholder::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -30%;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.25) 0%, transparent 70%);
    }

    .evt-date-badge {
        position: absolute;
        top: 14px;
        left: 14px;
        background: #ffffff;
        border-radius: 12px;
        padding: 8px 12px;
        text-align: center;
        min-width: 58px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        z-index: 2;
    }

    .evt-date-day {
        font-size: 20px;
        font-weight: 800;
        color: var(--evt-blue);
        line-height: 1;
        display: block;
    }

    .evt-date-month {
        font-size: 11px;
        font-weight: 700;
        color: var(--evt-slate-600);
        text-transform: uppercase;
        margin-top: 2px;
        display: block;
    }

    .evt-mode-pill {
        position: absolute;
        top: 14px;
        right: 14px;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        backdrop-filter: blur(8px);
        z-index: 2;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .evt-mode-online {
        background: rgba(16, 185, 129, 0.95);
        color: #ffffff;
    }

    .evt-mode-offline {
        background: rgba(12, 90, 166, 0.95);
        color: #ffffff;
    }

    .evt-mode-hybrid {
        background: rgba(245, 158, 11, 0.98);
        color: #0f172a;
    }

    .evt-card-body {
        padding: 22px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .evt-card-meta {
        font-size: 12.5px;
        color: var(--evt-slate-600);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .evt-card-title {
        font-size: 17.5px;
        font-weight: 700;
        line-height: 1.4;
        color: var(--evt-navy-dark);
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .evt-card-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .evt-card-title a:hover {
        color: var(--evt-blue);
    }

    .evt-card-summary {
        font-size: 13.5px;
        color: var(--evt-slate-600);
        line-height: 1.6;
        margin-bottom: 16px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex-grow: 1;
    }

    .evt-card-location {
        font-size: 13px;
        font-weight: 500;
        color: var(--evt-slate-700);
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 14px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .evt-card-footer {
        padding-top: 14px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: auto;
    }

    .evt-seats-badge {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--evt-slate-600);
    }

    .evt-seats-badge strong {
        color: var(--evt-navy-dark);
    }

    /* Sidebar Widgets */
    .evt-sidebar-widget {
        background: #ffffff;
        border: 1px solid var(--evt-slate-200);
        border-radius: 18px;
        padding: 26px;
        margin-bottom: 28px;
        box-shadow: 0 6px 20px rgba(8, 47, 92, 0.04);
    }

    .evt-widget-title {
        font-size: 17px;
        font-weight: 800;
        color: var(--evt-navy-dark);
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f1f5f9;
        position: relative;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .evt-widget-title::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 36px;
        height: 2px;
        background: var(--evt-blue);
    }

    /* Calendar Timeline in Calendar Tab */
    .evt-calendar-month-header {
        font-size: 20px;
        font-weight: 800;
        color: var(--evt-navy-dark);
        margin: 30px 0 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--evt-blue-light);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .evt-timeline-item {
        background: #ffffff;
        border: 1px solid var(--evt-slate-200);
        border-radius: 14px;
        padding: 18px 22px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 20px;
        transition: all 0.2s ease;
    }

    .evt-timeline-item:hover {
        border-color: var(--evt-blue);
        box-shadow: 0 8px 24px rgba(8, 47, 92, 0.08);
        transform: translateX(4px);
    }

    /* Pagination */
    .evt-pagination-wrap {
        margin-top: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .evt-page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 42px;
        height: 42px;
        padding: 0 14px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        color: var(--evt-slate-700);
        background: #ffffff;
        border: 1px solid var(--evt-slate-200);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .evt-page-btn:hover {
        background: var(--evt-blue-light);
        color: var(--evt-blue);
        border-color: #bfdbfe;
    }

    .evt-page-btn.active {
        background: var(--evt-navy-dark);
        color: #ffffff;
        border-color: var(--evt-navy-dark);
        box-shadow: 0 4px 12px rgba(5, 34, 68, 0.25);
    }

    .evt-page-btn.disabled {
        opacity: 0.4;
        pointer-events: none;
    }

    @media (max-width: 991px) {
        .evt-hero-banner {
            padding: 185px 0 70px;
        }

        .evt-hero-title {
            font-size: 30px;
        }

        .evt-spotlight-inner {
            padding: 24px;
        }

        .evt-timeline-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
    }

    @media (max-width: 768px) {
        .evt-hero-banner {
            padding: 165px 0 60px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- 1. HERO BANNER -->
<section class="evt-hero-banner">
    <div class="container">
        <div class="evt-breadcrumb">
            <a href="<?= site_url('/') ?>"><iconify-icon icon="solar:home-2-bold" class="align-middle"></iconify-icon> Beranda</a>
            <span>/</span>
            <span>Event &amp; Agenda PBJ</span>
            <?php if (! empty($selectedType)): ?>
                <span>/</span>
                <span class="text-white"><?= ucfirst($selectedType) ?></span>
            <?php endif; ?>
        </div>

        <h1 class="evt-hero-title">
            Agenda Kegiatan &amp; Pembinaan <br>
            <span>Pengadaan Barang/Jasa Provinsi Lampung</span>
        </h1>
        <p class="evt-hero-desc">
            Ikuti webinar nasional, bimbingan teknis bersertifikat, rapat koordinasi terpadu, dan lokakarya mitigasi risiko pengadaan bersama instruktur ahli LKPP dan Biro PBJ Setda Provinsi Lampung.
        </p>

        <!-- Search Form -->
        <form action="<?= site_url('event') ?>" method="get" class="evt-search-box">
            <iconify-icon icon="solar:magnifer-linear" class="text-slate-500 text-xl"></iconify-icon>
            <input type="text" name="q" value="<?= esc($keyword ?? '') ?>" placeholder="Cari webinar, bimtek kualifikasi, atau topik kegiatan...">
            <?php if (! empty($selectedType)): ?>
                <input type="hidden" name="tipe" value="<?= esc($selectedType) ?>">
            <?php endif; ?>
            <?php if (! empty($selectedMode)): ?>
                <input type="hidden" name="mode" value="<?= esc($selectedMode) ?>">
            <?php endif; ?>
            <button type="submit">
                <span>Cari Agenda</span>
                <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
            </button>
        </form>
    </div>
</section>

<!-- 2. LIVE METRICS RIBBON (Strictly Real Data, Zero Dummy Fallback) -->
<div class="container evt-metrics-section">
    <div class="evt-metrics-card">
        <div class="row g-4">
            <div class="col-lg-3 col-6">
                <div class="evt-kpi-item">
                    <div class="evt-kpi-icon text-primary bg-primary-subtle">
                        <iconify-icon icon="solar:calendar-bold"></iconify-icon>
                    </div>
                    <div>
                        <div class="evt-kpi-num"><?= number_format($stats['total_events']) ?></div>
                        <div class="evt-kpi-label">Total Agenda Terdaftar</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="evt-kpi-item">
                    <div class="evt-kpi-icon text-success bg-success-subtle">
                        <iconify-icon icon="solar:clock-circle-bold"></iconify-icon>
                    </div>
                    <div>
                        <div class="evt-kpi-num"><?= number_format($stats['upcoming_events']) ?></div>
                        <div class="evt-kpi-label">Kegiatan Mendatang</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="evt-kpi-item">
                    <div class="evt-kpi-icon text-warning bg-warning-subtle">
                        <iconify-icon icon="solar:user-check-bold"></iconify-icon>
                    </div>
                    <div>
                        <div class="evt-kpi-num"><?= number_format($stats['open_registration']) ?></div>
                        <div class="evt-kpi-label">Pendaftaran Dibuka</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="evt-kpi-item">
                    <div class="evt-kpi-icon text-info bg-info-subtle">
                        <iconify-icon icon="solar:users-group-rounded-bold"></iconify-icon>
                    </div>
                    <div>
                        <div class="evt-kpi-num"><?= number_format($stats['total_attendees']) ?></div>
                        <div class="evt-kpi-label">Peserta Terdaftar</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. MAIN CONTENT AREA -->
<section class="pb-80">
    <div class="container">
        <!-- View Mode Navigation Tabs -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
            <div class="evt-view-tabs">
                <a href="<?= site_url('event') ?>" class="evt-view-tab <?= $viewMode === 'all' ? 'active' : '' ?>">
                    <iconify-icon icon="solar:widget-2-bold"></iconify-icon>
                    <span>Semua Kegiatan</span>
                </a>
                <a href="<?= site_url('event/agenda') ?>" class="evt-view-tab <?= $viewMode === 'upcoming' ? 'active' : '' ?>">
                    <iconify-icon icon="solar:clock-circle-bold"></iconify-icon>
                    <span>Agenda Mendatang</span>
                </a>
                <a href="<?= site_url('event/kalender') ?>" class="evt-view-tab <?= $viewMode === 'calendar' ? 'active' : '' ?>">
                    <iconify-icon icon="solar:calendar-date-bold"></iconify-icon>
                    <span>Kalender Kegiatan</span>
                </a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="text-xs text-slate-600 fw-bold">Moda:</span>
                <a href="<?= site_url('event' . ($selectedType ? '?tipe=' . $selectedType . '&' : '?') . 'mode=online') ?>" class="badge <?= $selectedMode === 'online' ? 'bg-success text-white' : 'bg-light text-dark border' ?> text-decoration-none p-2 radius-6">Online</a>
                <a href="<?= site_url('event' . ($selectedType ? '?tipe=' . $selectedType . '&' : '?') . 'mode=offline') ?>" class="badge <?= $selectedMode === 'offline' ? 'bg-primary text-white' : 'bg-light text-dark border' ?> text-decoration-none p-2 radius-6">Offline</a>
                <a href="<?= site_url('event' . ($selectedType ? '?tipe=' . $selectedType . '&' : '?') . 'mode=hybrid') ?>" class="badge <?= $selectedMode === 'hybrid' ? 'bg-warning text-dark' : 'bg-light text-dark border' ?> text-decoration-none p-2 radius-6">Hybrid</a>
                <?php if (! empty($selectedMode)): ?>
                    <a href="<?= site_url('event' . ($selectedType ? '?tipe=' . $selectedType : '')) ?>" class="text-xs text-danger text-decoration-none ms-1" title="Reset Mode">&times; Reset</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Filter Pills by Type (Supporting all 8 dashboard types) -->
        <div class="evt-filter-wrapper">
            <div class="evt-pill-group">
                <a href="<?= site_url('event') ?>" class="evt-pill <?= empty($selectedType) ? 'active' : '' ?>">
                    <span>Semua Tipe</span>
                    <span class="badge bg-light text-dark radius-999"><?= $stats['total_events'] ?></span>
                </a>
                <?php
                $dashboardTypes = [
                    'webinar'     => 'Webinar',
                    'bimtek'      => 'Bimtek',
                    'workshop'    => 'Workshop',
                    'sosialisasi' => 'Sosialisasi',
                    'seminar'     => 'Seminar',
                    'pelatihan'   => 'Pelatihan',
                    'rakor'       => 'Rapat Koordinasi',
                    'klinik'      => 'Klinik PBJ',
                ];
                ?>
                <?php foreach ($dashboardTypes as $tKey => $tLabel): ?>
                    <a href="<?= site_url('event?tipe=' . $tKey . (! empty($selectedMode) ? '&mode=' . $selectedMode : '')) ?>" class="evt-pill <?= $selectedType === $tKey ? 'active' : '' ?>">
                        <span><?= esc($tLabel) ?></span>
                        <span class="badge bg-light text-dark radius-999"><?= $typeCounts[$tKey] ?? 0 ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if (! empty($keyword)): ?>
                <div class="text-sm text-slate-700">
                    Pencarian: <strong>"<?= esc($keyword) ?>"</strong>
                    <a href="<?= site_url('event') ?>" class="text-danger ms-2"><iconify-icon icon="solar:close-circle-bold"></iconify-icon> Reset</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- CALENDAR VIEW MODE (if tab is calendar) -->
        <?php if ($viewMode === 'calendar'): ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <?php if (empty($calendarGrouped)): ?>
                        <div class="text-center p-5 bg-white border radius-18">
                            <h5 class="fw-bold mb-2 text-slate-900">Belum ada kalender agenda terjadwal</h5>
                            <p class="text-slate-600">Pantau terus pembaharuan agenda melalui kanal resmi BegawiPengadaan.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($calendarGrouped as $monthName => $cEvents): ?>
                            <h3 class="evt-calendar-month-header">
                                <iconify-icon icon="solar:calendar-mark-bold" class="text-primary"></iconify-icon>
                                <span><?= esc($monthName) ?></span>
                            </h3>
                            <div class="d-grid gap-2">
                                <?php foreach ($cEvents as $ce): ?>
                                    <?php
                                    $ceStart = ! empty($ce['start_at']) ? strtotime($ce['start_at']) : time();
                                    $ceEnd   = ! empty($ce['end_at']) ? strtotime($ce['end_at']) : $ceStart;
                                    ?>
                                    <div class="evt-timeline-item">
                                        <div class="d-flex align-items-center gap-3" style="min-width: 140px;">
                                            <div class="text-center p-2 bg-light border radius-10" style="min-width: 50px;">
                                                <strong class="d-block text-lg text-primary"><?= date('d', $ceStart) ?></strong>
                                                <small class="text-slate-600 text-uppercase text-xs fw-bold"><?= date('M', $ceStart) ?></small>
                                            </div>
                                            <div class="text-xs text-slate-600">
                                                <span class="d-block fw-semibold text-slate-900"><?= date('H:i', $ceStart) ?> WIB</span>
                                                <span class="badge bg-light text-dark border"><?= ucfirst($ce['mode']) ?></span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge bg-primary-subtle text-primary text-xs text-uppercase fw-bold"><?= esc($ce['type']) ?></span>
                                                <?php if (! empty($ce['certificate_enabled'])): ?>
                                                    <span class="badge bg-success-subtle text-success text-xs fw-bold"><iconify-icon icon="solar:diploma-verified-bold"></iconify-icon> E-Sertifikat</span>
                                                <?php endif; ?>
                                                <span class="text-xs text-slate-600"><iconify-icon icon="solar:map-point-linear" class="text-danger"></iconify-icon> <?= esc($ce['location'] ?: 'Online') ?></span>
                                            </div>
                                            <h5 class="h6 fw-bold mb-0">
                                                <a href="<?= site_url('event/' . $ce['slug']) ?>" class="text-slate-900 text-decoration-none">
                                                    <?= esc($ce['title']) ?>
                                                </a>
                                            </h5>
                                        </div>
                                        <div>
                                            <a href="<?= site_url('event/' . $ce['slug']) ?>" class="btn btn-sm btn-outline-primary radius-8 px-3 fw-bold">
                                                Detail Acara
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Calendar Sidebar -->
                <div class="col-lg-4">
                    <div class="evt-sidebar-widget">
                        <h4 class="evt-widget-title">
                            <iconify-icon icon="solar:bell-bold" class="text-warning"></iconify-icon>
                            <span>Informasi Jadwal Acara</span>
                        </h4>
                        <p class="text-xs text-slate-600 mb-3" style="line-height: 1.6;">
                            Jadwal pelaksanaan disusun secara berkala oleh Biro Pengadaan Barang dan Jasa Provinsi Lampung. Peserta yang terdaftar akan menerima notifikasi resmi.
                        </p>
                        <a href="<?= site_url('event') ?>" class="btn btn-primary w-100 radius-10 fw-bold">Kembali ke Tampilan Katalog</a>
                    </div>
                </div>
            </div>

            <!-- STANDARD CATALOG / AGENDA VIEW -->
        <?php else: ?>
            <!-- Spotlight Upcoming Event (Hero Card) -->
            <?php if (! empty($spotlightEvent)): ?>
                <div class="evt-spotlight-card">
                    <div class="evt-spotlight-badge">
                        <iconify-icon icon="solar:flame-bold"></iconify-icon>
                        <span>Agenda Sorotan</span>
                    </div>
                    <div class="row g-0 align-items-center">
                        <div class="col-lg-5">
                            <div style="height: 100%; min-height: 320px; position: relative; background: var(--evt-navy-dark);">
                                <?php if (! empty($spotlightEvent['cover_url'])): ?>
                                    <img src="<?= $spotlightEvent['cover_url'] ?>"
                                        alt="<?= esc($spotlightEvent['title']) ?>"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <div class="evt-card-placeholder">
                                        <iconify-icon icon="solar:calendar-date-bold" style="font-size: 56px; color: var(--evt-gold); margin-bottom: 12px;"></iconify-icon>
                                        <span class="badge bg-warning text-dark text-xs fw-bold px-3 py-1 radius-6 mb-2"><?= esc($spotlightEvent['type_label']) ?></span>
                                        <h5 class="fw-bold text-white px-3 mb-0" style="font-size: 16px; line-height: 1.4;"><?= esc($spotlightEvent['title']) ?></h5>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="evt-spotlight-inner">
                                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                    <span class="badge bg-primary text-white px-3 py-1 radius-6 text-xs text-uppercase fw-bold"><?= esc($spotlightEvent['type_label']) ?></span>
                                    <span class="badge bg-success-subtle text-success px-3 py-1 radius-6 text-xs text-uppercase fw-bold"><?= esc($spotlightEvent['mode_label']) ?></span>
                                    <span class="badge bg-warning-subtle text-warning px-3 py-1 radius-6 text-xs text-uppercase fw-bold"><?= esc($spotlightEvent['status_label']) ?></span>
                                    <?php if (! empty($spotlightEvent['certificate_enabled'])): ?>
                                        <span class="badge bg-info-subtle text-info px-3 py-1 radius-6 text-xs text-uppercase fw-bold"><iconify-icon icon="solar:diploma-verified-bold"></iconify-icon> E-Sertifikat</span>
                                    <?php endif; ?>
                                </div>
                                <h2 class="evt-spotlight-title">
                                    <a href="<?= site_url('event/' . $spotlightEvent['slug']) ?>">
                                        <?= esc($spotlightEvent['title']) ?>
                                    </a>
                                </h2>
                                <p class="text-slate-600 text-sm mb-3"><?= esc($spotlightEvent['summary']) ?></p>

                                <div class="d-flex align-items-center gap-4 flex-wrap text-sm text-slate-700 mb-4 pb-3 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <iconify-icon icon="solar:calendar-date-bold" class="text-primary text-lg"></iconify-icon>
                                        <strong class="text-slate-900"><?= esc($spotlightEvent['start_date']) ?></strong>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <iconify-icon icon="solar:clock-circle-bold" class="text-warning text-lg"></iconify-icon>
                                        <span><?= esc($spotlightEvent['time_range']) ?></span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <iconify-icon icon="solar:users-group-rounded-bold" class="text-info text-lg"></iconify-icon>
                                        <span>
                                            <?php if ($spotlightEvent['is_unlimited']): ?>
                                                Kuota: <strong>Fleksibel / Terbuka</strong>
                                            <?php else: ?>
                                                Sisa Kuota: <strong><?= $spotlightEvent['remaining'] ?> kursi</strong>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-3">
                                    <a href="<?= site_url('event/' . $spotlightEvent['slug']) ?>" class="btn btn-primary px-4 py-2 radius-10 fw-bold d-inline-flex align-items-center gap-2">
                                        <span>Daftar / Detail Acara</span>
                                        <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Grid & Sidebar Layout -->
            <div class="row g-4">
                <!-- Left: Events Grid -->
                <div class="col-lg-8">
                    <?php if (empty($items) && empty($spotlightEvent)): ?>
                        <div class="text-center p-5 bg-white border radius-18">
                            <div class="mb-3 text-slate-400" style="font-size: 48px;">
                                <iconify-icon icon="solar:calendar-minimalistic-linear"></iconify-icon>
                            </div>
                            <h4 class="fw-bold mb-2 text-slate-900">Tidak Ada Kegiatan yang Sesuai</h4>
                            <p class="text-slate-600 mb-4">Gunakan kata kunci atau kombinasi filter lain untuk menemukan agenda pengadaan yang Anda cari.</p>
                            <a href="<?= site_url('event') ?>" class="btn btn-primary px-4 py-2 radius-10 fw-bold">Lihat Semua Event</a>
                        </div>
                    <?php else: ?>
                        <div class="row g-4">
                            <?php foreach ($items as $item): ?>
                                <?php
                                $modeClass = match ($item['mode']) {
                                    'online'  => 'evt-mode-online',
                                    'offline' => 'evt-mode-offline',
                                    default   => 'evt-mode-hybrid',
                                };
                                ?>
                                <div class="col-md-6">
                                    <article class="evt-card">
                                        <div class="evt-card-header">
                                            <?php if (! empty($item['cover_url'])): ?>
                                                <img src="<?= $item['cover_url'] ?>" alt="<?= esc($item['title']) ?>" class="evt-card-img">
                                            <?php else: ?>
                                                <div class="evt-card-placeholder">
                                                    <iconify-icon icon="solar:calendar-date-bold" style="font-size: 42px; color: var(--evt-gold); margin-bottom: 8px;"></iconify-icon>
                                                    <span class="badge bg-warning text-dark text-xs fw-bold px-2 py-1 radius-6 mb-1"><?= esc($item['type_label']) ?></span>
                                                    <small class="text-white-50 text-xs">BegawiPengadaan Lampung</small>
                                                </div>
                                            <?php endif; ?>
                                            <div class="evt-date-badge">
                                                <span class="evt-date-day"><?= esc($item['start_day']) ?></span>
                                                <span class="evt-date-month"><?= esc($item['start_month']) ?></span>
                                            </div>
                                            <span class="evt-mode-pill <?= $modeClass ?>">
                                                <?= esc($item['mode']) ?>
                                            </span>
                                        </div>
                                        <div class="evt-card-body">
                                            <div class="evt-card-meta">
                                                <span class="badge bg-primary-subtle text-primary text-xs text-uppercase fw-bold"><?= esc($item['type_label']) ?></span>
                                                <?php if (! empty($item['certificate_enabled'])): ?>
                                                    <span class="badge bg-success-subtle text-success text-xs fw-bold"><iconify-icon icon="solar:diploma-verified-bold"></iconify-icon> Sertifikat</span>
                                                <?php endif; ?>
                                                <span>&middot;</span>
                                                <span><iconify-icon icon="solar:clock-circle-linear" class="align-middle text-primary"></iconify-icon> <?= esc($item['time_range']) ?></span>
                                            </div>

                                            <h3 class="evt-card-title">
                                                <a href="<?= site_url('event/' . $item['slug']) ?>">
                                                    <?= esc($item['title']) ?>
                                                </a>
                                            </h3>

                                            <p class="evt-card-summary">
                                                <?= esc($item['summary']) ?>
                                            </p>

                                            <div class="evt-card-location">
                                                <iconify-icon icon="solar:map-point-bold" class="text-danger flex-shrink-0 text-base"></iconify-icon>
                                                <span><?= esc($item['location']) ?></span>
                                            </div>

                                            <div class="evt-card-footer">
                                                <div class="evt-seats-badge">
                                                    <?php if ($item['is_past']): ?>
                                                        <span class="badge bg-secondary-subtle text-secondary">Kegiatan Selesai</span>
                                                    <?php elseif ($item['is_unlimited']): ?>
                                                        <span class="text-slate-600">Kuota: <strong>Fleksibel</strong></span>
                                                    <?php elseif ($item['remaining'] > 0): ?>
                                                        Sisa: <strong><?= $item['remaining'] ?> kuota</strong>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning-subtle text-warning">Kuota Penuh <?= ! empty($item['waitlist_enabled']) ? '(Waitlist)' : '' ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <a href="<?= site_url('event/' . $item['slug']) ?>" class="btn btn-sm btn-outline-primary px-3 py-1 radius-8 fw-bold">
                                                    Detail &rarr;
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination Bar -->
                        <?php if ($totalPages > 1): ?>
                            <?php
                            $queryParams = [];
                            if (! empty($keyword)) $queryParams['q'] = $keyword;
                            if (! empty($selectedType)) $queryParams['tipe'] = $selectedType;
                            if (! empty($selectedMode)) $queryParams['mode'] = $selectedMode;
                            if (! empty($selectedTimeline)) $queryParams['timeline'] = $selectedTimeline;
                            $buildEvtUrl = function ($p) use ($queryParams) {
                                $params = array_merge($queryParams, ['page' => $p]);
                                return current_url() . '?' . http_build_query($params);
                            };
                            ?>
                            <nav class="evt-pagination-wrap" aria-label="Navigasi Halaman Event">
                                <a href="<?= $buildEvtUrl(max(1, $currentPage - 1)) ?>"
                                    class="evt-page-btn <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                    <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
                                    <span class="ms-1 d-none d-sm-inline">Sebelumnya</span>
                                </a>

                                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                    <?php if ($p === 1 || $p === $totalPages || ($p >= $currentPage - 1 && $p <= $currentPage + 1)): ?>
                                        <a href="<?= $buildEvtUrl($p) ?>"
                                            class="evt-page-btn <?= $p === $currentPage ? 'active' : '' ?>">
                                            <?= $p ?>
                                        </a>
                                    <?php elseif ($p === 2 && $currentPage > 3): ?>
                                        <span class="evt-page-btn disabled">...</span>
                                    <?php elseif ($p === $totalPages - 1 && $currentPage < $totalPages - 2): ?>
                                        <span class="evt-page-btn disabled">...</span>
                                    <?php endif; ?>
                                <?php endfor; ?>

                                <a href="<?= $buildEvtUrl(min($totalPages, $currentPage + 1)) ?>"
                                    class="evt-page-btn <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                    <span class="me-1 d-none d-sm-inline">Berikutnya</span>
                                    <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                                </a>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Right: Sidebar -->
                <div class="col-lg-4">
                    <!-- Quick Search Widget -->
                    <div class="evt-sidebar-widget">
                        <h4 class="evt-widget-title">
                            <iconify-icon icon="solar:magnifer-bold" class="text-primary"></iconify-icon>
                            <span>Cari Agenda PBJ</span>
                        </h4>
                        <form action="<?= site_url('event') ?>" method="get">
                            <div class="input-group">
                                <input type="text" name="q" class="form-control radius-8 border" placeholder="Topik atau narasumber..." value="<?= esc($keyword ?? '') ?>">
                                <button class="btn btn-primary px-3 radius-8" type="submit">
                                    <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Type Filter Widget (All 8 dashboard types) -->
                    <div class="evt-sidebar-widget">
                        <h4 class="evt-widget-title">
                            <iconify-icon icon="solar:layers-bold" class="text-warning"></iconify-icon>
                            <span>Tipe Kegiatan</span>
                        </h4>
                        <div class="d-grid gap-2">
                            <?php foreach ($dashboardTypes as $tKey => $tLabel): ?>
                                <a href="<?= site_url('event?tipe=' . $tKey . (! empty($selectedMode) ? '&mode=' . $selectedMode : '')) ?>" class="d-flex align-items-center justify-content-between p-2 radius-8 text-decoration-none <?= $selectedType === $tKey ? 'bg-primary-subtle text-primary fw-bold' : 'text-slate-700 bg-light' ?>">
                                    <span><?= esc($tLabel) ?></span>
                                    <span class="badge bg-white text-muted border"><?= $typeCounts[$tKey] ?? 0 ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Upcoming Top 3 Events -->
                    <div class="evt-sidebar-widget">
                        <h4 class="evt-widget-title">
                            <iconify-icon icon="solar:fire-bold" class="text-danger"></iconify-icon>
                            <span>Kegiatan Terdekat</span>
                        </h4>
                        <?php if (empty($upcomingSidebar)): ?>
                            <p class="text-xs text-slate-500 mb-0">Belum ada agenda mendatang dalam waktu dekat.</p>
                        <?php else: ?>
                            <?php foreach ($upcomingSidebar as $us): ?>
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <span class="badge bg-primary-subtle text-primary text-xs text-uppercase fw-bold"><?= esc($us['type']) ?></span>
                                        <?php if (! empty($us['certificate_enabled'])): ?>
                                            <span class="badge bg-success-subtle text-success text-xs fw-bold">Sertifikat</span>
                                        <?php endif; ?>
                                        <small class="text-slate-500 fw-semibold"><?= date('d M Y', strtotime($us['start_at'])) ?></small>
                                    </div>
                                    <h6 class="fw-bold mb-1">
                                        <a href="<?= site_url('event/' . $us['slug']) ?>" class="text-slate-900 text-decoration-none">
                                            <?= esc($us['title']) ?>
                                        </a>
                                    </h6>
                                    <small class="text-slate-600 d-block"><iconify-icon icon="solar:map-point-linear" class="text-danger"></iconify-icon> <?= esc($us['location'] ?: 'Online via Zoom') ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Host / Biro PBJ Notice Box -->
                    <div class="p-4 bg-dark text-white border radius-18 shadow-sm" style="background: linear-gradient(135deg, #052244 0%, #0a3d74 100%) !important;">
                        <span class="badge bg-warning text-dark text-xs fw-bold px-2 py-1 mb-2">Biro PBJ Setda Lampung</span>
                        <h5 class="fw-bold text-white mb-2">Layanan Pembinaan &amp; Advokasi</h5>
                        <p class="text-xs text-slate-300 mb-3" style="line-height: 1.65;">
                            Seluruh kegiatan peningkatan kapasitas diselenggarakan secara resmi untuk mendukung profesionalisme SDM pengadaan di Lingkungan Pemerintah Provinsi Lampung.
                        </p>
                        <div class="text-xs text-warning fw-semibold d-flex align-items-center gap-1">
                            <iconify-icon icon="solar:phone-bold"></iconify-icon>
                            <span>Helpdesk PBJ: +62 851-1777-9783</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('assets/dashboard/js/lib/iconify-icon.min.js') ?>"></script>
<script>
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