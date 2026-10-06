<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<?php $page_title = 'Tentang BegawiPBJ'; ?>
<?= view('landing/components/page_header', ['page_title' => $page_title]) ?>

<!--About Three Start-->
<section class="about-three">
    <div class="about-three__shape-2 rotate-me"></div>
    <div class="about-three__shape-3 text-rotate-box"></div>
    <div class="about-three__shape-4 float-bob-y"></div>
    <div class="container">
        <div class="row">

            <div class="col-xl-7">
                <div class="about-three__left wow slideInLeft" data-wow-delay="100ms"
                    data-wow-duration="2500ms">

                    <div class="about-three__shape-1 img-bounce">
                        <img src="<?= base_url('assets/main/') ?>images/shapes/about-three-shape-1.png" alt="">
                    </div>

                    <div class="about-three__img-box">

                        <div class="about-three__img-one">
                            <img src="<?= base_url('assets/main/') ?>images/resources/about-three-img-1.jpg" alt="">
                        </div>

                        <div class="about-three__img-two">
                            <img src="<?= base_url('assets/main/') ?>images/resources/about-three-img-2.jpg" alt="">
                        </div>


                        <div class="about-three__experience-box">

                            <div class="about-three__experience-box-inner">

                                <div class="about-three__experience-icon">
                                    <img src="<?= base_url('assets/main/') ?>images/icon/about-three-experience-icon.png" alt="">
                                </div>


                                <div class="about-three__experience-count-box">

                                    <div class="about-three__experience-count">

                                        <h3 class="odometer" data-count="25">00</h3>

                                        <span>+</span>

                                        <p>Tahun</p>

                                    </div>

                                    <p>Membangun Ekosistem</p>

                                </div>

                            </div>

                            <div class="about-three__experience-box-shape"></div>

                        </div>

                    </div>

                </div>
            </div>


            <div class="col-xl-5">

                <div class="about-three__right">


                    <div class="section-title-two text-left sec-title-animation animation-style2">


                        <div class="section-title-two__tagline-box">

                            <div class="section-title-two__tagline-shape">
                                <img src="<?= base_url('assets/main/') ?>images/shapes/section-title-two-shape-1.png" alt="">
                            </div>


                            <span class="section-title-two__tagline">
                                Tentang BegawiPBJ
                            </span>


                        </div>



                        <h2 class="section-title-two__title title-animation">

                            Membangun Ekosistem Digital
                            Pengadaan Barang/Jasa
                            <span>Pemerintah Lampung</span>

                        </h2>


                    </div>



                    <p class="about-three__text-1">

                        BegawiPBJ merupakan platform digital terpadu yang mendukung
                        pengembangan ekosistem Pengadaan Barang/Jasa Pemerintah
                        Provinsi Lampung melalui pembelajaran digital, pusat
                        pengetahuan, regulasi, informasi kegiatan, sertifikat
                        digital, serta pengelolaan data PBJ yang terstruktur.

                    </p>



                    <div class="about-three__mission-vission">


                        <div class="about-three__tab-box tabs-box">


                            <ul class="tab-buttons clearfix list-unstyled">

                                <li data-tab="#mission" class="tab-btn active-btn">
                                    <span>Visi dan Misi</span>
                                </li>


                                <li data-tab="#vission" class="tab-btn">
                                    <span>Arah Pengembangan</span>
                                </li>

                            </ul>



                            <div class="tabs-content">


                                <!--tab-->
                                <div class="tab active-tab" id="mission">


                                    <div class="tabs-content__inner">


                                        <ul class="list-unstyled about-three__mission-vission-list">


                                            <li>

                                                <div class="about-three__mission-vission-content">


                                                    <h4 class="about-three__mission-vission-title">

                                                        Pusat Pembelajaran PBJ Digital

                                                    </h4>


                                                    <p class="about-three__mission-vission-text">

                                                        Menyediakan pembelajaran berbasis digital melalui
                                                        program, course, kelas, materi, assessment,
                                                        hingga sertifikat untuk meningkatkan kompetensi
                                                        SDM Pengadaan Barang/Jasa.

                                                    </p>


                                                </div>

                                            </li>



                                            <li>

                                                <div class="about-three__mission-vission-content">


                                                    <h4 class="about-three__mission-vission-title">

                                                        Ekosistem Pengetahuan PBJ

                                                    </h4>


                                                    <p class="about-three__mission-vission-text">

                                                        Menghadirkan pusat informasi melalui Bunga Rampai,
                                                        artikel, FAQ, studi kasus, template dokumen,
                                                        media, dan praktik baik Pengadaan Barang/Jasa.

                                                    </p>


                                                </div>

                                            </li>


                                        </ul>


                                    </div>


                                </div>
                                <!--tab-->



                                <!--tab-->

                                <div class="tab" id="vission">


                                    <div class="tabs-content__inner">


                                        <ul class="list-unstyled about-three__mission-vission-list">


                                            <li>

                                                <div class="about-three__mission-vission-content">


                                                    <h4 class="about-three__mission-vission-title">

                                                        Repository Regulasi Terintegrasi

                                                    </h4>


                                                    <p class="about-three__mission-vission-text">

                                                        Mengelola regulasi PBJ secara terstruktur
                                                        melalui metadata, versi dokumen,
                                                        relasi regulasi, preview, dan download
                                                        yang tercatat.

                                                    </p>


                                                </div>

                                            </li>



                                            <li>

                                                <div class="about-three__mission-vission-content">


                                                    <h4 class="about-three__mission-vission-title">

                                                        Pengambilan Keputusan Berbasis Data

                                                    </h4>


                                                    <p class="about-three__mission-vission-text">

                                                        Mendukung Government Intelligence PBJ
                                                        melalui data kompetensi, statistik pembelajaran,
                                                        analisis kebutuhan SDM, dan informasi strategis
                                                        pengadaan.

                                                    </p>


                                                </div>

                                            </li>


                                        </ul>


                                    </div>


                                </div>

                                <!--tab-->


                            </div>


                        </div>


                    </div>


                </div>

            </div>


        </div>
    </div>
