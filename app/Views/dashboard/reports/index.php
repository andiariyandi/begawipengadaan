<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Laporan') ?></h6>
        <p class="text-secondary-light mb-0">Pratinjau UI laporan BegawiPengadaan.</p>
    </div>
    <button type="button" class="btn btn-primary-600 radius-8 px-20 py-11">Export Data</button>
</div>

<div class="card">
    <div class="card-body p-32 text-center">
        <span class="w-72-px h-72-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center mb-20"><iconify-icon icon="solar:document-text-outline" class="text-4xl"></iconify-icon></span>
        <h5 class="mb-12"><?= esc($pageTitle ?? 'Laporan') ?></h5>
        <p class="text-secondary-light mb-0">Template laporan telah tersedia untuk tahap UI.</p>
    </div>
</div>
<?= $this->endSection() ?>