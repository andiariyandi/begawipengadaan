<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
<style>
    /* =========================================================
       BEGAWIPBJ HOMEPAGE REFINED DESIGN DNA
       ========================================================= */
    :root {
        --bg-navy-dark: #052244;
        --bg-navy-main: #082f5c;
        --bg-navy-light: #0c5aa6;
        --bg-amber: #f59e0b;
        --bg-amber-dark: #d97706;
        --bg-emerald: #10b981;
        --bg-sky: #0ea5e9;
        --text-slate-900: #0f172a;
        --text-slate-700: #334155;
        --text-slate-500: #64748b;
        --card-border: #e2e8f0;
    }

    /* Hero Section */
    .hero-begawi {
        position: relative;
        background: linear-gradient(135deg, #052244 0%, #082f5c 55%, #0c5aa6 100%);
        color: #ffffff;
        padding: 140px 0 90px;
        overflow: hidden;
    }

    .hero-begawi::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at 85% 20%, rgba(245, 158, 11, 0.12) 0%, transparent 45%),
                    radial-gradient(circle at 10% 80%, rgba(14, 165, 233, 0.15) 0%, transparent 50%);
        pointer-events: none;
    }

    .hero-begawi__badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.24);
        padding: 8px 18px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.5px;
        color: #fde68a;
        margin-bottom: 24px;
        text-transform: uppercase;
    }

    .hero-begawi__title {
        font-size: 46px;
        font-weight: 800;
        line-height: 1.22;
        color: #ffffff;
        margin-bottom: 20px;
        letter-spacing: -0.5px;
    }

    .hero-begawi__title span {
        background: linear-gradient(90deg, #fcd34d 0%, #fbbf24 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-begawi__desc {
        font-size: 17px;
        line-height: 1.7;
        color: #cbd5e1;
        max-width: 640px;
        margin-bottom: 32px;
    }

    /* Global Search Box */
    .hero-search-box {
        background: #ffffff;
        border-radius: 16px;
        padding: 8px 12px 8px 20px;
        box-shadow: 0 20px 40px rgba(5, 34, 68, 0.35);
        display: flex;
        align-items: center;
        gap: 12px;
        max-width: 680px;
        margin-bottom: 24px;
    }

    .hero-search-box input {
        border: none;
        outline: none;
        font-size: 15px;
        color: #1e293b;
        flex: 1;
        background: transparent;
    }

    .hero-search-box input::placeholder {
        color: #94a3b8;
    }

    .hero-search-box select {
        border: none;
        outline: none;
        background: #f1f5f9;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        padding: 8px 14px;
        border-radius: 8px;
        cursor: pointer;
    }

    .hero-search-box .btn-search {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        padding: 10px 22px;
        font-weight: 700;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.25s ease;
    }

    .hero-search-box .btn-search:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(245, 158, 11, 0.35);
    }

    .hero-quick-tags {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        font-size: 13px;
        color: #94a3b8;
    }

    .hero-quick-tags span {
        font-weight: 600;
        color: #cbd5e1;
    }

    .hero-quick-tags a {
        background: rgba(255, 255, 255, 0.1);
        color: #f1f5f9;
        padding: 4px 12px;
        border-radius: 999px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .hero-quick-tags a:hover {
        background: rgba(245, 158, 11, 0.25);
        color: #fde68a;
    }

    /* KPI Glass Counter */
    .kpi-ribbon {
        margin-top: 50px;
        border-top: 1px solid rgba(255, 255, 255, 0.12);
        padding-top: 40px;
    }

    .kpi-card {
        background: rgba(255, 255, 255, 0.07);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 16px;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.3s ease;
    }

    .kpi-card:hover {
        background: rgba(255, 255, 255, 0.12);
        transform: translateY(-4px);
        border-color: rgba(245, 158, 11, 0.4);
    }

    .kpi-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        flex-shrink: 0;
    }

    .kpi-num {
        font-size: 28px;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.1;
        margin-bottom: 2px;
    }

    .kpi-label {
        font-size: 13px;
        font-weight: 500;
        color: #94a3b8;
        line-height: 1.3;
    }

    /* Section Styling */
    .section-spacious {
        padding: 80px 0;
    }

    .section-title-wrap {
        margin-bottom: 48px;
    }

    .section-title-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--bg-navy-light);
        margin-bottom: 8px;
    }

    .section-title-main {
        font-size: 32px;
        font-weight: 800;
        color: var(--bg-navy-dark);
        line-height: 1.25;
        margin-bottom: 12px;
        letter-spacing: -0.3px;
    }

    .section-title-desc {
        font-size: 16px;
        color: var(--text-slate-500);
        max-width: 680px;
        line-height: 1.6;
    }

    /* Ecosystem Pillars Grid */
    .pillar-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 32px 24px;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
    }

    .pillar-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--pillar-accent, #0c5aa6);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .pillar-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 35px rgba(8, 47, 92, 0.08);
        border-color: #cbd5e1;
    }

    .pillar-card:hover::before {
        opacity: 1;
    }

    .pillar-icon-box {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        margin-bottom: 20px;
        background: var(--pillar-bg, #eff6ff);
        color: var(--pillar-accent, #0c5aa6);
    }

    .pillar-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--bg-navy-dark);
        margin-bottom: 10px;
        line-height: 1.3;
    }

    .pillar-desc {
        font-size: 14px;
        color: var(--text-slate-500);
        line-height: 1.6;
        margin-bottom: 20px;
        flex-grow: 1;
    }

    .pillar-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 14px;
        font-weight: 700;
        color: var(--pillar-accent, #0c5aa6);
        text-decoration: none;
        transition: gap 0.2s ease;
    }

    .pillar-link:hover {
        gap: 10px;
        color: var(--bg-navy-dark);
    }

    /* Course Cards */
    .course-card-modern {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .course-card-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 32px rgba(8, 47, 92, 0.09);
        border-color: #cbd5e1;
    }

    .course-thumb-wrap {
        position: relative;
        height: 190px;
        background: #f1f5f9;
        overflow: hidden;
    }

    .course-thumb-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .course-card-modern:hover .course-thumb-wrap img {
        transform: scale(1.04);
    }

    .course-level-badge {
        position: absolute;
        top: 14px;
        left: 14px;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(6px);
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .course-duration-badge {
        position: absolute;
        bottom: 14px;
        right: 14px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(6px);
        color: #0f172a;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .course-body-modern {
        padding: 22px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .course-program-tag {
        font-size: 12px;
        font-weight: 700;
        color: #0284c7;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .course-title-modern {
        font-size: 17px;
        font-weight: 700;
        line-height: 1.4;
        color: var(--bg-navy-dark);
        margin-bottom: 12px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .course-title-modern a {
        color: inherit;
        text-decoration: none;
    }

    .course-title-modern a:hover {
        color: var(--bg-navy-light);
    }

    .course-meta-row {
        margin-top: auto;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Regulation Cards */
    .reg-card-modern {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .reg-card-modern:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(8, 47, 92, 0.08);
        border-color: #cbd5e1;
    }

    .reg-status-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 999px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .reg-status-badge.active {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .reg-status-badge.amended {
        background: #fffbeb;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .reg-status-badge.revoked {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .reg-doc-num {
        font-size: 13px;
        font-weight: 700;
        color: #0c5aa6;
        margin-bottom: 6px;
        display: block;
    }

    .reg-title-modern {
        font-size: 16px;
        font-weight: 700;
        line-height: 1.45;
        color: var(--bg-navy-dark);
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .reg-title-modern a {
        color: inherit;
        text-decoration: none;
    }

    .reg-title-modern a:hover {
        color: var(--bg-navy-light);
    }

    .reg-summary-text {
        font-size: 13px;
        color: var(--text-slate-500);
        line-height: 1.6;
        margin-bottom: 16px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .reg-actions-row {
        margin-top: auto;
        padding-top: 14px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    /* Lifecycle Stages Roadmap */
    .stage-card-step {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px 20px;
        text-align: center;
        position: relative;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .stage-card-step:hover {
        transform: translateY(-5px);
        border-color: #0c5aa6;
        box-shadow: 0 16px 30px rgba(8, 47, 92, 0.08);
    }

    .stage-number-badge {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0c5aa6 0%, #082f5c 100%);
        color: #ffffff;
        font-size: 18px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        box-shadow: 0 6px 14px rgba(12, 90, 166, 0.25);
    }

    .stage-step-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--bg-navy-dark);
        margin-bottom: 8px;
        line-height: 1.35;
    }

    .stage-step-desc {
        font-size: 13px;
        color: var(--text-slate-500);
        line-height: 1.55;
        margin-bottom: 16px;
        flex-grow: 1;
    }

    /* Knowledge Tabs */
    .knowledge-tab-nav {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 36px;
    }

    .knowledge-tab-btn {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 10px 22px;
        font-size: 14px;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .knowledge-tab-btn:hover {
        background: #f8fafc;
        color: #0c5aa6;
        border-color: #cbd5e1;
    }

    .knowledge-tab-btn.active {
        background: #082f5c;
        color: #ffffff;
        border-color: #082f5c;
        box-shadow: 0 6px 16px rgba(8, 47, 92, 0.2);
    }

    /* Article Modern Card */
    .article-card-modern {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
    }

    .article-card-modern:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 28px rgba(8, 47, 92, 0.08);
    }

    .article-thumb-modern {
        height: 180px;
        background: #e2e8f0;
        overflow: hidden;
    }

    .article-thumb-modern img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Template Modern Card */
    .template-item-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.25s ease;
        height: 100%;
    }

    .template-item-card:hover {
        border-color: #0c5aa6;
        background: #f8fafc;
        transform: translateY(-2px);
    }

    .template-file-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: #eff6ff;
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        flex-shrink: 0;
    }

    /* Event Card */
    .event-card-modern {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        display: flex;
        gap: 20px;
        align-items: flex-start;
        transition: all 0.3s ease;
        height: 100%;
    }

    .event-card-modern:hover {
        transform: translateY(-4px);
        border-color: #0c5aa6;
        box-shadow: 0 14px 28px rgba(8, 47, 92, 0.08);
    }

    .event-date-badge {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 12px 14px;
        text-align: center;
        flex-shrink: 0;
        min-width: 68px;
    }

    .event-date-day {
        font-size: 22px;
        font-weight: 800;
        color: #0c5aa6;
        line-height: 1;
        display: block;
    }

    .event-date-month {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        margin-top: 4px;
        display: block;
    }

    /* Biro PBJ Showcase */
    .biro-showcase-bg {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
    }

    /* Fast Action CTA */
    .cta-banner-modern {
        background: linear-gradient(135deg, #052244 0%, #082f5c 50%, #0c5aa6 100%);
        border-radius: 24px;
        padding: 60px 48px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(5, 34, 68, 0.2);
    }

    .cta-banner-modern::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.18) 0%, transparent 70%);
        pointer-events: none;
    }

    @media (max-width: 991px) {
        .hero-begawi {
            padding: 120px 0 60px;
        }
        .hero-begawi__title {
            font-size: 34px;
        }
        .section-title-main {
            font-size: 26px;
        }
        .cta-banner-modern {
            padding: 40px 24px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$stats          = $stats ?? ['total_regulations' => 0, 'total_courses' => 0, 'total_classes' => 0, 'total_knowledge' => 0, 'total_downloads' => 0, 'total_users' => 0];
$courses        = $courses ?? [];
$regulations    = $regulations ?? [];
$articles       = $articles ?? [];
$templates      = $templates ?? [];
$caseStudies    = $caseStudies ?? [];
$faqs           = $faqs ?? [];
$mediaItems     = $mediaItems ?? [];
$stages         = $stages ?? [];
$upcomingEvents = $upcomingEvents ?? [];
?>

<!-- =========================================================
     1. HERO SECTION: PORTAL UTAMA PBJ LAMPUNG
     ========================================================= -->
<section class="hero-begawi">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="hero-begawi__badge">
                    <iconify-icon icon="solar:shield-check-bold"></iconify-icon>
                    <span>Pemerintah Provinsi Lampung &middot; Biro PBJ Setda</span>
                </div>
                <h1 class="hero-begawi__title">
                    Ekosistem Digital Terpadu <br>
                    <span>Pengadaan Barang & Jasa</span>
                </h1>
                <p class="hero-begawi__desc">
                    Selamat datang di BegawiPBJ — platform satu pintu yang mengintegrasikan pembelajaran aparatur <strong>(Arena Belajar)</strong>, repositori literasi dan template dokumen <strong>(Bunga Rampai)</strong>, serta bank payung hukum dan silsilah aturan <strong>(Regulasi PBJ)</strong> di Provinsi Lampung.
                </p>

                <!-- Unified Portal Search -->
                <form action="<?= site_url('regulasi') ?>" method="get" id="heroSearchForm" class="hero-search-box">
                    <iconify-icon icon="solar:magnifer-linear" class="text-slate-400 text-xl"></iconify-icon>
                    <input type="text" name="q" id="heroSearchInput" placeholder="Cari materi kursus, regulasi perpres, template KAK/HPS, atau artikel..." required>
                    <select id="heroSearchTarget" class="d-none d-sm-block">
                        <option value="regulasi">Regulasi PBJ</option>
                        <option value="arena-belajar/course">Arena Belajar</option>
                        <option value="bunga-rampai/artikel">Bunga Rampai</option>
                    </select>
                    <button type="submit" class="btn-search">
                        <span>Cari</span>
                        <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                    </button>
                </form>

                <!-- Quick Keywords -->
                <div class="hero-quick-tags">
                    <span>Pencarian Cepat:</span>
                    <a href="<?= site_url('regulasi?category=peraturan-presiden') ?>">Perpres 12/2021</a>
                    <a href="<?= site_url('regulasi?category=peraturan-lkpp') ?>">Peraturan LKPP</a>
                    <a href="<?= site_url('bunga-rampai/template-dokumen') ?>">Template HPS &amp; KAK</a>
                    <a href="<?= site_url('arena-belajar/course') ?>">Course PBJ</a>
                    <a href="<?= site_url('bunga-rampai/studi-kasus') ?>">Studi Kasus Audit</a>
                </div>
            </div>

            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="position-relative">
                    <img src="<?= base_url('assets/main/images/resources/category-one-img-1.png') ?>" alt="BegawiPBJ Lampung" class="img-fluid" style="max-height: 420px; filter: drop-shadow(0 20px 30px rgba(0,0,0,0.35));">
                </div>
            </div>
        </div>

        <!-- Live KPI Ribbon Counter -->
        <div class="kpi-ribbon">
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <div class="kpi-card">
                        <div class="kpi-icon text-warning">
                            <iconify-icon icon="solar:diploma-verified-bold"></iconify-icon>
                        </div>
                        <div>
                            <div class="kpi-num"><?= number_format($stats['total_regulations'] > 0 ? $stats['total_regulations'] : 24) ?></div>
                            <div class="kpi-label">Regulasi PBJ Terbit</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="kpi-card">
                        <div class="kpi-icon text-info">
                            <iconify-icon icon="solar:mortarboard-bold"></iconify-icon>
                        </div>
                        <div>
                            <div class="kpi-num"><?= number_format($stats['total_courses'] > 0 ? $stats['total_courses'] : 18) ?>+</div>
                            <div class="kpi-label">Kursus &amp; Materi Belajar</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="kpi-card">
                        <div class="kpi-icon text-success">
                            <iconify-icon icon="solar:book-bookmark-bold"></iconify-icon>
                        </div>
                        <div>
                            <div class="kpi-num"><?= number_format($stats['total_knowledge'] > 0 ? $stats['total_knowledge'] : 45) ?>+</div>
                            <div class="kpi-label">Artikel &amp; Template Dokumen</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="kpi-card">
                        <div class="kpi-icon text-primary">
                            <iconify-icon icon="solar:users-group-rounded-bold"></iconify-icon>
                        </div>
                        <div>
                            <div class="kpi-num"><?= number_format($stats['total_users'] > 0 ? $stats['total_users'] : 150) ?>+</div>
                            <div class="kpi-label">Aparatur &amp; Peserta Terdaftar</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================
     2. PILAR EKOSISTEM BEGAWIPBJ (THE 5 CORE PILLARS)
     ========================================================= -->
<section class="section-spacious bg-white">
    <div class="container">
        <div class="section-title-wrap text-center">
            <div class="section-title-tag">
                <iconify-icon icon="solar:layers-bold"></iconify-icon>
                <span>Pilar Ekosistem BegawiPBJ</span>
            </div>
            <h2 class="section-title-main">Satu Pintu Layanan Digital Pengadaan Barang/Jasa</h2>
            <p class="section-title-desc mx-auto">Dirancang untuk memenuhi seluruh siklus kebutuhan Pejabat Pembuat Komitmen (PPK), Pokja Pemilihan, Pejabat Pengadaan, Penyedia Barang/Jasa, dan Auditor di Provinsi Lampung.</p>
        </div>

        <div class="row g-4">
            <!-- Pilar 1: Arena Belajar -->
            <div class="col-lg-4 col-md-6">
                <div class="pillar-card" style="--pillar-accent: #0c5aa6; --pillar-bg: #eff6ff;">
                    <div class="pillar-icon-box">
                        <iconify-icon icon="solar:mortarboard-bold"></iconify-icon>
                    </div>
                    <h3 class="pillar-title">Arena Belajar</h3>
                    <p class="pillar-desc">Pusat pelatihan mandiri berbasis e-learning, program sertifikasi PBJ, modul berjenjang, dan kelas interaktif bersama instruktur terakreditasi LKPP.</p>
                    <a href="<?= site_url('arena-belajar') ?>" class="pillar-link">
                        <span>Eksplorasi Pembelajaran</span>
                        <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                    </a>
                </div>
            </div>

            <!-- Pilar 2: Bunga Rampai -->
            <div class="col-lg-4 col-md-6">
                <div class="pillar-card" style="--pillar-accent: #10b981; --pillar-bg: #ecfdf5;">
                    <div class="pillar-icon-box">
                        <iconify-icon icon="solar:book-bookmark-bold"></iconify-icon>
                    </div>
                    <h3 class="pillar-title">Bunga Rampai PBJ</h3>
                    <p class="pillar-desc">Ensiklopedia literasi praktis: artikel wawasan, template dokumen kerja (KAK, HPS, Kontrak), studi kasus penanganan sanggah, dan galeri multimedia.</p>
                    <a href="<?= site_url('bunga-rampai') ?>" class="pillar-link">
                        <span>Buka Bunga Rampai</span>
                        <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                    </a>
                </div>
            </div>

            <!-- Pilar 3: Regulasi PBJ -->
            <div class="col-lg-4 col-md-6">
                <div class="pillar-card" style="--pillar-accent: #f59e0b; --pillar-bg: #fffbeb;">
                    <div class="pillar-icon-box">
                        <iconify-icon icon="solar:scale-bold"></iconify-icon>
                    </div>
                    <h3 class="pillar-title">Regulasi &amp; Payung Hukum</h3>
                    <p class="pillar-desc">Koleksi terstruktur peraturan perundang-undangan (Perpres, PerLKPP, Perda Lampung, Surat Edaran), riwayat naskah versi, dan silsilah relasi aturan hukum.</p>
                    <a href="<?= site_url('regulasi') ?>" class="pillar-link">
                        <span>Pusat Regulasi PBJ</span>
                        <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                    </a>
                </div>
            </div>

            <!-- Pilar 4: Klinik Konsultasi & FAQ -->
            <div class="col-lg-6 col-md-6">
                <div class="pillar-card" style="--pillar-accent: #8b5cf6; --pillar-bg: #f5f3ff;">
                    <div class="pillar-icon-box">
                        <iconify-icon icon="solar:chat-round-dots-bold"></iconify-icon>
                    </div>
                    <h3 class="pillar-title">Klinik Konsultasi &amp; Tanya Jawab</h3>
                    <p class="pillar-desc">Bimbingan teknis terarah dan tanya jawab spesifik mengenai persoalan regulasi, kendala pemilihan penyedia, penyesuaian harga, serta tata cara mitigasi risiko audit kepengadaan.</p>
                    <a href="<?= site_url('bunga-rampai/faq') ?>" class="pillar-link">
                        <span>Lihat FAQ &amp; Bimbingan</span>
                        <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                    </a>
                </div>
            </div>

            <!-- Pilar 5: Verifikasi Sertifikat Digital -->
            <div class="col-lg-6 col-md-6">
                <div class="pillar-card" style="--pillar-accent: #0284c7; --pillar-bg: #f0f9ff;">
                    <div class="pillar-icon-box">
                        <iconify-icon icon="solar:diploma-verified-bold"></iconify-icon>
                    </div>
                    <h3 class="pillar-title">Verifikasi Sertifikat Digital</h3>
                    <p class="pillar-desc">Validasi keabsahan sertifikat pelatihan, bukti tanda lulus, dan kompetensi yang diterbitkan resmi oleh Biro Pengadaan Barang dan Jasa Provinsi Lampung.</p>
                    <a href="<?= site_url('verifikasi-sertifikat') ?>" class="pillar-link">
                        <span>Cek Keabsahan Sertifikat</span>
                        <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================
     3. ARENA BELAJAR: KURSUS & KELAS TERPILIH
     ========================================================= -->
<section class="section-spacious bg-light">
    <div class="container">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
            <div>
                <div class="section-title-tag">
                    <iconify-icon icon="solar:mortarboard-bold"></iconify-icon>
                    <span>Arena Belajar PBJ</span>
                </div>
                <h2 class="section-title-main mb-1">Materi &amp; Kursus Terpopuler</h2>
                <p class="section-title-desc mb-0">Tingkatkan kompetensi Anda melalui modul belajar terstandar nasional.</p>
            </div>
            <a href="<?= site_url('arena-belajar/course') ?>" class="btn btn-outline-primary px-20 py-10 radius-8 fw-semibold d-inline-flex align-items-center gap-2">
                <span>Lihat Seluruh Kursus</span>
                <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
            </a>
        </div>

        <div class="row g-4">
            <?php if (empty($courses)): ?>
                <!-- Fallback Mock Courses jika DB belum terisi -->
                <?php
                $mockCourses = [
                    ['title' => 'Dasar-Dasar Pengadaan Barang/Jasa Pemerintah (Level-1)', 'level' => 'Pemula', 'hours' => '24 JP', 'program' => 'Sertifikasi PBJ', 'slug' => 'dasar-dasar-pengadaan-level-1'],
                    ['title' => 'Teknik Penyusunan HPS & Spesifikasi Teknis yang Akuntabel', 'level' => 'Menengah', 'hours' => '16 JP', 'program' => 'Teknis PPK', 'slug' => 'penyusunan-hps-spesifikasi'],
                    ['title' => 'Tata Kelola Pengadaan Melalui E-Katalog & E-Purchasing V6', 'level' => 'Pemula', 'hours' => '12 JP', 'program' => 'Katalog PBJ', 'slug' => 'tata-kelola-e-katalog'],
                ];
                foreach ($mockCourses as $c): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="course-card-modern">
                            <div class="course-thumb-wrap">
                                <img src="<?= base_url('assets/main/images/resources/courses-1-1.jpg') ?>" alt="<?= esc($c['title']) ?>">
                                <span class="course-level-badge"><?= esc($c['level']) ?></span>
                                <span class="course-duration-badge">
                                    <iconify-icon icon="solar:clock-circle-linear"></iconify-icon>
                                    <?= esc($c['hours']) ?>
                                </span>
                            </div>
                            <div class="course-body-modern">
                                <span class="course-program-tag"><?= esc($c['program']) ?></span>
                                <h3 class="course-title-modern">
                                    <a href="<?= site_url('arena-belajar/course') ?>"><?= esc($c['title']) ?></a>
                                </h3>
                                <p class="text-slate-500 text-sm mb-3">Panduan komprehensif bagi PPK dan Pokja dalam menerapkan regulasi terbaru.</p>
                                <div class="course-meta-row">
                                    <span class="text-xs text-muted d-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:users-group-rounded-linear"></iconify-icon> 120+ Peserta
                                    </span>
                                    <a href="<?= site_url('arena-belajar/course') ?>" class="btn btn-sm btn-primary px-3 py-1 radius-6">Pelajari</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?php foreach ($courses as $c): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="course-card-modern">
                            <div class="course-thumb-wrap">
                                <?php
                                $thumbImg = ! empty($c['cover_asset_path'])
                                    ? base_url('file/' . (int) ($c['cover_file_id'] ?? 0))
                                    : (! empty($c['cover_url']) ? $c['cover_url'] : base_url('assets/main/images/resources/courses-1-1.jpg'));
                                ?>
                                <img src="<?= esc($thumbImg) ?>" alt="<?= esc($c['title']) ?>" onerror="this.src='<?= base_url('assets/main/images/resources/courses-1-1.jpg') ?>'">
                                <span class="course-level-badge"><?= esc($c['level'] ?? 'Umum') ?></span>
                                <?php if (! empty($c['duration_hours'])): ?>
                                    <span class="course-duration-badge">
                                        <iconify-icon icon="solar:clock-circle-linear"></iconify-icon>
                                        <?= (int) $c['duration_hours'] ?> JP
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="course-body-modern">
                                <span class="course-program-tag"><?= esc($c['program_title'] ?? 'Arena Belajar') ?></span>
                                <h3 class="course-title-modern">
                                    <a href="<?= site_url('arena-belajar/course/' . esc($c['slug'])) ?>"><?= esc($c['title']) ?></a>
                                </h3>
                                <div class="course-meta-row">
                                    <span class="text-xs text-muted d-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:book-linear"></iconify-icon> Modul Terstruktur
                                    </span>
                                    <a href="<?= site_url('arena-belajar/course/' . esc($c['slug'])) ?>" class="btn btn-sm btn-primary px-3 py-1 radius-6">Buka Materi</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- =========================================================
     4. TAHAPAN SIKLUS PENGADAAN INTERAKTIF
     ========================================================= -->
<section class="section-spacious bg-white">
    <div class="container">
        <div class="section-title-wrap text-center">
            <div class="section-title-tag">
                <iconify-icon icon="solar:refresh-circle-bold"></iconify-icon>
                <span>Siklus Pengadaan Barang/Jasa</span>
            </div>
            <h2 class="section-title-main">Panduan Alur Kerja dari Perencanaan hingga Serah Terima</h2>
            <p class="section-title-desc mx-auto">Telusuri regulasi, standar dokumen, dan materi pembelajaran yang spesifik disesuaikan dengan setiap tahapan siklus PBJ.</p>
        </div>

        <div class="row g-3 row-cols-lg-5 row-cols-md-3 row-cols-sm-2 row-cols-1">
            <!-- Tahap 1 -->
            <div class="col">
                <div class="stage-card-step">
                    <div class="stage-number-badge">1</div>
                    <h4 class="stage-step-title">Perencanaan Pengadaan</h4>
                    <p class="stage-step-desc">Identifikasi kebutuhan, penetapan barang/jasa, cara pengadaan, jadwal, dan pengumuman SiRUP.</p>
                    <a href="<?= site_url('regulasi?stage_id=1') ?>" class="btn btn-sm btn-outline-primary w-100 radius-6">Regulasi Terkait</a>
                </div>
            </div>

            <!-- Tahap 2 -->
            <div class="col">
                <div class="stage-card-step">
                    <div class="stage-number-badge">2</div>
                    <h4 class="stage-step-title">Persiapan Pengadaan</h4>
                    <p class="stage-step-desc">Penyusunan Spesifikasi Teknis/KAK, Harga Perkiraan Sendiri (HPS), dan rancangan kontrak.</p>
                    <a href="<?= site_url('regulasi?stage_id=2') ?>" class="btn btn-sm btn-outline-primary w-100 radius-6">Regulasi Terkait</a>
                </div>
            </div>

            <!-- Tahap 3 -->
            <div class="col">
                <div class="stage-card-step">
                    <div class="stage-number-badge">3</div>
                    <h4 class="stage-step-title">Pemilihan Penyedia</h4>
                    <p class="stage-step-desc">Pelaksanaan E-Purchasing (Katalog), Tender, Seleksi, Pengadaan Langsung, dan Evaluasi Penawaran.</p>
                    <a href="<?= site_url('regulasi?stage_id=3') ?>" class="btn btn-sm btn-outline-primary w-100 radius-6">Regulasi Terkait</a>
                </div>
            </div>

            <!-- Tahap 4 -->
            <div class="col">
                <div class="stage-card-step">
                    <div class="stage-number-badge">4</div>
                    <h4 class="stage-step-title">Pelaksanaan Kontrak</h4>
                    <p class="stage-step-desc">Penerbitan SPPBJ, SPMK, pengawasan pekerjaan, addendum kontrak, dan mitigasi risiko.</p>
                    <a href="<?= site_url('regulasi?stage_id=4') ?>" class="btn btn-sm btn-outline-primary w-100 radius-6">Regulasi Terkait</a>
                </div>
            </div>

            <!-- Tahap 5 -->
            <div class="col">
                <div class="stage-card-step">
                    <div class="stage-number-badge">5</div>
                    <h4 class="stage-step-title">Serah Terima Hasil</h4>
                    <p class="stage-step-desc">Pemeriksaan hasil pekerjaan, Berita Acara Serah Terima (BAST), pembayaran, dan evaluasi kinerja penyedia.</p>
                    <a href="<?= site_url('regulasi?stage_id=5') ?>" class="btn btn-sm btn-outline-primary w-100 radius-6">Regulasi Terkait</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================
     5. PUSAT REGULASI PBJ TERBARU & UNGGULAN
     ========================================================= -->
<section class="section-spacious bg-light">
    <div class="container">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
            <div>
                <div class="section-title-tag">
                    <iconify-icon icon="solar:scale-bold"></iconify-icon>
                    <span>Bank Naskah Hukum PBJ</span>
                </div>
                <h2 class="section-title-main mb-1">Regulasi &amp; Produk Hukum Terbaru</h2>
                <p class="section-title-desc mb-0">Arsip resmi Peraturan Presiden, Peraturan LKPP, dan Peraturan Daerah Provinsi Lampung dengan status hukum terverifikasi.</p>
            </div>
            <a href="<?= site_url('regulasi') ?>" class="btn btn-outline-primary px-20 py-10 radius-8 fw-semibold d-inline-flex align-items-center gap-2">
                <span>Buka Pusat Regulasi</span>
                <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
            </a>
        </div>

        <div class="row g-4">
            <?php if (empty($regulations)): ?>
                <!-- Fallback Mock Regulations -->
                <?php
                $mockRegs = [
                    ['num' => 'Perpres No. 12 Tahun 2021', 'issuer' => 'Presiden RI', 'title' => 'Perubahan atas Peraturan Presiden Nomor 16 Tahun 2018 tentang Pengadaan Barang/Jasa Pemerintah', 'status' => 'active', 'year' => '2021', 'slug' => 'perpres-12-tahun-2021'],
                    ['num' => 'Peraturan LKPP No. 12 Tahun 2021', 'issuer' => 'LKPP', 'title' => 'Pedoman Pelaksanaan Pengadaan Barang/Jasa Pemerintah Melalui Penyedia', 'status' => 'active', 'year' => '2021', 'slug' => 'perlkpp-12-tahun-2021'],
                    ['num' => 'Peraturan Gubernur Lampung No. 34 Tahun 2023', 'issuer' => 'Gubernur Lampung', 'title' => 'Tata Cara Pelaksanaan E-Purchasing dan Pengadaan Langsung di Lingkungan Pemerintah Provinsi Lampung', 'status' => 'active', 'year' => '2023', 'slug' => 'pergub-lampung-34-2023'],
                ];
                foreach ($mockRegs as $r): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="reg-card-modern">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="reg-status-badge active">
                                    <iconify-icon icon="solar:check-circle-bold"></iconify-icon> Berlaku
                                </span>
                                <span class="text-xs text-muted fw-semibold"><?= esc($r['year']) ?></span>
                            </div>
                            <span class="reg-doc-num"><?= esc($r['num']) ?></span>
                            <h3 class="reg-title-modern">
                                <a href="<?= site_url('regulasi') ?>"><?= esc($r['title']) ?></a>
                            </h3>
                            <p class="reg-summary-text">Pedoman acuan teknis hukum pengadaan barang/jasa bagi aparatur sipil negara dan pelaku usaha.</p>
                            <div class="reg-actions-row">
                                <span class="text-xs text-muted"><?= esc($r['issuer']) ?></span>
                                <a href="<?= site_url('regulasi') ?>" class="btn btn-sm btn-outline-primary px-3 py-1 radius-6">Lihat Naskah</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?php foreach ($regulations as $r): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="reg-card-modern">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <?php $legalStatus = strtolower((string) ($r['legal_status'] ?? 'active')); ?>
                                <?php if (in_array($legalStatus, ['active', 'berlaku'], true)): ?>
                                    <span class="reg-status-badge active"><iconify-icon icon="solar:check-circle-bold"></iconify-icon> Berlaku</span>
                                <?php elseif (in_array($legalStatus, ['amended', 'diubah'], true)): ?>
                                    <span class="reg-status-badge amended"><iconify-icon icon="solar:pen-bold"></iconify-icon> Diubah</span>
                                <?php else: ?>
                                    <span class="reg-status-badge revoked"><iconify-icon icon="solar:close-circle-bold"></iconify-icon> Dicabut</span>
                                <?php endif; ?>

                                <?php if (! empty($r['document_year'])): ?>
                                    <span class="text-xs text-muted fw-semibold">Tahun <?= esc($r['document_year']) ?></span>
                                <?php endif; ?>
                            </div>

                            <span class="reg-doc-num"><?= esc($r['document_number'] ?? 'Naskah PBJ') ?></span>
                            <h3 class="reg-title-modern">
                                <a href="<?= site_url('regulasi/' . esc($r['slug'])) ?>"><?= esc($r['title']) ?></a>
                            </h3>
                            <p class="reg-summary-text"><?= esc(! empty($r['summary']) ? $r['summary'] : 'Dokumen regulasi resmi pengadaan barang/jasa Pemerintah Provinsi Lampung.') ?></p>
                            
                            <div class="reg-actions-row">
                                <span class="text-xs text-muted"><?= esc($r['issuer_name'] ?? 'Biro PBJ') ?></span>
                                <div class="d-flex align-items-center gap-1">
                                    <a href="<?= site_url('regulasi/' . esc($r['slug'])) ?>" class="btn btn-sm btn-outline-primary px-2 py-1 radius-6" title="Lihat Detail &amp; Silsilah">Naskah</a>
                                    <a href="<?= site_url('regulasi/' . esc($r['slug']) . '/unduh') ?>" class="btn btn-sm btn-primary px-2 py-1 radius-6" title="Unduh PDF">Unduh</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- =========================================================
     6. BUNGA RAMPAI: MULTI-TAB KNOWLEDGE HUB
     ========================================================= -->
<section class="section-spacious bg-white">
    <div class="container">
        <div class="section-title-wrap text-center">
            <div class="section-title-tag">
                <iconify-icon icon="solar:book-bookmark-bold"></iconify-icon>
                <span>Bunga Rampai Pengetahuan</span>
            </div>
            <h2 class="section-title-main">Pusat Literasi, Template Dokumen &amp; Studi Kasus</h2>
            <p class="section-title-desc mx-auto">Akses instan ribuan referensi praktis, format template terstandar, dan pembahasan solusi kasus pengadaan nyata.</p>
        </div>

        <!-- Tab Controls -->
        <div class="knowledge-tab-nav" role="tablist">
            <button class="knowledge-tab-btn active" id="tab-art-btn" data-bs-toggle="pill" data-bs-target="#tab-articles" type="button" role="tab">
                <iconify-icon icon="solar:document-text-bold"></iconify-icon>
                <span>Artikel PBJ</span>
            </button>
            <button class="knowledge-tab-btn" id="tab-tpl-btn" data-bs-toggle="pill" data-bs-target="#tab-templates" type="button" role="tab">
                <iconify-icon icon="solar:file-download-bold"></iconify-icon>
                <span>Template Dokumen</span>
            </button>
            <button class="knowledge-tab-btn" id="tab-cs-btn" data-bs-toggle="pill" data-bs-target="#tab-cases" type="button" role="tab">
                <iconify-icon icon="solar:shield-warning-bold"></iconify-icon>
                <span>Studi Kasus</span>
            </button>
            <button class="knowledge-tab-btn" id="tab-faq-btn" data-bs-toggle="pill" data-bs-target="#tab-faqs" type="button" role="tab">
                <iconify-icon icon="solar:question-circle-bold"></iconify-icon>
                <span>Tanya Jawab (FAQ)</span>
            </button>
        </div>

        <!-- Tab Panes -->
        <div class="tab-content">
            <!-- Pane 1: Artikel -->
            <div class="tab-pane fade show active" id="tab-articles" role="tabpanel">
                <div class="row g-4">
                    <?php if (empty($articles)): ?>
                        <div class="col-12 text-center py-4">
                            <p class="text-muted">Koleksi artikel wawasan PBJ dapat ditelusuri di portal Bunga Rampai.</p>
                            <a href="<?= site_url('bunga-rampai/artikel') ?>" class="btn btn-primary radius-8 px-20">Lihat Semua Artikel</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($articles as $art): ?>
                            <div class="col-lg-3 col-md-6">
                                <div class="article-card-modern">
                                    <div class="article-thumb-modern">
                                        <img src="<?= ! empty($art['cover_path']) ? base_url('file/' . (int) ($art['cover_file_id'] ?? 0)) : base_url('assets/main/images/blog/blog-1-1.jpg') ?>" alt="<?= esc($art['title']) ?>" onerror="this.src='<?= base_url('assets/main/images/blog/blog-1-1.jpg') ?>'">
                                    </div>
                                    <div class="p-3 d-flex flex-column flex-grow-1">
                                        <span class="text-xs text-primary fw-bold text-uppercase mb-1">Artikel PBJ</span>
                                        <h4 class="text-sm fw-bold mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            <a href="<?= site_url('bunga-rampai/artikel/' . esc($art['slug'])) ?>" class="text-dark text-decoration-none"><?= esc($art['title']) ?></a>
                                        </h4>
                                        <p class="text-xs text-slate-500 mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= esc($art['summary'] ?? '') ?></p>
                                        <a href="<?= site_url('bunga-rampai/artikel/' . esc($art['slug'])) ?>" class="mt-auto text-xs text-primary fw-bold text-decoration-none d-flex align-items-center gap-1">
                                            Baca Selengkapnya <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="<?= site_url('bunga-rampai/artikel') ?>" class="btn btn-outline-primary px-24 py-10 radius-8">Jelajahi Seluruh Artikel PBJ</a>
                </div>
            </div>

            <!-- Pane 2: Template Dokumen -->
            <div class="tab-pane fade" id="tab-templates" role="tabpanel">
                <div class="row g-3">
                    <?php if (empty($templates)): ?>
                        <div class="col-md-6">
                            <div class="template-item-card">
                                <div class="template-file-icon"><iconify-icon icon="solar:document-bold"></iconify-icon></div>
                                <div class="flex-grow-1">
                                    <h5 class="text-sm fw-bold mb-1">Format Kerangka Acuan Kerja (KAK) Jasa Konsultansi</h5>
                                    <span class="text-xs text-muted">Format DOCX &middot; Sesuai PerLKPP 12/2021</span>
                                </div>
                                <a href="<?= site_url('bunga-rampai/template-dokumen') ?>" class="btn btn-sm btn-outline-primary">Unduh</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="template-item-card">
                                <div class="template-file-icon"><iconify-icon icon="solar:document-bold"></iconify-icon></div>
                                <div class="flex-grow-1">
                                    <h5 class="text-sm fw-bold mb-1">Kertas Kerja Rincian Perhitungan HPS Pekerjaan Konstruksi</h5>
                                    <span class="text-xs text-muted">Format XLSX &middot; Dilengkapi formula analisa</span>
                                </div>
                                <a href="<?= site_url('bunga-rampai/template-dokumen') ?>" class="btn btn-sm btn-outline-primary">Unduh</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($templates as $tpl): ?>
                            <div class="col-md-6">
                                <div class="template-item-card">
                                    <div class="template-file-icon"><iconify-icon icon="solar:file-text-bold"></iconify-icon></div>
                                    <div class="flex-grow-1">
                                        <h5 class="text-sm fw-bold mb-1"><?= esc($tpl['title']) ?></h5>
                                        <span class="text-xs text-muted"><?= esc($tpl['summary'] ?? 'Template Dokumen Kerja SPSE') ?></span>
                                    </div>
                                    <a href="<?= site_url('bunga-rampai/template-dokumen/' . esc($tpl['slug'])) ?>" class="btn btn-sm btn-outline-primary">Lihat &amp; Unduh</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="<?= site_url('bunga-rampai/template-dokumen') ?>" class="btn btn-outline-primary px-24 py-10 radius-8">Lihat Semua Template Dokumen</a>
                </div>
            </div>

            <!-- Pane 3: Studi Kasus -->
            <div class="tab-pane fade" id="tab-cases" role="tabpanel">
                <div class="row g-4">
                    <?php if (empty($caseStudies)): ?>
                        <div class="col-md-6">
                            <div class="card p-24 radius-12 border bg-light h-100">
                                <span class="badge bg-warning-50 text-warning-700 px-2 py-1 text-xs fw-bold mb-2" style="width: fit-content;">Kasus Pemilihan</span>
                                <h5 class="text-base fw-bold mb-2">Penanganan Sanggahan Terkait Sertifikat Badan Usaha (SBU) yang Habis Masa Berlaku</h5>
                                <p class="text-sm text-slate-500 mb-3">Analisis yuridis mitigasi risiko pembatalan tender dan panduan pembuktian kualifikasi bagi Pokja.</p>
                                <a href="<?= site_url('bunga-rampai/studi-kasus') ?>" class="text-primary text-sm fw-bold text-decoration-none">Pelajari Solusi Kasus &rarr;</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-24 radius-12 border bg-light h-100">
                                <span class="badge bg-info-50 text-info-700 px-2 py-1 text-xs fw-bold mb-2" style="width: fit-content;">Kasus Kontrak</span>
                                <h5 class="text-base fw-bold mb-2">Pemberian Kesempatan 50 Hari Kalender &amp; Mekanisme Denda Keterlambatan</h5>
                                <p class="text-sm text-slate-500 mb-3">Penerapan Pasal 56 Perpres 16/2018 jo Perpres 12/2021 pada akhir tahun anggaran tanpa temuan BPK.</p>
                                <a href="<?= site_url('bunga-rampai/studi-kasus') ?>" class="text-primary text-sm fw-bold text-decoration-none">Pelajari Solusi Kasus &rarr;</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($caseStudies as $cs): ?>
                            <div class="col-md-6">
                                <div class="card p-24 radius-12 border bg-light h-100">
                                    <span class="badge bg-info-50 text-info-700 px-2 py-1 text-xs fw-bold mb-2" style="width: fit-content;">Studi Kasus</span>
                                    <h5 class="text-base fw-bold mb-2"><?= esc($cs['title']) ?></h5>
                                    <p class="text-sm text-slate-500 mb-3"><?= esc($cs['summary'] ?? '') ?></p>
                                    <a href="<?= site_url('bunga-rampai/studi-kasus/' . esc($cs['slug'])) ?>" class="text-primary text-sm fw-bold text-decoration-none">Pelajari Solusi &rarr;</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="<?= site_url('bunga-rampai/studi-kasus') ?>" class="btn btn-outline-primary px-24 py-10 radius-8">Jelajahi Seluruh Studi Kasus</a>
                </div>
            </div>

            <!-- Pane 4: FAQ -->
            <div class="tab-pane fade" id="tab-faqs" role="tabpanel">
                <div class="row g-3">
                    <?php if (empty($faqs)): ?>
                        <div class="col-12">
                            <div class="accordion" id="homeFaqAccordion">
                                <div class="accordion-item radius-10 mb-2 border">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#hfaq1">
                                            Kapan Pejabat Pembuat Komitmen (PPK) wajib menyusun Dokumen HPS?
                                        </button>
                                    </h2>
                                    <div id="hfaq1" class="accordion-collapse collapse show" data-bs-parent="#homeFaqAccordion">
                                        <div class="accordion-body text-slate-600 text-sm">
                                            HPS disusun paling lama 28 hari kerja sebelum batas akhir pemasukan penawaran untuk pemilihan dengan pascakualifikasi, atau sebelum pemasukan dokumen kualifikasi untuk prakualifikasi. HPS tidak bersifat rahasia, namun rincian HPS bersifat rahasia.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item radius-10 mb-2 border">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#hfaq2">
                                            Apakah pelatihan di Arena Belajar BegawiPBJ mendapatkan sertifikat resmi?
                                        </button>
                                    </h2>
                                    <div id="hfaq2" class="accordion-collapse collapse" data-bs-parent="#homeFaqAccordion">
                                        <div class="accordion-body text-slate-600 text-sm">
                                            Ya. Seluruh peserta yang menyelesaikan program pelatihan bersertifikasi dan lulus evaluasi kelulusan akan mendapatkan e-Sertifikat resmi ber-QR Code dari Biro PBJ Setda Provinsi Lampung yang keabsahannya dapat diverifikasi langsung.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="accordion" id="homeFaqAccordion">
                                <?php foreach ($faqs as $i => $fq): ?>
                                    <div class="accordion-item radius-10 mb-2 border">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button <?= $i === 0 ? '' : 'collapsed' ?> fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#hfaq_<?= $i ?>">
                                                <?= esc($fq['title']) ?>
                                            </button>
                                        </h2>
                                        <div id="hfaq_<?= $i ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" data-bs-parent="#homeFaqAccordion">
                                            <div class="accordion-body text-slate-600 text-sm">
                                                <?= esc($fq['summary'] ?? 'Informasi tanya jawab regulasi pengadaan.') ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="<?= site_url('bunga-rampai/faq') ?>" class="btn btn-outline-primary px-24 py-10 radius-8">Buka Halaman Tanya Jawab Lengkap</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================
     7. AGENDA & EVENT PBJ MENDATANG
     ========================================================= -->
<section class="section-spacious bg-light">
    <div class="container">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
            <div>
                <div class="section-title-tag">
                    <iconify-icon icon="solar:calendar-bold"></iconify-icon>
                    <span>Agenda &amp; Pelatihan PBJ</span>
                </div>
                <h2 class="section-title-main mb-1">Event, Webinar &amp; Bimtek Mendatang</h2>
                <p class="section-title-desc mb-0">Ikuti kegiatan peningkatan kapasitas pengadaan bersama pakar LKPP dan praktisi senior.</p>
            </div>
            <a href="<?= site_url('event') ?>" class="btn btn-outline-primary px-20 py-10 radius-8 fw-semibold d-inline-flex align-items-center gap-2">
                <span>Lihat Seluruh Event</span>
                <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
            </a>
        </div>

        <div class="row g-4">
            <?php if (empty($upcomingEvents)): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="event-card-modern">
                        <div class="event-date-badge">
                            <span class="event-date-day">28</span>
                            <span class="event-date-month">Okt</span>
                        </div>
                        <div>
                            <span class="badge bg-success-50 text-success-700 text-xs fw-bold mb-1">Online &middot; Zoom</span>
                            <h4 class="text-base fw-bold mb-1">Webinar Nasional: Optimalisasi E-Katalog V6 bagi Pelaku Usaha Mikro &amp; Kecil</h4>
                            <p class="text-xs text-muted mb-3">Sosialisasi teknis pendaftaran etalase dan transaksi e-purchasing Pemprov Lampung.</p>
                            <a href="<?= site_url('event') ?>" class="btn btn-sm btn-primary px-3 py-1 radius-6">Daftar Gratis</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="event-card-modern">
                        <div class="event-date-badge">
                            <span class="event-date-day">12</span>
                            <span class="event-date-month">Nov</span>
                        </div>
                        <div>
                            <span class="badge bg-primary-50 text-primary-700 text-xs fw-bold mb-1">Tatap Muka &middot; Bandar Lampung</span>
                            <h4 class="text-base fw-bold mb-1">Bimbingan Teknis: Penyusunan Dokumen Pemilihan &amp; Mitigasi Risiko Sanggah</h4>
                            <p class="text-xs text-muted mb-3">Pelatihan intensif khusus Pokja Pemilihan UKPBJ Kabupaten/Kota se-Lampung.</p>
                            <a href="<?= site_url('event') ?>" class="btn btn-sm btn-primary px-3 py-1 radius-6">Daftar Peserta</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="event-card-modern">
                        <div class="event-date-badge">
                            <span class="event-date-day">20</span>
                            <span class="event-date-month">Nov</span>
                        </div>
                        <div>
                            <span class="badge bg-warning-50 text-warning-700 text-xs fw-bold mb-1">Hybrid &middot; Gedung Pusiban</span>
                            <h4 class="text-base fw-bold mb-1">Sosialisasi Surat Edaran Pencegahan Korupsi &amp; Gratifikasi Pengadaan</h4>
                            <p class="text-xs text-muted mb-3">Kolaborasi bersama Inspektorat Daerah Provinsi Lampung dan APIP.</p>
                            <a href="<?= site_url('event') ?>" class="btn btn-sm btn-primary px-3 py-1 radius-6">Detail Acara</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($upcomingEvents as $evt): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="event-card-modern">
                            <div class="event-date-badge">
                                <?php
                                $eventTime = ! empty($evt['start_at']) ? strtotime($evt['start_at']) : time();
                                ?>
                                <span class="event-date-day"><?= date('d', $eventTime) ?></span>
                                <span class="event-date-month"><?= date('M', $eventTime) ?></span>
                            </div>
                            <div>
                                <span class="badge bg-primary-50 text-primary-700 text-xs fw-bold mb-1 text-capitalize">
                                    <?= esc($evt['mode'] ?? 'Online') ?> &middot; <?= esc($evt['type'] ?? 'Webinar') ?>
                                </span>
                                <h4 class="text-base fw-bold mb-1"><?= esc($evt['title']) ?></h4>
                                <p class="text-xs text-muted mb-3"><?= esc($evt['summary'] ?? ($evt['location'] ?? 'Informasi kegiatan PBJ.')) ?></p>
                                <a href="<?= site_url('event/' . esc($evt['slug'])) ?>" class="btn btn-sm btn-primary px-3 py-1 radius-6">Detail Acara</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- =========================================================
     8. BIRO PBJ SETDA PROVINSI LAMPUNG & TAUTAN EKOSISTEM
     ========================================================= -->
<section class="section-spacious biro-showcase-bg">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <div class="section-title-tag">
                    <iconify-icon icon="solar:city-bold"></iconify-icon>
                    <span>Tentang Penyelenggara</span>
                </div>
                <h2 class="section-title-main">Biro Pengadaan Barang &amp; Jasa Sekretariat Daerah Provinsi Lampung</h2>
                <p class="text-slate-600 mb-4" style="line-height: 1.7;">
                    Sebagai Unit Kerja Pengadaan Barang/Jasa (UKPBJ) Pemerintah Provinsi Lampung, kami berkomitmen mewujudkan pengadaan yang <strong>kredibel, transparan, akuntabel, dan bebas gratifikasi</strong>. BegawiPBJ dihadirkan guna mempermudah transfer pengetahuan, peningkatan SDM pengadaan, dan kepatuhan terhadap regulasi LKPP.
                </p>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3 p-3 bg-white border radius-12">
                            <div class="text-warning text-2xl"><iconify-icon icon="solar:shield-check-bold"></iconify-icon></div>
                            <div>
                                <strong class="d-block text-sm text-dark">Zona Integritas</strong>
                                <small class="text-slate-500">Bebas Korupsi &amp; Gratifikasi</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3 p-3 bg-white border radius-12">
                            <div class="text-info text-2xl"><iconify-icon icon="solar:users-group-two-rounded-bold"></iconify-icon></div>
                            <div>
                                <strong class="d-block text-sm text-dark">Layanan Konsultasi</strong>
                                <small class="text-slate-500">Pendampingan Pokja &amp; PPK</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="p-32 bg-white border radius-20 shadow-sm">
                    <h4 class="text-lg fw-bold mb-3 text-dark">Portal Mitra Ekosistem Nasional &amp; Daerah</h4>
                    <p class="text-sm text-slate-500 mb-4">Akses langsung ke sistem layanan pengadaan resmi pemerintah:</p>
                    <div class="d-grid gap-2">
                        <a href="https://lpse.lampungprov.go.id" target="_blank" class="btn btn-outline-neutral-300 p-3 d-flex align-items-center justify-content-between radius-10 text-dark text-decoration-none">
                            <div class="d-flex align-items-center gap-3">
                                <iconify-icon icon="solar:server-bold" class="text-primary text-xl"></iconify-icon>
                                <div>
                                    <strong class="d-block text-sm">LPSE Provinsi Lampung</strong>
                                    <small class="text-muted">Sistem Pengadaan Secara Elektronik (SPSE)</small>
                                </div>
                            </div>
                            <iconify-icon icon="solar:arrow-right-up-linear" class="text-muted"></iconify-icon>
                        </a>
                        <a href="https://sirup.lkpp.go.id" target="_blank" class="btn btn-outline-neutral-300 p-3 d-flex align-items-center justify-content-between radius-10 text-dark text-decoration-none">
                            <div class="d-flex align-items-center gap-3">
                                <iconify-icon icon="solar:document-add-bold" class="text-success text-xl"></iconify-icon>
                                <div>
                                    <strong class="d-block text-sm">SiRUP LKPP</strong>
                                    <small class="text-muted">Sistem Informasi Rencana Umum Pengadaan</small>
                                </div>
                            </div>
                            <iconify-icon icon="solar:arrow-right-up-linear" class="text-muted"></iconify-icon>
                        </a>
                        <a href="https://e-katalog.lkpp.go.id" target="_blank" class="btn btn-outline-neutral-300 p-3 d-flex align-items-center justify-content-between radius-10 text-dark text-decoration-none">
                            <div class="d-flex align-items-center gap-3">
                                <iconify-icon icon="solar:cart-large-4-bold" class="text-warning text-xl"></iconify-icon>
                                <div>
                                    <strong class="d-block text-sm">Katalog Elektronik (E-Katalog LKPP)</strong>
                                    <small class="text-muted">Belanja pengadaan daring komoditas nasional &amp; lokal</small>
                                </div>
                            </div>
                            <iconify-icon icon="solar:arrow-right-up-linear" class="text-muted"></iconify-icon>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================
     9. CALL TO ACTION (CTA) BANNER
     ========================================================= -->
<section class="section-spacious bg-white">
    <div class="container">
        <div class="cta-banner-modern">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-warning text-dark px-3 py-1 radius-6 text-xs fw-bold mb-3 text-uppercase">Mari Bergabung Bersama BegawiPBJ</span>
                    <h3 class="text-3xl fw-bold text-white mb-3">Tingkatkan Profesionalisme &amp; Kepatuhan Pengadaan Anda</h3>
                    <p class="text-slate-300 mb-0" style="max-width: 600px; font-size: 16px;">
                        Daftar akun sebagai aparatur pemerintah, staf pengadaan, atau penyedia barang/jasa untuk mengikuti kelas terstruktur, mengunduh template dokumen, dan memperoleh sertifikat kompetensi.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        <a href="<?= route_to('dashboard.auth.register') ?>" class="btn btn-warning px-24 py-12 radius-10 fw-bold text-dark d-inline-flex align-items-center gap-2">
                            <span>Daftar Akun Baru</span>
                            <iconify-icon icon="solar:user-plus-bold"></iconify-icon>
                        </a>
                        <a href="<?= site_url('kontak') ?>" class="btn btn-outline-light px-20 py-12 radius-10 fw-semibold d-inline-flex align-items-center gap-2">
                            <span>Hubungi Kami</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Dynamic search form target switcher
        const searchForm = document.getElementById('heroSearchForm');
        const targetSelect = document.getElementById('heroSearchTarget');
        const searchInput = document.getElementById('heroSearchInput');

        if (searchForm && targetSelect) {
            targetSelect.addEventListener('change', function () {
                const base = '<?= site_url('') ?>';
                searchForm.action = base + '/' + this.value;
                if (this.value === 'regulasi') {
                    searchInput.name = 'q';
                    searchInput.placeholder = 'Cari nomor regulasi, kata kunci, atau perpres...';
                } else if (this.value === 'arena-belajar/course') {
                    searchInput.name = 'keyword';
                    searchInput.placeholder = 'Cari topik kursus, kelas, atau silabus PBJ...';
                } else {
                    searchInput.name = 'q';
                    searchInput.placeholder = 'Cari artikel, template KAK/HPS, atau studi kasus...';
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>
