<?php $landingParts = APPPATH . 'Views' . DIRECTORY_SEPARATOR . 'landing' . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR; ?>
<?php $head_title = "Event Details || Begawipbj || Begawipbj PHP Template" ?>
<?php require_once($landingParts . 'layout/top-layout.php'); ?>
<!-- header -->
<?php require_once($landingParts . 'header/header.php'); ?>
<?php
$page_title = "Event Details";
require_once($landingParts . 'page_header.php');

?>

<!--Event Details Start-->
<section class="event-details">
    <div class="container">
        <div class="event-details__top">
            <div class="event-details__top-img">
                <img src="<?= base_url('assets/landing/') ?>images/event/event-details-top-img-1.jpg" alt="">
            </div>
        </div>
        <div class="event-details__content-box">
            <div class="row">
                <div class="col-xl-8 col-lg-7">
                    <div class="event-details__content-left">
                        <h3 class="event-details__content-title">About The Event</h3>
                        <p class="event-details__content-text-1">Meet the talented individuals who bring our
                            vision to life every day. With a shared passion and commitment, our team works
                            tirelessly to deliver exceptional quality and innovation. Discover the stories,
                            skills, and dedication that make us who we are.</p>
                        <ul class="list-unstyled event-details__point">
                            <li>
                                <div class="icon">
                                    <span class="icon-angles-right"></span>
                                </div>
                                <div class="content">
                                    <h5>Motivational Keynote Speakers:</h5>
                                    <p>Meet the talented individuals who bring our vision to life every day.
                                        With a shared passion and commitment, our team works tirelessly to
                                        deliver exceptional quality and innovation.</p>
                                </div>
                            </li>
                            <li>
                                <div class="icon">
                                    <span class="icon-angles-right"></span>
                                </div>
                                <div class="content">
                                    <h5>Interactive Workshops:</h5>
                                    <p>Meet the talented individuals who bring our vision to life every day.
                                        With a shared passion and commitment, our team works tirelessly to
                                        deliver exceptional quality and innovation.</p>
                                </div>
                            </li>
                            <li>
                                <div class="icon">
                                    <span class="icon-angles-right"></span>
                                </div>
                                <div class="content">
                                    <h5>Networking Opportunities:</h5>
                                    <p>Meet the talented individuals who bring our vision to life every day.
                                        With a shared passion and commitment, our team works tirelessly to
                                        deliver exceptional quality and innovation.</p>
                                </div>
                            </li>
                            <li>
                                <div class="icon">
                                    <span class="icon-angles-right"></span>
                                </div>
                                <div class="content">
                                    <h5>Registration:</h5>
                                    <p>Meet the talented individuals who bring our vision to life every day.
                                        With a shared passion and commitment, our team works tirelessly to
                                        deliver exceptional quality and innovation.</p>
                                </div>
                            </li>
                            <li>
                                <div class="icon">
                                    <span class="icon-angles-right"></span>
                                </div>
                                <div class="content">
                                    <h5>Motivational Keynote Speakers:</h5>
                                    <p>Meet the talented individuals who bring our vision to life every day.
                                        With a shared passion and commitment, our team works tirelessly to
                                        deliver exceptional quality and innovation.</p>
                                </div>
                            </li>
                            <li>
                                <div class="icon">
                                    <span class="icon-angles-right"></span>
                                </div>
                                <div class="content">
                                    <h5>Motivational Keynote Speakers:</h5>
                                    <p>Meet the talented individuals who bring our vision to life every day.
                                        With a shared passion and commitment, our team works tirelessly to
                                        deliver exceptional quality and innovation.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-5">
                    <div class="event-details__sidebar">
                        <h3 class="event-details__sidebar-title">Event Information</h3>
                        <ul class="list-unstyled event-details__list">
                            <li>
                                <div class="event-details__list-left">
                                    <div class="event-details__list-icon">
                                        <span class="icon-catagory"></span>
                                    </div>
                                    <p class="event-details__list-text">Cost:</p>
                                </div>
                                <div class="event-details__list-right">
                                    <h2>$50.20</h2>
                                </div>
                            </li>
                            <li>
                                <div class="event-details__list-left">
                                    <div class="event-details__list-icon">
                                        <span class="icon-calendar"></span>
                                    </div>
                                    <p class="event-details__list-text">Date:</p>
                                </div>
                                <div class="event-details__list-right">
                                    <p>May 10, 2025</p>
                                </div>
                            </li>
                            <li>
                                <div class="event-details__list-left">
                                    <div class="event-details__list-icon">
                                        <span class="icon-stamp"></span>
                                    </div>
                                    <p class="event-details__list-text">Total Slot:</p>
                                </div>
                                <div class="event-details__list-right">
                                    <p>22</p>
                                </div>
                            </li>
                            <li>
                                <div class="event-details__list-left">
                                    <div class="event-details__list-icon">
                                        <span class="icon-book"></span>
                                    </div>
                                    <p class="event-details__list-text">Booked Slot:</p>
                                </div>
                                <div class="event-details__list-right">
                                    <p>0</p>
                                </div>
                            </li>
                        </ul>
                        <div class="event-details__btn-box">
                            <a href="contact.php" class="thm-btn"><span class="icon-angles-right"></span>Book
                                Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="event-details__location-box">
            <h4 class="event-details__location-title">The Event Location</h4>
            <div class="row">
                <div class="col-xl-8 col-lg-7">
                    <div class="event-details__location-map">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4562.753041141002!2d-118.80123790098536!3d34.152323469614075!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80e82469c2162619%3A0xba03efb7998eef6d!2sCostco+Wholesale!5e0!3m2!1sbn!2sbd!4v1562518641290!5m2!1sbn!2sbd"
                            class="event-details__location-map-box" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-5">
                    <div class="event-details__location-venue">
                        <h4 class="event-details__location-venue-title">Event Venue</h4>
                        <ul class=" list-unstyled event-details__location-venue-list">
                            <li>
                                <div class="event-details__location-venue-left">
                                    <p>Venue</p>
                                </div>
                                <div class="event-details__location-venue-right">
                                    <p> Washington, USA.</p>
                                </div>
                            </li>
                            <li>
                                <div class="event-details__location-venue-left">
                                    <p>Location</p>
                                </div>
                                <div class="event-details__location-venue-right">
                                    <p>4517 Washington Ave.</p>
                                </div>
                            </li>
                            <li>
                                <div class="event-details__location-venue-left">
                                    <p>Phone Number</p>
                                </div>
                                <div class="event-details__location-venue-right">
                                    <p><a href="tel:10221457441">(+1) 0 221 457 441</a></p>
                                </div>
                            </li>
                            <li>
                                <div class="event-details__location-venue-left">
                                    <p>Web Site</p>
                                </div>
                                <div class="event-details__location-venue-right">
                                    <p><a href="#">http://www.begawipbj.work</a></p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Event Details End-->

