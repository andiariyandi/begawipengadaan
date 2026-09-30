<?= $this->extend('landing/layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-header"><div class="page-header__bg" style="background-image: url(<?= base_url('assets/main/images/backgrounds/page-header-bg.jpg') ?>);"></div><div class="container"><div class="page-header__inner"><h2>Instruktur</h2><ul class="thm-breadcrumb list-unstyled"><li><a href="<?= route_to('landing.home') ?>">Beranda</a></li><li><span class="icon-angle-right"></span></li><li><a href="<?= route_to('landing.learning.index') ?>">Arena Belajar</a></li><li><span class="icon-angle-right"></span></li><li>Instruktur</li></ul></div></div></section>
<section class="team-page"><div class="container"><div class="row">
<?php foreach ($instructors as $index => $instructor): ?>
<div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="<?= (($index % 3) + 1) * 100 ?>ms"><div class="team-one__single"><div class="team-one__img"><img src="<?= base_url($instructor['image']) ?>" alt="<?= esc($instructor['name']) ?>"><div class="team-one__social"><a href="<?= route_to('landing.learning.instructors.show', $instructor['slug']) ?>"><span class="icon-plus"></span></a></div></div><div class="team-one__content"><h3 class="team-one__title"><a href="<?= route_to('landing.learning.instructors.show', $instructor['slug']) ?>"><?= esc($instructor['name']) ?></a></h3><p class="team-one__sub-title"><?= esc($instructor['role']) ?></p></div></div></div>
<?php endforeach; ?>
</div></div></section>
<?= $this->endSection() ?>