<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>
<div class="bg-white border rounded p-4 p-lg-5 mb-4"><p class="text-uppercase text-muted small mb-2">Aktivitas Belajar</p><h1 class="mb-3">Tugas Saya</h1><p class="mb-0">Kelola tugas aktif, pengumpulan, revisi, dan riwayat tugas pembelajaran.</p></div>
<div class="row g-4"><?php foreach ($sectionContent['items'] as $index => $item): ?><div class="col-md-4"><div class="bg-white border rounded p-4 h-100"><span class="badge <?= $index === 0 ? 'bg-warning-subtle text-warning' : 'bg-light text-success' ?> mb-3"><?= $index === 0 ? 'Aktif' : 'Terjadwal' ?></span><h2 class="h5"><?= esc($item) ?></h2><p class="text-muted small">Prototype detail tugas dan status pengumpulan.</p><button type="button" class="btn btn-outline-success btn-sm">Lihat detail</button></div></div><?php endforeach; ?></div>
<?= $this->endSection() ?>
