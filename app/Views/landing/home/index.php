<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<section class="main-slider">
    <div class="main-slider__shape-1"><img src="<?= base_url('assets/main/images/shapes/banner-one-shape-1.png') ?>" alt=""></div>
    <div class="main-slider__shape-2"><img src="<?= base_url('assets/main/images/shapes/banner-one-shape-2.png') ?>" alt=""></div>
    <div class="main-slider__shape-3"><img src="<?= base_url('assets/main/images/shapes/banner-one-shape-3.png') ?>" alt=""></div>
    <div class="main-slider__shape-4"><img src="<?= base_url('assets/main/images/shapes/banner-one-shape-4.png') ?>" alt=""></div>
    <div class="main-slider__bg" style="background-image: url('<?= base_url('assets/main/images/backgrounds/slider-1-1.jpg') ?>');"></div>

    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-7 col-lg-7">
                <div class="main-slider__content">
                    <p class="main-slider__sub-title">Portal BegawiPBJ</p>
                    <h1 class="main-slider__title">Belajar dan bertumbuh bersama ekosistem Pengadaan Barang/Jasa.</h1>
                    <p class="main-slider__text">Jelajahi pembelajaran, pengetahuan, regulasi, event, dan sertifikat digital PBJ dalam satu portal terintegrasi.</p>
                    <div class="main-slider__btn-box">
                        <a href="<?= site_url('arena-belajar') ?>" class="thm-btn">Jelajahi Arena Belajar</a>
                        <a href="<?= site_url('regulasi') ?>" class="thm-btn thm-btn--outline">Lihat Regulasi</a>
                    </div>
                </div>
            </div>
            <div class="col-xl-5 col-lg-5">
                <div class="main-slider__image">
                    <img src="<?= base_url('assets/main/images/resources/banner-two-img-1.png') ?>" alt="Ilustrasi pembelajaran BegawiPBJ">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-one">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-6 col-lg-6">
                <div class="about-one__left">
                    <div class="about-one__img-box">
                        <div class="about-one__img"><img src="<?= base_url('assets/main/images/resources/abou-one-img-1.jpg') ?>" alt="Kegiatan pembelajaran PBJ"></div>
                        <div class="about-one__img-two"><img src="<?= base_url('assets/main/images/resources/abou-one-img-2.jpg') ?>" alt="Kolaborasi PBJ"></div>
                        <div class="about-one__shape"><img src="<?= base_url('assets/main/images/shapes/abou-one-img-shape-1.png') ?>" alt=""></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="about-one__right">
                    <div class="section-title text-left">
                        <span class="section-title__tagline">Tentang BegawiPBJ</span>
                        <h2 class="section-title__title">Satu pintu untuk kompetensi dan informasi PBJ.</h2>
                    </div>
                    <p class="about-one__text">BegawiPBJ menghadirkan layanan pembelajaran, pengetahuan, regulasi, agenda, serta sertifikat digital bagi ekosistem Pengadaan Barang/Jasa.</p>
                    <ul class="about-one__points list-unstyled">
                        <li><span class="icon"><i class="fa fa-check"></i></span>
                            <p>Program pembelajaran yang terstruktur</p>
                        </li>
                        <li><span class="icon"><i class="fa fa-check"></i></span>
                            <p>Referensi pengetahuan dan regulasi PBJ</p>
                        </li>
                        <li><span class="icon"><i class="fa fa-check"></i></span>
                            <p>Informasi event dan sertifikat digital</p>
                        </li>
                    </ul>
                    <a href="<?= site_url('tentang') ?>" class="thm-btn">Selengkapnya</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="course-one">
    <div class="course-one__bg" style="background-image: url('<?= base_url('assets/main/images/shapes/course-grid-title-shape-1.png') ?>');"></div>
    <div class="container">
        <div class="section-title text-center">
            <span class="section-title__tagline">Arena Belajar</span>
            <h2 class="section-title__title">Mulai perjalanan kompetensi PBJ Anda.</h2>
        </div>

        <div class="row">
            <?php foreach ($featuredCourses as $course): ?>
                <div class="col-xl-4 col-lg-4 col-md-6">
                    <div class="course-one__single">
                        <div class="course-one__img">
                            <img src="<?= base_url('assets/main/' . $course['image']) ?>" alt="<?= esc($course['title']) ?>">
                            <a href="<?= esc($course['url']) ?>"><span class="icon-plus"></span></a>
                        </div>
                        <div class="course-one__content">
                            <p class="course-one__category"><?= esc($course['category']) ?></p>
                            <h3 class="course-one__title"><a href="<?= esc($course['url']) ?>"><?= esc($course['title']) ?></a></h3>
                            <a href="<?= esc($course['url']) ?>" class="course-one__read-more">Lihat Detail <i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <a href="<?= site_url('arena-belajar/course') ?>" class="thm-btn thm-btn--outline">Lihat Semua Course</a>
        </div>
    </div>
</section>

<section class="why-choose-one">
    <div class="why-choose-one__shape-1"><img src="<?= base_url('assets/main/images/shapes/why-choose-one-shape-1.png') ?>" alt=""></div>
    <div class="container">
        <div class="section-title text-center">
            <span class="section-title__tagline">Layanan Utama</span>
            <h2 class="section-title__title">Temukan informasi yang Anda butuhkan.</h2>
        </div>
        <div class="row">
            <?php foreach ($services as $service): ?>
                <div class="col-xl-4 col-lg-4 col-md-6">
                    <div class="why-choose-one__single">
                        <div class="why-choose-one__icon"><img src="<?= base_url('assets/main/' . $service['icon']) ?>" alt=""></div>
                        <h3 class="why-choose-one__title"><a href="<?= esc($service['url']) ?>"><?= esc($service['title']) ?></a></h3>
                        <p class="why-choose-one__text"><?= esc($service['description']) ?></p>
                        <a href="<?= esc($service['url']) ?>" class="why-choose-one__read-more">Jelajahi <i class="fa fa-arrow-right"></i></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="counter-one" style="background-image: url('<?= base_url('assets/main/images/backgrounds/counter-one-bg.jpg') ?>');">
    <div class="container">
        <div class="row">
            <?php foreach ($statistics as $statistic): ?>
                <div class="col-xl-3 col-lg-3 col-md-6">
                    <div class="counter-one__single">
                        <div class="counter-one__count-box">
                            <h3 class="odometer" data-count="<?= esc(preg_replace('/[^0-9]/', '', $statistic['value'])) ?>">00</h3><span><?= str_contains($statistic['value'], '+') ? '+' : '' ?></span>
                        </div>
                        <p class="counter-one__text"><?= esc($statistic['label']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="newsletter-one">
    <div class="newsletter-one__shape-1"><img src="<?= base_url('assets/main/images/shapes/newsletter-one-shape-1.png') ?>" alt=""></div>
    <div class="newsletter-one__shape-2"><img src="<?= base_url('assets/main/images/shapes/newsletter-one-shape-2.png') ?>" alt=""></div>
    <div class="container">
        <div class="newsletter-one__inner">
            <div class="row align-items-center">
                <div class="col-xl-8 col-lg-8">
                    <div class="newsletter-one__content">
                        <h2 class="newsletter-one__title">Siap memulai perjalanan pembelajaran PBJ?</h2>
                        <p class="newsletter-one__text">Daftar untuk mengakses layanan sesuai peran dan kebutuhan Anda.</p>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4">
                    <div class="newsletter-one__btn-box text-xl-end">
                        <a href="<?= site_url('dashboard/daftar') ?>" class="thm-btn">Daftar Sekarang</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>