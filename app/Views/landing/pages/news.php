<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<style>
    :root {
        --news-navy-dark: #052244;
        --news-navy: #082f5c;
        --news-blue: #0c5aa6;
        --news-blue-light: #e0f2fe;
        --news-gold: #d97706;
        --news-gold-light: #fef3c7;
        --news-slate-900: #0f172a;
        --news-slate-700: #1e293b;
        --news-slate-500: #334155;
        --news-slate-200: #cbd5e1;
        --news-slate-50: #f8fafc;
    }

    /* Page Hero Banner - Optimized spacing to avoid header collision */
    .news-hero-banner {
        background: linear-gradient(135deg, var(--news-navy-dark) 0%, var(--news-navy) 60%, var(--news-blue) 100%);
        padding: 175px 0 85px;
        position: relative;
        overflow: hidden;
        color: #ffffff;
    }

    .news-hero-banner::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -10%;
        width: 600px;
        height: 600px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(217, 119, 6, 0.15) 0%, transparent 70%);
        pointer-events: none;
    }

    .news-hero-banner::after {
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

    .news-breadcrumb {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.16);
        backdrop-filter: blur(8px);
        padding: 6px 18px;
        border-radius: 999px;
        font-size: 13.5px;
        font-weight: 600;
        color: #ffffff;
        margin-bottom: 20px;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .news-breadcrumb a {
        color: #fde68a;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .news-breadcrumb a:hover {
        color: #ffffff;
    }

    .news-hero-title {
        font-size: 42px;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.25;
        margin-bottom: 16px;
        letter-spacing: -0.5px;
    }

    .news-hero-title span {
        color: #fbbf24;
    }

    .news-hero-desc {
        font-size: 16.5px;
        color: #f1f5f9;
        line-height: 1.65;
        max-width: 740px;
        margin-bottom: 32px;
        font-weight: 400;
    }

    /* Hero Search Bar */
    .news-search-box {
        background: #ffffff;
        border-radius: 999px;
        padding: 8px 10px 8px 24px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.22);
        display: flex;
        align-items: center;
        max-width: 680px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .news-search-box:focus-within {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.28);
    }

    .news-search-box i.fa-search {
        color: #64748b;
        font-size: 18px;
        margin-right: 12px;
    }

    .news-search-box input {
        border: none;
        outline: none;
        width: 100%;
        font-size: 15px;
        color: var(--news-slate-900);
        background: transparent;
        font-weight: 500;
    }

    .news-search-box input::placeholder {
        color: #94a3b8;
    }

    .news-search-box button {
        background: linear-gradient(135deg, #0284c7 0%, var(--news-blue) 100%);
        color: #ffffff;
        border: none;
        padding: 12px 28px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 14.5px;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .news-search-box button:hover {
        background: linear-gradient(135deg, var(--news-blue) 0%, #0369a1 100%);
        transform: translateX(2px);
    }

    /* 2. Live KPI Metrics Ribbon */
    .news-metrics-section {
        margin-top: -38px;
        position: relative;
        z-index: 10;
        margin-bottom: 48px;
    }

    .news-metrics-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px 32px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
    }

    .news-kpi-item {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .news-kpi-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .news-kpi-num {
        font-size: 26px;
        font-weight: 800;
        color: var(--news-slate-900);
        line-height: 1.1;
        margin-bottom: 2px;
    }

    .news-kpi-label {
        font-size: 13px;
        color: #334155;
        font-weight: 600;
    }

    /* 3. Category Filter Pills */
    .news-filter-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 32px;
    }

    .news-category-pills {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .news-cat-pill {
        padding: 8px 18px;
        border-radius: 999px;
        font-size: 13.5px;
        font-weight: 600;
        color: #334155;
        background: #f1f5f9;
        text-decoration: none;
        border: 1px solid #cbd5e1;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .news-cat-pill:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .news-cat-pill.active {
        background: #0284c7;
        color: #ffffff;
        border-color: #0284c7;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
    }

    .news-cat-pill .pill-badge {
        font-size: 11px;
        padding: 2px 7px;
        border-radius: 999px;
        background: rgba(0, 0, 0, 0.08);
        color: inherit;
        font-weight: 700;
    }

    .news-cat-pill.active .pill-badge {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    .news-sort-select {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 8px 14px;
        font-size: 13.5px;
        font-weight: 600;
        color: #0f172a;
        background-color: #ffffff;
        outline: none;
        cursor: pointer;
    }

    /* 4. Headline Showcase Carousel (Up to 5 Featured Articles) */
    .news-headline-carousel {
        position: relative;
        margin-bottom: 40px;
    }

    .news-headline-card {
        background: #ffffff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .news-headline-card:hover {
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.1);
    }

    .news-headline-img-wrap {
        height: 380px;
        position: relative;
        overflow: hidden;
        background: #e2e8f0;
    }

    .news-headline-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .news-headline-card:hover .news-headline-img {
        transform: scale(1.03);
    }

    .news-badge-headline {
        position: absolute;
        top: 18px;
        left: 18px;
        background: #d97706;
        color: #ffffff;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 7px 16px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(217, 119, 6, 0.35);
    }

    .news-headline-content {
        padding: 40px 44px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .news-cat-badge {
        display: inline-block;
        background: #e0f2fe;
        color: #0284c7;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 4px 12px;
        border-radius: 6px;
        border: 1px solid #bae6fd;
        margin-bottom: 12px;
    }

    .news-headline-title {
        font-size: 26px;
        font-weight: 800;
        line-height: 1.35;
        margin-bottom: 14px;
        color: var(--news-slate-900);
    }

    .news-headline-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .news-headline-title a:hover {
        color: #0284c7;
    }

    .news-headline-desc {
        font-size: 15px;
        color: #475569;
        line-height: 1.65;
        margin-bottom: 22px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .news-meta-row {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        font-size: 13px;
        color: #475569;
        font-weight: 600;
        margin-bottom: 26px;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
    }

    .news-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Carousel Indicators & Controls */
    .headline-carousel-indicators {
        position: static;
        margin: 16px 0 0;
        display: flex;
        justify-content: center;
        gap: 8px;
    }

    .headline-carousel-indicators button {
        background: #f1f5f9 !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 999px !important;
        padding: 6px 14px !important;
        height: auto !important;
        width: auto !important;
        text-indent: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        color: #475569 !important;
        opacity: 1 !important;
        transition: all 0.2s ease !important;
    }

    .headline-carousel-indicators button.active {
        background: #0284c7 !important;
        border-color: #0284c7 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(2, 132, 199, 0.25) !important;
    }

    .headline-carousel-ctrl {
        width: 44px;
        height: 44px;
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid #cbd5e1;
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.9;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0f172a;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .headline-carousel-ctrl:hover {
        background: #0284c7;
        color: #ffffff;
        border-color: #0284c7;
        opacity: 1;
    }

    .headline-carousel-ctrl.carousel-control-prev {
        left: -18px;
    }

    .headline-carousel-ctrl.carousel-control-next {
        right: -18px;
    }

    /* 5. News Grid Cards */
    .news-grid-card {
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .news-grid-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
        border-color: #93c5fd;
    }

    .news-card-thumb-wrap {
        height: 200px;
        position: relative;
        overflow: hidden;
        background: #e2e8f0;
    }

    .news-card-thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.35s ease;
    }

    .news-grid-card:hover .news-card-thumb {
        transform: scale(1.05);
    }

    .news-card-cat-badge {
        position: absolute;
        top: 14px;
        left: 14px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(4px);
        color: #0f172a;
        font-size: 11.5px;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 999px;
        border: 1px solid rgba(0, 0, 0, 0.08);
    }

    .news-card-read-badge {
        position: absolute;
        bottom: 12px;
        right: 12px;
        background: rgba(15, 23, 42, 0.85);
        color: #ffffff;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        backdrop-filter: blur(4px);
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .news-card-body {
        padding: 24px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .news-card-date {
        font-size: 12.5px;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .news-card-title {
        font-size: 17.5px;
        font-weight: 700;
        line-height: 1.4;
        margin-bottom: 10px;
        color: var(--news-slate-900);
        height: 48px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .news-card-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .news-card-title a:hover {
        color: #0284c7;
    }

    .news-card-summary {
        font-size: 13.5px;
        color: #475569;
        line-height: 1.6;
        margin-bottom: 18px;
        height: 64px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .news-card-footer {
        margin-top: auto;
        padding-top: 14px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .news-card-author {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12.5px;
        font-weight: 700;
        color: #0f172a;
    }

    .news-card-author-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #e0f2fe;
        color: #0284c7;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        border: 1px solid #bae6fd;
    }

    .news-card-link {
        font-size: 13px;
        font-weight: 700;
        color: #0284c7;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: gap 0.2s ease, color 0.2s ease;
    }

    .news-card-link:hover {
        color: #0369a1;
        gap: 7px;
    }

    /* 6. Sidebar Widgets */
    .news-sidebar-widget {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        margin-bottom: 28px;
    }

    .news-widget-title {
        font-size: 17px;
        font-weight: 800;
        color: var(--news-slate-900);
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .news-trending-item {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        padding-bottom: 14px;
        margin-bottom: 14px;
        border-bottom: 1px solid #f8fafc;
    }

    .news-trending-item:last-child {
        padding-bottom: 0;
        margin-bottom: 0;
        border-bottom: none;
    }

    .news-trending-num {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: #f1f5f9;
        color: #334155;
        font-weight: 800;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .news-trending-item:first-child .news-trending-num {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .news-trending-content h5 {
        font-size: 14px;
        font-weight: 700;
        line-height: 1.4;
        margin-bottom: 4px;
    }

    .news-trending-content h5 a {
        color: var(--news-slate-900);
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .news-trending-content h5 a:hover {
        color: #0284c7;
    }

    .news-trending-meta {
        font-size: 11.5px;
        color: #64748b;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .news-widget-categories {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .news-widget-categories li {
        margin-bottom: 8px;
    }

    .news-widget-categories a {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border-radius: 8px;
        text-decoration: none;
        color: #334155;
        font-size: 13.5px;
        font-weight: 600;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }

    .news-widget-categories a:hover,
    .news-widget-categories a.active {
        background: #e0f2fe;
        color: #0284c7;
        border-color: #bae6fd;
    }

    .news-widget-categories .cat-count {
        background: #ffffff;
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 999px;
        border: 1px solid #e2e8f0;
    }

    .news-official-banner {
        background: linear-gradient(135deg, var(--news-navy-dark) 0%, var(--news-navy) 100%);
        border-radius: 16px;
        padding: 26px;
        color: #ffffff;
        box-shadow: 0 8px 24px rgba(5, 34, 68, 0.25);
    }

    /* 7. Clean Pagination Bar */
    .news-pagination-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 40px;
    }

    .news-page-btn {
        min-width: 40px;
        height: 40px;
        padding: 0 12px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #1e293b;
        font-size: 13.5px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .news-page-btn:hover {
        background: #f1f5f9;
        color: #0284c7;
        border-color: #93c5fd;
    }

    .news-page-btn.active {
        background: #0284c7;
        border-color: #0284c7;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(2, 132, 199, 0.25);
    }

    .news-page-btn.disabled {
        opacity: 0.5;
        pointer-events: none;
        background: #f8fafc;
    }

    /* Responsive Queries */
    @media (max-width: 991.98px) {
        .news-hero-banner {
            padding: 150px 0 65px;
        }
        .news-hero-title {
            font-size: 32px;
        }
        .news-headline-img-wrap {
            height: 260px;
        }
        .news-headline-content {
            padding: 28px 24px;
        }
        .headline-carousel-ctrl.carousel-control-prev {
            left: 8px;
        }
        .headline-carousel-ctrl.carousel-control-next {
            right: 8px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- 1. HERO BANNER -->
<section class="news-hero-banner">
    <div class="container">
        <div class="news-breadcrumb">
            <a href="<?= site_url('/') ?>"><i class="fas fa-home me-1"></i> Beranda</a>
            <span>/</span>
            <span>Berita PBJ</span>
            <?php if (! empty($currentCategory)): ?>
                <span>/</span>
                <span class="text-white"><?= esc($currentCategory['name']) ?></span>
            <?php endif; ?>
        </div>

        <h1 class="news-hero-title">
            Kabar &amp; Publikasi Resmi <br>
            <span>Pengadaan Barang/Jasa Provinsi Lampung</span>
        </h1>
        <p class="news-hero-desc">
            Informasi terkini mengenai kebijakan LKPP, transformasi e-katalog lokal, penguatan integritas tata kelola, kegiatan bimtek aparatur, dan capaian strategis UKPBJ di Provinsi Lampung.
        </p>

        <!-- Search Form -->
        <form action="<?= site_url('berita') ?>" method="get" class="news-search-box">
            <i class="fas fa-search text-slate-400"></i>
            <input type="text" name="q" value="<?= esc($keyword ?? '') ?>" placeholder="Cari judul berita, topik kebijakan, atau kata kunci...">
            <?php if (! empty($selectedCategory)): ?>
                <input type="hidden" name="kategori" value="<?= esc($selectedCategory) ?>">
            <?php endif; ?>
            <button type="submit">
                <span>Cari Berita</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>
</section>

<!-- 2. LIVE METRICS RIBBON -->
<div class="container news-metrics-section">
    <div class="news-metrics-card">
        <div class="row g-4">
            <div class="col-lg-3 col-6">
                <div class="news-kpi-item">
                    <div class="news-kpi-icon" style="background:#e0f2fe; color:#0284c7;">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div>
                        <div class="news-kpi-num"><?= number_format($stats['total_news'] > 0 ? $stats['total_news'] : 12) ?></div>
                        <div class="news-kpi-label">Berita Publikasi Resmi</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="news-kpi-item">
                    <div class="news-kpi-icon" style="background:#e0f7fa; color:#00838f;">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div>
                        <div class="news-kpi-num"><?= number_format($stats['total_categories'] > 0 ? $stats['total_categories'] : 6) ?></div>
                        <div class="news-kpi-label">Kategori Liputan PBJ</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="news-kpi-item">
                    <div class="news-kpi-icon" style="background:#fef3c7; color:#d97706;">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div>
                        <div class="news-kpi-num"><?= number_format($stats['total_views'] > 0 ? $stats['total_views'] : 6800) ?>+</div>
                        <div class="news-kpi-label">Total Pembaca Terjangkau</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="news-kpi-item">
                    <div class="news-kpi-icon" style="background:#dcfce7; color:#16a34a;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <div class="news-kpi-num"><?= number_format($stats['this_month'] > 0 ? $stats['this_month'] : 5) ?></div>
                        <div class="news-kpi-label">Rilis Bulan Ini</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. MAIN CONTENT AREA -->
<section class="pb-80">
    <div class="container">
        <!-- Filter Pills & Sorting Row -->
        <div class="news-filter-wrapper">
            <div class="news-category-pills">
                <a href="<?= site_url('berita') . (! empty($keyword) ? '?q=' . urlencode($keyword) : '') ?>" 
                   class="news-cat-pill <?= empty($selectedCategory) ? 'active' : '' ?>">
                    <span>Semua Kategori</span>
                    <span class="pill-badge"><?= $stats['total_news'] ?></span>
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="<?= site_url('berita/kategori/' . $cat['slug']) . (! empty($keyword) ? '?q=' . urlencode($keyword) : '') ?>" 
                       class="news-cat-pill <?= $selectedCategory === $cat['slug'] ? 'active' : '' ?>">
                        <span><?= esc($cat['name']) ?></span>
                        <span class="pill-badge"><?= $cat['news_count'] ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <form action="<?= current_url() ?>" method="get" class="d-flex align-items-center gap-2">
                <?php if (! empty($keyword)): ?>
                    <input type="hidden" name="q" value="<?= esc($keyword) ?>">
                <?php endif; ?>
                <label for="newsSort" class="text-xs fw-bold text-muted text-uppercase">Urutkan:</label>
                <select id="newsSort" name="sort" class="news-sort-select" onchange="this.form.submit()">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Terbaru</option>
                    <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Terpopuler</option>
                    <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Terlama</option>
                </select>
            </form>
        </div>

        <?php if (! empty($keyword)): ?>
            <!-- Active Search Bar Notification -->
            <div class="alert alert-info d-flex align-items-center justify-content-between mb-4 radius-12 border-0 shadow-sm" style="background:#e0f2fe; color:#0369a1;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-search text-lg"></i>
                    <span>Menampilkan hasil pencarian untuk: <strong>"<?= esc($keyword) ?>"</strong> (<?= $totalItems ?> berita ditemukan)</span>
                </div>
                <a href="<?= site_url('berita') ?>" class="btn btn-sm btn-outline-primary radius-8 fw-semibold">Reset Pencarian</a>
            </div>
        <?php endif; ?>

        <!-- Headline Showcase (Supports up to 5 Featured Articles) -->
        <?php 
            $headlineList = ! empty($headlineArticles) ? $headlineArticles : (! empty($headlineArticle) ? [$headlineArticle] : []);
        ?>
        <?php if (! empty($headlineList)): ?>
            <?php if (count($headlineList) > 1): ?>
                <!-- Multi-headline Carousel (Up to 5 Featured News) -->
                <div id="headlineCarousel" class="carousel slide news-headline-carousel mb-40" data-bs-ride="carousel" data-bs-interval="6500">
                    <div class="carousel-inner">
                        <?php foreach ($headlineList as $hIndex => $hItem): ?>
                            <div class="carousel-item <?= $hIndex === 0 ? 'active' : '' ?>">
                                <div class="news-headline-card mb-0">
                                    <div class="row g-0 align-items-center">
                                        <div class="col-lg-6">
                                            <div class="news-headline-img-wrap">
                                                <img src="<?= $hItem['cover_url'] ?: base_url('assets/main/images/blog/blog-details-img-1.jpg') ?>" 
                                                     alt="<?= esc($hItem['title']) ?>" 
                                                     class="news-headline-img">
                                                <div class="news-badge-headline">
                                                    <i class="fas fa-star me-1"></i>
                                                    <span>Berita Utama #<?= $hIndex + 1 ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="news-headline-content">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <span class="news-cat-badge mb-0"><?= esc($hItem['category_name']) ?></span>
                                                    <span class="badge px-8 py-4 radius-6 text-2xs fw-bold shadow-none" style="background:#fef3c7; color:#92400e; border:1px solid #fde68a; font-size: 11px;">
                                                        Headline <?= $hIndex + 1 ?> / <?= count($headlineList) ?>
                                                    </span>
                                                </div>
                                                <h2 class="news-headline-title">
                                                    <a href="<?= site_url('berita/' . $hItem['slug']) ?>">
                                                        <?= esc($hItem['title']) ?>
                                                    </a>
                                                </h2>
                                                <p class="news-headline-desc"><?= esc($hItem['summary']) ?></p>

                                                <div class="news-meta-row">
                                                    <div class="news-meta-item">
                                                        <i class="far fa-calendar-alt text-primary"></i>
                                                        <span><?= esc($hItem['date_formatted']) ?></span>
                                                    </div>
                                                    <div class="news-meta-item">
                                                        <i class="far fa-clock text-warning"></i>
                                                        <span><?= esc($hItem['read_time']) ?> baca</span>
                                                    </div>
                                                    <div class="news-meta-item">
                                                        <i class="far fa-eye text-info"></i>
                                                        <span><?= number_format($hItem['view_count']) ?> views</span>
                                                    </div>
                                                </div>

                                                <div>
                                                    <a href="<?= site_url('berita/' . $hItem['slug']) ?>" class="btn btn-primary radius-999 px-24 py-11 d-inline-flex align-items-center gap-2 fw-bold text-sm" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border:none;">
                                                        <span>Baca Berita Lengkap</span>
                                                        <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Carousel Indicators (Pills 1..5) -->
                    <div class="carousel-indicators headline-carousel-indicators">
                        <?php foreach ($headlineList as $hIndex => $hItem): ?>
                            <button type="button" data-bs-target="#headlineCarousel" data-bs-slide-to="<?= $hIndex ?>" 
                                    class="<?= $hIndex === 0 ? 'active' : '' ?>" 
                                    aria-current="<?= $hIndex === 0 ? 'true' : 'false' ?>" 
                                    aria-label="Berita Utama <?= $hIndex + 1 ?>">
                                <span>Utama #<?= $hIndex + 1 ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Prev/Next Controls -->
                    <button class="carousel-control-prev headline-carousel-ctrl" type="button" data-bs-target="#headlineCarousel" data-bs-slide="prev" aria-label="Sebelumnya">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="carousel-control-next headline-carousel-ctrl" type="button" data-bs-target="#headlineCarousel" data-bs-slide="next" aria-label="Selanjutnya">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            <?php else: ?>
                <?php $singleArticle = $headlineList[0]; ?>
                <div class="news-headline-card mb-40">
                    <div class="row g-0 align-items-center">
                        <div class="col-lg-6">
                            <div class="news-headline-img-wrap">
                                <img src="<?= $singleArticle['cover_url'] ?: base_url('assets/main/images/blog/blog-details-img-1.jpg') ?>" 
                                     alt="<?= esc($singleArticle['title']) ?>" 
                                     class="news-headline-img">
                                <div class="news-badge-headline">
                                    <i class="fas fa-star me-1"></i>
                                    <span>Berita Utama</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="news-headline-content">
                                <span class="news-cat-badge"><?= esc($singleArticle['category_name']) ?></span>
                                <h2 class="news-headline-title">
                                    <a href="<?= site_url('berita/' . $singleArticle['slug']) ?>">
                                        <?= esc($singleArticle['title']) ?>
                                    </a>
                                </h2>
                                <p class="news-headline-desc"><?= esc($singleArticle['summary']) ?></p>

                                <div class="news-meta-row">
                                    <div class="news-meta-item">
                                        <i class="far fa-calendar-alt text-primary"></i>
                                        <span><?= esc($singleArticle['date_formatted']) ?></span>
                                    </div>
                                    <div class="news-meta-item">
                                        <i class="far fa-clock text-warning"></i>
                                        <span><?= esc($singleArticle['read_time']) ?> baca</span>
                                    </div>
                                    <div class="news-meta-item">
                                        <i class="far fa-eye text-info"></i>
                                        <span><?= number_format($singleArticle['view_count']) ?> views</span>
                                    </div>
                                </div>

                                <div>
                                    <a href="<?= site_url('berita/' . $singleArticle['slug']) ?>" class="btn btn-primary radius-999 px-24 py-11 d-inline-flex align-items-center gap-2 fw-bold text-sm" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border:none;">
                                        <span>Baca Berita Lengkap</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Main Grid & Sidebar Layout -->
        <div class="row g-4">
            <!-- Left: News Grid -->
            <div class="col-lg-8">
                <?php if (empty($items) && empty($headlineArticle)): ?>
                    <div class="news-empty-state text-center p-5 bg-white border radius-16">
                        <div class="w-64-px h-64-px rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3 text-2xl text-muted">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <h4 class="fw-bold mb-2 text-dark">Belum Ada Berita yang Sesuai</h4>
                        <p class="text-slate-500 mb-4" style="max-width: 480px; margin: 0 auto;">
                            Tidak ditemukan artikel berita untuk kata kunci atau kategori yang Anda pilih. Coba gunakan kata kunci lain atau tampilkan seluruh berita.
                        </p>
                        <a href="<?= site_url('berita') ?>" class="btn btn-primary px-4 py-2 radius-10">
                            Lihat Semua Berita
                        </a>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($items as $index => $item): ?>
                            <?php 
                                $sampleImages = [
                                    base_url('assets/main/images/blog/blog-list-1-1.jpg'),
                                    base_url('assets/main/images/blog/blog-list-1-3.jpg'),
                                    base_url('assets/main/images/blog/blog-1-1.jpg'),
                                    base_url('assets/main/images/blog/blog-1-2.jpg'),
                                    base_url('assets/main/images/blog/blog-1-3.jpg'),
                                    base_url('assets/main/images/blog/blog-2-1.jpg'),
                                ];
                                $thumbUrl = $item['cover_url'] ?: $sampleImages[$index % count($sampleImages)];
                            ?>
                            <div class="col-md-6">
                                <article class="news-grid-card">
                                    <div class="news-card-thumb-wrap">
                                        <img src="<?= $thumbUrl ?>" alt="<?= esc($item['title']) ?>" class="news-card-thumb">
                                        <span class="news-card-cat-badge"><?= esc($item['category_name']) ?></span>
                                        <span class="news-card-read-badge">
                                            <i class="far fa-clock"></i>
                                            <?= esc($item['read_time']) ?>
                                        </span>
                                    </div>
                                    <div class="news-card-body">
                                        <div class="news-card-date">
                                            <i class="far fa-calendar-alt text-primary"></i>
                                            <span><?= esc($item['date_formatted']) ?></span>
                                            <span class="text-slate-300">&middot;</span>
                                            <span class="text-muted"><i class="far fa-eye"></i> <?= number_format($item['view_count']) ?></span>
                                        </div>
                                        <h3 class="news-card-title">
                                            <a href="<?= site_url('berita/' . $item['slug']) ?>">
                                                <?= esc($item['title']) ?>
                                            </a>
                                        </h3>
                                        <p class="news-card-summary">
                                            <?= esc($item['summary']) ?>
                                        </p>
                                        <div class="news-card-footer">
                                            <div class="news-card-author">
                                                <span class="news-card-author-avatar">B</span>
                                                <span>Biro PBJ</span>
                                            </div>
                                            <a href="<?= site_url('berita/' . $item['slug']) ?>" class="news-card-link">
                                                <span>Baca</span>
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Clean Pagination Bar -->
                    <?php if ($totalPages > 1): ?>
                        <?php
                            $queryParams = [];
                            if (! empty($keyword)) $queryParams['q'] = $keyword;
                            if (! empty($sort) && $sort !== 'newest') $queryParams['sort'] = $sort;
                            $buildUrl = function ($p) use ($queryParams) {
                                $params = array_merge($queryParams, ['page' => $p]);
                                return current_url() . '?' . http_build_query($params);
                            };
                        ?>
                        <nav class="news-pagination-wrap" aria-label="Navigasi Halaman Berita">
                            <a href="<?= $buildUrl(max(1, $currentPage - 1)) ?>" 
                               class="news-page-btn <?= $currentPage <= 1 ? 'disabled' : '' ?>" 
                               aria-label="Halaman Sebelumnya">
                                <i class="fas fa-chevron-left"></i>
                                <span class="ms-1 d-none d-sm-inline">Sebelumnya</span>
                            </a>

                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <?php if ($p === 1 || $p === $totalPages || ($p >= $currentPage - 1 && $p <= $currentPage + 1)): ?>
                                    <a href="<?= $buildUrl($p) ?>" 
                                       class="news-page-btn <?= $p === $currentPage ? 'active' : '' ?>">
                                        <?= $p ?>
                                    </a>
                                <?php elseif ($p === 2 && $currentPage > 3): ?>
                                    <span class="news-page-btn disabled">...</span>
                                <?php elseif ($p === $totalPages - 1 && $currentPage < $totalPages - 2): ?>
                                    <span class="news-page-btn disabled">...</span>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <a href="<?= $buildUrl(min($totalPages, $currentPage + 1)) ?>" 
                               class="news-page-btn <?= $currentPage >= $totalPages ? 'disabled' : '' ?>" 
                               aria-label="Halaman Berikutnya">
                                <span class="me-1 d-none d-sm-inline">Berikutnya</span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Right: Sidebar Widgets -->
            <div class="col-lg-4">
                <!-- Search Box Widget -->
                <div class="news-sidebar-widget">
                    <h4 class="news-widget-title">
                        <i class="fas fa-search text-primary"></i>
                        <span>Cari Artikel Berita</span>
                    </h4>
                    <form action="<?= site_url('berita') ?>" method="get">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control radius-8 border" placeholder="Kata kunci pengadaan..." value="<?= esc($keyword ?? '') ?>">
                            <button class="btn btn-primary px-3 radius-8" type="submit" aria-label="Cari">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Category Directory Widget -->
                <div class="news-sidebar-widget">
                    <h4 class="news-widget-title">
                        <i class="fas fa-folder-open text-warning"></i>
                        <span>Kategori Liputan</span>
                    </h4>
                    <ul class="news-widget-categories">
                        <li>
                            <a href="<?= site_url('berita') ?>" class="<?= empty($selectedCategory) ? 'active' : '' ?>">
                                <span>Semua Berita</span>
                                <span class="cat-count"><?= $stats['total_news'] ?></span>
                            </a>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                            <li>
                                <a href="<?= site_url('berita/kategori/' . $cat['slug']) ?>" class="<?= $selectedCategory === $cat['slug'] ? 'active' : '' ?>">
                                    <span><?= esc($cat['name']) ?></span>
                                    <span class="cat-count"><?= $cat['news_count'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Trending / Popular News Widget -->
                <div class="news-sidebar-widget">
                    <h4 class="news-widget-title">
                        <i class="fas fa-fire text-danger"></i>
                        <span>Berita Terpopuler</span>
                    </h4>
                    <?php foreach ($popularNews as $rank => $pop): ?>
                        <div class="news-trending-item">
                            <div class="news-trending-num"><?= $rank + 1 ?></div>
                            <div class="news-trending-content">
                                <h5>
                                    <a href="<?= site_url('berita/' . $pop['slug']) ?>">
                                        <?= esc($pop['title']) ?>
                                    </a>
                                </h5>
                                <div class="news-trending-meta">
                                    <span><?= date('d M Y', strtotime($pop['published_at'])) ?></span>
                                    <span>&middot;</span>
                                    <span><i class="far fa-eye"></i> <?= number_format($pop['view_count']) ?> views</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Official Bulletin / Biro PBJ Notice -->
                <div class="news-official-banner mb-4">
                    <span class="badge bg-warning text-dark px-3 py-1 radius-6 text-xs fw-bold mb-3 text-uppercase">Kanal Resmi</span>
                    <h4 class="text-lg fw-bold mb-2 text-white">Biro Pengadaan Barang &amp; Jasa Setda Provinsi Lampung</h4>
                    <p class="text-xs text-slate-300 mb-3" style="line-height: 1.6; color: #cbd5e1;">
                        Seluruh siaran pers, klarifikasi, dan pengumuman resmi dipublikasikan melalui kanal BegawiPBJ untuk mendukung keterbukaan informasi publik dan akuntabilitas PBJ.
                    </p>
                    <div class="d-flex align-items-center gap-2 pt-2 border-top border-white-10" style="border-top: 1px solid rgba(255,255,255,0.15);">
                        <i class="fas fa-phone-alt text-warning text-sm"></i>
                        <span class="text-xs text-white">Helpdesk: +62 851-1777-9783</span>
                    </div>
                </div>

                <!-- Quick Tags Widget -->
                <div class="news-sidebar-widget">
                    <h4 class="news-widget-title">
                        <i class="fas fa-tags text-info"></i>
                        <span>Topik Populer</span>
                    </h4>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= site_url('berita?q=P3DN') ?>" class="badge bg-light text-dark p-2 radius-6 text-decoration-none border fw-semibold">#P3DN</a>
                        <a href="<?= site_url('berita?q=Katalog') ?>" class="badge bg-light text-dark p-2 radius-6 text-decoration-none border fw-semibold">#E-Katalog</a>
                        <a href="<?= site_url('berita?q=Integritas') ?>" class="badge bg-light text-dark p-2 radius-6 text-decoration-none border fw-semibold">#Integritas</a>
                        <a href="<?= site_url('berita?q=Sertifikasi') ?>" class="badge bg-light text-dark p-2 radius-6 text-decoration-none border fw-semibold">#Sertifikasi Level-1</a>
                        <a href="<?= site_url('berita?q=Maturitas') ?>" class="badge bg-light text-dark p-2 radius-6 text-decoration-none border fw-semibold">#UKPBJ Proaktif</a>
                        <a href="<?= site_url('berita?q=Probity') ?>" class="badge bg-light text-dark p-2 radius-6 text-decoration-none border fw-semibold">#Probity Audit</a>
                        <a href="<?= site_url('berita?q=UMKM') ?>" class="badge bg-light text-dark p-2 radius-6 text-decoration-none border fw-semibold">#UMKM Lokal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('assets/dashboard/js/lib/iconify-icon.min.js') ?>"></script>
<?= $this->endSection() ?>
