<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>
<div class="bg-white border rounded p-4 p-lg-5 mb-4"><p class="text-uppercase text-muted small mb-2">Pusat Informasi</p><h1 class="mb-3">Notifikasi</h1><p class="mb-0">Pembaruan pembelajaran, tugas, penilaian, dan sistem akan tampil di sini.</p></div>
<div class="bg-white border rounded p-4"><div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h4 mb-0">Notifikasi terbaru</h2><span class="badge bg-success">04 belum dibaca</span></div><div class="list-group list-group-flush"><?php foreach ($sectionContent['items'] as $item): ?><div class="list-group-item px-0"><strong><?= esc($item) ?></strong><small class="d-block text-muted mt-1">Baru saja · notifikasi prototype</small></div><?php endforeach; ?></div></div>
<?= $this->endSection() ?>
