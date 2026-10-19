<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>
<?php
$grouped = $grouped ?? [];
$totalBookmarks = 0;
foreach ($grouped as $g) { $totalBookmarks += count($g['items'] ?? []); }
?>
<div class="ws-page-header">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="ws-eyebrow"><i class="fas fa-bookmark"></i> Personal</span>
            <h1>Bookmark Saya</h1>
            <p>Akses cepat ke konten yang Anda simpan dari Bunga Rampai, Regulasi, Berita, Event, dan Arena Belajar.</p>
        </div>
        <div>
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold">
                <i class="fas fa-bookmark me-1"></i> Total <?= (int) $totalBookmarks ?> Tersimpan
            </span>
        </div>
    </div>
</div>

<?php if ($msg = session()->getFlashdata('success')): ?>
    <div class="alert alert-success rounded-4 mb-3"><?= esc($msg) ?></div>
<?php endif; ?>
<?php if ($msg = session()->getFlashdata('info')): ?>
    <div class="alert alert-info rounded-4 mb-3"><?= esc($msg) ?></div>
<?php endif; ?>
<?php if ($msg = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger rounded-4 mb-3"><?= esc($msg) ?></div>
<?php endif; ?>

<?php if ($totalBookmarks === 0): ?>
    <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white text-center">
        <i class="fas fa-bookmark fs-2 text-muted opacity-50 mb-2"></i>
        <h2 class="h5 fw-bold mb-2" style="color: #082f5c;">Belum Ada Bookmark</h2>
        <p class="text-muted small mb-0">Buka halaman publik mana pun dan klik tombol <strong>Simpan</strong> untuk menambahkannya.</p>
    </div>
<?php else: ?>
    <?php foreach ($grouped as $type => $group): ?>
        <?php if (empty($group['items'])) { continue; } ?>
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 mb-3 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="h5 mb-0"><?= esc($group['label']) ?></h2>
                <span class="badge bg-primary-subtle text-primary-emphasis"><?= count($group['items']) ?></span>
            </div>
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0">
                    <thead><tr><th>Judul</th><th>Disimpan</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($group['items'] as $item): ?>
                            <tr>
                                <td>
                                    <?php $href = \App\Models\Knowledge\BookmarkModel::routeFor($type, (int) $item['resource_id']); ?>
                                    <?php if ($href !== null): ?>
                                        <a class="text-primary-600 fw-semibold" href="<?= esc($href) ?>"><?= esc($item['title']) ?></a>
                                    <?php else: ?>
                                        <span><?= esc($item['title']) ?></span>
                                    <?php endif; ?>
                                    <small class="d-block text-muted">#<?= (int) $item['resource_id'] ?> · <?= esc($item['created_at']) ?></small>
                                </td>
                                <td><?= esc($item['created_at']) ?></td>
                                <td class="text-end">
                                    <form method="post" action="<?= route_to('workspace.bookmarks.index') ?>" class="d-inline" onsubmit="return confirm('Hapus bookmark ini?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="type" value="<?= esc($type) ?>">
                                        <input type="hidden" name="id" value="<?= (int) $item['resource_id'] ?>">
                                        <button type="submit" class="btn btn-link text-danger-main p-0">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<?= $this->endSection() ?>