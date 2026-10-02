<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>
<div class="bg-white border rounded p-4 p-lg-5 mb-4"><p class="text-uppercase text-muted small mb-2">Operator Workspace</p><h1 class="mb-3">Operasional</h1><p class="mb-0">Pantau verifikasi operasional, enrollment, kelas, approval, dan laporan.</p></div>
<div class="row g-4"><?php foreach (['Verifikasi Operasional', 'Enrollment', 'Kelas', 'Approval', 'Laporan Operasional'] as $item): ?><div class="col-md-6"><div class="bg-white border rounded p-4 h-100"><span class="badge bg-light text-success mb-3">Prototype</span><h2 class="h5"><?= esc($item) ?></h2><p class="text-muted small">Ringkasan operasional dan antrean kerja.</p><button type="button" class="btn btn-outline-success btn-sm">Buka modul</button></div></div><?php endforeach; ?></div>
<?= $this->endSection() ?>
