<?= $this->extend('landing/layouts/main') ?>

<?= $this->section('content') ?>
<section class="main-slider">
    <div class="main-slider__carousel owl-carousel owl-theme thm-owl__carousel" data-owl-options='{"loop":true,"animateOut":"fadeOut","animateIn":"fadeIn","items":1,"margin":0,"stagePadding":0,"smartSpeed":1000,"autoplay":7000,"nav":true,"navText":["<span class=\"icon-right-arrow\"></span>","<span class=\"icon-right-arrow\"></span>"],"dots":false}'>
        <div class="item main-slider__slide-1">
            <div class="main-slider__bg" style="background-image: url(<?= base_url('assets/main/images/backgrounds/slider-1-1.jpg') ?>);"></div>
            <div class="container">
                <div class="row">
                    <div class="col-xl-8">
                        <div class="main-slider__content">
                            <p class="main-slider__sub-title">Ekosistem Digital PBJ</p>
                            <h2 class="main-slider__title">Belajar, berbagi pengetahuan, dan tumbuh bersama <span>BegawiPBJ</span></h2>
                            <p class="main-slider__text">Platform digital Pengadaan Barang/Jasa Pemerintah Provinsi Lampung untuk meningkatkan kompetensi, akses regulasi, dan kolaborasi ekosistem PBJ.</p>
                            <div class="main-slider__btn-box">
                                <a href="<?= route_to('landing.learning.index') ?>" class="thm-btn">Jelajahi Arena Belajar <span class="icon-angles-right"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="item main-slider__slide-2">
            <div class="main-slider__bg" style="background-image: url(<?= base_url('assets/main/images/backgrounds/slider-1-2.jpg') ?>);"></div>
            <div class="container">
                <div class="row">
                    <div class="col-xl-8">
                        <div class="main-slider__content">
                            <p class="main-slider__sub-title">Pusat Pengetahuan PBJ</p>
                            <h2 class="main-slider__title">Akses pengetahuan dan <span>regulasi</span> secara terstruktur</h2>
                            <p class="main-slider__text">Temukan artikel, FAQ, studi kasus, template, media, serta repository regulasi PBJ yang mudah ditelusuri.</p>
                            <div class="main-slider__btn-box">
                                <a href="<?= route_to('landing.knowledge.index') ?>" class="thm-btn">Buka Bunga Rampai <span class="icon-angles-right"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="category-one">
    <div class="container">
        <div class="section-title text-center">
            <div class="section-title__tagline-box"><span class="section-title__tagline">Layanan Utama</span></div>
            <h2 class="section-title__title">Satu ekosistem untuk kebutuhan <br>Pengadaan Barang/Jasa</h2>
        </div>
        <div class="row">
            <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="100ms">
                <div class="category-one__single">
                    <div class="category-one__icon"><span class="icon-online-learning"></span></div>
                    <h3 class="category-one__title"><a href="<?= route_to('landing.learning.index') ?>">Arena Belajar</a></h3>
                    <p class="category-one__text">Program, course, kelas, dan instruktur PBJ.</p>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                <div class="category-one__single">
                    <div class="category-one__icon"><span class="icon-book"></span></div>
                    <h3 class="category-one__title"><a href="<?= route_to('landing.knowledge.index') ?>">Bunga Rampai</a></h3>
                    <p class="category-one__text">Artikel, FAQ, studi kasus, template, dan media.</p>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="300ms">
                <div class="category-one__single">
                    <div class="category-one__icon"><span class="icon-certificate"></span></div>
                    <h3 class="category-one__title"><a href="<?= route_to('landing.regulations.index') ?>">Regulasi PBJ</a></h3>
                    <p class="category-one__text">Repository regulasi, versi, relasi, dan status hukum.</p>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="400ms">
                <div class="category-one__single">
                    <div class="category-one__icon"><span class="icon-calendar"></span></div>
                    <h3 class="category-one__title"><a href="<?= route_to('landing.events.index') ?>">Event dan Agenda</a></h3>
                    <p class="category-one__text">Webinar, sosialisasi, workshop, dan kegiatan PBJ.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-one">
    <div class="about-one__shape-1 float-bob-y"><img src="<?= base_url('assets/main/images/shapes/about-one-shape-1.png') ?>" alt=""></div>
    <div class="container">
        <div class="row">
            <div class="col-xl-6">
                <div class="about-one__left">
                    <div class="about-one__img-box">
                        <div class="about-one__img"><img src="<?= base_url('assets/main/images/resources/abou-one-img-1.jpg') ?>" alt="Aktivitas pembelajaran BegawiPBJ"></div>
                        <div class="about-one__img-2"><img src="<?= base_url('assets/main/images/resources/abou-one-img-2.jpg') ?>" alt="Kolaborasi pembelajaran"></div>
                        <div class="about-one__experience">
                            <div class="about-one__experience-icon"><span class="icon-graduation-cap"></span></div>
                            <div class="about-one__experience-content">
                                <h3>PBJ</h3>
                                <p>Berdaya dan Kompeten</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="about-one__right">
                    <div class="section-title text-left">
                        <div class="section-title__tagline-box"><span class="section-title__tagline">Tentang BegawiPBJ</span></div>
                        <h2 class="section-title__title">Ruang belajar dan pengetahuan untuk ekosistem PBJ Lampung</h2>
                    </div>
                    <p class="about-one__text">BegawiPBJ menyatukan pembelajaran digital, pusat pengetahuan, regulasi, berita, event, sertifikat digital, statistik publik, dan layanan pendukung dalam satu platform terintegrasi.</p>
                    <ul class="list-unstyled about-one__points">
                        <li>
                            <div class="icon"><span class="icon-check"></span></div>
                            <div class="text">
                                <p>Pembelajaran PBJ yang terstruktur dan terukur</p>
                            </div>
                        </li>
                        <li>
                            <div class="icon"><span class="icon-check"></span></div>
                            <div class="text">
                                <p>Akses publik terhadap pengetahuan dan regulasi PBJ</p>
                            </div>
                        </li>
                        <li>
                            <div class="icon"><span class="icon-check"></span></div>
                            <div class="text">
                                <p>Layanan berbasis peran, keamanan, dan auditabilitas</p>
                            </div>
                        </li>
                    </ul>
                    <div class="about-one__btn-box"><a href="<?= route_to('landing.pages.about') ?>" class="thm-btn">Selengkapnya <span class="icon-angles-right"></span></a></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="courses-one">
    <div class="container">
        <div class="courses-one__top">
            <div class="section-title text-left">
                <div class="section-title__tagline-box"><span class="section-title__tagline">Arena Belajar</span></div>
                <h2 class="section-title__title">Jelajahi program dan course PBJ</h2>
            </div>
            <div class="courses-one__btn-box"><a href="<?= route_to('landing.learning.courses.index') ?>" class="thm-btn">Lihat Semua Course <span class="icon-angles-right"></span></a></div>
        </div>
        <div class="row">
            <div class="col-xl-4 col-lg-6 wow fadeInUp" data-wow-delay="100ms">
                <div class="courses-one__single">
                    <div class="courses-one__img"><img src="<?= base_url('assets/main/images/resources/courses-1-1.jpg') ?>" alt="Dasar Pengadaan Barang Jasa">
                        <div class="courses-one__price">
                            <p>Program</p>
                        </div>
                    </div>
                    <div class="courses-one__content">
                        <div class="courses-one__rating"><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span></div>
                        <h3 class="courses-one__title"><a href="<?= route_to('landing.learning.courses.index') ?>">Dasar-Dasar Pengadaan Barang/Jasa Pemerintah</a></h3>
                        <p class="courses-one__text">Fondasi pengetahuan, proses, dan prinsip PBJ.</p>
                        <div class="courses-one__bottom">
                            <div class="courses-one__students"><span class="icon-user"></span>
                                <p>Course Publik</p>
                            </div>
                            <div class="courses-one__btn"><a href="<?= route_to('landing.learning.courses.index') ?>">Lihat <span class="icon-right-arrow"></span></a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 wow fadeInUp" data-wow-delay="200ms">
                <div class="courses-one__single">
                    <div class="courses-one__img"><img src="<?= base_url('assets/main/images/resources/courses-1-2.jpg') ?>" alt="Manajemen Kontrak">
                        <div class="courses-one__price">
                            <p>Course</p>
                        </div>
                    </div>
                    <div class="courses-one__content">
                        <div class="courses-one__rating"><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span></div>
                        <h3 class="courses-one__title"><a href="<?= route_to('landing.learning.courses.index') ?>">Manajemen Kontrak dan Pengendalian Pelaksanaan</a></h3>
                        <p class="courses-one__text">Pengelolaan kontrak yang akuntabel dan efektif.</p>
                        <div class="courses-one__bottom">
                            <div class="courses-one__students"><span class="icon-user"></span>
                                <p>Course Publik</p>
                            </div>
                            <div class="courses-one__btn"><a href="<?= route_to('landing.learning.courses.index') ?>">Lihat <span class="icon-right-arrow"></span></a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 wow fadeInUp" data-wow-delay="300ms">
                <div class="courses-one__single">
                    <div class="courses-one__img"><img src="<?= base_url('assets/main/images/resources/courses-1-3.jpg') ?>" alt="Penyusunan Spesifikasi">
                        <div class="courses-one__price">
                            <p>Kelas</p>
                        </div>
                    </div>
                    <div class="courses-one__content">
                        <div class="courses-one__rating"><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span></div>
                        <h3 class="courses-one__title"><a href="<?= route_to('landing.learning.classes.index') ?>">Penyusunan Spesifikasi Teknis dan KAK</a></h3>
                        <p class="courses-one__text">Praktik menyusun kebutuhan pengadaan secara tepat.</p>
                        <div class="courses-one__bottom">
                            <div class="courses-one__students"><span class="icon-user"></span>
                                <p>Kelas Terjadwal</p>
                            </div>
                            <div class="courses-one__btn"><a href="<?= route_to('landing.learning.classes.index') ?>">Lihat <span class="icon-right-arrow"></span></a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="counter-one">
    <div class="counter-one__bg" style="background-image: url(<?= base_url('assets/main/images/backgrounds/counter-one-bg.jpg') ?>);"></div>
    <div class="container">
        <div class="row">
            <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="100ms">
                <div class="counter-one__single">
                    <div class="counter-one__icon"><span class="icon-online-learning"></span></div>
                    <div class="counter-one__content">
                        <h3 class="odometer" data-count="0">00</h3>
                        <p class="counter-one__text">Program Publik</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                <div class="counter-one__single">
                    <div class="counter-one__icon"><span class="icon-book"></span></div>
                    <div class="counter-one__content">
                        <h3 class="odometer" data-count="0">00</h3>
                        <p class="counter-one__text">Konten Pengetahuan</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="300ms">
                <div class="counter-one__single">
                    <div class="counter-one__icon"><span class="icon-calendar"></span></div>
                    <div class="counter-one__content">
                        <h3 class="odometer" data-count="0">00</h3>
                        <p class="counter-one__text">Event dan Agenda</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="400ms">
                <div class="counter-one__single">
                    <div class="counter-one__icon"><span class="icon-certificate"></span></div>
                    <div class="counter-one__content">
                        <h3 class="odometer" data-count="0">00</h3>
                        <p class="counter-one__text">Layanan Sertifikat</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="blog-one">
    <div class="container">
        <div class="blog-one__top">
            <div class="section-title text-left">
                <div class="section-title__tagline-box"><span class="section-title__tagline">Bunga Rampai</span></div>
                <h2 class="section-title__title">Pengetahuan terbaru seputar PBJ</h2>
            </div>
            <div class="blog-one__btn-box"><a href="<?= route_to('landing.knowledge.articles.index') ?>" class="thm-btn">Lihat Semua Artikel <span class="icon-angles-right"></span></a></div>
        </div>
        <div class="row">
            <div class="col-xl-4 col-lg-6 wow fadeInUp" data-wow-delay="100ms">
                <div class="blog-one__single">
                    <div class="blog-one__img"><img src="<?= base_url('assets/main/images/blog/blog-1-1.jpg') ?>" alt="Artikel PBJ"><a href="<?= route_to('landing.knowledge.articles.index') ?>"><span class="icon-plus"></span></a></div>
                    <div class="blog-one__content">
                        <ul class="list-unstyled blog-one__meta">
                            <li><a href="<?= route_to('landing.knowledge.articles.index') ?>"><span class="icon-user"></span>BegawiPBJ</a></li>
                            <li><a href="<?= route_to('landing.knowledge.articles.index') ?>"><span class="icon-calendar"></span>Artikel</a></li>
                        </ul>
                        <h3 class="blog-one__title"><a href="<?= route_to('landing.knowledge.articles.index') ?>">Panduan Praktis Memahami Tahapan Pengadaan</a></h3><a href="<?= route_to('landing.knowledge.articles.index') ?>" class="blog-one__read-more">Baca Selengkapnya <span class="icon-right-arrow"></span></a>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 wow fadeInUp" data-wow-delay="200ms">
                <div class="blog-one__single">
                    <div class="blog-one__img"><img src="<?= base_url('assets/main/images/blog/blog-1-2.jpg') ?>" alt="Studi kasus PBJ"><a href="<?= route_to('landing.knowledge.case-studies.index') ?>"><span class="icon-plus"></span></a></div>
                    <div class="blog-one__content">
                        <ul class="list-unstyled blog-one__meta">
                            <li><a href="<?= route_to('landing.knowledge.case-studies.index') ?>"><span class="icon-user"></span>BegawiPBJ</a></li>
                            <li><a href="<?= route_to('landing.knowledge.case-studies.index') ?>"><span class="icon-calendar"></span>Studi Kasus</a></li>
                        </ul>
                        <h3 class="blog-one__title"><a href="<?= route_to('landing.knowledge.case-studies.index') ?>">Praktik Baik Pengelolaan Pengadaan Pemerintah</a></h3><a href="<?= route_to('landing.knowledge.case-studies.index') ?>" class="blog-one__read-more">Baca Selengkapnya <span class="icon-right-arrow"></span></a>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 wow fadeInUp" data-wow-delay="300ms">
                <div class="blog-one__single">
                    <div class="blog-one__img"><img src="<?= base_url('assets/main/images/blog/blog-1-3.jpg') ?>" alt="Regulasi PBJ"><a href="<?= route_to('landing.regulations.index') ?>"><span class="icon-plus"></span></a></div>
                    <div class="blog-one__content">
                        <ul class="list-unstyled blog-one__meta">
                            <li><a href="<?= route_to('landing.regulations.index') ?>"><span class="icon-user"></span>BegawiPBJ</a></li>
                            <li><a href="<?= route_to('landing.regulations.index') ?>"><span class="icon-calendar"></span>Regulasi</a></li>
                        </ul>
                        <h3 class="blog-one__title"><a href="<?= route_to('landing.regulations.index') ?>">Menelusuri Regulasi PBJ Secara Terstruktur</a></h3><a href="<?= route_to('landing.regulations.index') ?>" class="blog-one__read-more">Baca Selengkapnya <span class="icon-right-arrow"></span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="newsletter-one">
    <div class="container">
        <div class="newsletter-one__inner">
            <div class="newsletter-one__shape-1"><img src="<?= base_url('assets/main/images/shapes/newsletter-one-shape-1.png') ?>" alt=""></div>
            <div class="newsletter-one__left">
                <h2 class="newsletter-one__title">Mulai perjalanan pembelajaran PBJ Anda</h2>
                <p class="newsletter-one__text">Daftar untuk mengakses layanan digital BegawiPBJ sesuai status dan peran Anda.</p>
            </div>
            <div class="newsletter-one__right"><a href="<?= route_to('dashboard.auth.register.account') ?>" class="thm-btn">Daftar Sekarang <span class="icon-angles-right"></span></a></div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>