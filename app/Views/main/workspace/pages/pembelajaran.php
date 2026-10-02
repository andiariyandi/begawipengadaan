<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>
<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2"><?= esc($sectionContent['eyebrow']) ?></p>
    <h1 class="mb-3">Kelas Saya</h1>
    <p class="mb-0"><?= esc($sectionContent['description']) ?></p>
</div>
<div class="row g-4 mb-4">
    <?php foreach ($sectionContent['cards'] as $card): ?>
        <div class="col-md-4"><div class="bg-white border rounded p-4 h-100"><p class="text-muted"><?= esc($card['title']) ?></p><strong class="display-6"><?= esc($card['value']) ?></strong><small class="d-block text-muted mt-2"><?= esc($card['meta']) ?></small></div></div>
    <?php endforeach; ?>
</div>
<div class="bg-white border rounded p-4">
    <h2 class="h4 mb-3">Materi dan kelas yang tersedia</h2>
    <div class="row g-3"><?php foreach ($sectionContent['items'] as $item): ?><div class="col-md-4"><div class="border rounded p-3 h-100"><span class="badge bg-light text-success mb-3">Kelas</span><strong class="d-block"><?= esc($item) ?></strong><small class="text-muted">Lihat materi dan modul pembelajaran</small></div></div><?php endforeach; ?></div>
</div>
<?= $this->endSection() ?>
