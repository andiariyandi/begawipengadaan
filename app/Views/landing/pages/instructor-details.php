<?php $landingParts = APPPATH . 'Views' . DIRECTORY_SEPARATOR . 'landing' . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR; ?>
<?php $head_title = "Instructor Details || Begawipbj || Begawipbj PHP Template" ?>
<?php require_once($landingParts . 'layout/top-layout.php'); ?>
<!-- header -->
<?php require_once($landingParts . 'header/header.php'); ?>
<?php
$page_title = "Instructor Details";
require_once($landingParts . 'page_header.php');

?>

<!--Team Details Start-->
<section class="team-details">
    <div class="container">
        <div class="row">
            <div class="col-xl-5">
                <div class="team-details__left wow slideInLeft" data-wow-delay="100ms"
                    data-wow-duration="2500ms">
                    <div class="team-details__img">
                        <img src="<?= base_url('assets/landing/') ?>images/team/team-details-img-1.jpg" alt="">
                    </div>
                </div>
            </div>
            <div class="col-xl-7">
                <div class="team-details__right">
                    <div class="team-details__name-and-ratting-box">
                        <div class="team-details__name-box">
                            <h3 class="team-details__name">Jordan Walk</h3>
                            <p class="team-details__sub-title">Sr. Python Developer</p>
                        </div>
                        <div class="team-details__ratting-box">
                            <ul class="team-details__ratting list-unstyled">
                                <li>
                                    <span class="icon-star"></span>
                                </li>
                                <li>
                                    <span class="icon-star"></span>
                                </li>
                                <li>
                                    <span class="icon-star"></span>
                                </li>
                                <li>
                                    <span class="icon-star"></span>
                                </li>
                                <li>
                                    <span class="icon-star"></span>
                                </li>
                            </ul>
                            <p class="team-details__ratting-text">(5.0 / 4.2 Rating)</p>
                        </div>
                    </div>
                    <ul class="team-details__meta list-unstyled">
                        <li>
                            <div class="icon">
                                <span class="fas fa-users"></span>
                            </div>
                            <div class="content">
                                <p class="odometer" data-count="200">00</p>
                                <span>+ Student Trinned</span>
                            </div>
                        </li>
                        <li>
                            <div class="icon">
                                <span class="far fa-play-circle"></span>
                            </div>
                            <div class="content">
                                <p class="odometer" data-count="76">00</p>
                                <span> Courses</span>
                            </div>
                        </li>
                        <li>
                            <div class="icon">
                                <span class="fas fa-graduation-cap"></span>
                            </div>
                            <div class="content">
                                <p class="odometer" data-count="12">00</p>
                                <span> Years Experiance</span>
                            </div>
                        </li>
                    </ul>
                    <h3 class="team-details__title">About Me</h3>
                    <p class="team-details__text-1">Jordan Walk is an experienced Senior Python Developer with a
                        strong focus on building scalable and efficient solutions.<br>
                        With extensive knowledge in Python and backend development, Jordan has a track record of
                        leading successful projects, optimizing performance, and implementing best practices.
                    </p>
                    <div class="team-details__points-box">
                        <h3 class="team-details__points-title">Education</h3>
                        <ul class="team-details__points-list list-unstyled">
                            <li>
                                <div class="team-details__points-content">
                                    <div class="team-details__points-icon">
                                        <img src="<?= base_url('assets/landing/') ?>images/icon/team-details-points-icon.png" alt="">
                                    </div>
                                    <p class="team-details__points-text">Bachelorâ€™s Degree in Computer Science
                                        <br> or a Related Field
                                    </p>
                                </div>
                                <p>Oxford University</p>
                            </li>
                            <li>
                                <div class="team-details__points-content">
                                    <div class="team-details__points-icon">
                                        <img src="<?= base_url('assets/landing/') ?>images/icon/team-details-points-icon.png" alt="">
                                    </div>
                                    <p class="team-details__points-text">Optional Advanced Degree<br>
                                        Masterâ€™s or Certificate Program</p>
                                </div>
                                <p>Oxford University</p>
                            </li>
                        </ul>
                    </div>
                    <div class="team-details__have-question-and-social">
                        <div class="team-details__have-question">
                            <div class="team-details__have-question-icon">
                                <span class="icon-contact"></span>
                            </div>
                            <div class="team-details__have-question-content">
                                <p>Have a Questions</p>
                                <h5><a href="mailto:Jordan@domain.com">Jordan@domain.com</a></h5>
                            </div>
                        </div>
                        <div class="team-details__have-question-social">
                            <a href="#"><span class="fab fa-linkedin-in"></span></a>
                            <a href="#"><span class="fab fa-pinterest-p"></span></a>
                            <a href="#"><span class="fab fa-facebook-f"></span></a>
                            <a href="#"><span class="fab fa-instagram"></span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Team Details End-->

