<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-body p-24">
        <h5><?= esc($pageTitle ?? 'Pengaturan Sistem') ?></h5>
        <p>View pengaturan berhasil dimuat.</p>
    </div>
</div>
<?= $this->endSection() ?>