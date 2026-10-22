<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Kategori Layanan PBJ') ?></h6>
        <p class="text-secondary-light mb-0">Klasifikasi resmi pengadaan (Barang, Pekerjaan Konstruksi, Jasa Konsultansi, Jasa Lainnya, Swakelola, E-Purchasing).</p>
    </div>
    <a href="<?= site_url('dashboard/landing/kategori-layanan/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
        <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
        <span>Tambah Kategori</span>
    </a>
</div>

<!-- Reusable Tabbed Console Navigation -->
<?= view('dashboard/landing/_nav_landing', ['section' => 'portal', 'activeSub' => 'kategori-layanan']) ?>

<div class="card basic-data-table">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle begawi-table" data-table-name="kategori-layanan" data-page-length="10">
                <thead>
                    <tr>
                        <th style="width: 50px;">Urutan</th>
                        <th>Nama Kategori</th>
                        <th>Kode / Slug</th>
                        <th>Deskripsi</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 120px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-secondary-light">Belum ada kategori layanan. Klik tombol di atas untuk membuat.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td class="text-center fw-bold text-secondary-light"><?= esc($cat['sort_order']) ?></td>
                                <td>
                                    <strong class="text-sm d-block text-primary-light"><?= esc($cat['name']) ?></strong>
                                </td>
                                <td>
                                    <code class="text-xs"><?= esc($cat['code']) ?></code>
                                </td>
                                <td>
                                    <p class="text-xs text-secondary-light mb-0"><?= esc($cat['description']) ?></p>
                                </td>
                                <td>
                                    <a href="<?= site_url('dashboard/landing/kategori-layanan/toggle/' . $cat['id']) ?>" class="badge <?= $cat['is_active'] ? 'bg-success-100 text-success-600' : 'bg-neutral-100 text-secondary-light' ?> px-10 py-6 radius-4 text-xs" title="Klik untuk ubah status">
                                        <?= $cat['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="<?= site_url('dashboard/landing/kategori-layanan/ubah/' . $cat['id']) ?>" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-success-main hover-text-white" title="Ubah Kategori">
                                            <iconify-icon icon="solar:pen-new-square-linear"></iconify-icon>
                                        </a>
                                        <a href="<?= site_url('dashboard/landing/kategori-layanan/hapus/' . $cat['id']) ?>" onclick="return confirm('Hapus kategori ini?')" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-danger-main hover-text-white" title="Hapus Kategori">
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
