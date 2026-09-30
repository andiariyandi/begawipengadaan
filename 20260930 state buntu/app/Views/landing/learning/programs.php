<?= $this->extend('landing/layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-header"><div class="page-header__bg" style="background-image: url(<?= base_url('assets/main/images/backgrounds/page-header-bg.jpg') ?>);"></div><div class="container"><div class="page-header__inner"><h2>Program Pembelajaran</h2><ul class="thm-breadcrumb list-unstyled"><li><a href="<?= route_to('landing.home') ?>">Beranda</a></li><li><span class="icon-angle-right"></span></li><li><a href="<?= route_to('landing.learning.index') ?>">Arena Belajar</a></li><li><span class="icon-angle-right"></span></li><li>Program</li></ul></div></div></section>
<section class="courses-page"><div class="container"><div class="row">
<?php foreach ($programs as $index => $program): ?>
<div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="<?= ($index + 1) * 100 ?>ms"><div class="courses-one__single"><div class="courses-one__img"><img src="<?= base_url($program['image']) ?>" alt="<?= esc($program['title']) ?>"><div class="courses-one__price"><p><?= esc($program['level']) ?></p></div></div><div class="courses-one__content"><h3 class="courses-one__title"><a href="<?= route_to('landing.learning.program.show', $program['slug']) ?>"><?= esc($program['title']) ?></a></h3><p class="courses-one__text"><?= esc($program['summary']) ?></p><div class="courses-one__bottom"><div class="courses-one__students"><span class="icon-book"></span><p><?= esc((string) $program['courses']) ?> Course</p></div><div class="courses-one__btn"><a href="<?= route_to('landing.learning.program.show', $program['slug']) ?>">Lihat <span class="icon-right-arrow"></span></a></div></div></div></div></div>
<?php endforeach; ?>
</div></div></section>
<?= $this->endSection() ?>