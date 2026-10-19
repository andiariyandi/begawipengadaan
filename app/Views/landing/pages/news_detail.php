<?= $this->extend('landing/layouts/public') ?>

<?php
/**
 * ============================================================================
 *  BERITA PBJ - Detail Page (/berita/{slug})
 *  Halaman bacaan lengkap artikel berita PBJ dengan sticky TOC, author bio,
 *  share bar, prev/next navigation, dan related articles grid.
 * ============================================================================
 */

if (! function_exists('newsCategoryColor')) {
    function newsCategoryColor(string $slug): array
    {
        $palette = [
            ['#0c5aa6', '#e0f2fe'],
            ['#d97706', '#fef3c7'],
            ['#16a34a', '#dcfce7'],
            ['#9333ea', '#faf5ff'],
            ['#dc2626', '#fee2e2'],
            ['#0891b2', '#cffafe'],
            ['#db2777', '#fce7f3'],
            ['#65a30d', '#ecfccb'],
        ];
        $hash = 0;
        for ($i = 0, $n = strlen($slug); $i < $n; $i++) {
            $hash = ($hash * 31 + ord($slug[$i])) & 0xfffffff;
        }
        return $palette[$hash % count($palette)];
    }
}

if (! function_exists('newsAuthorInitials')) {
    function newsAuthorInitials(?string $name): string
    {
        $name = trim((string) $name);
        if ($name === '') return 'B';
        $words = preg_split('/\s+/', $name);
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= mb_strtoupper(mb_substr($w, 0, 1));
        }
        return $initials ?: 'B';
    }
}

// Parse tags
$rawTags = $news['tags'] ?? '';
$tagsList = [];
if (is_string($rawTags) && trim($rawTags) !== '') {
    $tagsList = array_values(array_filter(array_map('trim', preg_split('/[,\n;]+/', $rawTags) ?: [])));
}

$authorName = $news['author_name'] ?? 'Biro PBJ Setda Lampung';
$authorBio  = $news['author_bio'] ?? null;
$authorRole = $news['author_role'] ?? 'Tim Komunikasi Publik &middot; Biro Pengadaan Barang &amp; Jasa Sekretariat Daerah Provinsi Lampung';
$authorInit = newsAuthorInitials($authorName);

$sourceName = $news['source_name'] ?? null;
$sourceUrl  = $news['source_url']  ?? null;

$coverUrl = ! empty($news['cover_path'])
    ? base_url($news['cover_path'])
    : base_url('assets/main/images/blog/blog-details-img-1.jpg');

$readTimeClean = trim((string) $readTime);
if (is_numeric($readTimeClean)) {
    $readTimeClean .= ' menit';
} elseif ($readTimeClean === '') {
    $readTimeClean = '5 menit';
}

$publishedAt = $news['published_at'] ?? $news['created_at'] ?? null;
$dateLong    = $publishedAt ? date('d F Y', strtotime($publishedAt)) : date('d F Y');
$dateShort   = $publishedAt ? date('d M Y', strtotime($publishedAt)) : date('d M Y');

$catSlug = $news['category_slug'] ?? 'umum';
[$catFg, $catBg] = newsCategoryColor($catSlug);
?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-news.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-news.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-news.css') : '1.0' ?>" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Reading Progress Bar -->
<div id="readingProgressBar" aria-hidden="true"></div>

