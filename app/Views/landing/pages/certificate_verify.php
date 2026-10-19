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
                    Periksa otentisitas dan keabsahan sertifikat kompetensi pengadaan barang dan jasa Pemerintah Provinsi Lampung yang diterbitkan secara elektronik oleh Biro Pengadaan Barang dan Jasa Sekretariat Daerah.
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
                <form action="<?= site_url('verifikasi-sertifikat/hasil') ?>" method="post" id="certVerificationForm">
                    <?= csrf_field() ?>
                    <label for="certNumberInput" class="form-label fw-bold text-dark mb-2">
                        <i class="fas fa-barcode text-primary me-1"></i> Masukkan Nomor Sertifikat atau Token Verifikasi
                    </label>
                    <div class="cert-search-input-wrap">
                        <i class="fas fa-search cert-search-icon"></i>
                        <input
                            type="text"
                            id="certNumberInput"
                            name="certificate_number"
                            class="cert-search-input"
                            placeholder="Contoh: SERT/2026/00003 atau masukkan token kriptografi..."
                            value="<?= esc($keyword) ?>"
                            required
                            autocomplete="off">
                        <button type="submit" class="cert-btn-verify">
                            <i class="fas fa-check-circle"></i> Verifikasi Sekarang
                        </button>
                    </div>
                </form>

                <!-- Quick Samples -->
                <?php if (! empty($sampleNumbers)): ?>
                    <div class="cert-quick-samples">
                        <span class="cert-quick-label"><i class="fas fa-lightbulb text-warning me-1"></i> Coba Nomor Terbit Resmi:</span>
                        <?php foreach ($sampleNumbers as $sample): ?>
                            <button type="button" class="cert-sample-pill btn-sample-fill" data-sample="<?= esc($sample) ?>">
                                <?= esc($sample) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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
<section class="container">
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

