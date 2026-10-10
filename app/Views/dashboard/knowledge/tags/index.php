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
<div class="card"><div class="card-body p-24">
    <?php if (empty($items)): ?>
        <p class="text-secondary-light mb-0">Belum ada tag.</p>
    <?php else: ?>
    <div class="d-flex flex-wrap gap-12">
        <?php foreach ($items as $tag): ?>
            <div class="border rounded-8 p-12 d-flex align-items-center gap-12 bg-base">
                <span class="badge text-bg-primary-50 text-primary-600 px-16 py-8 radius-8 text-sm fw-medium d-inline-flex align-items-center"><iconify-icon icon="solar:hashtag-outline" class="me-4 text-base"></iconify-icon><?= esc($tag['name']) ?></span>
                <div class="d-flex align-items-center gap-8 ms-auto">
                    <a href="<?= route_to('dashboard.knowledge.tags.edit', (string) $tag['id']) ?>" class="w-32-px h-32-px bg-warning-focus text-warning-main rounded-circle d-inline-flex align-items-center justify-content-center" title="Ubah Tag">
                        <iconify-icon icon="solar:pen-linear"></iconify-icon>
                    </a>
                    <button type="button" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center border-0"
                        data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                        data-action="<?= route_to('dashboard.knowledge.tags.delete', (string) $tag['id']) ?>"
                        data-method="POST"
                        data-title="Hapus Tag"
                        data-message="Apakah Anda yakin ingin menghapus tag '<?= esc($tag['name']) ?>'?"
                        data-btn-text="Hapus Tag"
                        data-btn-class="btn-danger-600"
                        data-icon="fluent:delete-24-regular"
                        data-icon-color="danger"
                        title="Hapus Tag">
                        <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div></div>
<?= $this->endSection() ?>