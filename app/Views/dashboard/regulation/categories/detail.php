<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$category    = $category ?? [];
$regulations = $regulations ?? [];
$iconUrl     = ! empty($category['icon_file_id']) ? base_url('file/' . (int) $category['icon_file_id']) : null;
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Kategori Regulasi</h6>
        <p class="text-secondary-light mb-0">ID Registrasi: #<?= esc($category['id'] ?? '-') ?> &middot; Slug: <code><?= esc($category['slug'] ?? '-') ?></code></p>
    </div>
    <div class="d-flex gap-12">
        <a href="<?= site_url('dashboard/regulasi/kategori') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-10 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            <span>Kembali</span>
        </a>
        <a href="<?= site_url('dashboard/regulasi/kategori/' . (int) $category['id'] . '/ubah') ?>" class="btn btn-primary-600 radius-8 px-20 py-10 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:pen-2-outline" class="text-lg"></iconify-icon>
            <span>Ubah Kategori</span>
        </a>
    </div>
</div>

<div class="card radius-16 border shadow-sm mb-24 bg-white">
    <div class="card-body p-24">
        <div class="d-flex align-items-start gap-3 mb-20">
            <?php if ($iconUrl): ?>
                <div class="w-64-px h-64-px radius-12 overflow-hidden flex-shrink-0 border bg-neutral-50 p-2">
                    <img src="<?= esc($iconUrl) ?>" alt="Icon" class="w-100 h-100 object-fit-contain">
                </div>
            <?php else: ?>
                <div class="w-56-px h-56-px bg-primary-50 text-primary-600 radius-12 d-flex align-items-center justify-content-center text-3xl flex-shrink-0">
                    <iconify-icon icon="<?= esc($category['icon_class'] ?? 'solar:document-outline') ?>"></iconify-icon>
                </div>
            <?php endif; ?>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <h5 class="fw-bold mb-0 text-primary-light"><?= esc($category['name'] ?? '-') ?></h5>
                    <?= view('dashboard/components/status_badge', ['status' => ($category['is_active'] ?? 0) ? 'active' : 'inactive']) ?>
                </div>
                <code class="text-primary-700 bg-primary-50 px-8 py-2 radius-4 text-xs fw-bold"><?= esc($category['slug'] ?? '-') ?></code>
            </div>
        </div>

        <div class="border-top pt-16">
            <span class="text-xs text-secondary-light text-uppercase fw-semibold d-block mb-1">Deskripsi Kategori:</span>
            <p class="text-sm text-primary-light mb-0"><?= nl2br(esc($category['description'] ?? '-') ?: '-') ?></p>
        </div>
    </div>
</div>

<div class="card radius-12 border bg-white shadow-sm mb-24">
    <div class="card-header bg-base border-bottom py-16 px-24 d-flex align-items-center justify-content-between">
        <h6 class="text-md fw-bold mb-0">Regulasi dalam Kategori Ini (<?= count($regulations) ?>)</h6>
        <a href="<?= site_url('dashboard/regulasi/dokumen/tambah') ?>" class="btn btn-outline-primary-600 btn-sm radius-8">
            <iconify-icon icon="solar:add-circle-outline"></iconify-icon> Tambah Regulasi
        </a>
    </div>
    <div class="card-body p-24">
        <?php if (empty($regulations)): ?>
            <div class="text-center py-24 text-secondary-light">
                <iconify-icon icon="solar:document-outline" class="text-4xl d-block mb-2 text-neutral-400"></iconify-icon>
                Belum ada dokumen regulasi yang dimasukkan ke dalam kategori ini.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table bordered-table sm-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Nomor Dokumen</th>
                            <th>Judul Regulasi</th>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Workflow</th>
                            <th class="text-center">Status Legal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($regulations as $r): ?>
                            <tr>
                                <td>
                                    <a href="<?= site_url('dashboard/regulasi/dokumen/' . (int) $r['id']) ?>" class="fw-bold text-sm text-primary-700">
                                        <?= esc($r['document_number'] ?? '-') ?>
                                    </a>
                                </td>
                                <td><span class="text-xs text-primary-light fw-medium text-line-2"><?= esc($r['title'] ?? '-') ?></span></td>
                                <td class="text-center"><span class="fw-medium text-xs"><?= esc($r['document_year'] ?? '-') ?></span></td>
                                <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => $r['workflow_status'] ?? 'draft']) ?></td>
                                <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => $r['legal_status'] ?? 'active']) ?></td>
                                <td class="text-center">
                                    <a href="<?= site_url('dashboard/regulasi/dokumen/' . (int) $r['id']) ?>" class="w-32-px h-32-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center" title="Detail">
                                        <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div>
    <button type="button" class="btn btn-outline-danger-600 radius-8 px-20 py-10 d-inline-flex align-items-center gap-2"
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
        <span>Hapus Kategori</span>
    </button>
</div>
<?= $this->endSection() ?>