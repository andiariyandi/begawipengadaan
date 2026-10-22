<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/certificates/_nav_certificate', ['activeSub' => 'verification']) ?>
<?php
$certificate = $certificate ?? null;
$searchQuery = $searchQuery ?? '';
$notFound    = $notFound ?? false;
?>

<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Verifikasi Sertifikat PBJ</h6>
        <p class="text-secondary-light mb-0">Pemeriksaan keaslian dan status keabsahan dokumen sertifikat resmi BegawiPengadaan.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('verifikasi-sertifikat') ?>" target="_blank" class="btn btn-outline-primary-600 radius-8 px-16 py-9 d-flex align-items-center gap-2 text-sm">
            <iconify-icon icon="solar:link-circle-outline" class="text-lg"></iconify-icon>
            Buka Halaman Verifikasi Publik
        </a>
    </div>
</div>

<!-- Search Input Card -->
<div class="card radius-12 border bg-base shadow-none mb-24">
    <div class="card-body p-24">
        <form method="post" action="<?= site_url('dashboard/sertifikat/verifikasi/cari') ?>">
            <?= csrf_field() ?>
            <label class="form-label text-sm fw-semibold mb-2">Input Nomor Sertifikat atau Token Hash Verifikasi</label>
            <div class="input-group">
                <span class="input-group-text bg-base border-end-0 radius-start-8 ps-16">
                    <iconify-icon icon="solar:magnifer-linear" class="text-xl text-secondary-light"></iconify-icon>
                </span>
                <input type="text" name="search_query" class="form-control border-start-0 py-12 text-sm" placeholder="Contoh: BGW-LRN-2026-000001 atau BGW-EVT-2026-000001 atau token..." value="<?= esc($searchQuery) ?>" required autofocus>
                <button type="submit" class="btn btn-primary-600 radius-end-8 px-24 py-12 text-sm fw-medium d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:shield-check-bold" class="text-lg"></iconify-icon>
                    Periksa Keabsahan
                </button>
            </div>
            <span class="text-xs text-secondary-light mt-2 d-block">
                Pencarian dapat menggunakan Nomor Sertifikat resmi (BGW-LRN-... / BGW-EVT-...) atau 64-karakter Verification Token Hash.
            </span>
        </form>
    </div>
</div>

