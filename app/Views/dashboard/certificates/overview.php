<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$stats              = $stats ?? ['total' => 0, 'learning_total' => 0, 'event_total' => 0, 'revoked_total' => 0, 'active_templates' => 0, 'pending_approval' => 0];
$recentCertificates = $recentCertificates ?? [];
?>

<!-- Header & Quick Actions -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Overview Sertifikat PBJ</h6>
        <p class="text-secondary-light mb-0">Pusat monitoring, tata kelola penerbitan, dan verifikasi sertifikat BegawiPBJ Provinsi Lampung.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?= site_url('dashboard/sertifikat/learning') ?>" class="btn btn-outline-primary-600 radius-8 px-14 py-9 d-flex align-items-center gap-2 text-sm">
            <iconify-icon icon="solar:square-academic-cap-2-outline" class="text-lg"></iconify-icon>
            Learning Cert
        </a>
        <a href="<?= site_url('dashboard/sertifikat/event') ?>" class="btn btn-outline-success-600 radius-8 px-14 py-9 d-flex align-items-center gap-2 text-sm">
            <iconify-icon icon="solar:calendar-mark-outline" class="text-lg"></iconify-icon>
            Event Cert
        </a>
        <a href="<?= site_url('dashboard/sertifikat/batch') ?>" class="btn btn-primary-600 radius-8 px-16 py-9 d-flex align-items-center gap-2 text-sm">
            <iconify-icon icon="solar:layers-minimalistic-outline" class="text-lg"></iconify-icon>
            Batch Generator
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3 mb-24">
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Total Sertifikat Terbit</span>
                    <h4 class="fw-bold mb-0 text-primary-light"><?= number_format($stats['total'] ?? 0) ?></h4>
                    <span class="text-xs text-secondary-light mt-1 d-block">Semua aktivitas PBJ</span>
                </div>
                <div class="w-56-px h-56-px bg-primary-50 text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-3xl">
                    <iconify-icon icon="solar:medal-ribbons-star-bold"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Sertifikat Pembelajaran</span>
                    <h4 class="fw-bold text-success-main mb-0"><?= number_format($stats['learning_total'] ?? 0) ?></h4>
                    <span class="text-xs text-secondary-light mt-1 d-block">LMS / Arena Belajar</span>
                </div>
                <div class="w-56-px h-56-px bg-success-50 text-success-600 rounded-circle d-flex align-items-center justify-content-center text-3xl">
                    <iconify-icon icon="solar:diploma-verified-bold"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Sertifikat Agenda / Event</span>
                    <h4 class="fw-bold text-info-main mb-0"><?= number_format($stats['event_total'] ?? 0) ?></h4>
                    <span class="text-xs text-secondary-light mt-1 d-block">Presensi & Workshop</span>
                </div>
                <div class="w-56-px h-56-px bg-info-50 text-info-600 rounded-circle d-flex align-items-center justify-content-center text-3xl">
                    <iconify-icon icon="solar:calendar-check-bold"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Sertifikat Dicabut</span>
                    <h4 class="fw-bold text-danger-main mb-0"><?= number_format($stats['revoked_total'] ?? 0) ?></h4>
                    <span class="text-xs text-secondary-light mt-1 d-block">Status Tidak Berlaku</span>
                </div>
                <div class="w-56-px h-56-px bg-danger-50 text-danger-600 rounded-circle d-flex align-items-center justify-content-center text-3xl">
                    <iconify-icon icon="solar:danger-triangle-bold"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Workflow Navigation -->
<div class="row g-3 mb-24">
    <div class="col-md-4">
        <div class="card p-20 radius-12 border bg-base shadow-none h-100">
            <div class="d-flex gap-3">
                <div class="w-48-px h-48-px bg-primary-50 text-primary-600 radius-8 d-flex align-items-center justify-content-center text-2xl flex-shrink-0">
                    <iconify-icon icon="solar:square-academic-cap-2-bold"></iconify-icon>
                </div>
                <div class="flex-grow-1">
                    <h6 class="text-md fw-semibold mb-1">Learning Flow</h6>
                    <p class="text-secondary-light text-xs mb-3">Penerbitan berbasis kelulusan 100% materi & assessment di kelas LMS.</p>
                    <a href="<?= site_url('dashboard/sertifikat/learning') ?>" class="text-primary-600 text-sm fw-medium d-inline-flex align-items-center gap-1 hover-underline">
                        Kelola Kelas <iconify-icon icon="solar:alt-arrow-right-outline"></iconify-icon>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-20 radius-12 border bg-base shadow-none h-100">
            <div class="d-flex gap-3">
                <div class="w-48-px h-48-px bg-success-50 text-success-600 radius-8 d-flex align-items-center justify-content-center text-2xl flex-shrink-0">
                    <iconify-icon icon="solar:calendar-mark-bold"></iconify-icon>
                </div>
                <div class="flex-grow-1">
                    <h6 class="text-md fw-semibold mb-1">Event & Agenda Flow</h6>
                    <p class="text-secondary-light text-xs mb-3">Penerbitan berbasis kode presensi sandi valid untuk peserta berstatus Hadir.</p>
                    <a href="<?= site_url('dashboard/sertifikat/event') ?>" class="text-success-600 text-sm fw-medium d-inline-flex align-items-center gap-1 hover-underline">
                        Pilih Event <iconify-icon icon="solar:alt-arrow-right-outline"></iconify-icon>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-20 radius-12 border bg-base shadow-none h-100">
            <div class="d-flex gap-3">
                <div class="w-48-px h-48-px bg-purple-50 text-purple-600 radius-8 d-flex align-items-center justify-content-center text-2xl flex-shrink-0" style="background:#f3e8ff;color:#7e22ce;">
                    <iconify-icon icon="solar:shield-check-bold"></iconify-icon>
                </div>
                <div class="flex-grow-1">
                    <h6 class="text-md fw-semibold mb-1">Verifikasi & Keaslian</h6>
                    <p class="text-secondary-light text-xs mb-3">Pemeriksaan keabsahan QR code dan nomor sertifikat resmi secara internal.</p>
                    <a href="<?= site_url('dashboard/sertifikat/verifikasi') ?>" class="text-purple-600 text-sm fw-medium d-inline-flex align-items-center gap-1 hover-underline" style="color:#7e22ce;">
                        Periksa Sertifikat <iconify-icon icon="solar:alt-arrow-right-outline"></iconify-icon>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Issued Table -->
