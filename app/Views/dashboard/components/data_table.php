<div class="card border radius-12">
    <div class="card-header border-bottom bg-transparent py-16 px-24 d-flex align-items-center justify-content-between">
        <h6 class="card-title mb-0"><?= esc($tableTitle ?? 'Data Tabel') ?></h6>
        <?php if (! empty($tableAction)): ?>
            <?= $tableAction ?>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 <?= esc($tableClass ?? '') ?>">
                <?= $slot ?? '' ?>
            </table>
        </div>
    </div>
</div>