</section>
<!--About Three End-->
<!--About Three End-->

<!--Why Choose Three Start-->
<!--Why Choose Three Start-->
<section class="why-choose-three">
    <div class="why-choose-three__shape-1 img-bounce"></div>

    <div class="container">

        <div class="row">


            <div class="col-xl-6">


                <div class="why-choose-three__left">


                    <div class="section-title-two text-left sec-title-animation animation-style2">


                        <div class="section-title-two__tagline-box">

                            <div class="section-title-two__tagline-shape">
                                <img src="<?= base_url('assets/main/') ?>images/shapes/section-title-two-shape-1.png" alt="">
                            </div>


                            <span class="section-title-two__tagline">
                                Mengapa BegawiPBJ
                            </span>


                        </div>


                        <h2 class="section-title-two__title title-animation">

                            Satu Platform Untuk
                            Ekosistem PBJ
                            <span>Terintegrasi</span>

                        </h2>


                    </div>



                    <p class="why-choose-three__text-1">

                        BegawiPBJ menghubungkan pembelajaran,
                        regulasi, pengetahuan, komunikasi,
                        sertifikasi, dan analitik PBJ dalam satu
                        platform digital yang aman, terstruktur,
                        dan berbasis peran.

                    </p>



                    <div class="why-choose-three__ceo-speech-box">


                        <div class="why-choose-three__ceo-speech">


                            <div class="why-choose-three__ceo-img-two">

                                <img src="<?= base_url('assets/main/') ?>images/resources/why-choose-three-ceo-img-two.jpg" alt="">

                            </div>



                            <div class="why-choose-three__ceo-details">


                                <div class="why-choose-three__ceo-img">

                                    <img src="<?= base_url('assets/main/') ?>images/resources/why-choose-three-ceo-img.jpg" alt="">

                                </div>



                                <div class="why-choose-three__ceo-content">

                                    <p>PENGEMBANG PLATFORM</p>

                                    <h5>
                                        BegawiPBJ
                                    </h5>

                                </div>


                            </div>




                            <p class="why-choose-three__ceo-text">

                                BegawiPBJ hadir sebagai infrastruktur
                                digital untuk memperkuat kapasitas,
                                transparansi, dan kolaborasi dalam
                                Pengadaan Barang/Jasa Pemerintah
                                Provinsi Lampung.

                            </p>



                        </div>


                        <div class="why-choose-three__ceo-speech-border"></div>


                    </div>


                </div>


            </div>




            <div class="col-xl-6">


                <div class="why-choose-three__right">


                    <div class="row">



                        <div class="col-xl-6 col-lg-6 col-md-6">


                            <ul class="list-unstyled why-choose-three__feature">


                                <li>

                                    <div class="why-choose-three__feature-single">


                                        <div class="why-choose-three__feature-img">

                                            <img src="<?= base_url('assets/main/') ?>images/icon/why-choose-three-feature-1.png" alt="">

                                        </div>



                                        <div class="why-choose-three__feature-content">


                                            <h4 class="why-choose-three__feature-title">

                                                Pembelajaran Terstruktur

                                            </h4>



                                            <p class="why-choose-three__feature-text">

                                                Program pembelajaran dirancang
                                                berdasarkan kebutuhan kompetensi,
                                                kelas, materi, assessment,
                                                dan progres peserta.

                                            </p>


                                        </div>


                                    </div>


                                </li>




                                <li>

                                    <div class="why-choose-three__feature-single">


                                        <div class="why-choose-three__feature-img">

                                            <img src="<?= base_url('assets/main/') ?>images/icon/why-choose-three-feature-2.png" alt="">

                                        </div>



                                        <div class="why-choose-three__feature-content">


                                            <h4 class="why-choose-three__feature-title">

                                                Akses Pembelajaran Digital

                                            </h4>



                                            <p class="why-choose-three__feature-text">

                                                Peserta dapat mengakses materi,
                                                aktivitas kelas, dan informasi
                                                kompetensi melalui sistem digital
                                                yang mudah digunakan.

                                            </p>


                                        </div>


                                    </div>


                                </li>



                            </ul>


                        </div>





                        <div class="col-xl-6 col-lg-6 col-md-6">


                            <ul class="list-unstyled why-choose-three__feature why-choose-three__feature--two">



                                <li>


                                    <div class="why-choose-three__feature-single">


                                        <div class="why-choose-three__feature-img">

                                            <img src="<?= base_url('assets/main/') ?>images/icon/why-choose-three-feature-3.png" alt="">

                                        </div>



                                        <div class="why-choose-three__feature-content">


                                            <h4 class="why-choose-three__feature-title">

                                                Assessment dan Evaluasi

                                            </h4>



                                            <p class="why-choose-three__feature-text">

                                                Mendukung evaluasi melalui
                                                quiz, assignment, penilaian,
                                                feedback peserta, serta proses
                                                kelulusan pembelajaran.

                                            </p>


                                        </div>


                                    </div>


                                </li>





                                <li>


                                    <div class="why-choose-three__feature-single">


                                        <div class="why-choose-three__feature-img">

                                            <img src="<?= base_url('assets/main/') ?>images/icon/why-choose-three-feature-4.png" alt="">

                                        </div>



                                        <div class="why-choose-three__feature-content">


                                            <h4 class="why-choose-three__feature-title">

                                                Government Intelligence PBJ

                                            </h4>



                                            <p class="why-choose-three__feature-text">

                                                Menyediakan informasi strategis
                                                mengenai kompetensi SDM,
                                                statistik pembelajaran,
                                                dan kondisi ekosistem PBJ.

                                            </p>


                                        </div>


                                    </div>


                                </li>



                            </ul>


                        </div>



                    </div>


                </div>


            </div>



        </div>

    </div>

