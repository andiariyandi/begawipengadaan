<?= $this->extend('landing/layouts/public') ?>
<?= $this->section('content') ?>
<?= view('landing/components/page_header', ['page_title' => $pageTitle]) ?>
<section class="category-one knowledge-catalog"><div class="category-one__bg-shape" style="background-image:url(<?= base_url('assets/main/images/shapes/category-one-bg-shape.png') ?>)"></div><div class="container">
<div class="section-title text-center"><div class="section-title__tagline-box"><div class="section-title__tagline-shape"></div><p class="section-title__tagline">Data Terbuka PBJ</p></div><h2 class="section-title__title"><?= esc($pageTitle) ?></h2><p class="learning-catalog__intro"><?= esc($description) ?></p></div>
<div class="row"><?php foreach ([['500+','Materi pembelajaran'],['32','Kelas aktif'],['20+','Pengajar dan praktisi'],['1.240','Peserta terdaftar']] as $stat): ?><div class="col-xl-3 col-lg-6 col-md-6"><article class="category-one__single"><h3 class="category-one__title" style="font-size:36px;color:var(--begawi-primary)"><?= $stat[0] ?></h3><p class="category-one__text"><?= $stat[1] ?></p></article></div><?php endforeach; ?></div>
</div></section><?= $this->endSection() ?>
