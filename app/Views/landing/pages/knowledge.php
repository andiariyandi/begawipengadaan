<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>

<?= view('landing/components/page_header', ['page_title' => 'Bunga Rampai']) ?>

<?php
$featured    = $featured ?? [];
$articles    = $articles ?? [];
$caseStudies = $caseStudies ?? [];
$mediaItems  = $mediaItems ?? [];
?>

<?php if ($msg = session()->getFlashdata('success')): ?>
    <div class="alert alert-success radius-8 mb-3 container"><?= esc($msg) ?></div>
<?php endif; ?>
<?php if ($msg = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-8 mb-3 container"><?= esc($msg) ?></div>
<?php endif; ?>

<section class="category-one knowledge-home">
    <div class="category-one__bg-shape" style="background-image: url(<?= base_url('assets/main/images/shapes/category-one-bg-shape.png') ?>);"></div>
    <div class="container">
        <div class="section-title text-center">
            <div class="section-title__tagline-box"><div class="section-title__tagline-shape"></div><p class="section-title__tagline">Pengetahuan PBJ Lampung</p></div>
            <h2 class="section-title__title">Temukan Wawasan dan Referensi</h2>
            <p class="text-muted">Artikel, studi kasus, FAQ, template, dan media untuk mendukung proses PBJ.</p>
        </div>
        <div class="row">
            <?php foreach ([
                ['title' => 'Artikel PBJ', 'text' => 'Wawasan, regulasi, dan praktik baik pengadaan.', 'url' => 'bunga-rampai/artikel', 'icon' => 'why-choose-one-icon-1.png'],
                ['title' => 'Tanya Jawab PBJ', 'text' => 'Jawaban singkat untuk pertanyaan yang sering muncul.', 'url' => 'bunga-rampai/faq', 'icon' => 'why-choose-one-icon-2.png'],
                ['title' => 'Studi Kasus', 'text' => 'Belajar dari pengalaman dan tantangan di lapangan.', 'url' => 'bunga-rampai/studi-kasus', 'icon' => 'why-choose-one-icon-3.png'],
                ['title' => 'Template Dokumen', 'text' => 'Contoh dokumen pendukung proses PBJ.', 'url' => 'bunga-rampai/template-dokumen', 'icon' => 'why-choose-one-icon-4.png'],
                ['title' => 'Media PBJ', 'text' => 'Infografik dan materi visual yang mudah dipahami.', 'url' => 'bunga-rampai/media', 'icon' => 'why-choose-one-icon-1.png'],
                ['title' => 'Video PBJ', 'text' => 'Sajian video pembelajaran dan diskusi.', 'url' => 'bunga-rampai/video', 'icon' => 'why-choose-one-icon-2.png'],
                ['title' => 'Galeri Kegiatan', 'text' => 'Dokumentasi kolaborasi ekosistem PBJ Lampung.', 'url' => 'bunga-rampai/galeri', 'icon' => 'why-choose-one-icon-3.png'],
            ] as $item): ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <article class="category-one__single">
                        <div class="category-one__icon"><img src="<?= base_url('assets/main/images/icon/' . $item['icon']) ?>" alt=""></div>
                        <h3 class="category-one__title"><a href="<?= site_url($item['url']) ?>"><?= esc($item['title']) ?></a></h3>
                        <p class="category-one__text"><?= esc($item['text']) ?></p>
                        <a class="category-one__btn" href="<?= site_url($item['url']) ?>"><span class="icon-angles-right"></span> Jelajahi</a>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (! empty($featured)): ?>
<section class="featured-one knowledge-featured">
    <div class="container">
        <div class="section-title text-left">
            <div class="section-title__tagline-box"><div class="section-title__tagline-shape"></div><p class="section-title__tagline">Konten Unggulan</p></div>
            <h2 class="section-title__title">Pilihan Editor</h2>
        </div>
        <div class="row">
            <?php foreach ($featured as $feat):
                $fRoute = match ($feat['type']) {
                    'article'    => 'public.knowledge.article.detail',
                    'case_study' => 'public.knowledge.case-study.detail',
                    'template'   => 'public.knowledge.templates.detail',
                    default      => 'public.knowledge.index',
                };
                $fUrl = route_to($fRoute, $feat['slug']);
            ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <article class="blog-two__single">
                        <div class="blog-two__img"><img src="<?= base_url('assets/main/images/blog/blog-2-' . (((int) $feat['id']) % 4 + 1) . '.jpg') ?>" alt="<?= esc($feat['title']) ?>"></div>
                        <div class="blog-two__content">
                            <h4 class="blog-two__title"><a href="<?= esc($fUrl) ?>"><?= esc($feat['title']) ?></a></h4>
                            <p class="blog-two__text"><?= esc($feat['summary'] ?? '') ?></p>
                            <a class="thm-btn thm-btn--sm" href="<?= esc($fUrl) ?>"><span class="icon-angles-right"></span> Buka</a>
                            <?= view('landing/components/bookmark_button', ['type' => 'knowledge_article', 'id' => (int) $feat['id'], 'title' => $feat['title'] ?? null, 'size' => 'sm']) ?>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (! empty($articles)): ?>
<section class="knowledge-latest">
    <div class="container">
        <div class="section-title d-flex justify-content-between align-items-end flex-wrap">
            <div>
                <div class="section-title__tagline-box"><div class="section-title__tagline-shape"></div><p class="section-title__tagline">Artikel Terbaru</p></div>
                <h2 class="section-title__title">Update Terbaru</h2>
            </div>
            <a class="thm-btn" href="<?= route_to('public.knowledge.articles') ?>"><span>Lihat semua</span><i class="icon-angles-right"></i></a>
        </div>
        <div class="row">
            <?php foreach (array_slice($articles, 0, 3) as $item): ?>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <article class="blog-two__single h-100">
                        <div class="blog-two__img"><img src="<?= base_url('assets/main/images/blog/blog-2-' . (((int) $item['id']) % 4 + 1) . '.jpg') ?>" alt=""></div>
                        <div class="blog-two__content">
                            <h4 class="blog-two__title"><a href="<?= route_to('public.knowledge.article.detail', $item['slug']) ?>"><?= esc($item['title']) ?></a></h4>
                            <p class="blog-two__text"><?= esc($item['summary'] ?? '') ?></p>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (! empty($caseStudies)): ?>
<section class="knowledge-latest knowledge-cases">
    <div class="container">
        <div class="section-title d-flex justify-content-between align-items-end flex-wrap">
            <div>
                <div class="section-title__tagline-box"><div class="section-title__tagline-shape"></div><p class="section-title__tagline">Studi Kasus</p></div>
                <h2 class="section-title__title">Pelajaran dari Lapangan</h2>
            </div>
            <a class="thm-btn" href="<?= route_to('public.knowledge.case-studies') ?>"><span>Lihat semua</span><i class="icon-angles-right"></i></a>
        </div>
        <div class="row">
            <?php foreach (array_slice($caseStudies, 0, 3) as $item): ?>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <article class="blog-two__single h-100">
                        <div class="blog-two__img"><img src="<?= base_url('assets/main/images/blog/blog-2-' . (((int) $item['id']) % 4 + 1) . '.jpg') ?>" alt=""></div>
                        <div class="blog-two__content">
                            <h4 class="blog-two__title"><a href="<?= route_to('public.knowledge.case-study.detail', $item['slug']) ?>"><?= esc($item['title']) ?></a></h4>
                            <p class="blog-two__text"><?= esc($item['summary'] ?? '') ?></p>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (! empty($mediaItems)): ?>
<section class="knowledge-media-latest">
    <div class="container">
        <div class="section-title d-flex justify-content-between align-items-end flex-wrap">
            <div>
                <div class="section-title__tagline-box"><div class="section-title__tagline-shape"></div><p class="section-title__tagline">Media &amp; Video</p></div>
                <h2 class="section-title__title">Konten Visual Terbaru</h2>
            </div>
            <a class="thm-btn" href="<?= route_to('public.knowledge.media') ?>"><span>Lihat semua</span><i class="icon-angles-right"></i></a>
        </div>
        <div class="row">
            <?php foreach (array_slice($mediaItems, 0, 6) as $item):
                $type = (string) ($item['type'] ?? '');
                $isVideo = $type === 'youtube' || $type === 'video_file';
                $icon = $isVideo ? 'solar:video-outline' : 'solar:gallery-wide-outline';
            ?>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <article class="category-one__single">
                        <div class="category-one__icon"><iconify-icon icon="<?= esc($icon) ?>" class="text-4xl text-primary-600"></iconify-icon></div>
                        <h3 class="category-one__title"><?= esc($item['title'] ?? '(Tanpa judul)') ?></h3>
                        <p class="category-one__text"><?= esc($item['caption'] ?? '') ?></p>
                        <?php if (! empty($item['external_url']) && $isVideo): ?>
                            <a class="category-one__btn" href="<?= esc($item['external_url']) ?>" target="_blank" rel="noopener"><span class="icon-play"></span> Tonton</a>
                        <?php endif; ?>
                        <?= view('landing/components/bookmark_button', ['type' => 'knowledge_media', 'id' => (int) $item['id'], 'title' => $item['title'] ?? null, 'size' => 'sm']) ?>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?= $this->endSection() ?>