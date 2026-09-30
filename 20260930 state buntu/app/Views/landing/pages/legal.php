<?= $this->extend('landing/layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-header">
    <div class="page-header__bg" style="background-image: url(<?= base_url('assets/main/images/backgrounds/page-header-bg.jpg') ?>);"></div>
    <div class="container"><div class="page-header__inner"><h2><?= esc($heading) ?></h2><ul class="thm-breadcrumb list-unstyled"><li><a href="<?= route_to('landing.home') ?>">Beranda</a></li><li><span class="icon-angle-right"></span></li><li><?= esc($heading) ?></li></ul></div></div>
</section>

<section class="faq-page">
    <div class="container"><div class="row justify-content-center"><div class="col-xl-9"><div class="faq-page__left"><div class="section-title text-left"><div class="section-title__tagline-box"><span class="section-title__tagline">BegawiPBJ</span></div><h2 class="section-title__title"><?= esc($heading) ?></h2></div><p class="faq-page__text"><?= esc($lead) ?></p><div class="faq-page__accordion thm-accordion-box" data-grp-name="legal-accordion">
                <?php foreach ($sections as $index => $section): ?>
                    <div class="accrodion <?= $index === 0 ? 'active' : '' ?>">
                        <div class="accrodion-title"><h4><?= esc($section['title']) ?></h4></div>
                        <div class="accrodion-content"><div class="inner"><p><?= esc($section['body']) ?></p></div></div>
                    </div>
                <?php endforeach; ?>
            </div></div></div></div></div>
</section>
<?= $this->endSection() ?>