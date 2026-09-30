<?php
$pageTitle = $pageTitle ?? 'BegawiPBJ';
$pageDescription = $pageDescription ?? 'Portal pembelajaran dan informasi Pengadaan Barang/Jasa.';
$pageStyles = $pageStyles ?? [];
?>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?= esc($pageTitle) ?></title>
<link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/main/images/favicons/apple-touch-icon.png') ?>" />
<link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/main/images/favicons/favicon-32x32.png') ?>" />
<link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/main/images/favicons/favicon-16x16.png') ?>" />
<link rel="manifest" href="<?= base_url('assets/main/images/favicons/site.webmanifest') ?>" />
<meta name="description" content="<?= esc($pageDescription) ?>" />

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
<link rel="stylesheet" href="<?= base_url('assets/main/css/flaticon.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/owl.carousel.min.css') ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/owl.theme.default.min.css') ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/nice-select.css') ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/jquery-ui.css') ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/aos.css') ?>" />

<?php foreach ($pageStyles as $style): ?>
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/' . esc($style)) ?>" />
<?php endforeach; ?>

<link rel="stylesheet" href="<?= base_url('assets/main/css/style.css') ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/responsive.css') ?>" />
