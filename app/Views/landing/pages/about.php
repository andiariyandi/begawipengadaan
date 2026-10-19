<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-about.css') ?>" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="about-hero text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="about-badge-pill">
                    <i class="fas fa-landmark"></i> <?= esc($pageBanner['badge_label'] ?? 'Biro Pengadaan Barang dan Jasa Setda Provinsi Lampung') ?>
                </div>
                <h1 class="about-hero__title">
                    <?= !empty($pageBanner['title']) ? $pageBanner['title'] : 'Membangun Ekosistem Pengadaan Digital <span>Berintegritas &amp; Berdampak</span>' ?>
                </h1>
                <p class="about-hero__subtitle">
                    <?= esc($pageBanner['subtitle'] ?? 'BegawiPengadaan merupakan pusat inovasi, pembelajaran digital, regulasi hukum, dan intelijen pengadaan terpadu yang dirancang untuk mewujudkan tata kelola Pengadaan Barang/Jasa Pemerintah Provinsi Lampung yang transparan, profesional, dan akuntabel.') ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Filosofi & Sekilas BegawiPengadaan -->
<section class="abt-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="abt-sec-tag"><i class="fas fa-feather-alt me-1"></i> Filosofi Daerah</span>
                <h2 class="abt-sec-title">
                    Makna <span>"Begawi"</span> dalam Pengadaan Modern
                </h2>
                <div class="abt-sec-desc mb-4">
                    <?= nl2br(esc(setting('organization.philosophy', "Kata \"Begawi\" berakar dari kearifan bahasa dan adat budaya Lampung yang mencerminkan etos kerja luhur, bergotong-royong, dan mengabdi dengan penuh kesungguhan serta kehormatan.\n\nDalam konteks pengadaan barang dan jasa modern, BegawiPengadaan hadir mengintegrasikan semangat pengabdian tersebut dengan teknologi digital tingkat lanjut. Tujuannya adalah menjadikan seluruh proses pengadaan di Provinsi Lampung tidak sekadar taat administratif, melainkan memberikan nilai manfaat nyata bagi kemakmuran masyarakat Bumi Ruwa Jurai."))) ?>
                </div>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?= site_url('arena-belajar') ?>" class="thm-btn">
                        <i class="fas fa-graduation-cap me-1"></i> Jelajahi Arena Belajar
                    </a>
                    <a href="<?= site_url('regulasi') ?>" class="btn btn-outline-secondary px-4 py-3 rounded-3 fw-bold">
                        <i class="fas fa-balance-scale me-1"></i> Repositori Regulasi
                    </a>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="abt-philo-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <?php
                        $abtLogo = setting('organization.logo_url');
                        $abtLogoSrc = ! empty($abtLogo) ? (str_starts_with($abtLogo, 'http') ? $abtLogo : base_url($abtLogo)) : base_url('assets/main/images/resources/logo-4.png');
                        ?>
                        <img src="<?= esc($abtLogoSrc) ?>" alt="BegawiPengadaan" height="50" style="object-fit: contain;">
                        <div>
                            <h4 class="text-white fw-bold mb-0"><?= esc(setting('organization.institution_level', 'UKPBJ Level Proaktif')) ?></h4>
                            <span class="badge bg-warning text-dark fw-bold"><?= esc(setting('organization.provincial_unit', 'Pemerintah Provinsi Lampung')) ?></span>
                        </div>
                    </div>
                    <p class="abt-philo-quote">
                        "Pengadaan Barang dan Jasa bukan sekadar membelanjakan anggaran negara, melainkan instrumen strategis pembangunan yang menggerakkan ekonomi lokal dan mensejahterakan rakyat."
                    </p>
                    <hr class="border-light opacity-25 my-4">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="h5 fw-bold text-warning mb-1">Level Proaktif</div>
                            <small class="text-light opacity-75">Tingkat Kematangan UKPBJ</small>
                        </div>
                        <div class="col-6">
                            <div class="h5 fw-bold text-success mb-1">Zona Integritas</div>
                            <small class="text-light opacity-75">Bebas Korupsi &amp; Pungli</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Visi dan Misi -->
