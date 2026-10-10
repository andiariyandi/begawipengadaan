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
                                <button type="button" class="btn btn-outline-danger-600 btn-sm radius-8 px-12 py-6 d-inline-flex align-items-center gap-1"
                                    data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                                    data-action="<?= route_to('dashboard.knowledge.carousel.disable-item', $a['type'], (string) $a['id']) ?>"
                                    data-method="POST"
                                    data-title="Nonaktifkan Carousel"
                                    data-message="Apakah Anda yakin ingin menonaktifkan artikel ini dari carousel?"
                                    data-btn-text="Nonaktifkan"
                                    data-btn-class="btn-danger-600"
                                    data-icon="solar:close-circle-linear"
                                    data-icon-color="danger">
                                    <iconify-icon icon="solar:close-circle-linear"></iconify-icon>
                                    Nonaktifkan
                                </button>
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
                                <button type="button" class="btn btn-outline-danger-600 btn-sm radius-8 px-12 py-6 d-inline-flex align-items-center gap-1"
                                    data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                                    data-action="<?= route_to('dashboard.knowledge.carousel.disable-item', 'media', (string) $m['id']) ?>"
                                    data-method="POST"
                                    data-title="Nonaktifkan Carousel"
                                    data-message="Apakah Anda yakin ingin menonaktifkan media ini dari carousel?"
                                    data-btn-text="Nonaktifkan"
                                    data-btn-class="btn-danger-600"
                                    data-icon="solar:close-circle-linear"
                                    data-icon-color="danger">
                                    <iconify-icon icon="solar:close-circle-linear"></iconify-icon>
                                    Nonaktifkan
                                </button>
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