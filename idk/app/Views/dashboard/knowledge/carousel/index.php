<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$articles = $articles ?? [];
$media = $media ?? [];
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Carousel dan Konten Unggulan</h6>
        <p class="text-secondary-light mb-0">Kelola konten yang ditampilkan pada area unggulan Bunga Rampai.</p>
    </div>
    <a href="<?= route_to('dashboard.knowledge.carousel.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Carousel</a>
</div>
<?php if ($msg = session()->getFlashdata('success')): ?><div class="alert alert-success radius-8 mb-3"><?= esc($msg) ?></div><?php endif; ?>
<div class="card mb-24">
    <div class="card-body p-24">
        <h6 class="fw-semibold mb-3">Artikel di Carousel</h6>
        <?php if (empty($articles)): ?>
            <p class="text-secondary-light mb-0">Belum ada artikel yang diaktifkan untuk carousel.</p>
        <?php else: ?>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead><tr><th>Judul</th><th>Tipe</th><th>Urutan</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    <?php foreach ($articles as $a): ?>
                        <tr>
                            <td><a href="<?= route_to('dashboard.knowledge.articles.detail', (string) $a['id']) ?>" class="text-primary-600"><?= esc($a['title']) ?></a></td>
                            <td><?= esc($a['type']) ?></td>
                            <td><?= (int) $a['carousel_order'] ?></td>
                            <td><span class="badge bg-primary-subtle text-primary-emphasis"><?= esc($a['workflow_status']) ?></span></td>
                            <td class="text-end">
                                <form method="post" action="<?= route_to('dashboard.knowledge.carousel.disable', $a['type']) ?>" class="d-inline" onsubmit="return confirm('Nonaktifkan dari carousel?')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="target_id" value="<?= (int) $a['id'] ?>">
                                    <button type="submit" class="btn btn-link text-danger-main p-0">Nonaktifkan</button>
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
<div class="card">
    <div class="card-body p-24">
        <h6 class="fw-semibold mb-3">Media di Carousel</h6>
        <?php if (empty($media)): ?>
            <p class="text-secondary-light mb-0">Belum ada media yang diaktifkan untuk carousel.</p>
        <?php else: ?>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead><tr><th>Judul</th><th>Tipe</th><th>Urutan</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    <?php foreach ($media as $m): ?>
                        <tr>
                            <td><a href="<?= route_to('dashboard.knowledge.media-gallery.detail', (string) $m['id']) ?>" class="text-primary-600"><?= esc($m['title'] ?: '(Tanpa judul)') ?></a></td>
                            <td><?= esc($m['type']) ?></td>
                            <td><?= (int) $m['sort_order'] ?></td>
                            <td><span class="badge bg-primary-subtle text-primary-emphasis"><?= esc($m['workflow_status']) ?></span></td>
                            <td class="text-end">
                                <form method="post" action="<?= route_to('dashboard.knowledge.carousel.disable', 'media') ?>" class="d-inline" onsubmit="return confirm('Nonaktifkan dari carousel?')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="target_id" value="<?= (int) $m['id'] ?>">
                                    <button type="submit" class="btn btn-link text-danger-main p-0">Nonaktifkan</button>
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