<section class="abt-section abt-section--alt" id="visi-misi">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="abt-sec-tag"><i class="fas fa-compass me-1"></i> Haluan Strategis</span>
            <h2 class="abt-sec-title">Visi &amp; Misi <span>2024–2029</span></h2>
            <p class="abt-sec-desc">
                Arah kebijakan dan target kinerja Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung dalam mewujudkan ekosistem PBJ yang unggul dan berdaya saing.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="abt-vm-card">
                    <div class="abt-vm-icon abt-vm-icon--gold">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3 class="fw-bold text-dark h4 mb-3">Visi Utama</h3>
                    <p class="text-muted leading-relaxed" style="font-size: 1.05rem;">
                        <em>"<?= esc(setting('organization.vision', 'Terwujudnya Tata Kelola Pengadaan Barang dan Jasa Pemerintah Provinsi Lampung yang Profesional, Transparan, Akuntabel, dan Menjadi Penggerak Utama Pertumbuhan Ekonomi Lokal yang Inklusif.')) ?>"</em>
                    </p>
                    <div class="mt-4 pt-3 border-top">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fw-bold">
                            <i class="fas fa-bullseye me-1"></i> Fokus: Kematangan Proaktif &amp; E-Katalog Lokal
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="abt-vm-card">
                    <div class="abt-vm-icon abt-vm-icon--blue">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3 class="fw-bold text-dark h4 mb-3">5 Misi Strategis</h3>
                    <?php
                    $missionItems = setting('vision_mission.mission_items');
                    $missions = [];
                    if (!empty($missionItems)) {
                        $decoded = json_decode($missionItems, true);
                        if (is_array($decoded)) {
                            $missions = $decoded;
                        } else {
                            $missions = array_filter(array_map('trim', explode("\n", $missionItems)));
                        }
                    }
                    if (empty($missions)) {
                        $missionRaw = setting('organization.mission', '');
                        if (!empty($missionRaw)) {
                            $missions = array_filter(array_map('trim', explode("\n", $missionRaw)));
                        }
                    }
                    if (empty($missions)) {
                        $missions = [
                            'Peningkatan Kompetensi Berkelanjutan: Mengembangkan kapasitas SDM PBJ melalui Arena Belajar digital, bimtek teknis, dan sertifikasi terstandarisasi.',
                            'Penguatan Tata Kelola & Advokasi: Memberikan layanan pembinaan, pendampingan hukum, dan mitigasi risiko pengadaan bagi seluruh OPD.',
                            'Akselerasi E-Purchasing & UMKM: Mengoptimalkan E-Katalog Lokal Lampung dan Toko Daring guna menyerap produk dalam negeri (P3DN).',
                            'Pusat Literasi & Regulasi Terbuka: Menjamin kemudahan akses regulasi, standar dokumen, dan telaah hukum melalui Bunga Rampai.',
                            'Government Intelligence PBJ: Mengintegrasikan data pengadaan untuk pelaporan analitik, monitoring kinerja, dan pengambilan keputusan pimpinan.',
                        ];
                    }
                    ?>
                    <ul class="abt-vm-list">
                        <?php foreach ($missions as $m): ?>
                            <?php
                            $parts = explode(':', $m, 2);
                            if (count($parts) === 2): ?>
                                <li><strong><?= esc(trim($parts[0])) ?>:</strong> <?= esc(trim($parts[1])) ?></li>
                            <?php else: ?>
                                <li><?= esc($m) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 5 Pilar Ekosistem BegawiPengadaan -->