<!-- MAIN SECTION — Following Landing News UI Benchmark -->
<section class="berita-main-section news-detail-section">
    <div class="container">

        <!-- Breadcrumb Navigation (Clean & Minimalist) -->
        <nav class="news-detail-breadcrumb-clean" aria-label="Breadcrumb">
            <a href="<?= site_url('/') ?>"><i class="fas fa-home"></i> Beranda</a>
            <span class="sep">/</span>
            <a href="<?= site_url('berita') ?>">Berita PBJ</a>
            <span class="sep">/</span>
            <a href="<?= site_url('berita/kategori/' . urlencode($catSlug)) ?>"><?= esc($news['category_name'] ?? 'Berita') ?></a>
            <span class="sep">/</span>
            <span class="current text-truncate"><?= esc(mb_strimwidth($news['title'] ?? '', 0, 52, '...')) ?></span>
        </nav>

        <!-- TOP NEWS COVER IMAGE — "yang paling atas adalah gambar dari berita" -->
        <div class="news-detail-cover mb-4">
            <div class="news-detail-cover__media">
                <img src="<?= $coverUrl ?>" alt="<?= esc($news['title']) ?>" loading="eager">
                <div class="news-detail-cover__badges">
                    <span class="news-detail-cover__badge">
                        <span class="dot"></span> Berita Resmi PBJ
                    </span>
                    <?php if (! empty($news['category_name'])): ?>
                        <a href="<?= site_url('berita/kategori/' . urlencode($catSlug)) ?>" class="news-detail-cover__cat">
                            <i class="fas fa-folder-open"></i> <?= esc($news['category_name']) ?>
                        </a>
                    <?php endif; ?>
                </div>
                <?php if (! empty($news['cover_caption'])): ?>
                    <div class="news-detail-cover__caption">
                        <i class="fas fa-camera"></i>
                        <span><?= esc($news['cover_caption']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 2-COLUMN MAIN CONTENT GRID -->
        <div class="row g-4">
            <!-- Left: Article Column -->
            <div class="col-lg-8">
                <article class="news-article">
                    <!-- Clean Article Meta Bar -->
                    <div class="news-detail-meta-clean">
                        <div class="author-chip">
                            <span class="avatar"><?= esc($authorInit) ?></span>
                            <span>Oleh <strong><?= esc($authorName) ?></strong></span>
                        </div>
                        <span class="meta-divider d-none d-sm-inline">&middot;</span>
                        <div class="meta-item"><i class="far fa-calendar-alt text-primary"></i> <time datetime="<?= esc($publishedAt) ?>"><?= esc($dateLong) ?></time></div>
                        <span class="meta-divider d-none d-sm-inline">&middot;</span>
                        <div class="meta-item"><i class="far fa-clock text-warning"></i> <?= esc($readTimeClean) ?></div>
                        <span class="meta-divider d-none d-sm-inline">&middot;</span>
                        <div class="meta-item"><i class="far fa-eye text-info"></i> <?= number_format($news['view_count'] ?? 0) ?> views</div>
                    </div>

                    <!-- Article Title H1 -->
                    <h1 class="news-detail-title-clean"><?= esc($news['title']) ?></h1>

                    <!-- Lead / Summary Box -->
                    <?php if (! empty($news['summary'])): ?>
                        <div class="news-lead-summary-box mb-4">
                            <p class="news-detail-excerpt mb-0">
                                <?= esc($news['summary']) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Key Takeaways for Stakeholders -->
                    <div class="news-key-takeaways p-3 p-md-4 radius-14 mb-4 border" style="background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%); border-color: #bfdbfe !important;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="fas fa-lightbulb text-warning fs-5"></i>
                            <h4 class="fw-bold mb-0 text-dark" style="font-size: 16.5px;">Poin Penting untuk Pemangku Kepentingan</h4>
                        </div>
                        <p class="text-secondary mb-0" style="font-size: 14.5px; line-height: 1.65;">
                            <?= esc($news['summary'] ?: 'Publikasi resmi ini merangkum arahan kebijakan dan tata kelola pengadaan barang/jasa di lingkungan Pemerintah Provinsi Lampung untuk transparansi, akuntabilitas, dan efisiensi belanja publik.') ?>
                        </p>
                    </div>

                    <!-- Article Body Content -->
                    <div class="news-article-body" id="articleBodyContent">
                        <?= $news['content'] /* HTML allowed in article body */ ?>

                        <?php if (! empty($sourceName) || ! empty($sourceUrl)): ?>
                            <div class="news-source">
                                <strong><i class="fas fa-info-circle text-primary me-1"></i> Sumber Informasi:</strong>
                                <?php if (! empty($sourceName)): ?>
                                    <?= esc($sourceName) ?>
                                <?php endif; ?>
                                <?php if (! empty($sourceUrl)): ?>
                                    &middot; <a href="<?= esc($sourceUrl) ?>" target="_blank" rel="noopener noreferrer">Kunjungi Sumber <i class="fas fa-external-link-alt ms-1"></i></a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tags -->
                    <?php if (! empty($tagsList)): ?>
                        <div class="news-tags-bar">
                            <span class="news-tags-bar__label"><i class="fas fa-tags"></i> Topik Terkait:</span>
                            <?php foreach ($tagsList as $t): ?>
                                <a href="<?= site_url('berita?q=' . urlencode($t)) ?>" class="news-tag-pill"><?= esc($t) ?></a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Share Bar -->
                    <div class="news-share-bar">
                        <div class="news-share-bar__label">
                            <i class="fas fa-share-alt"></i> Bantu Sebarkan Kabar Ini
                        </div>
                        <div class="news-share-bar__buttons">
                            <?php
                                $waText  = urlencode($news['title'] . ' - ' . ($shareUrl ?? site_url('berita/' . $news['slug'])));
                                $waUrl   = 'https://wa.me/?text=' . $waText;
                                $fbUrl   = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($shareUrl ?? current_url());
                                $twText  = urlencode($news['title']);
                                $twUrl   = 'https://twitter.com/intent/tweet?url=' . urlencode($shareUrl ?? current_url()) . '&text=' . $twText;
                                $liUrl   = 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($shareUrl ?? current_url());
                            ?>
                            <a href="<?= $waUrl ?>" target="_blank" rel="noopener noreferrer" class="news-share-btn news-share-btn--wa" title="Bagikan ke WhatsApp" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                            <a href="<?= $fbUrl ?>" target="_blank" rel="noopener noreferrer" class="news-share-btn news-share-btn--fb" title="Bagikan ke Facebook" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="<?= $twUrl ?>" target="_blank" rel="noopener noreferrer" class="news-share-btn news-share-btn--tw" title="Bagikan ke X/Twitter" aria-label="X/Twitter"><i class="fab fa-x-twitter"></i></a>
                            <a href="<?= $liUrl ?>" target="_blank" rel="noopener noreferrer" class="news-share-btn news-share-btn--li" title="Bagikan ke LinkedIn" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            <button type="button" id="copyLinkBtn" class="news-share-btn news-share-btn--copy" title="Salin Tautan" aria-label="Salin Tautan"><i class="fas fa-link"></i></button>
                        </div>
                    </div>
                </article>

                <!-- Author Bio -->
                <div class="news-author-bio">
                    <div class="news-author-bio__avatar"><?= esc($authorInit) ?></div>
                    <div class="news-author-bio__body">
                        <div class="news-author-bio__role"><?= esc($authorRole) ?></div>
                        <h3 class="news-author-bio__name"><?= esc($authorName) ?></h3>
                        <p class="news-author-bio__desc">
                            <?php if (! empty($authorBio)): ?>
                                <?= esc($authorBio) ?>
                            <?php else: ?>
                                Penulis aktif yang mengelola publikasi berita dan dokumentasi kegiatan
                                di lingkungan Biro Pengadaan Barang &amp; Jasa Sekretariat Daerah
                                Provinsi Lampung. Kontribusi artikel ini telah melalui proses
                                verifikasi redaksi PPID sesuai UU No. 14 Tahun 2008 tentang
                                Keterbukaan Informasi Publik.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <!-- Prev / Next Navigation -->
                <div class="news-prevnext">
                    <a href="<?= ! empty($prevNews) ? site_url('berita/' . $prevNews['slug']) : '#' ?>"
                        class="news-prevnext__item <?= empty($prevNews) ? 'is-disabled' : '' ?>"
                        <?= ! empty($prevNews) ? 'title="' . esc($prevNews['title']) . '"' : '' ?>>
                        <div class="news-prevnext__icon"><i class="fas fa-arrow-left"></i></div>
                        <div class="news-prevnext__body">
                            <div class="news-prevnext__hint">
                                <span>Berita Sebelumnya</span>
                            </div>
                            <div class="news-prevnext__title">
                                <?= ! empty($prevNews) ? esc(mb_strimwidth($prevNews['title'], 0, 72, '...')) : 'Tidak ada berita sebelumnya' ?>
                            </div>
                        </div>
                    </a>
                    <a href="<?= ! empty($nextNews) ? site_url('berita/' . $nextNews['slug']) : '#' ?>"
                        class="news-prevnext__item is-next <?= empty($nextNews) ? 'is-disabled' : '' ?>"
                        <?= ! empty($nextNews) ? 'title="' . esc($nextNews['title']) . '"' : '' ?>>
                        <div class="news-prevnext__icon"><i class="fas fa-arrow-right"></i></div>
                        <div class="news-prevnext__body">
                            <div class="news-prevnext__hint">
                                <span>Berita Selanjutnya</span>
                            </div>
                            <div class="news-prevnext__title">
                                <?= ! empty($nextNews) ? esc(mb_strimwidth($nextNews['title'], 0, 72, '...')) : 'Tidak ada berita selanjutnya' ?>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Right: Sticky Sidebar -->
            <div class="col-lg-4">
                <aside class="detail-sidebar">
                    <!-- Search Widget -->
                    <div class="detail-widget">
                        <h4 class="detail-widget__title">
                            <i class="fas fa-search text-primary"></i>
                            <span>Cari Artikel Berita</span>
                        </h4>
                        <form action="<?= site_url('berita') ?>" method="get" class="berita-searchbox news-search-box">
                            <input type="text" name="q" placeholder="Kata kunci pengadaan..." aria-label="Cari Berita">
                            <button type="submit" aria-label="Cari"><i class="fas fa-search"></i></button>
                        </form>
                    </div>

                    <!-- Sticky TOC -->
                    <div class="detail-widget" id="detailTocWidget">
                        <h4 class="detail-widget__title">
                            <i class="fas fa-list-ol text-primary"></i>
                            <span>Daftar Isi</span>
                        </h4>
                        <ul class="detail-toc" id="detailTocList">
                            <li class="h3"><p class="detail-toc-empty">Memuat sub-bagian&hellip;</p></li>
                        </ul>
                    </div>

                    <!-- Related Sidebar -->
                    <div class="detail-widget">
                        <h4 class="detail-widget__title accent-gold">
                            <i class="fas fa-newspaper"></i>
                            <span>Berita Terkait</span>
                        </h4>
                        <?php if (empty($relatedNews)): ?>
                            <p class="text-sm text-muted mb-0">Belum ada berita terkait.</p>
                        <?php else: ?>
                            <div class="detail-related-list">
                                <?php foreach (array_slice($relatedNews, 0, 4) as $rn):
                                    $rnThumb = ! empty($rn['cover_path'])
                                        ? base_url($rn['cover_path'])
                                        : base_url('assets/main/images/blog/blog-list-1-1.jpg');
                                ?>
                                    <div class="detail-related-item">
                                        <a href="<?= site_url('berita/' . $rn['slug']) ?>" class="detail-related-thumb">
                                            <img src="<?= $rnThumb ?>" alt="<?= esc($rn['title']) ?>" loading="lazy">
                                        </a>
                                        <div style="min-width: 0; flex: 1;">
                                            <h5 class="detail-related-title">
                                                <a href="<?= site_url('berita/' . $rn['slug']) ?>"><?= esc($rn['title']) ?></a>
                                            </h5>
                                            <div class="detail-related-meta">
                                                <i class="far fa-calendar-alt"></i> <?= date('d M Y', strtotime($rn['published_at'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Popular News Sidebar -->
                    <div class="detail-widget">
                        <h4 class="detail-widget__title accent-red">
                            <i class="fas fa-fire"></i>
                            <span>Paling Banyak Dibaca</span>
                        </h4>
                        <?php if (empty($popularNews)): ?>
                            <p class="text-sm text-muted mb-0">Belum ada data.</p>
                        <?php else: ?>
                            <div class="detail-related-list">
                                <?php foreach (array_slice($popularNews, 0, 4) as $pop):
                                    $popThumb = ! empty($pop['cover_path'])
                                        ? base_url($pop['cover_path'])
                                        : base_url('assets/main/images/blog/blog-list-1-1.jpg');
                                ?>
                                    <div class="detail-related-item">
                                        <a href="<?= site_url('berita/' . $pop['slug']) ?>" class="detail-related-thumb">
                                            <img src="<?= $popThumb ?>" alt="<?= esc($pop['title']) ?>" loading="lazy">
                                        </a>
                                        <div style="min-width: 0; flex: 1;">
                                            <h5 class="detail-related-title">
                                                <a href="<?= site_url('berita/' . $pop['slug']) ?>"><?= esc($pop['title']) ?></a>
                                            </h5>
                                            <div class="detail-related-meta">
                                                <i class="far fa-eye"></i> <?= number_format($pop['view_count']) ?> views
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Newsletter -->
                    <div class="detail-newsletter">
                        <div class="detail-newsletter__icon"><i class="fas fa-envelope-open-text"></i></div>
                        <h4 class="detail-newsletter__title">Newsletter PBJ Lampung</h4>
                        <p class="detail-newsletter__lead">
                            Dapatkan rangkuman mingguan kabar &amp; kebijakan pengadaan langsung ke email Anda.
                        </p>
                        <form class="detail-newsletter__form" onsubmit="event.preventDefault(); this.querySelector('button').innerHTML='<i class=\'fas fa-check\'></i> OK';">
                            <input type="email" placeholder="Email Anda" aria-label="Email">
                            <button type="submit" title="Daftar">
                                <i class="fas fa-paper-plane"></i>
                                <span>Daftar</span>
                            </button>
                        </form>
                    </div>
                </aside>
            </div>
        </div>

        <!-- Related News Grid -->
        <?php if (! empty($relatedNews)): ?>
            <div class="news-related">
                <h2 class="news-related__title">
                    <i class="fas fa-book-open"></i>
                    <span>Rekomendasi Berita untuk Anda</span>
                </h2>
                <div class="row g-4">
                    <?php foreach (array_slice($relatedNews, 0, 3) as $rn):
                        $rnThumb = ! empty($rn['cover_path'])
                            ? base_url($rn['cover_path'])
                            : base_url('assets/main/images/blog/blog-list-1-1.jpg');
                    ?>
                        <div class="col-md-4">
                            <article class="news-related__card">
                                <a href="<?= site_url('berita/' . $rn['slug']) ?>" class="news-related__media d-block text-decoration-none">
                                    <img src="<?= $rnThumb ?>" alt="<?= esc($rn['title']) ?>" loading="lazy">
                                    <span class="news-related__cat"><?= esc($rn['category_name'] ?? 'Berita') ?></span>
                                </a>
                                <div class="news-related__body">
                                    <div class="news-related__meta">
                                        <i class="far fa-calendar-alt"></i> <?= date('d M Y', strtotime($rn['published_at'])) ?>
                                        &middot; <i class="far fa-eye"></i> <?= number_format($rn['view_count'] ?? 0) ?>
                                    </div>
                                    <h3 class="news-related__heading">
                                        <a href="<?= site_url('berita/' . $rn['slug']) ?>"><?= esc($rn['title']) ?></a>
                                    </h3>
                                    <a href="<?= site_url('berita/' . $rn['slug']) ?>" class="news-related__cta">
                                        <span>Baca Selengkapnya</span> <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Closing CTA -->
        <div class="detail-cta">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="detail-cta__eyebrow">
                        <i class="fas fa-bullhorn"></i> Keterbukaan Informasi Publik
                    </span>
                    <h3 class="detail-cta__title text-white">Pusat Siaran Pers &amp; Klarifikasi Berita PBJ Lampung</h3>
                    <p class="detail-cta__lead text-white" style="opacity: 0.92;">
                        Membutuhkan konfirmasi, klarifikasi berita, atau materi siaran pers resmi terkait
                        pengadaan barang dan jasa Pemerintah Provinsi Lampung? Tim PPID Biro PBJ siap
                        melayani permohonan Anda.
                    </p>
                </div>
                <div class="col-lg-4">
                    <div class="detail-cta__actions">
                        <a href="<?= site_url('kontak') ?>" class="detail-cta__btn detail-cta__btn--primary">
                            <i class="fas fa-envelope-open-text"></i>
                            <span>Hubungi PPID</span>
                        </a>
                        <a href="<?= site_url('berita') ?>" class="detail-cta__btn detail-cta__btn--ghost">
                            <i class="fas fa-newspaper"></i>
                            <span>Berita Lainnya</span>
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
(function () {
    var body = document.getElementById('articleBodyContent');
    var tocList = document.getElementById('detailTocList');
    if (body && tocList) {
        var headings = body.querySelectorAll('h2, h3');
        if (headings.length === 0) {
            tocList.innerHTML = '<li class="h3"><p class="detail-toc-empty">Artikel ini belum memiliki sub-bagian.</p></li>';
        } else {
            var html = '';
            headings.forEach(function (h, i) {
                if (! h.id) {
                    h.id = 'toc-' + (i + 1) + '-' + h.textContent.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                }
                var level = h.tagName.toLowerCase() === 'h3' ? 'h3' : 'h2';
                html += '<li class="' + level + '"><a href="#' + h.id + '" data-toc-target="' + h.id + '">' + h.textContent.trim() + '</a></li>';
            });
            tocList.innerHTML = html;
        }

        var tocLinks = tocList.querySelectorAll('a[data-toc-target]');
        if (tocLinks.length > 0 && 'IntersectionObserver' in window) {
            var observed = [];
            tocLinks.forEach(function (a) {
                var t = document.getElementById(a.getAttribute('data-toc-target'));
                if (t) observed.push({ el: t, link: a });
            });
            var spy = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) {
                        tocLinks.forEach(function (a) { a.classList.remove('is-active'); });
                        var link = observed.find(function (o) { return o.el === e.target; });
                        if (link) link.link.classList.add('is-active');
                    }
                });
            }, { rootMargin: '0px 0px -70% 0px', threshold: 0 });
            observed.forEach(function (o) { spy.observe(o.el); });
        }

        tocList.addEventListener('click', function (ev) {
            var a = ev.target.closest('a[data-toc-target]');
            if (! a) return;
            ev.preventDefault();
            var target = document.getElementById(a.getAttribute('data-toc-target'));
            if (target) {
                window.scrollTo({ top: target.getBoundingClientRect().top + window.scrollY - 90, behavior: 'smooth' });
            }
        });
    }

    var bar = document.getElementById('readingProgressBar');
    if (bar && body) {
        var updateProgress = function () {
            var rect = body.getBoundingClientRect();
            var top    = window.scrollY + rect.top;
            var height = rect.height;
            var scrolled = window.scrollY - top;
            var pct = Math.max(0, Math.min(100, (scrolled / Math.max(1, height - window.innerHeight)) * 100));
            bar.style.width = pct + '%';
        };
        window.addEventListener('scroll', updateProgress, { passive: true });
        window.addEventListener('resize', updateProgress);
        updateProgress();
    }

    var copyBtn = document.getElementById('copyLinkBtn');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            var url = <?= json_encode($shareUrl ?? current_url()) ?>;
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(function () {
                    copyBtn.classList.add('is-copied');
                    copyBtn.innerHTML = '<i class="fas fa-check"></i>';
                    setTimeout(function () {
                        copyBtn.classList.remove('is-copied');
                        copyBtn.innerHTML = '<i class="fas fa-link"></i>';
                    }, 1800);
                });
            } else {
                try {
                    var ta = document.createElement('textarea');
                    ta.value = url;
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                    copyBtn.classList.add('is-copied');
                    copyBtn.innerHTML = '<i class="fas fa-check"></i>';
                    setTimeout(function () {
                        copyBtn.classList.remove('is-copied');
                        copyBtn.innerHTML = '<i class="fas fa-link"></i>';
                    }, 1800);
                } catch (e) {}
            }
        });
    }
})();
</script>
<?= $this->endSection() ?>
