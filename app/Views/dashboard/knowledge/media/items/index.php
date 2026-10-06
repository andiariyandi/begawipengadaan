<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$items = $items ?? [];
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Item Media</h6>
        <p class="text-secondary-light mb-0">Daftar item media yang tersimpan.</p>
    </div>
    <a href="<?= route_to('dashboard.knowledge.media-gallery.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Item Media</a>
</div>
<div class="card"><div class="card-body p-24">
    <?php if (empty($items)): ?>
        <p class="text-secondary-light mb-0">Belum ada item media.</p>
    <?php else: ?>
    <div class="table-responsive scroll-sm">
        <table class="table bordered-table sm-table mb-0">
            <thead><tr><th>Item</th><th>Tipe</th><th>Urutan</th><th>Diperbarui</th><th class="text-center">Status</th></tr></thead>
            <tbody>
                <?php foreach ($items as $row): ?>
                    <tr>
                        <td><a href="<?= route_to('dashboard.knowledge.media-items.detail', (string) $row['id']) ?>" class="text-primary-600"><?= esc($row['title'] ?: '(Tanpa judul)') ?></a></td>
                        <td><?= esc($row['type']) ?></td>
                        <td><?= (int) $row['sort_order'] ?></td>
                        <td><?= esc($row['updated_at'] ?? '-') ?></td>
                        <td class="text-center"><span class="badge bg-primary-subtle text-primary-emphasis"><?= esc($row['workflow_status'] ?? 'draft') ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div></div>
<?= $this->endSection() ?>