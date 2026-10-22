<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $items = $items ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Tagar Konten (Hashtags)</h6>
        <p class="text-secondary-light mb-0">Kelola kata kunci dan tag pencarian untuk mempermudah navigasi materi di Bunga Rampai.</p>
    </div>
    <a href="<?= route_to('dashboard.knowledge.tags.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
        <iconify-icon icon="solar:add-circle-linear" class="text-xl"></iconify-icon>
        <span>Tambah Tag</span>
    </a>
</div>

<!-- Reusable Tabbed Console Navigation -->
<?= view('dashboard/knowledge/_nav_knowledge', ['section' => 'taxonomy', 'activeSub' => 'tags']) ?>

<div class="card border-0 radius-16 shadow-sm bg-base">
    <div class="card-header border-bottom p-20 bg-base d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <iconify-icon icon="solar:hashtag-bold" class="text-primary-600 text-xl"></iconify-icon>
            <h6 class="text-base fw-bold mb-0">Semua Tagar Aktif (<?= count($items) ?>)</h6>
        </div>
    </div>
    <div class="card-body p-24">
        <?php if (empty($items)): ?>
            <div class="text-center py-32 text-secondary-light">
                <iconify-icon icon="solar:tag-broken" class="text-5xl mb-2 text-neutral-400"></iconify-icon>
                <p class="mb-0 text-sm">Belum ada tag yang ditambahkan.</p>
            </div>
        <?php else: ?>
        <div class="row g-3">
            <?php foreach ($items as $tag): ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="border radius-12 p-16 d-flex align-items-center justify-content-between bg-neutral-50 hover-border-primary transition-all">
                        <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                            <span class="w-36-px h-36-px bg-primary-100 text-primary-700 radius-8 d-inline-flex align-items-center justify-content-center flex-shrink-0">
                                <iconify-icon icon="solar:hashtag-bold" class="text-lg"></iconify-icon>
                            </span>
                            <div class="overflow-hidden">
                                <span class="fw-bold text-primary-light text-sm text-truncate d-block" title="<?= esc($tag['name']) ?>">
                                    <?= esc($tag['name']) ?>
                                </span>
                                <small class="text-secondary-light text-xs text-truncate d-block"><?= esc($tag['slug']) ?></small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-1 flex-shrink-0">
                            <a href="<?= route_to('dashboard.knowledge.tags.edit', (string) $tag['id']) ?>" class="btn btn-icon btn-sm btn-outline-warning-600 radius-6 p-4" title="Ubah Tag">
                                <iconify-icon icon="solar:pen-linear" class="text-base"></iconify-icon>
                            </a>
                            <button type="button" class="btn btn-icon btn-sm btn-outline-danger-600 radius-6 p-4"
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
                                <iconify-icon icon="solar:trash-bin-trash-outline" class="text-base"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>