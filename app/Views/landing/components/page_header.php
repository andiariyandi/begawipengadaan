<?php
$title = $title ?? 'BegawiPBJ';
$breadcrumbs = $breadcrumbs ?? [['label' => $title]];
?>
<section class="page-header">
    <div class="page-header__bg" style="background-image: url('<?= base_url('assets/main/images/resources/page-header-img-1.png') ?>');"></div>
    <div class="page-header__shape-1"><img src="<?= base_url('assets/main/images/shapes/page-header-shape-1.png') ?>" alt=""></div>
    <div class="page-header__shape-2"><img src="<?= base_url('assets/main/images/shapes/page-header-shape-2.png') ?>" alt=""></div>
    <div class="page-header__shape-3"><img src="<?= base_url('assets/main/images/shapes/page-header-shape-3.png') ?>" alt=""></div>
    <div class="page-header__shape-4"><img src="<?= base_url('assets/main/images/shapes/page-header-shape-4.png') ?>" alt=""></div>
    <div class="page-header__shape-5"><img src="<?= base_url('assets/main/images/shapes/page-header-shape-5.png') ?>" alt=""></div>
    <div class="container">
        <div class="page-header__inner">
            <h1 class="page-header__title"><?= esc($title) ?></h1>
            <?= view('landing/components/breadcrumb', ['breadcrumbs' => $breadcrumbs]) ?>
        </div>
    </div>
</section>