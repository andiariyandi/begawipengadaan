<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>

<?= view('landing/components/page_header', ['page_title' => $pageTitle]) ?>

<?php
$item = $item ?? null;
$bookmarkType = $bookmarkType ?? null;
$catalogType = $catalogType ?? '';
$fileMeta = $fileMeta ?? null;
$related = $related ?? [];
?>

<?php if ($msg = session()->getFlashdata('success')): ?>
    <div class="alert alert-success radius-8 mb-3 container"><?= esc($msg) ?></div>
<?php endif; ?>
<?php if ($msg = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-8 mb-3 container"><?= esc($msg) ?></div>
<?php endif; ?>

<section class="about-three learning-detail">
    <div class="container">
        <div class="row">
            <div class="col-xl-8">
                <div class="about-three__right">
                    <div class="section-title-two text-left">
                        <div class="section-title-two__tagline-box"><span class="section-title-two__tagline">Bunga Rampai BegawiPBJ</span></div>
                        <h2 class="section-title-two__title"><?= esc($itemTitle) ?></h2>
                    </div>
                    <?php if (! empty($item['summary'])): ?>
                        <p class="learning-detail__lead"><?= nl2br(esc($item['summary'])) ?></p>
                    <?php endif; ?>
                    <?php if (! empty($item['content'])): ?>
                        <div class="about-three__text-1 knowledge-detail__content">
                            <?= nl2br(esc($item['content'])) ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Konten belum tersedia.</p>
                    <?php endif; ?>

                    <?php if ($catalogType === 'templates'): ?>
                        <div class="mt-24 d-flex gap-12 flex-wrap">
                            <?php if (! empty($fileMeta)): ?>
                                <a href="<?= route_to('public.knowledge.templates.download', $item['slug']) ?>" class="thm-btn"><span class="icon-download"></span> Unduh Template</a>
                            <?php else: ?>
                                <span class="thm-btn thm-btn--disabled" aria-disabled="true"><span class="icon-info"></span> Lampiran belum tersedia</span>
                            <?php endif; ?>
                            <a href="<?= route_to('public.knowledge.articles') ?>" class="thm-btn thm-btn--outline"><span class="icon-angles-right"></span> Kembali ke Bunga Rampai</a>
                            <?php if ($bookmarkType !== null): ?>
                                <?= view('landing/components/bookmark_button', ['type' => $bookmarkType, 'id' => (int) $item['id'], 'title' => $item['title'] ?? null]) ?>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="mt-24 d-flex gap-12 flex-wrap">
                            <a href="<?= route_to('public.knowledge.index') ?>" class="thm-btn"><span class="icon-angles-right"></span> Kembali ke Bunga Rampai</a>
                            <?php if ($bookmarkType !== null): ?>
                                <?= view('landing/components/bookmark_button', ['type' => $bookmarkType, 'id' => (int) $item['id'], 'title' => $item['title'] ?? null]) ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="learning-detail__card">
                    <h3>Informasi Materi</h3>
                    <p>Jenis</p>
                    <strong><?= esc([
                        'articles'   => 'Artikel',
                        'cases'      => 'Studi Kasus',
                        'templates'  => 'Template Dokumen',
                        'faqs'       => 'FAQ',
                        'media'      => 'Media',
                        'videos'     => 'Video',
                        'gallery'    => 'Galeri',
                    ][$catalogType] ?? ucfirst($catalogType)) ?></strong>
                    <p>Status</p>
                    <strong>Publik</strong>
                    <?php if (! empty($item['author_name'])): ?>
                        <p>Penulis</p><strong><?= esc($item['author_name']) ?></strong>
                    <?php endif; ?>
                    <?php if (! empty($item['published_at'])): ?>
                        <p>Dipublikasikan</p><strong><?= esc(date('d M Y', strtotime($item['published_at']))) ?></strong>
                    <?php endif; ?>
                    <?php if (! empty($fileMeta)): ?>
                        <p>Berkas</p>
                        <strong><?= esc($fileMeta['original_name']) ?></strong>
                        <small class="d-block text-muted"><?= esc(strtoupper($fileMeta['extension'])) ?> · <?= number_format((float) $fileMeta['size_bytes'] / 1024, 1) ?> KB</small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (! empty($related)): ?>
<section class="related-one">
    <div class="container">
        <div class="section-title text-center">
            <div class="section-title__tagline-box"><div class="section-title__tagline-shape"></div><p class="section-title__tagline">Konten Terkait</p></div>
            <h2 class="section-title__title">Materi Serupa</h2>
        </div>
        <div class="row">
            <?php foreach ($related as $rel):
                $relRoute = match ($rel['type']) {
                    'article'    => 'public.knowledge.article.detail',
                    'case_study' => 'public.knowledge.case-study.detail',
                    'template'   => 'public.knowledge.templates.detail',
                    default      => 'public.knowledge.index',
                };
            ?>
                <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                    <article class="blog-two__single h-100">
                        <div class="blog-two__content">
                            <h4 class="blog-two__title">
                                <a href="<?= esc(route_to($relRoute, $rel['slug'])) ?>"><?= esc($rel['title']) ?></a>
                            </h4>
                            <p class="blog-two__text"><?= esc($rel['summary'] ?? '') ?></p>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?= $this->endSection() ?>