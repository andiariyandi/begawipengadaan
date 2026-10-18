<?= $this->extend('landing/layouts/public') ?>

<?php
/**
 * ============================================================================
 *  BERITA PBJ - Landing Page Indeks (/berita, /berita/kategori/*)
 *  Pusat informasi publikasi Pengadaan Barang/Jasa Pemerintah Provinsi Lampung.
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

if (! function_exists('newsTrendingBadge')) {
    function newsTrendingBadge(array $item, int $index): ?string
    {
        $views = (int) ($item['view_count'] ?? 0);
        if ($views >= 1000) {
            return '<span class="berita-card__flag berita-card__flag--hot"><i class="fas fa-fire"></i> Trending</span>';
        }
        if (($index ?? 0) < 2) {
            return '<span class="berita-card__flag berita-card__flag--new"><i class="fas fa-sparkles"></i> Terbaru</span>';
        }
        return null;
    }
}

$stats = $stats ?? [];
$stats = array_merge([
    'total_news'        => 0,
    'total_categories'  => max(1, count($categories ?? [])),
    'total_views'       => 0,
    'this_month'        => 0,
], $stats);

$headlineList = ! empty($headlineArticles) ? $headlineArticles : (! empty($headlineArticle) ? [$headlineArticle] : []);
?>
<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-news.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-news.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-news.css') : '1.0' ?>" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- MAIN CONTENT AREA -->
<section class="berita-main-section" id="kategori-liputan-section">
    <div class="container">

        <?php if (! empty($currentCategory) || ! empty($keyword)): ?>
            <!-- Filter Active Notification -->
            <div class="d-flex align-items-center justify-content-between p-3 mb-4 radius-12 bg-light border">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-filter text-primary"></i>
                    <span class="text-muted small">Menampilkan Berita:</span>
                    <?php if (! empty($currentCategory)): ?>
                        <span class="badge bg-primary text-white fw-bold px-3 py-2"><?= esc($currentCategory['name']) ?></span>
                    <?php endif; ?>
                    <?php if (! empty($keyword)): ?>
                        <span class="badge bg-warning text-dark fw-bold px-3 py-2">"<?= esc($keyword) ?>"</span>
                    <?php endif; ?>
                </div>
                <a href="<?= site_url('berita') ?>" class="btn btn-sm btn-outline-secondary radius-8 fw-bold">
                    <i class="fas fa-times me-1"></i> Reset Filter
                </a>
            </div>
        <?php endif; ?>

        <!-- Headline Carousel / Single -->
        <?php if (! empty($headlineList)): ?>
            <div class="berita-headline mb-4">
                <?php if (count($headlineList) > 1): ?>
                    <div id="headlineCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
                        <div class="carousel-inner radius-16 overflow-hidden">
                            <?php foreach ($headlineList as $hIndex => $hItem):
                                $hThumb = ! empty($hItem['cover_path'])
                                    ? base_url($hItem['cover_path'])
                                    : (! empty($hItem['cover_url']) ? $hItem['cover_url'] : base_url('assets/main/images/blog/blog-details-img-1.jpg'));
                            ?>
                                <div class="carousel-item <?= $hIndex === 0 ? 'active' : '' ?>">
                                    <div class="berita-headline__inner news-headline-card">
                                        <div class="berita-headline__media">
                                            <img src="<?= $hThumb ?>" alt="<?= esc($hItem['title']) ?>" loading="lazy">
                                            <div class="berita-headline__badges">
                                                <span class="berita-headline__badge">
                                                    <i class="fas fa-star"></i> Berita Utama
                                                </span>
                                            </div>
                                            <div class="berita-headline__counter">
                                                <span class="dot"></span>
                                                <span>Utama #<?= $hIndex + 1 ?> / <?= count($headlineList) ?></span>
                                            </div>
                                            <div class="berita-headline__caption">
                                                <span class="berita-headline__cat">
                                                    <i class="fas fa-folder-open"></i> <?= esc($hItem['category_name'] ?? 'Berita') ?>
                                                </span>
                                                <h2 class="berita-headline__title">
                                                    <a href="<?= site_url('berita/' . $hItem['slug']) ?>">
                                                        <?= esc($hItem['title']) ?>
                                                    </a>
                                                </h2>
                                                <div class="berita-headline__meta">
                                                    <span><i class="far fa-calendar-alt"></i> <?= esc($hItem['date_formatted'] ?? date('d M Y', strtotime($hItem['published_at'] ?? 'now'))) ?></span>
                                                    <span><i class="far fa-clock"></i> <?= esc($hItem['read_time'] ?? '5') ?> mnt baca</span>
                                                    <span><i class="far fa-eye"></i> <?= number_format($hItem['view_count'] ?? 0) ?> views</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="berita-headline__nav prev" type="button" data-bs-target="#headlineCarousel" data-bs-slide="prev" aria-label="Sebelumnya">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="berita-headline__nav next" type="button" data-bs-target="#headlineCarousel" data-bs-slide="next" aria-label="Selanjutnya">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <div class="berita-headline__indicators">
                            <?php foreach ($headlineList as $hIndex => $hItem): ?>
                                <button type="button" data-bs-target="#headlineCarousel" data-bs-slide-to="<?= $hIndex ?>"
                                    class="<?= $hIndex === 0 ? 'is-active' : '' ?>"
                                    aria-label="Slide <?= $hIndex + 1 ?>"></button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <?php
                    $singleArticle = $headlineList[0];
                    $sThumb = ! empty($singleArticle['cover_path'])
                        ? base_url($singleArticle['cover_path'])
                        : (! empty($singleArticle['cover_url']) ? $singleArticle['cover_url'] : base_url('assets/main/images/blog/blog-details-img-1.jpg'));
                    ?>
                    <div class="berita-headline-single news-headline-card radius-16 overflow-hidden">
                        <div class="berita-headline__media">
                            <img src="<?= $sThumb ?>" alt="<?= esc($singleArticle['title']) ?>" loading="lazy">
                            <div class="berita-headline__badges">
                                <span class="berita-headline__badge">
                                    <i class="fas fa-star"></i> Berita Utama
                                </span>
                            </div>
                            <div class="berita-headline__caption">
                                <span class="berita-headline__cat">
                                    <i class="fas fa-folder-open"></i> <?= esc($singleArticle['category_name'] ?? 'Berita') ?>
                                </span>
                                <h2 class="berita-headline__title">
                                    <a href="<?= site_url('berita/' . $singleArticle['slug']) ?>">
                                        <?= esc($singleArticle['title']) ?>
                                    </a>
                                </h2>
                            </div>
                        </div>
                        <div class="berita-headline-single__body p-4">
                            <p class="berita-headline-single__desc text-muted mb-3" style="font-size: 15px; line-height: 1.65;">
                                <?= esc($singleArticle['summary'] ?? '') ?>
                            </p>
                            <div class="berita-headline__meta mb-3">
                                <span><i class="far fa-calendar-alt"></i> <?= esc($singleArticle['date_formatted'] ?? date('d M Y', strtotime($singleArticle['published_at'] ?? 'now'))) ?></span>
                                <span><i class="far fa-clock"></i> <?= esc($singleArticle['read_time'] ?? '5') ?> mnt baca</span>
                                <span><i class="far fa-eye"></i> <?= number_format($singleArticle['view_count'] ?? 0) ?> views</span>
                            </div>
                            <a href="<?= site_url('berita/' . $singleArticle['slug']) ?>" class="berita-cta__btn berita-cta__btn--primary" style="display: inline-flex;">
                                <span>Baca Berita Lengkap</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Main Grid & Sidebar Layout -->
        <div class="row g-4">
            <div class="col-lg-8">
                <?php
                $hasItems    = ! empty($items);
                $hasHeadline = ! empty($headlineList);
                ?>
                <?php if (! $hasItems && ! $hasHeadline): ?>
                    <div class="berita-empty">
                        <div class="berita-empty__icon"><i class="fas fa-newspaper"></i></div>
                        <h3 class="berita-empty__title">Belum Ada Berita yang Sesuai</h3>
                        <p class="berita-empty__desc">
                            Tidak ditemukan artikel berita untuk kata kunci atau kategori yang Anda pilih.
                            Coba gunakan kata kunci lain atau tampilkan seluruh berita PBJ Lampung.
                        </p>
                        <a href="<?= site_url('berita') ?>" class="berita-cta__btn" style="background: var(--berita-blue-500); color: #ffffff;">
                            <i class="fas fa-arrow-left"></i>
                            <span>Lihat Semua Berita</span>
                        </a>
                    </div>
                <?php else: ?>

                    <!-- GRID VIEW -->
                    <div class="berita-grid row g-4" id="beritaGridView">
                        <?php foreach ($items as $index => $item):
                            [$fgColor, $bgColor] = newsCategoryColor($item['category_slug'] ?? $item['slug'] ?? 'default');
                            $thumb = ! empty($item['cover_path'])
                                ? base_url($item['cover_path'])
                                : (! empty($item['cover_url']) ? $item['cover_url'] : base_url('assets/main/images/blog/blog-list-1-1.jpg'));
                            $author = $item['author_name'] ?? 'Biro PBJ Setda Lampung';
                        ?>
                            <div class="col-md-6">
                                <article class="berita-card news-grid-card" style="--cat-accent: <?= $fgColor ?>">
                                    <a href="<?= site_url('berita/' . $item['slug']) ?>" class="berita-card__media d-block text-decoration-none">
                                        <img src="<?= $thumb ?>" alt="<?= esc($item['title']) ?>" loading="lazy">
                                        <span class="berita-card__cat"><?= esc($item['category_name'] ?? 'Berita') ?></span>
                                        <span class="berita-card__readtime">
                                            <i class="far fa-clock"></i> <?= esc($item['read_time'] ?? '5') ?> mnt
                                        </span>
                                        <?= newsTrendingBadge($item, $index) ?>
                                    </a>
                                    <div class="berita-card__body">
                                        <div class="berita-card__meta">
                                            <span><i class="far fa-calendar-alt"></i> <?= esc($item['date_formatted'] ?? date('d M Y', strtotime($item['published_at'] ?? 'now'))) ?></span>
                                            <span>&middot;</span>
                                            <span><i class="far fa-eye"></i> <?= number_format($item['view_count'] ?? 0) ?> views</span>
                                        </div>
                                        <h3 class="berita-card__title">
                                            <a href="<?= site_url('berita/' . $item['slug']) ?>">
                                                <?= esc($item['title']) ?>
                                            </a>
                                        </h3>
                                        <p class="berita-card__summary">
                                            <?= esc($item['summary'] ?? '') ?>
                                        </p>
                                        <div class="berita-card__footer">
                                            <div class="berita-card__author">
                                                <span class="berita-card__author-avatar"><?= newsAuthorInitials($author) ?></span>
                                                <span><?= esc($author) ?></span>
                                            </div>
                                            <a href="<?= site_url('berita/' . $item['slug']) ?>" class="berita-card__cta">
                                                <span>Baca</span>
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- LIST VIEW -->
                    <div class="berita-list" id="beritaListView">
                        <?php foreach ($items as $index => $item):
                            [$fgColor, $bgColor] = newsCategoryColor($item['category_slug'] ?? $item['slug'] ?? 'default');
                            $thumb = ! empty($item['cover_path'])
                                ? base_url($item['cover_path'])
                                : (! empty($item['cover_url']) ? $item['cover_url'] : base_url('assets/main/images/blog/blog-list-1-1.jpg'));
                            $author = $item['author_name'] ?? 'Biro PBJ Setda Lampung';
                        ?>
                            <article class="berita-list__item news-grid-card" style="--cat-accent: <?= $fgColor ?>">
                                <a href="<?= site_url('berita/' . $item['slug']) ?>" class="berita-list__thumb text-decoration-none">
                                    <img src="<?= $thumb ?>" alt="<?= esc($item['title']) ?>" loading="lazy">
                                    <span class="berita-list__cat"><?= esc($item['category_name'] ?? 'Berita') ?></span>
                                </a>
                                <div class="berita-list__body">
                                    <h3 class="berita-list__title">
                                        <a href="<?= site_url('berita/' . $item['slug']) ?>">
                                            <?= esc($item['title']) ?>
                                        </a>
                                    </h3>
                                    <p class="berita-list__summary"><?= esc($item['summary'] ?? '') ?></p>
                                    <div class="berita-list__footer">
                                        <div class="berita-card__meta">
                                            <span><i class="far fa-calendar-alt"></i> <?= esc($item['date_formatted'] ?? date('d M Y', strtotime($item['published_at'] ?? 'now'))) ?></span>
                                            <span>&middot;</span>
                                            <span><i class="far fa-clock"></i> <?= esc($item['read_time'] ?? '5') ?> mnt</span>
                                            <span>&middot;</span>
                                            <span><i class="far fa-eye"></i> <?= number_format($item['view_count'] ?? 0) ?> views</span>
                                        </div>
                                        <a href="<?= site_url('berita/' . $item['slug']) ?>" class="berita-card__cta">
                                            <span>Baca Selengkapnya</span>
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Smart Pagination -->
                    <?php if (($totalPages ?? 1) > 1): ?>
                        <?php
                        $queryParams = [];
                        if (! empty($keyword))   $queryParams['q']   = $keyword;
                        if (! empty($sort) && $sort !== 'newest') $queryParams['sort'] = $sort;
                        $buildUrl = function ($p) use ($queryParams) {
                            $params = array_merge($queryParams, ['page' => $p]);
                            return current_url() . '?' . http_build_query($params);
                        };
                        $cp = (int) ($currentPage ?? 1);
                        $tp = (int) ($totalPages ?? 1);
                        ?>
                        <div class="berita-pagination">
                            <div class="berita-pagination__info">
                                Halaman <strong><?= $cp ?></strong> dari <strong><?= $tp ?></strong> &middot; <strong><?= number_format($stats['total_news'] ?? 0) ?></strong> total berita
                            </div>
                            <nav class="berita-pagination__list" aria-label="Navigasi Halaman">
                                <a href="<?= $buildUrl(max(1, $cp - 1)) ?>" class="berita-pgbtn <?= $cp <= 1 ? 'is-disabled' : '' ?>" aria-label="Sebelumnya">
                                    <i class="fas fa-chevron-left"></i>
                                    <span class="d-none d-sm-inline">Sebelumnya</span>
                                </a>

                                <?php if ($cp > 3): ?>
                                    <a href="<?= $buildUrl(1) ?>" class="berita-pgbtn">1</a>
                                    <?php if ($cp > 4): ?>
                                        <span class="berita-pgbtn is-ellipsis">&hellip;</span>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php for ($p = max(1, $cp - 1); $p <= min($tp, $cp + 1); $p++): ?>
                                    <a href="<?= $buildUrl($p) ?>" class="berita-pgbtn <?= $p === $cp ? 'is-active' : '' ?>"><?= $p ?></a>
                                <?php endfor; ?>

                                <?php if ($cp < $tp - 2): ?>
                                    <?php if ($cp < $tp - 3): ?>
                                        <span class="berita-pgbtn is-ellipsis">&hellip;</span>
                                    <?php endif; ?>
                                    <a href="<?= $buildUrl($tp) ?>" class="berita-pgbtn"><?= $tp ?></a>
                                <?php endif; ?>

                                <a href="<?= $buildUrl(min($tp, $cp + 1)) ?>" class="berita-pgbtn <?= $cp >= $tp ? 'is-disabled' : '' ?>" aria-label="Berikutnya">
                                    <span class="d-none d-sm-inline">Berikutnya</span>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </nav>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Right: Sidebar Widgets -->
            <div class="col-lg-4">
                <!-- Search Widget -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-search"></i>
                        <span>Cari Artikel Berita</span>
                    </h4>
                    <form action="<?= site_url('berita') ?>" method="get" class="berita-searchbox news-search-box" id="beritaSidebarSearchForm">
                        <input type="text" id="beritaSidebarSearchInput" name="q" placeholder="Kata kunci pengadaan..." value="<?= esc($keyword ?? '') ?>">
                        <button type="submit" aria-label="Cari"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <!-- Category Directory Widget -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title accent-gold">
                        <i class="fas fa-folder-open"></i>
                        <span>Kategori Liputan</span>
                    </h4>
                    <ul class="berita-catlist">
                        <li>
                            <a href="<?= site_url('berita') ?>" class="news-cat-pill <?= empty($selectedCategory) ? 'is-active' : '' ?>">
                                <span class="left"><span class="dot" style="background:#0284c7"></span>Semua Kategori</span>
                                <span class="count"><?= (int) ($stats['total_news'] ?? 0) ?></span>
                            </a>
                        </li>
                        <?php foreach (($categories ?? []) as $cat):
                            [$fg, $bg] = newsCategoryColor($cat['slug']);
                        ?>
                            <li>
                                <a href="<?= site_url('berita/kategori/' . $cat['slug']) ?>"
                                    class="news-cat-pill <?= $selectedCategory === $cat['slug'] ? 'is-active' : '' ?>"
                                    style="--cat-accent: <?= $fg ?>">
                                    <span class="left"><span class="dot" style="background: <?= $fg ?>"></span><?= esc($cat['name']) ?></span>
                                    <span class="count"><?= (int) $cat['news_count'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Trending / Popular News Widget -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title accent-red">
                        <i class="fas fa-fire"></i>
                        <span>Berita Terpopuler</span>
                    </h4>
                    <?php if (empty($popularNews)): ?>
                        <p class="text-sm text-muted mb-0">Belum ada data popularitas.</p>
                    <?php else: ?>
                        <?php foreach (($popularNews ?? []) as $rank => $pop): ?>
                            <div class="berita-trend">
                                <div class="berita-trend__rank <?= $rank < 3 ? 'is-top' : '' ?>">
                                    <?= $rank + 1 ?>
                                </div>
                                <div class="berita-trend__body">
                                    <h5 class="berita-trend__title">
                                        <a href="<?= site_url('berita/' . $pop['slug']) ?>">
                                            <?= esc($pop['title']) ?>
                                        </a>
                                    </h5>
                                    <div class="berita-trend__meta">
                                        <i class="far fa-calendar-alt"></i>
                                        <span><?= date('d M Y', strtotime($pop['published_at'])) ?></span>
                                        <span>&middot;</span>
                                        <i class="far fa-eye"></i>
                                        <span><?= number_format($pop['view_count']) ?> views</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Informasi Statistik Berita PBJ (di bawah Berita Terpopuler) -->
                <div class="berita-widget berita-stats-widget">
                    <h4 class="berita-widget__title accent-blue">
                        <i class="fas fa-chart-line text-primary"></i>
                        <span>Statistik Publikasi</span>
                    </h4>
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="<?= site_url('berita') ?>" class="text-decoration-none">
                                <div class="p-2 radius-8 border bg-light h-100 transition-all hover-shadow-sm text-center">
                                    <div class="fw-bold text-dark" style="font-size: 18px;"><?= number_format($stats['total_news']) ?></div>
                                    <small class="text-muted d-block fw-semibold" style="font-size: 11px; line-height: 1.25;">Berita Publikasi Resmi</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= site_url('berita') ?>" class="text-decoration-none">
                                <div class="p-2 radius-8 border bg-light h-100 transition-all hover-shadow-sm text-center">
                                    <div class="fw-bold text-warning-emphasis" style="font-size: 18px;"><?= number_format($stats['total_categories']) ?></div>
                                    <small class="text-muted d-block fw-semibold" style="font-size: 11px; line-height: 1.25;">Kategori Liputan PBJ</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= site_url('berita?sort=popular') ?>" class="text-decoration-none">
                                <div class="p-2 radius-8 border bg-light h-100 transition-all hover-shadow-sm text-center">
                                    <div class="fw-bold text-success" style="font-size: 18px;"><?= number_format($stats['total_views']) ?>+</div>
                                    <small class="text-muted d-block fw-semibold" style="font-size: 11px; line-height: 1.25;">Total Pembaca Terjangkau</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= site_url('berita?sort=newest') ?>" class="text-decoration-none">
                                <div class="p-2 radius-8 border bg-light h-100 transition-all hover-shadow-sm text-center">
                                    <div class="fw-bold text-primary" style="font-size: 18px;"><?= number_format($stats['this_month']) ?></div>
                                    <small class="text-muted d-block fw-semibold" style="font-size: 11px; line-height: 1.25;">Rilis Bulan Ini</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Official Redaksi & PPID Bulletin -->
                <div class="berita-widget">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                            <i class="fas fa-landmark"></i>
                        </div>
                        <div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle small fw-bold" style="font-size: 11.5px;">REDAKSI RESMI</span>
                            <h5 class="fw-bold mb-0 text-dark" style="font-size: 14.5px; line-height: 1.35;">Biro Pengadaan Barang &amp; Jasa Setda Provinsi Lampung</h5>
                        </div>
                    </div>
                    <p class="text-muted small mb-3" style="font-size: 13.5px; line-height: 1.6;">
                        Kanal siaran pers dan publikasi berita resmi pengelolaan pengadaan barang/jasa Pemerintah Provinsi Lampung yang terverifikasi dan akuntabel.
                    </p>
                    <a href="<?= site_url('kontak') ?>" class="btn btn-sm btn-outline-primary w-100 radius-8 fw-bold" style="font-size: 13px;">
                        <i class="fas fa-envelope-open-text me-1"></i> Kontak PPID Biro PBJ
                    </a>
                </div>

                <!-- Tag Cloud Widget -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title accent-cyan">
                        <i class="fas fa-tags"></i>
                        <span>Topik Populer</span>
                    </h4>
                    <div class="berita-tags">
                        <a href="<?= site_url('berita?q=P3DN') ?>" class="berita-tag">#P3DN</a>
                        <a href="<?= site_url('berita?q=Katalog') ?>" class="berita-tag">#E-Katalog</a>
                        <a href="<?= site_url('berita?q=Integritas') ?>" class="berita-tag">#Integritas</a>
                        <a href="<?= site_url('berita?q=Sertifikasi') ?>" class="berita-tag">#Sertifikasi</a>
                        <a href="<?= site_url('berita?q=Maturitas') ?>" class="berita-tag">#UKPBJ Proaktif</a>
                        <a href="<?= site_url('berita?q=Probity') ?>" class="berita-tag">#Probity Audit</a>
                        <a href="<?= site_url('berita?q=UMKM') ?>" class="berita-tag">#UMKM Lokal</a>
                        <a href="<?= site_url('berita?q=Bimtek') ?>" class="berita-tag">#Bimtek SPSE</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Closing CTA Banner -->
        <div class="berita-cta">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="berita-cta__eyebrow">
                        <i class="fas fa-bullhorn"></i> Keterbukaan Informasi Publik
                    </span>
                    <h3 class="berita-cta__title text-white">Pusat Siaran Pers &amp; Klarifikasi Berita PBJ Lampung</h3>
                    <p class="berita-cta__lead text-white" style="opacity: 0.92;">
                        Membutuhkan konfirmasi, klarifikasi berita, atau materi siaran pers resmi terkait
                        pengadaan barang dan jasa Pemerintah Provinsi Lampung? Tim Pejabat Pengelola
                        Informasi &amp; Dokumentasi (PPID) Biro PBJ siap melayani permohonan Anda.
                    </p>
                </div>
                <div class="col-lg-4">
                    <div class="berita-cta__actions">
                        <a href="<?= site_url('kontak') ?>" class="berita-cta__btn berita-cta__btn--primary">
                            <i class="fas fa-envelope-open-text"></i>
                            <span>Hubungi PPID</span>
                        </a>
                        <a href="<?= site_url('arena-belajar') ?>" class="berita-cta__btn berita-cta__btn--ghost">
                            <i class="fas fa-graduation-cap"></i>
                            <span>Arena Belajar</span>
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
    function beritaQuickSearch(term) {
        var input = document.getElementById('beritaSidebarSearchInput');
        var form = document.getElementById('beritaSidebarSearchForm');
        if (input && form) {
            input.value = term;
            form.submit();
        }
    }

    (function() {
        var gridBtn = document.getElementById('viewGridBtn');
        var listBtn = document.getElementById('viewListBtn');
        var grid = document.getElementById('beritaGridView');
        var list = document.getElementById('beritaListView');
        if (!gridBtn || !listBtn || !grid || !list) return;

        function apply(mode) {
            if (mode === 'list') {
                grid.classList.add('is-hidden');
                list.classList.add('is-active');
                listBtn.classList.add('is-active');
                gridBtn.classList.remove('is-active');
            } else {
                list.classList.remove('is-active');
                grid.classList.remove('is-hidden');
                gridBtn.classList.add('is-active');
                listBtn.classList.remove('is-active');
            }
        }

        try {
            var saved = localStorage.getItem('berita_view_mode');
            if (saved === 'list') apply('list');
        } catch (e) {}

        gridBtn.addEventListener('click', function() {
            apply('grid');
            try {
                localStorage.setItem('berita_view_mode', 'grid');
            } catch (e) {}
        });
        listBtn.addEventListener('click', function() {
            apply('list');
            try {
                localStorage.setItem('berita_view_mode', 'list');
            } catch (e) {}
        });
    })();
</script>
<?= $this->endSection() ?>