<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $items = $items ?? []; ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Kategori Berita</h6>
        <p class="text-secondary-light mb-0">Kelola klasifikasi berita.</p>
    </div>
    <a href="<?= site_url('dashboard/komunikasi/kategori-berita/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Kategori</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Slug</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Jumlah Berita</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr><td colspan="6" class="text-center text-secondary-light">Belum ada kategori.</td></tr>
                    <?php else: foreach ($items as $c): ?>
                        <tr>
                            <td><h6 class="text-md mb-0 fw-medium"><?= esc($c['name']) ?></h6></td>
                            <td><code class="text-primary-600"><?= esc($c['slug']) ?></code></td>
                            <td><?= esc($c['description'] ?? '-') ?: '-' ?></td>
                            <td class="text-center"><?= (int) ($c['news_count'] ?? 0) ?></td>
                            <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => ($c['is_active'] ?? 0) ? 'active' : 'inactive']) ?></td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-sm btn-outline-neutral-300 radius-8" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <iconify-icon icon="solar:menu-dots-bold" class="text-lg"></iconify-icon>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-8 radius-8 border border-neutral-200">
                                        <li>
                                            <a class="dropdown-item px-12 py-8 radius-6 d-flex align-items-center gap-2 text-secondary-light" href="<?= site_url('dashboard/komunikasi/kategori-berita/' . (int) $c['id'] . '/ubah') ?>">
                                                <iconify-icon icon="solar:pen-linear" class="text-lg"></iconify-icon>
                                                <span>Edit</span>
                                            </a>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item px-12 py-8 radius-6 d-flex align-items-center gap-2 text-danger-600"
                                                data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                                                data-action="<?= site_url('dashboard/komunikasi/kategori-berita/' . (int) $c['id'] . '/hapus') ?>"
                                                data-method="POST"
                                                data-title="Hapus Kategori"
                                                data-message="Apakah Anda yakin ingin menghapus kategori berita ini?"
                                                data-btn-text="Hapus Kategori"
                                                data-btn-class="btn-danger-600"
                                                data-icon="fluent:delete-24-regular"
                                                data-icon-color="danger">
                                                <iconify-icon icon="solar:trash-bin-trash-linear" class="text-lg"></iconify-icon>
                                                <span>Hapus</span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
