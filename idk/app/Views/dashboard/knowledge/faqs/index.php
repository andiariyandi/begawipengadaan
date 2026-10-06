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
        <h6 class="fw-semibold mb-1">FAQ</h6>
        <p class="text-secondary-light mb-0">Kelola pertanyaan yang sering diajukan.</p>
    </div>
    <a href="<?= route_to('dashboard.knowledge.faqs.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah FAQ</a>
</div>
<?php if ($msg = session()->getFlashdata('success')): ?><div class="alert alert-success radius-8 mb-3"><?= esc($msg) ?></div><?php endif; ?>
<?php if ($msg = session()->getFlashdata('error')): ?><div class="alert alert-danger radius-8 mb-3"><?= esc($msg) ?></div><?php endif; ?>
<div class="card">
    <div class="card-body p-24">
        <?php if (empty($items)): ?>
            <p class="text-secondary-light mb-0">Belum ada FAQ. Klik "Tambah FAQ" untuk membuat.</p>
        <?php else: ?>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead><tr><th>Pertanyaan</th><th>Status</th><th>Diperbarui</th><th class="text-center">Aksi</th></tr></thead>
                <tbody>
                    <?php foreach ($items as $faq): ?>
                        <?php $statusKey = $faq['workflow_status'] ?? 'draft'; ?>
                        <tr>
                            <td><h6 class="text-md mb-0 fw-medium"><?= esc($faq['title']) ?></h6><span class="text-sm text-secondary-light">#<?= (int) $faq['id'] ?></span></td>
                            <td class="text-center"><span class="badge bg-primary-subtle text-primary-emphasis"><?= esc($statusLabels[$statusKey] ?? ucfirst($statusKey)) ?></span></td>
                            <td><?= esc($faq['updated_at'] ?? '-') ?></td>
                            <td class="text-center">
                                <a href="<?= route_to('dashboard.knowledge.faqs.detail', (string) $faq['id']) ?>" class="text-primary-600 me-12">Detail</a>
                                <a href="<?= route_to('dashboard.knowledge.faqs.edit', (string) $faq['id']) ?>" class="text-warning-main me-12">Ubah</a>
                                <form method="post" action="<?= route_to('dashboard.knowledge.faqs.delete', (string) $faq['id']) ?>" class="d-inline" onsubmit="return confirm('Arsipkan FAQ ini?')">
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
    </div>
</div>
<?= $this->endSection() ?>