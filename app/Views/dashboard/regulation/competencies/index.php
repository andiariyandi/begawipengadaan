<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $competencies = $competencies ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Kompetensi Terkait</h6>
        <p class="text-secondary-light mb-0">Master data standar kompetensi PBJ yang terhubung dengan regulasi dan jalur pembelajaran.</p>
    </div>
    <a href="<?= site_url('dashboard/regulasi/kompetensi/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
        <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
        <span>Tambah Kompetensi</span>
    </a>
</div>

<div class="card radius-12 border bg-white shadow-sm">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="min-width: 140px;">Kode / Key</th>
                        <th style="min-width: 260px;">Nama Standar Kompetensi</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($competencies)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-32 text-secondary-light">
                                <iconify-icon icon="solar:medal-star-outline" class="text-4xl d-block mb-2 text-neutral-400"></iconify-icon>
                                Belum ada kompetensi terdaftar.
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($competencies as $c): ?>
                        <tr>
                            <td><code class="text-primary-700 bg-primary-50 px-8 py-2 radius-4 text-xs fw-bold"><?= esc($c['key']) ?></code></td>
                            <td><h6 class="text-sm mb-0 fw-bold text-neutral-900"><?= esc($c['name']) ?></h6></td>
                            <td><span class="text-xs text-secondary-light text-line-2 max-w-400-px"><?= esc($c['description'] ?? '-') ?: '-' ?></span></td>
                            <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => ($c['is_active'] ?? 0) ? 'active' : 'inactive']) ?></td>
                            <td class="text-center">
                                <a href="<?= site_url('dashboard/regulasi/kompetensi/' . (int) $c['id'] . '/ubah') ?>" class="w-32-px h-32-px bg-warning-focus text-warning-main rounded-circle d-inline-flex align-items-center justify-content-center" title="Ubah Kompetensi">
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