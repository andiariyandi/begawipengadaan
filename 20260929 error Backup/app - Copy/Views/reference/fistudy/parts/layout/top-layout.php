<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title> <?php echo $head_title; ?> </title>
    <!-- favicons Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/landing/') ?>images/favicons/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/landing/') ?>images/favicons/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/landing/') ?>images/favicons/favicon-16x16.png" />
    <link rel="manifest" href="<?= base_url('assets/landing/') ?>images/favicons/site.webmanifest" />
    <meta name="description" content="Begawipbj - Ekosistem digital Pengadaan Barang/Jasa." />

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">


    <link
        href="https://fonts.googleapis.com/css2?family=Roboto+Serif:ital,opsz,wght@0,8..144,100..900;1,8..144,100..900&display=swap"
        rel="stylesheet">


    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/animate.min.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/custom-animate.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/swiper.min.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/font-awesome-all.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/jarallax.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/jquery.magnific-popup.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/odometer.min.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/flaticon.css">
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/owl.carousel.min.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/owl.theme.default.min.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/nice-select.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/jquery-ui.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/aos.css" />


    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/banner.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/slider.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/footer.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/sliding-text.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/category.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/about.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/courses.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/why-choose.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/live-class.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/video-one.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/blog.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/counter.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/team.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/newsletter.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/testimonial.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/contact.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/courses-search.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/pricing.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/process.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/event.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/page-header.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/error.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/become-a-teacher.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/shop.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/coming-soon.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/faq.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/module-css/gallery.css" />

    <!-- template styles -->
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/style.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/responsive.css" />
    <link rel="stylesheet" href="<?= base_url('assets/landing/') ?>css/user-area.css" />
    <?php if (isset($dark_mode) && !empty($dark_mode)) { ?>
        <link href="<?= base_url('assets/landing/') ?>css/dark.css" rel="stylesheet">
    <?php } ?>
</head>

<body class="custom-cursor">

    <div class="custom-cursor__cursor"></div>
    <div class="custom-cursor__cursor-two"></div>

    <!--Start Preloader-->
    <div class="loader js-preloader">
        <div></div>
        <div></div>
        <div></div>
    </div>
    <!--End Preloader-->




    <div class="page-wrapper">