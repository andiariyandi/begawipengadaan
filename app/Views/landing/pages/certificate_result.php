<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<style>
    /* Universal high-contrast text utilities */
    .text-slate-900 {
        color: #0f172a !important;
    }

    .text-slate-800 {
        color: #1e293b !important;
    }

    .text-slate-700 {
        color: #334155 !important;
    }

    .text-slate-600 {
        color: #475569 !important;
    }

    .text-slate-500 {
        color: #64748b !important;
    }

    /* Hero Banner - 205px padding top gives 45px+ safe breathing room under the 160px absolute menu bar */
    .result-hero {
        position: relative;
        background: linear-gradient(135deg, #052244 0%, #0a3a6b 55%, #0c5aa6 100%);
        padding: 205px 0 75px;
        color: #ffffff;
        text-align: center;
    }

    .result-section {
        padding: 0 0 90px;
        margin-top: -40px;
        position: relative;
        z-index: 10;
    }

    /* Official Certificate Card Container */
    .cert-official-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(5, 34, 68, 0.18), 0 0 0 1px rgba(226, 232, 240, 0.8);
        overflow: hidden;
        position: relative;
    }

    /* Certificate Header / Emblem Bar */
    .cert-official-header {
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        padding: 32px 36px 20px;
        border-bottom: 2px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .cert-official-emblem {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .cert-official-emblem img {
        height: 60px;
        width: auto;
    }

    .cert-official-emblem__text h4 {
        font-size: 1.15rem;
        font-weight: 800;
        color: #052244;
        margin: 0 0 3px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .cert-official-emblem__text p {
        font-size: 0.825rem;
        color: #64748b;
        margin: 0;
        font-weight: 600;
    }

    /* Status Bar Ribbon */
    .cert-status-ribbon {
        padding: 14px 36px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: 700;
        font-size: 0.95rem;
    }

    .cert-status-ribbon--valid {
        background: linear-gradient(90deg, #ecfdf5 0%, #d1fae5 100%);
        color: #065f46;
        border-bottom: 1px solid #a7f3d0;
    }

    .cert-status-ribbon--revoked {
        background: linear-gradient(90deg, #fef2f2 0%, #fee2e2 100%);
        color: #991b1b;
        border-bottom: 1px solid #fecaca;
    }

    .cert-status-ribbon--expired {
        background: linear-gradient(90deg, #fffbeb 0%, #fef3c7 100%);
        color: #92400e;
        border-bottom: 1px solid #fde68a;
    }

    /* Certificate Body Content */
    .cert-official-body {
        padding: 40px 48px;
    }

    .cert-recipient-title {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #64748b;
        margin-bottom: 6px;
    }

    .cert-recipient-name {
        font-size: 2.25rem;
        font-weight: 800;
        color: #052244;
        margin-bottom: 24px;
        line-height: 1.2;
    }

    .cert-number-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        padding: 8px 16px;
        border-radius: 8px;
        font-family: monospace;
        font-size: 1.15rem;
        font-weight: 800;
        color: #0c5aa6;
        margin-bottom: 28px;
    }

    .cert-btn-copy {
        background: transparent;
        border: none;
        color: #64748b;
        cursor: pointer;
        padding: 2px 6px;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .cert-btn-copy:hover {
        background: #e2e8f0;
        color: #0c5aa6;
    }

    /* Details Grid */
    .cert-details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 24px;
        padding: 28px 0;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 32px;
    }

    .cert-detail-item__label {
        font-size: 0.825rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .cert-detail-item__val {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        line-height: 1.4;
    }

    /* Security & Signature Area */
    .cert-security-footer {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 24px;
        padding: 20px 24px;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px dashed #cbd5e1;
    }

    .cert-qr-mock {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .cert-qr-box {
        width: 72px;
        height: 72px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 34px;
        color: #052244;
    }

    .cert-security-hash {
        font-family: monospace;
        font-size: 0.815rem;
        color: #475569;
        margin: 0;
    }

    /* Action Toolbar */
    .cert-action-bar {
        margin-top: 32px;
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        justify-content: center;
    }

    .btn-cert-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.95rem;
        text-decoration: none;
        transition: all 0.25s ease;
        cursor: pointer;
    }

    .btn-cert-primary {
        background: #0c5aa6;
        color: #ffffff;
        border: 1px solid #0c5aa6;
    }

    .btn-cert-primary:hover {
        background: #083b70;
        color: #ffffff;
    }

    .btn-cert-outline {
        background: #ffffff;
        color: #334155;
        border: 1px solid #cbd5e1;
    }

    .btn-cert-outline:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    /* Error Card */
    .cert-error-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 50px 36px;
        text-align: center;
        box-shadow: 0 20px 45px rgba(5, 34, 68, 0.12);
        border: 1px solid #e2e8f0;
    }

    .cert-error-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #fee2e2;
        color: #dc2626;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        margin-bottom: 24px;
    }

    .cert-error-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 12px;
    }

    .cert-error-desc {
        color: #64748b;
        max-width: 600px;
        margin: 0 auto 30px;
        line-height: 1.7;
    }

    .cert-tips-box {
        background: #f8fafc;
        border-radius: 12px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        text-align: left;
        max-width: 650px;
        margin: 0 auto 32px;
    }

    /* Print Styles */
    @media print {
        body * {
            visibility: hidden;
        }

        .cert-official-card,
        .cert-official-card * {
            visibility: visible;
        }

        .cert-official-card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none;
            border: 2px solid #052244;
        }

        .cert-action-bar,
        .main-header,
        .site-footer {
            display: none !ns;
        }
    }

    @media (max-width: 991px) {
        .result-hero {
            padding: 175px 0 65px;
        }
    }

    @media (max-width: 768px) {
        .result-hero {
            padding: 155px 0 55px;
        }

        .cert-official-header {
            flex-direction: column;
            text-align: center;
        }

        .cert-official-body {
            padding: 24px 18px;
        }

        .cert-recipient-name {
            font-size: 1.65rem;
        }

        .cert-status-ribbon {
            flex-direction: column;
            gap: 8px;
            text-align: center;
        }
    }
</style>
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
                                <img src="<?= base_url('assets/main/images/resources/logo-4.png') ?>" alt="BegawiPengadaan Lampung">
                                <div class="cert-official-emblem__text">
                                    <h4>Pemerintah Provinsi Lampung</h4>
                                    <p>Biro Pengadaan Barang dan Jasa Sekretariat Daerah</p>
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