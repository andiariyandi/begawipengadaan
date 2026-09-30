<?= $this->extend('landing/layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-header">
    <div class="page-header__bg" style="background-image: url(<?= base_url('assets/main/images/backgrounds/page-header-bg.jpg') ?>);"></div>
    <div class="container">
        <div class="page-header__inner">
            <h2>Tentang BegawiPBJ</h2>
            <ul class="thm-breadcrumb list-unstyled">
                <li><a href="<?= route_to('landing.home') ?>">Beranda</a></li>
                <li><span class="icon-angle-right"></span></li>
                <li>Tentang</li>
            </ul>
        </div>
    </div>
</section>

<section class="about-one about-page">
    <div class="container">
        <div class="row">
            <div class="col-xl-6">
                <div class="about-one__left">
                    <div class="about-one__img-box">
                        <div class="about-one__img"><img src="<?= base_url('assets/main/images/resources/abou-one-img-1.jpg') ?>" alt="BegawiPBJ"></div>
                        <div class="about-one__img-2"><img src="<?= base_url('assets/main/images/resources/abou-one-img-2.jpg') ?>" alt="Kolaborasi PBJ"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="about-one__right">
                    <div class="section-title text-left">
                        <div class="section-title__tagline-box"><span class="section-title__tagline">Tentang Kami</span></div>
                        <h2 class="section-title__title">Ekosistem Digital PBJ Pemerintah Provinsi Lampung</h2>
                    </div>
                    <p class="about-one__text">BegawiPBJ adalah platform digital terpadu untuk mendukung ekosistem Pengadaan Barang/Jasa Pemerintah di lingkungan Pemerintah Provinsi Lampung.</p>
                    <p class="about-one__text">Platform ini menyatukan pembelajaran digital, pusat pengetahuan PBJ, repository regulasi, berita dan event, sertifikat digital, statistik publik, serta layanan pendukung dalam satu aplikasi yang aman, terstruktur, dapat diaudit, dan berbasis peran.</p>
                    <ul class="list-unstyled about-one__points">
                        <li>
                            <div class="icon"><span class="icon-check"></span></div>
                            <div class="text">
                                <p>Meningkatkan kompetensi SDM dan pemangku kepentingan PBJ</p>
                            </div>
                        </li>
                        <li>
                            <div class="icon"><span class="icon-check"></span></div>
                            <div class="text">
                                <p>Menyediakan pengetahuan, regulasi, dan informasi kegiatan yang mudah diakses</p>
                            </div>
                        </li>
                        <li>
                            <div class="icon"><span class="icon-check"></span></div>
                            <div class="text">
                                <p>Mendukung tata kelola digital yang aman dan akuntabel</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="why-choose-one">
    <div class="why-choose-one__bg" style="background-image: url(<?= base_url('assets/main/images/backgrounds/why-choose-one-bg.jpg') ?>);"></div>
    <div class="container">
        <div class="row">
            <div class="col-xl-6">
                <div class="why-choose-one__left">
                    <div class="section-title text-left">
                        <div class="section-title__tagline-box"><span class="section-title__tagline">Nilai Utama</span></div>
                        <h2 class="section-title__title">Dirancang untuk pembelajaran dan tata kelola PBJ</h2>
                    </div>
                    <p class="why-choose-one__text">BegawiPBJ memisahkan layanan publik, workspace berbasis peran, dan dashboard back-office untuk menjaga pengalaman pengguna sekaligus keamanan akses data.</p>
                    <ul class="list-unstyled why-choose-one__points">
                        <li>
                            <div class="icon"><span class="icon-check"></span></div>
                            <div class="text">
                                <h4>Terstruktur</h4>
                                <p>Konten, regulasi, dan layanan dikelola dalam domain yang jelas.</p>
                            </div>
                        </li>
                        <li>
                            <div class="icon"><span class="icon-check"></span></div>
                            <div class="text">
                                <h4>Aman</h4>
                                <p>Akses ditentukan oleh autentikasi, status akun, role, permission, dan konteks sumber daya.</p>
                            </div>
                        </li>
                        <li>
                            <div class="icon"><span class="icon-check"></span></div>
                            <div class="text">
                                <h4>Akuntabel</h4>
                                <p>Aktivitas penting dapat ditelusuri melalui workflow dan audit trail.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>