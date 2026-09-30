<?php $landingParts = APPPATH . 'Views' . DIRECTORY_SEPARATOR . 'landing' . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR; ?>
<?php $head_title = "Coming Soon || Begawipbj || Begawipbj PHP Template" ?>
<?php require_once($landingParts . 'layout/top-layout.php'); ?>
<!-- header -->

<!--Start Coming Soon page-->
<section class="coming-soon-page full-height">
    <div class="coming-soon-page__bg"
        style="background-image: url(<?= base_url('assets/landing/') ?>images/backgrounds/coming-soon-page-bg.jpg);"></div>
    <div class="coming-soon-page__content">
        <div class="inner">
            <div class="big-title">We're Coming Soon...</div>
            <div class="timer-box clearfix">
                <div class="countdown-timer">
                    <div class="default-coundown">
                        <div class="box">
                            <div class="countdown coming-soon-countdown" data-countdown-time="2025/12/28">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text">
                <p>
                    Website is under construction. We'll be here soon with new<br>
                    awesome site, Subscribe to be notified.
                </p>
            </div>
            <div class="coming-soon-page__subscribe-box">
                <form class="subscribe-form" action="#">
                    <input placeholder="Enter your email address" type="email">
                    <button type="submit" class="thm-btn coming-soon-page__btn"> <span
                            class="icon-angles-right"></span> Send Massage</button>
                </form>
            </div>
        </div>
    </div>
</section>
<!--End Coming Soon page-->


<?php require_once($landingParts . 'layout/bottom-layout.php'); ?>