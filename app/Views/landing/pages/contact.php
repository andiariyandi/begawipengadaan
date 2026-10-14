<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<style>
    html {
        scroll-behavior: smooth;
    }
    #form-pesan {
        scroll-margin-top: 130px;
    }

    /* Universal high-contrast text utilities */
    .text-slate-900 { color: #0f172a !important; }
    .text-slate-800 { color: #1e293b !important; }
    .text-slate-700 { color: #334155 !important; }
    .text-slate-600 { color: #475569 !important; }
    .text-slate-500 { color: #64748b !important; }

    /* Reliable Iconify display */
    iconify-icon {
        display: inline-block;
        vertical-align: middle;
        line-height: 1;
        width: 1.15em;
        height: 1.15em;
    }

    /* Hero Banner - 215px padding top gives 55px+ safe breathing room under the 160px absolute menu bar */
    .contact-hero {
        position: relative;
        background: linear-gradient(135deg, #052244 0%, #0a3a6b 55%, #0c5aa6 100%);
        padding: 215px 0 90px;
        color: #ffffff;
        overflow: hidden;
    }
    .contact-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 85% 15%, rgba(245, 158, 11, 0.18) 0%, transparent 45%),
                    radial-gradient(circle at 10% 85%, rgba(16, 185, 129, 0.14) 0%, transparent 50%);
        pointer-events: none;
    }
    .contact-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(245, 158, 11, 0.22);
        border: 1px solid rgba(245, 158, 11, 0.55);
        color: #fef3c7;
        padding: 8px 20px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 22px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
    }
    .contact-hero__title {
        font-size: 2.85rem;
        font-weight: 800;
        line-height: 1.25;
        color: #ffffff;
        margin-bottom: 18px;
        letter-spacing: -0.5px;
    }
    .contact-hero__title span {
        color: #f59e0b;
    }
    .contact-hero__subtitle {
        font-size: 1.15rem;
        color: #e2e8f0;
        line-height: 1.75;
        max-width: 820px;
        margin: 0 auto;
    }

    /* Info Cards Grid */
    .contact-info-section {
        margin-top: -50px;
        position: relative;
        z-index: 10;
        padding-bottom: 40px;
    }
    .contact-info-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 30px 26px;
        border: 1px solid #e2e8f0;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 12px 30px rgba(5, 34, 68, 0.08);
        transition: all 0.3s ease;
    }
    .contact-info-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 36px rgba(5, 34, 68, 0.14);
        border-color: #0c5aa6;
    }
    .contact-info-icon {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 20px;
    }
    .contact-info-icon--blue { background: #eff6ff; color: #0c5aa6; border: 1px solid #bfdbfe; }
    .contact-info-icon--green { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .contact-info-icon--amber { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .contact-info-icon--purple { background: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe; }

    .contact-info-title {
        font-size: 1.2rem;
        font-weight: 800;
        color: #052244;
        margin-bottom: 8px;
    }
    .contact-info-desc {
        color: #334155;
        font-size: 0.935rem;
        line-height: 1.65;
        margin-bottom: 20px;
    }
    .contact-info-link {
        font-weight: 700;
        font-size: 0.885rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    /* Form & Map Section */
    .contact-main-section {
        padding: 50px 0 80px;
    }
    .contact-form-box {
        background: #ffffff;
        border-radius: 20px;
        padding: 40px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(5, 34, 68, 0.05);
    }
    .contact-form-title {
        font-size: 1.85rem;
        font-weight: 800;
        color: #052244;
        margin-bottom: 8px;
    }
    .contact-form-subtitle {
        color: #475569;
        font-size: 0.975rem;
        margin-bottom: 28px;
        line-height: 1.6;
    }
    .contact-label {
        font-weight: 700;
        font-size: 0.885rem;
        color: #0f172a;
        margin-bottom: 7px;
        display: block;
    }
    .contact-input {
        width: 100%;
        padding: 13px 16px;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        font-size: 0.95rem;
        color: #0f172a;
        background-color: #ffffff;
        transition: all 0.2s ease;
    }
    .contact-input::placeholder {
        color: #94a3b8;
    }
    .contact-input:focus {
        border-color: #0c5aa6;
        background-color: #ffffff;
        box-shadow: 0 0 0 3.5px rgba(12, 90, 166, 0.15);
        outline: none;
    }
    .contact-submit-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: linear-gradient(135deg, #0c5aa6 0%, #083b70 100%);
        color: #ffffff;
        border: none;
        padding: 15px 36px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.25s ease;
        box-shadow: 0 4px 14px rgba(12, 90, 166, 0.25);
    }
    .contact-submit-btn:hover {
        background: linear-gradient(135deg, #083b70 0%, #052244 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(12, 90, 166, 0.35);
        color: #ffffff;
    }

    /* Map Box */
    .contact-map-box {
        background: #ffffff;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(5, 34, 68, 0.05);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .contact-map-iframe {
        width: 100%;
        height: 380px;
        border: 0;
        border-radius: 14px;
        flex-grow: 1;
    }

    /* FAQ Quick Accordion */
    .contact-faq-section {
        background: #f8fafc;
        padding: 70px 0;
        border-top: 1px solid #e2e8f0;
    }
    .contact-faq-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 14px;
        overflow: hidden;
        transition: border-color 0.2s ease;
    }
    .contact-faq-card:hover {
        border-color: #cbd5e1;
    }
    .contact-faq-header {
        padding: 18px 24px;
        cursor: pointer;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #ffffff;
        user-select: none;
    }
    .contact-faq-body {
        padding: 0 24px 20px;
        color: #334155;
        font-size: 0.95rem;
        line-height: 1.7;
    }

    @media (max-width: 991px) {
        .contact-hero { padding: 185px 0 75px; }
        .contact-hero__title { font-size: 2.35rem; }
    }

    @media (max-width: 768px) {
        .contact-hero { padding: 165px 0 65px; }
        .contact-hero__title { font-size: 2rem; }
        .contact-info-section { margin-top: -30px; }
        .contact-form-box { padding: 24px 18px; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="contact-hero text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="contact-badge-pill">
                    <i class="fas fa-headset"></i> Layanan Bantuan &amp; Helpdesk Pengadaan Barang/Jasa
                </div>
                <h1 class="contact-hero__title">
                    Hubungi Biro PBJ &amp; <span>Helpdesk LPSE</span>
                </h1>
                <p class="contact-hero__subtitle">
                    Pemerintah Provinsi Lampung siap memberikan layanan konsultasi teknis, fasilitasi SPSE, pendampingan regulasi, dan informasi pelatihan kompetensi pengadaan barang/jasa.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 4 Info Cards -->
<section class="contact-info-section">
    <div class="container">
        <div class="row g-4">
            <!-- Lokasi Kantor -->
            <div class="col-xl-3 col-md-6">
                <div class="contact-info-card">
                    <div>
                        <div class="contact-info-icon contact-info-icon--blue">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h4 class="contact-info-title">Lokasi Kantor</h4>
                        <p class="contact-info-desc">
                            <?= esc($contact['address']) ?>
                        </p>
                    </div>
                    <div>
                        <a href="<?= esc($contact['maps_dir_link']) ?>" target="_blank" rel="noopener noreferrer" class="contact-info-link text-primary">
                            <i class="fas fa-directions"></i> Petunjuk Arah Maps
                        </a>
                    </div>
                </div>
            </div>

            <!-- WhatsApp Helpdesk -->
            <div class="col-xl-3 col-md-6">
                <div class="contact-info-card">
                    <div>
                        <div class="contact-info-icon contact-info-icon--green">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <h4 class="contact-info-title">WhatsApp Helpdesk</h4>
                        <p class="contact-info-desc">
                            Layanan chat cepat untuk pertanyaan seputar SPSE, verifikasi profil penyedia, dan konsultasi PBJ.
                        </p>
                    </div>
                    <div>
                        <a href="<?= esc($contact['whatsapp_link']) ?>" target="_blank" rel="noopener noreferrer" class="contact-info-link text-success">
                            <i class="fab fa-whatsapp"></i> <?= esc($contact['whatsapp']) ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Email Resmi -->
            <div class="col-xl-3 col-md-6">
                <div class="contact-info-card">
                    <div>
                        <div class="contact-info-icon contact-info-icon--amber">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>
                        <h4 class="contact-info-title">Surat Elektronik</h4>
                        <p class="contact-info-desc">
                            Surat resmi dan permohonan tertulis: <br>
                            <strong><?= esc($contact['email']) ?></strong><br>
                            <span class="small text-muted"><?= esc($contact['email_alt']) ?></span>
                        </p>
                    </div>
                    <div>
                        <a href="mailto:<?= esc($contact['email']) ?>" class="contact-info-link text-warning">
                            <i class="fas fa-paper-plane"></i> Kirim Email Resmi
                        </a>
                    </div>
                </div>
            </div>

            <!-- Jam Pelayanan -->
            <div class="col-xl-3 col-md-6">
                <div class="contact-info-card">
                    <div>
                        <div class="contact-info-icon contact-info-icon--purple">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h4 class="contact-info-title">Jam Pelayanan</h4>
                        <p class="contact-info-desc mb-1">
                            <?= esc($contact['hours_weekday']) ?>
                        </p>
                        <p class="contact-info-desc mb-1">
                            <?= esc($contact['hours_friday']) ?>
                        </p>
                        <span class="badge bg-light text-danger border">
                            <?= esc($contact['hours_weekend']) ?>
                        </span>
                    </div>
                    <div class="pt-2">
                        <span class="small text-muted fw-bold">
                            <i class="fas fa-check-circle text-success me-1"></i> Pelayanan Bebas Biaya
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Form & Map Section -->
<section class="contact-main-section">
    <div class="container">
        <div class="row g-5">
            <!-- Form Konsultasi & Pengaduan -->
            <div class="col-lg-7" id="form-pesan">
                <div class="contact-form-box">
                    <h2 class="contact-form-title">Formulir Layanan &amp; Konsultasi</h2>
                    <p class="contact-form-subtitle">
                        Sampaikan pertanyaan, kendala teknis SPSE, permohonan bimtek, atau konsultasi pengadaan barang/jasa melalui formulir di bawah ini.
                    </p>

                    <!-- Flash Messages -->
                    <?php if (session()->getFlashdata('contact_success')): ?>
                        <div class="alert alert-success d-flex align-items-center gap-3 p-3 rounded-3 mb-4" role="alert">
                            <i class="fas fa-check-circle fa-2x"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Berhasil Terkirim!</h6>
                                <p class="mb-0 small"><?= session()->getFlashdata('contact_success') ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('contact_error')): ?>
                        <div class="alert alert-danger d-flex align-items-center gap-3 p-3 rounded-3 mb-4" role="alert">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Perhatian</h6>
                                <p class="mb-0 small"><?= session()->getFlashdata('contact_error') ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form action="<?= site_url('kontak') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="contact-label">Nama Lengkap *</label>
                                <input type="text" id="name" name="name" class="contact-input" placeholder="Contoh: Budi Santoso, S.T." value="<?= old('name') ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="contact-label">Alamat Email *</label>
                                <input type="email" id="email" name="email" class="contact-input" placeholder="contoh@lampungprov.go.id" value="<?= old('email') ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="contact-label">Nomor WhatsApp / Telepon</label>
                                <input type="text" id="phone" name="phone" class="contact-input" placeholder="Contoh: 081234567890" value="<?= old('phone') ?>">
                            </div>

                            <div class="col-md-6">
                                <label for="organization" class="contact-label">Instansi / OPD / Nama Perusahaan</label>
                                <input type="text" id="organization" name="organization" class="contact-input" placeholder="Contoh: Dinas Bina Marga Provinsi Lampung" value="<?= old('organization') ?>">
                            </div>

                            <div class="col-12">
                                <label for="service_type" class="contact-label">Kategori Layanan *</label>
                                <select id="service_type" name="service_type" class="contact-input" required>
                                    <option value="" disabled <?= old('service_type') ? '' : 'selected' ?>>Pilih Kategori Permohonan / Layanan...</option>
                                    <?php foreach ($serviceCategories as $cat): ?>
                                        <option value="<?= esc($cat) ?>" <?= old('service_type') === $cat ? 'selected' : '' ?>><?= esc($cat) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="subject" class="contact-label">Subjek / Pokok Pertanyaan *</label>
                                <input type="text" id="subject" name="subject" class="contact-input" placeholder="Contoh: Permohonan Konsultasi E-Katalog Pengadaan Komputer" value="<?= old('subject') ?>" required>
                            </div>

                            <div class="col-12">
                                <label for="message" class="contact-label">Isi Pesan / Uraian Konsultasi *</label>
                                <textarea id="message" name="message" class="contact-input" rows="5" placeholder="Tuliskan rincian pertanyaan atau permasalahan yang ingin Anda konsultasikan..." required><?= old('message') ?></textarea>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="contact-submit-btn">
                                    <i class="fas fa-paper-plane"></i> Kirim Permohonan Konsultasi
                                </button>
                                <span class="d-block small text-muted mt-2">
                                    <i class="fas fa-shield-alt text-primary me-1"></i> Data Anda dijamin kerahasiaannya sesuai standar integritas UKPBJ Lampung.
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Peta Lokasi Kantor -->
            <div class="col-lg-5">
                <div class="contact-map-box">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h4 class="fw-bold text-dark mb-0">Lokasi Kantor Resmi</h4>
                            <small class="text-muted">Kompleks Kantor Gubernur Lampung</small>
                        </div>
                        <a href="<?= esc($contact['maps_dir_link']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-external-link-alt me-1"></i> Buka Peta
                        </a>
                    </div>
                    <iframe
                        src="<?= esc($contact['maps_embed_url']) ?>"
                        class="contact-map-iframe"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Peta Kantor Gubernur Lampung">
                    </iframe>
                    <div class="mt-3 p-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fas fa-building text-primary"></i>
                            <strong class="text-dark small">Gedung Biro Pengadaan Barang dan Jasa</strong>
                        </div>
                        <p class="small text-muted mb-0">
                            Kompleks Kantor Gubernur Lampung, Jl. Wolter Monginsidi No. 69, Teluk Betung, Bandar Lampung 35211
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick FAQ Section -->
<section class="contact-faq-section">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fw-bold text-uppercase mb-2">
                Tanya Jawab Layanan
            </span>
            <h2 class="h3 fw-bold text-dark">Pertanyaan Umum Terkait Helpdesk</h2>
            <p class="text-muted">Informasi ringkas seputar prosedur konsultasi dan bantuan PBJ Provinsi Lampung.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- FAQ 1 -->
                <div class="contact-faq-card">
                    <div class="contact-faq-header" onclick="toggleFaq(this)">
                        <span>Apakah layanan konsultasi dan helpdesk Biro PBJ dipungut biaya?</span>
                        <i class="fas fa-chevron-down text-primary"></i>
                    </div>
                    <div class="contact-faq-body">
                        Seluruh layanan konsultasi, helpdesk LPSE, dan fasilitasi teknis pengadaan pada Biro Pengadaan Barang dan Jasa Pemerintah Provinsi Lampung <strong>100% GRATIS dan bebas biaya</strong>. Pimpinan dan staf kami menolak segala bentuk gratifikasi.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="contact-faq-card">
                    <div class="contact-faq-header" onclick="toggleFaq(this)">
                        <span>Berapa lama estimasi respon terhadap permohonan konsultasi online?</span>
                        <i class="fas fa-chevron-down text-primary"></i>
                    </div>
                    <div class="contact-faq-body">
                        Untuk pesan yang masuk melalui formulir online dan email, tim helpdesk akan merespons maksimal <strong>1 x 24 jam kerja</strong>. Untuk kebutuhan mendesak, silakan gunakan hotline WhatsApp kami.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="contact-faq-card">
                    <div class="contact-faq-header" onclick="toggleFaq(this)">
                        <span>Bagaimana cara mengajukan pendampingan PBJ untuk Perangkat Daerah?</span>
                        <i class="fas fa-chevron-down text-primary"></i>
                    </div>
                    <div class="contact-faq-body">
                        Perangkat Daerah (OPD) dapat mengirimkan surat permohonan resmi yang ditujukan kepada Kepala Biro Pengadaan Barang dan Jasa Setda Provinsi Lampung, atau mengisi formulir konsultasi dengan memilih kategori "Konsultasi Perencanaan &amp; Pemilihan PBJ".
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="contact-faq-card">
                    <div class="contact-faq-header" onclick="toggleFaq(this)">
                        <span>Bagaimana solusi jika akun SPSE penyedia terkunci atau lupa password?</span>
                        <i class="fas fa-chevron-down text-primary"></i>
                    </div>
                    <div class="contact-faq-body">
                        Penyedia barang/jasa dapat membawa dokumen legalitas perusahaan dan KTP direktur/penanggung jawab ke Helpdesk LPSE Provinsi Lampung secara langsung, atau melakukan konfirmasi verifikasi melalui WhatsApp Helpdesk resmi kami.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('assets/dashboard/js/lib/iconify-icon.min.js') ?>"></script>
<script>
    function toggleFaq(header) {
        const body = header.nextElementSibling;
        const icon = header.querySelector('i');
        if (body.style.display === 'none' || body.style.display === '') {
            body.style.display = 'block';
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            body.style.display = 'none';
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
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
                    requestAnimationFrame(function() { isClamping = false; });
                }
            }
        }, { passive: true });
    })();
</script>
<?= $this->endSection() ?>