<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$groupedBookmarks = $groupedBookmarks ?? [];
$totalBookmarks = $totalBookmarks ?? 0;
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Bookmark Koleksi PBJ</h6>
        <p class="text-secondary-light mb-0">Daftar materi pengetahuan, artikel, template, dan video yang Anda simpan sebagai favorit.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary-50 text-primary-600 border border-primary-200 px-12 py-8 radius-8 text-sm">
            <i class="fas fa-bookmark me-1"></i> Total: <strong><?= (int) $totalBookmarks ?> Item</strong>
        </span>
    </div>
</div>

<?php if ($totalBookmarks === 0): ?>
    <div class="card border-0 radius-16 shadow-sm bg-white">
        <div class="card-body p-48 text-center">
            <span class="w-72-px h-72-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center mb-20 shadow-xs">
                <iconify-icon icon="solar:bookmark-broken" class="text-4xl"></iconify-icon>
            </span>
            <h5 class="mb-12 fw-bold text-dark">Belum Ada Konten yang Disimpan</h5>
            <p class="text-secondary-light mb-24 max-w-500-px mx-auto text-sm">
                Anda belum menyimpan materi apa pun ke bookmark. Anda dapat menandai artikel, panduan pengadaan, template dokumen, dan infografik saat menjelajahi modul Bunga Rampai.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="<?= route_to('dashboard.knowledge.articles') ?>" class="btn btn-outline-primary-600 radius-8 px-20 py-10 text-sm">
                    Jelajahi Artikel PBJ
                </a>
                <a href="<?= route_to('dashboard.knowledge.templates') ?>" class="btn btn-outline-info-600 radius-8 px-20 py-10 text-sm">
                    Jelajahi Template Dokumen
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row gy-24">
        <?php foreach ($groupedBookmarks as $typeKey => $group): ?>
            <?php if (empty($group['items'])) continue; ?>
            <div class="col-12">
                <div class="card border-0 radius-16 shadow-sm bg-white">
                    <div class="card-header border-bottom p-20 bg-white d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:bookmark-square-bold" class="text-primary-600 text-xl"></iconify-icon>
                            <h6 class="text-base fw-bold mb-0"><?= esc($group['label']) ?></h6>
                        </div>
                        <span class="badge bg-neutral-100 text-neutral-800 border radius-6 px-10 py-4 text-xs fw-semibold">
                            <?= count($group['items']) ?> Tersimpan
                        </span>
                    </div>
                    <div class="card-body p-20">
                        <div class="table-responsive scroll-sm">
                            <table class="table bordered-table sm-table mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>Judul Konten Pengetahuan</th>
                                        <th style="width: 180px;">Disimpan Pada</th>
                                        <th style="width: 140px;" class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($group['items'] as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <iconify-icon icon="solar:file-text-linear" class="text-primary-600 text-lg flex-shrink-0"></iconify-icon>
                                                    <span class="fw-semibold text-dark text-sm"><?= esc($item['title']) ?></span>
                                                </div>
                                            </td>
                                            <td class="text-secondary-light text-xs">
                                                <i class="far fa-clock me-1"></i>
                                                <?= esc(date('d M Y, H:i', strtotime($item['created_at']))) ?>
                                            </td>
                                            <td class="text-end">
                                                <?php
                                                $targetUrl = '#';
                                                if ($typeKey === 'knowledge_article') {
                                                    $targetUrl = route_to('dashboard.knowledge.articles.detail', (string) $item['resource_id']);
                                                } elseif ($typeKey === 'knowledge_case') {
                                                    $targetUrl = route_to('dashboard.knowledge.cases.detail', (string) $item['resource_id']);
                                                } elseif ($typeKey === 'knowledge_faq') {
                                                    $targetUrl = route_to('dashboard.knowledge.faqs.detail', (string) $item['resource_id']);
                                                } elseif ($typeKey === 'knowledge_template') {
                                                    $targetUrl = route_to('dashboard.knowledge.templates.detail', (string) $item['resource_id']);
                                                } elseif ($typeKey === 'knowledge_media') {
                                                    $targetUrl = route_to('dashboard.knowledge.media-gallery.detail', (string) $item['resource_id']);
                                                }
                                                ?>
                                                <a href="<?= $targetUrl ?>" class="btn btn-outline-primary-600 btn-sm radius-8 px-12 py-6 d-inline-flex align-items-center gap-1 text-xs">
                                                    <iconify-icon icon="solar:eye-linear"></iconify-icon>
                                                    <span>Buka Konten</span>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>