<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$c        = $certificate;
$isIssued = ($c['status'] ?? '') === 'issued';
$isLearning = ($c['source_type'] ?? '') === 'learning_enrollment';
$issuedAtFmt = ! empty($c['issued_at'])
    ? date('d F Y, H:i', strtotime($c['issued_at']))
    : '—';
$expiresAtFmt = ! empty($c['expires_at'])
    ? date('d F Y', strtotime($c['expires_at']))
    : 'Berlaku Selamanya';
$revokedAtFmt = ! empty($c['revoked_at'])
    ? date('d F Y, H:i', strtotime($c['revoked_at']))
    : '—';
?>

<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div class="d-flex align-items-center gap-3">
        <a href="<?= site_url('dashboard/sertifikat/riwayat') ?>" class="w-40-px h-40-px radius-8 border bg-base d-flex align-items-center justify-content-center text-secondary-light hover-bg-neutral-100">
            <iconify-icon icon="solar:arrow-left-outline" class="text-xl"></iconify-icon>
        </a>
        <div>
            <h6 class="fw-semibold mb-1">Detail Sertifikat Resmi PBJ</h6>
            <p class="text-secondary-light mb-0">Nomor Registrasi: <span class="font-monospace fw-bold text-primary-light"><?= esc($c['certificate_number']) ?></span></p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-warning-600 radius-8 px-16 py-9 d-flex align-items-center gap-2 text-sm"
            data-bs-toggle="modal"
            data-bs-target="#confirmationActionModal"
            data-action="<?= site_url('dashboard/sertifikat/' . (int) $c['id'] . '/hapus-dan-requeue') ?>"
            data-method="POST"
            data-type="warning"
            data-icon="solar:restart-bold"
            data-icon-color="warning"
            data-btn-class="btn-warning-600"
            data-btn-text="Ya, Hapus &amp; Masuk Antrean"
            data-title="Hapus &amp; Ajukan Ulang Sertifikat"
            data-message="Apakah Anda yakin ingin menghapus sertifikat nomor <?= esc($c['certificate_number']) ?>? Dokumen sertifikat ini akan dihapus dan peserta akan dimasukkan kembali ke Antrean Approval Pengajuan Terbit Sertifikat untuk diterbitkan ulang (regenerate).">
            <iconify-icon icon="solar:restart-bold" class="text-lg"></iconify-icon>
            Hapus &amp; Ajukan Ulang (Regenerate)
        </button>
        <a href="<?= site_url('dashboard/sertifikat/' . $c['id'] . '/unduh') ?>" class="btn btn-success-600 radius-8 px-16 py-9 d-flex align-items-center gap-2 text-sm text-white">
            <iconify-icon icon="solar:download-minimalistic-outline" class="text-lg"></iconify-icon>
            Unduh PDF
        </a>
        <button type="button" onclick="window.print()" class="btn btn-outline-secondary radius-8 px-16 py-9 d-flex align-items-center gap-2 text-sm">
            <iconify-icon icon="solar:printer-outline" class="text-lg"></iconify-icon>
            Cetak
        </button>
    </div>
</div>

