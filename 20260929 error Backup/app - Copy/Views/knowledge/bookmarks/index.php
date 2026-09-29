<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="mb-24">
    <h6 class="mb-0">Artikel Tersimpan</h6>
</div>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success radius-12 mb-16"><?= esc(session()->getFlashdata('message')) ?></div>
<?php endif; ?>

<?php if (empty($articles)): ?>
    <p class="text-secondary-light">
        Anda belum menyimpan artikel apapun. <a href="<?= route_to('knowledge.articles.browse') ?>">Jelajahi artikel</a>.
    </p>
<?php else: ?>
    <?php foreach ($articles as $article): ?>
        <div class="card p-24 radius-12 mb-16">
            <span class="bg-neutral-200 text-neutral-600 px-16 py-4 radius-4 text-sm mb-8 d-inline-block"><?= esc($article['category_name']) ?></span>
            <h6 class="mb-8"><a href="<?= route_to('knowledge.articles.detail', $article['id']) ?>" class="text-primary-light"><?= esc($article['title']) ?></a></h6>
            <p class="text-secondary-light text-sm mb-0">Disimpan pada <?= esc(date('d M Y', strtotime($article['bookmarked_at']))) ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection(); ?>
