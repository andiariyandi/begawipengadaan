<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BegawiPBJ — Ekosistem Digital Pengadaan Barang/Jasa Pemerintah Provinsi Lampung.">
    <title><?= esc($title ?? 'BegawiPBJ') ?></title>

    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/main/images/favicons/apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/main/images/favicons/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/main/images/favicons/favicon-16x16.png') ?>">
    <link rel="manifest" href="<?= base_url('assets/main/images/favicons/site.webmanifest') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Serif:ital,opsz,wght@0,8..144,100..900;1,8..144,100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/main/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/animate.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/custom-animate.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/swiper.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/font-awesome-all.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/jarallax.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/jquery.magnific-popup.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/odometer.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/flaticon.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/owl.carousel.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/owl.theme.default.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/nice-select.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/jquery-ui.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/aos.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/responsive.css') ?>">


    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/banner.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/slider.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/footer.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/sliding-text.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/category.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/about.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/courses.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/why-choose.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/live-class.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/video-one.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/blog.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/counter.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/team.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/newsletter.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/testimonial.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/contact.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/process.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/page-header.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/become-a-teacher.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/shop.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/faq.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/error.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/gallery.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/event.css') ?>">
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
        <?= $this->renderSection('content') ?>
        <?= $this->include('landing/partials/footer') ?>
    </div>

    <div class="mobile-nav__wrapper">
        <div class="mobile-nav__overlay mobile-nav__toggler"></div>
        <div class="mobile-nav__content">
            <span class="mobile-nav__close mobile-nav__toggler"><i class="fa fa-times"></i></span>
            <div class="logo-box">
                <a href="<?= route_to('landing.home') ?>" aria-label="BegawiPBJ">
                    <img src="<?= base_url('assets/main/images/resources/logo-1.png') ?>" width="155" alt="BegawiPBJ">
                </a>
            </div>
            <div class="mobile-nav__container"></div>
            <ul class="mobile-nav__contact list-unstyled">
                <li><i class="fa fa-envelope"></i><a href="mailto:info@begawipbj.lampungprov.go.id">info@begawipbj.lampungprov.go.id</a></li>
            </ul>
        </div>
    </div>

    <div class="search-popup">
        <div class="search-popup__overlay search-toggler"></div>
        <div class="search-popup__content">
            <form action="<?= route_to('landing.knowledge.search') ?>" method="get">
                <label for="search" class="sr-only">Cari konten</label>
                <input type="search" id="search" name="q" placeholder="Cari pengetahuan PBJ..." autocomplete="off">
                <button type="submit" aria-label="Cari"><i class="icon-search"></i></button>
            </form>
        </div>
    </div>

    <a href="#" data-target="html" class="scroll-to-target scroll-to-top"><i class="icon-up-arrow"></i></a>

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
</body>

</html>