<?php if (! empty($issuedToken)): ?>
    <div class="alert alert-warning radius-8 mb-24 border border-warning-main">
        <div class="d-flex align-items-start gap-3">
            <iconify-icon icon="solar:shield-warning-bold" class="text-3xl text-warning-main flex-shrink-0"></iconify-icon>
            <div>
                <h6 class="mb-4 fw-semibold text-warning-main">Token Verifikasi Baru (Ditampilkan Sekali)</h6>
                <p class="mb-8 text-sm text-neutral-800">Simpan token ini untuk pengujian atau verifikasi langsung. Token disimpan di database hanya dalam bentuk hash SHA-256 demi keamanan kredensial.</p>
                <code class="p-8 radius-6 bg-white border d-inline-block fw-bold text-sm text-neutral-900 select-all"><?= esc($issuedToken) ?></code>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row gy-4">
    <!-- Main Credential Details -->
    <div class="col-xl-8">
        <div class="card radius-12 border bg-white shadow-none">
            <!-- Header card -->
            <div class="p-24 border-bottom bg-neutral-50 radius-top-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="w-56-px h-56-px bg-primary-100 text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-3xl flex-shrink-0">
                            <iconify-icon icon="solar:diploma-verified-outline"></iconify-icon>
                        </div>
                        <div>
                            <span class="text-xs text-secondary-light fw-medium d-block text-uppercase">
                                <?= $isLearning ? 'Learning Completion Credential' : 'Event Attendance Credential' ?>
                            </span>
                            <h5 class="fw-bold text-primary-light font-monospace mb-0"><?= esc($c['certificate_number']) ?></h5>
                        </div>
                    </div>
                    <div>
                        <?php if ($c['status'] === 'issued'): ?>
                            <span class="badge bg-success-50 text-success-600 px-12 py-6 radius-4 fw-bold text-xs">
                                <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon>
                                AKTIF / BERLAKU
                            </span>
                        <?php elseif ($c['status'] === 'revoked'): ?>
                            <span class="badge bg-danger-50 text-danger-600 px-12 py-6 radius-4 fw-bold text-xs">
                                <iconify-icon icon="solar:close-circle-bold" class="me-1"></iconify-icon>
                                DICABUT (TIDAK BERLAKU)
                            </span>
                        <?php else: ?>
                            <span class="badge bg-warning-50 text-warning-600 px-12 py-6 radius-4 fw-bold text-xs">
                                <?= esc(strtoupper($c['status'])) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card-body p-24">
                <!-- Recipient Info -->
                <div class="p-20 radius-8 border bg-neutral-50 mb-24 text-center">
                    <span class="text-xs text-secondary-light d-block text-uppercase fw-semibold mb-2">Diberikan Kepada Penerima Resmi:</span>
                    <h4 class="fw-bold text-primary-light mb-1"><?= esc($c['user_name'] ?? '—') ?></h4>
                    <span class="text-secondary-light text-sm"><?= esc($c['user_email'] ?? '') ?></span>
                </div>

                <!-- Distinct Activity Information -->
                <h6 class="text-sm fw-bold text-primary-light mb-3">Informasi Sumber Kegiatan</h6>
                <div class="row g-3 mb-24">
                    <?php if ($isLearning): ?>
                        <div class="col-md-6">
                            <div class="p-16 radius-8 border h-100">
                                <span class="text-secondary-light text-xs d-block mb-1">PROGRAM PEMBELAJARAN</span>
                                <h6 class="text-sm fw-semibold text-primary-light mb-0"><?= esc($c['program_title'] ?? 'Program Reguler PBJ') ?></h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-16 radius-8 border h-100">
                                <span class="text-secondary-light text-xs d-block mb-1">COURSE & KELAS</span>
                                <h6 class="text-sm fw-semibold text-primary-light mb-1"><?= esc($c['course_title'] ?? '—') ?></h6>
                                <span class="text-xs text-secondary-light">Kelas: <?= esc($c['class_name'] ?? '—') ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-16 radius-8 border h-100">
                                <span class="text-secondary-light text-xs d-block mb-1">HASIL PENILAIAN / KELULUSAN</span>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success-50 text-success-600 px-8 py-3 radius-4 text-xs">Lulus 100%</span>
                                    <?php if (! empty($c['final_score'])): ?>
                                        <span class="fw-bold text-sm text-primary-light">Nilai: <?= number_format((float) $c['final_score'], 1) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-16 radius-8 border h-100">
                                <span class="text-secondary-light text-xs d-block mb-1">TRAINER PENGAJAR</span>
                                <span class="text-sm text-primary-light fw-medium"><?= esc($c['trainers_names'] ?? 'Pengajar BegawiPengadaan') ?></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="col-md-6">
                            <div class="p-16 radius-8 border h-100">
                                <span class="text-secondary-light text-xs d-block mb-1">AGENDA / EVENT</span>
                                <h6 class="text-sm fw-semibold text-primary-light mb-0"><?= esc($c['event_title'] ?? $c['source_label'] ?? '—') ?></h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-16 radius-8 border h-100">
                                <span class="text-secondary-light text-xs d-block mb-1">MODE & LOKASI</span>
                                <h6 class="text-sm fw-semibold text-primary-light mb-1"><?= esc(ucfirst($c['event_mode'] ?? 'Offline')) ?></h6>
                                <span class="text-xs text-secondary-light"><?= esc($c['event_location'] ?? '—') ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-16 radius-8 border h-100">
                                <span class="text-secondary-light text-xs d-block mb-1">JADWAL PELAKSANAAN</span>
                                <span class="text-xs text-secondary-light d-block">
                                    <?= ! empty($c['event_start_at']) ? date('d M Y, H:i', strtotime($c['event_start_at'])) : '—' ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-16 radius-8 border h-100">
                                <span class="text-secondary-light text-xs d-block mb-1">STATUS KEHADIRAN (PRESENSI)</span>
                                <span class="badge bg-success-50 text-success-600 px-8 py-3 radius-4 text-xs">
                                    <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon>
                                    Hadir Sah
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Credential Metadata -->
                <h6 class="text-sm fw-bold text-primary-light mb-3">Kredensial & Validasi Sistem</h6>
                <div class="row g-3 mb-24">
                    <div class="col-md-6">
                        <div class="p-16 radius-8 border h-100">
                            <span class="text-secondary-light text-xs d-block mb-1">TANGGAL PENERBITAN</span>
                            <span class="text-sm fw-semibold text-primary-light"><?= esc($issuedAtFmt) ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-16 radius-8 border h-100">
                            <span class="text-secondary-light text-xs d-block mb-1">MASA BERLAKU</span>
                            <span class="text-sm fw-semibold text-primary-light"><?= esc($expiresAtFmt) ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-16 radius-8 border h-100">
                            <span class="text-secondary-light text-xs d-block mb-1">DITERBITKAN OLEH</span>
                            <span class="text-sm fw-semibold text-primary-light"><?= esc($c['issued_by_name'] ?? 'Sistem BegawiPengadaan') ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-16 radius-8 border h-100">
                            <span class="text-secondary-light text-xs d-block mb-1">TEMPLATE YANG DIGUNAKAN</span>
                            <span class="text-sm fw-semibold text-primary-light"><?= esc($c['template_name'] ?? 'Standar BegawiPengadaan') ?></span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-16 radius-8 border bg-neutral-100">
                            <span class="text-secondary-light text-xs d-block mb-1">INTEGRITY HASH (SHA-256)</span>
                            <code class="text-xs text-primary-light word-break-all d-block"><?= esc($c['verification_token_hash'] ?? '—') ?></code>
                            <span class="text-xs text-secondary-light mt-1 d-block">Tercatat secara kriptografis untuk perlindungan pemalsuan sertifikat.</span>
                        </div>
                    </div>
                </div>

                <?php if (! empty($c['revoked_at'])): ?>
                    <div class="p-20 radius-8 bg-danger-50 border border-danger-main mb-24">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <iconify-icon icon="solar:danger-triangle-bold" class="text-danger-main text-xl"></iconify-icon>
                            <h6 class="text-danger-main text-sm fw-bold mb-0">Sertifikat Telah Dicabut</h6>
                        </div>
                        <p class="text-xs text-neutral-800 mb-1">Waktu Pencabutan: <strong><?= esc($revokedAtFmt) ?></strong> oleh <strong><?= esc($c['revoked_by_name'] ?? 'Administrator') ?></strong></p>
                        <p class="text-xs text-neutral-800 mb-0">Alasan Resmi: <strong><?= esc($c['revocation_reason'] ?? 'Tidak ada alasan dicantumkan') ?></strong></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Panel Regenerasi & Re-queue Sertifikat -->
        <div class="card radius-12 border mt-24">
            <div class="card-body p-24">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h6 class="fw-semibold mb-2 text-warning-main d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:restart-bold" class="text-xl"></iconify-icon>
                            Hapus &amp; Ajukan Ulang Sertifikat (Regenerate)
                        </h6>
                        <p class="text-secondary-light text-xs mb-0">
                            Hapus file PDF dan data nomor sertifikat ini, lalu kembalikan peserta ke antrean approval agar sertifikat dapat diproses ulang dengan format/data terbaru.
                        </p>
                    </div>
                    <button type="button" class="btn btn-warning-600 text-white radius-8 px-16 py-9 text-xs fw-semibold d-inline-flex align-items-center gap-2 shadow-xs"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmationActionModal"
                        data-action="<?= site_url('dashboard/sertifikat/' . (int) $c['id'] . '/hapus-dan-requeue') ?>"
                        data-method="POST"
                        data-type="warning"
                        data-icon="solar:restart-bold"
                        data-icon-color="warning"
                        data-btn-class="btn-warning-600"
                        data-btn-text="Ya, Hapus &amp; Masuk Antrean"
                        data-title="Hapus &amp; Ajukan Ulang Sertifikat"
                        data-message="Apakah Anda yakin ingin menghapus sertifikat nomor <?= esc($c['certificate_number']) ?>? Dokumen sertifikat ini akan dihapus dan peserta akan dimasukkan kembali ke Antrean Approval Pengajuan Terbit Sertifikat untuk diterbitkan ulang (regenerate).">
                        <iconify-icon icon="solar:restart-bold" class="text-md"></iconify-icon>
                        Hapus &amp; Ajukan Ulang
                    </button>
                </div>
            </div>
        </div>

        <?php if ($isIssued): ?>
            <!-- Revocation Action Panel -->
            <div class="card radius-12 border mt-24">
                <div class="card-body p-24">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h6 class="fw-semibold mb-2 text-danger-main d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:shield-cross-outline" class="text-xl"></iconify-icon>
                                Tindakan Pencabutan Sertifikat
                            </h6>
                            <p class="text-secondary-light text-xs mb-0">
                                Pencabutan sertifikat akan membatalkan status keabsahan dokumen dan menandai verifikasi publik sebagai tidak sah. Tindakan ini permanen.
                            </p>
                        </div>
                        <button type="button" class="btn btn-outline-danger-600 radius-8 px-16 py-9 text-xs fw-semibold d-inline-flex align-items-center gap-2"
                            data-bs-toggle="modal"
                            data-bs-target="#confirmationActionModal"
                            data-action="<?= site_url('dashboard/sertifikat/' . (int) $c['id'] . '/cabut') ?>"
                            data-method="POST"
                            data-type="danger"
                            data-icon="solar:shield-cross-bold"
                            data-icon-color="danger"
                            data-btn-class="btn-danger-600"
                            data-btn-text="Ya, Cabut Sertifikat"
                            data-title="Konfirmasi Pencabutan Sertifikat"
                            data-message="Anda akan mencabut sertifikat nomor <?= esc($c['certificate_number']) ?>. Tindakan pencabutan ini membatalkan status keabsahan dokumen secara permanen."
                            data-require-input="true"
                            data-input-label="Alasan Pencabutan Resmi *"
                            data-input-name="revocation_reason"
                            data-input-placeholder="Tuliskan alasan resmi pencabutan sertifikat...">
                            <iconify-icon icon="solar:close-circle-outline" class="text-lg"></iconify-icon>
                            Cabut Sertifikat Ini
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: Verification & Security Card -->
    <div class="col-xl-4">
        <div class="card radius-12 border text-center shadow-none mb-24">
            <div class="card-body p-24">
                <div class="w-80-px h-80-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center mb-16 mx-auto">
                    <iconify-icon icon="solar:qr-code-bold" class="text-4xl"></iconify-icon>
                </div>
                <h6 class="fw-semibold mb-2">QR Code & Verifikasi</h6>
                <p class="text-secondary-light text-xs mb-16">
                    Sertifikat ini memiliki QR Code tertanam yang menghubungkan langsung ke portal verifikasi online publik tanpa mengekspos data pribadi rahasia.
                </p>
                <div class="p-12 radius-8 bg-neutral-50 border text-start mb-16">
                    <span class="text-xs text-secondary-light d-block mb-1">Verifikasi Admin:</span>
                    <a href="<?= site_url('dashboard/sertifikat/verifikasi') ?>" class="text-xs text-primary-600 fw-semibold hover-underline d-flex align-items-center gap-1">
                        Dashboard Verifikasi <iconify-icon icon="solar:arrow-right-outline"></iconify-icon>
                    </a>
                </div>
                <div class="p-12 radius-8 bg-neutral-50 border text-start mb-20">
                    <span class="text-xs text-secondary-light d-block mb-1">Verifikasi Online Publik:</span>
                    <span class="text-xs text-primary-light font-monospace d-block text-truncate">/verifikasi-sertifikat</span>
                </div>
                <a href="<?= site_url('verifikasi-sertifikat') ?>" target="_blank" class="btn btn-outline-primary-600 w-100 radius-8 text-xs py-10 d-flex align-items-center justify-content-center gap-2">
                    <iconify-icon icon="solar:square-top-down-outline"></iconify-icon>
                    Buka Halaman Verifikasi Publik
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>