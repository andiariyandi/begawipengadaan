<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= esc($pageTitle ?? $title ?? 'BegawiPengadaan') ?></title>

    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/main/images/favicons/apple-touch-icon.png') ?>" />
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/main/images/favicons/favicon-32x32.png') ?>" />
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/main/images/favicons/favicon-16x16.png') ?>" />
    <link rel="manifest" href="<?= base_url('assets/main/images/favicons/site.webmanifest') ?>" />
    <meta name="description" content="<?= esc($metaDescription ?? 'BegawiPengadaan - Platform informasi, pembelajaran, dan pengadaan barang/jasa Pemerintah Provinsi Lampung.') ?>" />
    <meta name="keywords" content="<?= esc($metaKeywords ?? 'BegawiPengadaan, PBJ, pengadaan barang jasa, Provinsi Lampung') ?>" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Serif:ital,opsz,wght@0,8..144,100..900;1,8..144,100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/main/css/bootstrap.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/animate.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/custom-animate.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/swiper.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/font-awesome-all.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/jarallax.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/jquery.magnific-popup.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/odometer.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/flaticon.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/owl.carousel.min.css') ?>" />
    <script src="<?= base_url('assets/dashboard/js/lib/iconify-icon.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('assets/main/css/owl.theme.default.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/nice-select.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/jquery-ui.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/aos.css') ?>" />

    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/banner.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/slider.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/footer.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/sliding-text.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/category.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/about.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/courses.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/why-choose.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/live-class.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/video-one.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/blog.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/counter.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/team.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/newsletter.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/testimonial.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/contact.css') ?>" />

    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/page-header.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/courses-search.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/event.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/faq.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/gallery.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/error.css') ?>" />

    <link rel="stylesheet" href="<?= base_url('assets/main/css/style.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/responsive.css') ?>" />

        <!-- BegawiPengadaan Landing Design System & Core Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-core.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-core.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-core.css') : '2.0' ?>" />

    <?= $this->renderSection('pageStyles') ?>
</head>

<?php $currentPath = trim(service('uri')->getPath(), '/'); ?>
<?php $isLandingHome = !empty($isHome) || $currentPath === '' || $currentPath === 'index.php'; ?>

<body class="custom-cursor <?= $isLandingHome ? 'landing-home' : 'landing-internal' ?>">
    <div class="custom-cursor__cursor"></div>
    <div class="custom-cursor__cursor-two"></div>

    <div class="loader js-preloader">
        <div></div>
        <div></div>
        <div></div>
    </div>

    <div class="page-wrapper">
        <?= $this->include('landing/components/header') ?>

        <?= $this->renderSection('content') ?>

        <?= $this->include('landing/components/footer') ?>
    </div>

    <?= $this->include('landing/components/mobile_menu') ?>
    <?= $this->include('landing/components/search_popup') ?>
    <?= $this->include('landing/components/whatsapp_chat') ?>
    <?= $this->include('landing/components/satisfaction_survey_modal') ?>

    <a href="#" data-target="html" class="scroll-to-target scroll-to-top">
        <span class="scroll-to-top__wrapper"><span class="scroll-to-top__inner"></span></span>
        <span class="scroll-to-top__text">Kembali ke Atas</span>
    </a>

    <script src="<?= base_url('assets/main/js/jquery-3.6.0.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/jarallax.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/jquery.ajaxchimp.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/jquery.appear.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/swiper.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/jquery.magnific-popup.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/jquery.validate.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/odometer.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/wNumb.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/wow.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/isotope.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/owl.carousel.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/jquery-ui.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/jquery.nice-select.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/marquee.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/aos.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/gsap/gsap.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/gsap/ScrollTrigger.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/gsap/SplitText.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/script.js') ?>"></script>

    <script>
        (function() {
            var header = document.querySelector('.main-header');
            if (header) {
                var onScroll = function() {
                    if (window.scrollY > 30) {
                        header.classList.add('is-scrolled');
                    } else {
                        header.classList.remove('is-scrolled');
                    }
                };
                window.addEventListener('scroll', onScroll, { passive: true });
                onScroll();
            }
            
            var mobileSurveyBtn = document.querySelector('.mobile-survey-btn');
            if (mobileSurveyBtn) {
                mobileSurveyBtn.addEventListener('click', function() {
                    var wrapper = document.querySelector('.mobile-nav__wrapper');
                    if (wrapper) wrapper.classList.remove('expanded');
                    document.body.classList.remove('locked');
                });
            }
        })();
    </script>

    <?= $this->renderSection('pageScripts') ?>
</body>

</html>