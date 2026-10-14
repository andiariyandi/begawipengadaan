<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <h4>BegawiPengadaan Dashboard</h4>
        <p>Monitoring ekosistem Pengadaan Barang/Jasa Pemerintah.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <a href="<?= base_url('dashboard/courses') ?>">Arena Belajar</a>
    </div>
    <div class="col-md-3">
        <a href="<?= base_url('dashboard/regulations') ?>">Regulasi</a>
    </div>
    <div class="col-md-3">
        <a href="<?= base_url('dashboard/events') ?>">Event</a>
    </div>
    <div class="col-md-3">
        <a href="<?= base_url('dashboard/statistics') ?>">Statistik</a>
    </div>
</div>

<?= $this->endSection() ?>