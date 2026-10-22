<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$items = $items ?? [];
$counts = $counts ?? [];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Kategori Konten PBJ</h6>
        <p class="text-secondary-light mb-0">Kelola taksonomi dan klasifikasi bertingkat untuk artikel, materi media, dan dokumen Bunga Rampai.</p>
    </div>
    <a href="<?= route_to('dashboard.knowledge.categories.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
        <iconify-icon icon="solar:add-circle-linear" class="text-xl"></iconify-icon>
        <span>Tambah Kategori</span>
    </a>
</div>

<!-- Reusable Tabbed Console Navigation -->
<?= view('dashboard/knowledge/_nav_knowledge', ['section' => 'taxonomy', 'activeSub' => 'categories']) ?>

<div class="card border-0 radius-16 shadow-sm bg-base">
    <div class="card-header border-bottom p-20 bg-base d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <iconify-icon icon="solar:folder-with-files-bold" class="text-primary-600 text-xl"></iconify-icon>
            <h6 class="text-base fw-bold mb-0">Daftar Kategori Pengetahuan (<?= count($items) ?>)</h6>
        </div>
    </div>
    <div class="card-body p-24">
        <?php if (empty($items)): ?>
            <div class="text-center py-32 text-secondary-light">
                <iconify-icon icon="solar:folder-error-broken" class="text-5xl mb-2 text-neutral-400"></iconify-icon>
                <p class="mb-0 text-sm">Belum ada kategori yang ditambahkan.</p>
            </div>
        <?php else: ?>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 align-middle begawi-table" data-table-name="kategori-pengetahuan">
                <thead>
                    <tr>
                        <th>Nama Kategori</th>
                        <th>Slug URL</th>
                        <th>Kategori Induk (Parent)</th>
                        <th class="text-center">Jumlah Konten</th>
                        <th class="text-center">Urutan</th>
                        <th class="text-center">Status</th>
                        <th class="text-end no-sort no-export action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $byId = [];
                        foreach ($items as $it) { $byId[(int) $it['id']] = $it; }
                    ?>
                    <?php foreach ($items as $cat): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="w-32-px h-32-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center">
                                        <iconify-icon icon="solar:folder-linear"></iconify-icon>
                                    </span>
                                    <div>
                                        <span class="fw-semibold text-primary-light text-sm d-block"><?= esc($cat['name']) ?></span>
                                        <?php if (! empty($cat['description'])): ?>
                                            <small class="text-secondary-light text-xs"><?= esc(mb_strimwidth($cat['description'], 0, 50, '...')) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><code class="text-primary-600 bg-primary-50 px-8 py-2 radius-4 text-xs"><?= esc($cat['slug']) ?></code></td>
                            <td>
                                <?php if (! empty($cat['parent_id']) && isset($byId[(int) $cat['parent_id']])): ?>
                                    <span class="badge bg-neutral-100 text-neutral-700 border radius-6 text-xs">
                                        <i class="fas fa-level-up-alt fa-rotate-90 me-1"></i> <?= esc($byId[(int) $cat['parent_id']]['name']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-secondary-light text-xs">Root (Utama)</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info-50 text-info-700 border border-info-200 px-10 py-4 radius-6 fw-bold">
                                    <?= (int) ($counts[(int) $cat['id']] ?? 0) ?>
                                </span>
                            </td>
                            <td class="text-center fw-semibold text-secondary-light">
                                <?= (int) ($cat['sort_order'] ?? 0) ?>
                            </td>
                            <td class="text-center">
                                <?= view('dashboard/components/status_badge', ['status' => ! empty($cat['is_active']) ? 'active' : 'inactive']) ?>
                            </td>
                            <td class="text-end">
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <a href="<?= route_to('dashboard.knowledge.categories.edit', (string) $cat['id']) ?>" class="btn btn-outline-warning-600 btn-sm radius-8 p-6" title="Edit Kategori">
                                        <iconify-icon icon="solar:pen-linear" class="text-base"></iconify-icon>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger-600 btn-sm radius-8 p-6"
                                        data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                                        data-action="<?= route_to('dashboard.knowledge.categories.delete', (string) $cat['id']) ?>"
                                        data-method="POST"
                                        data-title="Hapus Kategori"
                                        data-message="Apakah Anda yakin ingin menghapus kategori '<?= esc($cat['name']) ?>'?"
                                        data-btn-text="Hapus Kategori"
                                        data-btn-class="btn-danger-600"
                                        data-icon="fluent:delete-24-regular"
                                        data-icon-color="danger"
                                        title="Hapus Kategori">
                                        <iconify-icon icon="solar:trash-bin-trash-linear" class="text-base"></iconify-icon>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>