</section>
<!--Why Choose Three End-->
<!--Why Choose Three End-->


<!-- Counter One Start -->
<section class="counter-one">

    <div class="counter-one__bg" style="background-image: url(<?= base_url('assets/main/') ?>images/backgrounds/counter-one-bg.jpg);">

        <div class="counter-one__video-link">

            <div class="counter-one__video-shape-1">
                <img src="<?= base_url('assets/main/') ?>images/shapes/counter-one-video-shape-1.png" alt="">
            </div>


            <a href="https://www.youtube.com/" class="video-popup">

                <div class="counter-one__video-icon">

                    <span class="icon-play"></span>

                    <i class="ripple"></i>

                </div>

            </a>

        </div>

    </div>



    <div class="counter-one__shape-1"
        style="background-image: url(<?= base_url('assets/main/') ?>images/shapes/counter-one-shape-1.png);">
    </div>



    <div class="container">

        <div class="row">

            <div class="col-xl-8">

                <div class="counter-one__left">


                    <ul class="counter-one__list list-unstyled">


                        <li>

                            <div class="counter-one__count-hover-img"
                                style="background-image: url(<?= base_url('assets/main/') ?>images/resources/counter-one-single-hover-img.jpg);">
                            </div>


                            <div class="counter-one__count count-box">

                                <h3 class="count-text" data-stop="10" data-speed="1500">
                                    00
                                </h3>

                                <span>k</span>

                            </div>


                            <p>
                                Peserta Pembelajaran
                            </p>


                        </li>




                        <li>

                            <div class="counter-one__count-hover-img"
                                style="background-image: url(<?= base_url('assets/main/') ?>images/resources/counter-one-single-hover-img.jpg);">
                            </div>


                            <div class="counter-one__count count-box">

                                <h3 class="count-text" data-stop="50" data-speed="1500">
                                    00
                                </h3>

                                <span>+</span>

                            </div>


                            <p>
                                Program dan Course
                            </p>


                        </li>




                        <li>

                            <div class="counter-one__count-hover-img"
                                style="background-image: url(<?= base_url('assets/main/') ?>images/resources/counter-one-single-hover-img.jpg);">
                            </div>


                            <div class="counter-one__count count-box">

                                <h3 class="count-text" data-stop="15" data-speed="1500">
                                    00
                                </h3>

                                <span>+</span>

                            </div>


                            <p>
                                Modul Pengetahuan PBJ
                            </p>


                        </li>



                    </ul>


                </div>

            </div>

        </div>

    </div>

