<?php
/**
 * Standard WowDash Empty State Component
 *
 * Parameters:
 * @var string|null $title       Main headline (default: 'Tidak Ada Data')
 * @var string|null $message     Helpful descriptive message
 * @var string|null $icon        Iconify icon name (default: 'solar:box-minimalistic-outline')
 * @var string|null $actionUrl   Optional CTA button link
 * @var string|null $actionLabel Optional CTA button label
 * @var string|null $actionIcon  Optional CTA button icon
 */
$title       = $title ?? 'Tidak Ada Data';
$message     = $message ?? 'Belum ada data untuk ditampilkan pada kriteria ini.';
$icon        = $icon ?? 'solar:box-minimalistic-outline';
$actionUrl   = $actionUrl ?? null;
$actionLabel = $actionLabel ?? ($actionText ?? null);
$actionIcon  = $actionIcon ?? 'solar:add-circle-outline';
?>
<div class="text-center py-40 px-24">
    <div class="w-64-px h-64-px bg-neutral-100 text-secondary-light rounded-circle d-inline-flex align-items-center justify-content-center mb-16">
        <iconify-icon icon="<?= esc($icon) ?>" class="text-3xl"></iconify-icon>
    </div>
    <h6 class="text-md fw-semibold text-primary-light mb-8"><?= esc($title) ?></h6>
    <p class="text-secondary-light text-sm mb-0 mx-auto" style="max-width: 440px;"><?= esc($message) ?></p>
    <?php if ($actionUrl && $actionLabel): ?>
        <div class="mt-16">
            <a href="<?= esc($actionUrl) ?>" class="btn btn-primary-600 radius-8 px-20 py-9 text-sm d-inline-flex align-items-center gap-2">
                <iconify-icon icon="<?= esc($actionIcon) ?>" class="text-lg"></iconify-icon>
                <span><?= esc($actionLabel) ?></span>
            </a>
        </div>
    <?php endif; ?>
</div>
