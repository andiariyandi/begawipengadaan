<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$fmt = static fn(int $n): string => number_format($n, 0, ',', '.');
$flashMsg = session()->getFlashdata('message');
$flashErr = session()->getFlashdata('error');
?>

<!-- Alerts -->
<?php if ($flashMsg): ?>
    <div class="alert alert-success bg-success-50 text-success-600 border-success-200 px-24 py-11 mb-24 radius-8 d-flex align-items-center justify-content-between" role="alert">
        <div class="d-flex align-items-center gap-12">
            <iconify-icon icon="solar:check-circle-bold" class="text-xl"></iconify-icon>
            <span class="text-md fw-medium"><?= esc($flashMsg) ?></span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Riwayat Persetujuan') ?></h6>
        <p class="text-secondary-light mb-0">Rekap seluruh keputusan administratif yang telah ditetapkan lengkap dengan jejak peninjau.</p>
    </div>
    <div class="d-flex align-items-center gap-12">
        <a href="<?= site_url('dashboard/sistem/approval') ?>" class="btn btn-outline-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-6">
            <iconify-icon icon="solar:inbox-line-bold" class="text-lg"></iconify-icon> Antrean Persetujuan
        </a>
    </div>
</div>

<!-- Card Table -->
<div class="card">
    <div class="card-body p-24">
        <!-- Filter Bar -->
        <form method="get" action="<?= site_url('dashboard/sistem/approval/riwayat') ?>" class="d-flex flex-wrap justify-content-between align-items-center gap-16 mb-24">
            <div>
                <h6 class="text-lg mb-4">Daftar Keputusan Selesai</h6>
                <p class="text-secondary-light text-sm mb-0">Total <?= count($historyList) ?> catatan keputusan persetujuan tersimpan.</p>
            </div>
            <div class="d-flex flex-wrap gap-12 align-items-center">
                <select name="status" class="form-select form-select-sm w-auto radius-8" onchange="this.form.submit()">
                    <option value="all" <?= ($statusParam ?? '') === 'all' ? 'selected' : '' ?>>Semua Keputusan</option>
                    <option value="approved" <?= ($statusParam ?? '') === 'approved' ? 'selected' : '' ?>>Disetujui</option>
                    <option value="rejected" <?= ($statusParam ?? '') === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
                </select>

                <div class="input-group input-group-sm w-auto">
                    <input type="search" name="q" value="<?= esc($searchParam ?? '') ?>" class="form-control radius-8" placeholder="Cari pemohon/peninjau...">
                    <button class="btn btn-primary-600 px-12" type="submit">
                        <iconify-icon icon="solar:magnifer-outline"></iconify-icon>
                    </button>
                </div>

                <?php if (($statusParam ?? '') !== 'all' || ! empty($searchParam)): ?>
                    <a href="<?= site_url('dashboard/sistem/approval/riwayat') ?>" class="btn btn-sm btn-outline-neutral-300 radius-8 px-12 py-6 d-inline-flex align-items-center gap-4">
                        <iconify-icon icon="solar:restart-bold"></iconify-icon> Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Jenis Permintaan</th>
                        <th>Pemohon</th>
                        <th class="text-center">Keputusan</th>
                        <th>Diproses Oleh</th>
                        <th>Waktu Keputusan</th>
                        <th>Catatan / Alasan</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($historyList)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-40">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <span class="w-56-px h-56-px bg-neutral-100 text-secondary-light rounded-circle d-inline-flex align-items-center justify-content-center mb-12">
                                        <iconify-icon icon="solar:clipboard-check-outline" class="text-2xl"></iconify-icon>
                                    </span>
                                    <h6 class="text-md fw-semibold mb-4">Tidak Ada Riwayat Keputusan</h6>
                                    <p class="text-secondary-light text-sm mb-0">Belum ada pengajuan yang diproses atau kriteria filter tidak ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($historyList as $item): ?>
                            <tr>
                                <td>
                                    <a href="<?= site_url('dashboard/sistem/approval/' . $item['id']) ?>" class="text-primary-600 fw-bold">
                                        #<?= esc((string) $item['id']) ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <h6 class="text-sm fw-medium mb-2"><?= esc($item['type_label']) ?></h6>
                                        <span class="text-xs text-secondary-light font-monospace">
                                            <?= esc($item['resource_type']) ?> #<?= esc((string) $item['resource_id']) ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-sm fw-medium text-primary-light"><?= esc($item['applicant']) ?></span>
                                        <span class="text-xs text-secondary-light"><?= esc($item['requested_by_email'] ?: '-') ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', [
                                        'status' => $item['status'],
                                        'label'  => $item['decision_label'],
                                    ]) ?>
                                </td>
                                <td>
                                    <span class="text-sm fw-semibold text-primary-light"><?= esc($item['reviewer']) ?></span>
                                </td>
                                <td>
                                    <span class="text-sm text-secondary-light"><?= esc($item['formatted_time']) ?></span>
                                </td>
                                <td>
                                    <span class="text-sm text-secondary-light text-truncate-2" title="<?= esc($item['note_display']) ?>">
                                        <?= esc($item['note_display']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('dashboard/sistem/approval/' . $item['id']) ?>" class="btn btn-sm btn-outline-primary-600 radius-6 px-12 py-4">
                                        Detail
                                    </a>
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