</section>
<!-- Counter One End -->


<!--Testimonial Two Start -->
<!--Testimonial Two Start -->
<section class="testimonial-two">

    <div class="testimonial-two__shape-6">
        <img src="<?= base_url('assets/main/') ?>images/shapes/testimonial-two-shape-6.png" alt="">
    </div>


    <div class="testimonial-two__shape-1">

        <img src="<?= base_url('assets/main/') ?>images/shapes/testimonial-two-shape-1.png" alt="">


        <div class="testimonial-two__shape-icon-1">

            <img src="<?= base_url('assets/main/') ?>images/icon/testimonial-two-shape-icon-1.png"
                alt="" class="zoom-fade">

        </div>


        <div class="testimonial-two__shape-img-2">

            <img src="<?= base_url('assets/main/') ?>images/testimonial/testimonial-two-shape-img-2.jpg" alt="">

        </div>


        <div class="testimonial-two__shape-img-3 img-bounce">

            <img src="<?= base_url('assets/main/') ?>images/testimonial/testimonial-two-shape-img-3.jpg" alt="">

        </div>


    </div>



    <div class="testimonial-two__shape-2">

        <img src="<?= base_url('assets/main/') ?>images/shapes/testimonial-two-shape-2.png" alt="">


        <div class="testimonial-two__shape-icon-2 float-bob-y">

            <img src="<?= base_url('assets/main/') ?>images/icon/testimonial-two-shape-icon-2.png" alt="">

        </div>


        <div class="testimonial-two__shape-img-1 zoom-fade">

            <img src="<?= base_url('assets/main/') ?>images/testimonial/testimonial-two-shape-img-1.jpg" alt="">

        </div>


    </div>



    <div class="testimonial-two__shape-3 float-bob-x">

        <img src="<?= base_url('assets/main/') ?>images/shapes/testimonial-two-shape-3.png" alt="">

    </div>


    <div class="testimonial-two__shape-4"></div>

    <div class="testimonial-two__shape-5"></div>



    <div class="container">


        <div class="testimonial-two__inner">


            <div class="testimonial-two__quote">

                <span class="icon-quote"></span>

            </div>



            <div class="testimonial-two__slider">



                <div class="testimonial-two__main-content">


                    <div class="testimonial-two__carousel owl-carousel owl-theme">



                        <!-- Item 1 -->

                        <div class="testimonial-two__item">


                            <div class="testimonial-two__inner-content">


                                <div class="testimonial-two__rating">

                                    <i class="icon-star"></i>
                                    <i class="icon-star"></i>
                                    <i class="icon-star"></i>
                                    <i class="icon-star"></i>
                                    <i class="icon-star"></i>

                                </div>



                                <p class="testimonial-two__text">

                                    BegawiPBJ membantu memperluas akses pembelajaran
                                    dan informasi Pengadaan Barang/Jasa melalui satu
                                    platform digital yang terintegrasi.

                                </p>




                                <div class="testimonial-two__client-info">


                                    <div class="testimonial-two__client-logo">

                                        <img src="<?= base_url('assets/main/') ?>images/testimonial/testimonial-two-client-logo.png"
                                            alt="">

                                    </div>



                                    <div class="testimonial-two__client-name-box">


                                        <h4 class="testimonial-two__client-name">

                                            <a href="testimonials.php">
                                                Peserta Pembelajaran PBJ
                                            </a>

                                        </h4>



                                        <p class="testimonial-two__client-sub-title">

                                            Pengguna BegawiPBJ

                                        </p>


                                    </div>


                                </div>


                            </div>


                        </div>





                        <!-- Item 2 -->


                        <div class="testimonial-two__item">


                            <div class="testimonial-two__inner-content">


                                <div class="testimonial-two__rating">

                                    <i class="icon-star"></i>
                                    <i class="icon-star"></i>
                                    <i class="icon-star"></i>
                                    <i class="icon-star"></i>
                                    <i class="icon-star"></i>

                                </div>



                                <p class="testimonial-two__text">

                                    Repository regulasi dan pusat pengetahuan
                                    memberikan kemudahan dalam mendapatkan
                                    referensi PBJ yang terpercaya dan terstruktur.

                                </p>




                                <div class="testimonial-two__client-info">


                                    <div class="testimonial-two__client-logo">

                                        <img src="<?= base_url('assets/main/') ?>images/testimonial/testimonial-two-client-logo.png"
                                            alt="">

                                    </div>



                                    <div class="testimonial-two__client-name-box">


                                        <h4 class="testimonial-two__client-name">

                                            <a href="testimonials.php">
                                                Pengguna Publik
                                            </a>

                                        </h4>



                                        <p class="testimonial-two__client-sub-title">

                                            Ekosistem PBJ

                                        </p>


                                    </div>


                                </div>


                            </div>


                        </div>





                        <!-- Item 3 -->


                        <div class="testimonial-two__item">


                            <div class="testimonial-two__inner-content">


                                <div class="testimonial-two__rating">

                                    <i class="icon-star"></i>
                                    <i class="icon-star"></i>
                                    <i class="icon-star"></i>
                                    <i class="icon-star"></i>
                                    <i class="icon-star"></i>

                                </div>




                                <p class="testimonial-two__text">

                                    Platform digital ini mendukung peningkatan
                                    kompetensi dan kolaborasi antar pemangku
                                    kepentingan Pengadaan Barang/Jasa.

                                </p>




                                <div class="testimonial-two__client-info">


                                    <div class="testimonial-two__client-logo">

                                        <img src="<?= base_url('assets/main/') ?>images/testimonial/testimonial-two-client-logo.png"
                                            alt="">

                                    </div>




                                    <div class="testimonial-two__client-name-box">


                                        <h4 class="testimonial-two__client-name">

                                            <a href="testimonials.php">
                                                Mitra BegawiPBJ
                                            </a>

                                        </h4>




                                        <p class="testimonial-two__client-sub-title">

                                            Pemangku Kepentingan PBJ

                                        </p>



                                    </div>



                                </div>



                            </div>



                        </div>




                    </div>


                </div>
                <div class="testimonial-two__thumb-outer-box">


                    <div class="testimonial-two__thumb-carousel owl-carousel owl-theme">


                        <div class="testimonial-two__thumb-item">

                            <div class="testimonial-two__img-holder-box">


                                <div class="testimonial-two__img-holder">

                                    <img src="<?= base_url('assets/main/') ?>images/testimonial/testimonial-2-1.jpg" alt="">

                                </div>


                            </div>


                        </div>




                        <div class="testimonial-two__thumb-item">


                            <div class="testimonial-two__img-holder-box">


                                <div class="testimonial-two__img-holder">


                                    <img src="<?= base_url('assets/main/') ?>images/testimonial/testimonial-2-2.jpg" alt="">


                                </div>


                            </div>


                        </div>




                        <div class="testimonial-two__thumb-item">


                            <div class="testimonial-two__img-holder-box">


                                <div class="testimonial-two__img-holder">


                                    <img src="<?= base_url('assets/main/') ?>images/testimonial/testimonial-2-3.jpg" alt="">


                                </div>


                            </div>


                        </div>



                    </div>


                </div>


            </div>


        </div>


    </div>


