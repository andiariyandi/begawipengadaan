<?php $landingParts = APPPATH . 'Views' . DIRECTORY_SEPARATOR . 'landing' . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR; ?>
<?php $head_title = "Contact || Begawipbj || Begawipbj PHP Template" ?>
<?php require_once($landingParts . 'layout/top-layout.php'); ?>
<!-- header -->
<?php require_once($landingParts . 'header/header.php'); ?>
<?php
$page_title = "Contact";
require_once($landingParts . 'page_header.php');

?>

<!--Contact Two Start-->
<section class="contact-two">
    <div class="container">
        <ul class="row list-unstyled">
            <li class="col-xl-3 col-lg-6 col-md-6 wow fadeInLeft" data-wow-delay="100ms">
                <div class="contact-two__single">
                    <div class="contact-two__icon">
                        <img src="<?= base_url('assets/landing/') ?>images/icon/contact-two-icon-1.png" alt="">
                    </div>
                    <h3 class="contact-two__title">Our Address</h3>
                    <p>Â 3149 New Creek Road, Huntsville,<br> Alabama, USA</p>
                </div>
            </li>
            <li class="col-xl-3 col-lg-6 col-md-6 wow fadeInLeft" data-wow-delay="200ms">
                <div class="contact-two__single">
                    <div class="contact-two__icon">
                        <img src="<?= base_url('assets/landing/') ?>images/icon/contact-two-icon-2.png" alt="">
                    </div>
                    <h3 class="contact-two__title">Contact Number</h3>
                    <p>Â <a href="tel:1200123456789">+12 (00) 123 456789</a></p>
                    <p>Â <a href="tel:9100012458963">+91 (000) 1245 8963</a></p>
                </div>
            </li>
            <li class="col-xl-3 col-lg-6 col-md-6 wow fadeInRight" data-wow-delay="300ms">
                <div class="contact-two__single">
                    <div class="contact-two__icon">
                        <img src="<?= base_url('assets/landing/') ?>images/icon/contact-two-icon-3.png" alt="">
                    </div>
                    <h3 class="contact-two__title">Email Addresss</h3>
                    <p>Â <a href="info@domain.com">info@domain.com</a></p>
                    <p>Â <a href="mailto:support@domain.com">support@domain.com</a></p>
                </div>
            </li>
            <li class="col-xl-3 col-lg-6 col-md-6 wow fadeInRight" data-wow-delay="400ms">
                <div class="contact-two__single">
                    <div class="contact-two__icon">
                        <img src="<?= base_url('assets/landing/') ?>images/icon/contact-two-icon-4.png" alt="">
                    </div>
                    <h3 class="contact-two__title">Class Schedule</h3>
                    <p>Â 10:00 AM - 6:00 PM
                        <br> Monday - Friday
                    </p>
                </div>
            </li>
        </ul>
    </div>
</section>
<!--Contact Two End-->

<!--Contact Three Start-->
<section class="contact-three">
    <div class="container">
        <div class="row">
            <div class="col-xl-6 col-lg-6">
                <div class="contact-three__left">
                    <div class="contact-three__img">
                        <img src="<?= base_url('assets/landing/') ?>images/resources/contact-three-img-1.jpg" alt="">
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="contact-three__right">
                    <div class="section-title-two text-left sec-title-animation animation-style1">
                        <div class="section-title-two__tagline-box">
                            <div class="section-title-two__tagline-shape">
                                <img src="<?= base_url('assets/landing/') ?>images/shapes/section-title-two-shape-1.png" alt="">
                            </div>
                            <span class="section-title-two__tagline">Get in Touch</span>
                        </div>
                        <h2 class="section-title-two__title title-animation">Weâ€™re Here to Help and Ready to
                            Hear from You
                        </h2>
                    </div>
                    <form class="contact-form-validated contact-three__form" action="<?= base_url('assets/landing/') ?>inc/sendemail.php"
                        method="post">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6">
                                <h4 class="contact-three__input-title">Full Name</h4>
                                <div class="contact-three__input-box">
                                    <input type="text" name="name" placeholder="Jhon Doe" required="">
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6">
                                <h4 class="contact-three__input-title">Email Address *</h4>
                                <div class="contact-three__input-box">
                                    <input type="email" name="email" placeholder="jhon@doman.com" required="">
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <h4 class="contact-three__input-title">Subject *</h4>
                                <div class="contact-three__input-box">
                                    <input type="text" name="subject" placeholder="write about your enquiry"
                                        required="">
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <h4 class="contact-three__input-title">Message *</h4>
                                <div class="contact-three__input-box text-message-box">
                                    <textarea name="message" placeholder="Write Your Message" required></textarea>
                                </div>
                                <div class="contact-three__btn-box">
                                    <button type="submit" class="thm-btn-two contact-three__btn"><span>Submit
                                            Review</span><i class="icon-angles-right"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="result"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Contact Three End-->



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