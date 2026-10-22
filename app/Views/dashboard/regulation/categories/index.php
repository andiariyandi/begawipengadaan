<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/regulation/_nav_regulation', ['activeSub' => 'categories']) ?>
<?php $categories = $categories ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Kategori Regulasi</h6>
        <p class="text-secondary-light mb-0">Kelola taksonomi dan klasifikasi jenis dokumen regulasi Pengadaan Barang/Jasa.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/regulasi/dokumen') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:document-text-outline" class="text-xl"></iconify-icon>
            <span>Naskah Dokumen</span>
        </a>
        <a href="<?= site_url('dashboard/regulasi/kategori/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
            <span>Tambah Kategori</span>
        </a>
    </div>
</div>

<div class="card radius-12 border bg-white shadow-sm">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 align-middle begawi-table" data-table-name="kategori-regulasi" data-page-length="10">
                <thead>
                    <tr>
                        <th style="min-width: 240px;">Kategori Regulasi</th>
                        <th>Slug URL</th>
                        <th>Deskripsi</th>
                        <th class="text-center" style="width: 150px;">Jumlah Dokumen</th>
                        <th class="text-center" style="width: 110px;">Status</th>
                        <th class="text-center no-sort no-export action-col" style="width: 140px;">Aksi</th>
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
                        <?php $docCount = (int) ($c['regulation_count'] ?? 0); ?>
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
                            <td><span class="text-xs text-secondary-light text-line-2 max-w-300-px"><?= esc($c['description'] ?? '-') ?: '-' ?></span></td>
                            <td class="text-center">
                                <?php if ($docCount > 0): ?>
                                    <span class="badge bg-primary-50 text-primary-600 radius-6 px-10 py-4 text-xs fw-bold d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:document-text-outline" class="text-sm"></iconify-icon>
                                        <?= number_format($docCount) ?> Dokumen
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-neutral-100 text-neutral-600 radius-6 px-10 py-4 text-xs fw-semibold">
                                        0 Dokumen
                                    </span>
                                <?php endif; ?>
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

                                    <?php if ($docCount === 0): ?>
                                        <!-- Jika 0 dokumen: Tombol Hapus Permanen -->
                                        <button type="button" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center border-0" title="Hapus Permanen (0 Dokumen)" data-bs-toggle="modal" data-bs-target="#deleteCatModal<?= (int) $c['id'] ?>">
                                            <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                        </button>
                                    <?php else: ?>
                                        <!-- Jika > 0 dokumen: Opsi Nonaktifkan / Aktifkan -->
                                        <?php if (! empty($c['is_active'])): ?>
                                            <button type="button" class="w-32-px h-32-px bg-warning-focus text-warning-main rounded-circle d-inline-flex align-items-center justify-content-center border-0" title="Nonaktifkan (Ada <?= $docCount ?> Dokumen)" data-bs-toggle="modal" data-bs-target="#deactivateCatModal<?= (int) $c['id'] ?>">
                                                <iconify-icon icon="solar:shield-cross-outline"></iconify-icon>
                                            </button>
                                        <?php else: ?>
                                            <form method="post" action="<?= site_url('dashboard/regulasi/kategori/' . (int) $c['id'] . '/toggle') ?>" class="d-inline" onsubmit="return confirm('Aktifkan kembali kategori <?= esc($c['name'], 'js') ?>?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center border-0" title="Aktifkan Kembali">
                                                    <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modals Konfirmasi Hapus & Nonaktifkan -->
<?php foreach ($categories as $c): ?>
    <?php $docCount = (int) ($c['regulation_count'] ?? 0); ?>
    <?php if ($docCount === 0): ?>
        <div class="modal fade" id="deleteCatModal<?= (int) $c['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content radius-12 border-0 shadow">
                    <div class="modal-header border-bottom py-16 px-24">
                        <h6 class="modal-title text-md fw-bold text-danger-600 d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:trash-bin-trash-bold" class="text-xl"></iconify-icon>
                            <span>Hapus Permanen Kategori</span>
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="<?= site_url('dashboard/regulasi/kategori/' . (int) $c['id'] . '/hapus') ?>">
                        <?= csrf_field() ?>
                        <div class="modal-body p-24">
                            <p class="text-secondary-light mb-12">Apakah Anda yakin ingin menghapus kategori berikut secara permanen?</p>
                            <div class="p-16 radius-8 bg-neutral-50 border mb-16">
                                <div class="fw-bold text-neutral-900"><?= esc($c['name']) ?></div>
                                <div class="text-xs text-secondary-light mt-1">Slug: <code><?= esc($c['slug']) ?></code></div>
                                <div class="badge bg-success-50 text-success-600 mt-2">0 Dokumen Terkait (Aman Dihapus)</div>
                            </div>
                            <p class="text-xs text-danger-600 mb-0">
                                <iconify-icon icon="solar:info-circle-bold" class="align-middle me-1"></iconify-icon>
                                Tindakan ini tidak dapat dibatalkan. Data kategori akan dihapus dari basis data.
                            </p>
                        </div>
                        <div class="modal-footer border-top py-12 px-24">
                            <button type="button" class="btn btn-outline-neutral-300 radius-8 px-16 py-8 text-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger-600 radius-8 px-20 py-8 text-sm">Ya, Hapus Permanen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="modal fade" id="deactivateCatModal<?= (int) $c['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content radius-12 border-0 shadow">
                    <div class="modal-header border-bottom py-16 px-24">
                        <h6 class="modal-title text-md fw-bold text-warning-main d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:shield-cross-bold" class="text-xl"></iconify-icon>
                            <span>Nonaktifkan Kategori</span>
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="<?= site_url('dashboard/regulasi/kategori/' . (int) $c['id'] . '/hapus') ?>">
                        <?= csrf_field() ?>
                        <div class="modal-body p-24">
                            <div class="alert alert-warning bg-warning-50 text-warning-main border-warning-100 p-16 radius-8 mb-16 d-flex align-items-start gap-2">
                                <iconify-icon icon="solar:danger-triangle-bold" class="text-xl flex-shrink-0 mt-2"></iconify-icon>
                                <div class="text-xs">
                                    Kategori ini tidak dapat dihapus permanen karena masih terhubung dengan <strong><?= $docCount ?> dokumen regulasi</strong>.
                                </div>
                            </div>
                            <p class="text-secondary-light mb-12">Apakah Anda ingin <strong>menonaktifkan</strong> kategori <strong><?= esc($c['name']) ?></strong>?</p>
                            <div class="p-16 radius-8 bg-neutral-50 border mb-16">
                                <div class="fw-bold text-neutral-900"><?= esc($c['name']) ?></div>
                                <div class="text-xs text-secondary-light mt-1">Status saat ini: <span class="badge bg-success-50 text-success-600">Aktif</span> &bull; <strong><?= $docCount ?> Dokumen</strong></div>
                            </div>
                            <p class="text-xs text-secondary-light mb-0">
                                Kategori yang dinonaktifkan tidak akan muncul pada pilihan dokumen baru publik, namun seluruh naskah yang sudah terbit tetap aman.
                            </p>
                        </div>
                        <div class="modal-footer border-top py-12 px-24">
                            <button type="button" class="btn btn-outline-neutral-300 radius-8 px-16 py-8 text-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning-600 text-neutral-900 radius-8 px-20 py-8 text-sm fw-semibold">Ya, Nonaktifkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?= $this->endSection() ?>
