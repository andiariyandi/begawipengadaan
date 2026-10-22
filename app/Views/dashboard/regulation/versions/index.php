<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $versions = $versions ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Versi Dokumen Regulasi</h6>
        <p class="text-secondary-light mb-0">Riwayat naskah versi dan berkas digital dokumen regulasi Pengadaan Barang/Jasa.</p>
    </div>
</div>

<!-- Reusable Tabbed Console Navigation -->
<?= view('dashboard/regulation/_nav_regulation', ['section' => 'regulations', 'activeSub' => 'versi']) ?>

<div class="card radius-12 border bg-base shadow-sm">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 align-middle begawi-table" data-table-name="versi-dokumen-regulasi" data-page-length="10">
                <thead>
                    <tr>
                        <th style="min-width: 280px;">Regulasi PBJ</th>
                        <th class="text-center">Versi</th>
                        <th>Nama File Dokumen</th>
                        <th>Ukuran Berkas</th>
                        <th>Waktu Unggah</th>
                        <th>Pengunggah</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($versions)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-32 text-secondary-light">
                                <iconify-icon icon="solar:history-outline" class="text-4xl d-block mb-2 text-neutral-400"></iconify-icon>
                                Belum ada riwayat versi dokumen tercatat.
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($versions as $v): ?>
                        <tr>
                            <td>
                                <a href="<?= site_url('dashboard/regulasi/dokumen/' . (int) ($v['regulation_id'] ?? 0)) ?>" class="fw-bold text-sm text-primary-700 text-hover-primary-900 d-block mb-1">
                                    <?= esc($v['document_number'] ?? '-') ?>
                                </a>
                                <span class="text-xs text-secondary-light text-line-2"><?= esc($v['regulation_title'] ?? '-') ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-neutral-100 text-neutral-800 radius-6 px-10 py-4 text-xs fw-bold">v<?= esc($v['version_number']) ?></span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <iconify-icon icon="solar:document-text-outline" class="text-primary-600 text-lg flex-shrink-0"></iconify-icon>
                                    <span class="text-xs fw-medium text-primary-light text-line-1 max-w-220-px"><?= esc($v['file_original_name'] ?? 'Berkas Naskah') ?></span>
                                </div>
                            </td>
                            <td><span class="text-xs text-secondary-light"><?= isset($v['file_size']) ? esc(round($v['file_size'] / 1024)) . ' KB' : '-' ?></span></td>
                            <td><span class="text-xs text-secondary-light"><?= esc($v['created_at'] ? date('d M Y H:i', strtotime((string) $v['created_at'])) : '-') ?></span></td>
                            <td><span class="text-xs text-primary-light fw-medium"><?= esc($v['uploader_name'] ?? 'Admin') ?></span></td>
                            <td class="text-center">
                                <?php if (! empty($v['is_current'])): ?>
                                    <span class="badge bg-success-50 text-success-700 radius-6 px-8 py-4 text-xs fw-bold">Versi Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-neutral-100 text-neutral-600 radius-6 px-8 py-4 text-xs">Arsip Lama</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <?php if (! empty($v['file_id'])): ?>
                                        <a href="<?= base_url('file/' . (int) $v['file_id']) ?>" target="_blank" class="w-32-px h-32-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center" title="Pratinjau / Unduh Berkas">
                                            <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= site_url('dashboard/regulasi/dokumen/' . (int) ($v['regulation_id'] ?? 0)) ?>" class="w-32-px h-32-px bg-neutral-100 text-neutral-700 rounded-circle d-inline-flex align-items-center justify-content-center" title="Buka Regulasi">
                                        <iconify-icon icon="solar:arrow-right-up-linear"></iconify-icon>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>