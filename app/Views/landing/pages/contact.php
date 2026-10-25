<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-about.css') ?>" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="contact-hero text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="contact-badge-pill">
                    <i class="fas fa-headset"></i> <?= esc($pageBanner['badge_label'] ?? 'Layanan Bantuan & Helpdesk Pengadaan Barang/Jasa') ?>
                </div>
                <h1 class="contact-hero__title">
                    <?= !empty($pageBanner['title']) ? $pageBanner['title'] : 'Hubungi Biro PBJ & <span>Helpdesk LPSE</span>' ?>
                </h1>
                <p class="contact-hero__subtitle">
                    <?= esc($pageBanner['subtitle'] ?? 'Pemerintah Provinsi Lampung siap memberikan layanan konsultasi teknis, fasilitasi SPSE, pendampingan regulasi, dan informasi pelatihan kompetensi pengadaan barang/jasa.') ?>
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
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div>
                            <h2 class="contact-form-title mb-1">Formulir Layanan &amp; Konsultasi</h2>
                            <p class="contact-form-subtitle mb-0">
                                Sampaikan pertanyaan, kendala teknis SPSE, permohonan bimtek, atau konsultasi pengadaan barang/jasa melalui formulir di bawah ini.
                            </p>
                        </div>
                    </div>

                    <!-- Kotak Pelacakan Tiket (Lacak Status & Jawaban) -->
                    <div class="p-3 rounded-4 bg-light border mb-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <strong class="small text-dark"><i class="fas fa-search text-primary me-1"></i> Lacak Status Tiket Konsultasi</strong>
                            <span class="text-muted text-2xs">Masukkan nomor tiket (contoh: TKT-20261020-XXXXX)</span>
                        </div>
                        <form method="get" action="<?= site_url('kontak#lacak-tiket') ?>" id="lacak-tiket" class="row g-2 align-items-center">
                            <div class="col-sm-9">
                                <input type="text" name="lacak" class="form-control form-control-sm rounded-3" placeholder="Nomor Tiket: TKT-20261020-..." value="<?= esc($trackCode ?? '') ?>" required>
                            </div>
                            <div class="col-sm-3">
                                <button type="submit" class="btn btn-primary btn-sm w-100 rounded-3 fw-bold">
                                    <i class="fas fa-search me-1"></i> Cek Status
                                </button>
                            </div>
                        </form>

                        <?php if (! empty($trackCode) && ! empty($trackedTicket)): ?>
                            <div class="mt-3 p-3 bg-white rounded-3 border">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2 pb-2 border-bottom">
                                    <div>
                                        <span class="badge bg-primary-subtle text-primary border font-monospace">#<?= esc($trackedTicket['ticket_number']) ?></span>
                                        <strong class="text-dark small ms-1"><?= esc($trackedTicket['subject']) ?></strong>
                                    </div>
                                    <?php
                                    $tStatusClass = match ($trackedTicket['status']) {
                                        'unread'        => 'bg-danger-subtle text-danger border border-danger-subtle',
                                        'waiting_admin' => 'bg-info-subtle text-info border border-info-subtle',
                                        'in_progress'   => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                        'waiting_user'  => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                        'read'          => 'bg-light text-secondary border',
                                        'replied'       => 'bg-success-subtle text-success border border-success-subtle',
                                        'closed'        => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                        default         => 'bg-light text-dark border',
                                    };
                                    $tStatusText = match ($trackedTicket['status']) {
                                        'unread'        => 'Menunggu Ditelaah',
                                        'waiting_admin' => 'Tanggapan Masuk',
                                        'in_progress'   => 'Sedang Ditelaah',
                                        'waiting_user'  => 'Menunggu Konfirmasi',
                                        'read'          => 'Sudah Dibaca Petugas',
                                        'replied'       => 'Sudah Dibalas',
                                        'closed'        => 'Selesai &amp; Ditutup',
                                        default         => ucfirst($trackedTicket['status']),
                                    };
                                    ?>
                                    <span class="badge <?= $tStatusClass ?> px-2 py-1 small"><?= $tStatusText ?></span>
                                </div>
                                <div class="small text-muted mb-2">
                                    Diajukan: <strong><?= date('d M Y, H:i', strtotime($trackedTicket['created_at'])) ?> WIB</strong> &middot; Kategori: <strong><?= esc($trackedTicket['service_category'] ?: 'Umum') ?></strong>
                                </div>
                                <div class="p-2 bg-light rounded-2 text-dark small mb-3" style="white-space: pre-wrap;"><?= esc($trackedTicket['message']) ?></div>

                                <?php $trackedAtts = $trackedTicket['parsed_notes']['attachments'] ?? []; ?>
                                <?php if (! empty($trackedAtts)): ?>
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <?php foreach ($trackedAtts as $tAtt): ?>
                                            <a href="<?= esc($tAtt['url'] ?? '#') ?>" target="_blank" rel="noopener noreferrer" class="badge bg-light text-primary border text-2xs p-2 d-inline-flex align-items-center gap-1 text-decoration-none">
                                                <i class="fas fa-paperclip"></i>
                                                <span><?= esc($tAtt['name'] ?? 'Lampiran') ?></span>
                                                <?php if (! empty($tAtt['size_human'])): ?>
                                                    <small class="text-muted">(<?= esc($tAtt['size_human']) ?>)</small>
                                                <?php endif; ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (! empty($trackedTicket['public_threads'])): ?>
                                    <h6 class="fw-bold text-dark small mb-2"><i class="fas fa-comments text-success me-1"></i> Jawaban &amp; Percakapan Helpdesk:</h6>
                                    <div class="d-flex flex-column gap-2 mb-2">
                                        <?php foreach ($trackedTicket['public_threads'] as $pth): ?>
                                            <div class="p-2 rounded-2 <?= ($pth['sender_type'] ?? '') === 'administrator' ? 'bg-primary-subtle border border-primary-subtle' : 'bg-light border' ?> small">
                                                <div class="d-flex justify-content-between mb-1 text-2xs text-muted">
                                                    <strong><?= esc($pth['sender_name'] ?? 'Petugas') ?></strong>
                                                    <span><?= date('d M Y H:i', strtotime($pth['created_at'])) ?> WIB</span>
                                                </div>
                                                <div class="text-dark" style="white-space: pre-wrap;"><?= esc($pth['message']) ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning py-2 px-3 rounded-2 text-2xs mb-0">
                                        <i class="fas fa-clock me-1"></i> Belum ada balasan dari petugas. Tiket Anda sedang dalam antrean telaah Helpdesk Biro PBJ.
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php elseif (! empty($trackCode)): ?>
                            <div class="alert alert-warning py-2 px-3 rounded-3 small mt-3 mb-0">
                                <i class="fas fa-exclamation-circle me-1"></i> Tiket dengan nomor <strong><?= esc($trackCode) ?></strong> tidak ditemukan. Pastikan nomor tiket yang Anda masukkan sudah sesuai.
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (session()->get('isLoggedIn') === true): ?>
                        <div class="alert alert-primary d-flex align-items-center justify-content-between p-3 rounded-3 mb-4">
                            <div class="small">
                                <i class="fas fa-user-check text-primary me-1"></i> Anda login sebagai <strong><?= esc(session()->get('name') ?: session()->get('username')) ?></strong>. Seluruh pesan Anda akan tersimpan di <strong>Ruang Kerja (Workspace)</strong> dengan fitur percakapan dua arah saling membalas.
                            </div>
                            <a href="<?= route_to('workspace.consultation.index') ?>" class="btn btn-sm btn-primary ms-3 text-nowrap fw-bold">
                                <i class="fas fa-comments me-1"></i> Buka Tiket Saya
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="p-3 bg-light rounded-3 border mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="small text-muted">
                                <span class="badge bg-info text-white me-1">Info</span> Formulir terbuka untuk umum. Ingin percakapan langsung dua arah dengan petugas Helpdesk?
                            </div>
                            <a href="<?= route_to('dashboard.auth.login') ?>" class="btn btn-sm btn-outline-primary fw-bold text-nowrap">
                                <i class="fas fa-sign-in-alt me-1"></i> Masuk / Login
                            </a>
                        </div>
                    <?php endif; ?>

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

                    <form action="<?= site_url('kontak') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="text" name="website_url" style="display:none !important; position:absolute; left:-9999px;" tabindex="-1" autocomplete="off" aria-hidden="true">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="contact-label">Nama Lengkap *</label>
                                <input type="text" id="name" name="name" class="contact-input" placeholder="Contoh: Budi Santoso, S.T." value="<?= old('name', session()->get('name') ?: session()->get('username') ?: '') ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="contact-label">Alamat Email *</label>
                                <input type="email" id="email" name="email" class="contact-input" placeholder="contoh@lampungprov.go.id" value="<?= old('email', session()->get('email') ?: '') ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="contact-label">Nomor WhatsApp / Telepon</label>
                                <input type="text" id="phone" name="phone" class="contact-input" placeholder="Contoh: 081234567890" value="<?= old('phone', session()->get('phone') ?: '') ?>">
                            </div>

                            <div class="col-md-6">
                                <label for="organization" class="contact-label">Instansi / OPD / Nama Perusahaan</label>
                                <input type="text" id="organization" name="organization" class="contact-input" placeholder="Contoh: Dinas Bina Marga Provinsi Lampung" value="<?= old('organization') ?>">
                            </div>

                            <div class="col-12">
                                <label for="service_type" class="contact-label">Kategori Layanan *</label>
                                <select id="service_type" name="service_type" class="contact-input ignore form-select" required>
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

                            <div class="col-12">
                                <label for="attachment" class="contact-label">Lampiran Berkas Pendukung (Opsional)</label>
                                <input type="file" id="attachment" name="attachment" class="contact-input form-control form-control-sm" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.webp,.zip,.rar">
                                <small class="text-muted text-xs d-block mt-1">
                                    <i class="fas fa-paperclip text-primary me-1"></i> Format berkas: PDF, Word, Excel, Gambar (JPG/PNG), atau ZIP (Maksimal 1 MB).
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label for="captcha_answer" class="contact-label">
                                    Verifikasi Keamanan (Captcha) *: <span class="badge bg-primary text-white ms-1 font-monospace px-2 py-1"><?= esc($captchaQuestion ?? '4 + 5') ?> = ?</span>
                                </label>
                                <input type="number" id="captcha_answer" name="captcha_answer" class="contact-input" placeholder="Tulis hasil penjumlahan..." required>
                                <small class="text-muted text-2xs d-block mt-1">Hitung angka penjumlahan di atas untuk memverifikasi Anda bukan bot spam.</small>
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