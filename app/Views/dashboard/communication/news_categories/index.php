<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $items = $items ?? []; ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Kategori Berita</h6>
        <p class="text-secondary-light mb-0">Kelola klasifikasi berita.</p>
    </div>
    <a href="<?= site_url('dashboard/komunikasi/kategori-berita/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Kategori</a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success radius-8 mb-16"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Slug</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Jumlah Berita</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr><td colspan="6" class="text-center text-secondary-light">Belum ada kategori.</td></tr>
                    <?php else: foreach ($items as $c): ?>
                        <tr>
                            <td><h6 class="text-md mb-0 fw-medium"><?= esc($c['name']) ?></h6></td>
                            <td><code class="text-primary-600"><?= esc($c['slug']) ?></code></td>
                            <td><?= esc($c['description'] ?? '-') ?: '-' ?></td>
                            <td class="text-center"><?= (int) ($c['news_count'] ?? 0) ?></td>
                            <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => ($c['is_active'] ?? 0) ? 'active' : 'inactive']) ?></td>
                            <td class="text-center">
                                <a href="<?= site_url('dashboard/komunikasi/kategori-berita/' . (int) $c['id'] . '/ubah') ?>" class="text-warning-main">Ubah</a>
                                <form action="<?= site_url('dashboard/komunikasi/kategori-berita/' . (int) $c['id'] . '/hapus') ?>" method="post" class="d-inline ms-12" onsubmit="return confirm('Hapus kategori?')">
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
