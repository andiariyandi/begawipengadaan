<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>
<div class="bg-white border rounded p-4 p-lg-5 mb-4"><p class="text-uppercase text-muted small mb-2">Assessment dan Feedback</p><h1 class="mb-3">Penilaian</h1><p class="mb-0">Pantau pre-test, quiz, post-test, grading, nilai, dan feedback pembelajaran.</p></div>
<div class="row g-4 mb-4"><?php foreach ($sectionContent['cards'] as $card): ?><div class="col-md-6"><div class="bg-white border rounded p-4"><p class="text-muted mb-2"><?= esc($card['title']) ?></p><strong class="display-6"><?= esc($card['value']) ?></strong><small class="d-block text-muted mt-2"><?= esc($card['meta']) ?></small></div></div><?php endforeach; ?></div>
<div class="bg-white border rounded p-4"><h2 class="h4 mb-3">Daftar assessment</h2><div class="list-group list-group-flush"><?php foreach ($sectionContent['items'] as $item): ?><div class="list-group-item px-0 d-flex justify-content-between"><span><?= esc($item) ?></span><span class="badge bg-light text-success">Tersedia</span></div><?php endforeach; ?></div></div>
<?= $this->endSection() ?>
