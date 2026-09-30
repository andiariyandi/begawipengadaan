<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<!-- Banner One Start -->
<section class="banner-one">
    <div class="banner-one__bg-shape-1"
        style="background-image: url(<?= base_url('assets/main/images/shapes/banner-one-bg-shape-1.png') ?>);"></div>
    <div class="banner-one__icon-1 img-bounce">
        <img src="<?= base_url('assets/main/images/icon/idea-bulb.png') ?>" alt="">
    </div>
    <div class="banner-one__icon-2 float-bob-x">
        <img src="<?= base_url('assets/main/images/icon/3d-alarm.png') ?>" alt="">
    </div>
    <div class="banner-one__icon-3 float-bob-y">
        <img src="<?= base_url('assets/main/images/icon/linke-icon.png') ?>" alt="">
    </div>
    <div class="banner-one__shape-4 float-bob-x">
        <img src="<?= base_url('assets/main/images/shapes/banner-one-shape-4.png') ?>" alt="">
    </div>
    <div class="container">
        <div class="row">
            <div class="col-xl-6">
                <div class="banner-one__left">
                    <div class="banner-one__title-box">
                        <div class="banner-one__title-box-shape">
                            <img src="<?= base_url('assets/main/images/shapes/banner-one-title-box-shape-1.png') ?>" alt="">
                        </div>
                        <h2 class="banner-one__title">Pusat Belajar <br>
                            <span class="banner-one__title-clr-1">Pengadaan</span>
                            <span class="banner-one__title-clr-2">Barang/Jasa <br> Pemerintah</span>
                        </h2>
                    </div>
                    <p class="banner-one__text">Akses pembelajaran, pengetahuan, dan regulasi Pengadaan Barang/Jasa <br>
                        untuk mendukung kompetensi dan tata kelola pengadaan.</p>
                    <div class="banner-one__thm-and-other-btn-box">
                        <div class="banner-one__btn-box">
                            <a href="<?= route_to('landing.learning.index') ?>" class="thm-btn"><span
                                    class="icon-angles-right"></span>Jelajahi Arena</a>
                        </div>
                        <div class="banner-one__other-btn-box">
                            <a href="<?= route_to('landing.knowledge.index') ?>" class="banner-one__other-btn-1"><span
                                    class="icon-thumbs-up"></span>Bunga Rampai</a>
                            <a href="<?= route_to('landing.regulations.index') ?>" class="banner-one__other-btn-1 banner-one__other-btn-2"><span
                                    class="icon-thumbs-up"></span>Regulasi PBJ</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="banner-one__right">
                    <div class="banner-one__img-box">
                        <div class="banner-one__img">
                            <img src="<?= base_url('assets/main/images/resources/banner-two-img-1.png') ?>" alt="">
                            <div class="banner-one__img-shape-box rotate-me">
                                <div class="banner-one__img-shape-1">
                                    <div class="banner-one__img-shape-2"></div>
                                </div>
                                <div class="banner-one__shape-1">
                                    <img src="<?= base_url('assets/main/images/shapes/banner-one-shape-1.png') ?>" alt="">
                                </div>
                                <div class="banner-one__shape-2 rotate-me">
                                    <img src="<?= base_url('assets/main/images/shapes/banner-one-shape-2.png') ?>" alt="">
                                </div>
                                <div class="banner-one__shape-3">
                                    <img src="<?= base_url('assets/main/images/shapes/banner-one-shape-3.png') ?>" alt="">
                                </div>
                            </div>
                            <div class="banner-one__udemy-review">
                                <div class="banner-one__udemy-review-img">
                                    <img src="<?= base_url('assets/main/images/resources/banner-one-udemy-review-img.jpg') ?>" alt="">
                                </div>
                                <div class="banner-one__udemy-review-logo">
                                    <img src="<?= base_url('assets/main/images/resources/banner-one-udemy-review-logo.png') ?>" alt="">
                                </div>
                                <div class="banner-one__udemy-review-client-info">
                                    <p class="banner-one__udemy-review-client-name">BegawiPBJ/</p>
                                    <div class="banner-one__udemy-review-star">
                                        <span class="icon-star"></span>
                                        <span class="icon-star"></span>
                                        <span class="icon-star"></span>
                                        <span class="icon-star"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="banner-one__student-trained">
                                <div class="banner-one__student-trained-shape-1 rotate-me">
                                    <img src="<?= base_url('assets/main/images/shapes/banner-one-student-trained-shape-1.png') ?>" alt="">
                                </div>
                                <ul class="list-unstyled banner-one__student-trained-list">
                                    <li>
                                        <div class="banner-one__student-trained-img">
                                            <img src="<?= base_url('assets/main/images/resources/banner-one-student-trained-img-1-1.jpg') ?>" alt="">
                                        </div>
                                    </li>
                                    <li>
                                        <div class="banner-one__student-trained-img">
                                            <img src="<?= base_url('assets/main/images/resources/banner-one-student-trained-img-1-2.jpg') ?>" alt="">
                                        </div>
                                    </li>
                                </ul>
                                <div class="banner-one__student-trained-count-box">
                                    <div class="banner-one__student-trained-count-box-inner count-box">
                                        <p class="count-text" data-stop="0" data-speed="3000">00</p>
                                        <span>+</span>
                                    </div>
                                    <p class="banner-one__student-trained-text">Peserta Terdaftar</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="banner-one__category-search-box">
                    <div class="banner-one__category-search-inner">
                        <div class="banner-one__category-select-box">
                            <div class="select-box">
                                <select class="wide">
                                    <option data-display="Semua Layanan">Semua Layanan</option>
                                    <option value="arena-belajar">Arena Belajar</option>
                                    <option value="bunga-rampai">Bunga Rampai</option>
                                    <option value="regulasi">Regulasi PBJ</option>
                                    <option value="berita">Berita</option>
                                    <option value="event">Event</option>
                                </select>
                            </div>
                        </div>
                        <form class="banner-one__category-form" action="<?= route_to('landing.knowledge.search') ?>" method="get">
                            <div class="banner-one__category-input">
                                <input type="search" name="q" placeholder="Cari pembelajaran atau pengetahuan PBJ">
                            </div>
                            <button type="submit" class="banner-one__category-btn">Cari</button>
                        </form>
                    </div>
                    <div class="banner-one__tags">
                        <a href="<?= route_to('landing.learning.index') ?>">Arena Belajar</a>
                        <a href="<?= route_to('landing.knowledge.index') ?>">Bunga Rampai</a>
                        <a href="<?= route_to('landing.regulations.index') ?>">Regulasi</a>
                        <a href="<?= route_to('landing.news.index') ?>">Berita</a>
                        <a href="<?= route_to('landing.events.index') ?>">Event</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Banner One End -->

