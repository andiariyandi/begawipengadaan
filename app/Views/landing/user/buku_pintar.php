<?= $this->extend('landing/parts/layout/user') ?>
<?= $this->section('content') ?>
<section class="landing-user-page blog-one">
    <div class="section-title text-center">
        <p class="section-title__tagline">Pengetahuan PBJ</p>
        <h2 class="section-title__title">Buku Pintar</h2>
    </div>
    <?php if (empty($articles)): ?>
        <div class="landing-user-empty">
            <span class="icon-book landing-user-empty__icon"></span>
            <h3>Belum ada artikel tersimpan</h3>
            <p>Simpan artikel penting agar mudah Anda baca kembali.</p>
            <a class="thm-btn" href="<?= route_to('knowledge.articles.browse') ?>">Jelajahi Artikel</a>
        </div>
    <?php else: ?>
        <div class="row landing-card-grid">
            <?php foreach ($articles as $article): ?>
                <div class="col-xl-4 col-md-6 landing-card-column">
                    <article class="blog-one__single">
                        <div class="blog-one__content">
                            <p class="blog-one__meta"><span class="icon-tags"></span><?= esc($article['category_name']) ?> <span class="icon-calendar"></span><?= esc(date('d M Y', strtotime($article['bookmarked_at']))) ?></p>
                            <h3 class="blog-one__title"><a href="<?= route_to('knowledge.articles.detail', $article['id']) ?>"><?= esc($article['title']) ?></a></h3>
                            <a class="thm-btn-two" href="<?= route_to('knowledge.articles.detail', $article['id']) ?>">Baca Artikel <i class="icon-angles-right"></i></a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
