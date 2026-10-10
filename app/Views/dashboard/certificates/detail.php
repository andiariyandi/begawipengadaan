<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
    $c = $certificate;
    $isIssued = ($c['status'] ?? '') === 'issued';
    $issuedAtFmt = ! empty($c['issued_at'])
        ? date('d F Y', strtotime($c['issued_at']))
        : '—';
    $expiresAtFmt = ! empty($c['expires_at'])
        ? date('d F Y', strtotime($c['expires_at']))
        : 'Berlaku Selamanya';
    $revokedAtFmt = ! empty($c['revoked_at'])
        ? date('d F Y H:i', strtotime($c['revoked_at']))
        : '—';
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Kredensial Sertifikat</h6>
        <p class="text-secondary-light mb-0">Nomor Registrasi: <?= esc($c['certificate_number']) ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/sertifikat') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            Kembali
        </a>
        <?php if ($isIssued): ?>
            <a href="<?= site_url('dashboard/sertifikat/' . $c['id'] . '/unduh') ?>" class="btn btn-success-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
                <iconify-icon icon="solar:download-square-outline" class="text-lg"></iconify-icon>
                Unduh PDF
            </a>
            <button type="button" onclick="window.print()" class="btn btn-outline-neutral-300 radius-8 px-16 py-11 d-flex align-items-center gap-2">
                <iconify-icon icon="solar:printer-outline" class="text-lg"></iconify-icon>
                Cetak
            </button>
        <?php endif; ?>
        <a href="<?= site_url('dashboard/sertifikat/' . $c['id'] . '/ubah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:pen-2-outline" class="text-lg"></iconify-icon>
            Ubah
        </a>
    </div>
</div>


