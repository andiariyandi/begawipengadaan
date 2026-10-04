<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $items = $items ?? []; ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Tag</h6>
        <p class="text-secondary-light mb-0">Kelola tag untuk klasifikasi konten Bunga Rampai.</p>
    </div>
    <a href="<?= route_to('dashboard.knowledge.tags.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Tag</a>
</div>
<?php if ($msg = session()->getFlashdata('success')): ?><div class="alert alert-success radius-8 mb-3"><?= esc($msg) ?></div><?php endif; ?>
<?php if ($msg = session()->getFlashdata('error')): ?><div class="alert alert-danger radius-8 mb-3"><?= esc($msg) ?></div><?php endif; ?>
<div class="card"><div class="card-body p-24">
    <?php if (empty($items)): ?>
        <p class="text-secondary-light mb-0">Belum ada tag.</p>
    <?php else: ?>
    <div class="d-flex flex-wrap gap-12">
        <?php foreach ($items as $tag): ?>
            <div class="border rounded p-12 d-flex align-items-center gap-8">
                <span class="badge text-bg-primary-50 text-primary-600 px-16 py-12 radius-8 text-sm"><iconify-icon icon="solar:hashtag-outline" class="me-4"></iconify-icon><?= esc($tag['name']) ?></span>
                <a href="<?= route_to('dashboard.knowledge.tags.edit', (string) $tag['id']) ?>" class="text-warning-main text-sm">Ubah</a>
                <form method="post" action="<?= route_to('dashboard.knowledge.tags.delete', (string) $tag['id']) ?>" class="d-inline" onsubmit="return confirm('Hapus tag ini?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-link text-danger-main text-sm p-0">Hapus</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div></div>
<?= $this->endSection() ?>