<!--Skill One Start-->
<section class="skill-one">
    <div class="skill-one__shape-2"></div>
    <div class="skill-one__shape-3"></div>
    <div class="container">
        <div class="row">
            <div class="col-xl-6">
                <div class="skill-one__left">
                    <div class="section-title-two text-left sec-title-animation animation-style1">
                        <div class="section-title-two__tagline-box">
                            <div class="section-title-two__tagline-shape">
                                <img src="<?= base_url('assets/landing/') ?>images/shapes/section-title-two-shape-1.png" alt="">
                            </div>
                            <span class="section-title-two__tagline">Skills</span>
                        </div>
                        <h2 class="section-title-two__title title-animation">My Skills & Experties <br>About
                            Digital
                            <span>Technology</span>
                        </h2>
                    </div>
                    <p class="skill-one__text">A deep understanding of Python, including core concepts, advanced
                        topics, libraries (e.g., Pandas, NumPy, Flask), and best practices for code optimization
                        and debugging, is essential. </p>
                    <ul class="skill-one__progress-list list-unstyled">
                        <li>
                            <div class="skill-one__progress">
                                <h4 class="skill-one__progress-title">Python Programming Expertise</h4>
                                <div class="bar">
                                    <div class="bar-inner count-bar" data-percent="80%">
                                        <div class="count-text">80%</div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="skill-one__progress">
                                <h4 class="skill-one__progress-title">Data Analytics & Research</h4>
                                <div class="bar">
                                    <div class="bar-inner count-bar" data-percent="60%">
                                        <div class="count-text">45%</div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="skill-one__progress">
                                <h4 class="skill-one__progress-title">Software Development</h4>
                                <div class="bar">
                                    <div class="bar-inner count-bar" data-percent="90%">
                                        <div class="count-text">90%</div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="skill-one__class-btn-box">
                        <a href="contact.php" class="thm-btn-two">
                            <span>Get Resume</span>
                            <i class="icon-angles-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="skill-one__right wow slideInRight" data-wow-delay="100ms"
                    data-wow-duration="2500ms">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="skill-one__img-1">
                                <img src="<?= base_url('assets/landing/') ?>images/resources/skill-one-img-1.jpg" alt="">
                            </div>
                            <div class="skill-one__experience-box">
                                <div class="skill-one__experience-box-inner">
                                    <div class="skill-one__experience-icon">
                                        <img src="<?= base_url('assets/landing/') ?>images/icon/skill-one-experience-icon.png" alt="">
                                    </div>
                                    <div class="skill-one__experience-count-box">
                                        <div class="skill-one__experience-count">
                                            <h3 class="odometer" data-count="25">00</h3>
                                            <span>+</span>
                                            <p>Years</p>
                                        </div>
                                        <p>of experience</p>
                                    </div>
                                </div>
                                <div class="skill-one__experience-box-shape"></div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="skill-one__img-2">
                                <img src="<?= base_url('assets/landing/') ?>images/resources/skill-one-img-2.jpg" alt="">
                                <div class="skill-one__shape-1 text-rotate-box">
                                    <img src="<?= base_url('assets/landing/') ?>images/shapes/skill-one-shape-1.png" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Skill One End-->


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