<section class="abt-section" id="pilar">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="abt-sec-tag"><i class="fas fa-cubes me-1"></i> Arsitektur Layanan</span>
            <h2 class="abt-sec-title">5 Pilar Ekosistem <span>BegawiPengadaan</span></h2>
            <p class="abt-sec-desc">
                Inovasi terpadu yang menghubungkan seluruh pilar kebutuhan aparatur pemerintah, penyedia barang/jasa, dan publik.
            </p>
        </div>

        <div class="row g-4">
            <!-- Pilar 1 -->
            <div class="col-lg-4 col-md-6">
                <div class="abt-pillar-card">
                    <span class="abt-pillar-num">01</span>
                    <div class="abt-pillar-icon bg-primary text-white">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h4>Arena Belajar LMS</h4>
                    <p>
                        Pusat pelatihan digital dengan kurikulum berjenjang, modul interaktif, tugas mandiri, kuis asesmen, dan sertifikat kelulusan resmi.
                    </p>
                    <a href="<?= site_url('arena-belajar') ?>" class="text-primary fw-bold text-decoration-none small">
                        Lihat Program Pelatihan <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- Pilar 2 -->
            <div class="col-lg-4 col-md-6">
                <div class="abt-pillar-card">
                    <span class="abt-pillar-num">02</span>
                    <div class="abt-pillar-icon bg-warning text-dark">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h4>Bunga Rampai PBJ</h4>
                    <p>
                        Knowledge hub terlengkap berisi artikel telaah, studi kasus lapangan, template dokumen kerja, video edukasi, dan tanya jawab (FAQ).
                    </p>
                    <a href="<?= site_url('bunga-rampai') ?>" class="text-primary fw-bold text-decoration-none small">
                        Akses Pusat Pengetahuan <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- Pilar 3 -->
            <div class="col-lg-4 col-md-6">
                <div class="abt-pillar-card">
                    <span class="abt-pillar-num">03</span>
                    <div class="abt-pillar-icon bg-info text-white">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <h4>Pusat Regulasi PBJ</h4>
                    <p>
                        Repositori hukum terstruktur mencakup Perpres, PerLKPP, Perda, Pergub, hingga Standar Dokumen Pemilihan (SDP) dengan fitur riwayat versi.
                    </p>
                    <a href="<?= site_url('regulasi') ?>" class="text-primary fw-bold text-decoration-none small">
                        Cari Regulasi PBJ <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- Pilar 4 -->
            <div class="col-lg-6 col-md-6">
                <div class="abt-pillar-card">
                    <span class="abt-pillar-num">04</span>
                    <div class="abt-pillar-icon bg-success text-white">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h4>Verifikasi Sertifikat Digital</h4>
                    <p>
                        Layanan pembuktian keabsahan sertifikat kelulusan berbasis token kriptografi SHA-256 yang aman, terintegrasi QR code, dan anti-pemalsuan.
                    </p>
                    <a href="<?= site_url('verifikasi-sertifikat') ?>" class="text-primary fw-bold text-decoration-none small">
                        Uji Keabsahan Sertifikat <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- Pilar 5 -->
            <div class="col-lg-6 col-md-12">
                <div class="abt-pillar-card">
                    <span class="abt-pillar-num">05</span>
                    <div class="abt-pillar-icon bg-purple text-white" style="background:#7c3aed;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4>Government Intelligence PBJ</h4>
                    <p>
                        Pemantauan analitik pengadaan, statistik kompetensi aparatur pemerintah daerah, dan data strategis pengadaan barang/jasa secara real-time.
                    </p>
                    <a href="<?= site_url('statistik') ?>" class="text-primary fw-bold text-decoration-none small">
                        Lihat Statistik Publik <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Budaya Kerja PATIK -->
