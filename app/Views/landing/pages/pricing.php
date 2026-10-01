<?php $landingParts = APPPATH . 'Views' . DIRECTORY_SEPARATOR . 'landing' . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR; ?>
<?php $head_title = "Pricing || Begawipbj || Begawipbj PHP Template" ?>
<?php require_once($landingParts . 'layout/top-layout.php'); ?>
<!-- header -->
<?php require_once($landingParts . 'header/header.php'); ?>
<?php
$page_title = "Pricing";
require_once($landingParts . 'page_header.php');

?>

<!--Pricing One Start -->
<section class="pricing-one pricing-page">
    <div class="container">
        <div class="section-title-two text-center sec-title-animation animation-style1">
            <div class="section-title-two__tagline-box">
                <div class="section-title-two__tagline-shape">
                    <img src="<?= base_url('assets/landing/') ?>images/shapes/section-title-two-shape-1.png" alt="">
                </div>
                <span class="section-title-two__tagline">Our Pricing</span>
            </div>
            <h2 class="section-title-two__title title-animation">Your Source for Ideas, <br>Insights, and
                <span>Inspiration</span>
            </h2>
        </div>
        <div class="row">
            <!--Pricing One Single Start -->
            <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInLeft" data-wow-delay="100ms">
                <div class="pricing-one__single">
                    <h4 class="pricing-one__package-name"><a href="#">Basic</a></h4>
                    <div class="pricing-one__price-box">
                        <h3>$15</h3>
                        <p>Month</p>
                    </div>
                    <ul class="list-unstyled pricing-one__feature">
                        <li>5 Courses</li>
                        <li>10 Videos</li>
                        <li>15 Audios</li>
                        <li>Enhanced Security</li>
                        <li>Limited Access</li>
                    </ul>
                    <div class="pricing-one__btn-box">
                        <a href="pricing.php" class="thm-btn"><span
                                class="icon-angles-right"></span>Subscribe</a>
                    </div>
                </div>
            </div>
            <!--Pricing One Single End -->
            <!--Pricing One Single Start -->
            <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInLeft" data-wow-delay="200ms">
                <div class="pricing-one__single">
                    <h4 class="pricing-one__package-name"><a href="#">Standard</a></h4>
                    <div class="pricing-one__price-box">
                        <h3>$60</h3>
                        <p>Month</p>
                    </div>
                    <ul class="list-unstyled pricing-one__feature">
                        <li>5 Courses</li>
                        <li>10 Videos</li>
                        <li>15 Audios</li>
                        <li>Enhanced Security</li>
                        <li>Limited Access</li>
                    </ul>
                    <div class="pricing-one__btn-box">
                        <a href="pricing.php" class="thm-btn"><span
                                class="icon-angles-right"></span>Subscribe</a>
                    </div>
                </div>
            </div>
            <!--Pricing One Single End -->
            <!--Pricing One Single Start -->
            <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInRight" data-wow-delay="300ms">
                <div class="pricing-one__single">
                    <h4 class="pricing-one__package-name"><a href="#">Premium</a></h4>
                    <div class="pricing-one__price-box">
                        <h3>$70</h3>
                        <p>Month</p>
                    </div>
                    <ul class="list-unstyled pricing-one__feature">
                        <li>5 Courses</li>
                        <li>10 Videos</li>
                        <li>15 Audios</li>
                        <li>Enhanced Security</li>
                        <li>Limited Access</li>
                    </ul>
                    <div class="pricing-one__btn-box">
                        <a href="pricing.php" class="thm-btn"><span
                                class="icon-angles-right"></span>Subscribe</a>
                    </div>
                </div>
            </div>
            <!--Pricing One Single End -->
            <!--Pricing One Single Start -->
            <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInRight" data-wow-delay="400ms">
                <div class="pricing-one__single">
                    <h4 class="pricing-one__package-name"><a href="#">Ultimate</a></h4>
                    <div class="pricing-one__price-box">
                        <h3>$80</h3>
                        <p>Month</p>
                    </div>
                    <ul class="list-unstyled pricing-one__feature">
                        <li>5 Courses</li>
                        <li>10 Videos</li>
                        <li>15 Audios</li>
                        <li>Enhanced Security</li>
                        <li>Limited Access</li>
                    </ul>
                    <div class="pricing-one__btn-box">
                        <a href="pricing.php" class="thm-btn"><span
                                class="icon-angles-right"></span>Subscribe</a>
                    </div>
                </div>
            </div>
            <!--Pricing One Single End -->
        </div>
    </div>
</section>
<!--Pricing One End -->

<!--Newsletter Two Start -->
<section class="newsletter-two">
    <div class="container">
        <div class="newsletter-two__inner">
            <div class="newsletter-two__img">
                <img src="<?= base_url('assets/landing/') ?>images/resources/newsletter-two-img-1.png" alt="">
            </div>
            <div class="newsletter-two__inner-content">
                <div class="newsletter-two__shape-bg"
                    style="background-image: url(<?= base_url('assets/landing/') ?>images/shapes/newsletter-two-shape-bg.png);"></div>
                <div class="newsletter-two__like">
                    <img src="<?= base_url('assets/landing/') ?>images/shapes/newsletter-two-like.png" alt="">
                </div>
                <div class="newsletter-two__title-box">
                    <h3 class="newsletter-two__title">Subscribe our <span>Newsletter</span></h3>
                    <p class="newsletter-two__text">Explore a diverse selection of courses all in one platform,
                        <br>
                        designed to cater to various learning
                    </p>
                </div>
                <div class="newsletter-two__form-box">
                    <form class="newsletter-two__form">
                        <div class="newsletter-two__input">
                            <input type="email" placeholder="Enter Your Email">
                        </div>
                        <button type="submit" class="newsletter-two__btn"> <span
                                class="icon-arrow-circle"></span>
                            Subscribe</button>
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
                        <p class="contact-info-one__sub-title">Email Address:</p>
                        <h5 class="contact-info-one__email"><a
                                href="mailto:info@example.com">info@example.com</a></h5>
                    </div>
                </div>
            </li>
            <li>
                <div class="contact-info-one__single">
                    <div class="contact-info-one__icon">
                        <span class="icon-phone"></span>
                    </div>
                    <div class="contact-info-one__content">
                        <p class="contact-info-one__sub-title">Phone Number</p>
                        <h5 class="contact-info-one__email"><a href="tel:001239957689">+00 123 (99) 57689</a>
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
                        <p class="contact-info-one__sub-title">Our Address</p>
                        <h5 class="contact-info-one__email">1234 Elm Street Springfield,</h5>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</section>
<!--Contact Info One End -->


<?php require_once($landingParts . 'footer/footer_two.php'); ?>
<?php require_once($landingParts . 'layout/bottom-layout.php'); ?>