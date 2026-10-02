<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>
<div class="bg-white border rounded p-4 p-lg-5 mb-4"><p class="text-uppercase text-muted small mb-2">Personal</p><h1 class="mb-3">Bookmark Saya</h1><p class="mb-0">Akses cepat ke course, materi, lesson, Bunga Rampai, regulasi, berita, dan event yang disimpan.</p></div>
<div class="row g-4"><?php foreach (['Pembelajaran', 'Bunga Rampai', 'Regulasi', 'Berita', 'Event'] as $type): ?><div class="col-md-4"><div class="bg-white border rounded p-4 h-100"><span class="text-success fs-3">★</span><h2 class="h5 mt-3"><?= esc($type) ?></h2><p class="text-muted small">Konten <?= strtolower($type) ?> tersimpan.</p><button type="button" class="btn btn-outline-success btn-sm">Buka bookmark</button></div></div><?php endforeach; ?></div>
<?= $this->endSection() ?>
