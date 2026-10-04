<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$articles = (new \App\Models\Knowledge\ArticleModel())
    ->select('id, title, type, workflow_status')
    ->where('deleted_at IS NULL', null, false)
    ->where('workflow_status', 'published')
    ->orderBy('title', 'ASC')->findAll();
$media = (new \App\Models\Knowledge\KnowledgeMediaItemModel())
    ->select('id, title, type, workflow_status')
    ->where('workflow_status', 'published')
    ->orderBy('title', 'ASC')->findAll();
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div><h6 class="fw-semibold mb-1">Tambah Carousel</h6><p class="text-secondary-light mb-0">Aktifkan artikel atau media ke carousel utama.</p></div>
    <a href="<?= route_to('dashboard.knowledge.carousel') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<?php if ($msg = session()->getFlashdata('error')): ?><div class="alert alert-danger radius-8 mb-3"><?= esc($msg) ?></div><?php endif; ?>
<div class="card"><div class="card-body p-24">
    <form method="post" action="<?= route_to('dashboard.knowledge.carousel.create') ?>">
        <?= csrf_field() ?>
        <div class="row gy-20">
            <div class="col-md-4">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tipe Target</label>
                <select name="target_type" class="form-select radius-8" required>
                    <option value="article">Artikel</option>
                    <option value="media">Media</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Konten (Published)</label>
                <select name="target_id" class="form-select radius-8" required>
                    <option value="">— Pilih konten —</option>
                    <?php foreach ($articles as $a): ?>
                        <option value="<?= (int) $a['id'] ?>">[Artikel] <?= esc($a['title']) ?></option>
                    <?php endforeach; ?>
                    <?php foreach ($media as $m): ?>
                        <option value="<?= (int) $m['id'] ?>">[Media] <?= esc($m['title'] ?: '(Tanpa judul)') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Urutan</label>
                <input type="number" min="0" name="order" class="form-control radius-8" value="0">
            </div>
            <div class="col-12 d-flex justify-content-end gap-12">
                <a href="<?= route_to('dashboard.knowledge.carousel') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Aktifkan</button>
            </div>
        </div>
    </form>
</div></div>
<?= $this->endSection() ?>