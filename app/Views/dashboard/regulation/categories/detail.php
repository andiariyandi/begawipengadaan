<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $category = $category ?? []; $regulations = $regulations ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Kategori</h6>
        <p class="text-secondary-light mb-0">ID: <?= esc($category['id'] ?? '-') ?></p>
    </div>
    <div class="d-flex gap-12">
        <a href="<?= site_url('dashboard/regulasi/kategori') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
        <a href="<?= site_url('dashboard/regulasi/kategori/' . (int) $category['id'] . '/ubah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Ubah</a>
    </div>
</div>

<div class="card mb-24">
    <div class="card-body p-24">
        <div class="row gy-20">
            <div class="col-md-6"><p class="text-secondary-light mb-4">Nama Kategori</p><h6 class="mb-0"><?= esc($category['name'] ?? '-') ?></h6></div>
            <div class="col-md-3"><p class="text-secondary-light mb-4">Slug</p><h6 class="mb-0 text-primary-600"><?= esc($category['slug'] ?? '-') ?></h6></div>
            <div class="col-md-3">
                <p class="text-secondary-light mb-4">Status</p>
                <?= view('dashboard/components/status_badge', ['status' => ($category['is_active'] ?? 0) ? 'active' : 'inactive']) ?>
            </div>
            <div class="col-12">
                <p class="text-secondary-light mb-4">Deskripsi</p>
                <p class="mb-0"><?= esc($category['description'] ?? '-') ?: '-' ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-24">
        <h6 class="text-lg mb-16">Regulasi dalam Kategori Ini (<?= count($regulations) ?>)</h6>
        <?php if (empty($regulations)): ?>
            <p class="text-secondary-light mb-0">Belum ada regulasi dalam kategori ini.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table bordered-table sm-table mb-0">
                    <thead><tr><th>Nomor</th><th>Judul</th><th>Tahun</th><th>Status</th><th class="text-center">Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($regulations as $r): ?>
                            <tr>
                                <td><?= esc($r['document_number'] ?? '-') ?></td>
                                <td><?= esc($r['title'] ?? '-') ?></td>
                                <td><?= esc($r['document_year'] ?? '-') ?></td>
                                <td><?= view('dashboard/components/status_badge', ['status' => $r['workflow_status'] ?? 'draft']) ?></td>
                                <td class="text-center">
                                    <a href="<?= site_url('dashboard/regulasi/dokumen/' . (int) $r['id']) ?>" class="text-primary-600">Detail</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="mt-16">
    <button type="button" class="btn btn-outline-danger-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2"
        data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
        data-action="<?= site_url('dashboard/regulasi/kategori/' . (int) $category['id'] . '/hapus') ?>"
        data-method="POST"
        data-title="Hapus Kategori Regulasi"
        data-message="Apakah Anda yakin ingin menghapus kategori '<?= esc($category['name'] ?? '') ?>' ini?"
        data-btn-text="Hapus Kategori"
        data-btn-class="btn-danger-600"
        data-icon="fluent:delete-24-regular"
        data-icon-color="danger">
        <iconify-icon icon="solar:trash-bin-trash-linear" class="text-xl"></iconify-icon>
        Hapus Kategori
    </button>
</div>
<?= $this->endSection() ?>