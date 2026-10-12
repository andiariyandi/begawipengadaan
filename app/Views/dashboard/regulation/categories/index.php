<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $categories = $categories ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Kategori Regulasi</h6>
        <p class="text-secondary-light mb-0">Kelola taksonomi dan klasifikasi jenis dokumen regulasi Pengadaan Barang/Jasa.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/regulasi/kategori/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
            <span>Tambah Kategori</span>
        </a>
    </div>
</div>

<div class="card radius-12 border bg-white shadow-sm">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="min-width: 260px;">Kategori Regulasi</th>
                        <th>Slug URL</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Jumlah Dokumen</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-32 text-secondary-light">
                                <iconify-icon icon="solar:folder-with-files-outline" class="text-4xl d-block mb-2 text-neutral-400"></iconify-icon>
                                Belum ada kategori regulasi terdaftar.
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($categories as $c): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <?php if (! empty($c['icon_file_id'])): ?>
                                        <div class="w-40-px h-40-px radius-8 overflow-hidden flex-shrink-0 border bg-neutral-50 p-2">
                                            <img src="<?= base_url('file/' . (int) $c['icon_file_id']) ?>" alt="Icon" class="w-100 h-100 object-fit-contain">
                                        </div>
                                    <?php else: ?>
                                        <div class="w-40-px h-40-px bg-primary-50 text-primary-600 radius-8 d-flex align-items-center justify-content-center flex-shrink-0">
                                            <iconify-icon icon="<?= esc($c['icon_class'] ?? 'solar:document-outline') ?>" class="text-xl"></iconify-icon>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h6 class="text-sm mb-0 fw-bold text-neutral-900"><?= esc($c['name']) ?></h6>
                                    </div>
                                </div>
                            </td>
                            <td><code class="text-primary-700 bg-primary-50 px-8 py-2 radius-4 text-xs"><?= esc($c['slug']) ?></code></td>
                            <td><span class="text-xs text-secondary-light text-line-2 max-w-350-px"><?= esc($c['description'] ?? '-') ?: '-' ?></span></td>
                            <td class="text-center">
                                <span class="badge bg-neutral-100 text-neutral-800 radius-6 px-10 py-4 text-xs fw-bold">
                                    <?= number_format((int) ($c['regulation_count'] ?? 0)) ?> Dokumen
                                </span>
                            </td>
                            <td class="text-center">
                                <?= view('dashboard/components/status_badge', ['status' => ($c['is_active'] ?? 0) ? 'active' : 'inactive']) ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <a href="<?= site_url('dashboard/regulasi/kategori/' . (int) $c['id']) ?>" class="w-32-px h-32-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center" title="Lihat Detail">
                                        <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                    </a>
                                    <a href="<?= site_url('dashboard/regulasi/kategori/' . (int) $c['id'] . '/ubah') ?>" class="w-32-px h-32-px bg-warning-focus text-warning-main rounded-circle d-inline-flex align-items-center justify-content-center" title="Ubah Kategori">
                                        <iconify-icon icon="solar:pen-new-square-outline"></iconify-icon>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>