</section>
<!--Testimonial Two End -->
<!--Testimonial Two End -->

<!-- Sliding Text Three Start-->
<section class="sliding-text-three sliding-text-four">

    <div class="sliding-text-three__wrap">


        <ul class="sliding-text-three__list list-unstyled marquee_mode">


            <li>

                <h2 data-hover="Pembelajaran PBJ"
                    class="sliding-text-three__title count-box">

                    Pembelajaran PBJ

                    <img src="<?= base_url('assets/main/') ?>images/shapes/sliding-text-three-shape-1.png" alt="">

                </h2>

            </li>



            <li>

                <h2 data-hover="Regulasi PBJ"
                    class="sliding-text-three__title count-box">

                    Regulasi PBJ

                    <img src="<?= base_url('assets/main/') ?>images/shapes/sliding-text-three-shape-1.png" alt="">

                </h2>

            </li>



            <li>

                <h2 data-hover="Bunga Rampai"
                    class="sliding-text-three__title count-box">

                    Bunga Rampai

                    <img src="<?= base_url('assets/main/') ?>images/shapes/sliding-text-three-shape-1.png" alt="">

                </h2>

            </li>



            <li>

                <h2 data-hover="Sertifikat Digital"
                    class="sliding-text-three__title count-box">

                    Sertifikat Digital

                    <img src="<?= base_url('assets/main/') ?>images/shapes/sliding-text-three-shape-1.png" alt="">

                </h2>

            </li>



            <li>

                <h2 data-hover="Government Intelligence PBJ"
                    class="sliding-text-three__title count-box">

                    Government Intelligence PBJ

                    <img src="<?= base_url('assets/main/') ?>images/shapes/sliding-text-three-shape-1.png" alt="">

                </h2>

            </li>



        </ul>


    </div>


