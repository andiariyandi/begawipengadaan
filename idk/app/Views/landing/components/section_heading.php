<?php
$eyebrow = $eyebrow ?? null;
$title = $title ?? '';
$description = $description ?? null;
$alignment = $alignment ?? 'start';
$actionUrl = $actionUrl ?? null;
$actionLabel = $actionLabel ?? 'Lihat Semua';
$alignmentClass = $alignment === 'center' ? 'text-center mx-auto' : '';
?>
<div class="<?= esc($alignmentClass) ?> mb-32" <?= $alignment === 'center' ? 'style="max-width: 720px;"' : '' ?>>
    <?php if ($eyebrow): ?><span class="d-inline-flex align-items-center rounded-pill bg-primary bg-opacity-10 text-primary fw-semibold small px-12 py-7 mb-10"><?= esc($eyebrow) ?></span><?php endif; ?>
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-16 <?= $alignment === 'center' ? 'justify-content-center' : '' ?>">
        <div>
            <h2 class="font-heading fw-bold mb-10"><?= esc($title) ?></h2>
            <?php if ($description): ?><p class="text-muted-landing mb-0"><?= esc($description) ?></p><?php endif; ?>
        </div>
        <?php if ($actionUrl): ?><a href="<?= esc($actionUrl) ?>" class="btn btn-landing-outline rounded-pill px-16 py-9"><?= esc($actionLabel) ?><iconify-icon icon="solar:arrow-right-linear" class="ms-6"></iconify-icon></a><?php endif; ?>
    </div>
</div>
