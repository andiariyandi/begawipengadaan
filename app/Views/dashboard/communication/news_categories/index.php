<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/communication/_nav_communication', ['activeSub' => 'news-categories']) ?>
<?php
$items = $items ?? [];
$totalCats = count($items);
$activeCats = count(array_filter($items, fn($c) => !empty($c['is_active'])));
$inactiveCats = $totalCats - $activeCats;
$totalNewsAssigned = array_sum(array_column($items, 'news_count'));
?>

<!-- Breadcrumb & Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <div class="d-flex align-items-center gap-2 mb-4">
            <span class="badge bg-primary-50 text-primary-600 border border-primary-200 px-8 py-4 radius-6 text-xs fw-semibold">
                <iconify-icon icon="solar:folder-with-files-bold" class="align-middle me-1"></iconify-icon> Komunikasi & Informasi
            </span>
            <span class="text-secondary-light text-xs">/ Berita / Kategori</span>
        </div>
        <h5 class="fw-bold mb-1 text-primary-light">Kategori Berita</h5>
        <p class="text-secondary-light text-sm mb-0">Kelola dan organisasikan taksonomi kategori untuk publikasi artikel berita.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= route_to('dashboard.communication.news') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-10 d-inline-flex align-items-center gap-2 text-sm fw-medium">
            <iconify-icon icon="solar:document-text-linear" class="text-lg"></iconify-icon>
            <span>Daftar Berita</span>
        </a>
        <a href="<?= site_url('dashboard/komunikasi/kategori-berita/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-10 d-inline-flex align-items-center gap-2 text-sm fw-medium shadow-sm">
            <iconify-icon icon="solar:add-circle-linear" class="text-lg"></iconify-icon>
            <span>Tambah Kategori</span>
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-24">
    <div class="col-6 col-md-3">
        <div class="card h-100 border radius-12 p-16">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-xs fw-medium d-block mb-1">Total Kategori</span>
                    <h4 class="fw-bold mb-0 text-primary-light"><?= number_format($totalCats) ?></h4>
                </div>
                <div class="w-44-px h-44-px bg-primary-50 text-primary-600 radius-8 d-flex justify-content-center align-items-center">
                    <iconify-icon icon="solar:folder-favourite-bookmark-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border radius-12 p-16">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-xs fw-medium d-block mb-1">Kategori Aktif</span>
                    <h4 class="fw-bold mb-0 text-success-main"><?= number_format($activeCats) ?></h4>
                </div>
                <div class="w-44-px h-44-px bg-success-50 text-success-600 radius-8 d-flex justify-content-center align-items-center">
                    <iconify-icon icon="solar:check-circle-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border radius-12 p-16">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-xs fw-medium d-block mb-1">Kategori Nonaktif</span>
                    <h4 class="fw-bold mb-0 text-warning-main"><?= number_format($inactiveCats) ?></h4>
                </div>
                <div class="w-44-px h-44-px bg-warning-50 text-warning-600 radius-8 d-flex justify-content-center align-items-center">
                    <iconify-icon icon="solar:eye-closed-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border radius-12 p-16">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-xs fw-medium d-block mb-1">Total Artikel Terkait</span>
                    <h4 class="fw-bold mb-0 text-info-main"><?= number_format($totalNewsAssigned) ?></h4>
                </div>
                <div class="w-44-px h-44-px bg-info-50 text-info-600 radius-8 d-flex justify-content-center align-items-center">
                    <iconify-icon icon="solar:document-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card border radius-12 overflow-hidden shadow-sm">
    <div class="card-header bg-transparent border-bottom py-16 px-20 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <iconify-icon icon="solar:folders-outline" class="text-xl text-primary-600"></iconify-icon>
            <h6 class="fw-bold mb-0 text-base">Daftar Kategori Berita</h6>
            <span class="badge bg-neutral-100 text-neutral-600 radius-4 text-xs"><?= $totalCats ?> Item</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="position-relative">
                <input type="text" id="categorySearchInput" class="form-control form-control-sm radius-8 ps-36 text-xs" placeholder="Cari nama atau slug..." style="width: 220px;">
                <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-12 text-secondary-light text-sm"></iconify-icon>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table sm-table mb-0 align-middle" id="categoryTable">
                <thead class="bg-neutral-50 text-secondary-light text-xs">
                    <tr>
                        <th class="ps-20 py-12" style="width: 50px;">No</th>
                        <th class="py-12">Nama Kategori</th>
                        <th class="py-12">Slug</th>
                        <th class="py-12">Deskripsi</th>
                        <th class="text-center py-12">Artikel Berita</th>
                        <th class="text-center py-12">Status</th>
                        <th class="text-center pe-20 py-12" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-48">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <div class="w-64-px h-64-px bg-neutral-100 text-neutral-400 radius-circle d-flex align-items-center justify-content-center mb-12">
                                        <iconify-icon icon="solar:folder-open-outline" class="text-3xl"></iconify-icon>
                                    </div>
                                    <h6 class="fw-semibold text-secondary-light mb-1">Belum Ada Kategori</h6>
                                    <p class="text-secondary-light text-xs mb-16">Silakan buat kategori berita baru untuk mulai mengorganisasikan artikel.</p>
                                    <a href="<?= site_url('dashboard/komunikasi/kategori-berita/tambah') ?>" class="btn btn-primary-600 btn-sm radius-8 px-16 py-8">
                                        <iconify-icon icon="solar:add-circle-linear" class="align-middle me-1"></iconify-icon> Tambah Kategori
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($items as $c): ?>
                            <tr class="category-row">
                                <td class="ps-20 text-secondary-light text-xs fw-semibold"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="w-32-px h-32-px bg-primary-50 text-primary-600 radius-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                            <iconify-icon icon="solar:folder-bold" class="text-lg"></iconify-icon>
                                        </div>
                                        <div>
                                            <a href="<?= site_url('dashboard/komunikasi/kategori-berita/' . (int) $c['id'] . '/ubah') ?>" class="fw-semibold text-primary-light hover-primary-600 text-sm mb-0 d-block category-name">
                                                <?= esc($c['name']) ?>
                                            </a>
                                            <span class="text-xs text-secondary-light">ID: #<?= (int) $c['id'] ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code class="bg-neutral-100 text-neutral-700 px-8 py-3 radius-4 text-xs font-monospace category-slug"><?= esc($c['slug']) ?></code>
                                </td>
                                <td>
                                    <span class="text-secondary-light text-xs" style="max-width: 280px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        <?= esc($c['description'] ?? '-') ?: '-' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php $cnt = (int) ($c['news_count'] ?? 0); ?>
                                    <?php if ($cnt > 0): ?>
                                        <a href="<?= route_to('dashboard.communication.news') . '?category=' . (int) $c['id'] ?>" class="badge bg-primary-50 text-primary-600 border border-primary-200 px-10 py-5 radius-8 text-xs fw-bold hover-bg-primary-100 transition-2">
                                            <iconify-icon icon="solar:document-text-bold" class="align-middle me-1"></iconify-icon> <?= $cnt ?> Artikel
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-neutral-100 text-secondary-light px-8 py-4 radius-6 text-xs">0 Artikel</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($c['is_active'])): ?>
                                        <span class="badge bg-success-50 text-success-600 border border-success-200 px-8 py-4 radius-6 text-xs fw-semibold">
                                            <iconify-icon icon="solar:check-circle-bold" class="align-middle me-1"></iconify-icon> Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-neutral-100 text-neutral-500 border border-neutral-300 px-8 py-4 radius-6 text-xs fw-semibold">
                                            <iconify-icon icon="solar:close-circle-bold" class="align-middle me-1"></iconify-icon> Nonaktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-20">
                                    <div class="dropdown">
                                        <button class="btn btn-icon btn-sm btn-outline-neutral-300 radius-8" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Aksi">
                                            <iconify-icon icon="solar:menu-dots-bold" class="text-lg"></iconify-icon>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end p-8 radius-8 border border-neutral-200 shadow-sm">
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-6 d-flex align-items-center gap-2 text-xs text-secondary-light" href="<?= site_url('dashboard/komunikasi/kategori-berita/' . (int) $c['id'] . '/ubah') ?>">
                                                    <iconify-icon icon="solar:pen-linear" class="text-base text-primary-600"></iconify-icon>
                                                    <span>Edit Kategori</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-6 d-flex align-items-center gap-2 text-xs text-secondary-light" href="<?= route_to('dashboard.communication.news') . '?category=' . (int) $c['id'] ?>">
                                                    <iconify-icon icon="solar:eye-linear" class="text-base text-info-600"></iconify-icon>
                                                    <span>Lihat Berita Terkait</span>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider my-4"></li>
                                            <li>
                                                <button type="button" class="dropdown-item px-12 py-8 radius-6 d-flex align-items-center gap-2 text-xs text-danger-600 border-0 bg-transparent w-100 text-start"
                                                    data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                                                    data-action="<?= site_url('dashboard/komunikasi/kategori-berita/' . (int) $c['id'] . '/hapus') ?>"
                                                    data-method="POST"
                                                    data-title="Hapus Kategori Berita"
                                                    data-message="Apakah Anda yakin ingin menghapus kategori &quot;<?= esc($c['name']) ?>&quot;? Berita di dalamnya tidak akan terhapus namun kategorinya akan dikosongkan."
                                                    data-btn-text="Hapus Kategori"
                                                    data-btn-class="btn-danger-600"
                                                    data-icon="fluent:delete-24-regular"
                                                    data-icon-color="danger">
                                                    <iconify-icon icon="solar:trash-bin-trash-linear" class="text-base text-danger-600"></iconify-icon>
                                                    <span>Hapus Kategori</span>
                                                </button>
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
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('categorySearchInput');
    const table = document.getElementById('categoryTable');
    if (!searchInput || !table) return;

    searchInput.addEventListener('input', function () {
        const val = this.value.toLowerCase().trim();
        const rows = table.querySelectorAll('tbody tr.category-row');
        rows.forEach(row => {
            const name = row.querySelector('.category-name')?.textContent.toLowerCase() || '';
            const slug = row.querySelector('.category-slug')?.textContent.toLowerCase() || '';
            if (name.includes(val) || slug.includes(val)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