<!-- Sliding Text One Start-->
<section class="sliding-text-one">
    <div class="sliding-text-one__wrap">
        <ul class="sliding-text__list list-unstyled marquee_mode">
            <li>
                <h2 data-hover="Pembelajaran PBJ" class="sliding-text__title">
                    Pembelajaran PBJ
                    <img src="<?= base_url('assets/main/images/shapes/sliding-text-shape-1.png') ?>" alt="">
                </h2>
            </li>
            <li>
                <h2 data-hover="Bunga Rampai" class="sliding-text__title">
                    Bunga Rampai
                    <img src="<?= base_url('assets/main/images/shapes/sliding-text-shape-1.png') ?>" alt="">
                </h2>
            </li>
            <li>
                <h2 data-hover="Regulasi PBJ" class="sliding-text__title">Regulasi PBJ
                    <img src="<?= base_url('assets/main/images/shapes/sliding-text-shape-1.png') ?>" alt="">
                </h2>
            </li>
            <li>
                <h2 data-hover="Informasi Publik" class="sliding-text__title">Informasi Publik
                    <img src="<?= base_url('assets/main/images/shapes/sliding-text-shape-1.png') ?>" alt="">
                </h2>
            </li>
        </ul>
    </div>
</section>
<!-- Sliding Text One End -->

