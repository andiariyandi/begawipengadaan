<?= $this->extend(in_array('kms.manage', session()->get('permissions') ?? [], true) ? 'layout/layout1' : 'layout/public'); ?>

<?= $this->section('content'); ?>

<div class="mb-24 d-flex align-items-center justify-content-between">
    <h6 class="mb-0">Studi Kasus PBJ</h6>
    <?php if (in_array('kms.manage', session()->get('permissions') ?? [], true)): ?>
        <a href="<?= route_to('knowledge.caseStudies.create') ?>" class="btn btn-primary btn-sm px-16 py-8 radius-8">
            <iconify-icon icon="ic:baseline-plus"></iconify-icon> Tambah
        </a>
    <?php endif; ?>
</div>

<div class="card p-24 radius-12 mb-24">
    <form method="get" class="row g-3">
        <div class="col-md-6">
            <input type="text" name="q" class="form-control" placeholder="Cari studi kasus..." value="<?= esc($keyword ?? '') ?>">
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

<?php if (empty($caseStudies)): ?>
    <p class="text-secondary-light">Tidak ada studi kasus ditemukan.</p>
<?php else: ?>
    <?php foreach ($caseStudies as $caseStudy): ?>
        <?php $authorContext = match ($caseStudy['author_profile_type'] ?? null) {
            'asn' => trim(($caseStudy['author_asn_institution'] ?? '') . ' / ' . ($caseStudy['author_asn_position'] ?? ''), ' /'),
            'provider' => $caseStudy['author_provider_business'] ?? '',
            'academic_practitioner' => $caseStudy['author_academic_institution'] ?? '',
            default => 'Masyarakat umum',
        }; ?>
        <div class="card p-24 radius-12 mb-16">
            <span class="bg-neutral-200 text-neutral-600 px-16 py-4 radius-4 text-sm mb-8 d-inline-block"><?= esc($caseStudy['category_name']) ?></span>
            <h6 class="mb-8"><a href="<?= route_to('knowledge.caseStudies.detail', $caseStudy['id']) ?>" class="text-primary-light"><?= esc($caseStudy['title']) ?></a></h6>
            <p class="text-secondary-light text-sm mb-0">
                Oleh <?= esc($caseStudy['author_name']) ?><?php if ($authorContext !== ''): ?> (<?= esc($authorContext) ?>)<?php endif; ?> &middot; <?= esc(date('d M Y', strtotime($caseStudy['created_at']))) ?>
            </p>
            <?php if (in_array('kms.manage', session()->get('permissions') ?? [], true)): ?>
                <div class="mt-12 d-flex gap-8">
                    <a href="<?= route_to('knowledge.caseStudies.edit', $caseStudy['id']) ?>" class="btn btn-outline-primary btn-sm">Edit</a>
                    <form action="<?= route_to('knowledge.caseStudies.delete', $caseStudy['id']) ?>" method="post" onsubmit="return confirm('Hapus studi kasus ini?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection(); ?>
