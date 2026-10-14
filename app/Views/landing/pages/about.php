<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<style>
    html {
        scroll-behavior: smooth;
    }
    #visi-misi, #pilar, #struktur, #maklumat {
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

    /* Hero Banner - 215px padding top gives 55px+ safe clearance below 160px absolute menu bar */
    .about-hero {
        position: relative;
        background: linear-gradient(135deg, #052244 0%, #0a3a6b 55%, #0c5aa6 100%);
        padding: 215px 0 90px;
        color: #ffffff;
        overflow: hidden;
    }
    .about-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 85% 15%, rgba(245, 158, 11, 0.18) 0%, transparent 45%),
                    radial-gradient(circle at 10% 85%, rgba(16, 185, 129, 0.14) 0%, transparent 50%);
        pointer-events: none;
    }
    .about-badge-pill {
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
    .about-hero__title {
        font-size: 2.85rem;
        font-weight: 800;
        line-height: 1.25;
        color: #ffffff;
        margin-bottom: 18px;
        letter-spacing: -0.5px;
    }
    .about-hero__title span {
        color: #f59e0b;
    }
    .about-hero__subtitle {
        font-size: 1.15rem;
        color: #e2e8f0;
        line-height: 1.75;
        max-width: 820px;
        margin: 0 auto;
    }

    /* Section Global */
    .abt-section {
        padding: 75px 0;
    }
    .abt-section--alt {
        background-color: #f8fafc;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
    }
    .abt-sec-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #0c5aa6;
        font-size: 0.885rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 10px;
    }
    .abt-sec-title {
        font-size: 2.25rem;
        font-weight: 800;
        color: #052244;
        line-height: 1.3;
        margin-bottom: 16px;
    }
    .abt-sec-title span {
        color: #0c5aa6;
    }
    .abt-sec-desc {
        color: #334155;
        font-size: 1.05rem;
        line-height: 1.75;
    }

    /* Begawi Philosophy Card */
    .abt-philo-card {
        background: linear-gradient(135deg, #052244 0%, #0c5aa6 100%);
        border-radius: 20px;
        padding: 45px 40px;
        color: #ffffff;
        box-shadow: 0 20px 45px rgba(5, 34, 68, 0.18);
        position: relative;
        overflow: hidden;
    }
    .abt-philo-card::after {
        content: '\f02d';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: 25px;
        bottom: -20px;
        font-size: 140px;
        color: rgba(255, 255, 255, 0.05);
        pointer-events: none;
    }
    .abt-philo-quote {
        font-size: 1.35rem;
        font-weight: 700;
        color: #fde68a;
        line-height: 1.5;
        margin-bottom: 18px;
        font-style: italic;
    }

    /* Visi Misi Cards */
    .abt-vm-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 32px;
        border: 1px solid #e2e8f0;
        height: 100%;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
    }
    .abt-vm-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(5, 34, 68, 0.08);
        border-color: #cbd5e1;
    }
    .abt-vm-icon {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        margin-bottom: 20px;
    }
    .abt-vm-icon--gold { background: rgba(245, 158, 11, 0.15); color: #d97706; }
    .abt-vm-icon--blue { background: rgba(12, 90, 166, 0.15); color: #0c5aa6; }
    .abt-vm-list {
        padding-left: 20px;
        margin-bottom: 0;
    }
    .abt-vm-list li {
        color: #334155;
        font-size: 0.95rem;
        line-height: 1.65;
        margin-bottom: 12px;
    }
    .abt-vm-list li:last-child {
        margin-bottom: 0;
    }

    /* 5 Pillars Grid */
    .abt-pillar-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 28px;
        border: 1px solid #e2e8f0;
        height: 100%;
        transition: all 0.3s ease;
        position: relative;
    }
    .abt-pillar-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(5, 34, 68, 0.08);
        border-color: #0c5aa6;
    }
    .abt-pillar-num {
        position: absolute;
        right: 20px;
        top: 15px;
        font-size: 2rem;
        font-weight: 900;
        color: #e2e8f0;
    }
    .abt-pillar-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 18px;
    }
    .abt-pillar-card h4 {
        font-size: 1.15rem;
        font-weight: 700;
        color: #052244;
        margin-bottom: 10px;
    }
    .abt-pillar-card p {
        font-size: 0.9rem;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 16px;
    }

    /* Budaya PATIK */
    .abt-patik-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: flex-start;
        gap: 18px;
        transition: all 0.3s ease;
    }
    .abt-patik-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(5, 34, 68, 0.08);
        border-color: #0c5aa6;
    }
    .abt-patik-letter {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        background: linear-gradient(135deg, #052244 0%, #0c5aa6 100%);
        color: #f59e0b;
        font-size: 26px;
        font-weight: 900;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(12, 90, 166, 0.25);
    }
    .abt-patik-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
    }
    .abt-patik-desc {
        font-size: 0.875rem;
        color: #475569;
        line-height: 1.6;
        margin: 0;
    }

    /* Struktur Organisasi Tree */
    .abt-org-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 36px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }
    .abt-org-lead {
        background: linear-gradient(135deg, #052244 0%, #0a3a6b 100%);
        color: #ffffff;
        padding: 24px;
        border-radius: 14px;
        text-align: center;
        margin-bottom: 30px;
        box-shadow: 0 8px 24px rgba(5, 34, 68, 0.15);
    }
    .abt-org-lead h4 {
        font-size: 1.25rem;
        font-weight: 800;
        color: #ffffff;
        margin-bottom: 4px;
    }
    .abt-org-lead p {
        color: #fcd34d;
        font-size: 0.95rem;
        font-weight: 600;
        margin: 0;
    }
    .abt-org-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        height: 100%;
        transition: all 0.25s ease;
    }
    .abt-org-item:hover {
        background: #ffffff;
        border-color: #0c5aa6;
        box-shadow: 0 8px 20px rgba(12, 90, 166, 0.08);
    }
    .abt-org-item h5 {
        font-size: 1.05rem;
        font-weight: 700;
        color: #052244;
        margin-bottom: 8px;
    }
    .abt-org-item p {
        font-size: 0.85rem;
        color: #64748b;
        line-height: 1.5;
        margin: 0;
    }

    /* Maklumat Pelayanan */
    .abt-maklumat-card {
        background: #ffffff;
        border: 2px solid #f59e0b;
        border-radius: 18px;
        padding: 36px;
        position: relative;
    }
    .abt-maklumat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #f59e0b 0%, #0c5aa6 100%);
        border-radius: 18px 18px 0 0;
    }

    /* KPI Stats Bar */
    .abt-kpi-bar {
        background: #052244;
        color: #ffffff;
        border-radius: 18px;
        padding: 36px 24px;
        margin-top: 50px;
    }
    .abt-kpi-val {
        font-size: 2.25rem;
        font-weight: 900;
        color: #f59e0b;
        margin-bottom: 4px;
    }
    .abt-kpi-lbl {
        font-size: 0.875rem;
        color: #cbd5e1;
        font-weight: 600;
        margin: 0;
    }

    @media (max-width: 991px) {
        .about-hero { padding: 185px 0 75px; }
        .about-hero__title { font-size: 2.35rem; }
    }

    @media (max-width: 768px) {
        .about-hero { padding: 165px 0 65px; }
        .about-hero__title { font-size: 2rem; }
        .abt-sec-title { font-size: 1.75rem; }
        .abt-philo-card { padding: 28px 20px; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="about-hero text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="about-badge-pill">
                    <i class="fas fa-landmark"></i> Biro Pengadaan Barang dan Jasa Setda Provinsi Lampung
                </div>
                <h1 class="about-hero__title">
                    Membangun Ekosistem Pengadaan Digital <span>Berintegritas &amp; Berdampak</span>
                </h1>
                <p class="about-hero__subtitle">
                    BegawiPBJ merupakan pusat inovasi, pembelajaran digital, regulasi hukum, dan intelijen pengadaan terpadu yang dirancang untuk mewujudkan tata kelola Pengadaan Barang/Jasa Pemerintah Provinsi Lampung yang transparan, profesional, dan akuntabel.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Filosofi & Sekilas BegawiPBJ -->
<section class="abt-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="abt-sec-tag"><i class="fas fa-feather-alt me-1"></i> Filosofi Daerah</span>
                <h2 class="abt-sec-title">
                    Makna <span>"Begawi"</span> dalam Pengadaan Modern
                </h2>
                <p class="abt-sec-desc mb-4">
                    Kata <strong>"Begawi"</strong> berakar dari kearifan bahasa dan adat budaya Lampung yang mencerminkan etos kerja luhur, bergotong-royong, dan mengabdi dengan penuh kesungguhan serta kehormatan.
                </p>
                <p class="abt-sec-desc mb-4">
                    Dalam konteks pengadaan barang dan jasa modern, BegawiPBJ hadir mengintegrasikan semangat pengabdian tersebut dengan teknologi digital tingkat lanjut. Tujuannya adalah menjadikan seluruh proses pengadaan di Provinsi Lampung tidak sekadar taat administratif, melainkan memberikan nilai manfaat nyata bagi kemakmuran masyarakat Bumi Ruwa Jurai.
                </p>
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
                        <img src="<?= base_url('assets/main/images/resources/logo-4.png') ?>" alt="BegawiPBJ" height="50">
                        <div>
                            <h4 class="text-white fw-bold mb-0">UKPBJ Level Proaktif</h4>
                            <span class="badge bg-warning text-dark fw-bold">Pemerintah Provinsi Lampung</span>
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
                        <em>"Terwujudnya Tata Kelola Pengadaan Barang dan Jasa Pemerintah Provinsi Lampung yang Profesional, Transparan, Akuntabel, dan Menjadi Penggerak Utama Pertumbuhan Ekonomi Lokal yang Inklusif."</em>
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
                    <ul class="abt-vm-list">
                        <li>
                            <strong>Peningkatan Kompetensi Berkelanjutan:</strong> Mengembangkan kapasitas SDM PBJ melalui Arena Belajar digital, bimtek teknis, dan sertifikasi terstandarisasi.
                        </li>
                        <li>
                            <strong>Penguatan Tata Kelola &amp; Advokasi:</strong> Memberikan layanan pembinaan, pendampingan hukum, dan mitigasi risiko pengadaan bagi seluruh OPD.
                        </li>
                        <li>
                            <strong>Akselerasi E-Purchasing &amp; UMKM:</strong> Mengoptimalkan E-Katalog Lokal Lampung dan Toko Daring guna menyerap produk dalam negeri (P3DN).
                        </li>
                        <li>
                            <strong>Pusat Literasi &amp; Regulasi Terbuka:</strong> Menjamin kemudahan akses regulasi, standar dokumen, dan telaah hukum melalui Bunga Rampai.
                        </li>
                        <li>
                            <strong>Government Intelligence PBJ:</strong> Mengintegrasikan data pengadaan untuk pelaporan analitik, monitoring kinerja, dan pengambilan keputusan pimpinan.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 5 Pilar Ekosistem BegawiPBJ -->
<section class="abt-section" id="pilar">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="abt-sec-tag"><i class="fas fa-cubes me-1"></i> Arsitektur Layanan</span>
            <h2 class="abt-sec-title">5 Pilar Ekosistem <span>BegawiPBJ</span></h2>
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
                        <em>"Dengan ini, kami pimpinan dan seluruh aparatur Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung menyatakan sanggup menyelenggarakan pelayanan pengadaan barang dan jasa sesuai standar pelayanan yang telah ditetapkan, memberikan pelayanan yang profesional, transparan, akuntabel, bebas dari gratifikasi dan korupsi, serta siap menerima sanksi sesuai peraturan perundang-undangan apabila tidak menepati janji ini."</em>
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
                    requestAnimationFrame(function() { isClamping = false; });
                }
            }
        }, { passive: true });
    })();
</script>
<?= $this->endSection() ?>