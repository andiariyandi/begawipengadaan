<?php $breadcrumbs = $breadcrumbs ?? []; ?>
<nav class="page__title-breadcrumb" aria-label="breadcrumb">
    <ol class="breadcrumb justify-content-center">
        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Beranda</a></li>
        <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
            <?php $isLast = $index === array_key_last($breadcrumbs); ?>
            <li class="breadcrumb-item <?= $isLast ? 'active' : '' ?>" <?= $isLast ? 'aria-current="page"' : '' ?>>
                <?php if (! $isLast && isset($breadcrumb['url'])): ?>
                    <a href="<?= esc($breadcrumb['url']) ?>"><?= esc($breadcrumb['label']) ?></a>
                <?php else: ?>
                    <?= esc($breadcrumb['label']) ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>