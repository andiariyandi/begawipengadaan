<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$items = $items ?? [];
$statusLabels = [
    'draft' => 'Draft', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan',
    'submitted' => 'Diajukan', 'in_review' => 'Ditinjau', 'approved' => 'Disetujui',
    'revision_required' => 'Perlu Revisi', 'rejected' => 'Ditolak',
];
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Studi Kasus</h6>
        <p class="text-secondary-light mb-0">Kelola studi kasus dan pembelajaran dari praktik PBJ.</p>
    </div>
    <a href="<?= route_to('dashboard.knowledge.case-studies.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Studi Kasus</a>
</div>
<?php if ($msg = session()->getFlashdata('success')): ?><div class="alert alert-success radius-8 mb-3"><?= esc($msg) ?></div><?php endif; ?>
<div class="card"><div class="card-body p-24">
    <?php if (empty($items)): ?>
        <p class="text-secondary-light mb-0">Belum ada studi kasus.</p>
    <?php else: ?>
    <div class="table-responsive scroll-sm">
        <table class="table bordered-table sm-table mb-0">
            <thead><tr><th>Judul</th><th>Penulis</th><th>Diperbarui</th><th class="text-center">Status</th><th class="text-center">Aksi</th></tr></thead>
            <tbody>
                <?php foreach ($items as $row): ?>
                    <?php $statusKey = $row['workflow_status'] ?? 'draft'; ?>
                    <tr>
                        <td><h6 class="text-md mb-0 fw-medium"><?= esc($row['title']) ?></h6><span class="text-sm text-secondary-light">#<?= (int) $row['id'] ?></span></td>
                        <td><?= esc($row['author_name'] ?? '-') ?></td>
                        <td><?= esc($row['updated_at'] ?? '-') ?></td>
                        <td class="text-center"><span class="badge bg-primary-subtle text-primary-emphasis"><?= esc($statusLabels[$statusKey] ?? ucfirst($statusKey)) ?></span></td>
                        <td class="text-center">
                            <a href="<?= route_to('dashboard.knowledge.case-studies.detail', (string) $row['id']) ?>" class="text-primary-600 me-12">Detail</a>
                            <a href="<?= route_to('dashboard.knowledge.case-studies.edit', (string) $row['id']) ?>" class="text-warning-main me-12">Ubah</a>
                            <form method="post" action="<?= route_to('dashboard.knowledge.case-studies.delete', (string) $row['id']) ?>" class="d-inline" onsubmit="return confirm('Arsipkan studi kasus ini?')">
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