<section class="abt-section abt-section--alt">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-8">
                <span class="abt-sec-tag"><i class="fas fa-award me-1"></i> Nilai Kerja Utama</span>
                <h2 class="abt-sec-title">Budaya Kerja <span>P-A-T-I-K</span></h2>
                <p class="abt-sec-desc mb-0">
                    Fondasi integritas aparatur Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung dalam melayani dengan dedikasi prima.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <span class="badge bg-warning text-dark px-3 py-2 fw-bold font-monospace" style="font-size: 1rem;">
                    #BegawiPATIKLampung
                </span>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-4 col-md-6">
                <div class="abt-patik-card">
                    <div class="abt-patik-letter">P</div>
                    <div>
                        <h4 class="abt-patik-title">Profesional</h4>
                        <p class="abt-patik-desc">Bekerja berlandaskan keahlian teknis, standar kompetensi tinggi, dan kepatuhan penuh terhadap SOP pengadaan.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="abt-patik-card">
                    <div class="abt-patik-letter">A</div>
                    <div>
                        <h4 class="abt-patik-title">Akuntabel</h4>
                        <p class="abt-patik-desc">Setiap tahapan proses dan keputusan dapat dipertanggungjawabkan secara hukum, etika, dan keterbukaan informasi publik.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="abt-patik-card">
                    <div class="abt-patik-letter">T</div>
                    <div>
                        <h4 class="abt-patik-title">Transparan</h4>
                        <p class="abt-patik-desc">Memberikan akses informasi yang jelas, terbuka, adil, dan tidak diskriminatif bagi seluruh penyedia dan masyarakat.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6">
                <div class="abt-patik-card">
                    <div class="abt-patik-letter">I</div>
                    <div>
                        <h4 class="abt-patik-title">Integritas</h4>
                        <p class="abt-patik-desc">Menolak segala bentuk suap, gratifikasi, dan konflik kepentingan demi menjaga kehormatan pengadaan pemerintah.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12">
                <div class="abt-patik-card">
                    <div class="abt-patik-letter">K</div>
                    <div>
                        <h4 class="abt-patik-title">Kolaboratif</h4>
                        <p class="abt-patik-desc">Membangun sinergi aktif antara perangkat daerah, instansi pembina LKPP, penyedia swasta, akademisi, dan publik.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Struktur Organisasi & Layanan Terpadu -->
