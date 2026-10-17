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

    /* Reading Progress Bar */
    #readingProgressBar {
        position: fixed;
        top: 0;
        left: 0;
        height: 4px;
        background: linear-gradient(90deg, #0284c7 0%, #38bdf8 50%, #f59e0b 100%);
        z-index: 99999;
        width: 0%;
        transition: width 0.1s linear;
    }

    /* Detail Header Banner */
    .news-detail-header-section {
        background: linear-gradient(135deg, var(--news-navy-dark) 0%, var(--news-navy) 70%, var(--news-blue) 100%);
        padding: 175px 0 65px;
        color: #ffffff;
        position: relative;
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

    .news-detail-cat-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f59e0b;
        color: #052244;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 6px 14px;
        border-radius: 8px;
        margin-bottom: 16px;
    }

    .news-detail-title {
        font-size: 38px;
        font-weight: 800;
        line-height: 1.28;
        color: #ffffff;
        margin-bottom: 24px;
        letter-spacing: -0.4px;
    }

    .news-detail-meta-bar {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        font-size: 13.5px;
        color: #f1f5f9;
        font-weight: 600;
        padding-top: 18px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }

    .news-detail-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .news-detail-author-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #0284c7;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
        border: 1px solid rgba(255, 255, 255, 0.4);
    }

    /* Article Content Container */
    .news-article-wrap {
        background: #ffffff;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
        margin-top: -30px;
        position: relative;
        z-index: 5;
    }

    /* Accessibility Toolbar */
    .news-reading-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        padding-bottom: 20px;
        margin-bottom: 24px;
        border-bottom: 1px solid #f1f5f9;
    }

    .news-font-control-group {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 4px 8px;
        border-radius: 8px;
    }

    .news-font-btn {
        background: transparent;
        border: none;
        color: #334155;
        font-weight: 700;
        font-size: 12.5px;
        padding: 2px 8px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .news-font-btn:hover,
    .news-font-btn.active {
        background: #0284c7;
        color: #ffffff;
    }

    .news-featured-img-wrap {
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 30px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        background: #f1f5f9;
    }

    .news-featured-img {
        width: 100%;
        max-height: 480px;
        object-fit: cover;
        display: block;
    }

    .news-img-caption {
        padding: 10px 16px;
        font-size: 12.5px;
        color: #64748b;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        font-style: italic;
    }

    .news-lead-summary-box {
        background: #f0f9ff;
        border-left: 4px solid #0284c7;
        border-radius: 0 12px 12px 0;
        padding: 22px 26px;
        font-size: 16.5px;
        line-height: 1.7;
        color: #0c4a6e;
        font-weight: 500;
        margin-bottom: 32px;
    }

    .news-article-body {
        font-size: 16.5px;
        line-height: 1.85;
        color: #1e293b;
        margin-bottom: 36px;
        transition: font-size 0.2s ease;
    }

    .news-article-body p {
        margin-bottom: 1.4rem;
    }

    .news-article-body h2,
    .news-article-body h3,
    .news-article-body h4 {
        color: var(--news-slate-900);
        font-weight: 800;
        margin-top: 2rem;
        margin-bottom: 1rem;
        line-height: 1.35;
    }

    .news-article-body ul,
    .news-article-body ol {
        margin-bottom: 1.4rem;
        padding-left: 24px;
    }

    .news-article-body li {
        margin-bottom: 0.5rem;
    }

    .news-article-body blockquote {
        background: #f8fafc;
        border-left: 4px solid #f59e0b;
        padding: 18px 24px;
        margin: 1.8rem 0;
        font-style: italic;
        color: #334155;
        border-radius: 0 10px 10px 0;
    }

    /* Key Takeaways Callout */
    .news-keypoints-card {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 14px;
        padding: 22px 26px;
        margin-bottom: 34px;
    }

    .news-keypoints-title {
        font-size: 15px;
        font-weight: 800;
        color: #92400e;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
    }

    /* Social Share Bar */
    .news-share-bar {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 34px;
    }

    .news-share-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--news-slate-900);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .news-share-btn-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .news-share-btn {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: #ffffff;
        text-decoration: none;
        transition: transform 0.2s ease, opacity 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .news-share-btn:hover {
        transform: translateY(-2px);
        opacity: 0.9;
        color: #ffffff;
    }

    .news-share-wa {
        background: #25D366;
    }

    .news-share-tg {
        background: #0088cc;
    }

    .news-share-tw {
        background: #000000;
    }

    .news-share-fb {
        background: #1877f2;
    }

    .news-share-in {
        background: #0077b5;
    }

    .news-share-copy {
        background: #475569;
    }

    /* Tags Bar */
    .news-tags-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        padding-bottom: 28px;
        margin-bottom: 28px;
        border-bottom: 1px solid #f1f5f9;
    }

    .news-tag-pill {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
        font-size: 12.5px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 999px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .news-tag-pill:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    /* Redaksi / Biro PBJ Identity Box */
    .news-author-box {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 24px;
        display: flex;
        gap: 20px;
        align-items: center;
        margin-bottom: 34px;
    }

    .news-author-large-avatar {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        background: #0284c7;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
    }

    .news-author-box-name {
        font-size: 17px;
        font-weight: 800;
        color: var(--news-slate-900);
        margin-bottom: 6px;
    }

    .news-author-box-desc {
        font-size: 13.5px;
        color: #475569;
        line-height: 1.6;
        margin-bottom: 0;
    }

    /* Prev & Next Article Navigation */
    .news-nav-row {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
    }

    .news-nav-card {
        flex: 1;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px 22px;
        text-decoration: none;
        transition: border-color 0.2s ease, transform 0.2s ease;
        display: flex;
        flex-direction: column;
    }

    .news-nav-card:hover {
        border-color: #0284c7;
        transform: translateY(-2px);
    }

    .news-nav-card-label {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        color: #0284c7;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .news-nav-card-title {
        font-size: 14.5px;
        font-weight: 700;
        color: var(--news-slate-900);
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Related News Cards */
    .related-news-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .related-news-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.08);
    }

    .related-news-thumb {
        height: 150px;
        width: 100%;
        object-fit: cover;
    }

    .related-news-body {
        padding: 18px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .related-news-title {
        font-size: 15px;
        font-weight: 700;
        line-height: 1.4;
        margin-bottom: 12px;
    }

    .related-news-title a {
        color: var(--news-slate-900);
        text-decoration: none;
    }

    .related-news-title a:hover {
        color: #0284c7;
    }

    /* Sidebar Widgets */
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

    /* Copy Toast */
    #copyToast {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: #0f172a;
        color: #ffffff;
        padding: 12px 22px;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 600;
        display: none;
        align-items: center;
        gap: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
        z-index: 9999;
    }

    @media (max-width: 991.98px) {
        .news-detail-header-section {
            padding: 150px 0 50px;
        }

        .news-detail-title {
            font-size: 28px;
        }

        .news-article-wrap {
            padding: 24px;
            margin-top: -20px;
        }

        .news-nav-row {
            flex-direction: column;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Sticky Reading Progress Bar -->
<div id="readingProgressBar"></div>

<!-- 1. DETAIL HEADER BANNER -->
<section class="news-detail-header-section">
    <div class="container">
        <div class="news-breadcrumb">
            <a href="<?= site_url('/') ?>"><i class="fas fa-home me-1"></i> Beranda</a>
            <span>/</span>
            <a href="<?= site_url('berita') ?>">Berita PBJ</a>
            <?php if (! empty($news['category_name'])): ?>
                <span>/</span>
                <a href="<?= site_url('berita/kategori/' . ($news['category_slug'] ?? 'umum')) ?>">
                    <?= esc($news['category_name']) ?>
                </a>
            <?php endif; ?>
        </div>

        <div class="news-detail-cat-badge">
            <i class="fas fa-folder-open me-1"></i>
            <span><?= esc($news['category_name'] ?? 'Berita PBJ') ?></span>
        </div>

        <h1 class="news-detail-title">
            <?= esc($news['title']) ?>
        </h1>

        <div class="news-detail-meta-bar">
            <div class="news-detail-meta-item">
                <span class="news-detail-author-avatar">B</span>
                <strong><?= esc($news['author_name'] ?? 'Biro PBJ Setda Lampung') ?></strong>
            </div>
            <div class="news-detail-meta-item">
                <i class="far fa-calendar-alt text-warning me-1"></i>
                <span><?= date('d F Y', strtotime($news['published_at'] ?? $news['created_at'])) ?></span>
            </div>
            <div class="news-detail-meta-item">
                <i class="far fa-clock text-info me-1"></i>
                <span><?= esc($readTime) ?> baca</span>
            </div>
            <div class="news-detail-meta-item">
                <i class="far fa-eye text-success me-1"></i>
                <span><?= number_format($news['view_count']) ?> views</span>
            </div>
            <div class="news-detail-meta-item ms-lg-auto">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 radius-6 fw-bold">
                    <i class="fas fa-check-circle me-1"></i> Publikasi Resmi
                </span>
            </div>
        </div>
    </div>
</section>

<!-- 2. MAIN DETAIL CONTENT & SIDEBAR -->
<section class="pb-80">
    <div class="container">
        <div class="row g-4">
            <!-- Left: Main Article -->
            <div class="col-lg-8">
                <article class="news-article-wrap">
                    <!-- Reading Accessibility Toolbar -->
                    <div class="news-reading-toolbar">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-xs fw-bold text-muted text-uppercase">Ukuran Teks:</span>
                            <div class="news-font-control-group">
                                <button type="button" class="news-font-btn" onclick="setArticleFontSize('small')" title="Teks Lebih Kecil">A-</button>
                                <button type="button" class="news-font-btn active" id="btnFontNormal" onclick="setArticleFontSize('normal')" title="Teks Standar">A</button>
                                <button type="button" class="news-font-btn" onclick="setArticleFontSize('large')" title="Teks Lebih Besar">A+</button>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" onclick="window.print()" class="btn btn-sm btn-light border radius-8 fw-semibold text-xs d-inline-flex align-items-center gap-1 text-slate-700">
                                <i class="fas fa-print text-primary"></i>
                                <span>Cetak</span>
                            </button>
                            <button type="button" id="btnTopCopyLink" class="btn btn-sm btn-light border radius-8 fw-semibold text-xs d-inline-flex align-items-center gap-1 text-slate-700">
                                <i class="fas fa-link text-info"></i>
                                <span>Salin Tautan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <div class="news-featured-img-wrap">
                        <img src="<?= ! empty($news['cover_path']) ? base_url($news['cover_path']) : base_url('assets/main/images/blog/blog-details-img-1.jpg') ?>"
                            alt="<?= esc($news['title']) ?>"
                            class="news-featured-img">
                        <div class="news-img-caption">
                            <i class="fas fa-camera me-1"></i> Foto: Dokumentasi Humas Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung
                        </div>
                    </div>

                    <!-- Lead Executive Summary -->
                    <?php if (! empty($news['summary'])): ?>
                        <div class="news-lead-summary-box">
                            <i class="fas fa-quote-left text-primary me-2"></i>
                            <?= esc($news['summary']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Body Content -->
                    <div class="news-article-body" id="articleBodyContent">
                        <?php if (! empty($news['content'])): ?>
                            <?php
                            // Format double newlines into clean paragraphs if plain text
                            $paragraphs = explode("\n\n", trim($news['content']));
                            foreach ($paragraphs as $para) {
                                $trimmed = trim($para);
                                if (empty($trimmed)) continue;

                                // Check if bullet point list
                                if (str_starts_with($trimmed, '•') || str_starts_with($trimmed, '-') || preg_match('/^\d+\./', $trimmed)) {
                                    echo '<ul>';
                                    $lines = explode("\n", $trimmed);
                                    foreach ($lines as $line) {
                                        $cleanLine = preg_replace('/^[•\-\d\.]+\s*/', '', trim($line));
                                        if (! empty($cleanLine)) {
                                            echo '<li>' . esc($cleanLine) . '</li>';
                                        }
                                    }
                                    echo '</ul>';
                                } elseif (str_starts_with($trimmed, '"') && str_ends_with($trimmed, '"')) {
                                    // Blockquote
                                    echo '<blockquote>' . esc($trimmed) . '</blockquote>';
                                } else {
                                    echo '<p>' . nl2br(esc($trimmed)) . '</p>';
                                }
                            }
                            ?>
                        <?php else: ?>
                            <p>Artikel informasi resmi dipublikasikan oleh Biro Pengadaan Barang dan Jasa Pemerintah Provinsi Lampung.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Key Takeaways Callout Box -->
                    <div class="news-keypoints-card">
                        <div class="news-keypoints-title">
                            <i class="fas fa-shield-alt text-primary me-2"></i>
                            <span>Poin Penting untuk Pemangku Kepentingan:</span>
                        </div>
                        <ul class="mb-0 text-sm text-slate-700" style="padding-left: 20px;">
                            <li class="mb-1">Seluruh proses pengadaan di lingkungan Pemprov Lampung wajib mematuhi ketentuan Perpres Nomor 12 Tahun 2021 dan prinsip etika pengadaan.</li>
                            <li class="mb-1">Prioritaskan penggunaan produk dalam negeri (P3DN) bersertifikat TKDN minimal 25% melalui e-katalog lokal.</li>
                            <li>Informasi pendampingan dan helpdesk teknis SPSE dapat diakses melalui portal resmi BegawiPengadaan.</li>
                        </ul>
                    </div>

                    <!-- Social Share Bar -->
                    <div class="news-share-bar">
                        <div class="news-share-title">
                            <i class="fas fa-share-alt text-primary me-2"></i>
                            <span>Bagikan Informasi Ini:</span>
                        </div>
                        <div class="news-share-btn-group">
                            <a href="https://api.whatsapp.com/send?text=<?= urlencode($news['title'] . ' ' . $shareUrl) ?>"
                                target="_blank" class="news-share-btn news-share-wa" title="Bagikan ke WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <a href="https://telegram.me/share/url?url=<?= urlencode($shareUrl) ?>&text=<?= urlencode($news['title']) ?>"
                                target="_blank" class="news-share-btn news-share-tg" title="Bagikan ke Telegram">
                                <i class="fab fa-telegram-plane"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?text=<?= urlencode($news['title']) ?>&url=<?= urlencode($shareUrl) ?>"
                                target="_blank" class="news-share-btn news-share-tw" title="Bagikan ke X (Twitter)">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($shareUrl) ?>"
                                target="_blank" class="news-share-btn news-share-fb" title="Bagikan ke Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($shareUrl) ?>"
                                target="_blank" class="news-share-btn news-share-in" title="Bagikan ke LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <button type="button" class="news-share-btn news-share-copy" id="btnCopyLink" title="Salin Tautan">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tags -->
                    <div class="news-tags-bar">
                        <span class="text-xs fw-bold text-muted text-uppercase me-2">Topik Terkait:</span>
                        <a href="<?= site_url('berita?q=PBJ') ?>" class="news-tag-pill">#PengadaanBarangJasa</a>
                        <a href="<?= site_url('berita?q=Pemprov') ?>" class="news-tag-pill">#PemprovLampung</a>
                        <a href="<?= site_url('berita?q=UKPBJ') ?>" class="news-tag-pill">#UKPBJ</a>
                        <a href="<?= site_url('berita?q=E-Katalog') ?>" class="news-tag-pill">#EKatalog</a>
                        <a href="<?= site_url('berita?q=Akuntabilitas') ?>" class="news-tag-pill">#AkuntabilitasPublik</a>
                    </div>

                    <!-- Redaksi / Biro PBJ Identity Box -->
                    <div class="news-author-box">
                        <div class="news-author-large-avatar">
                            <i class="fas fa-landmark"></i>
                        </div>
                        <div>
                            <h4 class="news-author-box-name">Biro Pengadaan Barang &amp; Jasa Sekretariat Daerah Provinsi Lampung</h4>
                            <p class="news-author-box-desc">
                                Unit Kerja Pengadaan Barang/Jasa (UKPBJ) Pembina Pemerintah Provinsi Lampung yang bertugas mengoordinasikan perumusan kebijakan, pendampingan hukum, dan transformasi pengadaan digital modern di Provinsi Lampung.
                            </p>
                        </div>
                    </div>

                    <!-- Previous & Next Article Navigation -->
                    <div class="news-nav-row">
                        <?php if (! empty($prevNews)): ?>
                            <a href="<?= site_url('berita/' . $prevNews['slug']) ?>" class="news-nav-card">
                                <div class="news-nav-card-label">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    <span>Berita Sebelumnya</span>
                                </div>
                                <div class="news-nav-card-title"><?= esc($prevNews['title']) ?></div>
                            </a>
                        <?php endif; ?>

                        <?php if (! empty($nextNews)): ?>
                            <a href="<?= site_url('berita/' . $nextNews['slug']) ?>" class="news-nav-card text-end">
                                <div class="news-nav-card-label justify-content-end">
                                    <span>Berita Selanjutnya</span>
                                    <i class="fas fa-arrow-right ms-1"></i>
                                </div>
                                <div class="news-nav-card-title"><?= esc($nextNews['title']) ?></div>
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Back to Catalog Button -->
                    <div class="text-center pt-3 border-top">
                        <a href="<?= site_url('berita') ?>" class="btn btn-outline-primary px-4 py-2 radius-10 d-inline-flex align-items-center gap-2 fw-semibold">
                            <i class="fas fa-arrow-left"></i>
                            <span>Kembali ke Indeks Berita</span>
                        </a>
                    </div>
                </article>

                <!-- Related News Carousel/Grid -->
                <?php if (! empty($relatedNews)): ?>
                    <div class="mt-5">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <span class="badge bg-primary-subtle text-primary px-3 py-1 radius-6 text-xs fw-bold text-uppercase mb-1">Rekomendasi</span>
                                <h3 class="h4 fw-bold text-dark mb-0">Berita Terkait Lainnya</h3>
                            </div>
                            <a href="<?= site_url('berita') ?>" class="text-sm fw-bold text-primary text-decoration-none">
                                Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="row g-4">
                            <?php foreach ($relatedNews as $idx => $rel): ?>
                                <?php
                                $sampleRelImgs = [
                                    base_url('assets/main/images/blog/blog-list-1-1.jpg'),
                                    base_url('assets/main/images/blog/blog-list-1-3.jpg'),
                                    base_url('assets/main/images/blog/blog-1-1.jpg'),
                                ];
                                $relThumb = ! empty($rel['cover_path']) ? base_url($rel['cover_path']) : $sampleRelImgs[$idx % count($sampleRelImgs)];
                                ?>
                                <div class="col-md-4">
                                    <article class="related-news-card">
                                        <img src="<?= $relThumb ?>" alt="<?= esc($rel['title']) ?>" class="related-news-thumb">
                                        <div class="related-news-body">
                                            <span class="text-xs text-muted mb-1"><?= date('d M Y', strtotime($rel['published_at'])) ?></span>
                                            <h4 class="related-news-title">
                                                <a href="<?= site_url('berita/' . $rel['slug']) ?>"><?= esc($rel['title']) ?></a>
                                            </h4>
                                            <a href="<?= site_url('berita/' . $rel['slug']) ?>" class="text-xs fw-bold text-primary text-decoration-none mt-auto">
                                                Baca Selengkapnya &rarr;
                                            </a>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right: Sidebar -->
            <div class="col-lg-4">
                <!-- Share & Actions Box -->
                <div class="news-sidebar-widget text-center">
                    <h4 class="news-widget-title justify-content-center">
                        <i class="fas fa-print text-primary me-2"></i>
                        <span>Aksi Dokumen</span>
                    </h4>
                    <p class="text-xs text-slate-500 mb-3" style="color: #64748b;">Cetak artikel ini untuk arsip fisik atau lampiran bahan rapat:</p>
                    <button type="button" onclick="window.print()" class="btn btn-outline-secondary w-100 py-2 radius-10 d-inline-flex align-items-center justify-content-center gap-2 mb-2 fw-semibold">
                        <i class="fas fa-print"></i>
                        <span>Cetak Halaman (Print)</span>
                    </button>
                </div>

                <!-- Popular News Widget -->
                <div class="news-sidebar-widget">
                    <h4 class="news-widget-title">
                        <i class="fas fa-fire text-danger me-2"></i>
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

                <!-- Categories Directory Widget -->
                <div class="news-sidebar-widget">
                    <h4 class="news-widget-title">
                        <i class="fas fa-folder-open text-warning me-2"></i>
                        <span>Kategori Liputan</span>
                    </h4>
                    <ul class="news-widget-categories" style="list-style: none; padding: 0; margin: 0;">
                        <?php foreach ($categories as $cat): ?>
                            <li class="mb-2">
                                <a href="<?= site_url('berita/kategori/' . $cat['slug']) ?>"
                                    class="d-flex align-items-center justify-content-between p-2 radius-8 text-decoration-none <?= ($news['category_slug'] ?? '') === $cat['slug'] ? 'bg-primary-subtle text-primary fw-bold' : 'text-slate-700 bg-light' ?>">
                                    <span><?= esc($cat['name']) ?></span>
                                    <span class="badge bg-white text-muted border"><?= $cat['news_count'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Ecosystem Navigation Banner -->
                <div class="p-4 bg-light border radius-18 mb-4">
                    <h5 class="fw-bold text-dark mb-2">Jelajahi Ekosistem BegawiPengadaan</h5>
                    <p class="text-xs text-slate-500 mb-3" style="color: #64748b;">Tingkatkan pemahaman dan kapabilitas Anda melalui kanal lainnya:</p>
                    <div class="d-grid gap-2">
                        <a href="<?= site_url('regulasi') ?>" class="btn btn-sm btn-white border d-flex align-items-center justify-content-between p-2 radius-8 text-decoration-none text-dark fw-semibold" style="background:#ffffff;">
                            <span><i class="fas fa-balance-scale text-warning me-2"></i> Pusat Regulasi PBJ</span>
                            <i class="fas fa-arrow-right text-muted"></i>
                        </a>
                        <a href="<?= site_url('arena-belajar') ?>" class="btn btn-sm btn-white border d-flex align-items-center justify-content-between p-2 radius-8 text-decoration-none text-dark fw-semibold" style="background:#ffffff;">
                            <span><i class="fas fa-graduation-cap text-info me-2"></i> Arena Belajar &amp; Sertifikasi</span>
                            <i class="fas fa-arrow-right text-muted"></i>
                        </a>
                        <a href="<?= site_url('bunga-rampai') ?>" class="btn btn-sm btn-white border d-flex align-items-center justify-content-between p-2 radius-8 text-decoration-none text-dark fw-semibold" style="background:#ffffff;">
                            <span><i class="fas fa-book-reader text-success me-2"></i> Bunga Rampai &amp; Template</span>
                            <i class="fas fa-arrow-right text-muted"></i>
                        </a>
                        <a href="<?= site_url('event') ?>" class="btn btn-sm btn-white border d-flex align-items-center justify-content-between p-2 radius-8 text-decoration-none text-dark fw-semibold" style="background:#ffffff;">
                            <span><i class="fas fa-calendar-star text-primary me-2"></i> Agenda &amp; Event PBJ</span>
                            <i class="fas fa-arrow-right text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Copy Link Toast Feedback -->
<div id="copyToast">
    <i class="fas fa-check-circle text-success text-xl"></i>
    <span>Tautan berita berhasil disalin ke clipboard!</span>
</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('assets/dashboard/js/lib/iconify-icon.min.js') ?>"></script>
<script>
    // Reading Progress Bar
    window.addEventListener('scroll', function() {
        const docElem = document.documentElement;
        const docBody = document.body;
        const scrollTop = docElem['scrollTop'] || docBody['scrollTop'];
        const scrollBottom = (docElem['scrollHeight'] || docBody['scrollHeight']) - window.innerHeight;
        const scrollPercent = scrollBottom > 0 ? (scrollTop / scrollBottom) * 100 : 0;
        const bar = document.getElementById('readingProgressBar');
        if (bar) {
            bar.style.width = scrollPercent + '%';
        }
    });

    // Font Size Adjuster
    function setArticleFontSize(size) {
        const bodyContent = document.getElementById('articleBodyContent');
        if (!bodyContent) return;
        
        document.querySelectorAll('.news-font-btn').forEach(btn => btn.classList.remove('active'));
        
        if (size === 'small') {
            bodyContent.style.fontSize = '14.5px';
            event.target.classList.add('active');
        } else if (size === 'large') {
            bodyContent.style.fontSize = '18.5px';
            event.target.classList.add('active');
        } else {
            bodyContent.style.fontSize = '16.5px';
            const normalBtn = document.getElementById('btnFontNormal');
            if (normalBtn) normalBtn.classList.add('active');
        }
    }

    // Copy Link functionality
    document.addEventListener('DOMContentLoaded', function() {
        const btnCopy = document.getElementById('btnCopyLink');
        const btnTopCopy = document.getElementById('btnTopCopyLink');
        const toast = document.getElementById('copyToast');

        function triggerCopy() {
            const url = '<?= $shareUrl ?>';
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(function() {
                    showToast();
                }).catch(function() {
                    prompt('Salin tautan ini:', url);
                });
            } else {
                prompt('Salin tautan ini:', url);
            }
        }

        function showToast() {
            if (toast) {
                toast.style.display = 'flex';
                setTimeout(function() {
                    toast.style.display = 'none';
                }, 3000);
            }
        }

        if (btnCopy) btnCopy.addEventListener('click', triggerCopy);
        if (btnTopCopy) btnTopCopy.addEventListener('click', triggerCopy);
    });
</script>
<?= $this->endSection() ?>