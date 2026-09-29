<?= $this->extend('layout/public'); ?>

<?= $this->section('content'); ?>

<div class="card p-24 radius-12">
    <?php $authorContext = match ($article['author_profile_type'] ?? null) {
        'asn' => trim(($article['author_asn_institution'] ?? '') . ' / ' . ($article['author_asn_position'] ?? ''), ' /'),
        'provider' => $article['author_provider_business'] ?? '',
        'academic_practitioner' => $article['author_academic_institution'] ?? '',
        default => 'Masyarakat umum',
    }; ?>
    <span class="bg-neutral-200 text-neutral-600 px-16 py-4 radius-4 text-sm mb-8 d-inline-block"><?= esc($article['category_name']) ?></span>
    <div class="d-flex align-items-start justify-content-between">
        <h5 class="mb-8"><?= esc($article['title']) ?></h5>
        <?php if (session()->get('isLoggedIn')): ?>
            <form action="<?= route_to('knowledge.articles.bookmark', $article['id']) ?>" method="post">
                <?= csrf_field() ?>
                <button type="submit" class="btn <?= $isBookmarked ? 'btn-warning' : 'btn-outline-warning' ?> btn-sm px-16 py-8 radius-8">
                    <iconify-icon icon="mdi:bookmark<?= $isBookmarked ? '' : '-outline' ?>"></iconify-icon>
                    <?= $isBookmarked ? 'Tersimpan' : 'Simpan' ?>
                </button>
            </form>
        <?php else: ?>
            <a href="<?= route_to('signin') ?>" class="btn btn-outline-primary btn-sm px-16 py-8 radius-8">Login untuk menyimpan</a>
        <?php endif; ?>
    </div>
    <p class="text-secondary-light text-sm mb-24">
        Oleh <?= esc($article['author_name']) ?><?php if ($authorContext !== ''): ?> (<?= esc($authorContext) ?>)<?php endif; ?> &middot; <?= esc(date('d M Y', strtotime($article['created_at']))) ?> &middot; <?= esc($article['view_count']) ?> dibaca
    </p>
    <div class="content-body">
        <?= nl2br(esc($article['content'])) ?>
    </div>
</div>

<?= $this->endSection(); ?>