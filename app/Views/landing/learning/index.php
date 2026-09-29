<?= $this->extend('landing/layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-header">
    <div class="page-header__bg" style="background-image: url(<?= base_url('assets/main/images/backgrounds/page-header-bg.jpg') ?>);"></div>
    <div class="container"><div class="page-header__inner"><h2>Arena Belajar</h2><ul class="thm-breadcrumb list-unstyled"><li><a href="<?= route_to('landing.home') ?>">Beranda</a></li><li><span class="icon-angle-right"></span></li><li>Arena Belajar</li></ul></div></div>
</section>

<section class="courses-page">
    <div class="container">
        <div class="courses-page__top">
            <div class="section-title text-center">
                <div class="section-title__tagline-box"><span class="section-title__tagline">Arena Belajar BegawiPBJ</span></div>
                <h2 class="section-title__title">Program dan course pembelajaran PBJ</h2>
                <p class="courses-page__text">Akses materi pembelajaran, assessment, progres, dan aktivitas kelas memerlukan akun aktif serta enrollment yang sah.</p>
            </div>
        </div>
        <div class="row">
            <?php foreach ($courses as $index => $course): ?>
                <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="<?= (($index % 3) + 1) * 100 ?>ms">
                    <div class="courses-one__single">
                        <div class="courses-one__img">
                            <img src="<?= base_url($course['image']) ?>" alt="<?= esc($course['title']) ?>">
                            <div class="courses-one__price"><p><?= esc($course['level']) ?></p></div>
                        </div>
                        <div class="courses-one__content">
                            <div class="courses-one__rating"><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span></div>
                            <h3 class="courses-one__title"><a href="<?= route_to('landing.learning.courses.show', $course['slug']) ?>"><?= esc($course['title']) ?></a></h3>
                            <p class="courses-one__text"><?= esc($course['summary']) ?></p>
                            <div class="courses-one__bottom"><div class="courses-one__students"><span class="icon-clock"></span><p><?= esc($course['duration']) ?></p></div><div class="courses-one__btn"><a href="<?= route_to('landing.learning.courses.show', $course['slug']) ?>">Lihat <span class="icon-right-arrow"></span></a></div></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5"><a href="<?= route_to('landing.learning.courses.index') ?>" class="thm-btn">Lihat Semua Course <span class="icon-angles-right"></span></a></div>
    </div>
</section>

<section class="courses-page courses-page--classes">
    <div class="container">
        <div class="section-title text-center"><div class="section-title__tagline-box"><span class="section-title__tagline">Kelas Terjadwal</span></div><h2 class="section-title__title">Kelas yang tersedia</h2></div>
        <div class="row">
            <?php foreach ($classes as $index => $class): ?>
                <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="<?= ($index + 1) * 100 ?>ms"><div class="courses-one__single"><div class="courses-one__img"><img src="<?= base_url($class['image']) ?>" alt="<?= esc($class['name']) ?>"><div class="courses-one__price"><p><?= esc($class['status']) ?></p></div></div><div class="courses-one__content"><h3 class="courses-one__title"><a href="<?= route_to('landing.learning.classes.show', $class['slug']) ?>"><?= esc($class['name']) ?></a></h3><p class="courses-one__text"><?= esc($class['course']) ?></p><div class="courses-one__bottom"><div class="courses-one__students"><span class="icon-calendar"></span><p><?= esc($class['schedule']) ?></p></div><div class="courses-one__btn"><a href="<?= route_to('landing.learning.classes.show', $class['slug']) ?>">Lihat <span class="icon-right-arrow"></span></a></div></div></div></div></div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5"><a href="<?= route_to('landing.learning.classes.index') ?>" class="thm-btn">Lihat Semua Kelas <span class="icon-angles-right"></span></a></div>
    </div>
</section>
<?= $this->endSection() ?>