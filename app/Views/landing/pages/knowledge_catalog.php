<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>

<?= view('landing/components/page_header', ['page_title' => $pageTitle]) ?>

<?php
$items       = $items ?? [];
$catalogType = $catalogType ?? '';
$bookmarkType = $bookmarkType ?? null;
$detailSlugRoute = [
    'articles'   => 'public.knowledge.article.detail',
    'cases'      => 'public.knowledge.case-study.detail',
    'templates'  => 'public.knowledge.templates.detail',
    'faqs'       => null,
    'media'      => null,
    'videos'     => null,
    'gallery'    => null,
];
$detailRoute = $detailSlugRoute[$catalogType] ?? null;
?>

<section class="category-one knowledge-catalog">
    <div class="category-one__bg-shape" style="background-image: url(<?= base_url('assets/main/images/shapes/category-one-bg-shape.png') ?>);"></div>
    <div class="container">
        <div class="section-title text-center">
            <div class="section-title__tagline-box"><div class="section-title__tagline-shape"></div><p class="section-title__tagline">Bunga Rampai BegawiPBJ</p></div>
            <h2 class="section-title__title"><?= esc($pageTitle) ?></h2>
            <p class="learning-catalog__intro"><?= esc($description) ?></p>
        </div>

        <?php if ($msg = session()->getFlashdata('success')): ?>
            <div class="alert alert-success radius-8 mb-3"><?= esc($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = session()->getFlashdata('error')): ?>
            <div class="alert alert-danger radius-8 mb-3"><?= esc($msg) ?></div>
        <?php endif; ?>

        <?php if (empty($items)): ?>
            <div class="text-center py-5"><p class="text-muted mb-0">Belum ada konten pada kategori ini.</p></div>
        <?php else: ?>

            <?php if ($catalogType === 'faqs'): ?>
                <div class="row knowledge-faq-list">
                    <?php foreach ($items as $item): ?>
                        <div class="col-xl-6">
                            <details class="knowledge-faq">
                                <summary><?= esc($item['title']) ?><span class="icon-plus"></span></summary>
                                <p><?= nl2br(esc($item['content'] ?? $item['summary'] ?? 'Belum ada jawaban.')) ?></p>
                                <?php if ($bookmarkType !== null): ?>
                                    <div class="mt-2"><?= view('landing/components/bookmark_button', ['type' => $bookmarkType, 'id' => (int) $item['id'], 'title' => $item['title'] ?? null, 'size' => 'sm']) ?></div>
                                <?php endif; ?>
                            </details>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php elseif ($catalogType === 'gallery'): ?>
                <div class="row gallery-page knowledge-gallery">
                    <?php foreach ($items as $index => $item): ?>
                        <?php
                            $thumb = ! empty($item['file_id']) && ! empty($item['file_original_name'])
                                ? base_url('assets/main/images/gallery/gallery-page-1-' . (($index % 4) + 1) . '.jpg')
                                : base_url('assets/main/images/gallery/gallery-page-1-' . (($index % 4) + 1) . '.jpg');
                        ?>
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="gallery-page__single">
                                <div class="gallery-page__img">
                                    <div class="gallery-page__img-box"><img src="<?= esc($thumb) ?>" alt="<?= esc($item['title'] ?? 'Galeri') ?>"></div>
                                    <div class="gallery-page__icon"><span class="icon-plus"></span></div>
                                </div>
                                <h3 class="knowledge-gallery__title"><?= esc($item['title'] ?? '(Tanpa judul)') ?></h3>
                                <?php if (! empty($item['caption'])): ?>
                                    <p class="small text-muted mb-2"><?= esc($item['caption']) ?></p>
                                <?php endif; ?>
                                <?php if ($bookmarkType !== null): ?>
                                    <?= view('landing/components/bookmark_button', ['type' => $bookmarkType, 'id' => (int) $item['id'], 'title' => $item['title'] ?? null, 'size' => 'sm']) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php elseif ($catalogType === 'articles' || $catalogType === 'cases'): ?>
                <div class="row blog-page knowledge-blog">
                    <?php foreach ($items as $index => $item): ?>
                        <?php $url = $detailRoute !== null ? route_to($detailRoute, $item['slug']) : '#'; ?>
                        <div class="col-xl-6 col-lg-6">
                            <article class="blog-two__single">
                                <div class="blog-two__img"><img src="<?= base_url('assets/main/images/blog/blog-2-' . (($index % 4) + 1) . '.jpg') ?>" alt="<?= esc($item['title']) ?>"></div>
                                <div class="blog-two__content">
                                    <ul class="blog-two__meta list-unstyled">
                                        <li><span class="icon-tags"></span> <?= esc($catalogType === 'cases' ? 'Studi Kasus' : 'Pengetahuan PBJ') ?></li>
                                        <?php if (! empty($item['published_at'])): ?>
                                            <li><span class="icon-clock"></span> <?= esc(date('d M Y', strtotime($item['published_at']))) ?></li>
                                        <?php endif; ?>
                                    </ul>
                                    <h4 class="blog-two__title"><a href="<?= esc($url) ?>"><?= esc($item['title']) ?></a></h4>
                                    <p class="blog-two__text"><?= esc($item['summary'] ?? '') ?></p>
                                    <a class="thm-btn" href="<?= esc($url) ?>"><span class="icon-angles-right"></span> <?= esc($catalogType === 'cases' ? 'Baca Studi Kasus' : 'Baca Artikel') ?></a>
                                    <?php if ($bookmarkType !== null): ?>
                                        <?= view('landing/components/bookmark_button', ['type' => $bookmarkType, 'id' => (int) $item['id'], 'title' => $item['title'] ?? null, 'size' => 'sm']) ?>
                                    <?php endif; ?>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php elseif ($catalogType === 'templates'): ?>
                <div class="knowledge-document-list">
                    <?php foreach ($items as $item): ?>
                        <article class="knowledge-document">
                            <a class="knowledge-document__icon" href="<?= route_to('public.knowledge.templates.download', $item['slug']) ?>" aria-label="Unduh <?= esc($item['title']) ?>"><span class="icon-file"></span></a>
                            <div>
                                <h3><a href="<?= route_to('public.knowledge.templates.detail', $item['slug']) ?>"><?= esc($item['title']) ?></a></h3>
                                <p><?= esc($item['summary'] ?? '') ?></p>
                                <?php if (! empty($item['cover_file_id'])): ?>
                                    <small><span class="icon-file"></span> Lampiran tersedia</small>
                                <?php else: ?>
                                    <small>Belum ada lampiran</small>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex gap-8 flex-wrap">
                                <a class="thm-btn" href="<?= route_to('public.knowledge.templates.download', $item['slug']) ?>"><span class="icon-download"></span> Unduh</a>
                                <a class="thm-btn thm-btn--outline" href="<?= route_to('public.knowledge.templates.detail', $item['slug']) ?>"><span class="icon-eye"></span> Detail</a>
                                <?php if ($bookmarkType !== null): ?>
                                    <?= view('landing/components/bookmark_button', ['type' => $bookmarkType, 'id' => (int) $item['id'], 'title' => $item['title'] ?? null, 'size' => 'sm']) ?>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

            <?php elseif ($catalogType === 'media' || $catalogType === 'videos'): ?>
                <div class="row">
                    <?php foreach ($items as $item):
                        $type = (string) ($item['type'] ?? '');
                        $isVideo = $type === 'youtube' || $type === 'video_file';
                        $icon = $isVideo ? 'video' : 'gallery';
                        $url = ! empty($item['external_url']) ? $item['external_url'] : '#';
                    ?>
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <article class="category-one__single">
                                <div class="category-one__icon"><iconify-icon icon="solar:<?= esc($icon) ?>-outline" class="text-4xl text-primary-600"></iconify-icon></div>
                                <h3 class="category-one__title"><?= esc($item['title'] ?? '(Tanpa judul)') ?></h3>
                                <p class="category-one__text"><?= esc($item['caption'] ?? '') ?></p>
                                <?php if (! empty($item['external_url']) && $isVideo): ?>
                                    <a class="category-one__btn" href="<?= esc($url) ?>" target="_blank" rel="noopener"><span class="icon-play"></span> Tonton</a>
                                <?php endif; ?>
                                <?php if ($bookmarkType !== null): ?>
                                    <?= view('landing/components/bookmark_button', ['type' => $bookmarkType, 'id' => (int) $item['id'], 'title' => $item['title'] ?? null, 'size' => 'sm']) ?>
                                <?php endif; ?>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <div class="row">
                    <?php foreach ($items as $item): ?>
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <article class="category-one__single">
                                <div class="category-one__icon"><img src="<?= base_url('assets/main/images/icon/why-choose-one-icon-1.png') ?>" alt=""></div>
                                <h3 class="category-one__title"><?= esc($item['title'] ?? '(Tanpa judul)') ?></h3>
                                <p class="category-one__text"><?= esc($item['caption'] ?? $item['summary'] ?? '') ?></p>
                                <?php if ($bookmarkType !== null): ?>
                                    <?= view('landing/components/bookmark_button', ['type' => $bookmarkType, 'id' => (int) $item['id'], 'title' => $item['title'] ?? null, 'size' => 'sm']) ?>
                                <?php endif; ?>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>