<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Portal Ekosistem PBJ') ?></h6>
        <p class="text-secondary-light mb-0">Kelola tautan resmi sistem pengadaan nasional dan daerah (SPSE LPSE, SiRUP, E-Katalog, Bela Pengadaan, JDIH).</p>
    </div>
    <a href="<?= site_url('dashboard/landing/portal-eksternal/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
        <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
        <span>Tambah Portal</span>
    </a>
</div>

<div class="card basic-data-table">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">Urutan</th>
                        <th style="width: 60px;">Ikon</th>
                        <th>Nama & Akronim</th>
                        <th>Kategori</th>
                        <th>URL Tautan Resmi</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 120px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($portals)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-secondary-light">Belum ada portal mitra. Klik "Tambah Portal" untuk membuat.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($portals as $p): ?>
                            <tr>
                                <td class="text-center fw-bold text-secondary-light"><?= esc($p['sort_order']) ?></td>
                                <td class="text-center">
                                    <div class="bg-primary-100 text-primary-600 radius-8 d-inline-flex align-items-center justify-content-center border border-neutral-200" style="width: 40px; height: 40px;">
                                        <iconify-icon icon="<?= esc($p['logo_url'] ?: 'solar:global-bold') ?>" class="text-lg"></iconify-icon>
                                    </div>
                                </td>
                                <td>
                                    <strong class="text-sm d-block text-primary-light"><?= esc($p['name']) ?></strong>
                                    <?php if (! empty($p['acronym'])): ?>
                                        <span class="badge bg-neutral-200 text-neutral-800 text-2xs"><?= esc($p['acronym']) ?></span>
                                    <?php endif; ?>
                                    <small class="text-secondary-light d-block"><?= esc($p['description']) ?></small>
                                </td>
                                <td>
                                    <span class="badge <?= $p['category'] === 'daerah' ? 'bg-info-100 text-info-600' : 'bg-warning-100 text-warning-main' ?> text-xs text-uppercase radius-4 px-8 py-4">
                                        <?= esc($p['category']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= esc($p['url']) ?>" target="_blank" rel="noopener noreferrer" class="text-primary-600 text-xs d-inline-flex align-items-center gap-1">
                                        <span class="text-truncate" style="max-width: 250px;"><?= esc($p['url']) ?></span>
                                        <iconify-icon icon="solar:arrow-right-up-linear"></iconify-icon>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?= site_url('dashboard/landing/portal-eksternal/toggle/' . $p['id']) ?>" class="badge <?= $p['is_active'] ? 'bg-success-100 text-success-600' : 'bg-neutral-100 text-secondary-light' ?> px-10 py-6 radius-4 text-xs" title="Klik untuk ubah status">
                                        <?= $p['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="<?= site_url('dashboard/landing/portal-eksternal/ubah/' . $p['id']) ?>" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-success-main hover-text-white" title="Ubah Portal">
                                            <iconify-icon icon="solar:pen-new-square-linear"></iconify-icon>
                                        </a>
                                        <a href="<?= site_url('dashboard/landing/portal-eksternal/hapus/' . $p['id']) ?>" onclick="return confirm('Hapus portal ini?')" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-danger-main hover-text-white" title="Hapus Portal">
                                            <iconify-icon icon="solar:trash-bin-trash-linear"></iconify-icon>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
