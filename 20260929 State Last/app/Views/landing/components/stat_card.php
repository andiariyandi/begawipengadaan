<?php
$icon = $icon ?? 'solar:chart-square-linear';
$value = $value ?? '0';
$label = $label ?? 'Statistik';
$description = $description ?? null;
$variant = $variant ?? 'primary';
$colorClass = $variant === 'success' ? 'text-success bg-success bg-opacity-10' : ($variant === 'warning' ? 'text-warning bg-warning bg-opacity-10' : ($variant === 'danger' ? 'text-danger bg-danger bg-opacity-10' : 'text-primary bg-primary bg-opacity-10'));
?>
<div class="landing-card h-100 p-20">
    <span class="d-inline-flex align-items-center justify-content-center rounded-3 <?= esc($colorClass) ?>" style="width: 48px; height: 48px;"><iconify-icon icon="<?= esc($icon) ?>" class="text-2xl"></iconify-icon></span>
    <h3 class="font-heading fw-bold mt-20 mb-4"><?= esc((string) $value) ?></h3>
    <p class="fw-semibold mb-4"><?= esc($label) ?></p>
    <?php if ($description): ?><p class="text-muted-landing small mb-0"><?= esc($description) ?></p><?php endif; ?>
</div>
