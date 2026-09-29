<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<section class="hero__area hero__height hero__height-2 d-flex align-items-center p-relative" data-background="<?= base_url('assets/main/assets/img/hero/home-2/hero-bg.jpg') ?>">
    <div class="hero__shape">
        <img class="hero-2-dot" src="<?= base_url('assets/main/assets/img/hero/home-2/hero-dot.png') ?>" alt="">
        <img class="hero-2-dot-2" src="<?= base_url('assets/main/assets/img/hero/home-2/hero-dot-2.png') ?>" alt="">
        <img class="hero-2-dot-3" src="<?= base_url('assets/main/assets/img/hero/home-2/hero-dot-3.png') ?>" alt="">
    </div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xxl-7 col-xl-7 col-lg-7">
                <div class="hero__content-2 mt-40">
                    <span class="hero__pre-title">Portal BegawiPBJ</span>
                    <h1 class="hero__title hero__title-2">Belajar dan bertumbuh bersama ekosistem <span>Pengadaan Barang/Jasa</span>.</h1>
                    <p>Jelajahi pembelajaran, pengetahuan, regulasi, event, dan sertifikat digital PBJ dalam satu portal terintegrasi.</p>
                    <div class="hero__btn">
                        <a href="<?= site_url('arena-belajar') ?>" class="e-btn">Jelajahi Arena Belajar</a>
                        <a href="<?= site_url('regulasi') ?>" class="e-btn e-btn-border">Lihat Regulasi</a>
                    </div>
                </div>
            </div>
            <div class="col-xxl-5 col-xl-5 col-lg-5">
                <div class="hero__thumb-wrapper p-relative">
                    <div class="hero__thumb-2 scene">
                        <img class="hero-2-thumb" src="<?= base_url('assets/main/assets/img/hero/home-2/hero-thumb.png') ?>" alt="Ilustrasi pembelajaran BegawiPBJ">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about__area pt-115 pb-120">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xxl-6 col-xl-6 col-lg-6">
                <div class="about__thumb-wrapper p-relative">
                    <div class="about__thumb ml-100"><img src="<?= base_url('assets/main/assets/img/about/about.jpg') ?>" alt="Kegiatan pembelajaran PBJ"></div>
                    <div class="about__banner mt--70"><img src="<?= base_url('assets/main/assets/img/about/about-banner.jpg') ?>" alt="Kolaborasi PBJ"></div>
                </div>
            </div>
            <div class="col-xxl-6 col-xl-6 col-lg-6">
                <div class="about__content pl-70 pr-60 pt-25">
                    <div class="section__title-wrapper mb-25">
                        <span class="section__sub-title">Tentang BegawiPBJ</span>
                        <h2 class="section__title">Satu pintu untuk <span class="yellow-bg">kompetensi</span> dan informasi PBJ.</h2>
                        <p>BegawiPBJ menghadirkan layanan pembelajaran, pengetahuan, regulasi, agenda, serta sertifikat digital bagi ekosistem Pengadaan Barang/Jasa.</p>
                    </div>
                    <div class="about__list mb-40">
                        <ul>
                            <li>Program pembelajaran yang terstruktur</li>
                            <li>Referensi pengetahuan dan regulasi PBJ</li>
                            <li>Informasi event dan sertifikat digital</li>
                        </ul>
                    </div>
                    <a href="<?= site_url('tentang') ?>" class="e-btn e-btn-border">Selengkapnya</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="course__area grey-bg pt-115 pb-120">
    <div class="container">
        <div class="row align-items-end">
            <div class="col-xxl-6 col-xl-6 col-lg-6">
                <div class="section__title-wrapper mb-60">
                    <span class="section__sub-title">Arena Belajar</span>
                    <h2 class="section__title">Mulai perjalanan kompetensi PBJ Anda.</h2>
                </div>
            </div>
            <div class="col-xxl-6 col-xl-6 col-lg-6">
                <div class="course__more mb-60 float-lg-end"><a href="<?= site_url('arena-belajar') ?>" class="link-btn">Lihat semua pembelajaran <i class="far fa-arrow-right"></i></a></div>
            </div>
        </div>
        <div class="row">
            <?php foreach ($heroPrograms as $program): ?>
                <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                    <div class="course__item white-bg mb-30 fix">
                        <div class="course__thumb w-img fix">
                            <a href="<?= esc($program['url']) ?>"><img src="<?= base_url($program['image']) ?>" alt="<?= esc($program['title']) ?>"></a>
                        </div>
                        <div class="course__content">
                            <div class="course__meta d-flex align-items-center justify-content-between">
                                <div class="course__lesson"><span><?= esc($program['category']) ?></span></div>
                            </div>
                            <h3 class="course__title"><a href="<?= esc($program['url']) ?>"><?= esc($program['title']) ?></a></h3>
                            <div class="course__bottom d-sm-flex justify-content-between align-items-center">
                                <div class="course__btn"><a href="<?= esc($program['url']) ?>" class="link-btn">Lihat detail <i class="far fa-arrow-right"></i></a></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="services__area pt-115 pb-85">
    <div class="container">
        <div class="row align-items-end">
            <div class="col-xxl-6 col-xl-6 col-lg-6">
                <div class="section__title-wrapper mb-50">
                    <span class="section__sub-title">Layanan Utama</span>
                    <h2 class="section__title">Temukan informasi yang Anda butuhkan.</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <?php foreach ($services as $service): ?>
                <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                    <div class="services__item mb-30">
                        <div class="services__icon"><span><i class="<?= esc($service['icon']) ?>"></i></span></div>
                        <div class="services__content">
                            <h3 class="services__title"><a href="<?= esc($service['url']) ?>"><?= esc($service['title']) ?></a></h3>
                            <p><?= esc($service['description']) ?></p>
                            <a href="<?= esc($service['url']) ?>" class="link-btn">Jelajahi <i class="far fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="counter__area counter__area-2 pt-100 pb-100" data-background="<?= base_url('assets/main/assets/img/counter/counter-bg.jpg') ?>">
    <div class="container">
        <div class="row">
            <?php foreach ($statistics as $statistic): ?>
                <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-3 col-sm-6">
                    <div class="counter__item text-center mb-30">
                        <h4><span class="counter"><?= esc($statistic['value']) ?></span></h4>
                        <p><?= esc($statistic['label']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="cta__area pt-115 pb-120">
    <div class="container">
        <div class="cta__inner blue-bg fix p-relative">
            <div class="cta__shape"><img src="<?= base_url('assets/main/assets/img/cta/cta-shape.png') ?>" alt=""></div>
            <div class="row align-items-center">
                <div class="col-xxl-8 col-xl-8 col-lg-8">
                    <div class="cta__content">
                        <h3 class="cta__title">Siap memulai perjalanan pembelajaran PBJ?</h3>
                        <p>Daftar untuk mengakses layanan sesuai peran dan kebutuhan Anda.</p>
                    </div>
                </div>
                <div class="col-xxl-4 col-xl-4 col-lg-4">
                    <div class="cta__more d-md-flex justify-content-lg-end p-relative z-index-1">
                        <a href="<?= site_url('dashboard/daftar') ?>" class="e-btn e-btn-white">Daftar Sekarang</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>