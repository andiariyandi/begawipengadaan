<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>
<?php
$grouped = $grouped ?? [];
$totalBookmarks = 0;
foreach ($grouped as $g) { $totalBookmarks += count($g['items'] ?? []); }
?>
<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2">Personal</p>
    <h1 class="mb-2">Bookmark Saya</h1>
    <p class="mb-0">Akses cepat ke konten yang Anda simpan dari Bunga Rampai, Regulasi, Berita, Event, dan Arena Belajar.</p>
    <p class="small text-muted mt-12 mb-0">Total: <strong><?= (int) $totalBookmarks ?></strong> bookmark tersimpan.</p>
</div>

<?php if ($msg = session()->getFlashdata('success')): ?>
    <div class="alert alert-success radius-8 mb-3"><?= esc($msg) ?></div>
<?php endif; ?>
<?php if ($msg = session()->getFlashdata('info')): ?>
    <div class="alert alert-info radius-8 mb-3"><?= esc($msg) ?></div>
<?php endif; ?>
<?php if ($msg = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-8 mb-3"><?= esc($msg) ?></div>
<?php endif; ?>

<?php if ($totalBookmarks === 0): ?>
    <div class="bg-white border rounded p-4">
        <p class="text-secondary-light mb-0">Belum ada bookmark. Buka halaman publik mana pun dan klik tombol <strong>Simpan</strong> untuk menambahkannya.</p>
    </div>
<?php else: ?>
    <?php foreach ($grouped as $type => $group): ?>
        <?php if (empty($group['items'])) { continue; } ?>
        <div class="bg-white border rounded p-4 mb-3">
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