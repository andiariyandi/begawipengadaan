<div class="approval-timeline">
    <?php if (! empty($steps)): ?>
        <ul class="list-unstyled mb-0">
            <?php foreach ($steps as $step): ?>
                <li class="d-flex gap-16 mb-20 position-relative">
                    <span class="w-32-px h-32-px radius-circle bg-<?= esc($step['badge'] ?? 'primary') ?>-50 text-<?= esc($step['badge'] ?? 'primary') ?>-600 d-flex align-items-center justify-content-center flex-shrink-0">
                        <iconify-icon icon="<?= esc($step['icon'] ?? 'solar:check-circle-outline') ?>"></iconify-icon>
                    </span>
                    <div>
                        <h6 class="text-md fw-medium mb-4"><?= esc($step['title']) ?></h6>
                        <p class="text-sm text-secondary-light mb-4"><?= esc($step['desc'] ?? '') ?></p>
                        <small class="text-xs text-muted"><?= esc($step['time'] ?? '') ?></small>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted text-sm mb-0">Belum ada riwayat persetujuan.</p>
    <?php endif; ?>
</div>
