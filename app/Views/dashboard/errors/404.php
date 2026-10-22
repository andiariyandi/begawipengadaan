<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="card basic-data-table border-0 shadow-sm radius-12">
    <div class="card-body py-80 text-center">
        <div class="mb-24">
            <span class="w-88-px h-88-px bg-danger-50 text-danger-600 rounded-circle d-inline-flex align-items-center justify-content-center">
                <iconify-icon icon="solar:danger-triangle-bold" class="text-48-px"></iconify-icon>
            </span>
        </div>
        <h3 class="mb-12 text-primary-light fw-bold"><?= esc($pageTitle ?? $title ?? '404 - Halaman / Data Tidak Ditemukan') ?></h3>
        <p class="text-secondary-light mb-28 max-w-600-px mx-auto fs-6">
            <?= esc($message ?? 'Data atau halaman yang Anda cari tidak ditemukan pada sistem. Kemungkinan tautan salah atau sumber daya telah dipindahkan.') ?>
        </p>
        <div class="d-flex align-items-center justify-content-center gap-3">
            <a href="javascript:history.back()" class="btn btn-outline-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
                <iconify-icon icon="solar:arrow-left-linear" class="text-xl"></iconify-icon> Kembali
            </a>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
                <iconify-icon icon="solar:home-2-linear" class="text-xl"></iconify-icon> Beranda Dashboard
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
