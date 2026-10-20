<?php $landingParts = APPPATH . 'Views' . DIRECTORY_SEPARATOR . 'landing' . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR; ?>
<?php $head_title = "Become a Teacher || Begawipbj || Begawipbj PHP Template" ?>
<?php require_once($landingParts . 'layout/top-layout.php'); ?>
<!-- header -->
<?php require_once($landingParts . 'header/header.php'); ?>
<?php
$page_title = "Become a Teacher";
require_once($landingParts . 'page_header.php');

?>
<!--Become A Teacher Start-->
<section class="become-a-teacher">
    <div class="container">
        <div class="become-a-teacher__top">
            <div class="section-title-two text-center sec-title-animation animation-style1">
                <div class="section-title-two__tagline-box">
                    <div class="section-title-two__tagline-shape">
                        <img src="<?= base_url('assets/landing/') ?>images/shapes/section-title-two-shape-1.png" alt="">
                    </div>
                    <span class="section-title-two__tagline">Instructor</span>
                </div>
                <h2 class="section-title-two__title title-animation">Apply As
                    <span>Instructor</span>
                </h2>
            </div>
            <div class="become-a-teacher__tab-box tabs-box">
                <ul class="tab-buttons clearfix list-unstyled">
                    <li data-tab="#become-an-intructor" class="tab-btn active-btn"><span>Become an
                            Intructor.</span></li>
                    <li data-tab="#intructor-rules" class="tab-btn"><span>Intructor Rules.</span></li>
                    <li data-tab="#start-courses" class="tab-btn"><span>Start with courses.</span></li>
                </ul>
                <div class="tabs-content">
                    <!--tab-->
                    <div class="tab active-tab" id="become-an-intructor">
                        <div class="become-a-teacher__content">
                            <p>Meet the talented individuals who bring our vision to life every day. With a
                                shared passion and commitment, our team works tirelessly to deliver exceptional
                                quality and innovation. Discover the stories, skills, and dedication that make
                                us who we are. and dedication that make
                                us who we are.</p>
                        </div>
                    </div>
                    <!--tab-->
                    <!--tab-->
                    <div class="tab" id="intructor-rules">
                        <div class="become-a-teacher__content">
                            <p>Meet the talented individuals who bring our vision to life every day. With a
                                shared passion and commitment, our team works tirelessly to deliver exceptional
                                quality and innovation. Discover the stories, skills, and dedication that make
                                us who we are. and dedication that make
                                us who we are.</p>
                        </div>
                    </div>
                    <!--tab-->
                    <!--tab-->
                    <div class="tab" id="start-courses">
                        <div class="become-a-teacher__content">
                            <p>Meet the talented individuals who bring our vision to life every day. With a
                                shared passion and commitment, our team works tirelessly to deliver exceptional
                                quality and innovation. Discover the stories, skills, and dedication that make
                                us who we are. and dedication that make
                                us who we are.</p>
                        </div>
                    </div>
                    <!--tab-->
                </div>
            </div>
        </div>
        <div class="become-a-teacher__bottom">
            <div class="row">
                <div class="col-xl-4">
                    <div class="become-a-teacher__img-box">
                        <div class="become-a-teacher__img">
                            <img src="<?= base_url('assets/landing/') ?>images/resources/become-a-teacher-img-1.jpg" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-xl-8">
                    <div class="become-a-teacher__right">
                        <div class="section-title-two text-left sec-title-animation animation-style1">
                            <div class="section-title-two__tagline-box">
                                <div class="section-title-two__tagline-shape">
                                    <img src="<?= base_url('assets/landing/') ?>images/shapes/section-title-two-shape-1.png" alt="">
                                </div>
                                <span class="section-title-two__tagline">For Become an Intructor</span>
                            </div>
                            <h2 class="section-title-two__title title-animation">Instructor Registration
                            </h2>
                        </div>
                        <form class="contact-form-validated contact-three__form"
                            action="<?= base_url('assets/landing/') ?>inc/sendemail.php" method="post" novalidate="novalidate">
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
                                        <input type="email" name="email" placeholder="jhon@doman.com"
                                            required="">
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6">
                                    <h4 class="contact-three__input-title">User</h4>
                                    <div class="contact-three__input-box">
                                        <input type="text" name="Phone" placeholder="user" required="">
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6">
                                    <h4 class="contact-three__input-title">Phone</h4>
                                    <div class="contact-three__input-box">
                                        <input type="text" name="Phone" placeholder="Phone" required="">
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6">
                                    <h4 class="contact-three__input-title">Password</h4>
                                    <div class="contact-three__input-box">
                                        <input type="text" name="Phone" placeholder="Password" required="">
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6">
                                    <h4 class="contact-three__input-title">Password Confirmation</h4>
                                    <div class="contact-three__input-box">
                                        <input type="text" name="Phone" placeholder="Password Confirmation"
                                            required="">
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <h4 class="contact-three__input-title">Bio</h4>
                                    <div class="contact-three__input-box text-message-box">
                                        <textarea name="message" placeholder="Write Your Message"></textarea>
                                    </div>
                                    <div class="contact-three__btn-box">
                                        <button type="submit"
                                            class="thm-btn-two contact-three__btn"><span>Become a
                                                Instructor</span><i class="icon-angles-right"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="result"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Become A Teacher End-->





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