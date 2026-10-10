<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $item = $item ?? []; $coverSrc = null; if (! empty($item['cover_path'])) { $coverSrc = base_url('file/' . (int) $item['cover_file_id']); } ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Berita</h6>
        <p class="text-secondary-light mb-0">ID: <?= (int) ($newsId ?? 0) ?> · <?= esc($item['workflow_status'] ?? 'draft') ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= route_to('dashboard.communication.news.edit', (string) ($newsId ?? '')) ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Ubah</a>
        <a href="<?= route_to('dashboard.communication.news') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
    </div>
</div>
<div class="row gy-4">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body p-0">
                <div class="h-260-px bg-neutral-100 d-flex align-items-center justify-content-center overflow-hidden">
                    <?php if ($coverSrc): ?>
                        <img src="<?= esc($coverSrc) ?>" alt="<?= esc($item['alt_text'] ?? $item['title'] ?? '') ?>" style="width:100%;height:100%;object-fit:cover;">
                    <?php else: ?>
                        <iconify-icon icon="solar:news-outline" class="text-primary-600 text-6xl"></iconify-icon>
                    <?php endif; ?>
                </div>
                <div class="p-24">
                    <h5 class="mb-12"><?= esc($item['title'] ?? '(Tanpa judul)') ?></h5>
                    <div class="d-flex flex-wrap align-items-center gap-16 text-secondary-light text-sm mb-16">
                        <span><iconify-icon icon="solar:tag-outline" class="me-4"></iconify-icon> <?= esc($item['category_name'] ?? 'Tanpa kategori') ?></span>
                        <span><iconify-icon icon="solar:user-outline" class="me-4"></iconify-icon> <?= esc($item['author_name'] ?? '-') ?></span>
                        <span><iconify-icon icon="solar:calendar-outline" class="me-4"></iconify-icon> <?= ! empty($item['published_at']) ? esc(date('d M Y H:i', strtotime($item['published_at']))) : 'Belum dipublikasikan' ?></span>
                        <?= view('dashboard/components/status_badge', ['status' => $item['workflow_status'] ?? 'draft']) ?>
                    </div>
                    <?php if (! empty($item['summary'])): ?>
                        <p class="text-secondary-light mb-16"><strong>Ringkasan:</strong> <?= nl2br(esc($item['summary'])) ?></p>
                    <?php endif; ?>
                    <p class="mb-0"><?= ! empty($item['content']) ? nl2br(esc($item['content'])) : '<em class="text-secondary-light">Konten belum diisi.</em>' ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card"><div class="card-body p-24">
            <p class="text-secondary-light mb-4">Slug</p>
            <p class="mb-16"><code><?= esc($item['slug'] ?? '-') ?></code></p>
            <p class="text-secondary-light mb-4">Visibilitas</p>
            <p class="mb-16"><?= esc(ucfirst($item['visibility'] ?? '-')) ?></p>
            <p class="text-secondary-light mb-4">View</p>
            <p class="mb-16"><?= (int) ($item['view_count'] ?? 0) ?> kali</p>
            <p class="text-secondary-light mb-4">Diperbarui</p>
            <p class="mb-16"><?= esc($item['updated_at'] ?? '-') ?></p>
            <button type="button" class="btn btn-outline-danger-600 w-100 radius-8 d-flex align-items-center justify-content-center gap-2"
                data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                data-action="<?= route_to('dashboard.communication.news.delete', (string) ($newsId ?? '')) ?>"
                data-method="POST"
                data-title="Hapus Berita"
                data-message="Apakah Anda yakin ingin menghapus berita ini secara permanen?"
                data-btn-text="Hapus Berita"
                data-btn-class="btn-danger-600"
                data-icon="fluent:delete-24-regular"
                data-icon-color="danger">
                <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
                Hapus Berita
            </button>
        </div></div>
    </div>
</div>
<?= $this->endSection() ?>
