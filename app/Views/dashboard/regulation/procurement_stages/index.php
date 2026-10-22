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
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stages)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-32 text-secondary-light">
                                <iconify-icon icon="solar:route-outline" class="text-4xl d-block mb-2 text-neutral-400"></iconify-icon>
                                Belum ada tahapan PBJ terdaftar.
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($stages as $s): ?>
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-neutral-100 text-neutral-800 radius-6 px-10 py-4 fw-bold">#<?= (int) ($s['sort_order'] ?? 0) ?></span>
                            </td>
                            <td><code class="text-primary-700 bg-primary-50 px-8 py-2 radius-4 text-xs fw-bold"><?= esc($s['key'] ?? '-') ?></code></td>
                            <td><h6 class="text-sm mb-0 fw-bold text-neutral-900"><?= esc($s['name'] ?? '-') ?></h6></td>
                            <td><span class="text-xs text-secondary-light text-line-2 max-w-400-px"><?= esc($s['description'] ?? '-') ?: '-' ?></span></td>
                            <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => ($s['is_active'] ?? 0) ? 'active' : 'inactive']) ?></td>
                            <td class="text-center">
                                <a href="<?= site_url('dashboard/regulasi/tahapan-pbj/' . (int) $s['id'] . '/ubah') ?>" class="w-32-px h-32-px bg-warning-focus text-warning-main rounded-circle d-inline-flex align-items-center justify-content-center" title="Ubah Tahapan">
                                    <iconify-icon icon="solar:pen-new-square-outline"></iconify-icon>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>