<?= $this->extend('landing/parts/layout/user') ?>
<?= $this->section('content') ?>

<section class="landing-user-page blog-one">

    <div class="section-title text-center">
        <p class="section-title__tagline">Referensi PBJ</p>
        <h2 class="section-title__title">Regulasi</h2>
    </div>

    <?php if (empty($regulations)): ?>

        <div class="landing-user-empty">
            <span class="icon-folder landing-user-empty__icon"></span>
            <h3>Belum ada regulasi tersimpan</h3>
            <p>Simpan regulasi penting agar mudah Anda baca kembali.</p>
            <a class="thm-btn" href="<?= route_to('regulation.browse') ?>">
                Jelajahi Regulasi
            </a>
        </div>

    <?php else: ?>

        <div class="row landing-card-grid">

            <?php foreach ($regulations as $reg): ?>

                <div class="col-xl-4 col-md-6 landing-card-column">

                    <article class="blog-one__single">

                        <div class="blog-one__content">

                            <p class="blog-one__meta">
                                <span class="icon-tags"></span>
                                <?= esc($reg['category_name'] ?? 'Regulasi') ?>

                                <span class="icon-calendar"></span>
                                <?= esc($reg['tahun'] ?? '-') ?>
                            </p>

                            <h3 class="blog-one__title">
                                <a href="<?= route_to('regulation.detail', $reg['id']) ?>">
                                    <?= esc($reg['title'] ?? 'Regulasi') ?>
                                </a>
                            </h3>

                            <a class="thm-btn-two" href="<?= route_to('regulation.detail', $reg['id']) ?>">
                                Lihat Detail
                                <i class="icon-angles-right"></i>
                            </a>

                        </div>

                    </article>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</section>

<?= $this->endSection() ?>