<?php if (! empty($issuedToken)): ?>
    <div class="alert alert-warning radius-8 mb-24 border border-warning-main">
        <div class="d-flex align-items-start gap-3">
            <iconify-icon icon="solar:shield-warning-bold" class="text-3xl text-warning-main flex-shrink-0"></iconify-icon>
            <div>
                <h6 class="mb-4 fw-semibold text-warning-main">Token Verifikasi Baru (Satu Kali Ditampilkan)</h6>
                <p class="mb-8 text-sm text-neutral-800">Simpan token ini sekarang. Token digunakan untuk verifikasi publik kredensial dan tidak akan ditampilkan lagi demi keamanan.</p>
                <code class="p-8 radius-6 bg-white border d-inline-block fw-bold text-sm text-neutral-900 select-all"><?= esc($issuedToken) ?></code>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row gy-4">
    <!-- Main Credential Sheet -->
    <div class="col-xl-8">
        <div class="card radius-12 border bg-white shadow-none">
            <!-- Sheet Header -->
            <div class="p-32 border-bottom bg-neutral-50 radius-top-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="w-56-px h-56-px bg-primary-100 text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-2xl flex-shrink-0">
                            <iconify-icon icon="solar:diploma-verified-outline"></iconify-icon>
                        </div>
                        <div>
                            <span class="text-xs text-secondary-light fw-medium d-block text-uppercase">BegawiPBJ Official Credential</span>
                            <h5 class="fw-bold text-primary-light mb-0"><?= esc($c['certificate_number']) ?></h5>
                        </div>
                    </div>
                    <div>
                        <?= view('dashboard/components/status_badge', ['status' => $c['status']]) ?>
                    </div>
                </div>
            </div>

            <!-- Sheet Body -->
            <div class="card-body p-32">
                <div class="p-24 radius-8 border bg-neutral-50 mb-24 text-center">
                    <span class="text-xs text-secondary-light d-block text-uppercase fw-semibold mb-2">Diberikan Kepada:</span>
                    <h4 class="fw-bold text-neutral-900 mb-1"><?= esc($c['user_name'] ?? '—') ?></h4>
                    <span class="text-secondary-light text-sm"><?= esc($c['user_email'] ?? '') ?></span>
                </div>

                <div class="row gy-4 mb-24">
                    <div class="col-md-6">
                        <div class="p-16 radius-8 border h-100">
                            <span class="text-secondary-light text-xs d-block mb-1">Program / Kelas Pelatihan</span>
                            <h6 class="text-sm fw-semibold text-neutral-900 mb-1"><?= esc($c['source_label'] ?? '—') ?></h6>
                            <span class="text-2xs text-secondary-light">ID Sumber: #<?= esc($c['source_id']) ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-16 radius-8 border h-100">
                            <span class="text-secondary-light text-xs d-block mb-1">Format Template Sertifikat</span>
                            <h6 class="text-sm fw-semibold text-neutral-900 mb-1"><?= esc($c['template_name'] ?? 'Format Standar PBJ') ?></h6>
                            <span class="text-2xs text-secondary-light">Otomatisasi Generator Sistem</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-16 radius-8 border h-100">
                            <span class="text-secondary-light text-xs d-block mb-1">Tanggal Penerbitan Resmi</span>
                            <h6 class="text-sm fw-semibold text-neutral-900 mb-0"><?= esc($issuedAtFmt) ?></h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-16 radius-8 border h-100">
                            <span class="text-secondary-light text-xs d-block mb-1">Masa Berlaku Kredensial</span>
                            <h6 class="text-sm fw-semibold text-neutral-900 mb-0"><?= esc($expiresAtFmt) ?></h6>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-16 radius-8 border bg-neutral-50">
                            <span class="text-secondary-light text-xs d-block mb-1">Hash Tanda Tangan Digital (SHA-256)</span>
                            <code class="text-xs text-neutral-900 word-break-all d-block"><?= esc($c['token_preview'] ?? '—') ?>…</code>
                            <span class="text-2xs text-secondary-light mt-1 d-block">Tersimpan secara kriptografis untuk validasi integritas dokumen.</span>
                        </div>
                    </div>
                </div>

                <?php if (! empty($c['revoked_at'])): ?>
                    <div class="p-20 radius-8 bg-danger-50 border border-danger-200">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <iconify-icon icon="solar:danger-triangle-bold" class="text-danger-main text-lg"></iconify-icon>
                            <h6 class="text-danger-main text-sm fw-semibold mb-0">Sertifikat Telah Dicabut</h6>
                        </div>
                        <p class="text-xs text-neutral-800 mb-1">Dicabut pada: <strong><?= esc($revokedAtFmt) ?></strong> oleh <strong><?= esc($c['revoked_by_name'] ?? 'Administrator') ?></strong></p>
                        <p class="text-xs text-neutral-800 mb-0">Alasan: <?= esc($c['revocation_reason'] ?? 'Tidak ada keterangan') ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($isIssued): ?>
            <!-- Revocation Action Panel -->
            <div class="card radius-12 border mt-24">
                <div class="card-body p-24">
                    <h6 class="fw-semibold mb-8 text-danger-main d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:shield-cross-outline"></iconify-icon>
                        Tindakan Pencabutan Sertifikat
                    </h6>
                    <p class="text-secondary-light text-sm mb-16">Pencabutan sertifikat akan membatalkan status validitas dokumen dan menonaktifkan verifikasi publik secara permanen.</p>
                    <button type="button" class="btn btn-outline-danger-600 radius-8 px-20 py-11 text-sm fw-semibold d-inline-flex align-items-center gap-2"
                            data-bs-toggle="modal"
                            data-bs-target="#confirmationActionModal"
                            data-action="<?= esc(site_url('dashboard/sertifikat/' . $c['id'] . '/cabut')) ?>"
                            data-method="POST"
                            data-title="Cabut Sertifikat Resmi"
                            data-message="Pencabutan sertifikat nomor <?= esc($c['certificate_number']) ?> akan membatalkan status validitas dokumen dan menonaktifkan verifikasi publik secara permanen."
                            data-btn-text="Ya, Cabut Sertifikat"
                            data-btn-class="btn-danger-600"
                            data-icon="solar:shield-cross-bold"
                            data-icon-color="danger"
                            data-require-input="true"
                            data-input-label="Alasan Resmi Pencabutan *"
                            data-input-name="revocation_reason"
                            data-input-placeholder="Contoh: Terjadi kesalahan integritas penilaian atau pembatalan kelulusan.">
                        <iconify-icon icon="solar:shield-cross-outline" class="text-lg"></iconify-icon>
                        <span>Cabut Sertifikat Ini</span>
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Sidebar Column: Verification Box -->
    <div class="col-xl-4">
        <div class="card radius-12 border text-center">
            <div class="card-body p-24">
                <div class="w-100-px h-100-px bg-primary-50 rounded-circle d-inline-flex align-items-center justify-content-center mb-16 mx-auto">
                    <iconify-icon icon="solar:qr-code-outline" class="text-primary-600 text-5xl"></iconify-icon>
                </div>
                <h6 class="fw-semibold mb-8">Verifikasi Kredensial</h6>
                <p class="text-secondary-light text-xs mb-16">
                    Setiap sertifikat resmi dilengkapi QR Code unik yang terhubung ke portal verifikasi publik BegawiPBJ.
                </p>
                <div class="p-12 radius-8 bg-neutral-50 border text-start mb-20">
                    <span class="text-2xs text-secondary-light d-block mb-1">URL Verifikasi:</span>
                    <span class="text-xs text-neutral-800 fw-medium">/verifikasi-sertifikat</span>
                </div>
                <a href="<?= site_url('verifikasi-sertifikat') ?>" target="_blank" class="btn btn-outline-primary-600 w-100 radius-8 text-sm py-10 d-flex align-items-center justify-content-center gap-2">
                    <iconify-icon icon="solar:square-top-down-outline"></iconify-icon>
                    Buka Portal Verifikasi
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>