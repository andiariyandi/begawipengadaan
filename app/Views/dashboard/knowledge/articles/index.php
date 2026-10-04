<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$items     = $items ?? [];
$categories = $categories ?? [];
$status   = $filterStatus ?? null;
$keyword  = $filterKeyword ?? null;
$category = $filterCategory !== null ? (string) $filterCategory : '';
$statusLabels = [
    'draft' => 'Draft', 'submitted' => 'Diajukan', 'in_review' => 'Ditinjau',
    'approved' => 'Disetujui', 'published' => 'Dipublikasikan',
    'revision_required' => 'Perlu Revisi', 'rejected' => 'Ditolak', 'archived' => 'Diarsipkan',
];
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Artikel</h6>
        <p class="text-secondary-light mb-0">Kelola artikel dan praktik baik PBJ.</p>
    </div>
    <a href="<?= route_to('dashboard.knowledge.articles.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Artikel</a>
</div>
<form method="get" action="<?= route_to('dashboard.knowledge.articles') ?>" class="card mb-3">
    <div class="card-body p-16">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Pencarian</label>
                <input type="text" name="q" class="form-control radius-8" value="<?= esc($keyword ?? '') ?>" placeholder="Judul atau ringkasan">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select radius-8">
                    <option value="">— Semua —</option>
                    <?php foreach ($statusLabels as $k => $label): ?>
                        <option value="<?= $k ?>" <?= ($status === $k) ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Kategori</label>
                <select name="category" class="form-select radius-8">
                    <option value="">— Semua —</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= ($category === (string) $c['id']) ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary-600 radius-8 flex-grow-1">Filter</button>
                <a href="<?= route_to('dashboard.knowledge.articles') ?>" class="btn btn-outline-neutral-300 radius-8">Reset</a>
            </div>
        </div>
    </div>
</form>
<?php if (! empty($errors = session()->getFlashdata('errors'))): ?>
    <div class="alert alert-danger radius-8 mb-3"><ul class="mb-0"><?php foreach ((array) $errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<?php if ($msg = session()->getFlashdata('success')): ?>
    <div class="alert alert-success radius-8 mb-3"><?= esc($msg) ?></div>
<?php endif; ?>
<?php if ($msg = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-8 mb-3"><?= esc($msg) ?></div>
<?php endif; ?>
<div class="card">
    <div class="card-body p-24">
        <?php if (empty($items)): ?>
            <p class="text-secondary-light mb-0">Belum ada artikel. Klik "Tambah Artikel" untuk membuat konten baru.</p>
        <?php else: ?>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead><tr><th>Artikel</th><th>Kategori</th><th>Penulis</th><th>Diperbarui</th><th class="text-center">Status</th><th class="text-center">Aksi</th></tr></thead>
                <tbody>
                    <?php foreach ($items as $article): ?>
                        <?php
                            $cats = $this->db ? null : null;
                            $catRows = db_connect()->table('knowledge_article_categories kac')
                                ->select('kc.name')
                                ->join('knowledge_categories kc', 'kc.id = kac.category_id')
                                ->where('kac.article_id', (int) $article['id'])
                                ->get()->getResultArray();
                            $catNames = array_map(static fn ($r) => $r['name'], $catRows);
                            $statusKey = $article['workflow_status'] ?? 'draft';
                            $statusLabel = $statusLabels[$statusKey] ?? ucfirst($statusKey);
                        ?>
                        <tr>
                            <td><h6 class="text-md mb-0 fw-medium"><?= esc($article['title']) ?></h6><span class="text-sm text-secondary-light">#<?= (int) $article['id'] ?></span></td>
                            <td><?= esc(implode(', ', $catNames) ?: '—') ?></td>
                            <td><?= esc($article['author_name'] ?? '-') ?></td>
                            <td><?= esc($article['updated_at'] ?? '-') ?></td>
                            <td class="text-center"><span class="badge bg-primary-subtle text-primary-emphasis"><?= esc($statusLabel) ?></span></td>
                            <td class="text-center">
                                <a href="<?= route_to('dashboard.knowledge.articles.detail', (string) $article['id']) ?>" class="text-primary-600 me-12">Detail</a>
                                <a href="<?= route_to('dashboard.knowledge.articles.edit', (string) $article['id']) ?>" class="text-warning-main me-12">Ubah</a>
                                <form method="post" action="<?= route_to('dashboard.knowledge.articles.delete', (string) $article['id']) ?>" class="d-inline" onsubmit="return confirm('Arsipkan artikel ini?')">
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