<!-- Result Section -->
<?php if ($certificate): ?>
    <div class="card radius-12 border shadow-none mb-24 border-2 <?= $certificate['status'] === 'issued' ? 'border-success-main' : ($certificate['status'] === 'revoked' ? 'border-danger-main' : 'border-warning-main') ?>">
        <div class="card-header py-16 px-24 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <?php if ($certificate['status'] === 'issued'): ?>
                    <iconify-icon icon="solar:verified-check-bold" class="text-2xl text-success-main"></iconify-icon>
                    <div>
                        <h6 class="mb-0 fw-bold text-success-main">SERTIFIKAT SAH DAN VALID</h6>
                        <span class="text-xs text-secondary-light">Tercatat resmi dalam database BegawiPengadaan Pemerintah Provinsi Lampung</span>
                    </div>
                <?php elseif ($certificate['status'] === 'revoked'): ?>
                    <iconify-icon icon="solar:close-circle-bold" class="text-2xl text-danger-main"></iconify-icon>
                    <div>
                        <h6 class="mb-0 fw-bold text-danger-main">SERTIFIKAT INI TELAH DICABUT</h6>
                        <span class="text-xs text-secondary-light">Dokumen ini tidak lagi berlaku secara hukum maupun administratif</span>
                    </div>
                <?php else: ?>
                    <iconify-icon icon="solar:danger-triangle-bold" class="text-2xl text-warning-main"></iconify-icon>
                    <div>
                        <h6 class="mb-0 fw-bold text-warning-main">STATUS SERTIFIKAT: <?= esc(strtoupper($certificate['status'])) ?></h6>
                    </div>
                <?php endif; ?>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= site_url('dashboard/sertifikat/' . $certificate['id']) ?>" class="btn btn-outline-primary-600 radius-8 px-14 py-7 text-xs d-flex align-items-center gap-1">
                    <iconify-icon icon="solar:eye-outline"></iconify-icon>
                    Detail Lengkap Admin
                </a>
                <a href="<?= site_url('dashboard/sertifikat/' . $certificate['id'] . '/unduh') ?>" class="btn btn-success-600 radius-8 px-14 py-7 text-xs text-white d-flex align-items-center gap-1">
                    <iconify-icon icon="solar:download-minimalistic-outline"></iconify-icon>
                    Unduh PDF
                </a>
            </div>
        </div>

        <div class="card-body p-24">
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-xs text-secondary-light d-block mb-1">NOMOR SERTIFIKAT</span>
                            <span class="font-monospace fw-bold text-lg text-primary-light"><?= esc($certificate['certificate_number']) ?></span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-xs text-secondary-light d-block mb-1">STATUS KEABSAHAN</span>
                            <?php if ($certificate['status'] === 'issued'): ?>
                                <span class="badge bg-success-50 text-success-600 px-12 py-6 radius-4 fw-semibold text-xs">
                                    Aktif / Diterbitkan
                                </span>
                            <?php elseif ($certificate['status'] === 'revoked'): ?>
                                <span class="badge bg-danger-50 text-danger-600 px-12 py-6 radius-4 fw-semibold text-xs">
                                    Dicabut
                                </span>
                            <?php else: ?>
                                <span class="badge bg-warning-50 text-warning-600 px-12 py-6 radius-4 fw-semibold text-xs">
                                    <?= esc(ucfirst($certificate['status'])) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if ($certificate['status'] === 'revoked'): ?>
                            <div class="col-12">
                                <div class="p-12 radius-8 bg-danger-50 border border-danger-main text-danger-main">
                                    <span class="text-xs fw-bold d-block mb-1">ALASAN PENCABUTAN:</span>
                                    <span class="text-xs"><?= esc($certificate['revocation_reason'] ?? 'Tidak dicantumkan alasan') ?></span>
                                    <span class="text-xs d-block mt-1 text-secondary-light">Dicabut pada: <?= ! empty($certificate['revoked_at']) ? date('d M Y, H:i', strtotime($certificate['revoked_at'])) : '—' ?></span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-sm-6">
                            <span class="text-xs text-secondary-light d-block mb-1">NAMA PENERIMA</span>
                            <span class="fw-semibold text-sm text-primary-light d-block"><?= esc($certificate['user_name'] ?? '—') ?></span>
                            <span class="text-xs text-secondary-light"><?= esc($certificate['user_email'] ?? '') ?></span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-xs text-secondary-light d-block mb-1">SUMBER KEGIATAN</span>
                            <span class="badge <?= ($certificate['source_type'] ?? '') === 'learning_enrollment' ? 'bg-primary-50 text-primary-600' : 'bg-info-50 text-info-600' ?> px-8 py-3 radius-4 text-xs mb-1">
                                <?= ($certificate['source_type'] ?? '') === 'learning_enrollment' ? 'Learning (LMS)' : 'Event / Agenda' ?>
                            </span>
                            <span class="text-xs text-primary-light d-block fw-medium">
                                <?= esc($certificate['source_label'] ?? '—') ?>
                            </span>
                        </div>

                        <div class="col-sm-6">
                            <span class="text-xs text-secondary-light d-block mb-1">TANGGAL TERBIT</span>
                            <span class="text-sm text-secondary-light">
                                <?= ! empty($certificate['issued_at']) ? date('d F Y, H:i:s', strtotime($certificate['issued_at'])) : '—' ?>
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-xs text-secondary-light d-block mb-1">PENERBIT RESMI</span>
                            <span class="text-sm text-secondary-light">
                                BegawiPengadaan — Pemerintah Provinsi Lampung
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 text-center border-start ps-md-24">
                    <span class="text-xs text-secondary-light d-block mb-2">VERIFIKASI INTEGRITAS</span>
                    <div class="p-16 radius-8 bg-neutral-100 d-inline-block mb-2">
                        <iconify-icon icon="solar:qr-code-bold" class="text-6xl text-primary-light"></iconify-icon>
                    </div>
                    <span class="font-monospace text-xs text-secondary-light d-block text-truncate" title="<?= esc($certificate['verification_token_hash']) ?>">
                        HASH: <?= strtoupper(substr((string) $certificate['verification_token_hash'], 0, 16)) ?>...
                    </span>
                    <span class="badge bg-success-50 text-success-600 px-8 py-3 radius-4 text-xs mt-2">
                        Checksum Valid
                    </span>
                </div>
            </div>
        </div>
    </div>
<?php elseif ($notFound): ?>
    <div class="card radius-12 border bg-base shadow-none p-32 text-center">
        <iconify-icon icon="solar:danger-circle-outline" class="text-5xl text-danger-main mb-2 d-block mx-auto"></iconify-icon>
        <h6 class="fw-bold mb-1 text-danger-main">Sertifikat Tidak Ditemukan</h6>
        <p class="text-secondary-light text-sm mb-0">
            Nomor sertifikat atau token hash "<strong><?= esc($searchQuery) ?></strong>" tidak terdaftar pada sistem BegawiPengadaan.
        </p>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>