<!-- Directory / Ledger of Verifiable Certificates -->
<section class="cert-directory-section">
    <div class="container">
        <div class="cert-section-header d-flex flex-wrap justify-content-between align-items-end">
            <div>
                <h2 class="cert-section-title">Direktori Publik Sertifikat Terdaftar</h2>
                <p class="cert-section-subtitle mb-0">
                    Daftar publikasi sertifikat resmi yang telah diterbitkan untuk transparansi publik dan validasi pemangku kepentingan.
                </p>
            </div>
            <div class="mt-3 mt-md-0 text-muted small">
                Menampilkan <strong><?= count($certificates) ?></strong> dari <strong><?= number_format($pagerData['totalRows']) ?></strong> sertifikat
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="cert-filter-bar">
            <div class="cert-filter-pills">
                <a href="<?= site_url('verifikasi-sertifikat?' . http_build_query(array_merge($_GET, ['tipe' => 'all', 'page' => 1]))) ?>"
                    class="cert-filter-pill <?= $sourceFilter === 'all' || $sourceFilter === '' ? 'active' : '' ?>">
                    <i class="fas fa-th-large"></i> Semua Sumber
                </a>
                <a href="<?= site_url('verifikasi-sertifikat?' . http_build_query(array_merge($_GET, ['tipe' => 'learning_enrollment', 'page' => 1]))) ?>"
                    class="cert-filter-pill <?= $sourceFilter === 'learning_enrollment' ? 'active' : '' ?>">
                    <i class="fas fa-graduation-cap"></i> Pelatihan (Arena Belajar)
                </a>
                <a href="<?= site_url('verifikasi-sertifikat?' . http_build_query(array_merge($_GET, ['tipe' => 'event_registration', 'page' => 1]))) ?>"
                    class="cert-filter-pill <?= $sourceFilter === 'event_registration' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-alt"></i> Event &amp; Seminar PBJ
                </a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-bold">Status:</span>
                <a href="<?= site_url('verifikasi-sertifikat?' . http_build_query(array_merge($_GET, ['status' => 'all', 'page' => 1]))) ?>"
                    class="btn btn-sm <?= $statusFilter === 'all' || $statusFilter === '' ? 'btn-dark' : 'btn-outline-secondary' ?>">Semua</a>
                <a href="<?= site_url('verifikasi-sertifikat?' . http_build_query(array_merge($_GET, ['status' => 'issued', 'page' => 1]))) ?>"
                    class="btn btn-sm <?= $statusFilter === 'issued' ? 'btn-success' : 'btn-outline-success' ?>">Valid (Issued)</a>
                <a href="<?= site_url('verifikasi-sertifikat?' . http_build_query(array_merge($_GET, ['status' => 'revoked', 'page' => 1]))) ?>"
                    class="btn btn-sm <?= $statusFilter === 'revoked' ? 'btn-danger' : 'btn-outline-danger' ?>">Dicabut</a>
            </div>
        </div>

        <!-- Certificate Cards Grid -->
        <?php if (! empty($certificates)): ?>
            <div class="row g-4">
                <?php foreach ($certificates as $cert): ?>
                    <div class="col-xl-4 col-md-6">
                        <div class="cert-card">
                            <div>
                                <div class="cert-card__top">
                                    <span class="cert-card__status-badge <?= $cert['status_badge'] ?>">
                                        <i class="fas <?= $cert['is_valid'] ? 'fa-check-circle' : ($cert['is_revoked'] ? 'fa-ban' : 'fa-info-circle') ?> me-1"></i>
                                        <?= esc($cert['status_label']) ?>
                                    </span>
                                    <span class="badge bg-light text-dark border">
                                        <?= $cert['source_type'] === 'learning_enrollment' ? 'Arena Belajar' : 'Event PBJ' ?>
                                    </span>
                                </div>

                                <div class="cert-card__number">
                                    <i class="fas fa-stamp text-warning"></i> <?= esc($cert['certificate_number']) ?>
                                </div>

                                <h3 class="cert-card__title">
                                    <?= esc($cert['title']) ?>
                                </h3>

                                <div class="cert-card__user">
                                    <i class="fas fa-user-circle text-primary"></i>
                                    <span><?= esc($cert['user_name']) ?></span>
                                </div>

                                <div class="cert-card__meta">
                                    <div class="d-flex justify-content-between">
                                        <span><i class="far fa-calendar-alt me-1"></i> Diterbitkan:</span>
                                        <strong><?= esc($cert['issued_at_human']) ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span><i class="fas fa-key me-1"></i> Keamanan:</span>
                                        <span class="font-monospace text-muted"><?= esc($cert['security_code']) ?></span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <a href="<?= site_url('verifikasi-sertifikat/' . rawurlencode($cert['certificate_number'])) ?>" class="cert-card__btn-view">
                                    <i class="fas fa-shield-alt"></i> Cek Keabsahan Lengkap
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination Bar -->
            <?php if ($pagerData['totalPages'] > 1): ?>
                <div class="begawi-pagination">
                    <a href="<?= site_url('verifikasi-sertifikat?' . http_build_query(array_merge($_GET, ['page' => $pagerData['prevPage']]))) ?>"
                        class="begawi-pagination__link <?= ! $pagerData['hasPrev'] ? 'disabled' : '' ?>"
                        aria-label="Halaman Sebelumnya">
                        <i class="fas fa-chevron-left"></i>
                    </a>

                    <?php for ($p = 1; $p <= $pagerData['totalPages']; $p++): ?>
                        <?php if ($p === 1 || $p === $pagerData['totalPages'] || abs($p - $pagerData['page']) <= 2): ?>
                            <a href="<?= site_url('verifikasi-sertifikat?' . http_build_query(array_merge($_GET, ['page' => $p]))) ?>"
                                class="begawi-pagination__link <?= $p === $pagerData['page'] ? 'active' : '' ?>">
                                <?= $p ?>
                            </a>
                        <?php elseif (abs($p - $pagerData['page']) === 3): ?>
                            <span class="px-2 text-muted">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <a href="<?= site_url('verifikasi-sertifikat?' . http_build_query(array_merge($_GET, ['page' => $pagerData['nextPage']]))) ?>"
                        class="begawi-pagination__link <?= ! $pagerData['hasNext'] ? 'disabled' : '' ?>"
                        aria-label="Halaman Berikutnya">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="text-center py-5 bg-light rounded-4 border">
                <i class="fas fa-search-minus text-muted fa-3x mb-3"></i>
                <h4 class="fw-bold text-dark">Tidak Ada Data Sertifikat</h4>
                <p class="text-muted">Tidak ditemukan sertifikat dengan kriteria pencarian yang Anda tentukan.</p>
                <a href="<?= site_url('verifikasi-sertifikat') ?>" class="btn btn-outline-primary mt-2">
                    <i class="fas fa-sync-alt me-1"></i> Reset Pencarian
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('assets/dashboard/js/lib/iconify-icon.min.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quick sample fill handler
        const sampleButtons = document.querySelectorAll('.btn-sample-fill');
        const certInput = document.getElementById('certNumberInput');
        const certForm = document.getElementById('certVerificationForm');

        sampleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const sampleVal = this.getAttribute('data-sample');
                if (sampleVal && certInput) {
                    certInput.value = sampleVal;
                    certForm.submit();
                }
            });
        });
    });

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