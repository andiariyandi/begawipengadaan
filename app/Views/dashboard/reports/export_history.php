<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$fmt = static fn($n): string => number_format((float) $n, 0, ',', '.');
?>

<?= view('dashboard/reports/_nav_reports', ['activeReport' => 'export-history']) ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Riwayat Export') ?></h6>
        <p class="text-secondary-light mb-0">Arsip riwayat permohonan dan pengunduhan file laporan platform BegawiPengadaan dari jejak audit.</p>
    </div>
    <div class="dropdown">
        <button type="button" class="btn btn-primary-600 radius-8 px-20 py-11 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <iconify-icon icon="solar:download-minimalistic-outline" class="me-6"></iconify-icon> Ekspor Laporan Baru
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-md radius-8 border-neutral-200">
            <li><a class="dropdown-item py-8 text-sm" href="<?= site_url('dashboard/laporan/pembelajaran?export=csv') ?>"><iconify-icon icon="solar:book-2-outline" class="me-2 text-primary-600"></iconify-icon> Laporan Pembelajaran (CSV)</a></li>
            <li><a class="dropdown-item py-8 text-sm" href="<?= site_url('dashboard/laporan/event?export=csv') ?>"><iconify-icon icon="solar:calendar-date-outline" class="me-2 text-success-main"></iconify-icon> Laporan Event & Agenda (CSV)</a></li>
            <li><a class="dropdown-item py-8 text-sm" href="<?= site_url('dashboard/laporan/sertifikat?export=csv') ?>"><iconify-icon icon="solar:medal-ribbons-star-outline" class="me-2 text-warning-main"></iconify-icon> Laporan Sertifikat (CSV)</a></li>
            <li><a class="dropdown-item py-8 text-sm" href="<?= site_url('dashboard/laporan/kompetensi?export=csv') ?>"><iconify-icon icon="solar:danger-triangle-outline" class="me-2 text-danger-main"></iconify-icon> Laporan Gap Kompetensi (CSV)</a></li>
            <li><a class="dropdown-item py-8 text-sm" href="<?= site_url('dashboard/laporan/pengguna?export=csv') ?>"><iconify-icon icon="solar:users-group-two-rounded-outline" class="me-2 text-purple"></iconify-icon> Laporan Pengguna & Profil (CSV)</a></li>
        </ul>
    </div>
</div>

<div class="row row-cols-xxxl-4 row-cols-lg-2 row-cols-1 gy-4">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Total Export</p>
                <h6 class="mb-0"><?= $fmt($total ?? 0) ?></h6>
                <p class="text-sm text-primary-light mt-12 mb-0">Aktivitas export tercatat</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Berhasil</p>
                <h6 class="mb-0"><?= $fmt($success ?? 0) ?></h6>
                <p class="text-sm text-primary-light mt-12 mb-0">File berhasil dibuat</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Sedang Diproses</p>
                <h6 class="mb-0"><?= $fmt($in_progress ?? 0) ?></h6>
                <p class="text-sm text-primary-light mt-12 mb-0">Antrean pembuatan</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-5 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Gagal / Dibatalkan</p>
                <h6 class="mb-0"><?= $fmt($failed ?? 0) ?></h6>
                <p class="text-sm text-primary-light mt-12 mb-0">Perlu pemeriksaan</p>
            </div>
        </div>
    </div>
</div>

<div class="card mt-24">
    <div class="card-body p-24">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-20">
            <div>
                <h6 class="text-lg mb-0">Daftar Riwayat Export & Audit Log</h6>
                <p class="text-secondary-light text-sm mb-0 mt-4">Pencatatan riil seluruh pengunduhan file laporan oleh administrator.</p>
            </div>
        </div>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Nama File</th>
                        <th>Jenis Laporan</th>
                        <th>Format</th>
                        <th>Dibuat Oleh</th>
                        <th>Waktu Export</th>
                        <th>Ukuran</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($history)): ?>
                        <tr><td colspan="7" class="text-center text-secondary-light py-24">Belum ada riwayat export yang tercatat. Silakan lakukan ekspor dari menu laporan terkait.</td></tr>
                    <?php else: ?>
                        <?php foreach ($history as $export): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-10">
                                        <span class="w-36-px h-36-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center">
                                            <iconify-icon icon="solar:document-text-outline" class="text-xl"></iconify-icon>
                                        </span>
                                        <h6 class="text-md fw-medium mb-0"><?= esc($export['file']) ?></h6>
                                    </div>
                                </td>
                                <td><?= esc($export['type']) ?></td>
                                <td><span class="badge bg-primary-50 text-primary-600 px-10 py-6 radius-6"><?= esc($export['format']) ?></span></td>
                                <td><?= esc($export['by']) ?></td>
                                <td><?= esc($export['time']) ?></td>
                                <td><?= esc($export['size']) ?></td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', ['status' => $export['status'] === 'Selesai' ? 'approved' : ($export['status'] === 'Diproses' ? 'review' : 'rejected'), 'label' => $export['status']]) ?>
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
