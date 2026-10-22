<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Kelola Testimoni Pengguna') ?></h6>
        <p class="text-secondary-light mb-0">Ulasan nyata pimpinan daerah, PPK, Pokja pemilihan, auditor, dan penyedia barang/jasa beserta dampak terukur.</p>
    </div>
    <a href="<?= site_url('dashboard/landing/testimoni/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
        <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
        <span>Tambah Testimoni</span>
    </a>
</div>

<!-- Reusable Tabbed Console Navigation -->
<?= view('dashboard/landing/_nav_landing', ['section' => 'team', 'activeSub' => 'testimoni']) ?>

<div class="card basic-data-table">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle begawi-table" data-table-name="testimoni-pengguna" data-page-length="10">
                <thead>
                    <tr>
                        <th style="width: 50px;">Urutan</th>
                        <th style="width: 60px;">Foto</th>
                        <th>Nama & Jabatan</th>
                        <th>Kutipan Ulasan</th>
                        <th>Dampak Terukur</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 120px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($testimonials)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-secondary-light">Belum ada testimoni. Klik "Tambah Testimoni" untuk membuat.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($testimonials as $t): ?>
                            <tr>
                                <td class="text-center fw-bold text-secondary-light"><?= esc($t['sort_order']) ?></td>
                                <td class="text-center">
                                    <img src="<?= base_url($t['avatar_url']) ?>" alt="Avatar" class="w-40-px h-40-px rounded-circle object-fit-cover border border-neutral-200" onerror="this.src='<?= base_url('assets/dashboard/images/users/user1.png') ?>'">
                                </td>
                                <td>
                                    <strong class="text-sm d-block text-primary-light"><?= esc($t['name']) ?></strong>
                                    <small class="text-primary-600 d-block fw-semibold"><?= esc($t['role_title']) ?></small>
                                    <small class="text-secondary-light"><?= esc($t['organization']) ?></small>
                                </td>
                                <td>
                                    <p class="text-xs text-secondary-light mb-1" style="max-width: 320px; font-style: italic;">
                                        &ldquo;<?= esc($t['quote']) ?>&rdquo;
                                    </p>
                                    <div class="text-warning-main text-xs">
                                        <?php for ($i = 0; $i < (int) ($t['rating'] ?: 5); $i++): ?>★<?php endfor; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if (! empty($t['impact_statement'])): ?>
                                        <div class="p-8 bg-success-100 radius-8 border border-success-200 text-xs text-success-600 fw-semibold" style="max-width: 260px;">
                                            <iconify-icon icon="solar:graph-up-bold" class="me-1"></iconify-icon>
                                            <?= esc($t['impact_statement']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-secondary-light text-xs">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('dashboard/landing/testimoni/toggle/' . $t['id']) ?>" class="badge <?= $t['is_active'] ? 'bg-success-100 text-success-600' : 'bg-neutral-100 text-secondary-light' ?> px-10 py-6 radius-4 text-xs" title="Klik untuk ubah status">
                                        <?= $t['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="<?= site_url('dashboard/landing/testimoni/ubah/' . $t['id']) ?>" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-success-main hover-text-white" title="Ubah Testimoni">
                                            <iconify-icon icon="solar:pen-new-square-linear"></iconify-icon>
                                        </a>
                                        <a href="<?= site_url('dashboard/landing/testimoni/hapus/' . $t['id']) ?>" onclick="return confirm('Hapus testimoni ini?')" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-danger-main hover-text-white" title="Hapus Testimoni">
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
