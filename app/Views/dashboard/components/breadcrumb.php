<?php $items = $breadcrumbs ?? []; ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? $title ?? 'Dashboard') ?></h6>
        <p class="text-secondary-light mb-0">Manajemen data BegawiPBJ.</p>
    </div>
    <?php if ($items !== []): ?>
        <ul class="d-flex align-items-center gap-2 mb-0">
            <?php foreach ($items as $index => $item): ?>
                <li class="fw-medium">
                    <?php if (!empty($item['url'])): ?>
                        <a href="<?= esc($item['url']) ?>" class="hover-text-primary"><?= esc($item['label']) ?></a>
                    <?php else: ?>
                        <?= esc($item['label']) ?>
                    <?php endif; ?>
                </li>
                <?php if ($index < count($items) - 1): ?><li>-</li><?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
