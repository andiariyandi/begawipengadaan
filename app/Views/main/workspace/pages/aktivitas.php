<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>
<div class="bg-white border rounded p-4 p-lg-5 mb-4"><p class="text-uppercase text-muted small mb-2">Audit Personal</p><h1 class="mb-3">Aktivitas</h1><p class="mb-0">Riwayat login dan aktivitas belajar pengguna ditampilkan secara kronologis.</p></div>
<div class="bg-white border rounded p-4"><h2 class="h4 mb-3">Aktivitas terbaru</h2><div class="list-group list-group-flush"><?php foreach ($sectionContent['items'] as $item): ?><div class="list-group-item px-0 d-flex justify-content-between"><span><?= esc($item) ?></span><small class="text-muted">Hari ini</small></div><?php endforeach; ?></div></div>
<?= $this->endSection() ?>
