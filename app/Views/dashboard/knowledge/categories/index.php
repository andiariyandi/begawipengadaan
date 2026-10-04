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
<?php if ($msg = session()->getFlashdata('success')): ?><div class="alert alert-success radius-8 mb-3"><?= esc($msg) ?></div><?php endif; ?>
<?php if ($msg = session()->getFlashdata('error')): ?><div class="alert alert-danger radius-8 mb-3"><?= esc($msg) ?></div><?php endif; ?>
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
                        <td class="text-center"><span class="badge bg-primary-subtle text-primary-emphasis"><?= ! empty($cat['is_active']) ? 'Aktif' : 'Nonaktif' ?></span></td>
                        <td class="text-center">
                            <a href="<?= route_to('dashboard.knowledge.categories.edit', (string) $cat['id']) ?>" class="text-warning-main me-12">Ubah</a>
                            <form method="post" action="<?= route_to('dashboard.knowledge.categories.delete', (string) $cat['id']) ?>" class="d-inline" onsubmit="return confirm('Hapus kategori ini?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-link text-danger-main p-0">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div></div>
<?= $this->endSection() ?>