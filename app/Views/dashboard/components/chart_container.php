<div class="card border radius-12">
    <div class="card-body p-24">
        <div class="d-flex align-items-center justify-content-between mb-16">
            <h6 class="text-lg fw-semibold mb-0"><?= esc($chartTitle ?? 'Grafik Statistik') ?></h6>
            <?php if (! empty($chartAction)): ?>
                <?= $chartAction ?>
            <?php endif; ?>
        </div>
        <div id="<?= esc($chartId ?? 'chartContainer') ?>" style="min-height: <?= esc($chartHeight ?? '300px') ?>;"></div>
    </div>
</div>