</section>
<!-- Sliding Text Three End -->



<!--Newsletter Two Start -->

<section class="newsletter-two newsletter-three">

    <div class="container">


        <div class="newsletter-two__inner">


            <div class="newsletter-two__img">

                <img src="<?= base_url('assets/main/') ?>images/resources/newsletter-two-img-1.png" alt="">

            </div>



            <div class="newsletter-two__inner-content">


                <div class="newsletter-two__shape-bg"
                    style="background-image: url(<?= base_url('assets/main/') ?>images/shapes/newsletter-two-shape-bg.png);">
                </div>



                <div class="newsletter-two__like">

                    <img src="<?= base_url('assets/main/') ?>images/shapes/newsletter-two-like.png" alt="">

                </div>



                <div class="newsletter-two__title-box">


                    <h3 class="newsletter-two__title">

                        Informasi <span>PBJ Terbaru</span>

                    </h3>



                    <p class="newsletter-two__text">

                        Dapatkan informasi terbaru mengenai pembelajaran,
                        regulasi, agenda, dan perkembangan Pengadaan
                        Barang/Jasa melalui BegawiPBJ.

                    </p>


                </div>




                <div class="newsletter-two__form-box">


                    <form class="newsletter-two__form">


                        <div class="newsletter-two__input">

                            <input type="email" placeholder="Masukkan Email Anda">

                        </div>



                        <button type="submit" class="newsletter-two__btn">

                            <span class="icon-arrow-circle"></span>

                            Berlangganan

                        </button>


                    </form>


                </div>


            </div>


        </div>


    </div>


</section>

<!--Newsletter Two End -->




<!--Contact Info One Start -->

<section class="contact-info-one">


    <div class="container">


        <ul class="list-unstyled contact-info-one__list">


            <li>


                <div class="contact-info-one__single">


                    <div class="contact-info-one__icon">

                        <span class="icon-envelope"></span>

                    </div>



                    <div class="contact-info-one__content">


                        <p class="contact-info-one__sub-title">
                            Email Address:
                        </p>



                        <h5 class="contact-info-one__email">

                            <a href="mailto:info@begawipbj.go.id">

                                info@begawipbj.go.id

                            </a>

                        </h5>


                    </div>


                </div>


            </li>





            <li>


                <div class="contact-info-one__single">


                    <div class="contact-info-one__icon">

                        <span class="icon-phone"></span>

                    </div>



                    <div class="contact-info-one__content">


                        <p class="contact-info-one__sub-title">
                            Phone Number
                        </p>



                        <h5 class="contact-info-one__email">

                            Pemerintah Provinsi Lampung

                        </h5>


                    </div>


                </div>


            </li>





            <li>


                <div class="contact-info-one__single">


                    <div class="contact-info-one__icon">

                        <span class="icon-location"></span>

                    </div>



                    <div class="contact-info-one__content">


                        <p class="contact-info-one__sub-title">
                            Our Address
                        </p>



                        <h5 class="contact-info-one__email">

                            Provinsi Lampung, Indonesia

                        </h5>


                    </div>


                </div>


            </li>



        </ul>


    </div>


</section>

<!--Contact Info One End -->


<?= $this->endSection() ?>