<div class="card radius-12 border shadow-none">
    <div class="card-header py-16 px-20 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h6 class="card-title fw-semibold mb-0">Sertifikat Terbaru</h6>
            <span class="text-secondary-light text-xs">Daftar sertifikat yang baru diterbitkan melalui sistem BegawiPBJ</span>
        </div>
        <a href="<?= site_url('dashboard/sertifikat/riwayat') ?>" class="btn btn-outline-secondary radius-8 px-14 py-7 text-xs d-flex align-items-center gap-1">
            Lihat Semua Riwayat
            <iconify-icon icon="solar:arrow-right-outline"></iconify-icon>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0">
                <thead>
                    <tr>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold">NOMOR SERTIFIKAT</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold">PENERIMA</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold">SUMBER AKTIVITAS</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold">TANGGAL TERBIT</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold">STATUS</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentCertificates)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-32 text-secondary-light">
                                <iconify-icon icon="solar:diploma-outline" class="text-4xl text-secondary-light mb-2 d-block mx-auto"></iconify-icon>
                                Belum ada sertifikat yang diterbitkan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentCertificates as $c): ?>
                            <tr>
                                <td>
                                    <a href="<?= site_url('dashboard/sertifikat/' . $c['id']) ?>" class="fw-semibold text-primary-600 hover-underline font-monospace text-sm">
                                        <?= esc($c['certificate_number']) ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="w-32-px h-32-px rounded-circle bg-primary-50 text-primary-600 d-flex align-items-center justify-content-center fw-semibold text-xs flex-shrink-0">
                                            <?= strtoupper(substr((string) ($c['user_name'] ?? 'U'), 0, 1)) ?>
                                        </div>
                                        <div>
                                            <span class="d-block fw-medium text-sm text-primary-light"><?= esc($c['user_name'] ?? '—') ?></span>
                                            <span class="d-block text-xs text-secondary-light"><?= esc($c['user_email'] ?? '') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (($c['source_type'] ?? '') === 'learning_enrollment'): ?>
                                        <span class="badge bg-primary-50 text-primary-600 px-8 py-3 radius-4 text-xs mb-1 d-inline-block">Learning</span>
                                    <?php else: ?>
                                        <span class="badge bg-info-50 text-info-600 px-8 py-3 radius-4 text-xs mb-1 d-inline-block">Event</span>
                                    <?php endif; ?>
                                    <span class="d-block text-xs text-secondary-light text-truncate" style="max-width: 260px;">
                                        <?= esc($c['source_label'] ?? '—') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-sm text-secondary-light">
                                        <?= ! empty($c['issued_at']) ? date('d M Y, H:i', strtotime($c['issued_at'])) : '—' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($c['status'] === 'issued'): ?>
                                        <span class="badge bg-success-50 text-success-600 px-10 py-4 radius-4 fw-medium text-xs">Aktif</span>
                                    <?php elseif ($c['status'] === 'revoked'): ?>
                                        <span class="badge bg-danger-50 text-danger-600 px-10 py-4 radius-4 fw-medium text-xs">Dicabut</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-50 text-warning-600 px-10 py-4 radius-4 fw-medium text-xs"><?= esc(ucfirst($c['status'])) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-2">
                                        <a href="<?= site_url('dashboard/sertifikat/' . $c['id']) ?>" class="w-32-px h-32-px bg-primary-50 text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-md hover-scale" title="Detail Sertifikat">
                                            <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                        </a>
                                        <a href="<?= site_url('dashboard/sertifikat/' . $c['id'] . '/unduh') ?>" class="w-32-px h-32-px bg-success-50 text-success-600 rounded-circle d-flex align-items-center justify-content-center text-md hover-scale" title="Unduh PDF">
                                            <iconify-icon icon="solar:download-minimalistic-outline"></iconify-icon>
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