<section class="abt-section" id="struktur">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="abt-sec-tag"><i class="fas fa-sitemap me-1"></i> Tata Kelola Organisasi</span>
            <h2 class="abt-sec-title">Struktur Organisasi <span>Biro PBJ</span></h2>
            <p class="abt-sec-desc">
                Susunan tugas dan fungsi Biro Pengadaan Barang dan Jasa Sekretariat Daerah Pemerintah Provinsi Lampung.
            </p>
        </div>

        <div class="abt-org-box">
            <!-- Kepala Biro -->
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="abt-org-lead">
                        <i class="fas fa-user-tie fa-2x mb-2 text-warning"></i>
                        <h4>Kepala Biro Pengadaan Barang dan Jasa</h4>
                        <p>Sekretariat Daerah Provinsi Lampung</p>
                    </div>
                </div>
            </div>

            <!-- Bagian-Bagian -->
            <div class="row g-4 mt-2">
                <div class="col-lg-4">
                    <div class="abt-org-item">
                        <div class="d-flex align-items-center gap-2 mb-2 text-primary">
                            <i class="fas fa-boxes fa-lg"></i>
                            <span class="fw-bold small text-uppercase">Bagian I</span>
                        </div>
                        <h5>Bagian Pengelolaan PBJ</h5>
                        <p>
                            Melaksanakan perencanaan, pemilihan penyedia, pengelolaan kontrak, koordinasi Pokja Pemilihan, dan fasilitasi e-purchasing katalog daerah.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="abt-org-item">
                        <div class="d-flex align-items-center gap-2 mb-2 text-primary">
                            <i class="fas fa-gavel fa-lg"></i>
                            <span class="fw-bold small text-uppercase">Bagian II</span>
                        </div>
                        <h5>Bagian Pembinaan &amp; Advokasi PBJ</h5>
                        <p>
                            Membina SDM pengadaan, mengelola pembelajaran kompetensi, memberikan telaah dan pendampingan hukum, serta mitigasi risiko PBJ.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="abt-org-item">
                        <div class="d-flex align-items-center gap-2 mb-2 text-primary">
                            <i class="fas fa-network-wired fa-lg"></i>
                            <span class="fw-bold small text-uppercase">Bagian III</span>
                        </div>
                        <h5>Bagian Layanan LPSE</h5>
                        <p>
                            Mengelola infrastruktur SPSE, verifikasi data pelaku usaha, keamanan sistem informasi pengadaan, serta helpdesk teknis pengguna.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maklumat Pelayanan -->
        <div class="row justify-content-center mt-5" id="maklumat">
            <div class="col-lg-10">
                <div class="abt-maklumat-card text-center">
                    <div class="badge bg-warning text-dark px-3 py-2 fw-bold text-uppercase mb-3">
                        <i class="fas fa-shield-alt me-1"></i> Maklumat Pelayanan Publik
                    </div>
                    <h3 class="fw-bold text-dark mb-3">Janji Integritas Pelayanan UKPBJ Lampung</h3>
                    <p class="text-muted leading-relaxed mb-4" style="font-size: 1.05rem;">
                        <em><?= esc(setting('organization.service_pledge', '"Dengan ini, kami pimpinan dan seluruh aparatur Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung menyatakan sanggup menyelenggarakan pelayanan pengadaan barang dan jasa sesuai standar pelayanan yang telah ditetapkan, memberikan pelayanan yang profesional, transparan, akuntabel, bebas dari gratifikasi dan korupsi, serta siap menerima sanksi sesuai peraturan perundang-undangan apabila tidak menepati janji ini."')) ?></em>
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-4 text-muted small fw-bold">
                        <span><i class="fas fa-check-circle text-success me-1"></i> Layanan Cepat 1x24 Jam</span>
                        <span><i class="fas fa-hand-holding-usd text-danger me-1"></i> Bebas Biaya (Tanpa Pungli)</span>
                        <span><i class="fas fa-lock text-primary me-1"></i> Perlindungan Kerahasiaan Pemohon</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live KPI Counter Banner -->
        <div class="abt-kpi-bar">
            <div class="row text-center g-4">
                <div class="col-lg-3 col-6">
                    <div class="abt-kpi-val"><?= number_format($stats['total_regulations'] ?? 0) ?></div>
                    <p class="abt-kpi-lbl">Regulasi PBJ Terhimpun</p>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="abt-kpi-val"><?= number_format($stats['total_courses'] ?? 0) ?></div>
                    <p class="abt-kpi-lbl">Course Pembelajaran Aktif</p>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="abt-kpi-val"><?= number_format($stats['total_certificates'] ?? 0) ?></div>
                    <p class="abt-kpi-lbl">Sertifikat Resmi Diterbitkan</p>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="abt-kpi-val"><?= number_format($stats['total_users'] ?? 0) ?></div>
                    <p class="abt-kpi-lbl">Pengguna Terdaftar</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="abt-section abt-section--alt text-center">
    <div class="container">
        <h2 class="abt-sec-title">Butuh Konsultasi atau Pendampingan PBJ?</h2>
        <p class="abt-sec-desc max-w-700 mx-auto mb-4">
            Tim Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung siap membantu konsultasi teknis, bimbingan penggunaan SPSE, dan peningkatan kompetensi pengadaan.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="<?= site_url('kontak') ?>" class="thm-btn">
                <i class="fas fa-headset me-1"></i> Hubungi Helpdesk PBJ
            </a>
            <a href="<?= site_url('verifikasi-sertifikat') ?>" class="btn btn-outline-primary px-4 py-3 rounded-3 fw-bold">
                <i class="fas fa-certificate me-1"></i> Cek Keabsahan Sertifikat
            </a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('assets/dashboard/js/lib/iconify-icon.min.js') ?>"></script>
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