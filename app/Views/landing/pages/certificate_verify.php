<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-about.css') ?>" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="cert-hero text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="cert-badge-pill">
                    <i class="fas fa-shield-alt"></i> Portal Resmi Verifikasi Sertifikat Digital PBJ
                </div>
                <h1 class="cert-hero__title">
                    Verifikasi Keabsahan <span>Sertifikat Digital</span>
                </h1>
                <p class="cert-hero__subtitle">
                    Periksa otentisitas dan keabsahan sertifikat kelulusan kegiatan belajar pengadaan barang dan jasa Pemerintah Provinsi Lampung yang diterbitkan secara elektronik oleh Biro Pengadaan Barang dan Jasa Sekretariat Daerah.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Search Verification Box -->
<section class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="cert-search-card">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                        <div><?= esc(session()->getFlashdata('error')) ?></div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= site_url('verifikasi-sertifikat/hasil') ?>" method="post" id="certVerificationForm">
                    <?= csrf_field() ?>
                    <!-- Anti-Bot Honeypot -->
                    <div class="d-none">
                        <input type="text" name="website_url" autocomplete="off" tabindex="-1">
                    </div>

                    <label for="certNumberInput" class="form-label fw-bold text-dark mb-2">
                        <i class="fas fa-barcode text-primary me-1"></i> Masukkan Nomor Sertifikat atau Token Verifikasi
                    </label>

                    <div class="cert-search-input-wrap mb-4">
                        <i class="fas fa-search cert-search-icon"></i>
                        <input
                            type="text"
                            id="certNumberInput"
                            name="certificate_number"
                            class="cert-search-input"
                            placeholder="Contoh: SERT/2026/00003 atau masukkan kode/token kriptografi..."
                            value="<?= esc($keyword ?? '') ?>"
                            required
                            autocomplete="off">
                    </div>

                    <!-- Simple Math Captcha Anti-Spam Widget (High-Contrast Guaranteed) -->
                    <div class="cert-captcha-box" style="background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px;">
                        <div class="row align-items-center g-3">
                            <div class="col-md-7">
                                <label for="cert-captcha" class="form-label mb-1 fw-bold text-dark small d-flex align-items-center">
                                    <i class="fas fa-shield-alt text-primary me-2 fs-6"></i> Verifikasi Keamanan (Anti-Spam Captcha) <span class="text-danger ms-1">*</span>
                                </label>
                                <div class="text-muted" style="font-size: 0.82rem;">Hitung dan masukkan angka hasil penjumlahan di samping:</div>
                            </div>
                            <div class="col-md-5 d-flex align-items-center justify-content-md-end gap-2">
                                <div class="cert-captcha-math font-monospace" style="background: #ffffff !important; color: #0f172a !important; font-weight: 800 !important; font-size: 1.25rem !important; padding: 8px 18px !important; border: 2px solid #0c5aa6 !important; border-radius: 10px !important; letter-spacing: 3px !important; white-space: nowrap !important; user-select: none !important; box-shadow: 0 2px 6px rgba(12, 90, 166, 0.12) !important; line-height: 1.2 !important;" aria-label="Soal Matematika Captcha">
                                    <?= esc($captchaQuestion ?? '7 + 5') ?> = ?
                                </div>
                                <input
                                    type="number"
                                    class="form-control font-monospace cert-captcha-input"
                                    id="cert-captcha"
                                    name="captcha_answer"
                                    placeholder="Hasil"
                                    required
                                    autocomplete="off"
                                    style="max-width: 130px; height: 50px; font-size: 1.15rem; font-weight: 700; text-align: center; border: 2px solid #cbd5e1; border-radius: 10px; background: #ffffff; color: #0f172a;">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid d-md-flex justify-content-md-end">
                        <button type="submit" class="cert-btn-verify px-4 py-3">
                            <i class="fas fa-check-circle me-1"></i> Verifikasi Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Live KPI Statistics Ribbon -->
<section class="cert-stats-section">
    <div class="container">
        <div class="row g-3">
            <div class="col-xl-3 col-md-6">
                <div class="cert-stat-card">
                    <div class="cert-stat-icon cert-stat-icon--blue">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div>
                        <div class="cert-stat-number"><?= number_format($stats['total_issued'] ?? 0) ?></div>
                        <p class="cert-stat-label">Sertifikat Resmi Diterbitkan</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="cert-stat-card">
                    <div class="cert-stat-icon cert-stat-icon--amber">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <div class="cert-stat-number"><?= number_format($stats['total_learning'] ?? 0) ?></div>
                        <p class="cert-stat-label">Pelatihan Arena Belajar</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="cert-stat-card">
                    <div class="cert-stat-icon cert-stat-icon--emerald">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <div class="cert-stat-number"><?= number_format($stats['total_event'] ?? 0) ?></div>
                        <p class="cert-stat-label">Event &amp; Seminar PBJ</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="cert-stat-card">
                    <div class="cert-stat-icon cert-stat-icon--purple">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div>
                        <div class="cert-stat-number">100%</div>
                        <p class="cert-stat-label">Keabsahan Kriptografi SHA-256</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3 Security Pillars -->
<section class="container pb-5">
    <div class="cert-trust-grid">
        <div class="cert-trust-item">
            <div class="cert-trust-item__icon">
                <i class="fas fa-fingerprint"></i>
            </div>
            <div>
                <h4 class="cert-trust-item__title">Kriptografi Anti-Pemalsuan</h4>
                <p class="cert-trust-item__desc">
                    Setiap sertifikat dilengkapi token kriptografis unik berbasis algoritma SHA-256 yang mustahil dipalsukan atau diubah secara sepihak.
                </p>
            </div>
        </div>
        <div class="cert-trust-item">
            <div class="cert-trust-item__icon">
                <i class="fas fa-signature"></i>
            </div>
            <div>
                <h4 class="cert-trust-item__title">Tanda Tangan Digital Resmi</h4>
                <p class="cert-trust-item__desc">
                    Disahkan langsung oleh Kepala Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung melalui otentikasi sistem BegawiPengadaan.
                </p>
            </div>
        </div>
        <div class="cert-trust-item">
            <div class="cert-trust-item__icon">
                <i class="fas fa-history"></i>
            </div>
            <div>
                <h4 class="cert-trust-item__title">Jejak Audit Terverifikasi</h4>
                <p class="cert-trust-item__desc">
                    Riwayat penerbitan, tanggal kelulusan, dan status sertifikat (aktif atau dicabut) tercatat transparan dalam basis data pemerintah.
                </p>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('assets/main/js/lib/iconify-icon.min.js') ?>"></script>
<script>
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