<!--Related Event Start-->
<section class="related-event">
    <div class="container">
        <div class="related-event__inner">
            <h3 class="related-event__title">Related Event</h3>
            <div class="related-event__carousel owl-theme owl-carousel">
                <!--Event One Single Start -->
                <div class="item">
                    <div class="event-one__single">
                        <div class="event-one__img">
                            <img src="<?= base_url('assets/landing/') ?>images/event/event-1-1.jpg" alt="">
                            <div class="event-one__date">
                                <p>25</p>
                                <span>Dec</span>
                            </div>
                        </div>
                        <div class="event-one__content">
                            <p class="event-one__time"> <span class="icon-clock"></span> 7:00 pm - 9:00 pm</p>
                            <h4 class="event-one__title"><a href="event-details.php">Graphic Design
                                    Essentials From Concept to Creation</a></h4>
                            <div class="event-one__location">
                                <div class="event-one__location-icon">
                                    <span class="icon-location"></span>
                                </div>
                                <p class="event-one__loation-text">1234 Elm Street, Springfield, IL
                                    62704</p>
                            </div>
                            <div class="event-one__btn-box">
                                <a href="#" class="thm-btn"><span class="icon-angles-right"></span>Subscribe</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Event One Single End -->
                <!--Event One Single Start -->
                <div class="item">
                    <div class="event-one__single">
                        <div class="event-one__img">
                            <img src="<?= base_url('assets/landing/') ?>images/event/event-1-2.jpg" alt="">
                            <div class="event-one__date">
                                <p>25</p>
                                <span>Dec</span>
                            </div>
                        </div>
                        <div class="event-one__content">
                            <p class="event-one__time"> <span class="icon-clock"></span> 7:00 pm - 9:00 pm</p>
                            <h4 class="event-one__title"><a href="event-details.php">Innovative Teaching
                                    Strategies
                                    for
                                    the 21st Century Classroom</a></h4>
                            <div class="event-one__location">
                                <div class="event-one__location-icon">
                                    <span class="icon-location"></span>
                                </div>
                                <p class="event-one__loation-text">1234 Elm Street, Springfield, IL
                                    62704</p>
                            </div>
                            <div class="event-one__btn-box">
                                <a href="#" class="thm-btn"><span class="icon-angles-right"></span>Subscribe</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Event One Single End -->
                <!--Event One Single Start -->
                <div class="item">
                    <div class="event-one__single">
                        <div class="event-one__img">
                            <img src="<?= base_url('assets/landing/') ?>images/event/event-1-3.jpg" alt="">
                            <div class="event-one__date">
                                <p>25</p>
                                <span>Dec</span>
                            </div>
                        </div>
                        <div class="event-one__content">
                            <p class="event-one__time"> <span class="icon-clock"></span> 7:00 pm - 9:00 pm</p>
                            <h4 class="event-one__title"><a href="event-details.php">Role of Social-Emotional
                                    Learning
                                    in Student Success</a></h4>
                            <div class="event-one__location">
                                <div class="event-one__location-icon">
                                    <span class="icon-location"></span>
                                </div>
                                <p class="event-one__loation-text">1234 Elm Street, Springfield, IL
                                    62704</p>
                            </div>
                            <div class="event-one__btn-box">
                                <a href="#" class="thm-btn"><span class="icon-angles-right"></span>Subscribe</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Event One Single End -->
                <!--Event One Single Start -->
                <div class="item">
                    <div class="event-one__single">
                        <div class="event-one__img">
                            <img src="<?= base_url('assets/landing/') ?>images/event/event-1-4.jpg" alt="">
                            <div class="event-one__date">
                                <p>25</p>
                                <span>Dec</span>
                            </div>
                        </div>
                        <div class="event-one__content">
                            <p class="event-one__time"> <span class="icon-clock"></span> 7:00 pm - 9:00 pm</p>
                            <h4 class="event-one__title"><a href="event-details.php">Key Features and
                                    Considerations
                                    for Educational Institutions</a></h4>
                            <div class="event-one__location">
                                <div class="event-one__location-icon">
                                    <span class="icon-location"></span>
                                </div>
                                <p class="event-one__loation-text">1234 Elm Street, Springfield, IL
                                    62704</p>
                            </div>
                            <div class="event-one__btn-box">
                                <a href="#" class="thm-btn"><span class="icon-angles-right"></span>Subscribe</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Event One Single End -->
                <!--Event One Single Start -->
                <div class="item">
                    <div class="event-one__single">
                        <div class="event-one__img">
                            <img src="<?= base_url('assets/landing/') ?>images/event/event-1-5.jpg" alt="">
                            <div class="event-one__date">
                                <p>25</p>
                                <span>Dec</span>
                            </div>
                        </div>
                        <div class="event-one__content">
                            <p class="event-one__time"> <span class="icon-clock"></span> 7:00 pm - 9:00 pm</p>
                            <h4 class="event-one__title"><a href="event-details.php">Key Features and
                                    Considerations
                                    for Educational Institutions</a></h4>
                            <div class="event-one__location">
                                <div class="event-one__location-icon">
                                    <span class="icon-location"></span>
                                </div>
                                <p class="event-one__loation-text">1234 Elm Street, Springfield, IL
                                    62704</p>
                            </div>
                            <div class="event-one__btn-box">
                                <a href="#" class="thm-btn"><span class="icon-angles-right"></span>Subscribe</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Event One Single End -->
                <!--Event One Single Start -->
                <div class="item">
                    <div class="event-one__single">
                        <div class="event-one__img">
                            <img src="<?= base_url('assets/landing/') ?>images/event/event-1-6.jpg" alt="">
                            <div class="event-one__date">
                                <p>25</p>
                                <span>Dec</span>
                            </div>
                        </div>
                        <div class="event-one__content">
                            <p class="event-one__time"> <span class="icon-clock"></span> 7:00 pm - 9:00 pm</p>
                            <h4 class="event-one__title"><a href="event-details.php">Unlocking the Future of
                                    Education: How LMS Transforms Learning </a></h4>
                            <div class="event-one__location">
                                <div class="event-one__location-icon">
                                    <span class="icon-location"></span>
                                </div>
                                <p class="event-one__loation-text">1234 Elm Street, Springfield, IL
                                    62704</p>
                            </div>
                            <div class="event-one__btn-box">
                                <a href="#" class="thm-btn"><span class="icon-angles-right"></span>Subscribe</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Event One Single End -->
            </div>
        </div>
    </div>
</section>
<!--Related Event End-->



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