<!-- Category One Start -->
<section class="category-one">
    <div class="category-one__bg-shape"
        style="background-image: url(<?= base_url('assets/main/images/shapes/category-one-bg-shape.png') ?>);"></div>
    <div class="category-one__shape-1">
        <img src="<?= base_url('assets/main/images/shapes/category-one-shape-1.png') ?>" alt="">
    </div>
    <div class="category-one__shape-2">
        <img src="<?= base_url('assets/main/images/shapes/category-one-shape-2.png') ?>" alt="">
    </div>
    <div class="category-one__shape-3">
        <img src="<?= base_url('assets/main/images/shapes/category-one-shape-3.png') ?>" alt="">
    </div>
    <div class="category-one__social-media">
        <ul class="category-one__social-media-list list-unstyled">
            <li>
                <h3>Pembelajaran</h3>
            </li>
            <li><img src="<?= base_url('assets/main/images/shapes/category-one-social-media-shape-1.png') ?>" alt=""></li>
            <li>
                <h3>Pengetahuan</h3>
            </li>
            <li><img src="<?= base_url('assets/main/images/shapes/category-one-social-media-shape-1.png') ?>" alt=""></li>
            <li>
                <h3>Regulasi</h3>
            </li>
        </ul>
    </div>
    <div class="category-one__brand-box">
        <ul class="category-one__brand-list list-unstyled">
            <li>
                <div class="category-one__brand"><img src="<?= base_url('assets/main/images/brand/category-one-brand-1-1.png') ?>" alt=""></div>
            </li>
            <li>
                <div class="category-one__brand"><img src="<?= base_url('assets/main/images/brand/category-one-brand-1-2.png') ?>" alt=""></div>
            </li>
            <li>
                <div class="category-one__brand"><img src="<?= base_url('assets/main/images/brand/category-one-brand-1-3.png') ?>" alt=""></div>
            </li>
            <li>
                <div class="category-one__brand"><img src="<?= base_url('assets/main/images/brand/category-one-brand-1-4.png') ?>" alt=""></div>
            </li>
        </ul>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-xl-6 col-lg-7">
                <div class="category-one__left">
                    <div class="section-title text-left sec-title-animation animation-style2">
                        <div class="section-title__tagline-box">
                            <div class="section-title__tagline-shape"></div>
                            <span class="section-title__tagline">Layanan</span>
                        </div>
                        <h2 class="section-title__title title-animation">Jelajahi Layanan Kami untuk<br> Mendukung
                            Kebutuhan <span>PBJ <img src="<?= base_url('assets/main/images/shapes/section-title-shape-2.png') ?>" alt=""></span></h2>
                    </div>
                    <ul class="category-one__category-list list-unstyled">
                        <li>
                            <div class="category-one__count-and-arrow">
                                <div class="category-one__count-box">
                                    <div class="category-one__count"></div>
                                    <div class="category-one__count-content">
                                        <h3><a href="<?= route_to('landing.learning.index') ?>">Arena Belajar</a></h3>
                                        <p>Program dan kelas pembelajaran</p>
                                    </div>
                                </div>
                                <div class="category-one__count-arrow"><a href="<?= route_to('landing.learning.index') ?>"><span class="icon-arrow-up-right-2"></span></a></div>
                            </div>
                            <div class="category-one__hover-icon-and-arrow">
                                <div class="category-one__hover-icon-box">
                                    <div class="category-one__hover-icon"><img src="<?= base_url('assets/main/images/icon/category-one-hover-icon-1-1.png') ?>" alt=""></div>
                                    <div class="category-one__hover-content">
                                        <h3><a href="<?= route_to('landing.learning.index') ?>">Arena Belajar</a></h3>
                                        <p>Program dan kelas pembelajaran</p>
                                    </div>
                                </div>
                                <div class="category-one__hover-arrow"><a href="<?= route_to('landing.learning.index') ?>"><span class="icon-arrow-up-right-2"></span></a></div>
                            </div>
                        </li>
                        <li>
                            <div class="category-one__count-and-arrow">
                                <div class="category-one__count-box">
                                    <div class="category-one__count"></div>
                                    <div class="category-one__count-content">
                                        <h3><a href="<?= route_to('landing.knowledge.index') ?>">Bunga Rampai</a></h3>
                                        <p>Artikel, studi kasus, dan media</p>
                                    </div>
                                </div>
                                <div class="category-one__count-arrow"><a href="<?= route_to('landing.knowledge.index') ?>"><span class="icon-arrow-up-right-2"></span></a></div>
                            </div>
                            <div class="category-one__hover-icon-and-arrow">
                                <div class="category-one__hover-icon-box">
                                    <div class="category-one__hover-icon"><img src="<?= base_url('assets/main/images/icon/category-one-hover-icon-1-1.png') ?>" alt=""></div>
                                    <div class="category-one__hover-content">
                                        <h3><a href="<?= route_to('landing.knowledge.index') ?>">Bunga Rampai</a></h3>
                                        <p>Artikel, studi kasus, dan media</p>
                                    </div>
                                </div>
                                <div class="category-one__hover-arrow"><a href="<?= route_to('landing.knowledge.index') ?>"><span class="icon-arrow-up-right-2"></span></a></div>
                            </div>
                        </li>
                        <li>
                            <div class="category-one__count-and-arrow">
                                <div class="category-one__count-box">
                                    <div class="category-one__count"></div>
                                    <div class="category-one__count-content">
                                        <h3><a href="<?= route_to('landing.regulations.index') ?>">Regulasi PBJ</a></h3>
                                        <p>Dokumen dan relasi regulasi</p>
                                    </div>
                                </div>
                                <div class="category-one__count-arrow"><a href="<?= route_to('landing.regulations.index') ?>"><span class="icon-arrow-up-right-2"></span></a></div>
                            </div>
                            <div class="category-one__hover-icon-and-arrow">
                                <div class="category-one__hover-icon-box">
                                    <div class="category-one__hover-icon"><img src="<?= base_url('assets/main/images/icon/category-one-hover-icon-1-1.png') ?>" alt=""></div>
                                    <div class="category-one__hover-content">
                                        <h3><a href="<?= route_to('landing.regulations.index') ?>">Regulasi PBJ</a></h3>
                                        <p>Dokumen dan relasi regulasi</p>
                                    </div>
                                </div>
                                <div class="category-one__hover-arrow"><a href="<?= route_to('landing.regulations.index') ?>"><span class="icon-arrow-up-right-2"></span></a></div>
                            </div>
                        </li>
                        <li>
                            <div class="category-one__count-and-arrow">
                                <div class="category-one__count-box">
                                    <div class="category-one__count"></div>
                                    <div class="category-one__count-content">
                                        <h3><a href="<?= route_to('landing.events.index') ?>">Agenda dan Event</a></h3>
                                        <p>Informasi kegiatan dan agenda PBJ</p>
                                    </div>
                                </div>
                                <div class="category-one__count-arrow"><a href="<?= route_to('landing.events.index') ?>"><span class="icon-arrow-up-right-2"></span></a></div>
                            </div>
                            <div class="category-one__hover-icon-and-arrow">
                                <div class="category-one__hover-icon-box">
                                    <div class="category-one__hover-icon"><img src="<?= base_url('assets/main/images/icon/category-one-hover-icon-1-1.png') ?>" alt=""></div>
                                    <div class="category-one__hover-content">
                                        <h3><a href="<?= route_to('landing.events.index') ?>">Agenda dan Event</a></h3>
                                        <p>Informasi kegiatan dan agenda PBJ</p>
                                    </div>
                                </div>
                                <div class="category-one__hover-arrow"><a href="<?= route_to('landing.events.index') ?>"><span class="icon-arrow-up-right-2"></span></a></div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-xl-6 col-lg-5 wow slideInRight" data-wow-delay="100ms" data-wow-duration="2500ms">
                <div class="category-one__right">
                    <div class="category-one__img">
                        <img src="<?= base_url('assets/main/images/resources/category-one-img-1.png') ?>" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Category One End -->
<?= $this->endSection() ?>