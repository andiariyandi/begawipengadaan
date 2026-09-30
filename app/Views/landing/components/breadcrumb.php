<?php $items = $breadcrumbs ?? []; ?>
<?php if ($items !== []): ?>
<section class="page-header">
    <div class="page-header__bg" style="background-image: url(<?= base_url('assets/main/images/shapes/page-header-bg-shape.png') ?>);"></div>
    <div class="page-header__shape-1 float-bob-x"><img src="<?= base_url('assets/main/images/shapes/page-header-shape-1.png') ?>" alt=""></div>
    <div class="page-header__shape-2 float-bob-y"><img src="<?= base_url('assets/main/images/shapes/page-header-shape-2.png') ?>" alt=""></div>
    <div class="container"><div class="page-header__inner">
        <?php $last = end($items); $lastLabel = is_array($last) ? ($last['label'] ?? '') : (string) $last; ?>
        <h2><?= esc($lastLabel) ?></h2>
        <div class="thm-breadcrumb__box"><ul class="thm-breadcrumb list-unstyled"><li><a href="<?= site_url('/') ?>">Beranda</a></li><?php foreach ($items as $item): ?><?php $label = is_array($item) ? ($item['label'] ?? '') : (string) $item; ?><?php $url = is_array($item) ? ($item['url'] ?? null) : null; ?><li><?php if ($url): ?><a href="<?= esc($url) ?>"><?= esc($label) ?></a><?php else: ?><?= esc($label) ?><?php endif; ?></li><?php endforeach; ?></ul></div>
    </div></div>
</section>
<?php endif; ?>
