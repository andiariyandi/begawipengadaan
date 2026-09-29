<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan | BegawiPBJ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/font-awesome-all.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/flaticon.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/error.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/responsive.css') ?>">
</head>
<body>
    <section class="error-page">
        <div class="error-page__shape-1 float-bob-x"><img src="<?= base_url('assets/main/images/shapes/error-page-shape-1.png') ?>" alt=""></div>
        <div class="container"><div class="error-page__inner"><div class="error-page__title-box"><h2 class="error-page__title">404</h2></div><h3 class="error-page__tagline">Halaman tidak ditemukan</h3><p class="error-page__text">Halaman yang Anda tuju tidak tersedia atau telah dipindahkan.</p><a href="<?= route_to('landing.home') ?>" class="thm-btn">Kembali ke Beranda <span class="icon-angles-right"></span></a></div></div>
    </section>
</body>
</html>