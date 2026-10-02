<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>
<div class="bg-white border rounded p-4 p-lg-5 mb-4"><p class="text-uppercase text-muted small mb-2">Personal</p><h1 class="mb-3">Profil Saya</h1><p class="mb-0">Kelola data diri, profil kompetensi, pengaturan, dan riwayat sesuai role.</p></div>
<div class="row g-4"><?php foreach (['Data Diri', 'Profil Kompetensi', 'Pengaturan', 'Riwayat Belajar'] as $item): ?><div class="col-md-6"><div class="bg-white border rounded p-4 h-100"><h2 class="h5"><?= esc($item) ?></h2><p class="text-muted small">Ringkasan <?= strtolower($item) ?> pengguna.</p><button type="button" class="btn btn-outline-success btn-sm">Kelola</button></div></div><?php endforeach; ?></div>
<?= $this->endSection() ?>
