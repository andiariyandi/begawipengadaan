<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Ringkasan') ?></h6>
        <p class="text-secondary-light mb-0">Pratinjau UI BegawiPBJ.</p>
    </div>
</div>

<div class="card">
    <div class="card-body p-32 text-center">
        <span class="w-72-px h-72-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center mb-20"><iconify-icon icon="solar:chart-2-outline" class="text-4xl"></iconify-icon></span>
        <h5 class="mb-12"><?= esc($pageTitle ?? 'Ringkasan') ?></h5>
        <p class="text-secondary-light mb-0">Halaman UI sedang disiapkan.</p>
    </div>
</div>
<?= $this->endSection() ?>