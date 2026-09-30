<?php $landingParts = APPPATH . 'Views' . DIRECTORY_SEPARATOR . 'landing' . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR; ?>
<?php $head_title = "Course Grid || Begawipbj || Begawipbj PHP Template" ?>
<?php require_once($landingParts . 'layout/top-layout.php'); ?>
<!-- header -->
<?php require_once($landingParts . 'header/header.php'); ?>
<?php
$page_title = "Course Grid";
require_once($landingParts . 'page_header.php');

?>

<!--Course Grid Start-->
<section class="course-grid">
    <div class="container">
        <div class="row">
            <div class="col-xl-4 col-lg-5">
                <div class="course-grid__left">
                    <div class="course-grid__sidebar">
                        <div class="course-grid__search course-grid__single">
                            <div class="course-grid__title-box">
                                <h3 class="course-grid__title">Search Now</h3>
                                <div class="course-grid__title-shape-1">
                                    <img src="<?= base_url('assets/landing/') ?>images/shapes/course-grid-title-shape-1.png" alt="">
                                </div>
                            </div>
                            <p class="course-grid__search-text">With the release of Letraset sheets containi
                                Lorem Ipsum passages</p>
                            <form action="#">
                                <input type="search" placeholder="Find by Categories">
                                <button type="submit"><i class="icon-search"></i>Search</button>
                            </form>
                        </div>
                        <div class="course-grid__price-filter course-grid__single">
                            <div class="course-grid__title-box">
                                <h3 class="course-grid__title">Price Filter</h3>
                                <div class="course-grid__title-shape-1">
                                    <img src="<?= base_url('assets/landing/') ?>images/shapes/course-grid-title-shape-1.png" alt="">
                                </div>
                            </div>
                            <div class="course-grid__price-filter-free-and-paid-course">
                                <label class="custom-radio">
                                    <input type="radio" name="myRadios" value="1" checked>
                                    <span class="radio-dot"></span>
                                    <span class="radio-text">Paid Course</span>
                                </label>
                                <label class="custom-radio">
                                    <input type="radio" name="myRadios" value="2">
                                    <span class="radio-dot"></span>
                                    <span class="radio-text">Free Course</span>
                                </label>
                            </div>
                            <div class="course-grid__price-filter-ranger">
                                <p class="course-grid__price-filter-title">Price: $89 - $65</p>
                                <div class="price-ranger">
                                    <div id="slider-range"></div>
                                    <div class="ranger-min-max-block">
                                        <input type="text" readonly class="min">
                                        <span>-</span>
                                        <input type="text" readonly class="max">
                                        <input type="submit" value="Apply">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="course-grid__categories course-grid__single">
                            <div class="course-grid__title-box">
                                <h3 class="course-grid__title">Categories</h3>
                                <div class="course-grid__title-shape-1">
                                    <img src="<?= base_url('assets/landing/') ?>images/shapes/course-grid-title-shape-1.png" alt="">
                                </div>
                            </div>
                            <ul class="list-unstyled course-grid__list-item">
                                <li>
                                    <div class="course-grid__list-check"></div>
                                    <p class="course-grid__list-text">Accounting & Finace (12)</p>
                                </li>
                                <li>
                                    <div class="course-grid__list-check"></div>
                                    <p class="course-grid__list-text">Programming & Tech (25)</p>
                                </li>
                                <li>
                                    <div class="course-grid__list-check"></div>
                                    <p class="course-grid__list-text">Art & Design (59)</p>
                                </li>
                                <li>
                                    <div class="course-grid__list-check"></div>
                                    <p class="course-grid__list-text">Health & Fitness (24)</p>
                                </li>
                                <li>
                                    <div class="course-grid__list-check"></div>
                                    <p class="course-grid__list-text">Sales & Marketing (40)</p>
                                </li>
                                <li>
                                    <div class="course-grid__list-check"></div>
                                    <p class="course-grid__list-text">User Research (40)</p>
                                </li>
                                <li>
                                    <div class="course-grid__list-check"></div>
                                    <p class="course-grid__list-text">Business Development (30)</p>
                                </li>
                            </ul>
                        </div>
                        <div class="course-grid__skill course-grid__single">
                            <div class="course-grid__title-box">
                                <h3 class="course-grid__title">Skills Level</h3>
                                <div class="course-grid__title-shape-1">
                                    <img src="<?= base_url('assets/landing/') ?>images/shapes/course-grid-title-shape-1.png" alt="">
                                </div>
                            </div>
                            <ul class="list-unstyled course-grid__list-item">
                                <li>
                                    <div class="course-grid__list-check"></div>
                                    <p class="course-grid__list-text">All Level (290)</p>
                                </li>
                                <li>
                                    <div class="course-grid__list-check"></div>
                                    <p class="course-grid__list-text">Intermediate (230)</p>
                                </li>
                                <li>
                                    <div class="course-grid__list-check"></div>
                                    <p class="course-grid__list-text">Beginner (40)</p>
                                </li>
                                <li>
                                    <div class="course-grid__list-check"></div>
                                    <p class="course-grid__list-text">Free Seminar (300)</p>
                                </li>
                                <li>
                                    <div class="course-grid__list-check"></div>
                                    <p class="course-grid__list-text">Professional (50)</p>
                                </li>
                                <li>
                                    <div class="course-grid__list-check"></div>
                                    <p class="course-grid__list-text">Advanced (30)</p>
                                </li>
                            </ul>
                        </div>
                        <div class="course-grid__discount course-grid__single">
                            <div class="course-grid__discount-shape-bg"
                                style="background-image: url(<?= base_url('assets/landing/') ?>images/shapes/course-grid-discount-shape-bg.png);">
                            </div>
                            <h4 class="course-grid__discount-title">20% Discount</h4>
                            <p class="course-grid__discount-text">is simply dummy text of the printing <br> and
                                typesetting industry</p>
                            <div class="course-grid__discount-img">
                                <img src="<?= base_url('assets/landing/') ?>images/resources/course-grid-discount-img-1.png" alt="">
                            </div>
                            <div class="course-grid__discount-coupon">
                                <p>Use Coupon</p>
                                <h5>#FuStudy56</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8 col-lg-7">
                <div class="course-grid__right">
                    <div class="course-list__right-top">
                        <p class="course-list__right-top-text">Showing 12 Courses of 60</p>
                        <div class="course-list__right-top-btn">
                            <a href="#"><span class="icon-border"></span></a>
                            <a href="#"><span class="icon-menu"></span></a>
                        </div>
                        <div class="course-list__showing-sort">
                            <p>Sort by: </p>
                            <div class="select-box">
                                <select class="wide">
                                    <option data-display="Last Features">Last Features</option>
                                    <option value="1">Sort by popular</option>
                                    <option value="2">Sort by Price</option>
                                    <option value="3">Sort by Ratings</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="course-grid__right-content-box">
                        <div class="row">
                            <!--Courses Two Single Start-->
                            <div class="col-xl-6">
                                <div class="courses-two__single">
                                    <div class="courses-two__img-box">
                                        <div class="courses-two__img">
                                            <img src="<?= base_url('assets/landing/') ?>images/resources/courses-2-1.jpg" alt="">
                                        </div>
                                        <div class="courses-two__heart">
                                            <a href="course-details.php"><span class="icon-heart"></span></a>
                                        </div>
                                    </div>
                                    <div class="courses-two__content">
                                        <div class="courses-two__doller-and-review">
                                            <div class="courses-two__doller">
                                                <p>$240.00</p>
                                            </div>
                                            <div class="courses-two__review">
                                                <p><i class="icon-star"></i> 4.5 <span>(129 Reviews)</span></p>
                                            </div>
                                        </div>
                                        <h3 class="courses-two__title"><a href="course-details.php">Getting
                                                Started with
                                                Computers and Beginner's Guide to Basic Skills</a></h3>
                                        <div class="courses-two__btn-and-client-box">
                                            <div class="courses-two__btn-box">
                                                <a href="course-details.php" class="thm-btn-two">
                                                    <span>Enroll Now</span>
                                                    <i class="icon-angles-right"></i>
                                                </a>
                                            </div>
                                            <div class="courses-two__client-box">
                                                <div class="courses-two__client-img">
                                                    <img src="<?= base_url('assets/landing/') ?>images/resources/courses-two-client-img-1.jpg"
                                                        alt="">
                                                </div>
                                                <div class="courses-two__client-content">
                                                    <h4>Sarah Alison</h4>
                                                    <p><span class="odometer" data-count="12">00</span><i>+</i>
                                                        Years Experian</p>
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="courses-two__meta list-unstyled">
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-chart-simple"></span>
                                                </div>
                                                <p>Beginner</p>
                                            </li>
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-book"></span>
                                                </div>
                                                <p>45 Lesson</p>
                                            </li>
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-clock"></span>
                                                </div>
                                                <p>620h, 55min</p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!--Courses Two Single End-->
                            <!--Courses Two Single Start-->
                            <div class="col-xl-6">
                                <div class="courses-two__single">
                                    <div class="courses-two__img-box">
                                        <div class="courses-two__img">
                                            <img src="<?= base_url('assets/landing/') ?>images/resources/courses-2-2.jpg" alt="">
                                        </div>
                                        <div class="courses-two__heart">
                                            <a href="course-details.php"><span class="icon-heart"></span></a>
                                        </div>
                                    </div>
                                    <div class="courses-two__content">
                                        <div class="courses-two__doller-and-review">
                                            <div class="courses-two__doller">
                                                <p>$120.00</p>
                                            </div>
                                            <div class="courses-two__review">
                                                <p><i class="icon-star"></i> 4.5 <span>(129 Reviews)</span></p>
                                            </div>
                                        </div>
                                        <h3 class="courses-two__title"><a href="course-details.php">Cooking
                                                Made Easy: Essential
                                                Skills for Everyday Meals</a></h3>
                                        <div class="courses-two__btn-and-client-box">
                                            <div class="courses-two__btn-box">
                                                <a href="course-details.php" class="thm-btn-two">
                                                    <span>Enroll Now</span>
                                                    <i class="icon-angles-right"></i>
                                                </a>
                                            </div>
                                            <div class="courses-two__client-box">
                                                <div class="courses-two__client-img">
                                                    <img src="<?= base_url('assets/landing/') ?>images/resources/courses-two-client-img-2.jpg"
                                                        alt="">
                                                </div>
                                                <div class="courses-two__client-content">
                                                    <h4>Mitchel March</h4>
                                                    <p><span class="odometer" data-count="20">00</span><i>+</i>
                                                        Years Experian</p>
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="courses-two__meta list-unstyled">
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-chart-simple"></span>
                                                </div>
                                                <p>Advance</p>
                                            </li>
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-book"></span>
                                                </div>
                                                <p>120 Lesson</p>
                                            </li>
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-clock"></span>
                                                </div>
                                                <p>12h, 55min</p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!--Courses Two Single End-->
                            <!--Courses Two Single Start-->
                            <div class="col-xl-6">
                                <div class="courses-two__single">
                                    <div class="courses-two__img-box">
                                        <div class="courses-two__img">
                                            <img src="<?= base_url('assets/landing/') ?>images/resources/courses-2-3.jpg" alt="">
                                        </div>
                                        <div class="courses-two__heart">
                                            <a href="course-details.php"><span class="icon-heart"></span></a>
                                        </div>
                                    </div>
                                    <div class="courses-two__content">
                                        <div class="courses-two__doller-and-review">
                                            <div class="courses-two__doller">
                                                <p>$640.00</p>
                                            </div>
                                            <div class="courses-two__review">
                                                <p><i class="icon-star"></i> 4.5 <span>(129 Reviews)</span></p>
                                            </div>
                                        </div>
                                        <h3 class="courses-two__title"><a href="course-details.php">Graphic
                                                Design Basics: Learn
                                                the Foundations of Visual </a></h3>
                                        <div class="courses-two__btn-and-client-box">
                                            <div class="courses-two__btn-box">
                                                <a href="course-details.php" class="thm-btn-two">
                                                    <span>Enroll Now</span>
                                                    <i class="icon-angles-right"></i>
                                                </a>
                                            </div>
                                            <div class="courses-two__client-box">
                                                <div class="courses-two__client-img">
                                                    <img src="<?= base_url('assets/landing/') ?>images/resources/courses-two-client-img-3.jpg"
                                                        alt="">
                                                </div>
                                                <div class="courses-two__client-content">
                                                    <h4>Mr. Harry</h4>
                                                    <p><span class="odometer" data-count="5">00</span><i>+</i>
                                                        Years Experian</p>
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="courses-two__meta list-unstyled">
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-chart-simple"></span>
                                                </div>
                                                <p>Entry Level</p>
                                            </li>
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-book"></span>
                                                </div>
                                                <p>45 Lesson</p>
                                            </li>
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-clock"></span>
                                                </div>
                                                <p>620h, 55min</p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!--Courses Two Single End-->
                            <!--Courses Two Single Start-->
                            <div class="col-xl-6">
                                <div class="courses-two__single">
                                    <div class="courses-two__img-box">
                                        <div class="courses-two__img">
                                            <img src="<?= base_url('assets/landing/') ?>images/resources/courses-2-4.jpg" alt="">
                                        </div>
                                        <div class="courses-two__heart">
                                            <a href="course-details.php"><span class="icon-heart"></span></a>
                                        </div>
                                    </div>
                                    <div class="courses-two__content">
                                        <div class="courses-two__doller-and-review">
                                            <div class="courses-two__doller">
                                                <p>$350.00</p>
                                            </div>
                                            <div class="courses-two__review">
                                                <p><i class="icon-star"></i> 4.5 <span>(129 Reviews)</span></p>
                                            </div>
                                        </div>
                                        <h3 class="courses-two__title"><a href="course-details.php">Photography
                                                for Everyone: How
                                                to Capture Stunning Photos with Ease</a></h3>
                                        <div class="courses-two__btn-and-client-box">
                                            <div class="courses-two__btn-box">
                                                <a href="course-details.php" class="thm-btn-two">
                                                    <span>Enroll Now</span>
                                                    <i class="icon-angles-right"></i>
                                                </a>
                                            </div>
                                            <div class="courses-two__client-box">
                                                <div class="courses-two__client-img">
                                                    <img src="<?= base_url('assets/landing/') ?>images/resources/courses-two-client-img-4.jpg"
                                                        alt="">
                                                </div>
                                                <div class="courses-two__client-content">
                                                    <h4>Alisa Olivia</h4>
                                                    <p><span class="odometer" data-count="8">00</span><i>+</i>
                                                        Years Experian</p>
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="courses-two__meta list-unstyled">
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-chart-simple"></span>
                                                </div>
                                                <p>Advance</p>
                                            </li>
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-book"></span>
                                                </div>
                                                <p>80 Lesson</p>
                                            </li>
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-clock"></span>
                                                </div>
                                                <p>156h, 55min</p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!--Courses Two Single End-->
                            <!--Courses Two Single Start-->
                            <div class="col-xl-6">
                                <div class="courses-two__single">
                                    <div class="courses-two__img-box">
                                        <div class="courses-two__img">
                                            <img src="<?= base_url('assets/landing/') ?>images/resources/courses-2-5.jpg" alt="">
                                        </div>
                                        <div class="courses-two__heart">
                                            <a href="course-details.php"><span class="icon-heart"></span></a>
                                        </div>
                                    </div>
                                    <div class="courses-two__content">
                                        <div class="courses-two__doller-and-review">
                                            <div class="courses-two__doller">
                                                <p>$150.00</p>
                                            </div>
                                            <div class="courses-two__review">
                                                <p><i class="icon-star"></i> 4.5 <span>(129 Reviews)</span></p>
                                            </div>
                                        </div>
                                        <h3 class="courses-two__title"><a href="course-details.php">Writing
                                                Made Simple: From
                                                Ideas to Finished Pieces for All Levels</a></h3>
                                        <div class="courses-two__btn-and-client-box">
                                            <div class="courses-two__btn-box">
                                                <a href="course-details.php" class="thm-btn-two">
                                                    <span>Enroll Now</span>
                                                    <i class="icon-angles-right"></i>
                                                </a>
                                            </div>
                                            <div class="courses-two__client-box">
                                                <div class="courses-two__client-img">
                                                    <img src="<?= base_url('assets/landing/') ?>images/resources/courses-two-client-img-5.jpg"
                                                        alt="">
                                                </div>
                                                <div class="courses-two__client-content">
                                                    <h4>Jordan Walk</h4>
                                                    <p><span class="odometer" data-count="16">00</span><i>+</i>
                                                        Years Experian</p>
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="courses-two__meta list-unstyled">
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-chart-simple"></span>
                                                </div>
                                                <p>Medium</p>
                                            </li>
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-book"></span>
                                                </div>
                                                <p>45 Lesson</p>
                                            </li>
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-clock"></span>
                                                </div>
                                                <p>326h, 55min</p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!--Courses Two Single End-->
                            <!--Courses Two Single Start-->
                            <div class="col-xl-6">
                                <div class="courses-two__single">
                                    <div class="courses-two__img-box">
                                        <div class="courses-two__img">
                                            <img src="<?= base_url('assets/landing/') ?>images/resources/courses-2-6.jpg" alt="">
                                        </div>
                                        <div class="courses-two__heart">
                                            <a href="course-details.php"><span class="icon-heart"></span></a>
                                        </div>
                                    </div>
                                    <div class="courses-two__content">
                                        <div class="courses-two__doller-and-review">
                                            <div class="courses-two__doller">
                                                <p>$290.00</p>
                                            </div>
                                            <div class="courses-two__review">
                                                <p><i class="icon-star"></i> 4.5 <span>(129 Reviews)</span></p>
                                            </div>
                                        </div>
                                        <h3 class="courses-two__title"><a href="course-details.php"> A
                                                Beginnerâ€™s Guide to
                                                Building Your Online Presence"</a></h3>
                                        <div class="courses-two__btn-and-client-box">
                                            <div class="courses-two__btn-box">
                                                <a href="course-details.php" class="thm-btn-two">
                                                    <span>Enroll Now</span>
                                                    <i class="icon-angles-right"></i>
                                                </a>
                                            </div>
                                            <div class="courses-two__client-box">
                                                <div class="courses-two__client-img">
                                                    <img src="<?= base_url('assets/landing/') ?>images/resources/courses-two-client-img-6.jpg"
                                                        alt="">
                                                </div>
                                                <div class="courses-two__client-content">
                                                    <h4>Aman Ellison</h4>
                                                    <p><span class="odometer" data-count="12">00</span><i>+</i>
                                                        Years Experian</p>
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="courses-two__meta list-unstyled">
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-chart-simple"></span>
                                                </div>
                                                <p>Beginner</p>
                                            </li>
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-book"></span>
                                                </div>
                                                <p>45 Lesson</p>
                                            </li>
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-clock"></span>
                                                </div>
                                                <p>620h, 55min</p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!--Courses Two Single End-->
                        </div>
                    </div>
                    <div class="courses-grid__btn-box">
                        <a href="course-details.php" class="thm-btn-two">
                            <span>More Course</span>
                            <i class="icon-angles-right"></i>
                        </a>
                    </div>
                    <div class="blog-list__pagination">
                        <ul class="pg-pagination list-unstyled">
                            <li class="prev">
                                <a href="#" aria-label="prev"><i class="fas fa-arrow-left"></i></a>
                            </li>
                            <li class="count active"><a href="#">01</a></li>
                            <li class="count"><a href="#">02</a></li>
                            <li class="count"><a href="#">03</a></li>
                            <li class="next">
                                <a href="#" aria-label="Next"><i class="fas fa-arrow-right"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Course Grid End-->

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