<?= $this->extend('layout/public'); ?>

<?= $this->section('content'); ?>

<div class="mb-24">
    <h6 class="mb-4">Artikel PBJ</h6>
</div>

<div class="card p-24 radius-12 mb-24">
    <form method="get" class="row g-3">
        <div class="col-md-6">
            <input type="text" name="q" class="form-control" placeholder="Cari artikel..." value="<?= esc($keyword ?? '') ?>">
        </div>
        <div class="col-md-4">
            <select name="category_id" class="form-select">
                <option value="">Semua Kategori</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= esc($category['id']) ?>" <?= ((string) $categoryId === (string) $category['id']) ? 'selected' : '' ?>>
                        <?= esc($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100 px-24 py-12 radius-8">Cari</button>
        </div>
    </form>
</div>

<?php if (empty($articles)): ?>
    <p class="text-secondary-light">Tidak ada artikel ditemukan.</p>
<?php else: ?>
    <?php foreach ($articles as $article): ?>
        <?php $authorContext = match ($article['author_profile_type'] ?? null) {
            'asn' => trim(($article['author_asn_institution'] ?? '') . ' / ' . ($article['author_asn_position'] ?? ''), ' /'),
            'provider' => $article['author_provider_business'] ?? '',
            'academic_practitioner' => $article['author_academic_institution'] ?? '',
            default => 'Masyarakat umum',
        }; ?>
        <div class="card p-24 radius-12 mb-16">
            <span class="bg-neutral-200 text-neutral-600 px-16 py-4 radius-4 text-sm mb-8 d-inline-block"><?= esc($article['category_name']) ?></span>
            <h6 class="mb-8"><a href="<?= route_to('knowledge.articles.detail', $article['id']) ?>" class="text-primary-light"><?= esc($article['title']) ?></a></h6>
            <p class="text-secondary-light text-sm mb-0">
                Oleh <?= esc($article['author_name']) ?><?php if ($authorContext !== ''): ?> (<?= esc($authorContext) ?>)<?php endif; ?> &middot; <?= esc(date('d M Y', strtotime($article['created_at']))) ?> &middot; <?= esc($article['view_count']) ?> dibaca
            </p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection(); ?>