<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $stages = $stages ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Tahapan PBJ</h6>
        <p class="text-secondary-light mb-0">Master data siklus tahapan Pengadaan Barang/Jasa Pemerintah untuk taksonomi dan filter regulasi.</p>
    </div>
    <a href="<?= site_url('dashboard/regulasi/tahapan-pbj/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
        <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
        <span>Tambah Tahapan</span>
    </a>
</div>

<!-- Reusable Tabbed Console Navigation -->
<?= view('dashboard/regulation/_nav_regulation', ['section' => 'stages', 'activeSub' => 'tahapan-pbj']) ?>

<div class="card radius-12 border bg-base shadow-sm">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 align-middle begawi-table" data-table-name="tahapan-siklus-pbj" data-page-length="10">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">Urutan</th>
                        <th>Kode / Key</th>
                        <th>Nama Tahapan Siklus PBJ</th>
                        <th>Deskripsi</th>
                        <th class="text-center" style="width: 150px;">Jumlah Dokumen</th>
                        <th class="text-center" style="width: 110px;">Status</th>
                        <th class="text-center no-sort no-export action-col" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stages)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-32 text-secondary-light">
                                <iconify-icon icon="solar:route-outline" class="text-4xl d-block mb-2 text-neutral-400"></iconify-icon>
                                Belum ada tahapan PBJ terdaftar.
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($stages as $s): ?>
                        <?php $docCount = (int) ($s['regulation_count'] ?? 0); ?>
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-neutral-100 text-neutral-800 radius-6 px-10 py-4 fw-bold">#<?= (int) ($s['sort_order'] ?? 0) ?></span>
                            </td>
                            <td><code class="text-primary-700 bg-primary-50 px-8 py-2 radius-4 text-xs fw-bold"><?= esc($s['key'] ?? '-') ?></code></td>
                            <td><h6 class="text-sm mb-0 fw-bold text-neutral-900"><?= esc($s['name'] ?? '-') ?></h6></td>
                            <td><span class="text-xs text-secondary-light text-line-2 max-w-350-px"><?= esc($s['description'] ?? '-') ?: '-' ?></span></td>
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
                                <?= view('dashboard/components/status_badge', ['status' => ($s['is_active'] ?? 0) ? 'active' : 'inactive']) ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <a href="<?= site_url('dashboard/regulasi/tahapan-pbj/' . (int) $s['id'] . '/ubah') ?>" class="w-32-px h-32-px bg-warning-focus text-warning-main rounded-circle d-inline-flex align-items-center justify-content-center" title="Ubah Tahapan">
                                        <iconify-icon icon="solar:pen-new-square-outline"></iconify-icon>
                                    </a>

                                    <?php if ($docCount === 0): ?>
                                        <!-- Jika 0 dokumen: Tombol Hapus Permanen -->
                                        <button type="button" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center border-0" title="Hapus Permanen (0 Dokumen)" data-bs-toggle="modal" data-bs-target="#deleteStageModal<?= (int) $s['id'] ?>">
                                            <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                        </button>
                                    <?php else: ?>
                                        <!-- Jika > 0 dokumen: Opsi Nonaktifkan / Aktifkan -->
                                        <?php if (! empty($s['is_active'])): ?>
                                            <button type="button" class="w-32-px h-32-px bg-warning-focus text-warning-main rounded-circle d-inline-flex align-items-center justify-content-center border-0" title="Nonaktifkan (Ada <?= $docCount ?> Dokumen)" data-bs-toggle="modal" data-bs-target="#deactivateStageModal<?= (int) $s['id'] ?>">
                                                <iconify-icon icon="solar:shield-cross-outline"></iconify-icon>
                                            </button>
                                        <?php else: ?>
                                            <form method="post" action="<?= site_url('dashboard/regulasi/tahapan-pbj/' . (int) $s['id'] . '/toggle') ?>" class="d-inline" onsubmit="return confirm('Aktifkan kembali tahapan <?= esc($s['name'], 'js') ?>?');">
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

<!-- Modals Konfirmasi Hapus & Nonaktifkan Tahapan -->
<?php foreach ($stages as $s): ?>
    <?php $docCount = (int) ($s['regulation_count'] ?? 0); ?>
    <?php if ($docCount === 0): ?>
        <div class="modal fade" id="deleteStageModal<?= (int) $s['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content radius-12 border-0 shadow">
                    <div class="modal-header border-bottom py-16 px-24">
                        <h6 class="modal-title text-md fw-bold text-danger-600 d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:trash-bin-trash-bold" class="text-xl"></iconify-icon>
                            <span>Hapus Permanen Tahapan PBJ</span>
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="<?= site_url('dashboard/regulasi/tahapan-pbj/' . (int) $s['id'] . '/hapus') ?>">
                        <?= csrf_field() ?>
                        <div class="modal-body p-24">
                            <p class="text-secondary-light mb-12">Apakah Anda yakin ingin menghapus tahapan siklus PBJ berikut secara permanen?</p>
                            <div class="p-16 radius-8 bg-neutral-50 border mb-16">
                                <div class="fw-bold text-neutral-900"><?= esc($s['name']) ?></div>
                                <div class="text-xs text-secondary-light mt-1">Kode: <code><?= esc($s['key']) ?></code> &bull; Urutan: #<?= (int) ($s['sort_order'] ?? 0) ?></div>
                                <div class="badge bg-success-50 text-success-600 mt-2">0 Dokumen Terkait (Aman Dihapus)</div>
                            </div>
                            <p class="text-xs text-danger-600 mb-0">
                                <iconify-icon icon="solar:info-circle-bold" class="align-middle me-1"></iconify-icon>
                                Tindakan ini tidak dapat dibatalkan. Data tahapan akan dihapus permanen dari basis data.
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
        <div class="modal fade" id="deactivateStageModal<?= (int) $s['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content radius-12 border-0 shadow">
                    <div class="modal-header border-bottom py-16 px-24">
                        <h6 class="modal-title text-md fw-bold text-warning-main d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:shield-cross-bold" class="text-xl"></iconify-icon>
                            <span>Nonaktifkan Tahapan PBJ</span>
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="<?= site_url('dashboard/regulasi/tahapan-pbj/' . (int) $s['id'] . '/hapus') ?>">
                        <?= csrf_field() ?>
                        <div class="modal-body p-24">
                            <div class="alert alert-warning bg-warning-50 text-warning-main border-warning-100 p-16 radius-8 mb-16 d-flex align-items-start gap-2">
                                <iconify-icon icon="solar:danger-triangle-bold" class="text-xl flex-shrink-0 mt-2"></iconify-icon>
                                <div class="text-xs">
                                    Tahapan ini tidak dapat dihapus permanen karena masih terhubung dengan <strong><?= $docCount ?> dokumen regulasi</strong>.
                                </div>
                            </div>
                            <p class="text-secondary-light mb-12">Apakah Anda ingin <strong>menonaktifkan</strong> tahapan <strong><?= esc($s['name']) ?></strong>?</p>
                            <div class="p-16 radius-8 bg-neutral-50 border mb-16">
                                <div class="fw-bold text-neutral-900"><?= esc($s['name']) ?></div>
                                <div class="text-xs text-secondary-light mt-1">Status saat ini: <span class="badge bg-success-50 text-success-600">Aktif</span> &bull; <strong><?= $docCount ?> Dokumen Terkait</strong></div>
                            </div>
                            <p class="text-xs text-secondary-light mb-0">
                                Tahapan yang dinonaktifkan tidak akan muncul pada pilihan filter/tagging regulasi baru, namun seluruh relasi naskah yang sudah terhubung tetap aman.
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
