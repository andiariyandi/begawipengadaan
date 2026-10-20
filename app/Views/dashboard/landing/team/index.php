<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Tim Inovator & Pengembang') ?></h6>
        <p class="text-secondary-light mb-0">Tokoh pengarah strategis, penanggung jawab teknis, perekayasa perangkat lunak, dan instruktur di balik ekosistem.</p>
    </div>
    <a href="<?= site_url('dashboard/landing/tim/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
        <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
        <span>Tambah Anggota Tim</span>
    </a>
</div>

<div class="card basic-data-table">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">Urutan</th>
                        <th style="width: 60px;">Foto</th>
                        <th>Nama & NIP</th>
                        <th>Jabatan & Kategori</th>
                        <th>Badge Spesialisasi & Bio</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 120px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($members)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-secondary-light">Belum ada anggota tim. Klik tombol di atas untuk menambah.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($members as $m): ?>
                            <?php
                            $social = json_decode($m['social_links'] ?? '{}', true) ?: [];
                            $badge = $social['badge'] ?? 'Inovator';
                            ?>
                            <tr>
                                <td class="text-center fw-bold text-secondary-light"><?= esc($m['sort_order']) ?></td>
                                <td class="text-center">
                                    <img src="<?= base_url($m['photo_url']) ?>" alt="Foto" class="w-40-px h-40-px rounded-circle object-fit-cover border border-neutral-200" onerror="this.src='<?= base_url('assets/dashboard/images/users/user1.png') ?>'">
                                </td>
                                <td>
                                    <strong class="text-sm d-block text-primary-light"><?= esc($m['name']) ?></strong>
                                    <?php if (! empty($m['nip'])): ?>
                                        <small class="text-secondary-light font-monospace text-2xs">NIP: <?= esc($m['nip']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-primary-600 d-block fw-semibold"><?= esc($m['role_title']) ?></small>
                                    <span class="badge bg-neutral-200 text-neutral-800 text-2xs text-uppercase"><?= esc($m['category']) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-primary-100 text-primary-600 radius-4 text-2xs mb-1">
                                        <iconify-icon icon="solar:crown-star-bold" class="text-warning-main me-1"></iconify-icon>
                                        <?= esc($badge) ?>
                                    </span>
                                    <p class="text-xs text-secondary-light mb-0 text-truncate" style="max-width: 320px;"><?= esc($m['bio']) ?></p>
                                </td>
                                <td>
                                    <a href="<?= site_url('dashboard/landing/tim/toggle/' . $m['id']) ?>" class="badge <?= $m['is_active'] ? 'bg-success-100 text-success-600' : 'bg-neutral-100 text-secondary-light' ?> px-10 py-6 radius-4 text-xs" title="Klik untuk ubah status">
                                        <?= $m['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="<?= site_url('dashboard/landing/tim/ubah/' . $m['id']) ?>" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-success-main hover-text-white" title="Ubah Profil">
                                            <iconify-icon icon="solar:pen-new-square-linear"></iconify-icon>
                                        </a>
                                        <a href="<?= site_url('dashboard/landing/tim/hapus/' . $m['id']) ?>" onclick="return confirm('Hapus anggota tim ini?')" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-danger-main hover-text-white" title="Hapus Profil">
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
