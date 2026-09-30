<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= esc($title ?? 'BegawiPBJ') ?></title>

    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/main/images/favicons/apple-touch-icon.png') ?>" />
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/main/images/favicons/favicon-32x32.png') ?>" />
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/main/images/favicons/favicon-16x16.png') ?>" />
    <link rel="manifest" href="<?= base_url('assets/main/images/favicons/site.webmanifest') ?>" />
    <meta name="description" content="<?= esc($metaDescription ?? 'BegawiPBJ - Platform informasi, pembelajaran, dan pengadaan barang/jasa Pemerintah Provinsi Lampung.') ?>" />
    <meta name="keywords" content="<?= esc($metaKeywords ?? 'BegawiPBJ, PBJ, pengadaan barang jasa, Provinsi Lampung') ?>" />

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

    <style>
        :root {
            --begawi-primary: #0c5aa6;
            --begawi-dark: #082f5c;
            --begawi-accent: #f3a20b;
        }

        .main-menu__logo img { max-height: 58px; width: auto; }
        .main-menu__btn-box-1 .thm-btn,
        .main-menu__btn-box-2 .thm-btn { min-width: auto; }
        .main-menu__btn-box-1 .thm-btn { background: transparent; border: 1px solid var(--begawi-primary); color: var(--begawi-primary); }
        .main-menu__btn-box-1 .thm-btn:hover { background: var(--begawi-primary); color: #fff; }
        .main-menu__btn-box-2 .thm-btn { background: var(--begawi-primary); }
        .main-menu__btn-box-2 .thm-btn:hover { background: var(--begawi-dark); }
        .main-menu__list > li.current > a,
        .main-menu__list > li:hover > a { color: var(--begawi-primary); }
        .main-menu__category-btn p { font-size: 15px; }
        .site-footer__copyright-text a { color: var(--begawi-accent); }
        .page-header__inner h2 { text-transform: none; }
        .pagination-wrapper .disabled { opacity: .45; pointer-events: none; }
    </style>

    <?= $this->renderSection('pageStyles') ?>
</head>

<body class="custom-cursor">
    <div class="custom-cursor__cursor"></div>
    <div class="custom-cursor__cursor-two"></div>

    <div class="loader js-preloader">
        <div></div>
        <div></div>
        <div></div>
    </div>

    <div class="page-wrapper">
        <?= $this->include('landing/partials/header') ?>

        <div class="stricky-header stricked-menu main-menu">
            <div class="sticky-header__content"></div>
        </div>

        <?= $this->renderSection('content') ?>

        <?= $this->include('landing/partials/footer') ?>
    </div>

    <?= $this->include('landing/partials/mobile_menu') ?>
    <?= $this->include('landing/partials/search_popup') ?>

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

    <?= $this->renderSection('pageScripts') ?>
</body>
</html>
