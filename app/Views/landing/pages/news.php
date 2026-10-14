<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<style>
    :root {
        --news-navy-dark: #052244;
        --news-navy: #082f5c;
        --news-blue: #0c5aa6;
        --news-blue-light: #e0f2fe;
        --news-gold: #f59e0b;
        --news-gold-light: #fef3c7;
        --news-slate-900: #0f172a;
        --news-slate-700: #334155;
        --news-slate-500: #64748b;
        --news-slate-200: #e2e8f0;
        --news-slate-50: #f8fafc;
    }

    /* Page Hero Banner */
    .news-hero-banner {
        background: linear-gradient(135deg, var(--news-navy-dark) 0%, var(--news-navy) 60%, var(--news-blue) 100%);
        padding: 160px 0 80px;
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
        background: radial-gradient(circle, rgba(245, 158, 11, 0.12) 0%, transparent 70%);
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
        background: radial-gradient(circle, rgba(12, 90, 166, 0.25) 0%, transparent 70%);
        pointer-events: none;
    }

    .news-breadcrumb {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        padding: 6px 16px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 500;
        color: #e2e8f0;
        margin-bottom: 18px;
        border: 1px solid rgba(255, 255, 255, 0.2);
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
        font-size: 40px;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.25;
        margin-bottom: 14px;
        letter-spacing: -0.5px;
    }

    .news-hero-title span {
        color: var(--news-gold);
    }

    .news-hero-desc {
        font-size: 16px;
        color: #cbd5e1;
        line-height: 1.6;
        max-width: 720px;
        margin-bottom: 30px;
    }

    /* Hero Search Bar */
    .news-search-box {
        background: #ffffff;
        border-radius: 16px;
        padding: 8px 10px 8px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        max-width: 650px;
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.24);
        border: 1px solid rgba(255, 255, 255, 0.8);
    }

    .news-search-box input {
        border: none;
        outline: none;
        font-size: 15px;
        color: var(--news-slate-900);
        flex: 1;
        background: transparent;
    }

    .news-search-box input::placeholder {
        color: #94a3b8;
    }

    .news-search-box button {
        background: linear-gradient(135deg, var(--news-blue) 0%, var(--news-navy) 100%);
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
    }

    .news-search-box button:hover {
        background: linear-gradient(135deg, var(--news-gold) 0%, #d97706 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(245, 158, 11, 0.35);
    }

    /* Metrics Ribbon */
    .news-metrics-section {
        margin-top: -45px;
        position: relative;
        z-index: 10;
        margin-bottom: 50px;
    }

    .news-metrics-card {
        background: #ffffff;
        border: 1px solid var(--news-slate-200);
        border-radius: 18px;
        padding: 24px 28px;
        box-shadow: 0 14px 35px rgba(8, 47, 92, 0.08);
    }

    .news-kpi-item {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .news-kpi-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        flex-shrink: 0;
    }

    .news-kpi-num {
        font-size: 24px;
        font-weight: 800;
        color: var(--news-navy-dark);
        line-height: 1.1;
    }

    .news-kpi-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--news-slate-500);
        margin-top: 3px;
    }

    /* Filter & Category Pills */
    .news-filter-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 36px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--news-slate-200);
    }

    .news-category-pills {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .news-cat-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        border-radius: 999px;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--news-slate-700);
        background: #ffffff;
        border: 1px solid var(--news-slate-200);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .news-cat-pill:hover {
        background: var(--news-blue-light);
        color: var(--news-blue);
        border-color: #bae6fd;
    }

    .news-cat-pill.active {
        background: var(--news-navy-dark);
        color: #ffffff;
        border-color: var(--news-navy-dark);
        box-shadow: 0 4px 14px rgba(5, 34, 68, 0.2);
    }

    .news-cat-pill .pill-badge {
        font-size: 11px;
        font-weight: 700;
        background: rgba(0, 0, 0, 0.08);
        padding: 2px 7px;
        border-radius: 999px;
    }

    .news-cat-pill.active .pill-badge {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
    }

    .news-sort-select {
        background: #ffffff;
        border: 1px solid var(--news-slate-200);
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 600;
        color: var(--news-slate-700);
        outline: none;
        cursor: pointer;
    }

    /* Headline / Featured Card */
    .news-headline-card {
        background: #ffffff;
        border: 1px solid var(--news-slate-200);
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 40px;
        box-shadow: 0 16px 36px rgba(8, 47, 92, 0.07);
        transition: all 0.3s ease;
    }

    .news-headline-card:hover {
        box-shadow: 0 22px 45px rgba(8, 47, 92, 0.12);
        border-color: #cbd5e1;
    }

    .news-headline-img-wrap {
        position: relative;
        height: 100%;
        min-height: 340px;
        background: linear-gradient(135deg, var(--news-navy-dark), var(--news-blue));
        overflow: hidden;
    }

    .news-headline-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    .news-headline-card:hover .news-headline-img {
        transform: scale(1.04);
    }

    .news-badge-headline {
        position: absolute;
        top: 18px;
        left: 18px;
        background: linear-gradient(135deg, var(--news-gold) 0%, #d97706 100%);
        color: #ffffff;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 5px 14px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(217, 119, 6, 0.4);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .news-headline-content {
        padding: 36px 32px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 100%;
    }

    .news-cat-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--news-blue);
        background: var(--news-blue-light);
        padding: 4px 12px;
        border-radius: 6px;
        margin-bottom: 12px;
        width: fit-content;
    }

    .news-headline-title {
        font-size: 26px;
        font-weight: 800;
        line-height: 1.3;
        color: var(--news-navy-dark);
        margin-bottom: 14px;
        letter-spacing: -0.3px;
    }

    .news-headline-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .news-headline-title a:hover {
        color: var(--news-blue);
    }

    .news-headline-desc {
        font-size: 15px;
        color: var(--news-slate-500);
        line-height: 1.65;
        margin-bottom: 22px;
    }

    .news-meta-row {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        font-size: 13px;
        color: var(--news-slate-500);
        margin-bottom: 24px;
    }

    .news-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* Standard News Grid Card */
    .news-grid-card {
        background: #ffffff;
        border: 1px solid var(--news-slate-200);
        border-radius: 18px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .news-grid-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 36px rgba(8, 47, 92, 0.1);
        border-color: #cbd5e1;
    }

    .news-card-thumb-wrap {
        position: relative;
        height: 200px;
        background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
        overflow: hidden;
    }

    .news-card-thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .news-grid-card:hover .news-card-thumb {
        transform: scale(1.05);
    }

    .news-card-cat-badge {
        position: absolute;
        top: 14px;
        left: 14px;
        background: rgba(5, 34, 68, 0.85);
        backdrop-filter: blur(8px);
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .news-card-read-badge {
        position: absolute;
        bottom: 14px;
        right: 14px;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(6px);
        color: var(--news-slate-900);
        font-size: 11.5px;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .news-card-body {
        padding: 22px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .news-card-date {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--news-slate-500);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .news-card-title {
        font-size: 17.5px;
        font-weight: 700;
        line-height: 1.4;
        color: var(--news-navy-dark);
        margin-bottom: 10px;
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
        color: var(--news-blue);
    }

    .news-card-summary {
        font-size: 13.5px;
        color: var(--news-slate-500);
        line-height: 1.6;
        margin-bottom: 18px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex-grow: 1;
    }

    .news-card-footer {
        padding-top: 14px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: auto;
    }

    .news-card-author {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--news-slate-700);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .news-card-author-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #0284c7;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
    }

    .news-card-link {
        font-size: 13px;
        font-weight: 700;
        color: var(--news-blue);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: gap 0.2s ease;
    }

    .news-card-link:hover {
        gap: 7px;
        color: var(--news-navy-dark);
    }

    /* Sidebar Widgets */
    .news-sidebar-widget {
        background: #ffffff;
        border: 1px solid var(--news-slate-200);
        border-radius: 18px;
        padding: 26px;
        margin-bottom: 28px;
        box-shadow: 0 6px 20px rgba(8, 47, 92, 0.04);
    }

    .news-widget-title {
        font-size: 17px;
        font-weight: 800;
        color: var(--news-navy-dark);
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f1f5f9;
        position: relative;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .news-widget-title::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 36px;
        height: 2px;
        background: var(--news-blue);
    }

    /* Category list in sidebar */
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
        padding: 9px 14px;
        border-radius: 10px;
        color: var(--news-slate-700);
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 600;
        transition: all 0.2s ease;
        background: #f8fafc;
    }

    .news-widget-categories a:hover,
    .news-widget-categories a.active {
        background: var(--news-blue-light);
        color: var(--news-blue);
    }

    .news-widget-categories .cat-count {
        font-size: 11.5px;
        font-weight: 700;
        background: #ffffff;
        padding: 2px 8px;
        border-radius: 999px;
        color: var(--news-slate-500);
        border: 1px solid var(--news-slate-200);
    }

    /* Trending List in sidebar */
    .news-trending-item {
        display: flex;
        gap: 14px;
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 1px dashed var(--news-slate-200);
    }

    .news-trending-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .news-trending-num {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: var(--news-blue-light);
        color: var(--news-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 800;
        flex-shrink: 0;
    }

    .news-trending-content h5 {
        font-size: 14px;
        font-weight: 700;
        line-height: 1.35;
        margin-bottom: 4px;
    }

    .news-trending-content h5 a {
        color: var(--news-navy-dark);
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .news-trending-content h5 a:hover {
        color: var(--news-blue);
    }

    .news-trending-meta {
        font-size: 12px;
        color: var(--news-slate-500);
    }

    /* Official Notice Callout in sidebar */
    .news-official-banner {
        background: linear-gradient(135deg, var(--news-navy-dark) 0%, var(--news-navy) 100%);
        border-radius: 18px;
        padding: 24px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
    }

    .news-official-banner::after {
        content: '';
        position: absolute;
        top: -20px;
        right: -20px;
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: rgba(245, 158, 11, 0.2);
    }

    /* Pagination */
    .news-pagination-wrap {
        margin-top: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .news-page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 42px;
        height: 42px;
        padding: 0 14px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        color: var(--news-slate-700);
        background: #ffffff;
        border: 1px solid var(--news-slate-200);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .news-page-btn:hover {
        background: var(--news-blue-light);
        color: var(--news-blue);
        border-color: #bae6fd;
    }

    .news-page-btn.active {
        background: var(--news-navy-dark);
        color: #ffffff;
        border-color: var(--news-navy-dark);
        box-shadow: 0 4px 12px rgba(5, 34, 68, 0.25);
    }

    .news-page-btn.disabled {
        opacity: 0.4;
        pointer-events: none;
    }

    /* Empty State */
    .news-empty-state {
        background: #ffffff;
        border: 2px dashed var(--news-slate-200);
        border-radius: 20px;
        padding: 60px 30px;
        text-align: center;
    }

    .news-empty-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: var(--news-blue-light);
        color: var(--news-blue);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin-bottom: 20px;
    }

    @media (max-width: 991px) {
        .news-hero-banner {
            padding: 130px 0 60px;
        }
        .news-hero-title {
            font-size: 30px;
        }
        .news-headline-img-wrap {
            min-height: 240px;
        }
        .news-headline-content {
            padding: 24px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- 1. HERO BANNER -->
<section class="news-hero-banner">
    <div class="container">
        <div class="news-breadcrumb">
            <a href="<?= site_url('/') ?>"><iconify-icon icon="solar:home-2-bold" class="align-middle"></iconify-icon> Beranda</a>
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
            <iconify-icon icon="solar:magnifer-linear" class="text-slate-400 text-xl"></iconify-icon>
            <input type="text" name="q" value="<?= esc($keyword ?? '') ?>" placeholder="Cari judul berita, topik kebijakan, atau kata kunci...">
            <?php if (! empty($selectedCategory)): ?>
                <input type="hidden" name="kategori" value="<?= esc($selectedCategory) ?>">
            <?php endif; ?>
            <button type="submit">
                <span>Cari Berita</span>
                <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
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
                    <div class="news-kpi-icon text-primary bg-primary-subtle">
                        <iconify-icon icon="solar:document-text-bold"></iconify-icon>
                    </div>
                    <div>
                        <div class="news-kpi-num"><?= number_format($stats['total_news'] > 0 ? $stats['total_news'] : 12) ?></div>
                        <div class="news-kpi-label">Berita Publikasi Resmi</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="news-kpi-item">
                    <div class="news-kpi-icon text-info bg-info-subtle">
                        <iconify-icon icon="solar:folder-with-files-bold"></iconify-icon>
                    </div>
                    <div>
                        <div class="news-kpi-num"><?= number_format($stats['total_categories'] > 0 ? $stats['total_categories'] : 6) ?></div>
                        <div class="news-kpi-label">Kategori Liputan PBJ</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="news-kpi-item">
                    <div class="news-kpi-icon text-warning bg-warning-subtle">
                        <iconify-icon icon="solar:eye-bold"></iconify-icon>
                    </div>
                    <div>
                        <div class="news-kpi-num"><?= number_format($stats['total_views'] > 0 ? $stats['total_views'] : 6800) ?>+</div>
                        <div class="news-kpi-label">Total Pembaca Terjangkau</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="news-kpi-item">
                    <div class="news-kpi-icon text-success bg-success-subtle">
                        <iconify-icon icon="solar:calendar-date-bold"></iconify-icon>
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
            <div class="alert alert-info d-flex align-items-center justify-content-between mb-4 radius-12">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:magnifer-linear" class="text-xl"></iconify-icon>
                    <span>Menampilkan hasil pencarian untuk: <strong>"<?= esc($keyword) ?>"</strong> (<?= $totalItems ?> berita ditemukan)</span>
                </div>
                <a href="<?= site_url('berita') ?>" class="btn btn-sm btn-outline-primary radius-8">Reset Pencarian</a>
            </div>
        <?php endif; ?>

        <!-- Headline Article Hero Card (Page 1 only without keyword) -->
        <?php if (! empty($headlineArticle)): ?>
            <div class="news-headline-card">
                <div class="row g-0 align-items-center">
                    <div class="col-lg-6">
                        <div class="news-headline-img-wrap">
                            <img src="<?= $headlineArticle['cover_url'] ?: base_url('assets/main/images/blog/blog-details-img-1.jpg') ?>" 
                                 alt="<?= esc($headlineArticle['title']) ?>" 
                                 class="news-headline-img">
                            <div class="news-badge-headline">
                                <iconify-icon icon="solar:star-fall-bold"></iconify-icon>
                                <span>Berita Utama</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="news-headline-content">
                            <span class="news-cat-badge"><?= esc($headlineArticle['category_name']) ?></span>
                            <h2 class="news-headline-title">
                                <a href="<?= site_url('berita/' . $headlineArticle['slug']) ?>">
                                    <?= esc($headlineArticle['title']) ?>
                                </a>
                            </h2>
                            <p class="news-headline-desc"><?= esc($headlineArticle['summary']) ?></p>

                            <div class="news-meta-row">
                                <div class="news-meta-item">
                                    <iconify-icon icon="solar:calendar-linear" class="text-primary"></iconify-icon>
                                    <span><?= esc($headlineArticle['date_formatted']) ?></span>
                                </div>
                                <div class="news-meta-item">
                                    <iconify-icon icon="solar:clock-circle-linear" class="text-warning"></iconify-icon>
                                    <span><?= esc($headlineArticle['read_time']) ?> baca</span>
                                </div>
                                <div class="news-meta-item">
                                    <iconify-icon icon="solar:eye-linear" class="text-info"></iconify-icon>
                                    <span><?= number_format($headlineArticle['view_count']) ?> views</span>
                                </div>
                            </div>

                            <div>
                                <a href="<?= site_url('berita/' . $headlineArticle['slug']) ?>" class="thm-btn">
                                    <span>Baca Berita Lengkap</span>
                                    <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Main Grid & Sidebar Layout -->
        <div class="row g-4">
            <!-- Left: News Grid -->
            <div class="col-lg-8">
                <?php if (empty($items) && empty($headlineArticle)): ?>
                    <div class="news-empty-state">
                        <div class="news-empty-icon">
                            <iconify-icon icon="solar:document-medicine-bold"></iconify-icon>
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
                                            <iconify-icon icon="solar:clock-circle-linear"></iconify-icon>
                                            <?= esc($item['read_time']) ?>
                                        </span>
                                    </div>
                                    <div class="news-card-body">
                                        <div class="news-card-date">
                                            <iconify-icon icon="solar:calendar-linear" class="text-primary"></iconify-icon>
                                            <span><?= esc($item['date_formatted']) ?></span>
                                            <span class="text-slate-300">&middot;</span>
                                            <span class="text-muted"><iconify-icon icon="solar:eye-linear" class="align-middle"></iconify-icon> <?= number_format($item['view_count']) ?></span>
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
                                                <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
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
                                <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
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
                                <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
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
                        <iconify-icon icon="solar:magnifer-bold" class="text-primary"></iconify-icon>
                        <span>Cari Artikel Berita</span>
                    </h4>
                    <form action="<?= site_url('berita') ?>" method="get">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control radius-8 border" placeholder="Kata kunci pengadaan..." value="<?= esc($keyword ?? '') ?>">
                            <button class="btn btn-primary px-3 radius-8" type="submit">
                                <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Category Directory Widget -->
                <div class="news-sidebar-widget">
                    <h4 class="news-widget-title">
                        <iconify-icon icon="solar:folder-with-files-bold" class="text-warning"></iconify-icon>
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
                        <iconify-icon icon="solar:fire-bold" class="text-danger"></iconify-icon>
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
                                    <span><iconify-icon icon="solar:eye-linear"></iconify-icon> <?= number_format($pop['view_count']) ?> views</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Official Bulletin / Biro PBJ Notice -->
                <div class="news-official-banner mb-4">
                    <span class="badge bg-warning text-dark px-3 py-1 radius-6 text-xs fw-bold mb-3 text-uppercase">Kanal Resmi</span>
                    <h4 class="text-lg fw-bold mb-2 text-white">Biro Pengadaan Barang &amp; Jasa Setda Provinsi Lampung</h4>
                    <p class="text-xs text-slate-300 mb-3" style="line-height: 1.6;">
                        Seluruh siaran pers, klarifikasi, dan pengumuman resmi dipublikasikan melalui kanal BegawiPBJ untuk mendukung keterbukaan informasi publik dan akuntabilitas PBJ.
                    </p>
                    <div class="d-flex align-items-center gap-2 pt-2 border-top border-white-10">
                        <iconify-icon icon="solar:phone-calling-bold" class="text-warning text-lg"></iconify-icon>
                        <span class="text-xs text-white">Helpdesk: +62 851-1777-9783</span>
                    </div>
                </div>

                <!-- Quick Tags Widget -->
                <div class="news-sidebar-widget">
                    <h4 class="news-widget-title">
                        <iconify-icon icon="solar:tag-bold" class="text-info"></iconify-icon>
                        <span>Topik Populer</span>
                    </h4>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= site_url('berita?q=P3DN') ?>" class="badge bg-light text-dark p-2 radius-6 text-decoration-none border">#P3DN</a>
                        <a href="<?= site_url('berita?q=Katalog') ?>" class="badge bg-light text-dark p-2 radius-6 text-decoration-none border">#E-Katalog</a>
                        <a href="<?= site_url('berita?q=Integritas') ?>" class="badge bg-light text-dark p-2 radius-6 text-decoration-none border">#Integritas</a>
                        <a href="<?= site_url('berita?q=Sertifikasi') ?>" class="badge bg-light text-dark p-2 radius-6 text-decoration-none border">#Sertifikasi Level-1</a>
                        <a href="<?= site_url('berita?q=Maturitas') ?>" class="badge bg-light text-dark p-2 radius-6 text-decoration-none border">#UKPBJ Proaktif</a>
                        <a href="<?= site_url('berita?q=Probity') ?>" class="badge bg-light text-dark p-2 radius-6 text-decoration-none border">#Probity Audit</a>
                        <a href="<?= site_url('berita?q=UMKM') ?>" class="badge bg-light text-dark p-2 radius-6 text-decoration-none border">#UMKM Lokal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
