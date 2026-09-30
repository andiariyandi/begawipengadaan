<?php $landingParts = APPPATH . 'Views' . DIRECTORY_SEPARATOR . 'landing' . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR; ?>
<?php $head_title = "Course Details || Begawipbj || Begawipbj PHP Template" ?>
<?php require_once($landingParts . 'layout/top-layout.php'); ?>
<!-- header -->
<?php require_once($landingParts . 'header/header.php'); ?>
<?php
$page_title = "Course Details";
require_once($landingParts . 'page_header.php');

?>

<!--Course Details Start-->
<section class="course-details">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-7">
                <div class="course-details__left">
                    <div class="course-details__img">
                        <img src="<?= base_url('assets/landing/') ?>images/resources/course-details-img-1.jpg" alt="">
                    </div>
                    <div class="course-details__content">
                        <div class="course-details__tag-box">
                            <div class="course-details__tag-shape"></div>
                            <span class="course-details__tag">Web Programming</span>
                        </div>
                        <h3 class="course-details__title">Master Python Programming for<br>
                            Beginners and Beyond</h3>
                        <div class="course-details__client-and-ratting-box">
                            <div class="course-details__client-box">
                                <div class="course-details__client-img">
                                    <img src="<?= base_url('assets/landing/') ?>images/resources/course-details-client-img-1.jpg" alt="">
                                </div>
                                <div class="course-details__client-content">
                                    <p><span class="odometer" data-count="20">00</span>+
                                        Years Experiance</p>
                                    <h4>Jordan Walk</h4>
                                </div>
                            </div>
                            <div class="course-details__ratting-box-1">
                                <ul class="course-details__ratting-list-1 list-unstyled">
                                    <li>
                                        <p>Last Update</p>
                                        <h4>January 02, 2025</h4>
                                    </li>
                                    <li>
                                        <p>Available</p>
                                        <h4>24 Students</h4>
                                    </li>
                                    <li>
                                        <p>(5.0 / 4.2 Rating)</p>
                                        <ul class="course-details__ratting list-unstyled">
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
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="course-details__main-tab-box tabs-box">
                            <ul class="tab-buttons list-unstyled">
                                <li data-tab="#overview" class="tab-btn tab-btn-one">
                                    <p><span class="icon-pen-ruler"></span>Overview</p>
                                </li>
                                <li data-tab="#curriculum" class="tab-btn tab-btn-two">
                                    <p><span class="icon-book"></span>Curriculum</p>
                                </li>
                                <li data-tab="#instructor" class="tab-btn tab-btn-three">
                                    <p><span class="icon-graduation-cap"></span>Instructor</p>
                                </li>
                                <li data-tab="#review" class="tab-btn active-btn tab-btn-four">
                                    <p><span class="icon-comments"></span>Review</p>
                                </li>
                            </ul>
                            <div class="tabs-content">
                                <div class="tab" id="overview">
                                    <div class="course-details__tab-inner">
                                        <div class="course-details__overview">
                                            <h3 class="course-details__overview-title">Course Overview</h3>
                                            <p class="course-details__overview-text-1">Master Python Programming
                                                for Beginners and Beyond is a comprehensive course designed to
                                                take you from the fundamentals of Python to advanced topics,
                                                providing you with the skills needed to solve real-world
                                                problems. </p>
                                            <p class="course-details__overview-text-2">Whether you're new to
                                                programming or looking to deepen your Python knowledge, this
                                                course covers essential concepts and hands-on projects to make
                                                you proficient in Python, one of the world's most versatile and
                                                in-demand programming languages.</p>
                                            <h3 class="course-details__overview-title-2">What You Will Learn?
                                            </h3>
                                            <ul class="course-details__overview-points list-unstyled">
                                                <li>
                                                    <div class="course-details__overview-points-icon">
                                                        <span class="fas fa-check"></span>
                                                    </div>
                                                    <div class="course-details__overview-points-content">
                                                        <h5>Python Basics:</h5>
                                                        <p>Understand the fundamentals of Python, including
                                                            syntax, variables, data <br>types, and control
                                                            structures.</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="course-details__overview-points-icon">
                                                        <span class="fas fa-check"></span>
                                                    </div>
                                                    <div class="course-details__overview-points-content">
                                                        <h5>Data Structures:</h5>
                                                        <p>Dive deep into lists, tuples, dictionaries, and sets
                                                            for efficient data storage<br> and manipulation.</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="course-details__overview-points-icon">
                                                        <span class="fas fa-check"></span>
                                                    </div>
                                                    <div class="course-details__overview-points-content">
                                                        <h5>Functions and Modules:</h5>
                                                        <p>Learn to write reusable functions and use Python
                                                            modules for better code <br>organization.</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="course-details__overview-points-icon">
                                                        <span class="fas fa-check"></span>
                                                    </div>
                                                    <div class="course-details__overview-points-content">
                                                        <h5>Object-Oriented Programming (OOP): </h5>
                                                        <p>Grasp OOP principles with Python, including classes,
                                                            objects, inheritance,<br> and encapsulation.</p>
                                                    </div>
                                                </li>
                                            </ul>
                                            <h3 class="course-details__overview-title-3">Requirement?</h3>
                                            <p class="course-details__overview-text-3">Whether you're new to
                                                programming or looking to deepen your Python knowledge, this
                                                course covers essential concepts and hands-on projects to make
                                                you proficient in Python, one of the world's most versatile and
                                                in-demand programming languages.</p>
                                            <div class="course-details__points-box">
                                                <div class="row">
                                                    <div class="col-xl-6">
                                                        <ul class="course-details__points-list-2 list-unstyled">
                                                            <li>
                                                                <div class="course-details__points-list-icon">
                                                                    <img src="<?= base_url('assets/landing/') ?>images/icon/course-details-points-list-icon-1.png"
                                                                        alt="">
                                                                </div>
                                                                <h3 class="course-details__points-list-title">No
                                                                    Prior Coding Experience<br> Required:</h3>
                                                                <p class="course-details__points-list-text">This
                                                                    course is beginner-friendly and<br> does not
                                                                    assume prior programming<br> knowledge.</p>
                                                            </li>
                                                            <li>
                                                                <div class="course-details__points-list-icon">
                                                                    <img src="<?= base_url('assets/landing/') ?>images/icon/course-details-points-list-icon-2.png"
                                                                        alt="">
                                                                </div>
                                                                <h3 class="course-details__points-list-title">
                                                                    Computer with Java<br> Installed:</h3>
                                                                <p class="course-details__points-list-text">A
                                                                    computer with Java installed is<br> capable
                                                                    of running Java applications<br> and
                                                                    applets.</p>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-xl-6">
                                                        <ul class="course-details__points-list-2 list-unstyled">
                                                            <li>
                                                                <div class="course-details__points-list-icon">
                                                                    <img src="<?= base_url('assets/landing/') ?>images/icon/course-details-points-list-icon-3.png"
                                                                        alt="">
                                                                </div>
                                                                <h3 class="course-details__points-list-title">
                                                                    Computer with Python<br> Installed: </h3>
                                                                <p class="course-details__points-list-text">
                                                                    You'll need a computer (Windows,<br> macOS,
                                                                    or Linux) with Python<br> installed. </p>
                                                            </li>
                                                            <li>
                                                                <div class="course-details__points-list-icon">
                                                                    <img src="<?= base_url('assets/landing/') ?>images/icon/course-details-points-list-icon-4.png"
                                                                        alt="">
                                                                </div>
                                                                <h3 class="course-details__points-list-title">
                                                                    Willingness to Learn &<br> Creativity: </h3>
                                                                <p class="course-details__points-list-text">An
                                                                    open mind and enthusiasm for<br> learning
                                                                    are all you need to succeed in<br> this
                                                                    course.</p>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--Tab-->
                                <div class="tab" id="curriculum">
                                    <div class="course-details__tab-inner">
                                        <div class="course-details__curriculam">
                                            <h3 class="course-details__curriculam-title">Course Curriculum</h3>
                                            <p class="course-details__curriculam-text">Through engaging lessons
                                                and hands-on projects, youâ€™ll learn Python fundamentals, data
                                                structures, object-oriented programming, and popular libraries
                                                like NumPy and pandas. Youâ€™ll also tackle real-world
                                                applications such as data analysis and web scraping.</p>
                                            <div class="course-details__curriculam-faq">
                                                <div class="accrodion-grp" data-grp-name="faq-one-accrodion">
                                                    <div class="accrodion">
                                                        <div class="accrodion-title">
                                                            <div class="accrodion-title-box">
                                                                <div class="accrodion-title__count"></div>
                                                                <div class="accrodion-title-text">
                                                                    <h4>What is construction?</h4>
                                                                </div>
                                                            </div>
                                                            <ul class="accrodion-meta list-unstyled">
                                                                <li>
                                                                    <p><span class="icon-book"></span>80 Lesson
                                                                    </p>
                                                                </li>
                                                                <li>
                                                                    <p><span class="icon-clock"></span>62h,
                                                                        20min</p>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="accrodion-content">
                                                            <div class="inner">
                                                                <h3 class="accrodion-content__title">
                                                                    Intermediate</h3>
                                                                <p class="accrodion-content__text">This
                                                                    curriculum offers a structured path
                                                                    with
                                                                    estimated times for each module, allowing
                                                                    students to plan their study schedule
                                                                    effectively.</p>
                                                                <ul
                                                                    class="accrodion-content__points list-unstyled">
                                                                    <li>
                                                                        <p
                                                                            class="accrodion-content__points-text">
                                                                            <span
                                                                                class="fal fa-video"></span>Overview
                                                                            of Python and Its Applications
                                                                        </p>
                                                                        <div
                                                                            class="accrodion-content__points-btn">
                                                                            <a href="#">Preview</a>
                                                                        </div>
                                                                        <div class="accrodion-content__icon">
                                                                            <span
                                                                                class="far fa-lock-alt"></span>
                                                                        </div>
                                                                    </li>
                                                                    <li>
                                                                        <p
                                                                            class="accrodion-content__points-text">
                                                                            <span
                                                                                class="fal fa-video"></span>Overview
                                                                            of Python and Its Applications
                                                                        </p>
                                                                        <div
                                                                            class="accrodion-content__points-btn">
                                                                            <a href="#">Preview</a>
                                                                        </div>
                                                                        <div class="accrodion-content__icon">
                                                                            <span
                                                                                class="far fa-lock-alt"></span>
                                                                        </div>
                                                                    </li>
                                                                    <li>
                                                                        <p
                                                                            class="accrodion-content__points-text">
                                                                            <span
                                                                                class="far fa-microphone"></span>Understanding
                                                                            Variables and Data Types
                                                                        </p>
                                                                        <div class="accrodion-content__icon">
                                                                            <span
                                                                                class="far fa-lock-alt"></span>
                                                                        </div>
                                                                    </li>
                                                                    <li>
                                                                        <p
                                                                            class="accrodion-content__points-text">
                                                                            <span
                                                                                class="far fa-microphone"></span>Tuples:
                                                                            Understanding Immutability and Use
                                                                            Cases
                                                                        </p>
                                                                        <div class="accrodion-content__icon">
                                                                            <span
                                                                                class="far fa-lock-alt"></span>
                                                                        </div>
                                                                    </li>
                                                                    <li>
                                                                        <p
                                                                            class="accrodion-content__points-text">
                                                                            <span
                                                                                class="fal fa-file-alt"></span>
                                                                            Nested Loops and Loop Control
                                                                            Statements (break, continue, pass)
                                                                        </p>
                                                                        <div class="accrodion-content__icon">
                                                                            <span
                                                                                class="far fa-lock-alt"></span>
                                                                        </div>
                                                                    </li>
                                                                </ul>
                                                            </div><!-- /.inner -->
                                                        </div>
                                                    </div>
                                                    <div class="accrodion active">
                                                        <div class="accrodion-title">
                                                            <div class="accrodion-title-box">
                                                                <div class="accrodion-title__count"></div>
                                                                <div class="accrodion-title-text">
                                                                    <h4>What is construction?</h4>
                                                                </div>
                                                            </div>
                                                            <ul class="accrodion-meta list-unstyled">
                                                                <li>
                                                                    <p><span class="icon-book"></span>45 Lesson
                                                                    </p>
                                                                </li>
                                                                <li>
                                                                    <p><span class="icon-clock"></span>62h,
                                                                        20min</p>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="accrodion-content">
                                                            <div class="inner">
                                                                <h3 class="accrodion-content__title">
                                                                    Intermediate</h3>
                                                                <p class="accrodion-content__text">This
                                                                    curriculum offers a structured path
                                                                    with
                                                                    estimated times for each module, allowing
                                                                    students to plan their study schedule
                                                                    effectively.</p>
                                                                <ul
                                                                    class="accrodion-content__points list-unstyled">
                                                                    <li>
                                                                        <p
                                                                            class="accrodion-content__points-text">
                                                                            <span
                                                                                class="fal fa-video"></span>Overview
                                                                            of Python and Its Applications
                                                                        </p>
                                                                        <div
                                                                            class="accrodion-content__points-btn">
                                                                            <a href="#">Preview</a>
                                                                        </div>
                                                                        <div class="accrodion-content__icon">
                                                                            <span
                                                                                class="far fa-lock-alt"></span>
                                                                        </div>
                                                                    </li>
                                                                    <li>
                                                                        <p
                                                                            class="accrodion-content__points-text">
                                                                            <span
                                                                                class="fal fa-video"></span>Overview
                                                                            of Python and Its Applications
                                                                        </p>
                                                                        <div
                                                                            class="accrodion-content__points-btn">
                                                                            <a href="#">Preview</a>
                                                                        </div>
                                                                        <div class="accrodion-content__icon">
                                                                            <span
                                                                                class="far fa-lock-alt"></span>
                                                                        </div>
                                                                    </li>
                                                                    <li>
                                                                        <p
                                                                            class="accrodion-content__points-text">
                                                                            <span
                                                                                class="far fa-microphone"></span>Understanding
                                                                            Variables and Data Types
                                                                        </p>
                                                                        <div class="accrodion-content__icon">
                                                                            <span
                                                                                class="far fa-lock-alt"></span>
                                                                        </div>
                                                                    </li>
                                                                    <li>
                                                                        <p
                                                                            class="accrodion-content__points-text">
                                                                            <span
                                                                                class="far fa-microphone"></span>Tuples:
                                                                            Understanding Immutability and Use
                                                                            Cases
                                                                        </p>
                                                                        <div class="accrodion-content__icon">
                                                                            <span
                                                                                class="far fa-lock-alt"></span>
                                                                        </div>
                                                                    </li>
                                                                    <li>
                                                                        <p
                                                                            class="accrodion-content__points-text">
                                                                            <span
                                                                                class="fal fa-file-alt"></span>
                                                                            Nested Loops and Loop Control
                                                                            Statements (break, continue, pass)
                                                                        </p>
                                                                        <div class="accrodion-content__icon">
                                                                            <span
                                                                                class="far fa-lock-alt"></span>
                                                                        </div>
                                                                    </li>
                                                                </ul>
                                                            </div><!-- /.inner -->
                                                        </div>
                                                    </div>
                                                    <div class="accrodion">
                                                        <div class="accrodion-title">
                                                            <div class="accrodion-title-box">
                                                                <div class="accrodion-title__count"></div>
                                                                <div class="accrodion-title-text">
                                                                    <h4>Control Flow and Loops</h4>
                                                                </div>
                                                            </div>
                                                            <ul class="accrodion-meta list-unstyled">
                                                                <li>
                                                                    <p><span class="icon-book"></span>80 Lesson
                                                                    </p>
                                                                </li>
                                                                <li>
                                                                    <p><span class="icon-clock"></span>62h,
                                                                        20min</p>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="accrodion-content">
                                                            <div class="inner">
                                                                <h3 class="accrodion-content__title">
                                                                    Intermediate</h3>
                                                                <p class="accrodion-content__text">This
                                                                    curriculum offers a structured path
                                                                    with
                                                                    estimated times for each module, allowing
                                                                    students to plan their study schedule
                                                                    effectively.</p>
                                                                <ul
                                                                    class="accrodion-content__points list-unstyled">
                                                                    <li>
                                                                        <p
                                                                            class="accrodion-content__points-text">
                                                                            <span
                                                                                class="fal fa-video"></span>Overview
                                                                            of Python and Its Applications
                                                                        </p>
                                                                        <div
                                                                            class="accrodion-content__points-btn">
                                                                            <a href="#">Preview</a>
                                                                        </div>
                                                                        <div class="accrodion-content__icon">
                                                                            <span
                                                                                class="far fa-lock-alt"></span>
                                                                        </div>
                                                                    </li>
                                                                    <li>
                                                                        <p
                                                                            class="accrodion-content__points-text">
                                                                            <span
                                                                                class="fal fa-video"></span>Overview
                                                                            of Python and Its Applications
                                                                        </p>
                                                                        <div
                                                                            class="accrodion-content__points-btn">
                                                                            <a href="#">Preview</a>
                                                                        </div>
                                                                        <div class="accrodion-content__icon">
                                                                            <span
                                                                                class="far fa-lock-alt"></span>
                                                                        </div>
                                                                    </li>
                                                                    <li>
                                                                        <p
                                                                            class="accrodion-content__points-text">
                                                                            <span
                                                                                class="far fa-microphone"></span>Understanding
                                                                            Variables and Data Types
                                                                        </p>
                                                                        <div class="accrodion-content__icon">
                                                                            <span
                                                                                class="far fa-lock-alt"></span>
                                                                        </div>
                                                                    </li>
                                                                    <li>
                                                                        <p
                                                                            class="accrodion-content__points-text">
                                                                            <span
                                                                                class="far fa-microphone"></span>Tuples:
                                                                            Understanding Immutability and Use
                                                                            Cases
                                                                        </p>
                                                                        <div class="accrodion-content__icon">
                                                                            <span
                                                                                class="far fa-lock-alt"></span>
                                                                        </div>
                                                                    </li>
                                                                    <li>
                                                                        <p
                                                                            class="accrodion-content__points-text">
                                                                            <span
                                                                                class="fal fa-file-alt"></span>
                                                                            Nested Loops and Loop Control
                                                                            Statements (break, continue, pass)
                                                                        </p>
                                                                        <div class="accrodion-content__icon">
                                                                            <span
                                                                                class="far fa-lock-alt"></span>
                                                                        </div>
                                                                    </li>
                                                                </ul>
                                                            </div><!-- /.inner -->
                                                        </div>
                                                    </div>
                                                    <div class="accrodion">
                                                        <div class="accrodion-title">
                                                            <div class="accrodion-title-box">
                                                                <div class="accrodion-title__count"></div>
                                                                <div class="accrodion-title-text">
                                                                    <h4>Functions and Modules</h4>
                                                                </div>
                                                            </div>
                                                            <ul class="accrodion-meta list-unstyled">
                                                                <li>
                                                                    <p><span class="icon-book"></span>80 Lesson
                                                                    </p>
                                                                </li>
                                                                <li>
                                                                    <p><span class="icon-clock"></span>62h,
                                                                        20min</p>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="accrodion-content">
                                                            <div class="inner">
                                                                <h3 class="accrodion-content__title">
                                                                    Intermediate</h3>
                                                                <p class="accrodion-content__text">This
                                                                    curriculum offers a structured path
                                                                    with
                                                                    estimated times for each module, allowing
                                                                    students to plan their study schedule
                                                                    effectively.</p>
                                                                <ul
                                                                    class="accrodion-content__points list-unstyled">
                                                                    <li>
                                                                        <p
                                                                            class="accrodion-content__points-text">
                                                                            <span
                                                                                class="fal fa-video"></span>Overview
                                                                            of Python and Its Applications
                                                                        </p>
                                                                        <div
                                                                            class="accrodion-content__points-btn">
                                                                            <a href="#">Preview</a>
                                                                        </div>
                                                                        <div class="accrodion-content__icon">
                                                                            <span
                                                                                class="far fa-lock-alt"></span>
                                                                        </div>
                                                                    </li>
                                                                    <li>
                                                                        <p
                                                                            class="accrodion-content__points-text">
                                                                            <span
                                                                                class="fal fa-video"></span>Overview
                                                                            of Python and Its Applications
                                                                        </p>
                                                                        <div
                                                                            class="accrodion-content__points-btn">
                                                                            <a href="#">Preview</a>
                                                                        </div>
                                                                        <div class="accrodion-content__icon">
                                                                            <span
                                                                                class="far fa-lock-alt"></span>
                                                                        </div>
                                                                    </li>
                                                                    <li>
                                                                        <p
                                                                            class="accrodion-content__points-text">
                                                                            <span
                                                                                class="far fa-microphone"></span>Understanding
                                                                            Variables and Data Types
                                                                        </p>
                                                                        <div class="accrodion-content__icon">
                                                                            <span
                                                                                class="far fa-lock-alt"></span>
                                                                        </div>
                                                                    </li>
                                                                    <li>
                                                                        <p
                                                                            class="accrodion-content__points-text">
                                                                            <span
                                                                                class="far fa-microphone"></span>Tuples:
                                                                            Understanding Immutability and Use
                                                                            Cases
                                                                        </p>
                                                                        <div class="accrodion-content__icon">
                                                                            <span
                                                                                class="far fa-lock-alt"></span>
                                                                        </div>
                                                                    </li>
                                                                    <li>
                                                                        <p
                                                                            class="accrodion-content__points-text">
                                                                            <span
                                                                                class="fal fa-file-alt"></span>
                                                                            Nested Loops and Loop Control
                                                                            Statements (break, continue, pass)
                                                                        </p>
                                                                        <div class="accrodion-content__icon">
                                                                            <span
                                                                                class="far fa-lock-alt"></span>
                                                                        </div>
                                                                    </li>
                                                                </ul>
                                                            </div><!-- /.inner -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--Tab-->
                                <div class="tab" id="instructor">
                                    <div class="course-details__tab-inner">
                                        <div class="course-details__Instructor">
                                            <div class="course-details__Instructor-img">
                                                <img src="<?= base_url('assets/landing/') ?>images/resources/course-details-Instructor-img.jpg"
                                                    alt="">
                                            </div>
                                            <div class="course-details__Instructor-content">
                                                <div
                                                    class="course-details__Instructor-client-name-box-and-view">
                                                    <div class="course-details__Instructor-client-name-box">
                                                        <h4>Jordan Walk</h4>
                                                        <p>Sr. Python Developer</p>
                                                    </div>
                                                    <div class="course-details__Instructor-view">
                                                        <a href="#">View Details<span
                                                                class="icon-angles-right"></span></a>
                                                    </div>
                                                </div>
                                                <ul
                                                    class="course-details__Instructor-ratting-list list-unstyled">
                                                    <li>
                                                        <p><span class="fas fa-star"></span>(5.0 / 4.2 Rating)
                                                        </p>
                                                    </li>
                                                    <li>
                                                        <p><span class="fas fa-play-circle"></span>76 Courses
                                                        </p>
                                                    </li>
                                                </ul>
                                                <p class="course-details__Instructor-text">Jordan Walk is an
                                                    experienced Senior Python Developer with a strong focus on
                                                    building scalable and efficient solutions.<br>
                                                    With extensive knowledge in Python and backend development,
                                                    Jordan has a track record of leading successful projects,
                                                    optimizing performance, and implementing best practices.</p>
                                                <div class="course-details__Instructor-social">
                                                    <a href="#"><span class="fab fa-linkedin-in"></span></a>
                                                    <a href="#"><span class="fab fa-pinterest-p"></span></a>
                                                    <a href="#"><span class="fab fa-facebook-f"></span></a>
                                                    <a href="#"><span class="fab fa-instagram"></span></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--Tab-->
                                <div class="tab active-tab" id="review">
                                    <div class="course-details__tab-inner">
                                        <div class="course-details__ratting-and-review-box">
                                            <ul class="course-details__ratting-box list-unstyled">
                                                <li>
                                                    <div class="course-details__ratting-list">
                                                        <span class="icon-star"></span>
                                                        <span class="icon-star"></span>
                                                        <span class="icon-star"></span>
                                                        <span class="icon-star"></span>
                                                        <span class="icon-star"></span>
                                                    </div>
                                                    <div class="progress-levels">
                                                        <!--Skill Box-->
                                                        <div class="progress-box">
                                                            <div class="inner count-box">
                                                                <div class="bar">
                                                                    <div class="bar-innner">
                                                                        <div class="skill-percent">
                                                                            <span class="count-text"
                                                                                data-speed="3000"
                                                                                data-stop="90">0</span>
                                                                            <span class="percent">%</span>
                                                                        </div>
                                                                        <div class="bar-fill" data-percent="90">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="course-details__ratting-list">
                                                        <span class="icon-star"></span>
                                                        <span class="icon-star"></span>
                                                        <span class="icon-star"></span>
                                                        <span class="icon-star"></span>
                                                        <span class="fill-white icon-star"></span>
                                                    </div>
                                                    <div class="progress-levels">
                                                        <!--Skill Box-->
                                                        <div class="progress-box">
                                                            <div class="inner count-box">
                                                                <div class="bar">
                                                                    <div class="bar-innner">
                                                                        <div class="skill-percent">
                                                                            <span class="count-text"
                                                                                data-speed="3000"
                                                                                data-stop="70">0</span>
                                                                            <span class="percent">%</span>
                                                                        </div>
                                                                        <div class="bar-fill" data-percent="70">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="course-details__ratting-list">
                                                        <span class="icon-star"></span>
                                                        <span class="icon-star"></span>
                                                        <span class="icon-star"></span>
                                                        <span class="fill-white icon-star"></span>
                                                        <span class="fill-white icon-star"></span>
                                                    </div>
                                                    <div class="progress-levels">
                                                        <!--Skill Box-->
                                                        <div class="progress-box">
                                                            <div class="inner count-box">
                                                                <div class="bar">
                                                                    <div class="bar-innner">
                                                                        <div class="skill-percent">
                                                                            <span class="count-text"
                                                                                data-speed="3000"
                                                                                data-stop="60">0</span>
                                                                            <span class="percent">%</span>
                                                                        </div>
                                                                        <div class="bar-fill" data-percent="60">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="course-details__ratting-list">
                                                        <span class="icon-star"></span>
                                                        <span class="icon-star"></span>
                                                        <span class="fill-white icon-star"></span>
                                                        <span class="fill-white icon-star"></span>
                                                        <span class="fill-white icon-star"></span>
                                                    </div>
                                                    <div class="progress-levels">
                                                        <!--Skill Box-->
                                                        <div class="progress-box">
                                                            <div class="inner count-box">
                                                                <div class="bar">
                                                                    <div class="bar-innner">
                                                                        <div class="skill-percent">
                                                                            <span class="count-text"
                                                                                data-speed="3000"
                                                                                data-stop="40">0</span>
                                                                            <span class="percent">%</span>
                                                                        </div>
                                                                        <div class="bar-fill" data-percent="40">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="course-details__ratting-list">
                                                        <span class="icon-star"></span>
                                                        <span class="fill-white icon-star"></span>
                                                        <span class="fill-white icon-star"></span>
                                                        <span class="fill-white icon-star"></span>
                                                        <span class="fill-white icon-star"></span>
                                                    </div>
                                                    <div class="progress-levels">
                                                        <!--Skill Box-->
                                                        <div class="progress-box">
                                                            <div class="inner count-box">
                                                                <div class="bar">
                                                                    <div class="bar-innner">
                                                                        <div class="skill-percent">
                                                                            <span class="count-text"
                                                                                data-speed="3000"
                                                                                data-stop="30">0</span>
                                                                            <span class="percent">%</span>
                                                                        </div>
                                                                        <div class="bar-fill" data-percent="30">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                            <div class="course-details__review-box">
                                                <div class="course-details__review-count">
                                                    <span class="odometer" data-count="4.7">00</span>
                                                </div>
                                                <div class="course-details__review-content">
                                                    <p>127 Reviews</p>
                                                    <ul class="course-details__review-ratting list-unstyled">
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
                                                    <div class="course-details__review-text">
                                                        <p><span class="icon-star"></span>Excellent</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="comment-one">
                                            <h3 class="comment-one__title">Total Reviews (165)</h3>
                                            <ul class="comment-one__single-list list-unstyled">
                                                <li>
                                                    <div class="comment-one__single">
                                                        <div class="comment-one__image-box">
                                                            <div class="comment-one__image">
                                                                <img src="<?= base_url('assets/landing/') ?>images/blog/comment-1-1.jpg"
                                                                    alt="">
                                                            </div>
                                                        </div>
                                                        <div class="comment-one__content">
                                                            <div class="comment-one__name-box">
                                                                <h4>Alisa Manama <span>Product Designer</span>
                                                                </h4>
                                                            </div>
                                                            <p>This article really resonates with me! As a
                                                                working parent,
                                                                online
                                                                learning has been a game-changer. Iâ€™m able to
                                                                continue my
                                                                education
                                                                without sacrificing family time. I genuinely
                                                                believe that this
                                                                is
                                                                the
                                                                future of education.</p>
                                                            <div class="comment-one__btn-box">
                                                                <a href="blog-details.php"
                                                                    class="comment-one__btn">Reply</a>
                                                                <span>18 July, 2025</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="comment-one__single">
                                                        <div class="comment-one__image-box">
                                                            <div class="comment-one__image">
                                                                <img src="<?= base_url('assets/landing/') ?>images/blog/comment-1-2.jpg"
                                                                    alt="">
                                                            </div>
                                                        </div>
                                                        <div class="comment-one__content">
                                                            <div class="comment-one__name-box">
                                                                <h4>Jordan Walk <span>By Author</span></h4>
                                                            </div>
                                                            <p>Absolutely! Itâ€™s amazing how online learning
                                                                adapts to our lives,
                                                                isnâ€™t it? Being able to balance work, family,
                                                                and education is
                                                                such a huge advantage. Glad itâ€™s working so well
                                                                for you!</p>
                                                            <div class="comment-one__btn-box">
                                                                <a href="blog-details.php"
                                                                    class="comment-one__btn">Reply</a>
                                                                <span>18 July, 2025</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                            <ul
                                                class="comment-one__single-list comment-one__single-list-2 list-unstyled">
                                                <li>
                                                    <div class="comment-one__single">
                                                        <div class="comment-one__image-box">
                                                            <div class="comment-one__image">
                                                                <img src="<?= base_url('assets/landing/') ?>images/blog/comment-1-3.jpg"
                                                                    alt="">
                                                            </div>
                                                        </div>
                                                        <div class="comment-one__content">
                                                            <div class="comment-one__name-box">
                                                                <h4>Lisa Oliva <span>Fashion Designer</span>
                                                                </h4>
                                                            </div>
                                                            <p>This article really resonates with me! As a
                                                                working parent,
                                                                online learning has been a game-changer. Iâ€™m
                                                                able to continue my
                                                                education without sacrificing family time. I
                                                                genuinely believe
                                                                that this is the future of education.</p>
                                                            <div class="comment-one__btn-box">
                                                                <a href="blog-details.php"
                                                                    class="comment-one__btn">Reply</a>
                                                                <span>18 July, 2025</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="comment-form">
                                            <h3 class="comment-form__title">Submit Your Reviews</h3>
                                            <div class="comment-form__text-and-ratting">
                                                <p class="comment-form__text">Your Ratings </p>
                                                <ul class="comment-form__ratting list-unstyled">
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
                                            </div>
                                            <form action="<?= base_url('assets/landing/') ?>inc/sendemail.php"
                                                class="comment-form__form contact-form-validated"
                                                novalidate="novalidate">
                                                <div class="row">
                                                    <div class="col-xl-6 col-lg-6">
                                                        <div class="comment-form__input-box">
                                                            <input type="text" placeholder="Full Name"
                                                                name="name">
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-lg-6">
                                                        <div class="comment-form__input-box">
                                                            <input type="email" placeholder="Email Address"
                                                                name="email">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-xl-12">
                                                        <div class="comment-form__input-box text-message-box">
                                                            <textarea name="message"
                                                                placeholder="Write Review"></textarea>
                                                        </div>
                                                        <div class="comment-form__btn-box">
                                                            <button type="submit"
                                                                class="comment-form__btn"><span
                                                                    class="icon-arrow-circle"></span>Submit
                                                                Review</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            <div class="result"></div>
                                        </div>
                                    </div>
                                </div>
                                <!--Tab-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5">
                <div class="course-details__right">
                    <div class="course-details__info-box">
                        <div class="course-details__video-link">
                            <div class="course-details__video-link-bg"
                                style="background-image: url(<?= base_url('assets/landing/') ?>images/backgrounds/course-details-video-link-bg.jpg);">
                            </div>
                            <a href="https://www.youtube.com/watch?v=Get7rqXYrbQ" class="video-popup">
                                <div class="course-details__video-icon">
                                    <span class="icon-play"></span>
                                    <i class="ripple"></i>
                                </div>
                            </a>
                        </div>
                        <div class="course-details__doller-and-btn-box">
                            <h3 class="course-details__doller">$120.00</h3>
                            <div class="course-details__doller-btn-box">
                                <a href="course-details.php" class="thm-btn-two">
                                    <span>Enroll Now</span>
                                    <i class="icon-angles-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="course-details__social-box">
                            <h5 class="course-details__social-title">Share Course</h5>
                            <div class="course-details__social-list">
                                <a href="#"><span class="fab fa-linkedin-in"></span></a>
                                <a href="#"><span class="fab fa-pinterest-p"></span></a>
                                <a href="#"><span class="fab fa-facebook-f"></span></a>
                                <a href="#"><span class="fab fa-instagram"></span></a>
                            </div>
                        </div>
                        <div class="course-details__info-list">
                            <h3 class="course-details__info-list-title">This Course Included</h3>
                            <ul class="course-details__info-list-1 list-unstyled">
                                <li>
                                    <p><i class="icon-book"></i>Lesson</p>
                                    <span>50</span>
                                </li>
                                <li>
                                    <p><i class="icon-clock"></i>Duration</p>
                                    <span>30h, 20m</span>
                                </li>
                                <li>
                                    <p><i class="icon-chart-simple"></i>Skill Level</p>
                                    <span>Advance</span>
                                </li>
                                <li>
                                    <p><i class="icon-globe"></i>Language</p>
                                    <span>English, French</span>
                                </li>
                                <li>
                                    <p><i class="icon-stamp"></i>Certificate</p>
                                    <span>After Completed </span>
                                </li>
                                <li>
                                    <p><i class="icon-hourglass"></i>Deadline</p>
                                    <span>November 23, 2025</span>
                                </li>
                            </ul>
                        </div>
                        <div class="course-details__cuppon-box">
                            <h5 class="course-details__cuppon-title">Apply Coupon</h5>
                            <form action="#" class="course-details__search-form">
                                <input type="text" placeholder="Enter Coupon Code">
                                <button type="submit">Apply</button>
                            </form>
                            <p class="course-details__cuppon-text">30 days Money back grantee</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Course Details End-->


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