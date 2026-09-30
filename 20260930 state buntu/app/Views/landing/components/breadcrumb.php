<?php $breadcrumbs = $breadcrumbs ?? []; ?>
<nav class="page-header__breadcrumb" aria-label="Breadcrumb">
    <ol class="list-unstyled d-flex align-items-center justify-content-center">
        <li><a href="<?= site_url('/') ?>">Beranda</a></li>
        <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
            <?php $isLast = $index === array_key_last($breadcrumbs); ?>
            <li aria-current="<?= $isLast ? 'page' : 'false' ?>">
                <?php if (! $isLast && ! empty($breadcrumb['url'])): ?>
                    <a href="<?= esc($breadcrumb['url']) ?>"><?= esc($breadcrumb['label']) ?></a>
                <?php else: ?>
                    <?= esc($breadcrumb['label']) ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>