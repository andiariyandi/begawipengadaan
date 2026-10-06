<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$items = $items ?? [];
$statusOptions = [
    'draft' => 'Draft', 'submitted' => 'Diajukan', 'in_review' => 'Ditinjau',
    'approved' => 'Disetujui', 'published' => 'Dipublikasikan', 'rejected' => 'Ditolak',
    'revision_required' => 'Perlu Revisi', 'archived' => 'Diarsipkan',
];
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Berita</h6>
        <p class="text-secondary-light mb-0">Kelola berita dan publikasi BegawiPBJ.</p>
    </div>
    <a href="<?= route_to('dashboard.communication.news.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Berita</a>
</div>

<?= $this->include('dashboard/components/filter_bar') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success radius-8 mb-16"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-8 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Berita</th>
                        <th>Kategori</th>
                        <th>Penulis</th>
                        <th>Tanggal Publikasi</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($items)): ?>
                    <tr><td colspan="6" class="text-center text-secondary-light">Belum ada berita. Klik "Tambah Berita".</td></tr>
                <?php else: foreach ($items as $n): ?>
                    <tr>
                        <td>
                            <h6 class="text-md mb-0 fw-medium"><?= esc($n['title'] ?? '(Tanpa judul)') ?></h6>
                            <span class="text-sm text-secondary-light">#<?= (int) $n['id'] ?> · <?= esc($n['slug'] ?? '') ?></span>
                        </td>
                        <td><?= esc($n['category_name'] ?? '-') ?: '-' ?></td>
                        <td><?= esc($n['author_name'] ?? '-') ?: '-' ?></td>
                        <td><?= ! empty($n['published_at']) ? esc(date('d M Y H:i', strtotime($n['published_at']))) : '-' ?></td>
                        <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => $n['workflow_status'] ?? 'draft']) ?></td>
                        <td class="text-center">
                            <a href="<?= route_to('dashboard.communication.news.detail', (string) $n['id']) ?>" class="text-primary-600 me-12">Detail</a>
                            <a href="<?= route_to('dashboard.communication.news.edit', (string) $n['id']) ?>" class="text-warning-main me-12">Ubah</a>
                            <form action="<?= route_to('dashboard.communication.news.delete', (string) $n['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus berita ini?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-link text-danger-main p-0">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>