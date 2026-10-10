<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$items = $items ?? [];
$counts = $counts ?? [];
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Kategori</h6>
        <p class="text-secondary-light mb-0">Kelola kategori konten Bunga Rampai (mendukung hierarki parent-child).</p>
    </div>
    <a href="<?= route_to('dashboard.knowledge.categories.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Kategori</a>
</div>
<div class="card"><div class="card-body p-24">
    <?php if (empty($items)): ?>
        <p class="text-secondary-light mb-0">Belum ada kategori.</p>
    <?php else: ?>
    <div class="table-responsive scroll-sm">
        <table class="table bordered-table sm-table mb-0">
            <thead><tr><th>Nama</th><th>Slug</th><th>Parent</th><th>Jumlah Konten</th><th>Urutan</th><th class="text-center">Status</th><th class="text-center">Aksi</th></tr></thead>
            <tbody>
                <?php
                    $byId = [];
                    foreach ($items as $it) { $byId[(int) $it['id']] = $it; }
                ?>
                <?php foreach ($items as $cat): ?>
                    <tr>
                        <td><h6 class="text-md mb-0 fw-medium"><?= esc($cat['name']) ?></h6></td>
                        <td><span class="text-primary-600"><?= esc($cat['slug']) ?></span></td>
                        <td><?= esc(! empty($cat['parent_id']) ? ($byId[(int) $cat['parent_id']]['name'] ?? '-') : '—') ?></td>
                        <td><?= (int) ($counts[(int) $cat['id']] ?? 0) ?></td>
                        <td><?= (int) ($cat['sort_order'] ?? 0) ?></td>
                        <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => ! empty($cat['is_active']) ? 'active' : 'inactive']) ?></td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button class="btn btn-icon btn-sm btn-outline-neutral-300 radius-8" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <iconify-icon icon="solar:menu-dots-bold" class="text-lg"></iconify-icon>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end p-8 radius-8 border border-neutral-200">
                                    <li>
                                        <a class="dropdown-item px-12 py-8 radius-6 d-flex align-items-center gap-2 text-secondary-light" href="<?= route_to('dashboard.knowledge.categories.edit', (string) $cat['id']) ?>">
                                            <iconify-icon icon="solar:pen-linear" class="text-lg"></iconify-icon>
                                            <span>Edit</span>
                                        </a>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item px-12 py-8 radius-6 d-flex align-items-center gap-2 text-danger-600"
                                            data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                                            data-action="<?= route_to('dashboard.knowledge.categories.delete', (string) $cat['id']) ?>"
                                            data-method="POST"
                                            data-title="Hapus Kategori"
                                            data-message="Apakah Anda yakin ingin menghapus kategori ini?"
                                            data-btn-text="Hapus Kategori"
                                            data-btn-class="btn-danger-600"
                                            data-icon="fluent:delete-24-regular"
                                            data-icon-color="danger">
                                            <iconify-icon icon="solar:trash-bin-trash-linear" class="text-lg"></iconify-icon>
                                            <span>Hapus</span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div></div>
<?= $this->endSection() ?>