<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24"><div><h6 class="fw-semibold mb-1">Tag</h6><p class="text-secondary-light mb-0">Kelola tag untuk klasifikasi konten Bunga Rampai.</p></div><button type="button" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Tag</button></div>
<div class="card"><div class="card-body p-24"><div class="d-flex flex-wrap gap-12"><?php foreach (['PBJ', 'Perencanaan', 'Kontrak', 'Penyedia', 'Sertifikasi', 'Regulasi', 'Pengadaan Digital', 'Praktik Baik', 'ASN', 'Kompetensi'] as $tag): ?><span class="badge text-bg-primary-50 text-primary-600 px-16 py-12 radius-8 text-sm"><iconify-icon icon="solar:hashtag-outline" class="me-4"></iconify-icon><?= esc($tag) ?></span><?php endforeach; ?></div></div></div>
<?= $this->endSection() ?>