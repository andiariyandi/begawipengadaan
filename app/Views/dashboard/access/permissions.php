<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/users/_nav_access', ['activeSub' => 'roles']) ?>
<?php
$permissions = $permissions ?? [];
$pager = $pager ?? null;
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Daftar Permission</h6>
        <p class="text-secondary-light mb-0">Daftar hak akses granular BegawiPengadaan untuk setiap modul dan aksi.</p>
    </div>
    <div class="d-flex flex-wrap gap-12 align-items-center">
        <a href="<?= site_url('dashboard/akses/role') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-10 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            <span>Kembali ke Role</span>
        </a>

        <!-- 1-Click Sync dari Dashboard (Solusi Server Deploy Tanpa SSH/Terminal) -->
        <form action="<?= site_url('dashboard/akses/permission/sync-katalog') ?>" method="post" class="d-inline" onsubmit="return confirm('Jalankan sinkronisasi 90 katalog permission sistem ke database dan perbarui hak akses role default?');">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-success radius-8 px-16 py-10 d-inline-flex align-items-center gap-2" title="Sinkronkan seluruh 90 katalog permission sistem ke database secara instan">
                <iconify-icon icon="solar:refresh-outline" class="text-lg"></iconify-icon>
                <span>Sinkronkan Katalog Sistem</span>
            </button>
        </form>

        <!-- Tombol Buka Modal Import JSON/CSV -->
        <button type="button" class="btn btn-outline-primary-600 radius-8 px-16 py-10 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#importPermissionModal">
            <iconify-icon icon="solar:upload-track-outline" class="text-lg"></iconify-icon>
            <span>Import JSON / CSV</span>
        </button>

        <a href="<?= site_url('dashboard/akses/permission/tambah') ?>" class="btn btn-primary-600 radius-8 px-16 py-10 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline" class="text-lg"></iconify-icon>
            <span>Tambah Permission</span>
        </a>
    </div>
</div>

<div class="card border radius-12">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
        <h6 class="text-md mb-0">Hak Akses Granular</h6>
        <span class="badge bg-primary-50 text-primary-600 px-12 py-6 radius-8 fw-semibold text-xs">
            Total <?= count($permissions) ?> Permission
        </span>
    </div>

    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="min-width: 220px;">Kode Kunci (Key)</th>
                        <th style="min-width: 130px;">Domain / Modul</th>
                        <th>Nama &amp; Deskripsi</th>
                        <th class="text-center" style="width: 80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($permissions)): ?>
                        <tr>
                            <td colspan="4" class="p-0">
                                <?= view('dashboard/components/empty_state', [
                                    'title'       => 'Belum Ada Permission',
                                    'message'     => 'Belum ada permission yang terdaftar di sistem.',
                                    'actionUrl'   => site_url('dashboard/akses/permission/tambah'),
                                    'actionLabel' => 'Tambah Permission',
                                ]) ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($permissions as $permission): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-neutral-100 text-primary-600 font-monospace px-10 py-4 radius-4 fw-semibold text-xs">
                                            <?= esc($permission['key']) ?>
                                        </span>
                                        <?php if (! empty($permission['is_system'])): ?>
                                            <span class="badge bg-neutral-200 text-secondary-dark text-xxs" title="Izin Sistem Dilindungi">Sistem</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info-focus text-info-main px-12 py-4 rounded-pill fw-medium text-xs">
                                        <?= esc($permission['domain']) ?>
                                    </span>
                                </td>
                                <td>
                                    <h6 class="text-sm mb-2 fw-medium text-neutral-800"><?= esc($permission['name']) ?></h6>
                                    <span class="text-xs text-secondary-light"><?= esc($permission['description'] ?? '-') ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-outline-neutral-200 btn-sm radius-8 px-8 py-4 text-secondary-light" data-bs-toggle="dropdown" aria-expanded="false">
                                            <iconify-icon icon="solar:menu-dots-bold" class="text-lg"></iconify-icon>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8 p-8 border">
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= site_url('dashboard/akses/permission/' . (int) $permission['id']) ?>">
                                                    <iconify-icon icon="solar:eye-outline" class="text-lg text-primary-600"></iconify-icon>
                                                    <span>Detail Permission</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($pager): ?>
            <div class="card-footer border-top bg-transparent pt-16 pb-0 px-0">
                <?= $pager->links('permissions', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Import Permission (JSON / CSV) -->
<div class="modal fade" id="importPermissionModal" tabindex="-1" aria-labelledby="importPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-12 border-0 shadow">
            <form action="<?= site_url('dashboard/akses/permission/import') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom py-16 px-24">
                    <h6 class="modal-title fs-5" id="importPermissionModalLabel">
                        <i class="fas fa-file-import text-primary-600 me-2"></i> Import Berkas Permission
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-24">
                    <p class="text-secondary-light text-sm mb-16">
                        Unggah berkas berformat <strong>.JSON</strong> atau <strong>.CSV</strong> untuk menambahkan data permission secara massal ke database.
                    </p>

                    <div class="mb-20">
                        <label class="form-label fw-semibold text-neutral-800 text-sm mb-8">Pilih Berkas (.json / .csv) <span class="text-danger">*</span></label>
                        <input type="file" name="import_file" class="form-control radius-8" accept=".json,.csv" required>
                    </div>

                    <div class="bg-neutral-50 p-16 radius-8 border">
                        <span class="fw-semibold text-xs text-neutral-800 d-block mb-8">Format Kolom CSV (Pemisah Titik Koma atau Koma):</span>
                        <code class="text-xs text-primary-700 d-block mb-4">key;name;domain;description</code>
                        <span class="text-secondary-light text-xxs">Contoh baris:</span>
                        <code class="text-xxs text-neutral-700 d-block">bunga.article.manage;Mengelola Naskah PBJ;bunga;Akses membuat naskah PBJ</code>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-10" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-10">
                        <i class="fas fa-upload me-1"></i> Mulai Unggah &amp; Impor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
