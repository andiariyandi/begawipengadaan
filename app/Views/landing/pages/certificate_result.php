<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-about.css') ?>" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="result-hero">
    <div class="container">
        <h1 class="h2 fw-bold text-white mb-2">
            Hasil Otentikasi Sertifikat Digital
        </h1>
        <p class="text-white-50 mb-0">
            Pemerintah Provinsi Lampung — Biro Pengadaan Barang dan Jasa Sekretariat Daerah
        </p>
    </div>
</section>

<!-- Main Result Content -->
<section class="result-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-9 col-lg-10">

                <?php if ($result !== null): ?>
                    <!-- SUCCESS / FOUND CERTIFICATE -->
                    <div class="cert-official-card" id="printableCertificate">
                        <!-- Header with Emblem -->
                        <div class="cert-official-header">
                            <div class="cert-official-emblem">
                                <?php
                                $certLogo = setting('organization.logo_url');
                                $certLogoSrc = ! empty($certLogo) ? (str_starts_with($certLogo, 'http') ? $certLogo : base_url($certLogo)) : base_url('assets/main/images/resources/logo-4.png');
                                ?>
                                <img src="<?= esc($certLogoSrc) ?>" alt="BegawiPengadaan Lampung" style="object-fit: contain;">
                                <div class="cert-official-emblem__text">
                                    <h4><?= esc(setting('organization.provincial_unit', 'Pemerintah Provinsi Lampung')) ?></h4>
                                    <p><?= esc(setting('organization.agency_name', 'Biro Pengadaan Barang dan Jasa Sekretariat Daerah')) ?></p>
                                </div>
                            </div>
                            <div class="text-md-end text-center mt-2 mt-md-0">
                                <span class="badge bg-primary text-white px-3 py-2">
                                    <i class="fas fa-lock me-1"></i> VERIFIKASI RESMI
                                </span>
                            </div>
                        </div>

                        <!-- Status Ribbon -->
                        <div class="cert-status-ribbon <?= $result['is_valid'] ? 'cert-status-ribbon--valid' : ($result['is_revoked'] ? 'cert-status-ribbon--revoked' : 'cert-status-ribbon--expired') ?>">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas <?= $result['is_valid'] ? 'fa-check-circle' : ($result['is_revoked'] ? 'fa-times-circle' : 'fa-exclamation-triangle') ?> fa-lg"></i>
                                <span>STATUS KEABSAHAN: <?= strtoupper(esc($result['status_label'])) ?></span>
                            </div>
                            <div class="small font-monospace">
                                REF: <?= esc($result['security_code']) ?>
                            </div>
                        </div>

                        <!-- Body Content -->
                        <div class="cert-official-body">
                            <div class="cert-recipient-title">Sertifikat Terverifikasi Atas Nama:</div>
                            <h2 class="cert-recipient-name">
                                <?= esc($result['user_name']) ?>
                            </h2>

                            <div class="cert-number-badge">
                                <i class="fas fa-certificate text-warning"></i>
                                <span id="certNumText"><?= esc($result['certificate_number']) ?></span>
                                <button type="button" class="cert-btn-copy" onclick="copyCertNumber()" title="Salin Nomor Sertifikat">
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>

                            <!-- Details Grid -->
                            <div class="cert-details-grid">
                                <div>
                                    <div class="cert-detail-item__label">Kegiatan / Program Pembelajaran</div>
                                    <p class="cert-detail-item__val text-primary"><?= esc($result['title']) ?></p>
                                </div>
                                <div>
                                    <div class="cert-detail-item__label">Sumber Layanan PBJ</div>
                                    <p class="cert-detail-item__val"><?= esc($result['source_label']) ?></p>
                                </div>
                                <div>
                                    <div class="cert-detail-item__label">Tanggal Penerbitan</div>
                                    <p class="cert-detail-item__val"><?= esc($result['issued_at_human']) ?></p>
                                </div>
                                <div>
                                    <div class="cert-detail-item__label">Masa Berlaku</div>
                                    <p class="cert-detail-item__val"><?= esc($result['expires_at_human']) ?></p>
                                </div>
                            </div>

                            <!-- Electronic Signature (TTE) Official Panel -->
                            <div class="cert-tte-panel my-4 p-3 bg-light rounded-3 border border-primary-subtle">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary text-white px-2 py-1">
                                            <i class="fas fa-file-signature me-1"></i> TTE RESMI
                                        </span>
                                        <span class="fw-bold text-dark small">Tanda Tangan Elektronik Terverifikasi</span>
                                    </div>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle small">
                                        <i class="fas fa-check-double me-1"></i> <?= esc($result['tte_status'] ?? 'DITANDATANGANI SECARA ELEKTRONIK & SAH') ?>
                                    </span>
                                </div>
                                <div class="row g-2 pt-2 border-top border-secondary-subtle small">
                                    <div class="col-md-6">
                                        <div class="text-muted">Pejabat Penandatangan:</div>
                                        <div class="fw-bold text-dark"><?= esc($result['tte_signer_name'] ?? 'Ir. S. Hendriyanto, S.T., M.T.') ?></div>
                                        <div class="text-secondary"><?= esc($result['tte_signer_position'] ?? 'Kepala Biro Pengadaan Barang dan Jasa') ?></div>
                                        <div class="text-muted font-monospace">NIP. <?= esc($result['tte_signer_nip'] ?? '19730915 199902 1 001') ?></div>
                                    </div>
                                    <div class="col-md-6 text-md-end text-start mt-2 mt-md-0">
                                        <div class="text-muted">Waktu Pembubuhan TTE:</div>
                                        <div class="fw-semibold text-dark"><?= esc($result['tte_timestamp'] ?? '—') ?></div>
                                        <div class="text-muted mt-1">Segel Kriptografis:</div>
                                        <div class="font-monospace text-primary fw-bold"><?= esc($result['tte_checksum'] ?? '—') ?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Issuer & Cryptographic Hash Footer -->
                            <div class="cert-security-footer">
                                <div class="cert-qr-mock">
                                    <div class="cert-qr-box">
                                        <i class="fas fa-qrcode"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small mb-1">
                                            <i class="fas fa-fingerprint text-success me-1"></i> Integritas Kriptografis SHA-256
                                        </div>
                                        <p class="cert-security-hash">
                                            Token: <?= esc($result['masked_hash']) ?>
                                        </p>
                                        <p class="small text-muted mb-0">
                                            Pengesahan: <?= esc($result['issuer']) ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fw-bold">
                                        <i class="fas fa-database me-1"></i> TERCATAT DALAM BASIS DATA RESMI
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="cert-action-bar">
                        <button type="button" class="btn-cert-action btn-cert-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Cetak Bukti Verifikasi
                        </button>
                        <button type="button" class="btn-cert-action btn-cert-outline" onclick="copyVerifyLink()">
                            <i class="fas fa-share-alt"></i> Salin Tautan Verifikasi
                        </button>
                        <a href="<?= site_url('verifikasi-sertifikat') ?>" class="btn-cert-action btn-cert-outline">
                            <i class="fas fa-search"></i> Periksa Sertifikat Lain
                        </a>
                    </div>

                <?php else: ?>
                    <!-- ERROR / NOT FOUND -->
                    <div class="cert-error-card">
                        <div class="cert-error-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h2 class="cert-error-title">Sertifikat Tidak Ditemukan</h2>
                        <p class="cert-error-desc">
                            <?= esc($error ?? 'Nomor sertifikat atau kode verifikasi yang Anda masukkan tidak tercatat dalam pangkalan data resmi BegawiPengadaan Pemerintah Provinsi Lampung.') ?>
                        </p>

                        <!-- Verification Help Tips -->
                        <div class="cert-tips-box">
                            <h5 class="fw-bold text-dark mb-2">
                                <i class="fas fa-info-circle text-primary me-2"></i>Panduan Pemeriksaan:
                            </h5>
                            <ul class="mb-0 text-muted small ps-3">
                                <li class="mb-1">Pastikan format nomor sertifikat sesuai contoh: <code>SERT/2026/00003</code> atau <code>BGPBJ-2026-XXXXX</code>.</li>
                                <li class="mb-1">Jika menggunakan QR scanner, pastikan kamera membaca kode token secara utuh tanpa terpotong.</li>
                                <li class="mb-1">Periksa apakah terdapat spasi tambahan sebelum atau sesudah nomor sertifikat.</li>
                                <li>Jika Anda baru saja menyelesaikan pelatihan atau kegiatan, sinkronisasi penerbitan membutuhkan waktu beberapa saat.</li>
                            </ul>
                        </div>

                        <!-- Quick Re-Search Form -->
                        <div class="row justify-content-center mb-4">
                            <div class="col-md-8">
                                <form action="<?= site_url('verifikasi-sertifikat/hasil') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            name="certificate_number"
                                            class="form-control form-control-lg"
                                            placeholder="Masukkan nomor sertifikat..."
                                            value="<?= esc($searched ?? '') ?>"
                                            required>
                                        <button class="btn btn-primary px-4 fw-bold" type="submit">
                                            <i class="fas fa-search me-1"></i> Cek Ulang
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div>
                            <a href="<?= site_url('verifikasi-sertifikat') ?>" class="btn-cert-action btn-cert-outline me-2">
                                <i class="fas fa-arrow-left"></i> Kembali ke Direktori
                            </a>
                            <a href="<?= site_url('kontak') ?>" class="btn-cert-action btn-cert-primary">
                                <i class="fas fa-headset"></i> Hubungi Helpdesk PBJ
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    function copyCertNumber() {
        const num = document.getElementById('certNumText').innerText;
        navigator.clipboard.writeText(num).then(() => {
            alert('Nomor sertifikat berhasil disalin: ' + num);
        });
    }

    function copyVerifyLink() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Tautan verifikasi resmi berhasil disalin ke papan klip!');
        });
    }

    (function() {
        // Bulletproof zero-jitter clamping at scroll extremities for ultra-smooth scrolling
        var isClamping = false;
        window.addEventListener('scroll', function() {
            var maxScroll = Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
            if (window.scrollY > maxScroll) {
                if (!isClamping) {
                    isClamping = true;
                    window.scrollTo(0, maxScroll);
                    requestAnimationFrame(function() {
                        isClamping = false;
                    });
                }
            }
        }, {
            passive: true
        });
    })();
</script>
<?= $this->endSection() ?>