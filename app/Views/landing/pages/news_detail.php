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

    /* Detail Header Banner */
    .news-detail-header-section {
        background: linear-gradient(135deg, var(--news-navy-dark) 0%, var(--news-navy) 70%, var(--news-blue) 100%);
        padding: 160px 0 60px;
        color: #ffffff;
        position: relative;
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
        margin-bottom: 20px;
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

    .news-detail-cat-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--news-gold);
        color: var(--news-navy-dark);
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 5px 14px;
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
        color: #cbd5e1;
        padding-top: 18px;
        border-top: 1px solid rgba(255, 255, 255, 0.15);
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
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    /* Main Article Container */
    .news-article-wrap {
        background: #ffffff;
        border: 1px solid var(--news-slate-200);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(8, 47, 92, 0.05);
        margin-top: -30px;
        position: relative;
        z-index: 10;
        margin-bottom: 40px;
    }

    .news-featured-img-wrap {
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 30px;
        background: #f1f5f9;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .news-featured-img {
        width: 100%;
        max-height: 480px;
        object-fit: cover;
    }

    .news-img-caption {
        font-size: 12.5px;
        color: var(--news-slate-500);
        font-style: italic;
        padding: 10px 14px 4px;
        text-align: center;
    }

    .news-lead-summary-box {
        background: #f0f7ff;
        border-left: 4px solid var(--news-blue);
        border-radius: 0 14px 14px 0;
        padding: 22px 26px;
        margin-bottom: 32px;
        font-size: 17px;
        font-weight: 600;
        line-height: 1.65;
        color: var(--news-navy-dark);
    }

    .news-article-body {
        font-size: 16px;
        line-height: 1.85;
        color: #334155;
    }

    .news-article-body p {
        margin-bottom: 22px;
    }

    .news-article-body h2,
    .news-article-body h3,
    .news-article-body h4 {
        color: var(--news-navy-dark);
        font-weight: 800;
        margin-top: 36px;
        margin-bottom: 16px;
        line-height: 1.35;
    }

    .news-article-body h2 { font-size: 24px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; }
    .news-article-body h3 { font-size: 20px; }

    .news-article-body blockquote {
        background: #f8fafc;
        border-left: 4px solid var(--news-gold);
        border-radius: 0 14px 14px 0;
        padding: 22px 28px;
        margin: 32px 0;
        font-style: italic;
        color: var(--news-navy-dark);
        font-size: 16.5px;
        line-height: 1.7;
        position: relative;
    }

    .news-article-body blockquote::before {
        content: '"';
        font-size: 60px;
        line-height: 1;
        position: absolute;
        top: 10px;
        left: 10px;
        opacity: 0.15;
        font-family: Georgia, serif;
    }

    .news-article-body ul,
    .news-article-body ol {
        margin-bottom: 24px;
        padding-left: 24px;
    }

    .news-article-body li {
        margin-bottom: 10px;
        line-height: 1.75;
    }

    /* Key Takeaways Card */
    .news-keypoints-card {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid #bbf7d0;
        border-radius: 16px;
        padding: 24px;
        margin: 36px 0;
    }

    .news-keypoints-title {
        font-size: 16px;
        font-weight: 800;
        color: #166534;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
    }

    /* Share Widget */
    .news-share-bar {
        background: #f8fafc;
        border: 1px solid var(--news-slate-200);
        border-radius: 14px;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin: 36px 0;
    }

    .news-share-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--news-navy-dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .news-share-btn-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .news-share-btn {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 18px;
        text-decoration: none;
        transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .news-share-btn:hover {
        transform: translateY(-2px);
        color: #ffffff;
        opacity: 0.9;
    }

    .news-share-wa { background: #25d366; }
    .news-share-tg { background: #0088cc; }
    .news-share-tw { background: #000000; }
    .news-share-fb { background: #1877f2; }
    .news-share-in { background: #0077b5; }
    .news-share-copy { background: #475569; cursor: pointer; border: none; }

    /* Tags Cloud */
    .news-tags-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        padding: 18px 0;
        border-top: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 36px;
    }

    .news-tag-pill {
        background: #f1f5f9;
        color: #475569;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .news-tag-pill:hover {
        background: var(--news-blue-light);
        color: var(--news-blue);
    }

    /* Redaksi / Author Box */
    .news-author-box {
        background: #f8fafc;
        border: 1px solid var(--news-slate-200);
        border-radius: 16px;
        padding: 26px;
        display: flex;
        gap: 20px;
        align-items: center;
        margin-bottom: 40px;
    }

    .news-author-large-avatar {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--news-navy-dark), var(--news-blue));
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        flex-shrink: 0;
    }

    .news-author-box-name {
        font-size: 16px;
        font-weight: 800;
        color: var(--news-navy-dark);
        margin-bottom: 4px;
    }

    .news-author-box-desc {
        font-size: 13px;
        color: var(--news-slate-500);
        line-height: 1.6;
        margin: 0;
    }

    /* Prev / Next Nav Cards */
    .news-nav-row {
        display: flex;
        gap: 16px;
        margin-bottom: 48px;
    }

    .news-nav-card {
        flex: 1;
        background: #ffffff;
        border: 1px solid var(--news-slate-200);
        border-radius: 14px;
        padding: 18px 22px;
        text-decoration: none;
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
    }

    .news-nav-card:hover {
        border-color: var(--news-blue);
        box-shadow: 0 10px 24px rgba(8, 47, 92, 0.08);
        transform: translateY(-2px);
    }

    .news-nav-card-label {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--news-slate-500);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .news-nav-card-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--news-navy-dark);
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
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

    /* Related News Card */
    .related-news-card {
        background: #ffffff;
        border: 1px solid var(--news-slate-200);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .related-news-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 28px rgba(8, 47, 92, 0.08);
        border-color: #cbd5e1;
    }

    .related-news-thumb {
        height: 160px;
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
        color: var(--news-navy-dark);
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .related-news-title a {
        color: inherit;
        text-decoration: none;
    }

    .related-news-title a:hover {
        color: var(--news-blue);
    }

    /* Toast Notification for Copy Link */
    #copyToast {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: var(--news-navy-dark);
        color: #ffffff;
        padding: 12px 22px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        display: none;
        z-index: 99999;
        align-items: center;
        gap: 8px;
    }

    @media (max-width: 991px) {
        .news-detail-header-section {
            padding: 130px 0 50px;
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

<!-- 1. DETAIL HEADER BANNER -->
<section class="news-detail-header-section">
    <div class="container">
        <div class="news-breadcrumb">
            <a href="<?= site_url('/') ?>"><iconify-icon icon="solar:home-2-bold" class="align-middle"></iconify-icon> Beranda</a>
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
            <iconify-icon icon="solar:folder-with-files-bold"></iconify-icon>
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
                <iconify-icon icon="solar:calendar-date-bold" class="text-warning"></iconify-icon>
                <span><?= date('d F Y', strtotime($news['published_at'] ?? $news['created_at'])) ?></span>
            </div>
            <div class="news-detail-meta-item">
                <iconify-icon icon="solar:clock-circle-bold" class="text-info"></iconify-icon>
                <span><?= esc($readTime) ?> baca</span>
            </div>
            <div class="news-detail-meta-item">
                <iconify-icon icon="solar:eye-bold" class="text-success"></iconify-icon>
                <span><?= number_format($news['view_count']) ?> views</span>
            </div>
            <div class="news-detail-meta-item ms-lg-auto">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 radius-6">
                    <iconify-icon icon="solar:check-circle-bold" class="align-middle"></iconify-icon> Publikasi Resmi
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
                    <!-- Featured Image -->
                    <div class="news-featured-img-wrap">
                        <img src="<?= ! empty($news['cover_path']) ? base_url($news['cover_path']) : base_url('assets/main/images/blog/blog-details-img-1.jpg') ?>" 
                             alt="<?= esc($news['title']) ?>" 
                             class="news-featured-img">
                        <div class="news-img-caption">
                            Foto: Dokumentasi Humas Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung
                        </div>
                    </div>

                    <!-- Lead Executive Summary -->
                    <?php if (! empty($news['summary'])): ?>
                        <div class="news-lead-summary-box">
                            <?= esc($news['summary']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Body Content -->
                    <div class="news-article-body">
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
                            <iconify-icon icon="solar:shield-check-bold" class="text-xl"></iconify-icon>
                            <span>Poin Penting untuk Pemangku Kepentingan:</span>
                        </div>
                        <ul class="mb-0 text-sm text-slate-700" style="padding-left: 20px;">
                            <li class="mb-1">Seluruh proses pengadaan di lingkungan Pemprov Lampung wajib mematuhi ketentuan Perpres Nomor 12 Tahun 2021 dan prinsip etika pengadaan.</li>
                            <li class="mb-1">Prioritaskan penggunaan produk dalam negeri (P3DN) bersertifikat TKDN minimal 25% melalui e-katalog lokal.</li>
                            <li>Informasi pendampingan dan helpdesk teknis SPSE dapat diakses melalui portal resmi BegawiPBJ.</li>
                        </ul>
                    </div>

                    <!-- Social Share Bar -->
                    <div class="news-share-bar">
                        <div class="news-share-title">
                            <iconify-icon icon="solar:share-circle-bold" class="text-primary text-xl"></iconify-icon>
                            <span>Bagikan Informasi Ini:</span>
                        </div>
                        <div class="news-share-btn-group">
                            <a href="https://api.whatsapp.com/send?text=<?= urlencode($news['title'] . ' ' . $shareUrl) ?>" 
                               target="_blank" class="news-share-btn news-share-wa" title="Bagikan ke WhatsApp">
                                <iconify-icon icon="logos:whatsapp-icon"></iconify-icon>
                            </a>
                            <a href="https://telegram.me/share/url?url=<?= urlencode($shareUrl) ?>&text=<?= urlencode($news['title']) ?>" 
                               target="_blank" class="news-share-btn news-share-tg" title="Bagikan ke Telegram">
                                <iconify-icon icon="logos:telegram"></iconify-icon>
                            </a>
                            <a href="https://twitter.com/intent/tweet?text=<?= urlencode($news['title']) ?>&url=<?= urlencode($shareUrl) ?>" 
                               target="_blank" class="news-share-btn news-share-tw" title="Bagikan ke X (Twitter)">
                                <iconify-icon icon="ri:twitter-x-fill"></iconify-icon>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($shareUrl) ?>" 
                               target="_blank" class="news-share-btn news-share-fb" title="Bagikan ke Facebook">
                                <iconify-icon icon="logos:facebook"></iconify-icon>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($shareUrl) ?>" 
                               target="_blank" class="news-share-btn news-share-in" title="Bagikan ke LinkedIn">
                                <iconify-icon icon="logos:linkedin-icon"></iconify-icon>
                            </a>
                            <button type="button" class="news-share-btn news-share-copy" id="btnCopyLink" title="Salin Tautan">
                                <iconify-icon icon="solar:copy-bold"></iconify-icon>
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
                            <iconify-icon icon="solar:shield-star-bold"></iconify-icon>
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
                                    <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
                                    <span>Berita Sebelumnya</span>
                                </div>
                                <div class="news-nav-card-title"><?= esc($prevNews['title']) ?></div>
                            </a>
                        <?php endif; ?>

                        <?php if (! empty($nextNews)): ?>
                            <a href="<?= site_url('berita/' . $nextNews['slug']) ?>" class="news-nav-card text-end">
                                <div class="news-nav-card-label justify-content-end">
                                    <span>Berita Selanjutnya</span>
                                    <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                                </div>
                                <div class="news-nav-card-title"><?= esc($nextNews['title']) ?></div>
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Back to Catalog Button -->
                    <div class="text-center pt-3 border-top">
                        <a href="<?= site_url('berita') ?>" class="btn btn-outline-primary px-4 py-2 radius-10 d-inline-flex align-items-center gap-2">
                            <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
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
                                Lihat Semua <iconify-icon icon="solar:arrow-right-linear" class="align-middle"></iconify-icon>
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
                        <iconify-icon icon="solar:printer-bold" class="text-primary"></iconify-icon>
                        <span>Aksi Dokumen</span>
                    </h4>
                    <p class="text-xs text-slate-500 mb-3">Cetak artikel ini untuk arsip fisik atau lampiran bahan rapat:</p>
                    <button type="button" onclick="window.print()" class="btn btn-outline-secondary w-100 py-2 radius-10 d-inline-flex align-items-center justify-content-center gap-2 mb-2">
                        <iconify-icon icon="solar:printer-outline" class="text-lg"></iconify-icon>
                        <span>Cetak Halaman (Print)</span>
                    </button>
                </div>

                <!-- Popular News Widget -->
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

                <!-- Categories Directory Widget -->
                <div class="news-sidebar-widget">
                    <h4 class="news-widget-title">
                        <iconify-icon icon="solar:folder-with-files-bold" class="text-warning"></iconify-icon>
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
                    <h5 class="fw-bold text-dark mb-2">Jelajahi Ekosistem BegawiPBJ</h5>
                    <p class="text-xs text-slate-500 mb-3">Tingkatkan pemahaman dan kapabilitas Anda melalui kanal lainnya:</p>
                    <div class="d-grid gap-2">
                        <a href="<?= site_url('regulasi') ?>" class="btn btn-sm btn-white border d-flex align-items-center justify-content-between p-2 radius-8 text-decoration-none text-dark">
                            <span><iconify-icon icon="solar:diploma-verified-bold" class="text-warning me-2"></iconify-icon> Pusat Regulasi PBJ</span>
                            <iconify-icon icon="solar:arrow-right-linear" class="text-muted"></iconify-icon>
                        </a>
                        <a href="<?= site_url('arena-belajar') ?>" class="btn btn-sm btn-white border d-flex align-items-center justify-content-between p-2 radius-8 text-decoration-none text-dark">
                            <span><iconify-icon icon="solar:mortarboard-bold" class="text-info me-2"></iconify-icon> Arena Belajar &amp; Sertifikasi</span>
                            <iconify-icon icon="solar:arrow-right-linear" class="text-muted"></iconify-icon>
                        </a>
                        <a href="<?= site_url('bunga-rampai') ?>" class="btn btn-sm btn-white border d-flex align-items-center justify-content-between p-2 radius-8 text-decoration-none text-dark">
                            <span><iconify-icon icon="solar:book-bookmark-bold" class="text-success me-2"></iconify-icon> Bunga Rampai &amp; Template</span>
                            <iconify-icon icon="solar:arrow-right-linear" class="text-muted"></iconify-icon>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Copy Link Toast Feedback -->
<div id="copyToast">
    <iconify-icon icon="solar:check-circle-bold" class="text-success text-xl"></iconify-icon>
    <span>Tautan berita berhasil disalin ke clipboard!</span>
</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnCopy = document.getElementById('btnCopyLink');
        const toast = document.getElementById('copyToast');

        if (btnCopy) {
            btnCopy.addEventListener('click', function () {
                const url = '<?= $shareUrl ?>';
                navigator.clipboard.writeText(url).then(function () {
                    if (toast) {
                        toast.style.display = 'flex';
                        setTimeout(function () {
                            toast.style.display = 'none';
                        }, 3000);
                    }
                }).catch(function () {
                    alert('Tautan: ' + url);
                });
            });
        }
    });
</script>
<?= $this->endSection() ?>
